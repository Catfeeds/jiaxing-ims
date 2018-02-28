<div class="panel-heading tabs-box">
    <ul class="nav nav-tabs">
        <?php
        $tabs = [
            ['name'=>'用户', 'url' => 'user/user/index'],
            ['name'=>'角色', 'url' => 'user/role/index'],
            ['name'=>'用户组', 'url' => 'user/group/index'],
            ['name'=>'部门', 'url' => 'user/department/index'],
            ['name'=>'职位', 'url' => 'user/position/index'],
        ];
        ?>
        @foreach($tabs as $tab)
        <li class="@if(Request::path() == $tab['url']) active @endif">
            <a class="text-sm" href="{{url($tab['url'])}}">{{$tab['name']}}</a>
        </li>
        @endforeach
    </ul>
</div>