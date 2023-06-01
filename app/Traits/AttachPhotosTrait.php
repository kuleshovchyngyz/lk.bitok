<?php

namespace App\Traits;

use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

trait AttachPhotosTrait
{
    public function attachPhotos($request, $user)
    {
        $photoTypes = [
            'cv_photo',
            'cv_photo_bf',
            'certificate_photo',
            'licence_photo',
            'permit_photo',
            'passport_photo'
        ];

        foreach ($photoTypes as $photoType) {
            if ($request->has($photoType) && is_array($request[$photoType])) {
                $photos = $request->file($photoType);
                $this->attach($photos, $user, $photoType);
            }
        }
    }
    public function attach($photos, $user, $type)
    {
        $thumbnail_url = url('/').'/default.jpg';
        foreach ($photos as $key => $singleFile) {

            $fileName = uniqid() . $singleFile->getClientOriginalName();
            Storage::disk('uploads')->put($fileName, file_get_contents($singleFile));
            if (in_array(strtolower($singleFile->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'bmp'])) {
                Image::make($singleFile->path())->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->save(public_path('uploads') . '/thumbnails/' . $fileName);
                $thumbnail_url = Storage::disk('uploads')->url('thumbnails/' . $fileName);
            }

            $attachments = new Attachment([
                'type' => $type,
                'url' => Storage::disk('uploads')->url($fileName),
                'thumbnail_url' => $thumbnail_url
            ]);
            $user->attachments()->save($attachments);
        }

    }


}
