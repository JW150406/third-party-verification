<?php

namespace App\Traits;

use Log;
use App\models\Telesales;
use App\models\Client;
use App\models\Programs;
use App\User;
use Carbon\Carbon;
use Storage;
use App\models\Salescenter;
use App\models\Commodity;
use App\models\Telesalesdata;
use DB;
use App\models\Clientsforms;
use App\models\Salescenterslocations;
use Auth;
use App\models\UserLocation;

trait DashboardTrait {

	/**
	 * This method is used to Returns conversion Rate
	 * @param $verifiedLeadCount, $totalCounts
	 */
	public function getConversionRatePercentage($verifiedLeadCount, $totalCounts) {
		try {
			
			$percentage = ($verifiedLeadCount / $totalCounts) * 100;
			return number_format($percentage, 2, '.', '');
		} catch (\Exception $e) {
			\Log::error("Error while calculating conversion rate: " . $e->getMessage());
			return 0;
		}
	}

	/**
	 * For dashboard visual 1
	 * @param $startDate, $endDate, $clientId, $salesCenterId, $locationId, $brand
	 */
	public function getConversionRateData($startDate,$endDate,$clientId,$salesCenterId = "",$locationId="",$brand)
	{
		// Get all the leads except pending and expired from telesales table
		$totalCounts = Telesales::getLeadsByRange($startDate, $endDate)
			->getLeadsByClientId($clientId);

	//for conversion rate total count
	$totalCountsForConversionRate = Telesales::getLeadsByRange($startDate, $endDate)
		->where('status', '!=', 'pending')
		->where('status', '!=', 'expired')
		->getLeadsByClientId($clientId);
		
		$verifiedLeadCount['count'] = Telesales::getLeadsByRange($startDate, $endDate)
			->getLeadByStatus(['verified'])
			->getLeadsByClientId($clientId);
			if($brand != ''){
				$totalCounts = $totalCounts->getLeadsByBrand($brand);
				$verifiedLeadCount['count'] = $verifiedLeadCount['count']->getLeadsByBrand($brand);
			}
			
		if($locationId != '')
		{			
		    if (is_array($locationId)) {
				$totalCounts = $totalCounts->getLeadsBySalesCenter($salesCenterId)->getLeadsByMultipleLocation($locationId)->count();
				$totalCountsForConversionRate = $totalCountsForConversionRate->getLeadsBySalesCenter($salesCenterId)->getLeadsByMultipleLocation($locationId)->count();
                $verifiedLeadCount['count'] = $verifiedLeadCount['count']->getLeadsBySalesCenter($salesCenterId)->getLeadsByMultipleLocation($locationId)->count();
            } else {
                if($locationId == "all")
                {
					$totalCounts = $totalCounts->getLeadsBySalesCenter($salesCenterId)->count();
					$totalCountsForConversionRate = $totalCountsForConversionRate->getLeadsBySalesCenter($salesCenterId)->count();
                    $verifiedLeadCount['count'] = $verifiedLeadCount['count']->getLeadsBySalesCenter($salesCenterId)->count();
                }
                else
                {
					$totalCounts = $totalCounts->getLeadsBySalesCenter($salesCenterId)->getLeadsBySCLocation($locationId)->count();
					$totalCountsForConversionRate = $totalCountsForConversionRate->getLeadsBySalesCenter($salesCenterId)->getLeadsBySCLocation($locationId)->count();
                    $verifiedLeadCount['count'] = $verifiedLeadCount['count']->getLeadsBySalesCenter($salesCenterId)->getLeadsBySCLocation($locationId)->count();
                }
            }
		}
		else if($salesCenterId != "")
		{
			$totalCounts = $totalCounts->getTotalLeadCountBySalesCenter($salesCenterId);	
			$totalCountsForConversionRate = $totalCountsForConversionRate->getTotalLeadCountBySalesCenter($salesCenterId);	
			$verifiedLeadCount['count'] = $verifiedLeadCount['count']->getTotalLeadCountBySalesCenter($salesCenterId);
		}
		else
		{
			$totalCounts = $totalCounts->count();
			$totalCountsForConversionRate = $totalCountsForConversionRate->count();
			$verifiedLeadCount['count'] = $verifiedLeadCount['count']->count();
		}

		if($totalCounts != 0)
		{
			$average = $this->getConversionRatePercentage($verifiedLeadCount['count'], $totalCountsForConversionRate);
		}
		else {
			$average = 0;
		}

		$verifiedLeadCount['count'] = $totalCounts;
		$verifiedLeadCount['percentage'] = $average;
		
		return $verifiedLeadCount;
	}

