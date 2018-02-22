<form class="form-horizontal" name="myform" id="myform" method="post">

<div class="panel">
    <div class="table-responsive">
        <table class="table table-form">

            <tr>
                <th colspan="4" align="left">基础资料</th>
            </tr>

            <tr>
                <td width="15%" align="right">单号</td>
                <td width="35%">
                    {{$settlement->sn}}
                </td>
                <td width="15%" align="right">供应商</td>
                <td width="35%">
                    {{$settlement->supplier->user->nickname}}
                </td>

            </tr>

            <tr>
                <td align="right">结算开始日期</td>
                <td>
                    {{$settlement->start_at}}
                </td>

                <td align="right">结算结束日期</td>
                <td>
                    {{$settlement->end_at}}
                </td>

            </tr>

            <tr>
                <td align="right">创建人</td>
                <td>
                    {{get_user($settlement->created_by,'nickname')}}
                </td>

                <td align="right">创建时间</td>
                <td>
                    @datetime($settlement->created_at)
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
                    <th colspan="7" align="left">计算周期内送货商品</th>
                </tr>
                <tr>
                    <th align="center">序号</th>
                    <th align="left">商品</th>
                    <th align="center">单位</th>
                    <th align="right">单价</th>
                    <th align="right">数量</th>
                    <th align="right">金额</th>
                    <th align="center">单价时间</th>
                    <th align="left">备注</th>
                </tr>
            </thead>

            <tbody>
                @foreach($datas as $i => $data)
                <tr>
                    <td align="center">
                        {{$i + 1}}
                    </td>
                    <td align="left">
                        {{$data->goods}}
                    </td>
                    <td align="center">
                        {{$data->unit_name}}
                    </td>
                    <td align="right">
                        {{$data->price}}
                    </td>
                    <td align="right">
                        @number($data->sum_quantity, 2)
                    </td>
                    <td align="right">
                        @number($data->sum_money, 2)
                    </td>
                    <td align="center">
                        {{$data->price_time}}
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
                    <td></td>
                    <td align="right">@number($datas->sum('sum_quantity'), 2)</td>
                    <td align="right">@number($datas->sum('sum_money'), 2)</td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="panel">
    <div class="table-responsive">
        <table class="table table-form m-b-none">
            <thead>
                <tr>
                    <th colspan="7" align="left">质量及交付管理[扣款]</th>
                </tr>
                <tr>
                    <th align="center">序号</th>
                    <th align="left">ID</th>
                    <th align="center">问题描述</th>
                    <th align="center">问题处理</th>
                    <th align="left">商品</th>
                    <th align="right">数量</th>
                    <th align="right">金额</th>
                    <th align="center">流程</th>
                </tr>
            </thead>

            <tbody>
                @foreach($qualitys as $i => $quality)
                <?php
                    $step = get_step_status($quality);
                ?>
                <tr>
                    <td align="center">
                        {{$i + 1}}
                    </td>
                    <td align="left">
                        {{$quality->id}}
                    </td>
                    <td align="left">
                        {{$quality->handle}}
                    </td>
                    <td align="left">
                        {{$quality->description}}
                    </td>
                    <td align="left">
                        {{$quality->product->name}}
                    </td>
                    <td align="right">
                        {{$quality->quantity}}
                    </td>
                    <td align="right">
                        {{$quality->money}}
                    </td>
                    <td align="center">
                        {{$step['name']}}
                    </td>
                    
                </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <td align="left"><strong>合计<strong></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right">@number($qualitys->sum('quantity'))</td>
                    <td align="right">@number($qualitys->sum('money'), 2)</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="panel">
    <div class="table-responsive">
        <table class="table table-form m-b-none">
        <tr>
            <th align="left">汇总合计金额：
                @number($datas->sum('sum_money') - $qualitys->sum('money'), 2)
                ，大写金额: {{str_rmb($datas->sum('sum_money') - $qualitys->sum('money'))}}</th>
        </tr>
    </table>
    </div>
</div>

@if($price['main'])
<div class="panel">
    <div class="table-responsive">
        <table class="table table-form">
            <tr>
                <td align="left">@include('attachment/view')</td>
            </tr>
        </table>
    </div>
</div>
@endif

<div class="panel">
    <div class="table-responsive">
        <table class="table table-form">
            <tr>
                <td align="left">

                    @if($step['edit'])
                        <a href="javascript:;" onclick="app.turn('{{$step['key']}}');" class="btn btn-danger">审批</a>
                        <input type="hidden" name="step_referer" value="{{session()->get('referer_'.Request::module().'_'.Request::controller().'_index')}}">
                    @endif

                    <a class="btn btn-default" href="{{url('supplier/settlement/print',['id' => $settlement->id])}}" target="_blank">打印</a>
                    <button type="button" onclick="history.back();" class="btn btn-default">返回</button>
                </td>
            </tr>
        </table>
    </div>
</div>

</form>