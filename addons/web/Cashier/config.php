<?php 
return [
    "name" => "收银开单",
    "order" => 11,
    "version" => "1.0",
    "description" => "收银开单",
    "access" => [
        1 => "本人",
        2 => "本人和下属",
        3 => "部门所有人",
        4 => "所有人"
    ],
    "controllers" => [
        "cashier" => [
            "name" => "收银开单",
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
        ]
    ]
];
