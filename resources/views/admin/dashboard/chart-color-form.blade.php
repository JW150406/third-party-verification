<!-- Add tpv user Modal Starts -->


<div class="team-addnewmodal v-star">
	<div class="modal fade" id="colorsModal" tabindex="-1" role="dialog" aria-labelledby="TpvUserModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content" style="width: 426px;">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Change Theme</h4>
				</div>
				<div class="modal-body">
					<div class="modal-form row">
						<div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="text-center">
                                <a href="javascript:void(0)" style="font-size: 12px;padding-right:  10px;font-weight:600;"class="preset-theme" id="reset">Default</a>
                                <a href="javascript:void(0)" style="padding-right: 10px;font-size: 12px;font-weight:600;" class="preset-theme" id="theme-1">Theme 1</a>
                                <a href="javascript:void(0)" style="padding-right: 10px;font-size: 12px;font-weight:600;"class="preset-theme" id="theme-2">Theme 2</a>
                                <a href="javascript:void(0)" style="padding-right: 10px;font-size: 12px;font-weight:600;"class="preset-theme" id="theme-3">Theme 3</a>
                                <a href="javascript:void(0)" style="padding-right: 10px;font-size: 12px;font-weight:600;"class="preset-theme" id="theme-4">Theme 4</a>
                                <a href="javascript:void(0)" style="padding-right: 10px;font-size: 12px;font-weight:600;"class="preset-theme" id="theme-5">Theme 5</a>
                                
                                <a href="javascript:void(0)" style="font-size: 12px;font-weight:600;"class="preset-theme theme-color" id="custom">Custom</a>
                            </div>
                            <?php 
                                $colors = explode(',',$colors);//colorArray(); 
                                if(count($colors) == 0)
                                {
                                    $colors = explode(',',implode(',',colorArray()));
                                }
                                $calenderColors = explode(',',$calenderColors);//colorArray(); 
                                if(count($calenderColors) == 0)
                                {
                                    $calenderColors = explode(',',implode(',',calenderColorArray()));
                                }
                                $i = 1;
                            ?>
                            <form class="chart-color-form" method="GET" action="{{ route('dashboard') }}" enctype="multipart/form-data" >
                            <label for="general_colors" style="margin-top:15px;">General Colors</label>
                                <input type="hidden" name="cid" value = "{{base64_encode($cId)}}">
                                <input type="hidden" name="colors" value = "{{implode(',',$colors)}}">
                                <input type="hidden" name="type" value = "{{base64_encode($type)}}">
                                <input type="hidden" name="sid" value = "{{base64_encode($sId)}}">
                                <input type="hidden" name="calenderColors" value = "{{implode(',',$calenderColors)}}">
                                <table style="padding:0 20px;">
                                    @foreach($colors as $k => $col)
                                    <?php 
                                        $toolTip = "This Color is used for  ";
                                        switch($k)
                                        {
                                            case 0:
                                                $toolTip .= "Good sale and Top 5 performer";
                                            break;
                                            case 1:
                                                $toolTip .= "Pending Leads";
                                            break;
                                            case 2:
                                                $toolTip .= "Bad sale";
                                            break;
                                            case 3:
                                                $toolTip .= "Cancelled Leads";
                                            break;
                                            case 4:
                                                $toolTip .= "Bar charts and donut chart";
                                            break;
                                            case 5:
                                                $toolTip .= "Bar charts and Bottom 5 performer";
                                            break;
                                            case 6:
                                                $toolTip .= "Bar charts and donut charts";
                                            break;
                                            case 7:
                                                $toolTip .= "Bar charts and donut charts";
                                            break;
                                        }
                                    ?>
                                    <tr><td style="width:120px; padding-left:30px;"><a>Color {{$i}}</a><span data-toggle="tooltip" data-placement="right" data-container="#colorsModal" data-original-title="{{$toolTip}}"><img height='12' width="12"src="{{asset('images/tooltip-hover-i.png')}}" alt="" style="margin-left:10px;"></span></td><td>
                                    <div class="form-group" style="width:150px;">
                                        <input id="generalcolor-{{$i++}}" autocomplete="off" type="color" class="form-control" name="color[]" placeholder="Color" value="{{$col}}">
                                    </div></td></tr>
                                    @endforeach
                                </table>
                                <label for="general_colors" style="margin-top:10px;">Calender Colors</label>
                                <table style="padding:0 20px;">
                                @php $j= 1; @endphp
                                    @foreach($calenderColors as $k => $col)
                                    <?php 
                                        $toolTip = "This Color is used for calender ";
                                        switch($k)
                                        {
                                            case 0:
                                                $toolTip .= "Good sale";
                                            break;
                                            case 1:
                                                $toolTip .= "Pending Leads";
                                            break;
                                            case 2:
                                                $toolTip .= "Bad sale";
                                            break;
                                            case 3:
                                                $toolTip .= "Cancelled Leads";
                                            break;
                                        }
                                    ?>
                                    <tr><td style="width:120px; padding-left:30px;"><a>Color {{$j}}</a><span data-toggle="tooltip" data-placement="right" data-container="#colorsModal" data-original-title="{{$toolTip}}"><img  height='12' width="12" style="margin-left:10px;" src="{{asset('images/tooltip-hover-i.png')}}" alt=""></span></td><td>
                                    <div class="form-group" style="width:150px;">
                                        <input id="calendercolor-{{$j++}}" autocomplete="off" type="color" class="form-control" name="calenderColor[]" placeholder="Color" value="{{$col}}">
                                    </div></td></tr>
                                    @endforeach
                                </table>
                                <div class="col-xs-12 col-sm-12 col-md-12 modalbtns">
                                    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                        <div class="btn-group">
                                            <button type="submit" class="btn btn-green" id="btn_save"><span class="save-text">Submit</span></button>
                                            <button type="button" class="btn btn-red cancel-btn" data-dismiss="modal">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@push('scripts')

