<table class="table">
    <tr>
        <th colspan="2" align="center" class="title">质量问题单</th>
    </tr>
    <tr>
        <td width="20%" align="right">主题</td>
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

</table>
