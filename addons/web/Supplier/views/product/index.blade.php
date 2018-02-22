<div class="panel">

    <div class="panel-heading tabs-box">
        <ul class="nav nav-tabs">
            <li class="@if($query['status'] == 1) active @endif">
                <a class="text-sm" href="{{url('',['status' => 1, 'advanced' => $query['advanced']])}}">正常</a>
            </li>
            <li class="@if($query['status'] == 0) active @endif">
                <a class="text-sm" href="{{url('',['status' => 0, 'advanced' => $query['advanced']])}}">禁用</a>
            </li>
        </ul>
    </div>

    <div class="wrapper">
        @include('product/query')
    </div>

    <form method="post" id="myform" name="myform">
        <div class="table-responsive">
            <table class="table m-b-none table-hover">
                <tr>
                    <th align="left"></th>
                    <th align="left">名称 / 规格 / 条码</th>
                    <th align="left">存货编码</th>
                    <th>单位</th>
                    <th align="right">最低库存</th>
                    <th align="right">最新单价</th>
                    <th align="center">排序</th>
                    <th align="center">ID</th>
                    <th align="center"></th>
            	</tr>
                @if(count($rows))
                @foreach($rows as $v)
                <tr>
                    <td align="center">
                        {{goodsImage($v)}}
                    </td>
    
                    <td align="left">
                        {{$v['name']}}
                        <div>{{$v['spec']}}</div>
                        <div>{{$v['barcode']}}</div>
                    </td>
                    <td align="left">
                        <div>{{$v['stock_number']}}</div>
                    </td>

                    <td align="center">{{option('goods.unit', $v['unit'])}}</td>
                    <td align="right">{{$v['stock_amount']}}</td>
                    <td align="right">@if($prices[$v['id']]) {{$prices[$v['id']]}} @else 无 @endif</td>
                    <td align="center">
                        <input type="text" class="form-control input-sort" name="sort[{{$v['id']}}]" value="{{$v['sort']}}">
                    </td>
                    <td align="center">{{$v['id']}}</td>
                    <td align="center">
                        <a class="option" href="javascript:viewBox('price', '历史单价 [{{$v['name']}} - {{$v['spec']}}]', '{{url('price', ['id'=>$v->id])}}');"> 历史单价 </a>
                        @if($access['create'])
                        <a class="option" href="{{url('create', ['id'=>$v->id])}}"> 编辑 </a>
                        @endif
                        @if($access['delete'])
                        <a class="option" href="javascript:app.confirm('{{url('delete',['id'=>$v->id])}}','确定要删除吗？');">删除</a>
                        @endif
                    </td>
                </tr>
                @endforeach
                @endif
            </table>
        </div>
    </form>

    <footer class="panel-footer">
        <div class="row">
            <div class="col-sm-1 hidden-xs">
                <button type="submit" onclick="optionSort('#myform','{{url('index')}}');" class="btn btn-default btn-sm"><i class="icon icon-sort-by-order"></i> 排序</button>
            </div>
            <div class="col-sm-11 text-right text-center-xs">
                {{$rows->render()}}
            </div>
        </div>
    </footer>

</div>