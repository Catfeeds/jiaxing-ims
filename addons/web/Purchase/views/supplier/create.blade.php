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
