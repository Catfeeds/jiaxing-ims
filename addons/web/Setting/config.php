<?php 
return [
    "name" => "系统配置",
    "order" => 255,
    "version" => "1.0",
    "description" => "系统模块",
    "access" => [
        1 => "本人",
        2 => "本人和下属",
        3 => "部门所有人",
        4 => "所有人"
    ],
    "controllers" => [
        "setting" => [
            "name" => "基础设置",
            "actions" => [
                "index" => [
                    "name" => "基本"
                ],
                "store" => [
                    "name" => "存储"
                ]
            ]
        ],
        "print" => [
            "name" => "打印模板",
            "actions" => [
                "index" => [
                    "name" => "模板显示"
                ],
                "create" => [
                    "name" => "更新模板"
                ],
                "export" => [
                    "name" => "导出模板"
                ],
                "param" => [
                    "name" => "模板参数"
                ],
                "demo" => [
                    "name" => "演示模板"
                ]
            ]
        ],
        "store" => [
            "name" => "门店",
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
        "mail" => [
            "name" => "邮件设置",
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
                "store" => [
                    "name" => "存储"
                ],
                "delete" => [
                    "name" => "删除"
                ]
            ]
        ],
        "sms" => [
            "name" => "短信设置",
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
                "store" => [
                    "name" => "存储"
                ],
                "delete" => [
                    "name" => "删除"
                ]
            ]
        ],
        "menu" => [
            "name" => "菜单设置",
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
        "widget" => [
            "name" => "部件设置",
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
        "option" => [
            "name" => "选项设置",
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
        ]
    ]
];
