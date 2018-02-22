<div class="panel">
    <div class="table-responsive">
        <table class="table table-form">
        	<tr>
        	    <th colspan="4" align="left">供应商资料</th>
        	</tr>

            <tr>
                <td width="15%" align="right">供应商名称</td>
                <td width="35%">
                    {{$inventory->supplier->user->nickname}}
                </td>

                <td width="15%" align="right">供应商代码</td>
                <td width="35%">
                    {{$inventory->supplier->user->username}}
                </td>
            </tr>

            <tr>
                <td align="right">法人代表</td>
                <td>
                    {{$inventory->supplier->legal}}
                </td>
                <td align="right">公司性质</td>
                <td>
                    {{$inventory->supplier->nature}}
                </td>
            </tr>

            <tr>
                <td align="right">公司电话</td>
                <td>
                    {{$inventory->supplier->user->tel}}
                </td>
                <td align="right">公司传真</td>
                <td align="left">
                    {{$inventory->supplier->user->fax}}
                </td>
            </tr>

            <tr>
                <td align="right">公司税号</td>
                <td>
                    {{$inventory->supplier->tax_number}}
                </td>

                <td align="right">联系地址</td>
                <td align="left">
                    {{$inventory->supplier->user->address}}
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="panel">
    <div class="table-responsive">
        <table class="table table-form">
            <tr>
                <th colspan="4" align="left">库存登记资料</th>
            </tr>

            <tr>
                <td width="15%" align="right">单号</td>
                <td width="35%">
                    {{$inventory->number}}
                </td>

                <td width="15%" align="right">创建时间</td>
                <td width="35%">
                    @datetime($inventory->created_at)
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="panel">
    <div class="table-responsive">
        <table class="table table-form table-hover m-b-none">

            <thead>
                <tr>
                    <th colspan="6" align="left">商品列表</th>
                </tr>
                <tr>
                    <th align="left">商品类别</th>
                    <th align="left">商品名称</th>
                    <th align="left">商品规格</th>
                    <th align="right">现存数量</th>
                    <th align="right">生产计划数量</th>
                    <th align="center">生产计划日期</th>
                    <th align="center">生产计划周期(天)</th>
                    <th align="left">备注</th>
                </tr>
            </thead>

            <tbody>
                @foreach($inventory->datas as $data)
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
                        {{(int)$data->plan}}
                    </td>
                    <td align="center">
                        {{$data->plan_date}}
                    </td>
                    <td align="center">
                        {{(int)$data->plan_cycle}}
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
                    <td align="right">{{array_sum($datas)}}</td>
                    <td align="right">{{$inventory->datas->sum('plan')}}</td>
                    <td align="left"></td>
                    <td align="left"></td>
                    <td align="left"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="panel">
<table class="table" width="100%">
<tr>
    <td align="left">
        <button type="button" class="btn btn-default"><i class="icon icon-print"></i> 打印</button>
        <a class="btn btn-default" href="javascript:history.go(-1);">返回</a>
    </td>
</tr>
</table>
</div>

