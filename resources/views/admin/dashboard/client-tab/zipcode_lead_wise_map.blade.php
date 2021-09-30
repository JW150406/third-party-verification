<div class="dashboard-box">
    <div class="map_alt_text1 text-center" style ="vertical-align:middle;padding:25% 0 ;"><h4> Loading... </h4></div>
        <div class="map1"></div>
</div>
<div id="map_legend_div" style="display:none;">
    <div class="text-center">
        <div id = "legend_container">
            <div id="map_legend">
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
     function loadMapBasedOnZipcode(data)
    {
        $.ajax({
            url: "{{route('admin.dashboard.loadMap1')}}",
            type: "GET",
            data: data,
            success: function(res)
            {
                $('.map_alt_text1').css('display','none');
                $('.map1').html(res.html);

                var legend = document.createElement('div');
                var div = document.createElement('div');

                div.innerHTML = '<p class = "legend_text"><img src="{{asset("images/marker_image/pins/pin-verified.png")}}" height = "10" width = "10">  {{config("constants.DASHBOARD_LEAD_CATEGORIES.good_sale")}}</p>';
                div.innerHTML += '<p class = "legend_text"><img src="{{asset("images/marker_image/pins/pin-pending.png")}}" height = "10" width = "10">  {{config("constants.DASHBOARD_LEAD_CATEGORIES.pending_leads")}}</p>';
                div.innerHTML += '<p class = "legend_text"><img src="{{asset("images/marker_image/pins/pin-declined.png")}}" height = "10" width = "10">  {{config("constants.DASHBOARD_LEAD_CATEGORIES.bad_sale")}}</p>';
                div.innerHTML += '<p class = "legend_text"><img src="{{asset("images/marker_image/pins/pin-cancelled.png")}}" height = "10" width = "10">  {{config("constants.DASHBOARD_LEAD_CATEGORIES.cancelled_leads")}}</p>';
                legend.appendChild(div);

                $('#legend_container').html(legend);
                $('#legend_container').children().attr('id','map_legend');
                var scriptData = res.js.split('<script type="text/javascript">');
                var newdata =  scriptData[1].trim();
                var appendData = '<script type="text/javascript" id="google_script_zipcode">'+newdata;
                
                var replaceData = String('function updateBounds() {var bounds = new google.maps.LatLngBounds();  bounds.extend(new google.maps.LatLng(49.38, -66.94));bounds.extend(new google.maps.LatLng(25.82, -124.39));map.fitBounds(bounds);\n map.controls[google.maps.ControlPosition.LEFT_BOTTOM].push(document.getElementById("map_legend"));var imagePath  = "{{asset("images/marker_image/m1.png")}}"; var clusterOption = {styles:[{url: imagePath,width: 55,height: 55,textColor: "#000000"}]};var markerCluster = new MarkerClusterer(map, markers_map,clusterOption);markerCluster.setCalculator(function(markers_map, clusterOption) { var count=0;var index = 0;for(i=0;i<markers_map.length;i++){if(markers_map[i].label > 1){count += parseInt(markers_map[i].label);}else{count++;}};var markerslen = markers_map.length; var dv = markerslen;while (dv !== 0) {dv = parseInt(dv / 10, 10);index++;}index = Math.min(index, clusterOption);return {text: count,index: index};});}//]]>');
                var appendData = appendData.replace('//]]>',replaceData);
                $('#google_script_zipcode').remove();
                
                $('body').append(appendData);
                initialize_map();
                updateBounds();
                // google.maps.event.addListener(markerCluster, "clusterclick", function(cluster) { var clickedMakrers = cluster.getMarkers();var content = "";for(i = 0 ;i<clickedMakrers.length;i++){content += clickedMakrers[i]["title"] +"<br/>";}iw_map = new google.maps.InfoWindow();iw_map.setPosition(cluster.getCenter());iw_map.setContent(content);iw_map.open(map,this);});google.maps.event.addListener(map, "click", function() {iw_map.close();});
            }
        });
    }
</script>
@endpush