	/**
	 * For get lead data for line chart as oer parameter values
	 * @param $startDate, $endDate, $clientId, $brand, $salesCenterId, $locationId, $monthYear
	 */
	public function getLeadsForLineChart($startDate,$endDate,$clientId,$brand,$salesCenterId = "",$locationId="",$monthYear="year")
	{
		$timezone = Auth::user()->timezone;
		$date = date_create(Carbon::now(), timezone_open($timezone));
		$timezoneFormatter = date_format($date, 'P');
		$conversionRate = [];
		$startYear = Carbon::parse($startDate)->format('Y');
		$endYear = Carbon::parse($endDate)->format('Y');
		$startMonth = Carbon::parse($startDate)->format('m');
		$endMonth = Carbon::parse($endDate)->format('m');
		$totalCounts = Telesales::getLeadsByRange($startDate,$endDate)->getLeadsByClientId($clientId)->getLeadsByBrand($brand);
		$verifiedLeadCount = Telesales::getLeadsByRange($startDate,$endDate)->getLeadByStatus(['verified'])
				->getLeadsByClientId($clientId)->getLeadsByBrand($brand);
				
		if($locationId != '')
		{
			if($locationId == 'all')
			{
				$totalCounts = $totalCounts->getLeadsBySalesCenter($salesCenterId);
				$verifiedLeadCount = $verifiedLeadCount->getLeadsBySalesCenter($salesCenterId);
			}
			else
			{
				$totalCounts = $totalCounts->getLeadsBySCLocation($locationId);
				$verifiedLeadCount = $verifiedLeadCount->getLeadsBySCLocation($locationId);
			}
		}
		if($monthYear == "year")
		{	
			$totalCounts = $totalCounts->select(DB::raw('count(telesales.id) as count,date_format(CONVERT_TZ(telesales.created_at,"+00:00","'.$timezoneFormatter.'"),"%m") as month,YEAR(telesales.created_at) as year'),DB::raw("(date_format(CONVERT_TZ(telesales.created_at,'+00:00','".$timezoneFormatter."'),'%d'))as day"))->groupBy('year')
			->having('year','>=',$startYear)
			->having('year','<=',$endYear)
			->get()->toArray();
			
			$verifiedLeadCount = $verifiedLeadCount->select(DB::raw('count(telesales.id) as count,date_format(CONVERT_TZ(telesales.created_at,"+00:00","'.$timezoneFormatter.'"),"%m") as month,YEAR(telesales.created_at) as year'),DB::raw("(date_format(CONVERT_TZ(telesales.created_at,'+00:00','".$timezoneFormatter."'),'%d'))as day"))->groupBy('year')
				->having('year','>=',$startYear)
				->having('year','<=',$endYear)
				->get()->toArray();
		}
		else if($monthYear == 'month')
		{
			$totalCounts = $totalCounts->select(DB::raw('count(CASE WHEN telesales.id is null THEN 0 ELSE telesales.id END) as count, date_format(CONVERT_TZ(telesales.created_at,"+00:00","'.$timezoneFormatter.'"),"%m") as month,YEAR(telesales.created_at) as year'),DB::raw("(date_format(CONVERT_TZ(telesales.created_at,'+00:00','".$timezoneFormatter."'),'%d'))as day"))->groupBy('month','year')
			->orderBy('year')
			->get()->toArray();

			$verifiedLeadCount = $verifiedLeadCount->select(DB::raw('count(CASE WHEN telesales.id is null THEN 0 ELSE telesales.id END) as count'),DB::raw('date_format(CONVERT_TZ(telesales.created_at,"+00:00","'.$timezoneFormatter.'"),"%m") as month,YEAR(telesales.created_at) as year'),DB::raw("(date_format(CONVERT_TZ(telesales.created_at,'+00:00','".$timezoneFormatter."'),'%d'))as day"))->groupBy('month','year')
			->orderBy('year')			
			->get()->toArray();	
		}
		else
		{
			$totalCounts = $totalCounts->select(
				DB::raw('(CASE WHEN count(telesales.id) = 0 THEN 0 ELSE count(telesales.id) END) as count'),
				DB::raw('date_format(CONVERT_TZ(telesales.created_at,"+00:00","'.$timezoneFormatter.'"),"%m") as month,YEAR(telesales.created_at) as year'),DB::raw("(date_format(CONVERT_TZ(telesales.created_at,'+00:00','".$timezoneFormatter."'),'%d'))as day"))
				->groupBy('day','month','year')->get()->toArray();
			$verifiedLeadCount = $verifiedLeadCount->getLeadsByRange($startDate,$endDate)->select(DB::raw('(CASE WHEN count(telesales.id) = 0 THEN 0 ELSE count(telesales.id) END) as count'),DB::raw('date_format(CONVERT_TZ(telesales.created_at,"+00:00","'.$timezoneFormatter.'"),"%m") as month,YEAR(telesales.created_at) as year'),DB::raw("(date_format(CONVERT_TZ(telesales.created_at,'+00:00','".$timezoneFormatter."'),'%d')) as day"))
			->groupBy('day','month','year')
			->get()->toArray();			
		}
		$startMonth = Carbon::parse($startDate,$timezone)->format('m');
		$endMonth = Carbon::parse($endDate,$timezone)->format('m');
		$noday = Carbon::parse($startDate,$timezone)->diffInDays(Carbon::parse($endDate,$timezone));
		$noMonth = Carbon::parse($startDate,$timezone)->diffInMonths(Carbon::parse($endDate,$timezone));
		$noYear = Carbon::parse($startDate,$timezone)->diffInYears(Carbon::parse($endDate,$timezone));
		$totalLeadCounts = [];
			if($monthYear == 'day'){
				for($i = 0; $i < $noday;$i++)
				{
					// dd(Carbon::parse($startDate,Auth::user()->timezone));
					$totalLeadCounts[Carbon::parse($startDate)->addDays($i)->setTimezone($timezone)->format('d-m-Y')] = 0;
				}
			}
			else if($monthYear == 'month'){
				for($i = 0; $i<= $noMonth;$i++)
				{
					$totalLeadCounts[Carbon::parse($startDate)->addMonths($i)->setTimezone($timezone)->format('m-Y')] = 0;
				}
			}
			else if($monthYear == 'year'){
				for($i = 0; $i <= $noYear;$i++)
				{
					$totalLeadCounts[Carbon::parse($startDate)->addMonths($i)->setTimezone($timezone)->format('Y')] = 0;
				}
			}
			$verifiedLeadCounts = $totalLeadCounts;
		foreach($totalCounts as $key => $val)
		{
			if($monthYear == 'day')
			{
				$totalLeadCounts[$val['day'].'-'.$val['month'].'-'.$val['year']] = $val['count'];
				if(isset($verifiedLeadCount[$key]['month']))
				{
					$verifiedLeadCounts[$verifiedLeadCount[$key]['day'].'-'.$verifiedLeadCount[$key]['month'].'-'.$verifiedLeadCount[$key]['year']] = $verifiedLeadCount[$key]['count'];
				}
			}
			
			else if($monthYear == 'month')
			{
				$totalLeadCounts[$val['month'].'-'.$val['year']] = $val['count'];
				if(isset($verifiedLeadCount[$key]['month']))
				{
					$verifiedLeadCounts[$verifiedLeadCount[$key]['month'].'-'.$verifiedLeadCount[$key]['year']] = $verifiedLeadCount[$key]['count'];
				}
			}
			else
			{
				$totalLeadCounts[$val['year']] = $val['count'];
				if(isset($verifiedLeadCount[$key]['year']))
				{
					$verifiedLeadCounts[$verifiedLeadCount[$key]['year']] = $verifiedLeadCount[$key]['count'];
				}
			}
		}
		
		$data['leads'] = $totalLeadCounts;
		$data['verified'] = $verifiedLeadCounts;
		return $data;
	}

	/**
	 * This method is used to get logo of selected client
	 * @param $clientId, $salesCenterId
	 */
	public function getClientLogoData($clientId="",$salesCenterId="")
	{
		
		if($clientId != "")
		{
			$clientLogo = Client::where('id',$clientId)->select('logo')->first();
			if (array_get($clientLogo, 'logo') && Storage::disk('s3')->exists(config()->get('constants.aws_folder') . $clientLogo->logo)) {
				$logo = Storage::disk('s3')->url($clientLogo->logo);
			}
			else
			{
				$logo = asset("images/PlaceholderLogo.png");
			}
		}
		if($salesCenterId != "")
		{
			$salesCenterLogo = Salescenter::where('id',$salesCenterId)->select('logo')->first();
			if (array_get($salesCenterLogo, 'logo') && Storage::disk('s3')->exists(config()->get('constants.aws_folder') . $salesCenterLogo->logo)) {
				$logo = Storage::disk('s3')->url($salesCenterLogo->logo);
			}
			else
			{
				$logo = asset("images/PlaceholderLogo.png");
			}
		}
        
        return $logo;
	}

  /**
   * Return sales center with total leads (Visual - 2 donut chart)
   * @param $clientId, $fromDate, $toDate, $brand, $salesCenterId
   */
  public function getSalesCentersWithTotalLeads($clientId, $fromDate, $toDate,$brand,$salesCenterId = "") {
	$salesCenters = Salescenter::select('salescenters.id', 'salescenters.name')->where('client_id', $clientId);
    if ($salesCenterId != "") {
      $salesCenters = $salesCenters->where('id', $salesCenterId);
    }
    $salesCenters = $salesCenters->get();
    $data = [];
    foreach ($salesCenters as $salesCenter) {
      $salesCenterArr = [];
      $salesCenterArr['name'] = array_get($salesCenter, 'name') . "-" . array_get($salesCenter, 'id');
      $salesCenterArr['value'] = Telesales::getLeadsByClientId($clientId)->getLeadsByRange($fromDate, $toDate)->getLeadsByBrand($brand)->getTotalLeadCountBySalesCenter($salesCenter->id);
		if ($salesCenterArr['value'] <= 0) {
			continue;
		}

      $data[] = $salesCenterArr;
    }

    return $data;
  }

