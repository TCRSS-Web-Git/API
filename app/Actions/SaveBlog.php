<?php

namespace App\Actions;

use App\Models\Blog;
use Exception;
use Mews\Purifier\Facades\Purifier;

class SaveBlog
{
    /**
     * @throws Exception
     */
    public function execute(Blog $blog, array $data): Blog
    {
        $supportedLanguages = config('app.supported_locales');

        $blog->published_at = $data['published_at'] ?? null;
        $blog->slug = $data['slug'] ?? null;
        $blog->category_id = $data['category_id'] ?? null;

        foreach ($supportedLanguages as $lang) {
            if (! isset($data[$lang])) {
                continue;
            }

            $blog->setTranslation('title', $data[$lang]['title'] ?? null, $lang);
            $blog->setTranslation('body', isset($data[$lang]['body']) ? Purifier::clean($data[$lang]['body']) : null, $lang);
            $blog->setTranslation('meta_title', $data[$lang]['meta_title'] ?? null, $lang);
            $blog->setTranslation('meta_description', $data[$lang]['meta_description'] ?? null, $lang);
        }

        $blog->save();

        return $blog;
    }
}
