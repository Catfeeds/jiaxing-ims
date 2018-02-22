<form method="post" enctype="multipart/form-data" action="{{url()}}" id="myform" name="myform">

<div class="panel">
    <div class="table-responsive">
        <table class="table table-form">
            <tr>
                <th align="left">库存登记资料</th>
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
                    <th align="left">商品类别</th>
                    <th align="left">商品名称</th>
                    <th align="left">商品规格</th>
                    <th align="center">现存数量</th>
                    <th align="center">生产计划数量</th>
                    <th align="center">生产计划日期</th>
                    <th align="center">生产计划周期(天)</th>
                    <th align="left">备注</th>
                </tr>
            </thead>

            <tbody>
                @if($supplier->products)
                @foreach($supplier->products as $i => $data)
                <tr>
                    <td align="left">
                        {{$categorys[$products[$data->id]->category_id]['text']}}
                    </td>
                    <td align="left">
                        {{$data->name}}
                    </td>
                    <td align="left">
                        {{$data->spec}}
                    </td>
                    <td align="center">
                        <input type="text" id="data_quantity" name="datas[{{$i}}][quantity]" class="form-control input-sm input-inline">
                    </td>
   
                    <td align="center">
                        <input type="text" id="data_plan" name="datas[{{$i}}][plan]" class="form-control input-sm input-inline">
                    </td>

                    <td align="center">
                        <input type="text" id="data_plan_date" data-toggle="date" name="datas[{{$i}}][plan_date]" class="form-control input-sm input-inline">
                    </td>

                    <td align="center">
                        <input type="text" id="data_plan_cycle" name="datas[{{$i}}][plan_cycle]" class="form-control input-sm input-inline"> 天
                    </td>

                    <td align="center">
                        <input type="hidden" id="data_product_id" name="datas[{{$i}}][product_id]" value="{{$data->id}}">
                        <input type="text" id="data_description" name="datas[{{$i}}][description]" class="form-control input-sm">
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>

<div class="panel">
    <div class="table-responsive">
        <table class="table table-form">
        <tr>
            <td align="left">
                <input type="hidden" id="supplier_id" name="supplier_id" value="{{$supplier->id}}">
                <button type="submit" class="btn btn-success"><i class="fa fa-check-circle"></i> 提交</button>
                <button type="button" onclick="history.back();" class="btn btn-default">返回</button>
            </td>
        </tr>
        </table>
    </div>
</div>

</form>