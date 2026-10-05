<?php
require_once __DIR__ . '/../session.php';
require_once __DIR__ . '/../services/AuthService.php';
header('Content-Type: application/json');

$auth = new AuthService();
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'login'; // ?action=register | login | logout

if ($method === 'POST' && $action === 'logout') {
  $_SESSION = [];
  session_destroy();
  echo json_encode(['success' => true]);
  exit;
}

if ($method === 'POST') {
  $data = json_decode(file_get_contents('php://input'), true) ?? [];

  if ($action === 'register') {
    echo json_encode($auth->register($data));
    exit;
  }

  $result = $auth->login($data);
  if (!empty($result['success'])) {
    session_regenerate_id(true);
    $_SESSION['user'] = $result['user'];
  }
  echo json_encode($result);
  exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Method not allowed']);
