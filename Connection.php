<?php

namespace DB;

use PDO;

class Connection
{
    public static function make()
    {
        $config = include '../config.php';
        $config = $config['database'];

        return new PDO(
            "{$config['connect']};dbname={$config['databasename']}",
            $config['username'],
            $config['password']
        );
    }
}