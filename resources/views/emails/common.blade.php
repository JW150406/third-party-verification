@include('emails.layouts.header')

<!-- start hero -->
<tr>
    <td align="center" bgcolor="#e9ecef">
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
            <tr>
                <td align="left" bgcolor="#ffffff" style="padding: 10px 24px 0;  border-top: 3px solid #d4dadf;">
                    <h1 style="margin: 0; font-size: 18px; font-weight: 700; line-height: 48px;">
                        @if (! empty($greeting))
                        {{ $greeting }}
                        @endif
                    </h1>
                </td>
            </tr>
        </table>
    </td>
</tr>
<!-- end hero -->

<!-- start copy block -->
<tr>
    <td align="center" bgcolor="#e9ecef">
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
            <!-- start copy -->
            <tr>
                <td align="left" bgcolor="#ffffff" style="padding: 24px; font-size: 16px; line-height: 24px;">
                    <p style="margin: 0;">{!! $msg !!}</p>
                </td>
            </tr>
            <!-- end copy -->

            <!-- start button -->
            @if(isset($action) && !empty($action))
            <tr>
                <td align="left" bgcolor="#ffffff">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td align="center" bgcolor="#ffffff" style="padding: 12px;">
                                <table border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td align="center" style="border-radius: 6px;">
                                            <a href="{{$action}}" target="_blank" style="background-color:#25365c;border:1px solid #e1e5ea;border-radius:6px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:13px;line-height:30px;text-align:center;text-decoration:none;width:120px;-webkit-text-size-adjust:none;mso-hide:all;">Click here</a>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <!-- end button -->
            @endif

            <!-- start copy -->
            <tr>
                <td align="left" bgcolor="#ffffff" style="padding: 24px; font-size: 16px; line-height: 24px; border-bottom: 3px solid #d4dadf">
                    <p style="margin: 0;">Regards,<br>
                        @if (isset($salutation) && !empty($salutation))
                            {{ $salutation }} 
                        @else 
                            The TPV360 Team
                        @endif
                    </p>
                </td>
            </tr>
            <!-- end copy -->

        </table>
    </td>
</tr>
<!-- end copy block -->

@include('emails.layouts.footer')