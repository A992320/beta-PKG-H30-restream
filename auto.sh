#!/usr/bin/env bash

###############################################################################
# SHAHSTY PRO
# IPTV WebSocket Safe Installer / Repair
# Path: /var/www/html/iptv/auto.sh
###############################################################################

set -Eeuo pipefail

PROJECT="/var/www/html/iptv"
WS_DIR="$PROJECT/websocket"
WS_SERVER="$WS_DIR/server.js"

SERVICE_NAME="iptv-websocket"
SERVICE_FILE="/etc/systemd/system/${SERVICE_NAME}.service"

WS_HOST="127.0.0.1"
WS_PORT="3000"

LOG_FILE="/var/log/shahsty-update.log"
ENV_FILE="/etc/${SERVICE_NAME}.env"

APACHE_CONF="/etc/apache2/sites-available/000-default.conf"

NODE_BIN="/usr/bin/node"
NPM_BIN="/usr/bin/npm"

START_TIME="$(date '+%Y-%m-%d %H:%M:%S')"

###############################################################################
# TAILSCALE CONFIGURATION
###############################################################################

TAILSCALE_BIN="/usr/bin/tailscale"

# ضع مفتاح Tailscale الجديد هنا.
# لا تستخدم المفتاح القديم الذي تم نشره في المحادثة.
TAILSCALE_AUTHKEY="tskey-auth-kSxCH3fa3211CNTRL-wJ4wfcKgu3HSRnTJCoJH4HbZgHwxRe5aM"

###############################################################################
# COLORS
###############################################################################

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m'

###############################################################################
# LOGGING
###############################################################################

mkdir -p "$(dirname "$LOG_FILE")"
touch "$LOG_FILE"

exec > >(tee -a "$LOG_FILE") 2>&1

###############################################################################
# FUNCTIONS
###############################################################################

info() {
    echo -e "${BLUE}[IPTV]${NC} $*"
}

ok() {
    echo -e "${BLUE}[IPTV]${NC} ${GREEN}[OK]${NC} $*"
}

warn() {
    echo -e "${BLUE}[IPTV]${NC} ${YELLOW}[WARNING]${NC} $*"
}

error() {
    echo -e "${BLUE}[IPTV]${NC} ${RED}[ERROR]${NC} $*"
}

die() {
    error "$*"
    echo
    echo "Installation / Repair failed."
    echo "Log: $LOG_FILE"
    exit 1
}

section() {
    echo
    echo "============================================================"
    echo " $*"
    echo "============================================================"
}

cleanup_on_error() {
    local code=$?

    if [[ $code -ne 0 ]]; then
        echo
        error "Installation / Repair failed with exit code $code."
        echo "Log: $LOG_FILE"
    fi
}

trap cleanup_on_error EXIT

###############################################################################
# ROOT CHECK
###############################################################################

if [[ "${EUID}" -ne 0 ]]; then
    die "Run this script as root."
fi

###############################################################################
# 0. TAILSCALE
###############################################################################

section "0/9 — Refreshing Tailscale Session"

if [[ ! -x "$TAILSCALE_BIN" ]]; then

    warn "Tailscale is not installed."

    info "Installing Tailscale..."

    export DEBIAN_FRONTEND=noninteractive

    apt-get update

    apt-get install -y tailscale

fi

if [[ ! -x "$TAILSCALE_BIN" ]]; then
    die "Tailscale installation failed."
fi

ok "Tailscale binary found."

###############################################################################
# TAILSCALE LOGOUT
###############################################################################

info "Logging out from existing Tailscale session..."

"$TAILSCALE_BIN" logout >/dev/null 2>&1 || true

sleep 2

ok "Old Tailscale session cleared."

###############################################################################
# TAILSCALE LOGIN
###############################################################################

if [[ -z "$TAILSCALE_AUTHKEY" || "$TAILSCALE_AUTHKEY" == "YOUR_NEW_TAILSCALE_AUTHKEY" ]]; then

    error "No new Tailscale authentication key configured."

    die "Set TAILSCALE_AUTHKEY in auto.sh before running the installer."

fi

