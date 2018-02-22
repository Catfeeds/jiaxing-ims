<table class="table">
    <tr>
        <th colspan="4" align="center" class="title">供应商送货单</th>
    </tr>

    <tr>
        <td width="15%" align="right">供应商名称</td>
        <td width="35%">
            {{$order->supplier->user->nickname}}
        </td>

        <td width="15%" align="right">供应商代码</td>
        <td width="35%">
            {{$order->supplier->user->username}}
        </td>
    </tr>

    <tr>
        <td align="right">法人代表</td>
        <td>
            {{$order->supplier->legal}}
        </td>
        <td align="right">公司性质</td>
        <td>
            {{$order->supplier->nature}}
        </td>
    </tr>

    <tr>
        <td align="right">公司电话</td>
        <td>
            {{$order->supplier->user->tel}}
        </td>
        <td align="right">公司传真</td>
        <td align="left">
            {{$order->supplier->user->fax}}
        </td>
    </tr>

    <tr>
        <td align="right">公司税号</td>
        <td>
            {{$order->supplier->tax_number}}
        </td>

        <td align="right">联系地址</td>
        <td align="left">
            {{$order->supplier->user->address}}
        </td>
    </tr>
</table>

<table class="table">
    <tr>
        <th colspan="4" align="left">送货资料</th>
    </tr>
    <tr>
        <td width="15%" align="right">单号</td>
        <td width="35%">
            {{$order->number}}
        </td>

        <td width="15%" align="right">创建时间</td>
        <td width="35%">
            @datetime($order->created_at)
        </td>
    </tr>
    <tr>
        <td width="15%" align="right">送货日期</td>
        <td width="35%">
            @date($order->delivery_date)
        </td>

        <td width="15%" align="right">备注</td>
        <td width="35%">
        </td>
    </tr>
</table>

<table class="table">

    <thead>
        <tr>
            <th align="left">商品名称</th>
            <th align="left">商品规格</th>
            <th align="right">送货数量</th>
            <th align="right">入库数量</th>
        </tr>
    </thead>

    <tbody>
        @foreach($order->datas as $data)
        <tr>
            <td align="left">
                {{$products[$data->product_id]->name}}
            </td>
            <td align="left">
                {{$products[$data->product_id]->spec}}
            </td>
            <td align="right">
                {{(int)$data->quantity}}
            </td>
            <td align="right">
                {{(int)$stock[$data->product_id]}}
            </td>
        </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <td align="left"><strong>合计<strong></td>
            <td></td>
            <td align="right">{{$order->datas->sum('quantity')}}</td>
            <td align="right">{{array_sum($stock)}}</td>
        </tr>
    </tfoot>
</table>