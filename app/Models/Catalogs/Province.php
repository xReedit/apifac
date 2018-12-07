<?php

namespace App\Models\Catalogs;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    public $incrementing = false;
    public $timestamps = false;

    static function idByDescription($description)
    {
        $code = static::where('description', $description)->get();
        if (count($code) > 0) {
            return $code[0]->id;
        }
        return '1501';
    }
}