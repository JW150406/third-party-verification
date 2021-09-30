@extends('layouts.admin')

@section('content')

<?php
$breadcrum = array() ;
if( Auth::user()->access_level =='tpv')
{
    $breadcrum[] = array('link' => route('client.findsalecenter'), 'text' =>  'Find Sales Center' );
    $breadcrum[] = array('link' =>  route('client.findsalecenter',['client' => $client->id]) , 'text' =>  $client->name );
    $breadcrum[] = array('link' => route('client.salescenter.show',['id' => $client->id, 'salescenter_id' => $salescenter->id ]), 'text' => $salescenter->name);
}
if(Auth::user()->access_level =='client'){
    $breadcrum[] = array('link' =>  route('client.salescenters',$client->id) , 'text' =>  'Sales Centers' );
    $breadcrum[] = array('link' => route('client.salescenter.show',['id' => $client->id, 'salescenter_id' => $salescenter->id ]), 'text' => $salescenter->name);
}

$breadcrum[] = array('link' => '', 'text' =>'Locations');
breadcrum ($breadcrum)
?>
	<?php if(Auth::user()->access_level == 'client' || Auth::user()->access_level == 'salescenter' ||  Auth::user()->can(['create-sales-center-locations', 'update-sales-center-locations', 'view-sales-center-locations', 'delete-sales-center-locations'])){ ?>
 <div class="tpv-contbx">
   <div class="container">
      <div class="row">
         <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="cont_bx3">
               <div class="col-xs-12 col-sm-12 col-md-12 tpv_heading">
                  <h1>Locations</h1>
               </div>
               <div class="tpvbtn">
                  <div class="col-xs-12 col-sm-12 col-md-12 top_sales">
                  <?php if(Auth::user()->access_level == 'client' || Auth::user()->access_level == 'salescenter' ||  Auth::user()->hasPermissionTo('create-sales-center-locations')){ ?>
                     <a class="btn btn-green" href="{{ route('client.salescenter.addlocation',['client_id' => $client_id, 'salescenter_id' =>$salescenter_id  ]) }}"  data-toggle="modal" data-target="#addsalescenterlocation" >Add Location<span class="add"><?php echo getimage('images/add.png') ?></span></a>
                  <?php }?>
                  </div>
                  <div class="clearfix"></div>
                  @if ($message = Session::get('success'))
                  <div class="alert alert-success">
                     <p>{{ $message }}</p>
                  </div>
                  @endif

               </div>
               <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx">
                  <div class="table-responsive">
                     <table class="table responsive">
                        <thead>
                           <tr class="heading">
                                <th>No</th>
                                <th>Name</th>
                                <th>Address</th>
                                 <th></th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php $i = 0; ?>
                           @foreach ($locations as $key => $center_location)
                           <?php if($i % 2 == 0){
                              $first_last_td_class = "light_c";
                              $second_and_middle_td_class = "white_c";
                              }else{
                              $first_last_td_class = "dark_c";
                              $second_and_middle_td_class = "grey_c";
                              }
                              ?>
                           <tr>
                              <td class="{{$first_last_td_class}}">{{ ++$i }}</td>
                              <td class="{{$second_and_middle_td_class}}">{{ $center_location->name }}</td>
                              <td class="{{$second_and_middle_td_class}}">{{ $center_location->street }} {{ $center_location->city }}, {{ $center_location->state }}, {{ $center_location->country }}, {{ $center_location->zip }}</td>
                              <td class="{{$first_last_td_class}}">
                                 <div class="btn-group">
                                 <?php if(Auth::user()->access_level == 'client' || Auth::user()->access_level == 'salescenter' || Auth::user()->hasPermissionTo('update-sales-center-locations') ){ ?>
                                    <a class="btn"  data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Edit Location"  href="{{ route('client.salescenter.location.edit',['client_id' => $client_id, 'salescenter_id'=>$center_location->salescenter_id, 'location_id' =>$center_location->id  ]) }}" role="button"><?php echo getimage("images/edit.png"); ?> </a>
                                 <?php }?>
                                 </div>
                              </td>
                           </tr>
                           @endforeach
                           @if(count($locations)==0)
                           <tr class="list-users">
                              <td colspan="5" class="text-center">No Record Found</td>
                           </tr>
                           @endif
                        </tbody>
                     </table>
                     {!! $locations->render() !!}
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php if(Auth::user()->access_level == 'client' || Auth::user()->access_level == 'salescenter' || Auth::user()->hasPermissionTo('create-sales-center-locations')){ ?>
<div class="team-addnewmodal">
<div class="modal fade" id="addsalescenterlocation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
      </div>
   </div>
</div>
     <?php }?>
     <?php }?>
@endsection
