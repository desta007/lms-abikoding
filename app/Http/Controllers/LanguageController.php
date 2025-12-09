<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    public function switch(Request $request)
    {
        $request->validate([
            'lang' => 'required|in:id,ja,en',
        ]);

        $locale = $request->lang;
        App::setLocale($locale);
        session(['locale' => $locale]);

        return redirect()->back()->withCookie(cookie('locale', $locale, 60 * 24 * 30));
    }
}