  /**
   * This method is used to Return lead count with their status by Sales center id
   * For Visual - 2 -> Tool tip pie chart use with $salesCenterId
   * For Visual -3 -> Pie chart use without $salesCenterId
   * @param $clientId, $fromDate, $toDate, $brand, $salesCenterId, $agentId, $locationId
   */
  public function getLeadStatusCount($clientId, $fromDate, $toDate,$brand="", $salesCenterId = "", $agentId = "", $locationId = "") {

	$leads = [];
	

    $dashboardLeadCategories = config()->get('constants.DASHBOARD_LEAD_CATEGORIES');

    foreach ($dashboardLeadCategories as $key => $category) {
      $leadsArr = [];
      $leadsArr['name'] = $category;

			$query = Telesales::getLeadByStatus($this->retrieveLeadStatus($key))->getLeadsByRange($fromDate, $toDate)->getLeadsByClientId($clientId);

			if ($salesCenterId != "") {
				if($locationId == '')
					$query->getLeadsBySalesCenter($salesCenterId);
            }

			if ($agentId != "") {
				$query->getLeadByAgentId($agentId);
			}
			if($brand != '' )
				$query->getLeadsByBrand($brand);
			
            if ($locationId != "") {
				if($locationId != "all")
				{
                	$query->getLeadsBySCLocation($locationId);
				}
				else
				{
                	$query->getLeadsBySalesCenter($salesCenterId);
				}
            }

	    $leadsArr['value'] = $query->count();

			if ($leadsArr['value'] <= 0) {
				continue;
			}

      $leads[] = $leadsArr;
    }
    return $leads;
  }

  /**
   * For Returns Lead status count by channel
   * For Visual - 4
   * @param $clientId, $fromDate, $toDate, $brand, $salesCenterId,$locationId
   */
  public function getLeadsCountWithStatusByChannel($clientId, $fromDate, $toDate,$brand,$salesCenterId = "",$locationId="") {
    $leads = [];
    $dashboardLeadCategories = config()->get('constants.DASHBOARD_LEAD_CATEGORIES');

    foreach ($dashboardLeadCategories as $key => $category) {
      $leadsArr = [];
	  $leadsArr['d2d'] = Telesales::getLeadByStatus($this->retrieveLeadStatus($key))->getLeadsByRange($fromDate, $toDate)->getLeadsByBrand($brand)->getLeadsByClientId($clientId)->getSaleByAgentTypes('d2d');
	 if($locationId !="") 
	 {
		 if($locationId == "all")
		 {
			$leadsArr['d2d'] = $leadsArr['d2d']->getLeadsBySalesCenter($salesCenterId);
		 }
		 else
		 $leadsArr['d2d'] = $leadsArr['d2d']->getLeadsBySCLocation($locationId);
	 }
	 $leadsArr['d2d'] = $leadsArr['d2d']->count();
	  $leadsArr['tele'] = Telesales::getLeadByStatus($this->retrieveLeadStatus($key))->getLeadsByRange($fromDate, $toDate)->getLeadsByBrand($brand)->getLeadsByClientId($clientId)->getSaleByAgentTypes('tele');

	  if($locationId !="") 
	 {
		 if($locationId == "all")
		 {
			 
			$leadsArr['tele'] = $leadsArr['tele']->getLeadsBySalesCenter($salesCenterId);
		 }
		 else
		 $leadsArr['tele'] = $leadsArr['tele']->getLeadsBySCLocation($locationId);
	 }
	 $leadsArr['tele'] = $leadsArr['tele']->count();

			// if ($leadsArr['d2d'] <= 0 && $leadsArr['tele'] <= 0) {
			// 	continue;
			// }
			$leads[] = $leadsArr;
    }
    return $leads;
	}

  /**
   * This method is used to get salesCenter by channel
   * @param $clientId, $fromDate, $toDate, $brand, $channel, $salesCenterId, $locationId
   */
  public function getSalesCentersByChannel($clientId, $fromDate, $toDate,$brand,$channel,$salesCenterId="",$locationId="") {
	  
	$salesCenters = User::withTrashed()->leftjoin('salescenters','salescenters.id','=','users.salescenter_id')
	->leftjoin('salesagent_detail','salesagent_detail.user_id','=','users.id')
	->where('salesagent_detail.agent_type',$channel)->select('salesagent_detail.agent_type','salescenters.name','salescenters.id')->where('salescenters.client_id',$clientId)->get()->unique();
	
	if($locationId != "")
	{
		$salesCenters =  Salescenterslocations::select('id', 'name')->where('client_id', $clientId)->where('salescenter_id',$salesCenterId);

		if($locationId == "all")
		{
			$salesCenters = $salesCenters->get();
		}
		else
		{
			$salesCenters = $salesCenters->where('id',$locationId)->get();
		}
	}
	$data = [];
	$dashboardLeadCategories = config()->get('constants.DASHBOARD_LEAD_CATEGORIES');
	$salesCenterArr = [];
    foreach ($salesCenters as $salesCenter) {
		foreach($dashboardLeadCategories as $key => $category) {
			
			$salesCenterArr[array_get($salesCenter, 'name')][$category] = Telesales::getLeadByStatus($this->retrieveLeadStatus($key))->getLeadsByRange($fromDate, $toDate)->getLeadsByBrand($brand)->getLeadsByClientId($clientId)->getSaleByAgentTypes($channel);
			
			if($locationId != "")
			{
				$salesCenterArr[array_get($salesCenter, 'name')][$category] = $salesCenterArr[array_get($salesCenter, 'name')][$category]->getLeadsBySCLocation($salesCenter->id)->count();
			}
			else
			{
				$salesCenterArr[array_get($salesCenter, 'name')][$category] = $salesCenterArr[array_get($salesCenter, 'name')][$category]->getTotalLeadCountBySalesCenter($salesCenter->id);
			}
		
			if ($salesCenterArr[array_get($salesCenter, 'name')][$category] == 0) {
				unset($salesCenterArr[array_get($salesCenter, 'name')][$category]);

			}
		}
		if(count($salesCenterArr[array_get($salesCenter, 'name')]) <= 0)
		{
			unset($salesCenterArr[array_get($salesCenter, 'name')]);
		}
	}
	
    return $salesCenterArr;
  }

