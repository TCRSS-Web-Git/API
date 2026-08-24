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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class BoardDirector extends Model implements Auditable, HasMedia
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

    public const HASHID_PREFIX = 'board_director_';

    public const string MEDIA_COLLECTION_IMAGE = 'image';

    protected $fillable = [
        'group_order',
        'order',
        'published_at',
        'published_by',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function publishedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::MEDIA_COLLECTION_IMAGE)
            ->singleFile()
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion(self::MEDIA_COLLECTION_IMAGE.'_optimized')
                    ->withResponsiveImages()
                    ->format('webp')
                    ->fit(Fit::Max, 1000, 1000)
                    ->optimize();
            });
    }

    public function getTranslationTable()
    {
        return 'board_director_translations';
    }

    public function getAllTranslations(): array
    {
        $locales = config('app.supported_locales');
        $translations = [];

        foreach ($locales as $locale) {
            $translations[$locale] = [
                'name' => $this->getTranslation('name', $locale),
                'position' => $this->getTranslation('position', $locale),
            ];
        }

        return $translations;
    }
}
