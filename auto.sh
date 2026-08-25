#!/bin/bash

set -e

IPTV_DIR="/var/www/html/iptv"
WS_DIR="$IPTV_DIR/websocket"
SERVICE_NAME="iptv-websocket"
SERVICE_FILE="/etc/systemd/system/${SERVICE_NAME}.service"
OVERRIDE_DIR="/etc/systemd/system/${SERVICE_NAME}.service.d"
OVERRIDE_FILE="${OVERRIDE_DIR}/override.conf"

echo "=============================================="
echo " IPTV WebSocket - Install / Repair Script"
echo "=============================================="

# ------------------------------------------------
# 1. التأكد من root
# ------------------------------------------------
if [ "$(id -u)" -ne 0 ]; then
    echo "[ERROR] يجب تشغيل السكربت بصلاحيات root."
    exit 1
fi

# ------------------------------------------------
# 2. التأكد من وجود المشروع
# ------------------------------------------------
if [ ! -d "$IPTV_DIR" ]; then
    echo "[ERROR] مجلد IPTV غير موجود:"
    echo "$IPTV_DIR"
    exit 1
fi

if [ ! -f "$WS_DIR/server.js" ]; then
    echo "[ERROR] ملف WebSocket غير موجود:"
    echo "$WS_DIR/server.js"
    exit 1
fi

echo "[OK] IPTV project found."
echo "[OK] WebSocket server found."

# ------------------------------------------------
# 3. تحديث APT
# ------------------------------------------------
echo
echo "[1/9] Updating packages..."

apt-get update

# ------------------------------------------------
# 4. تثبيت المتطلبات
# ------------------------------------------------
echo
echo "[2/9] Installing required packages..."

apt-get install -y \
    curl \
    ca-certificates \
    apache2

# ------------------------------------------------
# 5. التأكد من Node.js
# ------------------------------------------------
echo
echo "[3/9] Checking Node.js..."

if ! command -v node >/dev/null 2>&1; then

    echo "[INFO] Node.js غير مثبت، جاري التثبيت..."

    curl -fsSL https://deb.nodesource.com/setup_22.x | bash -
    apt-get install -y nodejs

fi

NODE_VERSION="$(node -v)"
NPM_VERSION="$(npm -v)"

echo "[OK] Node.js: $NODE_VERSION"
echo "[OK] npm: $NPM_VERSION"

# ------------------------------------------------
# 6. تثبيت Node dependencies
# ------------------------------------------------
echo
echo "[4/9] Installing WebSocket dependencies..."

cd "$WS_DIR"

if [ -f package-lock.json ]; then
    npm install
else
    npm install express socket.io
fi

# التأكد من وجود المكتبات المطلوبة
npm install express socket.io

echo
echo "[OK] Installed packages:"
npm list --depth=0 || true

# ------------------------------------------------
# 7. إنشاء WS_SECRET
# ------------------------------------------------
echo
echo "[5/9] Configuring WS_SECRET..."

SECRET_FILE="$WS_DIR/.ws_secret"

if [ -f "$SECRET_FILE" ]; then

    WS_SECRET="$(cat "$SECRET_FILE" | tr -d '[:space:]')"

    if [ -z "$WS_SECRET" ]; then
        WS_SECRET="$(openssl rand -hex 32)"
        echo "$WS_SECRET" > "$SECRET_FILE"
    fi

else

    WS_SECRET="$(openssl rand -hex 32)"
    echo "$WS_SECRET" > "$SECRET_FILE"

fi

chmod 600 "$SECRET_FILE"

echo "[OK] WS_SECRET configured."

# ------------------------------------------------
# 8. إنشاء systemd service
# ------------------------------------------------
echo
echo "[6/9] Creating systemd service..."

cat > "$SERVICE_FILE" <<EOF
[Unit]
Description=IPTV Socket.IO WebSocket Server
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
WorkingDirectory=$WS_DIR
ExecStart=/usr/bin/node $WS_DIR/server.js
Restart=always
RestartSec=3

Environment="NODE_ENV=production"
Environment="WS_PORT=3000"
Environment="WS_SECRET=$WS_SECRET"