  	/**
	 * For Returns Lead status count by commodity
	 * Visual - 5 - Horizontal Bar chart
	 * @param $clientId, $fromDate, $toDate, $brand, $salesCenterId, $locationId 
	 */
	public function getLeadsCountWithStatusByCommodity($clientId, $fromDate, $toDate,$brand,$salesCenterId="",$locationId="") {
		try {
			$salesCenterArr = [];
			
			$salesCenters = Salescenter::select('id', 'name')->where('client_id', $clientId);
			if($salesCenterId != "")
			{
				$salesCenters = $salesCenters->where('id',$salesCenterId);	
			}

			if($locationId != "")
			{
				$salesCenters =  Salescenterslocations::select('id', 'name')->where('client_id', $clientId)->where('salescenter_id',$salesCenterId);

				if($locationId == "all")
				{
				}
				else
				{
					$salesCenters = $salesCenters->where('id',$locationId);
				}
			}
			$salesCenters = $salesCenters->get();
			
			$dataSalesCenter = [];
			$leads = [];
			$commoditiesNames = [];

			//Retreive client record if exist
			$client = Client::with('forms')->findOrFail($clientId);
			//Prepare available commodities array form wise
			$availableCommodities = [];
			foreach ($client->forms as $form) {
					array_push($availableCommodities, $form->commodities->pluck('id')->toArray());
					array_push($commoditiesNames, $form->commodities->pluck('name')->toArray());
			}

			//Make unique combination of available commodities
			$availableCommodities = array_map("unserialize", array_unique(array_map("serialize", $availableCommodities)));

			//Make unique combination of available commodities names
			$commoditiesNames = array_map("unserialize", array_unique(array_map("serialize", $commoditiesNames)));

			$nameArr = [];
			if (!empty($availableCommodities)) {
				$dashboardLeadCategories = config()->get('constants.DASHBOARD_LEAD_CATEGORIES');

				//Retrieve leads status count by form ids
				foreach ($dashboardLeadCategories as $cKey => $category) {

					foreach ($availableCommodities as $key => $commodityArr) {
						$nameArr[$key] = implode(", ", $commoditiesNames[$key])."-".$key;
						$formIds = [];

						//Retrieve all the form ids belonging to current commodity combination
						if (count($commodityArr) == 1) {
								$formIds = \DB::table('form_commodities')->where('commodity_id', $commodityArr[0])->pluck('form_id')->toArray();
						} else {
								$listForms = [];
								foreach ($commodityArr as $commodity) {
									$listForms[] = \DB::table('form_commodities')->where('commodity_id', $commodity)->pluck('form_id')->toArray();
								}
								$formIds = call_user_func_array('array_intersect', $listForms);
						}

						//Make an array of form with similar commodity combination
						$forms = [];
						if (!empty($formIds)) {
							foreach ($formIds as $fId) {
								$formCommodities = \DB::table('form_commodities')->where('form_id', $fId)->pluck('commodity_id')->toArray();
								if ($formCommodities == $commodityArr) {
									$forms[] = $fId;
								} else {
									continue;
								}
							}
							if ($forms) {
								$leads[$cKey][$nameArr[$key]] = Telesales::getLeadByStatus($this->retrieveLeadStatus($cKey))
																->getLeadsByRange($fromDate, $toDate)
																->getLeadsByClientId($clientId)
																->getLeadsByBrand($brand)
																->getLeadByFormIds($forms)
                                                                ->whereHas('userWithTrashed', function($q) use ($clientId) {
                                                                    $q->where('client_id', $clientId);
                                                                });

																if($locationId != "")
																{
																	if($locationId == "all")
																	{
																		$leads[$cKey][$nameArr[$key]] = $leads[$cKey][$nameArr[$key]]->getLeadsBySalesCenter($salesCenterId);
																		// echo $leads[$cKey][$nameArr[$key]] ."<br/>";
																	}
																	else
																		$leads[$cKey][$nameArr[$key]] = $leads[$cKey][$nameArr[$key]]->getLeadsBySCLocation($locationId);
																}
																$leads[$cKey][$nameArr[$key]] = $leads[$cKey][$nameArr[$key]]->count();
								// if($salesCenterId != "")
								// {
									foreach ($salesCenters as $salesCenter) {
										$salesCenterArr[$category][$nameArr[$key]][array_get($salesCenter, 'name')] = Telesales::getLeadByStatus($this->retrieveLeadStatus($cKey))
																->getLeadsByRange($fromDate, $toDate)
																->getLeadsByClientId($clientId)
																->getLeadsByBrand($brand)
																->getLeadByFormIds($forms)
                                                                ->whereHas('userWithTrashed', function($q) use ($clientId) {
                                                                    $q->where('client_id', $clientId);
                                                                });
																if($locationId != "")
																{
																	$salesCenterArr[$category][$nameArr[$key]][array_get($salesCenter, 'name')] = $salesCenterArr[$category][$nameArr[$key]][array_get($salesCenter, 'name')]->getLeadsBySCLocation(array_get($salesCenter, 'id'))->count();
																	if($category == 'Good Sale')
																		$salesCenterArr['rate'][$nameArr[$key]][array_get($salesCenter, 'name')] = $salesCenterArr[$category][$nameArr[$key]][array_get($salesCenter, 'name')];
																	// else
																	// {
																		if(isset($salesCenterArr['rate']['total'][$nameArr[$key]][array_get($salesCenter, 'name')]))
																		{	
																			$salesCenterArr['rate']['total'][$nameArr[$key]][array_get($salesCenter, 'name')] += $salesCenterArr[$category][$nameArr[$key]][array_get($salesCenter, 'name')];
																		}
																		else
																		$salesCenterArr['rate']['total'][$nameArr[$key]][array_get($salesCenter, 'name')] = $salesCenterArr[$category][$nameArr[$key]][array_get($salesCenter, 'name')];
																	// }
																	if($category == 'Cancelled Leads')
																		$salesCenterArr['rate'][$nameArr[$key]][array_get($salesCenter, 'name')] = $this->getConversionRatePercentage($salesCenterArr['rate'][$nameArr[$key]][array_get($salesCenter, 'name')],$salesCenterArr['rate']['total'][$nameArr[$key]][array_get($salesCenter, 'name')]);

																}
																else
																	$salesCenterArr[$category][$nameArr[$key]][array_get($salesCenter, 'name')] = $salesCenterArr[$category][$nameArr[$key]][array_get($salesCenter, 'name')]->getTotalLeadCountBySalesCenter($salesCenter->id);
	
									}
								// }

							}
						}
					}
				}
			}
			$data = [];
			$data['leadsCount'] = $leads;
			$data['salesCenter'] = $salesCenterArr;
			$data['commoditiesNames'] = $nameArr;
			
			return $data;
		} catch (Exception $e) {
			\Log::info($e->getMessgage());
			return [];
		}
	}



	/**
	 * This method is used to Returns leads statuses by its category
	 * @param $category
	 */
	public function retrieveLeadStatus($category) {
		switch($category) {
		case "good_sale":
			return [config()->get('constants.LEAD_TYPE_VERIFIED')];
			break;
		case "bad_sale":
			return [config()->get('constants.LEAD_TYPE_DECLINE')];
			break;
		case "pending_leads":
			return [config()->get('constants.LEAD_TYPE_PENDING'), config()->get('constants.LEAD_TYPE_DISCONNECTED') ,config()->get('constants.LEAD_STATUS_SELF_VERIFIED')];
			break;
		case "cancelled_leads":
			return [config()->get('constants.LEAD_TYPE_CANCELED'), config()->get('constants.LEAD_TYPE_EXPIRED')];
			break;
		default:
			return [];
			break;
		}
	}

