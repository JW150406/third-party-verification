@php 
    $badSaleTotal = 0;
    $goodSaleTotal = 0;
    $pendingTotal = 0;
    $cancelTotal = 0;
    $total = 0;
@endphp
@forelse($data as $d => $v)
<tr>
   
    <td class="dashboard-table-color">{{$v['name']}}</td>
    <td>{{$v[config()->get('constants.DASHBOARD_LEAD_CATEGORIES_REVERSE.Bad Sale')]}}</td>
    
    <td>{{$v[config()->get('constants.DASHBOARD_LEAD_CATEGORIES_REVERSE.Cancelled Leads')]}}</td>
    <td>{{$v[config()->get('constants.DASHBOARD_LEAD_CATEGORIES_REVERSE.Good Sale')]}}</td>
    <td>{{$v[config()->get('constants.DASHBOARD_LEAD_CATEGORIES_REVERSE.Pending Leads')]}}</td>
    <td>{{$v['total_leads']}}</td>
    @php 
        $badSaleTotal += $v[config()->get('constants.DASHBOARD_LEAD_CATEGORIES_REVERSE.Bad Sale')];
        $goodSaleTotal += $v[config()->get('constants.DASHBOARD_LEAD_CATEGORIES_REVERSE.Good Sale')];
        $pendingTotal += $v[config()->get('constants.DASHBOARD_LEAD_CATEGORIES_REVERSE.Pending Leads')];
        $cancelTotal += $v[config()->get('constants.DASHBOARD_LEAD_CATEGORIES_REVERSE.Cancelled Leads')];
        $total += $v['total_leads'];
    @endphp
</tr>
@empty
<tr><td colspan='6' class="text-center"> No record found</td></tr>
@endforelse
@if(isset($data) && count($data) > 0)
<tr>
    <td class="dashboard-table-color">Total</td>
    <td>{{$badSaleTotal}}</td>
    <td>{{$cancelTotal}}</td>
    <td>{{$goodSaleTotal}}</td>
    <td>{{$pendingTotal}}</td>
    <td>{{$total}}</td>
</tr>
@endif
