<div class="panel-heading tabs-box">
    <ul class="nav nav-tabs">
        <?php
        $tabs = [
            ['key' => 'user', 'name'=>'用户', 'url' => 'user/user/index'],
            ['key' => 'role', 'name'=>'角色', 'url' => 'user/role/index'],
            ['key' => 'group', 'name'=>'用户组', 'url' => 'user/group/index'],
            ['key' => 'department', 'name'=>'部门', 'url' => 'user/department/index'],
            ['key' => 'position', 'name'=>'职位', 'url' => 'user/position/index'],
        ];
        ?>
        @foreach($tabs as $tab)
        <li class="@if(Request::controller() == $tab['key']) active @endif">
            <a class="text-sm" href="{{url($tab['url'])}}">{{$tab['name']}}</a>
        </li>
        @endforeach
    </ul>
</div>