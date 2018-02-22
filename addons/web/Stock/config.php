<?php 
return [
    "name" => "库存",
    "order" => 41,
    "version" => "1.0",
    "description" => "产品列表,产品类别,库存类型,仓库类别,库存管理,仓库列表。",
    "icons" => [
        16 => "images/16.png",
        48 => "images/48.png",
        128 => "images/128.png"
    ],
    "listens" => [
        //['supplier_price', 'Aike\Web\Supplier\Hooks\Price'],
        //['supplier_price_data', 'Aike\Web\Supplier\Hooks\PriceData'],
    ],
    'dialogs' => [
        'supplier' => [
            'name'  => '供应商',
            'table' => 'supplier',
            'join'  => 'user',
            'field' => 'user.name',
            'url'   => 'stock/supplier/dialog',
        ],
    ],
    "access" => [
        1 => "本人",
        2 => "本人和下属",
        3 => "部门所有人",
        4 => "所有人"
    ],
    "controllers" => [
        "product" => [
            "name" => "商品",
            "actions" => [
                "index" => [
                    "name" => "列表"
                ],
                "add" => [
                    "name" => "新建"
                ],
                "export" => [
                    "name" => "导出"
                ],
                "delete" => [
                    "name" => "删除"
                ],
                "dialog" => [
                    "name"   => "对话框",
                    "access" => 1
                ]
            ]
        ],
        "category" => [
            "name" => "商品类别",
            "actions" => [
                "index" => [
                    "name" => "列表"
                ],
                "add" => [
                    "name" => "新建"
                ],
                "delete" => [
                    "name" => "删除"
                ],
                "dialog" => [
                    "name"   => "对话框",
                ]
            ]
        ],
        "price" => [
            "name" => "产品单价",
            "actions" => [
                "index" => [
                    "name" => "列表"
                ],
                "add" => [
                    "name" => "新建"
                ],
                "delete" => [
                    "name" => "删除"
                ]
            ]
        ],
        "stock" => [
            "name" => "库存管理",
            "actions" => [
                "index" => [
                    "name" => "进出存列表",
                    "access" => 1
                ],
                "create" => [
                    "name" => "成品出入库单"
                ],
                "report" => [
                    "name" => "进出存汇总表"
                ],
                "view" => [
                    "name" => "查看"
                ],
                "merge" => [
                    "name" => "合并"
                ],
                "export" => [
                    "name" => "导出"
                ],
                "delete" => [
                    "name" => "删除"
                ]
            ]
        ],
        "warehouse" => [
            "name" => "仓库类别",
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
                "delete" => [
                    "name" => "删除"
                ]
            ]
        ],
        "supplier" => [
            "name" => "供应商",
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
                "delete" => [
                    "name" => "删除"
                ]
            ]
        ],
        "purchase" => [
            "name" => "采购",
            "actions" => [
                "guide" => [
                    "name" => "引导"
                ],
                "index" => [
                    "name" => "列表"
                ],
                "create" => [
                    "name" => "新建"
                ],
                "edit" => [
                    "name" => "编辑"
                ],
                "delete" => [
                    "name" => "删除"
                ]
            ]
        ],
        "type" => [
            "name" => "库存类型",
            "actions" => [
                "index" => [
                    "name" => "列表"
                ],
                "add" => [
                    "name" => "新建"
                ],
                "delete" => [
                    "name" => "删除"
                ]
            ]
        ]
    ]
];
