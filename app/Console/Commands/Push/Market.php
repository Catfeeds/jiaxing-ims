<?php namespace App\Console\Commands\Push;

use Illuminate\Console\Command;

use Log;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Market extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'push:market';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '销售每日8:30';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        Log::useFiles(storage_path('logs/cron.log'));
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // 极光推送接口
        // $push = new \JPush('3ad1c40439d890e02dabd757', '275dae57c087877f24e76c64');

        // $receiver['tag'] = ['test'];
        $receiver = 'all';

        // $result = $push->send($receiver, '销售每日8:30', ['url'=>'http://daily.shenghuafood.com']);

        Log::info('销售每日8:30', json_decode($result, true));
    }
}
