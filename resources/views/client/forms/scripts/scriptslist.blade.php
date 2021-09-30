

@extends('layouts.admin')
@section('content')
<?php
   $breadcrum = array();
   if( Auth::user()->access_level =='tpv'){
     $breadcrum[] = array('link' => route('client.index'), 'text' =>  'Clients' );
     $breadcrum[] = array('link' => route('client.show',$client->id) , 'text' =>  $client->name );
   }
     $breadcrum[] = array('link' =>  route('client.contact-forms',['id' => $client->id]) , 'text' =>  'Forms' );
     $breadcrum[] = array('link' => route('client.contact-page-layout',['id' => $client->id, 'form_id' => $form_id]) , 'text' =>  $form_detail->formname );
     $breadcrum[] = array('link' => route('client.contact-forms-scripts-langauge',['client_id' => $client->id, 'form_id' => $form_id]) , 'text' =>  $language  );
     $breadcrum[] = array('link' => '', 'text' =>  'Form Scripts' );
     breadcrum ($breadcrum);
    ?>
<div class="tpv-contbx">
   <div class="container">
      <div class="row">
         <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="cont_bx3">
               <div class="col-xs-12 col-sm-12 col-md-12 tpv_heading">
                  <h1>Scripts</h1>
               </div>
               <div class="tpvbtn">
                  <div class="col-xs-12 col-sm-12 col-md-12 top_sales">
                     <a class="btn btn-green" href="{{ route('client.contact-forms-new-scripts',['client_id' => $client->id, 'form_id' => $form_id,'language' => $language] ) }}"  data-toggle="modal" data-target="#addnewscript"   >New Script<span class="add"><?php echo getimage('images/add.png') ?></span></a>
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
                              <th>Sr.No</th>
                              <th>Form Name</th>
                              <th>Script For</th>
                              <th></th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php $i = 0; ?>
                           @foreach ($scripts_list as $key => $script)
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
                              <td class="{{$second_and_middle_td_class}}">{{ $script->title }}</td>
                              <td class="{{$second_and_middle_td_class}}">{{ $script_for[$script->scriptfor] }}</td>
                              <td class="{{$first_last_td_class}}">
                                 <div class="btn-group">
                                    <a class="btn"
                                       href="{{  route('client.edit-forms-script',['client_id' => $client->id,
                                       'form_id' => $form_id,
                                       'script_id' => $script->id]) }}"
                                       data-toggle="tooltip"
                                       data-placement="top" data-container="body"
                                       title=""
                                       data-original-title="Edit/View Script" role="button"><?php echo getimage("images/edit.png"); ?> </a>
                                    <a class="btn delete-script"
                                       href="javascript:void(0)"
                                       data-toggle="tooltip"
                                       data-placement="top" data-container="body"
                                       title=""
                                       data-original-title="Delete Script"
                                       data-id="{{ $script->id }}"
                                       id="delete-script-{{ $script->id }}"
                                       data-scriptname="{{ $script->title }}"
                                       role="button"><?php echo getimage("images/cancel.png"); ?> </a>
                                    <a
                                       class="btn"
                                       href="{{  route('client.view-script-questions',['client_id' => $client->id,
                                       'form_id' => $form_id,
                                       'script_id' => $script->id]) }}"
                                       data-toggle="tooltip"
                                       data-placement="top" data-container="body"
                                       title=""
                                       data-original-title="View Questions"
                                       >
                                    <?php echo getimage("images/view.png"); ?>
                                    </a>

                                 </div>
                              </td>
                           </tr>
                           @endforeach
                           @if(count($scripts_list)==0)
                           <tr class="list-users">
                              <td colspan="4" class="text-center">No Record Found</td>
                           </tr>
                           @endif
                        </tbody>
                     </table>
                     {!! $scripts_list->render() !!}
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@include('client.forms.scripts.scriptpoup')
<div class="team-addnewmodal">
  <div class="modal fade" id="addnewscript" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        </div>
    </div>
  </div>
</div>
@endsection
