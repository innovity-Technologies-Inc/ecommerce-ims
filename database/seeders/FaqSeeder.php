<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'How can I track my order?',
                'answer' => 'You can track your order by clicking on the "Track Order" link in your account dashboard or footer.',
                'sort_order' => 1,
            ],
            [
                'question' => 'What payment methods do you accept?',
                'answer' => 'We accept all major credit cards, PayPal, and Bank Transfers.',
                'sort_order' => 2,
            ],
            [
                'question' => 'Do you ship internationally?',
                'answer' => 'Yes, we ship to over 50 countries worldwide.',
                'sort_order' => 3,
            ],
            [
                'question' => 'How do I return an item?',
                'answer' => 'Please visit our Return Policy page for detailed instructions on how to return an item.',
                'sort_order' => 4,
            ],
            [
                'question' => 'Is my personal information secure?',
                'answer' => 'Yes, we use industry-standard encryption to protect your data.',
                'sort_order' => 5,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(
                ['question' => $faq['question']],
                [
                    'answer' => $faq['answer'],
                    'sort_order' => $faq['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
