<div class="panel">

    <div class="wrapper">
        @include('order/query1')
    </div>

<form id="orderform" method="post">
<div class="table-responsive">
<table class="table m-b-none b-t table-hover">

<tr>

    @if(isset($access['merge']) || isset($access['delete']))
        <th align="center">
            <input type="checkbox" class="select-all">
        </th>
    @endif

    <th align="center">单号</th>
    <th align="left">客户名称 / 打款人</th>
    <th align="left">流程</th>
    <th align="center">战略比</th>
    <th align="right">订单数量 / 订单重量(t)</th>
    <th align="center">费用活动</th>
    <th align="center">状态</th>
    <th align="center">创建时间</th>
    <th align="center"></th>
</tr>

<?php
$_status = array(
    'start' => '开始',
    'next'  => '正常',
    'last'  => '退回',
    'deny'  => '拒绝',
    'end'   => '结束',
);
?>

@if(count($rows)) @foreach($rows as $k => $v)

{{:$audit_now_config = $audit_config[$v['flow_step_id']]}}

<tr>

    @if(isset($access['merge']) || isset($access['delete']))
        <td align="center"><input type="checkbox" class="select-row" name="order_id[]" value="{{$v['id']}}" /></td>
    @endif

    <td align="center">
        <a href="{{url('order/order/view', [id => $v['id']])}}" href="javascript:;">{{$v['number']}}</a>
    </td>
    
    <td align="left">
        {{$v['company_name']}}
        <div class="text-muted">{{$v['invoice_company']}}</div>
    </td>

    <td align="left">
        @if(Auth::user()->role->name == $audit_now_config['role'])

            @if($audit_now_config['role'] == 'salesman')
                @if(in_array($v['circle_id'], (array)$circle['owner_user']))
                <span style="color:#f00;">
                @endif
            @else
                <span style="color:#f00;">
            @endif

        @elseif($audit_now_config['role'] == 'complete')
            <span style="color:#00cccc;">
        @else
            <span>
        @endif

        @if($v['flow_step_state'] == 'end')
            <span style="color:green;">流程结束。</span>
        @else
            <span style="color:#999;">[{{$_status[$v['flow_step_state']]}}]</span>
            <span class="badge">{{$v['flow_step_id']}}</span>
            {{$audit_now_config['name']}}
        @endif
        </span>
    </td>

    <td align="center">
        <?php
        $amount = $v->datas->sum('amount');
        $amount_total = 0;
        $v->datas->each(function ($v) use ($strategic, $materiel, &$amount_total, &$amount) {
            if (in_array($v['category_id'], $strategic) !== false) {
                $amount_total = $amount_total + $v['amount'];
            }
            if (in_array($v['category_id'], $materiel) !== false) {
                $amount = $amount - $v['amount'];
            }
        });
        if ($amount) {
            echo number_format(($amount_total / $amount) * 100, 2)."%";
        } else {
            echo '0.00%';
        }
        ?>
    </td>

    <td align="right">
        {{$v->datas->sum('amount')}}
        <br> 
        @number($v->datas->sum('weight') / 1000, 2)
    </td>

    <td align="center">
        @if($v->promotions->count())
            <a class="option" href="{{url('promotion/promotion/index', ['field_0'=>'promotion.customer_id','condition_0'=>'eq','search_0'=>$v['client_id'],'referer'=>1])}}">
                促销({{$v->promotions->count()}})
            </a>
        @else
            促销(0)
        @endif

        @if($v->approachs->count())
            <a class="option" href="{{url('approach/approach/index', ['field_0'=>'approach.customer_id','condition_0'=>'eq','search_0'=>$v['client_id'],'referer'=>1])}}">
                进店({{$v->approachs->count()}})
            </a>
        @else
            进店(0)
        @endif
    </td>

    <td align="center">
        @if($v['status'] == 0)
        <span class="label label-warning">废除</span>
        @else
        <span class="label label-success">正常</span>
        @endif
    </td>

    <td align="center">@datetime($v['add_time'])</td>
    
    <td align="center">
    
        @if(isset($access['repeal']) && $v['flow_step_id'] <= 1 && $v['status']==1)
        <a class="option" href="javascript:app.confirm('{{url('repeal',['id'=>$v['id']])}}', '确认要作废订单吗？');">作废</a>
        @endif

        @if(isset($access['export']))
        <a class="option" href="javascript:app.confirm('{{url('export',['id'=>$v['id']])}}', '确认要导出订单吗？');">导出({{$v['export']}})</a>
        @endif

        @if(isset($access['syncyonyou']))
        <a class="option" href="javascript:app.confirm('{{url('syncyonyou',['id'=>$v['id']])}}', '确认要同步订单吗？');">同步({{$v['yonyou']}})</a>
        @endif

    </td>

</tr>
 @endforeach 
 @endif

</table>
</div>

<footer class="panel-footer">
    <div class="row">
        <div class="col-sm-4 hidden-xs"></div>
        <div class="col-sm-8 text-right text-center-xs">
            {{$rows->render()}}
        </div>
    </div>
</footer>

</div>
