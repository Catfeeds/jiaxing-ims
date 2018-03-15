<style>
#tree {
    overflow: auto;
    position: relative;
    border: 1px solid #ddd;
    height:421px;
}
ul.fancytree-container {
    outline: none;
    height:421px;
    border: 0;
}
.tree-col > div {
    padding: 10px;
    padding-top: 0;
}
.tree-col > .col-sm-3 {
    padding-left: 0;
}
</style>

<div id="dialog-product-toolbar">
    <form id="search-form" name="mysearch" class="search-form form-inline" method="get">
        @include('searchForm3')
    </form>
</div>

<div class="tree-col">
    <div class="col-sm-9">
        <div class="list-jqgrid b-l b-r b-b">
            <table id="dialog-product-{{$gets['jqgrid']}}"></table>
            <div id="dialog-product-page"></div>
        </div>
    </div>

    <div class="col-sm-3 hidden-xs">
        <div class="list-tree">
            <div id="tree" class="ztree"></div>
        </div>
    </div>
    
</div>

<div class="clearfix"></div>

<script>
(function($) {

    var params = {{json_encode($gets)}};
    var $table = $("#dialog-product-{{$gets['jqgrid']}}");
    var jqgrid = "{{$gets['jqgrid']}}";

    var model = [
        {name: "text", index: 'product.name', label: '名称', width: 180, align: 'left'},
        {name: "product_spec", index: 'product.spec', label: '规格', width: 80, align: 'center'},
        {name: "category_name", index: 'product_category.name', label: '类别', width: 100, align: 'center'},
        {name: "product_barcode", index: 'product.barcode', label: '条码', width: 80, align: 'center'},
        {name: "stock_quantity", index: '', label: '库存数量', width: 80, align: 'right'},
        {name: "stock_cost", index: '', label: '成本', width: 60, align: 'right'},
        {name: "warehouse_name", index: 'warehouse.id', label: '仓库', width: 80, align: 'center'}
    ];

    $table.jqGrid({
        caption: '',
        datatype: 'json',
        mtype: 'POST',
        url: app.url('stock/stock-warehouse/dialog'),
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
        params.category_id = 0;
        $table.jqGrid('setGridParam', {
            postData: params,
            page: 1
        }).trigger('reloadGrid');
        return false;
    });

    $("#tree").fancytree({
        toggleEffect: false,
        clickFolderMode: 1,
        source: {
            type: 'post',
            url: app.url('stock/product-category/dialog'), 
            data: {
                fancytree: true
            }
        },
        /*
        init: function(e, res) {
            res.tree.activateKey(params.category_id);
            var node = res.tree.getActiveNode();
            if(node) {
                node.setSelected(true);
                node.setExpanded(true);
            }
        },*/
        activate: function(node) {
        },
        deactivate: function(node) {
        },
        click: function(event, res) {
            params.category_id = res.node.data.id;
            $table.jqGrid('setGridParam', {
                postData: params,
                page: 1
            }).trigger('reloadGrid');
        },
        focus: function(node) {
        },
        blur: function(node) {
        }
    });

})(jQuery);

</script>
