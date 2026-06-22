<?php

namespace Database\Factories;

use App\Modules\Blog\Models\BlogCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class BlogCategoryFactory extends Factory
{
    protected $model = BlogCategory::class;

    public function definition(): array
    {
        return ['is_active' => true, 'sort_order' => 0];
    }
}
