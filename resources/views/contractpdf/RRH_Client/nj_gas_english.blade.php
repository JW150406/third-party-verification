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
<div>
    <table style="border:0px;width: 100%">
        <tr>
            <td style="border:0px;">                
                <p><span style="font-weight: 400;">Customer Name: {{$gasData['cust_first_name']}} {{$gasData['cust_middle_initial']}} {{$gasData['cust_last_name']}}</span></p>
                <p><span style="font-weight: 400;">Address: {{ implode(", ", array_filter(array($gasData['service_addr_line_1'],$gasData['service_addr_line_2'],$gasData['service_addr_city'],$gasData['service_addr_county'],$gasData['service_addr_state'],$gasData['service_addr_zipcode']))) }}</span></p>
                <p><span style="font-weight: 400;">Utility Account Number: 
                    {{$gasData['account_number'] }} 
                </span></p>
            </td>
        </tr>        
    </table>
    </div>
    <p class="western"  align="center"><strong>New Jersey Third Party Supplier Contract Summary &ndash; Gas</strong></p>
    <table>
        <tbody>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Third Party Supplier (&ldquo;TPS&rdquo;) Information:</span></p>
                    <p><strong>By entering into this contract you are agreeing to purchase your Gas supply from this supplier.</strong></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Spring Power &amp; Gas</span></p>
                    <p><span style="font-weight: 400;">2500 Plaza 5, Harborside Financial Center, Jersey City, NJ 07311</span></p>
                    <p><span style="font-weight: 400;">Phone: 1.888.710.4782 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Website: www.springpowerandgas.us</span></p>
                    <p><span style="font-weight: 400;">BPU License No.: GSL-0153</span></p>
                    <p><span style="font-weight: 400;">Spring is responsible for your gas supply.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Price Structure</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">The price for all natural gas sold under this Agreement shall be a variable price per therm which shall reflect the cost to Spring to obtain natural gas from all sources (including energy, capacity, settlement, ancillaries), offsets, related transmission and distribution charges and other related factors, plus all applicable taxes, fees, charges or other assessments and Spring&rsquo;s costs, expenses, and profit margins. There is no cap on your variable rate, and there is no limit on how much the price may change from one billing cycle to the next. Please be aware that in the event of any changes in capacity, transmission, or transmission related charges, and/or regulatory or other changes, including changes to ICAP tags, Spring reserves the right to increase pricing and/or terminate this Agreement.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Generation/Supply Price</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Your initial price under this variable rate Agreement is </span><span style="font-weight: 400;">{{$gasData['rate'] ?? ''}} per {{$gasData['unit'] ?? ''}}</span><span style="font-weight: 400;"> effective for your first billing cycle and thereafter will vary each billing cycle based on the factors described above.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Statement Regarding Savings</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">There are no guaranteed savings.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Time required to change from TPS back to default service or another TPS</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">One to two billing cycles.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Incentives</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Spring&rsquo;s&nbsp;Zero Gas program matches 100% of your natural gas usage with carbon offsets. See Section 6. Customer may select one of two reward options, EITHER: (1) &ldquo;5% Ecogold Rewards&rdquo; OR (2) &ldquo;3% Cash Back.&rdquo; Rewards are calculated based on Customer&rsquo;s commodity supply charges. See Section 4.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Right to Cancel/Rescind</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Customer has 7 calendar days from the date of Utility&rsquo;s confirmation notice to contact their Utility and cancel this Agreement.&nbsp;</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Contract Start Date</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Agreement will begin with your first meter read by your Utility following your acceptance into the program.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Contract Term/Length</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Continuous from enrollment effective date.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Cancellation/Early Termination Fees</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">There is no early termination fee.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Renewal Terms</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Not applicable.&nbsp;</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Distribution Company Information</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Your Utility will continue to deliver your gas and you will continue to pay the Utility for this service. Call Utility in the event of an emergency or power outage. Elizabethtown Gas: 800.242.5830; PSE&amp;G: 1.800.436.7734; South Jersey Gas: 800.582.7060; NJNG: 1.800.221.0051</span></p>
                </td>
            </tr>
        </tbody>
    </table>
    <p><strong>This is a variable rate contract. </strong><span style="font-weight: 400;">A fixed rate is a price that will remain the same for a set period of time, whereas a variable rate is a price that will vary over time based on a number of factors, including weather fluctuations which may decrease or increase the variable rate. Call Spring to obtain a Spanish version of Agreement. &nbsp; </span><span style="font-weight: 400;">Version 07112019</span></p>
</body>

</html>