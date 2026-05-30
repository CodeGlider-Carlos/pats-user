/*
Archivo: ez/patsbot/js/catbot_widget.js
Módulo: PATS / CAT BOT
Propósito: Controlador front-end responsive del widget reutilizable CAT BOT PATS.
Responsabilidad: Inicializar múltiples instancias, enviar preguntas al endpoint real y renderizar según audiencia.
Conexiones: endpoints/catbot_ask.php y endpoints/catbot_feedback.php.
Tipo: Transversal reutilizable dentro de PATSBOT.
*/

(function () {
  'use strict';

  const widgets = new WeakMap();

  function esc(value) {
    return String(value || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function br(value) {
    return esc(value).replace(/\n/g, '<br>');
  }

  function txt(value, fallback = '') {
    const out = String(value || '').trim();
    return out || fallback;
  }

  function confidenceLabel(value) {
    const n = Number(value || 0);
    if (n >= 0.75) return 'Alta';
    if (n >= 0.45) return 'Media';
    if (n > 0) return 'Baja';
    return 'Sin coincidencia';
  }

  function readConfig(root) {
    try {
      return JSON.parse(root.getAttribute('data-config') || '{}');
    } catch (err) {
      console.error('[PATS CATBOT WIDGET] Configuración inválida.', err);
      return {};
    }
  }

  function contextPayload(root) {
    const out = {};
    root.querySelectorAll('[data-cw-context]').forEach((el) => {
      out[el.getAttribute('data-cw-context')] = el.value || '';
    });
    return out;
  }

  function setContext(root, ctx) {
    if (!ctx || typeof ctx !== 'object') return;

    Object.keys(ctx).forEach((key) => {
      const el = root.querySelector(`[data-cw-context="${key}"]`);
      if (el) el.value = ctx[key] || '';
    });

    updateContextSummary(root);
  }

  function updateContextSummary(root) {
    const ctx = contextPayload(root);
    const el = root.querySelector('[data-cw-context-summary]');
    if (!el) return;

    const parts = [];

    if (ctx.nombre_usuario) parts.push(ctx.nombre_usuario);
    if (ctx.tipo_duda) parts.push(labelize(ctx.tipo_duda));
    if (ctx.estatus_pats && ctx.estatus_pats !== 'no_consultado') parts.push(labelize(ctx.estatus_pats));

    el.textContent = parts.length ? parts.join(' · ') : 'Sin datos adicionales';
  }

  function labelize(value) {
    return String(value || '')
      .replace(/_/g, ' ')
      .replace(/\b\w/g, (m) => m.toUpperCase());
  }

  function addMessage(root, type, text, meta) {
    const box = root.querySelector('[data-cw-messages]');
    if (!box) return;

    const isUser = type === 'user';
    const article = document.createElement('article');

    article.className = `pats-cw-msg ${isUser ? 'is-user' : 'is-bot'}`;
    article.innerHTML = `
      <div class="pats-cw-avatar">${isUser ? 'T' : 'P'}</div>
      <div class="pats-cw-bubble">
        <strong>${isUser ? 'Tú' : 'Asistente PATS'}</strong>
        <p>${br(text)}</p>
        ${meta ? `<small>${esc(meta)}</small>` : ''}
      </div>
    `;

    box.appendChild(article);
    requestAnimationFrame(() => {
      box.scrollTop = box.scrollHeight;
    });
  }

  function loading(root, on) {
    const box = root.querySelector('[data-cw-messages]');
    if (!box) return;

    const old = box.querySelector('[data-cw-loading]');
    if (old) old.remove();

    if (!on) return;

    const article = document.createElement('article');
    article.className = 'pats-cw-msg is-bot';
    article.setAttribute('data-cw-loading', '1');
    article.innerHTML = `
      <div class="pats-cw-avatar">P</div>
      <div class="pats-cw-bubble">
        <strong>Asistente PATS</strong>
        <p>Consultando<span class="pats-cw-dots"><i></i><i></i><i></i></span></p>
      </div>
    `;

    box.appendChild(article);
    requestAnimationFrame(() => {
      box.scrollTop = box.scrollHeight;
    });
  }

  function makeWhatsapp(data, ctx) {
    const nombre = txt(ctx.nombre_usuario);
    const saludo = nombre ? `Hola ${nombre}. ` : 'Hola. ';
    const base = txt(data.respuesta_usuario_sugerida, 'Con gusto te apoyamos con tu consulta PATS.');
    return saludo + base;
  }

  function compactSummary(data, config) {
    const audiencia = config.audiencia || 'usuario';

    if (audiencia === 'usuario') {
      if (data.__mode === 'whatsapp' && data.respuesta_whatsapp) return data.respuesta_whatsapp;
      return txt(data.respuesta_usuario_sugerida, data.answer || 'Respuesta generada.');
    }

    if (data.__mode === 'whatsapp' && data.respuesta_whatsapp) {
      return data.respuesta_whatsapp;
    }

    if (data.respuesta_usuario_sugerida) {
      return data.respuesta_usuario_sugerida;
    }

    if (data.ruta_operativa) {
      return data.ruta_operativa;
    }

    return data.answer || 'Respuesta generada.';
  }

  function copyText(value, btn) {
    const text = String(value || '').trim();
    if (!text) return;

    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(text);
    } else {
      const ta = document.createElement('textarea');
      ta.value = text;
      document.body.appendChild(ta);
      ta.select();
      document.execCommand('copy');
      ta.remove();
    }

    if (btn) {
      const old = btn.textContent;
      btn.textContent = 'Copiado';
      setTimeout(() => { btn.textContent = old; }, 1200);
    }
  }

  function resultSection(title, content, className, copy) {
    const c = txt(content);
    if (!c) return '';

    return `
      <section class="pats-cw-rbox ${className || ''}">
        <header>
          <h4>${esc(title)}</h4>
          ${copy ? `<button type="button" data-cw-copy="${esc(c)}">Copiar</button>` : ''}
        </header>
        <p>${br(c)}</p>
      </section>
    `;
  }

  function alertsSection(alerts) {
    if (!Array.isArray(alerts) || !alerts.length) return '';

    return `
      <section class="pats-cw-rbox is-warning">
        <h4>Alertas</h4>
        <ul>${alerts.map((a) => `<li>${esc(a)}</li>`).join('')}</ul>
      </section>
    `;
  }

  function feedbackSection(logId, enabled) {
    if (!enabled || !logId) return '';

    return `
      <section class="pats-cw-rbox is-feedback">
        <h4>¿Te ayudó esta respuesta?</h4>
        <div class="pats-cw-feedback">
          <button type="button" data-cw-feedback="UTIL">Sí</button>
          <button type="button" data-cw-feedback="CONFUSO">Confusa</button>
          <button type="button" data-cw-feedback="NO_UTIL">No</button>
        </div>
        <textarea data-cw-feedback-comment rows="2" maxlength="1000" placeholder="Comentario opcional"></textarea>
        <small data-cw-feedback-status></small>
      </section>
    `;
  }

  function renderResult(root, data, config) {
    const panel = root.querySelector('[data-cw-result]');
    if (!panel) return;

    const ctx = contextPayload(root);
    const audiencia = config.audiencia || 'usuario';
    const mode = data.__mode || 'manual';
    const userAnswer = txt(data.respuesta_usuario_sugerida, data.answer || 'No encontré una respuesta disponible para esta consulta.');
    const whats = txt(data.respuesta_whatsapp) || makeWhatsapp(data, ctx);

    let body = '';

    if (audiencia === 'usuario') {
      if (mode === 'whatsapp') {
        body += resultSection('Texto para WhatsApp', whats, 'is-whatsapp', true);
      } else {
        body += resultSection('Respuesta', userAnswer, 'is-primary', false);
      }

      /*
      Para usuario final nunca se muestran:
      - validaciones internas
      - ruta operativa
      - módulos a consultar
      - errores a evitar
      - confianza / fuente / log visibles
      */
      body += feedbackSection(data.log_id || '', config.mostrarFeedback !== false);

      panel.innerHTML = `
        <div class="pats-cw-result-inner">
          <header class="pats-cw-result-head is-user">
            <span>Respuesta PATS</span>
            <h3>Información para ti</h3>
          </header>
          ${body}
        </div>
      `;
    } else {
      body += resultSection('Qué decir al usuario', userAnswer, 'is-primary', true);
      body += resultSection('WhatsApp', whats, 'is-whatsapp', true);

      if (mode === 'operativo' || mode === 'auto') {
        body += resultSection('Validar antes de confirmar', data.validaciones, 'is-critical', true);
        body += resultSection('Ruta interna', data.ruta_operativa, '', true);
        body += resultSection('Escalar si...', data.cuando_escalar, 'is-warning', true);
        body += resultSection('Evitar', data.errores_evitar, 'is-critical', true);
        body += alertsSection(data.alertas);
      }

      body += feedbackSection(data.log_id || '', config.mostrarFeedback !== false);

      panel.innerHTML = `
        <div class="pats-cw-result-inner">
          <header class="pats-cw-result-head">
            <span>Resultado</span>
            <h3>Guía de atención</h3>
            <div>
              <b>${esc(data.source || 'N/D')}</b>
              <b>${esc(confidenceLabel(data.confidence))}</b>
            </div>
          </header>
          ${body}
        </div>
      `;
    }

    panel.querySelectorAll('[data-cw-copy]').forEach((btn) => {
      btn.addEventListener('click', () => copyText(btn.getAttribute('data-cw-copy') || '', btn));
    });

    panel.querySelectorAll('[data-cw-feedback]').forEach((btn) => {
      btn.addEventListener('click', () => sendFeedback(root, btn.getAttribute('data-cw-feedback'), data.log_id || '', config));
    });
  }

  async function sendFeedback(root, value, logId, config) {
    if (!logId || !config.feedbackEndpoint) return;

    const panel = root.querySelector('[data-cw-result]');
    const comment = panel ? txt(panel.querySelector('[data-cw-feedback-comment]')?.value) : '';
    const status = panel ? panel.querySelector('[data-cw-feedback-status]') : null;

    if (status) status.textContent = 'Guardando...';

    try {
      const response = await fetch(config.feedbackEndpoint, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          log_id: logId,
          valor: value,
          comentario: comment
        })
      });

      const raw = await response.text();
      let data;

      try {
        data = JSON.parse(raw);
      } catch (err) {
        throw new Error('Respuesta no válida del endpoint.');
      }

      if (!response.ok || !data.ok) {
        throw new Error(data.error || 'No fue posible guardar.');
      }

      if (status) status.textContent = 'Gracias, tu respuesta fue registrada.';
      panel.querySelectorAll('[data-cw-feedback]').forEach((b) => {
        b.classList.toggle('is-selected', b.getAttribute('data-cw-feedback') === value);
      });
    } catch (err) {
      if (status) status.textContent = err.message || 'Error al guardar.';
    }
  }

  async function ask(root, question) {
    const state = widgets.get(root);
    if (!state || state.sending) return;

    const config = state.config;
    const mode = state.mode;
    const textarea = root.querySelector('[data-cw-question]');
    const send = root.querySelector('[data-cw-send]');

    state.sending = true;

    if (textarea) textarea.disabled = true;
    if (send) send.disabled = true;

    addMessage(root, 'user', question);
    loading(root, true);

    try {
      const ctx = contextPayload(root);

      const response = await fetch(config.askEndpoint, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          question: question,
          mode: mode === 'whatsapp' ? 'manual' : mode,
          source: config.contexto || 'catbot_widget',
          audience: config.audiencia || 'usuario',
          context: ctx
        })
      });

      const raw = await response.text();
      let data;

      try {
        data = JSON.parse(raw);
      } catch (err) {
        throw new Error('El endpoint no devolvió JSON válido.');
      }

      if (!response.ok || !data.ok) {
        throw new Error(data.error || 'No fue posible consultar el asistente.');
      }

      data.__mode = mode;

      if (mode === 'whatsapp' && !data.respuesta_whatsapp) {
        data.respuesta_whatsapp = makeWhatsapp(data, ctx);
      }

      loading(root, false);

      addMessage(
        root,
        'bot',
        compactSummary(data, config),
        config.audiencia === 'usuario' ? '' : `Fuente: ${data.source || 'N/D'} · Confianza: ${confidenceLabel(data.confidence)}`
      );

      renderResult(root, data, config);
    } catch (err) {
      loading(root, false);
      addMessage(root, 'bot', 'No pude completar la consulta. ' + (err.message || 'Intenta nuevamente.'));
    } finally {
      state.sending = false;
      if (textarea) textarea.disabled = false;
      if (send) send.disabled = false;
      if (textarea) textarea.focus();
    }
  }

  function setMode(root, mode) {
    const state = widgets.get(root);
    if (!state) return;

    const config = state.config || {};

    if (config.audiencia === 'usuario' && !['manual', 'whatsapp'].includes(mode)) {
      mode = 'manual';
    }

    if (!['manual', 'operativo', 'auto', 'whatsapp'].includes(mode)) {
      mode = 'manual';
    }

    state.mode = mode;

    root.querySelectorAll('[data-cw-mode]').forEach((btn) => {
      btn.classList.toggle('is-active', btn.getAttribute('data-cw-mode') === mode);
    });

    const label = root.querySelector('[data-cw-mode-label]');
    const labels = {
      manual: config.audiencia === 'usuario' ? 'Consulta PATS' : 'Usuario',
      operativo: 'Interno',
      auto: 'Completo',
      whatsapp: 'WhatsApp'
    };

    if (label) label.textContent = labels[mode] || labels.manual;
  }

  function clearChat(root) {
    const box = root.querySelector('[data-cw-messages]');
    const result = root.querySelector('[data-cw-result]');

    if (box) {
      box.innerHTML = `
        <article class="pats-cw-msg is-bot">
          <div class="pats-cw-avatar">P</div>
          <div class="pats-cw-bubble">
            <strong>Asistente PATS</strong>
            <p>Hola, puedo ayudarte con dudas generales sobre PATS.</p>
          </div>
        </article>
      `;
    }

    if (result) {
      result.innerHTML = `
        <div class="pats-cw-empty">
          <div>P</div>
          <h3>Respuesta PATS</h3>
          <p>Aquí aparecerá la respuesta a tu consulta.</p>
        </div>
      `;
    }
  }

  function resize(root) {
    const textarea = root.querySelector('[data-cw-question]');
    if (!textarea) return;

    textarea.style.height = 'auto';
    textarea.style.height = Math.min(textarea.scrollHeight, 132) + 'px';
  }

  function boot(root) {
    if (widgets.has(root)) return;

    const config = readConfig(root);

    widgets.set(root, {
      config: config,
      mode: config.defaultMode || 'manual',
      sending: false
    });

    setContext(root, config.contextoInicial || {});
    setMode(root, config.defaultMode || 'manual');

    root.querySelectorAll('[data-cw-context]').forEach((el) => {
      el.addEventListener('input', () => updateContextSummary(root));
      el.addEventListener('change', () => updateContextSummary(root));
    });

    root.querySelectorAll('[data-cw-mode]').forEach((btn) => {
      btn.addEventListener('click', () => setMode(root, btn.getAttribute('data-cw-mode') || 'manual'));
    });

    root.querySelectorAll('[data-cw-prompt]').forEach((btn) => {
      btn.addEventListener('click', () => {
        const textarea = root.querySelector('[data-cw-question]');
        if (!textarea) return;

        textarea.value = btn.getAttribute('data-cw-prompt') || '';
        resize(root);
        textarea.focus();
      });
    });

    const form = root.querySelector('[data-cw-form]');
    const textarea = root.querySelector('[data-cw-question]');

    if (form) {
      form.addEventListener('submit', (event) => {
        event.preventDefault();

        const question = txt(textarea ? textarea.value : '');
        if (!question) return;

        if (textarea) textarea.value = '';
        resize(root);
        ask(root, question);
      });
    }

    if (textarea) {
      textarea.addEventListener('input', () => resize(root));

      textarea.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && !event.shiftKey && window.innerWidth > 720) {
          event.preventDefault();
          form && form.requestSubmit();
        }
      });
    }

    const clearChatBtn = root.querySelector('[data-cw-clear-chat]');
    if (clearChatBtn) clearChatBtn.addEventListener('click', () => clearChat(root));

    const clearContextBtn = root.querySelector('[data-cw-clear-context]');
    if (clearContextBtn) {
      clearContextBtn.addEventListener('click', () => {
        root.querySelectorAll('[data-cw-context]').forEach((el) => {
          if (el.tagName === 'SELECT') el.selectedIndex = 0;
          else el.value = '';
        });
        updateContextSummary(root);
      });
    }

    resize(root);
    updateContextSummary(root);

    root.PatsCatbotWidget = {
      ask: (question) => ask(root, txt(question)),
      setMode: (mode) => setMode(root, mode),
      clear: () => clearChat(root),
      getContext: () => contextPayload(root),
      setContext: (ctx) => setContext(root, ctx)
    };
  }

  window.PatsCatbotWidgetInit = function (selector) {
    const nodes = selector
      ? document.querySelectorAll(selector)
      : document.querySelectorAll('[data-pats-catbot-widget]');

    nodes.forEach(boot);
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => window.PatsCatbotWidgetInit());
  } else {
    window.PatsCatbotWidgetInit();
  }
})();
