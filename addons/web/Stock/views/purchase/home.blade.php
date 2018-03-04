<style>
.row-sm { margin-top: 80px; margin-left: -8px; margin-right: -8px; }
.row-sm > div { padding-left: 8px; padding-right: 8px; }
.row-sm > div > .panel {
    margin-bottom: 16px !important;
    border-radius: 5px !important;
    text-align: center;
}
.row-todo .panel { max-width:360px; margin:0 auto;  padding-bottom: 10px; position: relative; }
.todo-logo { margin: 20px; border-radius: 50%; color: #fff; padding-top:24px; position:absolute; top:0; bottom:0; left:0; right:0; width: 88px; }
.todo-text { margin-left: 80px; padding: 30px 15px; font-weight: 400; }
.todo-text .px { padding-top: 10px; }
@media (max-width: 767px) {
    .row-sm { margin-top: 0; }
    .todo-text { margin-left: 0; }
}
.text-md { font-size: 20px; font-family: font-family: "Helvetica Neue",Helvetica,Arial,"Hiragino Sans GB","Hiragino Sans GB W3","Microsoft YaHei UI","Microsoft YaHei","WenQuanYi Micro Hei",sans-serif; }
</style>

<div class="panel no-border">
    @include('tabs', ['tabKey' => 'stock.purchase'])
    <form id="search-form-simple" class="search-form form-inline" action="{{url()}}" method="get">
        @include('searchForm3')
    </form>
</div>

<div class="row row-sm row-todo">

    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
        <div class="panel">
            <div class="todo-logo hidden-xs" style="background-color:#2c83e4;">
                <i class="fa fa-3x fa-sun-o"></i>
            </div>
            <div class="todo-text">
                <div class="text-md" style="color:#2c83e4;">本日采购金额</div>
                <div class="px text-base">{{$day}}元</div>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
        <div class="panel">
            <div class="todo-logo hidden-xs" style="background-color:#fd875a;">
                <i class="fa fa-3x fa-calendar"></i>
            </div>
            <div class="todo-text">
                <div class="text-md" style="color:#fd875a;">本月采购额</div>
                <div class="px text-base">{{$month}}元</div>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
        <div class="panel">
            <div class="todo-logo hidden-xs" style="background-color:#27c24c;">
                <i class="fa fa-3x fa-database"></i>
            </div>
            <div class="todo-text">
                <div class="text-md" style="color:#27c24c;">累计采购金额</div>
                <div class="px text-base">{{$all}}元</div>
            </div>
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