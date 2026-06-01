<?php
/*
Archivo: ez/patsbot/catbot.php
Módulo: PATS / CAT BOT Concierge
Propósito: Pantalla principal del asistente operativo para Concierge, Admisión, Caja y ADMINPATS.
Responsabilidad: Renderizar una interfaz real de atención al usuario PATS, con contexto de atención, preguntas rápidas, respuesta para usuario, WhatsApp, validaciones, ruta interna y feedback.
Conexiones: Usa endpoints/catbot_ask.php y endpoints/catbot_feedback.php. Mantiene acceso al Entrenador Express para ADMIN y ADMINPATS.
Tipo: Específico del módulo PATS. Esta versión NO reemplaza el modo entrenamiento ni sus privilegios actuales.
*/

session_start();

require_once '../../varSQL/bd.php';
require_once '../../varSQL/var.php';
require_once '../../varSQL/catalogos.php';

if (empty($_SESSION['usuario'])) {
  session_destroy();
  header("Location: ../../../index.php");
  exit;
}

$ver = time();

$adminrol   = strtoupper(trim($_SESSION['rol'] ?? ''));
$userLog    = trim($_SESSION['usuario'] ?? '');
$userName   = trim($_SESSION['nombre'] ?? $userLog);
$userRegion = trim($_SESSION['acroregion'] ?? ($_SESSION['region'] ?? ''));
$userUnidad = trim($_SESSION['acronu'] ?? ($_SESSION['unidad'] ?? ''));
$appy       = trim($_SESSION['app'] ?? '');

/*
|------------------------------------------------------------
| ROLES AUTORIZADOS CAT BOT PATS
|------------------------------------------------------------
| Este módulo es para atención interna: Concierge, Admisión,
| Caja, Recepción y ADMINPATS. No es para franquiciatario ni
| distribuidor externo.
*/
$rolesCatbot = [
  'ADMIN',
  'ADMINPATS',
  'CON',
  'CONCIERGE',
  'ADM',
  'ADMISION',
  'CAJ',
  'CAJA',
  'RECEPCION'
];

if (!in_array($adminrol, $rolesCatbot, true)) {
  header('Location: index.php');
  exit;
}

$canTrain = in_array($adminrol, ['ADMIN', 'ADMINPATS'], true);
$displayName = $userName !== '' ? $userName : $userLog;
$avatarLetter = mb_substr($displayName ?: 'P', 0, 1, 'UTF-8');
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Asistente PATS — Concierge</title>

  <link rel="icon" type="image/x-icon" href="../../img/ez.ico" />

  <link rel="stylesheet" href="../../css/index.css?v=<?= $ver ?>">
  <link rel="stylesheet" href="../../css/gloval_responsive.css?v=<?= $ver ?>">
  <link rel="stylesheet" href="css/pats_responsive.css?v=<?= $ver ?>">

  <!-- Interfaz Concierge real. No altera el CSS del entrenamiento. -->
  <link rel="stylesheet" href="css/catbot_concierge.css?v=<?= $ver ?>">
</head>

<body
  data-role="<?= htmlspecialchars($adminrol, ENT_QUOTES, 'UTF-8') ?>"
  data-region="<?= htmlspecialchars($userRegion, ENT_QUOTES, 'UTF-8') ?>"
  data-unidad="<?= htmlspecialchars($userUnidad, ENT_QUOTES, 'UTF-8') ?>"
>

<?php require_once '../../loglog/contador.php'; ?>

<div id="cabecera">
  <div class="bienvenido">PATS CONCIERGE</div>
  <?php require_once '../nav/noti_user.php'; ?>
</div>

<?php require_once '../nav/nav_mod.php'; ?>

