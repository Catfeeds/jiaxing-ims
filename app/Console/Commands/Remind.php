<?php namespace App\Console\Commands;

use Illuminate\Console\Command;

use Log;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use DB;

class Remind extends Command
{
    protected $name = 'remind';

    protected $description = 'remind log';

    public function handle()
    {
        $date = strtotime(date('Y-m-d H:i'));
        $rows = DB::table('remind_log')
        ->where('date', $date)
        ->get();

        foreach ($rows as $row) {
            $row['user_id'];

            // 推送

            // 短信

            // 邮箱

            // 站内
        }
    }
}
