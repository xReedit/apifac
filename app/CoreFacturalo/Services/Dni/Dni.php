<?php

namespace App\Core\Services\Dni;


class Dni
{
    public static function search($number)
    {
        // $res = Essalud::search($number);
        // if ($res['success']) {
            // return $res;
        // }

        // $res = Jne::search($number);
        // return $res;
	$token = '82f79c922bdb3c5c654b7e5c4c47150214ee051af6b8cadba3e112825cb94eb5';
        $res = Jne::search($number, $token, 1);
        if (!$res['success']) {
         	// token 2
       	   $token = '9032a50cc5152873fe7c0d1485ade12b09b050b01b5cdf3235d370665d9b41ab';
	   $res = Jne::search($number, $token, 1);
	   // return $res;

	if (!$res['success']) {
                // token 3
                $token = '7baf8819d3bf52e0fff21aef0c9c2a01d464de8af821531604086f689545c7ed';
                $res = Jne::search($number, $token, 1);
                return $res;
            } else {
                return $res;
            }


        } else {
	   return $res;
	}
    }
}
