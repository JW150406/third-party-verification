Hey <strong>{{$name}}</strong> <br>
You have been added to <strong>{{$addedby_vendor}}</strong>  by <strong>{{$addedby_firstname}}</strong><br>
Your username is: <strong>{{$email}}</strong><br>
Please click <a href="{!! url('/'.$vendor_id.'/verify', ['code'=>$verification_code]) !!}">Here</a> to generate your password.