<template>
  <div
    v-if="tr"
    class="room-ring"
    :class="['room-ring--' + tr.state]"
    :title="tooltip"
  >
    <svg class="room-ring__svg" viewBox="0 0 48 48" aria-hidden="true">
      <circle class="room-ring__track" cx="24" cy="24" :r="radius"></circle>
      <circle
        class="room-ring__arc"
        cx="24"
        cy="24"
        :r="radius"
        :stroke-dasharray="circumference"
        :stroke-dashoffset="dashOffset"
      ></circle>
    </svg>
    <div class="room-ring__text">
      <span class="room-ring__value">{{ tr.value }}</span>
      <span class="room-ring__label">{{ tr.label }}</span>
    </div>
  </div>
</template>

<script>
// Círculo de cuenta regresiva de la estadía. El aro se vacía en las últimas
// 24 h antes de la salida; en el centro va la unidad que importa
// (días, horas o minutos). Colores: verde con tiempo de sobra, amarillo cuando
// faltan 2 h o menos, rojo parpadeando cuando ya pasó la hora de salida.
// Mantiene su propio intervalo para que solo este componente se re-renderice.
const WARNING_MS = 2 * 3600000;
const FULL_RING_MS = 24 * 3600000;

function parseDateTime(date, time, fallbackTime) {
  if (!date) return null;
  const d = String(date).substring(0, 10).split('-');
  const t = String(time || fallbackTime).split(':');
  if (d.length < 3 || t.length < 2) return null;
  const result = new Date(
    parseInt(d[0], 10), parseInt(d[1], 10) - 1, parseInt(d[2], 10),
    parseInt(t[0], 10), parseInt(t[1], 10), 0, 0
  );
  return isNaN(result.getTime()) ? null : result;
}

export default {
  name: 'RoomCountdown',
  props: {
    rent: {
      type: Object,
      default: null,
    },
  },
  data() {
    return {
      tr: null,
      radius: 21,
      _timer: null,
    };
  },
  computed: {
    circumference() {
      return 2 * Math.PI * this.radius;
    },
    dashOffset() {
      const fraction = this.tr ? this.tr.fraction : 0;
      return this.circumference * (1 - fraction);
    },
    tooltip() {
      if (!this.tr) return '';
      const out = this.rent ? `${this.rent.output_date} ${String(this.rent.output_time).slice(0, 5)}` : '';
      return this.tr.state === 'expired'
        ? `Salida vencida (${out})`
        : `Quedan ${this.tr.full} · salida ${out}`;
    },
  },
  watch: {
    // Si cambia la salida (p. ej. tras una extensión), recalcular.
    'rent.output_date'() { this.tick(); },
    'rent.output_time'() { this.tick(); },
  },
  mounted() {
    this.tick();
    this._timer = setInterval(this.tick, 1000);
  },
  beforeDestroy() {
    if (this._timer) clearInterval(this._timer);
  },
  methods: {
    tick() {
      const r = this.rent;
      const outputAt = r && r.output_time ? parseDateTime(r.output_date, r.output_time) : null;
      if (!outputAt) {
        this.tr = null;
        return;
      }

      const now = new Date();
      const diff = outputAt - now;

      if (diff <= 0) {
        // Debajo de "SALIDA" va cuánto se pasó: +3d, +2h, +15min.
        const late = -diff;
        const lateDays = Math.floor(late / 86400000);
        const lateHours = Math.floor(late / 3600000);
        const lateMinutes = Math.floor(late / 60000);
        const label = lateDays >= 1 ? `+${lateDays}d` : (lateHours >= 1 ? `+${lateHours}h` : `+${lateMinutes}min`);
        this.tr = { state: 'expired', fraction: 1, value: 'SALIDA', label, full: '' };
        return;
      }

      // Escala fija: aro lleno con 24 h o más por delante. Así dos habitaciones
      // con la misma hora de salida se ven iguales sin importar cuándo entraron.
      const fraction = Math.min(1, Math.max(0.02, diff / FULL_RING_MS));

      const days = Math.floor(diff / 86400000);
      const hours = Math.floor((diff % 86400000) / 3600000);
      const minutes = Math.floor((diff % 3600000) / 60000);

      let value;
      let label;
      if (days >= 1) {
        value = `${days}d`;
        label = `${hours}h`;
      } else if (hours >= 1) {
        value = `${hours}h`;
        label = `${minutes}min`;
      } else {
        value = String(Math.max(1, minutes));
        label = 'min';
      }

      const parts = [];
      if (days) parts.push(`${days} ${days === 1 ? 'día' : 'días'}`);
      if (hours) parts.push(`${hours} h`);
      parts.push(`${minutes} min`);

      this.tr = {
        state: diff <= WARNING_MS ? 'warning' : 'ok',
        fraction,
        value,
        label,
        full: parts.join(' '),
      };
    },
  },
};
</script>

<style scoped>
.room-ring {
  position: relative;
  width: 58px;
  height: 58px;
  flex: 0 0 auto;
  border-radius: 50%;
  background: rgba(0, 0, 0, 0.22);
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.18);
}

.room-ring__svg {
  width: 100%;
  height: 100%;
  transform: rotate(-90deg);
}

.room-ring__track,
.room-ring__arc {
  fill: none;
  stroke-width: 4;
}

.room-ring__track {
  stroke: rgba(255, 255, 255, 0.25);
}

.room-ring__arc {
  stroke-linecap: round;
  transition: stroke-dashoffset 0.6s ease, stroke 0.3s ease;
}

.room-ring--ok .room-ring__arc { stroke: #22c55e; }
.room-ring--warning .room-ring__arc { stroke: #fbbf24; }

.room-ring__text {
  position: absolute;
  inset: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: #fff;
  line-height: 1;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.35);
}

.room-ring__value {
  font-size: 16px;
  font-weight: 700;
}

.room-ring__label {
  font-size: 10px;
  font-weight: 600;
  margin-top: 1px;
  opacity: 0.9;
}

/* Vencido: fondo blanco con aro y texto rojos, parpadeando, para que se vea
   de lejos aunque la tarjeta ya sea roja. */
.room-ring--expired {
  background: #fff;
  animation: room-ring-blink 1s ease-in-out infinite;
}
.room-ring--expired .room-ring__track { stroke: rgba(239, 68, 68, 0.25); }
.room-ring--expired .room-ring__arc { stroke: #ef4444; }
/* !important: las tarjetas fuerzan `.card-rent span { color: white !important }`
   y el texto quedaba blanco sobre blanco (círculo vacío). */
.room-ring--expired .room-ring__text,
.room-ring--expired .room-ring__text .room-ring__value,
.room-ring--expired .room-ring__text .room-ring__label {
  color: #dc2626 !important;
  text-shadow: none !important;
}
.room-ring--expired .room-ring__value {
  font-size: 10px;
  font-weight: 800;
  letter-spacing: 0.2px;
}
.room-ring--expired .room-ring__label {
  font-size: 10px;
  font-weight: 700;
}

@keyframes room-ring-blink {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.55; transform: scale(0.94); }
}

@media (max-width: 576px) {
  .room-ring {
    width: 46px;
    height: 46px;
  }
  .room-ring__value { font-size: 13px; }
  .room-ring__label { font-size: 8px; }
  .room-ring--expired .room-ring__value { font-size: 8px; }
  .room-ring--expired .room-ring__label { font-size: 8px; }
}
</style>
