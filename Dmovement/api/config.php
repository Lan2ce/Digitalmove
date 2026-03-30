<?php
// Genostra API Config
define('DB_HOST', 'localhost');
define('DB_NAME', 'genostra');
define('DB_USER', 'root');
define('DB_PASS', '');

define('AES_KEY', 'c1kgVioySoUVimtw'); // From JS

define('API_SECRET', 'your_secret_key_here'); // Change this

// i18n messages (subset from JS)
$i18n = [
    'en_US' => ['common' => ['loginInvalid' => 'Login invalid', 'systemExcept' => 'System exception']]
    // Add more...
];

session_start();
header('Content-Type: application/json');
?>
