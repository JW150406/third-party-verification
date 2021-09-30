@if(!empty($listUtility))
    @foreach($listUtility as $utility)
        <tr class="list-users">            
            <td class="grey_c">{{ $utility->commodity }}</td>
            <td class="grey_c">{{ $utility->brand_name }}</td>
            <td class="dark_c">{{ $utility->fullname }}</td>
            <td class="grey_c">{{ $utility->market }}</td>
            
            <td class="grey_c modalbtns">
                <!-- <button  class="btn delete-utility-mapping" title="Select Mapping" role="button" data-id="{{ $utility->id }}">Map</button> -->
                <input class="styled-checkbox activate-utility-mapping" id="status_active_{{ $utility->utid }}" onclick="uticheckr(this)"  type="checkbox" name="map" value="active" data-parsley-multiple="map" title="Activate Mapping" {{ ($utility->action=="checked")?'checked':'' }}  data-id="{{ $utility->utid }}" data-action="{{ $utility->action }}" data-utility-id = {{$utility->utility_id}} @if(!auth()->user()->hasPermissionTo('edit-utility')) disabled @endif>
                <label for="status_active_{{ $utility->utid }}" class=""><!-- Activate Mapping --></label>
            </td>
            
        </tr>
    @endforeach
@else
    <b style="text-align: center;">
        No Utility Found to Map
    </b>
@endif