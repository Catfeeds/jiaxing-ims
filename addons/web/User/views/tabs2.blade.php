<div class="panel-heading tabs-box">
    <ul class="nav nav-tabs">
        @foreach($tabs as $tab)
        <li class="@if(Request::controller() == $tab['key']) active @endif">
            <a class="text-sm" href="{{url($tab['url'])}}">{{$tab['name']}}</a>
        </li>
        @endforeach
    </ul>
</div>