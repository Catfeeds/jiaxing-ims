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
                    {{$plan->supplier->user->nickname}}
                </td>

                <td width="15%" align="right">供应商代码</td>
                <td width="35%">
                    {{$plan->supplier->user->username}}
                </td>
            </tr>

            <tr>
                <td align="right">法人代表</td>
                <td>
                    {{$plan->supplier->legal}}
                </td>
                <td align="right">公司性质</td>
                <td>
                    {{$plan->supplier->nature}}
                </td>
            </tr>

            <tr>
                <td align="right">公司电话</td>
                <td>
                    {{$plan->supplier->user->tel}}
                </td>
                <td align="right">公司传真</td>
                <td align="left">
                    {{$plan->supplier->user->fax}}
                </td>
            </tr>

            <tr>
                <td align="right">公司税号</td>
                <td>
                    {{$plan->supplier->tax_number}}
                </td>

                <td align="right">联系地址</td>
                <td align="left">
                    {{$plan->supplier->user->address}}
                </td>
            </tr>
        </table>

    </div>

</div>

<div class="panel">
    <div class="table-responsive">
        <table class="table table-form">

            <tr>
                <th colspan="4" align="left">计划资料</th>
            </tr>

            <tr>
                <td width="15%" align="right">单号</td>
                <td width="35%">
                    {{$plan->number}}
                </td>

                <td width="15%" align="right">创建时间</td>
                <td width="35%">
                    @datetime($plan->created_at)
                </td>

            </tr>

        </table>
    </div>
</div>

<div class="panel">
    <div class="table-responsive">
        <table class="table table-form m-b-none">

            <thead>
                <tr>
                    <th colspan="6" align="left">商品列表</th>
                </tr>
                <tr>
                    <th align="center">ID</th>
                    <th align="left">商品</th>
                    <th align="left">规格</th>
                    <th align="center">周期计划阶段</th>
                    <th align="right">周期订单数量</th>
                    <th align="right">周期订单已入库数量</th>
                    <th align="left">备注</th>
                </tr>
            </thead>

            <tbody>
                @foreach($plan->datas as $data)
                <tr>
                    <td align="center">
                        {{$data->id}}
                    </td>
                    <td align="left">
                        {{$products[$data->product_id]->name}}
                    </td>
                    <td align="left">
                        {{$products[$data->product_id]->spec}}
                    </td>

                    <td align="center">
                        {{$data->cycle}}
                    </td>

                    <td align="right">
                        {{(int)$data->quantity}}
                    </td>
                    <td align="right">
                        {{(int)$stock[$data->product_id]}}
                        <a href="javascript:;" class="btn btn-xs btn-success" onclick="viewBox('order-data','送货明细','{{url('order',['id'=>$plan->id, 'product_id' => $data->product_id])}}');">明细</a>
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
                    <td align="right">{{$plan->datas->sum('quantity')}}</td>
                    <td align="right">{{array_sum($stock)}}</td>
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
                <td align="left">@include('attachment/view')</td>
            </tr>
        </table>
    </div>
</div>

<div class="panel">
    <div class="table-responsive">
        <table class="table table-form">
            <tr>
                <th align="left" colspan="4">审批记录</th>
            </tr>
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

                    <!--
                    @if(authorise('order.create') && $step['number'] > 2)
                        <a class="btn btn-info" href="{{url('supplier/order/create',['plan_id' => $plan->id])}}">分次送货单</a>
                    @endif
                    -->
                    
                    <a class="btn btn-default" href="{{url('supplier/plan/print',['id' => $plan->id])}}" target="_blank">打印</a>
                    
                </td>
            </tr>
        </table>
    </div>
</div>

</form>