<?php

namespace Midtrans;

/** 
 * Check PHP version.
 */
if (version_compare(PHP_VERSION, '5.4', '<')) {
    throw new Exception('PHP version >= 5.4 required');
}

// Check PHP Curl & json decode capabilities.
if (!function_exists('curl_init') || !function_exists('curl_exec')) {
    throw new Exception('Midtrans needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
    throw new Exception('Midtrans needs the JSON PHP extension.');
}

// Configurations
require_once dirname(__FILE__) . '/Config.php';

// Midtrans API Resources
require_once dirname(__FILE__) . '/Transaction.php';

// Plumbing
require_once dirname(__FILE__) . '/ApiRequestor.php';
require_once dirname(__FILE__) . '/Notification.php';
require_once dirname(__FILE__) . '/CoreApi.php';
require_once dirname(__FILE__) . '/Snap.php';

// Sanitization
require_once dirname(__FILE__) . '/Sanitizer.php';
