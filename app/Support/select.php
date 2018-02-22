<?php

class select
{
    /**
     * 客户方法
     */
    public static function customer()
    {
        $user = Auth::user();
        $role = DB::table('role')->find($user->role_id);

        $res['role_type'] = 'all';
        $res['where']     = [];

        // 负责人
        if ($role['name'] == 'salesman') {
            $res['role_type'] = 'salesman';
            $res['where']['user.salesman_id'] = $user->id;

        // 客户
        } elseif ($role['name'] == 'client') {
            $res['role_type'] = 'customer';
            $res['where']['user.id'] = $user->id;
        }
        return $res;
    }

    /**
     * 选择圈负责客户列表
     */
    public static function circleCustomer()
    {
        $user = Auth::user();
        $role = DB::table('role')->find($user->role_id);

        $res['columns'] = [];

        // 登录账号类型
        switch ($role['name']) {
            // 业务员角色
            case 'salesman':
                // 圈审阅人
                $owner = DB::table('customer_circle')->where('owner_user_id', $user->id)->pluck('id')->toArray();
                if ($owner) {
                    $res['owner_user'] = $owner;
                }
                
                // 圈查阅人
                $assist = DB::table('customer_circle')->whereRaw(db_instr('owner_assist', $user->id))->pluck('id')->toArray();
                if ($assist) {
                    $res['owner_assist'] = $assist;
                }
                $circle = array_merge($owner, $assist);

                $res['whereIn']['client.circle_id'] = $circle;
                $res['circleIn'] = $circle;

                $res['columns'] = [
                    ['circle','client.circle_id','客户圈'],
                    ['region','user.province_id','客户地区'],
                    ['post','user.post','客户类型'],
                ];
                $res['circle'] = DB::table('customer_circle')->where('layer', 3)->orderBy('sort', 'asc')->get()->toArray();
                break;

            // 客户角色
            case 'client':
                $res['whereIn']['client.user_id'] = [$user->id];
                $res['circleIn'] = [];
                break;

            // 默认其他角色
            default:
                $res['columns'] = [
                    ['circle','client.circle_id','客户圈'],
                    ['region','user.province_id','客户地区'],
                    ['post','user.post','客户类型'],
                ];
                $res['circle'] = DB::table('customer_circle')->where('layer', 3)->orderBy('sort', 'asc')->get()->toArray();
        }
        return $res;
    }
}
