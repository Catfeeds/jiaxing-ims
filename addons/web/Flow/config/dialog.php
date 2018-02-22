<?php 
return [
    'user' => [
        'title' => '用户',
        'table' => 'user',
        'field' => 'nickname',
        'url'   => 'user/user/dialog',
    ],
    'role' => [
        'title' => '角色',
        'table' => 'role',
        'field' => 'title',
        'url'   => 'user/role/dialog',
    ],
    'department' => [
        'title' => '部门',
        'table' => 'department',
        'field' => 'title',
        'url'   => 'user/department/dialog',
    ],
    'supplier' => [
        'title' => '供应商',
        'table' => 'supplier',
        'join'  => 'user',
        'field' => 'user.nickname',
        'url'   => 'supplier/supplier/dialog',
    ],
    'customer' => [
        'title' => '客户',
        'table' => 'client',
        'join'  => 'user',
        'field' => 'user.nickname',
        'url'   => 'customer/customer/dialog',
    ],
    'customer_contact' => [
        'title' => '客户联系人',
        'table' => 'customer_contact',
        'join'  => 'user',
        'field' => 'user.nickname',
        'url'   => 'customer/contact/dialog',
    ],
    'supplier_product' => [
        'title' => '商品',
        'table' => 'product',
        'field' => 'name',
        'url'   => 'supplier/product/dialog',
    ],
    'product' => [
        'title' => '产品',
        'table' => 'product',
        'field' => 'name',
        'url'   => 'product/product/dialog',
    ],
    'promotion' => [
        'title' => '促销',
        'table' => 'promotion',
        'field' => 'number',
        'url'   => 'promotion/promotion/dialog',
    ],
    'circle' => [
        'title' => '客户圈',
        'table' => 'customer_circle',
        'field' => 'name',
        'url'   => 'customer/circle/dialog',
    ],
    'hr' => [
        'title' => '人事档案',
        'table' => 'hr',
        'field' => 'name',
        'url'   => 'hr/hr/dialog',
    ],
];
