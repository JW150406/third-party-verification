<script>
    /**
     * get sales centers and commodity
     */
	function getSalesCenterAndCommodities(client_id) {            
	    $.ajax({
	        url: "{{ route('ajax.getSalesCenterAndCommodity') }}",
	        type: "POST",
	        data: {
	            'client_id': client_id
	        },
	        success: function(res) {
	            if (res.status === true) {
	                var clientClass= '';
	                var html = '<option value="" selected>All Commodity</option>';
	                var commodities = res.data.commodity;
	                for (i = 0; i < commodities.length; i++) {
	                	clientClass = 'client-'+commodities[i].client_id;
	                    html += '<option class="commodity-opt '+clientClass+'"  client='+commodities[i].client_id+' value="' + commodities[i].id + '">' + commodities[i].name + '</option>'
	                }
	                $('#commodity').html(html);
	                var shtml = "";
	                @if(!(Auth::user()->hasAccessLevels('salescenter')))
	                    shtml = '<option value="" selected>All Sales Centers</option>';
	                @endif
	                
	                var sales_center = res.data.sales_centers;
	                for (i = 0; i < sales_center.length; i++) {
	                	clientClass = 'client-'+sales_center[i].client_id;
	                    shtml += '<option class="salecenters-opt '+clientClass+'" client='+sales_center[i].client_id+' value="' + sales_center[i].id + '">' + sales_center[i].name + '</option>'
	                }
	                $('#sales_center').html(shtml);

	                setLocations();
	            } else {
	                console.log(res.message);
	            }
	        }
	    })
	    
	}

    /**
     *  show selected client sales centers and hide other sales centers
     */
	function showSelectedSalesCenters(clientId = "") {
		console.log("sales: "+clientId);
		$('#sales_center').val('').trigger('change.select2');
		$('#commodity').val('').trigger('change.select2');
		if (clientId == "") {
	        // $(".salecenters-opt").show();
	        // $(".commodity-opt").show();
            $(".salecenters-opt").prop('disabled',false);
            $(".commodity-opt").prop('disabled',false);
		} else {
			//$(".salecenters-opt").hide();
            $(".salecenters-opt").prop('disabled',true);
			$(".commodity-opt").prop('disabled',true);
            //$(".client-"+clientId).show();
            $(".client-"+clientId).prop('disabled',false);
		}
        $("#sales_center,#commodity").select2();
    }

    /**
     * show and hide locations on sales center
     * @param salescenterId
     */
    function showSelectedLocations(salescenterId = "") {
		$('#location').val('').trigger('change.select2');
		if (salescenterId == "") {
	        $(".locations-opt").prop('disabled',false);
		} else {
			$(".locations-opt").prop('disabled',true);
	        $(".salescenter-"+salescenterId).prop('disabled',false);
		}
        $("#location").select2();
	}
	
	function showSelectedSalesAgents(salescenterId = "") {
		
		$('#sales_agent').val('').trigger('change.select2');
		if (salescenterId == "") {
	        $(".sales-agent-opt").prop('disabled',false);
		} else {
			$(".sales-agent-opt").prop('disabled',true);
	        $(".agent-salescenter-"+salescenterId).prop('disabled',false);
		}
        $("#sales-agent").select2();
    }

    /**
     * set client drop down
     * @param clientId
     */
    function setClient(clientId) {
    	if (clientId > 0) {
    		$("#client").val(clientId).trigger('change.select2');
    	}
    }

    /**
     * set sales center drop down
     * @param salescenterId
     */
    function setSalesCenter(salescenterId) {
    	if (salescenterId > 0) {
    		$("#sales_center").val(salescenterId).trigger('change.select2');
    	}
    }

    // set options of location filter
    function setLocations(isAllSalesCenter=true)
    {
        let clientId = $("#client").val();
        let salescenterId = $("#sales_center").val();
        setSalesCenterLocationOptions("location",clientId,salescenterId,'',isAllSalesCenter,"All Locations");
    }

    function resetFilterDate(startDate,endDate) 
    {
        $('#date_start,#filter_date,#submission_date').data('daterangepicker').setStartDate(startDate);
        $('#date_start,#filter_date,#submission_date').data('daterangepicker').setEndDate(endDate); 
    }

    $(document).ready(function() {

        $("#client").change(function() {
            showSelectedSalesCenters($(this).val());
        	showSelectedLocations();
        });

        $("#sales_center").change(function() {
        	let clientId = $('option:selected', this).attr('client');
        	setClient(clientId);
			showSelectedLocations($(this).val());
			showSelectedSalesAgents($(this).val());
		});
		
		$("#sales_agent").change(function() {
        	let salesCenterId = $('option:selected', this).attr('salescenter');
        	setSalesCenter(salesCenterId);
        });

        $("#location").change(function() {
        	let clientId = $('option:selected', this).attr('client');
        	let salescenterId = $('option:selected', this).attr('salescenter');
        	setClient(clientId);
        	setSalesCenter(salescenterId);
        });

        $("#commodity").change(function() {
        	let clientId = $('option:selected', this).attr('client');
        	setClient(clientId);
        });

        $("input,select").change(function() {
            $("#reset-filter").show();
        });

        $("#reset-filter").click(function() {
			brandReportFilters('');
            $(".select2").not(":disabled").prop("selectedIndex", 0).trigger('change.select2');
        	$(this).closest('form').trigger('reset');
            @if(!Auth::user()->hasAccessLevels('salescenter'))
            	showSelectedSalesCenters();
            	showSelectedLocations();
            	showSelectedSalesAgents();
            @endif
        	resetFilterDate(firstDay,today);
			$('#enrollment-report,#enrollment-table,#report-table,#lead-table,#critical-table,#billing-report,#call-details-report').DataTable().ajax.reload();
			@if(request()->route()->getName() == 'reports.reportform')
			loadStateData();
            @endif
            $("#reset-filter").hide();
        });
        $("#reset-filter").hide();
	});
	function brandReportFilters(clientId){
		$.ajax({
			type: "POST",
			url: "{{ route('reports.ajax.brands') }}",
			data: {'_token':'{{csrf_token()}}','clientId':clientId},
			success:function(data)
			{
				let brandOp = '<option value="" selected>All Brands</option>';
				let brands = data.data;
				$.each(brands,function(k,v){
					brandOp += '<option value='+v.id+'>'+v.name+'</option>';
				})
				$('#brand').html(brandOp);
			}
		})
	}
</script>