<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Client\ComplianceController;
use App\models\ComplianceReports;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Zipper;

class ComplianceReportingController extends ComplianceController
{
    //
    public function __construct(Request $request){
      parent::__construct($request);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
      $client_id = $this->client_id;
      $client = $this->client_detail;
      $compliance_templates =  $this->complianceTemplates->templateslist($client_id);
      $report_data = array();
      if(isset($request->search)){
        $this->validate($request,[
          'template' => 'required|numeric'
        ]);
        $report_data = $this->getComplianceReport($request);
      }

      return view('client.compliance-reports.reportform',compact('client_id','compliance_templates','client','report_data'));
    }

    /* Export Compliance Reports */
    public function export(Request $request){
      $params =  $this->get_query_params($request);
      $template_id  = $params['template'];
      $template_detail =   $this->complianceTemplates->gettemplate($template_id);
      $maped_fields = unserialize($template_detail->fields);
      $params['client_id'] = $template_detail->Client_id;
      $params['form_id'] = $template_detail->form_id;
      $compliance_data =  (new ComplianceReports)->exportData($maped_fields,$params);

      $filename = "Batch-Export-".date('y-m-d');
      Excel::create($filename, function ($excel) use ($compliance_data) {

       $excel->sheet('Report', function ($sheet) use ($compliance_data) {


             $i = 1;
           foreach( $compliance_data as   $data_to_export ){
             $column_name = 'A';
             foreach ($data_to_export as $key => $value) {
               if($i==1){
                 $column_value = $key;
               }else{
                 $column_value = $value;
               }
               $sheet->cell($column_name.$i,$column_value, function($cell, $cellvalue) {
                        $cell->setValue( $cellvalue );
                  });
               $column_name++;
               // code...
             }
             $i++;

           }
       });
   })->download();
    }

    /* Export all Compliance Reports */
  public function exportall(Request $request){
   $params =  $this->get_query_params($request);
       $client_id = $this->client_id;
    $compliance_templates =  $this->complianceTemplates->templateslist($client_id);
   if(count($compliance_templates) > 0) {
    $current_folder = time();
    $files = array();
     foreach ($compliance_templates as  $template) {
         $template_id  =  $template->id;
         $template_detail =   $this->complianceTemplates->gettemplate($template_id);
         $maped_fields = unserialize($template_detail->fields);
         $params['client_id'] = $template_detail->Client_id;
         $params['form_id'] = $template_detail->form_id;
         $compliance_data =  (new ComplianceReports)->exportData($maped_fields,$params);
         $filename = $template->name.time().rand();

         Excel::create($filename, function ($excel) use ($compliance_data) {

          $excel->sheet('Report', function ($sheet) use ($compliance_data) {


                $i = 1;
              foreach( $compliance_data as   $data_to_export ){
                $column_name = 'A';
                foreach ($data_to_export as $key => $value) {
                  if($i==1){
                    $column_value = $key;
                  }else{
                    $column_value = $value;
                  }
                  $sheet->cell($column_name.$i,$column_value, function($cell, $cellvalue) {
                           $cell->setValue( $cellvalue );
                     });
                  $column_name++;
                  // code...
                }
                $i++;

              }
          });
       })->store('xls', storage_path('excel/exports/'.$current_folder));

      $files[] = storage_path('excel/exports/'.$current_folder).'/'.$filename.".xls";
     }
     /* Create Zip */

     $zipname = ini_get('upload_tmp_dir')."/ComplianceExport-".rand().'.zip';
    if(count($files) > 0){

        Zipper::make($zipname)->add($files)->close();
    }
   /* Delete folder from storage*/
     $dir = storage_path('excel/exports/'.$current_folder);
     if (is_dir($dir)) {
         $objects = scandir($dir);
         foreach ($objects as $object) {
           if ($object != "." && $object != "..") {
             if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
           }
         }
         reset($objects);
         rmdir($dir);
       }

    header("Content-type: application/zip");
    header("Content-Disposition: attachment; filename=".basename($zipname));
    header("Content-length: " . filesize($zipname));
    header("Pragma: no-cache");
    header("Expires: 0");
    readfile("$zipname");
    unlink($zipname);



   }


  }


    public function getComplianceReport($request){
        $params =  $this->get_query_params($request);
        $template_id  = $params['template'];
        $template_detail =   $this->complianceTemplates->gettemplate($template_id);
        $maped_fields = unserialize($template_detail->fields);
        $params['client_id'] = $template_detail->Client_id;
        $params['form_id'] = $template_detail->form_id;
        return $compliance_data =  (new ComplianceReports)->getTemplateData($maped_fields,$params);
    }


    public function get_query_params($request){
        $params = array();
        if(isset($request->template)){
            $params['template'] = $request->template;
        }
        if(isset($request->date_start)){
             list($params['date_start'],$params['date_end'] ) = explode('-',$request->date_start);
          }
        return $params;

    }
}
