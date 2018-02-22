<table class="table m-b-none">
  <thead>
    <tr>
    <th align="left">商品类别</th>
    <th align="left">商品名称</th>
    <th align="right">数量</th>
    <th align="left">备注</th>
	</tr>
</thead>

   @if(count($rows)) 
   @foreach($rows as $v)
  <tr>
    <td align="left">{{$v['category_name']}}</td>
    <td align="left">{{$v['name']}} - {{$v['spec']}}</td>
    <td align="right">{{$v['quantity']}}</td>
    <td align="left">{{$v['budget_description']}}</td>
  </tr>
   @endforeach @endif
</table>
