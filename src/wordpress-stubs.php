<?php

// Only for PHPStan
if (!function_exists('add_shortcode')) {
    function add_shortcode(string $tag, callable $callback): void {
        // Do Nothing
    }
}