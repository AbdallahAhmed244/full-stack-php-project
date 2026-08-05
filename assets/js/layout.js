/*
 * layout.js
 * بيبني الـ Sidebar والـ Topbar لكل صفحة، وبيحافظ على "جلسة الدخول" الوهمية
 * عن طريق تمرير الدور (role) واسم المستخدم (name) في رابط الصفحة نفسها
 * (Query String) بدل استخدام Cookies/LocalStorage، عشان النسخة دي تفتح
 * وتشتغل مباشرة كملفات ثابتة من غير أي سيرفر.
 */

(function () {
  "use strict";

  function getSession() {
    const params = new URLSearchParams(window.location.search);
    const role = params.get("role");
    const name = params.get("name");
    if (!role) return null;
    return { role, name: name ? decodeURIComponent(name) : "" };
  }

  function sessionQuery(session) {
    if (!session) return "";
    return "?role=" + encodeURIComponent(session.role) + "&name=" + encodeURIComponent(session.name || "");
  }

  function withSession(url) {
    const session = getSession();
    if (!session) return url;
    const sep = url.includes("?") ? "&" : "?";
    return url + sep + "role=" + encodeURIComponent(session.role) + "&name=" + encodeURIComponent(session.name || "");
  }

  function requireLogin() {
    const session = getSession();
    if (!session) {
      window.location.href = "login.php";
      return null;
    }
    return session;
  }

  const NAV_ITEMS = {
    admin: [
      { key: "dashboard", label: "لوحة التحكم", icon: "bi-speedometer2", href: "admin-dashboard.html" },
      { key: "appointments", label: "المواعيد", icon: "bi-calendar-week", href: "admin-appointments.html" },
      { key: "patients", label: "المرضى", icon: "bi-person-heart", href: "admin-patients.html" },
      { key: "doctors", label: "الأطباء", icon: "bi-heart-pulse", href: "admin-doctors.html" },
      { key: "machines", label: "الأجهزة", icon: "bi-hdd-rack", href: "admin-machines.html" },
      { key: "centers", label: "المراكز", icon: "bi-hospital", href: "admin-centers.html" },
      { key: "roles", label: "الأدوار", icon: "bi-people", href: "admin-roles.html" },
      { key: "activity", label: "سجل النشاط", icon: "bi-clock-history", href: "admin-activity.html" },
    ],
    doctor: [
      { key: "dashboard", label: "لوحة التحكم", icon: "bi-speedometer2", href: "doctor-dashboard.html" },
      { key: "appointments", label: "مواعيدي", icon: "bi-calendar-check", href: "doctor-appointments.html" },
    ],
    patient: [
      { key: "dashboard", label: "لوحة التحكم", icon: "bi-speedometer2", href: "patient-dashboard.html" },
      { key: "book", label: "حجز موعد جديد", icon: "bi-calendar-plus", href: "patient-book-appointment.html" },
    ],
  };

  function buildSidebar(session, active) {
    const items = NAV_ITEMS[session.role] || [];
    const links = items
      .map((item) => {
        const cls = "nav-link" + (item.key === active ? " active" : "");
        return (
          '<a class="' + cls + '" href="' + withSession(item.href) + '">' +
          '<i class="bi ' + item.icon + ' ms-1"></i> ' + item.label +
          "</a>"
        );
      })
      .join("");

    return (
      '<div class="kc-brand mb-4 fs-4"><i class="bi bi-heart-pulse"></i> RenalCare</div>' +
      '<nav class="nav flex-column gap-1">' +
      links +
      '<button type="button" class="nav-link btn btn-link text-start w-100 px-2 mt-3" onclick="KCLayout.logout()">' +
      '<i class="bi bi-box-arrow-left ms-1"></i> تسجيل الخروج' +
      "</button>" +
      "</nav>"
    );
  }

  const ROLE_LABELS = { admin: "مدير", doctor: "طبيب", patient: "مريض" };

  function buildTopbar(session, title) {
    return (
      '<h1 class="h5 mb-0">' + title + "</h1>" +
      '<div class="text-muted small">' +
      "مرحباً، " + (session.name || "") +
      ' <span class="badge text-bg-light border kc-badge-role">' + (ROLE_LABELS[session.role] || session.role) + "</span>" +
      "</div>"
    );
  }

  function initLayout(options) {
    const session = requireLogin();
    if (!session) return null;

    if (options.role && options.role !== session.role) {
      // الصفحة دي مخصّصة لدور مختلف، رجّعه للوحة تحكمه الصحيحة
      const home = { admin: "admin-dashboard.html", doctor: "doctor-dashboard.html", patient: "patient-dashboard.html" }[session.role] || "login.html";
      window.location.href = withSession(home);
      return null;
    }

    const sidebar = document.getElementById("sidebar");
    const topbar = document.getElementById("topbar");
    if (sidebar) sidebar.innerHTML = buildSidebar(session, options.active);
    if (topbar) topbar.innerHTML = buildTopbar(session, options.title || "");

    // تحديث أي رابط داخلي (data-nav) عشان يفضل حامل بيانات الجلسة
    document.querySelectorAll("a[data-nav]").forEach((a) => {
      a.setAttribute("href", withSession(a.getAttribute("href")));
    });

    return session;
  }

  function logout() {
    window.location.href = "login.html";
  }

  function loginUrlFor(account) {
    return "login.html";
  }

  window.KCLayout = {
    getSession,
    withSession,
    initLayout,
    logout,
    sessionQuery,
  };
})();
