<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
	<meta charset="utf-8"> <!-- utf-8 works for most cases -->
	<meta name="viewport" content="width=900" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- Use the latest (edge) version of IE rendering engine -->
    <meta name="x-apple-disable-message-reformatting">  <!-- Disable auto-scale in iOS 10 Mail entirely -->
	<title></title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap" rel="stylesheet">
	<!-- CSS Reset -->
    <style>

		/* What it does: Remove spaces around the email design added by some email clients. */
		/* Beware: It can remove the padding / margin and add a background color to the compose a reply window. */
        html,
        body {
	        /*margin: 0px auto !important;*/
            margin-left: 0px auto !important;
            margin-right: 0px auto !important;
            margin-bottom: 0px auto !important;
            padding: 0 !important;
            height: auto !important;
            width: 100% !important;
            font-family: Poppins, sans-serif;
            /*background-color: #f2f2f5;*/
        }

        /* What it does: Stops email clients resizing small text. */
        * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
            box-sizing: border-box !important;
        }

        /* What it does: Centers email on Android 4.4 */
        div[style*="margin: 16px 0"] {
            margin:0 !important;
        }

        /* What it does: Stops Outlook from adding extra spacing to tables. */
        table,
        td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
        }

        /* What it does: Fixes webkit padding issue. Fix for Yahoo mail table alignment bug. Applies table-layout to the first 2 tables then removes for anything nested deeper. */
        table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            margin: 0 !important;
            width: 100%;
        }
        table table table {
            table-layout: auto;
        }

        /* What it does: Uses a better rendering method when resizing images in IE. */
        img {
            -ms-interpolation-mode:bicubic;
        }

        /* What it does: A work-around for email clients meddling in triggered links. */
        *[x-apple-data-detectors],	/* iOS */
        .x-gmail-data-detectors, 	/* Gmail */
        .x-gmail-data-detectors *,
        .aBn {
            border-bottom: 0 !important;
            cursor: default !important;
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }
        
        /* What it does: Prevents Gmail from displaying an download button on large, non-linked images. */
        .a6S {
	        display: none !important;
	        opacity: 0.01 !important;
        }
        /* If the above doesn't work, add a .g-img class to any image in question. */
        img.g-img + div {
	        display:none !important;
	   	}

        /* What it does: Prevents underlining the button text in Windows 10 */
        .button-link {
            text-decoration: none !important;
        }

        /* What it does: Removes right gutter in Gmail iOS app: https://github.com/TedGoas/Cerberus/issues/89  */
        /* Create one of these media queries for each additional viewport size you'd like to fix */
        /* Thanks to Eric Lepetit (@ericlepetitsf) for help troubleshooting */
        @media only screen and (min-device-width: 375px) and (max-device-width: 413px) { /* iPhone 6 and 6+ */
            .email-container {
                min-width: 320px !important;
            }
        }
        @media print {
            margin-top: 2.5cm;
        }

        .marker-label {
            height: 32px;
            vertical-align: top;
            margin-bottom: -8px;
        }
    </style>
    
    <!-- Progressive Enhancements -->
    <style>

        /* What it does: Hover styles for buttons */
        .button-td,
        .button-a {
            transition: all 100ms ease-in;
        }
        .button-td:hover,
        .button-a:hover {
            opacity: 0.9;
        }
        img{max-width: 100%;}
        *{
            box-sizing: border-box;
        }
        .email-container {
            width: 800px !important;
            margin: auto !important;
            padding: 0 40px 20px;
            background: #fff;
        }
        .table-block tr td{
            padding: 3px 5px;
            border-right: 1px solid #d8e6f4;
            font-size: 11px;
        }
        .table-block tr td:last-child{
            border-right: none;
        }
        .table-block tr{
            border-bottom: 1px solid #d8e6f4;
        }
        .table-block tbody tr:nth-child(odd){
            background-color: #f2f2f5;
        }
        .table-block .table-res thead tr{
            background-color: #f2f2f5;
        }
        .table-block .table-res tbody tr:nth-child(even){
            background-color: #f2f2f5;
        }
        .table-block .table-res tbody tr:nth-child(odd){
            background-color: #fff;
        }
        .table-block tr:last-child{
            border-bottom: none;
        }
        /* Media Queries */
        @media screen and (max-width: 830px) {

            .email-container {
                width: 100% !important;
                margin: auto !important;
            }

            /* What it does: Forces elements to resize to the full width of their container. Useful for resizing images beyond their max-width. */
            .fluid {
                max-width: 100% !important;
                height: auto !important;
                margin-left: auto !important;
                margin-right: auto !important;
            }

            /* What it does: Forces table cells into full-width rows. */
            .stack-column,
            .stack-column-center {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                direction: ltr !important;
                box-sizing: border-box;
                padding: 0 15px 19px !important;
            }
            /* And center justify these ones. */
            .stack-column-center {
                text-align: center !important;
            }

            /* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */
            .center-on-narrow {
                text-align: center !important;
                display: block !important;
                margin-left: auto !important;
                margin-right: auto !important;
                float: none !important;
            }
            table.center-on-narrow {
                display: inline-block !important;
            }

            /* What it does: Adjust typography on small screens to improve readability */
			.email-container p {
				font-size: 17px !important;
				line-height: 22px !important;
			}
            .container-width{
                padding: 20px 30px !important;
            }
            .button-td {
                max-width: 100% !important;
                width: 300px !important;
            }
            td.button-td > span {
                max-width: 100% !important;
                width: 300px!important;
                height: auto !important;
                margin-bottom: -5px;
            }
            td.button-td > span img {
                max-width: 100%;
            }
            .button-td a{
                font-size: 18px !important;
                padding: 20px 65px 20px 20px !important;
            }
            img{
                max-width: 100%;
            }

        }
        @media(max-width: 576px){
            
        }
    </style>

    <!-- What it does: Makes background images in 72ppi Outlook render at correct size. --> 
    <!--[if gte mso 9]>
    <xml>
      <o:OfficeDocumentSettings>
        <o:AllowPNG/>
        <o:PixelsPerInch>96</o:PixelsPerInch>
     </o:OfficeDocumentSettings>
    </xml>
    <![endif]-->
    
