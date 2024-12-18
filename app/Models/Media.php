<?php

namespace App\Models;

use App\Traits\EloquentDecodeHash;
use App\Traits\Hashidable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Mail\Attachment;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

class Media extends BaseMedia
{
    use EloquentDecodeHash;
    use HasFactory;
    use Hashidable;

    public const HASHID_PREFIX = 'media_';

    public function mailAttachment(string $conversion = ''): Attachment
    {
        $attachment = Attachment::fromStorageDisk($this->disk, $this->getPathRelativeToRoot($conversion))->as($this->file_name);

        if ($this->mime_type) {
            $attachment->withMime($this->mime_type);
        }

        return $attachment;
    }
}
