@extends('layouts.admin')
@section('content')

<?php
$breadcrum = array(
      array('link' => "", 'text' =>  'Find Sale Center' )
);
breadcrum ($breadcrum);
?>



		<div class="tpv-contbx" style="padding-bottom:0;">
			<div class="container">
					<div class="col-xs-12 col-sm-8 col-md-8">
					  <div class="cont_bx3 salescenter_contbx">
							<h1>Find Sales Center</h1>
                            <form class="form-horizontal"  enctype="multipart/form-data" role="form" method="GET" action="">
						     <div class="row">
								 <div class="col-xs-12 col-sm-8 col-md-8">

									<p>Select Client</p>
									<select class="selectmenu select-box-admin" name="client" id="client">
                                        <option value="">Select</option>
                                        @if(count($clients)>0)
                                           @foreach($clients as $client)
                                              <option value="{{$client->id}}"  <?php if($client_id == $client->id) echo "selected='selected'"; ?>>{{$client->name}}</option>
                                           @endforeach
                                        @endif
                                    </select>


                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
								 </div>
								 <div class="col-xs-12 col-sm-4 col-md-4">
									<button class="btn btn-green" type="submit">Submit<span class="add"><?php echo getimage('images/update_w.png') ?></span></button>
								 </div>
                             </div>
                        </form>
					</div>
				</div>
		</div>
	</div>

 @if($client_id != "")


<div class="tpv-contbx">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="cont_bx3">
                   <div class="tpvbtn">
					   <div class="col-xs-12 col-sm-12 col-md-12 top_sales">
                       <?php if(Auth::user()->hasPermissionTo('create-salescenters')){ ?>
                             <a href="{{route('client.createsalescenter',['id' =>$client_id ])}}" class="btn btn-green" data-toggle="modal" data-target="#addsalescenter" type="button">Add Sales Center<span class="add"><?php echo getimage('images/add.png'); ?></span></a>
                       <?php } ?>
                         </div>
                     </div>
                     @if ($message = Session::get('success'))
                         <div class="tpvbtn">
                              <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="alert alert-success">
                                        <p>{{ $message }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif


                        <?php if(Auth::user()->hasPermissionTo('view-salescenters')){ ?>
                     <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx">
						<div class="table-responsive">
						 	<table class="table">
                                <thead>
                                <tr class="heading acjin">
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                 $i = 0;
                                ?>
                                @if(count($client_salescenters) > 0)
                                @foreach ($client_salescenters as $key => $client_salescenter)
                                    <?php if($i % 2 == 0){
                                        $first_last_td_class = "light_c";
                                        $second_and_middle_td_class = "white_c";
                                    }else{
                                        $first_last_td_class = "dark_c";
                                        $second_and_middle_td_class = "grey_c";
                                    }
                                    ?>
                                    <tr class="list-users">
                                        <td class="{{$first_last_td_class}}">{{ ++$i }}</td>
                                        <td class="{{$second_and_middle_td_class}}"><a href="{{ route('client.salescenter.show',['id' => $client_id, 'salescenter_id' =>$client_salescenter->id  ]) }}">{{ $client_salescenter->name }}</a></td>
                                        <td class="{{$second_and_middle_td_class}}">{{ $client_salescenter->street }} {{ $client_salescenter->city }} ,{{ $client_salescenter->state }},
                                        {{ $client_salescenter->country }}, {{ $client_salescenter->zip }}
                                        </td>

                                        <td class="{{$first_last_td_class}}">
                                         <div class="btn-group">
                                         <?php if(Auth::user()->hasPermissionTo('view-salescenters')){ ?>
                                                <a href="{{ route('client.salescenter.show',['id' => $client_id, 'salescenter_id' =>$client_salescenter->id  ]) }}" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="View Sales center" class="btn"><?php echo getimage('images/view.png') ?></a>
                                         <?php  } ?>
                                         <?php if(Auth::user()->hasPermissionTo('update-salescenters')){ ?>
                                                <a class="btn" class="edit-link"  href="{{ route('client.salescenter.edit',['id' => $client_id, 'userid' =>$client_salescenter->id  ]) }}" data-toggle="tooltip" data-placement="top" data-container="body" title=""  data-original-title="Edit Sales Center"><?php echo getimage('/images/edit.png') ?></a>
                                                <?php  } ?>
                                            <?php if(Auth::user()->hasPermissionTo('delete-salescenters')){ ?>
                                                <?php if($client_salescenter->status=='active'){ ?>
                                                <a class="deactivate-clientuser btn delete-link"  href="javascript:void(0)" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Deactivate Sales Center" data-vid="{{ $client_salescenter->id }}" id="delete-clientuser-{{ $client_salescenter->id }}"  data-clientsalescenter="{{ $client_salescenter->name }}"><?php echo getimage('/images/deactivate_new.png') ?></a>
                                                    <?php } else {?>
                                                        <a class="activate-clientuser success-link btn"  href="javascript:void(0)" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Activate Sales Center"  data-vid="{{ $client_salescenter->id }}" id="delete-clientuser-{{ $client_salescenter->id }}"  data-clientsalescenter="{{ $client_salescenter->name }}"><?php echo getimage('/images/activate_new.png') ?></a>
                                                    <?php }?>
                                                 <?php  } ?>
                                             </div>

                                    </td>
                                    </tr>
                                @endforeach
                               @endif
                                @if(count($client_salescenters)==0)
                                <tr class="list-users">
                                    <td colspan="4" class="text-center">No Record Found</td>
                                </tr>
                                @endif
                                </tbody>
                            </table>

                            {!! $client_salescenters->render() !!}
                          </div>
                        </div>
                        <?php }?>
                  </div>
             </div>
         </div>
     </div>
 </div>

 @endif



  @include('client.salescenter.salescenterpoup')

  <div class="team-addnewmodal">
	  	<div class="modal fade" id="addsalescenter" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
			<div class="modal-content">

		  </div>
	</div>
 </div>

@endsection
