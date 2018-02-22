<div class="panel">
    <div class="wrapper">

        <form class="form-inline" method="post" id="query-form" name="query-form">
            对账客户: {{Dialog::user('customer', 'customer', '', 0, 0, 200)}}
            <?php $m = date('Y-m-01'); ?>
            &nbsp;&nbsp;开始日期: <input class="form-control input-inline input-sm" data-toggle="date" type="text" name="start_at" id="start_at" placeholder="开始日期" value="{{$m}}">
            &nbsp;&nbsp;结束日期: <input class="form-control input-inline input-sm" data-toggle="date" type="text" id="end_at" placeholder="开始日期" name="end_at" value="{{date('Y-m-d', strtotime("$m +1 month -1 day"))}}">
            <a href="javascript:formQuery();" class="btn btn-sm btn-info"><i class="icon icon-search"></i> 查询</a>
        </form>

    </div>

    <div class="list-jqgrid">
        <table id="account-single"></table>
    </div>
</div>

<script>
var $table = null;
var params = {};

(function($) {

    $table = $("#account-single");
    var model = [
        {name: "date", index: 'date', label: '单据日期', width: 100, align: 'center'},
        {name: "ddh", index: 'ddh', label: '订单号', width: 120, align: 'left'},
        {name: "digest", index: 'digest', label: '摘要', width: 220, align: 'left'},
        {name: "zp", index: 'zp', label: '赠品金额', width: 180, align: 'right'},
        {name: "jmoney", index: 'jmoney', label: '本期应收金额', width: 180, align: 'right'},
        {name: "dmoney", index: 'dmoney', label: '本期收回金额', width: 180, align: 'right'},
        {name: "balance", index: 'balance', label: '余额', width: 180, align: 'right'}
    ];

    $table.jqGrid({
        caption: '',
        datatype: 'json',
        mtype: 'POST',
        url: app.url('customer/account/query'),
        colModel: model,
        rowNum: 1000,
        multiselect: false,
        viewrecords: true,
        rownumbers: true,
        height: getPanelHeight(),
        footerrow: false,
        postData: params,
        gridComplete: function() {
            $(this).jqGrid('setColsWidth');
        },
        loadComplete: function(res) {
            var me = $(this);
            me.jqGrid('initPagination', res);
        }
    });

})(jQuery);


function formQuery()
{
    var query_form = $('#query-form');
    var query = query_form.serializeArray();
    for (var i = 0; i < query.length; i++) {
        params[query[i].name] = query[i].value;
    }

    $table.jqGrid('setGridParam', {
        postData: params,
        page: 1
    }).trigger('reloadGrid');
}

function getPanelHeight() {
    var list = $('.list-jqgrid').position();
    return top.iframeHeight - list.top - 45;
}

// 框架页面改变大小时会调用此方法
function iframeResize() {
    // 框架改变大小时设置Panel高度
    $table.jqGrid('setPanelHeight', getPanelHeight());
    // resize jqgrid大小
    $table.jqGrid('resizeGrid');
}

</script>