     @if(!empty($reference_id) && count($sale_detail)==0 )
                          <h2> No Record found </h2>
                        @endif

                        @if(count($sale_detail)>0)
                        @if(!empty($from_call))
                        <a href="{{ route('tpvagent.agentsales',['uid' =>  $sale_info->user_id ])}}" class="getagentsales"><i class="fa fa-arrow-left"></i> Back</a>
                        @endif
                            <table class="table table-bordered">
                            <tr>
                              <th>Label</th>
                              <th>Value</th>
                            </tr>
                            <tr>
                              <th>Reference ID</th>
                              <th>{{$reference_id}}</th>
                            </tr>
                            
                              @foreach($sale_detail as  $sale)
                              <tr>
                              <th>{{$sale->meta_key}}</th>
                              <td>{{$sale->meta_value}}</td>
                            </tr>
                               
                              @endforeach
                            </table>     
                            <div class="clearfix"></div>

                        <?php 
                                      
                        if( $sale_info->status=='pending' ){
                            ?>
                            <div class="form-group">
                              <a  data-target="#confirmreview" data-toggle="modal"  href="javascript:void(0);" class="btn btn-success verify-sale">Verify</a>&nbsp;&nbsp;
                               <a href="javascript:void(0);" class="btn btn-danger decline-form">Decline</a>&nbsp;&nbsp;
                            </div>
                            <div class="clearfix"></div>
                            <form class="form-horizontal decline-sale-form"  enctype="multipart/form-data" role="form" method="GET" action="" onSubmit="return false;" style="display:none">
                           
                              {{ csrf_field() }} 
                              <div class="form-group">
                                <label for="name" class="col-md-4 control-label">Decline Reason</label>

                                <div class="col-md-6">
                                    <ul class="list-inline">
                                  
                                     @if(count($dispositions) > 0)
                                       
                                       @foreach($dispositions as $singledisposition)
                                       <li>
                                            <span><input type="radio" name="reason" class="getreason" value="{{$singledisposition->id}}"  <?php if($disposition_id==""){ echo 'checked';} $disposition_id =$singledisposition->id ?>   ></span>
                                             <span> {{$singledisposition->description}}</span>
                                     </li>
                                       @endforeach
                                     @endif
                                    
                                     <li>
                                       <span><input type="radio" name="reason" class="getreason" value="other" ></span>
                                       <span>Other</span>
                                     </li>
                                    </ul>
                                    <input type="hidden" id="decline_reason" name="decline_reason" value="Incomplete information" class="form-control decline_reason">

                                    
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">

                                    <button type="submit" data-target="#confirmreview" data-toggle="modal" class="btn btn-primary decline-confirm" style="margin-top:10px;">
                                        Submit
                                    </button>

                                    
                                </div>
                            </div>
                            </div>
                            </form>
                    
                        <?php }else{
                            ?>

                            <table class="table table-bordered">
                            <tr>
                              <th>Status</th>
                              <th style="text-transform:capitalize">{{$sale_info->status}}</th>
                            </tr>
                            <tr>
                              <th>Reviewed by</th>
                              <th>{{$reviewedby}}</th>
                            </tr>
                            <tr>
                              <th>Decline Reason</th>
                              <th>{{$sale_info->decline_reason}}</th>
                            </tr>
                            </table>
                            <?php 
                        } ?>
                            
                @endif