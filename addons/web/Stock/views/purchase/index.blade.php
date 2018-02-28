<div class="panel no-border">

    @include('menus/purchase')

    <div class="wrapper-sm">
        <div class="btn-group">
            <a class="btn btn-sm btn-default" href="javascript:actionLink('print');"><i class="fa fa-print"></i> 打印</a>
            <a class="btn btn-sm btn-default" href="javascript:actionLink('invalid');"><i class="fa fa-trash"></i> 作废</a>
        </div>
        <a class="btn btn-sm btn-default" href="javascript:actionLink('filter');"> <i class="fa fa-filter"></i> 过滤</a>
    
    </div>
        
    <div style="display:none;">
        <form id="search-form-advanced" class="search-form" action="{{url()}}" method="get">
            @include('searchForm4')
        </form>
    </div>
    
    <div class="hidden-xs b-t">
        <form id="search-form-simple" class="search-form form-inline" action="{{url()}}" method="get">
            @include('searchForm3')
        </form>
    </div>
    
    <div class="list-jqgrid">
        <table id="jqgrid"></table>
        <div id="jqgrid-page"></div>
    </div>
        
</div>

<script>
var routes = {
    index: 'stock/purchase/index',
    invalid: 'stock/purchase/invalid',
    show: 'stock/purchase/show',
    export: 'stock/purchase/export',
};
var $table = null;
var params = paramsSimple = {{json_encode($search['query'])}};
var search = null;
var searchSimple = null;
var showDialog = null;

$.extend($.fn.fmatter, {
    arear_money: function(value, options, rowdata) {
        if(value > 0) {
            return '<span style="color:#f00;">'+value +'</span>';
        } else {
            return value;
        }
    }
});

(function($) {
    
    var data = {{json_encode($search['forms'])}};

    search = $('#search-form-advanced').searchForm({
        data: data,
        init: function(e) {
            var self = this;
        }
    });

    searchSimple = $('#search-form-simple').searchForm({
        data: data,
        init: function(e) {
            var self = this;
        }
    });
    searchSimple.find('#search-submit').on('click', function() {
        var query = searchSimple.serializeArray();
        $.map(query, function(row) {
            paramsSimple[row.name] = row.value;
        });
        $table.jqGrid('setGridParam', {
            postData: paramsSimple,
            page: 1
        }).trigger('reloadGrid');

        return false;
    });

    $table = $("#jqgrid");

    var model = {{json_encode($columns)}};

    $table.jqGrid({
        caption: '',
        datatype: 'json',
        mtype: 'GET',
        url: app.url(routes.index),
        colModel: model,
        rowNum: 25,
        autowidth: true,
        multiselect: true,
        viewrecords: true,
        rownumbers: false,
        width: '100%',
        height: getPanelHeight(),
        footerrow: false,
        postData: params,
        pager: '#jqgrid-page',
        ondblClickRow: function(rowid) {
            var row = $(this).getRowData(rowid);
            actionLink('show', row.id);
        },
        gridComplete: function() {
            $(this).jqGrid('setColsWidth');
        }
    });

})(jQuery);

function actionLink(action, id) {

    if(action == 'show') {
        showDialog = viewBox('show','采购明细', app.url(routes.show, {id: id}), 'lg');
        return;
    }

    if(action == 'invalid') {

        var selections = $table.jqGrid('getSelections');
        if (selections.length == 0) {
            $.toastr('error', '最少选择一行记录。', '提醒');
            return;
        }

        formBox('作废单据', app.url(routes.invalid, {id: selections[0].id}), 'purchase-invalid-form', function(res) {
            if(res.status) {
                $.toastr('success', res.data, '提醒');
                $table.jqGrid('setGridParam', {
                    postData: params,
                    page: 1
                }).trigger('reloadGrid');
                $(this).dialog("close");
            } else {
                $.toastr('error', res.data, '提醒');
            }
        });
        return;
    }

    if(action == 'filter') {
        // 过滤数据
        $(search).dialog({
            title: '数据过滤',
            modalClass: 'no-padder',
            buttons: [{
                text: "确定",
                'class': "btn-info",
                click: function() {
                    var query = search.serializeArray();
                    $.map(query, function(row) {
                        params[row.name] = row.value;
                    });
                    $table.jqGrid('setGridParam', {
                        postData: params,
                        page: 1
                    }).trigger('reloadGrid');
                    return false;
                }
            },{
                text: "取消",
                'class': "btn-default",
                click: function() {
                    $(this).dialog("close");
                }
            }]
        });
        return;
    }
}

function getPanelHeight() {
    var list = $('.list-jqgrid').position();
    return top.iframeHeight - list.top - 92;
}

$(window).on('resize', function() {
	$table.jqGrid('setGridHeight', getPanelHeight());
});
</script>