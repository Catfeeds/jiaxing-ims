<table class="table">
    <tr>
        <th colspan="4" align="center" class="title">供应商计划订单</th>
    </tr>

    <tr>
        <td width="15%" align="right">供应商名称</td>
        <td width="35%">
            {{$plan->supplier->user->nickname}}
        </td>

        <td width="15%" align="right">供应商代码</td>
        <td width="35%">
            {{$plan->supplier->user->username}}
        </td>
    </tr>

    <tr>
        <td align="right">法人代表</td>
        <td>
            {{$plan->supplier->legal}}
        </td>
        <td align="right">公司性质</td>
        <td>
            {{$plan->supplier->nature}}
        </td>
    </tr>

    <tr>
        <td align="right">公司电话</td>
        <td>
            {{$plan->supplier->user->tel}}
        </td>
        <td align="right">公司传真</td>
        <td align="left">
            {{$plan->supplier->user->fax}}
        </td>
    </tr>

    <tr>
        <td align="right">公司税号</td>
        <td>
            {{$plan->supplier->tax_number}}
        </td>

        <td align="right">联系地址</td>
        <td align="left">
            {{$plan->supplier->user->address}}
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
            {{$plan->number}}
        </td>

        <td width="15%" align="right">创建时间</td>
        <td width="35%">
            @datetime($plan->created_at)
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
            <th align="center">计划阶段</th>
            <th align="right">计划数量</th>
            <th align="right">入库数量</th>
            <th align="left">备注</th>
        </tr>
    </thead>

    <tbody>
        @foreach($plan->datas as $data)
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
            <td align="center">
                {{$data->cycle}}
            </td>
            <td align="right">
                {{(int)$data->quantity}}
            </td>
            <td align="right">
                {{(int)$order[$data->product_id]}}
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
            <td align="right">{{$plan->datas->sum('quantity')}}</td>
            <td align="right">{{array_sum($order)}}</td>
            <td></td>
        </tr>
    </tfoot>
</table>