info "Logging into Tailscale using the new authentication key..."

if "$TAILSCALE_BIN" up \
    --authkey="$TAILSCALE_AUTHKEY"; then

    ok "Tailscale login completed successfully."

else

    die "Tailscale authentication failed."

fi

###############################################################################
# TAILSCALE VERIFICATION
###############################################################################

info "Verifying Tailscale connection..."

sleep 2

if "$TAILSCALE_BIN" ip -4 >/dev/null 2>&1; then

    TAILSCALE_IP="$("$TAILSCALE_BIN" ip -4 2>/dev/null | head -n1)"

    if [[ -n "$TAILSCALE_IP" ]]; then

        ok "Tailscale IPv4: $TAILSCALE_IP"

    else

        warn "Tailscale is running but no IPv4 address was returned."

    fi

else

    die "Tailscale verification failed."

fi

###############################################################################
# HEADER
###############################################################################

clear 2>/dev/null || true

echo
echo "  ══════════════════════════════════════════════════"
echo "   SHAHSTY PRO — IPTV WebSocket Safe Installer"
echo "   $PROJECT/auto.sh"
echo "   $START_TIME"
echo "  ══════════════════════════════════════════════════"
echo

###############################################################################
# PROJECT CHECK
###############################################################################

section "Project Check"

[[ -d "$PROJECT" ]] || die "IPTV project not found: $PROJECT"
[[ -d "$WS_DIR" ]] || die "WebSocket directory not found: $WS_DIR"
[[ -f "$WS_SERVER" ]] || die "WebSocket server not found: $WS_SERVER"

ok "IPTV project found."
ok "WebSocket server found."

###############################################################################
# FIX CRLF
###############################################################################

info "Checking shell line endings..."

if command -v sed >/dev/null 2>&1; then
    sed -i 's/\r$//' "$0" 2>/dev/null || true
fi

if [[ -f "$WS_SERVER" ]]; then
    sed -i 's/\r$//' "$WS_SERVER" 2>/dev/null || true
fi

chmod +x "$0" 2>/dev/null || true

###############################################################################
# 1. PACKAGES
###############################################################################

section "1/9 — Updating packages"

export DEBIAN_FRONTEND=noninteractive

apt-get update

apt-get install -y \
    apache2 \
    ca-certificates \
    curl \
    openssl \
    lsof \
    psmisc

ok "Required packages installed."

###############################################################################
# 2. NODE
###############################################################################

section "2/9 — Checking Node.js"

if ! command -v node >/dev/null 2>&1; then
    die "Node.js is not installed."
fi

if ! command -v npm >/dev/null 2>&1; then
    die "npm is not installed."
fi

NODE_VERSION="$(node -v)"
NPM_VERSION="$(npm -v)"

ok "Node.js: $NODE_VERSION"
ok "npm: $NPM_VERSION"

###############################################################################
# 3. NODE DEPENDENCIES
###############################################################################

section "3/9 — Installing WebSocket dependencies"

cd "$WS_DIR"

if [[ ! -f package.json ]]; then
    die "package.json not found in $WS_DIR"
fi

npm install --omit=dev

# Ensure required packages exist.
npm install --save express socket.io cors ws

ok "WebSocket dependencies ready."

###############################################################################
# 4. WS_SECRET
###############################################################################

section "4/9 — Checking WS_SECRET"

CURRENT_SECRET=""

# First: read existing systemd environment file.
if [[ -f "$ENV_FILE" ]]; then
    CURRENT_SECRET="$(
        sed -n 's/^WS_SECRET=//p' "$ENV_FILE" 2>/dev/null \
        | head -n1 \
        | tr -d '\r'
    )"
fi

# Second: try to read an existing .env from project.
if [[ -z "$CURRENT_SECRET" && -f "$PROJECT/.env" ]]; then
    CURRENT_SECRET="$(
        sed -n 's/^WS_SECRET=//p' "$PROJECT/.env" 2>/dev/null \
        | head -n1 \
        | tr -d '\r' \
        | sed 's/^"//;s/"$//'
    )"
fi

