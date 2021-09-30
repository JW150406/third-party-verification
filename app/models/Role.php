<?php

namespace App\models;

use Illuminate\Support\Facades\Config;
use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{
    public  function getRolesForTPV()
    {
        if(\Auth::user()->hasRole('admin')) {
            $roles = ['admin','tpv_admin','tpv_qa'];
        } else {
            $roles = ['tpv_admin','tpv_qa'];
        }
        return $this->whereIn('name',$roles)->pluck('display_name', 'id');
    }
    public  function getRolesForClientUser()
    {
        return $this->where('accesslevel','client')->get();
    }
    public  function getRolesForSalesCenterUser()
    {
        if(\Auth::user()->hasRole('sales_center_location_admin')) {
            $roles = ['sales_center_qa','sales_center_location_admin'];
        } else if(\Auth::user()->hasRole('sales_center_qa')) {
            $roles = ['sales_center_qa'];
        } else {
            $roles = ['sales_center_admin','sales_center_qa','sales_center_location_admin'];
        }
        return $this->whereIn('name',$roles)->where('accesslevel','salescenter')->get();
    }
}
