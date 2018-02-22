<form method="post" action="{{url()}}" id="myform" name="myform">

<div class="panel">

    <div class="panel-heading tabs-box">
        <ul class="nav nav-tabs">
            <li class="@if(Request::action() == 'profile') active @endif">
                <a class="text-sm" href="{{url('profile')}}">我的资料</a>
            </li>
            <li class="@if(Request::action() == 'password') active @endif">
                <a class="text-sm" href="{{url('password')}}">修改密码</a>
            </li>
            <li class="@if(Request::action() == 'avatar') active @endif">
                <a class="text-sm" href="{{url('avatar')}}">上传头像</a>
            </li>
        </ul>
    </div>

    <div class="panel-body">

        <div class="row">

            <div class="col-sm-2">
                <div class="text-center">
                    <span class="thumb-lg w-auto-folded avatar m-t-sm">
                        <a href="{{url('avatar')}}" title="修改头像"><img src="{{avatar()}}" class="img-full" alt="{{Auth::user()->nickname}}"></a>
                        <div class="h4 font-thin m-t-sm">{{Auth::user()->username}}</div>
                    </span>
                </div>
            </div>
            <div class="col-sm-10">
                <div class="form-group">
                    <label class="control-label">主题</label>
                    <select id="change-theme" class="form-control input-inline input-sm" name="theme" id="theme">
                        <?php $themes = ['primary' => '原色', 'blue' => '经典蓝', 'wood' => '木质纸', 'purple' => '个性紫']; ?>
                        @if(count($themes))
                        @foreach($themes as $theme_key => $theme)
                            <option value="{{$theme_key}}" @if($user->theme == $theme_key) selected @endif>{{$theme}}</option>
                        @endforeach
                        @endif
                    </select>
                </div>

                <div class="form-group">
                    <label class="control-label">安全密钥</label>
                    <strong>{{$user->auth_secret}}</strong>
                    <a href="javascript:;" onclick='$.messager.alert("二次验证二维码","<div align=\"center\"><img src=\"{{$secretURL}}\"></div>");'>
                        <i class="fa fa-qrcode"></i>
                    </a>
                </div>
            </div>

        </div>

    </div>
    <div class="panel-footer">
        <div class="col-sm-offset-2">
            <button type="submit" class="btn btn-success"><i class="fa fa-check-circle"></i> 保存</button>
            <button type="button" onclick="history.back();" class="btn btn-default">返回</button>
        </div>
    </div>
</div>

</form>

<script>
$(function() {
    $('#change-theme').on('change', function() {
        var e = $(this).val();
        $('body').attr('class', 'theme-' + e);
        top.$('body').attr('class', 'theme-' + e);
    });
});
</script>