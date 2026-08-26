<script>
window.clearLoginLogs = function(){
    if(confirm("هل أنت متأكد من حذف جميع سجلات الدخول؟ لا يمكن التراجع عن هذا الإجراء.")){
        api({ajax_action:'clear_login_logs'}).then(d=>{ if(d.success) loadLoginLogs(); else al('alContainer',d.error,'e'); });
    }
};
window.exportLoginLogs = function(){
    api({ajax_action:'get_login_logs'}).then(d => {
        if(d.success && d.logs && d.logs.length > 0){
            let csvContent = "data:text/csv;charset=utf-8,\uFEFF";
            csvContent += "ID,IP Address,Username,Status,Blocked,Time\n";
            d.logs.forEach(l => {
                let row = [l.id, l.ip_address, l.username, l.status, (l.is_blocked==1?'Yes':'No'), l.attempt_time].join(",");
                csvContent += row + "\r\n";
            });
            let encodedUri = encodeURI(csvContent);
            let link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "login_logs_" + new Date().toISOString().slice(0,10) + ".csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        } else {
            al('alContainer', 'لا يوجد بيانات لتصديرها', 'e');
        }
    });
};
function loadLoginLogs(){
    $('llTbody').innerHTML = '<tr><td colspan="5" style="text-align:center"><span class="sp"></span> جاري التحميل...</td></tr>';
    api({ajax_action:'get_login_logs'}).then(d => {
        if(d.success){
            let h = '';
            if(!d.logs || d.logs.length === 0){
                h = '<tr><td colspan="5" style="text-align:center;color:var(--t3)">لا يوجد سجلات</td></tr>';
            } else {
                d.logs.forEach(l => {
                    let st = l.status === 'success' ? '<span style="color:#00D084;font-weight:bold">ناجح</span>' : '<span style="color:#E50914;font-weight:bold">فشل</span>';
                    h += `<tr>
                        <td>${l.id}</td>
                        <td dir="ltr" style="text-align:right">${esc(l.ip_address)}</td>
                        <td>${esc(l.username||'-')}</td>
                        <td>${st}</td>
                        <td dir="ltr" style="text-align:right">${esc(l.attempt_time)}</td>
                    </tr>`;
                });
            }
            $('llTbody').innerHTML = h;
            if(window.lucide) lucide.createIcons();
        } else {
            al('alContainer', d.error || 'حدث خطأ', 'e');
            $('llTbody').innerHTML = '<tr><td colspan="5" style="text-align:center;color:red">خطأ في التحميل</td></tr>';
        }
    }).catch(e => {
        $('llTbody').innerHTML = '<tr><td colspan="5" style="text-align:center;color:red">فشل الاتصال</td></tr>';
    });
}

