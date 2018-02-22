<div class="panel">
    <div class="table-responsive">
        <table class="table table-form">

            <thead>
                <tr>
                    <th colspan="6" align="left">送货列表</th>
                </tr>
                <tr>
                    <th align="left">类别</th>
                    <th align="left">商品</th>
                    <th align="left">规格</th>
                    <th align="center">日期</th>
                    <th align="right">数量</th>
                </tr>
            </thead>

            <tbody>
                @foreach($order as $data)
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
                        @datetime($data->created_at)
                    </td>
                    <td align="right">
                        {{$data->quantity}}
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
                    <td align="right">{{$order->sum('quantity')}}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>