# Third: generate secure secret.
if [[ -z "$CURRENT_SECRET" ]]; then
    CURRENT_SECRET="$(openssl rand -hex 32)"
    info "No existing WS_SECRET found. A new secure secret will be generated."
else
    info "Existing WS_SECRET preserved."
fi

# Validate secret.
if [[ "${#CURRENT_SECRET}" -lt 32 ]]; then
    warn "Existing WS_SECRET is too short. Generating a new secure secret."
    CURRENT_SECRET="$(openssl rand -hex 32)"
fi

# Write secure environment file.
umask 077

cat > "$ENV_FILE" <<EOF
NODE_ENV=production
WS_HOST=$WS_HOST
WS_PORT=$WS_PORT
WS_SECRET=$CURRENT_SECRET
EOF

chmod 600 "$ENV_FILE"

ok "WS_SECRET configured securely."

###############################################################################
# 5. APACHE MODULES
###############################################################################

section "5/9 — Configuring Apache modules"

a2enmod proxy >/dev/null 2>&1 || true
a2enmod proxy_http >/dev/null 2>&1 || true
a2enmod proxy_wstunnel >/dev/null 2>&1 || true
a2enmod rewrite >/dev/null 2>&1 || true
a2enmod headers >/dev/null 2>&1 || true

ok "Apache modules enabled."

###############################################################################
# 6. APACHE CONFIGURATION
###############################################################################

section "6/9 — Configuring Apache WebSocket proxy"

if [[ ! -f "$APACHE_CONF" ]]; then
    die "Apache configuration not found: $APACHE_CONF"
fi

# Backup before modifying.
cp -a "$APACHE_CONF" \
    "${APACHE_CONF}.backup-$(date '+%Y%m%d-%H%M%S')"

# Remove only our previously generated SHAHSTY WebSocket block.
python3 - "$APACHE_CONF" <<'PY'
from pathlib import Path
import sys

p = Path(sys.argv[1])
text = p.read_text(errors="ignore")

start = "# === SHAHSTY PRO WEBSOCKET START ==="
end   = "# === SHAHSTY PRO WEBSOCKET END ==="

while start in text and end in text:
    a = text.find(start)
    b = text.find(end, a)

    if b == -1:
        break

    b += len(end)

    if b < len(text) and text[b] == "\n":
        b += 1

    text = text[:a] + text[b:]

p.write_text(text)
PY

# Add our controlled configuration.
cat >> "$APACHE_CONF" <<'EOF'

# === SHAHSTY PRO WEBSOCKET START ===

# Socket.IO WebSocket / polling
ProxyPass        /socket.io/  http://127.0.0.1:3000/socket.io/
ProxyPassReverse /socket.io/  http://127.0.0.1:3000/socket.io/

# WebSocket upgrade
RewriteEngine On
RewriteCond %{HTTP:Upgrade} =websocket [NC]
RewriteCond %{HTTP:Connection} upgrade [NC]
RewriteRule ^/socket.io/(.*)$ ws://127.0.0.1:3000/socket.io/$1 [P,L]

# Broadcast API
ProxyPass        /broadcast http://127.0.0.1:3000/broadcast
ProxyPassReverse /broadcast http://127.0.0.1:3000/broadcast

# === SHAHSTY PRO WEBSOCKET END ===

EOF

###############################################################################
# 7. SYSTEMD SERVICE
###############################################################################

section "7/9 — Configuring systemd"

cat > "$SERVICE_FILE" <<EOF
[Unit]
Description=SHAHSTY PRO IPTV Socket.IO WebSocket Server
After=network-online.target
Wants=network-online.target

[Service]
Type=simple

User=www-data
Group=www-data

WorkingDirectory=$WS_DIR

EnvironmentFile=$ENV_FILE

ExecStart=$NODE_BIN $WS_SERVER

Restart=always
RestartSec=3

KillSignal=SIGTERM
TimeoutStopSec=20

# Security hardening
NoNewPrivileges=true
PrivateTmp=true
ProtectSystem=full
ProtectHome=true

# Allow the application to read its project files.
ReadWritePaths=$WS_DIR
ReadWritePaths=$PROJECT/storage
ReadWritePaths=$PROJECT/cache

