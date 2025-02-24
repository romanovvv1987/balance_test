<?php
namespace App\Infrastructure\Database;

use PDO;

/**
 *  DatabaseConnection Singleton
 */
class DatabaseConnection
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $dbPath = __DIR__ . '/../../../db/database.sqlite';
            self::$connection = new PDO("sqlite:$dbPath");
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$connection->exec('PRAGMA foreign_keys = ON');
        }
        return self::$connection;
    }
}