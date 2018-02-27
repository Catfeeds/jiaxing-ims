<?php

error_reporting(1);

// 建立镜像仓库
// sudo -Hu www git clone --mirror git@git.sc35.com:hawind/jiaxing-ims-web.git

$git = '/www/git/deploy/jiaxing-ims-web.git';
$web = '/www/htdocs/ims.dp-jx.com';
 
$token    = '1GpLHbLvtDuTPkWd';
$wwwUser  = 'www';
$wwwGroup = 'www';

$data = file_get_contents('php://input');
$hash = hash_hmac('sha256', $data, $token, false);

$signature = $_SERVER['HTTP_X_GOGS_SIGNATURE'];

if ($signature == $hash) {
    $json = json_decode($data, true);
    
    // $repo = $json['repository']['name'];

    // 这里不需要sudo -Hu www了，因为当前PHP运行的用户就是www
    echo shell_exec("cd $git && git remote update && git --work-tree=$web checkout master -f 2>&1");
} else {
    exit('signature error');
}
