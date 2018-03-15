<div class="panel no-border">

@include('tabs', ['tabKey' => 'stock.loss'])

<div class="wrapper-sm">
    <a class="btn btn-sm btn-default" href="javascript:history.back();"><i class="fa fa-remove"></i> 取消</a>
    <a class="btn btn-sm btn-info" href="javascript:_submit();"> <i class="fa fa-check"></i> 保存</a>
</div>
<div class="wrapper-sm b-t">

<form method="post" enctype="multipart/form-data" action="{{url()}}" id="myform" name="myform">

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
            <label for="user_id" class="control-label"><span class="red"> * </span> 报损员</label>
            {{Dialog::select2('user','user_id', $row['user_id'], 0, 0)}}
        </div>
    </div>
</div>
</div>
    <div id="jqgrid-editor-container" class="m-t m-b">
        <table id="grid-table"></table>
    </div>

    <div class="form-group">
        <textarea class="form-control" type="text" name="remark" id="remark" placeholder="暂无备注">{{$row['remark']}}</textarea>
    </div>
</div>

<input type="hidden" name="total_quantity" id="total_quantity" value="0" />
<input type="hidden" name="total_money" id="total_money" value="0.00" />

</form>

</div>

<script type="text/javascript">

var t = null;
var columns = {{json_encode($columns)}};

$(function() {

    $.each(select2List, function(key, row) {
        select2List[key].el = $('#' + key).select2Field(row.options);
    });

    var footerCalculate = function(rowid) {

        var cost_price = $(this).jqGrid('getCell', rowid, 'cost_price');
        var quantity   = $(this).jqGrid('getCell', rowid, 'quantity');
        $(this).jqGrid('setCell', rowid, 'cost_money', quantity * cost_price);
        
        var quantity = $(this).getCol('quantity', false, 'sum');
        var cost_money = $(this).getCol('cost_money', false, 'sum');
        
        $('#total_quantity').val(quantity);
        $('#total_money').val(cost_money);

        $(this).footerData('set',{product_name:'合计:', quantity: quantity, cost_money: cost_money});
    }

    t = $('#grid-table').jqGrid({
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
        gridComplete: function() {
            $(this).jqGrid('setColsWidth');
            footerCalculate.call(this);
        },
        // 进入编辑前调用
        beforeEditCell: function(rowid, cellname, value, iRow, iCol) {

            // 编辑前插入class
            $(this.rows[iRow]).find('td').eq(iCol).addClass('edit-cell-item');
            var row = t.jqGrid('getRowData', rowid);

            if(cellname == 'product_name') {
                t.setColProp(cellname, {
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
                            beforeSelect: function(row) {
                                if (row['stock_quantity'] == 0) {
                                    $.toastr('error', row.product_name + '库存不足。', '错误');
                                    return false;
                                }
                                row['quantity'] = 1;
                                return row;
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
            footerCalculate.call(this, rowid);

            // 编辑cell后保存时删除class
            $(this.rows[iRow]).find('td').eq(iCol).removeClass('edit-cell-item');
        }
    });

    // 初始化行数据
    for(var i=1; i <= 10; i++) {
        t.jqGrid('addRowData', i, {});
    }
});

function _submit() {

    var params = {};

    $.each(select2List, function(k, v) {
        params[k] = v.el.select2('val');
    });

    var dataset = t.jqGrid('getRowsData');
    if(dataset.v === true) {
        if(dataset.data.length == 0) {
            $.toastr('error', '商品不能为空。', '错误');
            return;
        } else {
            params.stock_line = dataset.data;
        }
    } else {
        return;
    }

    var query = $('#myform').serialize();
    $.post('{{url("create")}}', query + '&' + $.param(params), function(res) {
        if(res.status) {
            $.messager.alert('提醒', res.data, function() {
                location.href = res.url;
            });
        } else {
            $.toastr('error', res.data);
        }
    });
}

function getPanelHeight() {
    var list = $('#jqgrid-editor-container').position();
    return top.iframeHeight - list.top - 150;
}

// 框架页面改变大小时会调用此方法
function iframeResize() {
    // 框架改变大小时设置Panel高度
    t.jqGrid('setPanelHeight', getPanelHeight());
    // resize jqgrid大小
    t.jqGrid('resizeGrid');
}
</script>