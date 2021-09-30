<?php

use Illuminate\Support\Facades\Auth;
use App\models\Telesales;
use App\models\Settings;
use TNkemdilim\MoneyToWords\Converter;
use App\models\Role;
use App\models\PermissionRoleClientSpecific;

function breadcrum ($menu_array){
      ?>
<!--Breadcrumbs starts-->
<div class="page-breadcrumbs">
    <div class="container">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <ol class="breadcrumb">
                <li class="home_brdcumb"><span class="home-icon"><img src="/images/home.png" /></span><a
                        href="<?php echo  route('dashboard');?>">Home</a></li>
                <?php
              $i = 1;
              if(count($menu_array) >  0 ){
                  foreach($menu_array as $menu_items){
                      if(isset($menu_items['link']) && isset($menu_items['text']) ){
                        ?>
                <?php  if($menu_items['link']!=""){
                            ?>
                <li><a href="<?php echo $menu_items['link'] ?>"> <?php echo $menu_items['text']; ?> </a></li>
                <?php
                           }else{
                               ?>
                <li class="<?php echo (count($menu_array) == $i)? 'active':''; ?>"> <?php echo $menu_items['text']; ?>
                </li>
                <?php
                           } ?>

                <?php
                      }
                      $i++;
                  }
              }
            ?>

            </ol>
        </div>
    </div>
</div>
<!--Breadcrumbs ends-->
<div class="clearfix"></div>
<?php

 }


 function edit_btn($link, $tooltip_text = "", $custom_class=""){
   ?>
<a class="btn <?php echo $custom_class?>" href="<?php echo $link; ?>" data-toggle="tooltip" data-placement="top"
    data-container="body" title="" data-original-title="<?php echo $tooltip_text ?>" role="button"><img
        src="<?php echo asset('images/edit.png'); ?>" /></a>
<?php
 }

function view_btn($link, $tooltip_text = "",  $custom_class=""){
   ?>
<a class="btn <?php echo $custom_class?>" href="<?php echo $link; ?>" data-toggle="tooltip" data-placement="top"
    data-container="body" title="" data-original-title="<?php echo $tooltip_text ?>" role="button"><img
        src="<?php echo asset('images/view.png'); ?>" /></a>
<?php
 }
 function delete_btn($link,  $tooltip_text = "", $custom_class="", $custom_attributes = ""){
    ?>
<a class="btn <?php echo $custom_class?>" href="<?php echo $link; ?>" data-toggle="tooltip" data-placement="top"
    data-container="body" title="" data-original-title="<?php echo $tooltip_text ?>" role="button"
    <?php echo $custom_attributes; ?>><img src="<?php echo asset('images/cancel.png'); ?>" /></a>
<?php
  }

  function edit_btn_on_view_info_page($link, $custom_class=""){
    ?>

<a class="btn btn-green <?php echo $custom_class?>" href="<?php echo $link; ?>" role="button"> Edit</a>
<?php
  }
  function delete_btn_on_view_info_page($link, $custom_class="", $custom_attributes = ""){
    ?>
<a class="btn btn-red <?php echo $custom_class?>" href="<?php echo $link; ?>" role="button"
    <?php echo $custom_attributes; ?>>Delete</a>
<?php
  }

 function getimage($imagepath){
     return '<img src="'.asset($imagepath).'"/>';
 }
 function getFormIconImage($imagepath){
     return '<span class="form-icon"><img src="'.asset($imagepath).'"/></span>';
 }
 function getDataEnteredValue($valuesarray, $key){
    // print_r($valuesarray);
  $return_value = "";
   if(isset($valuesarray[$key])){
        $return_value = $valuesarray[$key];
    }

    return $return_value;
 }
 function HelperCheckClientUser($client_id){
  if(Auth::user()->access_level == 'client' || Auth::user()->access_level == 'salescenter' ){
      if(Auth::user()->client_id != $client_id ){
          abort(403);
      }
  }
}

/* Function to get fields label texts for multiple fields*/
function getFieldsLableForDisplay($field_label, $type = ""){
         $formFieldsArr = array();

         if($type == 'name'){
          $formFieldsArr[] = "Authorized First name";
          $formFieldsArr[] = "Authorized Middle initial";
          $formFieldsArr[] = "Authorized Last name";
        }
        else if($type == 'billingaddress'){
          $formFieldsArr[] = "BillingAddress";
          $formFieldsArr[] = "BillingAddress2";
          $formFieldsArr[] = "BillingState";
          $formFieldsArr[] = "BillingCity";
          $formFieldsArr[] = "BillingZip";
        }
        else if($type == 'gasbillingname'){
          $formFieldsArr[] = "Gas Billing first name";
          $formFieldsArr[] = "Gas Billing middle name";
          $formFieldsArr[] = "Gas Billing last name";
        }
        else if($type == 'billingname'){
          $formFieldsArr[] = "Billing first name";
          $formFieldsArr[] = "Billing middle name";
          $formFieldsArr[] = "Billing last name";
        }
        else if($type == 'electricbillingname'){
          $formFieldsArr[] = "Electric Billing first name";
          $formFieldsArr[] = "Electric Billing middle name";
          $formFieldsArr[] = "Electric Billing last name";
        }else if($type == 'serviceaddress'){
          $formFieldsArr[] = "ServiceAddress";
          $formFieldsArr[] = "ServiceAddress2";
          $formFieldsArr[] = "ServiceCity";
          $formFieldsArr[] = "ServiceState";
          $formFieldsArr[] = "ServiceZip";
        }
        else if($type == 'gasbillingaddress'){
          $formFieldsArr[] = "GasBillingAddress";
          $formFieldsArr[] = "GasBillingAddress2";
          $formFieldsArr[] = "GasBillingState";
          $formFieldsArr[] = "GasBillingCity";
          $formFieldsArr[] = "GasBillingZip";
        }
        else if($type == 'electricbillingaddress'){
          $formFieldsArr[] = "ElectricBillingAddress";
          $formFieldsArr[] = "ElectricBillingAddress2";
          $formFieldsArr[] = "ElectricBillingCity";
          $formFieldsArr[] = "ElectricBillingState";
          $formFieldsArr[] = "ElectricBillingZip";
        } else{
          $formFieldsArr[] = $field_label;
        }
        return $formFieldsArr;

}

