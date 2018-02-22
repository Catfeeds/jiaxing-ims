<script>

var supplier_id = 0;

var custom_product_name = function(editoption) {

    supplier_id = $('#supplier_price_supplier_id').val();
    if(supplier_id == '') {
        $.toastr('error', '供应商不能为空。', '错误');
        return;
    }
    
    editoption.url = 'product/product/dialog_jqgrid';
    editoption.title = '商品列表';

    return {
        editoptions: {
            dataInit: $.jgrid.celledit.dialog({
                srcField: editoption.srcField,
                mapField: editoption.mapField,
                suggest: {
                    cache: false,
                    url: editoption.url,
                    params: {supplier_id: supplier_id, type: 2, order:'asc', limit:1000}
                },
                dialog: {
                    title: editoption.title,
                    url: editoption.url,
                    params: {supplier_id: supplier_id, type: 2}
                }
            })
        }
    }
}
</script>