	/**
	 * This method is used to Returns leads verification method with their counts by channel
	 * Visual - 8 Donut pie chart with tabs
	 * @param $clientId, $fromDate, $toDate, $brand, $status, $channel
	 */
	public function leadVerificationMethodsCountByChannel($clientId, $fromDate, $toDate,$brand, $status = "verified", $channel = "d2d") {
		$verificationMethod = config()->get('constants.VERIFICATION_METHOD_FOR_REPORT');
		
		$leads = [];
		foreach ($verificationMethod as $key => $value) {
			$leadsArr = [];
			$leadsArr['name'] = $key;
			$leadsArr['value'] = Telesales::where('telesales.status', $status)->getLeadsByClientId($clientId)->getLeadsByRange($fromDate, $toDate)->getLeadsByBrand($brand)->getSaleByAgentTypes($channel, $clientId)->getLeadsByVerificationMethod($value)->count();

			if ($leadsArr['value'] <= 0) {
				continue;
			}
			// $leads['color'][] = $key == 'Customer Inbound' ? '#3A58A8' : ($key == 'Agent Inbound' ? '#727CB5 ' : ($key == 'Email' ? '#A0A3C1' : '#999999'));
			$leads['data'][] = $leadsArr;
		}
		
		return $leads;
		
	}


    /**
	 * Visual-6 Status By State
	 * @param $client_id, $start_date, $end_date, $brand, $salesCenterId, $salesLocationId, $chart 
	 */
     public function getStatusByStateData($client_id, $start_date, $end_date,$brand,$salesCenterId ="", $salesLocationId= " ",$chart="")
     {
		 $leadsByState= Telesales::getLeadsByClientId($client_id)->getLeadsByRange($start_date, $end_date)->getLeadsByBrand($brand)
		 ->leftjoin('users','users.id','=','telesales.user_id')
		 ->leftjoin('salescenterslocations','salescenterslocations.id','=','users.location_id')
			 ->leftjoin('telesales_zipcodes','telesales_zipcodes.telesale_id','=','telesales.id')
			 ->leftjoin('zip_codes','zip_codes.id','=','telesales_zipcodes.zipcode_id')
			 ->where('zip_codes.state','!=',null);
			 if($chart == 'map')
			 {
				$leadsByState = $leadsByState->groupBy('zip_codes.state');
			 }
			 else
			 	$leadsByState = $leadsByState->groupBy('salescenterslocations.name','zip_codes.state')->orderBy('zip_codes.state');
			 $leadsByState = $leadsByState->select('salescenterslocations.id',\DB::raw("count('id') as total"),'zip_codes.state','salescenterslocations.name');
         if ($salesCenterId != "") {
             $leadsByState = $leadsByState->getLeadsBySalesCenter($salesCenterId);
         }
         if ($salesLocationId != "") {
			 if($salesLocationId == "all")
			 {
				$leadsByState = $leadsByState->getLeadsBySalesCenter($salesCenterId);
			 }
			 else
             	$leadsByState = $leadsByState->getLeadsBySCLocation($salesLocationId);
         }
         $leadsByStateGood = (clone $leadsByState)->getLeadByStatus($this->retrieveLeadStatus('good_sale'));
		 
         $leadsByState = $leadsByState->get()->toArray();
		 

         $leadsByStateGood = $leadsByStateGood->get()->toArray();

		 $leadData = ['leadsByStateOverall' => $leadsByState, 'leadsByStateGood' =>$leadsByStateGood ];
		 if($chart != 'map'){
			 foreach($leadData['leadsByStateOverall'] as $key => &$val)
			 {
				$val['name'] = $val['name'].'#'.$val['id'];
			 }
			 foreach($leadData['leadsByStateGood'] as $key => &$val)
			 {
				$val['name'] = $val['name'].'#'.$val['id'];
			 }
		 }
         return $leadData;
     }

	/**
	 * This method is used to Returns sales agent list with their verified leads percentage
	 * @param $clientId, $fromDate, $toDate, $brand, $type, $salesLocationId, $salesCenterId
	 */
    public function getConversionRateBySalesAgents($clientId, $fromDate, $toDate,$brand, $type = "top",$salesLocationId = "",$salesCenterId="") {
        $sortType = ($type == "top") ? "desc" : "asc";
		$query = Telesales::getLeadsByClientId($clientId)->getLeadsByRange($fromDate, $toDate)->getLeadsByBrand($brand);
		if ($salesLocationId != "") {
			if($salesLocationId == "all")
			{
				$query = $query->getLeadsBySalesCenter($salesCenterId);
			}
			else
            	$query = $query->getLeadsBySCLocation($salesLocationId);
        }
        $totalLeads = $query->count();
		$salesAgents = Telesales::select("telesales.user_id", "users.first_name", "users.last_name", \DB::raw("count('telesales.id') As total_leads"))
		->join('users', 'users.id', 'telesales.user_id');
						// ->where('telesales.status', config()->get('constants.LEAD_TYPE_VERIFIED'));
						if ($salesLocationId != "" ) {
							if($salesLocationId == "all")
							{
								$salesAgents = $salesAgents->where('users.salescenter_id',$salesCenterId);
							}
							else
								$salesAgents = $salesAgents->where('users.location_id',$salesLocationId);
						}
                        $salesAgents= $salesAgents->getLeadsByClientId($clientId)
                        ->getLeadsByRange($fromDate, $toDate)
						->getLeadsByBrand($brand)
						->groupBy('telesales.user_id')
                        ->orderBy('total_leads', $sortType)
                        ->get()
						->toArray();
						
						// $verifiedLeads = Telesales::select("telesales.user_id", "users.first_name", "users.last_name", \DB::raw("count('telesales.id') As total_leads"))
						// ->join('users', 'users.id', 'telesales.user_id')
						// ->where('telesales.status', config()->get('constants.LEAD_TYPE_VERIFIED'));
						// if ($salesLocationId != "" ) {
						// 	if($salesLocationId == "all")
						// 	{
						// 		$salesAgents = $salesAgents->where('users.salescenter_id',$salesCenterId);
						// 	}
						// 	else
						// 		$salesAgents = $salesAgents->where('users.location_id',$salesLocationId);
						// }
                        // $verifiedLeads= $verifiedLeads->getLeadsByClientId($clientId)
                        // ->getLeadsByRange($fromDate, $toDate)
                        // ->groupBy('telesales.user_id')
                        // ->orderBy('total_leads', $sortType)
                        // ->get()
                        // ->toArray();
		$agents = [];
		// dd($salesAgents);
        foreach ($salesAgents as $key => $value) {
			
            // if ($value <= 0) {
            //     continue;
			// }
			$totalLeads = Telesales::getLeadsByClientId($clientId)
			->getLeadsByRange($fromDate, $toDate)->getLeadsByBrand($brand)->getLeadByAgentId($value['user_id'])->getLeadByStatus(['verified']);
			if ($salesLocationId != "") {
				if($salesLocationId == "all")
				{
					$totalLeads = $totalLeads->getLeadsBySalesCenter($salesCenterId);
				}
				else
					$totalLeads = $totalLeads->getLeadsBySCLocation($salesLocationId);
			}
			$totalLeads = $totalLeads->count();
			if($type == 'top')
			{
				if($totalLeads <= 0)
				continue;
			}
            $agentsArr = [];
			$agentsArr['id'] = $value['user_id'];
			$agentsArr['total'] =array_get($value, 'total_leads');
			$agentsArr['good'] =  $totalLeads;
			$agentsArr['name'] = implode(" ", array(array_get($value, 'first_name'), array_get($value, "last_name")));
			$agentsArr['value'] = $this->getConversionRatePercentage($totalLeads,array_get($value, 'total_leads'));
			$agentsArr['value'] = number_format($agentsArr['value'],0, '.', '');
            $agents[] = $agentsArr;
		}
        if ($type == "top") {
            $rates = array_column($agents, 'value');
			array_multisort($rates, SORT_DESC, $agents);
			
        } else {
			$rates = array_column($agents, 'value');
            array_multisort($rates, SORT_ASC, $agents);
		}
		
		$agents = array_reverse(array_slice($agents, 0, 5));
		
        return $agents;
	}
	

