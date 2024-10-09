<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class smsTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sms_templates')->insert([

            [
                'title' => 'اس ام اس جلسات اموزش',
                'key' => 'training_session_time_set',
                'template' => 'ایران تکنولوژی'.PHP_EOL.'همکار محترم'.PHP_EOL.'{customer}'.PHP_EOL.'جلسه آموزش شما در تاریخ '.' {session_date} '.' و در ساعت '.' {session_time} {location_status} {location_place} {address}'.' برگزار میشود'.PHP_EOL.'از دیدار شما بسیار خرسند خواهیم شد.'.PHP_EOL.'{instagram_link}',
                'params' => '',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'اس ام اس چک لیست مرحله اداری',
                'key' => 'checklist_office',
                'template' => 'ایران تکنولوژی'.PHP_EOL.'همکار محترم'.PHP_EOL.'{customer}'.PHP_EOL.'قرارداد شما در واحد اداری با موفقیت ثبت گردید و پروژه شما به واحد پشتیبانی انتقال یافت.'.PHP_EOL.'از همکاری با شما بسیار خوشحالیم.'.PHP_EOL.'{instagram_link}',
                'params' => '',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'اس ام اس طرح اولیه چک لیست',
                'key' => 'designer_first_design',
                'template' => 'ایران تکنولوژی'.PHP_EOL.'همکار محترم'.PHP_EOL.'{customer}'.PHP_EOL.' طرح اولیه سایت آماده تحویل می باشد . شما می توانید برای اطلاع از روند مشاهده طرح از طریق سیستم تیکت با شماره پیگیری {extra_data} اقدام نمایید و در صورت تمایل نظرات و تغییرات ارزشمند خود را با همان کد پیگیری ارسال بفرمایید '.PHP_EOL.'با تشکر'.PHP_EOL.'{instagram_link}',
                'params' => '',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'اس ام اس تاییدیه طرح چک لیست ',
                'key' => 'accepted_design',
                'template' => 'ایران تکنولوژی'.PHP_EOL.'همکار محترم'.PHP_EOL.'{customer}'.PHP_EOL.'طرح گرافیکی سایت با موفقیت نهایی گردید و پروژه به واحد ماژول گذاری انتقال یافت.'.PHP_EOL.'با احترام'.PHP_EOL.'{instagram_link}',
                'params' => '',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'اس ام اس چک لیست بخش فنی',
                'key' => 'checklist_technical',
                'template' => 'ایران تکنولوژی'.PHP_EOL.'همکار محترم'.PHP_EOL.'{customer}'.PHP_EOL.'پروژه شما با موفقیت ماژول گذاری گردید و به واحد تست و اشکال زدایی انتقال یافت .'.PHP_EOL.'از صبر و شکیبایی شما سپاسگزاریم'.PHP_EOL.'{instagram_link}',
                'params' => '',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'اس ام اس چک لیست بخش فنی',
                'key' => 'checklist_technical',
                'template' => 'ایران تکنولوژی'.PHP_EOL.'همکار محترم'.PHP_EOL.'{customer}'.PHP_EOL.'پروژه شما با موفقیت ماژول گذاری گردید و به واحد تست و اشکال زدایی انتقال یافت .'.PHP_EOL.'از صبر و شکیبایی شما سپاسگزاریم'.PHP_EOL.'{instagram_link}',
                'params' => '',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'اس ام اس چک لیست بخش پشتیبانی',
                'key' => 'checklist_support',
                'template' => 'ایران تکنولوژی'.PHP_EOL.'همکار محترم'.PHP_EOL.'{customer}'.PHP_EOL.'پروژه شما با موفقیت تست و اشکال زدایی گردید و هم اکنون آماده تحویل می باشد .لطفا منتظر ارتباط همکاران پشتیبانی جهت تحویل و تنظیم زمان آموزش باشید.'.PHP_EOL.'با آرزوی موفقیت'.PHP_EOL.'{instagram_link}',
                'params' => '',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'تحویل چک لیست به کارمندان بخش های بعد',
                'key' => 'checklist-process',
                'template' => 'چک لیست سایت {checklist} {customer} {contract} در لیست کارهای شما قرار گرفت.',
                'params' => '',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

        ]);
    }
}
