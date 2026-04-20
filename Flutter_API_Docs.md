# 📱 Full API Documentation for Flutter App

هذا الملف يحتوي على **جميع** مسارات الـ API (Endpoints) الموجودة في المشروع، منظمة ومجهزة لربطها بتطبيق الـ Flutter.

> **ملاحظة هامة جداً:**
> جميع الـ APIs ترجع البيانات باستخدام `Resources` و `Collections`، ومغلفة بـ `ApiTrait` لضمان توحيد شكل الرد (Response) في كل التطبيق ليكون كالتالي:
> ```json
> {
>   "success": true,
>   "message": "Message here",
>   "data": { ... },
>   "errors": null
> }
> ```

---

## 🟢 1. المصادقة (Authentication) - `Public`
* `POST /api/register` : لإنشاء حساب مريض جديد (يحتاج `name, email, password, password_confirmation`).
* `POST /api/login` : لتسجيل الدخول العادي.
* `POST /api/auth/google` : لتسجيل الدخول بواسطة جوجل.

## 🟢 2. الذكاء الاصطناعي (AI Diagnosis) - `Public`
* `POST /api/ai-diagnosis` : لرفع صورة الجلد وتلقي تشخيص OpenAI.
  * **البيانات المُرسلة (Form-Data):** `image` (ملف), `patient_age`, `patient_gender`, `symptoms`.
* `GET /api/ai-diagnosis/image/{filename}` : مسار آمن لجلب وعرض صورة التشخيص الطبي داخل التطبيق (Flutter `Image.network`).

---

## 🔒 الروابط المحمية (تتطلب `Authorization: Bearer {Token}`)

### 👤 حساب المستخدم (User)
* `POST /api/user` : جلب بيانات المستخدم الحالي.
* `POST /api/logout` : تسجيل الخروج وإلغاء التوكن.

### 🏠 الرئيسية والأعراض (Home & Symptoms)
* `GET /api/` : جلب بيانات الصفحة الرئيسية (Home).
* `GET /api/symptoms` : جلب قائمة الأعراض المتوفرة.

### ⏰ التذكيرات (Reminders)
* `GET /api/reminders` : جلب كل التذكيرات الخاصة بالمريض.
* `POST /api/reminders` : إنشاء تذكير جديد (للدواء مثلاً).
* `GET /api/reminders/upcoming/list` : جلب التذكيرات القادمة قريباً.
* `GET /api/reminders/{id}` : عرض تفاصيل تذكير محدد.
* `PUT /api/reminders/{id}` : تعديل التذكير.
* `DELETE /api/reminders/{id}` : حذف التذكير.
* `POST /api/reminders/{id}/toggle` : تفعيل / إيقاف التذكير.
* `GET /api/reminders/{id}/next-occurrences` : جلب أوقات الحدوث القادمة للتذكير.

### 📋 التقييمات الطبية (Assessments)
* `GET /api/assessments` : جلب قائمة التقييمات الطبية السابقة للمريض.
* `POST /api/assessments` : إضافة تقييم / فحص طبي جديد.
* `GET /api/assessments/{id}` : عرض تفاصيل تقييم معين.
* `DELETE /api/assessments/{id}` : مسح تقييم.
* `GET /api/assessments/stats/statistics` : جلب إحصائيات عامة عن تقييمات المريض.

### 💬 المحادثات والشات (Chat)
* `GET /api/chat/doctors` : جلب قائمة الأطباء وموظفي الدعم المتاحين للشات.
* `GET /api/chat/conversations` : جلب قائمة المحادثات النشطة للمريض.
* `GET /api/chat/{userId}` : جلب رسائل المحادثة بين المريض والطبيب (حسب الـ ID).
* `POST /api/chat/send` : إرسال رسالة.
  * **البيانات (JSON):** `receiver_id`, `message`, `ask_ai` (boolean).
* `PATCH /api/chat/{messageId}/read` : تحديد الرسالة كـ مقروءة.
* `POST /api/chat/typing` : إرسال إشعار بأن المستخدم "يكتب الآن...".

### 📞 تواصل معنا (Contact Us)
* `POST /api/contact-us` : إرسال رسالة أو شكوى للإدارة.
