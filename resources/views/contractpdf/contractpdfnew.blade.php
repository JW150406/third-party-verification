<!DOCTYPE html
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Contract</title>
  <style>
    @page {
      margin: 100px 25px 50px;
    }
    header {
      position: fixed;
      top: -80px;
      left: 0px;
      right: 0px;
      height: 50px;
      text-align: left;
      line-height: 35px;
  }
    body {
      font-family: "Helvetica";
      padding: 15px;
      font-size: 14px;
      border: 1px solid #000;
    }
    .logo-wrapper {
      display: block;
    }
    .logo-wrapper img {
      display: block;
      margin: 0 auto;
    }
    div {
      display: block;
    }
    .text-center {
      text-align: center;
    }
    h3 {
      margin-bottom: 0px;
    }
    table {
      width: 100%;
    }
    table p span {
      color: #727171;
    }
    .customer-sign img {
      border: 2px solid #d6d6d6;
      margin-top: 20px;
    }
    .customer-sign h5 {
      font-size: 14px;
      margin: 0;
      margin-top: 15px;
    }
    .gps-title {
      margin-top: 40px;
      margin-bottom: 20px;
    }
    .mtb10{
      margin-top: 10px;
      margin-bottom: 10px;
    }
    .map-img{
      border: 2px solid #ddd;
      padding: 2px;
    }
    .clr-gry{
      color: #727171;
    }
    .marker-label {
      height: 40px
    }
    .marker-td {
      vertical-align: top
    }
  </style>
</head>

<body>
  <header>
    <img src="{{$customer_info['client_logo']}}" alt="" style="max-height:60px" />
  </header>

  <h2 class="text-center"> {{$customer_info['client_name']}} Service Contract</h2>

  <p>This is the agreement between {{$customer_info['client_name']}} and {{$customer_info['firstname']}} {{$customer_info['middlename']}} {{$customer_info['lastname']}}.</p>
  <!-- <h1>Test PDF</h1> -->
  <h3>Enrollment Detail:</h3>

  <table>
    <tr>
      <td align="left">
        <p>
          Phone: <span class="underline-bottom">{{$customer_info['Phone']}}</span>
        </p>
        <p>
          Email: <span class="underline-bottom">{{$customer_info['email']}}</span>
        </p>
        <p>
          Service Address: <span class="underline-bottom">{{ implode(", ", array_filter(array($program_info['ServiceAddress1'],$program_info['ServiceAddress2'],$program_info['ServiceCity'],$program_info['ServiceCounty'],$program_info['ServiceState'],$program_info['ServiceZip']))) }}</span>
        </p>
        <p>
          Billing Name: <span class="underline-bottom">{{implode(" ",array_filter(array($program_info['BillingFirstName'],$program_info['BillingMiddleName'],$program_info['BillingLastName'])))}}</span>
        </p>
        <p>
          Billing Address: <span class="underline-bottom">{{ implode(", ",array_filter(array($program_info['BillingAddress'],$program_info['BillingAddress2'],$program_info['BillingCity'],$program_info['BillingCounty'],$program_info['BillingState'],$program_info['BillingZip'] )))}} </span>
        </p>
        <p>
          Account Number: <span class="underline-bottom">
            @if(!empty($program_info['AccountNumber']))
              {{ str_replace('()','',$program_info['AccountNumber']) }} 
            @endif
            </span>
        </p>
      </td>

    </tr>
    <tr>
      <td align="left">
        <p>
          Utility: <span class="underline-bottom">{{$program_info['Utility']}}</span>
        </p>
        <p>
          Program Code: <span class="underline-bottom">{{$program_info['ProgramCode']}}</span>
        </p>
        <p>
          Program Name: <span class="underline-bottom">{{$program_info['ProductName']}}</span>
        </p>
        <p>
          Rate: <span class="underline-bottom">{{$program_info['Rate']}}</span>
        </p>
        <p>
          MSF: <span class="underline-bottom">{{$program_info['Msf']}}</span>
        </p>
        <p>
          ETF : <span class="underline-bottom">{{$program_info['Etf']}}</span>
        </p>
        <p>
          Term : <span class="underline-bottom">{{$program_info['Term']}} months</span>
        </p>
        @foreach($custom_fields as $key => $fields)
            <p>
                {{$fields}} : <span class="underline-bottom">{{ $program_info[$key] ?? '' }}</span>
            </p>
        @endforeach
      </td>

    </tr>
  </table>

  <h3>Terms and Conditions:</h3>

  <p>These Terms and Conditions apply to customers offered the XYZ Energy Price Promise. If there is any conflict between these Terms and Conditions and the XYZ Energy Residential Agreement, these Terms and Conditions shall prevail. Any capitalised terms set out in these terms which are not defined in these Terms and Conditions have the same meaning as set out in the XYZ Energy Residential Agreement.</p>

 <h4>Availability.</h4>

  <p>The XYZ Energy Price Promise is only available for residential customers who have been offered the XYZ Energy Price Promise. The XYZ Energy Price Promise is only available in a selected number of regions and for a limited period of time. The XYZ Energy Price Promise offer may be extended to other regions and for additional time at XYZ Energy's sole discretion.</p>

  <p>The Price Promise applies only to fixed and variable electricity Energy rates paid on a monthly basis based on billing days or metered Energy. The Price Promise does not apply to Delivery charges or any LPG or Natural Gas related charges. Any other credits or fees you may receive or incur from time to time in accordance with the XYZ Energy Residential Agreement are not subject to the Price Promise.</p>

  <p>Price Protection applies to your Energy Rate for two years from the date you originally switched to this product and does not apply to charges under Delivery on your Price Plan which includes Network Services, Retailer Services and Metering. Beyond the Price Protection period we will give you notice of any change in accordance with the XYZ Energy Residential Agreement.</p>

  <p>If you accept the supply of Energy from Us pursuant to Our Price Promise then this Agreement is conditional on XYZ Energy being satisfied in all respects with the rates You are currently paying You electricity provider and any PPD You receive from them, as well as whether or not We are using the correct configuration for Your account. XYZ Energy reserves the right to request a copy of your most recent electricity bill from your current electricity provider and to contact your local Lines Company to satisfy itself as to these matters.</p>

  <p>The Price Promise cannot be taken to another network, but can be taken between properties on the same network if the price plan is still available.</p>

  <p>XYZ Energy seeks to pass through Network Service Charges. XYZ Energy seeks to recover the total charge it faces across all relevant consumers. An individual consumer’s charges may differ between the levelled charge and the underlying Network Charge due to changes in the timing of consumption.</p>

    @if($customer_info['signature'] != '')

    <div class="customer-sign">
      <img src="{{$customer_info['signature']}}" height=80PX">
      <h5>Customer Signature</h5>
    </div>
    @endif


