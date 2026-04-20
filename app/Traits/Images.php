<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait Images
{
    public function storeImage($image, string $path = 'images')
    {
        $fileName = time() . '_' . str_replace(' ', '_', $image->getClientOriginalName());
        $url = Storage::disk("local")->putFileAs($path, $image, $fileName);
        return $url;
    }

    public function updateImage($image, $oldImage = null, string $path = 'images')
    {
        if ($oldImage && Storage::disk('local')->exists($oldImage)) {
            Storage::disk('local')->delete($oldImage);
        }

        return $this->storeImage($image, $path);
    }

    public function deleteImage($imagePath)
    {
        if ($imagePath && Storage::disk('local')->exists($imagePath)) {
            Storage::disk('local')->delete($imagePath);
        }
    }
}
