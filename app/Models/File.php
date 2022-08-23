<?php

namespace App\Models;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class File extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function orders()
    {
        return $this->morphedByMany(
            Order::class,
            'fileable',
            'fileables',
            'file_id',
            'fileable_id',
            'id',
        );
    }
}
