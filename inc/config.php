<?php

/*
 * Db configs.
 */
define('HOST', 'localhost');
define('PORT', 3306);
define('DATABASE', 'db_test'); //''); ykpymexh_zlider
define('USERNAME', 'root'); // 'ykpymexh');
define('PASSWORD', 'r00t'); // 'Killemall88(!)');
define('CHARSET', 'utf8');

/*
 * Upload configs.
 */
define('UPLOAD_DIR', 'uploads');
define('UPLOAD_MAX_FILE_SIZE', 10485760); // 10MB.
define('UPLOAD_ALLOWED_MIME_TYPES', [
    'image/jpeg',
    'image/png',
    'image/gif',
]);

/*
 * Css vendor prefixes.
 * 
 * @link https://developer.mozilla.org/en-US/docs/Glossary/Vendor_Prefix Vendor Prefix.
 */
define('CSS_VENDOR_PREFIXES', [
    '-webkit-', // Chrome, Safari, newer versions of Opera, almost all iOS browsers;
    '-moz-', // Firefox.
    '-o-', // Old, pre-WebKit, versions of Opera.
    '-ms-', // Internet Explorer and Microsoft Edge.
    '', // General for all browsers.
]);

// Minimal animation-delay of the slider, in seconds.
define('CSS_SLIDER_MINIMAL_ANIMATION_DELAY', 3);
