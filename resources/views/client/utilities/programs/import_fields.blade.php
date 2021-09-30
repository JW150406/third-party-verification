@extends('layouts.admin')

@section('content')

 <div class="tpv-contbx">
			<div class="container">
                <div class="tpvbtn">
                    <div class="col-xs-12 col-sm-12 col-md-12"> <h2>Utilities Mapping</h2></div>
                    <form class="form-horizontal" method="POST" action="{{ route('utility.programs.import_process') }}">
                            {{ csrf_field() }}
                            <input type="hidden" name="csv_data_file_id" value="{{ $csv_data_file->id }}" />
                            <input type="hidden" name="client" value="{{ $client_id }}" />
                            <input type="hidden" name="utility" value="{{ $utility_id }}" />
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
                                    <?php $i = $j = 0; ?>
                                    @foreach ($csv_data as $row)
                                    @if($i==0)
                                        @foreach ($row as $key => $value)
                                      <?php  $j++;   if($j % 2 == 0){
                                            $first_last_td_class = "light_c";
                                            $second_and_middle_td_class = "white_c";
                                        }else{
                                            $first_last_td_class = "dark_c";
                                            $second_and_middle_td_class = "grey_c";
                                            }
                                        ?>
                                        <tr>
                                            <td class="{{ $first_last_td_class}}">{{ $key }}</td>
                                            <td class="{{ $second_and_middle_td_class}}">{{ $csv_data[1][$key]}}</td>
                                            <td class="{{ $first_last_td_class}}">
                                                <select name="fields[{{ $key }}]" class="selectsearch" >
                                                    <option value=''>Select</option>
                                                    @foreach ($database_fields as $db_field)
                                                        <option value="{{ $db_field }}"
                                                            @if ($key === $db_field) selected @endif>{{ $db_field }}</option>
                                                    @endforeach
                                                </select></td>
                                            </tr>
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
                    </form>
                </div>
             </div>
        </div>
  
@endsection