</head>
    <body>
        <main>
        <?php 
            $timeZone = $timeZone;
            $format = getDateFormat().' '.getTimeFormat();
        ?>
        <div class="email-container">
            <div class="header-sec" style="padding: 15px 0;">
                <table>
                    <tbody>
                        <tr>
                            <td>
                                <div class="logo-sec" style="display: inline-block; width: 130px; vertical-align:middle;">
                                    <img src="{{asset('images/tpv_receipt_logo.png')}}">
                                </div>
                            </td>
                            <td colspan="2">
                                <div class="heading" style="display:inline-block;  vertical-align:middle;margin-left: 75px;">
                                    <h3 style="margin: 0;">TPV Receipt</h3>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="middle-wrapper">
                <table>
                    <tbody>
                        <tr>
                            <td>
                                <div class="left-table-sec" style="display: inline-block;">
                                    <div class="table-block" >
                                        <table style="border: 1px solid #4875c5;">
                                            <tbody>
                                                <tr>
                                                    <td><strong>Client</strong></td>
                                                    <td>
                                                        <span style="width: 120px; display: block;">
                                                            @if(!empty($client_logo))
                                                                <img src="{{$client_logo}}" style="max-height: 40px">
                                                            @endif
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Client Name</strong></td>
                                                    <td>
                                                        @if(!empty($telesale->client))
                                                            {{ $telesale->client->name }}
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Sale ID</strong></td>
                                                    <td>{{$telesale->refrence_id}}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Sale Date</strong></td>
                                                    <td>
                                                        @if(!empty($telesale->created_at))
                                                            {{ $telesale->created_at->setTimezone($timeZone)->format($format) }}
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>TPV Completed</strong></td>
                                                    <td>
                                                        @if(!empty($telesale->updated_at))
                                                            {{ $telesale->updated_at->setTimezone($timeZone)->format($format) }}
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Disposition</strong></td>
                                                    <td>
                                                        @if($telesale->status =='verified') 
                                                            Good Sale 
                                                        @else 
                                                            No Sale 
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Sales Rep ID</strong></td>
                                                    <td>{{ $telesale->user->userid ?? '' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Customer Name</strong></td>
                                                    <td>{{$customer['name'] ?? '' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Service Address</strong></td>
                                                    <td>{{ $customer['serviceAddress'] ?? '' }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" style="text-align: center; background-color: #1f3864; color: #fff;">
                                                       <strong>Customer TPV Smartphone Information</strong> 
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Number</strong></td>
                                                    <td>{{$customer['phoneNumber'] ?? '' }}</td>                                    
                                                </tr>                               
                                                <tr>
                                                    <td><strong>IP Address</strong></td>
                                                    <td>{{ $selfverifyInfo->ip ?? '' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Platform - OS Version</strong></td>
                                                    <td>
                                                        @if(!empty($selfverifyInfo->os))
                                                            {{ $selfverifyInfo->os .' - '. $selfverifyInfo->os_version }}
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Browser - Version</strong></td>
                                                    <td>
                                                        @if(!empty($selfverifyInfo->browser))
                                                            {{ $selfverifyInfo->browser .' - '. $selfverifyInfo->browser_version }}
                                                        @endif
                                                    </td>
                                                </tr>                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </td>
                            <td style="vertical-align: top;">
                                <div class="map-sec" style="display: inline-block; padding-left: 26px;">
                                    <div class="map-img-area" style="text-align: left;">
                                        <span style="display:block">
                                            <img src="{{$gpsImage}}">
                                        </span>
                                    </div>
                                    <div class="marker-detail">
                                        <table>
                                            <tbody>
                                                <tr>                                                    
                                                    <td style="color: #FE9200; font-weight: 500; font-size: 13px;"><img src="{{asset('icon/orange-marker.png')}}" class="marker-label"> Service address</td>
                                                </tr>
                                                <tr>                                                    
                                                    <td style="color: #0062B1; font-weight: 500; font-size: 13px; "><img src="{{asset('icon/blue-marker.png')}}" class="marker-label">Customer position during Self-TPV</td>
                                                </tr>
                                                <tr>
                                                    <td style="color: #f542b3; font-weight: 500; font-size: 13px;"><img src="{{asset('icon/pink-marker.png')}}"  class="marker-label">Agent's sale position</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <span class="img-sec" style="display: block; width: 135px; max-width: 200px;margin-top: 16px">
                                                            <img src="{{ $signature }}">
                                                        </span>
                                                        <span style="font-size: 14px; color: #000; font-weight: 400; display: block;">Signature</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>                    
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="event-table" style="margin: 20px 0;">
                <div class="event-table-sec table-block">
                    <table style="width: 100%; table-layout:fixed; border: 1px solid #4875c5; border-bottom: none;">
                        <thead>
                            <tr style="text-align: center; background-color: #1f3864; color: #fff;">
                                <td colspan="3" style="border-right: none; text-align: left;"><strong>Event</strong></td>
                                <td style="width: 165px; text-align: left; padding-right: 0px"><strong>Timestamp</strong></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php  $afterQuestionEvent=array(); ?>
                            @foreach($eventBeforeAnswer as $event)
                                @if($event->event_type == 13 || $event->event_type == 14 || $event->event_type == 15)
                                    <?php $afterQuestionEvent[]=$event;  ?>
                                @else
                                    <tr>
                                        <td colspan="3">{!! $event->reason !!}</td>
                                        <td>
                                            @if(!empty($event->created_at))
                                                {{ $event->created_at->setTimezone($timeZone)->format($format) }}
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                    <table style="width: 100%; border: 1px solid #4875c5; border-top: none; border-bottom: none;page-break-after: avoid" class="table-res" autosize="1">
                        <thead style="border: 1px solid #4875c5; border-right: none; border-left: none; border-bottom: none; ">
                            <tr>
                                <td colspan="2" width="63%" style="text-align: center;"><strong>Questions</strong></td>
                                <td width="12%" style="text-align: center;"><strong>Response</strong></td>
                                <td style="text-align: center; width: 165px; padding-right: 0px"></td>
                            </tr>
                        </thead>
                        <tbody style="border: 1px solid #4875c5; border-right: none; border-left: none; border-top: none; ">
                            @foreach($questionAnswers as $value)
                                <tr>
                                    <td colspan="2">{{$value->question}}</td>
                                    <td style="text-align: center;">
                                        @if($value->verification_answer == 'Extra') 
                                            {{ $value->answer}}
                                        @else 
                                            {{ $value->verification_answer}}
                                        @endif 
                                    </td>
                                    <td>
                                        @if(!empty($value->created_at))
                                            {{ $value->created_at->setTimezone($timeZone)->format($format) }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach                            
                        </tbody>
                    </table>
                    @if(count($eventAfterAnswer) > 0)
                    <table style="width: 100%; table-layout:fixed; border: 1px solid #4875c5; border-top: none;">
                        <thead>
                            <tr style="text-align: center; background-color: #1f3864; color: #fff;">
                                <td colspan="3" style="border-right: none; text-align: left;"><strong>Event</strong></td>
                                <td style="width: 165px; text-align: left;padding-right: 0px"><strong>Timestamp</strong></td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($eventAfterAnswer as $event)
                                <tr>
                                    <td colspan="3">{{$event->reason}}</td>
                                    <td>
                                        @if(!empty($event->created_at))
                                            {{ $event->created_at->setTimezone($timeZone)->format($format)}}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
            <div class="footer-img" style="text-align: center; margin-top: 40px;">
                <span style="display: inline-block; width: 130px; vertical-align:middle;">
                    <img src="{{asset('images/tpv_receipt_logo.png')}}">
                </span>
            </div>
        </div>
        </main>
    </body>
</html>
