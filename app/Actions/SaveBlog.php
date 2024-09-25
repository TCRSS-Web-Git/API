<?php

namespace App\Actions;

use App\Models\Blog;
use Exception;

class SaveBlog
{
    /**
     * @throws Exception
     */
    public function execute(Blog $blog, array $data): Blog
    {
        $blog->published_at = $data['published_at'] ?? null;
        $blog->slug = $data['slug'] ?? null;
        $blog->category_id = $data['category_id'] ?? null;
        $blog->setTranslation('title', $data['en']['title'], 'en');
        $blog->setTranslation('title', $data['th']['title'], 'th');
        $blog->setTranslation('body', $data['en']['body'], 'en');
        $blog->setTranslation('body', $data['th']['body'], 'th');
        $blog->setTranslation('meta_title', $data['en']['meta_title'], 'en');
        $blog->setTranslation('meta_title', $data['th']['meta_title'], 'th');
        $blog->setTranslation('meta_description', $data['en']['meta_description'], 'en');
        $blog->setTranslation('meta_description', $data['th']['meta_description'], 'th');
        $blog->save();

        return $blog;
    }
}
