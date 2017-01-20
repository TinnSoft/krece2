<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{   
    protected $dates = ['deleted_at'];
    protected $table = 'product';
    protected $fillable=['public_id','user_id','account_id','name','description','reference'];
    
    public function estimatedetail()
    {
        return $this->belongsTo(EstimateDetail::class, 'id', 'product_id');
    }
  
}
