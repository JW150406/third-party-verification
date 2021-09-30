<div class="col-sm-12">
  <div class="declined_lead-wrapper">
    <div class="declined_lead-options">
      <div class="form-group radio-btns pdt0">
        <p><strong>Please choose an approprite call declined disposition:</strong></p>
        @php
        $customer_count = $sales_agent = $lead_detail = $other = 0;
        @endphp
        @foreach($dispositions as $disposition)
          @if($disposition->disposition_group == "customer")
            @php
            $customer_count++;
            @endphp
          @endif
          @if($disposition->disposition_group == "sales_agent")
            @php
            $sales_agent++;
            @endphp
          @endif
          @if($disposition->disposition_group == "lead_detail")
            @php
            $lead_detail++;
            @endphp
          @endif
          @if($disposition->disposition_group == "other")
            @php
            $other++;
            @endphp
          @endif
        @endforeach

        @if($customer_count > 0)
        <h4>Customer</h4>
        @foreach($dispositions as $disposition)
         @if($disposition->disposition_group == "customer")
        <label class="radio-inline radio-outer">
          <input type="radio" class="disposition_radio" name="disposition_id" value="{{ $disposition->id }}" style="top: -7px;">
          {{$disposition->description}}
        </label>
        @endif
        @endforeach
        @endif

        @if($sales_agent > 0)
        <h4>Sales Agent</h4>
        @foreach($dispositions as $disposition)
         @if($disposition->disposition_group == "sales_agent")
        <label class="radio-inline radio-outer">
          <input type="radio" class="disposition_radio" name="disposition_id" value="{{ $disposition->id }}" style="top: -7px;">
          {{$disposition->description}}
        </label>
        @endif
        @endforeach
        @endif

        @if($lead_detail > 0)
        <h4>Lead Detail</h4>
        @foreach($dispositions as $disposition)
         @if($disposition->disposition_group == "lead_detail" )
        <label class="radio-inline radio-outer">
          <input type="radio" class="disposition_radio" name="disposition_id" value="{{ $disposition->id }}" style="top: -7px;">
          {{$disposition->description}}
        </label>
        @endif
        @endforeach
        @endif

        @if($other > 0)
        <h4>Other</h4>
        @foreach($dispositions as $disposition )
         @if($disposition->disposition_group == "other" )
        <label class="radio-inline radio-outer">
          <input type="radio" class="disposition_radio" name="disposition_id" value="{{ $disposition->id }}" style="top: -7px;">
          {{$disposition->description}}
        </label>
        @endif
        @endforeach
        @endif

        @if($customer_count > 0 || $sales_agent > 0 || $lead_detail > 0 || $other > 0)
          <button type="button" class="btn btn-red mt15" id="final_decline" disabled="disabled">Submit</button>
        @else
          <p>No dispositions found</p>
        @endif
      </div>
    </div>
  </div>
</div>