function GetValidationRules(){

$validation_rules = '
';
$ValidationRules = unserialize(urldecode(trim($validation_rules)));



 return $ValidationRules;
}

function validationMappingArray(){
   $mapping_array = array(
     'email' => 'ServiceEmail',
     'ServiceZip' => 'ServiceZip',
     'ServiceCity' => 'ServiceCity',
     'ServiceState' => 'ServiceState',
     'ServiceAddress' => 'ServiceAddress1',
     'ServiceAddress2' => 'ServiceAddress2',
     'GasServiceZip' => 'ServiceZip',
     'GasServiceCity' => 'ServiceCity',
     'GasServiceState' => 'ServiceState',
     'GasServiceAddress' => 'ServiceAddress1',
     'GasServiceAddress2' => 'ServiceAddress2',
     'electricServiceZip' => 'ServiceZip',
     'electricServiceCity' => 'ServiceCity',
     'electricServiceState' => 'ServiceState',
     'electricServiceAddress' => 'ServiceAddress1',
     'electricServiceAddress2' => 'ServiceAddress2',
     'BillingAddress' => 'BillingAddress1',
     'BillingAddress2' => 'BillingAddress2',
     'BillingZip' => 'BillingZip',
     'BillingCity' => 'BillingCity',
     'BillingState' => 'BillingState',
     'GasBillingAddress' => 'BillingAddress1',
     'GasBillingAddress2' => 'BillingAddress2',
     'GasBillingZip' => 'BillingZip',
     'GasBillingCity' => 'BillingCity',
     'GasBillingState' => 'BillingState',
     'ElectricBillingAddress' => 'BillingAddress1',
     'ElectricBillingAddress2' => 'BillingAddress2',
     'ElectricBillingZip' => 'BillingZip',
     'ElectricBillingCity' => 'BillingCity',
     'Authorized First name' => 'ServiceFirstName',
     'Authorized Last name' => 'ServiceLastName',
     'Billing first name' => 'BillingFirstName',
     'Billing last name' => 'BillingLastName',
     'Gas Billing first name' => 'BillingFirstName',
     'Gas Billing last name' => 'BillingLastName',
     'Electric Billing first name' => 'BillingFirstName',
     'Electric Billing last name' => 'BillingLastName',
     'Phone Number' => 'ServicePhone',
     'Electric Account Number' => 'UtilityAccountNumber',
     'Gas Account Number' => 'UtilityAccountNumber',
     'Account Number' => 'UtilityAccountNumber',

   );
   return $mapping_array;

}

