<?php

namespace Database\Seeders;

use App\Models\MailSetting;
use Illuminate\Database\Seeder;

class MailSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MailSetting::create([
            'mail_transport' => 'smtp',
            'mail_host' => 'smtp.mailtrap.io',
            'mail_port' => '2525',
            'mail_username' => 'test_user',
            'mail_password' => 'test_password',
            'mail_encryption' => 'tls',
            'mail_from_address' => 'hello@smart-ecom.com',
            'mail_from_name' => 'smart-ecom',
        ]);
    }
}
