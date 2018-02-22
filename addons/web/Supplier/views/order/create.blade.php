<script>

var t = null;

$(function() {

    var models = {{json_encode($models)}};

    var footerCalculate = function() {
        var quantity = $(this).getCol('quantity', false, 'sum');
        var plan     = $(this).getCol('plan_quantity', false, 'sum');
        $(this).footerData('set',{plan_quantity: plan, quantity: quantity});
    }

    var editCombo = {};

    editCombo.plan_status = [
        {id:'0', text:'未送完'},
        {id:'1', text:'已送完'}
    ];

    t = $('#grid-table').jqGrid({
        caption: '',
        datatype: 'local',
        colModel: models,
        editCombo: editCombo,
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
                                product_id:'id', 
                                product_name:'text',
                                supplier_id:'supplier_id',
                            },
                            suggest: {
                                url: 'product/product/dialog_jqgrid',
                                params: {type:2, order:'asc', limit:1000}
                            },
                            dialog: {
                                title: '商品管理',
                                url: 'product/product/dialog_jqgrid',
                                params: {type: 2}
                            }
                        })
                    }
                });
            }

            if(cellname == 'plan_name') {

                if(row['product_id'] == '') {
                    $.toastr('error', '商品不能为空。', '错误');
                    return;
                }

                if(row['supplier_id'] == '') {
                    $.toastr('error', '商品没有指定供应商。', '错误');
                    return;
                }

                t.setColProp(cellname, {
                    editoptions: {
                        dataInit: $.jgrid.celledit.dialog({
                            srcField: 'plan_id',
                            mapField: {
                                plan_id: 'id',
                                plan_sn:'number',
                                plan_name:'text',
                                plan_quantity: 'plan_quantity',
                                plan_status: 'status',
                                plan_data_id: 'plan_data_id',
                                product_id:'product_id', 
                                product_name:'product_text',
                                supplier_id:'supplier_id',
                            },
                            suggest: {
                                cache: false,
                                url: 'supplier/plan/dialog',
                                params: {product_id: row['product_id'], order:'asc', limit:1000}
                            },
                            dialog: {
                                title: '订单管理',
                                url: 'supplier/plan/dialog',
                                params: {product_id: row['product_id']}
                            }
                        })
                    }
                });
            }

            if(cellname == 'plan_status') {
                t.setColProp(cellname, {
                    editoptions: {
                        dataInit: $.jgrid.celledit.dropdown({
                            valueField: 'id',
                            textField: 'text'
                        })
                    }
                });
            }

            if(cellname == 'delivery_date') {
                t.setColProp(cellname, {
                    editoptions: {
                        dataInit: function(element) {
                            datePicker({el: element, dateFmt: 'yyyy-MM-dd'});
                        }
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
        } else {
            $.toastr('error', res, '错误');
        }
    });
}

function getPanelHeight() {
    var list = $('#jqgrid-editor-container').position();
    return top.iframeHeight - list.top - 262;
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

    <div class="panel-footer">
        <button type="button" onclick="history.back();" class="btn btn-default">返回</button>
        <button type="button" onclick="_submit();" class="btn btn-success"><i class="fa fa-check-circle"></i> 提交</button>
    </div>

</div>

</form>