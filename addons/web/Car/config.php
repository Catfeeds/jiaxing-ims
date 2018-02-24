<?php 
return [
    "name" => "车辆",
    "order" => 11,
    "version" => "1.0",
    "description" => "车辆管理",
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
        "car" => [
            "name" => "车辆",
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
        "brand" => [
            "name" => "车牌",
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
        "type" => [
            "name" => "车型",
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
        "plate" => [
            "name" => "车牌",
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
        ]
    ]
];
