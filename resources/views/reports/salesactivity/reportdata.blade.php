@if(count($results) > 0)
<div class="table-responsive">
   <table class="table">
    <thead>
    <tr class="heading">
     @foreach($results[0] as $heading => $value)

        <td> {{ $heading }} </td>
         
     @endforeach
    </tr>
    </thead>
    <tbody> 
    


    
    <?php $i = 0; ?>
        @foreach($results as $report)
       
        <?php $i++;
    if($i % 2 == 0){
                                $first_last_td_class = "";
                                $second_and_middle_td_class = "";
                        }else{
                            $first_last_td_class = "";
                            $second_and_middle_td_class = "";
                        }
                                ?>
        
        
            <tr>
            @foreach($report as $headinglabel => $valueoflead )
              
              <td class="{{$first_last_td_class}}">{{ $valueoflead }}</td>
      
            @endforeach 
            </tr>  
       
           
          
   
        @endforeach

       

    
    </tbody>
</table>
</div>
@else
<h2>No Record Found</h2>
@endif