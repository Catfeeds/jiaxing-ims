<div class="row">

@if(count($categorys))
@foreach($categorys as $key => $category)

<div class="col-sm-6">

    @if(count($category))
    @foreach($category as $k => $cat)

        <div class="panel">
            <div class="panel-heading text-base b-b b-light">
                <i class="fa fa-file-text-o"></i>
                {{$cat['title']}}
            </div>

            <table class="table table-hover m-b-none">
                @if(count($rows[$cat['id']]))
                @foreach($rows[$cat['id']] as $row)
                <tr>
                    <td><a href="{{url('query', ['id' => $row['id']])}}">{{$row['title']}}</a></td>
                </tr>
                @endforeach
                @endif
            </table>
        </div>
    @endforeach
    @endif
</div>

@endforeach
@endif
</div>
