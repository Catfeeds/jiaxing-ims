<div class="panel-heading tabs-box">
    <ul class="nav nav-tabs">
        <?php
        $tabs = Aike\Web\Index\Menu::getTabs($tabKey);
        ?>
        @foreach($tabs as $tab)
        @if($tab['selected'] == 1)
        <li class="@if(Request::path() == $tab['url']) active @endif">
            <a class="text-sm" href="{{url($tab['url'])}}">{{$tab['name']}}</a>
        </li>
        @endif
        @endforeach
    </ul>
</div>