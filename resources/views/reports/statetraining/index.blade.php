@extends('layouts.admin')
@section('content')
<?php
$breadcrum = array(array('link' => '', 'text' => 'State Training report'));
breadcrum($breadcrum);
$request = Request::all();
?>


<div class="tpv-contbx" style="padding-bottom:0">
   <div class="container">
      <div class="col-xs-12 col-sm-12 col-md-12">
         <div class="cont_bx3 salesagent_contbx">
            <h1>Filters</h1>
            <?php if (Auth::user()->can(['view-salesagents'])) { ?>
               <form class="form-horizontal get-salesagents" enctype="multipart/form-data" role="form" method="GET" action="">
               <?php } ?>
               {{ csrf_field() }}
               <div class="row">


                  @if( Auth::user()->access_level =='tpv')
                  <div class="col-xs-12 col-sm-4 col-md-4">
                     <p>Select Client</p>
                     <select class="selectmenu select-box-admin selectclient" name="client" id="selectclient">
                        <option value="">Select</option>
                        @if(count($clients)>0)
                        @foreach($clients as $client)
                        <option value="{{$client->id}}" <?php if ($client_id == $client->id) echo "selected='selected'"; ?>>{{$client->name}}</option>
                        @endforeach
                        @endif
                     </select>
                  </div>

                  @endif
                  @if(Auth::user()->access_level =='client')
                  <input type="hidden" id="selectclient" name="client" value="{{$client_id}}" />
                  @endif
                  <div class="col-xs-12 col-sm-4 col-md-4">
                     <p>Sale Center</p>
                     <div class="select-center-wrapper">
                        <select class=" select-box-admin selectcenter" name="salecenter" id="salecenters">
                           <option value="">Select</option>
                           @if(count($sale_centers)>0)
                           @foreach($sale_centers as $salecenter)
                           <option value="{{$salecenter->id}}" <?php if ($salecenter_id == $salecenter->id) echo "selected='selected'"; ?>>{{$salecenter->name}}</option>
                           @endforeach
                           @endif

                        </select>
                     </div>
                  </div>
                  <div class="col-xs-12 col-sm-4 col-md-4">
                     <p>Location</p>
                     <div class="select-locaion-wrapper">
                        <select class="selectmenu select-box-admin selectlocation" name="location" id="location">
                           <option value="">Select</option>
                           @if(count($locations)>0)
                           @foreach($locations as $location)
                           <option value="{{$location->id}}" <?php if ($location_id == $location->id) echo "selected='selected'"; ?>>{{$location->name}}</option>
                           @endforeach
                           @endif

                        </select>
                     </div>
                  </div>
                  <div class="col-xs-12 col-sm-4 col-md-4">
                     <p>Certified</p>

                     <select class="selectmenu select-box-admin" name="certified" id="certified">
                        <option value="">Select</option>
                        <option value="1" <?php if (isset($request['certified']) && $request['certified'] == '1') echo "selected='selected'"; ?>>Yes</option>
                        <option value="0" <?php if (isset($request['certified']) && $request['certified'] == '0') echo "selected='selected'"; ?>>No</option>
                     </select>
                  </div>
                  <div class="col-xs-12 col-sm-4 col-md-4">
                     <p>Passed state test</p>

                     <select class="selectmenu select-box-admin" name="passed_state_test" id="passed_state_test">
                        <option value="">Select</option>
                        <option value="1" <?php if (isset($request['passed_state_test']) && $request['passed_state_test'] == '1') echo "selected='selected'"; ?>>Yes</option>
                        <option value="0" <?php if (isset($request['passed_state_test']) && $request['passed_state_test'] == '0') echo "selected='selected'"; ?>>No</option>
                     </select>
                  </div>
                  <div class="col-xs-12 col-sm-4 col-md-4">
                     <p>State</p>

                     <select class="selectmenu select-box-admin" name="state" id="state">
                        <option value="">Select</option>
                        <option value="MA" @if( isset($request['state']) && $request['state']=='MA' ) selected @endif>MA</option>
                        <option value="MD" @if( isset($request['state']) && $request['state']=='MD' ) selected @endif>MD</option>
                        <option value="NJ" @if( isset($request['state']) && $request['state']=='NJ' ) selected @endif>NJ</option>
                        <option value="NY" @if( isset($request['state']) && $request['state']=='NY' ) selected @endif>NY</option>
                        <option value="OH" @if( isset($request['state']) && $request['state']=='OH' ) selected @endif>OH</option>
                        <option value="PA" @if( isset($request['state']) && $request['state']=='PA' ) selected @endif>PA</option>


                        <option value="IL" @if( isset($request['state']) && $request['state']=='IL' ) selected @endif>IL</option>
                        <option value="CT" @if( isset($request['state']) && $request['state']=='CT' ) selected @endif>CT</option>
                        <option value="TX" @if( isset($request['state']) && $request['state']=='TX' ) selected @endif>TX</option>
                        <option value="MI" @if( isset($request['state']) && $request['state']=='MI' ) selected @endif>MI</option>
                     </select>
                  </div>


                  <div class="col-xs-12 col-sm-12 col-md-12">
                     <button class="btn btn-green btn-center" type="submit">Submit</button>
                  </div>

               </div>
               <?php if (Auth::user()->can(['view-salesagents'])) { ?>
               </form>
            <?php } ?>
         </div>
      </div>
   </div>
