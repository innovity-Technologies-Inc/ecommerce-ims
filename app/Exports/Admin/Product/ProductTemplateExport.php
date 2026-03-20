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
                900,
                50,
                1,
                1,
                0,
                0,
                'active',
                '8GB/128GB',
                'SAM-S21-8-128',
                '8/128',
                'Black',
                1000,
                900,
                20,
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
                1000,
                30,
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
            'discount_price',
            'stock',
            'is_new_arrival',
            'is_hot_deal',
            'is_featured',
            'is_top_pick',
            'status',
            'variant_name',
            'variant_sku',
            'variant_size',
            'variant_color',
            'variant_regular_price',
            'variant_discount_price',
            'variant_stock',
        ];
    }
}