	/**
	 * This method is used to Returns all sales center locations with their lead count & conversion rate
	 * @param $clientId, $fromDate, $toDate, $brand
	 */
	public function locationsWithLeadCountsAndConverationRate($clientId, $fromDate, $toDate,$brand) {
		$locations = Salescenterslocations::select('id', 'name')->where('client_id', $clientId)->get();

    $response = [];
		$locationArr = [];
		foreach ($locations as $location) {
			$locationArr = [];
			$totalLeads = $this->getLeads($clientId,$brand,$fromDate, $toDate, "", $location->id)->count();
		//   if ($totalLeads <= 0) {
		// 			continue;
		// 		}

      $locationArr['value'] = $totalLeads;

			$verifiedLeads = $this->getLeads($clientId,$brand,$fromDate, $toDate, "", $location->id, "", array(config()->get('constants.LEAD_TYPE_VERIFIED')))->count();

			if ($verifiedLeads > 0) {
				$locationArr['conversion_rate'] = $this->getConversionRatePercentage($verifiedLeads, $totalLeads);
			} else {
				$locationArr['conversion_rate'] = 0;
			}

			$locationArr['id'] = array_get($location, 'id');
			$locationArr['name'] = array_get($location, 'name');
			$response[] = $locationArr;
		}
		
		return $response;
	}

	/**
	 * This method is used to Return leads query as per conditions
	 * @param $clientId, $brand, $fromDate, $toDate, $salesCenterId, $locationId, $agentId, $status, $type, $verificationMethod, $programId, $utilityName, $state
	 */
	public function getLeads($clientId,$brand,$fromDate = "", $toDate = "", $salesCenterId = "", $locationId = "", $agentId = "", $status = [], $type = "", $verificationMethod = "",$programId="",$utilityName="",$state="") {
			$query = Telesales::getLeadsByClientId($clientId)
			->leftjoin('telesales_zipcodes','telesales_zipcodes.telesale_id','=','telesales.id')
			->leftjoin('zip_codes','zip_codes.id','=','telesales_zipcodes.zipcode_id');

			if ($fromDate != "" && $toDate != "") {
				$query->getLeadsByRange($fromDate, $toDate);
			}

			if ($locationId != "") {
				if($locationId == "all")
				{

				}
				else
					$query->getLeadsBySCLocation($locationId);
			}

			if ($agentId != "") {
				$query->getLeadByAgentId($agentId);
			}

			if (!empty($status)) {
				$query->whereIn('telesales.status', $status);
			}

			if ($type != "") {
				
				$query->getSaleByAgentTypes($type, $clientId);
			}
			if ($salesCenterId != "") {
				$query->getLeadsBySalesCenter($salesCenterId);
			}
			if ($verificationMethod != "") {
				$query->getLeadsByVerificationMethod($verificationMethod);
			}
			if($programId != "")
			{
				$query->getLeadByProgramId($programId,$brand);
			}
			if($utilityName != "")
			{
				$query->getLeadByUtility($utilityName,$brand);
			}
			if($utilityName == '' && $programId == '')
				$query->getLeadsByBrand($brand);
			if($state != '')
			{
				 $query->where('zip_codes.state',$state)
             	->groupBy('state');
			}
			return $query;
	}

	/**
	 * This method is used to Returns all sales center locations with their lead's status wise count and total counts
	 * @param $clientId, $fromDate, $toDate, $brand
	 */
	public function getLocationsWithLeadsCount($clientId, $fromDate, $toDate,$brand) {
		$locations = Salescenterslocations::select('id', 'name')->where('client_id', $clientId)->get();

		$response = [];
		$locationArr = [];

		foreach ($locations as $location) {
			$locationArr = [];
			$totalLeads = $this->getLeads($clientId,$brand,$fromDate, $toDate, "", $location->id)->count();

			if ($totalLeads <= 0) {
				continue;
			}

			$locationArr['total_leads'] = $totalLeads;
			$categories = config()->get('constants.DASHBOARD_LEAD_CATEGORIES');
	    	foreach ($categories as $key => $category) {
				$locationArr[$key] = $this->getLeads($clientId,$brand,$fromDate, $toDate, "", $location->id, "", $this->retrieveLeadStatus($key))->count();
			}

			$locationArr['id'] = array_get($location, 'id');
			$locationArr['name'] = array_get($location, 'name');
			$response[] = $locationArr;
		}
		return $response;
	}

	/**
	 * This method is used to Returns Lead status count by channel
	 * @param $clientId, $fromDate, $toDate, $brand, $salesCenterId, $locationId
	 */
  public function getLocationsWithLeadCountsByChannel($clientId, $fromDate, $toDate,$brand,$salesCenterId = "",$locationId="") {
	$leads = [];
		$locations = Salescenterslocations::select('id', 'name')->where('client_id', $clientId);
		if($locationId != "")
		{
			if($locationId == "all")
			{
				$locations = $locations->where('salescenter_id',$salesCenterId);
			}
			else
				$locations = $locations->where('id',$locationId);
		}
		if($salesCenterId != "")
		{
			$locations = $locations->where('salescenter_id',$salesCenterId);
		}
		$locations = $locations->get();
		foreach ($locations as $location) {
		  $leadsArr = [];

		  $leadsArr['d2d'] = $this->getLeads($clientId,$brand,$fromDate, $toDate, $salesCenterId, $location->id, "", "", "d2d")->count();
		  $leadsArr['tele'] = $this->getLeads($clientId,$brand, $fromDate, $toDate, $salesCenterId, $location->id, "", "", "tele")->count();

				if ($leadsArr['d2d'] <= 0 && $leadsArr['tele'] <= 0) {
					continue;
				}

				$leadsArr['id'] = array_get($location, 'id');
				$leadsArr['name'] = array_get($location, 'name')."#".array_get($location, 'id');
				$leads[] = $leadsArr;
	}
    return $leads;

  }

