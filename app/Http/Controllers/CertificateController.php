<?php
namespace App\Http\Controllers;

use App\CoreFacturalo\Util;
use App\Models\Company;
use Exception;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function record()
    {
        $company = Company::byUser();

        return [
            'certificate' => optional($company)->certificate
        ];
    }

    public function uploadFile(Request $request)
    {
        $company = Company::byUser();
        try {
            if ($company) {
                if ($request->hasFile('file')) {
                    $company = Company::byUser();
                    $password = $request->input('password');
                    $file = $request->file('file');
                    $pfx = file_get_contents($file);
                    $pem = Util::generateCertificatePEM($pfx, $password);
                    $name = 'certificate_'.$company->number.'.pem';
                    if(!file_exists(storage_path('app'.DIRECTORY_SEPARATOR.'certificates'))) {
                        mkdir(storage_path('app'.DIRECTORY_SEPARATOR.'certificates'));
                    }
                    file_put_contents(storage_path('app'.DIRECTORY_SEPARATOR.'certificates'.DIRECTORY_SEPARATOR.$name), $pem);
                    $company->certificate = $name;
                    $company->save();

                    return [
                        'success' => true,
                        'message' =>  __('app.actions.upload.success'),
                    ];
                } else {
                    throw new Exception("Archivo no encontrado.");
                }
            } else {
                throw new Exception("Empresa aún no creada.");
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' =>  $e->getMessage(),
            ];
        }
    }

    public function destroy()
    {
        $company = Company::byUser();
        $company->certificate = null;
        $company->save();

        return [
            'success' => true,
            'message' => 'Certificado eliminado con éxito'
        ];
    }
}