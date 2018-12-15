<?php

namespace App\CoreFacturalo\Helpers;

use Illuminate\Support\Facades\Storage;

trait StorageDocument
{
    public function uploadStorage($root, $folder, $file_content, $filename, $extension = 'xml')
    {
        Storage::put('tenants'.DIRECTORY_SEPARATOR.$root.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR.$filename.'.'.$extension, $file_content);
    }

    public function downloadStorage($root, $folder, $filename, $extension = 'xml')
    {
        return Storage::download('tenants'.DIRECTORY_SEPARATOR.$root.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR.$filename.'.'.$extension);
    }

    public function getStorage($root, $folder, $filename, $extension = 'xml')
    {
        return Storage::get('tenants'.DIRECTORY_SEPARATOR.$root.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR.$filename.'.'.$extension);
    }
}