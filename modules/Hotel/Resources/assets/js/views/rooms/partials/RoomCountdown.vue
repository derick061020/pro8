<template>
  <div class="rent-countdown-simple" v-if="tr">
    <div class="countdown-display">
      <div class="countdown-unit">
        <div class="countdown-value">{{ tr.days }}</div>
        <div class="countdown-label">{{ tr.days === 1 ? 'día' : 'días' }}</div>
      </div>
      <div class="countdown-separator"></div>
      <div class="countdown-unit">
        <div class="countdown-value">{{ pad(tr.hours) }}</div>
        <div class="countdown-label">hora</div>
      </div>
      <div class="countdown-separator"></div>
      <div class="countdown-unit">
        <div class="countdown-value">{{ pad(tr.minutes) }}</div>
        <div class="countdown-label">min</div>
      </div>
      <div class="countdown-separator"></div>
      <div class="countdown-unit">
        <div class="countdown-value">{{ pad(tr.seconds) }}</div>
        <div class="countdown-label">seg</div>
      </div>
    </div>
  </div>
</template>

<script>
// Componente aislado para el contador regresivo. Maneja su propio intervalo de
// 1s internamente, así solo este pequeño componente se re-renderiza cada
// segundo en lugar de toda la grilla de recepción (clave para el rendimiento).
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
      _timer: null,
    };
  },
  watch: {
    // Si cambia la salida (p. ej. tras una extensión), recalcular de inmediato.
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
    pad(n) {
      return String(n).padStart(2, '0');
    },
    tick() {
      const r = this.rent;
      if (!r || !r.output_date || !r.output_time) {
        this.tr = null;
        return;
      }

      const dateParts = String(r.output_date).split('-');
      const timeParts = String(r.output_time).split(':');
      if (dateParts.length < 3 || timeParts.length < 2) {
        this.tr = null;
        return;
      }

      const outputDate = new Date(
        parseInt(dateParts[0], 10),
        parseInt(dateParts[1], 10) - 1,
        parseInt(dateParts[2], 10),
        parseInt(timeParts[0], 10),
        parseInt(timeParts[1], 10),
        0, 0
      );

      const diff = outputDate - new Date();

      if (diff > 0) {
        this.tr = {
          days: Math.floor(diff / 86400000),
          hours: Math.floor((diff % 86400000) / 3600000),
          minutes: Math.floor((diff % 3600000) / 60000),
          seconds: Math.floor((diff % 60000) / 1000),
          isExpired: false,
        };
      } else {
        this.tr = { days: 0, hours: 0, minutes: 0, seconds: 0, isExpired: true };
      }
    },
  },
};
</script>