function DualFuelvalidationMappingArray(){
   $mapping_array = array(
     'email' => array(
                   'commodity' => "Electric",
                   'field' =>  'ServiceEmail'
                 ),
     'email for reward programs' => array(
                   'commodity' => "Electric",
                   'field' =>  'ServiceEmail'
                 ),
     'ServiceZip' => array(
                   'commodity' => "Electric",
                   'field' =>  'ServiceZip'
                 ),
     'ServiceCity' => array(
                   'commodity' => "Electric",
                   'field' =>  'ServiceCity'
                 ),
     'ServiceState' => array(
                   'commodity' => "Electric",
                   'field' =>  'ServiceState'
                 ),

     'ServiceAddress' => array(
                       'commodity' => "Electric",
                       'field' =>  'ServiceAddress1'
                     ),
     'ServiceAddress2'  => array(
                       'commodity' => "Electric",
                       'field' =>  'ServiceAddress2'
                     ),

     'GasServiceZip' => array(
                   'commodity' => "Gas",
                   'field' =>  'ServiceZip',
                 ),
     'GasServiceCity' => array(
                   'commodity' => "Gas",
                   'field' =>  'ServiceCity'
                 ),
     'GasServiceState' => array(
                   'commodity' => "Gas",
                   'field' =>  'ServiceState'
                 ),
     'GasServiceAddress' => array(
                       'commodity' => "Gas",
                       'field' =>  'ServiceAddress1'
                     ),
     'GasServiceAddress2' => array(
                       'commodity' => "Gas",
                       'field' =>  'ServiceAddress2'
                     ),
     'electricServiceZip' => array(
                   'commodity' => "Electric",
                   'field' =>  'ServiceZip'
                 ),
     'electricServiceCity' => array(
                   'commodity' => "Electric",
                   'field' =>  'ServiceCity'
                 ),
     'electricServiceState' => array(
                   'commodity' => "Electric",
                   'field' =>  'ServiceState'
                 ),

     'electricServiceAddress' => array(
                       'commodity' => "Electric",
                       'field' =>  'ServiceAddress1'
                     ),
     'electricServiceAddress2'  => array(
                       'commodity' => "Electric",
                       'field' =>  'ServiceAddress2'
                     ),

     'BillingAddress'   => array(
                       'commodity' => "Electric",
                       'field' =>  'BillingAddress1'
                     ),
     'BillingAddress2' =>  array(
                       'commodity' => "Electric",
                       'field' =>  'BillingAddress2'
                     ),
     'BillingZip' => array(
                   'commodity' => "Electric",
                   'field' =>  'BillingZip',
                 ),
     'BillingCity'  => array(
                   'commodity' => "Electric",
                   'field' =>  'BillingCity'
                 ),
     'BillingState' => array(
                   'commodity' => "Electric",
                   'field' =>  'BillingState'
                 ),
     'GasBillingAddress' => array(
                       'commodity' => "Gas",
                       'field' =>  'BillingAddress1'
                     ),
     'GasBillingAddress2' => array(
                       'commodity' => "Gas",
                       'field' =>  'BillingAddress2'
                     ),
     'GasBillingZip'  => array(
                       'commodity' => "Gas",
                       'field' =>  'BillingZip'
                     ),
     'GasBillingCity' => array(
                       'commodity' => "Gas",
                       'field' =>  'BillingCity'
                     ),
     'GasBillingState' => array(
                       'commodity' => "Gas",
                       'field' =>  'BillingState'
                     ),
     'ElectricBillingAddress' => array(
                       'commodity' => "Electric",
                       'field' =>  'BillingAddress1'
                     ),
     'ElectricBillingAddress2' => array(
                       'commodity' => "Electric",
                       'field' =>  'BillingAddress2'
                     ),
     'ElectricBillingZip' => array(
                           'commodity' => "Electric",
                           'field' =>  'BillingZip'
                         ),
     'ElectricBillingCity' => array(
                   'commodity' => "Electric",
                   'field' =>  'BillingCity'
                 ),
     'ElectricBillingState' => array(
                   'commodity' => "Electric",
                   'field' =>  'BillingState'
                 ),

     'Authorized First name' => array(
                   'commodity' => "Electric",
                   'field' =>  'ServiceFirstName'
                 ),

     'Authorized Last name' => array(
                   'commodity' => "Electric",
                   'field' =>  'ServiceLastName'
                 ),
     'Billing first name' => array(
                   'commodity' => "Electric",
                   'field' =>  'BillingFirstName'
                 ),
     'Billing last name' => array(
                   'commodity' => "Electric",
                   'field' =>  'BillingLastName'
                 ),
     'Gas Billing first name' => array(
                   'commodity' => "Gas",
                   'field' =>  'BillingFirstName'
                 ),
     'Gas Billing last name' =>array(
                   'commodity' => "Gas",
                   'field' =>  'BillingLastName'
                 ),
     'Electric Billing first name' => array(
                   'commodity' => "Electric",
                   'field' =>  'BillingFirstName'
                 ),
     'Electric Billing last name' => array(
                   'commodity' => "Electric",
                   'field' =>  'BillingLastName'
                 ),
     'Phone Number' => array(
                   'commodity' => "Electric",
                   'field' =>  'ServicePhone'
                 ),
     'Electric Account Number' => array(
                   'commodity' => "Electric",
                   'field' =>  'UtilityAccountNumber'
                 ),
     'Gas Account Number' => array(
                   'commodity' => "Gas",
                   'field' =>  'UtilityAccountNumber'
                 ),
     'Account Number' => array(
                   'commodity' => "Electric",
                   'field' =>  'UtilityAccountNumber'
                 )

   );
   return $mapping_array;

}

function getAllClients() {

    $clients = App\models\Client::orderBy('name');
    if(Auth::check() && Auth::user()->isAccessLevelToClient()) {
        $clients->where('id',Auth::user()->client_id);
    }    
    return $clients->get();
}

function getAllSalesCenter() {
    $salesCenters = App\models\Salescenter::orderBy('name');
    if(Auth::check() && Auth::user()->isAccessLevelToClient()) {
        $salesCenters->where('client_id',Auth::user()->client_id);
    }
    if(Auth::check() && Auth::user()->hasAccessLevels('salescenter')) {
        $salesCenters->where('id',Auth::user()->salescenter_id);
    }  
    return  $salesCenters->get();
}

function getStates() {
    return $states = App\models\Zipcodes::select('state')->distinct()->get();
}

function getZipCodes() {
    return $zipcodes = App\models\Zipcodes::select('zipcode')->distinct()->get();
}

/**
 * for get enable custom fields for program
 * @param $clientId
 * @return mixed
 */
function getEnableCustomFields($clientId) {
    return Settings::getEnableFields($clientId);
}

