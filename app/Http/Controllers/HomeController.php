<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use Illuminate\Support\Facades\Storage;
use App\models\Telesales;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = \Auth::user();
        if($user->access_level == 'salesagent') {
            return redirect()->route('my-account');
        } else if($user->access_level == 'tpvagent') {
            return redirect()->route('tpvagents.sales');
        } else {
            return redirect()->intended('/admin/dashboard');
        }
       // return view('home');
    }
    
    /**
     * This function is used to load contract pdf
     */
    public function testpdf(){
        
      $data = (new Telesales)->getDetailsForPdf('747');
      $mstatus  = 0;
      if(!empty($data)){
          $leadData = $data[0];
          $signature = "";
          if($leadData->signature != "") {
             $signature = url(Storage::url($leadData->signature));
          }
    
          $customer_info = array(
            'firstname' => $leadData->FirstName, 
            'lastname' => $leadData->LastName,
            'email' => $leadData->Email,
            'Phone' => $leadData->Phone,
            'signature' => $signature

       );
       $program_info = array(
           'Msf' => $leadData->Msf,
           'Etf' => $leadData->Etf,
           'Brand' => $leadData->Brand,
           'ProductName' => $leadData->ProductName,
           'BillingAddress' => $leadData->BillingAddress,
           'BillingCity' => $leadData->BillingCity,
           'BillingState' => $leadData->BillingState,
           'BillingZip' => $leadData->BillingZip,
           'BillingCounty' => $leadData->BillingCounty,
           'ProgramCode' => $leadData->ProgramCode,
       );
       
       $data = [          
           'title' => 'TPV Contract',         
           'heading' => 'TPV Contract', 
           'customer_info' => $customer_info ,
           'program_info' => $program_info        
         
          ];
    //    /   return view('contractpdf/contractpdfnew',$data);
          $pdf =  PDF::loadView('contractpdf/contractpdfnew',$data);
        return $pdf->stream();
       die();

          $name  = $leadData->FirstName;
          $email = 'dalvir@matrixmarketers.com';//$leadData->Email;
          $refrence_id = $leadData->refrence_id;
          $to    = "$name <$email>";
          $from        = "TPV";
          $subject     = "TPV contract copy";
          
          $mainMessage = " Dear $name,<br>";
          $mainMessage .= "Thank you for your enquiry regarding the full copy of terms and conditions, please find attached signed copy.<br><br>";
          $mainMessage .="If you have any further questions, please feel free to contact our Customer Service Team on 1800 JUST ENERGY (1800 785 733) Monday to Friday 8am to 8pm, excluding public holidays or email: <a href='mailto:customer.service@justenergy.com'>customer.service@justenergy.com</a><br>";
          $mainMessage .="We warmly welcome you to Just Energy. <br><br>";
          $mainMessage .="Regards<br>";
          //$mainMessage .="<img src=\"data:image/png;base64,".base64_encode(file_get_contents('https://spark.tpv.plus/images/just-energy-logo.png'))."\">";
          $mainMessage .="Customer Services Team";

          $encoded_content = chunk_split(base64_encode($pdf->output()));
          $from_name = "TPV";
          $from_mail = "no-reply@spark.tpv.plus";
          $filename = $refrence_id.".pdf";

           
           


          
 


          
          $fileatttype = "application/pdf";
          $fileattname = $refrence_id.".pdf";
          $headers = "From: $from";
        
          $semi_rand     = md5(time());
        //   $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
        //   $headers      .= "\nMIME-Version: 1.0\n" .
        //   "Content-Type: multipart/mixed;\n" .
        //   " boundary=\"{$mime_boundary}\"";
 

          $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 

        //headers for attachment 
        $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 

        //multipart boundary 
        $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
        "Content-Transfer-Encoding: 7bit\n\n" . $mainMessage . "\n\n"; 
            
          $message .= "--{$mime_boundary}\n" .
          "Content-Type: application/octet-stream;\n" .
          " name=\"{$fileattname}\"\n" .
          "Content-Disposition: attachment;\n" .
          " filename=\"{$fileattname}\"\n" .
          "Content-Transfer-Encoding: base64\n\n" .
          $encoded_content . "\n\n" .
          "-{$mime_boundary}-\n";
          

       echo   $mstatus = mail($to, $subject, $message , $headers);
          

      }
      die('sds fasdfa sdfssss');
         return  $mstatus;  
    }
}
