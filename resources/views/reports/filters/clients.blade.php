<div class="btn-group pull-right btn-sales-all margin-bottom-for-filters">
    <select class="select2 btn btn-green dropdown-toggle mr15 " id="client" name="client" data-parsley-required='true'  data-parsley-errors-container="#select2-filterclient-error-message" data-parsley-required-message="Please select client" @if(Auth::user()->isAccessLevelToClient()) disabled @endif>
        
        @if(Auth::user()->isAccessLevelToClient())
        <option value="{{$clients[0]->id}}" selected>{{$clients[0]->name}}</option>
        @else
        <option value="" selected>All Clients</option>
        @foreach($clients as $client)
        <option value="{{$client->id}}">{{$client->name}}</option>
        @endforeach
        @endif
    </select>
    <span id="select2-filterclient-error-message"></span>
</div>