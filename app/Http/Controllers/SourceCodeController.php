<?php

namespace App\Http\Controllers;

use App\Models\Level;
use App\Models\SourceCode;
use App\Models\SourceCodeCategory;
use Illuminate\Http\Request;

class SourceCodeController extends Controller
{
    public function index(Request $request)
    {
        $query = SourceCode::with(['category', 'level', 'instructor'])
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
            $query->whereIn('source_code_category_id', $categories);
        }

        // Filter by level (multiple)
        if ($request->has('level') && $request->level) {
            $levels = is_array($request->level) ? $request->level : [$request->level];
            $query->whereIn('level_id', $levels);
        }

        // Filter by technology
        if ($request->has('technology') && $request->technology) {
            $query->whereJsonContains('technologies', $request->technology);
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

        // Sort
        $sortBy = $request->get('sort', 'newest');
        match($sortBy) {
            'oldest' => $query->orderBy('created_at', 'asc'),
            'price_low' => $query->orderBy('price', 'asc'),
            'price_high' => $query->orderBy('price', 'desc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        $sourceCodes = $query->paginate(12)->withQueryString();
        $categories = SourceCodeCategory::all();
        $levels = Level::orderBy('order')->get();
        $instructors = \App\Models\User::where('role', 'instructor')
            ->whereHas('sourceCodes', function($q) {
                $q->published();
            })
            ->get();

        // Get unique technologies from all source codes
        $technologies = SourceCode::published()
            ->whereNotNull('technologies')
            ->get()
            ->pluck('technologies')
            ->flatten()
            ->unique()
            ->values();

        // If AJAX request, return only the source codes container HTML
        if ($request->ajax()) {
            return response()->json([
                'html' => view('partials.source-codes-list', compact('sourceCodes'))->render()
            ]);
        }

        return view('source-codes.index', compact('sourceCodes', 'categories', 'levels', 'instructors', 'technologies'));
    }

    public function show($slug)
    {
        $sourceCode = SourceCode::with(['category', 'level', 'instructor'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Get related source codes from the same category
        $relatedSourceCodes = SourceCode::with(['category', 'level', 'instructor'])
            ->published()
            ->where('source_code_category_id', $sourceCode->source_code_category_id)
            ->where('id', '!=', $sourceCode->id)
            ->limit(4)
            ->get();

        return view('source-codes.show', compact('sourceCode', 'relatedSourceCodes'));
    }
}
