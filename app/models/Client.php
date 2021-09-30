<?php

namespace App\models;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use Notifiable,SoftDeletes;

    protected $fillable = [
        'prefix','name', 'street', 'city','state','country','zip','code','program_id','utility_id','formname'
    ];

    protected $primarykey = 'id';
    protected $table = 'clients';

    protected $dates = ['deleted_at'];


    public function getNameAttribute($value)
    {
        return ucfirst($value);
    }
    public function getCodeAttribute($value)
    {
        return strtoupper($value);
    }
    /**
     * for use local scope query
     * @param $query
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('status','active');
    }
    /**
     * for check client is active or not
     * @return bool
     */
    public  function isActive()
    {
        return $this->status == 'active';
    }
   public function getClientDetails()
   {
      return $this->orderBy('id','DESC')
                  ->paginate(20);
   }  
   public function getClientsList()
   {
     return $this->select('id','name')->where([
        ['status', '=', 'active']                                
      ])->orderBy('name','asc')->get();
   }
   public function getClientCode($id)
   {
    $code =  $this->select('code')->where([
        ['id', '=', $id]                                
      ])->get();
      return  $code[0]->code;
   }

   public function getClientPrefix($id)
   {
    $prefix =  $this->select('prefix')->where([
        ['id', '=', $id]                                
      ])->first();
      return  $prefix->prefix;
   }

   public function getClientinfo($id)
   {
    $info =  $this->where([
        ['id', '=', $id]                                
      ])->get();
      return  $info[0];
   }
   

   public function updateClient($id,$inputs)
   {
    
      return $this -> where('id',$id)
                 ->update( array(                   
                    'name' => $inputs['name'],
                    'street' => $inputs['street'],   
                    'city' => $inputs['city'],   
                    'state' => $inputs['state'],   
                    'country' => $inputs['country'],   
                    'zip' => $inputs['zip'],
                    'code' => $inputs['code'],
                    'prefix' => $inputs['prefix'],
                    'contact_info' => $inputs['contact_info']
                    )            
                  );
   } 
   public function updateClientLogo($id,$path)
   {   
      return $this -> where('id',$id)
                 ->update( array(                   
                    'logo' => $path,
                    )            
                  );
   } 
   public function updateClientStatus($id,$status)
   {   
      return $this -> where('id',$id)
                 ->update( array(                   
                    'status' => $status,
                    )            
                  );
   } 
   public function getClientsListByStatus($status = null)
   {
     return $this->select('id','name')
     ->when($status, function ($query, $status) {
      return $query->where('status', $status);
        })
      ->orderBy('name','asc')->get();
   }

    public function forms()
    {
        return $this->hasMany('App\models\Clientsforms', 'client_id');
    }

    public function salesCenters()
    {
        return $this->hasMany(Salescenter::class, 'client_id');
    }

    public function commodity()
    {
        return $this->hasMany(Commodity::class, 'client_id');
    }

    public function dispositions()
    {
        return $this->hasMany(Dispositions::class, 'client_id');
    }

    public function scripts()
    {
        return $this->hasMany(FormScripts::class, 'client_id');
    }

    public function workflows()
    {
        return $this->hasMany(ClientWorkflow::class, 'client_id');
    }
}
