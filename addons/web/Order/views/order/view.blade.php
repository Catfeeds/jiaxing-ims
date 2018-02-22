@include('layouts/extjs')
<script type="text/javascript">
    var DATA_URL = "{{url('data')}}?table=edit&id={{$order['id']}}&client_id={{$order['client_id']}}&s={{time()}}";
</script>

<form id="myform" name="myform" action="{{url()}}" onsubmit="return false" method="post">
<div class="panel">

<div class="table-responsive">
<table class="table table-form">
    <tr>
        <th align="left" class="odd" colspan="4">客户资料</th>
    </tr>

    <tr>
        <td align="right" width="10%">客户名称</td>
        <td align="left" width="40%">{{$client['nickname']}} <span style="color:#265d95;">[{{$client['username']}}]</span></td>
        <td align="right" width="10%">经营负责人</td>
        <td align="left" width="40%">{{$client['fullname']}}</td>
    </tr>

    <tr>
        <td align="right">公司电话</td>
        <td align="left">{{$client['tel']}}</td>

        <td align="right">经营负责人手机</td>
        <td align="left">{{$client['mobile']}}</td>
    </tr>

    <tr>
        <td align="right">公司传真</td>
        <td align="left">{{$client['fax']}}</td>

        <td align="right">公司地址</td>
        <td align="left">{{$client['address']}}</td>
    </tr>

    <tr>
        <td align="right">收货人</td>
        <td align="left">{{$customer['warehouse_contact']}}</td>

        <td align="right">收货人手机</td>
        <td align="left">{{$customer['warehouse_mobile']}}</td>
    </tr>

    <tr>
        <td align="right">收货地址</td>
        <td align="left" colspan="3">{{$customer['warehouse_address']}}</td>
    </tr>
</table>
</div>
</div>

<div class="panel">

<div class="table-responsive">
<table class="table table-form">
    <tr>
        <th align="left" colspan="4">订单资料</th>
    </tr>

    <tr>
        <td align="right" width="10%">单 号 <span style="color:red;">*</span></td>
        <td align="left" width="40%">

            @if(isset($access['merge']))
                <input type="text" class="form-control input-sm input-inline" id="number" name="number" value="{{$order['number']}}" size="20" />
            @else
                {{$order['number']}}
            @endif

        </td>
        <td align="right" width="10%">订单日期</td>
        <td align="left" width="40%">{{$order['add_time'] > 0 ? date("Y-m-d H:i:s",$order['add_time']) : ""}}</td>
    </tr>

    <tr>
        <td align="right">付款日期</td>
        <td align="left">@datetime($order['pay_time'])</td>
        <td align="right">发货日期</td>
        <td align="left">@datetime($order['delivery_time'])</td>
    </tr>

    <tr>
        <td align="right">到货日期</td>
        <td align="left" colspan="3">@datetime($order['arrival_time'])</td>
    </tr>

    @if(Auth::user()->role_id == 16 || Auth::user()->role_id == 1)
        <tr>
            <td align="right">开票类型 <span style="color:red;">*</span></td>
            <td align="left">
                <select class="form-control input-sm input-inline" id="invoice_type" name="invoice_type" onchange="invoiceTypeText(this.value);">
                    <option value="0"> - </option>

                    @foreach(option('customer.invoice') as $v)

                        @if($customer['invoice_type'] == 0 && $v['id'] == 3)
                            {{:continue}}
                        @endif
                        
                        <option value="{{$v['id']}}" @if($order['invoice_type'] == $v['id']) selected @endif>{{$v['name']}}</option>
                    @endforeach

                </select>
            </td>
            <td align="right"><span id="invoice_type_title"> @if($order['invoice_type'] == 1) 发票单位 @else 打款人 @endif </span> <span style="color:red;">*</span></td>
            <td align="left">
                <span id="invoice_type_text">{{$order['invoice_company']}}</span>
            </td>
        </tr>
    @else
        <tr>
            <td align="right">开票类型</td>
            <td align="left">
                {{option('customer.invoice', $order['invoice_type'])}}
            </td>
            <td align="right"> @if($order['invoice_type'] == 1) 发票单位 @else 打款人 @endif </td>
            <td align="left">
                {{$order['invoice_company']}}
            </td>
        </tr>
    @endif

    <tr>
        <td align="right">下单人姓名</td>
        <td align="left">
            {{$order['order_people']}}
        </td>
        <td align="right">下单人电话</td>
        <td align="left">
            {{$order['order_people_phone']}}
        </td>
    </tr>
    
    <tr>
        <td align="right">送货车辆长度</td>
        <td align="left">
            @if($order['transport_car_type'])
                {{$order['transport_car_type']}}米
            @endif
        </td>
        <td align="right"></td>
        <td align="left">
        </td>
    </tr>
