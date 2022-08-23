<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory, Searchable;

    protected $guarded = [];

    public function order_detail()
    {
        return $this->belongsTo(OrderDetail::class);
    }

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
            'sku' => $this->sku
        ];
    }
}