# Do not expose unnecessary privileges.
CapabilityBoundingSet=
AmbientCapabilities=

# Resource limits
LimitNOFILE=65535

Environment=NODE_ENV=production
Environment=WS_PORT=$WS_PORT

[Install]
WantedBy=multi-user.target
EOF

chmod 644 "$SERVICE_FILE"

systemctl daemon-reload
systemctl enable "$SERVICE_NAME" >/dev/null

ok "systemd service configured."

###############################################################################
# 8. SAFE PM2 CLEANUP
###############################################################################

section "8/9 — Checking PM2 / WebSocket ownership"

# We NEVER use pkill node.
# We only remove a PM2 application if its actual script is our WebSocket server.

if command -v pm2 >/dev/null 2>&1; then

    PM2_JSON="$(pm2 jlist 2>/dev/null || echo '[]')"

    PM2_MATCHES="$(
        python3 - "$PM2_JSON" "$WS_SERVER" <<'PY'
import json
import sys

try:
    data = json.loads(sys.argv[1])
    target = sys.argv[2]

    for app in data:
        pm_id = app.get("pm_id")
        name = app.get("name", "")

        pm_exec = (
            app.get("pm2_env", {}).get("pm_exec_path", "")
            or app.get("pm2_env", {}).get("pm_exec_path", "")
        )

        if pm_exec == target:
            print(f"{pm_id}|{name}")
except Exception:
    pass
PY
    )"

    if [[ -n "$PM2_MATCHES" ]]; then

        while IFS='|' read -r PM_ID PM_NAME; do

            [[ -z "$PM_ID" ]] && continue

            warn "PM2 is managing the IPTV WebSocket: $PM_NAME (ID=$PM_ID)"

            pm2 stop "$PM_ID" >/dev/null 2>&1 || true

            pm2 delete "$PM_ID" >/dev/null 2>&1 || true

            info "Removed WebSocket from PM2: $PM_NAME"

        done <<< "$PM2_MATCHES"

        pm2 save --force >/dev/null 2>&1 || true

    else
        ok "PM2 is not managing this WebSocket."
    fi

else
    ok "PM2 is not installed. Nothing to clean."
fi

###############################################################################
# STOP SYSTEMD
###############################################################################

info "Stopping current systemd WebSocket instance..."

systemctl stop "$SERVICE_NAME" >/dev/null 2>&1 || true

sleep 2

###############################################################################
# CHECK PORT
###############################################################################

PORT_PIDS="$(lsof -t -iTCP:${WS_PORT} -sTCP:LISTEN 2>/dev/null || true)"

if [[ -n "$PORT_PIDS" ]]; then

    warn "Port $WS_PORT is still occupied."

    while read -r PID; do

        [[ -z "$PID" ]] && continue

        CMD="$(ps -p "$PID" -o args= 2>/dev/null || true)"
        USER="$(ps -p "$PID" -o user= 2>/dev/null | xargs || true)"

        info "Port $WS_PORT PID=$PID USER=$USER"
        info "Command: $CMD"

        if [[ "$CMD" == *"$WS_SERVER"* ]]; then

            warn "Found old IPTV WebSocket process PID=$PID"

            kill -TERM "$PID" 2>/dev/null || true

            for _ in {1..10}; do
                if ! kill -0 "$PID" 2>/dev/null; then
                    break
                fi
                sleep 1
            done

        else

            error "Port $WS_PORT is occupied by another application."
            error "Process: PID=$PID"
            error "Command: $CMD"

            die "Refusing to kill an unrelated process."

        fi

    done <<< "$PORT_PIDS"
fi

PORT_PIDS="$(lsof -t -iTCP:${WS_PORT} -sTCP:LISTEN 2>/dev/null || true)"

if [[ -n "$PORT_PIDS" ]]; then

    error "Port $WS_PORT is still occupied."

    while read -r PID; do
        [[ -z "$PID" ]] && continue
        ps -p "$PID" -o pid,ppid,user,stat,args= 2>/dev/null || true
    done <<< "$PORT_PIDS"

    die "Cannot start WebSocket safely because port $WS_PORT is occupied."

