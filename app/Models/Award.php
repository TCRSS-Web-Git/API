<?php

namespace App\Models;

use App\Traits\EloquentDecodeHash;
use App\Traits\EloquentFindByHash;
use App\Traits\HasFilter;
use App\Traits\Hashidable;
use App\Traits\HasTranslations;
use App\Traits\LatestAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Award extends Model implements Auditable, HasMedia
{
    use EloquentDecodeHash;
    use EloquentFindByHash;
    use HasFactory;
    use HasFilter;
    use Hashidable;
    use HasTranslations;
    use InteractsWithMedia;
    use LatestAudit;
    use \OwenIt\Auditing\Auditable;

    public const HASHID_PREFIX = 'award_';

    public const string MEDIA_COLLECTION_DESCRIPTION_PHOTO = 'photos';

    protected $fillable = [
        'published_at',
        'order',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function registerMediaCollections(): void
    {
        // รูปใน body
        $this->addMediaCollection(self::MEDIA_COLLECTION_DESCRIPTION_PHOTO)
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion(self::MEDIA_COLLECTION_DESCRIPTION_PHOTO.'_optimized')
                    ->withResponsiveImages()
                    ->format('webp')
                    ->fit(Fit::Max, 2000, 2000)
                    ->optimize();
            });
    }

    public function getTranslationTable()
    {
        return 'award_translations';
    }

    public function getAllTranslations(): array
    {
        $locales = config('app.supported_locales');
        $translations = [];

        foreach ($locales as $locale) {
            $translations[$locale] = [
                'title' => $this->getTranslation('title', $locale),
                'description' => $this->getTranslation('description', $locale),
            ];
        }

        return $translations;
    }
}
