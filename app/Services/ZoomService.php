<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ZoomService
{
    private ?string $clientId;
    private ?string $clientSecret;
    private ?string $accountId;
    private string $baseUrl = 'https://api.zoom.us/v2';
    private int $tokenCacheTime = 3600; // Cache token for 1 hour (tokens expire in 1 hour)

    public function __construct()
    {
        // Server-to-Server OAuth App credentials
        // Read from config first, then fallback to env directly
        $clientId = config('services.zoom.client_id');
        $clientSecret = config('services.zoom.client_secret');
        $accountId = config('services.zoom.account_id');
        
        // Fallback to env if config is null or empty
        $this->clientId = trim($clientId ?: env('ZOOM_CLIENT_ID', ''));
        $this->clientSecret = trim($clientSecret ?: env('ZOOM_CLIENT_SECRET', ''));
        $this->accountId = trim($accountId ?: env('ZOOM_ACCOUNT_ID', ''));
        
        // Validate required credentials (check if empty after trim)
        if (empty($this->clientId) || empty($this->clientSecret) || empty($this->accountId)) {
            Log::warning('Zoom credentials are not fully configured. Some features may not work.', [
                'client_id_set' => !empty($this->clientId),
                'client_secret_set' => !empty($this->clientSecret),
                'account_id_set' => !empty($this->accountId),
                'config_client_id' => $clientId ? 'SET' : 'NOT SET',
                'env_client_id' => env('ZOOM_CLIENT_ID') ? 'SET' : 'NOT SET',
            ]);
        }
    }

    /**
     * Generate Zoom access token using Server-to-Server OAuth
     * Tokens are cached to avoid unnecessary API calls
     */
    private function getAccessToken(): string
    {
        // Always read credentials fresh from config/env to ensure we have the latest values
        // This is important because config cache might not be updated immediately
        $clientId = trim(config('services.zoom.client_id') ?: env('ZOOM_CLIENT_ID', ''));
        $clientSecret = trim(config('services.zoom.client_secret') ?: env('ZOOM_CLIENT_SECRET', ''));
        $accountId = trim(config('services.zoom.account_id') ?: env('ZOOM_ACCOUNT_ID', ''));
        
        // If still empty, try reading from instance properties as last resort
        if (empty($clientId)) {
            $clientId = trim($this->clientId ?? '');
        }
        if (empty($clientSecret)) {
            $clientSecret = trim($this->clientSecret ?? '');
        }
        if (empty($accountId)) {
            $accountId = trim($this->accountId ?? '');
        }
        
        if (empty($clientId) || empty($clientSecret) || empty($accountId)) {
            Log::error('Zoom credentials validation failed', [
                'client_id_empty' => empty($clientId),
                'client_secret_empty' => empty($clientSecret),
                'account_id_empty' => empty($accountId),
                'config_client_id' => config('services.zoom.client_id') ? 'SET (' . strlen(config('services.zoom.client_id')) . ' chars)' : 'NOT SET',
                'env_client_id' => env('ZOOM_CLIENT_ID') ? 'SET (' . strlen(env('ZOOM_CLIENT_ID')) . ' chars)' : 'NOT SET',
            ]);
            
            throw new \Exception('Zoom credentials are not configured. Please set ZOOM_CLIENT_ID, ZOOM_CLIENT_SECRET, and ZOOM_ACCOUNT_ID in your .env file and run "php artisan config:clear".');
        }
        
        // Update instance properties with trimmed values for future use
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->accountId = $accountId;
        
        // Check cache first
        $cacheKey = 'zoom_access_token';
        $cachedToken = Cache::get($cacheKey);
        
        if ($cachedToken) {
            return $cachedToken;
        }

        $url = "https://zoom.us/oauth/token";
        
        // Server-to-Server OAuth uses account_credentials grant type
        // withBasicAuth must be called before post(), not after
        $response = Http::asForm()
            ->withBasicAuth($this->clientId, $this->clientSecret)
            ->post($url, [
                'grant_type' => 'account_credentials',
                'account_id' => $this->accountId,
            ]);

        if ($response->successful()) {
            $tokenData = $response->json();
            $accessToken = $tokenData['access_token'];
            
            // Cache the token (expires in 1 hour, cache for slightly less)
            $expiresIn = ($tokenData['expires_in'] ?? 3600) - 60; // Subtract 60 seconds for safety
            Cache::put($cacheKey, $accessToken, now()->addSeconds($expiresIn));
            
            return $accessToken;
        }

        Log::error('Zoom token generation failed', [
            'status' => $response->status(),
            'response' => $response->body(),
            'account_id' => $this->accountId
        ]);
        
        throw new \Exception('Failed to generate Zoom access token: ' . ($response->json()['error_description'] ?? $response->body()));
    }

    /**
     * Create a Zoom meeting using Server-to-Server OAuth
     * 
     * @param array $data Meeting data
     * @param string|null $userId User ID or email (defaults to 'me' for account owner)
     * @return array Meeting details
     */
    public function createMeeting(array $data, ?string $userId = null): array
    {
        $token = $this->getAccessToken();
        
        // Use 'me' for account owner if no userId specified
        $userId = $userId ?? 'me';
        
        // Format start_time if provided
        $startTime = null;
        if (isset($data['start_time'])) {
            $startTime = is_string($data['start_time']) 
                ? $data['start_time'] 
                : \Carbon\Carbon::parse($data['start_time'])->toIso8601String();
        }
        
        $meetingData = [
            'topic' => $data['topic'] ?? 'Live Session',
            'type' => $data['type'] ?? 2, // 2 = Scheduled meeting, 1 = Instant meeting
            'duration' => $data['duration'] ?? 60,
            'timezone' => $data['timezone'] ?? config('app.timezone', 'Asia/Jakarta'),
            'password' => $data['password'] ?? $this->generatePassword(),
            'settings' => [
                'host_video' => $data['host_video'] ?? true,
                'participant_video' => $data['participant_video'] ?? true,
                'join_before_host' => $data['join_before_host'] ?? false,
                'mute_upon_entry' => $data['mute_upon_entry'] ?? false,
                'waiting_room' => $data['waiting_room'] ?? false,
                'approval_type' => $data['approval_type'] ?? 0, // 0 = Automatically approve
                'registration_type' => $data['registration_type'] ?? 0, // 0 = No registration required
            ],
        ];
        
        // Only add start_time if provided (required for scheduled meetings)
        if ($startTime) {
            $meetingData['start_time'] = $startTime;
        }

        $response = Http::withToken($token)
            ->post("{$this->baseUrl}/users/{$userId}/meetings", $meetingData);

        if ($response->successful()) {
            return $response->json();
        }

        $errorData = $response->json();
        Log::error('Zoom meeting creation failed', [
            'status' => $response->status(),
            'response' => $response->body(),
            'meeting_data' => $meetingData
        ]);
        
        $errorMessage = $errorData['message'] ?? $errorData['error_description'] ?? 'Failed to create Zoom meeting';
        throw new \Exception('Failed to create Zoom meeting: ' . $errorMessage);
    }

    /**
     * Update a Zoom meeting
     */
    public function updateMeeting(string $meetingId, array $data): bool
    {
        $token = $this->getAccessToken();
        
        $response = Http::withToken($token)
            ->patch("{$this->baseUrl}/meetings/{$meetingId}", $data);

        if ($response->successful()) {
            return true;
        }

        $errorData = $response->json();
        Log::error('Zoom meeting update failed', [
            'meeting_id' => $meetingId,
            'status' => $response->status(),
            'response' => $response->body(),
            'data' => $data
        ]);
        
        return false;
    }

    /**
     * Delete a Zoom meeting
     * 
     * @param string $meetingId Meeting ID
     * @param array $options Additional options (e.g., ['occurrence_id' => 'xxx'])
     * @return bool
     */
    public function deleteMeeting(string $meetingId, array $options = []): bool
    {
        $token = $this->getAccessToken();
        
        $url = "{$this->baseUrl}/meetings/{$meetingId}";
        
        // Add query parameters if provided
        if (!empty($options)) {
            $url .= '?' . http_build_query($options);
        }
        
        $response = Http::withToken($token)
            ->delete($url);

        if ($response->successful()) {
            return true;
        }

        Log::error('Zoom meeting deletion failed', [
            'meeting_id' => $meetingId,
            'status' => $response->status(),
            'response' => $response->body(),
            'options' => $options
        ]);
        
        return false;
    }

    /**
     * Get Zoom meeting details
     * 
     * @param string $meetingId Meeting ID
     * @param array $options Additional options (e.g., ['occurrence_id' => 'xxx'])
     * @return array|null
     */
    public function getMeeting(string $meetingId, array $options = []): ?array
    {
        $token = $this->getAccessToken();
        
        $url = "{$this->baseUrl}/meetings/{$meetingId}";
        
        // Add query parameters if provided
        if (!empty($options)) {
            $url .= '?' . http_build_query($options);
        }
        
        $response = Http::withToken($token)
            ->get($url);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Zoom meeting retrieval failed', [
            'meeting_id' => $meetingId,
            'status' => $response->status(),
            'response' => $response->body(),
            'options' => $options
        ]);
        
        return null;
    }
    
    /**
     * Get account owner/user information
     * Useful for Server-to-Server OAuth to get account details
     */
    public function getAccountOwner(): ?array
    {
        $token = $this->getAccessToken();
        
        $response = Http::withToken($token)
            ->get("{$this->baseUrl}/users/me");

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Zoom account owner retrieval failed', [
            'status' => $response->status(),
            'response' => $response->body()
        ]);
        
        return null;
    }

    /**
     * Generate random password for meeting
     */
    private function generatePassword(int $length = 8): string
    {
        return substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
    }
}

