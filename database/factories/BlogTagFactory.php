<?php

namespace Database\Factories;

use App\Modules\Blog\Models\BlogTag;
use Illuminate\Database\Eloquent\Factories\Factory;

class BlogTagFactory extends Factory
{
    protected $model = BlogTag::class;

    public function definition(): array
    {
        return ['is_active' => true];
    }
}
