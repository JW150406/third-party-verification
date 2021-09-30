@extends('layouts.login')

@section('content')

    <div class="container" >
        <div class="row" >
            <div class="col-md-12" >
                <span class="vertical-align-inactivity-419">Please wait a moment while you are being redirected...</span>
            </div>
        </div>
    </div>
@endsection
@section("scripts")
<script type="text/javascript">
	jQuery(document).ready(function(){
		setTimeout(function(){
			window.location.href = "{!! url('/login') !!}";
		},5000);
	});
</script>
@endsection