</table>
</div>
</div>

@if($order['status'] == 1 || $order['status'] == 2)

<div class="panel">
<div class="panel-body">
@if(isset($access['product_add']))
<a class="btn btn-sm btn-default" href='javascript:iframeBox("订单产品","{{url('product_add')}}?table={{$table}}&order_id={{$order['id']}}&client_id={{$order['client_id']}}&s={{time()}}", "全部添加");'>新增产品</a>
@endif

@if(isset($access['product_edit']))
<a class="btn btn-sm btn-default" href='javascript:saveStore();'>保存编辑</a>
@endif

@if(isset($access['product_delete']))
<a class="btn btn-sm btn-default" href='javascript:removeStore();'>删除产品</a>
@endif

@if(isset($access['part']))
<a class="btn btn-sm btn-default" href='{{url('part')}}?order_id={{$order['id']}}'>拆分订单</a>
@endif

@if(isset($access['transport']))
<a class="btn btn-sm btn-default" href='javascript:iframeBox("物流","{{url('transport')}}?order_id={{$order['id']}}&client_id={{$order['client_id']}}", "提交表单");'>物流信息</a>
@endif

@if(isset($access['print']))
<a class="btn btn-sm btn-default" href='javascript:window.open("{{url('print',['order_id'=>$order['id']])}}", "_blank");'>打印订单</a>
@endif

@if(isset($access['audit']))
<a class="btn btn-sm btn-default" href='javascript:iframeBox("工作流程处理","{{url('audit')}}?order_id={{$order['id']}}&client_id={{$order['client_id']}}", "提交表单");'>工作流程</a>
@endif

@if(isset($access['export']))
<a class="btn btn-sm btn-default" href='javascript:app.confirm("{{url("export")}}?id={{$order["id"]}}", "确认要导出订单吗？");'>导出订单</a>
@endif

@if(authorise('receivable.create','customer'))
<a class="btn btn-sm btn-default" href='javascript:formBox("新建","{{url("customer/receivable/create", ["customer_id"=>$order["client_id"]])}}", "window-form");'>回款登记</a>
@endif

@if(authorise('cash.create','promotion'))
<a class="btn btn-sm btn-default" href='javascript:formBox("新建","{{url("promotion/cash/create", ["customer_id"=>$order["client_id"]])}}", "window-form");'>促销兑现</a>
@endif

<a class="btn btn-sm btn-default" href='javascript:iframeBox("合作要约","{{url('customer/contract/view',['customer_id'=>$order['client_id']])}}");'>合作要约</a>
<a class="btn btn-sm btn-default" href='javascript:syncCNNZ();'>同步订单</a>

@if(authorise('account.query','customer'))
<a class="btn btn-sm btn-default" href='{{url("customer/account/query")}}'>客户对账</a>
@endif

</div>
</div>

