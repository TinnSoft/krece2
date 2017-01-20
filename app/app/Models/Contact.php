<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $table = 'contact';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name','nit','address','account_id','user_id','public_id','city','email','phone1','phone2','fax',
        'phone_mobile','list_price_id','seller_id','payment_terms_id','observation','include_account_state','isProvider','isCustomer',
        'isDeleted','deleted_at'
    ];

   
     public function contact_others()
    {
        return $this->hasMany(Contact_others::class);
    }

    public function estimates()
    {
        return $this->hasMany(Estimate::class);
    }
    
}
