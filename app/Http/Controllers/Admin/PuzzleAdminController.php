<?php

namespace App\Http\Controllers\Admin;

use App\Models\PuzzleConfig;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class PuzzleAdminController extends Controller
{
    public function index()
    {
        $config = PuzzleConfig::first();
        
        if (!$config) {
            // Create default config if not exists
            $config = PuzzleConfig::create([
                'android_package' => 'com.antoniorutilio.puzzle',
                'android_store_link' => 'https://play.google.com/store/apps/details?id=com.antoniorutilio.puzzle',
                'android_deep_link_scheme' => 'https',
                'ios_app_id' => '',
                'ios_store_link' => '',
                'ios_deep_link_scheme' => 'virtuardpuzzle',
                'web_game_url' => '',
                'is_active' => true,
            ]);
        }

        $data = [
            'row' => $config,
            'breadcrumbs' => [
                [
                    'name' => __('Puzzle AR'),
                    'url' => route('admin.puzzle.config.index')
                ],
                [
                    'name' => __('Configuration'),
                    'class' => 'active'
                ],
            ],
            'page_title' => __('Puzzle AR Configuration'),
        ];

        return view('admin.puzzle.config', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'android_package' => 'required|string|max:255',
            'android_store_link' => 'required|url|max:500',
            'android_deep_link_scheme' => 'required|string|max:100',
            'ios_app_id' => 'nullable|string|max:100',
            'ios_store_link' => 'nullable|url|max:500',
            'ios_deep_link_scheme' => 'nullable|string|max:100',
            'web_game_url' => 'nullable|url|max:500',
            'is_active' => 'boolean',
        ]);

        $config = PuzzleConfig::first();
        
        if (!$config) {
            $config = new PuzzleConfig();
        }

        $config->fill($request->only([
            'android_package',
            'android_store_link',
            'android_deep_link_scheme',
            'ios_app_id',
            'ios_store_link',
            'ios_deep_link_scheme',
            'web_game_url',
        ]));
        
        // Handle is_active checkbox (if not checked, it won't be in request)
        $config->is_active = $request->has('is_active') && $request->is_active ? true : false;

        $config->save();

        return redirect()->route('admin.puzzle.config.index')
            ->with('success', __('Configuration saved successfully!'));
    }
}
