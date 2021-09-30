
<div class="dashboard-box location-box" style="height:68px;">
  <div style="padding:0 10px;">
      <h4 class="dash-hd-title-small" style="padding-left:0px; padding-bottom:4px;">Locations</h4>
    <select id="select2 form-control location-filter" class="select2 select-location auto-submit" style="border-bottom: 1px solid #d6d6d6;">
      <option value="all">All</option>
      <?php 
          if(Auth::check()) {
            $user = Auth::user();
        } else {
            $user = auth('api')->user();
        }
      ?>
        @forelse($locations as $location)
          @if($user->hasRole(['sales_center_location_admin']))
            @if($location->id == $user->location_id)
              <option value="{{ $location->id }}">{{ $location->name }}</option>
            @endif
              @else
              <option value="{{ $location->id }}">{{ $location->name }}</option>
          @endif
        @empty
        @endforelse
      </select>
  </div>

</div>

@push('scripts')
    <script>
        // Reason for disabling select2 search: https://stackoverflow.com/a/26808134/1820644
        $("#location-filter").select2({
            minimumResultsForSearch: -1
        });
    </script>
@endpush