</div>





<div class="tpv-contbx mt30">
   <div class="container">
      <div class="row">
         <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="cont_bx3">
               <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx client-new-tabs">
                  <div class="client-bg-white">
                     <h1>State Training List</h1>
                     <div class="sales_tablebx">

                        @if ($message = Session::get('success'))
                        <div class="tpvbtn">
                           <div class="col-xs-12 col-sm-12 col-md-12">
                              <div class="alert alert-success">
                                 <p>{{ $message }}</p>
                              </div>
                           </div>
                        </div>
                        @endif
                        <div class="col-xs-12 col-sm-12 col-md-12 " style="margin-top:20px;margin-bottom:20px;">

                           <a class="btn btn-green pull-right" href="{{ route('reports.statetrainingexport',$export_params) }}">Export </a>
                        </div>
                        <div class="clearfix"></div>



                        <div class="table-responsive">
                           <table class="table responsive">
                              <thead>
                                 <tr class="heading">
                                    <th>No</th>
                                    <th>Vendor Name</th>
                                    <th>OfficeName</th>
                                    <th>SparkAgentId</th>
                                    <th>AgentId</th>
                                    <th>FirstName</th>
                                    <th>LastName</th>
                                    <th>Certified</th>
                                    <th>State</th>
                                    <th>CodeofConduct</th>
                                    <th>BackgroundCheck</th>
                                    <th>DrugTest</th>
                                    <th>Certification Date</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <?php $i = 0; ?>
                                 @foreach ($users as $key => $agent_user)
                                 <?php if ($i % 2 == 0) {
                                    $first_last_td_class = "light_c";
                                    $second_and_middle_td_class = "white_c";
                                 } else {
                                    $first_last_td_class = "dark_c";
                                    $second_and_middle_td_class = "grey_c";
                                 }
                                 ?>
                                 <tr>
                                    <td class="{{$first_last_td_class}}">{{ ++$i }}</td>
                                    <td class="{{$second_and_middle_td_class}}">{{ $agent_user->VendorName }}</td>
                                    <td class="{{$second_and_middle_td_class}}"> {{ $agent_user->OfficeName }}</td>
                                    <td class="{{$second_and_middle_td_class}}">{{ $agent_user->SparkAgentId }}</td>
                                    <td class="{{$second_and_middle_td_class}}">{{ $agent_user->AgentId }}</td>
                                    <td class="{{$second_and_middle_td_class}}">{{ $agent_user->FirstName }}</td>
                                    <td class="{{$second_and_middle_td_class}}">{{ $agent_user->LastName }}</td>
                                    <td class="{{$second_and_middle_td_class}}">{{ $agent_user->Certified }}</td>
                                    <td class="{{$second_and_middle_td_class}}">{{ $agent_user->State }}</td>
                                    <td class="{{$second_and_middle_td_class}}">{{ $agent_user->CodeofConduct }}</td>
                                    <td class="{{$second_and_middle_td_class}}">{{ $agent_user->BackgroundCheck }}</td>
                                    <td class="{{$second_and_middle_td_class}}">{{ $agent_user->DrugTest }}</td>

                                    <td class="{{$first_last_td_class}}">
                                       {{ $agent_user->CertificationDate }}

                                    </td>
                                 </tr>
                                 @endforeach
                                 @if(count($users)==0)
                                 <tr class="list-users">
                                    <td colspan="13" class="text-center">No Record Found</td>
                                 </tr>
                                 @endif
                              </tbody>
                           </table>


                        </div>
                        @if(count($users)>0)
                        <div class="btnintable bottom_btns">
                           {!! $users->appends(request()->query())->links() !!}
                        </div>
                        @endif
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>



@endsection