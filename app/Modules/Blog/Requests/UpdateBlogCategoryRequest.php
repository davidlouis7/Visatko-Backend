<?php

namespace App\Modules\Blog\Requests;

class UpdateBlogCategoryRequest extends StoreBlogCategoryRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('blog_category')) === true;
    }

    public function rules(): array
    {
        return array_map(fn (array $rules): array => array_merge(['sometimes'], $rules), parent::rules());
    }
}
