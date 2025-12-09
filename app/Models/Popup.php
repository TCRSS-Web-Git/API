<?php

namespace App\Models;

use App\Traits\EloquentDecodeHash;
use App\Traits\EloquentFindByHash;
use App\Traits\Hashidable;
use App\Traits\LatestAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Popup extends Model implements Auditable, HasMedia
{
    use EloquentDecodeHash;
    use EloquentFindByHash;
    use HasFactory;
    use Hashidable;
    use InteractsWithMedia;
    use LatestAudit;
    use \OwenIt\Auditing\Auditable;

    public const HASHID_PREFIX = 'popup_';

    public const string MEDIA_COLLECTION_IMAGE = 'popup_image';

    protected $fillable = [
        'order',
        'is_active',
    ];

    protected $cast = [
        'is_active' => 'boolean',
    ];

    public function registerMediaCollections(): void
    {
        // image
        $this->addMediaCollection(self::MEDIA_COLLECTION_IMAGE)
            ->singleFile()
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion(self::MEDIA_COLLECTION_IMAGE.'_optimized')
                    ->withResponsiveImages()
                    ->format('webp')
                    ->background('FFFFFF')
                    ->fit(Fit::Crop, 1280, 720) // 16:9
                    ->optimize();
            });
    }
}