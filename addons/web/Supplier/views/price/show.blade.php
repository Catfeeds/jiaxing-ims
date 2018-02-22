<form class="form-horizontal" name="myform" id="myform" method="post">

<div class="panel">
    <div class="table-responsive">
        <table class="table table-form">

            <tr>
                <th colspan="4" align="left">基础资料</th>
            </tr>

            <tr>
                <td width="15%" align="right">单号</td>
                <td width="35%">
                    {{$price->sn}}
                </td>

                <td width="15%" align="right">创建时间</td>
                <td width="35%">
                    @datetime($price->created_at)
                </td>

            </tr>

        </table>
    </div>
</div>

<div class="panel">
    <div class="table-responsive">
        <table class="table table-form m-b-none">

            <thead>
                <tr>
                    <th colspan="6" align="left">商品列表</th>
                </tr>
                <tr>
                    <th align="center">存货编码</th>
                    <th align="left">类别</th>
                    <th align="left">商品</th>
                    <th align="left">规格</th>
                    <th align="right">单价</th>
                    <th align="center">生效时间</th>
                    <th align="left">备注</th>
                </tr>
            </thead>

            <tbody>
                @foreach($datas as $data)
                <tr>
                    <td align="center">
                        {{$products[$data->product_id]->stock_number}}
                    </td>
                    <td align="left">
                        {{$categorys[$products[$data->product_id]->category_id]['text']}}
                    </td>
                    <td align="left">
                        {{$products[$data->product_id]->name}}
                    </td>
                    <td align="left">
                        {{$products[$data->product_id]->spec}}
                    </td>

                    <td align="right">
                        {{$data->price}}
                    </td>

                    <td align="center">
                        @datetime($data->date)
                    </td>

                    <td align="left">
                        {{$data->description}}
                    </td>
                    
                </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <td align="left"><strong>合计<strong></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right"></td>
                    <td align="right"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@if($price['main'])
<div class="panel">
    <div class="table-responsive">
        <table class="table table-form">
            <tr>
                <td align="left">@include('attachment/view')</td>
            </tr>
        </table>
    </div>
</div>
@endif

<div class="panel">
    <div class="table-responsive">
        <table class="table table-form">
            <tr>
                <td align="left">

                    <button type="button" onclick="history.back();" class="btn btn-default">返回</button>

                    @if($step['edit'])
                        <a href="javascript:;" onclick="app.turn('{{$step['key']}}');" class="btn btn-danger">审批</a>
                        <input type="hidden" name="step_referer" value="{{session()->get('referer_'.Request::module().'_'.Request::controller().'_index')}}">
                    @endif

                    <a class="btn btn-default" href="{{url('supplier/price/print',['id' => $price->id])}}" target="_blank">打印</a>
                    
                </td>
            </tr>
        </table>
    </div>
</div>

</form>