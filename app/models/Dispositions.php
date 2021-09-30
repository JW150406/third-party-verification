<?php
namespace App\models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dispositions extends Model
{
    use SoftDeletes;
    protected $primarykey = 'id';
    protected $table = 'dispositions';

    protected $guarded = [];

    protected $appends = ['category','group'];
    protected $dates = ['deleted_at'];
    // protected $fillable = [
    //     'email_alert'
    //     ];

    /**
     * add custom category column
     * @return mixed|string
     */
    public function getCategoryAttribute()
    {
        if($this->type == 'decline' ) {
            $type = 'Declined';
        } else if($this->type == 'customerhangup' ) {
            $type = 'Call Disconnected';
        }else if($this->type == 'esignature_cancel' ) {
            $type = 'E-signature Cancel';
        }else if($this->type == 'do_not_enroll' ) {
            $type = 'Do Not Enroll';
        } else {
            $type = ucfirst($this->type);   
        }
        return $type;
    }

    /**
     * add custom group column
     * @return mixed|string
     */
    public function getGroupAttribute()
    {        
        if($this->disposition_group == 'lead_detail' ) {
            $group = 'Lead Detail';
        } else if($this->disposition_group == 'sales_agent' ) {
            $group = 'Sales Agent';
        } else if($this->disposition_group == 'customer' ){
            $group = 'Customer';    
        } else {
            $group = ucfirst($this->disposition_group);   
        }
        return $group;
    }

    public function saveDisposition($data){
        return $this->insertGetId($data);
    }
    public function updateDisposition($id,$data){
        return $this->where('id', $id)
               ->update($data);
    }

   /* public function getDisposition($id)
    {
          $response = $this->findOrFail($id);
          return $response;
    }*/

    public function getDisposition($client_id,$id){
        return $this->where([
            ['client_id', '=', $client_id],
            ['id', '=', $id],
        ])->firstOrFail();
    }

    public function getDispositionList($type = null)
    {
        return $this->select('id','description','allow_cloning')
        ->when($type, function ($query) use ($type) {
            return $query->where('type', $type);
        })->orderBy('description','asc')->get();
    }
    public function getList()
    {
        return $this->select('id','description','type','allow_cloning')->orderBy('description','asc')->paginate(20);
    }
    public function deleteDisposition($id)
    {
        return $this->where('id', $id)->delete();
    }

    public function activeAndInactiveDisposition($id, $status = "inactive")
    {
        $data = array(
            'status' => $status
        );
        return $this->where('id', $id)->update($data);
    }



}
