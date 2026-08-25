<section id="m3u-import" class="sec">
  <div class="shdr"><h1 class="stitle"><?= $t["m3u_import"] ?? "استيراد" ?><span><?= $t["m3u_lists"] ?? "قوائم M3U" ?></span></h1></div>

  <div class="bkgrid" style="margin-bottom:24px">
    <div class="bkc">
      <div class="bkc-title"><i class="fas fa-file-import" style="color:var(--red)"></i><?= $t["m3u_upload"] ?? "رفع قائمة M3U" ?></div>
      <div class="uz" id="m3uDropZone">
        <input type="file" id="m3uFileIn" accept=".m3u,.m3u8" onchange="m3uFileSelected(this)">
        <i class="fas fa-folder-open"></i>
        <h3><?= $t["m3u_drag"] ?? "اسحب وأفلت ملف M3U هنا، أو انقر للاختيار" ?></h3>
        <p><?= $t["m3u_supports"] ?? "يدعم: .m3u, .m3u8" ?> — يتم فحص صحة القائمة قبل الاستيراد</p>
      </div>
      <div id="m3uFileStatus" style="margin-top:10px;font-size:.8rem"></div>
    </div>

    <div class="bkc">
      <div class="bkc-title"><i class="fas fa-link" style="color:var(--red)"></i><?= $t["m3u_url"] ?? "رابط M3U" ?></div>
      <div class="fg" style="margin-bottom:0">
        <label class="fl"><?= $t["m3u_url"] ?? "رابط M3U" ?></label>
        <input type="text" id="m3uUrlIn" class="fi" placeholder="https://yourserver.com/playlist.m3u" style="direction:ltr;text-align:left" onkeydown="if(event.key==='Enter'){event.preventDefault();m3uImportFromUrl()}">
      </div>
      <button type="button" class="btn btn-p" id="m3uUrlBtn" style="width:100%;justify-content:center;margin-top:14px" onclick="m3uImportFromUrl()"><i class="fas fa-arrow-down"></i><?= $t["m3u_import"] ?? "استيراد" ?></button>
      <div id="m3uUrlStatus" style="margin-top:10px;font-size:.8rem"></div>
    </div>
  </div>

  <div class="tw">
    <div class="chdr"><span class="ctitle"><i class="fas fa-list" style="color:var(--red);margin-left:7px"></i><?= $t["m3u_imported"] ?? "القوائم المستوردة" ?></span></div>
    <div id="m3uPlaylistsLoading" style="padding:30px;text-align:center;color:var(--t3)"><span class="sp"></span><?= $t["loading_dots"] ?? "جارٍ التحميل..." ?></div>
    <div id="m3uPlaylistsEmpty" class="empty" style="display:none"><i class="fas fa-file-import"></i><p><?= $t["m3u_none"] ?? "لا توجد قوائم مستوردة بعد" ?></p></div>
    <table id="m3uPlaylistsTbl" style="display:none"><thead><tr><th><?= $t["col_source"] ?? "المصدر" ?></th><th><?= $t["col_type"] ?? "النوع" ?></th><th><?= $t["col_channels_count"] ?? "عدد القنوات" ?></th><th><?= $t["col_import_date"] ?? "تاريخ الاستيراد" ?></th><th><?= $t["col_actions"] ?? "إجراءات" ?></th></tr></thead><tbody id="m3uPlaylistsBody"></tbody></table>
  </div>
</section>

<!-- نافذة تأكيد احترافية لاستيراد ملف M3U -->
<div class="m3u-confirm-overlay" id="m3uConfirmModal" hidden aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="m3uConfirmTitle">
  <div class="m3u-confirm-card" role="document">
    <div class="m3u-confirm-top">
      <div class="m3u-confirm-icon"><i class="fas fa-shield-alt"></i></div>
      <button type="button" class="m3u-confirm-close" id="m3uConfirmClose" aria-label="إغلاق"><i class="fas fa-times"></i></button>
    </div>
    <h2 id="m3uConfirmTitle">تم فحص الملف بنجاح</h2>
    <p class="m3u-confirm-lead" id="m3uConfirmLead">راجِع التفاصيل التالية قبل إضافة القنوات إلى مكتبتك.</p>
    <div class="m3u-confirm-file">
      <i class="fas fa-file-code"></i>
      <span id="m3uConfirmFileName"></span>
    </div>
    <div class="m3u-confirm-stats" aria-label="تفاصيل ملف القائمة">
      <div class="m3u-confirm-stat"><i class="fas fa-database"></i><span>حجم الملف</span><strong id="m3uConfirmFileSize"></strong></div>
      <div class="m3u-confirm-stat"><i class="fas fa-tv"></i><span>القنوات الصالحة</span><strong id="m3uConfirmChannels"></strong></div>
      <div class="m3u-confirm-stat"><i class="fas fa-layer-group"></i><span>الأقسام</span><strong id="m3uConfirmCategories"></strong></div>
    </div>
    <div class="m3u-confirm-note"><i class="fas fa-info-circle"></i><span id="m3uConfirmNote">لن تبدأ عملية الاستيراد إلا بعد موافقتك.</span></div>
    <div class="m3u-confirm-actions">
      <button type="button" class="btn btn-s" id="m3uConfirmCancel"><i class="fas fa-times"></i> إلغاء</button>
      <button type="button" class="btn btn-p" id="m3uConfirmApprove"><i class="fas fa-file-import"></i> بدء الاستيراد</button>
    </div>
  </div>
