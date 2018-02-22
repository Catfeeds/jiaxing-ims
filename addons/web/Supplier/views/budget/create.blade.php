<script>

var table = null;
var validate = {{json_encode($validate)}};

$(function() {

    var models = {{json_encode($models)}};

    var footerCalculate = function() {
        var quantity = $(this).getCol('quantity', false, 'sum');
        $(this).footerData('set',{product_name:'合计:', quantity: quantity});
    }

    table = $('#grid-table').jqGrid({
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
        height: 452,
        gridComplete: function() {
            footerCalculate.call(this);
        },
        // 进入编辑前调用
        beforeEditCell: function(rowid, cellname, value, iRow, iCol) {

            // 编辑前插入class
            $("#" + rowid).find('td').eq(iCol).addClass('edit-cell-item');

            if(cellname == 'product_name') {
                table.setColProp(cellname, {
                    editoptions: {
                        dataInit: $.jgrid.celledit.dialog({
                            srcField: 'product_id',
                            mapField: {product_id: 'id', stock_total: 'stock_total', product_name: 'text'},
                            suggest: {
                                url: 'supplier/product/dialog_jqgrid',
                                params: {owner_id:'{{auth()->id()}}', order:'asc', limit:1000}
                            },
                            dialog: {
                                title: '商品管理',
                                url: 'supplier/product/dialog_jqgrid',
                                params: {owner_id:'{{auth()->id()}}'}
                            }
                        })
                    }
                });
            }

            /*
            if(cellname == 'description') {
                table.setColProp(cellname, {
                    editoptions: {
                        dataInit: $.jgrid.celledit.dropdown({
                            valueField: 'id',
                            textField: 'text'
                        })
                    }
                });
            }
            */
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
    for(var i=1; i <= 15; i++) {
 	    table.jqGrid('addRowData', i, {});
    }
});

/* 
 * 保存数据
*/
function saveData() {

    var forms = ['sn', 'date', 'description', 'user'];

    var data = {};

    var passed = true;

    $.each(forms, function(i, key) {

        var value = $('#' + key).val() || '';
        data[key] = value;

        // 简单验证表单
        if(validate.rules[key] == 'required') {
            if(value == '') {
                $.toastr('error', validate.attrs[key] + ': 不能为空。', '错误');
                passed = false;
                return false;
            }
        }

    });

    if(passed) {
        var products = table.jqGrid('getRowsData');
        if(products.v === true) {
            if(products.data.length === 0) {
                $.toastr('error', '产品列表不能为空。', '错误');
            } else {
                data['products'] = products.data;
                $.post('{{url("store")}}', data, function(res) {
                    location.href = '{{url_referer("index")}}';
                });
            }
        }
    }
}

</script>

<div class="panel">

    <div class="wrapper">
        <div class="table-responsive">
            <table class="table table-form b-a m-b-none">

                <tr>
                    <td align="right">单据编号</td>
                    <td align="left">
                        <input type="text" id="sn" value="{{$budget['sn']}}" class="form-control input-sm input-inline">
                    </td>
                    <td align="right">单据时间</td>
                    <td align="left">
                        <input type="text" data-toggle="date" value="{{date('Y-m-d')}}" id="date" class="form-control input-sm input-inline">
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
                    <td colspan="4">
                        <textarea class="form-control" rows="2" type="text" id="description" placeholder="暂无备注"></textarea>
                    </td>
                </tr>

                <tr>
                    <td align="right">制单人</td>
                    <td align="left"><input type="text" id="user" class="form-control input-sm input-inline" value="{{auth()->user()->nickname}}"></td>
                    <td align="right"></td>
                    <td align="left"></td>
                </tr>

            </table>
        </div>
    </div>

    <div class="panel-footer no-border">
        <button type="button" onclick="history.back();" class="btn btn-default">返回</button>
        <button type="button" onclick="saveData();" class="btn btn-success"><i class="fa fa-check-circle"></i> 提交</button>
    </div>

</div>