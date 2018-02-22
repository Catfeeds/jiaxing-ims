<div class="wrapper" style="padding-bottom:0;">

    <div id="dialog-product-toolbar">
        <form id="search-form" name="mysearch" class="form-inline" method="get">
            @include('searchForm')
        </form>
    </div>

    <div class="m-t">
        <table id="dialog-product-{{$gets['jqgrid']}}"></table>
        <div id="dialog-product-page"></div>
    </div>
</div>

<script>
(function($) {
    var params = {{json_encode($gets)}};
    var $table = $("#dialog-product-{{$gets['jqgrid']}}");
    var jqgrid = "{{$gets['jqgrid']}}";

    var model = [
        {name: "nickname", index: 'user.nickname', label: '供应商', width: 200, align: 'left'},
        {name: "created_date", index: 'supplier_plan.created_at', label: '订单日期', width: 120, align: 'center'},
        {name: "product_text", index: 'product.name', label: '商品', width: 180, align: 'left'},
        {name: "plan_quantity", index: 'supplier_plan_data.quantity', label: '数量', width: 80, align: 'right'},
        {name: "id", index: 'supplier_plan.id', label: 'ID', width: 70, align: 'center'}
    ];

    $table.jqGrid({
        caption: '',
        datatype: 'json',
        mtype: 'POST',
        url: app.url('supplier/plan/dialog'),
        colModel: model,
        rowNum: 500,
        multiselect: true,
        viewrecords: true,
        rownumbers: false,
        height: 340,
        footerrow: false,
        postData: params,
        pager: '#dialog-product-page'
    });

    var data = {{json_encode($search['forms'])}};
    var search = $('#search-form').searchForm({
        data: data,
        init:function(e) {
            var self = this;
        }
    });

    search.find('#search-submit').on('click', function() {
        var query = search.serializeArray();
        $.map(query, function(row) {
            params[row.name] = row.value;
        });

        $table.jqGrid('setGridParam', {
            postData: params,
            page: 1
        }).trigger('reloadGrid');
        return false;
    });
})(jQuery);

</script>
