@extends('layouts.admin')

@section('content')
<?php  error_reporting(0); 

$today_verified_percent = floor(($today_verified * 100) /( $today_verified + $today_decline));
$today_decliine_percent = floor(($today_decline * 100) /( $today_verified + $today_decline));
$today_verified_percent = (is_nan($today_verified_percent)) ? "0" : $today_verified_percent;
$today_decliine_percent = (is_nan($today_decliine_percent)) ? "0" : $today_decliine_percent;

$weekly_verified_percent = floor(($weekly_verified * 100) /( $weekly_verified + $weekly_decline));
$weekly_decliine_percent = floor( ($weekly_decline * 100) /( $weekly_verified + $weekly_decline));
$weekly_verified_percent = (is_nan($weekly_verified_percent)) ? "0" : $weekly_verified_percent;
$weekly_decliine_percent = (is_nan($weekly_decliine_percent)) ? "0" : $weekly_decliine_percent;

$monthly_verified_percent = floor(($monthly_verified * 100) /( $monthly_verified + $monthly_decline));
$monthly_decliine_percent = floor(($monthly_decline * 100) /( $monthly_verified + $monthly_decline));
$monthly_verified_percent = (is_nan($monthly_verified_percent)) ? "0" : $monthly_verified_percent;
$monthly_decliine_percent = (is_nan($monthly_decliine_percent)) ? "0" : $monthly_decliine_percent;

