<div class="panel-heading tabs-box">
    <ul class="nav nav-tabs">
        <?php
        $tabs = [
            ['name'=>'采购统计', 'url' => 'stock/purchase/guide'],
            ['name'=>'采购单', 'url' => 'stock/purchase/create'],
            ['name'=>'采购单据', 'url' => 'stock/purchase/index'],
            ['name'=>'采购明细', 'url' => 'stock/purchase/line'],
            ['name'=>'作废单据', 'url' => 'stock/purchase/trash'],
            ['name'=>'还款记录', 'url' => 'stock/purchase-repayment/index'],
        ];
        ?>
        @foreach($tabs as $tab)
        <li class="@if(Request::path() == $tab['url']) active @endif">
            <a class="text-sm" href="{{url($tab['url'])}}">{{$tab['name']}}</a>
        </li>
        @endforeach
    </ul>
</div>