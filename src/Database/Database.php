<?php 

namespace Database;

use PDO;
use PDOException;

class Database 
{

    private static PDO $connection;

    public static function connect(): bool 
    {
        try {
            $dsn = 'mysql:host='.DATABASE_HOST.';dbname='.DATABASE_DB;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
            ];
            self::$connection = new PDO($dsn, DATABASE_USER, DATABASE_PW, $options);
            return true;
        } catch(PDOException $ex) {
            return false;
        }
    }

    public static function getConnection(): PDO 
    {
        return self::$connection;
    }
}