<div class="panel">

    <div class="wrapper">
        @include('order/count_query')
    </div>

<form class="form-horizontal" name="myform" id="myform" method="post">

    <div class="table-responsive">
        @foreach($years as $year)
        <table class="table m-b-none table-bordered b-t">

            <thead>
                <tr>
                    <th align="center" colspan="15"><h5>{{$supplier->user->nickname}} - {{$year}}年供应明细</h5></th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <th align="center" width="200">商品</th>
                    <th align="center">数量</th>
                    @for($i = 1; $i <= 12; $i++)
                    <th align="center">{{$i}}月</th>
                    @endfor
                    <th align="center">行小计</th>
                </tr>

                @foreach($rows as $product_id => $product)
                <tr>
                    <td align="center" style="vertical-align:middle;" rowspan="3">{{$product->name}}</td>
                    <td align="center">总周期计划</td>
                    @for($i = 1; $i <= 12; $i++)
                    <td align="right">
                        @number($plan_sum['data'][$product_id][$year][$i])
                    </td>
                    @endfor
                    <th align="right">@number(array_sum((array)$plan_sum['data'][$product_id][$year]))</th>
                </tr>
                <tr>
                    <td align="center">总订单</td>
                    @for($i = 1; $i <= 12; $i++)
                    <td align="right">
                        @number($order_sum['data'][$product_id][$year][$i])
                    </td>
                    @endfor
                    <th align="right">@number(array_sum((array)$order_sum['data'][$product_id][$year]))</th>
                </tr>
                <tr>
                    <td align="center">总入库</td>
                    @for($i = 1; $i <= 12; $i++)
                    <td align="right">
                        @number($stock_sum['data'][$product_id][$year][$i])
                    </td>
                    @endfor
                    <th align="right">@number(array_sum((array)$stock_sum['data'][$product_id][$year]))</th>
                </tr>
                @endforeach
                
                <tr>
                    <th align="center"></th>
                    <th align="center">总周期计划合计</th>
                    @for($i = 1; $i <= 12; $i++)
                    <th align="right">
                        @number($plan_sum['total'][$year][$i])
                    </th>
                    @endfor
                    <th align="right">@number(array_sum((array)$plan_sum['total'][$year]))</th>
                </tr>

                <tr>
                    <th align="center"></th>
                    <th align="center">总订单合计</th>
                    @for($i = 1; $i <= 12; $i++)
                    <th align="right">
                        @number($order_sum['total'][$year][$i])
                    </th>
                    @endfor
                    <th align="right">@number(array_sum((array)$order_sum['total'][$year]))</th>
                </tr>

                <tr>
                    <th align="center"></th>
                    <th align="center">总入库合计</th>
                    @for($i = 1; $i <= 12; $i++)
                    <th align="right">
                        @number($stock_sum['total'][$year][$i])
                    </th>
                    @endfor
                    <th align="right">@number(array_sum((array)$stock_sum['total'][$year]))</th>
                </tr>
                
            </tbody>
        </table>
        @endforeach
    </div>

</form>

</div>
</div>