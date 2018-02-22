<div class="panel">
    <form class="form-horizontal" method="post" action="{{url()}}" id="myform" name="myform">
        <div class="table-responsive">
            <table class="table table-form">
                <tr>
                    <td width="10%" align="right">名字</td>
                    <td width="40%" align="left">
                        {{$row->user->nickname}}
                    </td>
                    <td width="10%" align="right">所属供应商</td>
                    <td width="40%" align="left">
                        {{$row->supplier->user->nickname}}
                    </td>
                </tr>

                <tr>
                    <td align="right">电话</td>
                    <td align="left">
                        {{$row->user->tel}}
                    </td>
                    <td align="right">手机</td>
                    <td align="left">
                        {{$row->user->mobile}}
                    </td>
                </tr>

                <tr>
                    <td align="right">性别</td>
                    <td align="left">
                        {{option('user.gender', $row->user->gender)}}
                    </td>
                    <td align="right">生日</td>
                    <td align="left">
                        {{$row->user->birthday}}
                    </td>
                </tr>

                <tr>
                    <td align="right">职位</td>
                    <td align="left">
                        {{option('supplier.position', $row->user->post)}}
                    </td>
                    <td align="right">微信</td>
                    <td align="left">
                        {{$row->weixin}}
                    </td>
                </tr>
                <tr>
                    <td align="right">描述</td>
                    <td align="left" colspan="3">
                        {{$row->description}}
                    </td>
                </tr>
                <tr>
                    <td align="right"></td>
                    <td align="left">
                        <button type="button" onclick="history.back();" class="btn btn-default">返回</button>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
