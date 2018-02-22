<form method="post" action="{{url()}}" id="myform" name="myform">

<div class="panel">
<div class="table-responsive">
<table class="table table-form">
<tr>
    <th colspan="4" align="left">基本资料</th>
</tr>
<tr>
    <td align="right" width="12%">客户名称 <span class="red">*</span></td>
    <td width="38%">
        <input type="text" id="nickname" name="user[nickname]" value="{{old('user.nickname',$row['nickname'])}}" class="form-control input-sm" />
    </td>
    <td width="12%" align="right">客户代码 <span class="red">*</span></td>
    <td width="38%">
        <input type="text" id="username" name="user[username]" value="{{old('user.username',$row['username'])}}" class="form-control input-sm" />
    </td>
</tr>

<tr>
    <td align="right">登录密码 <span class="red">*</span></td>
    <td>
      <input type="password" id="password" name="user[password]" placeholder="不修改请留空" class="form-control input-sm" />
    </td>
    <td align="right">负责人 <span class="red">*</span></td>
    <td>
        {{Dialog::user('user','user[salesman_id]', old('user.salesman_id',$row['salesman_id']), 0, 0)}}
    </td>
</tr>

<tr>
    <td align="right">客户类型 <span class="red">*</span></td>
    <td>
        <select class="form-control input-inline input-sm" name="user[post]" id="post">
            <option value=""> - </option>
            @if(count($types))
                @foreach($types as $k => $v)
                    <option value="{{$v['id']}}" @if(old('user.post', $row['post']) == $v['id'])selected @endif>{{$v['title']}}</option>
                @endforeach
            @endif
        </select>
    </td>
    <td align="right">客户微信 <span class="red">*</span></td>
    <td>
        <input type="text" id="weixin" name="user[weixin]" value="{{old('user.weixin',$row['weixin'])}}" class="form-control input-sm" />
    </td>
</tr>

<tr>
    <td align="right">经营负责人 <span class="red">*</span></td>
    <td><input type="text" id="fullname" name="user[fullname]" value="{{old('user.fullname', $row['fullname'])}}" class="form-control input-sm" /></td>
    <td align="right">经营负责人手机 <span class="red">*</span></td>
    <td><input type="text" id="tel" name="user[mobile]" value="{{old('user.mobile', $row['mobile'])}}" class="form-control input-sm" /></td>
</tr>

<tr>
    <td align="right">经营负责人生日</td>
    <td>
      <input type="text" id="birthday" name="user[birthday]" value="{{old('user.birthday', $row['birthday'])}}" data-toggle="date" class="form-control input-inline input-sm" />
    </td>
    <td align="right">经营负责人性别</td>
    <td>
        <select class="form-control input-inline input-sm" name="user[gender]" id="gender">
            @foreach(option('user.gender') as $gender)
                <option value="{{$gender['id']}}" @if($row->gender == $gender['id']) selected @endif>{{$gender['name']}}</option>
            @endforeach
        </select>
    </td>
</tr>

<tr>
    <td align="right">邮箱地址</td>
    <td align="left">
        <input type="text" id="email" name="user[email]" value="{{old('user.email', $row['email'])}}" class="form-control input-sm" />
    </td>
    <td align="right">公司电话</td>
    <td><input type="text" id="tel" name="user[tel]" value="{{old('user.tel', $row['tel'])}}" class="form-control input-sm" /></td>
</tr>

<tr>
    <td align="right">公司传真</td>
    <td><input type="text" id="fax" name="user[fax]" value="{{old('user.fax', $row['fax'])}}" class="form-control input-sm" /></td>
    <td align="right">公司地址</td>
    <td>
        <input type="text" id="address" name="user[address]" value="{{old('user.address', $row['address'])}}" class="form-control input-sm" />
    </td>
</tr>

<tr>
    <td align="right">配送负责人 <span class="red">*</span></td>
    <td>
        {{Dialog::user('user','client[transport_user_id]', old('client.transport_user_id',$client['transport_user_id']), 0, 0)}}
    </td>

    <td align="right">客户圈</td>
    <td>
        {{Dialog::user('circle','client[circle_id]', old('client.circle_id', $client['circle_id']), 0, 0)}}
    </td>
</tr>

<tr>
    <th colspan="4" align="left">
        客户资料
    </th>
</tr>
<tr>
    <td align="right">销售负责人</td>
    <td>
        <input type="text" id="client_saleman_person" name="client[saleman_person]" value="{{old('client.saleman_person', $client['saleman_person'])}}" class="form-control input-sm" />
    </td>

    <td align="right">销售负责人手机</td>
    <td>
        <input type="text" id="client_saleman_phone" name="client[saleman_phone]" value="{{old('client.saleman_phone', $client['saleman_phone'])}}" class="form-control input-sm" />
    </td>
</tr>

<tr>
    <td align="right">订单负责人</td>
    <td>
        <input type="text" id="client_order_person" name="client[order_person]" value="{{$client['order_person']}}" class="form-control input-sm" />
    </td>

    <td align="right">订单负责人手机</td>
    <td>
        <input type="text" id="client_order_phone" name="client[order_phone]" value="{{$client['order_phone']}}" class="form-control input-sm" />
    </td>
</tr>

<tr>
    <td align="right">财务负责人</td>
    <td>
        <input type="text" id="client_finance_person" name="client[finance_person]" value="{{$client['finance_person']}}" class="form-control input-sm" />
    </td>

    <td align="right">财务负责人手机</td>
    <td>
        <input type="text" id="client_finance_phone" name="client[finance_phone]" value="{{$client['finance_phone']}}" class="form-control input-sm" />
    </td>
</tr>

