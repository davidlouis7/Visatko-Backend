<?php

namespace App\Modules\Blog\Requests;

use App\Modules\Blog\Models\BlogPost;
use Illuminate\Foundation\Http\FormRequest;

class StoreBlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', BlogPost::class) === true;
    }

    public function rules(): array
    {
        return ['author_id' => ['nullable', 'integer', 'exists:users,id'], 'category_id' => ['required', 'integer', 'exists:blog_categories,id'], 'thumbnail_media_id' => ['nullable', 'integer', 'exists:media,id'], 'banner_media_id' => ['nullable', 'integer', 'exists:media,id'], 'is_published' => ['sometimes', 'boolean'], 'is_featured' => ['sometimes', 'boolean'], 'published_at' => ['nullable', 'date'], 'sort_order' => ['sometimes', 'integer', 'min:0'], 'tag_ids' => ['sometimes', 'array'], 'tag_ids.*' => ['integer', 'distinct', 'exists:blog_tags,id'], 'translations' => ['required', 'array', 'min:1'], 'translations.*.title' => ['required', 'string', 'max:200'], 'translations.*.slug' => ['required', 'string', 'max:220'], 'translations.*.excerpt' => ['nullable', 'string'], 'translations.*.content' => ['required', 'string'], 'translations.*.meta_title' => ['nullable', 'string', 'max:255'], 'translations.*.meta_description' => ['nullable', 'string'], 'translations.*.meta_keywords' => ['nullable', 'string']];
    }
}
