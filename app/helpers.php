<?php

if (! function_exists('vasset')) {
    /**
     * Asset URL with auto cache-busting timestamp based on file modification time.
     *
     * @param string $path
     * @return string
     */
    function vasset(string $path): string
    {
        $relativePath = ltrim($path, '/');
        $fullPath = public_path($relativePath);
        
        $version = file_exists($fullPath) ? filemtime($fullPath) : time();
        return '/' . $relativePath . '?v=' . $version;
    }
}
