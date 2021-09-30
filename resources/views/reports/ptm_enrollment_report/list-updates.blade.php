@if(!empty($listenrollments) && sizeof($listenrollments))
    @foreach($listenrollments as $enrollment)
        <tr class="list-users">            
            <td class="grey_c">{{$enrollment->update_by}}</td>         
            <td class="grey_c">{{$enrollment->updated_at}}</td>         
            <td class="grey_c">{{$enrollment->assigned_kw}}</td> 
            <td class="grey_c">{{$enrollment->assigned_date}}</td> 
            <td class="grey_c">{{$enrollment->updated_from_status}}</td> 
            <td class="grey_c">{{$enrollment->updated_to_status}}</td> 
        </tr>
    @endforeach
@else
    <b style="text-align: center;">
        No status update found for the concerned Lead.
    </b>
@endif