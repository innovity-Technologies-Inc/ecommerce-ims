<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            ['name' => 'Global Textiles Co.', 'email' => 'contact@globaltextiles.com', 'mobile' => '1112223331', 'address' => 'New York, NY'],
            ['name' => 'Urban Fabrics Ltd.', 'email' => 'sales@urbanfabrics.com', 'mobile' => '1112223332', 'address' => 'Los Angeles, CA'],
            ['name' => 'Sole Manufacturers', 'email' => 'info@solemfg.com', 'mobile' => '1112223333', 'address' => 'Portland, OR'],
            ['name' => 'Eco-Apparel Sources', 'email' => 'support@ecoapparel.com', 'mobile' => '1112223334', 'address' => 'Austin, TX'],
            ['name' => 'Denim Masters', 'email' => 'orders@denimmasters.com', 'mobile' => '1112223335', 'address' => 'Chicago, IL'],
            ['name' => 'Luxury Silks Inc.', 'email' => 'vip@luxurysilks.com', 'mobile' => '1112223336', 'address' => 'San Francisco, CA'],
            ['name' => 'Modern Threads', 'email' => 'hello@modernthreads.com', 'mobile' => '1112223337', 'address' => 'Miami, FL'],
            ['name' => 'Classic Knits', 'email' => 'sales@classicknits.com', 'mobile' => '1112223338', 'address' => 'Seattle, WA'],
            ['name' => 'Elite Accessories', 'email' => 'contact@eliteacc.com', 'mobile' => '1112223339', 'address' => 'Boston, MA'],
            ['name' => 'Premium Leathers', 'email' => 'info@premiumleathers.com', 'mobile' => '1112223340', 'address' => 'Denver, CO'],
            ['name' => 'Trendsetters Garments', 'email' => 'sales@trendsetters.com', 'mobile' => '1112223341', 'address' => 'Atlanta, GA'],
            ['name' => 'Heritage Weavers', 'email' => 'support@heritageweavers.com', 'mobile' => '1112223342', 'address' => 'Philadelphia, PA'],
            ['name' => 'Pacific Sportswear', 'email' => 'orders@pacificsport.com', 'mobile' => '1112223343', 'address' => 'San Diego, CA'],
            ['name' => 'Atlantic Apparel', 'email' => 'info@atlanticapparel.com', 'mobile' => '1112223344', 'address' => 'Charlotte, NC'],
            ['name' => 'Northern Woolens', 'email' => 'contact@northernwoolens.com', 'mobile' => '1112223345', 'address' => 'Minneapolis, MN'],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::updateOrCreate(
                ['email' => $supplier['email']],
                [
                    'name' => $supplier['name'],
                    'mobile' => $supplier['mobile'],
                    'address' => $supplier['address'],
                ]
            );
        }
    }
}
