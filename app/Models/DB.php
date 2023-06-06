<?php

namespace App\Models;

use Illuminate\Support\Facades\DB as Base;

/**
 * @method static \Illuminate\Database\ConnectionInterface connection(string $name = null)
 *
 * @see \Illuminate\Database\DatabaseManager
 * @see \Illuminate\Database\Connection
 */
class DB extends Base
{
    public static function beginTransaction()
    {
        DB::connection('mysql')->beginTransaction();
    }

    public static function commit()
    {
        DB::connection('mysql')->commit();
    }

    public static function rollback()
    {
        DB::connection('mysql')->rollback();
    }
}
