<div class="panel-heading tabs-box">
    <ul class="nav nav-tabs">
        <?php
        $tabs = [
            ['key' => 'service', 'name'=>'服务管理', 'url' => 'stock/service/index'],
            ['key' => 'service-category', 'name'=>'服务类别', 'url' => 'stock/service-category/index'],
        ];
        ?>
        @foreach($tabs as $tab)
        <li class="@if(Request::controller() == $tab['key']) active @endif">
            <a class="text-sm" href="{{url($tab['url'])}}">{{$tab['name']}}</a>
        </li>
        @endforeach
    </ul>
</div>