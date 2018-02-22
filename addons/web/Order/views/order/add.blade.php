@include('layouts/extjs')

<script type="text/javascript">
    var DATA_URL = "{{url('data')}}?table=add&client_id={{$client['id']}}&s={{time()}}";
</script>

<table class="tlist">
     @if(Auth::user()->role->name != 'client')
    <tr>
        <td align="left">
            <form id="select" name="select" action="{{url()}}" method="get">
                @include('order/add_select')
                <button type="submit" class="btn btn-default btn-sm">过滤</button>
            </form>
        </td>
  </tr>
   @endif
</table>

 @if($selects['select']['client_id'] <= 0)

<div class="alert alert-warning" role="alert">新建订单请选择客户。</div>

 @endif

 @if($selects['select']['client_id'] > 0)
<form id="myform" name="myform" action="{{$query}}" method="post">
<table class="list">
    <tr class="odd">
        <th align="left" colspan="4">客户信息</th>
    </tr>

    <tr>
        <th align="right" width="100">客户名称</th>
        <td align="left" width="400">{{$client['nickname']}} <span style="color:#265d95;">[{{$client['username']}}]</span></td>
        <th align="right" width="100">公司电话</th>
        <td align="left">{{$client['tel']}}</td>
    </tr>
    
    <tr>
        <th align="right">公司地址</th>
        <td align="left">{{$client['address']}}</td>
        
        <th align="right">公司传真</th>
        <td align="left">{{$client['fax']}}</td>
    </tr>

    <tr>
        <th align="right">仓库地址</th>
        <td align="left" colspan="3">{{$customer->warehouse_address}}</td>
    </tr>
</table>

<table class="list">

    <tr class="odd">
        <th align="left" colspan="4">订单信息</th>
    </tr>

    <tr>
        <td align="right" width="10%">开票类型 <span style="color:red;">*</span></td>
        <td align="left" width="40%">
            <select class="form-control input-sm input-inline" id="invoice_type" name="invoice_type" onchange="invoiceTypeText(this.value);">
                <option value="0"> - </option>

                @foreach(option('customer.invoice') as $v)
                    @if($customer->invoice_type == 0 && $v['id'] == 3)
                        {{:continue}}
                    @endif
                    <option value="{{$v['id']}}">{{$v['name']}}</option>
                @endforeach

            </select>
        </td>
        <td align="right" width="10%"><span data-container="body" data-toggle="tooltip" title="" data-original-title="若没有请与客户经理联系。"></span><span id="invoice_type_title">开票抬头</span> <span style="color:red;">*</span></td>
        <td align="left" width="40%">
            <span id="invoice_type_text">无</span>
        </td>
    </tr>

    <tr>
        <td align="right">下单人姓名 <span style="color:red;">*</span></td>
        <td align="left">
            <input type="text" class="form-control input-sm input-inline" id="order_people" name="order_people" placeholder="请填写下单人姓名。">
        </td>
        <td align="right">下单人电话 <span style="color:red;">*</span></td>
        <td align="left">
            <input type="text" class="form-control input-sm input-inline" id="order_people_phone" name="order_people_phone" placeholder="请填写下单人联系电话。">
        </td>
    </tr>
    
    <tr>
        <td align="right">送货车辆长度 <span style="color:red;">*</span></td>
        <td align="left">
            <select class="form-control input-sm input-inline" id="transport_car_type" name="transport_car_type">
                <option value="0"> - </option>
                <option value="9.6">9.6米</option>
                <option value="13">13米</option>
                <option value="17.5">17.5米</option>
                <option value="4.2">4.2米</option>
                <option value="6.8">6.8米</option>
                <option value="8.6">8.6米</option>
            </select>
        </td>
        <td align="right"></td>
        <td align="left">
        </td>
    </tr>
    
</table>

<div class="line"></div>

<div class="panel">
<div class="panel-body">
<a class="btn btn-sm btn-default" href='javascript:iframeBox("添加产品","{{url('product_add')}}?table={{$table}}&order_id={{$order['id']}}&client_id={{$client['id']}}&s={{time()}}","全部添加");'>新增产品</a>
<a class="btn btn-sm btn-default" href='javascript:saveStore();'>保存编辑</a>
<a class="btn btn-sm btn-default" href='javascript:removeStore();'>删除产品</a>

@if(authorise('account.query','customer'))
<a class="btn btn-sm btn-default" href='{{url("customer/account/query")}}'>客户对账</a>
@endif

</div>
</div>

<script type="text/javascript">
var tbar = [];
tbar.push({
    xtype: 'button',
    text: '新增产品',
    handler: function() {
        iframeBox("添加产品","{{url('product_add')}}?table={{$table}}&order_id={{$order['id']}}&client_id={{$client['id']}}&s={{time()}}","全部添加");
    }
});
tbar.push({
    xtype: 'button',
    text: '保存编辑',
    handler: function() {
        saveStore();
    }
});
tbar.push({
    xtype: 'button',
    text: '删除产品',
    handler: function() {
        removeStore();
    }
});
</script>

@include('order/data')

<div class="line"></div>

<script type="text/javascript">
Ext.onReady(function() {
    Ext.create('Ext.TabPanel', {
        renderTo: 'tabs',
        activeTab: 0,
        plain: true,
        height: 135,
        margin: 0,
        defaults: {
            bodyPadding: 10,
            autoScroll: true,
        },
        items: [{
            id: 'tab-1',
            title: '备注信息',
            contentEl: 'tab1'
        }]
    });
});
</script>

<div id="tabs">
    <div id="tab1" class="x-hide-display">
        <textarea class="form-control" rows="3" id="description" name="description">{{$order['description']}}</textarea>
    </div>
</div>

<div class="line"></div>

<label class="checkbox-inline">
<input name="sms" id="sms" type="checkbox" checked="true"> 消息提醒
</label>

<div class="line"></div>

<div>
    <input type="hidden" name="client_id" value="{{$client['id']}}" />
    <button type="button" onclick="history.back();" class="btn btn-default">返回</button>
    <button type="submit" class="btn btn-success"><i class="fa fa-check-circle"></i> 提交</button>
</div>

</from>

 @endif

<script type="text/javascript">
function invoiceTypeText(id)
{
    var text = '';
    var title = '';
    if(id == 0) {
        title = '开票抬头';
        text = '无';
    }
    if(id == 1) {
        title = '税票单位';
        text = '<select class="form-control input-sm input-inline" id="invoice_company" name="invoice_company">\
                <option value=""> - </option>\
                 @if(count($client['bank'])) @foreach($client['bank'] as $k => $v) <option value="{{$v['tax_name']}}"  @if($order['invoice_company']==$v['tax_name']) selected="true" @endif >{{$v['tax_name']}}</option>\ @endforeach @endif </select>\ <span class="help-inline">若没有请与客户经理联系。</span>';
    }
    if(id == 2 || id == 3) {
        title = '打款人';
        text = '<input type="text" class="form-control input-sm input-inline" id="invoice_company" name="invoice_company" placeholder="请输入持卡人名。">';
    }
    document.getElementById('invoice_type_text').innerHTML = text;
    document.getElementById('invoice_type_title').innerHTML = title;
}
</script>
