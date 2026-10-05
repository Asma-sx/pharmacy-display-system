<?php
require_once __DIR__ . '/../../data/DB.php';

class MedicineService {
    private $pdo;

    public function __construct(){
        $this->pdo = DB::connect();
    }

    /**
     * Get all medicines (with optional search & category filter)
     * يستخدم SP: sp_get_medicines
     */
    public function getAll(array $filters = []) : array {
        $q        = $filters['q']        ?? null;
        $category = $filters['category'] ?? null;

        // CALL stored procedure
        $stmt = $this->pdo->prepare("CALL sp_get_medicines(?, ?)");
        $stmt->execute([$q, $category]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor(); // مهم مع CALL

        return $rows;
    }

    /**
     * Get a single medicine by id
     * يستخدم SP: sp_get_medicine_by_id
     */
    public function getById(int $id) : ?array {
        $stmt = $this->pdo->prepare("CALL sp_get_medicine_by_id(?)");
        $stmt->execute([$id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $row ?: null;
    }

    /**
     * Create medicine
     * يستخدم SP: sp_add_medicine
     * ويرجع id السجل الجديد
     */
    public function create(array $d) : int {
    // SP: sp_add_medicine(
    //  p_name, p_brand, p_category, p_description,
    //  p_active_ingredients, p_warnings,
    //  p_price, p_quantity, p_expiry_date,
    //  p_image_url, p_added_by
    // )
    $stmt = $this->pdo->prepare("CALL sp_add_medicine(?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->execute([
        $d['name'],
        $d['brand'] ?? null,
        $d['category'] ?? null,
        $d['description'] ?? null,
        $d['active_ingredients'] ?? null,
        $d['warnings'] ?? null,
        isset($d['price']) && $d['price'] !== '' ? $d['price'] : 0,
        isset($d['quantity']) && $d['quantity'] !== '' ? $d['quantity'] : 0,
        $d['expiry_date'] ?? null,
        $d['image_url'] ?? null,
        $d['added_by'] ?? null
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    return isset($row['new_id']) ? (int)$row['new_id'] : 0;
}


    /**
     * Update medicine
     * يستخدم SP: sp_update_medicine
     */
    public function update(int $id, array $d) : bool {
        $stmt = $this->pdo->prepare("CALL sp_update_medicine(?,?,?,?,?,?,?,?,?,?,?)");
        $ok = $stmt->execute([
            $id,
            $d['name'],
            $d['brand'] ?? null,
            $d['category'] ?? null,
            $d['description'] ?? null,
            $d['active_ingredients'] ?? null,
            $d['warnings'] ?? null,
            isset($d['price']) && $d['price'] !== '' ? $d['price'] : 0,
            isset($d['quantity']) && $d['quantity'] !== '' ? $d['quantity'] : 0,
            $d['expiry_date'] ?? null,
            $d['image_url'] ?? null
        ]);

        $stmt->closeCursor();
        return $ok;
    }

    /**
     * Delete medicine by id
     * يستخدم SP: sp_delete_medicine
     */
    public function delete(int $id) : bool {
        $stmt = $this->pdo->prepare("CALL sp_delete_medicine(?)");
        $ok = $stmt->execute([$id]);
        $stmt->closeCursor();
        return $ok;
    }
}
