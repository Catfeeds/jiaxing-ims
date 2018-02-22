<div class="padder">

    <div class="m-t-sm m-b-sm">
        <form id="search-form" name="mysearch" class="form-inline" method="get">
            @include('searchForm')
        </form>
    </div>

    <table id="dialog-product" data-formatter="stateFormatter" data-single-select="true" data-click-to-select="true" data-method="post" data-url="{{url('supplier/product/dialog')}}" data-height="380" data-side-pagination="server" data-pagination="true">
        <thead>
        <tr>
            <th data-field="state" data-checkbox="true"></th>
            <th data-field="name" data-sortable="true" data-align="left">名称</th>
            <th data-field="spec" data-sortable="true" data-align="left">规格</th>
            <th data-field="id" data-width="60" data-sortable="true" data-align="center">编号</th>
        </tr>
        </thead>
    </table>
</div>

<script>
(function($) {
    
    var $table = $('#dialog-product');

    var $scope  = {};
    var $item   = {};

    var params = {{json_encode($get)}};

    var selected = {};

    function getSelected()
    {
        selected = {};
        var id   = $('#{{$get["id"]}}').val();
        var text = $('#{{$get["id"]}}_text').text();

        if(id == '' || id == 0) {
            return;
        }

        id   = id.split(',');
        text = text.split(',');
        for (var i = 0; i < id.length; i++) {
            selected[id[i]] = text[i];
        }
    }

    function setSelected() {
        var id   = [];
        var text = [];
        $.each(selected, function(k, v) {
            id.push(k);
            text.push(v);
        })
        $('#{{$get["id"]}}').val(id);
        $('#{{$get["id"]}}_text').text(text);
    }

    function setRow(row)
    {
        if(params.multi == 0) {
            selected = {};
        }
        selected[row.id] = row.name;
        setSelected();
    }

    function unsetRow(row)
    {
        $.each(selected, function(id) {
            if(id == row.id) {
                delete selected[id];
            }
        });
        setSelected();
    }

    $table.bootstrapTable({
        iconSize:'sm',
        onLoadSuccess: function(res) {

            getSelected();

            $.each(selected, function(j) {
                for (var i = 0; i < res.data.length; i++) {
                    if(res.data[i].id == j) {
                        $table.bootstrapTable('check', i);
                   }
                }
            });
        },
        onCheck: function(row) {
            setRow(row);
        },
        onUncheck: function(row) {
            unsetRow(row);
        },
        onCheckAll: function(rows) {
            for (var i = 0; i < rows.length; i++) {
                setRow(rows[i]);
            }
        },
        onUncheckAll: function(rows) {
            for (var i = 0; i < rows.length; i++) {
                unsetRow(rows[i]);
            }
        }
    });

    var data = {{json_encode($search['forms'])}};
    var search = $('#search-form').searchForm({
        data:data,
        init:function(e) {
            var self = this;
            e.category = function(i) { 
                $.get(app.url('supplier/product-category/dialog'),function(res) {
                    var option = '';
                    $.map(res.rows, function(row) {
                        option += '<option value="'+row.id+'">'+row.layer_space + row.name+'</option>';
                    });
                    self._select(option, i);
                });
            }
        }
    });

    search.find('#search-submit').on('click', function() {
        var params = search.serializeArray();
        $.map(params, function(row) {
            data[row.name] = row.value;
        });
        $table.bootstrapTable('refresh', {
            url:app.url('supplier/product/dialog', data),
        });
        return false;
    });
})(jQuery);

</script>