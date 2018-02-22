<form method="post" enctype="multipart/form-data" action="{{url()}}" id="myform" name="myform">

<div class="panel">
<div class="table-responsive">
<table class="table table-form">
<tr>
    <th colspan="4" align="left">基本资料</th>
</tr>
<tr>
    <td width="15%" align="right">供应商名称 <span class="red">*</span></td>
    <td width="35%">
        <input type="text" id="nickname" name="user[nickname]" value="{{old('user.nickname', $supplier->user->nickname)}}" class="form-control input-sm" />
    </td>

    <td width="15%" align="right">法人代表</td>
    <td width="35%">
        <input type="text" id="legal" name="supplier[legal]" value="{{old('supplier.legal',$supplier->legal)}}" class="form-control input-sm" />
    </td>
</tr>

<tr>
    <td  align="right">供应商代码 <span class="red">*</span></td>
    <td>
        <input type="text" id="username" name="user[username]" value="{{old('user.username', $supplier->user->username)}}" class="form-control input-sm" />
    </td>

    <td align="right">登录密码</td>
    <td>
        <input type="password" id="password" name="user[password]" placeholder="不修改请留空。" class="form-control input-sm" />
    </td>

</tr>

<tr>

    <td align="right">公司性质</td>
    <td>
        <input type="text" id="nature" name="supplier[nature]" value="{{old('supplier.nature',$supplier->nature)}}" class="form-control input-sm" />
    </td>
    <td align="right">营业执照</td>
    <td>
        <input type="file" id="image" name="supplier[image]" value="{{old('supplier.image', $supplier->image)}}" />
        <span class="help-block">不更新营业执照请留空。</span>
    </td>
</tr>

<tr>
    <td align="right">公司电话</td>
    <td><input type="text" id="tel" name="user[tel]" value="{{old('user.tel', $supplier->user->tel)}}" class="form-control input-sm" /></td>
    <td align="right">公司传真</td>
    <td align="left">
        <input type="text" id="fax" name="user[fax]" value="{{old('user.fax', $supplier->user->fax)}}" class="form-control input-sm" />
    </td>
</tr>

<tr>
    <td align="right">公司税号</td>
    <td>
        <input type="text" id="tax_number" name="supplier[tax_number]" value="{{old('supplier.tax_number', $supplier->tax_number)}}" class="form-control input-sm" />
    </td>

    <td align="right">联系地址</td>
    <td align="left">
        <select class="form-control input-inline input-sm" name="user[address][]" id="province">
        </select>
        &nbsp;
        <select class="form-control input-inline input-sm" name="user[address][]" id="city">
        </select>
        &nbsp;
        <select class="form-control input-inline input-sm" name="user[address][]" id="county">
        </select>
    </td>
</tr>

<tr>
    <td align="right">相关文件</td>
    <td colspan="3">
        @include('attachment/create')
    </td>
</tr>

@if($supplier->id)

<tr>
    <td align="right">首选联系人</td>
    <td>
        <select class="form-control input-inline input-sm" name="supplier[contact_id]" id="contact_id">
            @foreach($contacts as $contact)
            <option value="{{$contact['id']}}" @if($supplier->contact_id == $contact->id) selected @endif>{{$contact->user->nickname}}</option>
            @endforeach
        </select>
    </td>
    <td colspan="2"></td>
</tr>

@else

<tr>
    <th colspan="4" align="left">首选联系人</th>
</tr>

<tr>
    <td align="right">姓名</td>
    <td><input type="text" id="nickname" name="contact[nickname]" class="form-control input-sm" /></td>
    
    <td align="right">生日</td>
    <td>
      <input type="text" id="birthday" name="contact[birthday]" data-toggle="date" class="form-control input-inline input-sm" />
    </td>
</tr>

<tr>
    <td align="right">手机</td>
    <td><input type="text" id="contact_mobile" name="contact[mobile]" class="form-control input-sm" /></td>

    <td align="right">电话</td>
    <td><input type="text" id="contact_tel" name="contact[tel]" class="form-control input-sm" /></td>
</tr>

<tr>
    <td align="right">邮箱</td>
    <td align="left">
        <input type="text" id="contact_email" name="contact[email]" class="form-control input-sm" />
    </td>
    <td align="right">微信</td>
    <td>
        <input type="text" id="supplier_contact_weixin" name="supplier_contact[weixin]" class="form-control input-sm" />
    </td>    
</tr>

<tr>
    <td align="right">性别</td>
    <td>
        <select class="form-control input-inline input-sm" name="user[gender]" id="gender">
            @foreach(option('user.gender') as $gender)
            <option value="{{$gender['id']}}">{{$gender['name']}}</option>
            @endforeach
        </select>
    </td>
    <td align="right">职位</td>
    <td>
        <select class="form-control input-inline input-sm" name="user[gender]" id="gender">
            @foreach(option('contact.post') as $post)
            <option value="{{$post['id']}}">{{$post['name']}}</option>
            @endforeach
        </select>
    </td>
</tr>
@endif

<tr>
    <th colspan="4" align="left">附加资料</th>
</tr>

<tr>
    <td align="right">账号状态</td>
    <td align="left">
        <select class="form-control input-inline input-sm" name="user[status]" id="status">
            <option value="1" @if($supplier->user->status == '1') selected @endif>启用</option>
            <option value="0" @if($supplier->user->status == '0') selected @endif>停用</option>
        </select>
    </td>

    <td align="right">绑定IP</td>
    <td align="left">
        <textarea name="user[auth_ip]" class="form-control input-sm" id="auth_ip" placeholder="请填写绑定IP地址，允许多行。">{{$supplier->user->auth_ip}}</textarea>
    </td>
</tr>

<tr>
<td align="right">安全密钥</td>
    <td align="left">
        <code id="secret">{{$supplier->user->auth_secret}}</code>
        <a class="btn btn-primary btn-xs" onclick="getSecret();" href="javascript:;" title="更新密钥后之前的密钥会失效。">更新</a>
    </td>
    <td align="right"></td>
    <td align="left">
        <label class="checkbox-inline"><input type="checkbox" name="user[auth_totp]" id="auth_totp" value="1" @if($supplier->user->auth_totp == 1) checked @endif> 二次验证</label>
    </td>
</tr>

<tr>
    <td colspan="4">
        <input type="hidden" name="user[id]" value="{{$supplier->user->id}}">
        <input type="hidden" name="supplier[id]" value="{{$supplier->id}}">
        <button type="button" onclick="history.back();" class="btn btn-default">返回</button>
        <button type="submit" class="btn btn-success"><i class="fa fa-check-circle"></i> 保存</button>
    </td>
</tr>

</table>

</div>
</div>

</form>

<script type="text/javascript">
new pcas('province','city', 'county', '{{$supplier->user->address[0]}}', '{{$supplier->user->address[1]}}', '{{$supplier->user->address[2]}}');

function getSecret() {
    $.messager.confirm('安全密钥', '确定要更新安全密钥?', function() {
        $.post("{{url('user/user/secret')}}",{id:'{{$supplier->user->id}}'}, function(res) {
            $("#secret").html(res.data);
        }, 'json');
    });
}
</script>
