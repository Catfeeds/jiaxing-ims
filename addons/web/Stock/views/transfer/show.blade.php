<div class="wrapper-sm">

<table class="table table-hover table-bordered m-b-sm">
<tr>
    <th>序号</th>
    <th>条码</th>
    <th>商品名称</th>
    <th>规格</th>
    <th>数量</th>
    <th>成本</th>
    <th>金额</th>
    <th>领料仓库</th>
    <th>领料日期</th>
    <th>备注</th>
</tr>
@foreach($lines as $i => $line)
<tr>
    <td class="text-center">{{$i + 1}}</td>
    <td class="text-center">{{$line->product_barcode}}</td>
    <td class="text-center">{{$line->product_name}}</td>
    <td class="text-center">{{$line->product_spec}}</td>
    <td class="text-right">{{$line->quantity}}</td>
    <td class="text-right">{{$line->cost_price}}</td>
    <td class="text-right">{{$line->cost_money}}</td>
    <td class="text-center">{{$line->warehouse_name}}</td>
    <td class="text-center">{{$line->date}}</td>
    <td class="text-left">{{$line->remark}}</td>
</tr>
@endforeach
</table>

<table class="table table-hover table-bordered m-b-none">
<tr>
    <th></th>
    <th>应退金额</th>
    <th>实际退款</th>
</tr>
<tr>
    <td width="60%"></td>
    <td class="text-right">{{$row['total_money']}}</td>
    <td class="text-right">{{$row['pay_money']}}</td>
</tr>
</table>

</div>