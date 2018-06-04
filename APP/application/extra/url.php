<?php
$protocal = (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') ? 'https://' : 'http://';
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'apply.tiaozhan.com';
$fe = getenv('PHP_URL_FE');
$be = getenv('PHP_URL_BE');
// $fe = $fe ? $fe : $protocal . $host;
// $be = $be ? $be : $protocal . $host;

return [
    'frontend' => $fe,
    'froutend_login' => $fe,
    'froutend_logout' => $fe,
    'backend' => $be,
    'backend_login_callback' => $be . '/api/v3/user/callback',
];
