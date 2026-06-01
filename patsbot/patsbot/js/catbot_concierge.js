/*
=========================================================
Archivo: ez/patsbot/js/catbot_concierge.js
Módulo: PATS / CAT BOT Concierge
Propósito: Controlar la interfaz real de atención al usuario PATS.
Responsabilidad: Enviar preguntas al endpoint existente, agregar contexto de atención, pintar respuesta para usuario, WhatsApp, validaciones, ruta interna, feedback y copia rápida.
Conexiones: endpoints/catbot_ask.php y endpoints/catbot_feedback.php.
Tipo: Específico del modo Concierge. No modifica entrenamiento.
=========================================================
*/

(function () {
  'use strict';

  const cfg = window.PATS_CATBOT_CONFIG || {};

  const endpoint = cfg.endpoint || 'endpoints/catbot_ask.php';
  const feedbackEndpoint = cfg.feedbackEndpoint || 'endpoints/catbot_feedback.php';

  const $form = document.getElementById('catbotForm');
  const $question = document.getElementById('catbotQuestion');
  const $sendBtn = document.getElementById('catbotSendBtn');
  const $messages = document.getElementById('catbotMessages');
  const $resultPanel = document.getElementById('catbotResultPanel');
  const $clearBtn = document.getElementById('catbotClearBtn');
  const $counter = document.getElementById('catbotCounter');
  const $toast = document.getElementById('catbotToast');
  const $whatsappHint = document.getElementById('catbotWhatsappHint');

  let currentMode = cfg.defaultMode || 'manual';
  let isSending = false;
  let isSendingFeedback = false;
  let lastLogId = null;
  let lastData = null;

  if (!$form || !$question || !$sendBtn || !$messages || !$resultPanel) {
    console.warn('[PATS CATBOT CONCIERGE] Elementos base no encontrados.');
    return;
  }

  function escapeHtml(value) {
    return String(value || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function nl2br(value) {
    return escapeHtml(value).replace(/\n/g, '<br>');
  }

  function text(value, fallback) {
    const t = String(value || '').trim();
    return t || (fallback || '');
  }

  function showToast(message) {
    if (!$toast) return;

    $toast.textContent = message;
    $toast.classList.add('is-visible');

    window.clearTimeout(showToast._timer);
    showToast._timer = window.setTimeout(() => {
      $toast.classList.remove('is-visible');
    }, 3600);
  }

  function scrollMessages() {
    requestAnimationFrame(() => {
      $messages.scrollTop = $messages.scrollHeight;
    });
  }

  function resizeTextarea() {
    $question.style.height = 'auto';
    $question.style.height = Math.min($question.scrollHeight, 180) + 'px';
  }

  function updateCounter() {
    if (!$counter) return;
    $counter.textContent = `${$question.value.length}/1200`;
  }

  function confidenceLabel(confidence) {
    const n = Number(confidence || 0);
    if (n >= 0.75) return 'Alta';
    if (n >= 0.45) return 'Media';
    if (n > 0) return 'Baja';
    return 'Sin coincidencia';
  }

  function getContext() {
    return {
      nombre_usuario: text(document.getElementById('ctxNombre')?.value),
      telefono: text(document.getElementById('ctxTelefono')?.value),
      tipo_duda: text(document.getElementById('ctxTipoDuda')?.value),
      estatus_pats: text(document.getElementById('ctxEstatus')?.value, 'no_consultado'),
      canal: text(document.getElementById('ctxCanal')?.value, 'presencial'),
      prioridad: text(document.getElementById('ctxPrioridad')?.value, 'normal'),
      operador: cfg.user || '',
      rol_operador: cfg.role || '',
      region: cfg.region || '',
      unidad: cfg.unidad || ''
    };
  }

  function contextLine(ctx) {
    const parts = [];

    if (ctx.tipo_duda) parts.push(`Duda: ${ctx.tipo_duda}`);
    if (ctx.estatus_pats && ctx.estatus_pats !== 'no_consultado') parts.push(`Estatus: ${ctx.estatus_pats}`);
    if (ctx.canal) parts.push(`Canal: ${ctx.canal}`);
    if (ctx.prioridad && ctx.prioridad !== 'normal') parts.push(`Prioridad: ${ctx.prioridad}`);

    return parts.join(' · ');
  }

  function clearContext() {
    ['ctxNombre', 'ctxTelefono'].forEach((id) => {
      const el = document.getElementById(id);
      if (el) el.value = '';
    });

    const defaults = {
      ctxTipoDuda: '',
      ctxEstatus: 'no_consultado',
      ctxCanal: 'presencial',
      ctxPrioridad: 'normal'
    };

    Object.keys(defaults).forEach((id) => {
      const el = document.getElementById(id);
      if (el) el.value = defaults[id];
    });

    showToast('Contexto limpiado.');
  }

  function setSending(state) {
    isSending = Boolean(state);
    $sendBtn.disabled = isSending;
    $question.disabled = isSending;

    if (isSending) {
      $sendBtn.innerHTML = '<span>Consultando...</span>';
    } else {
      $sendBtn.innerHTML = 'Consultar';
    }
  }

  function addMessage(type, value, meta) {
    const isUser = type === 'user';
    const $article = document.createElement('article');
    $article.className = `pats-concierge-msg ${isUser ? 'is-user' : 'is-bot'}`;

    $article.innerHTML = `
      <div class="pats-concierge-msg__avatar">${isUser ? 'T' : 'P'}</div>
      <div class="pats-concierge-bubble">
        <strong>${isUser ? 'Tú' : 'Asistente PATS'}</strong>
        <p>${nl2br(value)}</p>
        ${meta ? `<small>${escapeHtml(meta)}</small>` : ''}
      </div>
    `;

    $messages.appendChild($article);
    scrollMessages();
    return $article;
  }

  function addLoadingMessage() {
    const $article = document.createElement('article');
    $article.className = 'pats-concierge-msg is-bot';
    $article.setAttribute('data-loading', '1');
    $article.innerHTML = `
      <div class="pats-concierge-msg__avatar">P</div>
      <div class="pats-concierge-bubble">
        <strong>Asistente PATS</strong>
        <p>Analizando conocimiento PATS, reglas de atención y ruta segura...</p>
        <div class="pats-concierge-typing"><i></i><i></i><i></i></div>
      </div>
    `;
    $messages.appendChild($article);
    scrollMessages();
    return $article;
  }

  function removeLoadingMessage() {
    const $loading = $messages.querySelector('[data-loading="1"]');
    if ($loading) $loading.remove();
  }

  function buildWhatsappText(data) {
    if (data.respuesta_whatsapp) return text(data.respuesta_whatsapp);

    const base = text(data.respuesta_usuario_sugerida || data.answer);

    if (!base) {
      return 'Con gusto te apoyamos. Permíteme validar la información de tu PATS para orientarte correctamente.';
    }

    const clean = base
      .replace(/\s+/g, ' ')
      .replace(/Importante:/gi, 'Importante:')
      .trim();

    if (clean.length <= 520) return clean;

    return clean.slice(0, 500).trim() + '...';
  }

  function compactBotSummary(data) {
    const parts = [];

    if (data.respuesta_usuario_sugerida) {
      parts.push(`Qué decir al usuario:\n${data.respuesta_usuario_sugerida}`);
    }

    if (data.validaciones) {
      parts.push(`Antes de confirmar:\n${data.validaciones}`);
    }

    if (data.cuando_escalar) {
      parts.push(`Escalar si:\n${data.cuando_escalar}`);
    }

    if (Array.isArray(data.alertas) && data.alertas.length) {
      parts.push(`Alertas:\n- ${data.alertas.join('\n- ')}`);
    }

    return parts.join('\n\n') || data.answer || 'Respuesta generada.';
  }

  function copyToClipboard(value) {
    const v = text(value);
    if (!v) {
      showToast('No hay texto para copiar.');
      return;
    }

    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(v).then(() => showToast('Texto copiado.')).catch(() => fallbackCopy(v));
      return;
    }

    fallbackCopy(v);
  }

  function fallbackCopy(value) {
    const ta = document.createElement('textarea');
    ta.value = value;
    ta.setAttribute('readonly', 'readonly');
    ta.style.position = 'fixed';
    ta.style.left = '-9999px';
    document.body.appendChild(ta);
    ta.select();

    try {
      document.execCommand('copy');
      showToast('Texto copiado.');
    } catch (err) {
      showToast('No fue posible copiar automáticamente.');
    }

    ta.remove();
  }

  function resultCard(title, content, options) {
    const txt = text(content);
    if (!txt) return '';

    const cls = options && options.className ? options.className : '';
    const copy = options && options.copy !== false;
    const label = options && options.label ? options.label : 'Copiar';

    return `
      <section class="pats-concierge-result-card ${cls}">
        <header>
          <h3>${escapeHtml(title)}</h3>
          ${copy ? `<button type="button" data-copy-text="${escapeHtml(txt)}">${escapeHtml(label)}</button>` : ''}
        </header>
        <p>${nl2br(txt)}</p>
      </section>
    `;
  }

  function renderPills(data) {
    const pills = [
      ['Fuente', data.source || 'N/D'],
      ['Confianza', confidenceLabel(data.confidence)],
      ['Intent', data.intent || 'N/D'],
      ['Log', data.log_id || 'N/D']
    ];

    return pills.map(([k, v]) => `<span><b>${escapeHtml(k)}:</b> ${escapeHtml(v)}</span>`).join('');
  }

  function renderAlerts(alerts) {
    if (!Array.isArray(alerts) || !alerts.length) return '';

    return `
      <section class="pats-concierge-result-card is-alert">
        <header><h3>Alertas</h3></header>
        <ul>${alerts.map((a) => `<li>${escapeHtml(a)}</li>`).join('')}</ul>
      </section>
    `;
  }

  function renderFeedback(logId) {
    if (!logId) return '';

    return `
      <section class="pats-concierge-feedback" data-log-id="${escapeHtml(logId)}">
        <header>
          <h3>¿Sirvió esta respuesta?</h3>
          <span>Esto alimenta entrenamiento supervisado.</span>
        </header>

        <div class="pats-concierge-feedback-actions">
          <button type="button" data-feedback="UTIL">Útil</button>
          <button type="button" data-feedback="CONFUSO">Confuso</button>
          <button type="button" data-feedback="INCORRECTO">Incorrecto</button>
          <button type="button" data-feedback="NO_UTIL">No útil</button>
        </div>

        <textarea id="catbotFeedbackComment" maxlength="1000" rows="3" placeholder="Comentario opcional para entrenamiento..."></textarea>
        <div id="catbotFeedbackStatus" class="pats-concierge-feedback-status"></div>
      </section>
    `;
  }

  function renderResultPanel(data) {
    lastLogId = data.log_id || null;
    lastData = data;

    const whatsapp = buildWhatsappText(data);
    const noPrometer = text(data.errores_evitar) || 'No prometer beneficios, precios, vigencia, disponibilidad, reactivación o cobertura sin validar en fuente autorizada.';
    const siguiente = text(data.siguiente_accion) || inferNextAction(data);

    $resultPanel.innerHTML = `
      <section class="pats-concierge-result-top">
        <span>Guía de atención PATS</span>
        <h2>Respuesta estructurada</h2>
        <p>Usa primero el bloque de usuario. Valida internamente antes de confirmar información sensible.</p>
        <div class="pats-concierge-result-pills">${renderPills(data)}</div>
      </section>

      ${resultCard('Qué decir al usuario', data.respuesta_usuario_sugerida || data.answer, { className: 'is-primary' })}
      ${resultCard('WhatsApp listo para copiar', whatsapp, { className: 'is-whatsapp', label: 'Copiar WhatsApp' })}
      ${resultCard('Antes de confirmar', data.validaciones, { className: 'is-warning' })}
      ${resultCard('Siguiente acción recomendada', siguiente, { className: 'is-action' })}
      ${resultCard('Qué NO prometer', noPrometer, { className: 'is-danger' })}
      ${resultCard('Ruta interna', data.ruta_operativa, { className: 'is-internal' })}
      ${resultCard('Módulos a consultar', data.modulos_consultar, {})}
      ${resultCard('Escalar si...', data.cuando_escalar, { className: 'is-warning' })}
      ${renderAlerts(data.alertas)}
      ${renderFeedback(lastLogId)}
    `;

    bindResultActions();
  }

  function inferNextAction(data) {
    const ctx = getContext();

    if (ctx.prioridad === 'critica') {
      return 'Escalar a ADMINPATS o responsable operativo antes de confirmar al usuario.';
    }

    if (ctx.estatus_pats === 'no_consultado') {
      return 'Consultar vigencia/estatus PATS antes de confirmar beneficios o precios preferenciales.';
    }

    if (ctx.tipo_duda === 'cotizacion') {
      return 'Canalizar a cotización o LEAD+ según el flujo vigente.';
    }

    if (ctx.tipo_duda === 'pago' || ctx.tipo_duda === 'reactivacion') {
      return 'Validar pago, comprobante y vigencia antes de indicar reactivación.';
    }

    if (Number(data.confidence || 0) < 0.45) {
      return 'Respuesta de baja confianza: validar manualmente y, si falta información, enviar a entrenamiento.';
    }

    return 'Responder al usuario con el texto sugerido y documentar/validar si el caso requiere confirmación operativa.';
  }

  function bindResultActions() {
    $resultPanel.querySelectorAll('[data-copy-text]').forEach((btn) => {
      btn.addEventListener('click', () => copyToClipboard(btn.getAttribute('data-copy-text') || ''));
    });

    $resultPanel.querySelectorAll('[data-feedback]').forEach((btn) => {
      btn.addEventListener('click', () => sendFeedback(btn.getAttribute('data-feedback')));
    });
  }

  function setFeedbackState(disabled) {
    $resultPanel.querySelectorAll('[data-feedback]').forEach((btn) => {
      btn.disabled = Boolean(disabled);
    });
  }

  function setFeedbackStatus(message, ok) {
    const el = document.getElementById('catbotFeedbackStatus');
    if (!el) return;

    el.textContent = message || '';
    el.classList.toggle('is-ok', ok === true);
    el.classList.toggle('is-error', ok === false);
  }

  async function sendFeedback(value) {
    if (!lastLogId) {
      showToast('No hay respuesta reciente para calificar.');
      return;
    }

    if (isSendingFeedback) return;

    const allowed = ['UTIL', 'NO_UTIL', 'INCORRECTO', 'CONFUSO'];
    if (!allowed.includes(value)) {
      showToast('Valor de feedback no válido.');
      return;
    }

    const comentario = text(document.getElementById('catbotFeedbackComment')?.value);

    isSendingFeedback = true;
    setFeedbackState(true);
    setFeedbackStatus('Registrando feedback...', null);

    try {
      const response = await fetch(feedbackEndpoint, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify({
          log_id: lastLogId,
          valor: value,
          comentario: comentario
        })
      });

      const raw = await response.text();
      let data = null;

      try {
        data = JSON.parse(raw);
      } catch (err) {
        throw new Error('El endpoint de feedback no devolvió JSON válido: ' + raw.slice(0, 240));
      }

      if (!response.ok || !data.ok) {
        throw new Error(data.error || 'No fue posible registrar feedback.');
      }

      $resultPanel.querySelectorAll('[data-feedback]').forEach((btn) => {
        btn.classList.toggle('is-selected', btn.getAttribute('data-feedback') === value);
      });

      setFeedbackStatus('Feedback registrado correctamente.', true);
      showToast('Feedback registrado.');
    } catch (err) {
      const msg = err && err.message ? err.message : 'No fue posible registrar feedback.';
      setFeedbackStatus(msg, false);
      showToast(msg);
    } finally {
      isSendingFeedback = false;
      setFeedbackState(false);
    }
  }

  async function askCatbot(question) {
    const ctx = getContext();
    let endpointMode = currentMode;

    if (currentMode === 'whatsapp') {
      endpointMode = 'manual';
    }

    const response = await fetch(endpoint, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      credentials: 'same-origin',
      body: JSON.stringify({
        question: question,
        source: 'catbot_concierge',
        mode: endpointMode,
        context: ctx
      })
    });

    const raw = await response.text();
    let data = null;

    try {
      data = JSON.parse(raw);
    } catch (err) {
      throw new Error('El endpoint no devolvió JSON válido: ' + raw.slice(0, 300));
    }

    if (!response.ok || !data.ok) {
      throw new Error(data.error || 'No fue posible consultar el asistente PATS.');
    }

    return data;
  }

  async function handleSubmit(event) {
    event.preventDefault();

    if (isSending) return;

    const q = text($question.value);

    if (!q) {
      showToast('Escribe la duda del usuario o usa una pregunta rápida.');
      $question.focus();
      return;
    }

    if (q.length > 1200) {
      showToast('La pregunta es demasiado larga. Resume la situación.');
      return;
    }

    const ctx = getContext();
    const meta = contextLine(ctx);

    addMessage('user', q, meta);
    $question.value = '';
    resizeTextarea();
    updateCounter();

    addLoadingMessage();
    setSending(true);

    try {
      const data = await askCatbot(q);
      removeLoadingMessage();

      const summary = compactBotSummary(data);
      addMessage(
        'bot',
        summary,
        `Fuente: ${data.source || 'N/D'} · Confianza: ${confidenceLabel(data.confidence)}${data.log_id ? ' · Log ' + data.log_id : ''}`
      );

      renderResultPanel(data);
    } catch (err) {
      removeLoadingMessage();
      const msg = err && err.message ? err.message : 'Error inesperado al consultar el asistente.';
      addMessage('bot', 'No pude completar la consulta.\n\n' + msg);
      showToast(msg);
    } finally {
      setSending(false);
      $question.focus();
    }
  }

  function clearConversation() {
    $messages.innerHTML = `
      <article class="pats-concierge-msg is-bot">
        <div class="pats-concierge-msg__avatar">P</div>
        <div class="pats-concierge-bubble">
          <strong>Asistente PATS</strong>
          <p>Chat limpio. Captura el contexto y escribe la siguiente duda del usuario.</p>
        </div>
      </article>
    `;

    $resultPanel.innerHTML = `
      <section class="pats-concierge-result-empty">
        <div class="pats-concierge-orb"></div>
        <h2>Guía de atención</h2>
        <p>Consulta una duda para generar una respuesta clara, segura y lista para usar.</p>
        <div class="pats-concierge-empty-list">
          <span>Respuesta para el usuario</span>
          <span>Texto breve para WhatsApp</span>
          <span>Validaciones antes de confirmar</span>
          <span>Ruta interna y escalamiento</span>
        </div>
      </section>
    `;

    lastLogId = null;
    lastData = null;
    $question.focus();
  }

  function applyQuickPrompt(btn) {
    const prompt = btn.getAttribute('data-prompt') || '';
    if (!prompt) return;

    $question.value = prompt;
    resizeTextarea();
    updateCounter();
    $question.focus();
  }

  function setMode(mode) {
    if (!['manual', 'operativo', 'auto', 'whatsapp'].includes(mode)) return;

    currentMode = mode;

    document.querySelectorAll('[data-catbot-mode]').forEach((btn) => {
      btn.classList.toggle('is-active', btn.getAttribute('data-catbot-mode') === mode);
    });

    const eyebrow = document.getElementById('catbotModeEyebrow');
    const help = document.getElementById('catbotModeHelp');

    const labels = {
      manual: 'Modo Usuario',
      operativo: 'Modo Interno',
      auto: 'Modo Completo',
      whatsapp: 'Modo WhatsApp'
    };

    const helps = {
      manual: 'Pregunta como lo diría el usuario. El sistema separará qué decir y qué validar.',
      operativo: 'Describe el caso operativo. El sistema priorizará ruta, módulos, validaciones y escalamiento.',
      auto: 'Consulta combinada: respuesta para usuario, validación interna y alertas.',
      whatsapp: 'Genera una respuesta breve para copiar y enviar por WhatsApp.'
    };

    if (eyebrow) eyebrow.textContent = labels[mode];
    if (help) help.textContent = helps[mode];

    showToast('Modo activo: ' + labels[mode]);
  }

  function prepareWhatsapp() {
    setMode('whatsapp');

    if (lastData) {
      copyToClipboard(buildWhatsappText(lastData));
      return;
    }

    if (!$question.value.trim()) {
      $question.value = 'Redacta una respuesta breve y amable para WhatsApp sobre: ';
      resizeTextarea();
      updateCounter();
    }

    $question.focus();
  }

  function bindEvents() {
    $form.addEventListener('submit', handleSubmit);

    $question.addEventListener('input', () => {
      resizeTextarea();
      updateCounter();
    });

    $question.addEventListener('keydown', (event) => {
      if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        $form.requestSubmit();
      }
    });

    if ($clearBtn) $clearBtn.addEventListener('click', clearConversation);

    const $contextClear = document.getElementById('catbotContextClear');
    if ($contextClear) $contextClear.addEventListener('click', clearContext);

    if ($whatsappHint) $whatsappHint.addEventListener('click', prepareWhatsapp);

    document.querySelectorAll('[data-prompt]').forEach((btn) => {
      btn.addEventListener('click', () => applyQuickPrompt(btn));
    });

    document.querySelectorAll('[data-catbot-mode]').forEach((btn) => {
      btn.addEventListener('click', () => setMode(btn.getAttribute('data-catbot-mode') || 'manual'));
    });
  }

  function boot() {
    bindEvents();
    resizeTextarea();
    updateCounter();

    window.PatsCatbotConcierge = {
      clear: clearConversation,
      setMode: setMode,
      getContext: getContext,
      getLastLogId: () => lastLogId,
      getLastData: () => lastData,
      copyWhatsapp: () => lastData ? copyToClipboard(buildWhatsappText(lastData)) : showToast('Todavía no hay respuesta para copiar.')
    };
  }

  boot();
})();
