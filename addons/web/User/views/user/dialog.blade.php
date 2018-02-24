
<div class="padder">
    <div class="m-t-sm m-b-sm">
        <form id="user-dialog-search-form" name="user_dialog_search_form" class="form-inline" method="get">
            <!--
            <div class="btn-group" data-toggle="buttons">
                <label class="btn btn-sm btn-default active">
                    <input type="radio" name="group_id" value="1">用户
                </label>
                <label class="btn btn-sm btn-default">
                    <input type="radio" name="group_id" value="2">客户
                </label>
                <label class="btn btn-sm btn-default">
                    <input type="radio" name="group_id" value="4">供应商
                </label>
            </div>
            -->
            @include('searchForm')
        </form>
    </div>

    <table id="user-dialog">
    </table>
    <div id="user-dialog-page"></div>
</div>

<script>
(function($) {
    var selectBox = {};
    var params = {{json_encode($query)}};
    var sid    = params.prefix == 1 ? 'sid' : 'id';
    var $table = $("#user-dialog");

    var model = [
        {name: "name", index: 'user.name', label: '名字', minWidth: 220, align: 'left'},
        {name: "login", index: 'user.login', label: '账号', width: 180, align: 'left'},
        {name: "status", index: 'user.status', label: '状态', width: 80, align: 'center'},
        {name: "id", index: 'user.id', label: 'ID', width: 60, align: 'center'}
    ];

    $table.jqGrid({
        caption: '',
        datatype: 'json',
        mtype: 'POST',
        url: app.url('user/user/dialog'),
        colModel: model,
        rowNum: 25,
        multiboxonly: params.multi == 0 ? true : false,
        multiselect: true,
        viewrecords: true,
        rownumbers: false,
        height: 340,
        footerrow: false,
        postData: params,
        pager: '#user-dialog-page',
        gridComplete: function() {
            // 单选时禁用全选按钮
            if(params.multi == 0) {
                $("#cb_" + this.p.id).prop('disabled', true);
            }
            $(this).jqGrid('setColsWidth');
        },
        loadComplete: function(res) {
            var me = $(this);
            me.jqGrid('initPagination', res);
            // 设置默认选中
            setSelecteds(res);
        },
        // 双击选中
        ondblClickRow: function(id) {
            if(params.multi == 1) {
                $table.jqGrid('setSelection', id);
            }
            getSelecteds();
        },
    });

    function setSelecteds(res) {
        var ids = $('#'+params.id).val();
        ids = ids.split(',');
        $.each(ids, function(k, v) {
            if(v) {
                $table.jqGrid('setSelection', v);
            }
        });
    }

    function getSelecteds() {
        var rows = $table.jqGrid('getSelections');
        if(params.multi == 0) {
            if(rows.length > 1) {
                $.toastr('error', '只能选择一项。', '错误');
                return false;
            }
        }

        var id = [], text = [];
        for (var i = 0; i < rows.length; i++) {
            id.push(rows[i][sid]);
            text.push(rows[i].text);
        }

        // 回写数据
        $('#'+params.id).val(id.join(','));
        $('#'+params.id+'_text').text(text.join(','));

        // 关闭窗口
        $('#modal-dialog-user').dialog("close");

        return true;
    }

    window.selectBox = {getSelecteds: getSelecteds};

    var data = {{json_encode($search['forms'])}};
    var search = $('#user-dialog-search-form').searchForm({
        data: data,
        init:function(e) {}
    });

    search.find('#search-submit').on('click', function() {
        var query = search.serializeArray();
        $.map(query, function(row) {
            params[row.name] = row.value;
        });

        $table.jqGrid('setGridParam', {
            postData: params,
            page: 1
        }).trigger('reloadGrid');
        return false;
    });
})(jQuery);

</script>