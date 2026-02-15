<?php
namespace App\Http\Controllers;

use App\Models\PuzzleConfig;
use App\Models\PuzzleTracking;
use Illuminate\Http\Request;

class PuzzleController extends Controller
{
    public function index(Request $request)
    {
        $title = $request->title ?? 'Puzzle AR';
        $img = $request->img ?? 'https://virtuard.com/uploads/0000/1/2026/02/14/logodefinitivo1.png';
        // Get active configuration from database
        $config = PuzzleConfig::getActive();
        
        // Get user agent and platform
        $userAgent = $request->header('User-Agent');
        $platform = $this->detectPlatform($userAgent);
        $isAndroid = stripos($userAgent, 'android') !== false;
        $isIos = stripos($userAgent, 'iphone') !== false || stripos($userAgent, 'ipad') !== false;
        
        // Check if config is active
        $isActive = $config && $config->is_active;
        
        // Track page view
        PuzzleTracking::track('view', [
            'platform' => $platform,
            'user_agent' => $userAgent,
            'ip_address' => $request->ip(),
            'referrer' => $request->header('referer'),
            'query_params' => $request->all(),
        ]);
        
        // Prepare data with conditional values
        $androidPackage = $isActive ? $config->android_package : '';
        $androidStoreLink = $isActive ? $config->android_store_link : '';
        $iosStoreLink = $isActive ? $config->ios_store_link : '';
        $iosDeepLinkScheme = $isActive ? $config->ios_deep_link_scheme : 'virtuardpuzzle';
        $androidDeepLinkScheme = $isActive ? $config->android_deep_link_scheme : 'https';
        $webGameUrl = $isActive ? $config->web_game_url : '';
        
        // Construct Deep Link (only if config is active)
        $deepLink = '';
        $iosDeepLink = '';
        $isSharingMode = false;
        if ($isActive) {
            $currentDomain = $request->getHost();
            $queryString = http_build_query($request->all());
            $deepLink = "intent://{$currentDomain}/puzzleAR?{$queryString}#Intent;scheme={$androidDeepLinkScheme};package={$androidPackage};end";
            $iosDeepLink = "{$iosDeepLinkScheme}://open?{$queryString}";
            $isSharingMode = true;
        }
        
        // Prepare data array
        $data = [
            'title' => $title,
            'imgUrl' => $img,
            'config' => $isActive ? $config : null,
            'isSharingMode' => $isSharingMode,
            'androidStoreLink' => $androidStoreLink,
            'iosStoreLink' => $iosStoreLink,
            'deepLink' => $deepLink,
            'iosDeepLink' => $iosDeepLink,
            'isAndroid' => $isAndroid,
            'isIos' => $isIos,
            'androidPackage' => $androidPackage,
            'platform' => $platform,
            'webGameUrl' => $webGameUrl,
        ];
        
        return view('puzzle.index', $data);
    }

    /**
     * Track click events (for AJAX calls)
     */
    public function trackClick(Request $request)
    {
        $config = PuzzleConfig::getActive();
        $userAgent = $request->header('User-Agent');
        $platform = $this->detectPlatform($userAgent);
        
        PuzzleTracking::track('click', [
            'platform' => $platform,
            'user_agent' => $userAgent,
            'ip_address' => $request->ip(),
            'referrer' => $request->header('referer'),
            'img_url' => $request->input('img_url'),
            'title' => $request->input('title'),
            'deep_link_used' => $request->input('deep_link'),
            'redirect_url' => $request->input('redirect_url'),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Detect platform from user agent
     */
    private function detectPlatform($userAgent)
    {
        if (stripos($userAgent, 'android') !== false) {
            return 'android';
        } elseif (stripos($userAgent, 'iphone') !== false || stripos($userAgent, 'ipad') !== false) {
            return 'ios';
        } elseif (stripos($userAgent, 'mobile') !== false) {
            return 'mobile';
        }
        return 'desktop';
    }
}
