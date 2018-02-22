<?php namespace App\Console\Commands\Clear;

use Illuminate\Console\Command;

use DB;

class Document extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'clear:document';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '清除6个月前的一些图片资料';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $month = strtotime('-6 month');

        $upload_path = upload_path();

        // 删除6月前的经销门店资料
        $stores = DB::table('store')->where('add_time', '<=', $month)->take(10)->get();
        if (is_array($stores)) {
            foreach ($stores as $store) {
                $attachment = $store['attachment_0'].','.$store['attachment_1'].','.$store['attachment_2'].','.$store['attachment_3'];
                $attachment = array_filter(explode(',', $attachment));

                if (count($attachment)) {
                    $attachments = DB::table('store_attachment')->whereIn('id', $attachment)->get();
                    foreach ($attachments as $row) {
                        $file = $upload_path.'/'.$row['path'].'/'.$row['name'];
                        if (is_file($file)) {
                            unlink($file);
                        }
                        DB::table('store_attachment')->where('id', $row['id'])->delete();
                    }
                }
                DB::table('store')->where('id', $store['id'])->delete();
            }
        }

        // 删除6月前的销售市场巡查资料
        $markets = DB::table('inspect_market')->where('add_time', '<=', $month)->take(10)->get();

        if (is_array($markets)) {
            foreach ($markets as $market) {
                $attachment = array_filter(explode(',', $market['attachment']));
                if (count($attachment)) {
                    $attachments = DB::table('inspect_attachment')->whereIn('id', $attachment)->get();
                    foreach ($attachments as $row) {
                        $file = $upload_path.'/'.$row['path'].'/'.$row['name'];
                        if (is_file($file)) {
                            unlink($file);
                        }
                        DB::table('inspect_attachment')->where('id', $row['id'])->delete();
                    }
                }
                DB::table('inspect_market')->where('id', $market['id'])->delete();
            }
        }

        // 删除6月前的销售库存上报资料
        $stocks = DB::table('inspect_stock')->where('add_time', '<=', $month)->take(10)->get();
        if (is_array($stocks)) {
            foreach ($stocks as $stock) {
                $attachment = array_filter(explode(',', $stock['attachment']));

                if (count($attachment)) {
                    $attachments = DB::table('inspect_attachment')->whereIn('id', $attachment)->get();
                    foreach ($attachments as $row) {
                        $file = $upload_path.'/'.$row['path'].'/'.$row['name'];
                        if (is_file($file)) {
                            unlink($file);
                        }
                        DB::table('inspect_attachment')->where('id', $row['id'])->delete();
                    }
                }
                DB::table('inspect_stock')->where('id', $stock['id'])->delete();
                DB::table('inspect_stock_data')->where('inspect_stock_id', $stock['id'])->delete();
            }
        }
    }
}
