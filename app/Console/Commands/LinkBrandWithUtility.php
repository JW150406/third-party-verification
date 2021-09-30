<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\models\Client;
use App\models\Utilities;
use App\models\Brandcontacts;
use DB;
use Log;

class LinkBrandWithUtility extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'brand-utility:link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This commands links brands and utilities';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // This query is for link old brands with utility and brand contacts.    
        \Log::info('Brand utility link command start');
        $clients = Client::pluck('id');
        $brandContacts = [];
        $data = [];
        $utilities = [];
        foreach($clients as $key => $val)
        {
            $brandContacts = Brandcontacts::where('client_id',$val)->distinct()->pluck('name');
            $brandContacts =  Utilities::where('client_id',$val)->whereNotIn('utilityname',$brandContacts)->distinct()->pluck('utilityname');
            if(count($brandContacts) > 0)
            {
                foreach($brandContacts as $k => $v)
                {
                    $data['client_id'] = $val;
                    $data['name'] = $v;
                    $getId = Brandcontacts::create($data);
                }
            }
            
            $allBrandContacts = Brandcontacts::get(['name','id','client_id'])
            ->where('client_id',$val);  
            
            foreach($allBrandContacts as $ke =>$va)
            {
                DB::table('utilities')->where('utilityname',$va->name)->where('client_id',$val)->update(['brand_id' => $va->id]);
            }            
        }
        \Log::info('Brand utility link command end');
    }
}
