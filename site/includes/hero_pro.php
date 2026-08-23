<?php
/**
 * SHASHETY PRO — HERO PRO (بانر رئيسي متحرك + تحسينات تصميم الصفحة الرئيسية)
 * ملف إضافي بالكامل: لا يُعدّل أي ملف موجود.
 * للتعطيل: احذف سطر require_once الخاص به من index.php فقط.
 *
 * يعتمد على البيانات المحمّلة أصلاً في App.allContent (لا طلبات شبكة إضافية).
 */

// مفتاح الإخفاء من لوحة التحكم ← "التحكم بالواجهة الأمامية" ← البوستر المتحرك
if (!empty($hide_hero)) { return; }
?>
<style>
/* ══════════════════════════════════════════════════════════
   HERO PRO — البانر المتحرك
   ══════════════════════════════════════════════════════════ */
.shsx-hero{
  position:relative;width:100%;
  /* يسحب البانر خلف الشريط العلوي — القيمة تُضبط ديناميكياً بـ JS
     لأن ارتفاع الشريط يتغيّر مع شريط الأقسام */
  margin:calc(var(--shsx-top,88px) * -1) 0 26px;
  height:clamp(360px,52vw,560px);
  overflow:hidden;
  background:#0b0b0b;
  isolation:isolate;
  contain:paint;
}
.shsx-hero.shsx-empty{display:none}

.shsx-slide{
  position:absolute;inset:0;
  opacity:0;visibility:hidden;
  transition:opacity .85s var(--ease-out,ease), visibility .85s;
  will-change:opacity;
}
.shsx-slide.is-on{opacity:1;visibility:visible;z-index:2}

/* الخلفية: البوستر مكبّر ومموّه + حركة Ken Burns بطيئة */
.shsx-bg{
  position:absolute;inset:-6%;
  background-size:cover;background-position:center 22%;
  filter:blur(14px) saturate(125%) brightness(.55);
  transform:scale(1.06);
}
.shsx-slide.is-on .shsx-bg{animation:shsxKen 16s ease-out both}
@keyframes shsxKen{from{transform:scale(1.04)}to{transform:scale(1.16)}}

