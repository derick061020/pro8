<template>
  <transition name="crd-fade">
    <div v-if="visible" class="crd-overlay" @mousedown.self="onOverlayClick">
      <div class="crd-dialog" role="dialog" aria-modal="true">

        <!-- Botón cerrar -->
        <button class="crd-close" @click="close" aria-label="Cerrar">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>

        <!-- Éxito -->
        <template v-if="success">
          <div class="crd-icon-wrap crd-icon-wrap--success">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
          </div>
          <h3 class="crd-title">¡Reclamo registrado!</h3>
          <p class="crd-subtitle">Su código de seguimiento es:</p>

          <div class="crd-code-box">
            <span class="crd-code-text">{{ code }}</span>
            <button class="crd-copy-btn" @click="copyCode" :class="{ 'crd-copy-btn--copied': copied }">
              <svg v-if="!copied" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
              {{ copied ? 'Copiado' : 'Copiar' }}
            </button>
          </div>

          <p class="crd-note">
            Guarde este código para consultar el estado de su reclamo.<br>
            El plazo máximo de respuesta es de <strong>15 días hábiles</strong>.
          </p>

          <!-- Aviso correo -->
          <p v-if="email" class="crd-note" style="margin-top:8px;">
            Se envió una copia de su reclamo a <strong>{{ email }}</strong>.
          </p>
        </template>

        <!-- Error -->
        <template v-else>
          <div class="crd-icon-wrap crd-icon-wrap--error">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </div>
          <h3 class="crd-title">No se pudo registrar</h3>
          <p class="crd-error-msg">{{ errorMsg }}</p>
        </template>

        <!-- Footer -->
        <div class="crd-footer">
          <a v-if="success && pdfUrl" :href="pdfUrl" target="_blank" rel="noopener noreferrer" class="crd-pdf-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
              <polyline points="7 10 12 15 17 10"></polyline>
              <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            Descargar constancia (PDF)
          </a>
          <button class="crd-btn-primary" @click="close">
            {{ success ? 'Aceptar' : 'Cerrar' }}
          </button>
        </div>

      </div>
    </div>
  </transition>
</template>

<style scoped>
/* ── Overlay ── */
.crd-overlay {
  position: fixed;
  inset: 0;
  z-index: 2000;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
}

/* ── Dialog card ── */
.crd-dialog {
  position: relative;
  background: #ffffff;
  border: 1px solid #e4e4e7;
  border-radius: 12px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12), 0 2px 8px rgba(0, 0, 0, 0.06);
  width: 100%;
  max-width: 420px;
  padding: 36px 32px 28px;
  text-align: center;
  font-family: -apple-system, BlinkMacSystemFont, "Inter", "Segoe UI", sans-serif;
  color: #09090b;
}

/* ── Close button ── */
.crd-close {
  position: absolute;
  top: 14px;
  right: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
  background: transparent;
  border: 1px solid #e4e4e7;
  border-radius: 6px;
  color: #71717a;
  cursor: pointer;
  transition: background 0.15s, color 0.15s;
  padding: 0;
}
.crd-close:hover {
  background: #f4f4f5;
  color: #09090b;
}

/* ── Icon ── */
.crd-icon-wrap {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 56px;
  height: 56px;
  border-radius: 50%;
  margin-bottom: 18px;
}
.crd-icon-wrap--success {
  background: #f0fdf4;
  color: #16a34a;
  border: 1px solid #bbf7d0;
}
.crd-icon-wrap--error {
  background: #fef2f2;
  color: #dc2626;
  border: 1px solid #fecaca;
}

/* ── Text ── */
.crd-title {
  font-size: 18px;
  font-weight: 600;
  color: #09090b;
  margin: 0 0 6px;
  letter-spacing: -0.02em;
}
.crd-subtitle {
  font-size: 13px;
  color: #71717a;
  margin: 0 0 14px;
}
.crd-note {
  font-size: 12px;
  color: #a1a1aa;
  line-height: 1.6;
  margin: 14px 0 0;
}
.crd-error-msg {
  font-size: 13px;
  color: #dc2626;
  line-height: 1.6;
  margin: 8px 0 0;
}

