<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     * Switch language and direction.
     */
    public function switch(Request $request): RedirectResponse
    {
        $language = $request->input('lang', 'ar');
        $direction = $request->input('dir', 'rtl');
        
        // Store in session
        session(['locale' => $language, 'direction' => $direction]);
        
        // Redirect back
        return redirect()->back();
    }
}

