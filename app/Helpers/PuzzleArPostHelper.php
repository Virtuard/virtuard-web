<?php

namespace App\Helpers;

/**
 * Alur share PuzzleAR: post baru dulu, lalu link harus punya post_id
 * agar saat orang klik & main, tracking masuk ke post (puzzle_tracking.post_id).
 */
class PuzzleArPostHelper
{
    public static function appendPostIdToMessage($message, $postId)
    {
        if (empty($message) || (stripos($message, 'puzzleAR') === false && stripos($message, '/puzzleAR') === false)) {
            return $message;
        }
        if (preg_match('/post_id=\d+/', $message)) {
            return $message;
        }
        return preg_replace_callback(
            '#(https?://[^\s"\'<>]*puzzleAR[^\s"\'<>]*)#i',
            function ($m) use ($postId) {
                $url = $m[1];
                if (stripos($url, 'post_id=') !== false) {
                    return $url;
                }
                $sep = (strpos($url, '?') !== false) ? '&' : '?';
                return $url . $sep . 'post_id=' . (int) $postId;
            },
            $message
        );
    }
}
