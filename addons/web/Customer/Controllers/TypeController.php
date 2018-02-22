<?php namespace Aike\Web\Customer\Controllers;

use DB;
use Input;

use Aike\Web\Index\Controllers\DefaultController;

class TypeController extends DefaultController
{
    // 客户类型
    public function indexAction()
    {
        // 更新排序
        if ($post = $this->post('sort')) {
            foreach ($post as $k => $v) {
                $data['sort'] = $v;
                DB::table('customer_type')->where('id', $k)->update($data);
            }
        }
        $rows = DB::table('customer_type')->get();
        $this->view->set(array(
            'rows' => $rows,
        ));
        $this->view->display();
    }

    // 添加分类
    public function addAction()
    {
        $id = (int)Input::get('id');

        if ($post = $this->post()) {
            if (empty($post['title'])) {
                return $this->error('类别名称必须填写。');
            }

            if ($post['id'] > 0) {
                DB::table('customer_type')->where('id', $post['id'])->update($post);
            } else {
                DB::table('customer_type')->insert($post);
            }
            return $this->success('index', '保存成功。');
        }

        $row = DB::table('customer_type')->where('id', $id)->first();
        $this->view->set(array(
            'row'  => $row,
        ));
        $this->view->display();
    }

    //删除产品类别
    public function deleteAction()
    {
        $id = Input::get('id');
        if ($id <= 0) {
            return $this->error('编号不正确无法显示。');
        }
        $row = DB::table('customer_type')->where('id', $id)->delete();
        return $this->success('index', '删除成功');
    }
}
