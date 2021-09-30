<?php error_reporting(0); ?>



<div class="cont_bx3 cont_bx5">

  <div class="col-xs-12 col-sm-12 col-md-12">
    <div class="client-bg-white">
      <h1>Leading Agents</h1>

      <!-- <div class="col-xs-12 col-sm-6 col-md-6 sor_fil">
					<div class="col-xs-12 col-sm-4 col-md-4 sort">
						<div class="dropdown">
									<button class="btn dropdown-toggle" type="button" data-toggle="dropdown"><span class="sort_icon"><img src="{{asset('images/sort.png')}}"/></span>Sort by
									<span class="drop_blk"><img src="{{asset('images/dropicon_blk.png')}}"/></span></button>
									<ul class="dropdown-menu">
									  <li><a href="#">WTD</a></li>
									  <li><a href="#">YTD</a></li>
									  <li><a href="#">WTD</a></li>
									</ul>
						</div>
					</div>
					<div class="col-xs-12 col-sm-4 col-md-4 filter">
						<div class="dropdown">
									<button class="btn dropdown-toggle" type="button" data-toggle="dropdown"><span class="sort_icon"><img src="{{asset('images/filter.png')}}"/></span>Filters
									<span class="drop_blk"><img src="{{asset('images/dropicon_blk.png')}}"/></span></button>
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
							   <button type="submit"><img src="{{asset('images/search.png')}}"/></button>
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
                <th>ID</th>
                <th>Name</th>
                <th>Client Name</th>
                <th>Sale Center Name</th>
                <th>Verified/Non-Verified Leads</th>
                <th>Verified %</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $agent_verified_count = 0;
              $agent_decline_count = 0;
              ?>
              @if(count($top_agents) > 0)
              <?php $i = 1; ?>
              @foreach($top_agents as $agent)
              <?php $total_verified_decline_of_single_user = $agent->total_decline + $agent->total_verified; ?>
              <?php $agent_verified_count = $agent_verified_count +  $agent->total_verified; ?>
              <?php $agent_decline_count = $agent_decline_count + $agent->total_decline; ?>
              <?php $decline_progress =  number_format(($agent->total_decline / $total_verified_decline_of_single_user) * 100); ?>
              <?php $verified_progress =  number_format(($agent->total_verified / $total_verified_decline_of_single_user) * 100);
              if ($decline_progress == 0) $decline_progress = 2;
              if ($verified_progress == 0) $verified_progress = 2;
              if ($i % 2 == 0) {
                $first_last_td_class = "dark_c";
                $second_and_middle_td_class = "grey_c";
              } else {
                $first_last_td_class = "light_c";
                $second_and_middle_td_class = "white_c";
              }
              ?>
              <tr>
                <td class="{{$first_last_td_class}}">{{$agent->userid}}</td>
                <td class="{{$second_and_middle_td_class}}">{{$agent->first_name}} {{$agent->last_name}}</td>

                <td class="{{$second_and_middle_td_class}}">{{$agent->client_name}}</td>
                <td class="{{$second_and_middle_td_class}}">{{$agent->salescenter_name}}</td>
                <td class="{{$second_and_middle_td_class}}">

                  <div class="table-horizontal-graph">
                    <div class="verified graph-bars" style="width:{{$verified_progress}}%"></div>
                    <span class="verified-count">{{$agent->total_verified}}</span>
                    <div class="clearfix"></div>
                    <div class="declined graph-bars" style="width:{{$decline_progress}}%"></div>
                    <span class="decline-count">{{$agent->total_decline}}</span>
                  </div>


                </td>
                <td class="{{$first_last_td_class}}">
                  <?php
                  $total_percent =  number_format($agent->total_verified / ($total_verified_decline_of_single_user) * 100, 2);


                  // die();
                  ?>

                  {{ ( $total_percent == 'nan'  )? "0" : $total_percent  }}%</td>
              </tr>
              <?php $i++; ?>
              @endforeach
              @else
              <tr>
                <td colspan="6" align="center">
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