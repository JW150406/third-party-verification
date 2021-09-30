@extends('layouts.admin')

@section('content')
<ol class="breadcrumb bc-3">
   <li>
      <a href="{{route('dashboard')}}"><i class="fa fa-home"></i>Home</a>
   </li>
   <li>
      <a href="{{ route('reports.reportform') }}">Batch Export</a>
   </li>
   
  <li class="active">
      <strong>Result</strong>
   </li>
</ol>
 
        <div class="row">
            <div class="col-md-12">
             
               
                   @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                        @endif
                        <div class="add-newlink">
                        
                            <a class="btn btn-success btn-icon icon-left" href="{{ route('reports.exports',$export_params) }}"> <i class="fa fa-download"></i> Export</a>
                        </div>
                    <div class="table-responsive">
                       @include('reports.reportdata')
                    </div>
                    {!! $results->appends($query_params)->links()!!}
                    
                    
             
        </div>
    </div>
 
@endsection