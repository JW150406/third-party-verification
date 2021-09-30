
<div class="col-xs-2 col-sm-2 col-md-3">
    <div class="text-left dashboard-filter ">
        <div id="welcome">
            @include('admin.dashboard.filters')
        </div>
    </div>
</div>

<div class="col-xs-10 col-sm-10 col-md-9">
    <div class="text-right dashboard-menu">
        <ul>
            <li>
                <a href="{{ route('mobile-dashboard', ['cid' => base64_encode($cId)]) }}" class="{{ ($type == '') ? 'tab-active' : "" }}"><strong>General</strong></a>
            </li>
            <li>
                <a href="{{ htmlspecialchars_decode(route('mobile-dashboard', ['type' => base64_encode('salescenter'), 'cid' => base64_encode($cId)])) }}" class="{{ ($type == 'salescenter') ? 'tab-active' : "" }}"><strong>By Sales Centers</strong></a>
            </li>
        </ul>
    </div>
</div>