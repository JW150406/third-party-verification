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
        <p class="mt0" align="center">
            <strong>
            {{$electricData['utility_name']}}
            </strong>
        </p>
        <p class="mt10" align="center">
            <strong>Customer Choice Contract</strong>
        </p>

        <div class="contact-info mt10">
            <label>
                Account Holder or Legally Authorized Person:
                <span>{{implode(" ",array_filter(array($electricData['cust_first_name'],$electricData['cust_middle_initial'],$electricData['cust_last_name'])))}}</span>
            </label>
            <label>
                Address: <span>{{$electricData['service_addr_line_1']}} {{$electricData['service_addr_line_2']}}</span>
            </label>
            <div class="three-lable">
                <label style="width: 32%;">
                    City: <span>{{$electricData['service_addr_city']}}</span>
                </label>
                <label style="width: 32%;">
                    State: <span>{{$electricData['service_addr_state']}}</span>
                </label>
                <label style="width: 32%;">
                    Zip: <span>{{$electricData['service_addr_zipcode']}}</span>
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
                    Electric {{$electricData['act_num_verbiage' ?? 'Account Number']}} :
                    <span>{{$electricData['account_number']}}</span>
                </label>
                <label style="width: 28%;">
                    Date:
                    <span>{{$customer_info['date']}}</span>
                </label>
            </div>
        </div>

        <div class="row">
            <div class="col1" style="font-weight: bold;">
                Generation Service:
            </div>
            <div class="col2">
                My electricity rate will be fixed rate of <strong>${{$electricData['rate']}} / {{$electricData['unit']}}</strong>
                during the {{$electricData['term']}}-month contract.
            </div>
        </div>
        <div class="row">
            <div class="col1" style="font-weight: bold;">
                Term:
            </div>
            <div class="col2">
                The start date will begin with my successful enrollment into the Electric
                CHOICE Program with Utility Gas and Power (UGP) and will extend for a
                {{$electricData['term']}}-month period (the Initial Term).
            </div>
        </div>
        <div class="row">
            <div class="col1" style="font-weight: bold;">
                Renewal:
            </div>
            <div class="col2">
                After the initial Term, my contract with UGP will continue on a variable
                rate, month-to-month basis, cancellable at any time without penalty.
            </div>
        </div>
        <div class="row">
            <div class="col1" style="font-weight: bold;">
                Cancellation:
            </div>
            <div class="col2" style="font-weight: bold; font-style: italic;">
                I may cancel at any time within 30 days of the date of this contract
                with no penalty from UGP. Following the 30-day period, an early
                termination fee of $150 will apply.
            </div>
        </div>

        <p class="mt10">
            <strong>
                I acknowledge that I am the account holder or, Legally Authorized Person to execute a contract on behalf
                of the account holder. I understand that by
                signing this contract, I am switching the electric supplier for this
                account to Utility Gas and Power. I understand that electricity purchased
                for this account by Utility Gas and Power will be delivered through the
                EDU’s delivery system. The account holder, or the person who signed this
                contract on behalf of the account holder, has 30 days after today to cancel
                this contract for any reason through written or verbal notification to
                Utility Gas and Power.
            </strong>
        </p>

        <p class="mt10" style="vertical-align: middle;">
            Account Holder or Legally Authorized Signature: <img src="{{$customer_info['signature']}}" height="96px"
                width="320px" style="vertical-align: middle;">
        </p>

        <div class="contact-info mt10">
            <label>
                <strong> Name:</strong>
                <span>{{implode(" ",array_filter(array($electricData['contact_first_name'],$electricData['contact_middle_initial'],$electricData['contact_last_name'])))}}</span>
            </label>
        </div>

        <p class="mt10">
            If Legally Authorized, your relationship to the account holder:
            <span>{{$program_info['Relationship']}}</span>
        </p>
        <p class="mt10">
            For Agent use:
        </p>

        <div class="agent-info">
            <p> AGENT: <span>{{$salesAgents['name']}}</span></p>
            <p>AGENT Code: <span>{{$salesAgents['id']}}</span></p>
            <p>AGENT Phone: <span>{{$salesAgents['phone']}}</span></p>
        </div>
        <div class="page-break"></div>
        <p class="mt10">
            <em>
                <strong>
                    UTILITY GAS AND POWER(UGP) Residential Fixed Rate Electric CHOICE
                    Contract (Terms &amp; Conditions)
                </strong>
            </em>
        These Terms and Conditions (this “Agreement”) are your
        agreement for Generation Service with Utility Gas and Power. (“Utility Gas
        and Power”). Please keep a copy of this Agreement for your records. Utility
        Gas and Power is certified by the Public Utilities Commission of Ohio
        (“PUCO”) to offer and supply Generation Service in Ohio. As a Competitive
        Retail Electric Service (“CRES”) provider, Utility Gas and Power will
        supply the electric generation services to your Electric Distribution
        Utility (“EDU”) based on your usage. Your EDU then distributes or delivers
        the electricity to you. Your Distribution Service will remain with your
        current EDU, which is regulated by the PUCO. Your EDU also will continue to
        read your meter, provide your monthly bill and respond to emergencies.
        DEFINITIONS. “Competitive Retail Electric Service Provider” or “CRES”
        provider means, as defined by Chapter 4901:1-21 of the Substantive Rules
        applicable to electric service providers, an entity that sells electric
        energy to retail customers in Ohio. “Generation Service” means the
        production of electricity. “Generation-Related Charges” means those charges
        or costs associated with the production, procurement and supply of
        electricity. “Nonbypassable utility charges and fees” means those EDU
        charges and fees payable by you regardless of whether the EDU or a CRES
        provider provides Generation Service. “Transmission Services” means moving
        high voltage electricity from a generation facility to the distribution
        lines of an EDU, which is either bypassable or nonbypassable to you, as
        determined in accordance with your Distribution Service. “Distribution
        Service” means the physical delivery of electricity to customers by the
        EDU.
        </p>
        <p class="mt10">RIGHT OF RESCISSION. 
            Once you have been enrolled to receive Generation
            Service from Utility Gas and Power, your EDU will send you a confirmation
            letter. You have the right to rescind your enrollment without penalty
            within thirty (30) calendar days following the date of this contract or
            within 7 days of the postmark date of the confirmation letter by contacting
            your EDU, whichever is greater, and following the instructions contained in
            the letter. This right of rescission only applies when you initially switch
            to Utility Gas and Power and not upon renewal. Your EDU will not send a
            confirmation notice upon any renewal of this Agreement. In the event the
            EDU’s retail electric choice program is terminated or materially changed in
            any manner prior to the end of the initial “Term” (as listed above) or
            renewal thereof, Utility Gas and Power may terminate this Agreement,
            without penalty to either party. In the event a third party was involved in
            this Agreement, including, without limitation, a broker or a shopping
            website, or you are part of a municipal aggregation, the pricing contained
            herein may be inclusive of a broker fee. Price Comparison Qualification.
            Please be advised that the EDU’s standard offer rates generally change from
            time to time. Utility Gas and Power therefore does not provide any
            guarantee of savings in comparison to the EDU’s standard offer rates during
            the Term or the term of any renewals of this Agreement. If you received any
            price comparison(s) in connection with your enrollment, by accepting this
            offer from Utility Gas and Power, you understand and agree that Utility Gas
            and Power has informed you, prior to entering into this Agreement, that no
            guarantee of savings during the Term or the term of any renewals is being
            provided.
        </p>
        <p class="mt10">
            TERMS AND CONDITIONS OF SERVICE 1. Eligibility. Residential customer
            accounts that are on residential rates codes and are not enrolled in the
            Percentage of Income Plan Program (PIPP) are eligible for this offer from
            Utility Gas and Power. Utility Gas and Power reserves the right to refuse
            enrollment to any customer who is not current on their EDU charges.
        </p>
        <p class="mt10">
            2. Price. Starting with the first billing cycle of this Agreement through
            the last billing cycle of the initial Term, the price will be as stated
            above under the heading “Generation Service Charges.” During the term of
            this Agreement, you agree to pay Utility Gas and Power a price for all
            applicable combined Generation Service and Generation-Related Charges as
            specified in “Generation Service” listed above. You are responsible for,
            and your price does not include, applicable state and local taxes and/or
            EDU charges, which will be billed by the EDU. For the “Term” listed above,
            all kilowatt-hours (“kWh”) of electric energy metered by the EDU shall be
            billed at the rate per kWh specified above. In addition to Utility Gas and
            Power’s charges, you will be charged by your EDU for Distribution Service
            and other EDU charges and fees. An average residential customer, using 750
            kWh of electricity on a monthly basis, would incur approximately $35 to $40
            per month in such EDU charges and fees. In the event that any new, or any
            change in any existing, statute, rule, regulation, order, or other law, or
            procedure, tariff, rate class, or other process or charge, promulgated by
            any governmental authority or EDU, Independent System Operator, Regional
            Transmission Organization (“RTO”), such as PJM Interconnection, L.L.C.
            (“PJM”), or other regulated service provider, alters to the detriment of
            Utility Gas and Power its costs to perform or its economic returns under
            this Agreement (a “Change in Law or Regulation”), then Utility Gas and
            Power will provide written notice requesting your affirmative consent and
            agreement, describing the Change in Law or Regulation, the resulting price
            revisions, and the future date upon which such revised pricing is requested
            to be effective (a “Price Revision Request”). You then will be able to
            affirmatively consent and agree to such Price Revision Request, and if you
            agree, you will pay the revised price described in such Price Revision
            Request, and all other terms and conditions of this Agreement not modified
            by such Price Revision Request will remain in full force and effect. If,
            however, you do not affirmatively consent and agree to the Price Revision
            Request within thirty (30) calendar days, then Utility Gas and Power may
            terminate this Agreement without penalty, Cancellation Fee or further
            obligation (but you will remain responsible to pay Utility Gas and Power
            for any electricity supply used before this Agreement is terminated, as
            well as any late fees). Such termination will be effective on the next
            available drop date as established by the EDU. Any such Change in Law or
            Regulation may include, but is not limited to, implementation of changes to
            or adjustments in the implementation of PJM settlements or new or changed
            PJM and EDU charges for transmission, capacity and ancillary services, or
            generation adequacy rules, regulations implementing installed capacity
            obligations, emission allowance requirements, obligations associated with
            environmental or energy law and regulations (including, without limitation,
            alternative energy requirements, carbon and greenhouse gas, or other
            similar controls) or otherwise. Note that if, due to a change in market
            conditions, we wish to lower the price per kilowatt hour charged to you
            under this Agreement, we may do so without your consent, provided there are
            no other changes to the terms and conditions of this Agreement.
        </p>
        <p class="mt10">
            3. Term (Length of Agreement). Your service from Utility Gas and Power will
            begin with the next available meter-reading in the Term following: a) the
            thirty (30) day rescission period; b) the acceptance of the enrollment
            request by Utility Gas and Power (at its discretion and consistent with
            Section 7 below); and c) processing of the enrollment by your EDU, and will
            continue for the Term, unless otherwise terminated or renewed, ending on
            the meter read for the last month of service.
        </p>
        <p class="mt10">
            4. Billing. You will continue to receive a single bill, typically on a
            monthly basis, from your EDU that will contain both your EDU and Utility
            Gas and Power charges. If you do not pay your bill by the due date, Utility
            Gas and Power may cancel this Agreement after giving you a minimum of
            fourteen (14) calendar days’ written notice. Upon cancellation you will be
            returned to your EDU as a customer. You will remain responsible to pay
            Utility Gas and Power for any electricity used before this Agreement is
            cancelled as well as any late payment charges. Further, your failure to pay
            EDU charges may result in your electric service being disconnected in
            accordance with the EDU tariff.
        </p>
        <p class="mt10">
            5. Penalties, Fees and Exceptions. Your EDU may charge you a switching fee.
            If you do not pay the full amount owed to Utility Gas and Power by the due
            date of the bill, Utility Gas and Power may charge a late payment fee up to
            one and one-half percent (1.5%) of the outstanding balance per month, or
            the maximum legally allowed interest rate, whichever is lower until such
            payment is received by Utility Gas and Power. Utility Gas and Power will
            also charge a monthly service fee of $9.99. 6. Cancellation/Termination
            Provisions/Failure to Pay. If this Agreement is not rescinded during the
            rescission period, enrollment will be sent to your EDU. You have the right
            to terminate this contract without penalty if you move to a new location
            outside of your utility service area. You have the right to terminate this
            contract without penalty in the event the customer relocates outside the
            service territory of the incumbent electric provider or within the service
            territory of an electric provider that does not permit portability of the
            contract. You must notify UGP if you are moving to a new location outside
            of your utility service area in order to have your early termination charge
            credited back to you. However, UGP can provide service at the new location
            under a new contract if you notify UGP at the time of your move and
            establish service with the utility at the new location with UGP as your
            service provider. Termination typically is effective with the next full
            utility billing cycle that occurs after the utility is notified of the
            request to terminate service. You are responsible for all fees and charges
            until your service is terminated. If you change to another electric
            provider (including the utility), the utility may assess a switching fee
            under its tariff. If you return to the utility for service, then you may be
            charged a price other than the utility’s applicable tariff rate. Any
            failure to pay your bill shall be deemed a breach of this Agreement
            permitting Utility Gas and Power to terminate this Agreement upon fourteen
            (14) calendar days’ advance written notice. There will be a charge as
            specified in “Cancellation Fee” above if you terminate this Agreement for
            any other reason, except as expressly provided herein, or breach this
            Agreement in accordance with the preceding sentence. In addition to any
            applicable Cancellation Fee, you will remain responsible to pay Utility Gas
            and Power for any electricity supply used before this Agreement is
            cancelled or terminated for any reason, as well as any late fees. Should
            you cancel service with Utility Gas and Power and return to standard offer
            service with your EDU, you may not be served under the same rates, terms,
            and conditions that apply to other EDU customers. UGP does not offer budget
            billing.
        </p>
        <p class="mt10">
            7. Your Consent and Information Release Authorization. By accepting this
            offer from Utility Gas and Power, you understand and agree to the terms and
            conditions of this Agreement with Utility Gas and Power. You authorize
            Utility Gas and Power to obtain information from the EDU that includes, but
            is not limited to: your billing history, payment history, historical and
            expected electricity usage, meter-readings, and characteristics of
            electricity service. Utility Gas and Power reserves the right to check your
            credit with a consumer credit reporting agency to determine if your credit
            standing is satisfactory before accepting your enrollment request. This
            Agreement shall be considered executed by Utility Gas and Power following:
            a) acceptance of your enrollment request by Utility Gas and Power; b) the
            end of the thirty (30) day rescission period; and c) acceptance of
            enrollment by your EDU. You have the right to request from the UGP, twice
            within a twelve-month period, up to twenty-four months of the customer's
            payment history without charge.
        </p>
        <p class="mt10">
            8
            <strong>
                . Contract Renewal. Upon expiration of the initial Term and unless
                Utility Gas and Power renews for a set term in accordance with the
                following sentence, this Agreement will automatically renew on a
                month-to-month basis at a Variable price per kWh, based upon the
                applicable RTO prevailing market and business conditions for
                electricity at the EDU load zone or equivalent market delivery point.
                Pursuant to PUCO Case No. 14-568-EL-COI and PUCO guidelines, the retail
                electric product in any such month-to-month periods is defined as
                “Variable”. Notwithstanding the preceding sentence, if Utility Gas and
                Power chooses to renew this Agreement for a set term, then Utility Gas
                and Power will send advance written notice not less than forty-five
                (45) calendar days prior to the end of the Initial Term. This Agreement
                shall be automatically renewed on a month-to-month variable rate,
                cancellable at any time with written or verbal communication from you
                to Utility Gas and Power. Pricing excludes taxes, Distribution Service
                charges, Transmission Service, and other non-bypassable EDU charges and
                fees. No cancellation fee will apply during a month-to-month term, as
                specified in the renewal notice. At any time, you may contact Utility
                Gas and Power to enroll in a then-current plan.
            </strong>
        </p>
        <p class="mt10">
            9. Dispute Procedures. Contact Utility Gas and Power with any questions
            concerning the terms of service by phone at 1-855-747-4931 (toll-free) M-F
            8AM – 5PM EST or in writing at Utility Gas and Power, 2680 Corporate Park
            Dr Ste 100 Opelika, AL 36801. Our web address is utilitygasandpower.com. If
            your complaint is not resolved after you have called Utility Gas and Power
            and/or your EDU, or for general utility information, you may contact the
            Public Utilities Commission of Ohio for assistance at 1-800-686- 7826 (toll
            free) or TTY at 1-800-686-1570 (toll free) from 8:00 AM - 5:00 PM EST
            weekdays or at www.PUCO.ohio.gov. Hearing or speech impaired customers may
            contact the PUCO via 7-1-1 (Ohio relay service). The Ohio consumers’
            counsel (OCC) represents residential utility customers in matters before
            the PUCO. The OCC can be contacted at 1-877-742-5622 (toll free) from eight
            a.m. to five p.m. weekdays, or at http://www.pickocc.org.
        </p>
        <p class="mt10">
            10. Miscellaneous. Utility Gas and Power is prohibited from disclosing your
            social security number and/or account number(s) without your affirmative
            written consent except for Utility Gas and Power’s collections and
            reporting, participating in programs funded by the universal service fund
            pursuant to section 4928.54 of the Ohio Revised Code, or assigning your
            contract to another CRES provider. Utility Gas and Power assumes no
            responsibility or liability for the following items that are the
            responsibility of the EDU: operation and maintenance of the EDU’s
            electrical system, any interruption of service, termination of service, or
            deterioration of the EDU’s service. In the event of a power outage, you
            should contact your local EDU. You are responsible for providing Utility
            Gas and Power with accurate account information Any notice, demand or other
            communication to be given hereunder, including, without limitation, any
            renewal or termination notice, shall be in writing and delivered to the
            address or email address maintained on file for you. By entering this
            Agreement, you represent and agree that the account served by Utility Gas
            and Power under this Agreement is a residential account, in the EDU’s
            service territory, and you are not an existing Utility Gas and Power
            customer. Utility Gas and Power reserves the right, at any time, to not
            enroll or to terminate service to customer locations that do not meet the
            preceding criteria and return you to the EDU (or previous Utility Gas and
            Power product, if applicable) with no penalty to Utility Gas and Power.
            Utility Gas and Power’s environmental disclosure statement is available for
            viewing on our website at utilitygasandpower.com. You agree that Utility
            Gas and Power will make the required quarterly updates to the statement
            electronically on our website. We will also provide the information to you
            upon request.
        </p>
        <p class="mt10">
            11. Warranty and Force Majeure. Utility Gas and Power warrants title and
            the right to all electricity sold hereunder. THE WARRANTIES SET FORTH IN
            THIS PARAGRAPH ARE EXCLUSIVE AND ARE IN LIEU OF ALL OTHER WARRANTIES,
            WHETHER STATUTORY, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO ANY
            WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE OR ARISING
            OUT OF ANY COURSE OF DEALING OR PURPOSE OR USAGE OF TRADE. Utility Gas and
            Power will make commercially reasonable efforts to provide your electric
            service, but does not guarantee a continuous supply of electricity. Certain
            causes and events are out of the reasonable control of Utility Gas and
            Power and may result in interruptions in service. Utility Gas and Power is
            not liable for damages caused by acts of God, changes in laws, rules or
            regulations or other acts of any governmental authority (including the PUCO
            or RTO), accidents, strikes, labor troubles, required maintenance work,
            inability to access the local distribution utility system, nonperformance
            by the EDU or any other cause beyond Utility Gas and Power’s reasonable
            control.
        </p>
        <p class="mt10">
            12. REMEDIES. UNLESS OTHERWISE EXPRESSLY PROVIDED HEREIN, ANY LIABILITY
            UNDER THIS AGREEMENT WILL BE LIMITED TO DIRECT, ACTUAL DAMAGES AS THE SOLE
            AND EXCLUSIVE REMEDY, AND ALL OTHER REMEDIES OR DAMAGES AT LAW OR IN EQUITY
            ARE WAIVED. NEITHER PARTY WILL BE LIABLE TO THE OTHER PARTY OR ITS
            AFFILIATES FOR CONSEQUENTIAL, INCIDENTAL, PUNITIVE, EXEMPLARY OR INDIRECT
            DAMAGES, INCLUDING LOST PROFITS OR OTHER BUSINESS INTERRUPTION DAMAGES,
            WHETHER IN TORT OR CONTRACT, UNDER ANY INDEMNITY PROVISIONS OR OTHERWISE IN
            CONNECTION WITH THIS AGREEMENT. THE LIMITATIONS IMPOSED ON REMEDIES AND
            DAMAGE MEASUREMENT WILL BE WITHOUT REGARD TO CAUSE, INCLUDING NEGLIGENCE OF
            ANY PARTY, WHETHER SOLE, JOINT, CONCURRENT, ACTIVE OR PASSIVE; PROVIDED NO
            SUCH LIMITATION SHALL APPLY TO DAMAGES RESULTING FROM THE WILLFUL
            MISCONDUCT OF ANY PARTY.
        </p>
        <p class="mt10">
            13. Your Liability and Indemnification of Utility Gas and Power. You assume
            full responsibility for retail electricity furnished to you at the delivery
            point(s) and on your side of the delivery point(s), and agree to and shall
            indemnify, defend, and hold harmless Utility Gas and Power, its parent
            company and all of its affiliates, and all of their respective managers,
            members, officers, directors, shareholders, associates, employees,
            servants, and agents from and against all claims, losses, expenses,
            damages, demands, judgments, causes of action, and suits of any kind
            (hereinafter collectively referred to as “Claims”), including Claims for
            personal injury, death, or damages to property occurring at the delivery
            point(s) or on your side of the delivery point and upon the premise(s),
            arising out of or related to the electricity and/or your performance under
            this Agreement.
        </p>
        <p class="mt10">
            14. Assignment. You shall not assign this Agreement or its rights hereunder
            without the prior written consent of Utility Gas and Power. Utility Gas and
            Power may, without your consent, assign this Agreement to another CRES
            provider, including any successor, in accordance with the rules and
            regulations of the PUCO.
        </p>
        <p class="mt10">
            15. Choice of Law. This Agreement shall be construed and enforced in
            accordance with the laws of the State of Ohio without giving effect to any
            conflicts of law principles which otherwise might be applicable.
        </p>
        <p class="mt10">
            16. Contact Information. Utility Gas and Power, 2680 Corporate Park Dr
            Suite 100, Opelika, AL 36801. For more information call (855) 747-4931 M-F
            8AM – 5PM EST or visit utilitygasandpower.com.
        </p>
    </body>

</html>