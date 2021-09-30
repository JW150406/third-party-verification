<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class TpvAgentLanguage extends Model
{
    protected $fillable = [
        'user_id',
        'english',
        'spanish'
    ];

    /**
     *  create or update tpv agent language
     * @param $userId
     * @param array $languages
     * @return mixed
     */
    public function store($userId,array $languages){
    	$data = [
    		'user_id' => $userId,
    		'english' => in_array('en', $languages),
    		'spanish' => in_array('es', $languages),
    	];
        return $this->updateOrCreate(['user_id'=>$userId],$data);
    }
}
