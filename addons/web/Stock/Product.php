<?php namespace Aike\Web\Stock;

use DB;
use Aike\Web\Index\BaseModel;

class Product extends BaseModel
{
    protected $table = 'product';

    /**
     * 获取当前启用的产品列表
     */
    public static function gets($category_id = 0, $product_id = 0, $status = 1, $search = array())
    {
        $db = DB::table('product AS p')
        ->whereRaw('p.status=?', [$status]);

        if ($category_id) {
            $category_id = is_array($category_id) ? $category_id : explode(',', $category_id);
            $db->whereIn(DB::raw('p.category_id'), $category_id);
        }
        if ($product_id > 0) {
            $db->whereRaw('p.id=?', [$product_id]);
        }

        // 搜索产品
        if (!empty($search['key']) && !empty($search['value'])) {
            $value = $search['condition'] == 'like' ? '%'.$search['value'].'%' : $search['value'];
            $db->whereRaw($search['key'].' '.$search['condition'].'?', [$value]);
        }
        $rows = $db->LeftJoin('product_category AS pc', DB::raw('pc.id'), '=', DB::raw('p.category_id'))
        ->groupBy(\DB::raw('p.id'))
        ->orderByRaw('pc.lft ASC,p.sort ASC')
        ->selectRaw('p.*,pc.name AS category_name')
        ->get();

        $data = [];
        if (sizeof($rows)) {
            foreach ($rows as $row) {
                $data[$row['id']] = $row;
            }
        }
        unset($rows);
        return $data;
    }
}
