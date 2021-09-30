<div class="dashboard-box">
      <div class="table-responsive">
          <table class="table" id="conversion-rate-table">
              <tbody class="conversion-rate-report">
              </tbody>
          </table>
      </div>
  </div>

@push('scripts')
<script>

// Old Function which returns green and red two colours
// function getColorForLeadText(currentPer, lastPer) {
//   switch (true) {
//      case currentPer > lastPer:
//         return "font-green";
//         break;

//      case currentPer < lastPer:
//         return "font-red";
//         break;

//      case currentPer == lastPer:
//         return "font-yellow";
//         break;
//      default:
//       return "font-black"
//       break;
//   }
// }

// New Function which returns only green colours 
function getColorForLeadText(currentPer, lastPer) {
    return "font-green";
}

function loadConversionRateData(data)
{
    $.ajax({
        url: '{{route("dashboard.conversion-rate")}}',
        method:'post',
        data:data,
        success:function(data)
        {
            table = "";
            if(data.status == 'success')
            {
                leads = data.data;
                 table += "<tr><td> <div class='conv-rate'>Conversion Rate</div></td>";

                 let todayColor = getColorForLeadText(leads['todayLeads']['percentage'], leads['yesterdayLeads']['percentage']);
                 let weekColor = getColorForLeadText(leads['thisWeekLeads']['percentage'], leads['lastWeekLeads']['percentage']);
                 let monthColor = getColorForLeadText(leads['thisMonthLeads']['percentage'], leads['lastMonthLeads']['percentage']);
                 let yearColor = getColorForLeadText(leads['thisYearLeads']['percentage'], leads['lastYearLeads']['percentage']);

                 table += "<td><div class='box-rate-leads'><p class='titles'>Today</p><span class='font-conversion-rate "+todayColor+"'>" + leads['todayLeads']['percentage'] + "% </span><p class='percentages'>Yesterday: "+leads['yesterdayLeads']['percentage']+"% </p></div></td>";
                 table += "<td><div class='box-rate-leads'><p class='titles'>This Week</p><span class='font-conversion-rate "+weekColor+"'>" + leads['thisWeekLeads']['percentage'] + "%</span><p class='percentages'>Last Week: "+leads['lastWeekLeads']['percentage']+"% </p></div></td>";
                 table += "<td><div class='box-rate-leads'><p class='titles'>This Month</p><span class='font-conversion-rate "+monthColor+"'>" + leads['thisMonthLeads']['percentage'] + "%</span><p class='percentages'>Last Month: "+leads['lastMonthLeads']['percentage']+"% </p></div></td>";
                 table += "<td><div class='box-rate-leads'><p class='titles'>This Year</p><span class='font-conversion-rate "+yearColor+"'>" + leads['thisYearLeads']['percentage'] + "%</span><p class='percentages'>Last Year: "+leads['lastYearLeads']['percentage']+"% </p></div></td></tr>";

                 let todayLeadColor = getColorForLeadText(leads['todayLeads']['count'], leads['yesterdayLeads']['count']);
                 let weekLeadColor = getColorForLeadText(leads['thisWeekLeads']['count'], leads['lastWeekLeads']['count']);
                 let monthLeadColor = getColorForLeadText(leads['thisMonthLeads']['count'], leads['lastMonthLeads']['count']);
                 let yearLeadColor = getColorForLeadText(leads['thisYearLeads']['count'], leads['lastYearLeads']['count']);

                 table += "<tr><td><div class='leads'> Leads</div> </td>";
                 table += "<td><div class='box-rate-leads'><p class='titles'>Today</p><span class='font-conversion-rate "+todayLeadColor+"'>" + leads['todayLeads']['count'] + " </span><p class='percentages'>Yesterday: "+leads['yesterdayLeads']['count']+"</p></div></td>";
                 table += "<td><div class='box-rate-leads'><p class='titles'>This Week</p><span class='font-conversion-rate "+weekLeadColor+"'>" + leads['thisWeekLeads']['count'] + "</span> <p class='percentages'>Last Week: "+leads['lastWeekLeads']['count']+"</p></div></td>";
                 table += "<td><div class='box-rate-leads'><p class='titles'>This Month</p><span class='font-conversion-rate "+monthLeadColor+"'>" + leads['thisMonthLeads']['count'] + "</span><p class='percentages'>Last Month: "+leads['lastMonthLeads']['count']+"</p></div></td>";
                 table += "<td><div class='box-rate-leads'><p class='titles'>This Year</p><span class='font-conversion-rate "+yearLeadColor+"'>" + leads['thisYearLeads']['count'] + "</span><p class='percentages'>Last Year: "+leads['lastYearLeads']['count']+"</p></div></td></tr>";

                 $('.conversion-rate-report').html(table);
            }
        }
    });
}
</script>
@endpush
