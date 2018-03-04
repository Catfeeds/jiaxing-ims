<?php namespace Aike\Web\Index;

use Auth;
use DB;

class Menu extends BaseModel
{
    protected $table = 'menu';

    /**
     * 取得菜单列表
     */
    public static function getTabs($key)
    {
        $tabs['stock.purchase'] = [
            ['name'=>'采购统计', 'url' => 'stock/purchase/home'],
            ['name'=>'采购单', 'url' => 'stock/purchase/create'],
            ['name'=>'采购单据', 'url' => 'stock/purchase/index'],
            ['name'=>'采购明细', 'url' => 'stock/purchase/line'],
            ['name'=>'作废单据', 'url' => 'stock/purchase/invalid'],
            ['name'=>'还款记录', 'url' => 'stock/purchase-repayment/index'],
        ];
        $tabs['stock.requisition'] = [
            ['name'=>'领料统计', 'url' => 'stock/requisition/home'],
            ['name'=>'领料单', 'url' => 'stock/requisition/create'],
            ['name'=>'领料单据', 'url' => 'stock/requisition/index'],
            ['name'=>'领料明细', 'url' => 'stock/requisition/line'],
            ['name'=>'作废单据', 'url' => 'stock/requisition/invalid'],
        ];
        $tabs['stock.loss'] = [
            ['name'=>'报损统计', 'url' => 'stock/loss/home'],
            ['name'=>'报损单', 'url' => 'stock/loss/create'],
            ['name'=>'报损单据', 'url' => 'stock/loss/index'],
            ['name'=>'报损明细', 'url' => 'stock/loss/line'],
            ['name'=>'作废单据', 'url' => 'stock/loss/invalid'],
        ];
        $tabs['stock.purchase-return'] = [
            ['name'=>'退货统计', 'url' => 'stock/purchase-return/home'],
            ['name'=>'退货单', 'url' => 'stock/purchase-return/create'],
            ['name'=>'退货单据', 'url' => 'stock/purchase-return/index'],
            ['name'=>'退货明细', 'url' => 'stock/purchase-return/line'],
            ['name'=>'作废单据', 'url' => 'stock/purchase-return/invalid'],
        ];
        $tabs['stock.transfer'] = [
            ['name'=>'调拨统计', 'url' => 'stock/transfer/home'],
            ['name'=>'调拨单', 'url' => 'stock/transfer/create'],
            ['name'=>'调出审核', 'url' => 'stock/transfer/auditOutput'],
            ['name'=>'调入审核', 'url' => 'stock/transfer/auditInput'],
            ['name'=>'调拨单据', 'url' => 'stock/transfer/index'],
            ['name'=>'调拨明细', 'url' => 'stock/transfer/line'],
            ['name'=>'作废单据', 'url' => 'stock/transfer/invalid'],
        ];
        $tabs['stock.check'] = [
            ['name'=>'盘点统计', 'url' => 'stock/check/home'],
            ['name'=>'盘点单', 'url' => 'stock/check/create'],
            ['name'=>'盘点单据', 'url' => 'stock/check/index'],
            ['name'=>'盘点明细', 'url' => 'stock/check/line'],
            ['name'=>'作废单据', 'url' => 'stock/check/invalid'],
        ];
        $tabs['stock.product'] = [
            ['name'=>'商品管理', 'url' => 'stock/product/index'],
            ['name'=>'商品类别', 'url' => 'stock/product-category/index'],
        ];
        $tabs['stock.service'] = [
            ['name'=>'服务管理', 'url' => 'stock/service/index'],
            ['name'=>'服务类别', 'url' => 'stock/service-category/index'],
        ];
        $tabs['stock'] = [
            ['name'=>'库存统计', 'url' => 'stock/stock/count'],
            ['name'=>'库存列表', 'url' => 'stock/stock/index'],
            ['name'=>'销售出库表', 'url' => '#'],
            ['name'=>'商品销售对比', 'url' => '#'],
            ['name'=>'商品收发明细表', 'url' => 'stock/stock/line'],
            ['name'=>'库存预警', 'url' => 'stock/stock/warning'],
        ];
        $tabs['setting'] = [
            ['name'=>'基础设置', 'url' => 'setting/setting/index'],
            ['name'=>'门店设置', 'url' => 'setting/store/index'],
            ['name'=>'品牌设置', 'url' => 'car/brand/index'],
            ['name'=>'车型设置', 'url' => 'car/type/index'],
            ['name'=>'车牌设置', 'url' => 'car/plate/index'],
            ['name'=>'邮件设置', 'url' => 'setting/mail/index'],
            ['name'=>'部件设置', 'url' => 'setting/widget/index'],
        ];
        $tabs['user'] = [
            ['name'=>'用户', 'url' => 'user/user/index'],
            ['name'=>'角色', 'url' => 'user/role/index'],
            ['name'=>'用户组', 'url' => 'user/group/index'],
            ['name'=>'部门', 'url' => 'user/department/index'],
            ['name'=>'职位', 'url' => 'user/position/index'],
        ];
        $assets = Access::getRoleAuthorise(Auth::user()->role_id);
        if ($tabs[$key]) {
            $_tabs = $tabs[$key];
            foreach ($_tabs as &$tab) {
                if ($assets[$tab['url']]) {
                    $tab['selected'] = 1;
                }
            }
            return $_tabs;
        }
        return [];
    }
    
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
            ['id' =>'stock_stock', 'parent' => 'stock', 'name' => '基础设置', 'url' => 'stock/stock/home'],
            ['id' =>'stock_purchase', 'parent' => 'stock', 'name' => '采购入库', 'url' => 'stock/purchase/home'],
            ['id' =>'stock_count', 'parent' => 'stock', 'name' => '库存统计', 'url' => 'stock/stock/count'],
            ['id' =>'stock_requisition', 'parent' => 'stock', 'name' => '领料出库', 'url' => 'stock/requisition/home'],
            ['id' =>'stock_loss', 'parent' => 'stock', 'name' => '报损出库', 'url' => 'stock/loss/home'],
            ['id' =>'stock_return', 'parent' => 'stock', 'name' => '采购退货', 'url' => 'stock/purchase-return/home'],
            ['id' =>'stock_transfer', 'parent' => 'stock', 'name' => '库存调拨', 'url' => 'stock/transfer/home'],
            ['id' =>'stock_check', 'parent' => 'stock', 'name' => '库存盘点', 'url' => 'stock/check/home'],

