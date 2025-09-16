<?php 

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

trait FileUpload {

    public function uploadFile(UploadedFile $file, string $directory, string $prefix) {
        $filename = $prefix.'_'.uniqid().'.'. $file->getClientOriginalExtension();

        $file->move(public_path($directory), $filename);

        return $directory. '/'. $filename; //uploads/filename.extension
    }

    public function deleteFile(?string $path) {

        if (basename($path) === 'avatar.png') {
            return false;
        }

        $fullPath = public_path($path);
        if (File::exists($fullPath)) {
            File::delete($fullPath);
            return true;
        }

        return false;
    }
}