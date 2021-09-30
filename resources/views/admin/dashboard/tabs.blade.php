
<div class="col-xs-12 col-sm-4 col-md-3">
    <div class="text-left dashboard-filter ">
        <div id="welcome">
            @include('admin.dashboard.filters', ['user' => $user])
        </div>
    </div>
</div>
@php $class = 'col-xs-12 col-sm-8 col-md-9'; @endphp
@if($type == "")
@php $class = 'col-xs-12 col-sm-6 col-md-7'; @endphp
<div class="col-xs-12 col-sm-2 col-md-2">
<!-- <div class="text-left dashboard-menu">

        <ul>
            <li>
                <a href="javascript:void(0)" style="color:white;" class="open-color-pallate">Theme</a>
            </li>
        </ul>
</div> -->
</div>
@endif
<div class="{{$class}}">
    <div class="text-right dashboard-menu">
        <ul>
            @if($user->access_level == 'tpv' || $user->access_level == 'client' )
                <li>
                
                {{--@if($colors == '') 
                   @php $colors = json_encode(colorArray()); @endphp
                @else   
                     @php $colors = $colors; @endphp
                @endif
                @if($calenderColors == '') 
                   @php $calenderColors = json_encode(calenderColorArray()); @endphp
                @else   
                     @php $calenderColors = $calenderColors; @endphp
                @endif --}}
                    <a href="{{ route('dashboard', ['cid' => base64_encode($cId)]) }}" class="{{ ($type == '') ? 'tab-active' : "" }}"><strong>General</strong></a>
                </li>
            <li>
                <a href="{{ route('dashboard', ['type' => base64_encode('salescenter'), 'cid' => base64_encode($cId)]) }}" class="{{ ($type == 'salescenter') ? 'tab-active' : "" }}"><strong>By Sales Centers</strong></a>
            </li>
            @endif
        </ul>
    </div>
</div>