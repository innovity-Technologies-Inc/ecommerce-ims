<?php

namespace App\Exports\Admin\Product;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductTemplateExport implements FromCollection, WithHeadings
{
    public function collection(): Collection
    {
        return collect([
            [
                'Product 1',
                'Electronics',
                'Mobiles',
                'Samsung',
                'Short desc',
                'Long description',
                1000,
                10, // 10% discount
                1,
                1,
                0,
                'active',
                10, // min_stock_global
                'global', // min_stock_type
                '8GB/128GB',
                'SAM-S21-8-128',
                '8/128',
                'Black',
                1000,
                10, // 10% discount for variant
                5, // variant_min_stock_global
                'global', // variant_min_stock_type
            ],
            [
                'Product 1',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '12GB/256GB',
                'SAM-S21-12-256',
                '12/256',
                'Silver',
                1100,
                5, // 5% discount for variant
                5, // variant_min_stock_global
                'global', // variant_min_stock_type
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'product_name',
            'category',
            'subcategory',
            'brand',
            'short_description',
            'description',
            'regular_price',
            'discount_percentage',
            'is_new_arrival',
            'is_hot_deal',
            'is_featured',
            'status',
            'min_stock_global',
            'min_stock_type',
            'variant_name',
            'variant_sku',
            'variant_size',
            'variant_color',
            'variant_regular_price',
            'variant_discount_percentage',
            'variant_min_stock_global',
            'variant_min_stock_type',
        ];
    }
}
