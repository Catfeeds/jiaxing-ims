<?php namespace Aike\Web\Supplier;

use DB;
use Aike\Web\Index\BaseModel;

class Yonyou extends BaseModel
{
    protected $table = 'supplier_yonyou';
    
    public function supplier()
    {
        return $this->belongsTo('Aike\Web\Supplier\Supplier');
    }

    public function datas()
    {
        return $this->hasMany('Aike\Web\Supplier\YonyouData');
    }

    /**
     * 获取库存量及出入库详细产品列表
     *
     * 1.获取所有库存: balance();
     * 2.获取本期库存: balance('2013-07-01',2013-07-31');
     * 2.获取上期库存: balance('2013-07-31');
     */
    public static function balance($start = '', $end = '')
    {
        $model = DB::table('supplier_stock as ss')
        ->LeftJoin('supplier_stock_data as ssd', 'ss.id', '=', 'ssd.stock_id')
        ->groupBy('ssd.product_id')
        ->selectRaw('ssd.product_id,SUM(ssd.quantity) as quantity');
    
        // 获取本期库存
        if ($start && $end) {
            $model->whereRaw('ss.created_at between ? and ?', [strtotime($start), strtotime($end) + 86400]);
        } elseif ($start) {
            // 获取上期库存
            $model->whereRaw('ss.created_at < ?', [strtotime($start)]);
        }
        $rows = $model->get()->toArray();

        $data = [];
        if (is_array($rows)) {
            // 将入库和出库分开计算
            foreach ($rows as $row) {
                $product_id = $row['product_id'];
                $data[1][$product_id] = $row['quantity'];
            }
        }
        return $data;
    }
}
