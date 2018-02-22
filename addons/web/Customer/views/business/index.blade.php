<div class="panel">

    <div class="wrapper">
        @include('business/query')
    </div>

    <form method="post" id="myform" name="myform">
    <div class="table-responsive">
    <table class="table b-t table-hover">
        <thead>
            <tr>
                <th align="center">
                    <input type="checkbox" class="select-all">
                </th>
                <th align="left">客户名称</th>
                <th align="left">地区</th>
                <th align="left">资料来源</th>
                <th>客户类型</th>
                <th>联系人</th>
                <th align="center">联系人手机</th>
                <th>渠道说明</th>
                <th>产品说明</th>
                <th>合作说明</th>
                <th>补充说明</th>
                <th align="center">名片</th>
                <th>创建者</th>
                <th>{{url_order($search,'created_at','日期')}}</th>
                <th align="center">{{url_order($search,'id','ID')}}</th>
            </tr>
        </thead>

        <tbody>
        @if(count($rows))
            @foreach($rows as $row)
            <tr>
                <td align="center"><input type="checkbox" class="select-row" value="{{$row['id']}}" name="id[]"></td>
                <td align="left">{{$row->name}}</td>
                <td align="left" nowrap="true">{{$row->address}}</td>
                <td align="left">{{$row->source}}</td>
                <td align="center">{{$row->type}}</td>
                <td align="center">{{$row->contacts}}</td>
                <td align="center">{{$row->contacts_phone}}</td>

                <td align="left">{{$row->text_1}}</td>
                <td align="left">{{$row->text_2}}</td>
                <td align="left">{{$row->text_3}}</td>
                <td align="left">{{$row->description}}</td>
                <td align="center">
                    <a class="option" onclick="imageBox('preview', '名片预览', '{{url('index/attachment/show',['id'=>$row->attachment])}}')">名片</a>
                </td>
                <td align="center">{{get_user($row->created_by, 'nickname')}}</td>

                <td align="center">@datetime($row->created_at)</td>

                <td align="center">{{$row->id}}</td>
            </tr>
            @endforeach
         @endif
         </tbody>
    </table>
    </div>
    </form>

    <div class="panel-footer">
        <div class="row">
            <div class="col-sm-1 hidden-xs">
            </div>
            <div class="col-sm-11 text-right text-center-xs">
                {{$rows->render()}}
            </div>
        </div>
    </div>
</div>
