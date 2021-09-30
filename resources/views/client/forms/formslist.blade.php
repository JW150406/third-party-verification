@extends('layouts.admin')
@section('content')
<?php
$breadcrum = array();
if (Auth::user()->access_level == 'tpv') {
   $breadcrum[] = array('link' => route('client.index'), 'text' =>  'Clients');
   $breadcrum[] = array('link' => route('client.show', $client->id), 'text' =>  $client->name);
}

$breadcrum[] = array('link' => '', 'text' =>  'Lead creation forms');
breadcrum($breadcrum);
?>


<div class="tpv-contbx">
   <div class="container">
      <div class="row">
         <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="cont_bx3">
               <div class="col-xs-12 col-sm-12 col-md-12 tpv_heading">
                  <h1>Forms</h1>
               </div>
               <div class="tpvbtn">

                  <?php
                  $hasGasElectric  = 0;
                  $hasdual  = 0;
                  if (count($forms_list) > 0) {
                     if ($forms_list[0]->commodity_type == 'GasOrElectric') {
                        $hasGasElectric = 1;
                     }
                     if ($forms_list[0]->commodity_type == 'DualFuel') {
                        $hasdual  = 1;
                     }
                     if (isset($forms_list[1]->commodity_type) && $forms_list[1]->commodity_type == 'GasOrElectric') {
                        $hasGasElectric = 1;
                     }
                     if (isset($forms_list[1]->commodity_type) && $forms_list[1]->commodity_type == 'DualFuel') {
                        $hasdual  = 1;
                     }
                  }



                  ?>
                  @if( $hasdual == 0 || $hasGasElectric == 0)
                  <div class="col-xs-12 col-sm-12 col-md-12 top_sales">
                     @if( $hasGasElectric == 0)
                     <a class="btn btn-green" href="{{ route('client.create-contact-page',$client->id) }}?ftype=GasOrElectric">New form for gas or electric <span class="add"><?php echo getimage('images/add.png') ?></span></a>
                     @endif
                     @if( $hasdual == 0 )

                     <a class="btn btn-green" href="{{ route('client.create-contact-page',$client->id) }}?ftype=DualFuel">New form for dual fuel<span class="add"><?php echo getimage('images/add.png') ?></span></a>
                     @endif
                  </div>
                  @endif
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
                              <th>Sr.No</th>
                              <th>Form Name</th>
                              <th></th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php $i = 0; ?>
                           @foreach ($forms_list as $key => $form)
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
                              <td class="{{$second_and_middle_td_class}}">{{ $form->formname }}</td>
                              <td class="{{$first_last_td_class}}">
                                 <div class="btn-group">
                                    <a class="btn" href="{{  route('client.contact-page-layout',['id' => $client->id,'fid' => $form->id]) }}" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Edit/View Form" role="button"><?php echo getimage("images/edit.png"); ?> </a>
                                    <a class="delete-form btn" href="javascript:void(0)" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Delete Form" data-id="{{ $form->id }}" data-cid="{{ $client_id }}" id="delete-form-{{ $form->id }}" data-formname="{{ $form->formname }}" role="button"><?php echo getimage("images/cancel.png"); ?> </a>
                                    <a class="btn" href="{{route('client.contact-forms-scripts-langauge',['client_id' => $client_id, 'form_id' => $form->id])}}">
                                       <?php echo getimage("images/view.png"); ?>
                                    </a>
                                 </div>
                              </td>
                           </tr>
                           @endforeach
                           @if(count($forms_list)==0)
                           <tr class="list-users">
                              <td colspan="3" class="text-center">No Record Found</td>
                           </tr>
                           @endif
                        </tbody>
                     </table>
                     {!! $forms_list->render() !!}
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>



@include('client.forms.formpoup')

@endsection