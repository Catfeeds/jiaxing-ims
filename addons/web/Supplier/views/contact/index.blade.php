<div class="panel">
    <div class="wrapper">
        @include('contact/query')
    </div>

    <form method="post" id="myform" name="myform">
    <div class="table-responsive">
        <table class="table m-b-none table-hover">
            <tr>
                <th align="center">
                    <input class="select-all" type="checkbox">
                </th>
                <th align="left">姓名</th>
                <th>手机</th>
                <th>生日</th>
                <th>职位</th>
                <th align="left">所属供应商</th>
                <th align="center"></th>
            </tr>
            @if(count($rows))
                @foreach($rows as $row)
                <tr>
                    <td align="center">
                        <input class="select-row" type="checkbox" name="id[]" value="{{$row->id}}">
                    </td>
                    <td align="left">{{$row->user->nickname}}</td>
                    <td align="center">{{$row->user->mobile}}</td>
                    <td align="center">{{$row->user->birthday}}</td>
                    <td align="center">{{option('supplier.post', $row->user->post)}}</td>

                    <td align="left">{{$row->supplier->user->nickname}}</td>
                    <td align="center">
                        <a class="option" href="{{url('show',['id'=>$row->id])}}"> 查看 </a>
                        @if(isset($access['create']))
                            <a class="option" href="{{url('create',['id'=>$row->id])}}"> 编辑 </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            @endif
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