<?php

// namespace App\Traits;

// use Illuminate\Support\Facades\Storage;

// trait UploadImage
// {
//     public function storeImage($image, string $category, string $basePath = 'images')
//     {
//         $folder = "{$basePath}/{$category}";
//         $fileName = time() . '_' . str_replace(' ', '_', $image->getClientOriginalName());

//         $path = Storage::disk('public')->putFileAs($folder, $image, $fileName);
//         return $path;
//         // return url(Storage::url($path));

//     }

//     public function updateImage($image, $oldImage = null, string $category = 'images')
//     {
//         if ($oldImage) {
//             $relativePath = str_replace(url('/storage') . '/', '', $oldImage);

//             if (Storage::disk('public')->exists($relativePath)) {
//                 Storage::disk('public')->delete($relativePath);
//             }
//         }

//         return $this->storeImage($image, $category);
//     }

//     public function deleteImage($imagePath)
//     {
//         if (!$imagePath) return;

//         $relativePath = str_replace(url('/storage') . '/', '', $imagePath);

//         if (Storage::disk('public')->exists($relativePath)) {
//             Storage::disk('public')->delete($relativePath);
//         }
//     }

//     public function attachImages($request, $model, string $relationName = 'images', string $category = 'ads')
//     {
//         if ($request->hasFile($relationName)) {
//             foreach ($request->file($relationName) as $file) {
//                 $path = $this->storeImage($file, $category);
//                 $model->{$relationName}()->create(['url' => $path]);
//             }
//         }
//     }

//     public function replaceImages($request, $model, string $relationName = 'images', string $category = 'ads')
//     {
//         if ($request->hasFile($relationName)) {
//             foreach ($model->{$relationName} as $img) {
//                 $this->deleteImage($img->url);
//                 $img->delete();
//             }

//             $this->attachImages($request, $model, $relationName, $category);
//         }
//     }
// }


namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait Images
{

// store image
    //public function storeImage(){}
    //public function deleteImage(){}
    //public function updateImage(){}



}
