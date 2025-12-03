<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicalAdviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('medical_advice')->insert([

            [
                'title' => 'شرب كميات كافية من الماء',
                'desc' => 'المحافظة على ترطيب الجسم يساعد في تقليل الحمى وتسريع الشفاء.',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'الراحة التامة',
                'desc' => 'الراحة تساعد الجسم على مقاومة العدوى وتسريع التعافي.',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'تجنب الحكّة',
                'desc' => 'الحكّة قد تسبب التهابات أو تترك آثارًا على الجلد، يمكن استخدام كريمات مهدئة.',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'ارتداء ملابس قطنية واسعة',
                'desc' => 'تجنب الملابس الضيقة لأنها تزيد من تهيّج الجلد.',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'تخفيف الحمى',
                'desc' => 'يمكن استخدام كمادات ماء فاتر أو أدوية خافضة للحرارة حسب إرشاد الطبيب.',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'الحفاظ على نظافة الجلد',
                'desc' => 'الاستحمام بالماء الفاتر يساعد في تقليل الحكة ويمنع الالتهابات.',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'تجنب الاختلاط',
                'desc' => 'الجديري معدي جدًا، الأفضل عزل الطفل حتى تختفي البثور تمامًا.',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
