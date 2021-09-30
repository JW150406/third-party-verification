         <br/>
         <h3>Top Clients</h3>
         <table class="table table-striped responsive">
         <thead>
          <tr>         
           <th>Client Name</th>
           <th>Active users</th>
           <th>Verified Accounts </th>
           <th>Non-Verified Accounts</th>
           <th>Verified %</th>
           </tr>
           </thead>
           <tbody>
           <?php 
              $client_verified_count = 0;
              $client_decline_count = 0; 
           ?>
           @if(count($top_clients) > 0)
           @foreach($top_clients as $singleclient)
           <tr>
             <td>{{$singleclient['client_name']}}</td>
             <td>{{$singleclient['active_users']}}</td>             
             <td class="alert-success">{{$singleclient['total_verified']}} <?php $client_verified_count = $client_verified_count +  $singleclient['total_verified']; ?></td>
             <td class="alert-danger">{{$singleclient['total_decline']}} <?php $client_decline_count = $client_decline_count +  $singleclient['total_decline']; ?></td>
             <td>{{ number_format($singleclient['total_verified']/($singleclient['total_decline']+$singleclient['total_verified'])*100, 2)}}%</td>
             </tr>
            
           @endforeach
           <tr>
           <th></th>
           <th></th>
           <th class="alert-success">{{$client_verified_count}}</th>
           <th class="alert-danger">{{$client_decline_count}}</th>
           <th>{{ number_format($client_verified_count/($client_verified_count+$client_decline_count)*100, 2)}}%</th>
           </tr>
           @else
          <tr>
            <td colspan="5" align="center">
              No Record Found
            </td>
          </tr>
          @endif
          </tbody>
         </table>  
         <br> 
     
         @endif