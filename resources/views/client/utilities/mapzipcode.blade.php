
<div class="col-sm-12 col-sm-8 col-md-8">
                  <form   enctype="multipart/form-data" role="form" method="POST" action="{{ route('client.utility.mapzipcode', $utility->id)}}">
                  {{ csrf_field() }}
									 	<div class="zip-inputbx">
											 <label for="selectzipcode"></label>
                       <input  autocomplete="off" type="text" class="form-control" id="selectzipcode" name="selectzipcode" value="" required placeholder="Select Zipcode">
												<button class="btn btn-green" type="submit">Update</button>
                      </div> 
                    </form> 
                  </div>  
                  
									<div class="col-xs-12 col-sm-12 col-md-12">
										<h1>Assigned Zip codes</h1>  
									</div>
									  
									<div class="row">
                  <div class="col-xs-12 col-sm-12 col-md-12" >
                        <div class="full-width">
                                <div class="table-responsive">
                                @if(count($zipcodes) > 0)
            
                                  <table class="table">
                                    <thead>
                                      <tr class="heading">
                                      <th>Zip code</th>
                                      <th>City</th>
                                      <th>County</th>
                                      <th>state</th>
                                      </tr>
                                      </thead>
                                      <tbody>
                                        <?php $i = 0; ?>
                                    @foreach($zipcodes as $zip)
                                    <?php $i++; ?>
                                    <?php if($i % 2 == 0){
                                                $first_last_td_class = "light_c";
                                                $second_and_middle_td_class = "white_c";
                                            }else{
                                                $first_last_td_class = "dark_c";
                                                $second_and_middle_td_class = "grey_c";
                                            }
                                            ?>
                                    <tr>
                                    <td class="{{$first_last_td_class}}">{{ $zip->zipcode }}</td>
                                    <td class="{{$second_and_middle_td_class}}">{{ $zip->city }}</td>
                                    <td class="{{$second_and_middle_td_class}}">{{ $zip->county }}</td>
                                    <td class="{{$first_last_td_class}}">{{ $zip->state }}</td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                    </table>
                                  @endif
                              

                                </div>
                      </div>
									</div>
		   </div>
 


<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script> 
    function split( val ) {
      return val.split( /,\s*/ );
    }
    function extractLast( term ) {
      return split( term ).pop();
    }
 
    $( "#selectzipcode" )
      // don't navigate away from the field on tab when selecting an item
      .on( "keydown", function( event ) {
        if ( event.keyCode === $.ui.keyCode.TAB &&
            $( this ).autocomplete( "instance" ).menu.active ) {
          event.preventDefault();
        }
      })
      .autocomplete({
        source: function( request, response ) {
          $.getJSON( "/ajax/getzipcode", {
            find: extractLast( request.term )
          }, response );
        },
        search: function() {
          // custom minLength
          var term = extractLast( this.value );
          if ( term.length < 2 ) {
            return false;
          }
        },
        focus: function() {
          // prevent value inserted on focus
          return false;
        },
        select: function( event, ui ) {
          var terms = split( this.value );
          // remove the current input
          terms.pop();
          // add the selected item
          terms.push( ui.item.value );
          // add placeholder to get the comma-and-space at the end
          terms.push( "" );
          this.value = terms.join( ", " );
          return false;
        }
      });
 
  </script>
