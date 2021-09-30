<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
    }

    
	
	table {
	  font-family: Arial, Helvetica, sans-serif;
	  border-collapse: collapse;
	  width: 100%;
	}

	td{
	  border: 1px solid #ddd;
	  padding: 8px;
	}
	.container{
		padding:40px;
	}

</style>

<body>
	<div class="container">
		<div>
			<p><b>SCHEDULE A TO TERMS AND CONDITIONS</b></p>
			<p>ELECTRIC CONTRACT SUMMARY</p>
			<p>Residential and Small Commercial Electric Generation Supply service in Pennsylvania</p>
		</div>
		<div>
			<table>
				<tr>
					<td style="background:#f2f2f2;width:30%;"><b>ELECTRICGENERATION SUPPLIER INFORMATION</b></td>
					<td style="font-size:14px;"><p>Capital EnergyPALLCdba SunrisePower & Gas (Sunrise)</p>
						<p>1770 St. JamesPlace, Suite606, Houston, TX 77056</p>
						<p>Telephone No.:1-888-538-7001 (M-F9am-6pm ET)</p>
						<p>www.sunriseenergy.com</p>
						<p>Sunrise is responsible for the electric generation service charges</p>
					</td>
				</tr>
				<tr>
					<td style="background:#f2f2f2;"><b>PRICE STRUCTURE</b></td>
					<td>During the Contract Term, the rate for electric generation service per KWH is fixed.

					</td>
				</tr>
				<tr>
					<td style="background:#f2f2f2;"><b>GENERATION SUPPLYRATE</b></td>
					<td>{{$electricData['rate']}} <span>&#162;</span> per {{$electricData['unit']}}.
					</td>
				</tr>
				<tr>
					<td style="background:#f2f2f2;"><b>STATEMENT REGARDING SAVINGS</b></td>
					<td>Sunrise does not guarantee any savings during the Contact Duration
					</td>
				</tr>
				<tr>
					<td style="background:#f2f2f2;"><b>INCENTIVES</b></td>
					<td>Residential customers who have signed up for a plan with the Sunrise Power & Gas Rewards Program
						will have the ability to earn discount dollars for use on local deals, popular resturants,online
						shopping,movie tickets, and more. Details for the Program are available at
						www.sunrisepowerandgas.com/rewards
					</td>
				</tr>
				<tr>
					<td style="background:#f2f2f2;"><b>CONTRACT START DATE</b></td>
					<td>The Contract Term of this Agreement will start on the next available meter read date after your electric
						generation supplier is changed to Sunrise by your EDC.
					</td>
				</tr>
				<tr>
					<td style="background:#f2f2f2;"><b>CONTRACT DURATION/LENGTH</b></td>
					<td>{{$electricData['term']}} monthly billing cycles.

					</td>
				</tr>
				<tr>
					<td style="background:#f2f2f2;"><b>CANCELLATION/EARLY TERMINATION FEES</b></td>
					<td>You can avoid ${{$electricData['etf']}} early termination fee and the incentive recovery fee, if any,by completing the Contract Term.
					</td>
				</tr>
				<tr>
					<td style="background:#f2f2f2;"><b>END OF CONTRACT</b></td>
					<td>Prior to the expiration date of the Contract Term, you will receive two separate notifications explaining
						the proposed changes to the terms of service and your options going forward.The first notification will
						be provided no more than 60 days and no less than 45 days in advance of the expiration of theContract
						Duration.The second notification will be provided at least 30 days in advance. If you find the change(s)
						unacceptable, you may choose another supplier or return to EDC service without any penalty to you. If
						you do not respond to thenotifications, your service with Sunrise will continueunder thenew terms and
						the Agreement, as amended, will automatically renew on a month-to-month contract, either at same
						terms and conditions or at revised terms and conditionsor to anotherfixed duration contract as set forth
						in the notifications with no early cancellation fee. To the extent that you purchased our Renewable
						EnergyPlan at the time of enrollment,during a Renewal Period, the product you purchase from Sunrise
						will notbeaRenewableEnergyproductunless explicitlystated in theSecond Options Notification.
					</td>
				</tr>
				<tr>
					<td style="background:#f2f2f2;"><b>ELECTRIC DISTRIBUTION COMPANY CONTACT INFORMATION</b></td>
					<td>Your EDC is responsible fordistribution charges. In cases of emergencies relating to your service,such as
						a power outage, please call your EDC: Pennsylvania Power & Light Company at 1-800-342-5775,
						Philadelphia ElectricCompanyat 1-800-841-4141, West Penn Power at 1-888-544-4877, Metropolitan
						Edison Company 1-888-544-4877, Pennsylvania Electric Company at 1-888-544-4877, Pennsylvania
						PowerCompanyat1-888-544-4877, or DuquesneLight at1-888-393-7000.
					</td>
				</tr>
				<tr>
					<td><b>RIGHT TO RECISSION</b></td>
					<td>You have three (3) business days after you receive a written copy of your Agreement to rescind your
						enrollment with Sunrise by submitting a notice of cancellaton via mail to: 1770 St. James Place, Suite
						606, Houston, TX 77056, bycalling us at1-888-538-7001, or email us atCare@sunriseenergy.com
					</td>
				</tr>
				
				
			</table>
		</div>
	</div>
</body>