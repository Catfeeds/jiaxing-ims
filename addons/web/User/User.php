<?php namespace Aike\Web\User;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Notifications\Notifiable;

use Auth;
use Request;
use Session;
use DB;

use Aike\Web\Index\BaseModel;
use Aike\Web\Index\Menu;

class User extends BaseModel implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, Notifiable;

    protected $table = 'user';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'password_text', 'remember_token', 'auth_secret'];

    /**
     * 设置字段黑名单
     */
    protected $guarded = ['id', 'username', 'password'];

    public function department()
    {
        return $this->belongsTo('Aike\Web\User\Department');
    }

    public function leader()
    {
        return $this->belongsTo('Aike\Web\User\User', 'leader_id');
    }

    public function role()
    {
        return $this->belongsTo('Aike\Web\User\Role');
    }

    public function position()
    {
        return $this->belongsTo('Aike\Web\User\UserPosition', 'post');
    }

    /**
     * 验证权限
     */
    public function authorise($action = null, $asset_name = null)
    {
        if ($asset_name === null) {
            $asset_name = Request::module();
        }

        if ($action === null) {
            $action = Request::controller().'.'.Request::action();
        } else {
            if (substr_count($action, '.') === 0) {
                $action = Request::controller().'.'.$action;
            }
        }

        return Menu::check(Auth::user()->role_id, $action, $asset_name);
    }

    /**
     * 验证查看权限
     */
    public function authoriseAccess($action = null, $asset_name = null)
    {
        $level = User::authorise($action, $asset_name);

        $user = Auth::user();

        // 本人
        if ($level == 1) {
            return [$user->id];
        }

        // 本人和下属
        if ($level == 2) {
            $roles = Role::from(DB::raw('role as node, role as parent'))
            ->select(['node.id'])
            ->whereRaw('node.lft BETWEEN parent.lft AND parent.rgt')
            ->where('parent.id', $user->role_id)
            ->pluck('id');
            return User::whereIn('role_id', $roles)->pluck('id')->toArray();
        }

        // 部门所有人
        if ($level == 3) {
            $departments = Department::from(DB::raw('department as node, department as parent'))
            ->select(['node.id'])
            ->whereRaw('node.lft BETWEEN parent.lft AND parent.rgt')
            ->where('parent.id', $user->department_id)
            ->pluck('id');
            return User::whereIn('department_id', $departments)->pluck('id')->toArray();
        }

        return [];
    }

    /**
     * 检查动态密码
     */
    public static function wantsTotp()
    {
        if (env('AUTH_TOTP', true) == false) {
            return 0;
        }

        $auth_totp = Auth::user()->auth_totp;

        if ($auth_totp == 0) {
            return 0;
        } elseif ($auth_totp == 1 && Session::get('auth_totp') == true) {
            return 0;
        } else {
            return 1;
        }
    }

    /**
     * 取得用户列表
     */
    public static function getAll($userId = 0)
    {
        static $data = [];

        if (empty($data)) {
            $data = User::get(['id', 'department_id', 'role_id', 'username', 'nickname', 'email', 'mobile', 'birthday', 'gender'])->keyBy('id');
        }

        return $userId > 0 ? $data[$userId] : $data;
    }

    /**
    * 查询用户组
    */
    public function scopeGroup($query, $type)
    {
        $group = UserGroup::where('key', $type)->first();
        return $query->where('user.group_id', $group->id);
    }

    /**
     *
     * 传入部门编号，角色编码，用户编码，进行并集处理返回用户编号
     */
    public static function getDRU($receive_id, $status = 1)
    {
        if ($receive_id == '') {
            return [];
        }
        $receives = explode(',', str_replace(['u', 'r', 'd'], ['u_', 'r_', 'd_'], $receive_id));

        $scope = [];
        foreach ($receives as $receive) {
            list($type, $id) = explode('_', $receive);
            $scope[$type][] = $id;
        }

        return DB::table('user')
        ->where('status', $status)
        ->where(function ($q) use ($scope) {
            if ($scope['d']) {
                $q->orwhereIn('department_id', $scope['d']);
            }
            if ($scope['r']) {
                $q->orwhereIn('role_id', $scope['r']);
            }
            if ($scope['u']) {
                $q->orwhereIn('id', $scope['u']);
            }
        })->get(['id', 'role_id', 'login', 'name', 'email', 'mobile']);
    }
}
