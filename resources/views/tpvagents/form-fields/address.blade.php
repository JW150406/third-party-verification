<div class="form-group" rel="label">
    <label class="control-label">{{ ucfirst(array_get($field, 'label')) }}</label>
    <div class="row">
        <div class="col-sm-12 address-field">
            <input  type="text" 
                autocomplete="off"
                id="address_1"
                class="form-control autocompletestreet address"
                Placeholder="Address line 1"
                name="fields_{{$teleSalesDataId['address_1'] ?? ''}}" 
                value="{{$teleSalesData['address_1'] ?? ''}}" 
                data-parsley-required='true'
            >
        </div>
        <div class="col-sm-12 address-field">
            <input autocomplete="new-password"
                type="text"
                id="address_2"
                class="form-control autocompletestreet address"
                Placeholder="Apartment, suite, unit, building, floor, etc. (optional)"
                name="fields_{{$teleSalesDataId['address_2'] ?? ''}}" 
                value="{{$teleSalesData['address_2'] ?? ''}}" 
            >
        </div>
        <div class="col-sm-3 inline-block">
            <input autocomplete="new-password" 
                type="text"
                id="city"
                class="form-control"
                placeholder="City"
                name="fields_{{$teleSalesDataId['city'] ?? ''}}" 
                value="{{$teleSalesData['city'] ?? ''}}" 
            >
        </div>
        <div class="col-sm-3 address-field">
            <input autocomplete="new-password"
                type="text" 
                id="county" 
                class="form-control autocompletestreet address"
                Placeholder="County"
                name="fields_{{$teleSalesDataId['county'] ?? ''}}" 
                value="{{$teleSalesData['county'] ?? ''}}" 
            >
        </div>
        <div class="col-sm-3 inline-block">
            <input 
                class="form-control"
                type="text"
                id="state"
                Placeholder="State"
                autocomplete="new-password"
                name="fields_{{$teleSalesDataId['state'] ?? ''}}" 
                value="{{$teleSalesData['state'] ?? ''}}" 
            >
        </div>
        <div class="col-sm-3 inline-block">
            <input class="form-control zipcode-field"
                type="text"
                id="zipcode"
                name="fields_{{$teleSalesDataId['zipcode'] ?? ''}}" 
                value="{{$teleSalesData['zipcode'] ?? ''}}" 
                autocomplete="new-password"
                Placeholder="Zipcode"
                data-parsley-trigger="focusout"
                data-parsley-pattern="[0-9]{5}"
                data-parsley-pattern-message="Please enter 5 digit zipcode" 
            >
        </div>
        <div class="col-sm-3 inline-block">
            <input type="text" 
                id="country" 
                class="form-control hidee"
                Placeholder="Country"
                autocomplete="new-password"
                name="fields_{{$teleSalesDataId['country'] ?? ''}}" 
                value="{{$teleSalesData['country'] ?? ''}}" 
            >
        </div>
        <input type="hidden"
            id="lat" 
            name="fields_{{$teleSalesDataId['lat'] ?? ''}}" 
            value="{{$teleSalesData['lat'] ?? ''}}" >
        <input type="hidden" 
            id="lng" 
            name="fields_{{$teleSalesDataId['lng'] ?? ''}}" 
            value="{{$teleSalesData['lng'] ?? ''}}" >
        <div class="col-sm-12">
            <span class="zipcode-error" style="color:red;"></span>
        </div>
    </div>
</div>
<script>
    var AddAutocomplete = new google.maps.places.Autocomplete(document.getElementById('address_1'), {
        types: [],
        componentRestrictions: {country: "us"}
    });
    google.maps.event.addListener(AddAutocomplete, 'place_changed', function () {
        var place = AddAutocomplete.getPlace();
        $('#address_1').val(place.name);
        $('#lat').val(place.geometry.location.lat());
        $('#lng').val(place.geometry.location.lng());
        for (var i = 0; i < place.address_components.length; i++) {
            var addressType = place.address_components[i].types[0];

            if (addressType === "neighborhood" || addressType === "sublocality_level_1") {
                let address2 = place.address_components[i].long_name;
                $('#address_1').val(address2);
            }
            if (addressType === 'postal_code') {
                $('#zipcode').val(place.address_components[i].long_name);
            }
            if (addressType === "locality" || addressType === "administrative_area_level_2") {
                $('#city').val(place.address_components[i].long_name);
            }
            // code for service_county
            if (addressType === "administrative_area_level_2") {
                $('#county').val(place.address_components[i].long_name);
            }
            if (addressType === "administrative_area_level_1") {
                $('#state').val(place.address_components[i].long_name);
            }
            if (addressType === "country") {
                $('#country').val(place.address_components[i].long_name);
            }
        }
    });
</script>