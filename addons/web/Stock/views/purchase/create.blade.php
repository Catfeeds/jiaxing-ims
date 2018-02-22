<div class="panel no-border">

@include('purchase/menu')

<script>

var t = null;
var validate = {{json_encode($validate)}};

$(function() {

    $("#user_id").select2Field({
        width: '153px',
        ajax: {
            url: '/user/user/dialog'
        }
    });

    $("#supplier_id").select2Field({
        ajax: {
            url: '/stock/supplier/dialog'
        }
    });

    var models = {{json_encode($models)}};

    var footerCalculate = function() {
        var quantity = $(this).getCol('quantity', false, 'sum');
        $(this).footerData('set',{product_name:'合计:', quantity: quantity});
    }

    t = $('#grid-table').jqGrid({
        caption: '',
        datatype: 'local',
        colModel: models,
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
                                month_1:'month_1',
                                month_2:'month_2',
                                month_3:'month_3',
                                month_4:'month_4',
                                month_5:'month_5',
                                month_6:'month_6',
                                spec:'spec', 
                                product_id:'id',
                                stock_total:'stock_total',
                                product_name:'text',
                                supplier_id:'supplier_id',
                                supplier_name:'',
                                quantity: 'budget',
                            },
                            suggest: {
                                url: 'product/product/dialog_jqgrid',
                                params: {yonyou:'a',owner_id:'{{auth()->id()}}', type: 2, order:'asc', limit:1000}
                            },
                            dialog: {
                                title: '商品管理',
                                url: 'product/product/dialog_jqgrid',
                                params: {yonyou:'a',owner_id:'{{auth()->id()}}', type: 2}
                            }
                        })
                    }
                });
            }

            if(cellname == 'supplier_name') {
                t.setColProp(cellname, {
                    editoptions: {
                        dataInit: $.jgrid.celledit.dropdown({
                            mapField: {
                                supplier_id:'id',
                            },
                            valueField: 'id',
                            textField: 'text',
                            suggest: {
                                url: 'supplier/product/suppliers',
                                cache: false,
                                params: {product_id:row.product_id}
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
            footerCalculate.call(this);

            // 编辑cell后保存时删除class
            $("#" + rowid).find('td').eq(iCol).removeClass('edit-cell-item');
        }
    });

    /*
    t.jqGrid('setGroupHeaders', {
        useColSpanStyle: true, //没有表头的列是否与表头列位置的空单元格合并
        groupHeaders: [{
            startColumnName: 'month_1',
            numberOfColumns: 3,
            titleText: '去年包材使用量'
        },{
            startColumnName: 'month_4',
            numberOfColumns: 2,
            titleText: '本年包材使用量'
        }]
    });
    */
    // 初始化行数据
    for(var i=1; i <= 10; i++) {
        t.jqGrid('addRowData', i, {});
    }
});

/* 
 * 提交数据
*/
function _submit() {

    var data = [];

    var dataset = t.jqGrid('getRowsData');
    if(dataset.v === true) {
        if(dataset.data.length == 0) {
            $.toastr('error', '商品不能为空。', '错误');
            return;
        } else {
            data = dataset.data;
        }
    } else {
        return;
    }

    var query = $('#myform').serialize();
    $.post('{{url("create")}}', query + '&' + $.param({datas: data}), function(res) {
        if(res.status) {
            location.href = res.data;
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

<div class="wrapper-sm">
    <a class="btn btn-sm btn-default" href="javascript:history.back();"><i class="fa fa-remove"></i> 取消</a>
    <a class="btn btn-sm btn-info" href="javascript:actionLink('filter');"> <i class="fa fa-check"></i> 提交</a>
</div>
<div class="wrapper-sm b-t">

<form method="post" enctype="multipart/form-data" action="{{url()}}" id="myform" name="myform">

<div class="form-inline">
    <div class="row">
    <div class="form-group">
        <div class="col-sm-12">
            <label for="sort" class="control-label">供应商</label>
            {{Dialog::user('supplier','supplier_id', $row->supplier_id, 0, 0)}}
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <label for="sort" class="control-label">单据日期</label>
            <input type="text" name="date" data-toggle="date" value="{{date('Y-m-d')}}" id="date" class="form-control input-sm">
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <label for="sort" class="control-label">单据编号</label>
            <input type="text" name="user" readonly="readonly" id="user" value="{{Auth::user()->nickname}}" class="form-control input-sm">
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <label for="sort" class="control-label">制单人</label>
            {{Dialog::user('user','user_id', $row['user_id'], 0, 0)}}
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
    <input type="text" name="date" readonly="readonly" value="" id="date" class="form-control input-sm">
    </div></div>
    <div class="form-group">
    <div class="col-sm-12">
    <label for="sort" class="control-label">优惠金额</label>
    <input type="text" name="user" id="user" value="" class="form-control input-sm">
    </div></div>
    <div class="form-group">
    <div class="col-sm-12">
    <label for="sort" class="control-label">本次付款</label>
    <input type="text" name="user" id="user" value="" class="form-control input-sm">
    </div></div>
    <div class="form-group">
    <div class="col-sm-12">
    <label for="sort" class="control-label">本次欠款</label>
    <input type="text" name="user" id="user" readonly="readonly" value="" class="form-control input-sm">
    </div></div>
</div></div>

</form>

</div>

<!--
<div class="panel-footer">
    <button type="button" onclick="history.back();" class="btn btn-default">取消</button>
    <button type="button" onclick="_submit();" class="btn btn-success"><i class="fa fa-check-circle"></i> 提交</button>
</div>
-->
    