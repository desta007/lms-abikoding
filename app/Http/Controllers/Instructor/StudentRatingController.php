<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\StudentRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentRatingController extends Controller
{
    /**
     * Store or update a student rating
     */
    public function store(Request $request, $courseId, $studentId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        $userId = Auth::id();
        $user = Auth::user();
        
        $courseQuery = Course::where('id', $courseId);
        
        if (!$user->isAdmin()) {
            $courseQuery->where('instructor_id', $userId);
        }
        
        $course = $courseQuery->firstOrFail();

        // Verify student is enrolled in this course
        $enrollment = $course->enrollments()
            ->where('user_id', $studentId)
            ->firstOrFail();

        // Create or update rating
        $ratingData = [
            'course_id' => $courseId,
            'student_id' => $studentId,
            'instructor_id' => $userId,
        ];
        
        StudentRating::updateOrCreate(
            $ratingData,
            [
                'rating' => $request->rating,
                'review' => $request->review,
            ]
        );

        return redirect()->back()
            ->with('success', 'Rating siswa berhasil disimpan.');
    }

    /**
     * Update an existing student rating
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        $userId = Auth::id();
        $user = Auth::user();
        
        $query = StudentRating::where('id', $id);
        
        if (!$user->isAdmin()) {
            $query->where('instructor_id', $userId);
        }
        
        $rating = $query->firstOrFail();

        $rating->update([
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        return redirect()->back()
            ->with('success', 'Rating siswa berhasil diperbarui.');
    }

    /**
     * Delete a student rating
     */
    public function destroy($id)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        $query = StudentRating::where('id', $id);
        
        if (!$user->isAdmin()) {
            $query->where('instructor_id', $userId);
        }
        
        $rating = $query->firstOrFail();

        $rating->delete();

        return redirect()->back()
            ->with('success', 'Rating siswa berhasil dihapus.');
    }
}
