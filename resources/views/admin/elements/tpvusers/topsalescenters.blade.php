<?php error_reporting(0); ?>
<div class="cont_bx3">
   <div class="col-xs-12 col-sm-12 col-md-12">
      <div class="client-bg-white">
         <h1>Vendors</h1>

         <!-- <div class="col-xs-12 col-sm-6 col-md-6 sor_fil">
         <div class="col-xs-12 col-sm-4 col-md-4 sort">
            <div class="dropdown">
               <button class="btn dropdown-toggle" type="button" data-toggle="dropdown"><span class="sort_icon"><img src="{{ asset('images/sort.png')}}"/></span>Sort by
               <span class="drop_blk"><img src="{{ asset('images/dropicon_blk.png')}}"/></span></button>
               <ul class="dropdown-menu">
                  <li><a href="#">WTD</a></li>
                  <li><a href="#">YTD</a></li>
                  <li><a href="#">WTD</a></li>
               </ul>
            </div>
         </div>
         <div class="col-xs-12 col-sm-4 col-md-4 filter">
            <div class="dropdown">
               <button class="btn dropdown-toggle" type="button" data-toggle="dropdown"><span class="sort_icon"><img src="{{ asset('images/filter.png')}}"/></span>Filters
               <span class="drop_blk"><img src="{{ asset('images/dropicon_blk.png')}}"/></span></button>
               <ul class="dropdown-menu">
                  <li><a href="#">WTD</a></li>
                  <li><a href="#">YTD</a></li>
                  <li><a href="#">WTD</a></li>
               </ul>
            </div>
         </div>
         <div class="col-xs-12 col-sm-4 col-md-4 search">
            <div class="search-container">
               <form action="/action_page.php">
                  <button type="submit"><img src="{{ asset('images/search.png')}}"/></button>
                  <input type="text" placeholder="Search" name="search">
               </form>
            </div>
         </div>
      </div> -->
         <div class="mt30 sales_tablebx">
            <div class="table-responsive">
               <table class="table">
                  <thead>
                     <tr class="acjin">
                        <th>Sale Center Name</th>
                        <th>Client Name</th>
                        <th>Active users</th>
                        <th>Verified/Non-Verified Leads</th>
                        <th>Verified %</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php
                     $salescenters_verified_count = 0;
                     $salescenters_decline_count = 0;
                     ?>
                     @if(count($top_salescenters) > 0)
                     <?php $i = 1; ?>
                     @foreach($top_salescenters as $salescenters)
                     <?php $total_verified_decline_of_single_salescenter = $salescenters['total_verified'] + $salescenters['total_decline'];
                     if ($i % 2 == 0) {
                        $first_last_td_class = "dark_c";
                        $second_and_middle_td_class = "grey_c";
                     } else {
                        $first_last_td_class = "light_c";
                        $second_and_middle_td_class = "white_c";
                     }
                     ?>
                     <tr>
                        <td class="{{$first_last_td_class}}">{{$salescenters['salescenter_name']}}</td>
                        <td class="{{$second_and_middle_td_class}}">{{$salescenters['client_name']}}</td>
                        <td class="{{$second_and_middle_td_class}}">{{$salescenters['active_users']}}</td>
                        <td class="{{$second_and_middle_td_class}}">
                           <?php $verified_progress =  number_format(($salescenters['total_verified'] / $total_verified_decline_of_single_salescenter) * 100); ?>
                           <?php $decline_progress =  number_format(($salescenters['total_decline'] / $total_verified_decline_of_single_salescenter) * 100);
                           if ($decline_progress == 0) $decline_progress = 2;
                           if ($verified_progress == 0) $verified_progress = 2;
                           ?>
                           <div class="table-horizontal-graph">
                              <div class="verified graph-bars" style="width:{{$verified_progress}}%"></div>
                              <span class="verified-count">{{$salescenters['total_verified']}}</span>
                              <div class="clearfix"></div>
                              <div class="declined graph-bars" style="width:{{$decline_progress}}%"></div>
                              <span class="decline-count">{{$salescenters['total_decline']}}</span>
                           </div>
                           <?php $salescenters_verified_count = $salescenters_verified_count +  $salescenters['total_verified']; ?>
                           <?php $salescenters_decline_count = $salescenters_decline_count +  $salescenters['total_decline']; ?>
                        </td>
                        <td class="{{$first_last_td_class}}">{{ number_format($salescenters['total_verified']/($salescenters['total_decline']+$salescenters['total_verified'])*100, 2)}}%</td>
                     </tr>
                     <?php $i++; ?>
                     @endforeach
                     @else
                     <tr>
                        <td colspan="5" align="center">
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