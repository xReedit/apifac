<?php

namespace App\Core\Services\Dni;

use App\Core\Services\Helpers\Functions;
use App\Core\Services\Models\Person;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class Jne
{
    public static function search($number, $token, $fromApi = 0)
    {
        if (strlen($number) !== 8) {
            return [
                'success' => false,
                'message' => 'DNI tiene 8 digitos.'
            ];
        }


	// solo consultar de la api
        if ( $fromApi == 1 ) {

            $_url = 'https://apiperu.dev/api/dni/'.$number;


            $httpClient = new Client();

            $response = $httpClient->get(
                $_url,
                [
                    RequestOptions::HEADERS => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $token,
                    ]
                ]
            );

            $result = json_decode($response->getBody()->getContents());
            if ( $result->success ) {
                $person = new Person();
                $person->number = $number;
                $person->verification_code = Functions::verificationCode($number);
                $person->name = $result->data->nombres;
                // $person->name = $result->data->nombre_completo;
                $person->first_name = $result->data->apellido_paterno;
                $person->last_name = $result->data->apellido_materno;
                $person->names = $result->data->nombre_completo;
		$person->date_of_birthday = isset($result->data->fecha_nacimiento) ? $result->data->fecha_nacimiento : null;
                // $person->date_of_birthday = $result->data->fecha_nacimiento;
                // $person->names = $result->data->nombres;

                return [
                    'success' => true,
                    'source' => 'apidev',
                    'data' => $person
                ];
            }  else {

                return [
                    'success' => false,
                    'source' => 'apidev',
                    'message' => 'Datos no encontrados.'
                ];

            }



        }



        $client = new  Client(['base_uri' => 'http://aplicaciones007.jne.gob.pe/']);
        $response = $client->request('GET', 'srop_publico/Consulta/api/AfiliadoApi/GetNombresCiudadano?DNI='.$number);
        if ($response->getStatusCode() == 200 && $response != "") {
            $text = $response->getBody()->getContents();
            $parts = explode('|', $text);
            if (count($parts) === 3) {
                $person = new Person();
                $person->number = $number;
                $person->verification_code = Functions::verificationCode($number);
                $person->name = $parts[0].' '.$parts[1].', '.$parts[2];
                $person->first_name = $parts[0];
                $person->last_name = $parts[1];
                $person->names = $parts[2];

                return [
                    'success' => true,
                    'data' => $person
                ];
            } else {

                // api factulralo

                // $token = '7baf8819d3bf52e0fff21aef0c9c2a01d464de8af821531604086f689545c7ed';
                $_url = 'https://apiperu.dev/api/dni/'.$number;


                $httpClient = new Client();

                $response = $httpClient->get(
                    $_url,
                    [
                        RequestOptions::HEADERS => [
                            'Accept' => 'application/json',
                            'Authorization' => 'Bearer ' . $token,
                        ]
                    ]
                );

                $result = json_decode($response->getBody()->getContents());
                if ( $result->success ) {
                    $person = new Person();
                    $person->number = $number;
                    $person->verification_code = Functions::verificationCode($number);
                    $person->name = $result->data->nombres;
                    $person->first_name = $result->data->apellido_paterno;
                    $person->last_name = $result->data->apellido_materno;
                    $person->names = $result->data->nombre_completo;
		    $person->date_of_birthday = isset($result->data->fecha_nacimiento) ? $result->data->fecha_nacimiento : null;
		    // $person->date_of_birthday = $result->data->fecha_nacimiento;

                    return [
                        'success' => true,
			'source' => 'apidev',
                        'data' => $person
                    ];
                }  else {

                    return [
                        'success' => false,
                        'message' => 'Datos no encontrados.'
                    ];

                }


            }
        }

        return [
            'success' => false,
            'message' => 'Coneccion fallida.'
        ];
    }

}