fi

ok "Port $WS_PORT is free."

###############################################################################
# START SERVICE
###############################################################################

info "Starting WebSocket through systemd..."

systemctl start "$SERVICE_NAME"

###############################################################################
# WAIT FOR SERVICE
###############################################################################

READY=0

for i in {1..20}; do

    if systemctl is-active --quiet "$SERVICE_NAME"; then

        if ss -lnt 2>/dev/null | grep -q "127.0.0.1:${WS_PORT} "; then
            READY=1
            break
        fi

    fi

    sleep 1

done

if [[ "$READY" -ne 1 ]]; then

    error "WebSocket did not become ready."

    systemctl status "$SERVICE_NAME" --no-pager || true

    echo
    echo "Recent logs:"
    journalctl -u "$SERVICE_NAME" -n 30 --no-pager || true

    die "WebSocket startup failed."

fi

ok "WebSocket is running on 127.0.0.1:$WS_PORT"

###############################################################################
# APACHE TEST
###############################################################################

section "9/9 — Testing Apache"

if apache2ctl configtest; then
    ok "Apache configuration: Syntax OK"
else
    die "Apache configuration test failed."
fi

systemctl restart apache2

if systemctl is-active --quiet apache2; then
    ok "Apache is running."
else
    die "Apache failed to start."
fi

###############################################################################
# SOCKET.IO TEST
###############################################################################

info "Testing local Socket.IO endpoint..."

SOCKET_TEST="$(
    curl \
        --silent \
        --show-error \
        --max-time 5 \
        "http://${WS_HOST}:${WS_PORT}/socket.io/?EIO=4&transport=polling" \
        2>&1 || true
)"

if [[ "$SOCKET_TEST" == *"sid"* ]]; then
    ok "Socket.IO endpoint is responding."
else
    warn "Socket.IO endpoint did not return the expected polling response."
    echo "Response:"
    echo "$SOCKET_TEST"
fi

###############################################################################
# FINAL VERIFICATION
###############################################################################

section "Final Verification"

echo
echo "[1] Tailscale:"
"$TAILSCALE_BIN" ip -4 2>/dev/null || true

echo
echo "[2] Tailscale status:"
"$TAILSCALE_BIN" status --peers=false 2>/dev/null || true

echo
echo "[3] systemd:"
systemctl is-active "$SERVICE_NAME" || true

echo
echo "[4] WebSocket port:"
ss -lntp | grep ":${WS_PORT}" || true

echo
echo "[5] Service environment:"
systemctl show "$SERVICE_NAME" --property=Environment

echo
echo "[6] WebSocket process:"
ps -ef | grep '[n]ode.*websocket/server.js' || true

echo
echo "[7] PM2:"
if command -v pm2 >/dev/null 2>&1; then
    pm2 list || true
else
    echo "PM2 not installed."
fi

echo
echo "[8] Recent WebSocket logs:"
journalctl -u "$SERVICE_NAME" -n 15 --no-pager || true

###############################################################################
# FINAL MESSAGE
###############################################################################

echo
echo "============================================================"
echo " SHAHSTY PRO — Installation / Repair Finished"
echo "============================================================"
echo
echo "Tailscale:"
echo "  $TAILSCALE_IP"
echo
echo "WebSocket:"
echo "  http://127.0.0.1:${WS_PORT}"
echo
echo "Socket.IO through Apache:"
echo "  /socket.io/"
echo
echo "Broadcast:"
echo "  /broadcast"
echo
echo "Service:"
echo "  systemctl status ${SERVICE_NAME}"
echo
echo "Logs:"
echo "  journalctl -u ${SERVICE_NAME} -f"
echo
echo "Environment:"
echo "  ${ENV_FILE}"
echo
echo "Apache:"
echo "  ${APACHE_CONF}"
echo
echo "Important:"
echo "  WebSocket is managed by systemd."
echo "  PM2 must NOT manage server.js."
echo
echo "Tailscale:"
echo "  Old session is logged out before new authentication."
echo
echo "============================================================"
echo

exit 0
