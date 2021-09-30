@extends('layouts.admin')
@section('content')
<?php
$breadcrum = array();
$breadcrum[] =  array('link' => '', 'text' =>  "Programs Report");
breadcrum($breadcrum);
?>
<?php
$request = Request::all();


?>
<div class="tpv-contbx edit-agentinfo">
   <div class="container">
      <div class="row">
         <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="cont_bx3">
               <div class="col-xs-12 col-sm-12 col-md-12 tpv_heading">
                  <div class="client-bg-white">
                     <h1>Programs</h1>
                     <div class="sales_tablebx">

                        <!-- Tab panes -->
                        <div class="tab-content">
                           <!--agent details starts-->
                           <div role="tabpanel" class="tab-pane @if(!isset($request['search'])) active @endif " id="agentdetail">
                              <div class="row">
                                 <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="agent-detailform">
                                       <div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-6 col-md-offset-3">
                                          @if (count($errors) > 0)
                                          <div class="alert alert-danger">
                                             <strong>Whoops!</strong> Invalid input.<br><br>
                                             <ul>
                                                @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                                @endforeach
                                             </ul>
                                          </div>
                                          @endif
                                          <form enctype="multipart/form-data" role="form" method="get" action="">
                                             {{ csrf_field() }}
                                             <div class="form-group {{ $errors->has('vendorstatus') ? ' has-error' : '' }}">
                                                <label for="vendorstatus">Vendor status</label>
                                                <div class="dropdown select-dropdown">
                                                   <select class="selectsearch form-control vendorstatus" id="vendorstatus" name="vendorstatus">
                                                      <option value="">Select by status</option>
                                                      <option value="active" @if(isset($request['vendorstatus']) && $request['vendorstatus']=='active' ) selected @endif>Active</option>
                                                      <option value="inactive" @if(isset($request['vendorstatus']) && $request['vendorstatus']=='inactive' ) selected @endif>In-active</option>
                                                   </select>
                                                   @if ($errors->has('vendorstatus'))
                                                   <span class="help-block text-danger">
                                                      <strong>{{ $errors->first('vendorstatus') }}</strong>
                                                   </span>
                                                   @endif
                                                </div>
                                             </div>
                                             <div class="form-group {{ $errors->has('client') ? ' has-error' : '' }}">
                                                <label for="salesvendor">Vendor</label>
                                                <div class="dropdown select-dropdown">
                                                   <div class="update_client_by_location">
                                                      <select class="selectsearch form-control selectclientlocations_report" id="salesvendor" name="client">
                                                         <option value="">All Vendors</option>
                                                         @if( count($clients) > 0)
                                                         @foreach($clients as $client)
                                                         <option value="{{$client->id}}" @if(isset($request['client']) && $request['client']==$client->id ) selected @endif >{{$client->name}}</option>
                                                         @endforeach
                                                         @endif
                                                      </select>
                                                      @if ($errors->has('client'))
                                                      <span class="help-block text-danger">
                                                         <strong>{{ $errors->first('client') }}</strong>
                                                      </span>
                                                      @endif
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="btnintable bottom_btns">
                                                <div class="btn-group">
                                                   <button class="btn btn-green" type="submit">Submit</button>
                                                </div>
                                             </div>
                                          </form>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <!--agent details ends-->
                        </div>
                        <!--twilio setting content ends-->
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <div class="row mt30">
         <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="cont_bx3 report-main-tab report-tabs-result">
               <div class="col-xs-12 col-sm-12 col-md-12 tpv_heading">
                  <div class="client-bg-white">
                     @if(count($results) > 0)
                     <div class="row">
                     <div class="col-12">
                        <a class="btn btn-green pull-right" href="{{ route('reports.programexport',$export_params) }}">Export </a>
                     </div>
                     </div>
                     @endif
                     <div class="sales_tablebx mt30">
                        @if(count($results) > 0)
                        <div class="table-responsive">
                           <table class="table ">
                              <thead>
                                 <tr class="heading">
                                    @foreach($results[0] as $heading => $value)
                                    <td> {{ $heading }} </td>
                                    @endforeach
                                 </tr>
                              </thead>
                              <tbody>
                                 <?php $i = 0; ?>
                                 @foreach($results as $report)
                                 <?php $i++;
                                 if ($i % 2 == 0) {
                                    $first_last_td_class = "";
                                    $second_and_middle_td_class = "";
                                 } else {
                                    $first_last_td_class = "";
                                    $second_and_middle_td_class = "";
                                 }
                                 ?>
                                 <tr>
                                    @foreach($report as $headinglabel => $valueoflead )
                                    <td class="{{$first_last_td_class}}">{{ $valueoflead }}</td>
                                    @endforeach
                                 </tr>
                                 @endforeach
                              </tbody>
                           </table>
                        </div>
                        @if(count($results)> 0)
                        <div class="btnintable bottom_btns">
                           {!! $results->appends($query_params)->links()!!}
                        </div>
                        @endif

                        @else
                        <h2>No Record Found</h2>
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