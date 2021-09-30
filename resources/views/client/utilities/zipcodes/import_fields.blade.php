@extends('layouts.admin')

@section('content')
<?php 
$breadcrum = array(); 
$breadcrum[] =  array('link' => route('utility.importzip',['client' => $client_id]) , 'text' =>  'Import zip');
$breadcrum[] =  array('link' => '' , 'text' =>  "Zipcode Mapping"); 
breadcrum ($breadcrum);
 ?>

 <div class="tpv-contbx">
			<div class="container">
                <div class="tpvbtn">
                <div class="col-xs-12 col-sm-12 col-md-12"> <h2>Zipcodes Mapping</h2></div>
                </div>
                <form  method="POST" action="{{ route('client.utility.import_zip_process',['client_id' => $client_id]) }}">
                {{ csrf_field() }}
                            <input type="hidden" name="csv_data_file_id" value="{{ $csv_data_file->id }}" />
                            <input type="hidden" name="client" value="{{ $client_id }}" />
                    <div class="col-xs-12 col-sm-12 col-md-12 sales_tablebx"> 
                        <div class="table-responsive"> 
                                
                                <table class="table">
                                <thead>
                                    <tr class="heading">
                                        <th>CSV heading</th>
                                        <th>CSV first row </th>
                                        <th>Select Column</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                            <?php $i = 0;?>
                                    @foreach ($csv_data as $row)
                                    @if($i==$icount)
                                        <?php $j = 0; ?>
                                        <?php 
                                        
                                            if($j % 2 == 0){
                                                $first_last_td_class = "light_c";
                                                $second_and_middle_td_class = "white_c";
                                            }else{
                                                $first_last_td_class = "dark_c";
                                                $second_and_middle_td_class = "grey_c";
                                                }
                                            ?>
                                     
                                        @foreach ($row as $key => $value)
                                        <?php $checkzipcolumn = $key; ?>
                                        <?php 
                                        
                                        if($j % 2 == 0){
                                            $first_last_td_class = "light_c";
                                            $second_and_middle_td_class = "white_c";
                                        }else{
                                            $first_last_td_class = "dark_c";
                                            $second_and_middle_td_class = "grey_c";
                                            }
                                        ?>
                                    
                                            <tr>
                                            <td class="{{$first_last_td_class}}">{{ $csv_header_fields[$j] }}</td>
                                            <td class="{{$second_and_middle_td_class}}"><?php print_r($value);?> </td>
                                            <td class="{{$first_last_td_class}}">
                                                <select name="fields[{{ $key }}]" class="selectsearch">
                                                    <option value=''>Select</option>
                                                    <option value='market'>Market</option>
                                                    <option value='commodity'>Commodity</option> 
                                                    <option value='utilityname'>Brand</option> 
                                                       @foreach ($database_fields as $db_field)
                                                        <option value="{{$db_field }}"
                                                            @if ($key === $db_field) selected @endif>{{ $db_field }}</option>
                                                       @endforeach
                                                     
                                                </select></td>
                                            </tr>
                                              
                                            <?php $j++; ?>
                                        @endforeach
                                    @endif
                                    <?php $i++; ?>
                                    @endforeach
                                </tbody>
                                </table>
                                <div class="btnintable bottom_btns">
                                      <button type="submit" class="btn btn-green">
                                        Save <span class="add"><?php echo getimage('images/update_w.png')  ?></span>
                                    </button>
                                    </div>
                         </div>
                                
                        </div>
                    </div>
                   
                    
                 </form>
             </div>
    </div>

  

@endsection
