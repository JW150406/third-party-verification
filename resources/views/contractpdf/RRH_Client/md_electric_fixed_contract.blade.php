<html>

<head>
    <style>
        /* body{
            margin: 0;
            padding: 0;
        } */
        table {
            border-spacing: 0;
        }
        body{ font: message-box; }

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
        <img src="{{public_path('/images/rrh_contract_logo.png')}}" width="200px"/>
    </div>
</header>
    <div class="header-title">
        <p><strong>Maryland Electric Contract Summary&nbsp;</strong></p>
        <p><strong>(Residential &ndash; Fixed &ndash; 12 Months)</strong></p>
    </div>
    
    <table>
        <tbody>
            <tr>
                <td>
                    <p><strong>Electric Generation&nbsp; Supplier Information:</strong></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Spring Power &amp; Gas</span></p>
                    <p><span style="font-weight: 400;">111 East 14</span><span style="font-weight: 400;">th</span><span
                            style="font-weight: 400;"> Street #105</span></p>
                    <p><span style="font-weight: 400;">New York, NY 10003</span></p>
                    <p><span style="font-weight: 400;">Tel No. 1.888.710.4782&nbsp;&nbsp;</span></p>
                    <p><span style="font-weight: 400;">info@springpowerandgas.us</span></p>
                    <p><span style="font-weight: 400;">www.springpowerandgas.us</span></p>
                    <p><span style="font-weight: 400;">MD Electric License No.: IR-3537</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><strong>Price Structure</strong></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Unless otherwise agreed to in writing, the price for all
                            electricity sold under this Agreement will be a fixed price for twelve (12) months
                            (&ldquo;Initial Term&rdquo;). Thereafter, the price will be a price per kWh that varies each
                            billing cycle based on Spring&rsquo;s actual and estimated supply costs which shall reflect
                            the cost to Spring to obtain electricity from all sources (including energy, capacity,
                            settlement, ancillaries), RECs, related transmission and distribution charges and other
                            related factors, plus all applicable taxes, fees, charges or other assessments and
                            Spring&rsquo;s costs, expenses, and profit margins. There is no cap on your variable rate,
                            and there is no limit on how much the price may change from one billing cycle to the
                            next.&nbsp; Please be aware that in the event of any changes in capacity, transmission, or
                            transmission related charges, and/or regulatory or other changes, including changes to ICAP
                            tags, Spring reserves the right to increase pricing and/or terminate this Agreement.</span>
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><strong>Supply Price:</strong></p>
                </td>
                <td>
                    <?php $rateArr = explode('per',$program_info['Rate']); ?>
                    <p><span style="font-weight: 400;">Your fixed price under this Agreement is </span><span
                            style="font-weight: 400;">{{$electricData['rate']}}</span> <span
                            style="font-weight: 400;">&cent;</span><span
                            style="font-weight: 400;">/{{$electricData['unit']}} effective for the Initial Term. After the
                            Initial Term, the price may vary from each billing cycle based on the factors described
                            above. The variable price will reflect the cost of electricity provided through the Spring
                            Green program and the Wind REC. Spring&rsquo;s monthly price is available up to 12 days
                            before your next billing cycle by calling Spring at 1.888.710.4782.</span></p>
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
                    <p><strong>Green Incentives:</strong></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">The Spring Green program matches 100% of the customer&rsquo;s
                            electricity with Renewable Energy Certificates (RECs) sourced from national renewable
                            resources, in addition to the renewable content obligations required under Maryland
                            law.&nbsp;See Section 7 Renewable Energy Certificates for more details. Customer may select
                            one of two reward options, EITHER: (1) &ldquo;5% Ecogold Rewards&rdquo; OR (2) &ldquo;3%
                            Cash Back.&rdquo; Rewards are calculated based on Spring&rsquo;s supply charges. See Section
                            6 Supply Rewards for more details.&nbsp;</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><strong>Contract Start Date:</strong></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;"> Your supply service with Spring will begin after your utility
                            processes your enrollment, in accordance with utility rules and procedures.&nbsp;</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><strong>Contract Term/Length:</strong></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">Initial Term is twelve (12) months, followed by a price that
                            varies each billing cycle.&nbsp;</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><strong>Cancellation/Early Termination Fees:&nbsp;</strong></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;"> If you cancel prior to the end of the Initial Term, there is a
                            cancellation fee of $100. After the Initial Term, you may cancel at any time without
                            penalty.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><strong>Rescission:</strong></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;">A residential Customer may rescind this Agreement within 3
                            business days after the signing or receipt of this Agreement, whichever comes first, by
                            contacting Spring.</span></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><strong>Renewal Terms:&nbsp;</strong></p>
                </td>
                <td>
                    <p><span style="font-weight: 400;"> After the Initial Term, unless terminated by either party, your
                            contract continues on a price that varies each billing cycle until terminated. For
                            termination provisions, see Section 4 of the Terms and Conditions.&nbsp;</span></p>
                </td>
            </tr>
        </tbody>
    </table>
    <p><strong>For additional information, please refer to your Terms and Conditions. Please retain this document for
            your records. If you have any questions regarding this Agreement, contact your competitive supplier using
            the information above.</strong></p>
    <!-- <p><span style="font-weight: 400;">v07112019</span></p> -->
</body>

</html>