$yearly_verified_percent = floor(($yearly_verified * 100) /( $yearly_verified + $yearly_decline));
$yearly_decliine_percent = floor(($yearly_decline * 100) /( $yearly_verified + $yearly_decline));
$yearly_verified_percent = (is_nan($yearly_verified_percent)) ? "0" : $yearly_verified_percent;
$yearly_decliine_percent = (is_nan($yearly_decliine_percent)) ? "0" : $yearly_decliine_percent;
?>
<!-- dashboar heading text with range selector -->
<div class="cont_bx1">
   <div class="container">
      <div class="col-xs-12 col-sm-12 col-md-12">
         <div class="row">
            <div class="col-xs-12 col-sm-6 col-md-6">
               <div id="welcome">
                  <p>Dashboard</p>
               </div>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-6">
               <div class="select_range_bx  pull-right">
               <form class="get-clientreport-data" action="" method="post" >
                    {{ csrf_field() }} 
                    <input type="hidden" name="client_id" id="reportclientid" value="{{Auth::user()->client_id}}">
                        <div class="form-group">
                        <label class="control-label">Select Range</label>                 
                          <select class="changedaterange selectmenu">
                            <option value="today">Today</option>
                            <option value="wtd" selected>WTD</option>
                            <option value="mtd">MTD</option>
                            <option value="ytd">YTD</option>
                          </select>
                        </div>
                    </form> 
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- end range selector -->
<div class="cont_bx2">
   <div class="container">
      <div class="col-xs-12 col-sm-12 col-md-12">
         <div class="row">
            <!-- verified today -->
            <div class="col-xs-12 col-sm-6 col-md-6">
               <div class="verify_today">
                  <div class="col-xs-12 col-sm-12 col-md-12">
                     <h1>Verified Today</h1>
                  </div>
                  <div class="col-xs-12 col-sm-6 col-md-6 detailbx">

				       <div class="verified-numeric">
							<p>{{$today_verified}}</p>
						</div>
						<div class="verified-leads">
							<span class="leads">Leads</span><span class="verified">verified today</span>
						</div>
						<div class="viewmore-btn">
							<!-- <button class="btn viewmore hvr-bounce-in" type="button">View more</button> -->
						</div> 
                       
                  </div>
                  <div class="col-xs-12 col-sm-6 col-md-6">
                     <div  class="gauge" >
                        <canvas id="gauge1" width="130" height="80"></canvas>
                        <div class="dotted-border"></div>
                        <div class="gauge-arrow" data-percentage="40" style="transform: rotate(-90deg);"></div>
                     </div>
                     <p class="verify_report">{{$today_verified_percent}}% verified</p>
                  </div>
               </div>
            </div>
            <script>
               $(document).ready(function (){
               	$("#gauge1").gauge({{$today_verified_percent}},{ unit: "",type: "halfcircle"});
               		
               });
            </script>
            <!-- end verified today -->
            <!-- verified WTD  -->
            <div class="col-xs-12 col-sm-6 col-md-6">
               <div class="verify_today">
                  <div class="col-xs-12 col-sm-12 col-md-12">
                     <h1>Verified WTD</h1>
                  </div>
                  <div class="col-xs-12 col-sm-6 col-md-6 detailbx">
					    <div class="verified-numeric">
							<p>{{$weekly_verified}}</p>
						</div>
						<div class="verified-leads">
							<span class="leads">Leads</span><span class="verified">verified WTD</span>
						</div>
						<div class="viewmore-btn">
							<!-- <button class="btn viewmore hvr-bounce-in" type="button">View more</button> -->
						</div>   
                  </div>
                  <div class="col-xs-12 col-sm-6 col-md-6">
                     <div  class="gauge" >
                        <canvas id="wtdgauge" width="130" height="80"></canvas>
                        <div class="dotted-border"></div>
                        <div class="gauge-arrow" data-percentage="{{$weekly_verified_percent}}" style="transform: rotate(-90deg);"></div>
                     </div>
                     <p class="verify_report">{{$weekly_verified_percent}}% verified</p>
                  </div>
               </div>
            </div>
         </div>
         <script>
            $(document).ready(function (){
            	$("#wtdgauge").gauge({{$weekly_verified_percent}},{ unit: "",type: "halfcircle"});
            		
            });
         </script>
         <div class="row">
            <!-- verified MTD  -->
            <div class="col-xs-12 col-sm-6 col-md-6">
               <div class="verify_today">
                  <div class="col-xs-12 col-sm-12 col-md-12">
                     <h1>Verified MTD</h1>
                  </div>
                  <div class="col-xs-12 col-sm-6 col-md-6 detailbx">
				  <div class="verified-numeric">
							<p>{{$monthly_verified}}</p>
						</div>
						<div class="verified-leads">
							<span class="leads">Leads</span><span class="verified">verified MTD</span>
						</div>
						<div class="viewmore-btn">
							<!-- <button class="btn viewmore hvr-bounce-in" type="button">View more</button> -->
						</div>  
                    
                  </div>
                  <div class="col-xs-12 col-sm-6 col-md-6">
                     <div  class="gauge" >
                        <canvas id="monthlyverified" width="130" height="80"></canvas>
                        <div class="dotted-border"></div>
                        <div class="gauge-arrow" data-percentage="{{$monthly_verified_percent}}" style="transform: rotate(-90deg);"></div>
                     </div>
                     <p class="verify_report">{{$monthly_verified_percent}}% verified</p>
                  </div>
               </div>
            </div>
            <script>
               $(document).ready(function (){
               	$("#monthlyverified").gauge({{$monthly_verified_percent}},{ unit: "",type: "halfcircle"});
               		
               });
            </script>
            <!-- end verified MTD  -->
            <!-- verified YTD  -->
            <div class="col-xs-12 col-sm-6 col-md-6">
               <div class="verify_today">
                  <div class="col-xs-12 col-sm-12 col-md-12">
                     <h1>Verified YTD</h1>
                  </div>
                  <div class="col-xs-12 col-sm-6 col-md-6 detailbx">
				       <div class="verified-numeric">
							<p>{{$yearly_verified}}</p>
						</div>
						<div class="verified-leads">
							<span class="leads">Leads</span><span class="verified">verified YTD</span>
						</div>
						<div class="viewmore-btn">
							<!-- <button class="btn viewmore hvr-bounce-in" type="button">View more</button> -->
						</div> 
 
                  </div>
                  <div class="col-xs-12 col-sm-6 col-md-6">
                     <div  class="gauge" >
                        <canvas id="yearlyverified" width="130" height="80"></canvas>
                        <div class="dotted-border"></div>
                        <div class="gauge-arrow" data-percentage="{{$yearly_verified_percent}}" style="transform: rotate(-90deg);"></div>
                     </div>
                     <p class="verify_report">{{$yearly_verified_percent}}% verified</p>
                  </div>
               </div>
            </div>
            <script>
               $(document).ready(function (){
               	$("#yearlyverified").gauge({{$yearly_verified_percent}},{ unit: "",type: "halfcircle"});
               		
               });
            </script>
            <!--  end verified YTD  -->
         </div>
      </div>
   </div>
</div>

 <div class="cont_bx2" style="padding-top:0;">
   <div class="container">
          <div class="col-12" id="dashboard-report-wrapper">
                @include('admin.elements.client.index')         
              </div>
              </div>
  </div>
 
 
  
@endsection
