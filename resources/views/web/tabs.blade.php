<div class="panel-heading tabs-box">
    <ul class="nav nav-tabs">
        <?php
        $tabs = Aike\Web\Index\Menu::getTabs($tabKey);
        ?>
        @foreach($tabs as $tab)
        @if($tab['selected'] == 1)
        <?php
        $url = url($tab['url'], (array)$tab['query']);
        if (is_array($tab['i']) && is_array($tab['query'])) {
            $a = $b = '';
            foreach ($tab['i'] as $v) {
                $a .= $search['query'][$v];
                $b .= $tab['query'][$v];
            }
        } else {
            $a = Request::path();
            $b = $tab['url'];
        }
        ?>
        <li class="@if($a == $b) active @endif">
            <a class="text-sm" href="{{$url}}">{{$tab['name']}}</a>
        </li>
        @endif
        @endforeach
    </ul>
</div>
