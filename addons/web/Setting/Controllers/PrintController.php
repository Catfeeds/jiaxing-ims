<?php namespace Aike\Web\Setting\Controllers;

use DB;
use Input;
use Request;
use Validator;

use Aike\Web\Setting\Store;

use Aike\Web\Index\Controllers\DefaultController;

class PrintController extends DefaultController
{
    public $permission = ['template'];

    // 门店列表
    public function indexAction()
    {
        $search = search_form([
            'referer' => 1,
            'node'    => 'stock.purchase',
            'size'    => 'a4',
        ]);
        $query = $search['query'];

        return $this->display([
            'search'  => $search,
            'columns' => $columns,
            'query'   => $query,
        ]);
    }

    // 打印模板
    public function templateAction()
    {
        $node = Input::get('node');
        $size = Input::get('size');

        $data = ['store_name' => '门店名称'];
        $data['purchase_line'] = [
            ['product' => '商品名称A', 'b' => 'b1', 'c' => 'c1'],
        ];
        
        $res = 'prints/'.$node.'.'.$size.'.xlsx';
        $file = upload_path($res);
        if (!is_file($file)) {
            $file = public_path($res);
        }

        if (is_file($file)) {
            printExcel($file, $data, $size, 'html');
        } else {
            echo '<div style="margin:0 auto;">无打印模板</div>';
        }
    }

    // 导出模板
    public function exportAction()
    {
        $node = Input::get('node');
        $size = Input::get('size');
        $name = $node.'.'.$size.'.xlsx';
        $file = upload_path('prints/'.$name);
        return response()->download($file, $name);
    }

    // 演示模板
    public function demoAction()
    {
        $node = Input::get('node');
        $size = Input::get('size');
        $name = $node.'.'.$size.'.xlsx';
        $file = public_path('prints/'.$name);
        return response()->download($file, $name);
    }

    // 参数列表
    public function paramAction()
    {
        $stock_purchase[] = [
            'name' => '采购单据',
            'list' => [
                ['{store_name}','门店名称'],
                ['{date}','采购时间'],
                ['{print_time}','打印时间'],
                ['{user}','采购员'],
                ['', ''],
                ['{total_money}', '应收金额'],
                ['{discount_money}', '优惠金额'],
                ['{pay_money}', '付款金额'],
                ['{arrear_money}', '欠款金额'],
                ['{remark}', '备注'],
            ]
        ];
        $stock_purchase[] = [
            'name' => '采购单据列表',
            'list' => [
                ['purchase_line','采购列表'],
                ['',''],
                ['{k}','序号'],
                ['{product_name}','商品名称'],
                ['{product_spec}','商品规格'],
                ['{warehouse}','仓库'],
                ['{cost}','进价'],
                ['{num}','数量'],
                ['{total_cost}','合计'],
                ['{total_num}','总数量'],
                ['{remark}', '备注'],
                ['',''],
                ['{all_num}','合计数量'],
                ['{all_cost}','合计金额'],
            ]
        ];
        $params['stock.purchase'] = $stock_purchase;

        $node = Input::get('node');
        $_params = $params[$node];

        $rows = [];
        foreach ($_params as $i => $_param) {
            $rows['head'][] = $_param['name'];
            foreach ($_param['list'] as $j => $list) {
                $rows['body'][$j][$i] = $i;
            }
        }

        return $this->render([
            'rows'   => $rows,
            'params' => $_params
        ]);
    }

    // 更新模板
    public function createAction()
    {
        $gets = Input::get();

        if (Request::method() == 'POST') {
            $files = Input::file();
            $rules = [
                'file' => 'mimes:xlsx',
            ];
            $v = Validator::make($files, $rules, [], ['file' => '模板文件']);

            if ($v->fails()) {
                return $this->json($v->errors()->first());
            }

            $file = $files['file'];
            if ($file->isValid()) {
                $filename = $gets['node'].'.'.$gets['size'].'.xlsx';
                $upload_path = public_path().'/uploads/prints';
                if ($file->move($upload_path, $filename)) {
                    return $this->json('打印模板保存成功。', true);
                }
                return $this->json('打印模板保存失败。');
            }
            return $this->json('打印模板上传失败。');
        }
        return $this->render(array(
            'row'  => $row,
        ));
    }
}
