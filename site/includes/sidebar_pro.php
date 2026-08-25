<?php
/**
 * SHASHETY PRO — SIDEBAR PRO (قائمة جانبية عامة للصفحة الرئيسية)
 * ملف إضافي بالكامل: لا يُعدّل أي ملف موجود.
 * للتعطيل: احذف سطر require_once الخاص به من index.php فقط.
 *
 * يستبدل قائمة الأقسام القديمة بقائمة موحّدة تضم:
 * الرئيسية • المفضلة • الإشعارات • لوحة التحكم • كل الأقسام (مع بحث فوري)
 * ويعمل من نفس زر القوائم الموجود في الشريط العلوي.
 */
?>
<style>
/* نُخفي لوحة الأقسام القديمة — القائمة الجديدة تحل محلها */
#shsCatMenuPanel,#shsCatMenuOverlay{display:none!important}

/* منع التكرار: أزرار المفضلة والإشعارات ولوحة التحكم صارت داخل القائمة الجانبية */
.nav-actions .nav-btn[title="المفضلة"],
.nav-actions .nav-btn[title="الإشعارات"],
.nav-actions a[href="admin.php"]{display:none!important}

/* نقطة تنبيه على زر القائمة عند وجود إشعارات جديدة */
.shs-catmenu-btn{position:relative}
.shs-catmenu-btn.shsb-alert::after{
  content:'';position:absolute;top:-2px;left:-2px;
  width:9px;height:9px;border-radius:50%;
  background:var(--red,#e50914);
  border:2px solid #0d0d0d;
  box-shadow:0 0 8px rgba(229,9,20,.9);
  animation:shsbPing 1.9s ease-in-out infinite;
}
@keyframes shsbPing{0%,100%{transform:scale(1);opacity:1}50%{transform:scale(1.25);opacity:.75}}

.shsb-ov{
  position:fixed;inset:0;z-index:1200;
  background:rgba(0,0,0,.66);
  -webkit-backdrop-filter:blur(5px) saturate(120%);backdrop-filter:blur(5px) saturate(120%);
  opacity:0;visibility:hidden;transition:opacity .32s ease,visibility .32s;
}
.shsb-ov.open{opacity:1;visibility:visible}

.shsb{
  position:fixed;top:0;right:0;bottom:0;z-index:1210;
  width:min(330px,86vw);
  display:flex;flex-direction:column;
  background:linear-gradient(180deg,rgba(22,22,26,.97),rgba(13,13,16,.98));
  -webkit-backdrop-filter:blur(30px) saturate(180%);backdrop-filter:blur(30px) saturate(180%);
  border-left:1px solid rgba(255,255,255,.07);
  box-shadow:-20px 0 70px rgba(0,0,0,.7);
  transform:translateX(102%);
  transition:transform .42s cubic-bezier(0.22,1,0.36,1);
  direction:rtl;
}
.shsb.open{transform:translateX(0)}

/* ── الرأس ── */
.shsb-head{
  display:flex;align-items:center;gap:11px;
  padding:max(16px,env(safe-area-inset-top)) 18px 14px;
  border-bottom:1px solid rgba(255,255,255,.06);
  background:linear-gradient(180deg,rgba(229,9,20,.10),transparent);
  flex-shrink:0;
}
.shsb-logo{width:38px;height:38px;border-radius:10px;object-fit:cover;flex-shrink:0;border:1px solid rgba(255,255,255,.1)}
.shsb-logo-fb{
  width:38px;height:38px;border-radius:10px;flex-shrink:0;
  display:flex;align-items:center;justify-content:center;
  background:linear-gradient(135deg,var(--red,#e50914),#8b0009);color:#fff;font-weight:900;font-size:1.05rem;
  box-shadow:0 6px 18px rgba(229,9,20,.35);
}
.shsb-brand{flex:1;min-width:0}
.shsb-brand b{display:block;font-size:1rem;font-weight:900;color:#fff;letter-spacing:-.4px;
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.shsb-brand span{display:block;font-size:.68rem;color:var(--text-muted,#707070);margin-top:1px}
.shsb-x{
  width:32px;height:32px;border-radius:9px;flex-shrink:0;
  background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);color:#c9c9c9;
  display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .2s;
}
.shsb-x:hover{background:var(--red,#e50914);border-color:var(--red,#e50914);color:#fff;transform:rotate(90deg)}
.shsb-x svg{width:16px;height:16px}

/* ── الجسم القابل للتمرير ── */
.shsb-body{flex:1;overflow-y:auto;overscroll-behavior:contain;padding:14px 12px 8px}
.shsb-body::-webkit-scrollbar{width:5px}
.shsb-body::-webkit-scrollbar-thumb{background:rgba(255,255,255,.14);border-radius:99px}

.shsb-label{
  display:flex;align-items:center;gap:8px;
  font-size:.64rem;font-weight:800;letter-spacing:2px;color:#5f5f5f;
  text-transform:uppercase;padding:0 8px;margin:6px 0 8px;
}
.shsb-label::after{content:'';flex:1;height:1px;background:linear-gradient(to left,rgba(255,255,255,.09),transparent)}

/* ── عناصر القائمة ── */
.shsb-item{
  position:relative;width:100%;
  display:flex;align-items:center;gap:11px;
  padding:11px 12px;border-radius:11px;
  background:transparent;border:1px solid transparent;
  color:#cfcfcf;font-family:inherit;font-size:.88rem;font-weight:700;
  text-align:right;cursor:pointer;
  transition:background .2s,color .2s,border-color .2s,transform .2s;
}
.shsb-item:hover{background:rgba(255,255,255,.055);color:#fff;border-color:rgba(255,255,255,.07);transform:translateX(-3px)}
.shsb-item.is-active{background:rgba(229,9,20,.13);border-color:rgba(229,9,20,.3);color:#fff}
.shsb-item.is-active::before{
  content:'';position:absolute;right:0;top:50%;transform:translateY(-50%);
  width:3px;height:56%;border-radius:99px;background:var(--red,#e50914);
}
.shsb-ico{
  width:32px;height:32px;flex-shrink:0;border-radius:9px;
  display:flex;align-items:center;justify-content:center;
  background:rgba(255,255,255,.055);border:1px solid rgba(255,255,255,.08);
  color:#b9b9b9;transition:all .22s;
}
.shsb-ico svg{width:16px;height:16px;stroke-width:2}
.shsb-item:hover .shsb-ico{background:rgba(229,9,20,.16);border-color:rgba(229,9,20,.35);color:#ff5560}
.shsb-item.is-active .shsb-ico{background:rgba(229,9,20,.22);border-color:rgba(229,9,20,.45);color:#ff5560}
.shsb-item.shsb-accent .shsb-ico{background:rgba(229,9,20,.16);border-color:rgba(229,9,20,.35);color:#ff5560}
.shsb-txt{flex:1;min-width:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.shsb-cnt{
  font-size:.64rem;font-weight:800;color:#8a8a8a;flex-shrink:0;
  background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);
  padding:2px 7px;border-radius:99px;
}
.shsb-item.is-active .shsb-cnt{color:#ff8a91;background:rgba(229,9,20,.16);border-color:rgba(229,9,20,.28)}
.shsb-dot{
  width:7px;height:7px;border-radius:50%;background:var(--red,#e50914);flex-shrink:0;
  box-shadow:0 0 8px rgba(229,9,20,.8);
}
.shsb-chev{color:#5a5a5a;flex-shrink:0;display:flex}
.shsb-chev svg{width:14px;height:14px}
.shsb-item:hover .shsb-chev{color:#9a9a9a}

.shsb-sep{height:1px;background:rgba(255,255,255,.06);margin:12px 8px}

/* ── بحث الأقسام ── */
.shsb-find{position:relative;margin:0 6px 10px}
.shsb-find input{
  width:100%;padding:10px 36px 10px 12px;
  background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.09);
  border-radius:10px;color:#e8e8e8;font-family:inherit;font-size:.82rem;outline:none;
  transition:border-color .2s,background .2s;
}
.shsb-find input::placeholder{color:#5f5f5f}
.shsb-find input:focus{border-color:rgba(229,9,20,.5);background:rgba(255,255,255,.07)}
.shsb-find svg{
  position:absolute;right:12px;top:50%;transform:translateY(-50%);
  width:15px;height:15px;color:#5f5f5f;pointer-events:none;
}
.shsb-empty{padding:22px 12px;text-align:center;color:#6a6a6a;font-size:.82rem}

/* ظهور متدرّج للعناصر */
.shsb.open .shsb-anim{animation:shsbIn .38s ease both}
@keyframes shsbIn{from{opacity:0;transform:translateX(18px)}to{opacity:1;transform:translateX(0)}}

/* ── التذييل ── */
.shsb-foot{
  flex-shrink:0;padding:12px 18px max(14px,env(safe-area-inset-bottom));
  border-top:1px solid rgba(255,255,255,.06);
  display:flex;align-items:center;justify-content:space-between;gap:10px;
  font-size:.68rem;color:#5a5a5a;
}
.shsb-foot b{color:var(--red,#e50914);font-weight:800}

@media(max-width:520px){
  .shsb{width:min(310px,88vw)}
  .shsb-item{padding:12px 12px;font-size:.9rem}
}
@media (prefers-reduced-motion: reduce){
  .shsb{transition:none}
  .shsb.open .shsb-anim{animation:none}
}
</style>

<script>
(function(){
  'use strict';

  var CFG = {
    name    : <?php echo json_encode($site_name ?? 'SHASHETY PRO', JSON_UNESCAPED_UNICODE); ?>,
    logo    : <?php echo json_encode($site_logo ?? '', JSON_UNESCAPED_UNICODE); ?>,
    admin   : <?php echo (!empty($hide_admin_btn))     ? 'false' : 'true'; ?>,
    favs    : <?php echo (!empty($hide_favorites))     ? 'false' : 'true'; ?>,
    notifs  : <?php echo (!empty($hide_notifications)) ? 'false' : 'true'; ?>
  };

  function hx(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){
    return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}

  var I={
    close:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>',
    home :'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>',
    heart:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>',
    bell :'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>',
    shield:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="M12 8v4M12 16h.01"/></svg>',
    grid :'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>',
    chev :'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>',
    find :'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>'
  };

  var ov,panel,listEl,findEl,cntEl,built=false;

  function catIcon(name){
    try{ if(typeof window.shsCatIconSVG==='function') return window.shsCatIconSVG(name,'16px'); }catch(e){}
    return I.grid;
  }

  function build(){
    if(built)return;
    ov=document.createElement('div');
    ov.className='shsb-ov';ov.id='shsbOv';

    panel=document.createElement('aside');
    panel.className='shsb';panel.id='shsbPanel';
    panel.setAttribute('aria-hidden','true');
    panel.setAttribute('role','navigation');
    panel.setAttribute('aria-label','القائمة الرئيسية');

    var logo = CFG.logo
      ? '<img src="'+hx(CFG.logo)+'" class="shsb-logo" alt="" onerror="this.remove()">'
      : '<div class="shsb-logo-fb">'+hx((CFG.name||'S').trim().charAt(0))+'</div>';

    var quick='';
    quick+=row('home','الرئيسية','',I.home,'accent');
    if(CFG.favs)   quick+=row('favs','المفضلة','',I.heart);
    if(CFG.notifs) quick+=row('notifs','الإشعارات','',I.bell,'','<span class="shsb-dot" id="shsbNotifDot" style="display:none"></span>');
    if(CFG.admin)  quick+=row('admin','لوحة التحكم','',I.shield);

    panel.innerHTML=
      '<div class="shsb-head">'+logo+
        '<div class="shsb-brand"><b>'+hx(CFG.name)+'</b><span>القائمة الرئيسية</span></div>'+
        '<button type="button" class="shsb-x" aria-label="إغلاق">'+I.close+'</button>'+
      '</div>'+
      '<div class="shsb-body">'+
        '<div class="shsb-label shsb-anim">تصفّح</div>'+
        '<div class="shsb-quick">'+quick+'</div>'+
        '<div class="shsb-sep"></div>'+
        '<div class="shsb-label shsb-anim">الأقسام</div>'+
        '<div class="shsb-find shsb-anim">'+I.find+
          '<input type="text" id="shsbFind" placeholder="ابحث في الأقسام…" autocomplete="off">'+
        '</div>'+
        '<div id="shsbList"></div>'+
      '</div>'+
      '<div class="shsb-foot"><span id="shsbCount">—</span><b>'+hx(CFG.name)+'</b></div>';

    document.body.appendChild(ov);
    document.body.appendChild(panel);

    listEl=panel.querySelector('#shsbList');
    findEl=panel.querySelector('#shsbFind');
    cntEl =panel.querySelector('#shsbCount');

    ov.addEventListener('click',close);
    panel.querySelector('.shsb-x').addEventListener('click',close);
    panel.querySelector('.shsb-quick').addEventListener('click',onQuick);
    findEl.addEventListener('input',function(){filter(findEl.value);});
    document.addEventListener('keydown',function(e){
      if(e.key==='Escape'&&panel.classList.contains('open'))close();
    });
    built=true;
  }

  function row(act,txt,cnt,ico,mod,extra){
    return '<button type="button" class="shsb-item shsb-anim'+(mod==='accent'?' shsb-accent':'')+'" data-act="'+act+'">'+
             '<span class="shsb-ico">'+ico+'</span>'+
             '<span class="shsb-txt">'+hx(txt)+'</span>'+
             (cnt?'<span class="shsb-cnt">'+hx(cnt)+'</span>':'')+
             (extra||'')+
             '<span class="shsb-chev">'+I.chev+'</span>'+
           '</button>';
  }

  function onQuick(e){
    var b=e.target.closest('[data-act]');if(!b)return;
    var a=b.dataset.act;
    close();
    setTimeout(function(){
      if(a==='home'){
        try{ if(typeof closeCategoryView==='function') closeCategoryView(); }catch(err){}
        try{
          var sv=document.getElementById('searchViewSection');
          if(sv&&!sv.classList.contains('hidden')&&typeof clearSearchAndGoHome==='function') clearSearchAndGoHome();
        }catch(err){}
        window.scrollTo({top:0,behavior:'smooth'});
      }
      else if(a==='favs'  && typeof toggleFavPanel==='function')   toggleFavPanel();
      else if(a==='notifs'&& typeof toggleNotifPanel==='function') toggleNotifPanel();
      else if(a==='admin') window.location.href='admin.php';
    },230);
  }

  /* ── بناء قائمة الأقسام ── */
  function renderCats(){
    if(!listEl)return;
    var cats=(window.App&&Array.isArray(App.cats))?App.cats:[];
    if(!cats.length){
      listEl.innerHTML='<div class="shsb-empty">لا توجد أقسام متاحة</div>';
      if(cntEl)cntEl.textContent='—';
      return;
    }
    var active=(window.App&&App.currentCategoryView)?String(App.currentCategoryView.id):'';
    listEl.innerHTML=cats.map(function(c){
      var n=(parseInt(c.channel_count||0)+parseInt(c.series_count||0));
      var on=String(c.id)===active;
      return '<button type="button" class="shsb-item shsb-anim'+(on?' is-active':'')+'" '+
               'data-cat="'+hx(c.id)+'" data-name="'+hx(c.name||'')+'">'+
               '<span class="shsb-ico">'+catIcon(c.name)+'</span>'+
               '<span class="shsb-txt">'+hx(c.name||'')+'</span>'+
               (n>0?'<span class="shsb-cnt">'+n+'</span>':'')+
               '<span class="shsb-chev">'+I.chev+'</span>'+
             '</button>';
    }).join('');
    if(cntEl)cntEl.textContent=cats.length+' قسم متاح';

    listEl.onclick=function(e){
      var b=e.target.closest('[data-cat]');if(!b)return;
      var id=b.dataset.cat,nm=b.dataset.name;
      close();
      setTimeout(function(){
        try{ if(typeof openCategoryView==='function') openCategoryView(id,nm); }catch(err){}
      },230);
    };
  }

  function filter(q){
    q=String(q||'').trim().toLowerCase();
    var items=listEl.querySelectorAll('[data-cat]'),shown=0;
    for(var i=0;i<items.length;i++){
      var ok=!q||(items[i].dataset.name||'').toLowerCase().indexOf(q)>-1;
      items[i].style.display=ok?'':'none';
      if(ok)shown++;
    }
    var empty=listEl.querySelector('.shsb-noresult');
    if(!shown){
      if(!empty){
        empty=document.createElement('div');
        empty.className='shsb-empty shsb-noresult';
        empty.textContent='لا يوجد قسم بهذا الاسم';
        listEl.appendChild(empty);
      }
    }else if(empty)empty.remove();
  }

  /* ── فتح / إغلاق ── */
  function open(){
    build();
    renderCats();
    if(findEl){findEl.value='';filter('');}
    ov.classList.add('open');
    panel.classList.add('open');
    panel.setAttribute('aria-hidden','false');
    document.body.style.overflow='hidden';
  }
  function close(){
    if(!built)return;
    ov.classList.remove('open');
    panel.classList.remove('open');
    panel.setAttribute('aria-hidden','true');
    document.body.style.overflow='';
  }

  /* ── ربط بزر القوائم الموجود (استبدال دوال القائمة القديمة) ── */
  window.shsOpenCatMenu  = open;
  window.shsCloseCatMenu = close;
  window.shsCatMenuGoHome= function(){
    close();
    try{ if(typeof closeCategoryView==='function') closeCategoryView(); }catch(e){}
  };
  window.shsbOpenMenu = open;   /* للاستخدام اليدوي عند الحاجة */

  /* ── نقل تنبيه الإشعارات إلى زر القائمة (بعد إخفاء زر الجرس) ── */
  function syncAlert(){
    var badge=document.getElementById('notifBadge');
    var has=!!(badge&&badge.style.display&&badge.style.display!=='none');
    document.querySelectorAll('.shs-catmenu-btn').forEach(function(b){
      b.classList.toggle('shsb-alert',has);
    });
    var dot=document.getElementById('shsbNotifDot');
    if(dot)dot.style.display=has?'':'none';
  }
  function watchAlert(){
    var badge=document.getElementById('notifBadge');
    if(badge){
      try{ new MutationObserver(syncAlert).observe(badge,{attributes:true,attributeFilter:['style']}); }catch(e){}
    }
    syncAlert();
    setInterval(syncAlert,5000);   /* شبكة أمان خفيفة */
  }

  /* مزامنة القسم النشط مع بقية الواجهة */
  (function(){
    var orig=window.setActiveCatNavBtn;
    if(typeof orig!=='function')return;
    window.setActiveCatNavBtn=function(catId){
      var out=orig.apply(this,arguments);
      try{
        if(listEl)listEl.querySelectorAll('[data-cat]').forEach(function(b){
          b.classList.toggle('is-active',String(b.dataset.cat)===String(catId==null?'':catId));
        });
      }catch(e){}
      return out;
    };
  })();

  function init(){build();watchAlert();}
  if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',init);
  else init();
})();
</script>