<script>
function returnThemeColor(theme, type)
{
    if(type == 'general')
    {
        if(theme == 1)
        {
            return ['#114B5F','#1A936F','#88D498','#C6DABF','#F3E9D2','#25a5d0','#baf3e2','#D2947D'];
        }
        if(theme == 2)
        {
            return ['#264653','#2A9D8F','#E9C46A','#F4A261','#E76F51','#ee9781','#f3b5a5','#fae1db'];
        }
        if(theme == 3)
        {
            return ['#001D4A','#27476E','#006992','#EAF8BF','#ECA400','#ffb60a','#fff570','#ccbe00'];
        }
        if(theme == 4)
        {
            return ['#16697A','#489FB5','#82C0CC','#FFA62B','#EDE7E3','#8b775b','#f0e797','#1ea6a2'];
        }
        if(theme == 5)
        {
            return ['#3D405B','#81B29A','#F2CC8F','#E07A5F','#F4F1DE','#bd8732','#AAB584','#E5DCAC'];
        }
    }
    if(type == 'calender')
    {
        if(theme == 1)
        {
            return ['#5e2f27','#b97569','#e1c7c2','#6F6360'];
        }
        if(theme == 2)
        {
            return ['#a4d7d3','#318a66','#878787','#c8ca68'];
        }
        if(theme == 3)
        {
            return ['#546071','#6ba29b','#444E26','#AAAA6C'];
        }
        if(theme == 4)
        {
            return ['#234341','7E94C9','#BFBFBF','#A4C4DF'];
        }
        if(theme == 5)
        {
            return ['#515C28','#bd8732','#878787','#CBCC99'];
        }
    }
}
    $('.open-color-pallate').click(function(){
        $('#colorsModal').modal();
    })
    $('.preset-theme').click(function(){
        $('.preset-theme').removeClass('theme-color');
        $(this).addClass('theme-color');
        ri = 1;
        ci = 1;
        
        if($(this).attr('id') == 'reset')
        {
            <?php 
                $generalColors = colorArray();
                $calenderColor = calenderColorArray();
            ?>
            @foreach($generalColors as $col)
                $('#generalcolor-'+ri++).val('{{$col}}');
            @endforeach
            @foreach($calenderColor as $col)
                $('#calendercolor-'+ci++).val('{{$col}}');
            @endforeach
        }
        else if($(this).attr('id') == 'custom')
        {
            @foreach($colors as $col)
                $('#generalcolor-'+ri++).val('{{$col}}');
            @endforeach
            @foreach($calenderColors as $col)
                $('#calendercolor-'+ci++).val('{{$col}}');
            @endforeach
        }
        else
        {
            colors = returnThemeColor($(this).attr('id').split('-')[1],'general');

            for(i=1; i <= colors.length; i++)
            {
                $('#generalcolor-'+i).val(colors[i-1]);
            }
            calenderColors = returnThemeColor($(this).attr('id').split('-')[1],'calender');
            console.log(calenderColors);
            for(j=1 ; j <= calenderColors.length; j++)
            {
                $('#calendercolor-'+j).val(calenderColors[j-1]);
            }
        }
        
    })
</script>
@endpush