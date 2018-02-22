<div class="pagination pagination-sm m-n">

    <?php 
    $total = $paginator->total();
    $query = Input::get();

    $limit = $query['limit'];

    if ($total < $limit) {
        $limit = $total;
    }

    $query['page'] = $query['page'] > 0 ? $query['page'] : 1;
    $q = json_encode($query);

    $firstItem = $paginator->firstItem();
    $lastItem  = $paginator->lastItem();

    $to = (int)$firstItem;
    if ($firstItem < $lastItem) {
        $to .= '-'.$lastItem;
    }
    $url = Request::module().'/'.Request::controller().'/'.Request::action();
    ?>

    <span class="page_limit"><span class='page_value' data-url='{{$url}}' data-q='{{$q}}' style='vertical-align:middle;'>{{$to}}</span></span><span class="page_total" style="vertical-align:middle;"> / {{$total}}</span>

    <div class="btn-group">
    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
        <span class="btn btn-sm btn-default disabled" rel="prev"><i class="fa fa-chevron-left"></i></span>
    @else
        <a class="btn btn-sm btn-default" href="{{$paginator->previousPageUrl()}}" rel="prev"><i class="fa fa-chevron-left"></i></a>
    @endif

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
        <a class="btn btn-sm btn-default" href="{{$paginator->nextPageUrl()}}" rel="next"><i class="fa fa-chevron-right"></i></a>
    @else
        <span class="btn btn-sm btn-default disabled" rel="next"><i class="fa fa-chevron-right"></i></span>
    @endif
    </div>

</div>
