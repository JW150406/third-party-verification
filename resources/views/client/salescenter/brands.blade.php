<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="cont_bx3">
            <div class="tpvbtn">
                <div class="mt20">
                    <div class="col-md-6 col-sm-6">
                        <h4>Associated Brands</h4>
                    </div>
                </div>                
                <div class="col-md-12">
                    <div class="cont_bx3 mt30 sor_fil scroller">
                        <form id="brand-program-form">
                        <?php $salescentersId = array_column($salesCenterBrands,'brand_id'); ?>
                        @forelse($brands as $key => $val)
                        <div class="row" style="min-height: 75px;">
                            <div class="col-xs-4 col-sm-4 col-md-4">
                                <div class="form-group">
                                    <label class="checkbx-style"> {{ $val->name }}
                                        <input autocomplete="off" type="checkbox" name="brands" value="{{ $val->id }}" class="salescenter_brands" @if(in_array($val->id, $salescentersId)) checked @endif @if(!Auth::user()->can('edit-brand-info')) disabled @endif>
                                        <span class="checkmark" @if(!Auth::user()->can('edit-brand-info')) style="cursor:not-allowed; background:#ddd; border:#ddd;" @endif></span>
                                    </label>
                                </div>
                            </div>
                            @if(Auth::user()->can('edit-brand-info'))
                            <div class="col-xs-3 col-sm-3 col-md-3 brand-program-section @if(!in_array($val->id, $salescentersId)) hidee @endif">
                                <div class="form-group">
                                    <label for="brand-program-{{$val->id}}">Programs</label>
                                    <select class="form-control brand-programs" id="brand-program-{{$val->id}}" name="brand_programs[{{$val->id}}][]" data-parsley-required='true'  multiple="multiple" data-parsley-errors-container="#brand-program-error-{{$val->id}}" @if(!Auth::user()->can('edit-brand-info')) disabled @endif>
                                        
                                        @if(isset($brandPrograms[$val->id]))
                                            @php 
                                            $restrictedProgramIds = [];
                                            if(isset($restrictedPrograms[$val->id]) && isset($restrictedPrograms[$val->id][0]->restrictProg)) {
                                                $restrictedProgramIds = $restrictedPrograms[$val->id][0]->restrictProg->pluck('program_id')->toArray();
                                            }
                                            @endphp
                                            @foreach($brandPrograms[$val->id] as $brandProgram)
                                                <option value="{{$brandProgram->id}}" @if(in_array($brandProgram->id, $restrictedProgramIds)) selected @endif>{{$brandProgram->name.' ('.$brandProgram->code.')'}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div id="brand-program-error-{{$val->id}}"></div>
                                </div>
                            </div>
                            @endif
                        </div>
                        </form>
                        @empty
                        <h5>No Brands Found</h5>
                        @endforelse
                        
                    </div>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 bottom_btns">
                    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                        <div class="btn-group mt30 mb30">
                        @if($brands->count() > 0)
                        <button type="button" id = "save-salescenter-brands" class="btn btn-green" @if(!Auth::user()->can('edit-brand-info')) disabled @endif>Save</button>
                        @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>

$(document).ready(function(){
    $('.brand-programs').multiselect({placeholder: 'All'})    

    // $('.brand-programs').attr('disabled',true);
    $('.ms-options label').append('<span class="checkmark" style="left:10px;top:10px"></span>');
    $('.ms-options label').addClass('custom-checkbox').css('cssText','margin-bottom: 0px !important;');

    var uncheckBrands = [];
    var checkedBrands = [];
    $(document).on('change','.salescenter_brands',function(){
        if($(this).prop('checked'))
        {
            checkedBrands.push($(this).val());
            
            if(uncheckBrands.includes($(this).val()) == true)
            {
                var index = uncheckBrands.indexOf($(this).val());
                if (index > -1) {
                    uncheckBrands.splice(index, 1);
                }
            }
            $(this).closest('.row').find('.brand-program-section').show();
        }
        else
        {    
            if(uncheckBrands.includes($(this).val()) == false)
            {
                uncheckBrands.push($(this).val());
            }
            $(this).closest('.row').find('.brand-program-section').hide();
        }
        console.log(uncheckBrands);
    });
    $(document).on('click','#save-salescenter-brands',function(){

        
        console.log(uncheckBrands);
        var checkboxArr = [];
        let programs = {};
        let brandId = 0;
        $("input:checkbox[name=brands]:checked").each(function(){
            checkboxArr.push($(this).val());

            brandId = $(this).val();
            programs[brandId] = $("#brand-program-"+brandId).val();
        });

        console.log(programs);

        $.ajax({
            url:'{{route("client.salescenters.brands")}}',
            method:'post',
            data:{'_token':'{{csrf_token()}}','clientId':'{{$client->id}}','salescenterId':'{{$salescenter->id}}','brands':checkboxArr,'uncheckBrands':uncheckBrands, 'programs' : programs},
            success:function(data){
                $('html, body').animate({
                    scrollTop: $(".container").offset().top
                }, 400);
                printAjaxSuccessMsg(data.message);
            }
        })
    });

    setTimeout(function(){ 
        $('.brand-programs').multiselect('reload');
        $('.ms-options label').append('<span class="checkmark" style="left:10px;top:10px"></span>');
        $('.ms-options label').addClass('custom-checkbox').css('cssText','margin-bottom: 0px !important;'); 
    }, 8000);
});
</script>
@endpush