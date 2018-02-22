<div class="panel">
    <div class="wrapper">
        @include('contact/query')
    </div>

    <form method="post" id="myform" name="myform">
    <div class="table-responsive">
        <table class="table m-b-none b-t table-hover">
            <thead>
            <tr>
                <th align="left">
                    <label class="i-checks i-checks-sm m-b-none">
                        <input class="select-all" type="checkbox"><i></i>
                    </label>
                </th>
                <th align="left">姓名</th>
                <th>手机</th>
                <th>生日</th>
                <th>职位</th>
                <th>类型</th>
                <th align="center">{{url_order($search,'customer_contact.customer_id','所属客户')}}</th>
                <th align="center">{{url_order($search,'customer_contact.id','编号')}}</th>
                <th align="center"></th>
            </tr>
            </thead>
            <tbody>
            @if(count($rows))
                @foreach($rows as $row)
                <tr>
                    <td align="left">
                        <label class="i-checks i-checks-sm m-b-none">
                            <input class="select-row" type="checkbox" name="id[]" value="{{$row->id}}"><i></i>
                        </label>
                    </td>
                    <td align="left">{{$row->user->nickname}}</td>
                    <td align="center">{{$row->user->mobile}}</td>
                    <td align="center">{{$row->user->birthday}}</td>
                    <td align="center">{{option('contact.post', $row->user->post)}}</td>
                    <td align="center">{{option('contact.type', $row->type)}}</td>
                    <td align="center">{{$row->customer->user->nickname}}</td>
                    <td align="center">{{$row->id}}</td>
                    <td align="center">

                        <a class="option" href="javascript:viewBox('show','查看','{{url('show', ['id'=>$row->id])}}');">查看</a>
                        @if(isset($access['create']))
                        <a class="option" href="javascript:formBox('编辑','{{url('create', ['id'=>$row->id])}}','window-form');">编辑</a>
                        @endif
                        <!--
                        <a class="option" href="{{url('show',['id'=>$row->id])}}"> 查看 </a>
                        @if(isset($access['create']))
                            <a class="option" href="{{url('create',['id'=>$row->id])}}"> 编辑 </a>
                        @endif
                        -->
                    </td>
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