<script type="text/javascript">
var tbar = [];
@if(isset($access['product_add']))
tbar.push({
    xtype: 'button',
    text: '新增产品',
    handler: function() {
        iframeBox("订单产品","{{url('product_add')}}?table={{$table}}&order_id={{$order['id']}}&client_id={{$order['client_id']}}&s={{time()}}", "全部添加");
    }
});
@endif
@if(isset($access['product_edit']))
tbar.push({
    xtype: 'button',
    text: '保存编辑',
    handler: function() {
        saveStore();
    }
});
@endif
@if(isset($access['product_delete']))
tbar.push({
    xtype: 'button',
    text: '删除产品',
    handler: function() {
        removeStore();
    }
});
@endif
@if(isset($access['transport']))
tbar.push({
    xtype: 'button',
    text: '物流信息',
    handler: function() {
        iframeBox("物流","{{url('transport')}}?order_id={{$order['id']}}&client_id={{$order['client_id']}}&s={{time()}}", "提交表单");
    }
});
@endif
@if(isset($access['print']))
tbar.push({
    xtype: 'button',
    text: '打印订单',
    handler: function() {
        window.open("{{url('print',['order_id'=>$order['id']])}}", '_blank');
    }
});
@endif
@if(isset($access['audit']))
tbar.push({
    xtype: 'button',
    text: '工作流程',
    handler: function() {
        iframeBox("工作流程处理","{{url('audit')}}?order_id={{$order['id']}}&client_id={{$order['client_id']}}&s={{time()}}", "提交表单");
    }
});
@endif

@if(isset($access['export']))
tbar.push({
    xtype: 'button',
    text: '导出订单',
    handler: function() {
        app.confirm('{{url("export")}}?id={{$order["id"]}}', '确认要导出订单吗？');
    }
});
@endif

@if(authorise('receivable.create','customer'))
tbar.push({
    xtype: 'button',
    text: '回款登记',
    handler: function() {
        formBox('新建','{{url("customer/receivable/create", ["customer_id"=>$order["client_id"]])}}', 'window-form');
    }
});
@endif

@if(authorise('cash.create','promotion'))
tbar.push({
    xtype: 'button',
    text: '促销兑现',
    handler: function() {
        formBox('新建','{{url("promotion/cash/create", ["customer_id"=>$order["client_id"]])}}', 'window-form');
    }
});
@endif

tbar.push({
    xtype: 'button',
    text: '合作要约',
    handler: function() {
        iframeBox("合作要约","{{url('customer/contract/view',['customer_id'=>$order['client_id']])}}");
    }
});
tbar.push({
    xtype: 'button',
    text: '同步订单',
    handler: function() {
        syncCNNZ();
    }
});
</script>

@endif

@include('order/data')

@if($order['status'] == 0)
    <div class="alert alert-warning" role="alert">订单已经废除无法做任何审核操作。</div>
@endif

