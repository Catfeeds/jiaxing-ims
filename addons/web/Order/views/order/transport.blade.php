<style>
.content-body {
    margin: 10px 0;
    margin-bottom: 0;
}
.tab-content {
    background-color: #fff;
}
.tab-content .table {
    margin-bottom: 0;
}
.nav-tabs {
    padding-left: 10px;
}
</style>

<form id="myform" name="myform" action="{{url()}}" method="post">

<!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">预发配送信息</a></li>
    <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">实发配送信息</a></li>
  </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="home">
            <table class="table table-form">
                <tr>
                    <th align="right" width="100" style="white-space:nowrap;">预发承运公司: </th>
                    <td align="left">
                        <input type="text" name="transport[advance_car_company]" value="{{$transport['advance_car_company']}}" class="input-text" size="24" />
                        <span class="help-inline">运输公司名称</span>
                    </td>
                </tr>

                <tr>
                    <th align="right">预发车牌号: </th>
                    <td align="left">
                        <input type="text" name="transport[advance_car_number]" value="{{$transport['advance_car_number']}}" class="input-text" size="24" />
                        <span class="help-inline">运输公司车牌号</span>
                    </td>
                </tr>

                <tr>
                    <th align="right">预发数量: </th>
                    <td align="left">
                        <input type="text" name="transport[advance_amount]" value="{{$transport['advance_amount']}}" class="input-text" size="24" />
                        <span class="help-inline">默认获取当前订单实发数量</span>
                    </td>
                </tr>

                <tr>
                    <th align="right">预发重量: </th>
                    <td align="left">
                        <input type="text" name="transport[advance_weight]" value="{{$transport['advance_weight']}}" class="input-text" size="24" />
                        <span class="help-inline">默认获取当前订单实发重量</span>
                    </td>
                </tr>

                <tr>
                    <th align="right">预发时间: </th>
                    <td align="left">
                        <input type="text" name="transport[advance_time]" value="{{$transport['advance_time'] > 0 ? date("Y-m-d H:i:s",$transport['advance_time']) : ""}}" data-toggle="datetime" class="date input-text" size="24" />
                        <span class="help-inline">选择预计发货时间</span>
                    </td>
                </tr>

                <tr>
                    <th align="right">预发仓位: </th>
                    <td align="left">
                        <input type="text" name="transport[advance_depot]" value="{{$transport['advance_depot']}}" class="input-text" size="24" />
                        <span class="help-inline">预装车仓位号码</span>
                    </td>
                </tr>

                <tr>
                    <th align="right">预发仓号: </th>
                    <td align="left">
                        <input type="text" name="transport[advance_depot_number]" value="{{$transport['advance_depot_number']}}" class="input-text" size="24" />
                        <span class="help-inline">预装车仓号是仓位组合。</span>
                    </td>
                </tr>
            </table>
        </div>
        <div role="tabpanel" class="tab-pane" id="profile">
            <table class="table table-form">
                <tr>
                    <th align="right" width="100" style="white-space:nowrap;">承运公司: </th>
                    <td align="left">
                        <input type="text" name="transport[carriage]" class="input-text" size="24" value="{{$transport['carriage']}}" />
                        <span class="help-inline">承运公司名称</span>
                    </td>
                </tr>

                <tr>
                    <th align="right">承运司机: </th>
                    <td align="left">
                        <input type="text" name="transport[contact]" class="input-text" size="24" value="{{$transport['contact']}}" />
                        <span class="help-inline">承运公司司机姓名</span>
                    </td>
                </tr>

                <tr>
                    <th align="right">承运司机电话: </th>
                    <td align="left">
                        <input type="text" name="transport[phone]" class="input-text" size="24" value="{{$transport['phone']}}" />
                        <span class="help-inline">承运公司司机电话</span>
                    </td>
                </tr>

                <tr>
                    <th align="right">运单号: </th>
                    <td align="left">
                        <input type="text" name="transport[reference_number]" class="input-text" size="24" value="{{$transport['reference_number']}}" />
                        <span class="help-inline">此物流的运单号码</span>
                    </td>
                </tr>

                <tr>
                    <th align="right">发货方式: </th>
                    <td align="left">
                        <input type="text" name="transport[manner]" class="input-text" size="24" value="{{$transport['manner']}}" />
                        <span class="help-inline">例如：汽运、火车等</span>
                    </td>
                </tr>

                <tr>
                    <th align="right">到货方式: </th>
                    <td align="left">
                        <input type="text" name="transport[arrivalpattern]" class="input-text" size="24" value="{{$transport['arrivalpattern']}}" />
                        <span class="help-inline">例如：到站或到仓</span>
                    </td>
                </tr>

                <tr>
                    <th align="right">运费承担: </th>
                    <td align="left">
                        <input type="text" name="transport[freight]" class="input-text" size="24" value="{{$transport['freight']}}" />
                        <span class="help-inline">例如：100，必须输入数字金额</span>
                    </td>
                </tr>

                <tr>
                    <th align="right">运费付款方式: </th>
                    <td align="left">
                        <input type="text" name="transport[freight_manner]" class="input-text" size="24" value="{{$transport['freight_manner']}}" />
                        <span class="help-inline">填写代垫或者别的等</span>
                    </td>
                </tr>

                <tr>
                    <th align="right">发货时间: </th>
                    <td align="left">
                        <input type="text" name="order[delivery_time]" data-toggle="datetime" class="date input-text" size="24" value="{{$order['delivery_time'] > 0 ? date("Y-m-d H:i:s",$order['delivery_time']) : ""}}" readonly />
                        <span class="help-inline">发货时间选择即可。</span>
                    </td>
                </tr>

                <tr>
                    <th align="right">预计到货时间: </th>
                    <td align="left">
                        <input type="text" name="transport[advance_arrival_time]" data-toggle="datetime" class="date input-text" size="24" value="{{$transport['advance_arrival_time'] > 0 ? date("Y-m-d H:i:s",$transport['advance_arrival_time']) : ""}}" readonly />
                        <span class="help-inline">预计到货时间选择即可。</span>
                    </td>
                </tr>

                <tr>
                    <th align="right">备注信息: </th>
                    <td align="left">
                        <textarea cols="87" rows="4" class="input-text" id="desc" name="transport[desc]">{{$transport['desc']}}</textarea>
                    </td>
                </tr>
            </table>
        </div>
</div>

<input type="hidden" name="order_id" id="order_id" value="{{$order['id']}}" />
<input type="hidden" name="client_id" id="client_id" value="{{$order['client_id']}}" />

</form>

<script type="text/javascript">
// 弹窗回调保存事件
function iframeSave() {
    document.myform.submit();
}

//弹窗回调取消事件
function iframeCancel() {
}
</script>
