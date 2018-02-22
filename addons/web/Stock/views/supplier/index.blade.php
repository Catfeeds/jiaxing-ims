<div class="panel no-border">

    <div class="wrapper-sm">

        <div class="btn-group">
            <a class="btn btn-sm btn-default" href="javascript:actionLink('create');"><i class="fa fa-plus"></i> 新建</a>
            <a class="btn btn-sm btn-default" href="javascript:actionLink('edit');"><i class="fa fa-edit"></i> 编辑</a>
            <a class="btn btn-sm btn-default" href="javascript:actionLink('delete');"><i class="fa fa-remove"></i> 删除</a>
        </div>

        <a class="btn btn-sm btn-default" href="javascript:actionLink('export');"><i class="fa fa-share"></i> 导出</a>
        <a class="btn btn-sm btn-default" href="javascript:actionLink('filter');"> <i class="fa fa-filter"></i> 过滤</a>
    
    </div>
        
    <div style="display:none;">
        <form id="search-form-advanced" class="search-form" action="{{url()}}" method="get">
            @include('searchForm4')
        </form>
    </div>
    
    <div class="hidden-xs">
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
    index: 'stock/supplier/index',
    create: 'stock/supplier/create',
    delete: 'stock/supplier/delete',
    edit: 'stock/supplier/edit',
    show: 'stock/supplier/show',
    export: 'stock/supplier/export',
};
var $table = null;
var params = paramsSimple = {{json_encode($search['query'])}};
var search = null;
var searchSimple = null;

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
            actionLink('edit', row.id);
        },
        gridComplete: function() {
            $(this).jqGrid('setColsWidth');
        }
    });

})(jQuery);

function actionLink(action, id) {

    if(action == 'export') {
        $table.jqGrid('exportGrid');
        return;
    }

    if(action == 'show') {
        viewBox('show','查看', app.url(routes.show, {id: id}));
        return;
    }

    if(action == 'create') {
        formBox('新建', app.url(routes.create), 'stock-warehouse-form', function(res) {
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

    if(action == 'edit') {
        if(id == undefined) {
            var selections = $table.jqGrid('getSelections');
            if(selections.length) {
                id = selections[0].id;
            } else {
                $.toastr('error', '必须选择一行记录。', '错误');
                return;
            }
        }
        formBox('编辑', app.url(routes.edit, {id: id}), 'stock-warehouse-form', function(res) {
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

    if(action == 'delete') {
        var selections = $table.jqGrid('getSelections');
        var query = [];
        $.each(selections, function(i, selection) {
            query.push(selection.id);
        });
        if(query.length) {
            $.messager.confirm('操作确认', '确定要删除吗？', function() {
                $.post(app.url(routes.delete), {id: query}, function(res) {
                    if(res.status) {
                        $.toastr('success', res.data, '提醒');
                        $table.jqGrid('setGridParam', {
                            postData: params,
                            page: 1
                        }).trigger('reloadGrid');
                    } else {
                        $.toastr('error', res.data, '提醒');
                    }
                });
            });

        } else {
            $.toastr('error', '最少选择一行记录。', '错误');
        }
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
    return top.iframeHeight - list.top - 95;
}

$(window).on('resize', function() {
	$table.jqGrid('setGridHeight', getPanelHeight());
});
</script>