<style>
    .order_tabs { margin: 10px 0; }
    .order_tabs .tab-pane { overflow: auto; height: 200px; background-color: #fff;  }
    .order_tabs .nav-tabs { padding-left: 15px; }
</style>

<div class="order_tabs">

<!-- Nav tabs -->
  <ul class="nav nav-tabs p-l" role="tablist">
    <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">审核流程</a></li>
    <li role="presentation"><a href="#promotion" aria-controls="promotion" role="tab" data-toggle="tab">促销列表</a></li>
    <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">备注信息</a></li>
    <li role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">物流信息</a></li>
    <li role="presentation"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">随货同行</a></li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="home">
        @include('order/audit_info')
    </div>
    <div role="tabpanel" class="tab-pane" id="promotion">

        <div class="panel">
            <div class="table-responsive">
            <table class="table table-form">
                <tr>
                    <th align="center">促销编号</th>
                    <th align="center">促销类型</th>
                    <th align="center">开始日期</th>
                    <th align="center">结束日期</th>
                    <th align="center">流程</th>
                    <th align="center">创建时间</th>
                    <th align="center">编号</th>
                    <th align="center"></th>
                </tr>
                @foreach($promotions as $promotion)
                <tr>
                    <td align="center">{{$promotion['number']}}</td>
                    <td align="center">{{option('promotion.type', $promotion['type_id'])}}</td>
                    <td align="center">{{$promotion['start_at']}}</td>
                    <td align="center">{{$promotion['end_at']}}</td>
                    <td align="center">{{$promotion['step_number']}}</td>
                    <td align="center">@datetime($promotion['created_at'])</td>
                    <td align="center">{{$promotion['id']}}</td>
                    <td align="center"><a class="option" href="{{url('promotion/promotion/show', ['id' => $promotion['id']])}}">查看</a></td>
                </tr>
                @endforeach
            </table>
            </div>
        </div>

    </div>
    <div role="tabpanel" class="tab-pane" id="profile">
        {{$order['description']}}
    </div>
    <div role="tabpanel" class="tab-pane" id="messages">
        @include('order/transport_info')
    </div>
    <div role="tabpanel" class="tab-pane" id="settings">
        {{$order['goods']}}
    </div>
  </div>
</div>

<div class="panel">

    <div class="table-responsive">
    <table class="table table-form">
        <tr>
            <td colspan="3" align="left">
                <strong>汇款方式</strong>(请将货款汇入以下任意一个开户行、账号，并将汇款凭证传真至我司以便及时发货(028-38291666)
            </td>
        </tr>

        <tr>
            <td>
                ①&nbsp;&nbsp;收款人: 四川省川南酿造有限公司<br />
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;开户行: 工商银行眉山市分行营业部<br />
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;帐&nbsp;&nbsp;&nbsp;号: 2313 3991 1910 0244 382
            </td>

            <td>
                ②&nbsp;&nbsp;收款人: 四川省川南酿造有限公司<br />
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;开户行: 中国农业发展银行眉山市分行营业室<br />
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;帐&nbsp;&nbsp;&nbsp;号: 2035 1149 9001 0000 0199 391
            </td>
            <td>
                ③&nbsp;&nbsp;收款人：四川省川南酿造有限公司<br />
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;开户行：中国建设银行眉山财富中心支行<br />
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;帐&nbsp;&nbsp;&nbsp;号: 5100 1697 2680 5250 2839
            </td>
        </tr>
    </table>
    </div>
</div>

<div class="panel">
<div class="table-responsive">
<table class="table table-form">
  <tr>
    <td>
        <input type="hidden" name="order_id" value="{{$order['id']}}" />
        <button type="button" onclick="history.back();" class="btn btn-default">返回</button>
        @if(Auth::user()->role_id == 16 || Auth::user()->role_id == 1 || Auth::user()->role_id == 22)
            <button type="button" onclick="sumbitEdit();" class="btn btn-success"><i class="fa fa-check-circle"></i> 提交</button>
        @endif

        <div class="pull-right">
            <a class="btn btn-default btn-sm" href="{{url('view',array('id'=>$page['after']['id']))}}"><i class=" fa fa-chevron-left"></i> 上一条</a>
            <a class="btn btn-default btn-sm" href="{{url('view',array('id'=>$page['first']['id']))}}"><i class=" icon icon-chevron-right"></i> 下一条</a>
        </div>
    </td>
  </tr>
</table>
</div>
</div>

</from>

<script type="text/javascript">
function sumbitEdit()
{
    document.myform.submit();
}

function syncCNNZ()
{
    Ext.MessageBox.confirm('同步订单', '确定要同步此订单吗？', function(btn) {
        if(btn == 'yes') {
            $.getJSON("{{url('sync')}}",{id:'{{$order['id']}}'},function(res) {
                $.toastr('success', res.data, '同步订单');
            });
        }
    });
}

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
                @if(count($client['bank'])) @foreach($client['bank'] as $k => $v) <option value="{{$v['tax_name']}}" @if($order['invoice_company'] == $v['tax_name']) selected @endif >{{$v['tax_name']}}</option> @endforeach @endif </select>\
                <span class="help-inline">若没有请与客户经理联系。</span>';
    }
    if(id == 2 || id == 3) {
        title = '打款人';
        text = '<input type="text" class="form-control input-sm input-inline" id="invoice_company" name="invoice_company"> <span class="help-inline">请输入持卡人名。</span>';
    }
    document.getElementById('invoice_type_text').innerHTML = text;
    document.getElementById('invoice_type_title').innerHTML = title;
}
</script>
