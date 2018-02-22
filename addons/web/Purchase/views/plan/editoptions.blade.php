<script>

var custom_goods_name = function(editoption) {
    
    editoption.url = 'product/product/dialog_jqgrid';
    editoption.title = '商品列表';

    return {
        editoptions: {
            dataInit: $.jgrid.celledit.dialog({
                srcField: editoption.srcField,
                mapField: editoption.mapField,
                suggest: {
                    url: editoption.url,
                    params: {yonyou:'a',owner_id:'{{auth()->id()}}', type: 2, order:'asc', limit:1000}
                },
                dialog: {
                    title: editoption.title,
                    url: editoption.url,
                    params: {yonyou:'a',owner_id:'{{auth()->id()}}', type: 2}
                }
            })
        }
    }
}
</script>