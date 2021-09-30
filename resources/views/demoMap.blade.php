<html>
    <head>
    <title>Laravel Google Maps Example</title>
    {!! $map['js'] !!}

    </head>
<body>
    <div class="container">
            <!-- <div id="map_canvas" style="width:100%; height:400px;"></div> -->
            {!! $map['html'] !!}
    </div>
</body>
</html>
  
<!-- <script type="text/javascript">
    var markers = [
        {
            "title": 'India',
            "lat": '20.5937',
            "lng": '78.9629',
            "description": 'Aksa Beach is a popular beach and a vacation spot in Aksa village at Malad, Mumbai.'
        },
        {
            "title": 'Juhu Beach',
            "lat": '19.0883595',
            "lng": '72.82652380000002',
            "description": 'Juhu Beach is one of favourite tourist attractions situated in Mumbai.'
        },
        {
            "title": 'Girgaum Beach',
            "lat": '18.9542149',
            "lng": '72.81203529999993',
            "description": 'Girgaum Beach commonly known as just Chaupati is one of the most famous public beaches in Mumbai.'
        },
        
    ];
    window.onload = function () {
        LoadMap();
    //    var geocoder = new google.maps.Geocoder();
    //    address='380001';
    //         geocoder.geocode({ 'address': address }, function (results, status) {
                
    //             if (status == google.maps.GeocoderStatus.OK) {
    //                 var latitude = results[0].geometry.location.lat();
    //                 var longitude = results[0].geometry.location.lng();
    //                 alert(latitude);
    //                 $('#map_latitude').val(latitude);
    //                 $('#map_longitude').val(longitude);
    //             } else {
    //                 alert("Request failed.")
    //             }
    //         });
    }

    function LoadMap() {
        var mapOptions = {
            center: new google.maps.LatLng(markers[0].lat, markers[0].lng),
            zoom: 10,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        var map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);

        //Create and open InfoWindow.
        var infoWindow = new google.maps.InfoWindow({
            // pixelOffset: new google.maps.Size(00,100)
        });

        for (var i = 0; i < markers.length; i++) {
            var data = markers[i];
            var myLatlng = new google.maps.LatLng(data.lat, data.lng);
            var marker = new google.maps.Marker({
                position: myLatlng,
                map: map,
                title: data.title
            });
            //Attach click event to the marker.
            (function (marker, data) {
                google.maps.event.addListener(marker, "click", function (e) {
                //Wrap the content inside an HTML DIV in order to set height and width of InfoWindow.
                    infoWindow.setContent("<div style = 'width:200px;min-height:40px'>" + data.description + "</div>");
                    infoWindow.open(map, marker);
                });
            })(marker, data);
        }
    }
</script> -->

<script type="text/javascript">
        $(function () {
            $('#state').on("change", function () {
                var street = $('#address').val();
                var city = $('#city').val();
                var postal = $('#zipcode').val();
                var state = $(this).find("option:selected").text();
                if (street != '' && city != '' && state != '' && postal != '' && country != '') {
                    var address =   street + ',' + city + ',' + state + ',' + postal;
                    var geocoder = new google.maps.Geocoder();
                    geocoder.geocode({ 'address': address }, function (results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            var latitude = results[0].geometry.location.lat();
                            var longitude = results[0].geometry.location.lng();
                            $('#map_latitude').val(latitude);
                            $('#map_longitude').val(longitude);
                        } else {
                            alert("Request failed.")
                        }
                    });
                }
            });
        });
    </script>
