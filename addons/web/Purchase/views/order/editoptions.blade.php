<script>

// 自定义计算金额大写
var custom_purchase_order_data_footer = function() {

    var value = $(this).jqGrid('footerData', 'get');
    var rmb = listView.calc.rmb(value.money);
    $("#purchase_order_amount").val(rmb);
    
}

</script>