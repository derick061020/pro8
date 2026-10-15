#!/usr/bin/env bash
# Renovación automática del certificado SSL de hotelbuendia.net (servidor pro8).
#
# - Junta los dominios: los fijos de abajo + los hostnames de los clientes en la base
#   del sistema. Solo usa los que apuntan a este servidor (si uno no apunta, Let's
#   Encrypt rechazaría el certificado entero).
# - certbot solo pide un certificado nuevo si faltan menos de 30 días o si cambió la
#   lista de dominios. Los demás días no hace nada y el proxy no se toca.
# - Cuando sí renueva: para el proxy (libera el puerto 80), valida por HTTP, copia el
#   certificado a /root/certs y vuelve a levantar el proxy. Son unos segundos.
#
# Instalar (una vez):  cp renovar-ssl.sh /root/ && chmod +x /root/renovar-ssl.sh
#                      (crontab -l; echo '15 4 * * * /root/renovar-ssl.sh') | crontab -
# Probar a mano:       /root/renovar-ssl.sh && tail -n 30 /var/log/renovar-ssl.log
set -uo pipefail

DOMINIO="hotelbuendia.net"
FIJOS=("hotelbuendia.net" "www.hotelbuendia.net" "sistema.hotelbuendia.net" "admin.hotelbuendia.net" "apisu.hotelbuendia.net")
PROXY="proxy-proxy-1"
FPM="fpm_hotelbuendia_net"
CERT_NAME="hotelbuendia-http"
DESTINO="/root/certs"
LOG="/var/log/renovar-ssl.log"
# El panel admin del pro8 (Sistema → SSL) lee el estado de aquí
ESTADO_DIR="/root/hotelbuendia.net/storage/app/ssl"
CRON="15 4 * * *"
INICIO=$(date -Is)
LOCK="/run/renovar-ssl.lock"

exec >>"$LOG" 2>&1
echo "=== $(date '+%F %T') ==="

# Una sola ejecución a la vez
exec 9>"$LOCK"
if ! flock -n 9; then echo "Ya hay una renovación en curso"; exit 0; fi

# Pase lo que pase, el proxy tiene que quedar arriba
asegurar_proxy() {
    if [ "$(docker inspect -f '{{.State.Running}}' "$PROXY" 2>/dev/null)" != "true" ]; then
        echo "Levantando $PROXY"
        docker start "$PROXY" >/dev/null
    fi
}
trap asegurar_proxy EXIT

