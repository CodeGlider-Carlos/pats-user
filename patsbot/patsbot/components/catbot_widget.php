<?php
/*
Archivo: ez/patsbot/components/catbot_widget.php
Módulo: PATS / CAT BOT
Propósito: Componente reutilizable responsive del Asistente PATS para incrustarse en pantallas web y app responsive.
Responsabilidad: Renderizar un widget simple, premium y mobile-first sin duplicar lógica del motor.
Conexiones: Consume endpoints existentes en ez/patsbot/endpoints/catbot_ask.php y catbot_feedback.php.
Tipo: Transversal reutilizable dentro de PATSBOT.
*/

if (!function_exists('pats_catbot_widget')) {
  function pats_catbot_widget(array $opts = []): void
  {
    $id = preg_replace('/[^a-zA-Z0-9_\-]/', '', (string)($opts['id'] ?? ('patsCatbotWidget_' . uniqid())));
    $modo = (string)($opts['modo'] ?? 'manual');
    $contexto = (string)($opts['contexto'] ?? 'widget_usuario');
    $audiencia = (string)($opts['audiencia'] ?? 'usuario'); // usuario | concierge | interno
    $titulo = (string)($opts['titulo'] ?? 'Asistente PATS');
    $subtitulo = (string)($opts['subtitulo'] ?? 'Apoyo rápido para resolver dudas PATS.');
    $compacto = array_key_exists('compacto', $opts) ? (bool)$opts['compacto'] : true;
    $mostrarContexto = array_key_exists('mostrar_contexto', $opts) ? (bool)$opts['mostrar_contexto'] : false;
    $mostrarTabs = array_key_exists('mostrar_tabs', $opts) ? (bool)$opts['mostrar_tabs'] : false;
    $mostrarFeedback = array_key_exists('mostrar_feedback', $opts) ? (bool)$opts['mostrar_feedback'] : true;
    $mostrarQuickPrompts = array_key_exists('mostrar_quick_prompts', $opts) ? (bool)$opts['mostrar_quick_prompts'] : true;
    $endpointBase = rtrim((string)($opts['endpoint_base'] ?? 'endpoints'), '/');
    $askEndpoint = (string)($opts['ask_endpoint'] ?? ($endpointBase . '/catbot_ask.php'));
    $feedbackEndpoint = (string)($opts['feedback_endpoint'] ?? ($endpointBase . '/catbot_feedback.php'));

    $rol = strtoupper(trim($_SESSION['rol'] ?? ''));
    $usuario = trim($_SESSION['usuario'] ?? '');
    $nombre = trim($_SESSION['nombre'] ?? $usuario);
    $region = trim($_SESSION['acroregion'] ?? ($_SESSION['region'] ?? ''));
    $unidad = trim($_SESSION['acronu'] ?? ($_SESSION['unidad'] ?? ''));

    $contextoInicial = $opts['contexto_inicial'] ?? [];
    if (!is_array($contextoInicial)) {
      $contextoInicial = [];
    }

    $quickPrompts = $opts['quick_prompts'] ?? [
      ['label' => 'Qué es PATS', 'prompt' => 'Qué es PATS'],
      ['label' => 'Costo', 'prompt' => 'Cuánto cuesta PATS'],
      ['label' => 'Incluye', 'prompt' => 'Qué incluye PATS'],
      ['label' => 'Vigencia', 'prompt' => 'Cómo valido si PATS está activo'],
      ['label' => 'Pago', 'prompt' => 'Dónde puedo pagar PATS'],
      ['label' => 'Urgencias', 'prompt' => 'Cómo uso PATS en urgencias']
    ];

    $allowedModes = ['manual', 'operativo', 'auto', 'whatsapp'];
    if (!in_array($modo, $allowedModes, true)) {
      $modo = 'manual';
    }

    /*
    Regla de seguridad visual:
    Si el widget es para usuario final, no exponemos tabs internos ni contexto operativo por default.
    */
    if ($audiencia === 'usuario') {
      if (!array_key_exists('mostrar_tabs', $opts)) $mostrarTabs = false;
      if (!array_key_exists('mostrar_contexto', $opts)) $mostrarContexto = false;
      if (!in_array($modo, ['manual', 'whatsapp'], true)) $modo = 'manual';
    }

    $config = [
      'id' => $id,
      'askEndpoint' => $askEndpoint,
      'feedbackEndpoint' => $feedbackEndpoint,
      'defaultMode' => $modo,
      'contexto' => $contexto,
      'audiencia' => $audiencia,
      'mostrarContexto' => $mostrarContexto,
      'mostrarFeedback' => $mostrarFeedback,
      'mostrarQuickPrompts' => $mostrarQuickPrompts,
      'role' => $rol,
      'user' => $usuario,
      'nombre' => $nombre,
      'region' => $region,
      'unidad' => $unidad,
      'contextoInicial' => $contextoInicial
    ];

    $class = 'pats-cw';
    if ($compacto) $class .= ' is-compact';
    if (!$mostrarContexto) $class .= ' no-context';
    if ($audiencia === 'usuario') $class .= ' is-user-widget';
    ?>
    <section
      id="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>"
      class="<?= htmlspecialchars($class, ENT_QUOTES, 'UTF-8') ?>"
      data-pats-catbot-widget
      data-config='<?= htmlspecialchars(json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') ?>'
    >
      <div class="pats-cw-card">

        <header class="pats-cw-head">
          <div class="pats-cw-brand">
            <div class="pats-cw-mark">P</div>
            <div>
              <span>Asistente PATS</span>
              <h2><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h2>
              <p><?= htmlspecialchars($subtitulo, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
          </div>

          <div class="pats-cw-live">
            <i></i>
            <b>Activo</b>
          </div>
        </header>

        <?php if ($mostrarTabs): ?>
          <nav class="pats-cw-modes" aria-label="Modo de respuesta CAT BOT PATS">
            <button type="button" class="<?= $modo === 'manual' ? 'is-active' : '' ?>" data-cw-mode="manual">Usuario</button>
            <?php if ($audiencia !== 'usuario'): ?>
              <button type="button" class="<?= $modo === 'operativo' ? 'is-active' : '' ?>" data-cw-mode="operativo">Interno</button>
              <button type="button" class="<?= $modo === 'auto' ? 'is-active' : '' ?>" data-cw-mode="auto">Completo</button>
            <?php endif; ?>
            <button type="button" class="<?= $modo === 'whatsapp' ? 'is-active' : '' ?>" data-cw-mode="whatsapp">WhatsApp</button>
          </nav>
        <?php endif; ?>

        <?php if ($mostrarContexto): ?>
          <details class="pats-cw-context" data-cw-context-panel>
            <summary>
              <span>Contexto de atención</span>
              <b data-cw-context-summary>Sin datos adicionales</b>
            </summary>

            <div class="pats-cw-context-grid">
              <label>
                <span>Usuario</span>
                <input type="text" data-cw-context="nombre_usuario" placeholder="Nombre opcional">
              </label>

              <label>
                <span>Teléfono</span>
                <input type="text" data-cw-context="telefono" placeholder="Opcional">
              </label>

              <label>
                <span>Duda</span>
                <select data-cw-context="tipo_duda">
                  <option value="">Seleccionar</option>
                  <option value="que_es_pats">Qué es PATS</option>
                  <option value="costos">Costos</option>
                  <option value="beneficios">Beneficios</option>
                  <option value="vigencia">Vigencia</option>
                  <option value="pago">Pago</option>
                  <option value="reactivacion">Reactivación</option>
                  <option value="urgencias">Urgencias</option>
                  <option value="cotizacion">Cotización</option>
                  <option value="queja">Queja / reclamo</option>
                </select>
              </label>

              <label>
                <span>Estatus</span>
                <select data-cw-context="estatus_pats">
                  <option value="no_consultado">No consultado</option>
                  <option value="activo">Activo</option>
                  <option value="vencido">Vencido</option>
                  <option value="sin_registro">Sin registro</option>
                  <option value="inconsistente">Inconsistente</option>
                </select>
              </label>

              <label>
                <span>Canal</span>
                <select data-cw-context="canal">
                  <option value="presencial">Presencial</option>
                  <option value="telefono">Teléfono</option>
                  <option value="whatsapp">WhatsApp</option>
                  <option value="correo">Correo</option>
                  <option value="interno">Interno</option>
                </select>
              </label>

              <label>
                <span>Prioridad</span>
                <select data-cw-context="prioridad">
                  <option value="normal">Normal</option>
                  <option value="alta">Alta</option>
                  <option value="critica">Crítica</option>
                </select>
              </label>
            </div>

            <button type="button" class="pats-cw-clear-context" data-cw-clear-context>Limpiar contexto</button>
          </details>
        <?php endif; ?>

        <?php if ($mostrarQuickPrompts): ?>
          <div class="pats-cw-quick" aria-label="Preguntas rápidas">
            <?php foreach ($quickPrompts as $qp): ?>
              <?php
                $label = is_array($qp) ? (string)($qp['label'] ?? '') : '';
                $prompt = is_array($qp) ? (string)($qp['prompt'] ?? $label) : '';
                if ($label === '' || $prompt === '') continue;
              ?>
              <button type="button" data-cw-prompt="<?= htmlspecialchars($prompt, ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
              </button>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <main class="pats-cw-body">
          <section class="pats-cw-chat">
            <div class="pats-cw-chat-head">
              <div>
                <span data-cw-mode-label><?= $audiencia === 'usuario' ? 'Consulta PATS' : 'Usuario' ?></span>
                <h3>Escribe tu duda</h3>
              </div>
              <button type="button" data-cw-clear-chat>Limpiar</button>
            </div>

            <div class="pats-cw-messages" data-cw-messages>
              <article class="pats-cw-msg is-bot">
                <div class="pats-cw-avatar">P</div>
                <div class="pats-cw-bubble">
                  <strong>Asistente PATS</strong>
                  <p>Hola, puedo ayudarte con dudas generales sobre PATS.</p>
                </div>
              </article>
            </div>

            <form class="pats-cw-form" data-cw-form autocomplete="off">
              <textarea data-cw-question rows="2" maxlength="1200" placeholder="Escribe tu duda..." required></textarea>
              <button type="submit" data-cw-send>Enviar</button>
            </form>
          </section>

          <aside class="pats-cw-result" data-cw-result>
            <div class="pats-cw-empty">
              <div>P</div>
              <h3>Respuesta PATS</h3>
              <p>Aquí aparecerá la respuesta a tu consulta.</p>
            </div>
          </aside>
        </main>

      </div>
    </section>
    <?php
  }
}
?>