<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = ['id','shop_id','salt_type_id','product_size','quantity_sold','rate_per_pack','total_sales_amount','remarks','date'];

    public function shop(){
        return $this->belongsTo(Shop::class);
    }

    public function salt_type(){
        return $this->belongsTo(SaltType::class);
    }
}
