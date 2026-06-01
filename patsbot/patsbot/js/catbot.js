/*
=========================================================
ez/patsbot/assets/js/catbot.js
CAT BOT PATS - Asistente interno Concierge / Admisión
Con feedback: Útil / Confuso / Incorrecto / No útil
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
    let isSending = false;
    let isSendingFeedback = false;
    let lastLogId = null;
    let lastFeedbackValue = null;
    let currentMode = cfg.defaultMode || 'manual';

    if (!$form || !$question || !$sendBtn || !$messages || !$resultPanel) {
        console.warn('[PATS CATBOT] Elementos base no encontrados.');
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

    function safeText(value, fallback = '') {
        const txt = String(value || '').trim();
        return txt !== '' ? txt : fallback;
    }

    function showToast(message) {
        if (!$toast) return;

        $toast.textContent = message;
        $toast.classList.add('is-visible');

        window.clearTimeout(showToast._timer);
        showToast._timer = window.setTimeout(() => {
            $toast.classList.remove('is-visible');
        }, 3800);
    }

    function scrollMessages() {
        requestAnimationFrame(() => {
            $messages.scrollTop = $messages.scrollHeight;
        });
    }

    function resizeTextarea() {
        if (!$question) return;
        $question.style.height = 'auto';
        const nextHeight = Math.min($question.scrollHeight, 160);
        $question.style.height = `${nextHeight}px`;
    }

    function updateCounter() {
        if (!$counter || !$question) return;
        const len = $question.value.length;
        $counter.textContent = `${len}/1200`;
    }

    function setSending(state) {
        isSending = Boolean(state);

        $sendBtn.disabled = isSending;
        $question.disabled = isSending;

        if (isSending) {
            $sendBtn.innerHTML = `
        <span>Consultando</span>
        <span class="pats-cat-typing" aria-hidden="true">
          <i></i><i></i><i></i>
        </span>
      `;
        } else {
            $sendBtn.innerHTML = `
        <span>Consultar</span>
        <svg viewBox="0 0 24 24" aria-hidden="true">
          <path d="M3.6 20.4 21 12 3.6 3.6v6.3L15.2 12 3.6 14.1v6.3Z"></path>
        </svg>
      `;
        }
    }

    function addMessage(type, text, meta) {
        const isUser = type === 'user';
        const who = isUser ? 'Tú' : 'Asistente PATS';
        const avatar = isUser ? 'T' : 'P';

        const $article = document.createElement('article');
        $article.className = `pats-cat-msg ${isUser ? 'pats-cat-msg-user' : 'pats-cat-msg-bot'}`;

        const metaHtml = meta
            ? `<small style="display:block;margin-top:8px;color:${isUser ? 'rgba(255,255,255,.72)' : '#7a849d'};font-size:11px;font-weight:800;">${escapeHtml(meta)}</small>`
            : '';

        $article.innerHTML = `
      <div class="pats-cat-msg-avatar">${escapeHtml(avatar)}</div>
      <div class="pats-cat-bubble">
        <strong>${escapeHtml(who)}</strong>
        <p>${nl2br(text)}</p>
        ${metaHtml}
      </div>
    `;

        $messages.appendChild($article);
        scrollMessages();

        return $article;
    }

    function addLoadingMessage() {
        const $article = document.createElement('article');
        $article.className = 'pats-cat-msg pats-cat-msg-bot';
        $article.setAttribute('data-loading', '1');

        $article.innerHTML = `
      <div class="pats-cat-msg-avatar">P</div>
      <div class="pats-cat-bubble">
        <strong>Asistente PATS</strong>
        <p>
          Analizando la duda en el Manual PATS y en la base operativa interna...
          <span class="pats-cat-typing" aria-hidden="true">
            <i></i><i></i><i></i>
          </span>
        </p>
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

    function confidenceLabel(confidence) {
        const n = Number(confidence || 0);
        if (n >= 0.75) return 'Alta';
        if (n >= 0.45) return 'Media';
        if (n > 0) return 'Baja';
        return 'Sin coincidencia';
    }

    function renderMetaPill(label, value) {
        if (!value && value !== 0) return '';
        return `<span class="pats-cat-meta-pill">${escapeHtml(label)}: ${escapeHtml(value)}</span>`;
    }

    function renderResultBox(title, content, className) {
        const txt = safeText(content);
        if (!txt) return '';

        return `
      <section class="pats-cat-result-box ${className || ''}">
        <h4>${escapeHtml(title)}</h4>
        <p>${nl2br(txt)}</p>
      </section>
    `;
    }

    function renderAlerts(alerts) {
        if (!Array.isArray(alerts) || alerts.length === 0) return '';

        return `
      <section class="pats-cat-result-box is-warning">
        <h4>Alertas del asistente</h4>
        <div class="pats-cat-alert-list">
          ${alerts.map((item) => `<div class="pats-cat-alert">${nl2br(item)}</div>`).join('')}
        </div>
      </section>
    `;
    }

    function renderMatchedInfo(data) {
        const matched = data && data.matched ? data.matched : {};
        const manual = matched.manual || null;
        const operativo = matched.operativo || null;

        let html = '';

        if (manual) {
            html += `
        <section class="pats-cat-result-box">
          <h4>Coincidencia Manual PATS</h4>
          <p>
            Categoría: ${escapeHtml(manual.categoria || 'N/D')}<br>
            Subcategoría: ${escapeHtml(manual.subcategoria || 'N/D')}<br>
            Intent: ${escapeHtml(manual.intent || 'N/D')}<br>
            Método: ${escapeHtml(manual.search_method || 'N/D')}
          </p>
        </section>
      `;
        }

        if (operativo) {
            html += `
        <section class="pats-cat-result-box">
          <h4>Coincidencia Operativa</h4>
          <p>
            Código: ${escapeHtml(operativo.codigo || 'N/D')}<br>
            Proceso: ${escapeHtml(operativo.proceso || 'N/D')}<br>
            Fase: ${escapeHtml(operativo.fase || 'N/D')}<br>
            Nivel: ${escapeHtml(operativo.nivel || 'N/D')}<br>
            Método: ${escapeHtml(operativo.search_method || 'N/D')}
          </p>
        </section>
      `;
        }

        return html;
    }

    function renderFeedbackBox(logId) {
        if (!logId) return '';

        return `
      <section class="pats-cat-result-box pats-cat-feedback-box" data-log-id="${escapeHtml(logId)}">
        <h4>Calificar respuesta</h4>

        <p style="margin-bottom:12px;">
          Marca si esta respuesta ayudó. Esto sirve para mejorar el asistente interno PATS.
        </p>

        <div class="pats-cat-feedback-actions">
          <button type="button" class="pats-cat-feedback-btn" data-feedback="UTIL">
            Útil
          </button>
          <button type="button" class="pats-cat-feedback-btn" data-feedback="CONFUSO">
            Confuso
          </button>
          <button type="button" class="pats-cat-feedback-btn" data-feedback="INCORRECTO">
            Incorrecto
          </button>
          <button type="button" class="pats-cat-feedback-btn" data-feedback="NO_UTIL">
            No útil
          </button>
        </div>

        <div class="pats-cat-feedback-comment">
          <textarea
            id="catbotFeedbackComment"
            maxlength="1000"
            rows="3"
            placeholder="Comentario opcional: ¿qué faltó, qué estuvo mal o cómo debería responder?"
          ></textarea>
        </div>

        <div class="pats-cat-feedback-status" id="catbotFeedbackStatus"></div>
      </section>
    `;
    }

    function bindFeedbackButtons() {
        const buttons = $resultPanel.querySelectorAll('.pats-cat-feedback-btn[data-feedback]');
        buttons.forEach((btn) => {
            btn.addEventListener('click', () => {
                const value = btn.getAttribute('data-feedback');
                sendFeedback(value);
            });
        });
    }

    function setFeedbackButtonsState(disabled) {
        const buttons = $resultPanel.querySelectorAll('.pats-cat-feedback-btn[data-feedback]');
        buttons.forEach((btn) => {
            btn.disabled = Boolean(disabled);
        });
    }

    function setFeedbackStatus(message, type) {
        const $status = document.getElementById('catbotFeedbackStatus');
        if (!$status) return;

        $status.textContent = message || '';
        $status.classList.remove('is-ok', 'is-error');

        if (type) {
            $status.classList.add(type === 'ok' ? 'is-ok' : 'is-error');
        }
    }

    async function sendFeedback(value) {
        if (!lastLogId) {
            showToast('No hay una respuesta reciente para calificar.');
            return;
        }

        if (isSendingFeedback) return;

        const allowed = ['UTIL', 'NO_UTIL', 'INCORRECTO', 'CONFUSO'];
        if (!allowed.includes(value)) {
            showToast('Valor de feedback no válido.');
            return;
        }

        const $comment = document.getElementById('catbotFeedbackComment');
        const comentario = $comment ? $comment.value.trim() : '';

        isSendingFeedback = true;
        setFeedbackButtonsState(true);
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

            const text = await response.text();
            let data = null;

            try {
                data = JSON.parse(text);
            } catch (err) {
                throw new Error('El endpoint de feedback no devolvió JSON válido. Respuesta: ' + text.slice(0, 240));
            }

            if (!response.ok || !data.ok) {
                throw new Error(data.error || 'No fue posible registrar el feedback.');
            }

            lastFeedbackValue = value;

            const labels = {
                UTIL: 'Útil',
                NO_UTIL: 'No útil',
                INCORRECTO: 'Incorrecto',
                CONFUSO: 'Confuso'
            };

            $resultPanel.querySelectorAll('.pats-cat-feedback-btn').forEach((btn) => {
                btn.classList.toggle('is-selected', btn.getAttribute('data-feedback') === value);
            });

            setFeedbackStatus(`Feedback registrado: ${labels[value] || value}.`, 'ok');
            showToast('Feedback registrado correctamente.');

        } catch (err) {
            const msg = err && err.message ? err.message : 'No fue posible registrar el feedback.';
            setFeedbackStatus(msg, 'error');
            showToast(msg);
        } finally {
            isSendingFeedback = false;
            setFeedbackButtonsState(false);
        }
    }

    function renderResultPanel(data) {
        const confidence = confidenceLabel(data.confidence);
        const source = safeText(data.source, 'N/D');
        const intent = safeText(data.intent, 'Sin intent específico');
        const logId = data.log_id || null;

        lastLogId = logId;
        lastFeedbackValue = null;

        const resultHeader = `
      <div class="pats-cat-result-top">
        <span>Respuesta estructurada</span>
        <h3>Guía para atención PATS</h3>
        <p>
          Usa esta salida como apoyo interno. Si hay duda crítica,
          valida en el módulo correspondiente antes de confirmar al usuario.
        </p>

        <div class="pats-cat-result-meta">
          ${renderMetaPill('Fuente', source)}
          ${renderMetaPill('Confianza', confidence)}
          ${renderMetaPill('Intent', intent)}
          ${renderMetaPill('Log', logId)}
        </div>
      </div>
    `;

        const html = `
      <div class="pats-cat-result">
        ${resultHeader}

        ${renderResultBox(
            'Respuesta sugerida al usuario',
            data.respuesta_usuario_sugerida,
            'is-primary'
        )}

        ${renderResultBox(
            'Ruta operativa',
            data.ruta_operativa,
            ''
        )}

        ${renderResultBox(
            'Validaciones',
            data.validaciones,
            'is-critical'
        )}

        ${renderResultBox(
            'Módulos a consultar',
            data.modulos_consultar,
            ''
        )}

        ${renderResultBox(
            'Cuándo escalar',
            data.cuando_escalar,
            'is-warning'
        )}

        ${renderResultBox(
            'Frases sugeridas',
            data.frases_sugeridas,
            ''
        )}

        ${renderResultBox(
            'Errores a evitar',
            data.errores_evitar,
            'is-critical'
        )}

        ${renderAlerts(data.alertas)}

        ${renderFeedbackBox(logId)}

        ${renderMatchedInfo(data)}
      </div>
    `;

        $resultPanel.innerHTML = html;
        bindFeedbackButtons();
    }

    function compactBotSummary(data) {
        const parts = [];

        if (data.respuesta_usuario_sugerida) {
            parts.push(`Respuesta sugerida:\n${data.respuesta_usuario_sugerida}`);
        }

        if (data.ruta_operativa) {
            parts.push(`Ruta operativa:\n${data.ruta_operativa}`);
        }

        if (data.validaciones) {
            parts.push(`Validaciones:\n${data.validaciones}`);
        }

        if (Array.isArray(data.alertas) && data.alertas.length > 0) {
            parts.push(`Alertas:\n- ${data.alertas.join('\n- ')}`);
        }

        return parts.join('\n\n') || data.answer || 'Respuesta generada.';
    }

    async function askCatbot(question) {
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            credentials: 'same-origin',
           body: JSON.stringify({
  question: question,
  source: 'catbot_ui',
  mode: currentMode
})
        });

        const text = await response.text();
        let data = null;

        try {
            data = JSON.parse(text);
        } catch (err) {
            throw new Error('El endpoint no devolvió JSON válido. Respuesta: ' + text.slice(0, 240));
        }

        if (!response.ok || !data.ok) {
            throw new Error(data.error || 'No fue posible consultar el asistente PATS.');
        }

        return data;
    }

    async function handleSubmit(event) {
        event.preventDefault();

        if (isSending) return;

        const q = $question.value.trim();

        if (!q) {
            showToast('Escribe la duda o situación antes de consultar.');
            $question.focus();
            return;
        }

        if (q.length > 1200) {
            showToast('La pregunta es demasiado larga. Resume la situación.');
            return;
        }

        addMessage('user', q);
        $question.value = '';
        resizeTextarea();
        updateCounter();

        const $loading = addLoadingMessage();
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
            addMessage('bot', 'No pude completar la consulta. Revisa el detalle técnico o intenta nuevamente.\n\n' + msg);
            showToast(msg);
        } finally {
            setSending(false);
            $question.focus();
        }

        if ($loading && $loading.parentNode) {
            $loading.remove();
        }
    }

    function clearConversation() {
        $messages.innerHTML = `
      <article class="pats-cat-msg pats-cat-msg-bot">
        <div class="pats-cat-msg-avatar">P</div>
        <div class="pats-cat-bubble">
          <strong>Asistente PATS</strong>
          <p>
            Listo. Pregúntame otra situación de atención PATS.
            Ejemplo: <em>“El usuario pregunta cuánto cuesta un estudio y la plataforma no carga.”</em>
          </p>
        </div>
      </article>
    `;

        $resultPanel.innerHTML = `
      <div class="pats-cat-result-empty">
        <div class="pats-cat-orb"></div>
        <h3>Respuesta estructurada</h3>
        <p>
          Aquí se separará la respuesta sugerida al usuario, ruta operativa,
          validaciones, módulos y escalamiento.
        </p>
      </div>
    `;

        lastLogId = null;
        lastFeedbackValue = null;
        $question.focus();
    }

    function applyQuickPrompt(button) {
        const prompt = button.getAttribute('data-prompt') || '';
        if (!prompt) return;

        $question.value = prompt;
        resizeTextarea();
        updateCounter();
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

        if ($clearBtn) {
            $clearBtn.addEventListener('click', clearConversation);
        }

        document.querySelectorAll('.pats-cat-chip[data-prompt]').forEach((btn) => {
            btn.addEventListener('click', () => applyQuickPrompt(btn));
        });

        document.querySelectorAll('.pats-cat-mode-btn[data-mode]').forEach((btn) => {
  btn.addEventListener('click', () => {
    const mode = btn.getAttribute('data-mode') || 'manual';

    if (!['manual', 'operativo', 'auto'].includes(mode)) return;

    currentMode = mode;

    document.querySelectorAll('.pats-cat-mode-btn[data-mode]').forEach((b) => {
      b.classList.toggle('is-active', b === btn);
    });

    const $modeLabel = document.getElementById('catbotModeLabel');
    const $modeHelp = document.getElementById('catbotModeHelp');

    const labels = {
      manual: 'Manual de Usuario',
      operativo: 'Asistente Operativo',
      auto: 'Automático'
    };

    const helps = {
      manual: 'Modo Manual: pregunta como lo diría el usuario. Ejemplo: “qué es PATS”.',
      operativo: 'Modo Operativo: pregunta qué debe hacer Concierge, Admisión o Caja.',
      auto: 'Modo Automático: consultará Manual de Usuario y guía operativa interna.'
    };

    if ($modeLabel) {
      $modeLabel.textContent = labels[mode] || labels.manual;
    }

    if ($modeHelp) {
      $modeHelp.textContent = helps[mode] || helps.manual;
    }

    showToast('Modo activo: ' + (labels[mode] || labels.manual));
  });
});
    }

    function boot() {
        bindEvents();
        resizeTextarea();
        updateCounter();

        window.PatsCatbot = {
            clear: clearConversation,
            ask: async function (question) {
                $question.value = String(question || '');
                resizeTextarea();
                updateCounter();
                $form.requestSubmit();
            },
            getLastLogId: function () {
                return lastLogId;
            },
            getLastFeedbackValue: function () {
                return lastFeedbackValue;
            },
            sendFeedback: sendFeedback
        };
    }

    boot();

})();