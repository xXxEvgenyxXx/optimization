<?php

namespace App\Models;

use App\Core\Database;

class Material {
    public function all(): array {
        return Database::pdo()
        -> query("SELECT * FROM material")
        -> fetchAll();
    }

    public function find(int $id): ?array {
        $stm = Database::pdo() -> prepare ("SELECT * FROM material WHERE id=?");

        $stm -> execute([$id]);

        return $stm -> fetchAll() ?:null;
    }
}
?>