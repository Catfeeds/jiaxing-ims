<div class="panel no-border">

@include('menus/setting')

<div class="wrapper-sm">

<form class="form-horizontal" method="post" action="{{url('store')}}" id="myform" name="myform">

<div class="form-group">
    <label for="sort" class="control-label col-sm-2">系统名称</label>
    <div class="col-sm-6">
        <input type="text" name="data[title]" value="{{$setting['title']}}" id="title" class="form-control input-sm">
    </div>
</div>

<div class="form-group">
    <label for="sort" class="control-label col-sm-2">金额小数位数</label>
    <div class="col-sm-6">
        <input type="text" name="data[money_decimal]" value="{{$setting['money_decimal']}}" id="money_decimal" class="form-control input-sm">
    </div>
</div>

<div class="form-group">
    <label for="sort" class="control-label col-sm-2" >文件上传限制 <i class="fa fa-question-circle hinted" title="上传文件最大(MB)"></i></label>
    <div class="col-sm-6">
        <input type="text" name="data[upload_max]" value="{{$setting['upload_max']}}" id="upload_max" class="form-control input-sm">
    </div>
</div>

<div class="form-group">
    <label for="sort" class="control-label col-sm-2">文件上传格式</label>
    <div class="col-sm-6">
        <input type="text" name="data[upload_type]" value="{{$setting['upload_type']}}" id="upload_type" class="form-control input-sm">
    </div>
</div>

<div class="form-group">
    <label for="sort" class="control-label col-sm-2">图片上传限制 <i class="fa fa-question-circle hinted" title="上传文件最大(MB)"></i></label>
    <div class="col-sm-6">
        <input type="text" name="data[image_upload_max]" value="{{$setting['image_upload_max']}}" id="image_upload_max" class="form-control input-sm">
    </div>
</div>

<div class="form-group">
    <label for="sort" class="control-label col-sm-2">图片上传格式</label>
    <div class="col-sm-6">
        <input type="text" name="data[image_upload_type]" value="{{$setting['image_upload_type']}}" id="image_upload_type" class="form-control input-sm">
    </div>
</div>

<div class="form-group">
    <label for="sort" class="control-label col-sm-2">日期格式</label>
    <div class="col-sm-6">
        <input type="text" name="data[date_format]" value="{{$setting['date_format']}}" id="date_format" class="form-control input-sm">
    </div>
</div>

<div class="form-group">
    <label for="sort" class="control-label col-sm-2">时间格式</label>
    <div class="col-sm-6">
        <input type="text" name="data[time_format]" value="{{$setting['time_format']}}" id="time_format" class="form-control input-sm">
    </div>
</div>

<div class="form-group">
    <label for="sort" class="control-label col-sm-2">日期时间格式</label>
    <div class="col-sm-6">
        <input type="text" name="data[datetime_format]" value="{{$setting['datetime_format']}}" id="datetime_format" class="form-control input-sm">
    </div>
</div>

<div class="form-group">
    <label for="sort" class="control-label col-sm-2">登录尝试次数(次) <i class="fa fa-question-circle hinted" title="最大次数尝试失败后会禁用一段时间"></i></label>
    <div class="col-sm-6">
        <input type="text" name="data[login_try]" value="{{$setting['login_try']}}" id="login_try" class="form-control input-sm">
    </div>
</div>

<div class="form-group">
    <label for="sort" class="control-label col-sm-2">登录失败后锁定(分钟)</label>
    <div class="col-sm-6">
        <input type="text" name="data[login_lock]" value="{{$setting['login_lock']}}" id="login_lock" class="form-control input-sm">
    </div>
</div>

<div class="form-group">
    <label for="sort" class="control-label col-sm-2">登录失败显示验证码(次)</label>
    <div class="col-sm-6">
        <input type="text" name="data[login_captcha]" value="{{$setting['login_captcha']}}" id="login_captcha" class="form-control input-sm">
    </div>
</div>

<div class="form-group">
    <label for="sort" class="control-label col-sm-2"></label>
    <div class="col-sm-6">
    <button type="submit" class="btn btn-info">保存</button>
    </div>
</div>

</div>

</form>

</div>