<?php
$__adminUpdate = function_exists('adminUpdateStatus') ? adminUpdateStatus() : ['available' => false];
if (!empty($__adminUpdate['available'])):
    $uLocal = htmlspecialchars((string)$__adminUpdate['local'], ENT_QUOTES, 'UTF-8');
    $uRemote = htmlspecialchars((string)$__adminUpdate['remote'], ENT_QUOTES, 'UTF-8');
    $uChannel = htmlspecialchars((string)$__adminUpdate['channel'], ENT_QUOTES, 'UTF-8');
    $uLabel = htmlspecialchars((string)$__adminUpdate['label'], ENT_QUOTES, 'UTF-8');
    $uLog = $__adminUpdate['log'] ?? [];
?>
<style>
/* شريط تحديث أعلى الصفحة بالكامل */
#proUpdateBannerWrap {
    width: 100%;
    z-index: 99999;
}
.pro-update-banner {
    background: linear-gradient(90deg, #1e293b, #0f172a);
    border-bottom: 1px solid rgba(52, 211, 153, 0.4);
    padding: 8px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    flex-wrap: wrap;
    gap: 10px;
}
.pro-update-left {
    display: flex;
    align-items: center;
    gap: 12px;
}
.pro-update-icon {
    color: #34d399;
    font-size: 1.2rem;
    animation: pulseUpdate 2s infinite;
}
@keyframes pulseUpdate {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.8; }
    100% { transform: scale(1); opacity: 1; }
}
.pro-update-text {
    color: #e2e8f0;
    font-size: 0.9rem;
    font-weight: bold;
}
.pro-update-meta {
    color: #94a3b8;
    font-size: 0.8rem;
    margin-right: 8px;
    padding-right: 8px;
    border-right: 1px solid rgba(255,255,255,0.1);
}
.pro-update-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}
.pro-btn {
    padding: 6px 14px;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: bold;
    cursor: pointer;
    text-decoration: none;
    border: none;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.pro-btn-install {
    background: #10b981;
    color: #fff;
    border: 1px solid #059669;
}
.pro-btn-install:hover { background: #059669; }
.pro-btn-log {
    background: rgba(255,255,255,0.1);
    color: #fff;
    border: 1px solid rgba(255,255,255,0.2);
}
.pro-btn-log:hover { background: rgba(255,255,255,0.2); }
.pro-btn-dismiss {
    background: transparent;
    color: #94a3b8;
    border: 1px solid transparent;
}
.pro-btn-dismiss:hover { color: #cbd5e1; text-decoration: underline; }

/* نافذة سجل التحديث */
.pro-log-modal {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 999999;
    background: rgba(0,0,0,0.7);
    backdrop-filter: blur(4px);
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.pro-log-modal.active { display: flex; }
.pro-log-card {
    background: #0f172a;
    border: 1px solid rgba(52,211,153,0.3);
    border-radius: 12px;
    width: 100%;
    max-width: 550px;
    max-height: 80vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 10px 40px rgba(0,0,0,0.5);
}
.pro-log-header {
    padding: 15px 20px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: bold;
    color: #fff;
}
.pro-log-close {
    background: transparent;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    font-size: 1.2rem;
}
.pro-log-close:hover { color: #ef4444; }
.pro-log-body {
    padding: 20px;
    overflow-y: auto;
    font-size: 0.85rem;
    color: #cbd5e1;
    line-height: 1.8;
}
.pro-log-line {
    margin-bottom: 8px;
    padding-bottom: 8px;
    border-bottom: 1px solid rgba(255,255,255,0.03);
}
.pro-log-line:before { content: '•'; color: #10b981; margin-left: 8px; font-weight: bold; }
.pro-log-line:last-child { border: none; margin: 0; padding: 0; }
</style>

<div id="proUpdateBannerWrap">
    <div class="pro-update-banner">
        <div class="pro-update-left">
            <i class="fas fa-cloud-download-alt pro-update-icon"></i>
            <div class="pro-update-text">تحديث جديد متوفر للإدارة <span style="font-size: 0.75rem; color: #34d399; margin-right: 5px; background: rgba(52,211,153,0.1); padding: 2px 6px; border-radius: 4px; border: 1px solid rgba(52,211,153,0.3);"><?= $uLabel ?></span></div>
            <div class="pro-update-meta">v<?= $uLocal ?> ➔ v<?= $uRemote ?></div>
        </div>
        
        <div class="pro-update-actions">
            <form method="post" action="admin.php" style="margin:0; display:inline-block;">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="admin_update_notice_action" value="change_channel">
                <select name="new_channel" onchange="this.form.submit()" style="background: rgba(255,255,255,0.05); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; padding: 5px 10px; font-size: 0.75rem; font-weight: bold; cursor: pointer; outline: none; margin-left: 5px;">
                    <option value="stable" style="background:#0f172a; color:#fff;" <?= $uChannel === 'stable' ? 'selected' : '' ?>>القناة: مستقرة (Stable)</option>
                    <option value="testing" style="background:#0f172a; color:#fff;" <?= $uChannel === 'testing' ? 'selected' : '' ?>>القناة: بيتا (Beta)</option>
                </select>
            </form>
            <button type="button" class="pro-btn pro-btn-log" onclick="document.getElementById('proUpdateModal').classList.add('active')">
                <i class="fas fa-list-ul"></i> سجل التحديث
            </button>
            <button type="button" class="pro-btn pro-btn-install" onclick="startAjaxUpdate()">
                <i class="fas fa-bolt"></i> تحديث مباشر
            </button>
            <form method="post" action="admin.php" style="margin:0" onsubmit="return confirm('هل أنت متأكد من تجاهل هذا الإصدار (v<?= $uRemote ?>)؟')">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="admin_update_notice_action" value="dismiss">
                <input type="hidden" name="remote_version" value="<?= $uRemote ?>">
                <button type="submit" class="pro-btn pro-btn-dismiss">تجاوز</button>
            </form>
        </div>
        
        <!-- شريط التقدم المخفي (يظهر عند التحديث المباشر) -->
        <div id="proUpdateProgress" style="display:none; width:100%; margin-top:12px; padding-top:12px; border-top:1px solid rgba(255,255,255,0.05);">
            <div style="display:flex; justify-content:space-between; font-size:0.75rem; margin-bottom:6px; color:#cbd5e1; font-weight:bold;">
                <span id="proUpdateStatus">جاري الاتصال بخادم التحديث...</span>
                <span id="proUpdatePercent">0%</span>
            </div>
            <div style="width:100%; background:rgba(0,0,0,0.4); border-radius:10px; height:6px; overflow:hidden; box-shadow: inset 0 1px 3px rgba(0,0,0,0.5);">
                <div id="proUpdateBar" style="width:0%; background: linear-gradient(90deg, #b91c1c, #ef4444); height:100%; transition: width 0.3s ease; box-shadow: 0 0 10px rgba(239,68,68,0.8);"></div>
            </div>
        </div>
    </div>
</div>

<script>
function startAjaxUpdate() {
    if(!confirm('هل أنت متأكد من بدء التحديث المباشر الآن (v<?= $uRemote ?>)؟')) return;
    
    // إخفاء الأزرار وإظهار شريط التقدم
    document.querySelector('.pro-update-actions').style.display = 'none';
    var progContainer = document.getElementById('proUpdateProgress');
    progContainer.style.display = 'block';
    
    var bar = document.getElementById('proUpdateBar');
    var perc = document.getElementById('proUpdatePercent');
    var statusText = document.getElementById('proUpdateStatus');
    
    // محاكاة التقدم بينما يقوم الخادم بإنهاء العمل
    var progress = 0;
    var sim = setInterval(function() {
        if(progress < 85) {
            progress += Math.random() * 8;
            if(progress > 85) progress = 85;
            bar.style.width = progress + '%';
            perc.innerText = Math.floor(progress) + '%';
            
            if(progress > 20 && progress < 50) statusText.innerText = 'جاري تنزيل الحزمة...';
            else if(progress >= 50) statusText.innerText = 'جاري فك الضغط ونسخ الملفات...';
        }
    }, 600);

    // إرسال الطلب في الخلفية لصفحة التحديث
    var fd = new FormData();
    fd.append('csrf_token', '<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>');
    fd.append('do_update', '1');
    fd.append('channel', '<?= $uChannel ?>');

    fetch('update.php', {
        method: 'POST',
        body: fd
    }).then(function(res) {
        return res.text();
    }).then(function(html) {
        clearInterval(sim);
        bar.style.width = '100%';
        bar.style.background = 'linear-gradient(90deg, #059669, #10b981)';
        bar.style.boxShadow = '0 0 10px rgba(16,185,129,0.8)';
        perc.innerText = '100%';
        statusText.innerText = 'تم التحديث بنجاح! جاري إعادة تشغيل النظام...';
        statusText.style.color = '#34d399';
        
        setTimeout(function() {
            window.location.reload();
        }, 1500);
    }).catch(function(err) {
        clearInterval(sim);
        statusText.innerText = 'حدث خطأ أثناء الاتصال بالخادم!';
        statusText.style.color = '#ef4444';
        setTimeout(function() {
            document.querySelector('.pro-update-actions').style.display = 'flex';
            progContainer.style.display = 'none';
        }, 3000);
    });
}

// نقل الشريط ليكون قبل الهيدر (topbar) مباشرة
(function() {
    var banner = document.getElementById('proUpdateBannerWrap');
    var mainContainer = document.querySelector('.main');
    var topbar = document.querySelector('.topbar');
    
    if (banner && mainContainer && topbar) {
        // نضع الشريط قبل الـ topbar مباشرة
        mainContainer.insertBefore(banner, topbar);
        
        // إذا كان الهيدر ثابتاً، نقوم بتعديل المسافة
        var style = document.createElement('style');
        style.innerHTML = `
            .topbar { position: relative !important; }
            #proUpdateBannerWrap { width: 100%; border-radius: 0; border-left: 0; border-right: 0; border-top: 0; margin-bottom: 0; }
            .pro-update-banner { border-radius: 0; }
        `;
        document.head.appendChild(style);
    }
})();
</script>

<div class="pro-log-modal" id="proUpdateModal" onclick="if(event.target === this) this.classList.remove('active')">
    <div class="pro-log-card">
        <div class="pro-log-header">
            <span>سجل التحديثات - الإصدار <?= $uRemote ?></span>
            <button class="pro-log-close" onclick="document.getElementById('proUpdateModal').classList.remove('active')"><i class="fas fa-times"></i></button>
        </div>
        <div class="pro-log-body">
            <?php if (!empty($uLog)): foreach ($uLog as $line): ?>
                <div class="pro-log-line"><?= htmlspecialchars((string)$line, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endforeach; else: ?>
                <div style="color: #94a3b8; text-align: center;">لا توجد تفاصيل لهذا الإصدار.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
.pro-toast {
    position: fixed;
    top: 30px;
    left: 50%;
    transform: translateX(-50%) translateY(-50px);
    background: #0f172a;
    color: #e2e8f0;
    padding: 12px 24px;
    border-radius: 8px;
    border: 1px solid rgba(255,255,255,0.1);
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    z-index: 9999999;
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    font-size: 0.85rem;
    font-weight: bold;
    display: flex;
    align-items: center;
    gap: 12px;
    pointer-events: none;
}
.pro-toast.show { transform: translateX(-50%) translateY(0); opacity: 1; }
.pro-toast-success { border-bottom: 2px solid #10b981; }
.pro-toast-success i { color: #10b981; }
.pro-toast-error { border-bottom: 2px solid #ef4444; }
.pro-toast-error i { color: #ef4444; }
</style>

<div id="proToast" class="pro-toast">
    <i id="proToastIcon" class="fas fa-info-circle"></i>
    <span id="proToastText"></span>
</div>

<script>
function proShowToast(msg, type) {
    var toast = document.getElementById('proToast');
    var text = document.getElementById('proToastText');
    var icon = document.getElementById('proToastIcon');
    
    toast.className = 'pro-toast';
    toast.classList.add('pro-toast-' + type);
    
    if(type === 'success') icon.className = 'fas fa-check-circle';
    else if(type === 'error') icon.className = 'fas fa-exclamation-circle';
    else icon.className = 'fas fa-info-circle';
    
    text.innerText = msg;
    toast.classList.add('show');
    
    setTimeout(function() { toast.classList.remove('show'); }, 4000);
}

function checkForUpdatesAjax() {
    var icon = document.getElementById('checkUpdatesIcon');
    if(icon) icon.classList.add('fa-spin');
    
    var fd = new FormData();
    fd.append('csrf_token', '<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>');
    fd.append('admin_update_notice_action', 'check_updates');

    fetch('admin.php', {
        method: 'POST',
        body: fd
    }).then(res => res.json()).then(data => {
        if(icon) icon.classList.remove('fa-spin');
        if (data.status === 'success') {
            if (data.has_update) {
                proShowToast('تم العثور على تحديث جديد (v' + data.remote_version + ')! جاري عرض الشريط...', 'success');
                setTimeout(function(){ window.location.reload(); }, 1500);
            } else {
                proShowToast('نظامك هو الأحدث! لا توجد تحديثات جديدة حالياً.', 'success');
                // إذا كان الشريط ظاهراً ولكنه لم يعد هناك تحديث (تطابق الإصدارات)، نخفيه فوراً
                var banner = document.getElementById('proUpdateBannerWrap');
                if (banner) {
                    banner.style.transition = 'opacity 0.5s ease';
                    banner.style.opacity = '0';
                    setTimeout(function(){ banner.style.display = 'none'; }, 500);
                }
            }
        } else {
            proShowToast(data.message || 'حدث خطأ غير متوقع.', 'error');
        }
    }).catch(err => {
        if(icon) icon.classList.remove('fa-spin');
        proShowToast('فشل الاتصال بالخادم. يرجى المحاولة لاحقاً.', 'error');
    });
}
</script>