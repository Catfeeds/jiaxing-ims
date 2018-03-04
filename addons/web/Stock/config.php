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
        "product-category" => [
            "name" => "商品类别",
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
        "service" => [
            "name" => "服务",
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
        "service-category" => [
            "name" => "服务类别",
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
            "name" => "采购入库",
            "actions" => [
                "home" => [
                    "name" => "首页"
                ],
                "index" => [
                    "name" => "列表"
                ],
                "show" => [
                    "name" => "显示"
                ],
                "line" => [
                    "name" => "明细"
                ],
                "create" => [
                    "name" => "新建"
                ],
                "invalid" => [
                    "name" => "作废"
                ],
                "invalidEdit" => [
                    "name" => "作废表单"
                ],
                "delete" => [
                    "name" => "删除"
                ]
            ]
        ],
        "purchase-return" => [
            "name" => "采购退货",
            "actions" => [
                "home" => [
                    "name" => "首页"
                ],
                "index" => [
                    "name" => "列表"
                ],
                "show" => [
                    "name" => "显示"
                ],
                "line" => [
                    "name" => "明细"
                ],
                "create" => [
                    "name" => "新建"
                ],
                "invalid" => [
                    "name" => "作废"
                ],
                "invalidEdit" => [
                    "name" => "作废表单"
                ],
                "delete" => [
                    "name" => "删除"
                ]
            ]
        ],
        "transfer" => [
            "name" => "库存调拨",
            "actions" => [
                "home" => [
                    "name" => "首页"
                ],
                "index" => [
                    "name" => "列表"
                ],
                "auditInput" => [
                    "name" => "调入审核"
                ],
                "auditOutput" => [
                    "name" => "调出审核"
                ],
                "show" => [
                    "name" => "显示"
                ],
                "line" => [
                    "name" => "明细"
                ],
                "create" => [
                    "name" => "新建"
                ],
                "invalid" => [
                    "name" => "作废"
                ],
                "invalidEdit" => [
                    "name" => "作废表单"
                ],
                "delete" => [
                    "name" => "删除"
                ]
            ]
        ],
        "check" => [
            "name" => "库存盘点",
            "actions" => [
                "home" => [
                    "name" => "首页"
                ],
                "index" => [
                    "name" => "列表"
                ],
                "show" => [
                    "name" => "显示"
                ],
                "line" => [
                    "name" => "明细"
                ],
                "create" => [
                    "name" => "新建"
                ],
                "invalid" => [
                    "name" => "作废"
                ],
                "invalidEdit" => [
                    "name" => "作废表单"
                ],
                "delete" => [
                    "name" => "删除"
                ]
            ]
        ],
        "purchase-repayment" => [
            "name" => "采购还款",
            "actions" => [
                "index" => [
                    "name" => "列表"
                ],
                "create" => [
                    "name" => "新建"
                ],
                "delete" => [
                    "name" => "删除"
                ]
            ]
        ],
        "requisition" => [
            "name" => "领料出库",
            "actions" => [
                "home" => [
                    "name" => "首页"
                ],
                "index" => [
                    "name" => "列表"
                ],
                "show" => [
                    "name" => "显示"
                ],
                "line" => [
                    "name" => "明细"
                ],
                "create" => [
                    "name" => "新建"
                ],
                "invalid" => [
                    "name" => "作废"
                ],
                "invalidEdit" => [
                    "name" => "作废表单"
                ],
                "delete" => [
                    "name" => "删除"
                ]
            ]
        ],
        "loss" => [
            "name" => "报损出库",
            "actions" => [
                "home" => [
                    "name" => "首页"
                ],
                "index" => [
                    "name" => "列表"
                ],
                "show" => [
                    "name" => "显示"
                ],
                "line" => [
                    "name" => "明细"
                ],
                "create" => [
                    "name" => "新建"
                ],
                "invalid" => [
                    "name" => "作废"
                ],
                "invalidEdit" => [
                    "name" => "作废表单"
                ],
                "delete" => [
                    "name" => "删除"
                ]
            ]
        ],
        "stock" => [
            "name" => "库存统计",
            "actions" => [
                "home" => [
                    "name" => "基础设置"
                ],
                "index" => [
                    "name" => "库存列表"
                ],
                "count" => [
                    "name" => "库存统计"
                ],
                "line" => [
                    "name" => "收发明细"
                ],
                "warning" => [
                    "name" => "库存预警"
                ],
                "warningEdit" => [
                    "name" => "设置预警"
                ],
                "costEdit" => [
                    "name" => "修改成本"
                ],
            ]
        ]
    ]
];
