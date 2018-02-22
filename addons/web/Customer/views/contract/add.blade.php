<form method="post" action="{{url()}}" id="myform" name="myform">
<table class="list">
<thead>
<tr>
    <th align="left" colspan="2">基本信息</th>
</tr>
</thead>

<tr>
<th align="right" width="15%">合同类型</th>
<td>
    <select id='contract_type' name='contract_type'>
        <option value=""> - </option>
        <option value="1" @if($row['contract_type'] == 1)selected @endif>有效合同</option>
        <option value="2" @if($row['contract_type'] == 2)selected @endif>授权发货</option>
    </select>
    &nbsp;&nbsp;编号 <input type="text" size="20" class="input-text" name="contract_type_text" value="{{$row['contract_type_text']}}" />
    <span class="help-inline">有文本合同填写合同编号，有总经理授权的OA流程填写OA流程号。
</td>
</tr>

<tr>
    <th align="right">有效日期</th>
    <td align="left">
        <input type="text" data-toggle="date" class="text" size="13" name="end_time" value="{{date('Y-m-d',$row['end_time'])}}" />
    </td>
</tr>

<tr>
    <th align="right">地级渠道</th>
    <td align="left">
        {{:$channel = array('全国K/A','区域K/A','地区K/A','BC','批市','农贸','特渠','直营县区')}}
        @foreach($channel as $k => $v)
            <label class="checkbox-inline"><input type="checkbox" name="channel_item[city][]" value="{{$k}}" @if(in_array($k, (array)$row['channel_item']['city'])) checked @endif /> {{$v}}&nbsp;&nbsp;</label>
        @endforeach
    </td>
</tr>

<tr>
    <th align="right">县级渠道</th>
    <td align="left">
        {{:$channel = array('大店','小店')}}
        @foreach($channel as $k => $v)
            <label class="checkbox-inline"><input type="checkbox" name="channel_item[county][]" value="{{$k}}" @if(in_array($k, (array)$row['channel_item']['county'])) checked @endif /> {{$v}}&nbsp;&nbsp;</label>
        @endforeach
    </td>
</tr>
</table>

<table class="list" id="view_category_item">
    <thead>
    <tr>
        <th align="left">产品类别</th>
    </tr>
    </thead>
    @foreach($categorys as $category)
    <tr>
        <td align="left">
            <label class="checkbox-inline"><input type="checkbox" name="category_item[{{$category['id']}}]" value="1" @if(isset($row['category_item'][$category['id']])) checked @endif /> {{$category['layer_html']}}{{$category['name']}}</label>
        </td>
    </tr>
    @endforeach
</table>

@include('contract.add_product')

<table class="list">
<thead>
<tr>
    <th align="left" colspan="5" width="15%">销售目标</th>
</tr>
</thead>
<tr>
    <td class="left" colspan="5">
        <label class="checkbox-inline"><input type="checkbox" name="task_type" value="1" {if $row.task_type == 1}checked{/if} /> 销售任务</label>
        <label class="checkbox-inline"><input type="checkbox" name="reward_type" value="1" {if $row.reward_type == 1}checked{/if} /> 奖励计划</label>
    </td>
</tr>

<table class="list">
<thead>
<tr>
    <th align="left" colspan="5" width="15%">季度任务(万)</th>
</tr>
</thead>
<tr>
    {{:$quarter = array(1=>'一',2=>'二',3=>'三',4=>'四',5=>'五')}}
    @foreach($quarter as $v)
        <th align="center">{{$v}}季度</th>
    @endforeach
</tr>
<tr>
    @foreach($quarter as $k => $v)
    <td align="center"><input type="text" size="4" class="input-text" name="quarter_task[{{$k}}]" value="{{$row['quarter_task'][$k]}}" /> 万</td>
    @endforeach
</tr>
</table>

<table class="list">
<thead>
<tr>
    <th align="left" width="15%" colspan="15">月任务(万)</th>
</tr>
</thead>
<tr>
    {{:$months = range(1, 15)}}
    @foreach($months as $v)
        <th align="left">{{$v}}月</th>
    @endforeach
</tr>
<tr>
    @foreach($months as $v)
        <td><input type="text" size="4" class="input-text" name="month_task[{{$v}}]" value="{{$row['month_task'][$v]}}" /></td>
    @endforeach
</tr>
</table>

<table class="list">
<thead>
<tr>
    <th align="left" width="15%" colspan="5">产品奖励计划</th>
</tr>
</thead>
<!--
<tr>
    {{:$rewardData = array('330克爽口下饭菜/下饭菜','200克袋装豆豉','300酸菜鱼佐料','210克牛肉下饭香/牛肉酱','62/80麻辣萝卜干/海带丝')}}
    <td class="left" colspan="5">
        @foreach($rewardData as $k => $v)
            <label class="checkbox-inline"><input type="checkbox" name="reward_product_item[{{$k}}]" value="1" @if($row['reward_product_item'][$k] == 1) checked @endif /> {{$v}}&nbsp;&nbsp;</label>
        @endforeach
    </td>
</tr>
-->
<tr>
    <th align="center">一季度</th>
    <th align="center">二季度</th>
    <th align="center">三季度</th>
    <th align="center">四季度</th>
    <th align="center">五季度</th>
