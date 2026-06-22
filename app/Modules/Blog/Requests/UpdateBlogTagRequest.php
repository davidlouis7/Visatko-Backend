<?php

namespace App\Modules\Blog\Requests;

class UpdateBlogTagRequest extends StoreBlogTagRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('blog_tag')) === true;
    }

    public function rules(): array
    {
        return array_map(fn (array $rules): array => array_merge(['sometimes'], $rules), parent::rules());
    }
}
