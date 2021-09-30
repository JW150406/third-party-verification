<html>

<head>
    <style>
      table {
            border-spacing: 0;
        }

        table,
        th,
        td {
            border: 1px solid black;
            border-collapse: collapse;
            font: message-box;
        }

        table tbody tr td:first-child {

            /*text-align: right;*/
            /* background-color: #f2f2f2; */
        }

        p strong {
            font: message-box;
            font-weight: 600;
        }

        .header-title {
            width: 79%;
            display: inline-block;
            vertical-align: middle;
        }

        .header-title p {
            text-align: center;
            margin: 8px 0;
        }
        header {
            position: fixed;
            top: -120px;
            float:right;
            right: 0px!important;
        }
        @page {
            margin: 120px 25px 100px;
        }
        /* .header-logo {
            width: 20%;
            display: inline-block;
            vertical-align: middle;
            text-align: right;
        } */

        .header-logo img {
            width: 220px;
            margin: 20px 0;
        }
    </style>
</head>

<body>
<header>
    <img src="{{asset('images/rrh_contract_logo.png')}}" style="height: 80px;" />
</header>   
    <table style="border:0px;width: 100%">
        <tr>
            <td style="border:0px;">
                <p class="western"><span style="font-family: Calibri, serif;">Customer Name: {{$electricData['cust_first_name']}} {{$electricData['cust_middle_initial']}} {{$electricData['cust_last_name']}}</span></p>
                    <p class="western"><span style="font-family: Calibri, serif;">Utility Account Number:   
                        {{$electricData['account_number']}}
                    </span></p>
                <p class="western"><span style="font-family: Calibri, serif;">Address: {{ implode(", ", array_filter(array($electricData['service_addr_line_1'],$electricData['service_addr_line_2'],$electricData['service_addr_city'],$electricData['service_addr_county'],$electricData['service_addr_state'],$electricData['service_addr_zipcode']))) }}</span></p>

            </td>
        </tr>        
    </table>
    <p class="western" align="center"><span style="font-family: Calibri, serif;"><strong>NJ Third Party Supplier Contract Summary - Electricity and Renewable Energy Certificates</strong></span></p>
    <table cellspacing="0">
        <tbody>
            <tr valign="top">
                <td  height="86">
                    <p class="western"><span style="font-family: Calibri, serif;">Third Party Supplier (&ldquo;TPS&rdquo;) Information. </span><span style="font-family: Calibri, serif;"><strong>By entering into this contract you are agreeing to purchase your Electric supply from this supplier.</strong></span></p>
                </td>
                <td>
                    <p class="western" align="justify"><span style="font-family: Calibri, serif;">Spring Power &amp; Gas</span></p>
                    <p class="western" align="justify"><span style="color: #000000;"><span style="font-family: Calibri, serif;">2500 Plaza 5, Harborside Financial Center, Jersey City, NJ 07311</span></span></p>
                    <p class="western"><span style="font-family: Calibri, serif;">Phone: 1.888.710.4782 </span><span style="color: #000000;"><span style="font-family: Calibri, serif;">Website: </span></span><span style="font-family: Calibri, serif;">www.springpowerandgas.us</span></p>
                    <p class="western" align="justify"><span style="color: #000000;"><span style="font-family: Calibri, serif;">BPU License No.: ESL-0176 </span></span><span style="color: #000000;"><span style="font-family: Calibri, serif;"><u>Spring is responsible for your electric supply.</u></span></span></p>
                </td>
            </tr>
            <tr valign="top">
                <td  height="128">
                    <p class="western"><span style="font-family: Calibri, serif;">Price Structure</span></p>
                </td>
                <td>
                    <p class="western" align="justify"><span style="font-family: Calibri, serif;">The price for all electricity sold under this Agreement shall be a variable price per kWh which </span><span style="font-family: Calibri, serif;">shall reflect the </span><span style="font-family: Calibri, serif;">cost to Spring to obtain electricity from all sources (including energy, capacity, settlement, ancillaries), RECs, related transmission and distribution charges and other related factors, plus all applicable taxes, fees, charges or other assessments and Spring&rsquo;s costs, expenses, and profit margins</span><span style="font-family: Calibri, serif;">. There is no cap on your variable rate, and there is no limit on how much the price may change from one billing cycle to the next. </span><span style="font-family: Calibri, serif;">Please be aware that in the event of any changes in capacity, transmission, or transmission related charges, and/or regulatory or other changes, including changes to ICAP tags, Spring reserves the right to increase pricing and/or terminate this Agreement.</span></p>
                </td>
            </tr>
            <tr valign="top">
                <td  height="42">
                    <p class="western"><span style="font-family: Calibri, serif;">Generation/Supply Price</span></p>
                </td>
                <td>
                    <p class="western" align="justify"><span style="font-family: Calibri, serif;">Your initial price under this variable rate Agreement is </span><span style="font-family: Calibri, serif;"><u>{{$electricData['rate'] ?? ''}} per {{$electricData['unit'] ?? ''}}</u></span> <span style="font-family: Calibri, serif;">&cent;</span><span style="font-family: Calibri, serif;">/</span><span style="font-family: Calibri, serif;"> effective for your first billing cycle and thereafter will vary each billing cycle based on the factors described above. Price reflects the cost of electricity including Spring Green program and the Wind RECs. </span></p>
                </td>
            </tr>
            <tr valign="top">
                <td  height="3">
                    <p class="western"><span style="font-family: Calibri, serif;">Statement Regarding Savings </span></p>
                </td>
                <td>
                    <p class="western" align="justify"><span style="font-family: Calibri, serif;">There are no guaranteed savings.</span></p>
                </td>
            </tr>
            <tr>
                <td valign="top"  height="49">
                    <p class="western"><span style="font-family: Calibri, serif;">Amount of time required to change from TPS back to default service or to another TPS</span></p>
                </td>
                <td>
                    <p class="western"><span style="font-family: Calibri, serif;">One to two billing cycles.</span></p>
                </td>
            </tr>
            <tr valign="top">
                <td  height="61">
                    <p class="western"><span style="font-family: Calibri, serif;">Incentives</span></p>
                </td>
                <td>
                    <p class="western" align="justify"><span style="font-family: Calibri, serif;">Spring Green program matches 100% of your electricity usage with renewable energy certificates. See Section 5. </span><span style="color: #666666;"><span style="font-family: Arial, serif;"><span style="font-size: xx-small;"><span style="color: #000000;"><span style="font-family: Calibri, serif;"><span style="font-size: medium;">Customer may select one of two reward options, EITHER: (1) &ldquo;5% Ecogold Rewards&rdquo; OR (2) &ldquo;3% Cash Back.&rdquo; Rewards are calculated based on Customer&rsquo;s commodity supply charges. See Section 4. </span></span></span></span></span></span></p>
                </td>
            </tr>
            <tr valign="top">
                <td  height="27">
                    <p class="western"><span style="font-family: Calibri, serif;">Right to Cancel/Rescind</span></p>
                </td>
                <td>
                    <p class="western"><span style="font-family: Calibri, serif;">Customer has 7 calendar days from the date of Utility&rsquo;s confirmation notice to contact their Utility and cancel this Agreement. </span></p>
                </td>
            </tr>
            <tr valign="top">
                <td  height="23">
                    <p class="western"><span style="font-family: Calibri, serif;">Contract Start Date</span></p>
                </td>
                <td>
                    <p class="western" align="justify"><span style="font-family: Calibri, serif;">Agreement will begin with your first meter read by your Utility following your acceptance into the program.</span></p>
                </td>
            </tr>
            <tr valign="top">
                <td  height="5">
                    <p class="western"><span style="font-family: Calibri, serif;">Contract Term/Length</span></p>
                </td>
                <td>
                    <p class="western"><span style="font-family: Calibri, serif;">Continuous from enrollment effective date.</span></p>
                </td>
            </tr>
            <tr>
                <td valign="top"  height="5">
                    <p class="western"><span style="font-family: Calibri, serif;">Cancellation/Early Term Fees</span></p>
                </td>
                <td>
                    <p class="western"><span style="font-family: Calibri, serif;">There is no early termination fee.</span></p>
                </td>
            </tr>
            <tr valign="top">
                <td  height="4">
                    <p class="western"><span style="font-family: Calibri, serif;">Renewal Terms</span></p>
                </td>
                <td>
                    <p class="western" align="justify"><span style="font-family: Calibri, serif;">Not applicable. </span></p>
                </td>
            </tr>
            <tr valign="top">
                <td  height="71">
                    <p class="western"><span style="font-family: Calibri, serif;">Distribution Company Information</span></p>
                </td>
                <td>
                    <p><span style="font-size: small;"><span style="font-family: Calibri, serif;"><span style="font-size: medium;">Your Utility will continue to deliver your electricity and you will continue to pay the Utility for this service. Call your Utility in the event of an emergency or power outage. </span></span><span style="font-family: Calibri, serif;"><span style="font-size: medium;">Atlantic City Electric: </span></span><span style="font-family: Calibri, serif;"><span style="font-size: medium;">800.642.3780; </span></span><span style="font-family: Calibri, serif;"><span style="font-size: medium;">JCP&amp;L: </span></span><span style="font-family: Calibri, serif;"><span style="font-size: medium;">1.800.662.3115; </span></span><span style="font-family: Calibri, serif;"><span style="font-size: medium;">PSE&amp;G: </span></span><span style="font-family: Calibri, serif;"><span style="font-size: medium;">1.800.436.7734; NJNG: 1.800.221.0051</span></span></span></p>
                </td>
            </tr>
        </tbody>
    </table>
    <p class="western"><a name="_GoBack"></a> <span style="font-family: Calibri, serif;"><strong>This is a variable rate contract. </strong></span><span style="font-family: Calibri, serif;">A fixed rate is a price that will remain the same for a set period of time, whereas a variable rate is a price that will vary over time based on a number of conditions, including weather fluctuations which may decrease or increase the variable rate. Call Spring to obtain a Spanish version.</span> <span style="font-family: Calibri, serif;"><span style="font-size: xx-small;">Version 07112019</span></span></p>
</body>

</html>