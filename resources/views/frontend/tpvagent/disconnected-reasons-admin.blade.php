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
        <h4 style="margin-top: 12px;margin-bottom: 12px;text-decoration: underline;">Customer</h4>
        @foreach($dispositions as $disposition)
         @if($disposition->disposition_group == "customer")
        <label class="radio-inline radio-outer" style="line-height: 0;display: block;margin:19px 0 0 15px;">
          <input type="radio" class="disposition_radio" name="disposition_id" value="{{ $disposition->id }}" style="top: -7px;">
          {{$disposition->description}}
        </label>
        @endif
        @endforeach
        @endif

        @if($sales_agent > 0)
        <h4 style="margin-top: 12px;margin-bottom: 12px;text-decoration: underline;">Sales Agent</h4>
        @foreach($dispositions as $disposition)
         @if($disposition->disposition_group == "sales_agent")
            <label class="radio-inline radio-outer" style="line-height: 0;display: block;margin:19px 0 0 15px;">
            <input type="radio" class="disposition_radio" name="disposition_id" value="{{ $disposition->id }}" style="top: -7px;">
            {{$disposition->description}}
            </label>
        @endif
        @endforeach
        @endif

        @if($lead_detail > 0)
        <h4 style="margin-top: 12px;margin-bottom: 12px;text-decoration: underline;">Lead Detail</h4>
        @foreach($dispositions as $disposition)
         @if($disposition->disposition_group == "lead_detail" )
            <label class="radio-inline radio-outer" style="line-height: 0;display: block;margin:19px 0 0 15px;">
            <input type="radio" class="disposition_radio" name="disposition_id" value="{{ $disposition->id }}" style="top: -7px;">
            {{$disposition->description}}
            </label>
        
        @endif
        @endforeach
        @endif

        @if($other > 0)
        <h4 style="margin-top: 12px;margin-bottom: 12px;text-decoration: underline;">Other</h4>
        @foreach($dispositions as $disposition )
         @if($disposition->disposition_group == "other" )
        
            <label class="radio-inline radio-outer" style="line-height: 0;display: block;margin:19px 0 0 15px;">
            <input type="radio" class="disposition_radio" name="disposition_id" value="{{ $disposition->id }}" style="top: -7px;">
            {{$disposition->description}}
            </label>
        
        @endif
        @endforeach
        @endif

      </div>
    </div>
  </div>
</div>
