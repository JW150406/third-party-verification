<div class="utility-outer">
<p class="utility-sub-t" id="commodity-name"><strong> {{ $program->utility->commodity ?? ''}} Enrollment </strong></p>
<p class="utility-sub-t" id="customer-type"><strong>{{ $program->customer_type ?? ''}}</strong></p>
    <p class="utility-sub-t" id="program-name"><strong style="color:#bab9b9;">{{ $program->customer_type ?? ''}}</strong></p>
    <div class="residential-table">
        <div class="row">
            <div class="col-md-3 col-sm-3 br2 border-right">
                <p>Code</p><span id="program-code">{{ $program->code ?? ''}}</span>
            </div>
            <div class="col-md-3 col-sm-3">
                <p>Rate</p> <span id="program-rate">${{ $program->rate ?? ''}} per {{ $program->unit_of_measure ?? ''}}  </span>
            </div>
            <div class="col-md-2 col-sm-2">
                <p>Term</p><span id="program-term">{{ $program->term ?? ''}}</span>
            </div>
            <div class="col-md-2 col-sm-2">
                <p>MSF</p><span id="program-msf">${{ $program->msf ?? ''}}</span>
            </div>
            <div class="col-md-2 col-sm-2">
                <p>ETF</p><span id="program-etf">${{ $program->etf ?? ''}}</span>
            </div>
        </div>
        @if(!empty($customFields))
        <br>
        <div class="row" style="border-top: 1px solid #20497C;padding-top: 10px;">
            @foreach($customFields as $key => $fields)
                <div class="row" style="margin: 0px;">
                    <div class="col-md-3 col-sm-3">
                        <p>{{$fields}} :</p>
                    </div>
                    <div class="col-md-9 col-sm-9">
                        <span>{{array_get($program,$key)}}</span>
                    </div>
                </div>
            @endforeach
        </div>
        @endif
    </div>
</div>