</tr>
<tr>
    <td align="center"><input type="text" size="8" class="input-text" name="reward_task[]" value="{{$row['reward_task'][0]}}" /></td>
    <td align="center"><input type="text" size="8" class="input-text" name="reward_task[]" value="{{$row['reward_task'][1]}}" /></td>
    <td align="center"><input type="text" size="8" class="input-text" name="reward_task[]" value="{{$row['reward_task'][2]}}" /></td>
    <td align="center"><input type="text" size="8" class="input-text" name="reward_task[]" value="{{$row['reward_task'][3]}}" /></td>
    <td align="center"><input type="text" size="8" class="input-text" name="reward_task[]" value="{{$row['reward_task'][4]}}" /></td>
</tr>
</table>

<table class="list">
<tr>
    <th align="right" width="15%">付款方式</th>
    <td>
        <select id='pay_type' name='pay_type' onchange="this.value == 1 ? $('#pay_type_text').css('display','none') : $('#pay_type_text').css('display','');">
            <option value="1" @if($row['pay_type'] == 1)}selected @endif>款到发货</option>
            <option value="2" @if($row['pay_type'] == 2)}selected @endif>其他方式</option>
        </select>
        <span id="pay_type_text" style="display:@if($row['pay_type'] == 2) @else none @endif;">&nbsp;&nbsp;说明 <input type="text" size="40" class="input-text" name="pay_type_text" value="{{$row['pay_type_text']}}" /> 40个字以内</span>
    </td>
</tr>

<tr>
    <th align="right" width="15%">运费承担</th>
    <td>
        <select id='freight_type' name='freight_type' onchange="this.value == 1 ? $('#freight_type_text').css('display','none') : $('#freight_type_text').css('display','');">
            <option value="1" @if($row['freight_type'] == 1)selected @endif>公司标准</option>
            <option value="2" @if($row['freight_type'] == 2)selected @endif>其他方式</option>
        </select>
        <span id="freight_type_text" style="display:@if($row['pay_type'] == 2) @else none @endif;">&nbsp;&nbsp;说明 <input type="text" size="40" class="input-text" name="freight_type_text" value="{{$row['freight_type_text']}}" /> 40个字以内</span>
    </td>
</tr>

<tr>
    <th align="right" width="15%">销售支持</th>
    <td>
        <select id='support_type' name='support_type' onchange="this.value == 1 ? $('#support_type_text').css('display','none') : $('#support_type_text').css('display','');">
            <option value="1" @if($row['support_type'] == 1)selected @endif>公司标准</option>
            <option value="2" @if($row['support_type'] == 2)selected @endif>其他方式</option>
        </select>
        <span id="support_type_text" style="display:@if($row['support_type'] == 2) @else none @endif;">&nbsp;&nbsp;说明 <input type="text" size="40" class="input-text" name="support_type_text" value="{{$row['support_type_text']}}" /> 40个字以内</span>
    </td>
</tr>

<tr>
    <th align="right" width="15%">日常报损</th>
    <td>
        <select id='loss_type' name='loss_type' onchange="this.value == 1 ? $('#loss_type_text').css('display','none') : $('#loss_type_text').css('display','');">
            <option value="1" @if($row['loss_type'] == 1)selected @endif>公司标准</option>
            <option value="2" @if($row['loss_type'] == 2)selected @endif>其他方式</option>
        </select>
        <span id="loss_type_text" style="display:@if($row['loss_type'] == 2) @else none @endif;">&nbsp;&nbsp;说明 <input type="text" size="40" class="input-text" name="loss_type_text" value="{{$row['loss_type_text']}}" /> 40个字以内</span>
    </td>
</tr>

<tr>
    <th align="right" width="15%">发货标准</th>
    <td>
        <select id='transport_type' name='transport_type' onchange="this.value == 1 ? $('#transport_type_text').css('display','none') : $('#transport_type_text').css('display','');">
            <option value="1" @if($row['transport_type'] == 1)selected @endif>公司标准</option>
            <option value="2" @if($row['transport_type'] == 2)selected @endif>其他方式</option>
        </select>
        <span id="transport_type_text" style="display:@if($row['transport_type'] == 2) @else none @endif;">&nbsp;&nbsp;说明 <input type="text" size="40" class="input-text" name="transport_type_text" value="{{$row['transport_type_text']}}" /> 40个字以内</span>
    </td>
</tr>

<table class="list">
<tr>
    <th width="15%" align="right">价格说明</th>
    <td align="left"><textarea class="form-control input-sm" rows="2" name="client[price_text]">{{$client['price_text']}}</textarea></td>
</tr>
<tr>
    <th align="right">货品说明</th>
    <td align="left"><textarea class="form-control input-sm" rows="2" name="client[goods_text]">{{$client['goods_text']}}</textarea></td>
</tr>

<tr>
    <th align="right">其他说明</th>
    <td align="left"><textarea class="form-control input-sm" rows="2" name="client[other_text]">{{$client['other_text']}}</textarea></td>
</tr>
<tr>
    <th align="right">不良记录</th>
    <td align="left"><textarea class="form-control input-sm" rows="2" name="client[poor_text]">{{$client['poor_text']}}</textarea></td>
</tr>
</table>

<input type="hidden" id="id" name="id" value="{{$row['id']}}" />
<input type="hidden" id="client_id" name="client[id]" value="{{$client['id']}}" />
<button type="button" onclick="history.back();" class="btn btn-default">返回</button>
<button type="submit" class="btn btn-success"><i class="fa fa-check-circle"></i> 提交</button>

</form>