IP_SERVIDOR=$(curl -4 -s --max-time 10 https://api.ipify.org || true)
if [ -z "$IP_SERVIDOR" ]; then
    IP_SERVIDOR=$(hostname -I | awk '{print $1}')
fi

# Hostnames de los clientes guardados en el sistema
CLIENTES=$(docker exec "$FPM" php artisan tinker --execute='echo implode(PHP_EOL, \Hyn\Tenancy\Models\Hostname::pluck("fqdn")->all());' 2>/dev/null | tr -d '\r' || true)

DOMINIOS=()
OMITIDOS=()
for h in "${FIJOS[@]}" $CLIENTES; do
    h=$(echo "$h" | tr '[:upper:]' '[:lower:]' | xargs)
    [[ "$h" == *."$DOMINIO" || "$h" == "$DOMINIO" ]] || continue
    [[ " ${DOMINIOS[*]-} " == *" $h "* ]] && continue
    if getent ahostsv4 "$h" | awk '{print $1}' | grep -qx "$IP_SERVIDOR"; then
        DOMINIOS+=("$h")
    else
        echo "Omitido (no apunta a $IP_SERVIDOR): $h"
        OMITIDOS+=("$h")
    fi
done

if [ ${#DOMINIOS[@]} -eq 0 ]; then
    echo "ERROR: ningún dominio apunta a este servidor"
    exit 1
fi
echo "Dominios: ${DOMINIOS[*]}"

MTIME_ANTES=$(stat -c %Y "$DESTINO/$DOMINIO.crt" 2>/dev/null || echo 0)

ARGS=()
for h in "${DOMINIOS[@]}"; do ARGS+=(-d "$h"); done

# Hook que copia el certificado nuevo adonde lo lee el proxy (solo corre si hubo renovación)
DEPLOY="cp -L /etc/letsencrypt/live/$CERT_NAME/fullchain.pem $DESTINO/$DOMINIO.crt.new \
 && cp -L /etc/letsencrypt/live/$CERT_NAME/privkey.pem $DESTINO/$DOMINIO.key.new \
 && chmod 600 $DESTINO/$DOMINIO.key.new \
 && cp -p $DESTINO/$DOMINIO.crt $DESTINO/$DOMINIO.crt.anterior 2>/dev/null; cp -p $DESTINO/$DOMINIO.key $DESTINO/$DOMINIO.key.anterior 2>/dev/null; \
 mv $DESTINO/$DOMINIO.crt.new $DESTINO/$DOMINIO.crt && mv $DESTINO/$DOMINIO.key.new $DESTINO/$DOMINIO.key \
 && echo 'Certificado copiado a $DESTINO'"

certbot certonly --standalone --non-interactive --agree-tos --register-unsafely-without-email \
    --cert-name "$CERT_NAME" --expand --keep-until-expiring \
    --pre-hook "docker stop $PROXY" \
    --deploy-hook "$DEPLOY" \
    --post-hook "docker start $PROXY" \
    "${ARGS[@]}"
RC=$?
FIN=""
DIAS=""

asegurar_proxy

# Comprobación final: ¿qué certificado está sirviendo el proxy y cuánto le queda?
sleep 3
FIN=$(echo | timeout 10 openssl s_client -connect 127.0.0.1:443 -servername "sistema.$DOMINIO" 2>/dev/null | openssl x509 -noout -enddate 2>/dev/null | cut -d= -f2)
if [ -n "$FIN" ]; then
    DIAS=$(( ($(date -d "$FIN" +%s) - $(date +%s)) / 86400 ))
    echo "Certificado en servicio vence: $FIN ($DIAS días)"
    if [ "$DIAS" -lt 15 ]; then
        echo "ALERTA: el certificado vence en $DIAS días y no se pudo renovar. Revisa este log."
    fi
else
    echo "ALERTA: no se pudo leer el certificado del puerto 443"
fi

[ $RC -eq 0 ] && echo "OK" || echo "ERROR: certbot terminó con código $RC"

guardar_estado() {
    mkdir -p "$ESTADO_DIR"
    local renovado=false
    [ "$(stat -c %Y "$DESTINO/$DOMINIO.crt" 2>/dev/null || echo 0)" != "$MTIME_ANTES" ] && renovado=true
    # Próxima ejecución del cron (04:15 de hoy si aún no pasó, si no la de mañana)
    local proxima
    if [ "$(date +%H%M)" -lt 0415 ]; then proxima=$(date -Is -d 'today 04:15'); else proxima=$(date -Is -d 'tomorrow 04:15'); fi
    RC="$RC" INICIO="$INICIO" FIN_CERT="${FIN:-}" DIAS="${DIAS:-}" RENOVADO="$renovado" PROXIMA="$proxima" CRON="$CRON" \
    DOMS="${DOMINIOS[*]-}" OMIT="${OMITIDOS[*]-}" IP="$IP_SERVIDOR" SALIDA="$ESTADO_DIR/estado.json" python3 - <<'PY'
import json, os, datetime
e = os.environ
json.dump({
    "inicio": e["INICIO"],
    "fin": datetime.datetime.now().astimezone().isoformat(timespec="seconds"),
    "codigo": int(e["RC"]),
    "ok": e["RC"] == "0",
    "renovado": e["RENOVADO"] == "true",
    "dominios": e["DOMS"].split(),
    "omitidos": e["OMIT"].split(),
    "ip_servidor": e["IP"],
    "vence": e["FIN_CERT"] or None,
    "dias_restantes": int(e["DIAS"]) if e["DIAS"] else None,
    "proxima_ejecucion": e["PROXIMA"],
    "cron": e["CRON"],
}, open(e["SALIDA"], "w"), ensure_ascii=False, indent=2)
PY
    # Últimas 300 líneas del log para verlas desde el panel
    tail -n 300 "$LOG" > "$ESTADO_DIR/renovar-ssl.log" 2>/dev/null
    chmod 644 "$ESTADO_DIR"/estado.json "$ESTADO_DIR"/renovar-ssl.log 2>/dev/null
}
guardar_estado

exit $RC
