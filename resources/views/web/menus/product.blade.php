<div class="panel-heading tabs-box">
    <ul class="nav nav-tabs">
        <?php
        $tabs = [
            ['key' => 'product', 'name'=>'商品管理', 'url' => 'stock/product/index'],
            ['key' => 'product-category', 'name'=>'商品类别', 'url' => 'stock/product-category/index'],
        ];
        ?>
        @foreach($tabs as $tab)
        <li class="@if(Request::controller() == $tab['key']) active @endif">
            <a class="text-sm" href="{{url($tab['url'])}}">{{$tab['name']}}</a>
        </li>
        @endforeach
    </ul>
</div>