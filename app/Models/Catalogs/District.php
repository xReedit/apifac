<?php

namespace App\Models\Catalogs;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    public $incrementing = false;
    public $timestamps = false;

    static function idByDescription($description, $province_id = null)
    {
        $code = static::where('description', $description);
        if ($province_id) {
            $code = $code->where('province_id', $province_id);
        }
        $code = $code->get();
        if (count($code) > 0) {
            return $code[0]->id;
        }
        return '150101';
    }
}