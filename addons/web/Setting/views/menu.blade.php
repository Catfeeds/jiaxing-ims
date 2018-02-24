<div class="panel-heading tabs-box">
    <ul class="nav nav-tabs">

        <?php $tabs = [
            ['key' => 'setting', 'name'=>'基础设置', 'url' => 'setting/setting/index'],
            ['key' => 'store', 'name'=>'门店设置', 'url' => 'setting/store/index'],
            ['key' => 'brand', 'name'=>'品牌设置', 'url' => 'car/brand/index'],
            ['key' => 'type', 'name'=>'车型设置', 'url' => 'car/type/index'],
            ['key' => 'plate', 'name'=>'车牌设置', 'url' => 'car/plate/index'],
            ['key' => 'mail', 'name'=>'邮件设置', 'url' => 'setting/mail/index'],
            ['key' => 'widget', 'name'=>'部件设置', 'url' => 'setting/widget/index'],
        ];?>

        @foreach($tabs as $tab)
        <li class="@if(Request::controller() == $tab['key']) active @endif">
            <a class="text-sm" href="{{url($tab['url'])}}">{{$tab['name']}}</a>
        </li>
        @endforeach
    </ul>
</div>