  	/**
	 * This method is used to get commodity wise leads of particular location
	 * @param $clientId, $fromDate, $toDate, $brand, $locationId, $salesCenterId
	 */
    public function getLocationsLeadsByCommodity($clientId, $fromDate, $toDate,$brand,$locationId="",$salesCenterId="") {
        $salesCenterArr = [];
		$locations = Salescenterslocations::select('id', 'name')->where('client_id', $clientId);
		if($locationId != "")
		{
			if($locationId == "all")
			{
				$locations = $locations->where('salescenter_id',$salesCenterId);
			}
			else
				$locations = $locations->where('id',$locationId);
		}
		
		// if($salesCenterId != "")
		// {
		// 	$locations = $locations->where('salescenter_id',$salesCenterId);
		// }
		$locations = $locations->get();
		
        $leads = [];
        $commoditiesNames = [];

        //Retreive client record if exist
        $client = Client::with('forms')->findOrFail($clientId);
        //Prepare available commodities array form wise
        $availableCommodities = [];
        foreach ($client->forms as $form) {
            array_push($availableCommodities, $form->commodities->pluck('id')->toArray());
            array_push($commoditiesNames, $form->commodities->pluck('name')->toArray());
        }

        //Make unique combination of available commodities
        $availableCommodities = array_map("unserialize", array_unique(array_map("serialize", $availableCommodities)));

        //Make unique combination of available commodities names
        $commoditiesNames = array_map("unserialize", array_unique(array_map("serialize", $commoditiesNames)));

        $nameArr = [];

        if (!empty($availableCommodities)) {

            //Retrieve leads status count by form ids
            foreach ($locations as $key => $location) {
				foreach ($availableCommodities as $key => $commodityArr) {
					$nameArr[$key] = implode(", ", $commoditiesNames[$key])."#".$key;
					$formIds = [];

					//Retrieve all the form ids belonging to current commodity combination
					if (count($commodityArr) == 1) {
						$formIds = \DB::table('form_commodities')->where('commodity_id', $commodityArr[0])->pluck('form_id')->toArray();
					} else {
						$listForms = [];
						foreach ($commodityArr as $commodity) {
							$listForms[] = \DB::table('form_commodities')->where('commodity_id', $commodity)->pluck('form_id')->toArray();
						}
						$formIds = call_user_func_array('array_intersect', $listForms);
					}

					//Make an array of form with similar commodity combination
					$forms = [];
					if (!empty($formIds)) {
						foreach ($formIds as $fId) {
							$formCommodities = \DB::table('form_commodities')->where('form_id', $fId)->pluck('commodity_id')->toArray();
							if ($formCommodities == $commodityArr) {
								$forms[] = $fId;
							} else {
								continue;
							}
						}

						if ($forms) {
							$leads[$location->name.'#'.$location->id][$nameArr[$key]] = Telesales::getLeadsByRange($fromDate, $toDate)
								->getLeadsByClientId($clientId)
								->getLeadsByBrand($brand)
								->getLeadByFormIds($forms);
							if($locationId != "" && $salesCenterId!="")
							{
								$leads[$location->name.'#'.$location->id][$nameArr[$key]] = $leads[$location->name.'#'.$location->id][$nameArr[$key]]->getLeadsBySalesCenter($salesCenterId);
							}
							$leads[$location->name.'#'.$location->id][$nameArr[$key]] = $leads[$location->name.'#'.$location->id][$nameArr[$key]]->getLeadsBySCLocation($location->id)
							->count();
						}
					}
				}
			}
			foreach($leads as $key => $val)
			{
				$leadsarr[$key] = array_filter($leads[$key]);
				if(count($leadsarr[$key]) <= 0)
				{
					unset($leads[$key]);
				}
			}
        }
		$data = [];
        $data['leadsCount'] = $leads;
        $data['commoditiesNames'] = $nameArr;

        return $data;
    }

    /**
	 * This method is used to Retrieve locations with lead status & conversion rate
	 * @param $clientId, $fromDate, $toDate, $brand, $salesCenterId, $locationIds
	 */
    public function locationsWithLeadStatusCounts($clientId, $fromDate, $toDate,$brand, $salesCenterId = "", $locationIds = []) {
	    $query = Salescenterslocations::select('id', 'name')->where('client_id', $clientId);

	    if (!empty($locationIds)) {
            $query->whereIn('id', $locationIds);
        }

        if ($salesCenterId != "") {
            $query->where('salescenter_id', $salesCenterId);
        }

        $locations = $query->get();

        $leadsArr = [];
        foreach ($locations as $location) {
            $leads = [];
            $leads['id'] = $location->id;
            $leads['name'] = $location->name.'-'.$location->id;

            $leadCategories = config()->get('constants.DASHBOARD_LEAD_CATEGORIES');
            foreach ($leadCategories as $key => $category) {
                $leads[$key] = $this->getLeads($clientId,$brand,$fromDate, $toDate, $salesCenterId, $location->id, "", $this->retrieveLeadStatus($key))->count();
            }
            $leads['total_leads'] = $this->getLeads($clientId,$brand,$fromDate, $toDate, $salesCenterId, $location->id)->count();
            $leads['conversion_rate'] = $this->getConversionRatePercentage($leads['good_sale'], $leads['total_leads']);
            $leadsArr[] = $leads;
        }
        return $leadsArr;
	}

	/**
	 * This method is used to get leads based on top programs
	 * @param $clientId, $fromDate, $toDate, $brand
	 */
	public function topProgramsBasedOnLeads($clientId,$fromDate,$toDate,$brand)
	{
		$salescenterArr =[];
		$leads = Telesales::getLeadsByClientId($clientId)->getLeadsByRange($fromDate,$toDate)
				// ->getLeadsByBrand($brand)
				->getProgramsByLead()
				->leftjoin('programs','telesales_programs.program_id','=','programs.id')	
				->leftjoin('utilities','programs.utility_id','=','utilities.id')
				// ->leftjoin('brand_contacts','brand_contacts.id','=','utilities.brand_id')
				->select('telesales_programs.program_id','programs.id',\DB::raw('count(telesale_id) As value'),'programs.name');
				if($brand != "")
					$leads->where('utilities.brand_id',$brand);
				
				$leads = $leads->groupBy('telesales_programs.program_id')
				->having('value','>',0)
				->orderBy('value','DESC')
				->limit(5)
				->get()
				->toArray();
		$salesCenter = SalesCenter::where('client_id',$clientId)->get();
		foreach ($leads as $k => $v) {
			$leads[$k]['name'] = $v['name'].' - '.$v['id'];
		foreach ($salesCenter as $key => $value) {
				$salescenter[$v['program_id']][$value->name]['name'] = $value->name;
				$salescenter[$v['program_id']][$value->name]['count'] = Telesales::getLeadsByClientId($clientId)->getLeadsByRange($fromDate,$toDate)
										->getLeadByProgramId($v['program_id'],$brand)->getLeadsBySalesCenter($value->id)->count();
				$goodSales = Telesales::getLeadsByClientId($clientId)->getLeadsByRange($fromDate,$toDate)
							->getLeadByProgramId($v['program_id'],$brand)->getLeadsBySalesCenter($value->id)->where('telesales.status','verified')->count();

				$salescenter[$v['program_id']][$value->name]['conversionRate'] = $this->getConversionRatePercentage($goodSales, $salescenter[$v['program_id']][$value->name]['count']);
				
			if($salescenter[$v['program_id']][$value->name]['count'] <= 0)
			{
				unset($salescenter[$v['program_id']][$value->name]);
				continue;
			}
		}
		$salescenterArr =  $salescenter;
		}
		$data['leads'] = $leads;
		$data['salescenterArr'] = $salescenterArr;
		return $data;

	}
	
