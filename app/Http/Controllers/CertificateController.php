<?php
namespace App\Http\Controllers;

use App\Core\WS\Signed\Certificate\X509Certificate;
use App\Core\WS\Signed\Certificate\X509ContentType;
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
        if ($company) {
            if ($request->hasFile('file')) {
                try {
                    $company = Company::byUser();
                    $password = $request->input('password');
                    $file = $request->file('file');
                    $pfx = file_get_contents($file);
                    $certificate = new X509Certificate($pfx, $password);
                    $pem = $certificate->export(X509ContentType::PEM);
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
                } catch (Exception $e) {
                    return [
                        'success' => false,
                        'message' =>  $e->getMessage()
                    ];
                }
            }
            return [
                'success' => false,
                'message' =>  __('app.actions.upload.error'),
            ];
        } else {
            return [
                'success' => false,
                'message' =>  'La empresa aún no ha sido creada',
            ];
        }
    }
}