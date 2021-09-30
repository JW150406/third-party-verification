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
            height: 80px;
            margin: 20px 0;
        }
    </style>
</head>

<body>
<header>
<div class="header-logo">
        <img src="{{asset('images/contract/spring_logo.jpg')}}" />
    </div>
</header>
    <div class="header-title">
        <p><strong>Pennsylvania - Contract Summary&nbsp;</strong></p>
        <p><strong>Gas</strong></p>
    </div>
    
    <table>
        <tbody>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Natural Gas Supplier (&ldquo;NGS&rdquo;) Information:</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Spring Energy RRH, LLC d/b/a Spring Power &amp; Gas&nbsp;</span></p>
                    <p><span style="font-weight: 400;">111 East 14</span><span style="font-weight: 400;">th</span><span style="font-weight: 400;"> Street, #105, New York, NY 10003</span></p>
                    <p><span style="font-weight: 400;">Phone: 1.888.710.4782&nbsp;</span></p>
                    <p><span style="font-weight: 400;">Website: www.springpowerandgas.us</span></p>
                    <p><span style="font-weight: 400;">Spring is responsible for your gas commodity/supply charges.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Price Structure</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Variable Price. The price for all natural gas sold under this Agreement shall be a variable price per therm or Ccf which shall reflect the cost to Spring to obtain natural gas from all sources (including energy, capacity, settlement, ancillaries), offsets, related transmission and distribution charges and other related factors, plus all applicable taxes, fees, charges or other assessments and Spring&rsquo;s costs, expenses, and profit margins. There is no cap on your variable rate, and there is no limit on how much the price may change from one billing cycle to the next. You will be notified of price changes when the new price appears on your bill. Please be aware that in the event of any changes in capacity, transmission, or transmission related charges, and/or regulatory or other changes, including changes to ICAP tags, Spring reserves the right to increase pricing and/or terminate this Agreement.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Natural Gas Supply Price</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Your rate for the first billing cycle under this variable rate Agreement is </span><span style="font-weight: 400;">{{$gasData['rate'] ?? ''}} per {{$gasData['unit']}} </span><span style="font-weight: 400;">. Your rate for the second billing cycle is $</span><span style="font-weight: 400;">{{$gasData['Rate2'] ?? ''}}</span><span style="font-weight: 400;"> per Ccf.&nbsp; Thereafter, your rate will vary each billing cycle based on the factors described above.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Statement Regarding Savings&nbsp;</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Spring&rsquo;s natural gas supply price may be higher than the NGDC&rsquo;s price in any given month, and there is no guarantee of savings.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Contract Start Date</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Spring will begin furnishing natural gas supply service on a date set by the NGDC.&nbsp;&nbsp;</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Incentives</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Spring&rsquo;s&nbsp;Ecogold&nbsp;plan Zero Gas ensures that Spring offsets the carbon dioxide emissions associated with the natural gas consumption purchasing carbon offsets. The carbon offsets come from forestry projects. See Section 6 for more details. Customer may select one of two reward options, EITHER: (1) 5% Ecogold Rewards OR (2) 3% Cash Back. Rewards are calculated based on Customer&rsquo;s commodity supply charges. See Section 4 Supply Rewards for more details.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Deposit Requirements</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">None</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">Contract Duration/Length</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Continuous until cancelled.</span></p>
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
                    <p><span style="font-weight: 400;">Right of Rescission</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Customer may rescind this contract within 3 business days of receiving this Contract by calling or writing to Spring.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><span style="font-weight: 400;">End of Contract</span></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">This Agreement will continue until cancelled by either the Customer or Spring.</span></p>
                </td>
            </tr>
        </tbody>
    </table>
    <p><span style="font-weight: 400;">Version 08012020</span></p>
</body>

</html>