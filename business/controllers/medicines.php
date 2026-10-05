<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/../session.php';
require_once __DIR__ . '/../services/MedicineService.php';
header('Content-Type: application/json');

/** Validation Function */
function validateMedicine($data, $isUpdate = false) {
    $errors = [];

    if (empty($data['name'])) {
        $errors['name'] = 'Medicine name is required';
    }

    if (!isset($data['price']) || !is_numeric($data['price']) || $data['price'] < 0) {
        $errors['price'] = 'Price must be a positive number';
    }

    if (!isset($data['quantity']) || !is_numeric($data['quantity']) || $data['quantity'] < 0) {
        $errors['quantity'] = 'Quantity must be a non-negative number';
    }

    if (empty($data['expiry_date'])) {
        $errors['expiry_date'] = 'Expiry date is required';
    } else if (strtotime($data['expiry_date']) <= time()) {
        $errors['expiry_date'] = 'Expiry date must be in the future';
    }

    return $errors;
}

try {
    $method = $_SERVER['REQUEST_METHOD'];

    // Pharmacists can view; only admins can add, edit or delete
    if ($method === 'GET') { require_login_json(); } else { require_admin_json(); }

    $service = new MedicineService();

    if ($method === 'GET') {
        if (isset($_GET['id'])) {
            $med = $service->getById((int)$_GET['id']);
            echo json_encode($med ?: []);
        } else {
            $filters = [];
            if (isset($_GET['q'])) $filters['q'] = $_GET['q'];
            if (isset($_GET['category'])) $filters['category'] = $_GET['category'];
            $list = $service->getAll($filters);
            echo json_encode($list);
        }
        exit;
    }

    if ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        $data['added_by'] = current_user()['user_id'] ?? null;
        $errors = validateMedicine($data);

        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'errors' => $errors]);
            exit;
        }

        $id = $service->create($data);
        echo json_encode(['success' => true, 'id' => $id]);
        exit;
    }

    if ($method === 'PUT') {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'errors' => ['id' => 'ID is required']]);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        $errors = validateMedicine($data, true);

        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'errors' => $errors]);
            exit;
        }

        $ok = $service->update((int)$id, $data);
        echo json_encode(['success' => $ok]);
        exit;
    }

    if ($method === 'DELETE') {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'errors' => ['id' => 'ID is required']]);
            exit;
        }

        $ok = $service->delete((int)$id);
        echo json_encode(['success' => $ok]);
        exit;
    }

    http_response_code(405);
    echo json_encode(['success' => false, 'errors' => 'Method not allowed']);
} catch (Exception $e) {
    http_response_code(500);
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Server error']);
}
?>