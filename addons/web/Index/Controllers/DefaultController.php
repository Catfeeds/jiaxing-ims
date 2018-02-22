<?php namespace Aike\Web\Index\Controllers;

use Aike\Web\Index\Access;
use Aike\Web\Index\Menu;
use View;

class DefaultController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        Access::setPermissions($this->permission);

        // 登录认证和RBAC检查
        $this->middleware('auth');

        // 获取登录认证数据
        $this->middleware(function ($request, $next) {
            $this->user = $request->user();
            $this->access = Access::getNowRoleAssets();
            $menus = Menu::getItems();
            View::share([
                'menus'  => $menus,
                'access' => $this->access,
            ]);
            return $next($request);
        });
    }
}
