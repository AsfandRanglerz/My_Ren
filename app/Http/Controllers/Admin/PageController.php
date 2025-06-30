<?php

namespace App\Http\Controllers;

use App\Models\PrivacyPolicy;
use App\Models\TermCondition;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function privacyPolicy()
    {
        $data = PrivacyPolicy::where('description')->firstOrFail();
        return view('privacyPolicy.webView.blade', compact('data'));
    }

    public function termsCondition()
    {
        $data = TermCondition::where('description')->firstOrFail();
        return view('terms_and_condition.webView.blade', compact('data'));
    }
}
