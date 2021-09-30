
<div class="dashboard-box location-box" style="height:68px;">
  <div style="padding:0 10px;">
      <h4 class="dash-hd-title-small" style="padding-left:0px;padding-bottom:4px;">Brands</h4>
    <select id="dash-brand-filter" class="select2 auto-submit" style="border-bottom: 1px solid #d6d6d6;padding:0 10px;">
      <option value="">All</option>
        @forelse($brands as $brand)
              <option value="{{ $brand->id }}">{{ $brand->name }}</option>
        @empty
        @endforelse
      </select>
  </div>

</div>

@push('scripts')
    <script>
        // Reason for disabling select2 search: https://stackoverflow.com/a/26808134/1820644
        $("#dash-brand-filter").select2({
            minimumResultsForSearch: -1
        });
    </script>
@endpush
