<div class="wrapper-sm">

    <table class="table table-bordered m-b-none">
        <tr>
            @foreach($rows['head'] as $v)
                <th colspan="2">{{$v}}</th>
            @endforeach
        </tr>

        @foreach($rows['body'] as $i => $body)
        <tr>
            @foreach($body as $j => $v)
            @if(isset($params[$j]['list'][$i]))
                <td class="text-right">{{$params[$j]['list'][$i][1]}}</td>
                <td>{{$params[$j]['list'][$i][0]}}</td>
            @endif
            @endforeach
            </tr>
        @endforeach
    </table>
</div>