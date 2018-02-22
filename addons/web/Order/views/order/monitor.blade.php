<table class="tlist">
    <tr>
        <td class="left">
            <form id="select" name="select" action="{{url()}}" method="get">
                @include('order/select')
                <button type="submit" class="btn btn-default btn-sm">过滤</button>
            </form>
        </td>
    </tr>
</table>

<table class="list tab">

<tr class="odd">
    <th width="100">订单号</th>
    <th>客户名称</th>
     <th width="120">下单到付款天数</th>
    <th width="100">付款到发货天数</th>
    <th width="120">发货到到货天数</th>
    <!--
    <th width="80">在途时间</th>
    -->
    <th width="60">订单满足率</th>
    <!--
    <th width="100">付款时间</th>
    <th width="100">发货时间</th>
    -->
    <th width="60">订单单量</th>
    <th width="60">发货数量</th>
</tr>

 @if(count($rows)) @foreach($rows as $k => $v)

{{:$now_order_history = $order_history[$v['id']]['amount']}}
{{:$now_order_data = $order_data[$v['id']]['amount']}}

<tr>
    <td align="center"><a href="{{url('view')}}?id={{$v['id']}}">{{$v['number']}}</a></td>
    <td align="left">{{$v['company_name']}}</td>

    @if($v['add_time']>0 && $v['pay_time'] > 0)
        {{:$now_time = round(($v['pay_time']-$v['add_time'])/86400)}}
        <td align="center" @if($now_time >= 3) style="color:#f00;" @endif >{{$now_time}}天</td>
     @else
        <td align="center">无</td>
     @endif

     @if($v['pay_time']>0 && $v['delivery_time'] > 0)
        {{:$now_time = round(($v['delivery_time']-$v['pay_time'])/86400)}}
        <td align="center" @if($now_time >= 3) style="color:#f00;" @endif >{{$now_time}}天</td>
     @else
        <td align="center">无</td>
     @endif

     @if($v['delivery_time'] > 0 && $v['arrival_time'] > 0)
        {{:$now_time = round(($v['arrival_time']-$v['delivery_time'])/86400)}}
        <td align="center" @if($now_time >= 5) style="color:#f00;" @endif >{{$now_time}}天</td>
     @else
        <td align="center">无</td>
     @endif

    <!--
    <td align="center">
     @if($v['delivery_time'] > 0 && $v['arrival_time'] > 0)
        {{round(($v['arrival_time']-$v['delivery_time'])/86400)}}天
     @else
        无
     @endif
    </td>
    -->

    <td align="center">
    <a title="点击查看详情" href="{{url('monitor_data')}}?id={{$v['id']}}">
        
         @if($v['sum_fact_amount']>0&&$v['sum_amount']>0)
            {{:$p = number_format(($v['sum_fact_amount']/$v['sum_amount']) * 100, 2)}}
             @if($p>100) 100.00 @else {{$p}} @endif %
         @else
            无
         @endif
    </a>
    </td>

    <!--
    <td align="center" nowrap>
     @if($v['pay_time']>0) {{$v['pay_time'] > 0 ? date("Y-m-d H:i:s",$v['pay_time']) : ""}} @else 无 @endif
    </td>
    
    <td align="center" nowrap>
     @if($v['delivery_time']>0) {{$v['delivery_time'] > 0 ? date("Y-m-d H:i:s",$v['delivery_time']) : ""}} @else 无 @endif
    </td>
    -->

    <td align="right">{{$v['sum_amount']}}</td>
    <td align="right"> @if($v['delivery_time']>0) {{$v['sum_fact_amount']}} @else 无 @endif </td>

</tr>
 @endforeach @endif

<form>
</table>

<div class="pull-right">
{{$rows->render()}}
</div>
