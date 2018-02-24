<div class="wrapper-sm">

<form method="post" class="form-horizontal" action="{{url()}}" id="stock-warehouse-form" name="stock_warehouse_form">

<div class="form-group">
    <label for="name" class="col-sm-2 control-label">供应商名称 <span class="red">*</span></label>
    <div class="col-sm-10">
        <input type="text" class="form-control input-sm" name="name" value="{{$row['name']}}" />
    </div>
</div>

<div class="form-group">
    <label for="personal_name" class="col-sm-2 control-label">联系人</label>
    <div class="col-sm-10">
        <input type="text" class="form-control input-sm" name="personal_name" value="{{$row['personal_name']}}" />
    </div>
</div>

<div class="form-group">
    <label for="personal_mobile" class="col-sm-2 control-label">联系手机</label>
    <div class="col-sm-10">
        <input type="text" class="form-control input-sm" name="personal_mobile" value="{{$row['personal_mobile']}}" />
    </div>
</div>

<div class="form-group">
    <label for="tel" class="col-sm-2 control-label">电话</label>
    <div class="col-sm-10">
        <input type="text" class="form-control input-sm" name="tel" value="{{$row['tel']}}" />
    </div>
</div>

<div class="form-group">
    <label for="fax" class="col-sm-2 control-label">传真</label>
    <div class="col-sm-10">
        <input type="text" class="form-control input-sm" name="fax" value="{{$row['fax']}}" />
    </div>
</div>

<div class="form-group">
    <label for="settlement" class="col-sm-2 control-label">结算方式</label>
    <div class="col-sm-10">
        <input type="text" class="form-control input-sm" name="settlement" value="{{$row['settlement']}}" />
    </div>
</div>

<div class="form-group">
    <label for="bank_name" class="col-sm-2 control-label">银行户名</label>
    <div class="col-sm-10">
        <input type="text" class="form-control input-sm" name="bank_name" value="{{$row['bank_name']}}" />
    </div>
</div>

<div class="form-group">
    <label for="bank_deposit" class="col-sm-2 control-label">开户行</label>
    <div class="col-sm-10">
        <input type="text" class="form-control input-sm" name="bank_deposit" value="{{$row['bank_deposit']}}" />
    </div>
</div>

<div class="form-group">
    <label for="bank_account" class="col-sm-2 control-label">银行卡号</label>
    <div class="col-sm-10">
        <input type="text" class="form-control input-sm" name="bank_account" value="{{$row['bank_account']}}" />
    </div>
</div>

<div class="form-group">
    <label for="address" class="col-sm-2 control-label">地址</label>
    <div class="col-sm-10">
        <input type="text" class="form-control input-sm" name="address" value="{{$row['address']}}" />
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">状态</label>
    <div class="col-sm-10">
        <label class="radio-inline"><input type="radio" name="status" value="1" @if($row['status'] == '1') checked @endif>启用</label>
        <label class="radio-inline"><input type="radio" name="status" value="0" @if($row['status'] == '0') checked @endif>停用</label>
    </div>
</div>

<div class="form-group">
    <label for="remark" class="col-sm-2 control-label">备注</label>
    <div class="col-sm-10">
        <textarea class="form-control input-sm" rows="2" type="text" name="remark" id="remark">{{$row['remark']}}</textarea>
    </div>
</div>

<input type="hidden" name="id" value="{{$row['id']}}" />

</form>

</div>