<tr>
    <td align="right">后勤负责人</td>
    <td>
        <input type="text" id="client_logistics_person" name="client[logistics_person]" value="{{$client['logistics_person']}}" class="form-control input-sm" />
    </td>

    <td align="right">后勤负责人手机</td>
    <td>
        <input type="text" id="logistics_phone" name="client[logistics_phone]" value="{{$client['logistics_phone']}}" class="form-control input-sm" />
    </td>
</tr>

<tr>
    <td align="right">收货人</td>
    <td><input type="text" id="warehouse_contact" name="client[warehouse_contact]" placeholder="联系人姓名" value="{{$client['warehouse_contact']}}" class="form-control input-sm" /></td>

    <td align="right">收货人手机</td>
    <td><input type="text" id="warehouse_mobile" name="client[warehouse_mobile]" placeholder="联系人手机" value="{{$client['warehouse_mobile']}}" class="form-control input-sm" /></td>
</tr>

<tr>
    <td align="right">收货电话</td>
    <td><input type="text" id="warehouse_tel" name="client[warehouse_tel]" placeholder="仓库座机" value="{{$client['warehouse_tel']}}" class="form-control input-sm" /></td>

    <td align="right">收货地址</td>
    <td><input type="text" id="warehouse_address" name="client[warehouse_address]" value="{{$client['warehouse_address']}}" class="form-control input-sm" /></td>
</tr>

<tr>

    <td align="right">运费政策</td>
    <td align="left">
        <label class="checkbox-inline">
            <input type="checkbox" name="client[freight_type]" id="freight_type" value="1" @if(old('client.freight_type', $client['freight_type']) == 1) checked @endif> 不收费
        </label>
    </td>

    <td align="right">物料政策</td>
    <td align="left">
        <label class="checkbox-inline"><input type="checkbox" name="client[sp_materiel]" id="sp_materiel" value="1" @if(old('client.sp_materiel', $client['sp_materiel']) == 1) checked @endif> 不收费</label>
    </td>

</tr>

<tr>

    <td align="right">总经理审批订单</td>
    <td align="left">
        <label class="checkbox-inline"><input type="checkbox" name="client[order_approve]" id="order_approve" value="1" @if($client['order_approve'] == 1) checked @endif> 是</label>
    </td>
    
    <td align="right">选择地区</td>
    <td align="left">
        
        <select class="form-control input-inline input-sm" name="user[province_id]" id="area_province" onchange="ajaxArea('city', this.value, 0);">
            <option value="">选择</option>
        </select>
        <span>省</span>
        &nbsp;
        <select class="form-control input-inline input-sm" name="user[city_id]" id="area_city" onchange="ajaxArea('county', this.value, 0);">
            <option value="">选择</option>
        </select>
        <span>市</span>
        &nbsp;
        <select class="form-control input-inline input-sm" name="user[county_id]" id="area_county">
            <option value="">选择</option>
        </select>
    </td>
</tr>

<tr>
    <th colspan="4" align="left">
        其他资料
    </th>
</tr>

<tr>
    <td align="right">账号状态</td>
    <td align="left">
        <select class="form-control input-inline input-sm" name="user[status]" id="status">
            <option value="1" @if($row['status'] == '1') selected @endif>启用</option>
            <option value="0" @if($row['status'] == '0') selected @endif>停用</option>
        </select>
    </td>
    <td align="right">绑定IP</td>
    <td align="left">
        <textarea name="user[auth_ip]" class="form-control input-sm" id="auth_ip" placeholder="请填写绑定IP地址，允许多行。">{{$row['auth_ip']}}</textarea>
    </td>
</tr>

<tr>
    <td align="right">安全密钥</td>
    <td align="left">
        <code id="secret">{{$row['auth_secret']}}</code>
        <a class="btn btn-primary btn-xs" onclick="getSecret();" href="javascript:;" title="更新密钥后之前的密钥会失效。">更新</a>
    </td>

    <td align="right">其他选项</td>
    <td>
        <label class="checkbox-inline"><input type="checkbox" name="user[auth_totp]" id="auth_totp" value="1" @if($row['auth_totp'] == 1) checked @endif> 二次验证</label>
    </td>
</tr>

<tr>
    <td align="left" colspan="4">
        <input type="hidden" name="user[id]" value="{{$row['id']}}" />
        <input type="hidden" name="client[id]" value="{{$client['id']}}" />
        <button type="button" onclick="history.back();" class="btn btn-default">返回</button>
        <button type="submit" class="btn btn-success"><i class="fa fa-check-circle"></i> 保存</button>
    </td>
</tr>

</table>

</div>
</div>

</form>

<script type="text/javascript">
function getSecret() {
    $.messager.confirm('安全密钥', '确定要更新安全密钥。', function(r) {
        if (r) {
            $.post("{{url('user/user/secret')}}",{id:'{{$row['id']}}'}, function(res) {
                $("#secret").html(res.data);
            }, 'json');
        }
    });
}

function ajaxArea(type, parent_id, id) {
    if (parent_id > 0) {
        $.get("{{url('user/region/index')}}?type="+type+"&parent_id="+parent_id+"&id="+id, function(r) {
            $("#area_"+type).empty();
            $("#area_"+type).append('<option value="">选择</option>');
            $("#area_"+type).append(r);
        });
    } else {
        $("#area_"+type).empty();
    }
}

ajaxArea('province', 1, '{{$row['province_id']}}');

@if($row['province_id'] > 0)
ajaxArea('city', '{{$row['province_id']}}', '{{$row['city_id']}}');
@endif

@if($row['city_id'] > 0)
    ajaxArea('county', '{{$row['city_id']}}', '{{$row['county_id']}}');
@endif

</script>
