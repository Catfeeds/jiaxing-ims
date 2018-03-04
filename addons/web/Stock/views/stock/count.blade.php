<style>
.row-sm { margin-left: -8px; margin-right: -8px; }
.row-sm > div { padding-left: 8px; padding-right: 8px; }
.row-sm > div > .panel {
    margin-bottom: 16px !important;
    text-align: center;
}
.row-todo .panel { padding-bottom: 10px; position: relative; }
.todo-logo { color: #fff; padding-top:32px; position:absolute; top:0; bottom:0; left:0; width: 108px; }
.todo-text { margin-left: 108px; padding: 10px 15px; text-align: left; font-weight: 700; }
.todo-text .px { padding-top: 10px; }
@media (max-width: 767px) {
    .todo-text { margin-left: 0; }
}
.text-md { font-size: 20px; font-family: font-family: "Helvetica Neue",Helvetica,Arial,"Hiragino Sans GB","Hiragino Sans GB W3","Microsoft YaHei UI","Microsoft YaHei","WenQuanYi Micro Hei",sans-serif; }
</style>

<div class="panel no-border">

    @include('tabs', ['tabKey' => 'stock'])

    <div class="panel-heading tabs-box">
        <ul class="nav nav-tabs">
        @foreach(['day' => '今日', 'month' => '本月', 'year' => '本年'] as $k => $v)
        <li @if($search['query']['date'] == $k) class="active" @endif>
            <a class="text-sm" href="{{url(null, ['date' => $k])}}">{{$v}}</a>
        </li>
        @endforeach
        </ul>
    </div>

    <form id="search-form-simple" class="search-form form-inline" action="{{url()}}" method="get">
        @include('searchForm3')
        <input name="date" type="hidden" value="{{$search['query']['date']}}">
    </form>

    </div>

    <div class="row row-sm row-todo">
    
        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
            <div class="panel">
                <div class="todo-logo hidden-xs" style="background-color:#2c83e4;">
                    <i class="fa fa-3x fa-sign-in"></i>
                </div>
                <div class="todo-text">
                    <div class="text-md" style="color:#2c83e4;">入库金额</div>
                    <div class="px">@number($count->where('type', 1)->sum('total_money'), 2)</div>
                    <div class="text-base">入库数量：{{$count->where('type', 1)->sum('total_quantity')}}</div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
            <div class="panel">
                <div class="todo-logo hidden-xs" style="background-color:#fd875a;">
                    <i class="fa fa-4x fa-share-square-o"></i>
                </div>
                <div class="todo-text">
                    <div class="text-md" style="color:#fd875a;">出库金额</div>
                    <div class="px">@number($count->where('type', 2)->sum('total_money'), 2)</div>
                    <div class="text-base">出库数量：{{$count->where('type', 2)->sum('total_quantity')}}</div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
            <div class="panel">
                <div class="todo-logo hidden-xs" style="background-color:#27c24c;">
                    <i class="fa fa-3x fa-money"></i>
                </div>
                <div class="todo-text">
                    <div class="text-md" style="color:#27c24c;">库存金额</div>
                    <div class="px">@number($total_money, 2)</div>
                    <div class="text-base">库存数量：{{$total_quantity}}</div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
            <div class="panel">
                <div class="todo-logo hidden-xs" style="background-color:#7266ba;">
                    <i class="fa fa-3x fa-bell-o"></i>
                </div>
                <div class="todo-text">
                    <div class="text-md" style="color:#7266ba;">库存预警</div>
                    <div class="px">超限数量：0</div>
                    <div><a href="#">查看详情</a></div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel no-border">

    <div class="panel-heading">
        <span>月总销售金额</span> <span class="text-2x text-primary" style="font-weight:700;">0.00</span>
        &nbsp;&nbsp;
        <span>上月周期&nbsp;<i class="fa fa-long-arrow-down text-success"></i></span>
        
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <table class="table b-a">
        <tr>
            <th class="text-left">入库项目</th>
            <th class="text-right">数量</th>
            <th class="text-right">金额</th>
            <th></th>
        </tr>
        @foreach($types as $type)
        @if($type['type'] == 1)
        <tr>
            <td class="text-left">{{$type['name']}}</td>
            <td class="text-right">{{$type_list[$type['id']]['total_quantity']}}</td>
            <td class="text-right">{{$type_list[$type['id']]['total_money']}}</td>
            <td class="text-right"><a class="option" href="#">查看详情</a></td>
        </tr>
        @endif
        @endforeach
        </table>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
    <table class="table b-a">
        <tr>
            <th class="text-left">出库项目</th>
            <th class="text-right">数量</th>
            <th class="text-right">成本金额</th>
            <th class="text-right">售价金额</th>
            <th></th>
        </tr>
        @foreach($types as $type)
        @if($type['type'] == 2)
        <tr>
            <td class="text-left">{{$type['name']}}</td>
            <td class="text-right">{{$type_list[$type['id']]['total_quantity']}}</td>
            <td class="text-right">{{$type_list[$type['id']]['total_money']}}</td>
            <td class="text-right">{{$type_list[$type['id']]['sales_money']}}</td>
            <td class="text-right"><a class="option" href="#">查看详情</a></td>
        </tr>
        @endif
        @endforeach
        </table>
    </div>

    <div class="clearfix"></div>
        
    </div>

        
</div>
</div>

<script>

var $table = null;
var params = paramsSimple = {{json_encode($search['query'])}};
var searchSimple = null;

(function($) {
    
    var data = {{json_encode($search['forms'])}};

    searchSimple = $('#search-form-simple').searchForm({
        data: data,
        init: function(e) {
            var self = this;
        }
    });
    searchSimple.find('#search-submit').on('click', function() {
        var query = searchSimple.serializeArray();
        $.map(query, function(row) {
            paramsSimple[row.name] = row.value;
        });
    });
})(jQuery);

</script>