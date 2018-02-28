<div class="panel-heading tabs-box">
    <ul class="nav nav-tabs">
        <?php
        $tabs = [
            ['name'=>'领料统计', 'url' => 'stock/requisition/guide'],
            ['name'=>'领料单', 'url' => 'stock/requisition/create'],
            ['name'=>'领料单据', 'url' => 'stock/requisition/index'],
            ['name'=>'领料明细', 'url' => 'stock/requisition/line'],
            ['name'=>'作废单据', 'url' => 'stock/requisition/trash'],
        ];
        ?>
        @foreach($tabs as $tab)
        <li class="@if(Request::path() == $tab['url']) active @endif">
            <a class="text-sm" href="{{url($tab['url'])}}">{{$tab['name']}}</a>
        </li>
        @endforeach
    </ul>
</div>