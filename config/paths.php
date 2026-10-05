<?php

// Detect the folder the app lives in, so it works on localhost (XAMPP) and on any host
$__docRoot = rtrim(str_replace('\\', '/', (string)realpath($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
$__appDir  = str_replace('\\', '/', (string)realpath(__DIR__ . '/..'));
$__base    = ($__docRoot !== '' && strpos($__appDir, $__docRoot) === 0) ? substr($__appDir, strlen($__docRoot)) : '';
$__scheme  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
define('BASE_PATH', $__base);
define('BASE_URL', $__scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . BASE_PATH);
define('PRESENTATION_PATH', BASE_PATH . '/presentation');
define('BUSINESS_PATH', BASE_PATH . '/business');
define('API_PATH', BUSINESS_PATH . '/controllers');

define('API_AUTH', '/business/controllers/auth.php');
define('API_MEDICINES', '/business/controllers/medicines.php');


define('PAGE_HOME', PRESENTATION_PATH . '/home.php');
define('PAGE_LOGIN', PRESENTATION_PATH . '/login.php');
define('PAGE_REGISTER', PRESENTATION_PATH . '/register.php');
define('PAGE_MEDICINES', PRESENTATION_PATH . '/medicines.php');
define('PAGE_ADD_MEDICINE', PRESENTATION_PATH . '/add_medicine.php');
define('PAGE_EDIT_MEDICINE', PRESENTATION_PATH . '/edit_medicine.php');
define('PAGE_VIEW_MEDICINE', PRESENTATION_PATH . '/view_medicine.php');
define('PAGE_PROFILE', PRESENTATION_PATH . '/profile.php');
define('PAGE_ABOUT', PRESENTATION_PATH . '/about.php');
define('PAGE_CONFIRM', PRESENTATION_PATH . '/confirm.php');

function url_page($page) {
    return $page;  
}

function url_api($api) {
    return BASE_URL . $api;
}

function redirect_to($page, $params = []) {
    $url = $page;  
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    header('Location: ' . $url);
    exit;
}

function build_redirect($page, $params = []) {
    $url = $page;  
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    return $url;
}
?>