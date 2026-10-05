<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */



class DB {
  private static $pdo = null;

  public static function connect() {
    if (self::$pdo === null) {
      $cfg = include __DIR__ . '/config.php';
      $dsn = "mysql:host={$cfg['host']};dbname={$cfg['dbname']};charset=utf8mb4";
      try {
        self::$pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
      } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        die("Database connection failed.");
      }
    }
    return self::$pdo;
  }
}
