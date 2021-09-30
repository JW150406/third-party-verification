
@if(count($ComplianceReportdata)>0)
<?php
$quert_string = "?";
  foreach ($request as $key => $value) {
    $quert_string .="$key=$value&";
  }
?>
<div class="add-newlink">
   <a class="btn btn-success btn-icon icon-left" href="{{ route('client.compliance-reports-export',['client_id' =>$request['client'] ]).$quert_string  }}"> <i class="fa fa-download"></i> Export</a>

</div>
<br/>
<table class="table table-striped table-responsive report-result">
  <?php $j = 0;?>
  @foreach($ComplianceReportdata as $headervalue )

    <tr>
      @foreach($headervalue as $heading => $val)
        @if($j == 0)
          <th>{{$heading}}</th>
        @else
          <td>{{$val}}</td>
        @endif
      @endforeach
    </tr>
   <?php $j++;?>
@endforeach
</table>
  {!! $ComplianceReportdata->appends($request)->links()!!}
@else
<div class="text-center"><h2>No Record Found</h2></div>
@endif
