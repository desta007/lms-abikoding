<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserProfile;

class PointsService
{
    public static function awardPoints(User $user, string $action, int $points): void
    {
        $profile = $user->profile ?? UserProfile::create(['user_id' => $user->id]);
        $profile->increment('points', $points);
    }

    public static function deductPoints(User $user, int $points): void
    {
        $profile = $user->profile;
        if ($profile && $profile->points >= $points) {
            $profile->decrement('points', $points);
        }
    }
}

