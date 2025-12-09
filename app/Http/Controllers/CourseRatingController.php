<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseRatingController extends Controller
{
    /**
     * Store or update a course rating/review
     */
    public function store(Request $request, $courseId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        $course = Course::findOrFail($courseId);
        
        // Check if user is enrolled
        $enrollment = CourseEnrollment::where('course_id', $courseId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$enrollment) {
            return redirect()->back()
                ->with('error', 'Anda harus terdaftar dalam kursus ini untuk memberikan rating.');
        }

        // Check if student has completed all materials
        if (!$enrollment->hasCompletedAllMaterials()) {
            return redirect()->back()
                ->with('error', 'Anda harus menyelesaikan semua materi kursus sebelum memberikan rating.');
        }

        // Create or update rating
        CourseRating::updateOrCreate(
            [
                'course_id' => $courseId,
                'user_id' => Auth::id(),
            ],
            [
                'rating' => $request->rating,
                'review' => $request->review,
            ]
        );

        return redirect()->back()
            ->with('success', 'Rating berhasil disimpan.');
    }

    /**
     * Update an existing rating
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        $rating = CourseRating::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $rating->update([
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        return redirect()->back()
            ->with('success', 'Rating berhasil diperbarui.');
    }

    /**
     * Delete a rating
     */
    public function destroy($id)
    {
        $rating = CourseRating::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $rating->delete();

        return redirect()->back()
            ->with('success', 'Rating berhasil dihapus.');
    }
}
