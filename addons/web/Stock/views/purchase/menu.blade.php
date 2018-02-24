<div class="panel-heading tabs-box">
    <ul class="nav nav-tabs">
        <?php
        $tabs = [
            ['key' => 'guide', 'name'=>'采购统计', 'url' => 'stock/purchase/guide'],
            ['key' => 'create', 'name'=>'采购单', 'url' => 'stock/purchase/create'],
            ['key' => 'index', 'name'=>'采购单据', 'url' => 'stock/purchase/index'],
            ['key' => 'line', 'name'=>'采购明细', 'url' => 'stock/purchase/line'],
            ['key' => 'trash', 'name'=>'作废单据', 'url' => 'stock/purchase/trash'],
            ['key' => 'repay', 'name'=>'还款记录', 'url' => 'stock/purchase/repay'],
        ];
        ?>
        @foreach($tabs as $tab)
        <li class="@if(Request::action() == $tab['key']) active @endif">
            <a class="text-sm" href="{{url($tab['url'])}}">{{$tab['name']}}</a>
        </li>
        @endforeach
    </ul>
</div>