            ['id' =>'setting', 'name' => '设置', 'icon' => 'fa-gear'],
            ['id' =>'setting_setting', 'parent' => 'setting', 'name' => '系统设置', 'url' => 'setting/setting/index'],
            ['id' =>'setting_user', 'parent' => 'setting', 'name' => '组织架构', 'url' => 'user/user/index'],
            ['id' =>'stock_service', 'parent' => 'setting', 'name' => '服务管理', 'url' => 'stock/service/index'],
            ['id' =>'setting_option', 'parent' => 'setting', 'name' => '科目设置', 'url' => 'setting/option/index'],
        ];

        $assets = Access::getRoleAuthorise(Auth::user()->role_id);

        /*
        foreach ($menus as $menuId => &$menu) {
            if ($menu['url']) {
                if (isset($assets[$menu['url']])) {
                    $menu['selected'] = true;
                }
            }
        }
        */
        $menus = array_tree($menus, 'name', 'id', 'parent');

        foreach ($menus as $menuId => &$menu) {
            if ($menu['children']) {
                // 二级菜单
                foreach ($menu['children'] as $groupId => &$group) {
                    if ($group['url']) {
                        if ($group['selected'] == 1 || isset($assets[$group['url']])) {
                            $menu['selected']  = 1;
                            $group['selected'] = 1;
                        }
                    }

                    if ($group['children']) {
                        // 三级菜单
                        foreach ($group['children'] as $actionId => &$action) {
                            if ($action['selected'] == 1 || isset($assets[$action['url']])) {
                                $menu['selected']   = 1;
                                $group['selected']  = 1;
                                $action['selected'] = 1;
                            }
                        }
                    }
                }
            }
        }

        $data['children'] = $menus;
        $data['left']     = $left;

        return $data;
    }
}
