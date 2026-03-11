<?php

namespace Database\Seeders;

use App\Models\ContactSetting;
use Illuminate\Database\Seeder;

class ContactSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ContactSetting::updateOrCreate(['id' => 1], [
            'company_name' => 'Smart Ecom Store',
            'company_email' => 'contact@smartecom.com',
            'phone_number' => '+880 1234 567890',
            'address' => '121 King St, Melbourne VIC 3000, Australia',
            'map_link' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7302.131589963045!2d90.41127921609963!3d23.780671282181437!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755c79ebfc24eab%3A0xea7dab563f12457a!2sGulshan%201%2C%20Dhaka%201212!5e0!3m2!1sen!2sbd!4v1773262882886!5m2!1sen!2sbd',

            'facebook_url' => 'https://facebook.com/smartecom',
            'facebook_status' => true,

            'instagram_url' => 'https://instagram.com/smartecom',
            'instagram_status' => true,

            'tiktok_url' => 'https://tiktok.com/@smartecom',
            'tiktok_status' => true,

            'x_url' => 'https://x.com/smartecom',
            'x_status' => true,

            'thread_url' => 'https://threads.net/@smartecom',
            'thread_status' => true,

            'linkedin_url' => 'https://linkedin.com/company/smartecom',
            'linkedin_status' => true,

            'whatsapp_url' => 'https://wa.me/8801234567890',
            'whatsapp_status' => true,

            'youtube_url' => 'https://youtube.com/c/smartecom',
            'youtube_status' => true,
        ]);
    }
}