[Install]
WantedBy=multi-user.target
EOF

# إزالة override قديم حتى لا يسبب تضارب
rm -rf "$OVERRIDE_DIR"

echo "[OK] systemd service created."

# ------------------------------------------------
# 9. Apache
# ------------------------------------------------
echo
echo "[7/9] Configuring Apache modules..."

a2enmod proxy
a2enmod proxy_http
a2enmod proxy_wstunnel
a2enmod rewrite

# ------------------------------------------------
# إنشاء WebSocket Apache configuration
# ------------------------------------------------
echo
echo "[8/9] Checking Apache configuration..."

APACHE_CONF="/etc/apache2/sites-available/000-default.conf"

if [ -f "$APACHE_CONF" ]; then

    if ! grep -q "127.0.0.1:3000/socket.io" "$APACHE_CONF"; then

        cp "$APACHE_CONF" "${APACHE_CONF}.backup-$(date +%Y%m%d-%H%M%S)"

        sed -i '/<\/VirtualHost>/i\
    # IPTV Socket.IO WebSocket\
    RewriteEngine On\
    RewriteCond %{HTTP:Upgrade} websocket [NC]\
    RewriteCond %{HTTP:Connection} upgrade [NC]\
    RewriteRule ^/socket.io/(.*) ws://127.0.0.1:3000/socket.io/$1 [P,L]\
\
    ProxyPass /socket.io/ http://127.0.0.1:3000/socket.io/\
    ProxyPassReverse /socket.io/ http://127.0.0.1:3000/socket.io/\
\
    ProxyPass /broadcast http://127.0.0.1:3000/broadcast\
    ProxyPassReverse /broadcast http://127.0.0.1:3000/broadcast\
' "$APACHE_CONF"

        echo "[OK] Apache WebSocket proxy added."

    else

        echo "[OK] Apache WebSocket proxy already exists."

    fi

else

    echo "[WARNING] $APACHE_CONF غير موجود."
    echo "[WARNING] تخطينا تعديل Apache."

fi

# ------------------------------------------------
# صلاحيات
# ------------------------------------------------
echo
echo "[INFO] Fixing WebSocket permissions..."

chown -R www-data:www-data "$WS_DIR"

chmod 755 "$WS_DIR"
chmod 644 "$WS_DIR/server.js"
chmod 600 "$SECRET_FILE"

# ------------------------------------------------
# تشغيل الخدمة
# ------------------------------------------------
echo
echo "[9/9] Starting services..."

systemctl daemon-reload

systemctl enable "$SERVICE_NAME"

systemctl restart "$SERVICE_NAME"

# Apache configuration test
echo
echo "[INFO] Testing Apache configuration..."

if apache2ctl configtest; then
    systemctl restart apache2
else
    echo "[ERROR] Apache configuration failed."
    echo "[ERROR] لن يتم إعادة تشغيل Apache."
fi

# ------------------------------------------------
# فحص WebSocket
# ------------------------------------------------
sleep 2

echo
echo "=============================================="
echo " Verification"
echo "=============================================="

echo
echo "[1] systemd status:"
systemctl is-active "$SERVICE_NAME" || true

echo
echo "[2] Port 3000:"
ss -lntp | grep ':3000' || true

echo
echo "[3] Service environment:"
systemctl show "$SERVICE_NAME" --property=Environment

echo
echo "[4] Recent WebSocket logs:"
journalctl -u "$SERVICE_NAME" -n 15 --no-pager

echo
echo "=============================================="
echo " Installation / Repair Finished"
echo "=============================================="

echo
echo "WebSocket:"
echo "  127.0.0.1:3000"

echo
echo "Socket.IO through Apache:"
echo "  /socket.io/"

echo
echo "Broadcast:"
echo "  /broadcast"

echo
echo "WS_SECRET:"
echo "  $WS_SECRET"

echo
echo "Service:"
echo "  systemctl status $SERVICE_NAME"

echo
echo "Logs:"
echo "  journalctl -u $SERVICE_NAME -f"

echo
echo "=============================================="