/**
 * for get setting is on or off
 *
 * @param $clientId
 * @param $key
 * @param $default
 * @return bool
 */
function isOnSettings($clientId,$key, $default=true) {
    $settings = Settings::where('client_id',$clientId)->first();

    if (!empty($settings) && isset($settings[$key])) {
        return $settings[$key] == 1;
    }
    // default settings is on for all client
    return $default;
}

/**
 * for get self tpv delay time
 *
 * @param $clientId
 * @param $key
 * @return bool
 */
function getSelfTpvDelayTime($clientId) {
    $settings = Settings::select('self_tpv_call_delay')->where('client_id',$clientId)->first();

    $delays=[];
    if ($settings) {
        $delays = explode(',', $settings->self_tpv_call_delay);
    }
    
    return $delays;
}

/**
 * for get all off alerts of telesales
 * @param $clientId
 * @return array
 */
function getOffAlertsTele($clientId) {
    $settings = Settings::select('is_enable_alert1_tele', 'is_enable_alert2_tele', 'is_enable_alert3_tele', 'is_enable_alert4_tele', 'is_enable_alert5_tele', 'is_enable_alert6_tele', 'is_enable_alert7_tele','is_enable_alert8_tele','is_enable_alert9_tele','is_enable_alert10_tele','is_enable_alert11_tele','is_enable_alert12_tele')->where('client_id',$clientId)->first()->toArray();

    $offAlerts = [];
    if (!empty($settings)) {
        $alerts = getTeleAlertArray();

        array_walk(
            $settings,
            function ($value, $key) use (&$offAlerts, $alerts) {
                if (!$value) {
                    $aKey = substr($key,0,16);
                    if ($aKey == 'is_enable_alert7') {

                        // merge 2,3 and 4 event for alert 7
                        $offAlerts = array_merge($offAlerts,array(2,3,4));
                    } else {
                      if(isset($alerts[$aKey])) {
                        $offAlerts[] = $alerts[$aKey];
                      }
                    }
                }
            }
        );
        info('off alerts tele: '. print_r($offAlerts,true));
    }
    return $offAlerts;
}

/**
 * for get all off alerts of d2d
 * @param $clientId
 * @return array
 */
function getOffAlertsD2D($clientId) {
    $settings = Settings::select('is_enable_alert1_d2d', 'is_enable_alert2_d2d', 'is_enable_alert3_d2d', 'is_enable_alert4_d2d', 'is_enable_alert5_d2d', 'is_enable_alert6_d2d', 'is_enable_alert7_d2d', 'is_enable_alert8_d2d','is_enable_alert9_d2d','is_enable_alert10_d2d','is_enable_alert11_d2d','is_enable_alert12_d2d','is_enable_alert13_d2d')->where('client_id',$clientId)->first()->toArray();

    $offAlerts = [];
    if (!empty($settings)) {
        $alerts = getAlertArray();

        array_walk(
            $settings,
            function ($value, $key) use (&$offAlerts, $alerts) {
                if (!$value) {
                    $aKey = substr($key,0,16);
                    if ($aKey == 'is_enable_alert7') {

                        // merge 2,3 and 4 event for alert 7
                        $offAlerts = array_merge($offAlerts,array(2,3,4));
                    } else {
                        $offAlerts[] = $alerts[$aKey];
                    }
                }
            }
        );
        info('off alerts d2d: '. print_r($offAlerts,true));
    }
    return $offAlerts;
}

/**
 * for get all alert in event type
 * @return array
 */
function getAlertArray() {
    $alerts = [
        'is_enable_alert1' => 5,
        'is_enable_alert2' => 6,
        'is_enable_alert3' => 7,
        'is_enable_alert4' => 9,
        'is_enable_alert5' => 8,
        'is_enable_alert6' => 10,
        'is_enable_alert7' => "2,3,4",
        'is_enable_alert8' => 1,
        'is_enable_alert9' => 44,
        'is_enable_alert10' => 45,
        'is_enable_alert11' => 48,
        'is_enable_alert12' => 46,
        'is_enable_alert13' => 47,
    ];
    return $alerts;
}

function getTeleAlertArray() {
    $alerts = [
        'is_enable_alert1' => 5,
        'is_enable_alert2' => 6,
        'is_enable_alert3' => 7,
        'is_enable_alert4' => 9,
        'is_enable_alert5' => 8,
        'is_enable_alert6' => 10,
        'is_enable_alert7' => "2,3,4",
        'is_enable_alert8' => 44,
        'is_enable_alert9' => 45,
        'is_enable_alert10' => 48,
        'is_enable_alert11' => 46,
        'is_enable_alert12' => 47,
    ];
    return $alerts;
}

/**
 * for get all event if tele/d2d alerts is off
 * @return int[]
 */
function getAlertsEvent() {
    $event = [1,2,3,4,5,6,7,8,9,10,44,45,46,47,48];
    return $event;
}

/**
 * for get setting value by key
 *
 * @param $clientId
 * @param $key
 * @param $default
 * @return mixed
 */
function getSettingValue($clientId,$key, $default=1) {
    $settings = Settings::where('client_id',$clientId)->first();

    if (!empty($settings) && isset($settings[$key])) {
        return $settings[$key];
    }

    return $default;
}

