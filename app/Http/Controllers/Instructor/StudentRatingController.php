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

        $course = Course::where('id', $courseId)
            ->where('instructor_id', Auth::id())
            ->firstOrFail();

        // Verify student is enrolled in this course
        $enrollment = $course->enrollments()
            ->where('user_id', $studentId)
            ->firstOrFail();

        // Create or update rating
        StudentRating::updateOrCreate(
            [
                'course_id' => $courseId,
                'student_id' => $studentId,
                'instructor_id' => Auth::id(),
            ],
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

        $rating = StudentRating::where('id', $id)
            ->where('instructor_id', Auth::id())
            ->firstOrFail();

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
        $rating = StudentRating::where('id', $id)
            ->where('instructor_id', Auth::id())
            ->firstOrFail();

        $rating->delete();

        return redirect()->back()
            ->with('success', 'Rating siswa berhasil dihapus.');
    }
}
