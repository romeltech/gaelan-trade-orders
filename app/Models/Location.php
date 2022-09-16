<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    use HasFactory, Searchable;

    protected $guarded = [];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
            'code' => $this->code,
            'area' => $this->area,
        ];
    }
}
