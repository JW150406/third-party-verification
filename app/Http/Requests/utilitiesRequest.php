<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class utilitiesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if(\Request::isMethod('post')){
            return [
                "utilityname" => 'required',
                "commodity" => 'required',
                "market" => 'required'
            ];
        }
    
        return [];
        
    }

    public function messages()
    {
        return [ 
            'commodity.required' => 'Please enter commodity',  
            'market.required' => "Market is required",
            'utilityname.required' => "Name is required",
            
        ];
    }
}