/**
 * for get disable custom fields for program
 * @param $clientId
 * @return mixed
 */
function getDisableCustomFields($clientId) {
    return Settings::getDisableFields($clientId);
}

/**
 * for check is allow enrollment by state or not
 * @param $clientId
 * @return mixed
 */
function isEnableEnrollByState($clientId) {
    return Settings::where('client_id',$clientId)->where('is_enable_enroll_by_state',1)->exists();
}

function printDataErrors($errors, $key)
{
    $message='Row Number: '.$key;
    foreach ($errors as  $error) {
        echo '<p>'. $message.' -> '.$error. '</p>';
    }
}
function getRolesInTD($permissionRoles,$roles)
{	
    $row='';	
    if(!empty($permissionRoles) && !empty($roles)) {	
        $permissionRoleIds= $permissionRoles->pluck('id')->toArray();	
        foreach ($roles as $role){	
            if(in_array($role['id'],$permissionRoleIds)) {	
                $row.='<td><span class="permission-tick">✔</span></td>';	
            } else {	
                $row.='<td></td>';	
            }	
        }	
    } else {	
        $rolesCount= count($roles);	
        $row="<td colspan='$rolesCount'></td>";	
    }	
    return $row;	
}

// get roles client specific
function getRolesClientInTD($permissionRoles,$roles,$permissionId,$permissions)
{
  
    $row='';
    
    
    if(!empty($permissionRoles->toArray()) && !empty($roles)) {
        $permissionRoleIds= $permissionRoles->where('permission_id',$permissionId)->pluck('role_id')->toArray();
        foreach ($roles as $role){
            if(in_array($role['id'],$permissionRoleIds)) {
                $row.='<td><span class="permission-tick">✔</span></td>';
            } else {
                $row.='<td></td>';
            }
        }
    } else if(empty($permissionRoles->toArray())){
        $permissionRoleIds= $permissions->pluck('id')->toArray();	
        foreach ($roles as $role){	
            if(in_array($role['id'],$permissionRoleIds)) {	
                $row.='<td><span class="permission-tick">✔</span></td>';	
            } else {	
                $row.='<td></td>';	
            }	
        }	
    } else {
        $rolesCount= count($roles);
        $row="<td colspan='$rolesCount'></td>";
    }
    return $row;
}

/**
 * create permission row in td for edit
 * @param $permissionRoles
 * @param $roles
 * @param int $permissionId
 * @return string
 */
function getPermissionsInTDForEdit($permissionRoles, $permissionAccesslevels, $roles, $permissionId=0, $disabled='')
{	
    $row='';	
    if(!empty($permissionAccesslevels) && !empty($roles)) {	
        $permissionRoleIds= $permissionRoles->pluck('id')->toArray();	
        $accessLevels= $permissionAccesslevels->pluck('access_level')->toArray();	
        foreach ($roles as $role){	
            if ($role['name'] != 'admin') {	
                if (in_array($role['accesslevel'], $accessLevels)) {	
                    if(in_array($role['id'],$permissionRoleIds)) {
                        $name = $role['name'];	
                        $checked = 'checked';	
                        $hidden = "<input type='hidden' name='permissions[$name][]' value='$permissionId'>";	
                    } else {	
                        $checked = $hidden = '';	
                    }	
                    $inputId = 'permission'.$permissionId.$role['id'];	
                    if ($disabled == 'disabled') {	
                        $td = "<td> <input type='checkbox' value='$permissionId' class='styled-checkbox edit-role' id='$inputId' $checked $disabled> <label for='$inputId'></label>$hidden</td>";	
                    } else {	
                        $name = $role['name'];
                        $td = "<td> <input type='checkbox' name='permissions[$name][]' value='$permissionId' class='styled-checkbox edit-role' id='$inputId' $checked $disabled> <label for='$inputId'></label></td>";	
                    }	
                    $row .= $td;	
                } else {	
                    $row .= '<td></td>';	
                }	
            }	
        }	
    } else {	
        $rolesCount= $roles->count();	
        $row="<td colspan='$rolesCount'></td>";	
    }	
    return $row;	
}

// client specific permissions
function getPermissionsClientInTDForEdit($permissionClientRoles, $permissionAccesslevels, $roles, $permissionId=0, $disabled='',$permissionRoles)
{
    
    $row='';
    if(!empty($permissionAccesslevels) && !empty($roles)) {
        if(!empty($permissionClientRoles->toArray())){
            $permissionRoleIds= $permissionClientRoles->where('permission_id',$permissionId)->pluck('role_id')->toArray();
        }else{
            $permissionRoleIds= $permissionRoles->pluck('id')->toArray();
        }
       
        $accessLevels= $permissionAccesslevels->pluck('access_level')->toArray();
        
        foreach ($roles as $role){
            if ($role['name'] != 'admin') {
                if (in_array($role['accesslevel'], $accessLevels)) {
                    if(in_array($role['id'],$permissionRoleIds)) {
                        $name = $role['name'];
                        $checked = 'checked';
                        $hidden = "<input type='hidden' name='permissions[$name][]' value='$permissionId'>";
                    } else {
                        $checked = $hidden = '';
                    }
                    $inputId = 'permission'.$permissionId.$role['id'];

                    if ($disabled == 'disabled') {
                        $td = "<td> <input type='checkbox' value='$permissionId' class='styled-checkbox edit-role' id='$inputId' $checked $disabled> <label for='$inputId'></label>$hidden</td>";
                    } else {
                        $name = $role['name'];
                        $td = "<td> <input type='checkbox' name='permissions[$name][]' value='$permissionId' class='styled-checkbox edit-role' id='$inputId' $checked $disabled> <label for='$inputId'></label></td>";
                    }
                    $row .= $td;
                } else {
                    $row .= '<td></td>';
                }
            }
        }
    } else {
        $rolesCount= count($roles);
        $row="<td colspan='$rolesCount'></td>";
    }
    return $row;
}

