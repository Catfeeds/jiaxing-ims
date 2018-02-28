<div class="panel no-border">

@include('menus/purchase')

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
            <label for="sort" class="control-label"><span class="red"> * </span> 供应商</label>
            {{Dialog::select2('supplier','supplier_id', $row->supplier_id, 0, 0)}}
        </div>
    </div>
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
            <label for="user_id" class="control-label"><span class="red"> * </span> 采购员</label>
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

    <div class="form-inline">
    <div class="row">
    <div class="form-group">
    <div class="col-sm-12">
    <label for="sort" class="control-label">应收金额</label>
    <input type="text" name="rec_money" readonly="readonly" value="{{$row['rec_money']}}" id="rec_money" class="form-control input-sm money">
    </div></div>
    <div class="form-group">
    <div class="col-sm-12">
    <label for="sort" class="control-label"><span class="red"> * </span> 优惠金额</label>
    <input type="text" name="discount_money" id="discount_money" value="{{$row['discount_money']}}" class="form-control input-sm money">
    </div></div>
    <div class="form-group">
    <div class="col-sm-12">
    <label for="sort" class="control-label"><span class="red"> * </span> 本次付款</label>
    <input type="text" name="pay_money" id="pay_money" value="{{$row['pay_money']}}" class="form-control input-sm money">
    </div></div>
    <div class="form-group">
    <div class="col-sm-12">
    <label for="sort" class="control-label">本次欠款</label>
    <input type="text" name="arear_money" id="arear_money" readonly="readonly" value="{{$row['arear_money']}}" class="form-control input-sm money">
    </div></div>
</div>
</div>

<input type="hidden" name="quantity" id="total_quantity" value="0" />

</form>

</div>

<script type="text/javascript">

var t = null;
var columns = {{json_encode($columns)}};
var select2List = {};
var rec_money = 0.00;

$(function() {

    select2List.user_id = $("#user_id");
    select2List.user_id.select2Field({
        width: '153px',
        //multiple: true,
        ajax: {
            url: '/user/user/dialog'
        }
    });

    select2List.supplier_id = $("#supplier_id");
    select2List.supplier_id.select2Field({
        ajax: {
            url: '/stock/supplier/dialog'
        }
    });

    var footerCalculate = function(rowid) {

        var price = $(this).jqGrid('getCell', rowid, 'price');
        var quantity = $(this).jqGrid('getCell', rowid, 'quantity');
        $(this).jqGrid('setCell', rowid, 'money', quantity * price);
        
        var quantity = $(this).getCol('quantity', false, 'sum');
        var money = $(this).getCol('money', false, 'sum');
        
        $('#total_quantity').val(quantity);

        sumMoney(money, 'rec');

        $(this).footerData('set',{product_name:'合计:', quantity: quantity, money: money});
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
            $("#" + rowid).find('td').eq(iCol).addClass('edit-cell-item');
            var row = t.jqGrid('getRowData', rowid);

            if(cellname == 'product_name') {
                t.setColProp(cellname, {
                    editoptions: {
                        dataInit: $.jgrid.celledit.dialog({
                            srcField: 'product_id',
                            mapField: {
                                category_name:'category_name',
                                warehouse_name:'warehouse_name',
                                warehouse_id:'warehouse_id',
                                product_spec:'spec', 
                                product_id:'id',
                                product_name:'name',
                                product_code:'barcode',
                                product_spec:'spec',
                                last_price:'last_price',
                            },
                            suggest: {
                                url: 'stock/product/dialog',
                                params: {order:'asc', limit:1000}
                            },
                            dialog: {
                                title: '商品管理',
                                url: 'stock/product/dialog',
                                params: {}
                            }
                        })
                    }
                });
            }

            if(cellname == 'warehouse_name') {
                t.setColProp(cellname, {
                    editoptions: {
                        dataInit: $.jgrid.celledit.dropdown({
                            mapField: {
                                warehouse_id:'id',
                            },
                            valueField: 'id',
                            textField: 'text',
                            suggest: {
                                url: 'stock/warehouse/dialog',
                                cache: false,
                                params: {warehouse_id:row.warehouse_id}
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
            $("#" + rowid).find('td').eq(iCol).removeClass('edit-cell-item');
        },
        // 保存在本地的时候调用
        afterSaveCell: function(rowid, cellname, value, iRow, iCol) {

            // 计算页脚数据
            footerCalculate.call(this, rowid);

            // 编辑cell后保存时删除class
            $("#" + rowid).find('td').eq(iCol).removeClass('edit-cell-item');
        }
    });

    // 初始化行数据
    for(var i=1; i <= 10; i++) {
        t.jqGrid('addRowData', i, {});
    }

    // 监听金额改变
    $('input.money').on('input propertychange', function() {

        var money = $(this).val();
        if(this.id == 'discount_money') {
            sumMoney(money, 'discount');
        }
        if(this.id == 'pay_money') {
            sumMoney(money, 'pay');
        }
    });

});

function sumMoney(money, type) {
    if(type == 'rec') {
        $('#rec_money').val(money);
        $('#pay_money').val(money);
        $('#discount_money').val(0);
        $('#arear_money').val(0);
    }
    if(type == 'pay') {
        var rec       = $('#rec_money').val();
        var discount  = $('#discount_money').val();
        $('#arear_money').val(rec - money - discount);
    }
    if(type == 'discount') {
        var rec = $('#rec_money').val();
        $('#pay_money').val(rec - money);
        $('#arear_money').val(0);
    }
}

function _submit() {

    var params = {};

    $.each(select2List, function(k, v) {
        params[k] = v.select2('val');
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