</div>

<style>
/* واجهة M3U تستخدم متغيرات لوحة الإدارة الأصلية فقط. */
.m3u-confirm-overlay{position:fixed;z-index:10050;inset:0;display:flex;align-items:center;justify-content:center;padding:18px;background:rgba(0,0,0,.62);opacity:0;transition:opacity .18s var(--ease,ease);direction:rtl}.m3u-confirm-overlay[hidden]{display:none}.m3u-confirm-overlay.is-open{opacity:1}
.m3u-confirm-card{width:min(100%,500px);padding:0;overflow:hidden;background:var(--s1,#111);color:var(--t1,#fff);border:1px solid var(--brh,rgba(255,255,255,.14));border-radius:var(--r2,12px);box-shadow:0 18px 48px rgba(0,0,0,.42);transform:translateY(10px);transition:transform .18s var(--ease,ease)}.m3u-confirm-overlay.is-open .m3u-confirm-card{transform:translateY(0)}
.m3u-confirm-top{display:flex;align-items:center;justify-content:space-between;margin:0;padding:16px 18px;border-bottom:1px solid var(--br,rgba(255,255,255,.07));background:var(--s2,#1a1a1a)}.m3u-confirm-icon{display:grid;place-items:center;width:34px;height:34px;border-radius:var(--r1,6px);background:var(--red,#e50914);color:#fff;font-size:.94rem}.m3u-confirm-close{width:32px;height:32px;border:0;border-radius:var(--r1,6px);background:transparent;color:var(--t2,#b3b3b3);cursor:pointer}.m3u-confirm-close:hover{background:var(--s3,#242424);color:var(--t1,#fff)}
.m3u-confirm-card h2{margin:18px 18px 5px;font-size:1.05rem;color:var(--t1,#fff)}.m3u-confirm-lead{margin:0 18px 15px;color:var(--t2,#b3b3b3);font-size:.84rem;line-height:1.65}.m3u-confirm-file{display:flex;align-items:center;gap:9px;min-width:0;margin:0 18px 12px;padding:10px 11px;background:var(--s2,#1a1a1a);border:1px solid var(--br,rgba(255,255,255,.07));border-radius:var(--r1,6px)}.m3u-confirm-file i,.m3u-confirm-stat i,.m3u-confirm-note i{color:var(--red,#e50914)}.m3u-confirm-file span{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:.8rem;direction:ltr;text-align:right}
.m3u-confirm-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin:0 18px}.m3u-confirm-stat{padding:11px 6px;background:var(--s2,#1a1a1a);border:1px solid var(--br,rgba(255,255,255,.07));border-radius:var(--r1,6px);text-align:center}.m3u-confirm-stat i{display:block;margin-bottom:5px;font-size:.85rem}.m3u-confirm-stat span{display:block;color:var(--t3,#737373);font-size:.69rem}.m3u-confirm-stat strong{display:block;margin-top:3px;color:var(--t1,#fff);font-size:.82rem}
.m3u-confirm-note{display:flex;gap:8px;align-items:flex-start;margin:14px 18px 0;padding:9px 10px;background:var(--s2,#1a1a1a);border-radius:var(--r1,6px);color:var(--t2,#b3b3b3);font-size:.76rem;line-height:1.55}.m3u-confirm-note i{margin-top:2px}.m3u-confirm-actions{display:flex;gap:8px;justify-content:flex-end;margin-top:16px;padding:14px 18px;border-top:1px solid var(--br,rgba(255,255,255,.07));background:var(--s2,#1a1a1a)}.m3u-confirm-actions .btn{min-height:36px;font-size:.8rem}.m3u-validation-status{display:flex;align-items:flex-start;gap:8px;padding:10px 11px;border:1px solid var(--br,rgba(255,255,255,.07));border-radius:var(--r1,6px);background:var(--s2,#1a1a1a);color:var(--t2,#b3b3b3);line-height:1.6}.m3u-validation-status i{margin-top:3px;color:var(--red,#e50914)}.m3u-validation-status.is-danger{border-color:var(--red,#e50914);color:var(--t1,#fff)}
@media(max-width:480px){.m3u-confirm-card{border-radius:var(--r1,6px)}.m3u-confirm-actions{flex-direction:column-reverse}.m3u-confirm-actions .btn{width:100%;justify-content:center}}
</style>
<!-- [XTREAM-SECTION-START] قسم حساب Xtream IPTV -->
