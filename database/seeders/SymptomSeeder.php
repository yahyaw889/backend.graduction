<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SymptomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('symptoms')->insert([

            [
                'name' => 'طفح جلدي أحمر',
                'desc' => 'يبدأ عادةً كبقع حمراء صغيرة ثم تتحول إلى بثور مملوءة بالسائل.',
                'category' => 'varicella',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'حكة شديدة',
                'desc' => 'الحكة تزداد مع انتشار الطفح الجلدي.',
                'category' => 'varicella',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ارتفاع في درجة الحرارة',
                'desc' => 'حمّى خفيفة إلى متوسطة قبل ظهور الطفح.',
                'category' => 'varicella',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'إرهاق عام',
                'desc' => 'الشعور بالتعب والخمول قبل ظهور الأعراض الجلدية.',
                'category' => 'varicella',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'صداع خفيف',
                'desc' => 'يحدث غالبًا في بداية الإصابة.',
                'category' => 'varicella',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'فقدان الشهية',
                'desc' => 'يقل نشاط الطفل ورغبته في الطعام خلال العدوى.',
                'category' => 'varicella',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'آلام في العضلات',
                'desc' => 'تحدث في بداية المرض مع الحمى.',
                'category' => 'varicella',
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