<content class="back_content">

  <section class="pats-concierge-page" aria-label="Asistente PATS Concierge">

    <header class="pats-concierge-hero">
      <div class="pats-concierge-hero__main">
        <span class="pats-concierge-kicker">PATS · Asistente de atención</span>
        <h1>Concierge PATS</h1>
        <p>
          Responde dudas del usuario con lenguaje claro, valida antes de prometer beneficios
          y conserva una ruta interna segura para Concierge, Admisión y Caja.
        </p>
      </div>

      <div class="pats-concierge-hero__actions">
        <div class="pats-concierge-operator">
          <div class="pats-concierge-avatar"><?= htmlspecialchars($avatarLetter, ENT_QUOTES, 'UTF-8') ?></div>
          <div>
            <strong><?= htmlspecialchars($displayName ?: 'Usuario', ENT_QUOTES, 'UTF-8') ?></strong>
            <span><?= htmlspecialchars($adminrol, ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars($userUnidad ?: 'SIN UNIDAD', ENT_QUOTES, 'UTF-8') ?></span>
          </div>
        </div>

        <?php if ($canTrain): ?>
          <button type="button" class="pats-concierge-train" onclick="window.location.href='catbot_training_express.php'">
            Entrenador Express
          </button>
        <?php endif; ?>
      </div>
    </header>

    <div class="pats-concierge-modebar" role="group" aria-label="Modo de atención CAT BOT PATS">
      <button type="button" class="is-active" data-catbot-mode="manual">
        <strong>Usuario</strong>
        <span>Respuesta clara</span>
      </button>
      <button type="button" data-catbot-mode="operativo">
        <strong>Interno</strong>
        <span>Ruta operativa</span>
      </button>
      <button type="button" data-catbot-mode="auto">
        <strong>Completo</strong>
        <span>Usuario + validación</span>
      </button>
      <button type="button" data-catbot-mode="whatsapp">
        <strong>WhatsApp</strong>
        <span>Texto copiable</span>
      </button>
    </div>

    <div class="pats-concierge-shell">

      <aside class="pats-concierge-left">

        <section class="pats-concierge-card pats-concierge-context-card">
          <div class="pats-concierge-section-head">
            <span>Contexto de atención</span>
            <button type="button" id="catbotContextClear" class="pats-concierge-link-btn">Limpiar</button>
          </div>

          <div class="pats-concierge-context-grid">
            <label>
              <span>Nombre del usuario</span>
              <input type="text" id="ctxNombre" maxlength="120" placeholder="Opcional">
            </label>

            <label>
              <span>Teléfono</span>
              <input type="text" id="ctxTelefono" maxlength="30" placeholder="Opcional">
            </label>

            <label>
              <span>Tipo de duda</span>
              <select id="ctxTipoDuda">
                <option value="">Seleccionar</option>
                <option value="que_es_pats">Qué es PATS</option>
                <option value="costo">Costo / mensualidad</option>
                <option value="beneficios">Beneficios</option>
                <option value="vigencia">Vigencia / estatus</option>
                <option value="reactivacion">Reactivación</option>
                <option value="urgencias">Urgencias</option>
                <option value="especialistas">Especialistas</option>
                <option value="laboratorio">Laboratorio</option>
                <option value="imagenologia">Imagenología</option>
                <option value="farmacia">Farmacia</option>
                <option value="cotizacion">Cotización</option>
                <option value="pago">Pago / comprobante</option>
                <option value="queja">Queja / cobro</option>
              </select>
            </label>

            <label>
              <span>Estatus PATS</span>
              <select id="ctxEstatus">
                <option value="no_consultado">No consultado</option>
                <option value="activo">Activo</option>
                <option value="vencido">Vencido</option>
                <option value="inactivo">Inactivo</option>
                <option value="sin_registro">Sin registro</option>
                <option value="duda">Con inconsistencia</option>
              </select>
            </label>

            <label>
              <span>Canal</span>
              <select id="ctxCanal">
                <option value="presencial">Presencial</option>
                <option value="telefono">Teléfono</option>
                <option value="whatsapp">WhatsApp</option>
                <option value="recepcion">Recepción</option>
                <option value="caja">Caja</option>
              </select>
            </label>

            <label>
              <span>Prioridad</span>
              <select id="ctxPrioridad">
                <option value="normal">Normal</option>
                <option value="urgente">Urgente</option>
                <option value="critica">Crítica / escalar</option>
              </select>
            </label>
          </div>
        </section>

        <section class="pats-concierge-card">
          <div class="pats-concierge-section-head">
            <span>Preguntas rápidas</span>
          </div>

          <div class="pats-concierge-quick-list">
            <button type="button" data-prompt="Qué es PATS">Qué es PATS</button>
            <button type="button" data-prompt="Cuánto cuesta PATS y qué incluye">Costo e inclusión</button>
            <button type="button" data-prompt="Qué pasa si mi PATS está vencido o dejé de pagar">Vencido / falta de pago</button>
            <button type="button" data-prompt="Cómo se reactiva PATS">Reactivación</button>
            <button type="button" data-prompt="Cómo uso PATS en urgencias o consulta general">Urgencias / consulta</button>
            <button type="button" data-prompt="Cómo uso PATS para especialistas laboratorio imagenología o farmacia">Servicios médicos</button>
            <button type="button" data-prompt="PATS es seguro médico o póliza de gastos médicos">No es seguro</button>
            <button type="button" data-prompt="Qué hago si el usuario reclama un cobro o dice que no le aplicaron beneficio PATS">Queja / cobro</button>
          </div>
        </section>

        <section class="pats-concierge-card pats-concierge-rule">
          <span>Regla crítica</span>
          <p>
            Nunca confirmes precios, vigencia, disponibilidad, descuento, reactivación o cobertura
            sin validar en el módulo correspondiente o con ADMINPATS.
          </p>
        </section>

      </aside>

      <main class="pats-concierge-center">

        <section class="pats-concierge-chat-card">
          <div class="pats-concierge-chat-head">
            <div>
              <span id="catbotModeEyebrow">Modo Usuario</span>
              <h2>Consulta de atención</h2>
              <p id="catbotModeHelp">Pregunta como lo diría el usuario. El sistema separará qué decir y qué validar.</p>
            </div>
            <button type="button" id="catbotClearBtn" class="pats-concierge-soft-btn">Limpiar chat</button>
          </div>

          <div class="pats-concierge-messages" id="catbotMessages" aria-live="polite">
            <article class="pats-concierge-msg is-bot">
              <div class="pats-concierge-msg__avatar">P</div>
              <div class="pats-concierge-bubble">
                <strong>Asistente PATS</strong>
                <p>
                  Listo para apoyar la atención. Captura el contexto si lo tienes y escribe la duda del usuario.
                  Puedes usar una pregunta rápida o redactar el caso completo.
                </p>
              </div>
            </article>
          </div>

          <form id="catbotForm" class="pats-concierge-form" autocomplete="off">
            <textarea
              id="catbotQuestion"
              name="question"
              rows="3"
              maxlength="1200"
              placeholder="Ejemplo: El usuario pregunta si PATS cubre urgencias o si puede usarlo aunque está vencido..."
              required
            ></textarea>

            <div class="pats-concierge-form-foot">
              <span id="catbotCounter">0/1200</span>
              <div>
                <button type="button" id="catbotWhatsappHint" class="pats-concierge-soft-btn">Preparar WhatsApp</button>
                <button type="submit" id="catbotSendBtn" class="pats-concierge-send">Consultar</button>
              </div>
            </div>
          </form>
        </section>

      </main>

      <aside class="pats-concierge-right" id="catbotResultPanel">
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
      </aside>

    </div>

  </section>

</content>

<div class="pats-concierge-toast" id="catbotToast" aria-live="polite"></div>

<script>
window.PATS_CONTEXT = {
  view: 'catbot_concierge',
  rol: <?= json_encode($adminrol, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
  usuario: <?= json_encode($userLog, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
  nombre: <?= json_encode($userName, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
  region: <?= json_encode($userRegion, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
  unidad: <?= json_encode($userUnidad, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
};

window.PATS_CATBOT_CONFIG = {
  endpoint: 'endpoints/catbot_ask.php',
  feedbackEndpoint: 'endpoints/catbot_feedback.php',
  defaultMode: 'manual',
  role: <?= json_encode($adminrol, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
  user: <?= json_encode($userLog, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
  region: <?= json_encode($userRegion, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
  unidad: <?= json_encode($userUnidad, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
};
</script>

<script src="js/catbot_concierge.js?v=<?= $ver ?>"></script>

</body>
</html>
