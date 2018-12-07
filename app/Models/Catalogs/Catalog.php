<?php

namespace App\Models\Catalogs;

use Illuminate\Database\Eloquent\Model;

class Catalog extends Model
{
    public $incrementing = false;
    public $timestamps = false;

    public function codes()
    {
        return $this->hasMany(Code::class);
    }
}