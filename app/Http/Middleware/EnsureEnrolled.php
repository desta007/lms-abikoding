<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEnrolled
{
    public function handle(Request $request, Closure $next): Response
    {
        $courseId = $request->route('courseId') ?? $request->route('course');
        
        if (!$courseId) {
            abort(404);
        }

        $enrollment = \App\Models\CourseEnrollment::where('course_id', $courseId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$enrollment) {
            return redirect()->route('courses.show', \App\Models\Course::findOrFail($courseId)->slug)
                ->with('error', 'Anda harus terdaftar terlebih dahulu untuk mengakses konten kursus');
        }

        return $next($request);
    }
}