/* ── Code box ── */
.crd-code-box {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background: #f4f4f5;
  border: 1px solid #e4e4e7;
  border-radius: 8px;
  padding: 10px 16px;
}
.crd-code-text {
  font-family: 'Consolas', 'Monaco', 'Fira Code', monospace;
  font-size: 16px;
  font-weight: 700;
  color: #18181b;
  letter-spacing: 2px;
}
.crd-copy-btn {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-size: 12px;
  font-weight: 500;
  font-family: inherit;
  padding: 4px 10px;
  border-radius: 6px;
  border: 1px solid #e4e4e7;
  background: #ffffff;
  color: #18181b;
  cursor: pointer;
  transition: background 0.15s, border-color 0.15s;
  white-space: nowrap;
}
.crd-copy-btn:hover {
  background: #f4f4f5;
}
.crd-copy-btn--copied {
  border-color: #bbf7d0;
  background: #f0fdf4;
  color: #16a34a;
}

/* ── PDF button ── */
.crd-pdf-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  font-size: 13px;
  font-weight: 500;
  color: #16a34a;
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  border-radius: 8px;
  text-decoration: none;
  transition: background 0.15s;
}
.crd-pdf-btn:hover {
  background: #dcfce7;
}

/* ── Footer ── */
.crd-footer {
  margin-top: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
}
.crd-btn-primary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  height: 36px;
  padding: 0 20px;
  font-size: 14px;
  font-weight: 500;
  font-family: inherit;
  background: #18181b;
  color: #fafafa;
  border: 1px solid #18181b;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.15s;
  min-width: 100px;
}
.crd-btn-primary:hover {
  background: #27272a;
}

/* ── Transition ── */
.crd-fade-enter-active,
.crd-fade-leave-active {
  transition: opacity 0.2s ease;
}
.crd-fade-enter-active .crd-dialog,
.crd-fade-leave-active .crd-dialog {
  transition: opacity 0.2s ease, transform 0.2s ease;
}
.crd-fade-enter .crd-dialog,
.crd-fade-leave-to .crd-dialog {
  opacity: 0;
  transform: scale(0.96) translateY(-8px);
}
.crd-fade-enter,
.crd-fade-leave-to {
  opacity: 0;
}
</style>

<script>
export default {
  name: 'ClaimResultDialog',

  props: {
    visible: {
      type: Boolean,
      default: false,
    },
    success: {
      type: Boolean,
      default: false,
    },
    code: {
      type: String,
      default: '',
    },
    errorMsg: {
      type: String,
      default: 'Ocurrió un error al registrar el reclamo. Inténtelo nuevamente.',
    },
    pdfUrl: {
      type: String,
      default: '',
    },
    email: {
      type: String,
      default: '',
    },
  },

  data() {
    return {
      copied: false,
    };
  },

  methods: {
    close() {
      this.$emit('update:visible', false);
      this.$emit('closed');
    },

    onOverlayClick() {
      // no cerrar al hacer clic en el overlay (consistente con close-on-click-modal: false)
    },

    copyCode() {
      const write = () => {
        this.copied = true;
        setTimeout(() => { this.copied = false; }, 2000);
      };
      if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(this.code).then(write).catch(() => this.fallbackCopy(write));
      } else {
        this.fallbackCopy(write);
      }
    },

    fallbackCopy(cb) {
      const el = document.createElement('textarea');
      el.value = this.code;
      el.style.cssText = 'position:fixed;opacity:0';
      document.body.appendChild(el);
      el.select();
      try {
        document.execCommand('copy');
        cb && cb();
      } catch {
        this.$message && this.$message.error('No se pudo copiar automáticamente');
      }
      document.body.removeChild(el);
    },
  },
};
</script>
