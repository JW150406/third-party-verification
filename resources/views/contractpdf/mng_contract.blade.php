<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
    }

    .contact-info label {
        display: block;
        margin-bottom: 10px;
        font-weight: 600;
        font-family: Arial, Helvetica, sans-serif;
    }

    .contact-info label span {
      font-weight: 400;
      font-family: Arial, Helvetica, sans-serif;
    }

    .three-lable label {
        display: inline-block;
    }

    p {
        margin-bottom: 0px;
        margin-top: 2px;
    }

    .top-address {
        height: 110px;
        line-height: 2;
        font-family: Arial, Helvetica, sans-serif;
    }

    .mt10 {
        margin-top: 10px;
    }

    .agent-info {
        border: 2px solid #d5d5d5;
        padding: 3px;
        font-family: Arial, Helvetica, sans-serif;
    }
    .agent-info p{
        display: inline-block;
        width: 32%;
        font-family: Arial, Helvetica, sans-serif;
    }

    .row {
        display: block;
        margin-bottom: 10px;
    }

    .col1 {
        width: 25%;
        display: inline-block;
        vertical-align: top;
    }

    .col2 {
        width: 72%;
        display: inline-block;
        vertical-align: top;
    }

</style>

<body>
    <div style="float:left;">
        <img src="{{public_path('images/mng_contract_image.png')}}" height="110" width="300" alt="" />
    </div>
    <div class="top-address">
        <p align="right">
            2723 South State Street, Suite 150 PMB 218
        </p>
        <p align="right">
            Ann Arbor, Michigan 48104
        </p>
        <p align="right">
            888-988-MICH (6424)
        </p>
    </div>
    <p style="border:1px solid #214576; height:2px; width=580px; background-color:#214576;">
        <br />
    </p>
    <p class="mt10" align="center">
        <strong>Gas Customer Choice Contract</strong>
    </p>
    <p align="center">
        <strong>VARIABLE RATE/RESIDENTIAL</strong>
    </p>
    <p align="center">
        <strong>Choice Contract</strong>
    </p>
    <br />
    <div class="contact-info">
        <label>
            Account Holder Name:
            <span>{{implode(" ",array_filter(array($program_info['ServiceFirstName'],$program_info['ServiceMiddleName'],$program_info['ServiceLastName'])))}}</span>
        </label>
        <label>
            Address: <span>{{$program_info['ServiceAddress1']}}</span>
        </label>
        <div class="three-lable">
            <label style="width: 32%;">
                City: <span>{{$program_info['ServiceCity']}}</span>
            </label>
            <label style="width: 32%;">
                State: <span>{{$program_info['ServiceState']}}</span>
            </label>
            <label style="width: 32%;">
                Zip: <span>{{$program_info['ServiceZip']}}</span>
            </label>
        </div>
        <div class="three-lable">
            <label style="width: 40%;">
                Phone: <span>{{$customer_info['Phone']}}</span>
            </label>
            <label style="width: 55%;">
                Email: <span>{{$customer_info['email']}}</span>
            </label>
        </div>
        <div class="three-lable">
            <label style="width: 70%;">
                <span>@if(!empty($program_info['AccountNumber']))
                    {{ str_replace('()','',$program_info['AccountNumber']) }}
                    @endif</span>
            </label>
            <label style="width: 28%;">
                <span>{{$customer_info['date']}}</span>
            </label>
        </div>


    </div>

    <div class="row">
        <div class="col1" style="font-weight: bold;">
            Residential Gas Rate:
        </div>
        <div class="col2">
            My gas rate will be {{$program_info['Rate']}} for the first two months of this contract, and I will be on a Michigan
            Natural Gas, LLC (MNG) variable rate for the remaining term of the 12-month contract.
        </div>
    </div>
    <div class="row">
        <div class="col1" style="font-weight: bold;">
            Term:
        </div>
        <div class="col2">
            The start date will begin with my successful enrollment into the Gas Customer Choice Program with MNG and will
            extend for a 12-month period.
        </div>
    </div>
    <div class="row">
        <div class="col1" style="font-weight: bold;">
            Renewal:
        </div>
        <div class="col2">
            After the initial 12-month Term, my contract with MNG will continue on an MNG variable rate on a month to month
            basis, cancellable at any time without penalty.
        </div>
    </div>
    <div class="row">
        <div class="col1" style="font-weight: bold;">
            Cancellation:
        </div>
        <div class="col2" style="font-weight: bold; font-style: italic;">
            I may cancel at any time within 30 days with no penalty from MNG. Following the 30 day period, a $50
            cancellation fee will apply. However, I may enroll into a fixed-rate contract offered by MNG at any time without
            penalty.
        </div>
    </div>

    <strong class="mt10">
        I acknowledge that I am the account holder or, Legally Authorized Person
    </strong>
    <em><strong> </strong></em>
    <strong class="mt10">
        to execute a contract on behalf of the account holder. I understand that by
        signing this contract, I am switching the gas Supplier for this account to
        Michigan Natural Gas, LLC. I understand that gas purchased for this account
        by Michigan Natural Gas, LLC will be delivered through
        {{$program_info['Utility']}} delivery system. The account
        holder, or the person who signed this contract on behalf of the account
        holder, has 30 days after today to cancel this contract for any reason
        through written or verbal notification to Michigan Natural Gas, LLC.
        <br />
    </strong>
    <p class="mt10" style="vertical-align: middle;">
        Account Holder or Legally Authorized Signature:
        <!-- <div> -->
        <img src="{{$customer_info['signature']}}" height="96px" width="288px" style="vertical-align: middle;">
        <!-- Signature, width=3in, height = .4in  -->
        <!-- </div> -->
    </p>
    <div class="contact-info mt10">
        <label>
            Print Name:
            <span>{{implode(" ",array_filter(array($program_info['BillingFirstName'],$program_info['BillingMiddleName'],$program_info['BillingLastName'])))}}</span>
        </label>
    </div>


    <p>
        If Legally Authorized, your relationship to the account holder: <span>{{$program_info['Relationship']}}</span>
    </p>
    <p class="mt10">
        For Agent use:
    </p>
    <div class="agent-info">
       <p> AGENT: <span>{{$salesAgents['name']}}</span></p>
        <p>AGENT Code: <span>{{$salesAgents['id']}}</span></p>
        <p>AGENT Phone: <span>{{$salesAgents['phone']}}</span></p>
    </div>


    <p class="mt10" align="justify">
        <em><strong>Michigan Natural Gas, LLC (MNG): Residential</strong></em>
    </p>
    <p class="mt10" align="justify">
        <em>
            <strong>
                Gas Rate Gas Customer Choice Contract (Terms &amp; Conditions)
            </strong>
        </em>
    </p>
    <p>
        <em><strong>1. Purchase Agreement: </strong></em>
        Account holder or legally authorized person (Customer) agrees to purchase
        its natural gas for twelve (12) months from Michigan Natural Gas, LLC
        (MNG), a Michigan alternative gas supplier licensed by the Michigan Public
        Service Commission (MPSC). Customer understands that the gas under this
        contract will be delivered to Customer through the {{$program_info['Utility']}}
        Energy ({{$program_info['Utility']}}) distribution system and that {{$program_info['Utility']}} will remain
        responsible for gas emergencies, meter reading, and billing
        (except the Rate under this contract). Customer authorizes MNG to obtain
        billing, payment and usage history from {{$program_info['Utility']}}. After
        expiration of the initial term, this contract will continue on a
        month-to-month basis, cancellable at any time without penalty. Both
        Customer and MNG agree to abide by the rules and regulations of the
        Michigan Gas Customer Choice Program established by the MPSC
    </p>
    <p>
        <em><strong>2. Customer’s Right to Cancel:</strong></em>
        If Customer terminates the contract within 30 days from the date of this
        contract and returns to {{$program_info['Utility']}}, Customer understands that
        Customer will be required to remain with {{$program_info['Utility']}} for the next 12
        months (a $10 fee may apply if Customer has switched suppliers during the
        previous 12 months).
        <em>
            <strong>
                Customers have the right to cancel within 30 days with no penalty
                from MNG. Following the 30 day period, there will be a $50
                cancellation fee.
            </strong>
        </em>
    </p>
    <p align="justify">
        <em><strong>3. Billing and Payment</strong></em>
        <em><strong>:</strong></em>
        <strong>
            Customer will continue to receive a monthly bill from
            {{$program_info['Utility']}}
        </strong>
        . In addition to the cost of the gas provided under the terms of this
        contract, the bill will include but not be limited to {{$program_info['Utility']}}’
        delivery and customer charges, taxes and fees (which will be charged by
        {{$program_info['Utility']}} regardless of whether or not Customer is enrolled in the
        Michigan Gas Customer Choice program). Customer agrees to remit payment in
        full to {{$program_info['Utility']}} per the Michigan Gas Customer Choice Program
        Rules and service rate.
    </p>
    <p>
        <em><strong>4. Residential Gas Rate</strong></em>
        <em><strong>:</strong></em>
    </p>
    <p>
        The Rate will be {{$program_info['Rate']}} for the first two months of this contract,
        and on a variable rate for the remaining term of the 12-month contract.
        This price will vary month-to-month and will be calculated as a weighted
        average cost of gas (WACOG), consisting of a supply portfolio of purchases
        plus all MNG program charges and administration fees. For example,
        @if(strtoupper($program_info['Market']) == 'SEMCO')
            <span> package 1:500 thm @ 0.500; A23package 2; 600 @ 0.51B262 thm, would produce a WACOG of .5065/thm.</span>
        @elseif(strtoupper($program_info['Market']) == 'Consumers Energy')
            <span> package 1: Package 1: 50 MB43cf @ 5.00 ($250); Package 2:A23 60 Mcf @ 5.12 Mcf ($307.2). The WACOG = $5.065 (($250+307.2)/110).</span>
        @else
           <span> package 1:500 ccf @ A23.500; pBA3127B46ackage 2; 600 @ 0.512 ccf, would produce a WACOG of .5065/ccf.</span>
        @endif

    </p>
    <p>
        <em><strong>5. Price Protection: </strong></em>
        Michigan Natural Gas agrees that customer can elect to switch to any fixed
        rate currently being offered by MNG at no cost to the customer at any time.
    </p>
    <p>
        <em><strong>6. Changes in Consumption</strong></em>
        : Customer agrees to notify MNG of any material changes in anticipated
        consumption of natural gas under this contract.
    </p>
    <p>
        <em><strong>7. Term:</strong></em>
        The term of this contract shall commence with the next possible billing
        cycle by {{$program_info['Utility']}} and shall continue for an initial term of
        twelve (12) months. After the initial term, unless terminated by either
        party, and with at least thirty (30) days notice to Customer by MNG, the
        contract shall continue on a month-to-month basis and the Customer shall be
        automatically converted to an MNG variable rate. Customer understands that
        there may be delays in the start of the term and agrees not to hold MNG
        responsible for any such delays.
    </p>
    <p align="justify">
        <em><strong>8. Regulatory:</strong></em>
        Customer understands that the MPSC may change the rules and regulations of
        the Michigan Gas Customer Choice Program and in that event, MNG reserves
        the right to modify this contract with 10 days notice and acceptance by
        Customer.
    </p>
    <p align="justify">
        <em><strong>9. Limitation of Liability</strong></em>
        <em><strong>:</strong></em>
        MNG will not be responsible for any type of exemplary, consequential,
        indirect, punitive or incidental damages whatsoever arising out of or
        relating to this contract and the remedy in any suit or claim brought
        against MNG shall be limited to direct actual damages. MNG makes no
        warranties or representations other than those expressly made in this
        contact.
    </p>
    <p align="justify">
        <em><strong>10. Force Majeure:</strong></em>
        MNG will not be responsible for delivering gas to customers in the event of
        circumstance beyond its control, which include acts of God, acts of
        terrorism, orders, rules and regulations, or the action of any court or
        governmental authority. In the case of such an event, MNG may at its sole
        option reject this contract in whole and re-determine the Rate and continue
        to provide service to and upon acceptance by the Customer.
    </p>
    <p align="justify">
        <em><strong>11. Entire Contract</strong></em><em><strong>:</strong></em>
        This contract constitutes the entire contract between Customer and Michigan
        Natural Gas, LLC and supersedes all prior written and verbal contracts and
        representations made with respect to the terms and conditions contained
        herein.
    </p>
    <p align="justify">
        <em><strong>12. Customer Service:</strong></em>
        MNG can be reached at 888-988-6424 during normal business hours or may be
        reached at
        <u>
            <a href="mailto:info@michigannaturalgasllc.com">
                info@michigannaturalgasllc.com
            </a>
        </u>
        . Any written notices may be sent to Customer Service, Michigan Natural
        Gas, LLC, 2723 South State St Suite 150 PMB 218 Ann Arbor, Michigan 48104.
    </p>
</body>