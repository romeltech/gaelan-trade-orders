<?php

namespace App\Models;

use App\Models\User;
use App\Models\File;
use App\Models\Location;
use App\Models\OrderDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function order_details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function files()
    {
        return $this->morphToMany(
            File::class,
            'fileable',
            'fileables',
            'fileable_id',
            'file_id',
            '',
            'id'
        );

    }
}
