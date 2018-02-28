<div class="panel-heading tabs-box">
    <ul class="nav nav-tabs">

        <?php $tabs = [
            ['name'=>'基础设置', 'url' => 'setting/setting/index'],
            ['name'=>'门店设置', 'url' => 'setting/store/index'],
            ['name'=>'品牌设置', 'url' => 'car/brand/index'],
            ['name'=>'车型设置', 'url' => 'car/type/index'],
            ['name'=>'车牌设置', 'url' => 'car/plate/index'],
            ['name'=>'邮件设置', 'url' => 'setting/mail/index'],
            ['name'=>'部件设置', 'url' => 'setting/widget/index'],
        ];?>

        @foreach($tabs as $tab)
        <li class="@if(Request::path() == $tab['url']) active @endif">
            <a class="text-sm" href="{{url($tab['url'])}}">{{$tab['name']}}</a>
        </li>
        @endforeach
    </ul>
</div>