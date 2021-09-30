<div class="submenu">
    <nav role="navigation" class="dashboard-submenu">
        <ul id="menu_ul">
       {{-- @if($colors == '') 
            @php $colors = json_encode(colorArray()); @endphp
        @else   
                @php $colors = $colors; @endphp
        @endif
        @if($calenderColors == '') 
            @php $calenderColors = json_encode(calenderColorArray()); @endphp
        @else   
                @php $calenderColors = $calenderColors; @endphp
        @endif--}}
            @if($user->access_level != 'salescenter')
            <li>
                <a href="{{ route('dashboard', ['type' => base64_encode($type), 'cid' => base64_encode($cId)]) }}" @if ($sId == "") class="selected" @endif><strong>Overall</strong></a>
            </li>
            @endif
            @forelse($salesCenters as $salesCenter)
            <li>
            @php
            $activeClass = "";
            @endphp
            @if ($sId == $salesCenter->id)
                @php
                $activeClass = "selected";
                @endphp
            @endif
                <a href="{{ route('dashboard', ['type' => base64_encode($type), 'sid' => base64_encode(array_get($salesCenter, 'id')), 'cid' => base64_encode(array_get($salesCenter, 'client_id'))]) }}" data-id="{{$salesCenter->id}}" class="{{ $activeClass }}"><strong>{{$salesCenter->name}}</strong></a>
            </li>
            @empty
            @endforelse
            <li class="more hidee" data-width="80">
                <a href="javascript:void(0)" class="selected"><strong>More</strong></a>
                <ul class="overflow">
                </ul>
            </li>
        </ul>
    </nav>
</div>