	/**
	 * This method is used to get leads based on top providers
	 * @param $clientId, $fromDate, $toDate, $brand
	 */
	public function topProvidersBasedOnLeads($clientId,$fromDate,$toDate,$brand)
	{
		$salescenterArr =[];
		$leads = Telesales::getLeadsByClientId($clientId)->getLeadsByRange($fromDate,$toDate)
				// ->getLeadsByBrand($brand)
				->getLeadByUtility()
				->select(\DB::raw('count(telesale_id) As value'),'utilities.id','utilities.market as name','utilities.id');
				if($brand != '')
					$leads = $leads->where('utilities.brand_id',$brand);

				$leads = $leads->groupBy('utilities.market')
				->having('value','>',0)
				->orderBy('value','DESC')
				->limit(5)
				->get()
				->toArray();
			
		$salesCenter = SalesCenter::where('client_id',$clientId)->get();
		foreach ($leads as $k => $v) {
			$leads[$k]['name'] = $v['name'].'-'.$v['id'];
			foreach ($salesCenter as $key => $value) {
					$salescenter[$v['id']][$value->name]['name'] = $value->name;
					$salescenter[$v['id']][$value->name]['count'] = Telesales::getLeadsByClientId($clientId)->getLeadsByRange($fromDate,$toDate)
											->getLeadByUtility($v['id'],$brand)->getLeadsBySalesCenter($value->id)->count();
					$goodSales = Telesales::getLeadsByClientId($clientId)->getLeadsByRange($fromDate,$toDate)
								->getLeadByUtility($v['id'],$brand)->getLeadsBySalesCenter($value->id)->where('telesales.status','verified')->count();

					$salescenter[$v['id']][$value->name]['conversionRate'] = $this->getConversionRatePercentage($goodSales, $salescenter[$v['id']][$value->name]['count']);
					
				if($salescenter[$v['id']][$value->name]['count'] <= 0)
				{

					unset($salescenter[$v['id']][$value->name]);
					continue;
				}
			}
			$salescenterArr =  $salescenter;
			}			
		$data['leads'] = $leads;
		$data['salescenterArr'] = $salescenterArr;
		return $data;

	}

	/**
	 * This method is used to get leads based on map data of state
	 * @param $clientId, $fromDate, $toDate, $salesCenterId, $locationId
	 */
	public function getLeadsByStateMapData($clientId,$fromDate,$toDate,$salesCenterId="",$locationId="")
	{
		
			$state = Telesales::getLeadsByClientId($clientId)->getLeadsByRange($fromDate,$toDate)->getLeadByState();
			if($locationId!="")
			{
				if($locationId == "all")
				{
					$state  = $state->getLeadsBySalesCenter($salesCenterId);
				}
				else
					$state  = $state->getLeadsBySCLocation($locationId);
			}
			$state = $state
			->groupBy('state')
			->having('name','!=','null')->select('zipcode','state as name',DB::raw('count(telesalesdata.telesale_id) as value'))->get()->toArray();
		return $state;
	}

	/**
	 * For export data of commodity 
	 * @param $clientId, $fromDate, $toDate, $brand, $status, $commodityId, $salesCenterId, $locationId, $locationWiseLeads
	 */
	 public function commodityExport($clientId, $fromDate, $toDate,$brand,$status,$commodityId,$salesCenterId="",$locationId="",$locationWiseLeads='')
	{		
		
			$dataSalesCenter = [];
			$leads = [];
			$commoditiesNames = [];

			//Retreive client record if exist
			$client = Client::with('forms')->findOrFail($clientId);
			
			//Prepare available commodities array form wise
			$availableCommodities = [];
			foreach ($client->forms as $form) {
					array_push($availableCommodities, $form->commodities->pluck('id')->toArray());
					array_push($commoditiesNames, $form->commodities->pluck('name')->toArray());
			}
			
			//Make unique combination of available commodities
			$availableCommodities = array_map("unserialize", array_unique(array_map("serialize", $availableCommodities)));
			//Make unique combination of available commodities names
			$commoditiesNames = array_map("unserialize", array_unique(array_map("serialize", $commoditiesNames)));
			$nameArr = [];
			if (!empty($availableCommodities)) {
				$dashboardLeadCategories = config()->get('constants.DASHBOARD_LEAD_CATEGORIES');

				//Retrieve leads status count by form ids

				foreach ($availableCommodities as $key => $commodityArr) {
					$formIds = [];
					if($key == $commodityId)
					{
					//Retrieve all the form ids belonging to current commodity combination
					if (count($commodityArr) == 1) {
							$formIds = \DB::table('form_commodities')->where('commodity_id', $commodityArr[0])->pluck('form_id')->toArray();
					} else {
							$listForms = [];
							foreach ($commodityArr as $commodity) {
								$listForms[] = \DB::table('form_commodities')->where('commodity_id', $commodity)->pluck('form_id')->toArray();
							}
							$formIds = call_user_func_array('array_intersect', $listForms);
					}

					//Make an array of form with similar commodity combination
					$forms = [];
					if (!empty($formIds)) {
						foreach ($formIds as $fId) {
							$formCommodities = \DB::table('form_commodities')->where('form_id', $fId)->pluck('commodity_id')->toArray();
							if ($formCommodities == $commodityArr) {
								$forms[] = $fId;
							} else {
								continue;
							}
						}
						if ($forms) {
							
							if($locationWiseLeads == 'true')
							{
								
								$leads = Telesales::getLeadsByRange($fromDate, $toDate)
									->getLeadsByClientId($clientId)
									->getLeadsByBrand($brand)
									->getLeadByFormIds($forms)
									->leftjoin('telesales_zipcodes','telesales_zipcodes.telesale_id','=','telesales.id')
									->leftjoin('zip_codes','zip_codes.id','=','telesales_zipcodes.zipcode_id');
									
									if($locationId != "" && $salesCenterId != "")
									{
										$leads = $leads->getLeadsBySalesCenter($salesCenterId);
									}
                                    $leads = $leads->getLeadsBySCLocation($locationId);
							}
							else
							{
								$leads = Telesales::getLeadByStatus($this->retrieveLeadStatus($status))
																->getLeadsByRange($fromDate, $toDate)
																->getLeadsByClientId($clientId)
																->getLeadsByBrand($brand)
																->getLeadByFormIds($forms)->leftjoin('telesales_zipcodes','telesales_zipcodes.telesale_id','=','telesales.id')
																->leftjoin('zip_codes','zip_codes.id','=','telesales_zipcodes.zipcode_id');
	
																if($locationId != "")
																{
																	if($locationId == "all" && $salesCenterId != "")
																		{
																			$leads = $leads->getLeadsBySalesCenter($salesCenterId);
																		}
																		else
																			$leads = $leads->getLeadsBySCLocation($locationId);
																}
							}
															
						}
					}
				}
			}
		}
		return $leads;
	}
}
