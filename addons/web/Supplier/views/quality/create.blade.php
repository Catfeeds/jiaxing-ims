<div class="panel">

    <form class="form-horizontal" method="post" action="{{url()}}" id="myform" name="myform">
        <div class="table-responsive">
            <table class="table m-b-none table-form">

                <tr>
                    <td width="10%" align="right">主题</td>
                    <td align="left">
                        <input class="form-control input-sm" type="text" id="name" name="supplier_quality[name]" @if(!$fields['supplier_quality.name']) readonly="readonly" @endif value="{{old('name', $row->name)}}">
                    </td>
                <tr>

                <tr>
                    <td align="right">问题类别</td>
                    <td align="left">
                        <select @if(!$fields['supplier_quality.category_id']) readonly="readonly" onfocus="this.defaultIndex=this.selectedIndex;" onchange="this.selectedIndex=this.defaultIndex;" @endif class="form-control input-sm" name="supplier_quality[category_id]" id="category_id">
                            @foreach(option('supplier.quality.category') as $category)
                                <option value="{{$category['id']}}" @if($row->category_id == $category['id']) selected @endif>{{$category['name']}}</option>
                            @endforeach
                        </select>
                    </td>
                <tr>

                <tr>
                    <td align="right">问题来源</td>
                    <td align="left">
                        <select @if(!$fields['supplier_quality.source_id']) readonly="readonly" onfocus="this.defaultIndex=this.selectedIndex;" onchange="this.selectedIndex=this.defaultIndex;" @endif class="form-control input-sm" name="supplier_quality[source_id]" id="source_id">
                            @foreach(option('supplier.quality.source') as $source)
                                <option value="{{$source['id']}}" @if($row->source_id == $source['id']) selected @endif>{{$source['name']}}</option>
                            @endforeach
                        </select>
                    </td>
                <tr>

                </tr>
                    <td align="right">问题商品</td>
                    <td align="left">
                        {{Dialog::user('supplier_product','supplier_quality[product_id]', old('product_id', $row->product_id), 0, !$fields['supplier_quality.product_id'])}}
                    </td>
                </tr>

                <tr>
                    <td align="right">问题数量</td>
                    <td align="left">
                        <input @if(!$fields['supplier_quality.quantity']) readonly="readonly" @endif class="form-control input-sm" type="text" id="quantity" name="supplier_quality[quantity]" value="{{old('quantity', $row->quantity)}}">
                    </td>
                <tr>

                <tr>
                    <td align="right">问题处理</td>
                    <td align="left">
                        <textarea @if(!$fields['supplier_quality.handle']) readonly="readonly" @endif class="form-control" name="supplier_quality[handle]" id="handle">{{old('handle', $row->handle)}}</textarea>
                    </td>
                </tr>

                <tr>
                    <td align="right">问题描述</td>
                    <td align="left">
                        <textarea @if(!$fields['supplier_quality.description']) readonly="readonly" @endif class="form-control" name="supplier_quality[description]" id="description">{{old('description', $row->description)}}</textarea>
                    </td>
                </tr>

                </tr>
                    <td align="right">罚款金额</td>
                    <td align="left">
                        <input @if(!$fields['supplier_quality.money']) readonly="readonly" @endif type="text" class="form-control input-sm" name="supplier_quality[money]" id="money" value="{{old('money', $row->money)}}">
                    </td>
                </tr>

                <tr>
                    <td align="left" colspan="2">
                    
                        <input type="hidden" name="supplier_quality[id]" value="{{$row->id}}">

                        <input type="hidden" name="step_filter[master]" value="Aike\Web\Supplier\Quality::stepFilter">

                        <button type="button" onclick="history.back();" class="btn btn-default">返回</button>

                        @if($step['edit'])
                        <a class="btn btn-{{$step['color']}}" href="javascript:;" onclick="app.turn('{{$step['key']}}');">
                            <i class="icon icon-ok-sign"></i> @if($step['number'] == 1) 提交 @else 审核 @endif
                        </a>
                        <input type="hidden" name="step_referer" value="{{session()->get('referer_'.Request::module().'_'.Request::controller().'_index')}}">
                        @endif

                        <a class="btn btn-dark" href="javascript:;" onclick="app.draft('{{$step['key']}}');">
                            <i class="icon icon-coffee-cup"></i> 保存草稿
                        </a>

                        <a class="btn btn-default" href="javascript:;" onclick="app.turnlog('{{$step['key']}}');">
                            <i class="icon icon-tick"></i> 审核记录
                        </a>

                        <a class="btn btn-default" target="_blank" href="{{url('print',['id'=>$row->id])}}"><i class="icon icon-print"></i> 打印 </a>

                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>