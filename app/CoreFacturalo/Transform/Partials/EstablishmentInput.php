<?php

namespace App\CoreFacturalo\Transform\Partials;

use App\Models\Catalogs\Country;
use App\Models\Catalogs\Department;
use App\Models\Catalogs\District;
use App\Models\Catalogs\Province;

class EstablishmentInput
{
    public static function transform($data)
    {
        $country_id = $data['codigo_pais'];
        $district_id = $data['ubigeo'];
        $urbanization = array_key_exists('urbanizacion', $data)?$data['urbanizacion']:null;
        $address = $data['direccion'];
        $email = $data['correo_electronico'];
        $telephone = $data['telefono'];
        $code = $data['codigo_del_domicilio_fiscal'];

        $department_id = null;
        $province_id = null;

        if ($district_id) {
            $province_id = substr($district_id, 0 ,4);
            $department_id = substr($district_id, 0 ,2);
        }

        return [
            'country_id' => $country_id,
            'country' => [
                'id' => $country_id,
                'description' => Country::find($country_id)->description,
            ],
            'department_id' => $department_id,
            'department' => [
                'id' => $department_id,
                'description' => Department::find($department_id)->description,
            ],
            'province_id' => $province_id,
            'province' => [
                'id' => $province_id,
                'description' => Province::find($province_id)->description,
            ],
            'district_id' => $district_id,
            'district' => [
                'id' => $district_id,
                'description' => District::find($district_id)->description,
            ],
            'urbanization' => $urbanization,
            'address' => $address,
            'email' => $email,
            'telephone' => $telephone,
            'code' => $code,
        ];
    }
}