/* Personal dashboard shortcuts are stored locally for the signed-in browser. */
window.dashboardShortcuts = (function () {
    const storageKey = 'shashety_admin_dashboard_shortcuts_v1';
    const destinations = {
        categories: { label: 'الأقسام', icon: 'layout-grid' },
        channels: { label: 'القنوات', icon: 'tv' },
        'm3u-import': { label: 'استيراد M3U', icon: 'file-up' },
        xtream: { label: 'حساب Xtream', icon: 'satellite-dish' },
        series: { label: 'شاشتي (المسلسلات والأفلام)', icon: 'film' },
        vupload: { label: 'رفع الأفلام', icon: 'upload-cloud' },
        vmanage: { label: 'إدارة الفيديوهات', icon: 'video' },
        subscriptions: { label: 'خطط الاشتراك', icon: 'crown' },
        coupons: { label: 'أكواد التفعيل', icon: 'ticket' },
        subscribers: { label: 'المشتركون', icon: 'user-check' },
        'api-settings': { label: 'إعدادات API', icon: 'plug' },
        'site-settings': { label: 'إعدادات الموقع', icon: 'settings' },
        'system-tools': { label: 'صيانة النظام', icon: 'wrench' },
        backup: { label: 'النسخ الاحتياطي', icon: 'database' },
        users: { label: 'إدارة المستخدمين', icon: 'users', loader: 'loadUsers' },
        'login-logs': { label: 'سجل الدخول', icon: 'shield', loader: 'loadLoginLogs' },
        'general-settings': { label: 'الإعدادات العامة', icon: 'sliders-horizontal' },
        'frontend-control': { label: 'التحكم بالواجهة الأمامية', icon: 'layout-dashboard' },
        'company-info': { label: 'حول الشركة', icon: 'info' },
        update: { label: 'التحديثات والنظام', icon: 'refresh-cw', href: 'update.php' }
    };

    function read() {
        try {
            const saved = JSON.parse(localStorage.getItem(storageKey) || '[]');
            return Array.isArray(saved) ? saved.filter(function (item) {
                return item && typeof item.label === 'string' && destinations[item.target];
            }).slice(0, 24) : [];
        } catch (error) {
            return [];
        }
    }

    function write(items) {
        localStorage.setItem(storageKey, JSON.stringify(items));
    }

    function icon(name) {
        const element = document.createElement('i');
        element.setAttribute('data-lucide', name);
        element.setAttribute('aria-hidden', 'true');
        return element;
    }

    function render() {
        const list = document.getElementById('dashboardShortcutList');
        if (!list) return;
        const items = read();
        list.replaceChildren();

        if (!items.length) {
            const empty = document.createElement('p');
            empty.className = 'dashboard-shortcuts-empty';
            empty.textContent = 'أضف اختصاراً للوصول السريع إلى أي قسم تستخدمه كثيراً.';
            list.appendChild(empty);
            return;
        }

        items.forEach(function (item) {
            const destination = destinations[item.target];
            const shortcut = document.createElement('button');
            shortcut.type = 'button';
            shortcut.className = 'dashboard-shortcut';
            shortcut.title = destination.label;
            shortcut.appendChild(icon(destination.icon));

            const text = document.createElement('span');
            text.textContent = item.label;
            shortcut.appendChild(text);
            shortcut.addEventListener('click', function () { openItem(item); });

            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'dashboard-shortcut-remove';
            remove.setAttribute('aria-label', 'حذف الاختصار: ' + item.label);
            remove.title = 'حذف الاختصار';
            remove.appendChild(icon('x'));
            remove.addEventListener('click', function (event) {
                event.stopPropagation();
                if (confirm('حذف الاختصار «' + item.label + '»؟')) removeItem(item.id);
            });

            const itemWrap = document.createElement('div');
            itemWrap.className = 'dashboard-shortcut-wrap';
            itemWrap.append(shortcut, remove);
            list.appendChild(itemWrap);
        });

        if (window.lucide) window.lucide.createIcons();
    }

    function openItem(item) {
        const destination = destinations[item.target];
        if (!destination) return;
        if (destination.href) {
            window.location.href = destination.href;
            return;
        }
        if (typeof window.S === 'function') window.S(item.target);
        if (destination.loader && typeof window[destination.loader] === 'function') window[destination.loader]();
    }

    function removeItem(id) {
        write(read().filter(function (item) { return item.id !== id; }));
        render();
    }

    function open() {
        const name = document.getElementById('dashboardShortcutName');
        const target = document.getElementById('dashboardShortcutTarget');
        if (name) name.value = '';
        if (target) target.selectedIndex = 0;
        if (typeof window.OM === 'function') window.OM('dashboardShortcutM');
        window.setTimeout(function () { if (name) name.focus(); }, 80);
    }

    function save() {
        const name = document.getElementById('dashboardShortcutName');
        const target = document.getElementById('dashboardShortcutTarget');
        if (!target || !destinations[target.value]) return;
        const label = ((name && name.value) || destinations[target.value].label).trim().slice(0, 48);
        if (!label) return;
        const id = (window.crypto && window.crypto.randomUUID) ? crypto.randomUUID() : String(Date.now()) + Math.random().toString(16).slice(2);
        const items = read();
        items.push({ id: id, label: label, target: target.value });
        write(items.slice(-24));
        if (typeof window.CM === 'function') window.CM('dashboardShortcutM');
        render();
    }

    document.addEventListener('DOMContentLoaded', render);
    return { open: open, save: save, render: render };
}());</script>
