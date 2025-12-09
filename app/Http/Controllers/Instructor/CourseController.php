<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        // Admin can see all courses, instructor only sees their own
        $query = $user->isAdmin() 
            ? Course::with(['category', 'level'])
            : Course::where('instructor_id', $userId)->with(['category', 'level']);

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('subtitle', 'like', "%{$request->search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } else {
                $query->where('is_published', false);
            }
        }

        $courses = $query->latest()->paginate(12);

        // If AJAX request, return only the courses table HTML
        if ($request->ajax()) {
            return response()->json([
                'html' => view('instructor.courses.index', compact('courses'))->render()
            ]);
        }

        return view('instructor.courses.index', compact('courses'));
    }

    public function create()
    {
        $categories = Category::all();
        $levels = Level::orderBy('order')->get();
        
        return view('instructor.courses.create', compact('categories', 'levels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:courses',
            'thumbnail' => 'required|image|max:2048',
            'category_id' => 'required|exists:categories,id',
            'level_id' => 'required|exists:levels,id',
            'about_course' => 'required|string',
            'about_instructor' => 'required|string',
            'price' => 'required|numeric|min:0',
            'language' => 'required|string',
        ]);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        }

        // Admin can create courses and set themselves as instructor
        $validated['instructor_id'] = Auth::id();
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);

        $course = Course::create($validated);

        return redirect()->route('instructor.courses.show', $course->id)
            ->with('success', 'Kursus berhasil dibuat!');
    }

    public function show($id)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        // Admin can see all courses, instructor only sees their own
        $query = $user->isAdmin()
            ? Course::with(['chapters.materials', 'chapters.exams', 'category', 'level'])
            : Course::where('instructor_id', $userId)->with(['chapters.materials', 'chapters.exams', 'category', 'level']);
        
        $course = $query->findOrFail($id);

        $chaptersData = $course->chapters->map(function ($chapter) {
            return [
                'id' => $chapter->id,
                'title' => $chapter->title,
                'description' => $chapter->description,
                'order' => $chapter->order,
            ];
        })->values();

        $materialsData = $course->chapters->flatMap(function ($chapter) {
            return $chapter->materials->map(function ($material) {
                return [
                    'id' => $material->id,
                    'chapter_id' => $material->chapter_id,
                    'title' => $material->title,
                    'material_type' => $material->material_type,
                    'content' => $material->content,
                    'file_path' => $material->file_path,
                ];
            });
        })->values();

        return view('instructor.courses.show', compact('course', 'chaptersData', 'materialsData'));
    }

    public function edit($id)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        // Admin can edit all courses, instructor only edits their own
        $query = $user->isAdmin()
            ? Course::query()
            : Course::where('instructor_id', $userId);
        
        $course = $query->findOrFail($id);
        $categories = Category::all();
        $levels = Level::orderBy('order')->get();

        return view('instructor.courses.edit', compact('course', 'categories', 'levels'));
    }

    public function update(Request $request, $id)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        // Admin can update all courses, instructor only updates their own
        $query = $user->isAdmin()
            ? Course::query()
            : Course::where('instructor_id', $userId);
        
        $course = $query->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:courses,slug,' . $course->id,
            'thumbnail' => 'nullable|image|max:2048',
            'category_id' => 'required|exists:categories,id',
            'level_id' => 'required|exists:levels,id',
            'about_course' => 'required|string',
            'about_instructor' => 'required|string',
            'price' => 'required|numeric|min:0',
            'language' => 'required|string',
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        }

        $course->update($validated);

        return redirect()->route('instructor.courses.show', $course->id)
            ->with('success', 'Kursus berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        // Admin can delete all courses, instructor only deletes their own
        $query = $user->isAdmin()
            ? Course::query()
            : Course::where('instructor_id', $userId);
        
        $course = $query->findOrFail($id);
        
        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }
        
        $course->delete();

        return redirect()->route('instructor.courses.index')
            ->with('success', 'Kursus berhasil dihapus!');
    }

    public function publish($id)
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        // Admin can publish all courses, instructor only publishes their own
        $query = $user->isAdmin()
            ? Course::query()
            : Course::where('instructor_id', $userId);
        
        $course = $query->findOrFail($id);
        
        // Toggle publish status
        $course->update(['is_published' => !$course->is_published]);
        
        $status = $course->is_published ? 'dipublikasikan' : 'disembunyikan';
        
        return redirect()->back()
            ->with('success', "Kursus berhasil {$status}!");
    }
}
