<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->insert([
            [
                'id'             => 1 ,
                'title'          => 'create-contract',
                'label'          => 'ایجاد قرارداد' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 2 ,
                'title'          => 'delete-contract',
                'label'          => 'حذف قراردادها' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 3 ,
                'title'          => 'update-contract',
                'label'          => 'ویرایش قراردادها' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 4 ,
                'title'          => 'restore-contract',
                'label'          => 'بازگرداندن قراردادها' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 5 ,
                'title'          => 'update-date-contract',
                'label'          => 'ویرایش زمان قرارداد' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 6 ,
                'title'          => 'create-ancillary',
                'label'          => 'ایجاد زیرقرارد' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 7 ,
                'title'          => 'delete-ancillary',
                'label'          => 'حذف زیرقراردادها' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 8 ,
                'title'          => 'update-ancillary',
                'label'          => 'ویرایش زیرقراردادها' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 9 ,
                'title'          => 'restore-ancillary',
                'label'          => 'بازگردادن زیرقراردادها' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 10 ,
                'title'          => 'update-title-ancillary',
                'label'          => 'ویرایش عنوان زیرقرارداد' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 11 ,
                'title'          => 'create-baseProgress',
                'label'          => 'ایجاد مرحله اصلی' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 12 ,
                'title'          => 'delete-baseProgress',
                'label'          => 'حذف مراحل اصلی' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 13 ,
                'title'          => 'update-baseProgress',
                'label'          => 'ویرایش مراحل اصلی' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 14 ,
                'title'          => 'restore-baseProgress',
                'label'          => 'بازگردادن مراحل اصلی' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 15 ,
                'title'          => 'show-section',
                'label'          => 'مشاهده بخش های مربوطه' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 16 ,
                'title'          => 'show-software',
                'label'          => 'مشاهده نرم افزارها' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 17 ,
                'title'          => 'show-user',
                'label'          => 'نمایش کاربران' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 18 ,
                'title'          => 'create-user',
                'label'          => 'ایجاد کاربر' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 19 ,
                'title'          => 'delete-user',
                'label'          => 'حذف کاربر' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 20 ,
                'title'          => 'restore-user',
                'label'          => 'بازگردادن کاربر' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 21 ,
                'title'          => 'show-role',
                'label'          => 'مشاهده نفش ها' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 22 ,
                'title'          => 'create-role',
                'label'          => 'ایجاد نقش' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 23 ,
                'title'          => 'delete-role',
                'label'          => 'حذف نقش ها' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 24 ,
                'title'          => 'update-role',
                'label'          => 'ویرایش نقش ها' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 25 ,
                'title'          => 'restore-role',
                'label'          => 'بازگرداندن نقش ها' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 26 ,
                'title'          => 'show-permission',
                'label'          => 'مشاهده دسترسی ها' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 27 ,
                'title'          => 'create-permission',
                'label'          => 'ایجاد دسترسی ها' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 28 ,
                'title'          => 'delete-permission',
                'label'          => 'حذف دسترسی ها' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 29 ,
                'title'          => 'update-permission',
                'label'          => 'ویرایش دسترسی ها' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 30 ,
                'title'          => 'restore-permission',
                'label'          => 'بازگرداندن دسترسی ها' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 31 ,
                'title'          => 'change-status-sub-progress',
                'label'          => 'تغییر وضعیت زیرمراحل' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 32 ,
                'title'          => 'change-base-progress-status',
                'label'          => 'تغییر وضعیت مراحل' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 33 ,
                'title'          => 'ancillary-change-base-progress',
                'label'          => 'تغییر مراحل زیرقرارداد' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 34 ,
                'title'          => 'ancillary-change-sub-progress',
                'label'          => 'تغییر زیر مراحل زیرقراردها' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 35 ,
                'title'          => 'update-user',
                'label'          => 'تغییر کاربر' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 36 ,
                'title'          => 'create-contractType',
                'label'          => 'ایجاد نوع قرارداد' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 37 ,
                'title'          => 'delete-contractType',
                'label'          => 'حذف نوع قراردادها' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 38 ,
                'title'          => 'update-contractType',
                'label'          => 'تغییر نوع قرارداد' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 39 ,
                'title'          => 'restore-contractType',
                'label'          => 'بازگردانی نوع قراردادها' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 40 ,
                'title'          => 'show-user-contracts',
                'label'          => 'نمایش قراردادهای کاربر' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 41 ,
                'title'          => 'show-customer-contracts',
                'label'          => 'نمایش قراردادهای مشتری' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 42 ,
                'title'          => 'create-subProgress',
                'label'          => 'ایجاد زیرمرحله' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 43 ,
                'title'          => 'delete-subProgress',
                'label'          => 'حذف زیرمرحله' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 44 ,
                'title'          => 'update-subProgress',
                'label'          => 'تغییر زیرمرحله' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 45 ,
                'title'          => 'restore-subProgress',
                'label'          => 'بازگرداندن زیرمرحله' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 46 ,
                'title'          => 'show-customers',
                'label'          => 'نمایش مشتریان' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 47 ,
                'title'          => 'show-checklist',
                'label'          => 'نمایش چک لیست ها' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 48 ,
                'title'          => 'create-checklist',
                'label'          => 'ایجاد چک لیست' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 49 ,
                'title'          => 'delete-checklist',
                'label'          => 'حذف چک لیست' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 50 ,
                'title'          => 'update-checklist',
                'label'          => 'ویرایش چک لیست ها' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 51 ,
                'title'          => 'restore-checklist',
                'label'          => 'برگرداندن چک لیست' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 52 ,
                'title'          => 'show-titleChecklist',
                'label'          => 'مشاهده عناوین چک لیست' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 53 ,
                'title'          => 'create-titleChecklist',
                'label'          => 'ایجاد عناوین چک لیست' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 54 ,
                'title'          => 'delete-titleChecklist',
                'label'          => 'حذف عناوین چک لیست ها' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 55 ,
                'title'          => 'update-titleChecklist',
                'label'          => 'ویرایش عناوین چک لیست ها' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 56 ,
                'title'          => 'restore-titleChecklist',
                'label'          => 'بازگرداندن عناوین چک لیست' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 57 ,
                'title'          => 'assign-checklist-contract',
                'label'          => 'اختصاص دادن چک لیست به قرارداد' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 58 ,
                'title'          => 'show-contract-title_checklist',
                'label'          => 'مشاهده عناوین چک لیست قرارداد' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 59 ,
                'title'          => 'assign-title-checklist-office',
                'label'          => 'اختصاص دادن کاربر به موارد چک لیست بخش اداری' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 60 ,
                'title'          => 'assign-title-checklist-support',
                'label'          => 'اختصاص دادن کاربر به موارد چک لیست بخش پشتیبانی' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 61 ,
                'title'          => 'assign-title-checklist-design',
                'label'          => 'اختصاص دادن کاربر به موارد چک لیست بخش طراحی' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 62 ,
                'title'          => 'assign-title-checklist-sales',
                'label'          => 'اختصاص دادن کاربر به موارد چک لیست بخش فروش' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 63 ,
                'title'          => 'staff-approving-office',
                'label'          => 'تغییر و تایید موارد چک لیست بخش اداری' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 64 ,
                'title'          => 'staff-approving-programmer',
                'label'          => 'تغییر و تایید موارد چک لیست بخش برنامه نویسی' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 65 ,
                'title'          => 'staff-approving-graphic',
                'label'          => 'تغییر و تایید موارد چک لیست بخش گرافیک' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 66 ,
                'title'          => 'staff-approving-support',
                'label'          => 'تغییر و تایید موارد چک لیست بخش پشتیبانی' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 67 ,
                'title'          => 'staff-approving-sale',
                'label'          => 'تغییر و تایید موارد چک لیست بخش فروش' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 68 ,
                'title'          => 'manager-approving-office',
                'label'          => ' تایید موارد چک لیست مدیر اداری' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 69 ,
                'title'          => 'manager-approving-support',
                'label'          => ' تایید موارد چک لیست مدیر پشتیبانی' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 71 ,
                'title'          => 'manager-approving-sales',
                'label'          => ' تایید موارد چک لیست مدیر فروش' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 72 ,
                'title'          => 'admin-handle-sms-templates',
                'label'          => 'مدیریت اس ام اس ها' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 73 ,
                'title'          => 'technical-manager',
                'label'          => 'مدیر فنی' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 74 ,
                'title'          => 'administrator-manager',
                'label'          => 'مدیر اداری' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 75 ,
                'title'          => 'support-manager',
                'label'          => 'مدیر پشتیبانی' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 76 ,
                'title'          => 'sales-manager',
                'label'          => 'مدیر فروش' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 78 ,
                'title'          => 'programmer',
                'label'          => 'برنامه نویس' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 79 ,
                'title'          => 'graphic',
                'label'          => 'گرافیست' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 80 ,
                'title'          => 'designer',
                'label'          => 'طراح' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 82 ,
                'title'          => 'support',
                'label'          => 'کارمند پشتیبانی' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 83 ,
                'title'          => 'staff-approving-design',
                'label'          => 'تغییر و تایید موارد چک لیست بخش طراحی' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 84 ,
                'title'          => 'support-reverse-design',
                'label'          => 'برگشت طرح توسط پشتیبانی' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 85 ,
                'title'          => 'support-approve-design',
                'label'          => 'تایید طرح توسط پشتیبانی' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 86 ,
                'title'          => 'support-final-approve-design',
                'label'          => 'تایید نهایی طرح توسط پشتیبانی' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 87 ,
                'title'          => 'reverse-to-office',
                'label'          => 'برگشت چک لیست به بخش اداری' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 88 ,
                'title'          => 'reverse-to-programmer',
                'label'          => 'برگشت چک لیست به بخش برنامه نویس' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 89 ,
                'title'          => 'reverse-to-graphic',
                'label'          => 'برگشت چک لیست به بخش گرافیک' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 90 ,
                'title'          => 'reverse-to-support',
                'label'          => 'برگشت چک لیست به بخش پشتیبانی' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 91 ,
                'title'          => 'reverse-to-sale',
                'label'          => 'برگشت چک لیست به بخش فروش',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 92 ,
                'title'          => 'assign-title-checklist-programmer',
                'label'          => 'اختصاص دادن کاربر به موارد چک لیست بخش برنامه نویسی',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 93 ,
                'title'          => 'assign-title-checklist-graphic',
                'label'          => 'اختصاص دادن کاربر به موارد چک لیست بخش گرافیک',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 94 ,
                'title'          => 'manager-approving-programmer',
                'label'          => ' تایید موارد چک لیست مدیر برنامه نویسی',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 95 ,
                'title'          => 'manager-approving-graphic',
                'label'          => ' تایید موارد چک لیست مدیر گرافیک',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 96 ,
                'title'          => 'admin-edit-sections',
                'label'          => 'ویرایش بخش ها',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 97 ,
                'title'          => 'manager-sign-office-checklist',
                'label'          => 'امضای مدیر اداری',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 98 ,
                'title'          => 'manager-sign-designer-checklist',
                'label'          => 'امضای مدیر طراح',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 99 ,
                'title'          => 'manager-sign-programmer-checklist',
                'label'          => 'امضای مدیر برنامه نویس',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 100 ,
                'title'          => 'manager-sign-graphic-checklist',
                'label'          => 'امضای مدیر گرافیک',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 101 ,
                'title'          => 'manager-sign-support-checklist',
                'label'          => 'امضای مدیر پشتیبانی',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 102 ,
                'title'          => 'manager-sign-sale-checklist',
                'label'          => 'امضای مدیر فروش',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 104 ,
                'title'          => 'staff-sign-designer-checklist',
                'label'          => 'امضای کارمند طراح',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 105 ,
                'title'          => 'staff-sign-programmer-checklist',
                'label'          => 'امضای کارمند برنامه نویس',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 106 ,
                'title'          => 'staff-sign-graphic-checklist',
                'label'          => 'امضای کارمند گرافیک',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 107 ,
                'title'          => 'staff-sign-support-checklist',
                'label'          => 'امضای کارمند پشتیبانی',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 108 ,
                'title'          => 'reply-reversed-checklist',
                'label'          => 'پاسخ به چک لیست های برگشت خورده',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 109 ,
                'title'          => 'force-login',
                'label'          => 'وارد شدن ادمین به پروفایل کاربران',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 110 ,
                'title'          => 'show-all-user-todolist',
                'label'          => 'مشاهده کار همه کاربران',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 111 ,
                'title'          => 'show-office-user-todolist',
                'label'          => 'مشاهده کار کارمندان اداری',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 112 ,
                'title'          => 'show-programmer-user-todolist',
                'label'          => 'مشاهده کار کارمندان برنامه نویس',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 113,
                'title'          => 'show-graphic-user-todolist',
                'label'          => 'مشاهده کار کارمندان گرافیست',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 114 ,
                'title'          => 'show-support-user-todolist',
                'label'          => 'مشاهده کار کارمندان پشتیبانی',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 115 ,
                'title'          => 'show-sale-user-todolist',
                'label'          => 'مشاهده کار کارمندان فروش',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 116 ,
                'title'          => 'show-designer-user-todolist',
                'label'          => 'مشاهده کار کارمندان طراح',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 117 ,
                'title'          => 'show-training-session',
                'label'          => 'مشاهده گزارش جلسات آموزش',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 118 ,
                'title'          => 'change-training-session',
                'label'          => 'تغییرو تعریف جلسات آموزش',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 119 ,
                'title'          => 'show-sms-logs',
                'label'          => 'مشاهده گزارش اس ام اس ها',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 120 ,
                'title'          => 'manage-tasks',
                'label'          => 'ایجاد و ویرایش زمان بندی ها',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 121 ,
                'title'          => 'manager-show-requests',
                'label'          => 'مشاهده درخواست ها',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ] ,
            [
                'id'             => 122 ,
                'title'          => 'update-theme-link-contract',
                'label'          => 'ویرایش لینک طرح قرارداد' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
            [
                'id'             => 123 ,
                'title'          => 'admin-show-requests',
                'label'          => 'ادمین همه درخواست ها رو مشاهده کند' ,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now()
            ],
        ]);
    }
}
