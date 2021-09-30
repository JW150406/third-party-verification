@extends('layouts.admin')

@section('content')
<div class="tpv-contbx edit-agentinfo">
	 <div class="container">
            <div class="cont_bx3">
           
@if(count($report_data)>0)
<?php
$quert_string = "?";
$request = Request::all();
  foreach ($request as $key => $value) {
    $quert_string .="$key=$value&";
  }
?>           <div class="col-xs-12 col-sm-12 col-md-12">
 
                <a class="btn btn-green" href="{{ route('report.exportbatch').$quert_string  }}">Export<span class="add"><i class="fa fa-download"></i></span></a>

              </div>
                  
                <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx">
                    <div class="table-responsive">
                      <table class="table">
                          <?php $j = 0;?>
                          @foreach($report_data as $headervalue )
                          
                            <tr class="@if($j == 0) heading   @endif">
                              <?php $i = 0; ?>
                              @foreach($headervalue as $heading => $val)
                              <?php if($j % 2 == 0){
                                     $first_last_td_class = "light_c";
                                     $second_and_middle_td_class = "white_c";
                                }else{
                                    $first_last_td_class = "dark_c";
                                    $second_and_middle_td_class = "grey_c";
                                }
                                ?>
                                @if($j == 0)
                                  <td>{{$heading}}</td>
                                @else

                                  <td class="{{$first_last_td_class}}">{{$val}}</td>
                                @endif
                                <?php $i++; ?>
                              @endforeach
                            </tr>
                          <?php $j++;?>
                        @endforeach
                        </table>
                      </div>
                      <div class="btnintable bottom_btns" style="box-shadow: 0px 10px 15px 3px rgba(0,0,0,0.4);border-radius: 0 0 16px 16px; position: relative;top: -12px;z-index: 10;">
                      {!! $report_data->appends($request)->links()!!}
                      </div>
                  </div>
 

 


@else
<div class="text-center"><h2>No Record Found</h2></div>
@endif
@endsection
              
          </div> 
     </div>
</div>