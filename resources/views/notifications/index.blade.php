<x-app-layout>
    @php
        $initialUnreadCount = \App\Models\Notification::where('user_id', Auth::id())->where('is_read', false)->count();
    @endphp
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Notifikasi</h1>
                    <form method="POST" action="{{ route('notifications.mark-all-read') }}" 
                          onsubmit="
                              event.preventDefault();
                              fetch('{{ route('notifications.mark-all-read') }}', {
                                  method: 'POST',
                                  headers: {
                                      'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                      'Content-Type': 'application/json',
                                      'Accept': 'application/json'
                                  }
                              })
                              .then(() => {
                                  window.location.reload();
                              });
                          ">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">
                            Tandai Semua Sudah Dibaca
                        </button>
                    </form>
                </div>

                @if($notifications->count() > 0)
                    <div class="space-y-3">
                        @foreach($notifications as $notification)
                            <div class="p-4 border rounded-lg transition-all {{ $notification->is_read ? 'bg-white border-gray-200' : 'bg-indigo-50 border-indigo-200' }} hover:bg-gray-50 notification-item" 
                                 data-notification-id="{{ $notification->id }}"
                                 data-is-read="{{ $notification->is_read ? 'true' : 'false' }}">
                                <div class="flex items-start gap-4">
                                    <a href="{{ $notification->link ?: '#' }}" 
                                       class="flex-shrink-0 mt-1 notification-link"
                                       data-link="{{ $notification->link ?: '#' }}"
                                       @if(!$notification->is_read)
                                       onclick="
                                           event.preventDefault();
                                           const notificationDiv = event.target.closest('.notification-item');
                                           const link = notificationDiv.querySelector('.notification-link').dataset.link;
                                           
                                           fetch('{{ route('notifications.read', $notification->id) }}', {
                                               method: 'POST',
                                               headers: {
                                                   'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                   'Content-Type': 'application/json',
                                                   'Accept': 'application/json'
                                               }
                                           })
                                           .then(response => response.json())
                                           .then(data => {
                                               if (data.success) {
                                                   // Update badge count in navigation using Alpine.js
                                                   // Find the notification dropdown in nav and update unreadCount
                                                   const navElement = document.querySelector('nav');
                                                   if (navElement) {
                                                       const notificationDropdowns = navElement.querySelectorAll('[x-data]');
                                                       notificationDropdowns.forEach(dropdown => {
                                                           try {
                                                               if (dropdown.__x && dropdown.__x.$data && typeof dropdown.__x.$data.unreadCount === 'number') {
                                                                   dropdown.__x.$data.unreadCount = Math.max(0, dropdown.__x.$data.unreadCount - 1);
                                                               } else if (dropdown.__x && dropdown.__x.$data && typeof dropdown.__x.$data.loadUnreadCount === 'function') {
                                                                   // If loadUnreadCount method exists, call it to refresh
                                                                   dropdown.__x.$data.loadUnreadCount();
                                                               }
                                                           } catch (e) {
                                                               // Silently fail, badge will update on next page load
                                                           }
                                                       });
                                                   }
                                                   // Remove unread indicator
                                                   const unreadDot = notificationDiv.querySelector('.notification-unread-dot');
                                                   if (unreadDot) {
                                                       unreadDot.remove();
                                                   }
                                                   // Update background color
                                                   notificationDiv.classList.remove('bg-indigo-50', 'border-indigo-200');
                                                   notificationDiv.classList.add('bg-white', 'border-gray-200');
                                                   // Mark as read in data attribute
                                                   notificationDiv.dataset.isRead = 'true';
                                                   // Navigate to link
                                                   if (link && link !== '#') {
                                                       window.location.href = link;
                                                   }
                                               }
                                           })
                                           .catch(error => {
                                               console.error('Error:', error);
                                               // Navigate anyway
                                               if (link && link !== '#') {
                                                   window.location.href = link;
                                               }
                                           });
                                       "
                                       @endif
                                       >
                                        @if($notification->type === 'material_approval_request' || $notification->type === 'quiz_retake_request')
                                            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                        @elseif($notification->type === 'material_approved' || $notification->type === 'quiz_retake_approved')
                                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        @elseif($notification->type === 'material_rejected' || $notification->type === 'quiz_retake_rejected')
                                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                                <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </a>
                                    <a href="{{ $notification->link ?: '#' }}" 
                                       class="flex-1 min-w-0 notification-link"
                                       data-link="{{ $notification->link ?: '#' }}"
                                       @if(!$notification->is_read)
                                       onclick="
                                           event.preventDefault();
                                           const notificationDiv = event.target.closest('.notification-item');
                                           const link = notificationDiv.querySelector('.notification-link').dataset.link;
                                           
                                           fetch('{{ route('notifications.read', $notification->id) }}', {
                                               method: 'POST',
                                               headers: {
                                                   'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                   'Content-Type': 'application/json',
                                                   'Accept': 'application/json'
                                               }
                                           })
                                           .then(response => response.json())
                                           .then(data => {
                                               if (data.success) {
                                                   // Update badge count in navigation using Alpine.js
                                                   // Find the notification dropdown in nav and update unreadCount
                                                   const navElement = document.querySelector('nav');
                                                   if (navElement) {
                                                       const notificationDropdowns = navElement.querySelectorAll('[x-data]');
                                                       notificationDropdowns.forEach(dropdown => {
                                                           try {
                                                               if (dropdown.__x && dropdown.__x.$data && typeof dropdown.__x.$data.unreadCount === 'number') {
                                                                   dropdown.__x.$data.unreadCount = Math.max(0, dropdown.__x.$data.unreadCount - 1);
                                                               } else if (dropdown.__x && dropdown.__x.$data && typeof dropdown.__x.$data.loadUnreadCount === 'function') {
                                                                   // If loadUnreadCount method exists, call it to refresh
                                                                   dropdown.__x.$data.loadUnreadCount();
                                                               }
                                                           } catch (e) {
                                                               // Silently fail, badge will update on next page load
                                                           }
                                                       });
                                                   }
                                                   // Remove unread indicator
                                                   const unreadDot = notificationDiv.querySelector('.notification-unread-dot');
                                                   if (unreadDot) {
                                                       unreadDot.remove();
                                                   }
                                                   // Update background color
                                                   notificationDiv.classList.remove('bg-indigo-50', 'border-indigo-200');
                                                   notificationDiv.classList.add('bg-white', 'border-gray-200');
                                                   // Mark as read in data attribute
                                                   notificationDiv.dataset.isRead = 'true';
                                                   // Navigate to link
                                                   if (link && link !== '#') {
                                                       window.location.href = link;
                                                   }
                                               }
                                           })
                                           .catch(error => {
                                               console.error('Error:', error);
                                               // Navigate anyway
                                               if (link && link !== '#') {
                                                   window.location.href = link;
                                               }
                                           });
                                       "
                                       @endif
                                       >
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h3 class="text-lg font-semibold text-gray-900">{{ $notification->title }}</h3>
                                                <p class="text-sm text-gray-600 mt-1">{{ $notification->message }}</p>
                                                <p class="text-xs text-gray-400 mt-2">{{ $notification->created_at->format('d M Y, H:i') }}</p>
                                            </div>
                                            <div class="flex items-center gap-2 ml-4">
                                                @if(!$notification->is_read)
                                                    <div class="w-2 h-2 bg-indigo-600 rounded-full notification-unread-dot"></div>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                    <button type="button" 
                                            onclick="
                                                const button = this;
                                                const notificationDiv = button.closest('.notification-item');
                                                const isUnread = notificationDiv.dataset.isRead === 'false';
                                                
                                                if (confirm('Hapus notifikasi ini?')) {
                                                    fetch('{{ route('notifications.destroy', $notification->id) }}', {
                                                        method: 'DELETE',
                                                        headers: {
                                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                            'Content-Type': 'application/json',
                                                            'Accept': 'application/json'
                                                        }
                                                    })
                                                    .then(response => response.json())
                                                    .then(data => {
                                                        if (data.success) {
                                                            // Update badge count if notification was unread
                                                            if (isUnread) {
                                                                const navElement = document.querySelector('nav');
                                                                if (navElement) {
                                                                    const notificationDropdowns = navElement.querySelectorAll('[x-data]');
                                                                    notificationDropdowns.forEach(dropdown => {
                                                                        try {
                                                                            if (dropdown.__x && dropdown.__x.$data && typeof dropdown.__x.$data.unreadCount === 'number') {
                                                                                dropdown.__x.$data.unreadCount = Math.max(0, dropdown.__x.$data.unreadCount - 1);
                                                                            }
                                                                        } catch (e) {
                                                                            console.error('Error updating badge count:', e);
                                                                        }
                                                                    });
                                                                }
                                                            }
                                                            // Remove the notification element
                                                            notificationDiv.style.transition = 'opacity 0.3s';
                                                            notificationDiv.style.opacity = '0';
                                                            setTimeout(() => {
                                                                notificationDiv.remove();
                                                            }, 300);
                                                        }
                                                    })
                                                    .catch(error => {
                                                        console.error('Error:', error);
                                                        alert('Gagal menghapus notifikasi');
                                                    });
                                                }
                                            "
                                            class="text-gray-400 hover:text-red-600 transition-colors p-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $notifications->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <p class="text-gray-500 text-lg">Tidak ada notifikasi</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

