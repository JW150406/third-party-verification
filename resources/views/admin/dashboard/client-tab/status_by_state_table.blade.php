@if(count($overallSalesData) > 0)

@forelse($overallSalesData as $key => $sale)
    @php
        $goodSalesCount = 0;
        $conversionRateCount = 0;
        if(isset($goodSalesData[$key])){
            $goodSalesCount = $goodSalesData[$key];
        }
         if(isset($conversionRate[$key])){
            $conversionRateCount = $conversionRate[$key];
        }
    @endphp
    <tr><td style='color: #3A58A8; text-align: center'>{{ $key}}</td>
       <td style='color: #3A58A8; text-align: center;'>{{ $sale }}</td>
       <td style='color: #3A58A8; text-align: center;'>{{ $goodSalesCount }}</td>
       <td style='color:#3A58A8; text-align:center;'>{{ number_format($conversionRateCount, 2,'.','') }}%</td>
    </tr>
@empty

@endforelse
        
    <!-- <tr>
        <td style='background-color:#3A58A8;color: white; text-align: center;position:sticky;bottom:0;left:0;'>Total</td>
        <td style='background-color: #3A58A8;color: white; text-align: center;position:sticky;bottom:0;left:0;'>{{ $overallSalesTotal }}</td>
        <td style='background-color: #3A58A8;color: white; text-align: center;position:sticky;bottom:0;left:0;'>{{ $goodSalesTotal }}</td>
        <td style='background-color:#3A58A8;color: white; text-align:center;position:sticky;bottom:0;left:0;'>{{ number_format($conversionRateTotal, 2,'.','')}}%</td>
    </tr> -->
    
@endif