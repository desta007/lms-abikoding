<?php

namespace App\Listeners;

use App\Events\CourseCompleted;
use App\Notifications\CourseCompletedNotification;

class SendCourseCompletedNotification
{
    public function handle(CourseCompleted $event): void
    {
        $event->user->notify(new CourseCompletedNotification($event->course));
    }
}
