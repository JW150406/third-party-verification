<div class="submenu">
    <nav role="navigation" class="dashboard-submenu">
        <ul id="menu_ul">
            <li data-id="default">
                <a @if ($sId != "") href="<?php echo route('mobile-dashboard', ['type' => base64_encode($type), 'cid' => base64_encode($cId)]); ?>" @else href="javascript:void(0)" class="selected"  @endif data-id="default"><strong>Overall</strong></a>
            </li>
            @forelse($salesCenters as $salesCenter)
            <li data-id="{{$salesCenter->id}}">
            @php
            $activeClass = "";
            @endphp
            @if ($sId == $salesCenter->id)
                @php
                $activeClass = "selected";
                @endphp
            @endif
                <a @if($sId != $salesCenter->id) href="<?php echo route('mobile-dashboard', ['type' => base64_encode($type), 'sid' => base64_encode(array_get($salesCenter, 'id')), 'cid' => base64_encode(array_get($salesCenter, 'client_id'))]); ?>" @else href="javascript:void(0);" @endif data-id="{{$salesCenter->id}}" class="{{ $activeClass }}"><strong>{{$salesCenter->name}}</strong></a>
{{--                <a @if($sId != $salesCenter->id) href="{{ route('mobile-dashboard') }}?type={{str_replace("=", "%3D", base64_encode($type))}}&sid={{str_replace("=", "%3D", array_get($salesCenter, 'id'))}}&cid={{str_replace("=", "%3D", array_get($salesCenter, 'client_id'))}}" @else href="javascript:void(0);" @endif data-id="{{$salesCenter->id}}" class="{{ $activeClass }}"><strong>{{$salesCenter->name}}</strong></a>--}}
            </li>
            @empty
            @endforelse
            <li class="more hidee" data-width="80">
                <a class="selected"></a>
{{--                <a @if ($sId != "") href="{{ route('mobile-dashboard') }}?type={{str_replace("=", "%3D", base64_encode($type))}}&cid={{ str_replace("=", "%3D", $cId)}}" @else href="javascript:void(0)" @endif @if ($sId == "") class="selected" @endif><strong>Overall</strong></a>--}}
                <ul class="overflow">
                </ul>
            </li>
        </ul>
    </nav>
</div>
