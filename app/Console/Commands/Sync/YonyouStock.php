<?php namespace App\Console\Commands\Sync;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use DB;

class YonyouStock extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'sync:yonyoustock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步用友库存';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    // 同步历史数据
    public function sync($ym)
    {
        /** 数据同步 **/
        $ch = curl_init('http://118.122.82.249:90/yonyou.php?do=summary&ym='.$ym);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);

        $_rows = json_decode($res, true);

        $sql = [];

        /*
        $_exists = DB::table('stock_yonyou_data')
        ->whereRaw('DATE_FORMAT(date,"%Y%m")=?', [$ym])
        ->get();

        foreach ($_exists as $_exist) {
            $exists[$_exist['flag']][$_exist['code']][$_exist['date']] = $_exist['id'];
        }
        */
        
        $codes = [];
        foreach ($_rows as $_row) {
            // 0是出库，1是入库
            $flag = $_row['flag'];
            $code = $_row['code'];
            $date = ($ym == '201512' ? '2015-12-31' : $_row['ymd']);
            if ($codes[$flag][$code][$date]['quantity']) {
                $codes[$flag][$code][$date]['quantity'] = $codes[$flag][$code][$date]['quantity'] + $_row['quantity'];
                $codes[$flag][$code][$date]['money'] = $codes[$flag][$code][$date]['money'] + $_row['money'];
            } else {
                $codes[$flag][$code][$date]['quantity'] = $_row['quantity'];
                $codes[$flag][$code][$date]['price']    = $_row['price'];
                $codes[$flag][$code][$date]['money']    = $_row['money'];
            }
        }

        foreach ($codes as $flag => $rows) {
            foreach ($rows as $code => $row) {
                foreach ($row as $date => $cell) {
                    $quantity = $cell['quantity'];
                    $price    = $cell['price'];
                    $money    = $cell['money'];

                    if ($flag == 1) {
                        $sql[] = "('$date','$code','$quantity','0','$flag','$price','$money')";
                    } else {
                        $sql[] = "('$date','$code','0','$quantity','$flag','$price','$money')";
                    }
                }
            }
        }
        $count = count($sql);
        unset($rows);

        // 开启事物
        DB::beginTransaction();
        try {
            
            // 删除旧数据
            DB::table('stock_yonyou_data')
            ->whereRaw('DATE_FORMAT(date,"%Y%m")=?', [$ym])
            ->delete();

            // 分段插入数据
            $chunk = array_chunk($sql, 50, true);
            foreach ($chunk as $sql) {
                DB::insert('insert into stock_yonyou_data (date, code, quantity_set, quantity_get, flag, price, money) values '. join(',', $sql));
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            echo $e->getMessage();
        }
        echo $count;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // 年月参数
        $ym = $this->argument('ym');
        $this->sync($ym);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['ym', null, InputOption::VALUE_OPTIONAL, '201512', null],
        ];
    }
}
