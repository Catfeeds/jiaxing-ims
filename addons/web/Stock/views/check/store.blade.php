<div class="wrapper-sm">

<form method="post" action="{{url()}}" id="check-store-form">

<div class="form-inline">

    <div class="row">
        <div class="form-group">
            <div class="col-sm-12">
                <label for="sort" class="control-label"><span class="red"> * </span> 单据日期</label>
                <input type="text" name="date" data-toggle="date" value="{{date('Y-m-d')}}" id="date" class="form-control input-sm">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <label for="sort" class="control-label">单据编号</label>
                <input type="text" name="sn" readonly="readonly" id="sn" value="{{date('YmdHis')}}" class="form-control input-sm">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <label for="user_id" class="control-label"><span class="red"> * </span> 盘点员</label>
                {{Dialog::select2('user','user_id', $row['user_id'], 0, 0)}}
            </div>
        </div>
    </div>

</div>

<div id="jqgrid-editor-container" class="m-t m-b">
    <table id="jqgrid-check-store"></table>
</div>

<div class="form-group m-b-none">
    <textarea class="form-control" type="text" name="remark" id="remark" placeholder="暂无备注">{{$row['remark']}}</textarea>
</div>

<input type="hidden" name="type_id" id="stock_type_id" value="0" />
<input type="hidden" name="total_quantity" id="total_quantity" value="0" />
<input type="hidden" name="total_money" id="total_money" value="0.00" />

</form>

</div>

<script type="text/javascript">

var jqgrid_check_store = null;
var columns = {{json_encode($columns)}};

$(function() {

    $.each(select2List, function(key, row) {
        select2List[key].el = $('#' + key).select2Field(row.options);
    });

    $('#stock_type_id').val(stock_type_id);

    var subtotalFooter = function(rowid) {
        var cost_price = $(this).jqGrid('getCell', rowid, 'cost_price');
        var quantity   = $(this).jqGrid('getCell', rowid, 'quantity');
        $(this).jqGrid('setCell', rowid, 'cost_money', quantity * cost_price);
    }

    var totalFooter = function() {
        var quantity   = $(this).getCol('quantity', false, 'sum');
        var cost_money = $(this).getCol('cost_money', false, 'sum');
        $('#total_quantity').val(quantity);
        $('#total_money').val(cost_money);
        $(this).footerData('set',{product_name:'合计:', quantity: quantity, cost_money: cost_money});
    }

    jqgrid_check_store = $('#jqgrid-check-store').jqGrid({
        caption: '',
        datatype: 'local',
        colModel: columns,
        cellEdit: true,
        cellsubmit: 'clientArray',
        cellurl: '',
        multiselect: false,
        viewrecords: true,
        rownumbers: true,
        footerrow: true,
        height: 300,
        data: jqgrid_check_rows,
        gridComplete: function() {
            $(this).jqGrid('setColsWidth');
            var me = this;
            var ids = $(this).jqGrid('getDataIDs');
            $.each(ids, function(k, v) {
                subtotalFooter.call(me, v);
            });
            totalFooter.call(this);
        },
        // 进入编辑前调用
        beforeEditCell: function(rowid, cellname, value, iRow, iCol) {

            // 编辑前插入class
            $(this.rows[iRow]).find('td').eq(iCol).addClass('edit-cell-item');
            var row = jqgrid_check_store.jqGrid('getRowData', rowid);

            if(cellname == 'product_name') {
                jqgrid_check_store.setColProp(cellname, {
                    editoptions: {
                        dataInit: $.jgrid.celledit.dialog({
                            srcField: 'product_id',
                            mapField: {
                                warehouse_name:'warehouse_name',
                                category_name:'category_name',
                                product_name:'product_name',
                                product_barcode:'product_barcode',
                                product_spec:'product_spec',
                                product_unit:'product_unit',
                                stock_quantity:'stock_quantity',

                                warehouse_id:'warehouse_id',
                                product_id:'product_id',
                                price:'product_price',
                                cost_price:'stock_cost',
                                quantity: 1,
                            },
                            suggest: {
                                url: 'stock/stock-warehouse/dialog',
                                params: {order:'asc', limit:1000}
                            },
                            dialog: {
                                title: '商品管理',
                                dialogClass: 'modal-lg',
                                url: 'stock/stock-warehouse/dialog',
                                params: {}
                            }
                        })
                    }
                });
            }
        },
        // 进入编辑后调用
        afterEditCell: function(rowid, cellname, value, iRow, iCol) {
        },
        // 保存服务器时调用
        afterRestoreCell: function(rowid, value, iRow, iCol) {
            // 编辑cell后保存时删除class
            $(this.rows[iRow]).find('td').eq(iCol).removeClass('edit-cell-item');
        },
        // 保存在本地的时候调用
        afterSaveCell: function(rowid, cellname, value, iRow, iCol) {
            // 计算页脚数据
            subtotalFooter.call(this, rowid);
            totalFooter.call(this);
            // 编辑cell后保存时删除class
            $(this.rows[iRow]).find('td').eq(iCol).removeClass('edit-cell-item');
        }
    });
});

</script>