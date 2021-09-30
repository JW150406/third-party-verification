@if (!$user->hasRole(config('constants.ROLE_GLOBAL_ADMIN')))
@push('styles')
<style>
    .client-name-sec .select2-selection__arrow {
        display: none !important;
    }
</style>
@endpush
@if($identifier == 'mobile')
@push('styles')
<style>
.client-name-sec .select2-selection__arrow {
        display: none !important;
    }
</style>
@endpush
@endif
@push('scripts')
<script>
    // $('#dashboard-client-filter').prop('disabled', true);
    $('#dashboard-client-filter').data('select2').destroy();
    $('#dashboard-client-filter').attr('readonly', true);
</script>
@endpush
@endif

@if (!$user->hasRole(config('constants.ROLE_GLOBAL_ADMIN')))
    @php  $class = "disable-client-drop"; @endphp
@else
    @php $class = ''; @endphp
@endif

<form role="form" id="deshbordNewForm">
      @csrf
      <div class="form-group dash-filter client-name-sec {{$class}}">
      <select class="select2 form-control auto-submit" name="clientId" id="dashboard-client-filter">
              @forelse($clients as $client)
                  <option value="{{ $client->id }}" {{ $client->id == $cId ? 'selected' : '' }}>{{ $client->name }}</option>
              @empty
                  <option value="" selected>Select Client</option>
              @endforelse
          </select>
      </div>
      <input type="hidden" name="startDate" value = "" class="hidden-start-date">
      <input type="hidden" name="endDate" value = "" class="hidden-end-date">
      <input type="hidden" name="brand" value = "" class="hidden-brand">
      <input type="hidden" name="salesCenterId" value = "" class="hidden-salescenter-id">
      <input type="hidden" name="agentId" value = "" class="hidden-agent-id">
      <input type="hidden" name="salesAgentType" value = "d2d" class="hidden-agent-type">
      <input type="hidden" name="salesAgentLeadType" value = "Good Sale" class="hidden-agent-lead-type">
      <input type="hidden" name="salesLocationId" value = "" class="hidden-sales-location-id">
      <input type="hidden" name="salesCenter" value = "" class="hidden-salescenter">
      <input type="hidden" name="monthFilter" value = "" class="hidden-month-filter-value ">
      <input type="hidden" name="yearFilter" value = "" class="hidden-year-filter-value ">
      <input type="hidden" name="lineFilters" value = "day" class="hidden-line-filter-value">
      
      



    <div class="charthiddenfield">
        <input type="hidden" id="sheet_name" name="sheet_name" value="">
        <input type="hidden" id="sheet_title" name="sheet_title" value="">
        <input type="hidden" id="agent_id" name="agent_id" value="">
        <input type="hidden" id="location_id" name="location_id" value="">
        <input type="hidden" id="sales_center_id" name="sales_center_id" value="">
        <input type="hidden" id="channelType" name="channelType" value="">
        <input type="hidden" id="status" name="status" value="">
        <input type="hidden" id="commodity_type" name="commodity_type" value="">
        <input type="hidden" id="verificationMethod" name="verificationMethod" value="">
        <input type="hidden" id="sales_type" name="sales_type" value="">
        <input type="hidden" id="calender_day" name="calender_day" value="">
        <input type="hidden" id="month" name="month" value="">
        <input type="hidden" id="year" name="year" value="">
        <input type="hidden" id="locationCommodity" name="locationCommodity" value="">
        <input type="hidden" id="program_id" name="program_id" value="">
        <input type="hidden" id="utility_name" name="utility_name" value="">
        <input type="hidden" id="state" name="state" value="">
        <!-- <input type="hidden" id="dashboard_brand" name="brand" value=""> -->
        
    </div>
</form>


@push('scripts')

<script>
     $(".client-name-sec").click(function() {
        $(".select2-dropdown").addClass("intro");
    });
</script>

<script>
    $("#dashboard-client-filter").select2({
        dropdownCssClass: "client-dropdown",
        minimumResultsForSearch: -1
    });

    $("#dashboard-client-filter").on('change', function() {
        let cId = $(this).val();
        var url = new URL(document.URL);
        var type = url.searchParams.get("type");
        var colors = encodeURIComponent(url.searchParams.get("colors"));
        var calenderColors = encodeURIComponent(url.searchParams.get("calenderColors"));
        if(url.searchParams.has("colors"))
        {
            if (type) {
                window.location.href = "{{ url('admin/dashboard') }}" + "?type=" + type + "&cid=" + btoa(cId)+ "&colors=" +colors +"&calenderColors="+calenderColors;
            } else {
                window.location.href = "{{ url('admin/dashboard') }}" + "?cid=" + btoa(cId) + "&colors=" +colors +"&calenderColors="+calenderColors;
            }
        }
        else{
            if (type) {
                window.location.href = "{{ url('admin/dashboard') }}" + "?type=" + type + "&cid=" + btoa(cId);
            } else {
                window.location.href = "{{ url('admin/dashboard') }}" + "?cid=" + btoa(cId);
            }
        }
    });
    // $('.select2-container--open').addClass('cust-select-filter')

   

    // $("body").addClass("intro");

</script>
@endpush