/**
 * for generate delete button
 * @param string $class
 * @param array $attributes
 * @return string
 */
function getDeleteBtn($class ='', $attributes= [])
{
    $attribute = '';
    if (is_array($attributes)) {
        foreach ($attributes as $key => $value) {
            $attribute .= $key.'="'.$value. '" ';
        }
    }
    $deleteBtn = "<button
                        class='btn {$class}' 
                        role='button' 
                        data-toggle='tooltip' 
                        data-placement='top' 
                        data-container='body' 
                        {$attribute}>".
                        getimage('images/cancel.png').
                "</button>";
    return $deleteBtn;                
}

/**
 * get disabled button for listing
 * @param string $type
 * @return string
 */
function getDisabledBtn($type = 'view')
{
    if($type == 'view') {
        $btn = getimage("images/view-no.png");
    }else if($type == 'edit') {
        $btn = getimage("images/edit-no.png");
    }else if($type == 'clone') {
        $btn = getimage("images/copy-no.png");
    }else if($type == 'delete') {
        $btn = getimage("images/cancel-no.png");
    }else{
        $btn = getimage("images/deactivate_new-no.png");
    }
    return "<button  class='btn cursor-none'>$btn</button>";
}
function getWorkflows() {
    return  App\models\ClientWorkflow::groupBy('workflow_id')->with('client')->get();
}

function distance($lat1, $lon1, $lat2, $lon2, $unit)
{
  
    if (($lat1 == $lat2) && ($lon1 == $lon2)) {
        return 0;
    } else {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);
        
        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "M") {
            return ($miles * 1.609344) * 1000;
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }
}
function colorArray()
{
  $array = ['#1C5997', '#2F96FF', '#B1D8FF', '#CCCCCC','#EEEEEE', '#E29E91','#EA6C6A','#E92125'];
  // $array = ['#3A58A8', '#727CB5', '#A0A3C1', '#999999','#CCCCCC', '#E29E91','#EA6C6A','#E92125'];
	// return ['#3A58A8', '#CCCCCC', '#A0A3C1', '#999999', '#727CB5','#E29E91','#EA6C6A','#E92125'];
	return $array;
}

function calenderColorArray()
{
    $array = ['#2F96FF', '#B1D8FF', '#999999', '#DDDDDD'];
    // $array = ['#17374F', '#2F6D9D', '#74ABD6', '#C3DBED'];
	// return ['#3A58A8', '#CCCCCC', '#A0A3C1', '#999999', '#727CB5','#E29E91','#EA6C6A','#E92125'];
	return $array;
}

function getTimeZoneList()
{
	$OptionsArray = timezone_identifiers_list();
	$p=0;
	$n=0;
	$sortPositive = [];
	$sortNegative = [];
	foreach($OptionsArray as $key => $row)
	{
		$date = Carbon\Carbon::now()->setTimezone($row);
		if(strpos($date->format('P'),'-') === false)
		{
			
			$sortPositive[$date->format('P').$p++] = $row .' (GMT '.$date->format('P').') '.(((strpos($date->format('T'),'+') === false) && ((strpos($date->format('T'),'-')) === false))? '('.($date->format('T')).')': '').(($date->format('I') == 1)?' DST Applicable': '');
		}
		else
		{
			$sortNegative[$date->format('P').$n++] = $row .' (GMT '.$date->format('P').') '.(((strpos($date->format('T'),'+') === false) && ((strpos($date->format('T'),'-')) === false))? '('.($date->format('T')).')': '').(($date->format('I') == 1)?' DST Applicable': '');
		}
	}
	krsort($sortNegative);
	ksort($sortPositive);
	$sortArray = array_merge($sortNegative,$sortPositive);
	return $sortArray;
}
function getDateFormat()
{
	return 'm/d/Y';
}

function getTimeFormat()
{
	return 'h:i:s A';
}
function getClientSpecificTimeZone()
{
  return 'America/Toronto';
}
/**
 * Generate profile icon design for users and agent listing
 */
function getProfileIcon($user) {
    if (!empty($user->profile_picture) && Storage::disk('s3')->exists(config()->get('constants.aws_folder') . $user->profile_picture)) {
        $url = Storage::disk('s3')->url($user->profile_picture);
        return "<div class='loginimg'><img src='$url' class='listing-profile-pic' /></div>";
    } else if (!empty($user)) {
       $userName = strtoupper($user->first_name[0].$user->last_name[0]);
        return "<div class='profile-icon'>$userName</div>";
    } else {
        return "<div class='profile-icon'>NA</div>";
    }
}

