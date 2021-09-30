<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormField extends Model
{
    use SoftDeletes;
    
    protected $table = "form_fields";

    protected $fillable = ['form_id', 'label', 'type', 'meta', 'is_required', 'is_primary', 'is_allow_copy', 'created_by', 'is_verify', 'position', 'regex', 'regex_message','is_auto_caps','is_multienrollment'];

    protected $casts = [
	    'meta' => 'array',
	];

    public function form()
    {
        return $this->belongsTo('App\models\Clientsforms', 'form_id');
    }

    public function telesalesData()
    {
        return $this->hasMany('App\models\Telesalesdata', 'field_id');
    }

    public function telesalesDataTmp()
    {
        return $this->hasMany('App\models\TelesalesdataTmp', 'field_id');
    }
}
