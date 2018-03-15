<div class="panel no-border">

    @include('tabs', ['tabKey' => 'stock.check'])

    <div class="wrapper-sm">
        <a class="btn btn-sm btn-default" href="javascript:history.back();"><i class="fa fa-remove"></i> 取消</a>
        <a class="btn btn-sm btn-info" href="javascript:formCreate(8);"> <i class="fa fa-pencil-square-o"></i> 生成盘盈</a>
        <a class="btn btn-sm btn-info" href="javascript:formCreate(9);"> <i class="fa fa-pencil-square-o"></i> 生成盘亏</a>
    </div>

    <div class="wrapper-sm b-t">

        <form method="post" class="form-inline" action="{{url()}}" id="query-check-create">

            <div class="form-group">
                <label for="warehouse_id">仓库</label>
                <select class="form-control input-sm" name="warehouse_id" id="warehouse_id">
                    <option value=""> - </option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{$warehouse['id']}}">{{$warehouse['name']}}</option>
                    @endforeach
                </select>
            </div>

            <span class="hidden-xs">&nbsp;</span>

            <div class="form-group">
                <label for="warehouse_id">商品类别</label>
                <select class="form-control input-sm" name="category_id" id="category_id">
                    <option value=""> - </option>
                    @foreach($categorys as $category)
                        <option value="{{$category['id']}}">{{$category['layer_space']}}{{$category['name']}}</option>
                    @endforeach
                </select>
            </div>
            
            <button id="query-check-submit" class="btn btn-sm btn-default"> <i class="fa fa-search"></i> 查询</button>
        </form>

        <div id="jqgrid-editor-container" class="m-t-sm m-b">
            <table id="jqgrid-check-create"></table>
        </div>

    </div>

</div>

<script type="text/javascript">

var jqgrid_check_create = null;
var jqgrid_check_rows   = [];
var stock_type_id       = 0;
var columns = {{json_encode($columns)}};

$(function() {

    var footerRowRender = function(rowid) {
        var stock_quantity = $(this).jqGrid('getCell', rowid, 'stock_quantity');
        var quantity   = $(this).jqGrid('getCell', rowid, 'quantity');
        $(this).jqGrid('setCell', rowid, 'check', quantity - stock_quantity);
    };
    var footerRender = function() {
        var stock_quantity = $(this).getCol('stock_quantity', false, 'sum');
        var quantity       = $(this).getCol('quantity', false, 'sum');
        var check          = $(this).getCol('check', false, 'sum');
        $(this).footerData('set',{product_name:'合计:', stock_quantity: stock_quantity, quantity: quantity, check: check});
    }

    jqgrid_check_create = $('#jqgrid-check-create').jqGrid({
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
        height: getPanelHeight(),
        gridComplete: function() {
            $(this).jqGrid('setColsWidth');
        },
        // 进入编辑前调用
        beforeEditCell: function(rowid, cellname, value, iRow, iCol) {
            // 编辑前插入class
            $(this.rows[iRow]).find('td').eq(iCol).addClass('edit-cell-item');
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
            footerRowRender.call(this, rowid);
            footerRender.call(this);
            // 编辑cell后保存时删除class
            $(this.rows[iRow]).find('td').eq(iCol).removeClass('edit-cell-item');
        }
    });

    $('#query-check-submit').on('click', function() {
        var query = $('#query-check-create').serialize();
        $.post('{{url("create")}}', query, function(res) {
            if(res.status) {
                jqgrid_check_create.jqGrid('clearGridData').jqGrid('setGridParam', {
                    datatype: 'local',
                    data: res.data
                }).trigger('reloadGrid');
            }
        });
        return false;
    });

});

function formCreate(type) {

    var res = jqgrid_check_create.jqGrid('getRows', 'product_id');
    if (res.data.length == 0) {
        $.toastr('error', '盘点仓库不能为空。', '错误');
        return;
    } else {
        stock_type_id = type;
        jqgrid_check_rows = [];
        if (type == 8) {
            var title = '库存盘盈单';
            $.each(res.data, function(k, v) {
                if(v.check > 0) {
                    v.quantity = Math.abs(v.check);
                    jqgrid_check_rows.push(v);
                }
            });
        }
        if (type == 9) {
            var title = '库存盘亏单';
            $.each(res.data, function(k, v) {
                if(v.check < 0) {
                    v.quantity = Math.abs(v.check);
                    jqgrid_check_rows.push(v);
                }
            });
        }
    }

    if (jqgrid_check_rows.length == 0) {
        $.toastr('error', '盘点仓库数量不能为空。', '错误');
        return;
    }

    formDialog({
        title: title,
        url: app.url('stock/check/store'),
        id: 'stock-check-store',
        formId: 'check-store-form',
        dialogClass: 'modal-lg',
        onBeforeSend: function(query) {
            var res = jqgrid_check_store.jqGrid('getRows', 'product_id');
            if (res.errors.length > 0) {
                var v = res.errors[0];
                jqgrid_check_store.jqGrid("editCell", v[3], v[4], true);
                $.toastr('error', v[1], '错误');
                return false;
            }
            $.each(select2List, function(k, v) {
                query[k] = v.el.select2('val');
            });
            query.stock_line = res.data;
            return query;
        },
        onSuccess: function(res) {
            if (res.status) {
                $.toastr('success', res.data, '提醒');
                $(this).dialog('close');
            } else {
                $.toastr('error', res.data, '提醒');
            }
        }
    });
}

function getPanelHeight() {
    var list = $('#jqgrid-editor-container').position();
    return top.iframeHeight - list.top - 106;
}

$(window).on('resize', function() {
	jqgrid_check_create.jqGrid('setGridHeight', getPanelHeight());
});
</script>