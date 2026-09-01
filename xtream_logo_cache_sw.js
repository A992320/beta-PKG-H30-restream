/* كاش شعارات Xtream داخل المتصفح فقط — لا يوجد proxy أو جلب خادمي. */
const SHS_XTREAM_LOGO_CACHE = 'shs-xtream-logo-cache-v1';
const SHS_XTREAM_LOGO_MAX = 180;

self.addEventListener('install', event => {
  event.waitUntil(self.skipWaiting());
});
self.addEventListener('activate', event => {
  event.waitUntil(self.clients.claim());
});

async function shsCacheXtreamLogo(rawUrl){
  let url;
  try{
    url=new URL(rawUrl);
    if(url.protocol!=='http:'&&url.protocol!=='https:')return;
  }catch(e){return;}
  const cache=await caches.open(SHS_XTREAM_LOGO_CACHE);
  const existing=await cache.match(url.href,{ignoreVary:true});
  if(existing)return;
  try{
    const request=new Request(url.href,{mode:'no-cors',credentials:'omit',referrerPolicy:'no-referrer'});
    const response=await fetch(request);
    if(!response||(response.type!=='opaque'&&!response.ok))return;
    await cache.put(url.href,response.clone());
    const keys=await cache.keys();
    if(keys.length>SHS_XTREAM_LOGO_MAX){
      await Promise.all(keys.slice(0,keys.length-SHS_XTREAM_LOGO_MAX).map(key=>cache.delete(key)));
    }
  }catch(e){}
}

self.addEventListener('message', event => {
  const data=event.data||{};
  if(data.type==='SHS_CACHE_XTREAM_LOGO'&&typeof data.url==='string'){
    event.waitUntil(shsCacheXtreamLogo(data.url));
  }
});

self.addEventListener('fetch', event => {
  const request=event.request;
  if(!request||request.destination!=='image')return;
  let url;
  try{url=new URL(request.url);}catch(e){return;}
  if(url.origin===self.location.origin)return;
  event.respondWith((async()=>{
    const cache=await caches.open(SHS_XTREAM_LOGO_CACHE);
    const cached=await cache.match(request,{ignoreVary:true});
    return cached||fetch(request);
  })());
});
