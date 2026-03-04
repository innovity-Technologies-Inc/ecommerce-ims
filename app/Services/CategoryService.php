<?php

namespace App\Services;

use App\HelperClass;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryService
{
    /**
     * Store a newly created category.
     */
    public function storeCategory(array $data): Category
    {
        $categoryData = [
            'name' => $data['name'],
            'parent_id' => $data['parent_id'] ?? null,
            'slug' => Str::slug($data['name']),
        ];

        if (isset($data['icon'])) {
            $categoryData['icon'] = HelperClass::file_upload($data['icon'], 'categories');
        }

        return Category::create($categoryData);
    }

    /**
     * Update the specified category.
     */
    public function updateCategory(Category $category, array $data): Category
    {
        $categoryData = [
            'name' => $data['name'],
            'parent_id' => $data['parent_id'] ?? null,
            'slug' => Str::slug($data['name']),
        ];

        if (isset($data['icon'])) {
            if ($category->icon) {
                HelperClass::file_delete($category->icon);
            }
            $categoryData['icon'] = HelperClass::file_upload($data['icon'], 'categories');
        }

        $category->update($categoryData);

        return $category;
    }

    /**
     * Delete the specified category.
     */
    public function deleteCategory(Category $category): void
    {
        if ($category->icon) {
            HelperClass::file_delete($category->icon);
        }
        $category->delete();
    }
}
