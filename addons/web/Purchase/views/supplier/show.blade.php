<div class="panel">
    <div class="table-responsive">
        <table class="table table-form">
            <tr>
                <th colspan="4" align="left">
                    基本资料
                    <span class="pull-right">
                        <a class="btn btn-xs btn-info" href="{{url('create',['id'=>$supplier->id])}}"><i class="fa fa-pencil"></i> 编辑</a>
                    </span>
                </th>
            </tr>

            <tr>
                <td width="15%" align="right">供应商名称</td>
                <td width="35%">
                    {{$supplier->user->nickname}}
                </td>

                <td width="15%" align="right">法人代表</td>
                <td width="35%">
                    {{$supplier->legal}}
                </td>
            </tr>

            <tr>
                <td align="right">供应商代码</td>
                <td>
                    {{$supplier->user->username}}
                </td>

                <td align="right">登录密码</td>
                <td>
                    
                </td>

            </tr>

            <tr>
                <td align="right">公司性质</td>
                <td>
                    {{$supplier->nature}}
                </td>
                <td align="right">营业执照</td>
                <td>
                    <a class="btn btn-xs btn-default" href="javascript:imageBox('supplier-image', '营业执照', '{{$upload_url}}/{{$supplier['image']}}');">查看</a>
                </td>
            </tr>

            <tr>
                <td align="right">公司电话</td>
                <td>
                    {{$supplier->user->tel}}
                </td>
                <td align="right">公司传真</td>
                <td align="left">
                    {{$supplier->user->fax}}
                </td>
            </tr>

            <tr>
                <td align="right">公司税号</td>
                <td>
                    {{$supplier->tax_number}}
                </td>

                <td align="right">联系地址</td>
                <td align="left">
                    {{$supplier->user->address}}
                </td>
            </tr>

            <tr>
                <th colspan="4" align="left">附加资料</th>
            </tr>

            <tr>
                <td align="right">账号状态</td>
                <td align="left">
                    @if($supplier->user->status == 1) 启用 @else 停用 @endif
                </td>

                <td align="right">绑定IP</td>
                <td align="left">
                    {{$supplier->user->auth_ip}}
                </td>
            </tr>

            <tr>
                <td align="right">安全密钥</td>
                <td align="left">
                    <code id="secret">{{$supplier->user->auth_secret}}</code>
                    <a href="javascript:;" onclick='$.messager.alert("二次验证二维码","<div align=\"center\"><img src=\"{{$secretURL}}\"><code>{{$supplier->user->auth_secret}}</code></div>");'>
                        <i class="icon icon-qrcode"></i>
                    </a>
                </td>
                <td align="right">其他选项</td>
                <td align="left">
                    <label class="checkbox-inline">
                        <input type="checkbox" id="auth_totp" @if($supplier->user->auth_totp == 1) checked @endif disabled> 二次验证
                    </label>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="panel">
<table class="table table-form">
	<tr>
	    <th colspan="5" align="left">
	    	相关文件
	    </th>
	</tr>
	<tr>
	    <th align="left">文件名</th>
	    <th align="center" width="100">大小</th>
	    <th align="center" width="100">创建者</th>
	    <th align="center" width="150">时间</th>
	</tr>

	@if(count($files))
	@foreach($files as $file)
	<tr>
	    <td>
            {{$file['name']}}
            @if(in_array($file['type'],['jpg','png','gif','bmp']))
                <a class="btn btn-xs btn-default" onclick="imageBox('preview', '附件预览', '{{URL::to('uploads').'/'.$file['path']}}');">预览</a>
            @else
                <a class="btn btn-xs btn-default" href="{{url('index/attach/download',['id'=>$file['id']])}}">下载</a>
            @endif
        </td>
	    <td align="center">{{human_filesize($file['size'])}}</td>
	    <td align="center">{{get_user($file['created_by'], 'nickname')}}</td>
	    <td align="center">@datetime($file['created_at'])</td>
	</tr>
    @endforeach
    @endif

</table>
</div>
