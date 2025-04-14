<?php
/**
 * URL Helper Functions
 * 
 * This file contains utility functions for URL handling,
 * including base URL detection and path generation.
 */

/**
 * Detect the base URL of the application
 * Takes into account if the app is in a subdirectory
 * @return string The base URL with trailing slash
 */
function getBaseUrl() {
    // We want the application to operate at the root level only
    // No subdirectory should be included in paths
    return '';
}

/**
 * Generate a full URL path with the base URL
 * @param string $path The path to append to the base URL
 * @return string The full URL
 */
function url($path = '') {
    $baseUrl = getBaseUrl();
    
    // Ensure path starts with a slash
    if (!empty($path) && $path[0] !== '/') {
        $path = '/' . $path;
    }
    
    return $baseUrl . $path;
}

// Set the base URL as a global variable for use in templates
$baseUrl = getBaseUrl(); 