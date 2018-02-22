<div class="panel">

    <div class="wrapper">
        @include('order/count_query')
    </div>

<form class="form-horizontal" name="myform" id="myform" method="post">

    <div class="table-responsive">
        <table class="table table-bordered">

            <thead>
                <tr>    
                    <th align="center">序号</th>
                    <th align="left">供应商</th>
                    <th align="center">总周期计划数量</th>
                    <th align="center">总订单数量</th>
                    <th align="center">总入库数量</th>
                    <th align="center"></th>
                </tr>
            </thead>

            <tbody>

                <?php
                    $plan_total = $order_total = $stock_total = 0;
                ?>

                @foreach($suppliers as $i => $supplier)

                <?php

                $plan_sum = $order_sum = $stock_sum = 0;

                foreach ($supplier->plans as $plan) {
                    $plan_sum += $plan->datas->sum('quantity');
                }
                foreach ($supplier->orders as $order) {
                    $order_sum += $order->datas->sum('quantity');
                }
                foreach ($supplier->stocks as $stock) {
                    $stock_sum += $stock->datas->sum('quantity');
                }

                $plan_total  += $plan_sum;
                $order_total += $order_sum;
                $stock_total += $stock_sum;
                ?>
                
                <tr>
                    <td align="center">
                        {{$i + 1}}
                    </td>
                    <td align="left">
                        {{$supplier->user->nickname}}
                    </td>
                    <td align="right">
                        @number($plan_sum)
                    </td>
                    <td align="right">
                        @number($order_sum)
                    </td>
                    <td align="right">
                        @number($stock_sum)
                    </td>
                    <td align="center">
                        <a href="{{url('count_show', ['supplier_id' => $supplier->id])}}" class="option">详细</a>
                    </td>
                </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <th align="center" colspan="2">合计</th>
                    <th align="right">@number($plan_total)</th>
                    <th align="right">@number($order_total)</th>
                    <th align="right">@number($stock_total)</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>


</form>

</div>
</div>