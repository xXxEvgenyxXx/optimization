<?php

namespace App\Models;

use App\Core\Database;

class Product {
    public function all(): array {
        return Database::pdo()
        -> query("SELECT * FROM product")
        -> fetchAll();
    }

    public function find(int $id): ?array {
        $stm = Database::pdo() -> prepare ("SELECT * FROM product WHERE id=?");

        $stm -> execute([$id]);

        return $stm -> fetchAll() ?:null;
    }
}
?>