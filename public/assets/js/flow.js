(function (window) {

    var flow = {
        turn: function (table) {

            var key = $('#' + table + '_form').find('#master_key').val();
            var url = app.url('flow/form/turn', { key: key });
            $('#form-turn').__dialog({
                title: '单据审批',
                url: url,
                buttons: [{
                    text: "提交",
                    'class': "btn-success",
                    click: function () {

                        var gets = {};
                        var query = $('#myturn,#' + table + '_form').serialize();

                        // 循环子表
                        var tables = jqgridFormList[table];
                        if (tables.length) {
                            for (var i = 0; i < tables.length; i++) {
                                var t = tables[i];
                                var dataset = t.jqGrid('getRowsData');
                                var p = t[0].p;
                                var deleteds = p.deleteds;
                                if (dataset.v === true) {
                                    if (dataset.data.length == 0) {
                                        $.toastr('error', p.tableTitle + '不能为空。', '错误');
                                        return;
                                    } else {
                                        gets[p.table] = { rows: dataset.data, deleteds: deleteds };
                                    }
                                } else {
                                    return;
                                }
                            }
                        }

                        $.post(app.url('flow/form/turn'), query + '&' + $.param(gets), function (res) {
                            if (res.status) {

                                if (res.data) {
                                    // 返回页面刷新
                                    self.location.href = res.data;
                                }

                            } else {
                                $.toastr('error', res.data, '错误');
                            }
                        }, 'json');
                    }
                }, {
                    text: "取消",
                    'class': "btn-default",
                    click: function () {
                        $(this).dialog("close");
                    }
                }]
            });

        }, freeturn: function (table) {

            var key = $('#' + table + '_form').find('#master_key').val();
            var url = app.url('flow/form/freeturn', { key: key });
            $('#form-turn').__dialog({
                title: '单据审批',
                url: url,
                buttons: [{
                    text: "提交",
                    'class': "btn-success",
                    click: function () {

                        var gets = {};
                        var query = $('#myturn,#' + table + '_form').serialize();

                        // 循环子表
                        var tables = jqgridFormList[table];
                        if (tables.length) {
                            for (var i = 0; i < tables.length; i++) {
                                var t = tables[i];
                                var dataset = t.jqGrid('getRowsData');
                                var p = t[0].p;
                                var deleteds = p.deleteds;
                                if (dataset.v === true) {
                                    if (dataset.data.length == 0) {
                                        $.toastr('error', p.tableTitle + '不能为空。', '错误');
                                        return;
                                    } else {
                                        gets[p.table] = { rows: dataset.data, deleteds: deleteds };
                                    }
                                } else {
                                    return;
                                }
                            }
                        }

                        $.post(app.url('flow/form/freeturn'), query + '&' + $.param(gets), function (res) {
                            if (res.status) {

                                if (res.data) {
                                    // 返回页面刷新
                                    self.location.href = res.data;
                                }

                            } else {
                                $.toastr('error', res.data, '错误');
                            }
                        }, 'json');
                    }
                }, {
                    text: "取消",
                    'class': "btn-default",
                    click: function () {
                        $(this).dialog("close");
                    }
                }]
            });

        }, draft: function (table) {

            var gets = {};
            var query = $('#myturn,#' + table + '_form').serialize();

            // 循环子表
            var tables = jqgridFormList[table];
            if (tables.length) {
                for (var i = 0; i < tables.length; i++) {
                    var t = tables[i];
                    var dataset = t.jqGrid('getRowsData');
                    var p = t[0].p;
                    var deleteds = p.deleteds;
                    if (dataset.v === true) {
                        if (dataset.data.length == 0) {
                            $.toastr('error', p.tableTitle + '不能为空。', '错误');
                            return;
                        } else {
                            gets[p.table] = { rows: dataset.data, deleteds: deleteds };
                        }
                    } else {
                        return;
                    }
                }
            }

            $.post(app.url('flow/form/draft'), query + '&' + $.param(gets), function (res) {
                if (res.status) {
                    self.location.href = res.data;
                } else {
                    $.toastr('error', res.data, '错误');
                }
            }, 'json');

        }, remove: function (table) {

            var me = $('#'+ table + '_form');
            var rows = me.find('input[name="id[]"]:checked');
            if (rows.length == 0) {
                $.toastr('error', '最少选择一行记录。', '错误');
                return;
            }

            var formData = me.serialize();
            $.messager.confirm('操作确认', '确定要删除吗？', function () {

                $.post(app.url('flow/form/delete', {table: table}), formData, function (res) {
                    if (res.status) {
                        location.reload();
                    } else {
                        $.toastr('error', res.data, '错误');
                    }
                }, 'json');

            });

        }, store: function (table) {

            var gets = {};
            var query = $('#myturn,#' + table + '_form').serialize();

            // 循环子表
            var tables = jqgridFormList[table] || [];
            if (tables.length) {
                for (var i = 0; i < tables.length; i++) {
                    var t = tables[i];
                    var dataset = t.jqGrid('getRowsData');
                    var p = t[0].p;
                    var deleteds = p.deleteds;
                    if (dataset.v === true) {
                        if (dataset.data.length == 0) {
                            $.toastr('error', p.tableTitle + '不能为空。', '错误');
                            return;
                        } else {
                            gets[p.table] = { rows: dataset.data, deleteds: deleteds };
                        }
                    } else {
                        return;
                    }
                }
            }

            $.post(app.url('flow/form/store'), query + '&' + $.param(gets), function (res) {
                if (res.status) {

                    if (res.data) {
                        // 返回页面刷新
                        self.location.href = res.data;
                    }

                } else {
                    $.toastr('error', res.data, '提醒');
                }
            }, 'json');

        },
        turnlog: function (key) {
            var url = app.url('flow/form/log', { key: key });
            $('#form-turn').__dialog({
                title: '审批记录',
                url: url,
                buttons: [{
                    text: "取消",
                    'class': "btn-default",
                    click: function () {
                        $(this).dialog("close");
                    }
                }]
            });
        },

        quickForm: function(table, title, url, size) {
            
            size = size || 'md';

            $('#quick-form').__dialog({
                title: title,
                url: url,
                dialogClass: 'modal-' + size,
                destroy : true,

                buttons: [{
                    text: "提交",
                    'class': "btn-info",
                    click: function(e) {

                        var gets = $('#'+table+'_form').serialize();
                        var rows = {};

                        // 循环子表
                        var tables = jqgridFormList[table];

                        if(tables.length) {

                            for(var i=0; i < tables.length; i++) {
                                var t = tables[i];
                                var p = t[0].p;
                                var deleteds = p.deleteds;

                                // 有选择按钮
                                if(p.multiselect == true) {

                                    var dataset = t.jqGrid('getSelections');

                                    if(dataset.length == 0) {
                                        $.toastr('error', p.tableTitle + '不能为空。', '错误');
                                        return;
                                    } else {
                                        rows[p.table] = {rows: dataset, deleteds: deleteds};
                                    }

                                } else {

                                    var dataset = t.jqGrid('getDatas');
                                    if(dataset.v === true) {
                                        if(dataset.data.length == 0) {
                                            $.toastr('error', p.tableTitle + '不能为空。', '错误');
                                            return;
                                        } else {
                                            rows[p.table] = {rows: dataset.data, deleteds: deleteds};
                                        }
                                    } else {
                                        return;
                                    }
                                }
                            }
                        }

                        gets = gets +'&'+ $.param(rows);

                        var btn = $(e.target);
                        btn.prop('disabled', true);
                        btn.text('提交中');

                        $.post(app.url('flow/form/store'), gets, function(res) {

                            btn.prop('disabled', false);
                            btn.text('提交');

                            if(res.status) {
                                location.reload();
                            } else {
                                $.toastr('error', res.data, '提醒');
                            }

                        },'json');

                    }
                },{
                    text: "取消",
                    'class': "btn-default",
                    click: function(e) {
                        $(this).dialog("close");
                    }
                }]
            });
        }
    }

    window.flow = flow;

})(window);