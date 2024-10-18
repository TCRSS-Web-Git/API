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

class AnnualReport extends Model implements Auditable, HasMedia
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

    public const HASHID_PREFIX = 'annual_report_';

    public const string MEDIA_COLLECTION_COVER = 'cover';

    public const string MEDIA_COLLECTION_FILE = 'file';

    protected $fillable = [
        'published_at',
        'order',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function registerMediaCollections(): void
    {
        // cover
        $this->addMediaCollection(self::MEDIA_COLLECTION_COVER)
            ->singleFile()
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion(self::MEDIA_COLLECTION_COVER.'_optimized')
                    ->withResponsiveImages()
                    ->format('webp')
                    ->background('FFFFFF')
                    ->fit(Fit::Crop, 978, 1224) // 4:5
                    ->optimize();
            });

        // file
        $this->addMediaCollection(self::MEDIA_COLLECTION_FILE)
            ->singleFile();
    }

    public function getTranslationTable()
    {
        return 'annual_report_translations';
    }

    public function getAllTranslations(): array
    {
        $locales = config('app.supported_locales');
        $translations = [];

        foreach ($locales as $locale) {
            $translations[$locale] = [
                'title' => $this->getTranslation('title', $locale),
            ];
        }

        return $translations;
    }
}
