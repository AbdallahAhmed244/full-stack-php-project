/*
 * data.js
 * بيانات تجريبية (Mock Data) في الذاكرة بديلة لقاعدة البيانات وPHP.
 * ملحوظة: البيانات دي مش متخزنة بشكل دائم — أي إضافة/تعديل/حذف بيتنفذ في نفس
 * الصفحة فقط، ولو عملت Refresh هترجع البيانات الأصلية زي ما هي (Demo فقط).
 */

const KC = (function () {
  "use strict";

  // ---------- حسابات تجريبية لتسجيل الدخول ----------
  const demoAccounts = [
    { email: "admin@kidneycare.test", password: "admin123", role: "admin", name: "سارة عبد الله" },
    { email: "doctor@kidneycare.test", password: "doctor123", role: "doctor", name: "د. أحمد حسن" },
    { email: "patient@kidneycare.test", password: "patient123", role: "patient", name: "محمود سعيد" },
  ];

  // ---------- المراكز ----------
  const centers = [
    { id: 1, name_ar: "مركز القاهرة لغسيل الكلى", city_ar: "القاهرة", address_ar: "شارع رمسيس، القاهرة", phone: "0223456789", email: "cairo@kidneycare.test", capacity: 25, is_active: true },
    { id: 2, name_ar: "مركز الإسكندرية الطبي", city_ar: "الإسكندرية", address_ar: "طريق الكورنيش، الإسكندرية", phone: "0334567891", email: "alex@kidneycare.test", capacity: 18, is_active: true },
    { id: 3, name_ar: "مركز الجيزة لأمراض الكلى", city_ar: "الجيزة", address_ar: "شارع الهرم، الجيزة", phone: "0233456780", email: "giza@kidneycare.test", capacity: 12, is_active: false },
  ];

  // ---------- الأطباء ----------
  const doctors = [
    { id: 1, full_name_ar: "د. أحمد حسن", email: "doctor@kidneycare.test", phone: "01012345678", specialty_ar: "أمراض الكلى", license_number: "DR-1042", center_id: 1, gender: "male", bio_ar: "استشاري أمراض كلى بخبرة 12 سنة.", status: "active" },
    { id: 2, full_name_ar: "د. منى إبراهيم", email: "mona.dr@kidneycare.test", phone: "01098765432", specialty_ar: "الغدد الصماء والكلى", license_number: "DR-2087", center_id: 2, gender: "female", bio_ar: "متخصصة في متابعة مرضى الغسيل الدوري.", status: "active" },
    { id: 3, full_name_ar: "د. كريم فتحي", email: "karim.dr@kidneycare.test", phone: "01055512345", specialty_ar: "الباطنة والكلى", license_number: "DR-3311", center_id: 1, gender: "male", bio_ar: "", status: "inactive" },
  ];

  // ---------- المرضى ----------
  const patients = [
    { id: 1, full_name_ar: "محمود سعيد", national_id: "29001011234567", email: "patient@kidneycare.test", phone: "01111222333", date_of_birth: "1990-01-01", gender: "male", blood_type: "O+", primary_doctor_id: 1, preferred_center_id: 1, emergency_contact_name_ar: "منى سعيد", emergency_contact_phone: "01199988877", dialysis_start_date: "2022-05-10", address_ar: "المعادي، القاهرة", notes_ar: "يحتاج متابعة أسبوعية.", status: "active" },
    { id: 2, full_name_ar: "فاطمة الزهراء", national_id: "29505054567890", email: "fatma@kidneycare.test", phone: "01222333444", date_of_birth: "1995-05-05", gender: "female", blood_type: "A-", primary_doctor_id: 2, preferred_center_id: 2, emergency_contact_name_ar: "علي الزهراء", emergency_contact_phone: "01277766655", dialysis_start_date: "2023-02-20", address_ar: "سيدي جابر، الإسكندرية", notes_ar: "", status: "active" },
    { id: 3, full_name_ar: "يوسف عبد الرحمن", national_id: "28812129876543", email: "youssef@kidneycare.test", phone: "01333444555", date_of_birth: "1988-12-12", gender: "male", blood_type: "B+", primary_doctor_id: 1, preferred_center_id: 1, emergency_contact_name_ar: "", emergency_contact_phone: "", dialysis_start_date: "", address_ar: "", notes_ar: "حالة مستقرة.", status: "suspended" },
  ];

  // ---------- الأجهزة ----------
  const machines = [
    { id: 1, machine_number: "M-001", center_id: 1, status: "available", current_patient_name: null, last_maintenance_date: "2026-06-01", model_name: "Fresenius 4008S", notes_ar: "" },
    { id: 2, machine_number: "M-002", center_id: 1, status: "busy", current_patient_name: "محمود سعيد", last_maintenance_date: "2026-05-15", model_name: "Fresenius 4008S", notes_ar: "" },
    { id: 3, machine_number: "M-101", center_id: 2, status: "maintenance", current_patient_name: null, last_maintenance_date: "2026-07-10", model_name: "Nikkiso DBB-27", notes_ar: "قطعة غيار في الطلب." },
  ];

  // ---------- الأدوار ----------
  const roles = [
    { id: 1, slug: "admin", name_ar: "مدير", description_ar: "صلاحيات كاملة على النظام", users_count: 1 },
    { id: 2, slug: "doctor", name_ar: "طبيب", description_ar: "متابعة المرضى وسجلاتهم الطبية", users_count: doctors.length },
    { id: 3, slug: "patient", name_ar: "مريض", description_ar: "حجز الجلسات ومتابعة الحالة", users_count: patients.length },
  ];

  // ---------- سجل النشاط ----------
  const activity = [
    { id: 1, created_at: "2026-07-23 09:12", user_email: "admin@kidneycare.test", action: "patient.create", entity_type: "patient", entity_id: 3, details_ar: "إضافة مريض جديد: يوسف عبد الرحمن", ip_address: "10.0.0.5" },
    { id: 2, created_at: "2026-07-22 17:40", user_email: "doctor@kidneycare.test", action: "machine.update", entity_type: "machine", entity_id: 2, details_ar: "تحديث حالة الجهاز M-002 إلى مشغول", ip_address: "10.0.0.9" },
    { id: 3, created_at: "2026-07-22 11:05", user_email: "admin@kidneycare.test", action: "center.create", entity_type: "center", entity_id: 2, details_ar: "إضافة مركز: مركز الإسكندرية الطبي", ip_address: "10.0.0.5" },
    { id: 4, created_at: "2026-07-21 08:55", user_email: "admin@kidneycare.test", action: "doctor.update", entity_type: "doctor", entity_id: 3, details_ar: "تعطيل حساب د. كريم فتحي", ip_address: "10.0.0.5" },
  ];

  // ---------- المواعيد (نظام حجز حقيقي داخل الصفحة) ----------
  const appointments = [
    { id: 1, patient_id: 1, doctor_id: 1, center_id: 1, date: "2026-07-23", time: "16:00", status: "confirmed", notes_ar: "جلسة غسيل دورية أسبوعية" },
    { id: 2, patient_id: 2, doctor_id: 2, center_id: 2, date: "2026-07-24", time: "11:00", status: "pending", notes_ar: "" },
    { id: 3, patient_id: 1, doctor_id: 1, center_id: 1, date: "2026-07-17", time: "09:00", status: "completed", notes_ar: "جلسة سابقة — تمت بنجاح" },
    { id: 4, patient_id: 3, doctor_id: 1, center_id: 1, date: "2026-07-25", time: "14:00", status: "pending", notes_ar: "" },
    { id: 5, patient_id: 2, doctor_id: 2, center_id: 2, date: "2026-07-10", time: "10:00", status: "cancelled", notes_ar: "المريض ألغى الموعد" },
  ];

  function todayStr() {
    return new Date().toISOString().slice(0, 10);
  }

  function computeStats() {
    const today = todayStr();
    return {
      patients: patients.length,
      doctors: doctors.length,
      centers: centers.length,
      appointments_today: appointments.filter((a) => a.date === today && a.status !== "cancelled").length,
      machines_available: machines.filter((m) => m.status === "available").length,
      machines_busy: machines.filter((m) => m.status === "busy").length,
      machines_maintenance: machines.filter((m) => m.status === "maintenance").length,
      waiting: appointments.filter((a) => a.status === "pending").length,
      pending_appointments: appointments.filter((a) => a.status === "pending").length,
    };
  }

  return {
    demoAccounts,
    centers,
    doctors,
    patients,
    machines,
    roles,
    activity,
    appointments,
    todayStr,
    computeStats,
  };
})();
