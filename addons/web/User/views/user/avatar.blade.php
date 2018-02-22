<script src="{{$asset_url}}/vendor/jcrop/js/jquery.jcrop.min.js" type="text/javascript"></script>
<link href="{{$asset_url}}/vendor/jcrop/css/jquery.jcrop.min.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
$(function() {

  // Create variables (in this scope) to hold the API and image size
  var jcrop_api, boundx, boundy;

    $('#target').Jcrop({
        boxWidth:480,
        boxHeight:480,
        //minSize: [48,48],
        onChange: updatePreview,
        onSelect: updatePreview,
        onSelect: updateCoords,
        aspectRatio: 1,
    },function() {
        // Use the API to get the real image size
        var bounds = this.getBounds();
        boundx = bounds[0];
        boundy = bounds[1];
        // Store the API in the jcrop_api variable

        var x = (boundx - 128) / 2,
        y = (boundy - 128) / 2,
        x2 = x + 128,
        y2 = y + 128;
        this.setOptions({setSelect:[x, y, x2, y2]});

        jcrop_api = this;
    });

    function updateCoords(c)
    {
        $('#x').val(c.x);
        $('#y').val(c.y);
        $('#w').val(c.w);
        $('#h').val(c.h);
    };

    function updatePreview(c)
    {
        if (parseInt(c.w) > 0)
        {
            // 小头像预览Div的大小
            var rx = 48 / c.w;      
            var ry = 48 / c.h;
            $('#preview').css({
                width: Math.round(rx * boundx) + 'px',
                height: Math.round(ry * boundy) + 'px',
                marginLeft: '-' + Math.round(rx * c.x) + 'px',
                marginTop: '-' + Math.round(ry * c.y) + 'px'
            });

            // 大头像预览Div的大小
            var rx = 128 / c.w;
            var ry = 128 / c.h;
            $('#preview2').css({
                width: Math.round(rx * boundx) + 'px',
                height: Math.round(ry * boundy) + 'px',
                marginLeft: '-' + Math.round(rx * c.x) + 'px',
                marginTop: '-' + Math.round(ry * c.y) + 'px'
            });
        }
    };
});

function checkCoords()
{
    if(parseInt($('#w').val())) return true;
    alert('Please select a crop region then press submit.');
    return false;
};

</script>

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

        <div style="width:480px;float:left;">

            @if($src)
                <img id="target" src="{{$src}}">
            @else
                <span class="label bg-danger">请先上传图片</span>
            @endif

            <form class="m-t-sm" method="post" action="{{url()}}" enctype="multipart/form-data">
                <input type="file" name="image">
                <input type="submit" class="btn m-t-xs btn-info" value="上传">
            </form>

        </div>
        
        @if($src)
        <div style="width:128px;float:left;margin-left:10px;">

            <div style="width:128px;height:128px;overflow:hidden;"><img class="thumbnail" id="preview2" src="{{$src}}"></div>
            <div class="m-t-sm" style="width:48px;height:48px;overflow:hidden;"><img class="thumbnail" id="preview" src="{{$src}}"></div>
        
            <form action="{{url()}}" method="post" onsubmit="return checkCoords();">
                <input type="hidden" id="x" name="x">
                <input type="hidden" id="y" name="y">
                <input type="hidden" id="w" name="w">
                <input type="hidden" id="h" name="h">
                <input type="hidden" name="crop" value="{{$crop}}">
                <input type="submit" class="btn m-t-sm btn-success" value="裁剪">
            </form>
        </div>
        @endif

    </div>
</div>
