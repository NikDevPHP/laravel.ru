<?php declare(strict_types = 1);
/**
 * This file is part of laravel.ru package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\GraphQL\Serializers;

use App\Models\Article;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ArticleSerializer
 *
 * @package App\GraphQL\Serializers
 */
class ArticleSerializer extends AbstractSerializer
{
    /**
     * @param Article|Model $article
     * @return array
     */
    public function toArray(Model $article): array
    {
        return [
            'id'           => $article->id,
            'title'        => $article->capitalize_title,
            'url'          => route('article', ['slug' => $article->slug]),
            'image'        => $article->image_url,
            'content'      => $article->content_rendered,
            'source'       => $article->content_source,
            'status'       => $article->status,
            'published_at' => $article->published_at->toRfc3339String(),
            'user'         => UserSerializer::serialize($article->user),
            'tags'         => TagSerializer::collection($article->tags),
        ];
    }
}