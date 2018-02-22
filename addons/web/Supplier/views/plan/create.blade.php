<script>

var t = null;
var validate = {{json_encode($validate)}};

$(function() {

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
        height: getPanelHeight(),
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
    return top.iframeHeight - list.top - 350;
}

// 框架页面改变大小时会调用此方法
function iframeResize() {
    // 框架改变大小时设置Panel高度
    t.jqGrid('setPanelHeight', getPanelHeight());
    // resize jqgrid大小
    t.jqGrid('resizeGrid');
}

</script>

<form method="post" enctype="multipart/form-data" action="{{url()}}" id="myform" name="myform">
<div class="panel">

    <div class="table-responsive">
    <table class="table table-form m-b-none">

        <tr>
            <td align="right" width="10%">单据日期</td>
            <td align="left" width="40%">
                <input type="text" name="date" data-toggle="date" value="{{date('Y-m-d')}}" id="date" class="form-control input-sm input-inline">
            </td>
            <td align="right" width="10%">制单人</td>
            <td align="left" width="40%">
                <input type="text" name="user" id="user" value="{{Auth::user()->nickname}}" class="form-control input-sm input-inline">
            </td>
        </tr>

        <tr>
            <td colspan="4">
                <div id="jqgrid-editor-container">
                    <table id="grid-table"></table>
                </div>
            </td>
        </tr>

        <tr>
            <td align="left" colspan="4">
                @include('attachment/create')
            </td>
        </tr>

        <tr>
            <td colspan="4">
                <textarea class="form-control" rows="2" type="text" name="remark" id="remark" placeholder="暂无备注">{{$row['remark']}}</textarea>
            </td>
        </tr>

    </table>
    </div>

    <div class="panel-footer no-border">
        <p>
        <a href="{{url('product/yonyou/sync', ['ym' => date('Ym')])}}" class="btn btn-sm btn-default"><i class="icon icon-plus"></i> 同步({{date("Y-m")}})用友数据</a>
        <a href="{{url('product/yonyou/sync')}}" class="btn btn-sm btn-default"><i class="icon icon-plus"></i> 同步(2015-12-31)前用友数据</a>
        </p>
        <button type="button" onclick="history.back();" class="btn btn-default">返回</button>
        <button type="button" onclick="_submit();" class="btn btn-success"><i class="fa fa-check-circle"></i> 提交</button>
    </div>

</div>

</form>