/**
 * for remove commodity name form account name label
 * @param $label
 * @return string
 */
function getAccountNumberLabel($label)
{
    $search = config('constants.ACCOUNT_NUMBER_LABEL');
    if(preg_match("/{$search}/i", $label)) {
        $labels = explode("-",$label);
        if(isset($labels[0])) {
            $label = trim($labels[0]);
        }
    }
    return $label;
}

/**
 * for remove commodity name form label
 * @param $label
 * @return string
 */
function getLabel($label)
{
    $labels = explode("-",$label);
    if(isset($labels[0])) {
        $label = trim($labels[0]);
    }
    return $label;
}

function generateVerificationNumer($teleSale, $verification_number = '')
{
    // Old Logic :-
        // $check_verification_number = 2;
        // $validate_num = $verification_number = "";
        // while ($check_verification_number > 1) {
        //     $verification_number = rand(1000000, 9999999);
        //     $validate_num = (new Telesales)->validateConfirmationNumber($verification_number);
        //     if (!$validate_num) {
        //         $check_verification_number = 0;
        //     } else {
        //         $check_verification_number++;
        //     }
        // }
        // return $verification_number;

    // New Logic :-
    // verification code will be 8 char long, starting from letter T and 7 digits.It will be incremented by 1 on each request.
        \Log::info("generateVerificationNumer: Need to generate verification number");
        $validate_num = "";
        $check_number = (new Telesales)->checkVerificationNumber($verification_number);
        // \Log::info("generateVerificationNumer: checkNumber: " . $check_number);
        
        if ($check_number == null) { 
            $verification_number = "T0000001";
        } else {
            $last_number = preg_replace("/[^0-9]/", "", $check_number->verification_number);
            $new_number = $last_number + 1;
            $verification_number = "T".str_pad($new_number, 7, 0, STR_PAD_LEFT);
        }

        \Log::info("generateVerificationNumer: newVerificationNumber Generated: " . $verification_number);

        $check_verification_number = (new Telesales)->validateConfirmationNumber($verification_number);
        
        // \Log::info("generateVerificationNumer: checkVerification Number: " . $check_verification_number);

		if ($check_verification_number) {
            \Log::info("generateVerificationNumer: Need to get the new number ");
			return generateVerificationNumer($teleSale, $verification_number);
		}else {
            \Log::info("generateVerificationNumer: Use the same number");
			$teleSale->verification_number = $verification_number;
			$teleSale->save();
        }
        
        \Log::info("generateVerificationNumer: Return from here:" . $verification_number);
        return $verification_number;
}
/**
 * for convert customer name in string from array
 * @param $name
 * @return string
 */
function getNameFromArray($name)
{
    try{
        $fullName = [];
        $response = '';
        foreach ($name as $value) {
            switch ($value['meta_key']) {
                case 'first_name':
                    $fullName[0] = $value['meta_value'];
                    break;
                case 'middle_initial':
                    $fullName[1] = $value['meta_value'];
                    break;
                case 'last_name':
                    $fullName[2] = $value['meta_value'];
                    break;
                default:
                    break;
            }
        }
        if(!empty($fullName)) {
            // sort by key
            ksort($fullName);
            $response = ucwords(implode(' ', $fullName));
        }
        return $response;
    } catch (\Exception $e) {
        \Log::error($e);
    }   
}

/**
 * for convert customer address in string from array
 * @param $address
 * @return string
 */
function getAddressFromArray($address)
{
    try {
        $fullAddress = [];
        $response = '';
        foreach ($address as $key => $value) {
            if (!empty($value['meta_value'])) {
                switch ($value['meta_key']) {
                    case 'unit':
                        $fullAddress[0] = $value['meta_value'];
                        break;
                    case 'address_1':
                        $fullAddress[1] = $value['meta_value'];
                        break;
                    case 'address_2':
                        $fullAddress[2] = $value['meta_value'];
                        break;
                    case 'city':
                        $fullAddress[3] = $value['meta_value'];
                        break;
                    case 'county':
                        $fullAddress[4] = $value['meta_value'];
                        break;
                    case 'state':
                        $fullAddress[5] = $value['meta_value'];
                        break;
                    case 'zipcode':
                        $fullAddress[6] = $value['meta_value'];
                        break;
                    case 'country':
                        $fullAddress[7] = $value['meta_value'];
                        break;             
                    default:
                        break;
                }
            }
        }

        if (!empty($fullAddress)) {
            ksort($fullAddress);
            $response = ucwords(implode(', ',$fullAddress));
        }

        return $response;
    } catch (\Exception $e) {
        \Log::error($e);
    }
}

/**
 * for convert customer service address in string from array
 * @param $address
 * @return string
 */
