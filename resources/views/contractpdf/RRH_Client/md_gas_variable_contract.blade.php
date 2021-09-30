<html>

<head>
    <style>
        table {
            border-spacing: 0;
        }
        body{
            font-family: arial;
        }
        p span{
            margin-bottom:0px!important;
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
        table,
        th,
        td {
            border: 1px solid black;
            border-collapse: collapse;
            font: message-box;
        }

        table tbody tr td:first-child {

            text-align: right;
            background-color: #f2f2f2;
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
        <img src="{{asset('images/rrh_contract_logo.png')}}"  />
    </div>
</header>
    <div class="header-title">
        <p><strong>Maryland Gas Contract Summary&nbsp;</strong></p>
    </div>
    
    <table>
        <tbody>
            <tr>
                <td>
                    <p><strong>Gas&nbsp; Supplier Information:</strong></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Spring Power &amp; Gas</span></p>
                    <p><span style="font-weight: 400;">111 East 14</span><span style="font-weight: 400;">th</span><span style="font-weight: 400;"> Street, #105</span></p>
                    <p><span style="font-weight: 400;">New York, NY 10003</span></p>
                    <p><span style="font-weight: 400;">Tel No. 1.888.710.4782&nbsp;&nbsp;</span></p>
                    <p><span style="font-weight: 400;">info@springpowerandgas.us</span></p>
                    <p><span style="font-weight: 400;">www.springpowerandgas.us</span></p>
                    <p><span style="font-weight: 400;">MD Gas License No.: IR-3550</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><strong>Price Structure</strong></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">The price for all natural gas sold under this Agreement will be a variable price per therm based on Spring&rsquo;s actual and estimated supply costs which shall reflect the cost to Spring to obtain natural gas from all sources (including energy, capacity, settlement, ancillaries), offsets, related transmission and distribution charges and other related factors, plus all applicable taxes, fees, charges or other assessments and Spring&rsquo;s costs, expenses, and profit margins. There is no cap on your variable rate, and there is no limit on how much the price may change from one billing cycle to the next. Please be aware that in the event of any changes in capacity, transmission, or transmission related charges, and/or regulatory or other changes, including changes to ICAP tags, Spring reserves the right to increase pricing and/or terminate this Agreement.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><strong>Supply Price:</strong></p>
                </td>
                <td>
                    <?php $rateArr = explode('per', $program_info['Rate']); ?>
                    <p><span style="font-weight: 400;">Your initial price under this variable rate Agreement is </span><span style="font-weight: 400;">{{$gasData['rate']}}</span><span style="font-weight: 400;"> per {{$gasData['unit']}} effective for your first billing cycle. Thereafter, your price will vary each billing cycle based on the factors described above. Spring&rsquo;s monthly price is available by calling Spring at 1.888.710.4782.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><strong>Statement Regarding Savings:</strong></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">There are no guaranteed savings.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><strong>Incentives:</strong></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Spring&rsquo;s&nbsp;Ecogold&nbsp;plan Zero Gas ensures that Spring offsets the carbon dioxide emissions associated with the natural gas consumption by purchasing carbon offsets. The carbon offsets come from forestry projects. See Section 8 of the terms and conditions for more details. Customer may select one of two reward options, EITHER: (1) &ldquo;5% Ecogold Rewards&rdquo; OR (2) &ldquo;3% Cash Back.&rdquo; Rewards are calculated based on Spring&rsquo;s supply charges. See Section 6 Supply Rewards for more details.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><strong>Contract Start Date:</strong></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">This Agreement will begin when your utility processes the enrollment.&nbsp;</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><strong>Contract Term/Length:</strong></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">This Agreement will continue until cancelled by you or Spring.&nbsp;</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><strong>Cancellation/Early Termination Fees:&nbsp;</strong></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">There is no early termination fee.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><strong>Rescission:</strong></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">A residential Customer may rescind this Agreement within 3 business days after the signing or receipt of this Agreement, whichever comes first, by contacting Spring.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><strong>Renewal Terms:&nbsp;</strong></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Not applicable.</span></p>
                </td>
            </tr>
        </tbody>
    </table>
    <p>&nbsp;</p>
    <p><strong>For additional information, please refer to your Terms and Conditions. Please retain this document for your records. If you have any questions regarding this Agreement, contact your competitive supplier using the information above.</strong></p>
</body>

</html>