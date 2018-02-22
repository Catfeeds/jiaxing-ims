<form class="form-horizontal" name="myform" id="myform" method="post">

<div class="panel">
    <div class="table-responsive">
        <table class="table table-form">
        	<tr>
        	    <th colspan="4" align="left">供应商资料</th>
        	</tr>

            <tr>
                <td width="15%" align="right">供应商名称</td>
                <td width="35%">
                    {{$order->supplier->user->nickname}}
                </td>

                <td width="15%" align="right">供应商代码</td>
                <td width="35%">
                    {{$order->supplier->user->username}}
                </td>
            </tr>

            <tr>
                <td align="right">法人代表</td>
                <td>
                    {{$order->supplier->legal}}
                </td>
                <td align="right">公司性质</td>
                <td>
                    {{$order->supplier->nature}}
                </td>
            </tr>

            <tr>
                <td align="right">公司电话</td>
                <td>
                    {{$order->supplier->user->tel}}
                </td>
                <td align="right">公司传真</td>
                <td align="left">
                    {{$order->supplier->user->fax}}
                </td>
            </tr>

            <tr>
                <td align="right">公司税号</td>
                <td>
                    {{$order->supplier->tax_number}}
                </td>

                <td align="right">联系地址</td>
                <td align="left">
                    {{$order->supplier->user->address}}
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="panel">
    <div class="table-responsive">
        <table class="table table-form">
            <tr>
                <th colspan="4" align="left">订单资料</th>
            </tr>
            <tr>
                <td width="15%" align="right">单号</td>
                <td width="35%">
                    {{$order->number}}
                </td>

                <td width="15%" align="right">创建时间</td>
                <td width="35%">
                    @datetime($order->created_at)
                </td>
            </tr>
            <tr>
                <td width="15%" align="right">要求送货日期</td>
                <td width="35%">
                    @date($order->delivery_date)
                </td>
                <td width="15%" align="right">备注</td>
                <td width="35%"></td>
            </tr>
        </table>
    </div>
</div>

<div class="panel">
    <div class="table-responsive">
        <table class="table table-form m-b-none">

            <thead>
                <tr>
                    <th colspan="8" align="left">商品列表</th>
                </tr>
                <tr>
                    <th align="left">周期订单</th>
                    <th align="left">商品</th>
                    <th align="left">规格</th>
                    <th align="right">周期订单数量</th>
                    <th align="right">订单周期已入库数量</th>
                    <th align="right">本次计划送货数量</th>
                    <th align="left">备注</th>
                </tr>
            </thead>

            <tbody>
                @foreach($order->datas as $data)
                <tr>
                    <td align="left">
                        <a class="option" href="{{url('plan/show', ['id'=>$plan['id'][$data->product_id]])}}">{{$plan['sn'][$data->product_id]}}</a>
                    </td>
                    <td align="left">
                        {{$products[$data->product_id]->name}}
                    </td>
                    <td align="left">
                        {{$products[$data->product_id]->spec}}
                    </td>
                    <td align="right">
                        {{(int)$plan['quantity'][$data->product_id]}}
                    </td>
                    <td align="right">
                        {{(int)$stock[$data->product_id]}}
                        <a href="javascript:;" class="btn btn-xs btn-success" onclick="viewBox('order-data','入库明细','{{url('stock',['id'=>$data->order_id, 'product_id' => $data->product_id])}}');">明细</a>
                    </td>
                    <td align="right">
                        {{(int)$data->quantity}}
                    </td>
                    <td align="left">
                        {{$data->description}}
                    </td>
                </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <td align="left"><strong>合计<strong></td>
                    <td></td>
                    <td></td>
                    <td align="right">{{array_sum((array)$plan['quantity'])}}</td>
                    <td align="right">{{array_sum($stock)}}</td>
                    <td align="right">{{$order->datas->sum('quantity')}}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="panel">
    <div class="table-responsive">
        <table class="table table-form">
            <tr>
                <td align="left">

                    <button type="button" onclick="history.back();" class="btn btn-default">返回</button>
                
                    @if($step['edit'])
                        <a href="javascript:;" onclick="app.turn('{{$step['key']}}');" class="btn btn-danger">审批</a>
                        <input type="hidden" name="step_referer" value="{{session()->get('referer_'.Request::module().'_'.Request::controller().'_index')}}">
                    @endif

                    @if(authorise('stock.create') && $step['number'] > 2)
                        <a class="btn btn-info" href="{{url('supplier/stock/create',['order_id' => $order->id])}}">入库</a>
                    @endif

                    <a class="btn btn-default" href="{{url('supplier/order/print',['id' => $order->id])}}" target="_blank">打印</a>
                </td>
            </tr>
        </table>
    </div>
</div>

</form>