<?php
/**
 * Netflix Home — طبقة مظهر مستقلة للصفحة الرئيسية.
 * لا تغيّر بنية البطاقات أو منطق جلب المحتوى أو المشغّل.
 */
?>
<style>
/* ═══════════════════════════════════════════════════════════════
   SHASHITY PRO · NETFLIX HOME
   طبقة عرض مستقلة: صفوف أفقية، بانر سينمائي، وهوية داكنة نظيفة.
   ═══════════════════════════════════════════════════════════════ */
:root{--nx-page:#141414;--nx-raised:#1b1b1b;--nx-line:rgba(255,255,255,.09);--nx-soft:rgba(255,255,255,.72)}
body.shs-netflix-home{background:var(--nx-page);color:#f5f5f5}
body.shs-netflix-home::before{content:"";position:fixed;inset:0;z-index:-1;pointer-events:none;background:radial-gradient(80% 48% at 50% -12%,rgba(229,9,20,.10),transparent 68%)}

/* شريط علوي بسيط على طريقة منصات المشاهدة */
body.shs-netflix-home .navbar{min-height:72px;padding-inline:clamp(16px,3vw,52px);background:rgba(20,20,20,.92)!important;border-bottom:1px solid var(--nx-line)!important;box-shadow:none!important;transition:background .25s ease,box-shadow .25s ease}
body.shs-netflix-home .navbar.scrolled{background:rgba(20,20,20,.98)!important;box-shadow:0 8px 28px rgba(0,0,0,.30)!important}
body.shs-netflix-home .nav-logo-text{font-size:clamp(1.05rem,1.65vw,1.45rem);font-weight:900;letter-spacing:-.04em;color:#fff;text-shadow:none}
body.shs-netflix-home .nav-logo-img{filter:drop-shadow(0 3px 9px rgba(0,0,0,.45))}
body.shs-netflix-home .search-wrap{max-width:620px}
body.shs-netflix-home .search-wrap input{height:44px;border-radius:6px;background:rgba(0,0,0,.30);border:1px solid rgba(255,255,255,.20);box-shadow:none}
body.shs-netflix-home .search-wrap input:focus{border-color:#fff;background:rgba(0,0,0,.48);box-shadow:0 0 0 1px #fff}
body.shs-netflix-home .nav-btn,body.shs-netflix-home .shs-catmenu-btn{border-radius:6px;background:transparent;border-color:transparent}
body.shs-netflix-home .nav-btn:hover,body.shs-netflix-home .shs-catmenu-btn:hover{background:rgba(255,255,255,.10);border-color:transparent;color:#fff}

/* اختيار المحتوى في أعلى الصفحة مثل شريط Netflix: بسيط، سريع، والقسم النشط تحته خط أحمر. */
/* اختيار المحتوى — مقاس بعناية كي لا يتزاحم مع البحث، مثل شريط Netflix الحقيقي. */
body.shs-netflix-home .shs-netflix-top-tabs{position:absolute;left:clamp(310px,17.2vw,335px);top:50%;transform:translateY(-50%);z-index:4;display:flex;align-items:center;gap:17px;max-width:265px;overflow-x:auto;scrollbar-width:none;white-space:nowrap;direction:rtl;mask-image:linear-gradient(90deg,transparent 0,#000 10px,#000 calc(100% - 10px),transparent 100%)}
body.shs-netflix-home .shs-netflix-top-tabs::-webkit-scrollbar{display:none}
body.shs-netflix-home .shs-netflix-top-tabs[hidden]{display:none!important}
body.shs-netflix-home .shs-nx-top-tab{appearance:none;position:relative;border:0;background:transparent;color:rgba(255,255,255,.58);padding:10px 0 12px;cursor:pointer;font:750 .78rem/1 system-ui,'Cairo',sans-serif;letter-spacing:-.015em;transition:color .2s ease,opacity .2s ease;white-space:nowrap}
body.shs-netflix-home .shs-nx-top-tab:hover,body.shs-netflix-home .shs-nx-top-tab.is-active{color:#fff}
body.shs-netflix-home .shs-nx-top-tab::after{content:'';position:absolute;right:0;bottom:3px;left:0;height:2px;border-radius:99px;background:#e50914;box-shadow:0 0 10px rgba(229,9,20,.6);transform:scaleX(0);transform-origin:center;transition:transform .22s cubic-bezier(.2,.8,.2,1)}
body.shs-netflix-home .shs-nx-top-tab.is-active::after{transform:scaleX(1)}
@media(min-width:1800px){
  body.shs-netflix-home .navbar .nav-center{width:min(690px,calc(100% - 660px))!important;flex:0 1 min(690px,calc(100% - 660px))!important}
}
/* عند عرض متوسط لا نضغط عناصر الرأس؛ يصبح الاختيار شريطاً مستقلاً وهادئاً. */
@media(min-width:761px) and (max-width:1799px){
  body.shs-netflix-home .shs-netflix-top-tabs{position:fixed;top:84px;right:0;left:0;transform:none;display:flex;max-width:none;height:42px;padding:0 clamp(18px,3vw,42px);gap:22px;background:rgba(14,14,16,.94);border-bottom:1px solid rgba(255,255,255,.08);box-shadow:0 9px 20px rgba(0,0,0,.22);z-index:890;mask-image:none}
  body.shs-netflix-home .shs-nx-top-tab{font-size:.8rem;padding:11px 0 10px}
}

/* البانر هو نقطة التركيز، بخلفية عميقة وتدرجات هادئة */
body.shs-netflix-home .shsx-hero{height:clamp(430px,58vw,650px);margin-bottom:42px;background:#090909}
body.shs-netflix-home .shsx-bg{filter:blur(12px) saturate(1.08) brightness(.60)}
body.shs-netflix-home .shsx-scrim{background:linear-gradient(90deg,var(--nx-page) 0%,rgba(20,20,20,.88) 31%,rgba(20,20,20,.33) 64%,rgba(20,20,20,.03) 100%),linear-gradient(0deg,var(--nx-page) 0%,rgba(20,20,20,.05) 42%,rgba(0,0,0,.28) 100%)}
body.shs-netflix-home .shsx-inner{max-width:1480px;margin-inline:auto;padding-inline:clamp(20px,5.2vw,84px)}
body.shs-netflix-home .shsx-copy{max-width:min(620px,52vw)}
body.shs-netflix-home .shsx-tag{padding:5px 10px;border-radius:4px;letter-spacing:.03em;background:#e50914;box-shadow:none}
body.shs-netflix-home .shsx-title{font-size:clamp(2rem,4.35vw,4.25rem);letter-spacing:-.055em;line-height:1.02;text-wrap:balance}
body.shs-netflix-home .shsx-desc{font-size:clamp(.86rem,1.15vw,1rem);color:#e2e2e2;line-height:1.85}
body.shs-netflix-home .shsx-chip{background:transparent;border-color:rgba(255,255,255,.34);color:#e8e8e8;border-radius:3px}
body.shs-netflix-home .shsx-btn{border-radius:4px;padding:12px 22px;min-height:44px;box-shadow:none}
body.shs-netflix-home .shsx-btn-play{background:#fff;color:#121212}
body.shs-netflix-home .shsx-btn-play:hover{background:#e9e9e9;box-shadow:none}
body.shs-netflix-home .shsx-btn-info{background:rgba(109,109,110,.70);border-color:transparent}
body.shs-netflix-home .shsx-btn-info:hover{background:rgba(109,109,110,.95)}
body.shs-netflix-home .shsx-art{width:clamp(150px,16.5vw,244px);border-radius:5px;border-color:rgba(255,255,255,.16);box-shadow:0 26px 60px rgba(0,0,0,.62)}
body.shs-netflix-home .shsx-dots{justify-content:flex-start;padding-inline:clamp(20px,5.2vw,84px)}
body[dir="rtl"].shs-netflix-home .shsx-dots{justify-content:flex-end}
body.shs-netflix-home .shsx-dot{width:7px;height:7px;border-radius:50%;background:rgba(255,255,255,.42)}
body.shs-netflix-home .shsx-dot.is-on{width:22px;border-radius:99px;background:#e50914}
body.shs-netflix-home.shsx-has-hero .hero-welcome{display:none}

/* صفوف Netflix حقيقية: تمرير أفقي مع بطاقة بوستر ثابتة. */
body.shs-netflix-home #netflixStyleSliders{padding-bottom:14px}
body.shs-netflix-home .netflix-slider-row{margin-bottom:48px;contain:layout style paint}
body.shs-netflix-home .slider-header{padding:0 clamp(16px,3.2vw,52px);margin-bottom:13px;border:0}
body.shs-netflix-home .slider-title{font-size:clamp(1rem,1.55vw,1.28rem);font-weight:800;letter-spacing:-.025em}
body.shs-netflix-home .slider-title::before{display:none}
body.shs-netflix-home .slider-title-icon{width:30px;height:30px;background:transparent;border:0;box-shadow:none;font-size:1rem}
body.shs-netflix-home .slider-badge{margin-inline-start:3px;background:transparent;border:0;color:#aaa;font-size:.72rem;padding:0}
body.shs-netflix-home .slider-scroll-mask{overflow:visible}
body.shs-netflix-home .slider-cards-wrapper{display:flex!important;grid-template-columns:none!important;gap:9px!important;overflow-x:auto!important;overflow-y:visible!important;padding:8px clamp(16px,3.2vw,52px) 18px!important;scroll-snap-type:x proximity;scroll-padding-inline:clamp(16px,3.2vw,52px);scrollbar-width:none;overscroll-behavior-inline:contain;-webkit-overflow-scrolling:touch;direction:ltr}
body.shs-netflix-home .slider-cards-wrapper::-webkit-scrollbar{display:none}
body.shs-netflix-home .slider-cards-wrapper>.ch-card,body.shs-netflix-home .slider-cards-wrapper>.sr-card{flex:0 0 clamp(132px,11.6vw,205px);min-width:0;scroll-snap-align:start;transform-origin:center center}
body.shs-netflix-home .slider-cards-wrapper>.skeleton{flex:0 0 clamp(132px,11.6vw,205px);height:auto!important;padding-bottom:0!important;aspect-ratio:2/3;border-radius:4px!important}
body.shs-netflix-home .ch-card,body.shs-netflix-home .sr-card{border-radius:4px!important;background:#1a1a1a;border-color:transparent!important;box-shadow:none!important;transition:transform .24s ease,box-shadow .24s ease,z-index 0s linear .24s!important}
body.shs-netflix-home .ch-thumb,body.shs-netflix-home .sr-poster{border-radius:4px 4px 0 0;background:#171717!important}
/* عنوان المحتوى لا يجب أن يختفي في بطاقات الرئيسية: نعيده في مساحة صغيرة
   أسفل الصورة مع صف الأدوات، كي يعرف المستخدم اسم القناة أو الفيلم فوراً. */
body.shs-netflix-home .slider-cards-wrapper>.ch-card .ch-info,
body.shs-netflix-home .slider-cards-wrapper>.sr-card .sr-info{display:block!important;padding:8px 9px 9px!important;min-height:68px;background:linear-gradient(180deg,#1d1d1f,#151517)}
body.shs-netflix-home .slider-cards-wrapper>.ch-card .ch-name,
body.shs-netflix-home .slider-cards-wrapper>.sr-card .sr-name{display:-webkit-box!important;height:2.75em;margin-bottom:6px;font-size:clamp(.72rem,.85vw,.88rem);font-weight:800;color:#f5f5f5;line-height:1.38}
body.shs-netflix-home .slider-cards-wrapper>.ch-card .ch-info>div:last-child,
body.shs-netflix-home .slider-cards-wrapper>.sr-card .sr-info>div:last-child{justify-content:flex-end;min-height:25px}
body.shs-netflix-home .ch-card:hover,body.shs-netflix-home .sr-card:hover{transform:scale(1.085)!important;box-shadow:0 18px 36px rgba(0,0,0,.62)!important;z-index:20;transition-delay:.05s!important}
body.shs-netflix-home .ch-card:hover .ch-thumb::after,body.shs-netflix-home .sr-card:hover .sr-poster::after{background:rgba(0,0,0,.20)}
body.shs-netflix-home .ch-play-btn{width:48px!important;height:48px!important;border:2px solid rgba(255,255,255,.92);background:rgba(0,0,0,.58);box-shadow:none!important}
body.shs-netflix-home .ch-live-badge{top:7px!important;right:7px!important;padding:3px 6px!important;border-radius:3px!important;background:#e50914;box-shadow:none!important}
body.shs-netflix-home .ch-fmt-badge{top:7px!important;left:7px!important;background:rgba(0,0,0,.70)}
body.shs-netflix-home .ch-quality-badge{bottom:7px!important;right:7px!important;background:rgba(0,0,0,.72)}
body.shs-netflix-home .shs-row-arrow{width:64px;background:linear-gradient(90deg,rgba(20,20,20,.94),rgba(20,20,20,0))!important}
body.shs-netflix-home .shs-row-arrow.shs-right{background:linear-gradient(270deg,rgba(20,20,20,.94),rgba(20,20,20,0))!important}
body.shs-netflix-home .shs-row-arrow:hover{background-color:rgba(20,20,20,.98)!important}

/* صفحات القسم والبحث تبقى شبكة واضحة حتى لا تتغير وظائف التصفية. */
body.shs-netflix-home #categoryViewSection,body.shs-netflix-home #searchViewSection,body.shs-netflix-home #epSection{max-width:1560px;margin-inline:auto;padding-inline:clamp(16px,3.2vw,52px)!important}
body.shs-netflix-home .channels-row{gap:12px!important}
body.shs-netflix-home .channels-row .ch-card,body.shs-netflix-home .channels-row .sr-card{border-radius:5px!important;background:#1a1a1a}
body.shs-netflix-home .back-btn{border-radius:4px;background:rgba(255,255,255,.10);border:0;color:#fff}
body.shs-netflix-home .back-btn:hover{background:#e50914;color:#fff}

/* إحساس مؤسساتي هادئ في التذييل والقوائم الجانبية. */
body.shs-netflix-home .site-footer{margin-top:46px;background:#0d0d0d;border-top:1px solid var(--nx-line)}
body.shs-netflix-home .site-footer__inner{max-width:1360px}
body.shs-netflix-home .shs-catmenu-panel{background:#141414;border-left-color:var(--nx-line)}
body.shs-netflix-home .shs-catmenu-item.active{background:rgba(255,255,255,.10);color:#fff}
body.shs-netflix-home .shs-catmenu-title::before{background:#e50914}

@media(max-width:760px){
  body.shs-netflix-home .navbar{min-height:64px;padding-inline:14px}
  body.shs-netflix-home .shs-netflix-top-tabs{position:fixed;top:var(--navbar-h,64px);right:0;left:0;transform:none;display:flex;max-width:none;height:42px;padding:0 16px;gap:18px;background:rgba(20,20,20,.94);border-bottom:1px solid rgba(255,255,255,.08);box-shadow:0 8px 18px rgba(0,0,0,.24);z-index:890;overflow-x:auto;justify-content:flex-start}
  body.shs-netflix-home .shs-nx-top-tab{font-size:.78rem;padding:11px 0 10px}
  body.shs-netflix-home .search-wrap input{height:40px}
  body.shs-netflix-home .shsx-hero{height:auto;min-height:calc(100svh - 64px);margin-bottom:30px}
  body.shs-netflix-home .shsx-bg{filter:none;background-position:center top;transform:none}
  body.shs-netflix-home .shsx-scrim{background:linear-gradient(0deg,#141414 0%,rgba(20,20,20,.96) 27%,rgba(20,20,20,.34) 70%,rgba(0,0,0,.28) 100%)}
  body.shs-netflix-home .shsx-inner{padding:100px 18px 58px;text-align:center;align-items:stretch}
  body.shs-netflix-home .shsx-copy{max-width:none}
  body.shs-netflix-home .shsx-title{font-size:clamp(1.8rem,8vw,2.55rem)}
  body.shs-netflix-home .shsx-meta,body.shs-netflix-home .shsx-btns{justify-content:center}
  body.shs-netflix-home .shsx-art{display:none}
  body.shs-netflix-home .shsx-dots{justify-content:center!important;padding:0}
  body.shs-netflix-home .netflix-slider-row{margin-bottom:32px}
  body.shs-netflix-home .slider-header{padding-inline:16px;margin-bottom:8px}
  body.shs-netflix-home .slider-cards-wrapper{gap:7px!important;padding:7px 16px 16px!important;scroll-padding-inline:16px}
  body.shs-netflix-home .slider-cards-wrapper>.ch-card,body.shs-netflix-home .slider-cards-wrapper>.sr-card,body.shs-netflix-home .slider-cards-wrapper>.skeleton{flex-basis:clamp(118px,39vw,158px)}
  body.shs-netflix-home .ch-card:hover,body.shs-netflix-home .sr-card:hover{transform:none!important}
  body.shs-netflix-home .ch-play-btn{opacity:1!important;transform:translate(-50%,-50%) scale(.78)!important;width:40px!important;height:40px!important}
}

/* شريط Netflix موحّد: اختيار نوع المحتوى ثم الأقسام التابعة له في المسار نفسه. */
body.shs-netflix-home .shs-netflix-top-tabs{position:fixed!important;top:var(--navbar-h,84px)!important;right:0!important;left:0!important;transform:none!important;display:flex!important;align-items:center!important;gap:18px!important;max-width:none!important;height:48px!important;padding:0 clamp(16px,3vw,52px)!important;background:linear-gradient(90deg,rgba(9,9,11,.98),rgba(18,18,20,.94),rgba(9,9,11,.98))!important;border-bottom:1px solid rgba(255,255,255,.08)!important;box-shadow:0 10px 22px rgba(0,0,0,.26)!important;z-index:890!important;overflow-x:auto!important;overflow-y:hidden!important;scrollbar-width:none!important;mask-image:none!important;direction:rtl!important}
body.shs-netflix-home .shs-netflix-top-tabs::-webkit-scrollbar{display:none}
body.shs-netflix-home .shs-nx-top-tab{flex:0 0 auto;font-size:.8rem!important;padding:14px 0 13px!important;color:rgba(255,255,255,.68)!important}
body.shs-netflix-home .shs-nx-top-tab.is-active{color:#fff!important}
body.shs-netflix-home .shs-nx-top-tab::after{bottom:7px!important}
body.shs-netflix-home .shs-nx-top-divider{flex:0 0 auto;width:1px;height:20px;background:rgba(255,255,255,.18);margin:0 2px}
body.shs-netflix-home .shs-nx-top-category{flex:0 0 auto;appearance:none;border:0;background:transparent;color:rgba(255,255,255,.52);padding:7px 1px;cursor:pointer;font:650 .76rem/1 system-ui,'Cairo',sans-serif;white-space:nowrap;transition:color .2s ease}
body.shs-netflix-home .shs-nx-top-category:hover{color:#fff}
body.shs-netflix-home .shs-nx-top-all{flex:0 0 auto;appearance:none;display:inline-flex;align-items:center;gap:6px;border:1px solid rgba(229,9,20,.52);border-radius:999px;background:rgba(229,9,20,.10);color:#fff;padding:6px 10px;cursor:pointer;font:750 .74rem/1 system-ui,'Cairo',sans-serif;white-space:nowrap;transition:background .2s ease,border-color .2s ease}
body.shs-netflix-home .shs-nx-top-all:hover{background:#e50914;border-color:#e50914}
@media(max-width:760px){
  body.shs-netflix-home .shs-netflix-top-tabs{height:43px!important;gap:15px!important;padding-inline:14px!important}
  body.shs-netflix-home .shs-nx-top-tab{font-size:.76rem!important;padding:12px 0 11px!important}
  body.shs-netflix-home .shs-nx-top-tab::after{bottom:5px!important}
  body.shs-netflix-home .shs-nx-top-category{font-size:.72rem!important}
}

/* سطح مكتب: رأس واحد مدمج — شعار، بحث صغير، مجموعات، ثم كل الأقسام في المسار نفسه. */
@media(min-width:1000px){
  body.shs-netflix-home .navbar{
    height:72px!important;min-height:72px!important;padding:0 52px!important;
    display:grid!important;grid-template-columns:max-content minmax(230px,330px) minmax(0,1fr) max-content;
    grid-template-areas:'brand search tabs actions';gap:22px;align-items:center!important;
  }
  body.shs-netflix-home .navbar .nav-brand{position:static!important;grid-area:brand;left:auto!important;right:auto!important;margin:0!important}
  body.shs-netflix-home .navbar .nav-center{position:static!important;grid-area:search;width:auto!important;min-width:0!important;flex:none!important;margin:0!important}
  body.shs-netflix-home .navbar .search-wrap{width:100%!important;max-width:330px!important;margin:0!important}
body.shs-netflix-home .navbar .search-wrap input{height:38px!important;padding:8px 40px 8px 14px!important;border-radius:999px!important;font-size:.8rem!important}
  body.shs-netflix-home .navbar .nav-actions{position:static!important;grid-area:actions;right:auto!important;left:auto!important;margin:0!important;gap:5px!important}
  body.shs-netflix-home .navbar .nav-btn,body.shs-netflix-home .navbar .shs-catmenu-btn{width:38px!important;height:38px!important}
  body.shs-netflix-home .shs-netflix-top-tabs{
    position:static!important;grid-area:tabs;transform:none!important;right:auto!important;left:auto!important;top:auto!important;
    width:100%!important;max-width:none!important;height:42px!important;padding:0!important;gap:15px!important;
    background:transparent!important;border:0!important;box-shadow:none!important;z-index:auto!important;
    justify-content:flex-start!important;direction:rtl!important;
  }
  body.shs-netflix-home .shs-nx-top-tab{font-size:.78rem!important;padding:12px 0 11px!important}
  body.shs-netflix-home .shs-nx-top-tab::after{bottom:4px!important}
  body.shs-netflix-home .shs-nx-top-category{font-size:.73rem!important}
  body.shs-netflix-home .shs-nx-top-divider{height:18px!important}
}

/* طبقة Premium للرأس: تمييز واضح للمحتوى مع بقاء الشريط خفيفاً وسريعاً. */
body.shs-netflix-home .navbar{
  background:
    radial-gradient(440px 130px at 25% -90%,rgba(0,166,255,.20),transparent 70%),
    radial-gradient(460px 150px at 75% -85%,rgba(229,9,20,.18),transparent 72%),
    linear-gradient(110deg,rgba(7,9,13,.97),rgba(17,18,23,.96) 52%,rgba(8,9,12,.98))!important;
  border-bottom-color:rgba(255,255,255,.09)!important;
  box-shadow:0 12px 34px rgba(0,0,0,.30),inset 0 -1px rgba(255,255,255,.025)!important;
}
body.shs-netflix-home .nav-logo-text{letter-spacing:-.055em!important;text-shadow:0 2px 16px rgba(255,255,255,.12)!important}
body.shs-netflix-home .search-wrap input{
  background:linear-gradient(135deg,rgba(255,255,255,.10),rgba(255,255,255,.045))!important;
  border:1px solid rgba(255,255,255,.15)!important;
  box-shadow:inset 0 1px rgba(255,255,255,.08),0 7px 20px rgba(0,0,0,.20)!important;
  transition:border-color .2s ease,box-shadow .2s ease,background .2s ease!important;
}
body.shs-netflix-home .search-wrap input:focus{
  background:rgba(10,14,24,.88)!important;border-color:rgba(83,168,255,.76)!important;
  box-shadow:0 0 0 4px rgba(35,128,255,.13),0 10px 26px rgba(0,0,0,.28)!important;
}
body.shs-netflix-home .shs-netflix-top-tabs{scroll-snap-type:x proximity;scroll-padding-inline:18px}
/* شريط الاستكشاف مستقل عن محتوى الصفحة الرئيسية: يظل متاحاً داخل كل قسم. */
body.shs-netflix-home .shs-netflix-top-tabs:not([hidden]),
body.shs-netflix-home .shs-nx-scroll-controls:not([hidden]){visibility:visible!important;opacity:1!important}
body.shs-netflix-home .shs-nx-top-tab,
body.shs-netflix-home .shs-nx-top-category,
body.shs-netflix-home .shs-nx-top-all{scroll-snap-align:center}
body.shs-netflix-home .shs-nx-top-tab{
  border:1px solid transparent!important;border-radius:999px!important;padding:8px 11px!important;
  color:rgba(255,255,255,.72)!important;transition:color .2s ease,background .2s ease,border-color .2s ease,transform .2s ease!important;
}
body.shs-netflix-home .shs-nx-top-tab::after{right:12px!important;left:12px!important;bottom:2px!important}
body.shs-netflix-home .shs-nx-top-tab:hover{color:#fff!important;background:rgba(255,255,255,.075)!important;border-color:rgba(255,255,255,.10)!important}
body.shs-netflix-home .shs-nx-top-tab.is-active{
  color:#fff!important;background:linear-gradient(135deg,rgba(229,9,20,.24),rgba(229,9,20,.06))!important;
  border-color:rgba(229,9,20,.36)!important;box-shadow:0 7px 18px rgba(229,9,20,.13)!important;
}
body.shs-netflix-home .shs-nx-top-category{
  display:inline-flex!important;align-items:center!important;gap:6px!important;padding:6px 9px!important;border:1px solid rgba(255,255,255,.09)!important;border-radius:999px!important;
  background:rgba(255,255,255,.035)!important;color:rgba(255,255,255,.66)!important;
  box-shadow:inset 0 1px rgba(255,255,255,.035)!important;transition:color .18s ease,background .18s ease,border-color .18s ease,transform .18s ease!important;
}
body.shs-netflix-home .shs-nx-top-category:hover{color:#fff!important;background:rgba(255,255,255,.10)!important;border-color:rgba(255,255,255,.21)!important;transform:translateY(-1px)}
body.shs-netflix-home .shs-nx-category-icon{width:17px;height:17px;display:grid;place-items:center;border-radius:6px;background:linear-gradient(135deg,rgba(91,181,255,.24),rgba(229,9,20,.16));color:#cfe7ff;font-size:.64rem;line-height:1;box-shadow:inset 0 1px rgba(255,255,255,.15)}
body.shs-netflix-home .shs-nx-category-icon svg{width:13px!important;height:13px!important;display:block}
body.shs-netflix-home .shs-nx-top-category:hover .shs-nx-category-icon{background:linear-gradient(135deg,#319fff,#e50914);color:#fff;box-shadow:0 0 12px rgba(74,158,255,.25)}
body.shs-netflix-home .shs-nx-top-all{
  padding:7px 12px!important;border-color:rgba(229,9,20,.64)!important;
  background:linear-gradient(135deg,#f01422,#b40611)!important;box-shadow:0 8px 18px rgba(229,9,20,.24)!important;
}
body.shs-netflix-home .shs-nx-top-all:hover{background:linear-gradient(135deg,#ff3140,#d60716)!important;transform:translateY(-1px)}
body.shs-netflix-home .shs-nx-all-icon{width:16px;height:16px;display:grid;place-items:center;color:#fff}
body.shs-netflix-home .shs-nx-all-icon svg{width:16px;height:16px;display:block;filter:drop-shadow(0 1px 3px rgba(0,0,0,.28))}
body.shs-netflix-home .shs-nx-scroll-controls{display:inline-flex;align-items:center;gap:4px;flex:0 0 auto;padding:3px;border:1px solid rgba(255,255,255,.10);border-radius:999px;background:rgba(255,255,255,.035);box-shadow:inset 0 1px rgba(255,255,255,.055)}
body.shs-netflix-home .shs-nx-scroll-btn{appearance:none;width:25px;height:25px;display:grid;place-items:center;border:0;border-radius:50%;background:transparent;color:rgba(255,255,255,.83);cursor:pointer;font:900 1.35rem/.8 system-ui;transition:background .18s ease,color .18s ease,transform .18s ease}
body.shs-netflix-home .shs-nx-scroll-btn:hover{background:rgba(255,255,255,.16);color:#fff;transform:scale(1.08)}
body.shs-netflix-home .shs-nx-scroll-btn:focus-visible{outline:2px solid #63b3ff;outline-offset:2px}
@keyframes shs-nx-category-edge{0%,100%{border-color:rgba(92,180,255,.17);box-shadow:inset 0 1px rgba(255,255,255,.035),0 0 0 rgba(35,128,255,0)}50%{border-color:rgba(229,9,20,.42);box-shadow:inset 0 1px rgba(255,255,255,.08),0 0 13px rgba(229,9,20,.10)}}
body.shs-netflix-home .shs-nx-top-category{animation:shs-nx-category-edge 4.8s ease-in-out infinite}
body.shs-netflix-home .shs-nx-top-category:nth-of-type(3n){animation-delay:-1.6s}
body.shs-netflix-home .shs-nx-top-category:nth-of-type(4n){animation-delay:-3.2s}
body.shs-netflix-home .shs-nx-top-tab:focus-visible,
body.shs-netflix-home .shs-nx-top-category:focus-visible,
body.shs-netflix-home .shs-nx-top-all:focus-visible{outline:2px solid #63b3ff!important;outline-offset:3px!important}
@media(min-width:1000px){
  body.shs-netflix-home .navbar{grid-template-columns:max-content minmax(170px,230px) minmax(0,1fr) max-content!important;gap:14px!important}
body.shs-netflix-home .navbar .search-wrap{max-width:230px!important}
body.shs-netflix-home .navbar .search-wrap input{font-size:.76rem!important}
  body.shs-netflix-home .navbar .shs-catmenu-btn{display:none!important}
  body.shs-netflix-home .navbar .nav-actions{gap:4px!important}
  body.shs-netflix-home .navbar .nav-actions .nav-btn{display:grid!important}
  body.shs-netflix-home .navbar .shs-top-home{color:#fff!important;border-color:rgba(83,168,255,.40)!important;background:rgba(35,128,255,.11)!important}
  body.shs-netflix-home .navbar .shs-netflix-top-tabs{position:relative!important;z-index:1!important;padding-inline:36px!important}
  body.shs-netflix-home .navbar .shs-nx-scroll-controls{position:relative!important;grid-area:tabs;z-index:3;width:100%;height:42px;display:flex!important;justify-content:space-between;flex-direction:row-reverse;align-items:center;pointer-events:none;border:0;background:transparent;box-shadow:none;padding:0}
  body.shs-netflix-home .navbar .shs-nx-scroll-controls[hidden]{display:none!important}
  body.shs-netflix-home .navbar .shs-nx-scroll-btn{pointer-events:auto;width:30px;height:30px;border:1px solid rgba(255,255,255,.16);background:rgba(8,10,15,.90);box-shadow:0 5px 14px rgba(0,0,0,.34),inset 0 1px rgba(255,255,255,.08)}
  body.shs-netflix-home .navbar .shs-nx-scroll-btn:hover{border-color:rgba(229,9,20,.72);background:rgba(229,9,20,.92);box-shadow:0 7px 18px rgba(229,9,20,.28)}
  body.shs-netflix-home .shs-netflix-top-tabs{padding-block:3px!important;gap:8px!important}
  body.shs-netflix-home .shs-nx-top-tab{font-size:.76rem!important}
  body.shs-netflix-home .shs-nx-top-category{font-size:.70rem!important}
  body.shs-netflix-home .navbar .nav-btn,body.shs-netflix-home .navbar .shs-catmenu-btn{border-color:rgba(255,255,255,.13)!important;background:rgba(255,255,255,.05)!important;box-shadow:inset 0 1px rgba(255,255,255,.06)!important}
  body.shs-netflix-home .navbar .nav-btn:hover,body.shs-netflix-home .navbar .shs-catmenu-btn:hover{background:rgba(255,255,255,.12)!important;transform:translateY(-1px)}
}
@media(max-width:760px){
  body.shs-netflix-home .shs-nx-top-tab{padding:7px 9px!important}
  body.shs-netflix-home .shs-nx-top-category{padding:5px 8px!important}
  body.shs-netflix-home .shs-nx-top-all{padding:6px 10px!important}
  body.shs-netflix-home .shs-nx-scroll-controls{display:none}
}

/* أزيلت القائمة الجانبية من الواجهة الرئيسية؛ التنقل الآن من الشريط العلوي فقط. */
body.shs-netflix-home .shsb,body.shs-netflix-home .shsb-ov,
body.shs-netflix-home #shsCatMenuPanel,body.shs-netflix-home #shsCatMenuOverlay{display:none!important}
@media(max-width:999px){
  body.shs-netflix-home .shs-catmenu-btn{display:none!important}
}
@media(prefers-reduced-motion:reduce){body.shs-netflix-home *,body.shs-netflix-home *::before,body.shs-netflix-home *::after{scroll-behavior:auto!important;animation-duration:.01ms!important;transition-duration:.01ms!important}}
</style>
<script>document.body.classList.add('shs-netflix-home');</script>
