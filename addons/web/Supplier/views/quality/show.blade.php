<div class="panel">
    <form class="form-horizontal" method="post" action="{{url()}}" id="myform" name="myform">
        <div class="table-responsive">
            <table class="table table-form m-b-none">
                <tr>
                    <td width="10%" align="right">主题</td>
                    <td align="left">
                        {{$row->name}}
                    </td>
                </tr>
                <tr>
                    <td align="right">问题类别</td>
                    <td align="left">
                        {{option('supplier.quality.category', $row->category_id)}}
                    </td>
                </tr>

                <tr>
                    <td align="right">问题来源</td>
                    <td align="left">
                        {{option('supplier.quality.source', $row->source_id)}}
                    </td>
                </tr>

                <tr>
                    <td align="right">问题商品</td>
                    <td align="left">
                        {{$row->product->name}}
                    </td>
                </tr>
                <tr>
                    <td align="right">问题数量</td>
                    <td align="left">
                        {{$row->quantity}}
                    </td>
                </tr>

                <tr>
                    <td align="right">问题描述</td>
                    <td align="left">
                        {{$row->description}}
                    </td>
                </tr>

                <tr>
                    <td align="right">问题处理</td>
                    <td align="left">
                        {{$row->handle}}
                    </td>
                </tr>

                <tr>
                    <td align="right">罚款金额</td>
                    <td align="left">
                        {{$row->money}}
                    </td>
                </tr>

                <tr>
                    <td align="left" colspan="2">

                        <button type="button" onclick="history.back();" class="btn btn-default">返回</button>

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
