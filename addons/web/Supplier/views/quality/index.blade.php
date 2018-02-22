<div class="panel">
    <div class="wrapper">
        @include('quality/query')
    </div>

    <form method="post" id="myform" name="myform">
    <div class="table-responsive">
        <table class="table m-b-none table-hover">
            <tr>
                <th align="center">
                    <label class="i-checks i-checks-sm m-b-none">
                        <input class="select-all" type="checkbox"><i></i>
                    </label>
                </th>
                <th align="left">主题</th>
                <th align="center">类别</th>
                <th>产品</th>
                <th>数量</th>
                <th>金额</th>
                <th align="center">供应商</th>
                <th align="center">创建者</th>
                <th align="center">创建时间</th>
                <th align="left">流程</th>
                <th align="center">编号</th>
                <th align="center"></th>
            </tr>
            @if(count($rows))
                @foreach($rows as $row)

                <?php
                    $step = get_step_status($row);
                ?>

                <tr>
                    <td align="center">
                        <label class="i-checks i-checks-sm m-b-none">
                            <input class="select-row" type="checkbox" name="id[]" value="{{$row->id}}"><i></i>
                        </label>
                    </td>
                    <td align="left">
                        <a href="{{url('show',['id'=>$row->id])}}">{{$row->name}}</a>
                    </td>
                    <td align="center">{{option('supplier.quality.category', $row->category_id)}}</td>
                    <td align="center">{{$row->product->name}}</td>
                    <td align="center">{{$row->quantity}}</td>
                    <td align="center">{{$row->money}}</td>
                    <td align="center">{{$row->supplier->user->nickname}}</td>
                    <td align="center">{{get_user($row->created_by,'nickname')}}</td>
                    <td align="center">@datetime($row->created_at)</td>
                    <td align="left">{{$step['text']}}</td>
                    <td align="center">{{$row->id}}</td>
                    <td align="center">
                        <a class="option" href="{{url('show',['id'=>$row->id])}}"> 查看 </a>
                        @if($step['edit'])
                            <a class="option" href="{{url('edit', ['id'=>$row->id])}}"> 审核 </a>
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