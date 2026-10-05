<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */


require_once __DIR__ . '/../../data/DB.php';

class AuthService {
  private $pdo;
  public function __construct(){ $this->pdo = DB::connect(); }

  public function register($data) {
    if (empty($data['username']) || empty($data['password'])) {
      return ['error' => 'username and password required'];
    }
    if (strlen($data['password']) < 6) {
      return ['error' => 'password must be at least 6 characters'];
    }
    // Admin accounts are created by the database owner only; public sign-up creates pharmacists.
    // check exists
    $stmt = $this->pdo->prepare("SELECT user_id FROM users WHERE username = ?");
    $stmt->execute([$data['username']]);
    if ($stmt->fetch()) return ['error' => 'username exists'];

    $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
    $stmt = $this->pdo->prepare("INSERT INTO users (username, password_hash, role, full_name) VALUES (?, ?, ?, ?)");
    $stmt->execute([$data['username'], $password_hash, 'pharmacist', $data['full_name'] ?? null]);
    return ['success' => true, 'id' => $this->pdo->lastInsertId()];
  }

  public function login($data) {
    if (empty($data['username']) || empty($data['password'])) {
      return ['error' => 'username and password required'];
    }
    $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$data['username']]);
    $user = $stmt->fetch();
    if (!$user) return ['error' => 'invalid credentials'];
    if (password_verify($data['password'], $user['password_hash'])) {
      // For simplicity return user data (in real app use session or JWT)
      unset($user['password_hash']);
      return ['success' => true, 'user' => $user];
    }
    return ['error' => 'invalid credentials'];
  }
}