function getServiceAddressFromArray($address)
{
    try {
        $fullAddress = [];
        $response = '';
        foreach ($address as $key => $value) {
            if (!empty($value['meta_value'])) {
                switch ($value['meta_key']) {
                    case 'service_unit':
                        $fullAddress[0] = $value['meta_value'];
                        break;
                    case 'service_address_1':
                        $fullAddress[1] = $value['meta_value'];
                        break;
                    case 'service_address_2':
                        $fullAddress[2] = $value['meta_value'];
                        break;
                    case 'service_city':
                        $fullAddress[3] = $value['meta_value'];
                        break;
                    case 'service_county':
                        $fullAddress[4] = $value['meta_value'];
                        break;
                    case 'service_state':
                        $fullAddress[5] = $value['meta_value'];
                        break;
                    case 'service_zipcode':
                        $fullAddress[6] = $value['meta_value'];
                        break;
                    case 'service_country':
                        $fullAddress[7] = $value['meta_value'];
                        break;                                  
                    default:
                        break;
                }
            }
        }
        if (!empty($fullAddress)) {
            ksort($fullAddress);
            $response = ucwords(implode(', ',$fullAddress));
        }

        return $response;
    } catch (\Exception $e) {
        \Log::error($e);
    }
}

function getBillingAddressFromArray($address)
{
    try {
        $fullAddress = [];
        $response = '';
        foreach ($address as $key => $value) {
            if (!empty($value['meta_value'])) {
                switch ($value['meta_key']) {
                    case 'billing_unit':
                        $fullAddress[0] = $value['meta_value'];
                        break;
                    case 'billing_address_1':
                        $fullAddress[1] = $value['meta_value'];
                        break;
                    case 'billing_address_2':
                        $fullAddress[2] = $value['meta_value'];
                        break;
                    case 'billing_city':
                        $fullAddress[3] = $value['meta_value'];
                        break;
                    case 'billing_county':
                        $fullAddress[4] = $value['meta_value'];
                        break;
                    case 'billing_state':
                        $fullAddress[5] = $value['meta_value'];
                        break;
                    case 'billing_zipcode':
                        $fullAddress[6] = $value['meta_value'];
                        break;
                    case 'billing_country':
                        $fullAddress[7] = $value['meta_value'];
                        break;                                  
                    default:
                        break;
                }
            }
        }
        if (!empty($fullAddress)) {
            ksort($fullAddress);
            $response = ucwords(implode(', ',$fullAddress));
        }

        return $response;
    } catch (\Exception $e) {
        \Log::error($e);
    }
}

/**
 * for encode data
 * @param $data
 * @return string
 */
function encode($data)
{
    return base64_encode($data);
}

/**
 * for decode data
 * @param $data
 * @return false|string
 */
function decode($data)
{
    return base64_decode($data);
}
function getChannelOption($clientId)
{
    $disabled = '';
    if(!isOnSettings($clientId,'is_enable_d2d_app')) {
        $disabled = 'disabled';
    }
    $channel = "<option value='tele'>Tele</option><option value='d2d' $disabled>D2D</option>";

    return $channel;                                
}

// function to return time in min/sec/hour.
function getConvertedTime($init){   
    
    $hours = floor($init / 3600);
    $minutes = floor(($init / 60) % 60);
    $seconds = $init % 60;
    return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);

        // $dt = Carbon\Carbon::now();
        // $hours = $dt->diffInHours($dt->copy()->addSeconds($data));
        // $minutes = $dt->diffInMinutes($dt->copy()->addSeconds($data)->subHours($hours));
        // $seconds = $dt->diffInSeconds($dt->copy()->addSeconds($data)->subHours($hours)->subMinutes($minutes));

        // if($hours != 0){
        //     return number_format($hours .':'.$minutes,2);
        // }
        // else if($minutes != 0){
        //     return number_format('00'.$minutes .':'. $seconds ,2);
        // }
        // else if($seconds != 0){
        //     return $seconds .'00:00:00';
        // }

}
 //function to get rate in text new 
function getRateText($rate){
    if($rate == 0) {
        return "zero cents";
    }

    $number = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
    $number_array = explode('.',$rate."");
    $decimalDigit = '';
    $numaricDigit = '';
    if(isset($number_array[0]) && !empty($number_array[0]) && $number_array[0] != 0){
        $numaricDigit = $number->format($number_array[0]);        
        $numaricDigit .= ($number_array[0] == 1) ? ' dollar' : ' dollars';
    }
    if(isset($number_array[1]) && !empty($number_array[1])){
        $numbers = str_pad($number_array[1], 4, 0, STR_PAD_RIGHT);
        $decimalDigit = $number->format(((int)$numbers/100));
        $decimalDigit .= ($number_array[1] == 1) ? ' cent' : ' cents';
    }

    $rateInText = $numaricDigit .' '. $decimalDigit ;
    return $rateInText;
}


 //set client default permissions
function setDefaultClientPermissions($client_id){
    $roles = Role::all();
        $roles_array = [];
        foreach($roles->toArray() as $key=>$role){
           if($role['name'] == "admin" || $role['name'] == "tpv_admin" || $role['name'] == "tpv_qa"){
             unset($role[$key]);
           }else{
             $roles_array[$key] = $role;
           }
        }
        
        foreach($roles_array as $role){
            $permissions = DB::table('permission_role')->where('role_id',$role['id'])->pluck('permission_id');
            foreach($permissions as $permission){
                PermissionRoleClientSpecific::create([
                  'permission_id' => $permission,
                  'client_id' => $client_id,
                  'role_id' => $role['id']
                ]);
            }

        }
  
}

?>