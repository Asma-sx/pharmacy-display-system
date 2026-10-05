<?php
// Server-side session: the server, not the browser, decides who is logged in and what they can do.
if (session_status() === PHP_SESSION_NONE) {
  session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
  session_start();
}

function current_user() {
  return $_SESSION['user'] ?? null;
}

function require_login_json() {
  if (!current_user()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Please log in first.']);
    exit;
  }
}

function require_admin_json() {
  require_login_json();
  if ((current_user()['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Admins only.']);
    exit;
  }
}

function require_login_page() {
  if (!current_user()) {
    header('Location: ' . PAGE_LOGIN);
    exit;
  }
}

function require_admin_page() {
  require_login_page();
  if ((current_user()['role'] ?? '') !== 'admin') {
    header('Location: ' . PAGE_MEDICINES);
    exit;
  }
}
