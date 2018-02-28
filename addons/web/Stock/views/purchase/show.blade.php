<div class="wrapper-sm">

<table class="table table-hover table-bordered m-b-sm">
<tr>
    <th>序号</th>
    <th>条码</th>
    <th>商品名称</th>
    <th>规格</th>
    <th>数量</th>
    <th>进价</th>
    <th>金额</th>
    <th>供应商</th>
    <th>采购仓库</th>
    <th>时间</th>
    <th>备注</th>
</tr>
@foreach($lines as $i => $line) 
<tr>
    <td style="text-align:center;">{{$i + 1}}</td>
    <td style="text-align:center;">{{$line->barcode}}</td>
    <td style="text-align:center;">{{$line->product_name}}</td>
    <td style="text-align:center;">{{$line->product_spec}}</td>
    <td style="text-align:right;">{{$line->quantity}}</td>
    <td style="text-align:right;">{{$line->price}}</td>
    <td style="text-align:right;">{{$line->money}}</td>
    <td style="text-align:center;">{{$line->supplier_name}}</td>
    <td style="text-align:center;">{{$line->warehouse_name}}</td>
    <td style="text-align:center;">{{$line->date}}</td>
    <td style="text-align:left;">{{$line->remark}}</td>
</tr>
@endforeach
</table>

<table class="table table-hover table-bordered m-b-none">
<tr>
    <th>应收金额</th>
    <th>优惠金额</th>
    <th>付款金额</th>
    <th>欠款金额</th>
</tr>
<tr>
    <td style="text-align:right;">{{$row['rec_money']}}</td>
    <td style="text-align:right;">{{$row['discount_money']}}</td>
    <td style="text-align:right;">{{$row['pay_money']}}</td>
    <td style="text-align:right;">
        @if($row['arear_money'] > 0)
        <span class="text-danger">
        @else
        <span>
        @endif
        {{$row['arear_money']}}
        </span>
    </td>
</tr>
</table>

@if((int)$row['arear_money'] && $trash == false)
<a class="btn btn-sm btn-info m-t-sm" href="javascript:repay();"> <i class="fa fa-reply"></i> 还款</a>
@endif

<script type="text/javascript">
var stock_id = "{{$row['id']}}";
function repay() {
    formBox('还款', app.url('stock/purchase-repayment/create', {stock_id: stock_id}), 'purchase-repayment-form', function(res) {
        if(res.status) {
            $.toastr('success', res.data, '提醒');
            $table.trigger('reloadGrid');
            $(this).dialog("close");
            showDialog.dialog("close");
        } else {
            $.toastr('error', res.data, '提醒');
        }
    });
}
</script>