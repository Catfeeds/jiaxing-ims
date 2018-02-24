<?php namespace Aike\Web\Index;

use Auth;
use DB;

class Menu extends BaseModel
{
    protected $table = 'menu';

    /**
     * 取得菜单列表
     */
    public static function getItems()
    {
        static $data = [];

        if ($data) {
            return $data;
        }

        $menus = [
            ['id' =>'cashier', 'name' => '收银', 'icon' => 'fa-briefcase'],
            ['id' =>'cashier_cashier', 'parent' => 'cashier', 'name' => '收银开单', 'url' => 'cashier/cashier/index'],
            
            ['id' =>'stock', 'name' => '库存', 'icon' => 'fa-cube'],
            ['id' =>'stock_stock', 'parent' => 'stock', 'name' => '基础设置', 'url' => 'stock/stock/guide'],
            ['id' =>'stock_purchase', 'parent' => 'stock', 'name' => '采购入库', 'url' => 'stock/purchase/guide'],
            ['id' =>'stock_supplier', 'parent' => 'stock', 'name' => '供应商管理', 'url' => 'stock/supplier/index'],
            ['id' =>'stock_warehouse', 'parent' => 'stock', 'name' => '仓库管理', 'url' => 'stock/warehouse/index'],
            ['id' =>'stock_product', 'parent' => 'stock', 'name' => '商品管理', 'url' => 'stock/product/index'],
            ['id' =>'stock_service', 'parent' => 'stock', 'name' => '服务管理', 'url' => 'stock/service/index'],
        
            ['id' =>'setting', 'name' => '设置', 'icon' => 'fa-gear'],
            ['id' =>'setting_setting', 'parent' => 'setting', 'name' => '系统设置', 'url' => 'setting/setting/index'],
            ['id' =>'setting_user', 'parent' => 'setting', 'name' => '组织架构', 'url' => 'user/group/index'],
            //['id' =>'setting_user_index', 'parent' => 'setting_user', 'name' => '用户管理', 'url' => 'user/group/index'],
            //['id' =>'setting_role_index', 'parent' => 'setting_user', 'name' => '角色管理', 'url' => 'user/role/index'],
            ['id' =>'setting_option', 'parent' => 'setting', 'name' => '科目设置', 'url' => 'setting/option/index'],
            //['id' =>'setting_setting_index', 'parent' => 'setting_setting', 'name' => '基本设置', 'url' => 'setting/setting/index'],
        ];

        $assets = Access::getRoleAuthorise(Auth::user()->role_id);

        foreach ($menus as $menuId => &$menu) {
            if ($menu['url']) {
                if (isset($assets[$menu['url']])) {
                    $menu['selected'] = true;
                }
            }
        }
        $menus = array_tree($menus, 'name', 'id', 'parent');
        //$menus = DB::table('menu')->orderBy('lft', 'asc')->get();
        //$menus = array_tree($menus);

        $positions = [];

        foreach ($menus as $menuId => &$menu) {
            if ($menu['children']) {
                // 二级菜单
                foreach ($menu['children'] as $groupId => &$group) {
                    if ($group['url']) {
                        $group['url'] = str_replace('.', '/', $group['url']);
                        if ($group['access'] == 0 || isset($assets[$group['url']])) {
                            $menu['selected']   = 1;
                            $group['selected']  = 1;
                        }
                    }

                    if ($group['children']) {
                        
                        // 三级菜单
                        foreach ($group['children'] as $actionId => &$action) {
                            $action['url'] = str_replace('.', '/', $action['url']);

                            $positions[$action['url']] = $menuId.','.$groupId.','.$actionId;

                            if ($action['access'] == 0 || isset($assets[$action['url']])) {
                                if (empty($group['url'])) {
                                    $group['url'] = $action['url'];
                                }
                                $menu['selected']   = 1;
                                $group['selected']  = 1;
                                $action['selected'] = 1;
                            }
                        }
                    }
                }
            }
        }
        //$menus[0]['selected'] = 0;
        $data['children'] = $menus;
        $data['left']     = $left;

        return $data;
    }
}
