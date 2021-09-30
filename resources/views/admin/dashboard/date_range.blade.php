    <div class="dashboard-box dashboard-date-picker" style="height:63px;" id="date-range">
    <h4 class="dash-hd-title-small">Date</h4>
        <!-- new -->
        <?php
        if(Auth::check())
                $timezone = Auth::user()->timezone;
            else
                $timezone = getClientSpecificTimeZone();
        ?>
        <table style="margin-left: auto;margin-right: auto;">
          <tr>
            <td></td>
            <td><input id="startDate" type="text" name="start_date" class="form-control  auto-submit dashboard-new-datepicker" placeholder="Start Date" autocomplete="off" value="{{ \Carbon\Carbon::now()->setTimeZone($timezone)->startOfMonth()->format('m/d/Y') }}" readonly style="margin-left: 0;"></td>
            <td><span style="padding: 0 4px;font-size: 13px;">to</span></td>
            <td><input id="endDate" type="text" name="end_date" class="form-control  auto-submit dashboard-new-datepicker" placeholder="End Date" autocomplete="off" value="{{ \Carbon\Carbon::now()->setTimeZone($timezone)->format('m/d/Y') }}" readonly style="margin-left: 0;"></td>
          </tr>
          <!-- <tr>
           
          </tr> -->
        </table>
    </div>

    @push('scripts')
    <script>
    $(document).ready(function(){
        var today = new Date();
            usaTime = today.toLocaleString("en-US", {timeZone: "{{$timezone}}"});
            today = new Date(usaTime);
        $('#endDate').datepicker(
                'setStartDate',$('#startDate').val());
            $('#endDate').datepicker(
                'setEndDate',today);
        $('#startDate').datepicker({
            format: "mm/dd/yyyy",
            autoclose: true,
            endDate: today
        }).on('change', function() {
            $('#endDate').datepicker(
                'setStartDate',$(this).val());
            $('#endDate').datepicker(
            'setEndDate',today);
            $('.datepicker').hide();
        });
        $('#endDate').datepicker({
            format: "mm/dd/yyyy",
            autoclose: true,
            endDate: today,
        }).on('change', function() {
            'setEndDate', today,
            $('.datepicker').hide();
        });
    })
    </script>
    @endpush
