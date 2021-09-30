<div class="dashboard-box">
    <div class="map_alt_text2 text-center" style ="padding:25% 0;"><h4> Loading...</h4></div>
    <div class="map2"></div>
</div>

@push('scripts')
<script>
function loadMapBasedOnSalesAgent(data)
    {
        $.ajax({
            url: "{{route('admin.dashboard.loadMap2')}}",
            type: "GET",
            data: data,
            success: function(res)
            {
                $('.map_alt_text2').css('display','none');
                $('.map2').html(res.html);

                var scriptData = res.js.split('<script type="text/javascript">');
                var newdata =  scriptData[1].trim();
                var appendData = '<script type="text/javascript" id="google_script">'+newdata;
                var replaceData = String('function updateBounds() {var bounds = new google.maps.LatLngBounds();  bounds.extend(new google.maps.LatLng(49.38, -66.94));bounds.extend(new google.maps.LatLng(25.82, -124.39));map.fitBounds(bounds);}\n//]]>');
                var appendData = appendData.replace('//]]>',replaceData);
                $('#google_script').remove();
                $('body').append(appendData);
                initialize_map();
                updateBounds();
            }
        });
    }
</script>
@endpush