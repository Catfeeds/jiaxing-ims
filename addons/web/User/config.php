<?php 
return [
    "name" => "设置-用户-组织架构",
    "order" => 254,
    "version" => "1.0",
    "description" => "账户、职位、角色权限、部门管理。",
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
        "user" => [
            "name" => "用户",
            "actions" => [
                "index" => [
                    "name" => "列表"
                ],
                "view" => [
                    "name" => "查看"
                ],
                "add" => [
                    "name" => "新建"
                ],
                "delete" => [
                    "name" => "删除"
                ],
                "secret" => [
                    "name" => "密钥"
                ]
            ]
        ],
        "department" => [
            "name" => "部门",
            "actions" => [
                "index" => [
                    "name" => "列表"
                ],
                "view" => [
                    "name" => "查看"
                ],
                "add" => [
                    "name" => "新建"
                ],
                "delete" => [
                    "name" => "删除"
                ],
                "dialog" => [
                    "name" => "对话框"
                ]
            ]
        ],
        "role" => [
            "name" => "角色",
            "actions" => [
                "index" => [
                    "name" => "列表"
                ],
                "view" => [
                    "name" => "查看"
                ],
                "add" => [
                    "name" => "新建"
                ],
                "config" => [
                    "name" => "控制器权限"
                ],
                "delete" => [
                    "name" => "删除"
                ],
                "dialog" => [
                    "name" => "对话框"
                ]
            ]
        ],
        "group" => [
            "name" => "用户组",
            "actions" => [
                "index" => [
                    "name" => "列表"
                ],
                "add" => [
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
        "position" => [
            "name" => "用户职位",
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
