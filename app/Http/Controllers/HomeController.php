<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\Level;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with(['category', 'level', 'instructor', 'ratings'])
            ->published();

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('subtitle', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category (multiple)
        if ($request->has('category') && $request->category) {
            $categories = is_array($request->category) ? $request->category : [$request->category];
            $query->whereIn('category_id', $categories);
        }

        // Filter by level (multiple)
        if ($request->has('level') && $request->level) {
            $levels = is_array($request->level) ? $request->level : [$request->level];
            $query->whereIn('level_id', $levels);
        }

        // Filter by language
        if ($request->has('language') && $request->language) {
            $query->where('language', $request->language);
        }

        // Filter by instructor
        if ($request->has('instructor') && $request->instructor) {
            $query->byInstructor($request->instructor);
        }

        // Filter by price range
        if ($request->has('price_range')) {
            $priceRange = $request->price_range;
            if ($priceRange === 'free') {
                $query->where('price', 0);
            } elseif ($priceRange === 'paid') {
                $query->where('price', '>', 0);
            } elseif ($priceRange === 'under_100k') {
                $query->where('price', '>', 0)->where('price', '<=', 100000);
            } elseif ($priceRange === '100k_500k') {
                $query->where('price', '>', 100000)->where('price', '<=', 500000);
            } elseif ($priceRange === 'over_500k') {
                $query->where('price', '>', 500000);
            }
        }
        
        // Custom price range
        if ($request->has('min_price') && $request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && $request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort
        $sortBy = $request->get('sort', 'newest');
        match($sortBy) {
            'rating' => $query->withAvg('ratings', 'rating')->orderBy('ratings_avg_rating', 'desc'),
            'oldest' => $query->orderBy('created_at', 'asc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        $courses = $query->paginate(12)->withQueryString();
        $categories = Category::all();
        $levels = Level::orderBy('order')->get();
        $instructors = \App\Models\User::where('role', 'instructor')
            ->whereHas('courses', function($q) {
                $q->published();
            })
            ->get();
        $languages = Course::published()->distinct()->pluck('language')->filter();

        // If AJAX request, return only the courses container wrapper HTML
        if ($request->ajax()) {
            return response()->json([
                'html' => view('partials.courses-list', compact('courses'))->render()
            ]);
        }

        return view('home', compact('courses', 'categories', 'levels', 'instructors', 'languages'));
    }
}
