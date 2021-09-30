 <!--new-design-start-Declined Lead-options---->
<div class="col-sm-12">
  <div class="declined_lead-wrapper">
    <div class="declined_lead-options">
      <div class="form-group radio-btns pdt0">
          <?php
          $declineLead = false;
          if ($queId > 0) {
            $declineLead = true;
          } else if ($queId == 0 && $identityQuestionDecline === "true") {
              $declineLead = true;
          } else {
              $declineLead = false;
          }
            ?>
        @if($declineLead)
        <h3>Declined Lead</h3>
        <p>Please choose an approprite disposition</p>

        @php
        $customer_count = $sales_agent = $lead_detail = $other = 0;
        @endphp
        @foreach($dispositions as $disposition)
          @if($disposition->disposition_group == "customer" && $disposition->type == 'decline')
            @php
            $customer_count++;
            @endphp
          @endif
          @if($disposition->disposition_group == "sales_agent" && $disposition->type == 'decline')
            @php
            $sales_agent++;
            @endphp
          @endif
          @if($disposition->disposition_group == "lead_detail" && $disposition->type == 'decline')
            @php
            $lead_detail++;
            @endphp
          @endif
          @if($disposition->disposition_group == "" && $disposition->type == 'decline') 
            @php
            $other++;
            @endphp
          @endif
        @endforeach

        @if($customer_count > 0)
        <h4>Customer</h4>
        @foreach($dispositions as $disposition)
         @if($disposition->disposition_group == "customer" && $disposition->type == 'decline')
        <label class="radio-inline">
          <input type="radio" class="disposition_radio" name="disposition_id" value="{{ $disposition->id }}">
          {{$disposition->description}}
        </label>
        @endif
        @endforeach
        @endif
        
        @if($sales_agent > 0)
        <h4>Sales Agent</h4>
        @foreach($dispositions as $disposition)
         @if($disposition->disposition_group == "sales_agent" && $disposition->type == 'decline')
        <label class="radio-inline">
          <input type="radio" class="disposition_radio" name="disposition_id" value="{{ $disposition->id }}">
          {{$disposition->description}}
        </label>
        @endif
        @endforeach
        @endif
        
        @if($lead_detail > 0)
        <h4>Lead Detail</h4>
        @foreach($dispositions as $disposition)
         @if($disposition->disposition_group == "lead_detail"  && $disposition->type == 'decline')
        <label class="radio-inline">
          <input type="radio" class="disposition_radio" name="disposition_id" value="{{ $disposition->id }}">
          {{$disposition->description}}
        </label>
        @endif
        @endforeach
        @endif

        @if($other > 0)
        <h4>Other</h4>
        @foreach($dispositions as $disposition )
         @if($disposition->disposition_group == "" && $disposition->type == 'decline')
        <label class="radio-inline">
          <input type="radio" class="disposition_radio" name="disposition_id" value="{{ $disposition->id }}">
          {{$disposition->description}}
        </label>
        @endif
        @endforeach
        @endif

        @if($customer_count > 0 || $sales_agent > 0 || $lead_detail > 0 || $other > 0)
                  <button type="button" class="btn btn-red mt15" id="final_decline" disabled="disabled">Submit</button>
              @endif

<!-- <h4>Call Disconnected</h4>
 <p>Please choose an approprite disposition</p>

 <label class="radio-inline">
   <input type="radio" name="r1">
   Bad connection
 </label>
 <label class="radio-inline">
   <input type="radio" name="r1">
   Customer hang up
 </label> -->
 @else
 <h3>Call Disconnected</h3>
 <p>Please choose an approprite disposition</p>
 @foreach($dispositions as $disposition)
  @if($disposition->type == 'customerhangup')
 <label class="radio-inline">
   <input type="radio" class="disposition_radio" name="disposition_id" value="{{ $disposition->id }}">
   {{$disposition->description}}
 </label>
 @endif
 @endforeach
 <button type="button" class="btn btn-red mt15" id="hangup_decline" disabled="disabled">Submit</button>
 @endif

</div>
</div>
</div>
</div>
<!--end-Declined Lead-options---->
