<form method="post" enctype="multipart/form-data" action="{{url()}}" id="myform" name="myform">

<div class="panel">
    <div class="table-responsive">
        <table class="table table-form m-b-none">

            <thead>
                <tr>
                    <th colspan="6" align="left">商品列表</th>
                </tr>
                <tr>
                    <th align="left">类别</th>
                    <th align="left">商品</th>
                    <th align="left">规格</th>
                    <th align="center">本次计划送货数量</th>
                    <th align="center">本次入库数量</th>
                    <th align="center">周期订单状态</th>
                    <th align="left">备注</th>
                </tr>
            </thead>

            <tbody>
                @foreach($order->datas as $i => $data)
                <tr>
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
                        {{$data->quantity}}
                    </td>
   
                    <td align="center">
                        <input type="text" id="data_quantity" name="datas[{{$i}}][quantity]" class="form-control input-sm input-inline">
                        <input type="hidden" id="data_product_id" name="datas[{{$i}}][product_id]" value="{{$data->product_id}}">
                        <input type="hidden" id="plan_data_id" name="datas[{{$i}}][plan_data_id]" value="{{$data->plan_data_id}}">
                    </td>

                    <td align="center">
                        <select name="datas[{{$i}}][plan_status]" class="form-control input-sm input-inline">
                            <option value ="0">未送完</option>
                            <option value ="1">已送完</option>
                        </select>
                    </td>

                    <td align="center">
                        <input type="text" id="data_description" name="datas[{{$i}}][description]" class="form-control input-sm">
                    </td>
                </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <td align="left"><strong>合计<strong></td>
                    <td></td>
                    <td></td>
                    <td align="right">{{$order->datas->sum('quantity')}}</td>
                    <td align="right"></td>
                    <td align="center">
                        <input type="hidden" id="supplier_id" name="supplier_id" value="{{$order->supplier_id}}">
                        <input type="hidden" id="order_id" name="order_id" value="{{$order_id}}">
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="panel">
    <div class="table-responsive">
        <table class="table table-form">
        <tr>
            <td align="left">
                <button type="button" onclick="history.back();" class="btn btn-default">返回</button>
                <button type="submit" class="btn btn-success"><i class="fa fa-check-circle"></i> 确认</button>
            </td>
        </tr>
        </table>
    </div>
</div>

</form>