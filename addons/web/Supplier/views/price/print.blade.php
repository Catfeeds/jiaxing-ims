<table class="table">
    <tr>
        <th colspan="4" align="center" class="title">供应商商品价格单</th>
    </tr>

<table class="table">
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

<table class="table">

    <thead>
        <tr>
            <th colspan="7" align="left">商品列表</th>
        </tr>
        <tr>
            <th align="left">商品类别</th>
            <th align="left">商品名称</th>
            <th align="left">商品规格</th>
            <th align="right">单价</th>
            <th align="center">生效时间</th>
            <th align="left">备注</th>
        </tr>
    </thead>

    <tbody>
        @foreach($datas as $data)
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