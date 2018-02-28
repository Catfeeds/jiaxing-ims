<div class="panel-heading tabs-box">
    <ul class="nav nav-tabs">
        <?php
        $tabs = [
            ['name'=>'库存统计', 'url' => 'stock/stock/count'],
            ['name'=>'库存列表', 'url' => 'stock/stock/index'],
            ['name'=>'销售出库表', 'url' => '#'],
            ['name'=>'商品销售对比', 'url' => '#'],
            ['name'=>'商品收发明细表', 'url' => 'stock/stock/line'],
            ['name'=>'库存预警', 'url' => 'stock/stock/warning'],
        ];
        ?>
        @foreach($tabs as $tab)
        <li class="@if(Request::path() == $tab['url']) active @endif">
            <a class="text-sm" href="{{url($tab['url'])}}">{{$tab['name']}}</a>
        </li>
        @endforeach
    </ul>
</div>