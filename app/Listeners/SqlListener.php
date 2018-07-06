<?php

namespace App\Listeners;


use Illuminate\Database\Events\QueryExecuted;

class SqlListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param QueryExecuted  $event
     * @return void
     */
    public function handle(QueryExecuted $event)
    {
        $sql = str_replace("?", '%s', $event->sql);

        $log = vsprintf($sql, $event->bindings);
        $log = '['. date('Y-m-d H:i:s') . '] '. $log . "\r\n";
        $file_path = storage_path('logs/sql_'. date('Y-m-d') . '.log');

        file_put_contents($file_path, $log, FILE_APPEND);
    }
}
