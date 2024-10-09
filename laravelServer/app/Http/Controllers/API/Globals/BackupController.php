<?php

namespace App\Http\Controllers\API\Globals;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use File;
use ZipArchive;

class BackupController extends Controller
{
    public function backupFile() {
        $zip = new ZipArchive;

        $fileName = 'myNewFile.zip';
        if ($zip->open(public_path($fileName), ZipArchive::CREATE) === TRUE)
        {

            $files = File::files(public_path('upload/image'));

            foreach ($files as $key => $value) {
                $relativeNameInZipFile = basename($value);
                $zip->addFile($value, $relativeNameInZipFile);

            }
            $zip->close();
        }
        return response()->download(public_path($fileName));
    }
}
