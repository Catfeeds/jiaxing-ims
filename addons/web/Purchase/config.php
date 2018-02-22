<?php
return [
    "name" => "原辅料采购",
    "order" => 101,
    "version" => "1.0",
    "description" => "原辅料采购。",
    "icons" => [
        16 => "images/16.png",
        48 => "images/48.png",
        128 => "images/128.png"
    ],
    "listens" => [
        ['purchase_plan', 'Aike\Web\Purchase\Hooks\Plan'],
        ['purchase_plan_data', 'Aike\Web\Purchase\Hooks\PlanData'],
        ['purchase_order', 'Aike\Web\Purchase\Hooks\Order'],
        ['purchase_order_data', 'Aike\Web\Purchase\Hooks\OrderData'],
    ],
    'dialogs' => [
        'purchase_supplier' => [
            'name'  => '原辅料供应商',
            'table' => 'supplier',
            'join'  => 'user',
            'field' => 'user.nickname',
            'url'   => 'purchase/supplier/dialog',
        ],
    ],
    "access" => [
        1 => "本人",
        2 => "本人和下属",
        3 => "部门所有人",
        4 => "所有人"
    ],
    "controllers" => [
        "supplier" => [
            "name" => "供应商",
            "actions" => [
                "index" => [
                    "name" => "列表",
                    "access" => 1
                ],
                "show" => [
                    "name" => "显示"
                ],
                "create" => [
                    "name" => "新建"
                ],
                "delete" => [
                    "name" => "删除"
                ]
            ]
        ],
        "plan" => [
            "name" => "原辅料采购计划",
            "actions" => [
                "index" => [
                    "name" => "列表"
                ],
                "create" => [
                    "name" => "新建"
                ],
                "edit" => [
                    "name" => "编辑"
                ],
                "order" => [
                    "name" => "分单"
                ],
                "show" => [
                    "name" => "显示"
                ],
                "status" => [
                    "name" => "状态"
                ],
                "delete" => [
                    "name" => "删除"
                ]
            ]
        ],
        "order" => [
            "name" => "原辅料采购订单",
            "actions" => [
                "index" => [
                    "name" => "列表"
                ],
                "create" => [
                    "name" => "新建"
                ],
                "show" => [
                    "name" => "显示"
                ],
                "print" => [
                    "name" => "打印"
                ],
                "status" => [
                    "name" => "状态"
                ],
                "delete" => [
                    "name" => "删除"
                ]
            ]
        ]
    ]
];
