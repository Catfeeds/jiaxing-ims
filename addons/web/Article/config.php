<?php 
return [
    "name" => "公告",
    "order" => 1,
    "version" => "1.0",
    "description" => "新闻公告，企业发布内部公告，支持提醒和阅读记录。",
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
        "article" => [
            "name" => "文章",
            "actions" => [
                "index" => [
                    "name" => "列表",
                    "access" => 1
                ],
                "create" => [
                    "name" => "新建"
                ],
                "view" => [
                    "name" => "查看"
                ],
                "reader" => [
                    "name" => "阅读记录"
                ],
                "delete" => [
                    "name" => "删除"
                ]
            ]
        ]
    ]
];