.shsx-scrim{
  position:absolute;inset:0;
  background:
    linear-gradient(to left,rgba(10,10,10,.05) 0%,rgba(10,10,10,.55) 45%,rgba(10,10,10,.94) 88%),
    linear-gradient(to top,var(--bg,#0f0f0f) 0%,rgba(15,15,15,.45) 34%,rgba(0,0,0,.35) 100%);
}

/* اسم العمل بخط ضخم شبحي في الخلفية */
.shsx-ghost{
  position:absolute;right:2.2vw;top:50%;transform:translateY(-50%);
  font-size:clamp(2.6rem,7.5vw,6.4rem);font-weight:900;
  color:rgba(255,255,255,.055);
  letter-spacing:-2px;line-height:.95;
  white-space:nowrap;pointer-events:none;user-select:none;
  max-width:46%;overflow:hidden;text-overflow:ellipsis;
}

.shsx-inner{
  position:relative;z-index:3;height:100%;
  display:flex;align-items:center;gap:clamp(16px,3vw,46px);
  padding:calc(var(--shsx-top,88px) + 16px) clamp(18px,4vw,64px) clamp(28px,4vw,52px);
}
.shsx-copy{flex:1;min-width:0;max-width:640px}
.shsx-slide.is-on .shsx-copy>*{animation:shsxUp .7s var(--ease-out,ease) both}
.shsx-slide.is-on .shsx-copy>*:nth-child(1){animation-delay:.06s}
.shsx-slide.is-on .shsx-copy>*:nth-child(2){animation-delay:.13s}
.shsx-slide.is-on .shsx-copy>*:nth-child(3){animation-delay:.20s}
.shsx-slide.is-on .shsx-copy>*:nth-child(4){animation-delay:.27s}
.shsx-slide.is-on .shsx-copy>*:nth-child(5){animation-delay:.34s}
@keyframes shsxUp{from{opacity:0;transform:translateY(22px)}to{opacity:1;transform:translateY(0)}}

.shsx-tag{
  display:inline-flex;align-items:center;gap:6px;
  background:var(--red,#e50914);color:#fff;
  font-size:.68rem;font-weight:800;
  padding:5px 12px;border-radius:6px;margin-bottom:12px;
  box-shadow:0 6px 18px rgba(229,9,20,.35);
}
.shsx-title{
  font-size:clamp(1.6rem,4.2vw,3.3rem);font-weight:900;color:#fff;
  line-height:1.12;letter-spacing:-1px;margin:0 0 8px;
  text-shadow:0 4px 26px rgba(0,0,0,.75);
  display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;
}
.shsx-sub{font-size:clamp(.82rem,1.4vw,1rem);font-weight:700;color:#e6e6e6;margin-bottom:10px}
.shsx-meta{display:flex;flex-wrap:wrap;align-items:center;gap:8px;margin-bottom:14px}
.shsx-chip{
  font-size:.7rem;font-weight:700;color:#d6d6d6;
  background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.14);
  padding:3px 10px;border-radius:5px;white-space:nowrap;
}
.shsx-chip.shsx-year{color:var(--red,#e50914);background:transparent;border-color:transparent;padding-inline:0;font-weight:800}
.shsx-desc{
  font-size:clamp(.78rem,1.25vw,.93rem);line-height:1.85;color:#c4c4c4;
  margin:0 0 20px;max-width:56ch;
  display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;
}
.shsx-btns{display:flex;flex-wrap:wrap;gap:10px}
.shsx-btn{
  display:inline-flex;align-items:center;gap:8px;
  padding:12px 26px;border-radius:99px;
  font-family:inherit;font-size:.9rem;font-weight:800;cursor:pointer;
  border:1px solid transparent;transition:transform .22s var(--ease-spring,ease),box-shadow .22s,background .22s;
}
.shsx-btn svg{width:1.05em;height:1.05em;flex-shrink:0}
.shsx-btn-play{background:var(--red,#e50914);color:#fff;box-shadow:0 10px 30px rgba(229,9,20,.38)}
.shsx-btn-play:hover{transform:translateY(-2px) scale(1.03);box-shadow:0 14px 38px rgba(229,9,20,.52)}
.shsx-btn-info{background:rgba(255,255,255,.12);color:#fff;border-color:rgba(255,255,255,.2);backdrop-filter:blur(8px)}
.shsx-btn-info:hover{background:rgba(255,255,255,.2);transform:translateY(-2px)}

/* البوستر الجانبي */
.shsx-art{
  flex:0 0 auto;width:clamp(140px,17vw,236px);
  aspect-ratio:2/3;border-radius:14px;overflow:hidden;
  border:1px solid rgba(255,255,255,.12);
  box-shadow:0 26px 70px rgba(0,0,0,.75);
  background:#141414;position:relative;
}
.shsx-art img{width:100%;height:100%;object-fit:cover;display:block}
.shsx-slide.is-on .shsx-art{animation:shsxArtIn .9s var(--ease-out,ease) both .1s}
@keyframes shsxArtIn{from{opacity:0;transform:translateY(26px) scale(.94)}to{opacity:1;transform:translateY(0) scale(1)}}
.shsx-crown{
  position:absolute;top:9px;left:9px;z-index:2;
  background:rgba(0,0,0,.62);border:1px solid rgba(255,255,255,.22);backdrop-filter:blur(6px);
  color:#ffd166;font-size:.6rem;font-weight:800;padding:3px 8px;border-radius:6px;
}

/* الأسهم + النقاط */
.shsx-nav{
  position:absolute;z-index:6;top:50%;transform:translateY(-50%);
  width:40px;height:40px;border-radius:50%;
  background:rgba(0,0,0,.42);border:1px solid rgba(255,255,255,.16);
  color:#fff;display:flex;align-items:center;justify-content:center;
  cursor:pointer;opacity:0;transition:opacity .25s,background .25s,transform .25s;backdrop-filter:blur(6px);
}
.shsx-hero:hover .shsx-nav{opacity:1}
.shsx-nav:hover{background:var(--red,#e50914);border-color:var(--red,#e50914);transform:translateY(-50%) scale(1.08)}
.shsx-nav.shsx-prev{right:12px}
.shsx-nav.shsx-next{left:12px}

.shsx-dots{
  position:absolute;z-index:6;bottom:clamp(14px,2.4vw,26px);
  left:0;right:0;display:flex;justify-content:center;gap:7px;
}
.shsx-dot{
  width:22px;height:4px;border-radius:99px;cursor:pointer;
  background:rgba(255,255,255,.26);border:none;padding:0;
  transition:background .3s,width .3s;
}
.shsx-dot.is-on{background:var(--red,#e50914);width:38px}

/* هيكل تحميل البانر */
.shsx-hero-skel{position:absolute;inset:0;z-index:1}

/* ── الأجهزة اللوحية ── */
@media(max-width:900px){
  .shsx-hero{height:clamp(360px,62vw,460px);margin-bottom:20px}
  .shsx-ghost{display:none}
  .shsx-art{width:clamp(120px,26vw,168px)}
  .shsx-desc{-webkit-line-clamp:2}
  .shsx-nav{display:none}
  .shsx-scrim{background:
    linear-gradient(to left,rgba(10,10,10,.12) 0%,rgba(10,10,10,.68) 55%,rgba(10,10,10,.94) 100%),
    linear-gradient(to top,var(--bg,#0f0f0f) 0%,rgba(15,15,15,.4) 45%,rgba(0,0,0,.4) 100%)}
}

/* ── الهاتف: البوستر يملأ الشاشة والنص أسفله (نمط نتفليكس للجوال) ── */
@media(max-width:660px){
  .shsx-hero{
    height:auto;
    min-height:clamp(440px,118vw,600px);
    margin-bottom:18px;
  }
  /* البوستر يظهر واضحاً بلا تمويه لأنه هو الصورة الرئيسية على الجوال */
  .shsx-bg{
    inset:0;
    filter:none;
    background-position:center 14%;
    transform:none;
  }
  .shsx-slide.is-on .shsx-bg{animation:shsxKenM 20s ease-out both}
  @keyframes shsxKenM{from{transform:scale(1)}to{transform:scale(1.09)}}

  .shsx-scrim{background:linear-gradient(to top,
    var(--bg,#0f0f0f) 0%,
    rgba(12,12,12,.97) 26%,
    rgba(12,12,12,.72) 46%,
    rgba(10,10,10,.28) 70%,
    rgba(0,0,0,.45) 100%)}

  .shsx-inner{
    flex-direction:column;
    justify-content:flex-end;
    align-items:stretch;
    gap:0;
    padding:calc(var(--shsx-top,88px) + 12px) 18px 56px;
    text-align:center;
  }
  .shsx-copy{max-width:100%}
  .shsx-art{display:none}

  .shsx-tag{margin-bottom:10px}
  .shsx-title{font-size:clamp(1.5rem,7.5vw,2.2rem);margin-bottom:10px}
  .shsx-meta{justify-content:center;gap:6px;margin-bottom:12px}
  .shsx-chip{font-size:.66rem;padding:3px 9px}
  .shsx-desc{
    margin:0 auto 18px;font-size:.8rem;line-height:1.75;
    -webkit-line-clamp:3;max-width:36ch;
  }
  .shsx-btns{justify-content:center;gap:9px}
  .shsx-btn{flex:1 1 0;min-width:0;justify-content:center;padding:12px 12px;font-size:.84rem}
  .shsx-dots{bottom:18px}
}

@media(max-width:400px){
  .shsx-hero{min-height:clamp(420px,124vw,540px)}
  .shsx-inner{padding-inline:14px;padding-bottom:52px}
  .shsx-desc{-webkit-line-clamp:2;margin-bottom:16px}
  .shsx-btn{font-size:.78rem;padding:11px 10px;gap:6px}
}

/* ══════════════════════════════════════════════════════════
   تحسينات عامة للصفحة الرئيسية
   ══════════════════════════════════════════════════════════ */

/* شريط علوي شفاف فوق البانر ثم زجاجي عند النزول */
body.shsx-has-hero .navbar:not(.scrolled){
  background:linear-gradient(180deg,rgba(8,8,8,.9) 0%,rgba(8,8,8,.45) 58%,rgba(8,8,8,0) 100%)!important;
  backdrop-filter:blur(8px) saturate(140%)!important;
  -webkit-backdrop-filter:blur(8px) saturate(140%)!important;
  border-bottom-color:transparent;
  box-shadow:none!important;
}
body.shsx-has-hero .cat-navbar{background:linear-gradient(180deg,rgba(10,10,12,.55),rgba(10,10,12,.25))!important}
.nav-logo-text{text-shadow:0 2px 14px rgba(229,9,20,.35)}

/* ترحيب أهدأ (البانر صار هو البطل) */
.hero-welcome{padding:0 clamp(14px,2.4vw,26px);margin-bottom:22px}
.hero-welcome h1{font-size:clamp(1.1rem,1.8vw,1.5rem)}
.hero-welcome p{font-size:.85rem;color:#8d8d8d}

/* عناوين الصفوف */
.slider-header{
  border-right:none;
  padding:0 clamp(12px,2vw,20px);
  margin-bottom:12px;
}
.slider-title{font-size:clamp(.95rem,1.5vw,1.12rem);letter-spacing:-.3px;position:relative}
.slider-title::before{
  content:'';position:absolute;right:-10px;top:50%;transform:translateY(-50%);
  width:3px;height:1.15em;border-radius:99px;
  background:linear-gradient(to bottom,var(--red,#e50914),rgba(229,9,20,.25));
}
.slider-title-icon{
  background:linear-gradient(135deg,rgba(229,9,20,.22),rgba(229,9,20,.06));
  border-color:rgba(229,9,20,.35);
  box-shadow:0 4px 14px rgba(229,9,20,.14);
}
.netflix-slider-row{margin-bottom:36px}

/* بطاقات أنعم */
.ch-card,.sr-card{
  border-radius:clamp(9px,1.2vw,13px);
  box-shadow:0 6px 18px rgba(0,0,0,.5);
  border-color:rgba(255,255,255,.06);
}
.ch-card:hover,.sr-card:hover{
  transform:translateY(-9px) scale(1.045);
  box-shadow:0 20px 46px rgba(0,0,0,.85),0 0 0 1px rgba(229,9,20,.55),0 0 34px rgba(229,9,20,.28);
}
/* تدرّج خفيف أسفل البوستر يفصل الصورة عن الاسم */
.sr-poster::before{
  content:'';position:absolute;inset:auto 0 0 0;height:38%;z-index:2;
  background:linear-gradient(to top,rgba(0,0,0,.6),transparent);
  opacity:.85;pointer-events:none;
}
.ch-info,.sr-info{background:linear-gradient(to bottom,#171717,#131313)}
.ch-play-btn{
  width:44px;height:44px;font-size:1rem;
  background:rgba(229,9,20,.95);
  box-shadow:0 8px 24px rgba(229,9,20,.5);
  transform:translate(-50%,-50%) scale(.75);
  transition:opacity .22s,transform .28s var(--ease-spring,ease);
}
.ch-card:hover .ch-play-btn,.sr-card:hover .ch-play-btn{opacity:1;transform:translate(-50%,-50%) scale(1)}

/* شارة "جديد" على الأعمال المضافة حديثاً */
.shsx-new{
  position:absolute;top:6px;left:6px;z-index:6;
  background:var(--red,#e50914);color:#fff;
  font-size:.52rem;font-weight:900;letter-spacing:.5px;
  padding:2px 6px;border-radius:4px;
  box-shadow:0 4px 12px rgba(229,9,20,.45);
  pointer-events:none;
}

@media (prefers-reduced-motion: reduce){
  .shsx-slide.is-on .shsx-bg{animation:none}
}
</style>

<script>
(function(){
  'use strict';

  /* ── أدوات ── */
  function hx(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){
    return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}
  function safeUrl(u){
    u=String(u||'').trim();
    return /^(https?:)?\/\//i.test(u)||/^\/?[\w./-]+$/.test(u) ? u : '';
  }
  var ICON_PLAY='<svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>';
  var ICON_INFO='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>';
  var ICON_R='<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>';
  var ICON_L='<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>';

  var HERO={items:[],idx:0,timer:null,DELAY:7000,el:null,track:null,dots:null,built:false};

  /* ── بناء الهيكل الفارغ فور تحميل الصفحة ── */
  function mount(){
    var main=document.getElementById('mainContent');
    var welcome=document.getElementById('heroWelcome');
    if(!main||document.getElementById('shsxHero'))return;

    var hero=document.createElement('section');
    hero.className='shsx-hero shsx-empty';
    hero.id='shsxHero';
    hero.setAttribute('aria-label','المحتوى المميز');
    hero.innerHTML=
      '<div class="shsx-track" id="shsxTrack"></div>'+
      '<button type="button" class="shsx-nav shsx-prev" aria-label="السابق">'+ICON_R+'</button>'+
      '<button type="button" class="shsx-nav shsx-next" aria-label="التالي">'+ICON_L+'</button>'+
      '<div class="shsx-dots" id="shsxDots"></div>';
    main.insertBefore(hero,welcome||main.firstChild);

    HERO.el=hero;
    HERO.track=hero.querySelector('#shsxTrack');
    HERO.dots=hero.querySelector('#shsxDots');

    hero.querySelector('.shsx-prev').addEventListener('click',function(){go(HERO.idx-1);restart();});
    hero.querySelector('.shsx-next').addEventListener('click',function(){go(HERO.idx+1);restart();});
    hero.addEventListener('mouseenter',stop);
    hero.addEventListener('mouseleave',restart);

    /* سحب بالإصبع */
    var x0=null;
    hero.addEventListener('touchstart',function(e){x0=e.touches[0].clientX;stop();},{passive:true});
    hero.addEventListener('touchend',function(e){
      if(x0===null)return;
      var dx=e.changedTouches[0].clientX-x0;x0=null;
      if(Math.abs(dx)>45) go(HERO.idx+(dx>0?1:-1));   /* RTL */
      restart();
    },{passive:true});

    document.addEventListener('visibilitychange',function(){document.hidden?stop():restart();});
    fitTop();
    window.addEventListener('resize',fitTop,{passive:true});
    /* ارتفاع الشريط العلوي يتغيّر عند ظهور شريط الأقسام (يُعدَّل padding عبر JS الأصلي) */
    try{
      new MutationObserver(fitTop).observe(main,{attributes:true,attributeFilter:['style']});
    }catch(e){}
  }

  /* يطابق إزاحة البانر مع الحشوة العلوية الفعلية للمحتوى */
  function fitTop(){
    var main=document.getElementById('mainContent');
    if(!main)return;
    var pt=parseFloat(getComputedStyle(main).paddingTop)||88;
    document.documentElement.style.setProperty('--shsx-top',pt+'px');
  }

  /* ── اختيار العناصر المميزة من المحتوى المحمّل ── */
  function pick(){
    var all=(window.App&&Array.isArray(App.allContent))?App.allContent:[];
    var pool=all.filter(function(x){
      if(!x||!x.name)return false;
      if(x.is_active!==undefined&&x.is_active!==null&&parseInt(x.is_active)===0)return false;
      return !!safeUrl(x.poster_url||x.logo_url);
    });
    if(!pool.length)return [];

    /* المسلسلات/الأفلام أولاً لأن بوستراتها رأسية وأجمل في البانر */
    var vod=pool.filter(function(x){return x.globalType==='series';});
    var base=vod.length>=3?vod:pool;

    var seen={},out=[];
    function push(it,tag){
      var k=(it.globalType||'x')+'_'+it.id;
      if(seen[k])return;seen[k]=1;
      it._tag=tag;out.push(it);
    }
    base.slice().sort(function(a,b){return (parseInt(b.views_count||0))-(parseInt(a.views_count||0));})
        .slice(0,4).forEach(function(it){push(it,'الأكثر مشاهدة');});
    base.slice().sort(function(a,b){return (parseInt(b.id||0))-(parseInt(a.id||0));})
        .slice(0,3).forEach(function(it){push(it,'أُضيف حديثاً');});

    return out.slice(0,6);
  }

  /* ── بناء الشرائح ── */
  function build(){
    var items=pick();
    if(!items.length)return false;
    HERO.items=items;

    HERO.track.innerHTML=items.map(function(it,i){
      var img=safeUrl(it.poster_url||it.logo_url);
      var isVod=it.globalType==='series';
      var cat=it.cat_name||'';
      var views=parseInt(it.views_count||0);
      var chips=[];
      if(cat)chips.push('<span class="shsx-chip">'+hx(cat)+'</span>');
      chips.push('<span class="shsx-chip">'+(isVod?'مسلسلات وأفلام':'بث مباشر')+'</span>');
      if(it.quality)chips.push('<span class="shsx-chip">'+hx(it.quality)+'</span>');
      if(views>0)chips.push('<span class="shsx-chip">'+views.toLocaleString('en-US')+' مشاهدة</span>');
      var desc=String(it.description||'').trim()||
        (isVod?'استمتع بمشاهدة «'+it.name+'» بأعلى جودة متاحة وبلا إعلانات، في أي وقت ومن أي جهاز.'
              :'شاهد «'+it.name+'» مباشرةً بجودة عالية وبثّ مستقر على مدار الساعة.');
      return ''+
      '<article class="shsx-slide" data-i="'+i+'">'+
        (img?'<div class="shsx-bg" style="background-image:url(\''+hx(img).replace(/'/g,"%27")+'\')"></div>':'<div class="shsx-bg" style="background:#141414"></div>')+
        '<div class="shsx-scrim"></div>'+
        '<div class="shsx-ghost">'+hx(it.name)+'</div>'+
        '<div class="shsx-inner">'+
          '<div class="shsx-copy">'+
            '<span class="shsx-tag">'+hx(it._tag)+'</span>'+
            '<h2 class="shsx-title">'+hx(it.name)+'</h2>'+
            '<div class="shsx-meta">'+chips.join('')+'</div>'+
            '<p class="shsx-desc">'+hx(desc)+'</p>'+
            '<div class="shsx-btns">'+
              '<button type="button" class="shsx-btn shsx-btn-play" data-act="play" data-i="'+i+'">'+ICON_PLAY+' شاهد الآن</button>'+
              '<button type="button" class="shsx-btn shsx-btn-info" data-act="info" data-i="'+i+'">'+ICON_INFO+' معلومات أكثر</button>'+
            '</div>'+
          '</div>'+
          (img?'<div class="shsx-art"><span class="shsx-crown">'+(i===0?'★ الأول':'مميّز')+'</span><img src="'+hx(img)+'" alt="'+hx(it.name)+'" loading="lazy" decoding="async" onerror="this.parentNode.style.display=\'none\'"></div>':'')+
        '</div>'+
      '</article>';
    }).join('');

    HERO.dots.innerHTML=items.map(function(_,i){
      return '<button type="button" class="shsx-dot" data-i="'+i+'" aria-label="شريحة '+(i+1)+'"></button>';
    }).join('');

    HERO.track.addEventListener('click',onAction);
    HERO.dots.addEventListener('click',function(e){
      var b=e.target.closest('.shsx-dot');if(!b)return;
      go(parseInt(b.dataset.i,10));restart();
    });

    var skel=HERO.el.querySelector('.shsx-hero-skel');
    if(skel)skel.remove();
    HERO.el.classList.remove('shsx-empty');
    document.body.classList.add('shsx-has-hero');
    HERO.built=true;
    fitTop();
    go(0);
    restart();
    return true;
  }

  function onAction(e){
    var b=e.target.closest('[data-act]');if(!b)return;
    var it=HERO.items[parseInt(b.dataset.i,10)];if(!it)return;
    if(b.dataset.act==='play'){
      if(it.globalType==='series'){
        if(typeof window.openSeriesEpisodes==='function')
          window.openSeriesEpisodes(it.id,it.name,it.poster_url||'');
      }else{
        if(typeof window.openPlayerChannel==='function')window.openPlayerChannel(it);
      }
    }else{
      if(typeof window.showTmdbInfoClient==='function')
        window.showTmdbInfoClient(it.name,it.globalType==='series'?'tv':'movie');
    }
  }

  function go(i){
    var n=HERO.items.length;if(!n)return;
    HERO.idx=((i%n)+n)%n;
    var slides=HERO.track.children,dots=HERO.dots.children;
    for(var k=0;k<slides.length;k++)slides[k].classList.toggle('is-on',k===HERO.idx);
    for(var d=0;d<dots.length;d++)dots[d].classList.toggle('is-on',d===HERO.idx);
  }
  function stop(){if(HERO.timer){clearInterval(HERO.timer);HERO.timer=null;}}
  function restart(){
    stop();
    if(HERO.items.length<2||document.hidden)return;
    HERO.timer=setInterval(function(){go(HERO.idx+1);},HERO.DELAY);
  }

  /* ── انتظار وصول المحتوى ثم البناء ── */
  function waitAndBuild(){
    var tries=0;
    var iv=setInterval(function(){
      tries++;
      if(HERO.built||!HERO.el){clearInterval(iv);return;}
      var n=(window.App&&Array.isArray(App.allContent))?App.allContent.length:0;
      if(n>=4||(n>0&&tries>10)){clearInterval(iv);build();return;}
      if(tries>45){ /* لا محتوى صالح — نُخفي البانر بهدوء */
        clearInterval(iv);
        if(HERO.el)HERO.el.remove();
        document.body.classList.remove('shsx-has-hero');
      }
    },400);
  }

  /* ── إخفاء البانر خارج الصفحة الرئيسية (قسم/بحث/حلقات) ── */
  function syncVisibility(){
    var sliders=document.getElementById('netflixStyleSliders');
    if(!sliders)return;
    var apply=function(){
      var hidden=sliders.classList.contains('hidden');
      if(HERO.el)HERO.el.classList.toggle('hidden',hidden);
      document.body.classList.toggle('shsx-has-hero',!hidden&&HERO.built);
      hidden?stop():restart();
    };
    new MutationObserver(apply).observe(sliders,{attributes:true,attributeFilter:['class']});
    apply();
  }

  /* ── شارة "جديد" على البطاقات المضافة حديثاً (تغليف غير مدمِّر) ── */
  function patchCards(){
    var orig=window.renderItemsIntoSliderDOM;
    if(typeof orig!=='function')return;
    var WEEKS=14*24*3600*1000;
    window.renderItemsIntoSliderDOM=function(dom,items,cardType,highlightStr,noCap){
      var out=orig.apply(this,arguments);
      try{
        var list=items||[];
        if(cardType==='channels')list=list.filter(function(it){
          return it.is_active===undefined||it.is_active===null||parseInt(it.is_active)!==0;});
        if(!noCap&&list.length>40)list=list.slice(0,40);
        var cards=dom.children;
        if(cards.length!==list.length)return out;
        var now=Date.now();
        for(var i=0;i<list.length;i++){
          var card=cards[i],it=list[i];
          if(!card||!card.classList||!card.classList.contains('sr-card'))continue;
          if(card.querySelector('.shsx-new'))continue;
          var t=Date.parse(String(it.created_at||'').replace(' ','T'));
          if(!t||isNaN(t)||(now-t)>WEEKS)continue;
          var box=card.querySelector('.sr-poster');
          if(!box)continue;
          var b=document.createElement('span');
          b.className='shsx-new';b.textContent='جديد';
          box.appendChild(b);
        }
      }catch(err){}
      return out;
    };
  }

  /* التغليف يجب أن يسبق أول رسم للبطاقات — نُنفّذه فوراً لا عند DOMContentLoaded */
  patchCards();

  function init(){
    mount();
    syncVisibility();
    waitAndBuild();
  }

  /* عناصر الصفحة (#mainContent) مطبوعة قبل هذا السكربت، فالتنفيذ الفوري آمن */
  if(document.getElementById('mainContent')) init();
  else document.addEventListener('DOMContentLoaded',init);
})();
</script>
