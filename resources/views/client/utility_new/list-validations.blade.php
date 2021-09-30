
@if(!empty($listUtilityValidations))
    @foreach($listUtilityValidations as $validation)
        <tr class="list-users">            
            <td class="dark_c">{{ $validation->label }}</td>
            <td class="grey_c">{{ $validation->regex }}</td>
            <td class="grey_c">{{ $validation->regex_message }}</td>
            @if(auth()->user()->hasPermissionTo('delete-utility'))
            <td class="grey_c">
                <button  class="btn delete-utility-validation" title="Delete Utility Validation" role="button" data-id="{{ $validation->id }}">{!! getimage('images/cancel.png') !!}</button>
            </td>
            @endif
        </tr>
    @endforeach
@else
    <b style="text-align: center;">
        No Validation Found
    </b>
@endif