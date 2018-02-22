<?php

require __DIR__.'/../app/macros.php';

$uris = [];

foreach (['module', 'controller', 'action'] as $key => $uri) {
    $uris[$uri] = Request::segment($key + 1, 'index');
}

// 初始化所有模块
Module::allWithDetails();

$path = Request::path();

// 首字母大写
$controller = studly_case($uris['controller']);

$action = 'Aike\\Wap\\'.ucfirst($uris['module']).'\\Controllers\\'.$controller.'Controller@'.$uris['action'].'Action';
$method = Request::method();

if ($method == 'GET') {
    Route::get($path, $action);
}

if ($method == 'POST') {
    Route::post($path, $action);
}

View::addLocation(resource_path('views/wap'));
View::addLocation(base_path('addons/wap/'.ucfirst(Request::module()).'/views'));
