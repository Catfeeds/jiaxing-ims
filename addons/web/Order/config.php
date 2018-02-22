<?php 
return [
    "name" => "运营",
    "order" => 21,
    "version" => "1.0",
    "description" => "订单管理,销售支持,生产计划,订单类型,订单发货。",
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
        "order" => [
            "name" => "订单",
            "actions" => [
                "index" => [
                    "name" => "列表",
                    "access" => 1
                ],
                "view" => [
                    "name" => "查看"
                ],
                "add" => [
                    "name" => "新建"
                ],
                "export" => [
                    "name" => "导出"
                ],
                "syncyonyou" => [
                    "name" => "同步用友(外账)"
                ],
                "merge" => [
                    "name" => "合并"
                ],
                "part" => [
                    "name" => "拆分"
                ],
                "repeal" => [
                    "name" => "废除"
                ],
                "sendfax" => [
                    "name" => "传真订单"
                ],
                "transport" => [
                    "name" => "物流"
                ],
                "audit" => [
                    "name" => "审核"
                ],
                "print" => [
                    "name" => "打印"
                ],
                "data" => [
                    "name" => "订单数据"
                ],
                "monitor" => [
                    "name" => "监控"
                ],
                "monitor_data" => [
                    "name" => "监控数据"
                ],
                "pay" => [
                    "name" => "在线支付"
                ],
                "sync" => [
                    "name" => "订单同步"
                ],
                "product_add" => [
                    "name" => "产品添加"
                ],
                "product_edit" => [
                    "name" => "产品编辑"
                ],
                "product_delete" => [
                    "name" => "产品删除"
                ]
            ]
        ],
        "cost" => [
            "name" => "销售支持",
            "actions" => [
                "index" => [
                    "name" => "列表",
                    "access" => 1
                ]
            ]
        ],
        "plan" => [
            "name" => "生产计划",
            "actions" => [
                "index" => [
                    "name" => "订单",
                    "access" => 1
                ],
                "deliver" => [
                    "name" => "发货"
                ],
                "purchase" => [
                    "name" => "物料"
                ],
                "produce" => [
                    "name" => "生产"
                ],
                "summary" => [
                    "name" => "生产需求汇总"
                ],
                "produce_add" => [
                    "name" => "生产创建"
                ],
                "produce_state" => [
                    "name" => "生产状态"
                ],
                "count" => [
                    "name" => "订单统计"
                ],
                "coefficient" => [
                    "name" => "营运系数"
                ],
                "batch" => [
                    "name" => "生产批号"
                ],
                "materiel" => [
                    "name" => "SP汇总"
                ]
            ]
        ],
        "type" => [
            "name" => "订单类型",
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
        "transport" => [
            "name" => "订单发货",
            "actions" => [
                "index" => [
                    "name" => "列表",
                    "access" => 1
                ],
                "batch" => [
                    "name" => "批号查询",
                    "access" => 1
                ],
                "advance" => [
                    "name" => "预发",
                    "access" => 1
                ]
            ]
        ]
    ]
];
