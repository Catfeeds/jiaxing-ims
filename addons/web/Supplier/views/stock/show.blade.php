<div class="panel">
    <div class="table-responsive">
        <table class="table table-form">
        	<tr>
        	    <th colspan="4" align="left">供应商资料</th>
        	</tr>

            <tr>
                <td width="15%" align="right">供应商名称</td>
                <td width="35%">
                    {{$stock->supplier->user->nickname}}
                </td>

                <td width="15%" align="right">供应商代码</td>
                <td width="35%">
                    {{$stock->supplier->user->username}}
                </td>
            </tr>

            <tr>
                <td align="right">法人代表</td>
                <td>
                    {{$stock->supplier->legal}}
                </td>
                <td align="right">公司性质</td>
                <td>
                    {{$stock->supplier->nature}}
                </td>
            </tr>

            <tr>
                <td align="right">公司电话</td>
                <td>
                    {{$stock->supplier->user->tel}}
                </td>
                <td align="right">公司传真</td>
                <td align="left">
                    {{$stock->supplier->user->fax}}
                </td>
            </tr>

            <tr>
                <td align="right">公司税号</td>
                <td>
                    {{$stock->supplier->tax_number}}
                </td>

                <td align="right">联系地址</td>
                <td align="left">
                    {{$stock->supplier->user->address}}
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="panel">
    <div class="table-responsive">
        <table class="table table-form">
            <tr>
                <th colspan="4" align="left">入库资料</th>
            </tr>

            <tr>
                <td width="15%" align="right">单号</td>
                <td width="35%">
                    {{$stock->number}}
                </td>

                <td width="15%" align="right">创建时间</td>
                <td width="35%">
                    @datetime($stock->created_at)
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
                    <th colspan="6" align="left">入库列表</th>
                </tr>
                <tr>
                    <th align="left">类别</th>
                    <th align="left">商品</th>
                    <th align="left">规格</th>
                    <th align="center">送货日期</th>
                    <th align="right">送货数量</th>
                    <th align="right">入库数量</th>
                </tr>
            </thead>

            <tbody>
                @foreach($stock->datas as $data)
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
                        {{(int)$order[$data->product_id]}}
                    </td>
                    <td align="right">
                        {{(int)$data->quantity}}
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
                    <td align="right">{{array_sum($order)}}</td>
                    <td align="right">{{$stock->datas->sum('quantity')}}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>