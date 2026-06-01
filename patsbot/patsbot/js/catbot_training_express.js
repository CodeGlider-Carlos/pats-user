/*
=========================================================
ez/patsbot/js/catbot_training_express.js
Entrenador Express CAT BOT PATS
Versión conectada y robusta
=========================================================
*/

(function () {
  'use strict';

  const cfg = window.PATS_XTRAIN_CONFIG || {};

  const endpoints = {
    list: cfg.listEndpoint || 'endpoints/catbot_training_list.php',
    synonym: cfg.synonymEndpoint || 'endpoints/catbot_training_save_synonym.php',
    manual: cfg.manualEndpoint || 'endpoints/catbot_training_publish_manual.php',
    ops: cfg.opsEndpoint || 'endpoints/catbot_training_publish_ops.php',
    resolve: cfg.resolveEndpoint || 'endpoints/catbot_training_resolve.php',
    discard: cfg.discardEndpoint || 'endpoints/catbot_training_discard.php',
    ask: cfg.askEndpoint || 'endpoints/catbot_ask.php'
  };

  const $list = document.getElementById('xtrainList');
  const $summary = document.getElementById('xtrainSummary');
  const $search = document.getElementById('xtrainSearch');
  const $reload = document.getElementById('xtrainReload');
  const $tabs = document.getElementById('xtrainQuickTabs');
  const $toast = document.getElementById('xtrainToast');

  let currentEstado = 'PENDIENTE';
  let currentMotivo = '';
  let currentItems = [];

  if (!$list) {
    console.error('[XTRAIN] No existe #xtrainList. Revisa catbot_training_express.php');
    return;
  }

  console.log('[XTRAIN] JS cargado correctamente', endpoints);

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

  function text(value) {
    return String(value || '').trim();
  }

  function normalize(value) {
    return text(value)
      .toLowerCase()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .replace(/[¿?¡!.,;:()"]/g, ' ')
      .replace(/\s+/g, ' ')
      .trim();
  }

  function showToast(message) {
    console.log('[XTRAIN]', message);

    if (!$toast) {
      alert(message);
      return;
    }

    $toast.textContent = message;
    $toast.classList.add('is-visible');

    clearTimeout(showToast._timer);
    showToast._timer = setTimeout(() => {
      $toast.classList.remove('is-visible');
    }, 4200);
  }

  function motivoClass(motivo) {
    motivo = text(motivo);

    if (['INCORRECTO', 'CONFUSO', 'NO_UTIL'].includes(motivo)) return 'is-bad';
    if (motivo === 'BAJA_CONFIANZA') return 'is-warn';
    if (motivo === 'SIN_RESULTADO') return 'is-empty';

    return '';
  }

  function detectTema(question, intent) {
    const q = normalize(question + ' ' + intent);

    if (/vencido|vencio|venido|inactivo|adeudo|no vigente|falta/.test(q)) return 'Vigencia';
    if (/pago|pagar|caja|comprobante|voucher|referencia/.test(q)) return 'Pagos';
    if (/costo|cuesta|precio|mensualidad|anualidad/.test(q)) return 'Costos';
    if (/cotiza|cotizar|cotizacion|presupuesto|cirugia|procedimiento|lead/.test(q)) return 'Cotización';
    if (/qr|digital|codigo/.test(q)) return 'Uso PATS';
    if (/laboratorio|lab|analisis/.test(q)) return 'Laboratorio';
    if (/imagen|rayos|ultrasonido|tomografia/.test(q)) return 'Imagenología';
    if (/farmacia|medicamento|receta/.test(q)) return 'Farmacia';
    if (/queja|cobro|reclamo|inconformidad/.test(q)) return 'Quejas';
    if (/seguro|poliza|cobertura/.test(q)) return 'Identidad PATS';

    return 'Aprendizaje supervisado';
  }

  function detectIntent(question, fallbackIntent) {
    const q = normalize(question + ' ' + fallbackIntent);

    if (/vencido|vencio|venido|inactivo|adeudo|no vigente|dejo de pagar/.test(q)) return 'falta_pago';
    if (/reactivar|reactivacion|activar|volver a usar/.test(q)) return 'reactivacion';
    if (/donde.*paga|como.*paga|medios de pago|formas de pago|caja|comprobante|voucher|referencia/.test(q)) return 'pago';
    if (/costo|cuesta|precio|mensualidad|anualidad|800|9600/.test(q)) return 'costo';
    if (/cotiza|cotizar|cotizacion|presupuesto|lead|cirugia|procedimiento/.test(q)) return 'cotizacion';
    if (/vigencia|vigente|activo|estatus/.test(q)) return 'vigencia';
    if (/qr|pasaporte digital|codigo qr/.test(q)) return 'uso_qr';
    if (/laboratorio|lab|analisis/.test(q)) return 'laboratorio';
    if (/imagen|rayos|ultrasonido|tomografia/.test(q)) return 'imagenologia';
    if (/farmacia|medicamento|receta/.test(q)) return 'farmacia';
    if (/seguro|poliza|cobertura/.test(q)) return 'no_seguro';
    if (/queja|reclamo|cobro|inconformidad/.test(q)) return 'queja_cobro';
    if (/beneficio|incluye|descuento|gratis|preferencial/.test(q)) return 'beneficios';

    return text(fallbackIntent) || 'aprendizaje_supervisado';
  }

  function buildKeywords(question, intent, extra) {
    const base = [question, intent, extra || ''].join(' ');

    const stop = new Set([
      'que','como','cuando','donde','porque','para','con','sin','del','de','la','el','los','las',
      'un','una','y','o','a','en','mi','me','su','se','es','son','al','lo','pats','pasaporte','salud',
      'usuario','paciente','persona','dice','pregunta'
    ]);

    const parts = normalize(base)
      .split(/\s+/)
      .filter((w) => w.length >= 3 && !stop.has(w));

    return Array.from(new Set(parts)).join(',');
  }

  async function fetchJson(url, options) {
    const res = await fetch(url, options || {
      method: 'GET',
      credentials: 'same-origin',
      headers: {
        'Accept': 'application/json'
      }
    });

    const raw = await res.text();

    let data = null;

    try {
      data = JSON.parse(raw);
    } catch (err) {
      throw new Error('El endpoint no devolvió JSON válido: ' + raw.slice(0, 300));
    }

    if (!res.ok || !data.ok) {
      throw new Error(data.error || 'El endpoint respondió con error.');
    }

    return data;
  }

  async function postJson(url, payload) {
    return fetchJson(url, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(payload || {})
    });
  }

  async function askTest(question, mode) {
    return postJson(endpoints.ask, {
      question: question,
      mode: mode || 'auto',
      source: 'training_express_test'
    });
  }

  function renderSummary(summary) {
    const s = summary || {};

    if (!$summary) return;

    $summary.innerHTML = `
      <div><strong>${Number(s.pendientes || 0)}</strong><span>Pendientes</span></div>
      <div><strong>${Number(s.sin_resultado || 0)}</strong><span>Sin resultado</span></div>
      <div><strong>${Number(s.baja_confianza || 0)}</strong><span>Baja confianza</span></div>
      <div><strong>${Number(s.feedback_negativo || 0)}</strong><span>Feedback negativo</span></div>
    `;
  }

  function getSuggestion(item) {
    const q = normalize(item.pregunta || '');
    const motivo = text(item.motivo);

    if (/venido|vencio|descuemt|cotisar|pagr|pagarl/.test(q)) {
      return 'Parece error de escritura: usa Sinónimo rápido.';
    }

    if (motivo === 'SIN_RESULTADO') {
      return 'No encontró respuesta: puede necesitar respuesta nueva o sinónimo.';
    }

    if (motivo === 'BAJA_CONFIANZA') {
      return 'Respondió con poca seguridad: puede requerir respuesta específica.';
    }

    if (/que hago|que debe|como procedo|a quien|quien/.test(q)) {
      return 'Parece ruta interna: usa Ruta interna.';
    }

    return 'Elige la acción rápida que corresponda.';
  }

  function renderActionPanel(item, action) {
    const q = text(item.pregunta);
    const intent = detectIntent(q, item.intent_detectado);
    const tema = detectTema(q, intent);

    if (action === 'synonym') {
      return `
        <div class="pats-xtrain-form" data-form="synonym">
          <h4>Sinónimo rápido</h4>
          <p>Úsalo si la pregunta está mal escrita o es una forma distinta de decir algo ya conocido.</p>

          <label>Término usado</label>
          <input class="x-syn-from" type="text" value="${escapeHtml(q)}">

          <label>Debe entenderse como</label>
          <input class="x-syn-to" type="text" placeholder="Ej. PATS vencido / medios de pago / cotización de cirugía">

          <label>Tipo</label>
          <select class="x-syn-type">
            <option value="JERGA_OPERATIVA">Jerga operativa</option>
            <option value="ERROR_ESCRITURA">Error de escritura</option>
            <option value="SINONIMO">Sinónimo</option>
            <option value="MODULO">Módulo</option>
            <option value="SERVICIO">Servicio</option>
          </select>

          <div class="pats-xtrain-form-actions">
            <button type="button" class="pats-xtrain-primary x-action" data-action="save-synonym">
              Guardar y probar
            </button>
            <button type="button" class="pats-xtrain-soft x-action" data-action="cancel">
              Cancelar
            </button>
          </div>
        </div>
      `;
    }

    if (action === 'manual') {
      return `
        <div class="pats-xtrain-form" data-form="manual">
          <h4>Crear respuesta para el usuario</h4>
          <p>Escribe una respuesta clara y segura que el personal pueda decir al usuario.</p>

          <label>Tema</label>
          <input class="x-manual-tema" type="text" value="${escapeHtml(tema)}">

          <label>Pregunta corregida</label>
          <input class="x-manual-question" type="text" value="${escapeHtml(q)}">

          <label>Respuesta aprobada</label>
          <textarea class="x-manual-answer" placeholder="Ej. Si el usuario llega con PATS vencido, primero se valida su estatus..."></textarea>

          <details class="pats-xtrain-advanced">
            <summary>Opciones avanzadas</summary>

            <label>Intent</label>
            <input class="x-manual-intent" type="text" value="${escapeHtml(intent)}">

            <label>Keywords</label>
            <textarea class="x-manual-keywords">${escapeHtml(buildKeywords(q, intent))}</textarea>

            <label>Prioridad</label>
            <input class="x-manual-priority" type="number" value="900">
          </details>

          <div class="pats-xtrain-form-actions">
            <button type="button" class="pats-xtrain-primary x-action" data-action="publish-manual">
              Publicar y probar
            </button>
            <button type="button" class="pats-xtrain-soft x-action" data-action="cancel">
              Cancelar
            </button>
          </div>
        </div>
      `;
    }

    if (action === 'ops') {
      return `
        <div class="pats-xtrain-form" data-form="ops">
          <h4>Crear ruta interna</h4>
          <p>Úsalo cuando el personal necesita pasos, validaciones, módulos o escalamiento.</p>

          <label>Caso</label>
          <input class="x-ops-case" type="text" value="${escapeHtml(tema)}">

          <label>Qué decir al usuario</label>
          <textarea class="x-ops-say" placeholder="Ej. Permíteme validar tu estatus para orientarte correctamente..."></textarea>

          <label>Qué debe hacer el personal</label>
          <textarea class="x-ops-steps" placeholder="1. Validar identidad.&#10;2. Consultar PATS Vigencias.&#10;3. Revisar comprobante.&#10;4. Escalar si hay inconsistencia."></textarea>

          <details class="pats-xtrain-advanced">
            <summary>Opciones avanzadas</summary>

            <label>Validaciones</label>
            <textarea class="x-ops-validations">Validar información en la fuente correspondiente antes de confirmar al usuario.</textarea>

            <label>Módulos a consultar</label>
            <input class="x-ops-modules" type="text" value="PATS Concierge, PATS Vigencias, PATS MONEY, LEAD+ o ADMINPATS, según corresponda.">

            <label>Cuándo escalar</label>
            <textarea class="x-ops-escalate">Escalar a ADMINPATS si no hay información suficiente, si existe inconsistencia, reclamo o solicitud de excepción.</textarea>

            <label>Intent / Tipo de caso</label>
            <input class="x-ops-intent" type="text" value="${escapeHtml(intent)}">

            <label>Keywords</label>
            <textarea class="x-ops-keywords">${escapeHtml(buildKeywords(q, intent, tema))}</textarea>
          </details>

          <div class="pats-xtrain-form-actions">
            <button type="button" class="pats-xtrain-primary x-action" data-action="publish-ops">
              Publicar y probar
            </button>
            <button type="button" class="pats-xtrain-soft x-action" data-action="cancel">
              Cancelar
            </button>
          </div>
        </div>
      `;
    }

    return '';
  }

  function renderTestResult(data) {
    if (!data || !data.ok) {
      return `
        <div class="pats-xtrain-test is-bad">
          <strong>Prueba fallida</strong>
          <p>No se pudo comprobar el aprendizaje.</p>
        </div>
      `;
    }

    const source = text(data.source || 'SIN FUENTE');
    const confidence = Number(data.confidence || 0);
    const intent = text(data.intent || data.detected_intent || 'N/D');

    const ok = source !== 'SIN_RESULTADO' && confidence >= 0.22;

    return `
      <div class="pats-xtrain-test ${ok ? 'is-ok' : 'is-warn'}">
        <strong>${ok ? 'Aprendizaje aplicado' : 'Revisar aprendizaje'}</strong>
        <p>
          Fuente: <b>${escapeHtml(source)}</b><br>
          Intent: <b>${escapeHtml(intent)}</b><br>
          Confianza: <b>${escapeHtml(confidence.toFixed(4))}</b>
        </p>
      </div>
    `;
  }

  function renderItem(item) {
    const motivo = text(item.motivo);
    const estado = text(item.estado);
    const pregunta = text(item.pregunta);
    const respuesta = text(item.respuesta_actual);
    const intent = text(item.intent_detectado || 'N/D');
    const suggestion = getSuggestion(item);

    return `
      <article class="pats-xtrain-card" data-id="${escapeHtml(item.id)}">
        <header class="pats-xtrain-card-head">
          <div>
            <span class="pats-xtrain-card-kicker">Pregunta real</span>
            <h3>“${escapeHtml(pregunta || 'Sin pregunta')}”</h3>
            <p>
              ID ${escapeHtml(item.id)}
              ${item.log_id ? ' · Log ' + escapeHtml(item.log_id) : ''}
              · ${escapeHtml(item.updated_at || '')}
            </p>
          </div>

          <div class="pats-xtrain-pills">
            <span class="${motivoClass(motivo)}">${escapeHtml(motivo)}</span>
            <span>${escapeHtml(estado)}</span>
            <span>Veces: ${escapeHtml(item.veces_detectada || 1)}</span>
            <span>Score: ${escapeHtml(item.score || '0')}</span>
          </div>
        </header>

        <div class="pats-xtrain-card-body">
          <section class="pats-xtrain-info">
            <div class="pats-xtrain-box">
              <label>Respuesta actual / contexto</label>
              <p>${respuesta ? nl2br(respuesta) : 'Sin respuesta registrada.'}</p>
            </div>

            <div class="pats-xtrain-box is-suggestion">
              <label>Sugerencia del entrenador</label>
              <p>${escapeHtml(suggestion)}</p>
              <small>Intentos detectados: ${escapeHtml(intent)}</small>
            </div>
          </section>

        <aside class="pats-xtrain-actions">
  <button type="button" class="x-action" data-action="show-synonym">
    <span class="xtrain-ico">↻</span>
    Sinónimo rápido
  </button>

  <button type="button" class="x-action" data-action="show-manual">
    <span class="xtrain-ico">✎</span>
    Respuesta usuario
  </button>

  <button type="button" class="x-action" data-action="show-ops">
    <span class="xtrain-ico">⌁</span>
    Ruta interna
  </button>

  <button type="button" class="is-ok x-action" data-action="resolve">
    <span class="xtrain-ico">✓</span>
    Está bien
  </button>

  <button type="button" class="is-danger x-action" data-action="discard">
    <span class="xtrain-ico">×</span>
    Descartar
  </button>
</aside>
        </div>

        <div class="pats-xtrain-slot"></div>
      </article>
    `;
  }

  async function handleAction(btn) {
    const card = btn.closest('.pats-xtrain-card');
    if (!card) return;

    const id = Number(card.getAttribute('data-id') || 0);
    const action = btn.getAttribute('data-action');
    const item = currentItems.find((x) => Number(x.id) === id) || {};
    const slot = card.querySelector('.pats-xtrain-slot');

    if (!id || !action) return;

    try {
      btn.disabled = true;

      if (action === 'show-synonym') {
        slot.innerHTML = renderActionPanel(item, 'synonym');
        bindItemActions();
        return;
      }

      if (action === 'show-manual') {
        slot.innerHTML = renderActionPanel(item, 'manual');
        bindItemActions();
        return;
      }

      if (action === 'show-ops') {
        slot.innerHTML = renderActionPanel(item, 'ops');
        bindItemActions();
        return;
      }

      if (action === 'cancel') {
        slot.innerHTML = '';
        return;
      }

      if (action === 'save-synonym') {
        const from = text(card.querySelector('.x-syn-from')?.value);
        const to = text(card.querySelector('.x-syn-to')?.value);
        const tipo = text(card.querySelector('.x-syn-type')?.value) || 'JERGA_OPERATIVA';
        const intent = detectIntent(to || from, item.intent_detectado);

        if (!from || !to) {
          showToast('Captura el término usado y cómo debe entenderse.');
          return;
        }

        await postJson(endpoints.synonym, {
          queue_id: id,
          termino_usuario: from,
          termino_normalizado: to,
          tipo: tipo,
          intent: intent
        });

        const test = await askTest(item.pregunta, 'auto');
        slot.innerHTML = renderTestResult(test);

        showToast('Sinónimo guardado y probado.');
        await loadTraining();
        return;
      }

      if (action === 'publish-manual') {
        const tema = text(card.querySelector('.x-manual-tema')?.value) || 'Aprendizaje supervisado';
        const pregunta = text(card.querySelector('.x-manual-question')?.value) || text(item.pregunta);
        const respuesta = text(card.querySelector('.x-manual-answer')?.value);
        const intent = text(card.querySelector('.x-manual-intent')?.value) || detectIntent(pregunta, item.intent_detectado);
        const keywords = text(card.querySelector('.x-manual-keywords')?.value) || buildKeywords(pregunta, intent);
        const prioridad = Number(card.querySelector('.x-manual-priority')?.value || 900);

        if (!pregunta || !respuesta) {
          showToast('Captura la pregunta corregida y la respuesta aprobada.');
          return;
        }

        await postJson(endpoints.manual, {
          queue_id: id,
          categoria: tema,
          subcategoria: 'Respuesta express',
          intent: intent,
          pregunta_base: pregunta,
          respuesta: respuesta,
          respuesta_corta: respuesta.slice(0, 240),
          keywords: keywords,
          requiere_login: 0,
          requiere_datos_usuario: 0,
          prioridad: prioridad
        });

        const test = await askTest(item.pregunta, 'manual');
        slot.innerHTML = renderTestResult(test);

        showToast('Respuesta Manual publicada y probada.');
        await loadTraining();
        return;
      }

      if (action === 'publish-ops') {
        const caso = text(card.querySelector('.x-ops-case')?.value) || 'Aprendizaje supervisado PATS';
        const decir = text(card.querySelector('.x-ops-say')?.value);
        const pasos = text(card.querySelector('.x-ops-steps')?.value);
        const validaciones = text(card.querySelector('.x-ops-validations')?.value);
        const modulos = text(card.querySelector('.x-ops-modules')?.value);
        const escalar = text(card.querySelector('.x-ops-escalate')?.value);
        const intent = text(card.querySelector('.x-ops-intent')?.value) || detectIntent(item.pregunta, item.intent_detectado);
        const keywords = text(card.querySelector('.x-ops-keywords')?.value) || buildKeywords(item.pregunta, intent, caso);

        if (!decir || !pasos) {
          showToast('Captura qué decir al usuario y qué debe hacer el personal.');
          return;
        }

        await postJson(endpoints.ops, {
          queue_id: id,
          codigo: '',
          proceso: caso,
          fase: 'Atención express',
          tipo_caso: intent.toUpperCase(),
          subtipo_caso: caso,
          pregunta_operativa: item.pregunta,
          situacion_usuario: item.pregunta,
          respuesta_usuario_sugerida: decir,
          pasos_operativos: pasos,
          validaciones: validaciones || 'Validar información en la fuente correspondiente antes de confirmar al usuario.',
          modulos_consultar: modulos || 'PATS Concierge, PATS Vigencias, PATS MONEY, LEAD+ o ADMINPATS, según corresponda.',
          cuando_escalar: escalar || 'Escalar a ADMINPATS si no hay información suficiente, si existe inconsistencia, reclamo o solicitud de excepción.',
          frases_sugeridas: decir,
          errores_evitar: 'No prometer beneficios, precios, vigencia o reactivaciones sin validar en fuente autorizada.',
          roles_aplica: 'CON,CONCIERGE,ADM,ADMISION,CAJ,CAJA,ADMIN,ADMINPATS',
          nivel: 'OPERATIVO',
          keywords: keywords,
          prioridad: 900
        });

        const test = await askTest(item.pregunta, 'operativo');
        slot.innerHTML = renderTestResult(test);

        showToast('Ruta Operativa publicada y probada.');
        await loadTraining();
        return;
      }

      if (action === 'resolve') {
        const ok = window.confirm('¿Marcar esta pregunta como resuelta sin publicar nada nuevo?');
        if (!ok) return;

        await postJson(endpoints.resolve, {
          queue_id: id,
          comentario: 'Marcado como resuelto desde Entrenador Express.'
        });

        showToast('Pregunta marcada como resuelta.');
        await loadTraining();
        return;
      }

      if (action === 'discard') {
        const reason = window.prompt('Motivo de descarte:', 'No aplica para entrenamiento.');
        if (reason === null) return;

        await postJson(endpoints.discard, {
          queue_id: id,
          comentario: reason || 'Descartado desde Entrenador Express.'
        });

        showToast('Pregunta descartada.');
        await loadTraining();
      }

    } catch (err) {
      console.error('[XTRAIN ACTION ERROR]', err);
      showToast(err && err.message ? err.message : 'No fue posible completar la acción.');
    } finally {
      btn.disabled = false;
    }
  }

  function bindItemActions() {
    document.querySelectorAll('.x-action').forEach((btn) => {
      btn.onclick = () => handleAction(btn);
    });
  }

  async function loadTraining() {
    const params = new URLSearchParams();

    if (currentEstado) params.set('estado', currentEstado);
    if (currentMotivo) params.set('motivo', currentMotivo);
    if ($search && text($search.value)) params.set('q', text($search.value));

    $list.innerHTML = '<div class="pats-xtrain-empty">Cargando preguntas pendientes...</div>';

    try {
      const url = `${endpoints.list}?${params.toString()}`;
      console.log('[XTRAIN] Cargando:', url);

      const data = await fetchJson(url);

      renderSummary(data.summary);

      currentItems = Array.isArray(data.items) ? data.items : [];

      console.log('[XTRAIN] Items recibidos:', currentItems.length, currentItems);

      if (!currentItems.length) {
        $list.innerHTML = '<div class="pats-xtrain-empty">No hay preguntas pendientes con estos filtros.</div>';
        return;
      }

      $list.innerHTML = currentItems.map(renderItem).join('');
      bindItemActions();

    } catch (err) {
      console.error('[XTRAIN LOAD ERROR]', err);

      const msg = err && err.message ? err.message : 'Error al cargar entrenamiento.';

      $list.innerHTML = `
        <div class="pats-xtrain-empty">
          ${escapeHtml(msg)}
        </div>
      `;

      showToast(msg);
    }
  }

  function bindEvents() {
    if ($reload) {
      $reload.onclick = () => loadTraining();
    }

    if ($tabs) {
      $tabs.querySelectorAll('button').forEach((btn) => {
        btn.onclick = () => {
          $tabs.querySelectorAll('button').forEach((b) => b.classList.remove('is-active'));
          btn.classList.add('is-active');

          currentEstado = btn.getAttribute('data-estado') || 'PENDIENTE';
          currentMotivo = btn.getAttribute('data-motivo') || '';

          loadTraining();
        };
      });
    }

    if ($search) {
      let timer = null;

      $search.oninput = () => {
        clearTimeout(timer);
        timer = setTimeout(loadTraining, 350);
      };
    }

    const manual = document.querySelector('[data-xmanual]');
    const manualToggle = document.querySelector('[data-xmanual-toggle]');
    const manualBody = document.querySelector('[data-xmanual-body]');

    if (manual && manualToggle && manualBody) {
      manualToggle.onclick = () => {
        const open = manual.classList.toggle('is-open');

        manual.classList.toggle('is-collapsed', !open);
        manualToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        manualBody.hidden = !open;
      };
    } else {
      console.warn('[XTRAIN] No se encontró manual colapsable.');
    }
  }

  bindEvents();
  loadTraining();

})();