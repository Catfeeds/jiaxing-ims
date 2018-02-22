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
            <th data-field="supplier_name" data-sortable="true" data-align="center">供应商</th>
            <th data-field="id" data-width="60" data-sortable="true" data-align="center">编号</th>
        </tr>
        </thead>
    </table>
</div>

<script>
(function($) {
    var $table = $('#dialog-product');
    var $scope = {};
    var $item  = {};

    $.dialogProduct = {
        setData: function(scope, row) {
            $scope = scope;
            $item  = row;
        }
    };

    $table.bootstrapTable({
        iconSize:'sm',
        onLoadSuccess: function(res) {

            for (var i = 0; i < res.data.length; i++) {
                if(res.data[i].id == $item.product_id) {
                    $table.bootstrapTable('check',i);
                }
            }
        },
        onCheck: function(row) {
            $scope.$apply(function() {
                $.map($scope.mapping, function(key, val) {
                    $item[val] = row[key];
                });
            });
        },
        onUncheck: function(row) {
            $scope.$apply(function() {
                $.map($scope.mapping, function(key, val) {
                    $item[val] = null;
                });
            });
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
