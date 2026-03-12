<?php

namespace App\Services;

use App\HelperClass;
use App\Models\Category;
use DaiyanMozumder\LaravelFlexSearch\FlexSearch;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class CategoryService
{
    /**
     * Get all categories with search and sorting.
     */
    public function getAllCategories(array $params = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Category::with('parent');

        $filters = [];
        if (! empty($params['parent_id'])) {
            $filters['parent_id'] = $params['parent_id'];
        }
        if (! empty($params['slug'])) {
            $filters['slug'] = $params['slug'];
        }

        $flexSearch = app(FlexSearch::class);
        $searchTerm = $params['search'] ?? null;
        $searchableColumns = ['name', 'slug', 'parent.name'];

        // Apply Search and Filtering using FlexSearch
        $query = $flexSearch->apply($query, $filters, $searchTerm, $searchableColumns);

        // Apply Sorting
        $sort = $params['sort'] ?? 'latest';
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'a-z':
                $query->orderBy('name', 'asc');
                break;
            case 'z-a':
                $query->orderBy('name', 'desc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        return $query->paginate($perPage);
    }

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
