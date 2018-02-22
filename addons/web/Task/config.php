<?php 
return [
    "name" => "工作-工作任务",
    "order" => 32,
    "version" => "1.0",
    "description" => "任务安排计划",
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
        "task" => [
            "name" => "任务",
            "actions" => [
                "index" => [
                    "name" => "列表",
                    "access" => 1
                ],
                "view" => [
                    "name" => "查看"
                ],
                "add" => [
                    "name" => "创建"
                ],
                "comment" => [
                    "name" => "评论"
                ],
                "analysis" => [
                    "name" => "分析"
                ],
                "audit" => [
                    "name" => "审核"
                ],
                "delete" => [
                    "name" => "删除"
                ]
            ]
        ]
    ]
];
