/*
=========================================================
ez/patsbot/js/catbot_training.js
Panel de entrenamiento CAT BOT PATS
=========================================================
*/

(function () {
    'use strict';

    const cfg = window.PATS_TRAINING_CONFIG || {};

    const endpoints = {
        list: cfg.listEndpoint || 'endpoints/catbot_training_list.php',
        synonym: 'endpoints/catbot_training_save_synonym.php',
        resolve: 'endpoints/catbot_training_resolve.php',
        discard: 'endpoints/catbot_training_discard.php',
        publishManual: 'endpoints/catbot_training_publish_manual.php',
        publishOps: 'endpoints/catbot_training_publish_ops.php'
    };

    const $list = document.getElementById('trainList');
    const $summary = document.getElementById('trainSummary');
    const $motivo = document.getElementById('trainFilterMotivo');
    const $estado = document.getElementById('trainFilterEstado');
    const $q = document.getElementById('trainFilterQ');
    const $reload = document.getElementById('trainReloadBtn');
    const $toast = document.getElementById('trainToast');

    if (!$list) return;

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

    function showToast(message) {
        if (!$toast) return;

        $toast.textContent = message;
        $toast.classList.add('is-visible');

        window.clearTimeout(showToast._timer);
        showToast._timer = window.setTimeout(() => {
            $toast.classList.remove('is-visible');
        }, 3500);
    }

    function motivoClass(motivo) {
        if (['INCORRECTO', 'CONFUSO', 'NO_UTIL'].includes(motivo)) return 'is-bad';
        if (motivo === 'BAJA_CONFIANZA') return 'is-warn';
        if (motivo === 'REPETIDA') return 'is-ok';
        return '';
    }

    function cleanQuestion(q) {
        return String(q || '').trim();
    }

    function defaultKeywords(question) {
        const q = cleanQuestion(question);
        return q
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[¿?¡!.,;:()"]/g, ' ')
            .replace(/\s+/g, ',')
            .replace(/^,+|,+$/g, '');
    }

    async function postJson(url, payload) {
        const res = await fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload || {})
        });

        const text = await res.text();
        let data = null;

        try {
            data = JSON.parse(text);
        } catch (err) {
            throw new Error('El endpoint no devolvió JSON válido: ' + text.slice(0, 240));
        }

        if (!res.ok || !data.ok) {
            throw new Error(data.error || 'No fue posible completar la acción.');
        }

        return data;
    }

    function renderSummary(summary) {
        if (!$summary) return;

        const s = summary || {};

        $summary.innerHTML = `
      <div class="pats-train-kpi"><strong>${Number(s.pendientes || 0)}</strong><span>Pendientes</span></div>
      <div class="pats-train-kpi"><strong>${Number(s.sin_resultado || 0)}</strong><span>Sin resultado</span></div>
      <div class="pats-train-kpi"><strong>${Number(s.baja_confianza || 0)}</strong><span>Baja confianza</span></div>
      <div class="pats-train-kpi"><strong>${Number(s.feedback_negativo || 0)}</strong><span>Feedback negativo</span></div>
      <div class="pats-train-kpi"><strong>${Number(s.repetidas || 0)}</strong><span>Repetidas</span></div>
    `;
    }

    function renderManualForm(item) {
        const q = cleanQuestion(item.pregunta);
        const kw = defaultKeywords(q);

        return `
      <div class="pats-train-mini-form train-publish-form" data-form="manual">
        <label>Publicar en Manual de Usuario</label>

        <input type="text" class="manual-categoria" value="Aprendizaje supervisado" placeholder="Categoría">
        <input type="text" class="manual-subcategoria" value="Respuesta aprobada" placeholder="Subcategoría">
        <input type="text" class="manual-intent" value="${escapeHtml(item.intent_detectado || 'aprendizaje_supervisado')}" placeholder="Intent">

        <input type="text" class="manual-pregunta" value="${escapeHtml(q)}" placeholder="Pregunta base">

        <textarea class="manual-respuesta" placeholder="Respuesta que Concierge/Admisión puede decir al usuario..."></textarea>

        <textarea class="manual-corta" placeholder="Respuesta corta opcional..."></textarea>

        <textarea class="manual-keywords" placeholder="Keywords separadas por coma">${escapeHtml(kw)}</textarea>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
          <select class="manual-login">
            <option value="0">No requiere login</option>
            <option value="1">Requiere login</option>
          </select>

          <select class="manual-datos">
            <option value="0">No requiere datos</option>
            <option value="1">Requiere datos usuario</option>
          </select>
        </div>

        <input type="number" class="manual-prioridad" value="900" placeholder="Prioridad">

        <button type="button" class="pats-train-btn train-action" data-action="publish-manual">
          Publicar Manual
        </button>

        <button type="button" class="pats-train-btn is-light train-action" data-action="cancel-form" style="margin-top:8px;">
          Cancelar
        </button>
      </div>
    `;
    }

    function renderOpsForm(item) {
        const q = cleanQuestion(item.pregunta);
        const kw = defaultKeywords(q);

        return `
      <div class="pats-train-mini-form train-publish-form" data-form="ops">
        <label>Publicar en Asistente Operativo</label>

        <input type="text" class="ops-codigo" value="" placeholder="Código opcional, ej. OPS-PATS-QR-001">
        <input type="text" class="ops-proceso" value="Aprendizaje supervisado PATS" placeholder="Proceso">
        <input type="text" class="ops-fase" value="Atención y orientación" placeholder="Fase">

        <input type="text" class="ops-tipo" value="APRENDIZAJE_SUPERVISADO" placeholder="Tipo de caso">
        <input type="text" class="ops-subtipo" value="" placeholder="Subtipo opcional">

        <input type="text" class="ops-pregunta" value="${escapeHtml(q)}" placeholder="Pregunta operativa">
        <textarea class="ops-situacion" placeholder="Situación del usuario...">${escapeHtml(q)}</textarea>

        <textarea class="ops-respuesta" placeholder="Respuesta sugerida al usuario..."></textarea>
        <textarea class="ops-pasos" placeholder="Pasos operativos. Ejemplo: 1. Validar... 2. Consultar..."></textarea>
        <textarea class="ops-validaciones" placeholder="Validaciones necesarias..."></textarea>

        <input type="text" class="ops-modulos" value="PATS Concierge, PATS DROP, PATS MONEY, LEAD+ o PATS Vigencias, según corresponda." placeholder="Módulos a consultar">

        <textarea class="ops-escalar" placeholder="Cuándo escalar..."></textarea>
        <textarea class="ops-frases" placeholder="Frases sugeridas opcionales..."></textarea>
        <textarea class="ops-errores" placeholder="Errores a evitar opcionales..."></textarea>

        <input type="text" class="ops-roles" value="CON,CONCIERGE,ADM,ADMISION,CAJ,CAJA,ADMIN,ADMINPATS" placeholder="Roles aplica">

        <select class="ops-nivel">
          <option value="OPERATIVO">Operativo</option>
          <option value="CRITICO">Crítico</option>
          <option value="BASICO">Básico</option>
        </select>

        <textarea class="ops-keywords" placeholder="Keywords separadas por coma">${escapeHtml(kw)}</textarea>

        <input type="number" class="ops-prioridad" value="900" placeholder="Prioridad">

        <button type="button" class="pats-train-btn train-action" data-action="publish-ops">
          Publicar Operativo
        </button>

        <button type="button" class="pats-train-btn is-light train-action" data-action="cancel-form" style="margin-top:8px;">
          Cancelar
        </button>
      </div>
    `;
    }

    function renderItem(item) {
        const motivo = String(item.motivo || '');
        const estado = String(item.estado || '');
        const pregunta = String(item.pregunta || '');
        const respuesta = String(item.respuesta_actual || '');
        const intent = String(item.intent_detectado || '');

        return `
      <article class="pats-train-card" data-id="${escapeHtml(item.id)}">
        <div class="pats-train-card-head">
          <div>
            <h3>${escapeHtml(pregunta || 'Pregunta sin texto')}</h3>
            <small>
              ID ${escapeHtml(item.id)}
              ${item.log_id ? ' · Log ' + escapeHtml(item.log_id) : ''}
              · Actualizado: ${escapeHtml(item.updated_at || '')}
            </small>
          </div>

          <div class="pats-train-pillrow">
            <span class="pats-train-pill ${motivoClass(motivo)}">${escapeHtml(motivo)}</span>
            <span class="pats-train-pill">${escapeHtml(estado)}</span>
            <span class="pats-train-pill">Veces: ${escapeHtml(item.veces_detectada || 1)}</span>
            <span class="pats-train-pill">Score: ${escapeHtml(item.score || '0')}</span>
          </div>
        </div>

        <div class="pats-train-card-body">

          <div>
            <div class="pats-train-textbox">
              <label>Pregunta detectada</label>
              <p>${nl2br(pregunta)}</p>
            </div>

            <div class="pats-train-textbox">
              <label>Respuesta actual / contexto</label>
              <p>${respuesta ? nl2br(respuesta) : 'Sin respuesta registrada.'}</p>
            </div>

            <div class="pats-train-textbox">
              <label>Origen</label>
              <p>
                Intent: ${escapeHtml(intent || 'N/D')}<br>
                Source: ${escapeHtml(item.source || 'N/D')}<br>
                Creado: ${escapeHtml(item.created_at || '')}
              </p>
            </div>
          </div>

          <div class="pats-train-actions">

            <div class="pats-train-mini-form">
              <label>Entrenar como sinónimo</label>
              <input type="text" class="train-syn-from" value="${escapeHtml(pregunta)}" placeholder="Término usado">
              <input type="text" class="train-syn-to" placeholder="Debe entenderse como...">
              <select class="train-syn-tipo">
                <option value="JERGA_OPERATIVA">Jerga operativa</option>
                <option value="ERROR_ESCRITURA">Error de escritura</option>
                <option value="SINONIMO">Sinónimo</option>
                <option value="MODULO">Módulo</option>
                <option value="SERVICIO">Servicio</option>
              </select>
              <input type="text" class="train-syn-intent" value="${escapeHtml(intent)}" placeholder="Intent opcional">
              <button type="button" class="pats-train-btn train-action" data-action="synonym">
                Guardar sinónimo
              </button>
            </div>

            <div class="pats-train-mini-form">
              <label>Publicar respuesta</label>
              <button type="button" class="pats-train-btn train-action" data-action="show-manual-form">
                Crear respuesta Manual
              </button>
              <button type="button" class="pats-train-btn train-action" data-action="show-ops-form" style="margin-top:8px;">
                Crear respuesta Operativa
              </button>
            </div>

            <div class="train-form-slot"></div>

            <div class="pats-train-mini-form">
              <label>Gestión</label>
              <button type="button" class="pats-train-btn is-ok train-action" data-action="resolve">
                Marcar resuelto
              </button>
              <button type="button" class="pats-train-btn is-danger train-action" data-action="discard" style="margin-top:8px;">
                Descartar
              </button>
            </div>

          </div>

        </div>
      </article>
    `;
    }

    function readManualPayload(card, id) {
        return {
            queue_id: id,
            categoria: card.querySelector('.manual-categoria')?.value.trim() || 'Aprendizaje supervisado',
            subcategoria: card.querySelector('.manual-subcategoria')?.value.trim() || 'Respuesta aprobada',
            intent: card.querySelector('.manual-intent')?.value.trim() || 'aprendizaje_supervisado',
            pregunta_base: card.querySelector('.manual-pregunta')?.value.trim() || '',
            respuesta: card.querySelector('.manual-respuesta')?.value.trim() || '',
            respuesta_corta: card.querySelector('.manual-corta')?.value.trim() || '',
            keywords: card.querySelector('.manual-keywords')?.value.trim() || '',
            requiere_login: Number(card.querySelector('.manual-login')?.value || 0),
            requiere_datos_usuario: Number(card.querySelector('.manual-datos')?.value || 0),
            prioridad: Number(card.querySelector('.manual-prioridad')?.value || 900)
        };
    }

    function readOpsPayload(card, id) {
        return {
            queue_id: id,
            codigo: card.querySelector('.ops-codigo')?.value.trim() || '',
            proceso: card.querySelector('.ops-proceso')?.value.trim() || 'Aprendizaje supervisado PATS',
            fase: card.querySelector('.ops-fase')?.value.trim() || 'Atención y orientación',
            tipo_caso: card.querySelector('.ops-tipo')?.value.trim() || 'APRENDIZAJE_SUPERVISADO',
            subtipo_caso: card.querySelector('.ops-subtipo')?.value.trim() || '',
            pregunta_operativa: card.querySelector('.ops-pregunta')?.value.trim() || '',
            situacion_usuario: card.querySelector('.ops-situacion')?.value.trim() || '',
            respuesta_usuario_sugerida: card.querySelector('.ops-respuesta')?.value.trim() || '',
            pasos_operativos: card.querySelector('.ops-pasos')?.value.trim() || '',
            validaciones: card.querySelector('.ops-validaciones')?.value.trim() || '',
            modulos_consultar: card.querySelector('.ops-modulos')?.value.trim() || '',
            cuando_escalar: card.querySelector('.ops-escalar')?.value.trim() || '',
            frases_sugeridas: card.querySelector('.ops-frases')?.value.trim() || '',
            errores_evitar: card.querySelector('.ops-errores')?.value.trim() || '',
            roles_aplica: card.querySelector('.ops-roles')?.value.trim() || '',
            nivel: card.querySelector('.ops-nivel')?.value || 'OPERATIVO',
            keywords: card.querySelector('.ops-keywords')?.value.trim() || '',
            prioridad: Number(card.querySelector('.ops-prioridad')?.value || 900)
        };
    }

    async function handleAction(btn) {
        const card = btn.closest('.pats-train-card');
        const id = card ? Number(card.getAttribute('data-id') || 0) : 0;
        const action = btn.getAttribute('data-action');

        if (!id || !action) return;

        try {
            btn.disabled = true;

            if (action === 'show-manual-form') {
                const slot = card.querySelector('.train-form-slot');
                if (slot) {
                    const item = currentItems.find((x) => Number(x.id) === id) || {};
                    slot.innerHTML = renderManualForm(item);
                    bindItemActions();
                }
                return;
            }

            if (action === 'show-ops-form') {
                const slot = card.querySelector('.train-form-slot');
                if (slot) {
                    const item = currentItems.find((x) => Number(x.id) === id) || {};
                    slot.innerHTML = renderOpsForm(item);
                    bindItemActions();
                }
                return;
            }

            if (action === 'cancel-form') {
                const slot = card.querySelector('.train-form-slot');
                if (slot) slot.innerHTML = '';
                return;
            }

            if (action === 'publish-manual') {
                const payload = readManualPayload(card, id);

                if (!payload.pregunta_base || !payload.respuesta) {
                    showToast('Captura pregunta base y respuesta Manual.');
                    return;
                }

                const ok = window.confirm('¿Publicar esta respuesta en el Manual del CAT BOT?');
                if (!ok) return;

                const data = await postJson(endpoints.publishManual, payload);
                showToast(data.message || 'Respuesta Manual publicada.');
                await loadTraining();
                return;
            }

            if (action === 'publish-ops') {
                const payload = readOpsPayload(card, id);

                if (!payload.pregunta_operativa || !payload.respuesta_usuario_sugerida || !payload.pasos_operativos) {
                    showToast('Captura pregunta operativa, respuesta sugerida y pasos operativos.');
                    return;
                }

                const ok = window.confirm('¿Publicar esta respuesta en el Asistente Operativo?');
                if (!ok) return;

                const data = await postJson(endpoints.publishOps, payload);
                showToast(data.message || 'Respuesta Operativa publicada.');
                await loadTraining();
                return;
            }

            if (action === 'synonym') {
                const from = card.querySelector('.train-syn-from')?.value.trim() || '';
                const to = card.querySelector('.train-syn-to')?.value.trim() || '';
                const tipo = card.querySelector('.train-syn-tipo')?.value || 'JERGA_OPERATIVA';
                const intent = card.querySelector('.train-syn-intent')?.value.trim() || '';

                if (!from || !to) {
                    showToast('Captura término usado y cómo debe entenderse.');
                    return;
                }

                const data = await postJson(endpoints.synonym, {
                    queue_id: id,
                    termino_usuario: from,
                    termino_normalizado: to,
                    tipo: tipo,
                    intent: intent
                });

                showToast(data.message || 'Sinónimo guardado.');
                await loadTraining();
                return;
            }

            if (action === 'resolve') {
                const ok = window.confirm('¿Marcar esta pregunta como resuelta?');
                if (!ok) return;

                const data = await postJson(endpoints.resolve, {
                    queue_id: id,
                    comentario: 'Marcado como resuelto desde panel de entrenamiento.'
                });

                showToast(data.message || 'Marcado como resuelto.');
                await loadTraining();
                return;
            }

            if (action === 'discard') {
                const reason = window.prompt('Motivo de descarte:', 'No aplica para entrenamiento.');
                if (reason === null) return;

                const data = await postJson(endpoints.discard, {
                    queue_id: id,
                    comentario: reason || 'Descartado desde panel de entrenamiento.'
                });

                showToast(data.message || 'Registro descartado.');
                await loadTraining();
            }

        } catch (err) {
            showToast(err && err.message ? err.message : 'No fue posible completar la acción.');
        } finally {
            btn.disabled = false;
        }
    }

    function bindItemActions() {
        document.querySelectorAll('.train-action').forEach((btn) => {
            btn.onclick = () => handleAction(btn);
        });
    }

    let currentItems = [];

    async function loadTraining() {
        const params = new URLSearchParams();

        if ($motivo && $motivo.value) params.set('motivo', $motivo.value);
        if ($estado && $estado.value) params.set('estado', $estado.value);
        if ($q && $q.value.trim()) params.set('q', $q.value.trim());

        $list.innerHTML = '<div class="pats-train-empty">Cargando cola de entrenamiento...</div>';

        try {
            const res = await fetch(`${endpoints.list}?${params.toString()}`, {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json'
                }
            });

            const text = await res.text();
            let data = null;

            try {
                data = JSON.parse(text);
            } catch (err) {
                throw new Error('El endpoint no devolvió JSON válido: ' + text.slice(0, 240));
            }

            if (!res.ok || !data.ok) {
                throw new Error(data.error || 'No fue posible cargar entrenamiento.');
            }

            renderSummary(data.summary);

            currentItems = Array.isArray(data.items) ? data.items : [];

            if (!currentItems.length) {
                $list.innerHTML = '<div class="pats-train-empty">No hay preguntas con esos filtros.</div>';
                return;
            }

            $list.innerHTML = currentItems.map(renderItem).join('');
            bindItemActions();

        } catch (err) {
            const msg = err && err.message ? err.message : 'Error al cargar cola de entrenamiento.';
            $list.innerHTML = `<div class="pats-train-empty">${escapeHtml(msg)}</div>`;
            showToast(msg);
        }
    }

    function bindEvents() {
        if ($reload) {
            $reload.addEventListener('click', loadTraining);
        }

        [$motivo, $estado].forEach((el) => {
            if (el) el.addEventListener('change', loadTraining);
        });

        if ($q) {
            let t = null;
            $q.addEventListener('input', () => {
                window.clearTimeout(t);
                t = window.setTimeout(loadTraining, 400);
            });
        }


        const manualToggle = document.querySelector('[data-train-manual-toggle]');
        const manualBox = document.querySelector('[data-train-manual]');
        const manualBody = document.querySelector('[data-train-manual-body]');

        if (manualToggle && manualBox && manualBody) {
            manualToggle.addEventListener('click', () => {
                const isOpen = manualBox.classList.toggle('is-open');

                manualToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

                if (isOpen) {
                    manualBody.hidden = false;
                } else {
                    manualBody.hidden = true;
                }
            });
        }




    }

    bindEvents();
    loadTraining();

})();