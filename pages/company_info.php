<section id="company-info" class="sec">
  <div class="shdr"><h1 class="stitle"><?= $t["about_word"] ?? "حول" ?><span><?= $t["company_word"] ?? "الشركة" ?></span></h1></div>

  <style>
    /* بطاقات "حول" — الروابط تظهر كأزرار بأسمائها فقط، بلا إظهار العناوين */
    .ci-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:18px}
    @media(max-width:900px){.ci-grid{grid-template-columns:1fr}}
    .ci-card{border-radius:14px;overflow:hidden}
    .ci-name{color:var(--t1);font-size:1.25rem;font-weight:900;margin-bottom:4px;letter-spacing:-.3px}
    .ci-badge{font-size:.7rem;background:rgba(229,9,20,.18);color:#ff6b73;padding:2px 9px;border-radius:20px;vertical-align:middle;font-weight:700}
    .ci-desc{color:var(--t3);font-size:.83rem;line-height:1.7;margin-bottom:16px}
    .ci-rows{display:flex;flex-direction:column;gap:11px}
    .ci-row{display:flex;align-items:center;gap:12px;font-size:.92rem}
    .ci-ic{width:30px;height:30px;flex-shrink:0;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.85rem}
    .ci-lbl{color:var(--t3);font-size:.8rem;min-width:74px}
    .ci-val{color:var(--t1);font-weight:700}
    .ci-tel{direction:ltr;color:var(--t1);font-weight:700;transition:color .2s}
    .ci-tel:hover{color:#00D084}
    .ci-tels{display:flex;flex-direction:column;gap:3px}
    .ci-links{display:flex;flex-wrap:wrap;gap:9px;margin-top:18px;padding-top:16px;border-top:1px solid rgba(255,255,255,.06)}
    /* الزر يعرض اسم المنصة فقط — الرابط داخل href ولا يظهر للمستخدم */
    .ci-btn{
      display:inline-flex;align-items:center;gap:8px;
      padding:9px 16px;border-radius:99px;
      font-size:.82rem;font-weight:700;color:var(--t1);
      background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.09);
      transition:transform .2s,background .2s,border-color .2s;
    }
    .ci-btn i{font-size:.95rem}
    .ci-btn:hover{transform:translateY(-2px)}
    .ci-fb:hover{background:rgba(24,119,242,.18);border-color:rgba(24,119,242,.5);color:#fff}
    .ci-yt:hover{background:rgba(255,0,0,.18);border-color:rgba(255,0,0,.5);color:#fff}
    .ci-wa:hover{background:rgba(37,211,102,.18);border-color:rgba(37,211,102,.5);color:#fff}
    .ci-web:hover{background:rgba(76,201,240,.18);border-color:rgba(76,201,240,.5);color:#fff}
  </style>

  <div class="ci-grid">

    <!-- ══ إدارة الموقع ══ -->
    <div class="card ci-card" style="background:linear-gradient(135deg,rgba(229,9,20,.06),rgba(0,0,0,.22));border-color:rgba(229,9,20,.22)">
      <div class="chdr" style="border-bottom-color:rgba(255,255,255,.05)">
        <span class="ctitle"><i class="fas fa-globe" style="color:var(--red);margin-left:7px"></i><?= $t["site_management"] ?? "إدارة الموقع" ?></span>
      </div>
      <div class="cbody">
        <h3 class="ci-name">SHASHETY PRO <span class="ci-badge"><?= $t["official_version"] ?? "الإصدار الرسمي" ?></span></h3>
        <p class="ci-desc"><?= $t["company_desc"] ?? "نظام إدارة منصات البث المباشر المتقدم المستوحى من تقنيات البث الحديثة." ?></p>

        <div class="ci-rows">
          <div class="ci-row">
            <div class="ci-ic" style="background:rgba(245,166,35,.12);color:var(--gold)"><i class="fas fa-map-marker-alt"></i></div>
            <span class="ci-lbl"><?= $t["country_word"] ?? "الدولة" ?></span>
            <span class="ci-val"><?= $t["country_iraq"] ?? "العراق" ?></span>
          </div>
          <div class="ci-row">
            <div class="ci-ic" style="background:rgba(0,208,132,.12);color:#00D084"><i class="fas fa-phone-alt"></i></div>
            <span class="ci-lbl"><?= $t["contact_number"] ?? "رقم التواصل" ?></span>
            <a href="tel:009647512328848" class="ci-tel">00964 751 232 8848</a>
          </div>
        </div>

        <div class="ci-links">
          <a href="https://www.facebook.com/xxkpq" target="_blank" rel="noopener noreferrer" class="ci-btn ci-fb"><i class="fab fa-facebook-f" style="color:#1877F2"></i>Facebook</a>
          <a href="https://www.youtube.com/@moryanteali9763" target="_blank" rel="noopener noreferrer" class="ci-btn ci-yt"><i class="fab fa-youtube" style="color:#FF0000"></i>YouTube</a>
          <a href="https://wa.me/9647512328848" target="_blank" rel="noopener noreferrer" class="ci-btn ci-wa"><i class="fab fa-whatsapp" style="color:#25D366"></i>WhatsApp</a>
          <a href="https://shashty-pro.netlify.app" target="_blank" rel="noopener noreferrer" class="ci-btn ci-web"><i class="fas fa-globe" style="color:#4CC9F0"></i><?= $t["website_word"] ?? "الموقع" ?></a>
        </div>
      </div>
    </div>

    <!-- ══ الدعم الفني ══ -->
    <div class="card ci-card" style="background:linear-gradient(135deg,rgba(0,208,132,.06),rgba(0,0,0,.22));border-color:rgba(0,208,132,.22)">
      <div class="chdr" style="border-bottom-color:rgba(255,255,255,.05)">
        <span class="ctitle"><i class="fas fa-tools" style="color:#00D084;margin-left:7px"></i><?= $t["tech_support"] ?? "الدعم الفني" ?></span>
      </div>
      <div class="cbody">
        <h3 class="ci-name">Ahmed Saleh Mohamed <span class="ci-badge" style="background:rgba(0,208,132,.18);color:#3ee0a6"><?= $t["support_word"] ?? "دعم فني" ?></span></h3>
        <p class="ci-desc"><?= $t["support_desc"] ?? "للمساعدة التقنية وحل المشكلات ومتابعة التحديثات والتركيب." ?></p>

        <div class="ci-rows">
          <div class="ci-row">
            <div class="ci-ic" style="background:rgba(245,166,35,.12);color:var(--gold)"><i class="fas fa-map-marker-alt"></i></div>
            <span class="ci-lbl"><?= $t["country_word"] ?? "الدولة" ?></span>
            <span class="ci-val"><?= $t["country_egypt"] ?? "مصر" ?></span>
          </div>
          <div class="ci-row" style="align-items:flex-start">
            <div class="ci-ic" style="background:rgba(0,208,132,.12);color:#00D084;margin-top:2px"><i class="fas fa-phone-alt"></i></div>
            <span class="ci-lbl" style="margin-top:4px"><?= $t["contact_numbers"] ?? "أرقام التواصل" ?></span>
            <span class="ci-tels">
              <a href="tel:+201008789101" class="ci-tel">+20 100 878 9101</a>
              <a href="tel:+201204425511" class="ci-tel">+20 120 442 5511</a>
            </span>
          </div>
        </div>

        <div class="ci-links">
          <a href="https://www.facebook.com/sas.egy.2025" target="_blank" rel="noopener noreferrer" class="ci-btn ci-fb"><i class="fab fa-facebook-f" style="color:#1877F2"></i>Facebook</a>
          <a href="https://wa.me/201008789101" target="_blank" rel="noopener noreferrer" class="ci-btn ci-wa"><i class="fab fa-whatsapp" style="color:#25D366"></i>WhatsApp</a>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- ══ FRONTEND CONTROL — التحكم بالواجهة الأمامية (إضافة) ══ -->
<!-- ═══════════════════════════════════════════════════════════════════════
     قسم الإعدادات العامة الحساسة — يتحكم في index.php دون تعديل أي ملف
     كل حقل عليه تعليق يوضح وظيفته. كل شيء يُحفظ في قاعدة البيانات (جدول settings)
     ═══════════════════════════════════════════════════════════════════════ -->
