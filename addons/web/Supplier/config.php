<?php

return [
    "name" => "采购",
    "order" => 101,
    "version" => "1.0",
    "description" => "采购模块。",
    "listens" => [
        ['supplier_price', 'Aike\Web\Supplier\Hooks\Price'],
        ['supplier_price_data', 'Aike\Web\Supplier\Hooks\PriceData'],
    ],
    'dialogs' => [
        'supplier' => [
            'name'  => '包材供应商',
            'table' => 'supplier',
            'join'  => 'user',
            'field' => 'user.nickname',
            'url'   => 'supplier/supplier/dialog',
        ],
    ],
    "icons" => [
        16 => "images/16.png",
        48 => "images/48.png",
        128 => "images/128.png"
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
                "search" => [
                    "name" => "高级搜索"
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
        "contact" => [
            "name" => "联系人",
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
        "budget" => [
            "name" => "预算管理",
            "actions" => [
                "index" => [
                    "name" => "列表",
                    "access" => 1
                ],
                "create" => [
                    "name" => "创建"
                ],
                "summary" => [
                    "name" => "汇总"
                ],
                "show" => [
                    "name" => "查看"
                ],
                "delete" => [
                    "name" => "删除"
                ]
            ]
        ],
        "plan" => [
            "name" => "计划管理",
            "actions" => [
                "index" => [
                    "name" => "列表",
                    "access" => 1
                ],
                "create" => [
                    "name" => "新建"
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
            "name" => "订单管理",
            "actions" => [
                "index" => [
                    "name" => "列表",
                    "access" => 1
                ],
                "create" => [
                    "name" => "新建"
                ],
                "show" => [
                    "name" => "显示"
                ],
                "status" => [
                    "name" => "状态"
                ],
                "count" => [
                    "name" => "统计"
                ],
                "overtime" => [
                    "name" => "超时送货"
                ],
                "count_show" => [
                    "name" => "统计显示"
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
                    "name" => "列表",
                    "access" => 1
                ],
                "report" => [
                    "name" => "汇总",
                    "access" => 1
                ],
                "create" => [
                    "name" => "新建"
                ],
                "show" => [
                    "name" => "显示"
                ],
                "delete" => [
                    "name" => "删除"
                ]
            ]
        ],
        "inventory" => [
            "name" => "库存登记",
            "actions" => [
                "index" => [
                    "name" => "列表",
                    "access" => 1
                ],
                "report" => [
                    "name" => "汇总",
                    "access" => 1
                ],
                "create" => [
                    "name" => "新建"
                ],
                "show" => [
                    "name" => "显示"
                ],
                "delete" => [
                    "name" => "删除"
                ]
            ]
        ],
        "quality" => [
            "name" => "质量管理",
            "actions" => [
                "index" => [
                    "name" => "列表",
                    "access" => 1
                ],
                "report" => [
                    "name" => "汇总",
                    "access" => 1
                ],
                "create" => [
                    "name" => "新建"
                ],
                "show" => [
                    "name" => "显示"
                ],
                "delete" => [
                    "name" => "删除"
                ]
            ]
        ],
        "product" => [
            "name" => "商品管理",
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
                "import" => [
                    "name" => "导入"
                ],
                "delete" => [
                    "name" => "删除"
                ]
            ]
        ],
        "price" => [
            "name" => "价格管理",
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
                "edit" => [
                    "name" => "编辑"
                ],
                "delete" => [
                    "name" => "删除"
                ]
            ]
        ],
        "settlement" => [
            "name" => "结算管理",
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
        "purchase-plan" => [
            "name" => "原辅料采购计划",
            "actions" => [
                "index" => [
                    "name" => "列表"
                ],
                "create" => [
                    "name" => "新建"
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
        "purchase-order" => [
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
                "status" => [
                    "name" => "状态"
                ],
                "delete" => [
                    "name" => "删除"
                ]
            ]
        ],
        "product-category" => [
            "name" => "商品类别",
            "actions" => [
                "index" => [
                    "name" => "列表"
                ],
                "show" => [
                    "name" => "查看"
                ],
                "create" => [
                    "name" => "新建"
                ],
                "delete" => [
                    "name" => "删除"
                ]
            ]
        ]
    ]
];