{{--   <table>--}}
{{--    <tr>--}}
{{--      <td style="width: 200px; vertical-align: top;">--}}
{{--        <img src="gps_image_0000000455.png" class="map-img" width="100%" height="200px">--}}
{{--      </td>--}}
{{--      <td style="padding-left: 40px; vertical-align: top;">--}}
{{--        <h5 class="mtb10">Sales Agent Coordinates</h5>--}}
{{--        <h5 class="mtb10">Latitude: 10000055252<br>--}}
{{--        Longitude: 3254816458</h5>--}}
{{--        <h5 class="mtb10"> Estimated address:</h5>--}}
{{--        <h5 class="mtb10">123-125 Maple Street--}}
{{--          Rochester, NY--}}
{{--          10001</h5>--}}
{{--      </td>--}}
{{--    </tr>--}}
{{--  </table>--}}

  @if($gps_image !='')
    <h3 class="gps-title">GPS Information</h3>
        <?php $imagepath = Storage::disk('s3')->url($gps_image);?>

    <div class="gps-outer" style="display: inline-block; margin-top:50px;">
    <table>
        <tr>
            <td>
      <div class="gps-block" style="width: 250px; display: inline-block;margin-right: 10px;">
        <img src="{{$imagepath}}" class="map-img" height="200" alt="Google Location Image">
        <!-- <img src="" alt="Google Location Image" height="250"> -->
      </div>
  </td><td class="marker-td">
      <div style="width: 200px; display: inline-block; padding-right: 10px;">
        <table>
        <tr>
            <td class="marker-td" style="width: 30px">
        <img src="{{asset('icon/red-dot.png')}}" class="marker-label">
        </td><td>
        <h5 class="mtb10 topSpace"> Estimated address:</h5>
        <h5 class="mtb10 clr-gry">{{ $estimated_address }}</h5>
        <h5 class="mtb10"> Sales Agent Coordinates</h5>
        <h5 class="mtb10 clr-gry">Latitude: {{$customer_info['Lat']}}<br>
          Longitude: {{$customer_info['Lng']}}</h5>
          </td>
        </tr>
    </table>
      </div>
  </td><td class="marker-td">
      <div style="width: 200px; display: inline-block; padding-right:10px;">
        <table>
        <tr>
            <td class="marker-td" style="width: 30px">
          <img src="{{asset('icon/blue-dot.png')}}" class="marker-label">
          </td><td><h5 class="mtb10 topSpace"> Service Address</h5>
        <h5 class="mtb10 clr-gry">{{ $program_info['ServiceAddress1']}}, {{$program_info['ServiceAddress2']}}<br>
          {{$program_info['ServiceCity']}}, {{$program_info['ServiceCounty']}},{{$program_info['ServiceState']}} <br>{{$program_info['ServiceZip'] }}</h5>
          </td>
        </tr>
    </table>
      </div>
      </td>
        </tr>
    </table>
    </div>
@endif

</body>

</html>