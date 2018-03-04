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
    <td style="text-align:center;">{{$i + 1}}</td>
    <td style="text-align:center;">{{$line->product_barcode}}</td>
    <td style="text-align:center;">{{$line->product_name}}</td>
    <td style="text-align:center;">{{$line->product_spec}}</td>
    <td style="text-align:right;">{{$line->quantity}}</td>
    <td style="text-align:right;">{{$line->cost_price}}</td>
    <td style="text-align:right;">{{$line->cost_money}}</td>
    <td style="text-align:center;">{{$line->warehouse_name}}</td>
    <td style="text-align:center;">{{$line->date}}</td>
    <td style="text-align:left;">{{$line->remark}}</td>
</tr>
@endforeach
</table>

</div>