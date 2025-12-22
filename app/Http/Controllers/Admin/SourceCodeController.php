<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SourceCode;
use Illuminate\Http\Request;

class SourceCodeController extends Controller
{
    public function index(Request $request)
    {
        $query = SourceCode::with(['category', 'level', 'instructor']);

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

        $sourceCodes = $query->latest()->paginate(12);

        return view('admin.source-codes.index', compact('sourceCodes'));
    }
}
