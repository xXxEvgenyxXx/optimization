<?php

namespace App\Core;

use PDO;

class Database {
    private static ?PDO $pdo = null;

    public static function pdo():PDO {
        if(self::$pdo === null){
            $config = require __DIR__.'/../../config/db.php';

            $dsn = "mysql::host={$config['host']}; dbname={$config['dbname']}; charset={$config['charset']}";

            self::$pdo = new PDO(
                $dsn,
                $config['user'],
                $config['password'],
            );
        }

        return self::$pdo;
    }
}

?>