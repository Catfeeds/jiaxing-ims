<div class="panel">

    <form class="form-horizontal" method="post" action="{{url()}}" id="myform" name="myform">
        <div class="table-responsive">
            <table class="table table-form">

                <tr>
                    <td width="10%" align="right">名字</td>
                    <td align="left">
                        <input class="form-control input-inline input-sm" type="text" id="nickname" name="user[nickname]" value="{{old('user.nickname', $row->user->nickname)}}">
                    </td>

                    <td width="10%" align="right">所属供应商</td>
                    <td align="left">
                        {{Dialog::user('supplier','contact[supplier_id]', old('contact.supplier_id', $row->supplier_id), 0, 0)}}
                    </td>

                </tr>

                <tr>
                    <td align="right">手机</td>
                    <td align="left">
                        <input class="form-control input-inline input-sm" type="text" id="mobile" name="user[mobile]" value="{{old('user.mobile',$row->user->mobile)}}">
                    </td>
                    <td align="right">电话</td>
                    <td align="left">
                        <input type="text" class="form-control input-inline input-sm" name="user[tel]" id="tel" value="{{old('user.tel', $row->user->tel)}}">
                    </td>
                </tr>

                <tr>
                    <td align="right">生日</td>
                    <td align="left">
                        <input data-toggle="date" class="form-control input-inline input-sm" type="text" name="user[birthday]" id="birthday" placeholder="国历" value="{{old('user.birthday', $row->user->birthday)}}">
                    </td>
                    <td align="right">微信</td>
                    <td align="left">
                        <input class="form-control input-inline input-sm" type="text" id="weixin" name="contact[weixin]" value="{{old('contact.weixin', $row->weixin)}}">
                    </td>
                </tr>

                <tr>
                    <td align="right">性别</td>
                    <td align="left">
                        <select class="form-control input-inline input-sm" name="user[gender]" id="gender">
                            <option value=''> - </option>
                            @foreach(option('user.gender') as $gender)
                                <option value='{{$gender['id']}}' @if(old('user.gender', $row->user->gender) == $gender['id']) selected @endif>{{$gender['name']}}</option>
                            @endforeach
                        </select>
                    </td>
                    <td align="right">职位</td>
                    <td align="left">
                        <select class="form-control input-inline input-sm" id='post' name='user[post]'>
                            <option value=''> - </option>
                            @foreach(option('supplier.post') as $_post)
                                <option value='{{$_post['id']}}' @if(old('user.post', $row->user->post) == $_post['id']) selected @endif>{{$_post['name']}}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>

                <tr>
                    <td align="right">描述</td>
                    <td align="left" colspan="3">
                        <textarea class="form-control" name="contact[description]" id="description">{{old('contact.description', $row->description)}}</textarea>
                    </td>
                </tr>

                <tr>
                    <td align="right"></td>
                    <td align="left">
                        <input type="hidden" name="id" value="{{$row->id}}">
                        <button type="submit" class="btn btn-success"><i class="fa fa-check-circle"></i> 保存</button>
                        <button type="button" onclick="history.back();" class="btn btn-default">返回</button>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
