<?php error_reporting(0); ?>
<div class="cont_bx3 cont_bx4">
   <div class="col-xs-12 col-sm-12 col-md-12">
      <div class="client-bg-white">
         <h1>Brokers</h1>
         <div class="sales_tablebx mt30">
            <div class="table-responsive">
               <table class="table">
                  <thead>
                     <tr class="acjin">
                        <th>Sale Center Name</th>
                        <th>Client Name</th>
                        <th>Agent Name</th>
                        <th>Agent ID</th>

                        <th>Action</th>
                     </tr>
                  </thead>
                  <tbody>

                     @if(count($active_salesagents) > 0)
                     <?php $i = 1; ?>
                     @foreach($active_salesagents as $active_salesagent)
                     <?php
                     if ($i % 2 == 0) {
                        $first_last_td_class = "dark_c";
                        $second_and_middle_td_class = "grey_c";
                     } else {
                        $first_last_td_class = "light_c";
                        $second_and_middle_td_class = "white_c";
                     }
                     ?>
                     <tr>
                        <td class="{{$first_last_td_class}}">{{$active_salesagent['salescenter_name']}}</td>
                        <td class="{{$second_and_middle_td_class}}">{{$active_salesagent['name']}}</td>
                        <td class="{{$second_and_middle_td_class}}">{{$active_salesagent['first_name']}} {{$active_salesagent['last_name']}}</td>
                        <td class="{{$second_and_middle_td_class}}">{{$active_salesagent['userid']}}</td>
                        <td class="{{$first_last_td_class}}">
                           <p class="bx_icons all-clients" style="padding:0 0 0 0;">
                              <a href="javascript:void(0)" role="button" class="logout-agent btn red" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Logout" data-aid="{{ $active_salesagent['id'] }}" id="logout-agent-{{$active_salesagent['id'] }}" data-agentname="{{$active_salesagent['first_name'] }}" style="padding-top:5px;"><?php echo getimage('/images/activate.png') ?>
                              </a>
                           </p>
                        </td>
                     </tr>
                     <?php $i++; ?>
                     @endforeach
                     @else
                     <tr>
                        <td colspan="5" align="center" style="background:#fff">
                           No Record Found
                        </td>
                     </tr>
                     @endif
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</div> 


<div class="modal fade confirmation-model" id="logoutsalesagent">
   <div class="modal-dialog">
      <div class="modal-content">
         <form action="{{ route('logoutsalesagent')}}" method="POST">


            <input type="hidden" name="agentid" value="" id="logoutsalesagentid">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
               <h4 class="modal-title">Logout Action</h4>
            </div>

            <div class="modal-body">
               Are you sure you want to logout <strong class="agentnametologout"></strong>.
            </div>

            <div class="modal-footer pd0">
               <div class="btnintable bottom_btns pd0">
                  <div class="btn-group">
                     <button type="submit" class="btn btn-green">Confirm</button>
                     <button type="button" class="btn btn-red" data-dismiss="modal">Close</button>
                  </div>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>
<script>
   $('body').on('click', '.logout-agent', function(e) {
      $('#logoutsalesagent').modal();
      var aid = $(this).data('aid');
      var agentname = $(this).data('agentname');
      $('.agentnametologout').html(agentname);
      $('#logoutsalesagentid').val(aid);

   });
</script>
