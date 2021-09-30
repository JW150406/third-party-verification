<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <style>
        @page {
            margin: 200px 25px 40px;
        }

        header {
            position: fixed;
            top: -180px;
            left: 0px;
        }

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

        .top-address p {
            line-height: 1.2;
        }

        .mt10 {
            margin-top: 10px;
        }

        .agent-info {
            border: 2px solid #d5d5d5;
            padding: 3px;
            font-family: Arial, Helvetica, sans-serif;
        }

        .agent-info p {
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

        .mt0 {
            margin-top: 0px;
        }

        .mb15 {
            margin-bottom: 15px;
        }

        .ol-list {
            display: block;
            margin-top: 8px;
            width: 100%;
        }

        .ol-list span {
            border: 1px solid #d5d5d5;
            height: 20px;
            width: 10%;
            display: inline-block;
            vertical-align: top;
            text-align: center;
        }

        .ol-list ol {
            display: inline-block;
            margin-top: -10px;
            width: 80%
        }

        .page-break {
            page-break-after: always;
        }
    </style>

    <head>

    <body>
        <header>
            <div style="float:left;">
                <img src="{{public_path('images/ugp_contract_image.png')}}" height="110" width="300" alt="" />
            </div>
            <div class="top-address">
                <p align="right">
                    Utility Gas and Power
                </p>
                <p align="right">
                    2680 Corporate Park Drive
                </p>
                <p align="right">
                    Suite 100
                </p>
                <p align="right">
                    Opelika, AL 36801
                </p>
                <p align="right">
                    855-747-4931
                </p>
            </div>
            <p style="border:1px solid #214576; height:2px; width=580px; background-color:#214576;">
                <br />
            </p>
        </header>
        <p align="center" class="mt0">
            <strong>{{$gasData['utility_name']}}</strong>
        </p>
        <p align="center" class="mt0">
            <strong>Choice Contract</strong>
        </p>
        <br/>
        <div class="contact-info mt10">
            <label>
                Account Holder Name:
                <span>{{implode(" ",array_filter(array($gasData['cust_first_name'],$gasData['cust_middle_initial'],$gasData['cust_last_name'])))}}</span>
            </label>
            <label>
                Address: <span>{{$gasData['service_addr_line_1']}} {{$gasData['service_addr_line_2']}}</span>
            </label>
            <div class="three-lable">
                <label style="width: 32%;">
                    City: <span>{{$gasData['service_addr_city']}}</span>
                </label>
                <label style="width: 32%;">
                    State: <span>{{$gasData['service_addr_state']}}</span>
                </label>
                <label style="width: 32%;">
                    Zip: <span>{{$gasData['service_addr_zipcode']}}</span>
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
                    GAS {{$gasData['act_num_verbiage' ?? 'Account Number']}} :
                    <span>{{$gasData['account_number']}}</span>
                </label>
                <label style="width: 28%;">
                    Date:
                    <span>{{$customer_info['date']}}</span>
                </label>
            </div>
        </div>

        <div class="row">
            <div class="col1" style="font-weight: bold;">
                Gas Rate:
            </div>
            <div class="col2">
                My gas rate will be <strong>${{$gasData['rate']}} per {{$gasData['unit']}}</strong> during the {{$gasData['term']}}-month contract.
            </div>
        </div>
        <div class="row">
            <div class="col1" style="font-weight: bold;">
                Term:
            </div>
            <div class="col2">
                The start date will begin with my successful enrollment into the Gas CHOICE
                Program with Utility Gas and Power (UGP) and will extend for a {{$gasData['term']}}-month
                period (the Initial Term).
            </div>
        </div>
        <div class="row">
            <div class="col1" style="font-weight: bold;">
                Renewal:
            </div>
            <div class="col2">
                After the Initial Term, my contract with UGP will continue on a UGP
                variable rate on a month to month basis, cancellable at any time without
                penalty.
            </div>
        </div>
        <div class="row">
            <div class="col1" style="font-weight: bold;">
                Cancellation:
            </div>
            <div class="col2" style="font-weight: bold;">

                I may cancel within 30 days with no penalty from UGP. Following the
                30-day period, a $150 cancellation fee will apply.
            </div>
        </div>

        <p class="mt10">
            <strong>
                I acknowledge that I am the account holder or, Legally Authorized
                Person
            </strong>
            <em><strong> </strong></em>
            <strong>
                to execute a contract on behalf of the account holder. I understand
                that by signing this contract, I am switching the gas supplier for this
                account to Utility Gas and Power. I understand that gas purchased for
                this account by Utility Gas and Power will be delivered through the
                utility’s delivery system. The account holder, or the person who signed
                this contract on behalf of the account holder, has 30 days after today
                to cancel this contract for any reason through written or verbal
                notification to Utility Gas and Power.
            </strong>
        </p>
        <p class="mt10" style="vertical-align: middle;">
            Account Holder or Legally Authorized Signature: <img src="{{$customer_info['signature']}}" height="96px"
                width="320px" style="vertical-align: middle;">
        </p>
        <p class="mt10">
            <strong> Name:</strong>
            <span>{{implode(" ",array_filter(array($gasData['contact_first_name'],$gasData['contact_middle_initial'],$gasData['contact_last_name'])))}}</span>
        </p>
        <p class="mt10">
            If Legally Authorized, your relationship to the account holder:
            <span>{{$program_info['Relationship']}}</span>
        </p>
        <p class="mt10">
            For Agent use:
        </p>

        <div class="agent-info">
            <p>AGENT: <span>{{$salesAgents['name']}}</span></p>
            <p>AGENT Code: <span>{{$salesAgents['id']}}</span></p>
            <p>AGENT Phone: <span>{{$salesAgents['phone']}}</span></p>
        </div>
        <div class="page-break"></div>
        <p class="mt10">
            <strong>ACKNOWLEDGEMENT FORM:</strong>
        </p>

        <div class="ol-list" style="margin-top:30px;">
            <span>Yes</span>
            <ol type="A">
                <li>
                    <p class="mt10">
                        The representative stated he/she was representing a retail natural
                        gas supplier and was not from the natural gas company?
                    </p>
                </li>
            </ol>
        </div>

        <div class="ol-list">
            <span>Yes</span>
            <ol type="A" start="2">
                <li>
                    <p class="mt10">
                        The representative explained that by signing the enrollment form
                        you were entering an agreement/contract for retail natural gas
                        supplier to supply your natural gas?
                    </p>
                </li>
            </ol>
        </div>

        <div class="ol-list">
            <span>Yes</span>
            <ol type="A" start="3">
                <li>
                    <p class="mt10">
                        The representative explained the price for natural gas under the contract you signed is {{$gasData['rate']}} per {{$gasData['unit']}}, plus sales tax.
                    </p>
                </li>
            </ol>
        </div>

        <div class="ol-list">
            <span>Yes</span>
            <ol type="A" start="4">
                <li>
                    <p class="mt10">
                        The representative explained that the contract term is for {{$gasData['term']}} months.
                    </p>
                </li>
            </ol>
        </div>

        <div class="ol-list">
            <span>Yes</span>
            <ol type="A" start="5">
                <li>
                    <p class="mt10">
                        The representative explained your right to cancel?
                    </p>
                </li>
            </ol>
        </div>

        <div class="ol-list">
            <span>Yes</span>
            <ol type="A" start="6">
                <li>
                    <p class="mt10">
                        The representative left two completed right to cancel notices with
                        you?
                    </p>
                </li>
            </ol>
        </div>

        <div class="ol-list">
            <span>Yes</span>
            <ol type="A" start="7">
                <li>
                    <p class="mt10">
                        Did the representative disclose whether an early termination
                        liability fee would apply if you cancel the contract before the
                        expiration of the contract term? If such a fee does apply to your
                        contract, did the representative disclose the amount of the fee?
                    </p>
                </li>
            </ol>
        </div>

    
        <p class="mt10" style="vertical-align: middle;">
            Signature: <img src="{{$customer_info['signature']}}" height="96px"
                width="288px" style="vertical-align: middle;">
        </p>
        
        <p class="mt10" align="justify">
            After your successful enrollment with UGP and confirmation by the utility,
            UGP will supply your natural gas until either you or UGP cancels your
            service. Your service with UGP begins on the date provided to you by the
            utility. UGP is not responsible for utility delays in processing your
            enrollment or cancellation request. This contract governs your pricing with
            UGP during the term of your contract, but the timing of application of
            those prices to your utility bill may be impacted by the timing of your
            utility meter read cycles. The utility will deliver the natural gas you
            purchase from UGP to your premises, read your meter, provide emergency
            services, and issue your bill each month. The utility will charge you
            separately for those services. UGP’s charges for natural gas will appear as
            a separate line item on your bill. Sales tax will appear separately.
        </p>
        <p class="mt10" align="justify">
            For new customers, your contract begins on your effective date as
            established by the utility and continues for the period specified in your
            welcome letter. For existing UGP customers, choosing a new price plan, your
            contract is effective upon the date your price plan change request is
            processed by UGP, and continues for the length of your contract, unless a
            later effective date is determined between you and UGP. Your price plan,
            per unit price, as applicable if you have selected a fixed or variable
            plan, are specified in your welcome letter. UGP offers residential pricing
            for residential customers and commercial pricing for commercial customers.
            UGP may from time to time offer promotional or discounted prices. Sales tax
            and utility charges are not included in the price per Mcf or Ccf, whichever
            is applicable based on your utility. Your plan will incur a monthly service fee of ${{$gasData['msf']}}. If, due
            to a change in market conditions, UGP wishes to lower the price per Ccf or
            Mcf charged to the customer under an existing contract, it may do so
            without consent provided there are no other changes to the terms and
            conditions to the contract. UGP does not charge any late payment fees. UGP
            does not offer budget billing,
        </p>
        <p class="mt10" align="justify">
            With a UGP variable plan, your price per unit of natural gas may change or
            remain the same from month to month based on market conditions. Service
            under a variable plan is on a month-to-month contract basis until cancelled
            by you or UGP. Many factors influence retail natural gas pricing, including
            wholesale gas costs, which are impacted by the weather; general market
            conditions; transportation costs; operating expenses; and other factors.
            UGP sets its prices each month based on the most current information
            available, including, but not limited to, the NYMEX monthly contract price
            for that month. UGP cannot predict the volatility of the market or what its
            customers will pay for gas in the future. Under a variable plan, you may
            switch to another UGP price plan for which you qualify at any time at no
            additional charge. The per-unit price on a variable plan may be higher or
            lower than on a fixed plan.
        </p>
        <p class="mt10" align="justify">
            With a fixed price plan, UGP charges you a fixed price per unit during the
            term of the contract. This contract governs your pricing with UGP during
            the term of your contract, but the timing of application of those prices to
            your utility bill may be impacted by the timing of your utility meter read
            cycles. In addition, because meter read cycles may differ from the start
            and end dates of your fixed price plan contract, you may not receive the
            same number of bills as the number of months in your contract with UGP.
            When you select a fixed price plan, you commit to remain with UGP at the
            same price for the term of your contract. Although UGP’s currently
            available fixed price may change at any time, the price for the term of
            your contract will be UGP’s fixed price in effect at the time you selected
            your fixed plan. The per-unit price on a fixed plan may be higher or lower
            than on a variable plan. If you decide not to renew your contract with UGP,
            whether you select another plan with us, return your natural gas service to
            the utility or choose another natural gas supplier, in order to avoid an
            early termination charge on your current contract, please be sure any
            actions that you take become effective after your current contract expires.
        </p>
        <p class="mt10" align="justify">
            If you terminate your plan during the initial term of your contract, except
            as specifically set forth in your UGP contract at the time of enrollment,
            UGP will assess you an early termination charge of $150 for residential
            customers or $300 for commercial customers. Your UGP gas charges will be
            billed by the utility, along with the utility’s charges for its services.
            By agreeing to these terms and conditions of service, you agree to pay UGP
            charges in accordance with the utility’s payment procedures. If you do not
            pay your bills in accordance with those payment procedures or if you fail
            to comply with any agreed-upon payment arrangement, then the utility’s
            service may be terminated in accordance with the utility’s tariffs, and
            this contract may be cancelled. If that occurs, you are required to pay the
            balance owed, including any early termination charges. UGP reserves the
            right to bill you directly for our services, and if that occurs, we may
            complete a credit check and (at our sole discretion) require a security
            deposit. UGP and the utility are responsible for collecting amounts owed on
            their respective bills. Upon 14 days’ written notice, UGP may cancel this
            contract for nonpayment. In that event, you must pay the entire UGP balance
            due, including any early termination charges.
        </p>
        <p class="mt10" align="justify">
            UGP is not responsible for resolving disputes with the utility. However, if
            you have questions concerning your UGP service, you may call UGP toll-free
            at 1-855-747-4931 Monday-Friday from 8 a.m. to 5 p.m. EST, excluding
            national holidays. You also may contact us by mail at 2680 Corporate Park
            Dr Ste 100 Opelika, AL 36801, or by e-mail at info@utilitygasandpower.com.
            In the event of a dispute with UGP, you first should contact a UGP Account
            Manager within 30 days of receipt of your bill. If your complaint is not
            resolved after you have called UGP, or for general utility information,
            residential and business customers may contact the public utilities
            commission of Ohio (PUCO or Commission) for assistance at 1-800-686- 7826
            (toll free) from eight a.m. to five p.m. weekdays, or at
            http://www.puco.ohio.gov. Hearing or speech impaired customers may contact
            the PUCO via 7-1-1 (Ohio relay service). The Ohio consumers’ counsel (OCC)
            represents residential utility customers in matters before the PUCO. The
            OCC can be contacted at 1-877-742-5622 (toll free) from eight a.m. to five
            p.m. weekdays, or at http://www.pickocc.org. If you need to report a
            natural gas leak or emergency, call the utility at the number listed on
            your bill. Right of Rescission– If you are a new UGP customer, the utility
            will send you a letter confirming your UGP enrollment. You may rescind your
            enrollment without penalty within 7 business days of the postmark date of
            that letter by contacting the utility by phone (Columbia Gas of Ohio:
            1-800-344-4077; Duke Energy: 1-800-544-6900; Dominion East Ohio:
            1-800-362-7557) or in writing. If you cancel a fixed price plan prior to
            the end of the contract term, early termination charges will apply, as
            described above. This contract will terminate automatically if the utility
            does not serve the requested premises, if you move (other than as specified
            below), or if UGP returns you to the utility’s service.
        </p>
        <p class="mt10" align="justify">
            You have the right to terminate this contract without penalty if you move
            to a new location outside of your utility service area. You have the right
            to terminate this contract without penalty in the event the customer
            relocates outside the service territory of the incumbent natural gas
            company or within the service territory of an incumbent natural gas company
            that does not permit portability of the contract. You must notify UGP if
            you are moving to a new location outside of your utility service area in
            order to have your early termination charge credited back to you. However,
            UGP can provide service at the new location under a new contract if you
            notify UGP at the time of your move and establish service with the utility
            at the new location with UGP as your service provider. Termination
            typically is effective with the next full utility billing cycle that occurs
            after the utility is notified of the request to terminate service. You are
            responsible for all fees and charges until your service is terminated. If
            you change to another natural gas provider (including the utility), the
            utility may assess a switching fee under its tariff and UGP reserves the
            right to assess a switching fee. If you return to the utility for service,
            then you may be charged a price other than the utility’s applicable tariff
            rate.
        </p>
        <p class="mt10" align="justify">
            Other than for operation, maintenance, assignment and transfer of your
            account, or for commercial collection, percentage of income payment plan
            aggregation, and governmental aggregation, UGP will not disclose your
            account number without your written consent or pursuant to a court order or
            Commission order. Other than for credit checking and credit reporting, UGP
            will not release your social security number without your written consent
            to do so or pursuant to a court order. Under this contract, you are
            allowing the utility to provide UGP information about your account,
            including meter readings and historical data. You have the right to request
            from the UGP, twice within a twelve-month period, up to twenty-four months
            of the customer's payment history without charge. By providing your contact
            information to UGP (name, address, telephone number, fax number, e-mail
            address, etc.), you acknowledge that you are consenting to be contacted by
            mail, telephone, fax, voicemail, or e-mail by UGP, a third party on behalf
            of UGP, or an associated company. Calls for new service are recorded in
            compliance with Commission guidelines.
        </p>
        <p class="mt10" align="justify">
            This contract is subject to present and future legislation, orders, rules,
            regulations and decisions of any duly constituted governmental authority
            having jurisdiction over this contract or the services to be provided
            hereunder. If at some future date there is a change in any law, rule,
            regulation or pricing structure whereby UGP is prevented, prohibited or
            frustrated from carrying out the terms of this contract, then at its sole
            discretion UGP shall have the right to cancel this contract on 15 days’
            notice to you. If an event occurs that delays or makes it impossible for
            UGP to perform, such as an act of God, extraordinary weather occurrence, a
            facility outage on the utility system or interstate pipeline systems, a
            failure to perform by the utility, war, civil disturbance, or national
            emergency, our performance under these terms and conditions shall be
            excused for the duration of the event. Under such conditions, UGP may elect
            to discontinue service immediately, without notice. UGP reserves the right
            to assign this contract to another natural gas supplier approved by the
            PUCO. UGP is not responsible for any losses or damages resulting from any
            actions or policies of, or associated with, the utility, including
            interruption of service, termination of service, defective service, or
            operation and maintenance of the utility’s system, nor is UGP responsible
            for damages sought because of in-home or building damage. The remedy in any
            claim by you against UGP will be solely limited to direct actual damages.
            All other remedies at law or in equity are hereby waived by you. In no
            event, will either UGP or you be liable for consequential, incidental,
            indirect, special, or punitive damages. These limitations apply without
            regard to the cause of any liability or damages. There are no third-party
            beneficiaries to this contract. UGP MAKES NO REPRESENTATIONS OR WARRANTIES
            OTHER THAN THOSE EXPRESSLY SET FORTH HEREIN, AND UGP EXPRESSLY DISCLAIMS
            ALL OTHER WARRANTIES, EXPRESS OR IMPLIED, INCLUDING, BUT NOT LIMITED TO,
            MERCHANTABILITY AND FITNESS FOR A PARTICULAR USE.
        </p>
    </body>

</html>