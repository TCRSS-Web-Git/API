<?php

namespace App\Models;

use App\Enums\JobType;
use App\Traits\EloquentDecodeHash;
use App\Traits\EloquentFindByHash;
use App\Traits\HasFilter;
use App\Traits\Hashidable;
use App\Traits\HasTags;
use App\Traits\HasTranslations;
use App\Traits\LatestAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class JobPost extends Model implements Auditable, HasMedia
{
    use EloquentDecodeHash;
    use EloquentFindByHash;
    use HasFactory;
    use HasFilter;
    use Hashidable;
    use HasTags;
    use HasTranslations;
    use InteractsWithMedia;
    use LatestAudit;
    use \OwenIt\Auditing\Auditable;

    public const HASHID_PREFIX = 'job_';

    public const string MEDIA_COLLECTION_BODY_PHOTO = 'photos';

    protected $fillable = [
        'type',
        'published_at',
    ];

    protected $casts = [
        'type' => JobType::class,
        'published_at' => 'datetime',
    ];

    public function getTranslationTable()
    {
        return 'job_translations';
    }

    public function registerMediaCollections(): void
    {
        // รูปใน body
        $this->addMediaCollection(self::MEDIA_COLLECTION_BODY_PHOTO)
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion(self::MEDIA_COLLECTION_BODY_PHOTO.'_optimized')
                    ->withResponsiveImages()
                    ->format('webp')
                    ->fit(Fit::Max, 2000, 2000)
                    ->optimize();
            });
    }

    public function getAllTranslations(): array
    {
        $locales = config('app.supported_locales');
        $translations = [];

        foreach ($locales as $locale) {
            $translations[$locale] = [
                'title' => $this->getTranslation('title', $locale),
                'body' => $this->getTranslation('body', $locale),
                'meta_title' => $this->getTranslation('meta_title', $locale),
                'meta_description' => $this->getTranslation('meta_description', $locale),
            ];
        }

        return $translations;
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'location_id', 'id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'department_id', 'id');
    }
}
