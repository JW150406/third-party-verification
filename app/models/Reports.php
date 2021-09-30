<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Reports extends Model
{

    private $vendorname = "CALLCENTER SOLUTIONS LLC";

    public function getSalesCount($status = 'verified', $start_date = "", $end_date = "", $client_id = "", $salescenter_id = "")
    {

        $params = array(
            array('status', '=', $status)
        );
        $whereRaw = "1 = 1";

        if (!empty($start_date) && !empty($end_date)) {
            $whereRaw .= " and  date_format(updated_at,'%Y-%m-%d') >= '$start_date'";
            $whereRaw .= " and  date_format(updated_at,'%Y-%m-%d') <= '$end_date'";
        }
        if (empty($start_date) && !empty($end_date)) {
            $whereRaw .= " and  date_format(updated_at,'%Y-%m-%d') <= '$end_date'";
        }
        if (!empty($start_date) && empty($end_date)) {
            $whereRaw .= " and  date_format(updated_at,'%Y-%m-%d') >= '$start_date'";
        }
        if (!empty($client_id)) {
            $params[] = array('client_id', '=', $client_id);
        }


        return $users = Telesales::where($params)->whereRaw($whereRaw)->count();
    }

    public function getTopAgents($where = array(), $limit = '5')
    {

        $get_query = $this->get_query_params($where);
        $params = $get_query['wherearray'];
        $whereRaw = $get_query['whereraw'];
        $raw_start_end_date = $get_query['raw_start_end_date'];
        $raw_start_end_date_verified = str_replace('telesales', 'verifieddata', $raw_start_end_date);
        $raw_start_end_date_decline = str_replace('telesales', 'declinedata', $raw_start_end_date);

        return $telesales = Telesales::select(DB::raw("telesales.user_id, count(*) as total_sales, users.first_name,users.last_name,users.userid, clients.name as client_name, salescenters.name as salescenter_name, ( select count(*) from telesales as verifieddata where verifieddata.status='verified' and verifieddata.user_id= telesales.user_id  and {$raw_start_end_date_verified }  ) as total_verified ,
         ( select count(*) from telesales as declinedata where declinedata.status='decline' and declinedata.user_id=telesales.user_id and {$raw_start_end_date_decline} )  as total_decline"))
            ->join('users', 'users.id', '=', 'telesales.user_id')
            ->join('clients', 'clients.id', '=', 'users.client_id')
            ->join('salescenters', 'salescenters.id', '=', 'users.salescenter_id')
            ->where($params)
            ->whereRaw($whereRaw)
            ->groupBy('telesales.user_id')
            ->orderBy('total_sales', 'desc')
            ->limit($limit)
            ->get();

    }

    public function getTopOffice($where = array(), $limit = '5')
    {

        $get_query = $this->get_query_params($where);
        $params = $get_query['wherearray'];
        $whereRaw = $get_query['whereraw'];
        $raw_start_end_date = $get_query['raw_start_end_date'];


        $telesales = Telesales::select(DB::raw("count(*) as total_sales,salescenterslocations.name as location_name, salescenterslocations.salescenter_id as salescenter_id,
         ( select GROUP_CONCAT(id) from users as getactive_uses where getactive_uses.status='active' and  getactive_uses.location_id=salescenterslocations.id  and getactive_uses.access_level='salesagent'  ) as agent_users ,
         ( select count(*) from users as getactive_uses where getactive_uses.status='active' and  getactive_uses.location_id=salescenterslocations.id  and getactive_uses.access_level='salesagent'  ) as active_users  "))
            ->join('users', 'users.id', '=', 'telesales.user_id')
            ->join('salescenterslocations', 'salescenterslocations.id', '=', 'users.location_id')
            ->where($params)
            ->whereRaw($whereRaw)
            ->groupBy('users.location_id')
            ->orderBy('total_sales', 'desc')
            ->limit($limit)
            ->get();
        $sales = array();

        if (count($telesales) > 0) {

            foreach ($telesales as $telesale) {
                $agent_users = explode(',', $telesale['agent_users']);
                $total_verified = $this->getSalescenter_sales($agent_users, 'verified', $raw_start_end_date);
                $total_decline = $this->getSalescenter_sales($agent_users, 'decline', $raw_start_end_date);
                $salescenter = (new Salescenter)->getSalescenterinfo($telesale['salescenter_id']);
                $sales[] = array(
                    'total_sales' => $telesale['total_sales'],
                    'location_name' => $telesale['location_name'],
                    'active_users' => $telesale['active_users'],
                    'salescenter_name' => $salescenter->name,
                    'total_verified' => $total_verified,
                    'total_decline' => $total_decline,
                );

            }
        }
        // print_r($sales);
        return $sales;
    }

    public function getTopSalesCenters($where = array(), $limit = '5')
    {

        $get_query = $this->get_query_params($where);
        $params = $get_query['wherearray'];
        $whereRaw = $get_query['whereraw'];
        $raw_start_end_date = $get_query['raw_start_end_date'];


        $telesales = Telesales::select(DB::raw("count(*) as total_sales,   salescenters.name as salescenter_name, salescenters.client_id as client_id,
         ( select GROUP_CONCAT(id) from users as getactive_uses where status='active' and  getactive_uses.salescenter_id=salescenters.id  and access_level='salesagent'  ) as agent_users ,
         ( select count(*) from users as getactive_uses where status='active' and  getactive_uses.salescenter_id=salescenters.id   and getactive_uses.access_level='salesagent'  ) as active_users  "))
            ->join('users', 'users.id', '=', 'telesales.user_id')
            ->join('salescenters', 'salescenters.id', '=', 'users.salescenter_id')
            ->where($params)
            ->whereRaw($whereRaw)
            ->groupBy('users.salescenter_id')
            ->orderBy('total_sales', 'desc')
            ->limit($limit)
            ->get();
        $sales = array();
        if (count($telesales) > 0) {

            foreach ($telesales as $telesale) {
                $agent_users = explode(',', $telesale['agent_users']);
                $total_verified = $this->getSalescenter_sales($agent_users, 'verified', $raw_start_end_date);
                $total_decline = $this->getSalescenter_sales($agent_users, 'decline', $raw_start_end_date);
                $client = (new Client)->getClientinfo($telesale['client_id']);
                $sales[] = array(
                    'total_sales' => $telesale['total_sales'],
                    'salescenter_name' => $telesale['salescenter_name'],
                    'active_users' => $telesale['active_users'],
                    'client_name' => $client->name,
                    'total_verified' => $total_verified,
                    'total_decline' => $total_decline,
                );

            }
        }
        // print_r($sales);
        return $sales;
    }

    public function getTopClients($where = array(), $limit = '5')
    {


        $get_query = $this->get_query_params($where);
        $params = $get_query['wherearray'];
        $whereRaw = $get_query['whereraw'];
        $raw_start_end_date = $get_query['raw_start_end_date'];


        $telesales = Telesales::select(DB::raw("count(*) as total_sales,   salescenters.name as salescenter_name, salescenters.client_id as client_id,
         ( select GROUP_CONCAT(id) from users as getactive_uses where status='active' and  getactive_uses.salescenter_id=salescenters.id  and access_level='salesagent'  ) as agent_users ,
         ( select count(*) from users as getactive_uses where status='active' and  getactive_uses.salescenter_id=salescenters.id and getactive_uses.access_level='salesagent'    ) as active_users  "))
            ->join('users', 'users.id', '=', 'telesales.user_id')
            ->join('salescenters', 'salescenters.id', '=', 'users.salescenter_id')
            ->where($params)
            ->whereRaw($whereRaw)
            ->groupBy('users.salescenter_id')
            ->orderBy('total_sales', 'desc')
            ->limit($limit)
            ->get();


        $sales = array();
        if (count($telesales) > 0) {

            foreach ($telesales as $telesale) {
                $agent_users = explode(',', $telesale['agent_users']);
                $total_verified = $this->getSalescenter_sales($agent_users, 'verified', $raw_start_end_date);
                $total_decline = $this->getSalescenter_sales($agent_users, 'decline', $raw_start_end_date);
                $client = (new Client)->getClientinfo($telesale['client_id']);
                $sales[] = array(
                    'total_sales' => $telesale['total_sales'],
                    'salescenter_name' => $telesale['salescenter_name'],
                    'active_users' => $telesale['active_users'],
                    'client_name' => $client->name,
                    'total_verified' => $total_verified,
                    'total_decline' => $total_decline,
                );

            }
        }
        // print_r($sales);
        return $sales;
    }


    function getSalescenter_sales($userids = array(), $status = 'verified', $raw_start_end_date)
    {

        $result = Telesales::join('users', 'users.id', '=', 'telesales.user_id')
            ->where('telesales.status', $status)
            ->whereRaw($raw_start_end_date)
            ->whereIn('telesales.user_id', $userids)
            ->count();
        return $result;
    }

    function searchResults($params)
    {

        $whereArray = array();
        $export = "";
        if (isset($params['refrence_id']) && !empty($params['refrence_id'])) {
            $whereArray[] = array('telesales.refrence_id', '=', $params['refrence_id']);
        }
        if (isset($params['salesagentid']) && !empty($params['salesagentid'])) {
            $whereArray[] = array('salesagent.userid', '=', $params['salesagentid']);
        }
        if (isset($params['tpvagentid']) && !empty($params['tpvagentid'])) {
            $whereArray[] = array('tpvagent.userid', '=', $params['tpvagentid']);
        }
        if (isset($params['location_id']) && !empty($params['location_id'])) {
            $whereArray[] = array('salesagent.location_id', '=', $params['location_id']);
        }
        if (isset($params['export']) && !empty($params['export'])) {
            $export = 1;
        }


        $whereRaw = "1 = 1";
        if ((isset($params['date_start']) && !empty($params['date_start'])) && (isset($params['date_end']) && !empty($params['date_end']))) {
            $whereRaw .= " and  date_format(telesales.updated_at,'%Y-%m-%d') >= '" . $this->formatdate($params['date_start']) . "'";
            $whereRaw .= " and  date_format(telesales.updated_at,'%Y-%m-%d') <= '" . $this->formatdate($params['date_end']) . "'";
        }
        if ((isset($params['date_start']) && !empty($params['date_start'])) && (isset($params['date_end']) && empty($params['date_end']))) {
            $whereRaw .= " and  date_format(telesales.updated_at,'%Y-%m-%d') <= '" . $this->formatdate($params['date_end']) . "'";
        }
        if ((isset($params['date_start']) && empty($params['date_start'])) && (isset($params['date_end']) && !empty($params['date_end']))) {
            $whereRaw .= " and  date_format(telesales.updated_at,'%Y-%m-%d') >= '" . $this->formatdate($params['date_start']) . "'";
        }


        $query = DB::table('telesales')->select('telesales.id',
            'telesales.refrence_id',
            'telesales.status',
            DB::raw("date_format(telesales.updated_at,'%m/%d/%y %H:%i') as update_at"),
            DB::raw("(select `meta_value` from `telesalesdata` where `meta_key` like '%Account Number%' and `telesale_id` =telesales.id limit 1 ) as account_number"),
            DB::raw("(select `meta_value` from `telesalesdata` where `meta_key` like '%Billing First Name%' and `telesale_id` =telesales.id ) as billing_first_name"),
            DB::raw("(select `meta_value` from `telesalesdata` where `meta_key` like '%Billing Last Name%' and `telesale_id` =telesales.id ) as billing_last_name"),
            DB::raw("(select `meta_value` from `telesalesdata` where `meta_key` like '%Billing State%' and `telesale_id` =telesales.id ) as billing_state"),
            DB::raw("(select `meta_value` from `telesalesdata` where `meta_key` like '%Phone Number%' and `telesale_id` =telesales.id ) as phone_number"),
            DB::raw("(select `meta_value` from `telesalesdata` where `meta_key` like '%Auth First Name%' and `telesale_id` =telesales.id ) as authuser_first_name"),
            DB::raw("(select `meta_value` from `telesalesdata` where `meta_key` like '%Auth Last Name%' and `telesale_id` =telesales.id ) as authuser_last_name"),
            'telesales.disposition_id',
            'salesagent.first_name as salesagent_first_name',
            'salesagent.last_name as salesagent_last_name',
            'salesagent.userid as salesagent_userid',
            'salesagent.id as salesagent_id',
            'tpvagent.userid as tpvagent_userid',
            'tpvagent.id as tpvagent_id',
            'tpvagent.first_name as tpvagent_first_name',
            'tpvagent.last_name as tpvagent_last_name',
            'salescenters.name as salescenter_name',
            'salesagent.salescenter_id',
            'dispositions.description as disposition',
            'telesales.s3_recording_url as recording_url',
            'telesales.recording_id'

        )
            ->leftJoin('users as salesagent', 'salesagent.id', '=', 'telesales.user_id')
            ->leftJoin('salescenters', 'salescenters.id', '=', 'salesagent.salescenter_id')
            ->leftJoin('users as tpvagent', 'tpvagent.id', '=', 'telesales.reviewed_by')
            ->leftJoin('dispositions as dispositions', 'dispositions.id', '=', 'telesales.disposition_id');

        $query->where($whereArray)
            ->whereRaw($whereRaw);
        if (isset($params['accountnumber']) && !empty($params['accountnumber'])) {
            $account_number = $params['accountnumber'];
            $query->when($account_number, function ($query) use ($account_number) {

                return $query->whereIn('telesales.id', function ($query) use ($account_number) {
                    $query->select('telesale_id')
                        ->from(with(new Telesalesdata)->getTable())
                        ->where('meta_key', 'like', '%Account Number%')
                        ->where('meta_value', '=', $account_number);
                });
            });

        }

        $query->whereIn('telesales.status', ['verified', 'decline']);
        $query->orderBy('telesales.updated_at', 'desc');
        if ($export == 1) {
            $results = $query->toSql();
        } else {
            $results = $query->paginate(20);
        }

        // ->toSql();
        //  print_r( $results);
        //  die();
        return $results;
    }

    function formatdate($date)
    {
        return date('Y-m-d', strtotime($date));
    }


    function get_query_params($where)
    {
        $params = array();
        if (isset($where['client_id']) && !empty($where['client_id'])) {
            $params[] = array('telesales.client_id', '=', $where['client_id']);
        }
        if (isset($where['status']) && !empty($where['status'])) {
            $params[] = array('telesales.status', '=', $where['status']);
        }

        if (isset($where['salescenter_id']) && !empty($where['salescenter_id'])) {
            $params[] = array('users.salescenter_id', '=', $where['salescenter_id']);
        }
        if (isset($where['location_id']) && !empty($where['location_id'])) {
            $params[] = array('users.location_id', '=', $where['location_id']);
        }

        $whereRaw = $raw_start_end_date = "1 = 1";
        if ((isset($where['start_date']) && !empty($where['start_date'])) && (isset($where['end_date']) && !empty($where['end_date']))) {
            $whereRaw .= " and  date_format(telesales.created_at,'%Y-%m-%d') >= '" . $where['start_date'] . "'";
            $whereRaw .= " and  date_format(telesales.created_at,'%Y-%m-%d') <= '" . $where['end_date'] . "'";

            $raw_start_end_date .= " and  date_format(telesales.created_at,'%Y-%m-%d') >= '" . $where['start_date'] . "'";
            $raw_start_end_date .= " and  date_format(telesales.created_at,'%Y-%m-%d') <= '" . $where['end_date'] . "'";

        }
        if ((isset($where['start_date']) && !empty($where['start_date'])) && (isset($where['end_date']) && empty($where['end_date']))) {
            $whereRaw .= " and  date_format(telesales.created_at,'%Y-%m-%d') <= '" . $where['end_date'] . "'";
            $raw_start_end_date .= " and  date_format(telesales.created_at,'%Y-%m-%d') <= '" . $where['end_date'] . "'";
        }
        if ((isset($where['start_date']) && empty($where['start_date'])) && (isset($where['end_date']) && !empty($where['end_date']))) {
            $whereRaw .= " and  date_format(telesales.created_at,'%Y-%m-%d') >= '" . $where['start_date'] . "'";
            $raw_start_end_date .= " and  date_format(telesales.created_at,'%Y-%m-%d') >= '" . $where['start_date'] . "'";
        }

        return array('wherearray' => $params, 'whereraw' => $whereRaw, 'raw_start_end_date' => $raw_start_end_date);
    }

    function dailyExportResults($client_id, $status, $start_date, $end_date, $salesCenter, $commodity,$export=false)
    {

        $whereArray = array();
        // if (isset($params['refrence_id']) && !empty($params['refrence_id'])) {
        //     $whereArray[] = array('telesales.refrence_id', '=', $params['refrence_id']);
        // }
        // if (isset($params['client']) && !empty($params['client'])) {
        //     $whereArray[] = array('telesales.client_id', '=', $params['client']);
        // }
        // if (isset($params['salesagentid']) && !empty($params['salesagentid'])) {
        //     $whereArray[] = array('salesagent.userid', '=', $params['salesagentid']);
        // }
        // if (isset($params['salesagent']) && !empty($params['salesagent'])) {
        //     $whereArray[] = array('salesagent.id', '=', $params['salesagent']);
        // }



        // if (isset($params['tpvagentid']) && !empty($params['tpvagentid'])) {
        //     $whereArray[] = array('tpvagent.userid', '=', $params['tpvagentid']);
        // }
        // if (isset($params['location_id']) && !empty($params['location_id'])) {
        //     $whereArray[] = array('salesagent.location_id', '=', $params['location_id']);
        // }
        // if (isset($params['vendorstatus']) && !empty($params['vendorstatus'])) {
        //     $whereArray[] = array('clients.status', '=', $params['vendorstatus']);
        // }
        // if (isset($params['userstatus']) && !empty($params['userstatus'])) {
        //     $whereArray[] = array('salesagent.status', '=', $params['userstatus']);
        // }
        // if (isset($params['export']) && !empty($params['export'])) {
        //     $export = 1;
        // }
        // if (isset($params['status']) && !empty($params['status'])) {
        //     $status = [$params['status']];
        // } else {
        //     $status = ['verified', 'decline'];
        // }



         $whereRaw = "1 = 1";
        // if ((isset($params['date_start']) && !empty($params['date_start'])) && (isset($params['date_end']) && !empty($params['date_end']))) {
        //     $whereRaw .= " and  date_format(telesales.created_at,'%Y-%m-%d') >= '" . $this->formatdate($params['date_start']) . "'";
        //     $whereRaw .= " and  date_format(telesales.created_at,'%Y-%m-%d') <= '" . $this->formatdate($params['date_end']) . "'";
        // }
        // if ((isset($params['date_start']) && !empty($params['date_start'])) && (isset($params['date_end']) && empty($params['date_end']))) {
        //     $whereRaw .= " and  date_format(telesales.created_at,'%Y-%m-%d') <= '" . $this->formatdate($params['date_end']) . "'";
        // }
        // if ((isset($params['date_start']) && empty($params['date_start'])) && (isset($params['date_end']) && !empty($params['date_end']))) {
        //     $whereRaw .= " and  date_format(telesales.created_at,'%Y-%m-%d') >= '" . $this->formatdate($params['date_start']) . "'";
        // }
        // if (isset($params['program']) && !empty($params['program'])) {
        //     $whereRaw .= " and  telesales.id in ( SELECT telesale_id FROM `telesalesdata` WHERE (`meta_key` = '_programID' or `meta_key` = '_electricprogramID' or `meta_key` = '_gasprogramID' )  and  meta_value = '" . $params['program'] . "')";
        // }


        //   $query =  DB::table('telesales')->select('telesales.id',
        //    'telesales.refrence_id',
        //    'telesales.status',
        //    DB::raw( "date_format(telesales.updated_at,'%m/%d/%y %H:%i') as update_at") ,
        //    DB::raw( "(select `meta_value` from `telesalesdata` where `meta_key` like '%Account Number%' and `telesale_id` =telesales.id limit 1 ) as account_number") ,
        //    DB::raw( "(select `meta_value` from `telesalesdata` where `meta_key` like '%Billing First Name%' and `telesale_id` =telesales.id ) as billing_first_name") ,
        //    DB::raw( "(select `meta_value` from `telesalesdata` where `meta_key` like '%Billing Last Name%' and `telesale_id` =telesales.id ) as billing_last_name") ,
        //    DB::raw( "(select `meta_value` from `telesalesdata` where `meta_key` like '%Billing State%' and `telesale_id` =telesales.id ) as billing_state") ,
        //    DB::raw( "(select `meta_value` from `telesalesdata` where `meta_key` like '%Phone Number%' and `telesale_id` =telesales.id ) as phone_number") ,
        //    DB::raw( "(select `meta_value` from `telesalesdata` where `meta_key` like '%Auth First Name%' and `telesale_id` =telesales.id ) as authuser_first_name") ,
        //    DB::raw( "(select `meta_value` from `telesalesdata` where `meta_key` like '%Auth Last Name%' and `telesale_id` =telesales.id ) as authuser_last_name") ,
        //    DB::raw( "(select `meta_value` from `telesalesdata` where `meta_key` like '%Program Code%' and `telesale_id` =telesales.id ) as program_code") ,
        //    DB::raw( "(select `meta_value` from `telesalesdata` where `meta_key` = 'Program' and `telesale_id` =telesales.id ) as program_name") ,
        //    DB::raw( "(select `meta_value` from `telesalesdata` where `meta_key` = 'utility' and `telesale_id` =telesales.id ) as utility") ,
        //    DB::raw( "(select `meta_value` from `telesalesdata` where `meta_key` = 'Utility Type' and `telesale_id` =telesales.id ) as utility_type") ,
        //    DB::raw( "(select `meta_value` from `telesalesdata` where `meta_key` = 'rate' and `telesale_id` = telesales.id ) as rate") ,
        //    DB::raw( "(select `meta_value` from `telesalesdata` where `meta_key` = 'term' and `telesale_id` = telesales.id ) as term") ,
        //    DB::raw( "(select `meta_value` from `telesalesdata` where `meta_key` = 'msf' and `telesale_id` = telesales.id ) as msf") ,
        //    DB::raw( "(select `meta_value` from `telesalesdata` where `meta_key` = 'etf' and `telesale_id` = telesales.id ) as etf") ,
        //    'telesales.disposition_id',
        //    'salesagent.first_name as salesagent_first_name',
        //    'salesagent.last_name as salesagent_last_name',
        //    'salesagent.userid as salesagent_userid',
        //    'salesagent.id as salesagent_id',
        //    'tpvagent.userid as tpvagent_userid',
        //    'tpvagent.id as tpvagent_id',
        //    'tpvagent.first_name as tpvagent_first_name',
        //    'tpvagent.last_name as tpvagent_last_name',
        //    'salescenters.name as salescenter_name',
        //    'salesagent.salescenter_id',
        //    'dispositions.description as disposition',
        //    'telesales.s3_recording_url as recording_url',
        //    'telesales.recording_id',
        //    'clients.name as  vendor_name',
        //    'clients.code as  vender_code',
        //    'clients.id as  vendor_number',
        //    'locations.name as office'

        //    )
        //    ->leftJoin('users as salesagent', 'salesagent.id', '=', 'telesales.user_id')
        //    ->leftJoin('salescenters', 'salescenters.id', '=', 'salesagent.salescenter_id')
        //    ->leftJoin('users as tpvagent', 'tpvagent.id', '=', 'telesales.reviewed_by')
        //    ->leftJoin('dispositions as dispositions', 'dispositions.id', '=', 'telesales.disposition_id')
        //    ->leftJoin('clients as clients', 'clients.id', '=', 'salesagent.client_id')
        //    ->leftJoin('salescenterslocations as locations', 'locations.id', '=', 'salesagent.location_id');

        $query = DB::table('telesales')->select(
            DB::raw("date_format(telesales.updated_at,'%m/%d/%y %I:%i:%S %p') as CallDateTime"),
            DB::raw("telesales.call_duration as TotalCallTime"),
            DB::raw("'" . $this->vendorname . "' as VendorName"),
            DB::raw("clients.id as VendorNumber"),
            DB::raw("( select code from programs where id = (select meta_value from telesalesdata where meta_key = '_programID' and telesale_id =telesales.id ))   as  LdcCode"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'Commodity' and telesale_id =telesales.id   ) as UtilityTypeName"),
            DB::raw("(select GROUP_CONCAT(programs.name SEPARATOR ', ') from programs left join telesales_programs on telesales_programs.program_id = programs.id where telesales_programs.telesale_id = telesales.id) as ProgramName"),
            DB::raw("(select GROUP_CONCAT(programs.code SEPARATOR ', ') from programs left join telesales_programs on telesales_programs.program_id = programs.id where telesales_programs.telesale_id = telesales.id) as ProgramCode"),
            DB::raw("CASE
        WHEN  telesales.status = 'verified' THEN 'Good Sale'
        WHEN  telesales.status = 'decline' THEN 'No Sale'
        else
        telesales.status end as Verified "),
            DB::raw("concat((select meta_value from telesalesdata where meta_key = 'Account Number' and telesale_id =telesales.id limit 1 ), ' ') as AccountNumber"),
            DB::raw("UPPER(( select customer_type from programs where id = (select meta_value from telesalesdata where meta_key = '_programID' and telesale_id =telesales.id )  )) as PremiseTypeName"),

            DB::raw("CASE
            WHEN  (select meta_value from telesalesdata where meta_key = 'First name' and telesale_id =telesales.id )  != ''
            THEN  (select meta_value from telesalesdata where meta_key = 'First name' and telesale_id =telesales.id )
            WHEN  (select meta_value from telesalesdata where meta_key = 'Authorized First name' and telesale_id =telesales.id )  != ''
            THEN  (select meta_value from telesalesdata where meta_key = 'Authorized First name' and telesale_id =telesales.id )
        ELSE ''
        END as 'AuthorizationFirstName'"),
            //  DB::raw( "(select meta_value from telesalesdata where meta_key = 'Middle initial' and telesale_id =telesales.id ) as Mi") ,
            DB::raw("
        CASE
            WHEN  (select meta_value from telesalesdata where meta_key = 'Last name' and telesale_id =telesales.id )  != ''
            THEN  (select meta_value from telesalesdata where meta_key = 'Last name' and telesale_id =telesales.id )
            WHEN  (select meta_value from telesalesdata where meta_key = 'Authorized Last name' and telesale_id =telesales.id )  != ''
            THEN  (select meta_value from telesalesdata where meta_key = 'Authorized Last name' and telesale_id =telesales.id )

        ELSE ''
        END  as 'AuthorizationLastName'"),
            DB::raw("
        CONCAT (
            UPPER((select meta_value from telesalesdata where meta_key = 'ServiceAddress' and telesale_id =telesales.id )),' ' ,
            UPPER((select meta_value from telesalesdata where meta_key = 'ServiceAddress2' and telesale_id =telesales.id ))
        )
         as ServiceAddress

        "),
            //      DB::raw( "UPPER((select meta_value from telesalesdata where meta_key = 'ServiceAddress2' and telesale_id =telesales.id )) as ServiceAddress2") ,
            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'ServiceCity' and telesale_id =telesales.id )) as ServiceCity"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'ServiceState' and telesale_id =telesales.id ) as ServiceState"),
            DB::raw("concat((select meta_value from telesalesdata where meta_key = 'ServiceZip' and telesale_id =telesales.id ),' ') as ServiceZip"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'ServiceCounty' and telesale_id =telesales.id ) as ServiceCounty "),
            DB::raw("
        CASE
            WHEN (select meta_value from telesalesdata where meta_key = 'Email for reward programs' and telesale_id = telesales.id ) != '' THEN (select meta_value from telesalesdata where meta_key = 'Email for reward programs' and telesale_id = telesales.id )
            WHEN (select meta_value from telesalesdata where meta_key = 'Email' and telesale_id =telesales.id ) != '' THEN (select meta_value from telesalesdata where meta_key = 'Email' and telesale_id =telesales.id )
            ELSE ''
        END as Email"),
            DB::raw("concat(REPLACE(REPLACE(trim('(' from (select meta_value from telesalesdata where meta_key = 'Phone Number' and telesale_id =telesales.id ) ),'-',''),')',''  ),' ') as Btn"),
            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'Billing first name' and telesale_id =telesales.id )) as 'AccountFirstName'"),
            //  DB::raw( "(select meta_value from telesalesdata where meta_key = 'Middle initial' and telesale_id =telesales.id ) as Mi") ,
            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'Billing last name' and telesale_id =telesales.id )) as 'AccountLastName'"),

            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'BillingAddress' and telesale_id =telesales.id )) as BillingAddress"),
            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'BillingCity' and telesale_id =telesales.id )) as BillingCity"),
            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'BillingState' and telesale_id =telesales.id )) as BillingState"),
            DB::raw("concat((select meta_value from telesalesdata where meta_key = 'BillingZip' and telesale_id =telesales.id ), ' ') as BillingZip"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'BillingCounty' and telesale_id =telesales.id ) as BillingCounty"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'Language' and telesale_id =telesales.id  limit 1) as Language"),
            DB::raw("concat(format((select meta_value from telesalesdata where meta_key = 'rate' and telesale_id = telesales.id ),4), ' ') as Rate"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'term' and telesale_id = telesales.id ) as Term"),
            DB::raw("concat(  format(
        CASE
            WHEN (select meta_value from telesalesdata where meta_key = 'msf' and telesale_id = telesales.id and meta_value is not null ) != ''
            THEN (select meta_value from telesalesdata where meta_key = 'msf' and telesale_id = telesales.id  )
            WHEN (select meta_value from telesalesdata where meta_key = 'msf' and telesale_id = telesales.id ) IS NULL or (select meta_value from telesalesdata where meta_key = 'msf' and telesale_id = telesales.id ) = ''
            THEN (select msf from programs where id = ( select meta_value from telesalesdata where (meta_key = '_programID' or  meta_key = '_gasprogramID' or meta_key = '_electricprogramID' ) and telesale_id = telesales.id limit 1) )
            ELSE ''
        END,4) ,' ' ) as Msf")
            ,
            DB::raw("concat(format((select meta_value from telesalesdata where meta_key = 'etf' and telesale_id = telesales.id ),4) ,' ' ) as Etf"),
            DB::raw("salesagent.userid as AgentId"),
            DB::raw("'TELESALES' as Name"),
            // DB::raw("(select saleschannels from programs where id = (select meta_value from telesalesdata where meta_key = '_programID' and telesale_id =telesales.id )) as Name"),

            DB::raw("tpvagent.userid as TpvAgentId"),

            DB::raw("CONCAT_WS(  ' ',tpvagent.first_name,tpvagent.last_name ) as TpvAgentName"),
            DB::raw("'' as RateClass"),
            DB::raw("telesales.verification_number as MainId"),
            DB::raw("
        CASE
        WHEN telesales.status = 'verified'
        THEN 'Verified'
        WHEN telesales.status = 'decline'
        THEN (SELECT description FROM dispositions where id = telesales.disposition_id )
        ELSE ''
    END  as Concern"),
            DB::raw("concat('TPV',telesales.id) as ExternalSalesId"),

            DB::raw("(select meta_value from telesalesdata where meta_key = 'utility' and telesale_id =telesales.id ) as Brand"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'Program' and telesale_id =telesales.id ) as ProductName"),
            DB::raw("'' as MarketerCode"),
            DB::raw("locations.name as OfficeName"),
            DB::raw("'Data Entry' as Source")

        )
            ->leftJoin('users as salesagent', 'salesagent.id', '=', 'telesales.user_id')
            ->leftJoin('salescenters', 'salescenters.id', '=', 'salesagent.salescenter_id')
            ->leftJoin('users as tpvagent', 'tpvagent.id', '=', 'telesales.reviewed_by')
            ->leftJoin('dispositions as dispositions', 'dispositions.id', '=', 'telesales.disposition_id')
            ->leftJoin('clients as clients', 'clients.id', '=', 'salesagent.client_id')
            ->leftJoin('salescenterslocations as locations', 'locations.id', '=', 'salesagent.location_id')
            ->leftJoin('form_commodities', 'form_commodities.form_id', '=', 'telesales.form_id');

        if (isset($client_id) && !empty($client_id)) {
            $whereArray[] = array('telesales.client_id', '=', $client_id);
        }

        /*if (isset($start_date) && !empty($start_date)) {
            $whereArray[] = array('telesales.created_at', '>=', $start_date);
     `   }

        if (isset($end_date) && !empty($end_date)) {
            $whereArray[] = array('telesales.created_at', '<=', $end_date);
        }*/

        if (isset($status) && !empty($status) && $status !== 'all') {
            $whereArray[] = array('telesales.status', '=', $status);
        }

        if (isset($salesCenter) && !empty($salesCenter)) {
            $whereArray[] = array('salescenters.id', '=', $salesCenter);
        }

        $query->where($whereArray)->whereBetween('telesales.created_at',array($start_date. ' 00:00:00', $end_date . ' 23:59:59'))->whereRaw($whereRaw);

        //$query->where($whereArray)->whereRaw($whereRaw);

        if (isset($commodity) && !empty($commodity)) {
            $query->where('form_commodities.commodity_id', $commodity);
        }

        $query->orderBy('telesales.id', 'desc');
        if ($export == 1) {
            $results = $query->get();
        } else {
            $results = $query->paginate(10);
        }


        return $results;
    }


    public function exportdailydata()
    {
        $query = DB::table('telesales')->select(
            DB::raw("date_format(telesales.updated_at,'%m/%d/%y %I:%i:%S %p') as CallDateTime"),
            DB::raw("telesales.call_duration as TotalCallTime"),
            DB::raw("'" . $this->vendorname . "' as VendorName"),
            DB::raw("clients.id as VendorNumber"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'UDCAccountCode' and telesale_id =telesales.id ) as LdcCode"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'Commodity' and telesale_id =telesales.id   ) as UtilityTypeName"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'Program' and telesale_id =telesales.id ) as ProgramName"),
            DB::raw("CASE
        WHEN  telesales.status = 'verified' THEN 'Good Sale'
        WHEN  telesales.status = 'decline' THEN 'No Sale'
        else
        telesales.status end as Verified "),
            DB::raw("concat( (select meta_value from telesalesdata where meta_key = 'Account Number' and telesale_id =telesales.id limit 1 ), ' ') as AccountNumber"),
            DB::raw("UPPER(( select customer_type from programs where id = (select meta_value from telesalesdata where meta_key = '_programID' and telesale_id =telesales.id )  )) as PremiseTypeName"),
            DB::raw("CASE
                    WHEN  (select meta_value from telesalesdata where meta_key = 'First name' and telesale_id =telesales.id )  != ''
                    THEN  (select meta_value from telesalesdata where meta_key = 'First name' and telesale_id =telesales.id )
                    WHEN  (select meta_value from telesalesdata where meta_key = 'Authorized First name' and telesale_id =telesales.id )  != ''
                    THEN  (select meta_value from telesalesdata where meta_key = 'Authorized First name' and telesale_id =telesales.id )
                ELSE ''
                END  as 'AuthorizationFirstName'"),
            //  DB::raw( "(select meta_value from telesalesdata where meta_key = 'Middle initial' and telesale_id =telesales.id ) as Mi") ,
            DB::raw("CASE
                    WHEN  (select meta_value from telesalesdata where meta_key = 'Last name' and telesale_id =telesales.id )  != ''
                    THEN  (select meta_value from telesalesdata where meta_key = 'Last name' and telesale_id =telesales.id )
                    WHEN  (select meta_value from telesalesdata where meta_key = 'Authorized Last name' and telesale_id =telesales.id )  != ''
                    THEN  (select meta_value from telesalesdata where meta_key = 'Authorized Last name' and telesale_id =telesales.id )
                ELSE ''
                END as 'AuthorizationLastName'"),

            DB::raw("
        CONCAT (
            UPPER((select meta_value from telesalesdata where meta_key = 'ServiceAddress' and telesale_id =telesales.id )),' ' ,
            UPPER((select meta_value from telesalesdata where meta_key = 'ServiceAddress2' and telesale_id =telesales.id ))
        ) as ServiceAddress"),
            // DB::raw( "UPPER((select meta_value from telesalesdata where meta_key = 'ServiceAddress2' and telesale_id =telesales.id )) as ServiceAddress2") ,
            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'ServiceCity' and telesale_id =telesales.id )) as ServiceCity"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'ServiceState' and telesale_id =telesales.id ) as ServiceState"),
            DB::raw("concat((select meta_value from telesalesdata where meta_key = 'ServiceZip' and telesale_id =telesales.id ), ' ') as ServiceZip"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'ServiceCounty' and telesale_id =telesales.id ) as ServiceCounty "),
            DB::raw("
        CASE
            WHEN (select meta_value from telesalesdata where meta_key = 'Email for reward programs' and telesale_id = telesales.id ) != '' THEN (select meta_value from telesalesdata where meta_key = 'Email for reward programs' and telesale_id = telesales.id )
            WHEN (select meta_value from telesalesdata where meta_key = 'Email' and telesale_id =telesales.id ) != '' THEN (select meta_value from telesalesdata where meta_key = 'Email' and telesale_id =telesales.id )
            ELSE ''
        END as Email"),
            DB::raw("concat(REPLACE(REPLACE(trim('(' from (select meta_value from telesalesdata where meta_key = 'Phone Number' and telesale_id =telesales.id ) ),'-',''),')',''  ),' ') as Btn"),
            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'Billing first name' and telesale_id =telesales.id )) as 'AccountFirstName'"),
            //  DB::raw( "(select meta_value from telesalesdata where meta_key = 'Middle initial' and telesale_id =telesales.id ) as Mi") ,
            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'Billing last name' and telesale_id =telesales.id )) as 'AccountLastName'"),

            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'BillingAddress' and telesale_id =telesales.id )) as BillingAddress"),
            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'BillingCity' and telesale_id =telesales.id )) as BillingCity"),
            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'BillingState' and telesale_id =telesales.id )) as BillingState"),
            DB::raw("concat((select meta_value from telesalesdata where meta_key = 'BillingZip' and telesale_id =telesales.id ), ' ') as BillingZip"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'BillingCounty' and telesale_id =telesales.id ) as BillingCounty"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'Language' and telesale_id =telesales.id limit 1 ) as Language"),
            DB::raw("
        CASE
        WHEN (select meta_value from telesalesdata where meta_key = 'Program Code' and telesale_id =telesales.id) != '' THEN (select meta_value from telesalesdata where meta_key = 'Program Code' and telesale_id =telesales.id )

        WHEN (select meta_value from telesalesdata where meta_key = '_gasprogramID' and telesale_id =telesales.id ) != ''
        THEN (select product_id from programs where id = (select meta_value from telesalesdata where meta_key = '_gasprogramID' and telesale_id =telesales.id ))
        ELSE ''
    END as  ProgramCode "),
            DB::raw("concat( format((select meta_value from telesalesdata where meta_key = 'rate' and telesale_id = telesales.id ),4), ' ') as Rate"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'term' and telesale_id = telesales.id ) as Term"),
            DB::raw("
        concat(
        format(
        CASE
            WHEN (select meta_value from telesalesdata where meta_key = 'msf' and telesale_id = telesales.id  ) != '' THEN (select meta_value from telesalesdata where meta_key = 'msf' and telesale_id = telesales.id  )
            WHEN (select meta_value from telesalesdata where meta_key = 'msf' and telesale_id = telesales.id ) IS NULL or (select meta_value from telesalesdata where meta_key = 'msf' and telesale_id = telesales.id ) = ''
            THEN (select msf from programs where id = ( select meta_value from telesalesdata where (meta_key = '_programID' or  meta_key = '_gasprogramID' or meta_key = '_electricprogramID' ) and telesale_id = telesales.id limit 1) )
            ELSE ''
        END, 4), ' ') as Msf"),
            DB::raw("concat(  format( (select meta_value from telesalesdata where meta_key = 'etf' and telesale_id = telesales.id ), 4), ' ') as Etf"),
            DB::raw("salesagent.userid as AgentId"),
            DB::raw("'TELESALES' as Name"),
            //  DB::raw("(select saleschannels from programs where id = (select meta_value from telesalesdata where meta_key = '_programID' and telesale_id =telesales.id )) as Name"),
            DB::raw("tpvagent.userid as TpvAgentId"),

            DB::raw("CONCAT_WS(  ' ',tpvagent.first_name,tpvagent.last_name ) as TpvAgentName"),
            DB::raw("'' as RateClass"),
            DB::raw("telesales.verification_number as MainId"),
            DB::raw("CASE
        WHEN telesales.status = 'verified'
        THEN 'Verified'
        WHEN telesales.status = 'decline'
        THEN (SELECT description FROM dispositions where id = telesales.disposition_id )
        ELSE ''
    END  as Concern"),
            DB::raw("concat('TPV',telesales.id) as ExternalSalesId"),

            DB::raw("(select meta_value from telesalesdata where meta_key = 'utility' and telesale_id =telesales.id ) as Brand"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'Program' and telesale_id =telesales.id ) as ProductName"),
            DB::raw("'' as MarketerCode"),
            DB::raw("locations.name as OfficeName"),
            DB::raw("'Data Entry' as Source")

        )
            ->leftJoin('users as salesagent', 'salesagent.id', '=', 'telesales.user_id')
            ->leftJoin('salescenters', 'salescenters.id', '=', 'salesagent.salescenter_id')
            ->leftJoin('users as tpvagent', 'tpvagent.id', '=', 'telesales.reviewed_by')
            ->leftJoin('dispositions as dispositions', 'dispositions.id', '=', 'telesales.disposition_id')
            ->leftJoin('clients as clients', 'clients.id', '=', 'salesagent.client_id')
            ->leftJoin('salescenterslocations as locations', 'locations.id', '=', 'salesagent.location_id');


        $results = $query->get();

        return $results;
    }


    public function DualDataElectricCommodity($reference_id = null)
    {
        $whereArray = array();
        if (isset($reference_id) && !empty($reference_id)) {
            $whereArray[] = array('telesales.refrence_id', '=', $reference_id);
        }

        $query = DB::table('telesales')->select(
            DB::raw("date_format(telesales.updated_at,'%m/%d/%y %I:%i:%S %p') as CallDateTime"),
            DB::raw("telesales.call_duration as TotalCallTime"),
            DB::raw("'" . $this->vendorname . "' as VendorName"),
            DB::raw("clients.id as VendorNumber"),
            DB::raw("( select code from programs where id = (select meta_value from telesalesdata where meta_key = '_electricprogramID' and telesale_id =telesales.id )  ) as LdcCode"),
            DB::raw("'Electric' as UtilityTypeName"),
            DB::raw("( select name from programs where id = (select meta_value from telesalesdata where meta_key = '_electricprogramID' and telesale_id =telesales.id )  ) as ProgramName"),
            DB::raw("CASE
        WHEN  telesales.status = 'verified' THEN 'Good Sale'
        WHEN  telesales.status = 'decline' THEN 'No Sale'
        else
        telesales.status end as Verified "),
            DB::raw("concat( (select meta_value from telesalesdata where meta_key = 'Electric Account Number' and telesale_id =telesales.id limit 1 ), ' ') as AccountNumber"),
            DB::raw("UPPER(( select customer_type from programs where id = (select meta_value from telesalesdata where meta_key = '_electricprogramID' and telesale_id =telesales.id )  )) as PremiseTypeName"),
            DB::raw("CASE
                WHEN  (select meta_value from telesalesdata where meta_key = 'First name' and telesale_id =telesales.id )  != ''
                THEN  (select meta_value from telesalesdata where meta_key = 'First name' and telesale_id =telesales.id )
                WHEN  (select meta_value from telesalesdata where meta_key = 'Authorized First name' and telesale_id =telesales.id )  != ''
                THEN  (select meta_value from telesalesdata where meta_key = 'Authorized First name' and telesale_id =telesales.id )
            ELSE ''
            END  as 'AuthorizationFirstName'"),
            //  DB::raw( "(select meta_value from telesalesdata where meta_key = 'Middle initial' and telesale_id =telesales.id ) as Mi") ,
            DB::raw("CASE
                WHEN  (select meta_value from telesalesdata where meta_key = 'Last name' and telesale_id =telesales.id )  != ''
                THEN  (select meta_value from telesalesdata where meta_key = 'Last name' and telesale_id =telesales.id )
                WHEN  (select meta_value from telesalesdata where meta_key = 'Authorized Last name' and telesale_id =telesales.id )  != ''
                THEN  (select meta_value from telesalesdata where meta_key = 'Authorized Last name' and telesale_id =telesales.id )
            ELSE ''
            END  as 'AuthorizationLastName'"),

            DB::raw(" CONCAT (
            UPPER((select meta_value from telesalesdata where meta_key = 'ServiceAddress' and telesale_id =telesales.id )),' ' ,
            UPPER((select meta_value from telesalesdata where meta_key = 'ServiceAddress2' and telesale_id =telesales.id ))
        ) as ServiceAddress"),
            // DB::raw( "UPPER((select meta_value from telesalesdata where meta_key = 'ServiceAddress2' and telesale_id =telesales.id )) as ServiceAddress2") ,
            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'ServiceCity' and telesale_id =telesales.id )) as ServiceCity"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'ServiceState' and telesale_id =telesales.id ) as ServiceState"),
            DB::raw("concat((select meta_value from telesalesdata where meta_key = 'ServiceZip' and telesale_id =telesales.id ),' ') as ServiceZip"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'ServiceCounty' and telesale_id =telesales.id ) as ServiceCounty "),
            DB::raw("
        CASE
            WHEN (select meta_value from telesalesdata where meta_key = 'Email for reward programs' and telesale_id = telesales.id ) != '' THEN (select meta_value from telesalesdata where meta_key = 'Email for reward programs' and telesale_id = telesales.id )
            WHEN (select meta_value from telesalesdata where meta_key = 'Email' and telesale_id =telesales.id ) != '' THEN (select meta_value from telesalesdata where meta_key = 'Email' and telesale_id =telesales.id )
            ELSE ''
        END as Email"),
            DB::raw("concat(REPLACE(REPLACE(trim('(' from (select meta_value from telesalesdata where meta_key = 'Phone Number' and telesale_id =telesales.id ) ),'-',''),')',''  ),' ') as Btn"),

            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'Electric Billing first name' and telesale_id =telesales.id )) as 'AccountFirstName'"),
            //DB::raw( "(select meta_value from telesalesdata where meta_key = 'Electric Billing middle name' and telesale_id =telesales.id ) as Mi") ,
            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'Electric Billing last name' and telesale_id =telesales.id )) as 'AccountLastName'"),

            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'ElectricBillingAddress' and telesale_id =telesales.id )) as BillingAddress"),
            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'ElectricBillingCity' and telesale_id =telesales.id )) as BillingCity"),
            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'ElectricBillingState' and telesale_id =telesales.id )) as BillingState"),
            DB::raw("concat((select meta_value from telesalesdata where meta_key = 'ElectricBillingZip' and telesale_id =telesales.id ),' ') as BillingZip"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'ElectricBillingCounty' and telesale_id =telesales.id ) as BillingCounty"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'Language' and telesale_id =telesales.id limit 1 ) as Language"),
            DB::raw("CASE
        WHEN (select meta_value from telesalesdata where meta_key = 'Electric Program Code' and telesale_id =telesales.id) != '' THEN (select meta_value from telesalesdata where meta_key = 'Electric Program Code' and telesale_id =telesales.id )

        WHEN (select meta_value from telesalesdata where meta_key = '_electricprogramID' and telesale_id =telesales.id ) != ''
        THEN (select product_id from programs where id = (select meta_value from telesalesdata where meta_key = '_electricprogramID' and telesale_id =telesales.id ))
        ELSE ''
    END as  ProgramCode"),
            DB::raw("concat( format((select meta_value from telesalesdata where meta_key = 'electric_rate' and telesale_id = telesales.id ),4), ' ') as Rate"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'electric_term' and telesale_id = telesales.id ) as Term"),
            // DB::raw("(select meta_value from telesalesdata where meta_key = 'electric_msf' and telesale_id = telesales.id ) as Msf"),
            DB::raw("
        concat(   format(
            CASE
            WHEN (select meta_value from telesalesdata where meta_key = 'electric_msf' and telesale_id = telesales.id  ) != '' THEN (select meta_value from telesalesdata where meta_key = 'electric_msf' and telesale_id = telesales.id  )
            WHEN (select meta_value from telesalesdata where meta_key = 'electric_msf' and telesale_id = telesales.id ) IS NULL or (select meta_value from telesalesdata where meta_key = 'electric_msf' and telesale_id = telesales.id ) = ''
            THEN (select msf from programs where id = ( select meta_value from telesalesdata where   meta_key = '_electricprogramID'  and telesale_id = telesales.id limit 1) )
            ELSE ''
        END , 4), ' ') as Msf"),
            DB::raw("concat(  format((select meta_value from telesalesdata where meta_key = 'electric_etf' and telesale_id = telesales.id ), 4), ' ') as Etf"),
            DB::raw("salesagent.userid as AgentId"),
            DB::raw("'TELESALES' as Name"),
            //   DB::raw("(select saleschannels from programs where id = (select meta_value from telesalesdata where meta_key = '_programID' and telesale_id =telesales.id )) as Name"),
            DB::raw("tpvagent.userid as TpvAgentId"),

            DB::raw("CONCAT_WS(  ' ',tpvagent.first_name,tpvagent.last_name ) as TpvAgentName"),
            DB::raw("'' as RateClass"),
            DB::raw("telesales.verification_number as MainId"),
            DB::raw("CASE
        WHEN telesales.status = 'verified'
        THEN 'Verified'
        WHEN telesales.status = 'decline'
        THEN (SELECT description FROM dispositions where id = telesales.disposition_id )
        ELSE ''
    END  as Concern"),
            DB::raw("concat('TPV',telesales.id) as ExternalSalesId"),

            DB::raw("(select meta_value from telesalesdata where meta_key = 'electricutility' and telesale_id =telesales.id ) as Brand"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'ElectricProgram' and telesale_id =telesales.id ) as ProductName"),
            DB::raw("'' as MarketerCode"),
            DB::raw("locations.name as OfficeName"),
            DB::raw("'Data Entry' as Source")

        )
            ->leftJoin('users as salesagent', 'salesagent.id', '=', 'telesales.user_id')
            ->leftJoin('salescenters', 'salescenters.id', '=', 'salesagent.salescenter_id')
            ->leftJoin('users as tpvagent', 'tpvagent.id', '=', 'telesales.reviewed_by')
            ->leftJoin('dispositions as dispositions', 'dispositions.id', '=', 'telesales.disposition_id')
            ->leftJoin('clients as clients', 'clients.id', '=', 'salesagent.client_id')
            ->leftJoin('salescenterslocations as locations', 'locations.id', '=', 'salesagent.location_id')
            ->where($whereArray);

        $results = $query->get();

        return $results;
    }

    public function DualDataGasCommodity($reference_id = null)
    {
        $whereArray = array();
        if (isset($reference_id) && !empty($reference_id)) {
            $whereArray[] = array('telesales.refrence_id', '=', $reference_id);
        }

        $query = DB::table('telesales')->select(
            DB::raw("date_format(telesales.updated_at,'%m/%d/%y %I:%i:%S %p') as CallDateTime"),
            DB::raw("telesales.call_duration as TotalCallTime"),
            DB::raw("'" . $this->vendorname . "' as VendorName"),
            DB::raw("clients.id as VendorNumber"),
            DB::raw("( select code from programs where id = (select meta_value from telesalesdata where meta_key = '_gasprogramID' and telesale_id =telesales.id )  ) as LdcCode "),
            DB::raw("'Gas' as UtilityTypeName"),
            DB::raw("( select name from programs where id = (select meta_value from telesalesdata where meta_key = '_gasprogramID' and telesale_id =telesales.id )  ) as ProgramName"),
            DB::raw("CASE
        WHEN  telesales.status = 'verified' THEN 'Good Sale'
        WHEN  telesales.status = 'decline' THEN 'No Sale'
        else
        telesales.status end as Verified "),
            DB::raw("concat((select meta_value from telesalesdata where meta_key = 'Gas Account Number' and telesale_id =telesales.id limit 1 ), ' ') as AccountNumber"),
            DB::raw("UPPER(( select customer_type from programs where id = (select meta_value from telesalesdata where meta_key = '_gasprogramID' and telesale_id =telesales.id )  )) as PremiseTypeName"),
            DB::raw("CASE
                WHEN  (select meta_value from telesalesdata where meta_key = 'First name' and telesale_id =telesales.id )  != ''
                THEN  (select meta_value from telesalesdata where meta_key = 'First name' and telesale_id =telesales.id )
                WHEN  (select meta_value from telesalesdata where meta_key = 'Authorized First name' and telesale_id =telesales.id )  != ''
                THEN  (select meta_value from telesalesdata where meta_key = 'Authorized First name' and telesale_id =telesales.id )
            ELSE ''
            END  as 'AuthorizationFirstName'"),
            //  DB::raw( "(select meta_value from telesalesdata where meta_key = 'Middle initial' and telesale_id =telesales.id ) as Mi") ,
            DB::raw("CASE
          WHEN  (select meta_value from telesalesdata where meta_key = 'Last name' and telesale_id =telesales.id )  != ''
          THEN  (select meta_value from telesalesdata where meta_key = 'Last name' and telesale_id =telesales.id )
          WHEN  (select meta_value from telesalesdata where meta_key = 'Authorized Last name' and telesale_id =telesales.id )  != ''
          THEN  (select meta_value from telesalesdata where meta_key = 'Authorized Last name' and telesale_id =telesales.id )
      ELSE ''
      END  as 'AuthorizationLastName'"),

            DB::raw(" CONCAT (
            UPPER((select meta_value from telesalesdata where meta_key = 'GasServiceAddress' and telesale_id =telesales.id )),' ' ,
            UPPER((select meta_value from telesalesdata where meta_key = 'GasServiceAddress2' and telesale_id =telesales.id ))
        ) as ServiceAddress"),
            // DB::raw( "UPPER((select meta_value from telesalesdata where meta_key = 'GasServiceAddress2' and telesale_id =telesales.id )) as ServiceAddress2") ,
            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'GasServiceCity' and telesale_id =telesales.id )) as ServiceCity"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'GasServiceState' and telesale_id =telesales.id ) as ServiceState"),
            DB::raw("concat((select meta_value from telesalesdata where meta_key = 'GasServiceZip' and telesale_id =telesales.id ),' ') as ServiceZip"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'GasServiceCounty' and telesale_id =telesales.id ) as ServiceCounty "),
            DB::raw("
        CASE
            WHEN (select meta_value from telesalesdata where meta_key = 'Email for reward programs' and telesale_id = telesales.id ) != '' THEN (select meta_value from telesalesdata where meta_key = 'Email for reward programs' and telesale_id = telesales.id )
            WHEN (select meta_value from telesalesdata where meta_key = 'Email' and telesale_id =telesales.id ) != '' THEN (select meta_value from telesalesdata where meta_key = 'Email' and telesale_id =telesales.id )
            ELSE ''
        END as Email"),
            DB::raw("concat(REPLACE(REPLACE(trim('(' from (select meta_value from telesalesdata where meta_key = 'Phone Number' and telesale_id =telesales.id ) ),'-',''),')',''  ),' ') as Btn"),
            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'Gas Billing first name' and telesale_id =telesales.id )) as 'AccountFirstName'"),
            //  DB::raw( "(select meta_value from telesalesdata where meta_key = 'Gas Billing middle name' and telesale_id =telesales.id ) as Mi") ,
            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'Gas Billing last name' and telesale_id =telesales.id )) as 'AccountLastName'"),
            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'GasBillingAddress' and telesale_id =telesales.id )) as BillingAddress"),

            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'GasBillingCity' and telesale_id =telesales.id )) as BillingCity"),
            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'GasBillingState' and telesale_id =telesales.id )) as BillingState"),
            DB::raw("concat((select meta_value from telesalesdata where meta_key = 'GasBillingZip' and telesale_id =telesales.id ), ' ') as BillingZip"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'GasBillingCounty' and telesale_id =telesales.id ) as BillingCounty"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'Language' and telesale_id =telesales.id  limit 1) as Language"),
            DB::raw("
        CASE
        WHEN (select meta_value from telesalesdata where meta_key = 'Gas Program Code' and telesale_id =telesales.id) != ''
        THEN (select meta_value from telesalesdata where meta_key = 'Gas Program Code' and telesale_id =telesales.id )

        WHEN (select meta_value from telesalesdata where meta_key = '_gasprogramID' and telesale_id =telesales.id ) != ''
        THEN (select product_id from programs where id = (select meta_value from telesalesdata where meta_key = '_gasprogramID' and telesale_id = telesales.id ))
        ELSE ''
    END as  ProgramCode"),
            DB::raw("concat( format((select meta_value from telesalesdata where meta_key = 'gas_rate' and telesale_id = telesales.id ),4), ' ') as Rate"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'gas_term' and telesale_id = telesales.id ) as Term"),
            // DB::raw("(select meta_value from telesalesdata where meta_key = 'gas_msf' and telesale_id = telesales.id ) as Msf"),
            DB::raw("
        concat(  format( CASE
            WHEN (select meta_value from telesalesdata where meta_key = 'gas_msf' and telesale_id = telesales.id  ) != '' THEN (select meta_value from telesalesdata where meta_key = 'gas_msf' and telesale_id = telesales.id  )
            WHEN(select meta_value from telesalesdata where meta_key = 'gas_msf' and telesale_id = telesales.id ) IS NULL or (select meta_value from telesalesdata where meta_key = 'gas_msf' and telesale_id = telesales.id ) = ''
            THEN (select msf from programs where id = ( select meta_value from telesalesdata where   meta_key = '_gasprogramID'  and telesale_id = telesales.id limit 1) )
            ELSE ''
        END, 4), ' ') as Msf"),
            DB::raw("concat( format((select meta_value from telesalesdata where meta_key = 'gas_etf' and telesale_id = telesales.id ),4 ) ,' ') as Etf"),
            DB::raw("salesagent.userid as AgentId"),
            DB::raw("'TELESALES' as Name"),
            //  DB::raw("(select saleschannels from programs where id = (select meta_value from telesalesdata where meta_key = '_programID' and telesale_id =telesales.id )) as Name"),
            DB::raw("tpvagent.userid as TpvAgentId"),

            DB::raw("CONCAT_WS(  ' ',tpvagent.first_name,tpvagent.last_name ) as TpvAgentName"),
            DB::raw("'' as RateClass"),
            DB::raw("telesales.verification_number as MainId"),
            DB::raw("CASE
        WHEN telesales.status = 'verified'
        THEN 'Verified'
        WHEN telesales.status = 'decline'
        THEN (SELECT description FROM dispositions where id = telesales.disposition_id )
        ELSE ''
    END  as Concern"),
            DB::raw("concat('TPV',telesales.id) as ExternalSalesId"),

            DB::raw("(select meta_value from telesalesdata where meta_key = 'gasutility' and telesale_id =telesales.id ) as Brand"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'GasProgram' and telesale_id =telesales.id ) as ProductName"),
            DB::raw("'' as MarketerCode"),
            DB::raw("locations.name as OfficeName"),
            DB::raw("'Data Entry' as Source")

        )
            ->leftJoin('users as salesagent', 'salesagent.id', '=', 'telesales.user_id')
            ->leftJoin('salescenters', 'salescenters.id', '=', 'salesagent.salescenter_id')
            ->leftJoin('users as tpvagent', 'tpvagent.id', '=', 'telesales.reviewed_by')
            ->leftJoin('dispositions as dispositions', 'dispositions.id', '=', 'telesales.disposition_id')
            ->leftJoin('clients as clients', 'clients.id', '=', 'salesagent.client_id')
            ->leftJoin('salescenterslocations as locations', 'locations.id', '=', 'salesagent.location_id')
            ->where($whereArray);

        $results = $query->get();

        return $results;
    }

    /* Spark File export */

    public function sparkexportdailydata($client_id, $status, $start_date, $end_date,$salesCenter=null,$commodity=null,$location=null,$export=false)
    {
        $query = DB::table('telesales')->select(
            DB::raw("CASE
                WHEN  telesales.status = 'verified' THEN 'ENROLLMENT'
                WHEN  telesales.status = 'pending' THEN 'Pending' 
                ELSE 'NON ENROLLMENT' 
                END as 'EnrollmentType'"),
            DB::raw("clients.name as Company"),
            DB::raw("( select GROUP_CONCAT(market  SEPARATOR ', ') from utilities where id IN (select utility_id from programs where id IN (select program_id from telesales_programs where telesale_id = telesales.id)) ) as Utility"),
            DB::raw("(select GROUP_CONCAT(commodities.name) from commodities left join form_commodities on form_commodities.commodity_id = commodities.id where form_commodities.form_id = telesales.form_id) as CommodityType"),
            DB::raw("'Mass Market' as ContractPath"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where label = 'Account Number' and form_id = telesales.form_id LIMIT 1) and telesale_id =telesales.id limit 1 ) as UtilityAccountNumber"),
            DB::raw("CASE
                WHEN  (select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'first_name' and telesale_id =telesales.id LIMIT 1)  != ''
                THEN  (select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'first_name' and telesale_id =telesales.id  LIMIT 1 )
                ELSE ''
                END  as 'ServiceFirstName'"),
            DB::raw("CASE
                WHEN  (select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'last_name' and telesale_id =telesales.id  LIMIT 1)  != ''
                THEN  (select meta_value from telesalesdata where field_id = (select id from form_fields where type = 'fullname' and is_primary = 1 and form_id = telesales.form_id LIMIT 1) and meta_key = 'last_name' and telesale_id =telesales.id  LIMIT 1)
                ELSE ''
                END  as 'ServiceLastName'"),
            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'service_address_1' and telesale_id =telesales.id LIMIT 1)) as ServiceAddress1"),
            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'service_address_2' and telesale_id =telesales.id LIMIT 1)) as ServiceAddress2"),
            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'service_city' and telesale_id =telesales.id LIMIT 1)) as ServiceCity"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'service_state' and telesale_id =telesales.id LIMIT 1) as ServiceState"),
            DB::raw("concat((select meta_value from telesalesdata where meta_key = 'service_zipcode' and telesale_id =telesales.id LIMIT 1), ' ') as ServiceZip"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where label = 'Email' and form_id = telesales.form_id LIMIT 1) and telesale_id =telesales.id LIMIT 1) as ServiceEmail"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where label = 'Phone Number' and form_id = telesales.form_id LIMIT 1) and telesale_id =telesales.id LIMIT 1) as ServicePhone"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where label = 'Billing Name' and form_id = telesales.form_id LIMIT 1) and meta_key = 'first_name' and telesale_id =telesales.id ) as 'BillingFirstName'"),
            DB::raw("(select meta_value from telesalesdata where field_id = (select id from form_fields where label = 'Billing Name' and form_id = telesales.form_id LIMIT 1) and meta_key = 'last_name' and telesale_id =telesales.id ) as 'BillingLastName'"),
            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'billing_address_1' and telesale_id =telesales.id LIMIT 1)) as BillingAddress1"),
            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'billing_address_2' and telesale_id =telesales.id LIMIT 1)) as BillingAddress2"),
            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'billing_city' and telesale_id =telesales.id LIMIT 1)) as BillingCity"),
            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'billing_state' and telesale_id =telesales.id LIMIT 1)) as BillingState"),
            DB::raw("concat((select meta_value from telesalesdata where meta_key = 'billing_zipcode' and telesale_id =telesales.id LIMIT 1), ' ') as BillingZip"),

            DB::raw("salescenters.name as SalesCenter"),
            DB::raw("telesales.refrence_id as VerificationID"),
            DB::raw("telesales.id as ExternalSalesID"),
            DB::raw("CASE
                WHEN agent.agent_type = 'tele' THEN 'Telesales'
                WHEN agent.agent_type = 'd2d' THEN 'D2DSales'
                ELSE '' END as SalesChannel"),
            DB::raw("concat( salesagent.first_name, ' ',salesagent.last_name ) as SalesAgent"),
            DB::raw("date_format(telesales.created_at,'%m/%d/%Y') as SoldDate"),
            DB::raw("telesales.call_id as TPVCall"),
            DB::raw("'Res single' as RateClass")
            )
            ->leftJoin('users as salesagent', 'salesagent.id', '=', 'telesales.user_id')
            ->leftJoin('salesagent_detail as agent', 'agent.user_id', '=', 'telesales.user_id')
            ->leftJoin('salescenters', 'salescenters.id', '=', 'salesagent.salescenter_id')
            ->leftJoin('users as tpvagent', 'tpvagent.id', '=', 'telesales.reviewed_by')
            ->leftJoin('dispositions as dispositions', 'dispositions.id', '=', 'telesales.disposition_id')
            ->leftJoin('clients as clients', 'clients.id', '=', 'salesagent.client_id')
            //->leftJoin('form_commodities', 'form_commodities.form_id', '=', 'telesales.form_id')
            ->whereBetween('telesales.created_at',array($start_date,$end_date));

           /* ->where('telesales.created_at', '>=', $start_date)->where('telesales.created_at', '<=', $end_date)*/
        if (!empty($client_id)) {
            $query->where('telesales.client_id', $client_id);
        }
        if (!empty($commodity)) {
            $query->whereIn('telesales.form_id',function($query) use($commodity) {
               $query->select('form_id')->from('form_commodities')->where('commodity_id',$commodity)->distinct('form_id')->get();
            });
        }
        if (!empty($salesCenter)) {
            $query->where('salescenters.id', $salesCenter);
        }
        if (auth()->user()->hasMultiLocations()) {
            $locationIds = auth()->user()->locations->pluck('id');
            $query->whereIn('agent.location_id', $locationIds);
        }
        if (!empty($location)) {
            $query->where('agent.location_id', $location);
        }
        if (!empty($status)) {
            if($status == 'non_enrollment') {
                $query = $query->whereIn('telesales.status', ['decline','cancel','hangup','expired']);
            } else {
                $query = $query->where('telesales.status', $status);
            }
        }
        if($export) {
            $results = $query->get();
        } else {
            $results = $query;
        }

        return $results;
    }

    public function SparkDualDataElectricCommodity($reference_id = null)
    {
        $whereArray = array();
        if (isset($reference_id) && !empty($reference_id)) {
            $whereArray[] = array('telesales.refrence_id', '=', $reference_id);
        }

        $query = DB::table('telesales')->select(
            DB::raw("CASE
        WHEN  telesales.status = 'verified' THEN 'ENROLLMENT'
        WHEN  telesales.status = 'decline' THEN 'NON ENROLLMENT'
        else
        telesales.status end as EnrollmentType "),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'electricutility' and telesale_id =telesales.id )  as Company"),
            DB::raw("( select market from utilities where id = (select meta_value from telesalesdata where meta_key = '_electricutilityID' and telesale_id =telesales.id )  ) as Utility"),
            DB::raw("'Electric' as CommodityType"),
            DB::raw("'Mass Market' as ContractPath"),
            DB::raw("concat((select meta_value from telesalesdata where meta_key = 'Electric Account Number' and telesale_id =telesales.id limit 1 ), ' ')  as UtilityAccountNumber"),
            DB::raw("CASE
        WHEN  (select meta_value from telesalesdata where meta_key = 'First name' and telesale_id =telesales.id )  != ''
        THEN  (select meta_value from telesalesdata where meta_key = 'First name' and telesale_id =telesales.id )
        WHEN  (select meta_value from telesalesdata where meta_key = 'Authorized First name' and telesale_id =telesales.id )  != ''
        THEN  (select meta_value from telesalesdata where meta_key = 'Authorized First name' and telesale_id =telesales.id )
    ELSE ''
    END  as 'ServiceFirstName'"),
            DB::raw("CASE
        WHEN  (select meta_value from telesalesdata where meta_key = 'Last name' and telesale_id =telesales.id )  != ''
        THEN  (select meta_value from telesalesdata where meta_key = 'Last name' and telesale_id =telesales.id )
        WHEN  (select meta_value from telesalesdata where meta_key = 'Authorized Last name' and telesale_id =telesales.id )  != ''
        THEN  (select meta_value from telesalesdata where meta_key = 'Authorized Last name' and telesale_id =telesales.id )
    ELSE ''
    END  as 'ServiceLastName'"),
            DB::raw("
            UPPER((select meta_value from telesalesdata where meta_key = 'ServiceAddress' and telesale_id =telesales.id )) as ServiceAddress1"),

            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'ServiceAddress2' and telesale_id =telesales.id )) as ServiceAddress2"),
            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'ServiceCity' and telesale_id =telesales.id )) as ServiceCity"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'ServiceState' and telesale_id =telesales.id ) as ServiceState"),
            DB::raw("concat((select meta_value from telesalesdata where meta_key = 'ServiceZip' and telesale_id =telesales.id ), ' ') as ServiceZip"),
            DB::raw("
        CASE
            WHEN (select meta_value from telesalesdata where meta_key = 'Email for reward programs' and telesale_id = telesales.id ) != '' THEN (select meta_value from telesalesdata where meta_key = 'Email for reward programs' and telesale_id = telesales.id )
            WHEN (select meta_value from telesalesdata where meta_key = 'Email' and telesale_id =telesales.id ) != '' THEN (select meta_value from telesalesdata where meta_key = 'Email' and telesale_id =telesales.id )
            ELSE ''
        END as ServiceEmail"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'Phone Number' and telesale_id =telesales.id ) as ServicePhone"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'Electric Billing first name' and telesale_id =telesales.id ) as 'BillingFirstName'"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'Electric Billing last name' and telesale_id =telesales.id ) as 'BillingLastName'"),

            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'ElectricBillingAddress' and telesale_id =telesales.id )) as BillingAddress"),
            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'ElectricBillingAddress2' and telesale_id =telesales.id )) as BillingAddress2"),
            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'ElectricBillingCity' and telesale_id =telesales.id )) as BillingCity"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'ElectricBillingState' and telesale_id =telesales.id ) as BillingState"),
            DB::raw("concat((select meta_value from telesalesdata where meta_key = 'ElectricBillingZip' and telesale_id =telesales.id ),' ') as BillingZip"),

            DB::raw("salescenters.name as SalesCenter"),
            DB::raw("telesales.refrence_id as VerificationID"),
            DB::raw("telesales.id as ExternalSalesID"),
            DB::raw("CASE
                WHEN agent.agent_type = 'tele' THEN 'Telesales'
                WHEN agent.agent_type = 'd2d' THEN 'D2DSales'
                ELSE '' END as SalesChannel"),
            DB::raw("concat( salesagent.first_name, ' ',salesagent.last_name ) as SalesAgent"),
            DB::raw("date_format(telesales.created_at,'%m/%d/%Y') as SoldDate"),
            DB::raw("telesales.call_id as TPVCall"),
            DB::raw("'Res single' as RateClass")
        )
            ->leftJoin('users as salesagent', 'salesagent.id', '=', 'telesales.user_id')
            ->leftJoin('salesagent_detail as agent', 'agent.user_id', '=', 'telesales.user_id')
            ->leftJoin('salescenters', 'salescenters.id', '=', 'salesagent.salescenter_id')
            ->leftJoin('users as tpvagent', 'tpvagent.id', '=', 'telesales.reviewed_by')
            ->leftJoin('dispositions as dispositions', 'dispositions.id', '=', 'telesales.disposition_id')
            ->leftJoin('clients as clients', 'clients.id', '=', 'salesagent.client_id')
            ->leftJoin('salescenterslocations as locations', 'locations.id', '=', 'salesagent.location_id')
            ->leftJoin('form_commodities', 'form_commodities.form_id', '=', 'telesales.form_id')
            ->where($whereArray);
        $results = $query->get();

        return $results;
    }

    public function SparkDualDataGasCommodity($reference_id = null)
    {
        $whereArray = array();
        if (isset($reference_id) && !empty($reference_id)) {
            $whereArray[] = array('telesales.refrence_id', '=', $reference_id);
        }

        $query = DB::table('telesales')->select(
            DB::raw("CASE
        WHEN  telesales.status = 'verified' THEN 'ENROLLMENT'
        WHEN  telesales.status = 'decline' THEN 'NON ENROLLMENT'
        else
        telesales.status end as EnrollmentType"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'gasutility' and telesale_id =telesales.id ) as Company"),
            DB::raw("( select market from utilities where id = (select meta_value from telesalesdata where meta_key = '_gasutilityID' and telesale_id =telesales.id )  ) as Utility"),
            DB::raw("'Gas' as CommodityType"),
            DB::raw("'Mass Market' as ContractPath"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'Gas Account Number' and telesale_id =telesales.id limit 1 ) as UtilityAccountNumber"),
            DB::raw("'' as AlternateAccountNumber"),
            DB::raw("'' as UtilityMeterNumber"),
            DB::raw("'' as MeterType"),
            DB::raw("( select customer_type from programs where id = (select meta_value from telesalesdata where meta_key = '_gasprogramID' and telesale_id =telesales.id )  ) as CustomerType"),
            DB::raw("'' as CompanyName"),
            DB::raw("'' as DBAName"),
            DB::raw("'' as NameKey"),
            DB::raw("CASE
        WHEN  (select meta_value from telesalesdata where meta_key = 'First name' and telesale_id =telesales.id )  != ''
        THEN  (select meta_value from telesalesdata where meta_key = 'First name' and telesale_id =telesales.id )
        WHEN  (select meta_value from telesalesdata where meta_key = 'Authorized First name' and telesale_id =telesales.id )  != ''
        THEN  (select meta_value from telesalesdata where meta_key = 'Authorized First name' and telesale_id =telesales.id )
    ELSE ''
    END  as 'ServiceFirstName'"),
            DB::raw("CASE
        WHEN  (select meta_value from telesalesdata where meta_key = 'Last name' and telesale_id =telesales.id )  != ''
        THEN  (select meta_value from telesalesdata where meta_key = 'Last name' and telesale_id =telesales.id )
        WHEN  (select meta_value from telesalesdata where meta_key = 'Authorized Last name' and telesale_id =telesales.id )  != ''
        THEN  (select meta_value from telesalesdata where meta_key = 'Authorized Last name' and telesale_id =telesales.id )
    ELSE ''
    END  as 'ServiceLastName'"),
            // DB::raw( " CONCAT (
            //     UPPER((select meta_value from telesalesdata where meta_key = 'ServiceAddress' and telesale_id =telesales.id )),' ' ,
            //     UPPER((select meta_value from telesalesdata where meta_key = 'ServiceAddress2' and telesale_id =telesales.id ))
            // ) as ServiceAddress") ,
            DB::raw("
            UPPER((select meta_value from telesalesdata where meta_key = 'ServiceAddress' and telesale_id =telesales.id )) as ServiceAddress1"),

            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'ServiceAddress2' and telesale_id =telesales.id )) as ServiceAddress2"),
            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'ServiceCity' and telesale_id =telesales.id )) as ServiceCity"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'ServiceState' and telesale_id =telesales.id ) as ServiceState"),
            DB::raw("concat((select meta_value from telesalesdata where meta_key = 'ServiceZip' and telesale_id =telesales.id ), ' ') as ServiceZip"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'ServiceCounty' and telesale_id =telesales.id ) as ServiceCounty "),
            DB::raw("
        CASE
            WHEN (select meta_value from telesalesdata where meta_key = 'Email for reward programs' and telesale_id = telesales.id ) != '' THEN (select meta_value from telesalesdata where meta_key = 'Email for reward programs' and telesale_id = telesales.id )
            WHEN (select meta_value from telesalesdata where meta_key = 'Email' and telesale_id =telesales.id ) != '' THEN (select meta_value from telesalesdata where meta_key = 'Email' and telesale_id =telesales.id )
            ELSE ''
        END as ServiceEmail"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'Phone Number' and telesale_id =telesales.id ) as ServicePhone"),
            DB::raw("'' as ServiceFax"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'Gas Billing first name' and telesale_id =telesales.id ) as 'BillingFirstName'"),
            //  DB::raw( "(select meta_value from telesalesdata where meta_key = 'Middle initial' and telesale_id =telesales.id ) as Mi") ,
            DB::raw("(select meta_value from telesalesdata where meta_key = 'Gas Billing last name' and telesale_id =telesales.id ) as 'BillingLastName'"),

            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'GasBillingAddress' and telesale_id =telesales.id )) as BillingAddress"),
            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'GasBillingAddress2' and telesale_id =telesales.id )) as BillingAddress2"),
            DB::raw("UPPER((select meta_value from telesalesdata where meta_key = 'GasBillingCity' and telesale_id =telesales.id )) as BillingCity"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'GasBillingState' and telesale_id =telesales.id ) as BillingState"),
            DB::raw("concat((select meta_value from telesalesdata where meta_key = 'GasBillingZip' and telesale_id =telesales.id ), ' ') as BillingZip"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'GasBillingCounty' and telesale_id =telesales.id ) as BillingCounty"),
            DB::raw("'' as BillingEmail"),
            DB::raw("'' as BillingPhone"),
            DB::raw("'' as BillingFax"),
            DB::raw("'' as DateOfBirth"),
            DB::raw("'' as SSN"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'Language' and telesale_id =telesales.id  limit 1) as Language"),
            DB::raw("'' as DeliveryType"),
            DB::raw("'' as LifeSupport"),
            DB::raw("'' as TaxID"),
            DB::raw("'' as TaxExempt"),
            DB::raw("'' as 'TaxExempt%'"),
            DB::raw("'' as PromoCode"),
            DB::raw("'' as ReferFriendID"),
            DB::raw("'' as PromoCode"),
            DB::raw("'' as ProductType"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'GasProgram' and telesale_id =telesales.id ) as ProductOffering"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'gas_rate' and telesale_id = telesales.id ) as CommodityPrice"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'gas_term' and telesale_id = telesales.id ) as TermMonths"),
            DB::raw("
        CASE
            WHEN (select meta_value from telesalesdata where meta_key = 'gas_msf' and telesale_id = telesales.id  ) != '' THEN (select meta_value from telesalesdata where meta_key = 'gas_msf' and telesale_id = telesales.id  )
            WHEN (select meta_value from telesalesdata where meta_key = 'gas_msf' and telesale_id = telesales.id ) IS NULL or (select meta_value from telesalesdata where meta_key = 'gas_msf' and telesale_id = telesales.id ) = ''
            THEN (select msf from programs where id = ( select meta_value from telesalesdata where   meta_key = '_gasprogramID'  and telesale_id = telesales.id limit 1) )
            ELSE ''
        END as MonthlyFee"),

            //   DB::raw("(select meta_value from telesalesdata where meta_key = 'gas_msf' and telesale_id = telesales.id ) as MonthlyFee"),
            DB::raw("'' as DailyCharge"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'gas_etf' and telesale_id = telesales.id ) as ETF"),
            DB::raw("'' as RolloverProduct"),
            DB::raw("'' as isPriorityMovein"),
            DB::raw("'' as MoveInDate"),
            DB::raw("'' as SwitchDate"),
            DB::raw("'' as StartMonthYear"),
            DB::raw("'' as ReleaseDate"),
            DB::raw("'' as ReadCycle"),
            DB::raw("'Intersoft' as Marketer"),
            DB::raw("'' as Marketer2"),
            DB::raw("telesales.refrence_id as ExternalSalesID"),
            DB::raw("'Telesales' as SalesChannel"),
            DB::raw("concat( salesagent.first_name, ' ',salesagent.last_name ) as SalesAgent"),
            DB::raw("date_format(telesales.created_at,'%m/%d/%Y') as SoldDate"),
            DB::raw("'' as 'TelemarketingCall'"),
            DB::raw("telesales.call_id as TPVCall"),
            DB::raw("'' as AcknowledgeLetterOfAgency"),
            DB::raw("'' as Notes"),
            DB::raw("'' as ServicePlanOptionId"),
            DB::raw("'' as GRT"),
            DB::raw("'' as TOUMeter"),
            DB::raw("'' as GasPool"),
            DB::raw("'' as Zone"),
            DB::raw("'' as Pipeline"),
            DB::raw("'' as AggregatorFee"),
            DB::raw("'' as Adder"),
            DB::raw("'Res single' as RateClass"),
            DB::raw("'' as 'Usage'"),
            DB::raw("'' as JanContractedUsage"),
            DB::raw("'' as FebContractedUsage"),
            DB::raw("'' as MarContractedUsage"),
            DB::raw("'' as AprContractedUsage"),
            DB::raw("'' as MayContractedUsage"),
            DB::raw("'' as JunContractedUsage"),
            DB::raw("'' as JulContractedUsage"),
            DB::raw("'' as AugContractedUsage"),
            DB::raw("'' as SepContractedUsage"),
            DB::raw("'' as OctContractedUsage"),
            DB::raw("'' as NovContractedUsage"),
            DB::raw("'' as DecContractedUsage"),
            DB::raw("'' as UpperBand"),
            DB::raw("'' as LowerBand"),
            DB::raw("'' as FeeAbove"),
            DB::raw("'' as OverIndex"),
            DB::raw("'' as FeeBelow"),
            DB::raw("'' as UnderIndex"),
            DB::raw("'' as ChargeFuel"),
            DB::raw("'' as NetTerms"),
            DB::raw("'' as EffectiveStartDate"),
            DB::raw("'' as EffectiveEndDate"),
            DB::raw("'' as CreditCheck"),
            DB::raw("'' as MobilePhone"),
            DB::raw("'' as CustomField1"),
            DB::raw("'' as CustomField2"),
            DB::raw("'' as CustomField3"),
            DB::raw("'' as CustomField4"),
            DB::raw("'' as CustomField5")


        // DB::raw( "telesales.call_duration as TotalCallTime") ,
        // DB::raw( "clients.id as VendorNumber") ,
        // DB::raw( "(select meta_value from telesalesdata where meta_key = 'Program Code' and telesale_id =telesales.id ) as LdcCode") ,


        // DB::raw("(select meta_value from telesalesdata where meta_key = 'Program Code' and telesale_id =telesales.id ) as ProgramCode"),

        // DB::raw("salesagent.userid as AgentId"),
        // DB::raw("tpvagent.userid as TpvAgentId"),
        // DB::raw("concat( tpvagent.first_name, ' ',tpvagent.last_name ) as TpvAgentName"),
        // DB::raw("'' as RateClass"),
        // DB::raw("telesales.verification_number as MainId"),
        // DB::raw("'' as Concern"),


        // DB::raw("(select meta_value from telesalesdata where meta_key = 'Program' and telesale_id =telesales.id ) as ProductName"),
        // DB::raw("'' as MarketerCode"),
        // DB::raw("locations.name as OfficeName") ,
        // DB::raw("'' as Source")

        )
            ->leftJoin('users as salesagent', 'salesagent.id', '=', 'telesales.user_id')
            ->leftJoin('salescenters', 'salescenters.id', '=', 'salesagent.salescenter_id')
            ->leftJoin('users as tpvagent', 'tpvagent.id', '=', 'telesales.reviewed_by')
            ->leftJoin('dispositions as dispositions', 'dispositions.id', '=', 'telesales.disposition_id')
            ->leftJoin('clients as clients', 'clients.id', '=', 'salesagent.client_id')
            ->leftJoin('salescenterslocations as locations', 'locations.id', '=', 'salesagent.location_id')
            ->where($whereArray);


        $results = $query->get();

        return $results;
    }

    function salesagentactivity($params)
    {
        $whereArray = array();
        $export = "";
        if (isset($params['refrence_id']) && !empty($params['refrence_id'])) {
            $whereArray[] = array('telesales.refrence_id', '=', $params['refrence_id']);
        }
        if (isset($params['client']) && !empty($params['client'])) {
            $whereArray[] = array('telesales.client_id', '=', $params['client']);
        }
        if (isset($params['salesagentid']) && !empty($params['salesagentid'])) {
            $whereArray[] = array('salesagent.userid', '=', $params['salesagentid']);
        }
        if (isset($params['salesagent']) && !empty($params['salesagent'])) {
            $whereArray[] = array('salesagent.id', '=', $params['salesagent']);
        }


        if (isset($params['tpvagentid']) && !empty($params['tpvagentid'])) {
            $whereArray[] = array('tpvagent.userid', '=', $params['tpvagentid']);
        }
        if (isset($params['location_id']) && !empty($params['location_id'])) {
            $whereArray[] = array('salesagent.location_id', '=', $params['location_id']);
        }
        if (isset($params['vendorstatus']) && !empty($params['vendorstatus'])) {
            $whereArray[] = array('clients.status', '=', $params['vendorstatus']);
        }
        if (isset($params['userstatus']) && !empty($params['userstatus'])) {
            $whereArray[] = array('salesagent.status', '=', $params['userstatus']);
        }
        if (isset($params['export']) && !empty($params['export'])) {
            $export = 1;
        }
        if (isset($params['status']) && !empty($params['status'])) {
            $status = [$params['status']];
        } else {
            $status = ['verified', 'decline'];
        }


        $whereRaw = "1 = 1";
        if ((isset($params['date_start']) && !empty($params['date_start'])) && (isset($params['date_end']) && !empty($params['date_end']))) {
            $whereRaw .= " and  date_format(telesales.created_at,'%Y-%m-%d') >= '" . $this->formatdate($params['date_start']) . "'";
            $whereRaw .= " and  date_format(telesales.created_at,'%Y-%m-%d') <= '" . $this->formatdate($params['date_end']) . "'";
        }
        if ((isset($params['date_start']) && !empty($params['date_start'])) && (isset($params['date_end']) && empty($params['date_end']))) {
            $whereRaw .= " and  date_format(telesales.created_at,'%Y-%m-%d') <= '" . $this->formatdate($params['date_end']) . "'";
        }
        if ((isset($params['date_start']) && empty($params['date_start'])) && (isset($params['date_end']) && !empty($params['date_end']))) {
            $whereRaw .= " and  date_format(telesales.created_at,'%Y-%m-%d') >= '" . $this->formatdate($params['date_start']) . "'";
        }
        if (isset($params['program']) && !empty($params['program'])) {
            $whereRaw .= " and  telesales.id in ( SELECT telesale_id FROM `telesalesdata` WHERE (`meta_key` = '_programID' or `meta_key` = '_electricprogramID' or `meta_key` = '_gasprogramID' )  and  meta_value = '" . $params['program'] . "')";
        }


        $query = DB::table('telesales')->select(
            DB::raw("'Intersoft' as VendorName"),
            DB::raw("clients.id as VendorNumber"),
            DB::raw("salesagent.userid as AgentId"),
            DB::raw("salesagent.first_name as FirstName"),
            DB::raw("salesagent.last_name as LastName"),
            DB::raw("telesales.refrence_id  as ReferenceId"),
            DB::raw("date_format(telesales.created_at,'%m/%d/%Y') as LeadDate"),
            DB::raw("CASE
                WHEN  telesales.status = 'verified' THEN 'Good Sale'
                WHEN  telesales.status = 'decline' THEN 'No Sale'
                else
                telesales.status end as Verified ")

        )
            ->leftJoin('users as salesagent', 'salesagent.id', '=', 'telesales.user_id')
            ->leftJoin('salescenters', 'salescenters.id', '=', 'salesagent.salescenter_id')
            ->leftJoin('users as tpvagent', 'tpvagent.id', '=', 'telesales.reviewed_by')
            ->leftJoin('dispositions as dispositions', 'dispositions.id', '=', 'telesales.disposition_id')
            ->leftJoin('clients as clients', 'clients.id', '=', 'salesagent.client_id')
            ->leftJoin('salescenterslocations as locations', 'locations.id', '=', 'salesagent.location_id');


        $query->where($whereArray)
            ->whereRaw($whereRaw);
        if (isset($params['accountnumber']) && !empty($params['accountnumber'])) {
            $account_number = $params['accountnumber'];
            $query->when($account_number, function ($query) use ($account_number) {

                return $query->whereIn('telesales.id', function ($query) use ($account_number) {
                    $query->select('telesale_id')
                        ->from(with(new Telesalesdata)->getTable())
                        ->where('meta_key', 'like', '%Account Number%')
                        ->where('meta_value', '=', $account_number);
                });
            });

        }

        $query->whereIn('telesales.status', $status);
        $query->orderBy('telesales.id', 'desc');
        if ($export == 1) {
            $results = $query->get();
        } else {
            $results = $query->paginate(20);
        }

        // ->toSql();

        return $results;

    }

    function salesagentactivityNew($params)
    {
        $whereArray = array();
        $export = "";
        // dd($params);
        if (isset($params['client']) && !empty($params['client'])) {
            $whereArray[] = array('telesales.client_id', '=', $params['client']);
        }


        if (isset($params['export']) && !empty($params['export'])) {
            $export = $params['export'];
        }
        if (isset($params['status']) && !empty($params['status'])) {
            $status = [$params['status']];
        } else {
            $status = ['verified', 'decline','pending','hangup','cancel','expired','self-verified'];
        }


        $whereRaw = "1 = 1";
        if ((isset($params['date_start']) && !empty($params['date_start'])) && (isset($params['date_end']) && !empty($params['date_end']))) {
            $whereRaw .= " and telesales.created_at >= '" .$params['date_start'] . "'";
            $whereRaw .= " and telesales.created_at < '" . $params['date_end'] . "'";
        }
        $query = DB::table('telesales')->select(
            DB::raw("brand_contacts.name as Brand"),
            DB::raw("salescenters.name as VendorName"),
            DB::raw("salescenters.id as VendorNumber"),
            DB::raw("salesagent.userid as AgentId"),
            DB::raw("salesagent.first_name as FirstName"),
            DB::raw("salesagent.last_name as LastName"),
            DB::raw("telesales.refrence_id  as ReferenceId"),
            DB::raw("date_format(telesales.created_at,'%m/%d/%Y') as LeadDate"),
            DB::raw("(select GROUP_CONCAT(commodities.name) from commodities left join form_commodities on form_commodities.commodity_id = commodities.id where form_commodities.form_id = telesales.form_id) as CommodityType"),
            DB::raw("(select GROUP_CONCAT(programs.name SEPARATOR ', ') from programs left join telesales_programs on telesales_programs.program_id = programs.id where telesales_programs.telesale_id = telesales.id) as ProgramName"),
            DB::raw("(select GROUP_CONCAT(programs.code SEPARATOR ', ') from programs left join telesales_programs on telesales_programs.program_id = programs.id where telesales_programs.telesale_id = telesales.id) as ProgramCode"),
            DB::raw("CASE
                WHEN  telesales.status = 'verified' THEN 'Good Sale'
                WHEN  telesales.status = 'decline' THEN 'No Sale'
                WHEN  telesales.status = 'hangup' THEN 'Disconnected'
                WHEN  telesales.status = 'cancel' THEN 'Cancelled'
                else 
                CONCAT(UCASE(LEFT(telesales.status, 1)),SUBSTRING(telesales.status, 2)) end as VerificationStatus "),
            DB::raw("CASE
                WHEN agent.agent_type = 'tele' THEN 'Telesales'
                WHEN agent.agent_type = 'd2d' THEN 'D2DSales'
                ELSE '' END as Channel"),
            DB::raw("tpvagent.userid as TpvAgentId"),
            DB::raw("CONCAT_WS(  ' ',tpvagent.first_name,tpvagent.last_name ) as TpvAgentName"),
            DB::raw("date_format(telesales.updated_at,'%m/%d/%y %I:%i:%S %p') as CallDateTime"),
            DB::raw("telesales.call_duration as TotalCallTime"),
            DB::raw("(select meta_value from telesalesdata where meta_key = 'Language' and telesale_id =telesales.id  limit 1) as Language"),
            DB::raw("CASE
                WHEN telesales.status = 'verified'
                THEN 'Verified'
                WHEN telesales.status = 'decline'
                THEN (SELECT description FROM dispositions where id = telesales.disposition_id )
                ELSE ''
            END  as Disposition"),
            DB::raw("concat('TPV',telesales.id) as ExternalSalesId")

        )
            ->leftJoin('users as salesagent', 'salesagent.id', '=', 'telesales.user_id')
            ->leftJoin('salescenters', 'salescenters.id', '=', 'salesagent.salescenter_id')
            ->leftJoin('users as tpvagent', 'tpvagent.id', '=', 'telesales.reviewed_by')
            ->leftJoin('salesagent_detail as agent', 'agent.user_id', '=', 'telesales.user_id')
            ->leftJoin('dispositions as dispositions', 'dispositions.id', '=', 'telesales.disposition_id')
            ->leftJoin('clients as clients', 'clients.id', '=', 'salesagent.client_id')
            ->leftJoin('telesales_programs','telesales_programs.telesale_id','=','telesales.id')
            ->leftJoin('programs','programs.id','=','telesales_programs.program_id')
            ->leftJoin('utilities','programs.utility_id','=','utilities.id')
            ->leftJoin('brand_contacts','utilities.brand_id','=','brand_contacts.id');
        $query->where($whereArray)
            ->whereRaw($whereRaw);
        $query->whereIn('telesales.status', $status)->where('telesales.deleted_at','=',null);

        if (isset($params['commodity']) && !empty($params['commodity'])) {
            $commodity =$params['commodity'];
            $query->whereIn('telesales.form_id',function($query) use($commodity) {
               $query->select('form_id')->from('form_commodities')->where('commodity_id',$commodity)->distinct('form_id')->get();
            });
        }
        if (isset($params['sales_center']) && !empty($params['sales_center'])) {
            $query->where('salescenters.id', $params['sales_center']);
        }
        if (auth()->user()->hasMultiLocations()) {
            $locationIds = auth()->user()->locations->pluck('id');
            $query->whereIn('agent.location_id', $locationIds);
        }
        if (isset($params['location_id']) && !empty($params['location_id'])) {
            $query->where('agent.location_id', $params['location_id']);
        }
        if (isset($params['brandId']) && !empty($params['brandId'])) {
            $query->where('brand_contacts.id', $params['brandId']);
        }
        if ($export == 1) {
            $results = $query->get();
        } else {
            $results = $query;
        }

        // ->toSql();

        return $results;

    }

    function getCriticalAlertReport($params)
    {
        $query = DB::table('telesales')->select(
            DB::raw("telesales.refrence_id  as LeadNumber"),
            DB::raw("CONCAT_WS(' ',max(case when meta_key = 'first_name' then meta_value end), max(case when meta_key = 'middle_initial' then meta_value end), max(case when meta_key = 'last_name' then meta_value end)) as CustomerName"),
            DB::raw("GROUP_CONCAT(distinct clh.email_alert_message ) as AlertDescription"),
            /*DB::raw("CASE
                WHEN  telesales.status = 'cancel' THEN 'cancelled'
                else 
                'proceed' end as AlertStatus"),*/
            DB::raw("CASE
                WHEN  telesales.status = 'cancel' THEN 'cancelled'
                WHEN  telesales.status = 'decline' THEN 'declined'
                WHEN  telesales.status = 'hangup' THEN 'disconnected' 
                WHEN  telesales.status = 'self-verified' THEN 'Self verified' 
                else
                telesales.status end as LeadStatus"),
            DB::raw("clients.name as Client"),
            DB::raw("brand_contacts.name as Brand"),
            DB::raw("salescenters.name as SalesCenter"),
            DB::raw("CONCAT_WS(  ',',salescenters.street,salescenters.city,salescenters.state,salescenters.country,salescenters.zip) as SalesCenterLocation"),
            DB::raw("CONCAT_WS(  ' ',salesagent.first_name,salesagent.last_name ) as Agent"),
            "sd.external_id as ExternalId",
            DB::raw("date_format(telesales.created_at,'%m/%d/%Y %H:%i:%s') as DateOfSubmission"),
            DB::raw("CASE
            WHEN telesales.status = 'pending' THEN ''
                WHEN telesales.status = 'cancel' THEN ''
                else
                    CASE
                        WHEN telesales.verification_method = 3 THEN ''
                        WHEN telesales.verification_method = 4 THEN ''
                    else
                        date_format(telesales.reviewed_at,'%m/%d/%Y %H:%i:%s') end end as DateOfTPV")
            
            
            // DB::raw("critical_logs_history.email_alert_message as alert_description")
        )
        ->leftJoin('users as salesagent', 'salesagent.id', '=', 'telesales.user_id')
        ->leftJoin('salescenters', 'salescenters.id', '=', 'salesagent.salescenter_id')
        ->leftJoin('clients as clients', 'clients.id', '=', 'telesales.client_id')
        ->leftJoin('salesagent_detail as sd', 'sd.user_id', '=', 'salesagent.id')
        ->leftJoin('salescenterslocations as sl', 'sl.id', '=', 'sd.location_id')
        ->leftJoin('critical_logs_history as clh', 'clh.lead_id', '=', 'telesales.id')
        ->groupBy('clh.lead_id')
        ->leftJoin('telesalesdata as td', 'td.telesale_id', '=', 'telesales.id')
        ->groupBy('td.telesale_id')
        ->leftJoin('telesales_programs','telesales_programs.telesale_id','=','telesales.id')
        ->leftJoin('programs','programs.id','=','telesales_programs.program_id')
        ->leftJoin('utilities','programs.utility_id','=','utilities.id')
        ->leftJoin('brand_contacts','utilities.brand_id','=','brand_contacts.id')
        ;
        $query->whereIn('telesales.id',function($q){
               $q->select('lead_id')->from('critical_logs_history')->where('error_type', config()->get('constants.ERROR_TYPE_CRITICAL_LOGS.Critical'))->distinct('lead_id')->get();
            });

        if (!empty($params['client_id'])) {
            $query->where('telesales.client_id', $params['client_id']);
        }
        // if (!empty($params['refrence_id'])) {
        //     $query->where('telesales.refrence_id', $params['refrence_id']);
        // }
        if (!empty($params['salescenter_id'])) {
            $query->where('salescenters.id', $params['salescenter_id']);
        }
        if (auth()->user()->hasMultiLocations()) {
            $locationIds = auth()->user()->locations->pluck('id');
            $query->whereIn('sd.location_id', $locationIds);
        }
        if (!empty($params['location_id'])) {
            $query->where('sd.location_id', $params['location_id']);
        }
        if (!empty($params['sub_start_date']) && !empty($params['sub_end_date']) ) {
            if($params['sub_start_date'] == $params['sub_end_date']) {
                $query->whereDate('telesales.created_at',$params['sub_start_date']);
            } else {
                $query->whereBetween('telesales.created_at',[$params['sub_start_date'].' 00:00:00',$params['sub_end_date'].' 23:59:59']);
            }
        }
        if (!empty($params['verify_start_date']) && !empty($params['verify_end_date']) ) {
            if($params['verify_start_date'] == $params['verify_end_date']) {
                $query->whereDate('telesales.reviewed_at',$params['verify_start_date']);
            } else {
                $query->whereBetween('telesales.reviewed_at',[$params['verify_start_date'].' 00:00:00',$params['verify_end_date'].' 23:59:59']);
            }
        }

        if (!empty($params['search'])) {
            $search = $params['search'];
            $query->where(function($q) use ($search) {
                $q->where('telesales.refrence_id', 'LIKE', '%'.$search.'%')
                    ->orWhere('telesales.alert_status', 'LIKE', '%'.$search.'%')
                    ->orWhere('telesales.status', 'LIKE', '%'.$search.'%')
                    ->orWhere('clients.name', 'LIKE', '%'.$search.'%')
                    ->orWhere('salescenters.name', 'LIKE', '%'.$search.'%')
                    ->orWhere('salesagent.first_name', 'LIKE', '%'.$search.'%')
                    ->orWhere('salesagent.last_name', 'LIKE', '%'.$search.'%')
                    ->orWhere('sl.name', 'LIKE', '%'.$search.'%');
            });
        }
        return $query->get();
    }

}
