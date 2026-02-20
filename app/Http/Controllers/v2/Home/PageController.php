<?php

namespace App\Http\Controllers\v2\Home;

use App\Http\Controllers\Controller;

class PageController extends Controller
{
    public function termsAndConditions()
    {
        $data = [
            'page_title' => __('Terms And Conditions For Virtuard Reality Design'),
            'page_description' => __('These Terms And Conditions Outline The Rules, Responsibilities, And Legal Agreements That Govern The Use Of Virtuard Reality Design Services.'),
        ];

        return view('v2.page.terms', $data);
    }

    public function privacyPolicy()
    {
        $data = [
            'page_title' => __('Privacy Policy For Virtuard Reality Design'),
            'page_description' => __('This Privacy Policy Explains How Virtuard Collects, Uses, And Protects Your Personal Information When You Use Our Platform.'),
        ];

        return view('v2.page.privacy-policy', $data);
    }
}
