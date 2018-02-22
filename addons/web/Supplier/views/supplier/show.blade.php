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
            <th colspan="4" align="left">
                供应商品
            </th>
        </tr>
        <tr>
            <th align="left">名称</th>
            <th align="center" width="200">规格</th>
            <th align="center" width="160">单位</th>
            <th align="right" width="180">最新单价</th>
            <th align="center" width="160">ID</th>
            <th align="right" width="80"></th>
        </tr>

        @if(count($products))
        @foreach($products as $product)
        <tr>
            <td>{{$product->name}}</td>
            <td align="center">{{$product->spec}}</td>
            <td align="center">{{option('goods.unit', $product->unit)}}</td>
            <td align="right"><a class="option" href="javascript:viewBox('price', '历史单价 [{{$product['name']}} - {{$product['spec']}}]', '{{url('product/price', ['id'=>$product['id']])}}');"> {{$prices[$product->id]}} </a></td>
            <td align="center">{{$product->id}}</td>
            <td align="right">
                @if(authorise('product.create', 'supplier'))
                <a class="btn btn-xs btn-info" href="{{url('supplier/product/create',['id'=>$product->id,'refer'=>URL::full()])}}"><i class="fa fa-pencil"></i> 编辑</a>
                @endif
            </td>
        </tr>
        @endforeach
        @endif

    </table>
</div>

<div class="panel">
    <table class="table table-form">
        <tr>
            <th colspan="5" align="left">
                联系人
                @if(isset($access['create']))
                <span class="pull-right">
                    <a class="btn btn-xs btn-info" href="{{url('supplier/contact/create',['supplier_id'=>$supplier->id])}}"><i class="icon icon-plus"></i> 新增</a>
                </sapn>
                @endif
            </th>
        </tr>
        <tr>
            <th align="left">姓名</th>
            <th align="left" width="200">电话</th>
            <th align="center" width="160">职位</th>
            <th align="center" width="180">性别</th>
            <th align="center" width="160"></th>
        </tr>

        @if(count($contacts))
        @foreach($contacts as $contact)
        <tr>
            <td>@if($contact->id == $supplier->contact_id)<span class="label bg-danger">首选</span>@endif {{$contact->user->nickname}}</td>
            <td>{{$contact->user->mobile}}</td>
            <td align="center">{{option('contact.post', $contact->user->post)}}</td>
            <td align="center">{{option('user.gender', $contact->user->gender)}}</td>
            <td align="right">
                @if(isset($access['create']))
                <a class="btn btn-xs btn-info" href="{{url('supplier/contact/create',['id'=>$contact->id])}}">编辑</a>
                <a class="btn btn-xs btn-info" onclick="app.confirm('{{url('supplier/contact/delete',['id'=>$contact->id])}}','确定要删除吗？');" href="javascript:;">删除</a>
                @endif
            </td>
        </tr>
        @endforeach
        @endif

    </table>
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
