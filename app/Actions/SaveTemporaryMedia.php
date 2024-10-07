<?php

namespace App\Actions;

use App\Exceptions\FileNotFoundException;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SaveTemporaryMedia
{
    protected string $diskName;

    public function __construct()
    {
        $this->diskName = config('filesystems.temporary_disk');
    }

    public function getDiskName(): string
    {
        return $this->diskName;
    }

    public function execute(string $inputName): array
    {
        if (! request()->hasFile($inputName)) {
            throw new FileNotFoundException('Input file not found.');
        }

        $directory = Str::random(16);

        $file = request()->file($inputName);

        $fileCleanUp = $this->cleanUpFileName($file->getClientOriginalName());
        // Store file to temporary disk, without changing the filename
        $path = Storage::disk($this->diskName)->putFileAs($directory, $file, $fileCleanUp);

        return [
            'path' => $path,
            'name' => $fileCleanUp,
            'url' => $this->generateUrl($path),
        ];
    }

    public function generateUrl(string $path): string
    {
        return Storage::disk($this->diskName)->temporaryUrl($path, now()->addMinutes(120));
    }

    public function delete(string $path): void
    {
        if ($this->exists($path)) {
            Storage::disk($this->diskName)->delete($path);
        }
    }

    public function exists(string $path): bool
    {
        //        return Storage::disk($this->diskName)->exists($path); // not working for AWS S3
        return (bool) Storage::disk($this->diskName)->get($path);
    }

    /**
     * Save file from temporary disk to media collection
     */
    public function saveFileFromTemp($model, string $collectionName, array $file): ?Media
    {
        /*
         * Fix for saving file from temporary disk (AWS S3)
         * Cannot use addMediaFromDisk because it will error: Unable to check existence for ...
         * So we need to get the file content and save it to the media collection manually
         */
        $media = null;
        if ($this->exists($file['path'])) {
            $media = $model->addMediaFromString(Storage::disk($this->diskName)->get($file['path']))
                ->usingFileName(basename($file['path']))
                ->toMediaCollection($collectionName);
        }

        return $media;

    }

    public function cleanUpFileName(string $file): string
    {
        // remove zero width space characters from an UTF-8 string
        return preg_replace("/[\x{200B}-\x{200D}\x{FEFF}]/u", '', $file);
    }
}
