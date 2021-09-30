<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use DB;
class ComplianceReports extends Model
{


    public function getTemplateData($mapedData, $queryParams)
    {

       $query = $this->combine_options($mapedData);
       $query_where_params = $this->whereCondition($queryParams);

       return  $users = DB::table('telesales')
                       ->select(DB::raw($query))
                       ->where($query_where_params)
                       ->paginate(20);
    }
    public function exportData($mapedData, $queryParams)
    {

       $query = $this->combine_options($mapedData);
       $query_where_params = $this->whereCondition($queryParams);

       return  $users = DB::table('telesales')
                       ->select(DB::raw($query))
                       ->where($query_where_params)
                       ->get();
    }

   function whereCondition($queryParams){
         $where_params = array();
        $where_params[] = array('telesales.status', '!=', 'pending');
       if($queryParams['form_id']){
         $where_params[] = array('telesales.form_id', '=', $queryParams['form_id']);
       }
       if($queryParams['client_id']){
         $where_params[] = array('telesales.client_id', '=', $queryParams['client_id']);
       }
        
       if(isset($queryParams['date_start']) && $queryParams['date_start']!=""){
         $where_params[] = array('telesales.updated_at', '>=', date('Y-m-d',strtotime($queryParams['date_start'])));
       }
       if(isset($queryParams['date_end']) && $queryParams['date_end']!=""){
         $where_params[] = array('telesales.updated_at', '<=', date('Y-m-d',strtotime($queryParams['date_end'])));
       }
         return $where_params;

   }
    function combine_options($mapedData){
      $select_options = "";
      if(count($mapedData['values']) > 0) {
         for (  $i = 0;   $i < count($mapedData['values']); $i++) {
           if(isset($mapedData['allow_custom'][$i]) && $mapedData['allow_custom'][$i]== 1){
             if($mapedData['custom_value'][$i] == ""){
               $custom_val = "";
             }else{
               $custom_val = $mapedData['custom_value'][$i];
             }
             $select_options.=",  '$custom_val' as `".$mapedData['header'][$i]."`";
           }else
           if($mapedData['values'][$i]=='TPV Agent'){
             $select_options.=", ( select CONCAT( first_name ,' ', last_name) as name from users where telesales.reviewed_by = id  ) as `".$mapedData['header'][$i]."` ";
           }else if($mapedData['values'][$i]=='Sales Agent'){
             $select_options.=", ( select CONCAT( first_name ,' ', last_name) as name from users where telesales.user_id = id  ) as `".$mapedData['header'][$i]."` ";
           }else if($mapedData['values'][$i]=='Sales Center'){
             $select_options.=", ( select salescenters.name  from salescenters inner join users on users.salescenter_id = salescenters.id  where telesales.user_id = users.id  ) as `".$mapedData['header'][$i]."` ";
           }else if($mapedData['values'][$i]=='Location'){
             $select_options.=", ( select salescenterslocations.name  from salescenterslocations inner join users on users.location_id = salescenterslocations.id  where telesales.user_id = users.id  ) as `".$mapedData['header'][$i]."` ";
           }
           else if($mapedData['values'][$i]=='Status'){
             $select_options.=", status as `Status` ";
           }
           else if($mapedData['values'][$i]=='Create Time'){
             $select_options.=", DATE_FORMAT(created_at,  '%m-%d-%Y %H:%i:%s') as `".$mapedData['header'][$i]."` ";
           }
           else if($mapedData['values'][$i]=='Update Time'){
             $select_options.=", DATE_FORMAT(updated_at,  '%m-%d-%Y %H:%i:%s') as `".$mapedData['header'][$i]."` ";
           }
           else if($mapedData['values'][$i]=='Disposition'){
             $select_options.=", (select description from dispositions where id = telesales.disposition_id)  as  `".$mapedData['header'][$i]."`";
           }
           else{
             $select_options.=", ( select meta_value from telesalesdata where telesales.id = telesale_id and telesalesdata.meta_key ='".$mapedData['values'][$i]."' ) as `".$mapedData['header'][$i]."` ";
           }

         }
      }

      return $query = "telesales.refrence_id as `Lead ID`".$select_options;
    }

}
