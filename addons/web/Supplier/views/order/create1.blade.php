<form method="post" enctype="multipart/form-data" action="{{url()}}" id="myform" name="myform">

<div class="panel">
    <div class="table-responsive">
        <table class="table table-form">

            <tr>
                <th colspan="2" align="left">订单资料</th>
            </tr>

            <tr>
                <td width="15%" align="right">要求到货日期</td>
                <td>
                    <input type="text" data-toggle="date" id="delivery_date" name="delivery_date" class="form-control input-sm input-inline">
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
                    <th colspan="7" align="left">商品列表</th>
                </tr>
                <tr>
                    <th align="left">类别</th>
                    <th align="left">商品</th>
                    <th align="left">规格</th>
                    <th align="right">周期订单数量</th>
                    <th align="center">周期订单已入库数量</th>
                    <th align="center">本次计划送货数量</th>
                    <th align="left">备注</th>
                </tr>
            </thead>

            <tbody>
                @foreach($plan->datas as $i => $data)
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
                    <td align="right">
                        {{$stock[$data->product_id]}}
                    </td>
                    <td align="center">
                        <input type="text" id="data_quantity" name="datas[{{$i}}][quantity]" class="form-control input-sm">
                        <input type="hidden" id="data_number" name="datas[{{$i}}][product_id]" value="{{$data->product_id}}">
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
                    <td align="right">{{$plan->datas->sum('quantity')}}</td>
                    <td align="right">{{array_sum($stock)}}</td>
                    <td align="center">
                        <input type="hidden" id="supplier_id" name="supplier_id" value="{{$plan->supplier_id}}">
                        <input type="hidden" id="plan_id" name="plan_id" value="{{$plan_id}}">
                    </td>
                    <td></td>
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
                <button type="submit" class="btn btn-success"><i class="fa fa-check-circle"></i> 确认</button>
                <button type="button" onclick="history.back();" class="btn btn-default">返回</button>
            </td>
        </tr>
        </table>
    </div>
</div>

</form>