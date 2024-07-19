<?php

namespace App\Traits\Api\V1;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

trait UploadFilesTrait
{
    public function createDirectory($folderName)
    {
        $basePath = public_path('uploads');

        $filePath = "{$basePath}/{$folderName}";

        if (!File::exists($filePath)) {
            File::makeDirectory($filePath, 0755, true);
        }

        return $filePath;
    }

    public function deleteFile($filePath)
    {
        Storage::disk('uploads')->delete("$filePath");
    }
}
