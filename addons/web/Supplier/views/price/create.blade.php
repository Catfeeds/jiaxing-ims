<script>

var t = null;

$(function() {

    var models = {{json_encode($models)}};
    var datas  = {{json_encode($rows)}};

    t = $('#grid-table').jqGrid({
        caption: '',
        datatype: 'local',
        colModel: models,
        cellEdit: true,
        data: datas,
        cellurl: '',
        rowNum: 1000,
        cellsubmit: 'clientArray',
        multiselect: false,
        viewrecords: true,
        rownumbers: true,
        footerrow: true,
        height: getPanelHeight(),
        rowattr: function(row) {
            // 附加tr样式
            if (row.id > 0) {
                return {'class': 'edited'};
            }
        },
        gridComplete: function() {
            $(this).jqGrid('setColsWidth');
        },
        // 进入编辑前调用
        beforeEditCell: function(rowid, cellname, value, iRow, iCol) {

            // 编辑前插入class
            $("#" + rowid).find('td').eq(iCol).addClass('edit-cell-item');

            if(cellname == 'product_name') {
                t.setColProp(cellname, {
                    editoptions: {
                        dataInit: $.jgrid.celledit.dialog({
                            srcField: 'product_id',
                            mapField: {
                                spec:'spec', 
                                product_id:'id', 
                                product_name:'text',
                                supplier_id:'supplier_id',
                            },
                            suggest: {
                                url: 'product/product/dialog_jqgrid',
                                params: {owner_id:'{{auth()->id()}}', type: 2, order:'asc', limit:10000}
                            },
                            dialog: {
                                title: '商品管理',
                                url: 'product/product/dialog_jqgrid',
                                params: {owner_id:'{{auth()->id()}}', type: 2}
                            }
                        })
                    }
                });
            }

            if(cellname == 'date') {
                t.setColProp(cellname, {
                    editoptions: {
                        dataInit: function(element) {
                            datePicker({el: element, dateFmt: 'yyyy-MM-dd HH:mm'});
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
            // 编辑cell后保存时删除class
            $("#" + rowid).find('td').eq(iCol).removeClass('edit-cell-item');
        }
    });

    if(datas.length == 0) {
        for(var i=1; i <= 10; i++) {
            t.jqGrid('addRowData', i, {});
        }
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
    return top.iframeHeight - list.top - 261;
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
                <textarea class="form-control" rows="2" type="text" name="description" id="description" placeholder="暂无备注">{{$row['description']}}</textarea>
            </td>
        </tr>

    </table>
    </div>

    <div class="panel-footer">
        <input type="hidden" id="id" name="id" value="{{$price->id}}">
        <button type="button" onclick="history.back();" class="btn btn-default">返回</button>
        <button type="button" onclick="_submit();" class="btn btn-success"><i class="fa fa-check-circle"></i> 提交</button>
    </div>

</div>

</form>