<div class="panel">

    <div class="wrapper">
        <form class="form-inline" method="post" id="queryform" name="queryform">
            供应商: {{Dialog::user('supplier', 'supplier_id', '', 0, 0, 200)}}
            <?php $m = date('Y-m-01'); ?>
            &nbsp;&nbsp;开始日期: <input class="form-control input-inline input-sm" data-toggle="date" type="text" name="start_at" id="start_at" placeholder="开始日期" value="{{$m}}">
            &nbsp;&nbsp;结束日期: <input class="form-control input-inline input-sm" data-toggle="date" type="text" id="end_at" placeholder="开始日期" name="end_at" value="{{date('Y-m-d', strtotime("$m +1 month -1 day"))}}">
            <a href="javascript:formQuery();" class="btn btn-sm btn-info"><i class="icon icon-search"></i> 查询</a>
        </form>
    </div>

    <div class="table-responsive">
    <table class="table b-t table-form m-b-none">
    <!--
    <tr>
        <td align="right" width="15%">单据日期</td>
        <td align="left" width="35%">
            <input type="text" name="date" data-toggle="date" value="{{date('Y-m-d')}}" id="date" class="form-control input-sm input-inline">
        </td>
        <td align="right" width="15%">制单人</td>
        <td align="left" width="35%">
            <input type="text" name="user" id="user" value="{{Auth::user()->nickname}}" class="form-control input-sm input-inline">
        </td>
    </tr>
    -->
    <tr>
        <td align="left" colspan="4">
            <div id="jqgrid-editor-container">
                <table id="grid-table"></table>
            </div>
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

<script>

var t = null;
var params = {};

(function($) {

    t = $("#grid-table");
    var model = [
        {name: "date", index: 'date', label: '单据日期', width: 100, align: 'center'},
        {name: "product_id", index: 'product_id', label: '商品ID', hidden: true},
        {name: "goods", index: 'goods', label: '商品', width: 220, align: 'left'},
        {name: "unit_name", index: 'unit_name', label: '单位', width: 80, align: 'center'},
        {name: "quantity", index: 'quantity', formatter: 'number', formatoptions:{decimalPlaces:4}, label: '数量', width: 180, align: 'right'},
        {name: "price", index: 'price', label: '单价', width: 180, align: 'right'},
        {name: "money", index: 'money', formatter: 'number', formatoptions:{decimalPlaces:4}, label: '金额', width: 180, align: 'right'},
        {name: "price_time", index: 'price_time', label: '单价时间', width: 180, align: 'center'},
        {name: "code", index: 'code', label: '商品编码', width: 80, align: 'center'},
        {name: "description", index: 'description', editable: true, label: '描述', width: 220, align: 'left'},
    ];

    var footerCalculate = function() {
        
        var quantity = $(this).getCol('quantity', false, 'sum');
        var money = $(this).getCol('money', false, 'sum');

        $(this).footerData('set',{quantity: quantity, money: money});
    }

    t.jqGrid({
        caption: '',
        datatype: 'json',
        mtype: 'POST',
        url: app.url('supplier/settlement/query'),
        colModel: model,
        cellEdit: true,
        cellurl: '',
        cellsubmit: 'clientArray',
        rowNum: 1000,
        multiselect: false,
        viewrecords: true,
        rownumbers: true,
        height: getPanelHeight(),
        footerrow: true,
        postData: params,
        gridComplete: function() {
            $(this).jqGrid('setColsWidth');
            footerCalculate.call(this);
        },
        loadComplete: function(res) {
            var me = $(this);
            me.jqGrid('initPagination', res);
        },
        // 进入编辑前调用
        beforeEditCell: function(rowid, cellname, value, iRow, iCol) {
            // 编辑前插入class
            $("#" + rowid).find('td').eq(iCol).addClass('edit-cell-item');
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
            footerCalculate.call(this);
            // 编辑cell后保存时删除class
            $("#" + rowid).find('td').eq(iCol).removeClass('edit-cell-item');
        }
    });

})(jQuery);

/* 
 * 提交数据
*/
function _submit() {

    var data = [];

    var dataset = t.jqGrid('getDatas');
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

    var query = $('#queryform').serialize();
    $.post('{{url("create")}}', query + '&' + $.param({datas: data}), function(res) {
        if(res.status) {
            location.href = res.data;
        } else {
            $.toastr('error', res, '错误');
        }
    });
}

function formQuery()
{
    var query_form = $('#queryform');
    var query = query_form.serializeArray();
    for (var i = 0; i < query.length; i++) {
        params[query[i].name] = query[i].value;
    }

    t.jqGrid('setGridParam', {
        postData: params,
        page: 1
    }).trigger('reloadGrid');
}

function getPanelHeight() {
    var list = $('#jqgrid-editor-container').position();
    return top.iframeHeight - list.top - 205;
}

// 框架页面改变大小时会调用此方法
function iframeResize() {
    // 框架改变大小时设置Panel高度
    t.jqGrid('setPanelHeight', getPanelHeight());
    // resize jqgrid大小
    t.jqGrid('resizeGrid');
}

</script>