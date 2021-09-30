@extends('layouts.admin')
@section('content')

<?php 
$breadcrum = array();
if( Auth::user()->access_level =='tpv'){
$breadcrum[] =  array('link' => route('client.index') , 'text' =>  'Clients');
$breadcrum[] =  array('link' => route('client.show',$client->id) , 'text' =>  $client->name);
}

$breadcrum[] =  array('link' => '' , 'text' =>  "Tele Sale Info"); 
breadcrum ($breadcrum);
 ?>

 
 <div class="tpv-contbx">
		<div class="container">
					<div class="col-xs-12 col-sm-8 col-md-8">
                      @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success alert-dismissable">
                                {{ $message }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
					   <div class="cont_bx3 salescenter_contbx">
							<h1>Find Lead</h1>
                            <form class="form-horizontal"  enctype="multipart/form-data" role="form" method="GET" action="">
                            {{ csrf_field() }} 
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-8 col-md-8">
                                            <p>Enter reference ID</p>
                                            <div class=" {{ $errors->has('ref') ? ' has-error' : '' }}">
                                            <input id="name" type="text" class="form-control" name="ref" value="{{$reference_id}}"      required >

                                                @if ($errors->has('ref'))
                                                        <span class="help-block">
                                                        <strong>{{ $errors->first('ref') }}</strong>
                                                    </span>
                                                    @endif
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-4 col-md-4">
                                            <button class="btn btn-green" type="submit">Submit<span class="add"><img src="/images/update_w.png"/></span></button>
                                        </div>
                                    </div> 
                             </form> 
                       </div>

                  </div>
                <div class="clearfix"></div>
                 

                        @if(count($sale_detail)>0)
                        <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx">
                        <div class="table-responsive">
                            <table class="table">
                             <thead>
                              <tr class="heading">
                              <th>Label</th>
                              <th>Value</th>
                              </tr>
                            </thead>
                            <tbody>
                            <tr>
                              <td class="light_c">Reference ID</td>
                              <td class="white_c">{{$reference_id}}</td>
                            </tr>
                            <?php $j = 0;?>
                              @foreach($sale_detail as  $sale)
                              <?php 
                              	if(in_array($sale->meta_key,$formFieldsArr)){
                              $j++;   
                               if($j % 2 == 0){ 
                                            $first_last_td_class = "light_c";
                                            $second_and_middle_td_class = "white_c";
                                        }else{
                                            $first_last_td_class = "dark_c";
                                            $second_and_middle_td_class = "grey_c";
                                            }
                                        ?>

                              <tr>
                              <td class="{{$first_last_td_class}}">{{$sale->meta_key}}</td>
                              <td class="{{$second_and_middle_td_class}}">{{$sale->meta_value}}</td>
                            </tr>
                            <?php }?>
                              @endforeach
                              @if(count($image_data) > 0)

                                @foreach($image_data as  $media_data)
                                <?php
                                $j++;
                                 if($j % 2 == 0){ 
                                            $first_last_td_class = "light_c";
                                            $second_and_middle_td_class = "white_c";
                                        }else{
                                            $first_last_td_class = "dark_c";
                                            $second_and_middle_td_class = "grey_c";
                                            }
                                        ?>
                                <tr>
                                <td class="{{ $first_last_td_class}}">{{$media_data['file_type']}}</td>
                                <td class="{{ $second_and_middle_td_class}}"><a href="{{$media_data['url']}}" target="_blank">{{$media_data['name']}}</a></td>
                                </tr> 
                                 
                                @endforeach

                                @endif
                              <?php 
                             
                               if($j % 2 == 0){
                                           $first_last_td_class = "dark_c";
                                           $second_and_middle_td_class = "grey_c";
                                          
                                        }else{
                                            $first_last_td_class = "light_c";
                                            $second_and_middle_td_class = "white_c";
                                            }
                                        ?>
                              <tr>
                              <td class="{{$first_last_td_class}}">Status</td>
                              <td class="{{$second_and_middle_td_class}}" style="text-transform:capitalize">{{$sale_info->status}}</td>
                            </tr>
                            <?php 
                              $j++;   
                               if($j % 2 == 0){
                                           $first_last_td_class = "dark_c";
                                           $second_and_middle_td_class = "grey_c";
                                          
                                        }else{
                                            $first_last_td_class = "light_c";
                                            $second_and_middle_td_class = "white_c";
                                            }
                                        ?>
                            <tr>
                              <td class="{{$first_last_td_class}}">Reviewed by</td>
                              <td class="{{$second_and_middle_td_class}}">{{$reviewedby}}</td>
                            </tr>
                                        </tbody>
                           </table>    
                         </div>              
                        </div>              
                        @endif
         </div>
</div> 
 
        
@endsection