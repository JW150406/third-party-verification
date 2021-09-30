<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
 <title>{{ $title }}</title>
 <style>
 body {
    font-family: "Arial";
    padding:15px;
}
 .logo-wrapper {
   display : block;
 }
 .logo-wrapper img {
   display : block;
   margin: 0 auto;
 }
 table th {
   text-align:left;
 }
 .underline-bottom {
	padding: 0 10px;
	min-width: 50px;
	border-bottom: 1px solid;
	 
}
div {
  display:block;
}
table {
  width : 100%;
}
 </style>
</head><body>
<table>
 <tr>
 <td align="center">
   <img src="{{asset('images/harborside.jpg')}}" alt=""/>
 </td>
 
 </tr>
 <tr>
 <td>
 <p>
  This is the agreement between energy company and <span class="underline-bottom">{{$customer_info['firstname']}}</span>. Customer phone number is <span class="underline-bottom">{{$customer_info['Phone']}}</span> and email is <span class="underline-bottom">{{$customer_info['email']}}</span>. Full name of the customer is <span class="underline-bottom">{{$customer_info['firstname']}} {{$customer_info['lastname']}}  </span>  
  </p>
 </td>
 </tr>
 <tr>
 <td>
 <p>
   Program code is  <span class="underline-bottom">{{$program_info['ProgramCode']}}</span>.
  </p>
  <p>
   MSF:  <span class="underline-bottom">{{$program_info['Msf']}}</span>.
  </p>
  <p>
   ETF : <span class="underline-bottom">{{$program_info['Etf']}}</span>.
  </p>
  <p>
  Brand : <span class="underline-bottom">{{$program_info['Brand']}}</span>.
  </p>
 </td>
 </tr>
 <tr>
 @if($customer_info['signature'] != '')
 <td align="right">

   <img src="{{$customer_info['signature']}}" width="200" alt="" />
 </td>
 
 </tr>

 @endif;

</table>
 
</body>
</html>