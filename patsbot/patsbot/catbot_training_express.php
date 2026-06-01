<?php
/*
ez/patsbot/catbot_training_express.php
CAT BOT PATS — Entrenador Express
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

$rolesTraining = ['ADMIN', 'ADMINPATS'];

if (!in_array($adminrol, $rolesTraining, true)) {
  header('Location: catbot.php');
  exit;
}

$displayName = $userName !== '' ? $userName : $userLog;
$avatarLetter = mb_substr($displayName ?: 'P', 0, 1, 'UTF-8');
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Entrenador Express CHAT BOT PATS</title>

  <link rel="icon" type="image/x-icon" href="../../img/ez.ico" />

  <link rel="stylesheet" href="../../css/index.css?v=<?= $ver ?>">
  <link rel="stylesheet" href="../../css/gloval_responsive.css?v=<?= $ver ?>">
  <link rel="stylesheet" href="css/catbot.css?v=<?= $ver ?>">
  <link rel="stylesheet" href="css/catbot_training_express.css?v=<?= $ver ?>">
  <link rel="stylesheet" href="css/pats_responsive.css?v=<?= $ver ?>">
</head>

<body
  data-role="<?= htmlspecialchars($adminrol, ENT_QUOTES, 'UTF-8') ?>"
  data-region="<?= htmlspecialchars($userRegion, ENT_QUOTES, 'UTF-8') ?>"
  data-unidad="<?= htmlspecialchars($userUnidad, ENT_QUOTES, 'UTF-8') ?>"
>

<?php require_once '../../loglog/contador.php'; ?>

<div id="cabecera">
  <div class="bienvenido">Entrenador Chatbot</div>
  <?php require_once '../nav/noti_user.php'; ?>
</div>

<?php require_once '../nav/nav_mod.php'; ?>

<content class="back_content">

  <section class="pats-xtrain-page">

    <div class="pats-xtrain-hero">
      <div class="pats-xtrain-hero-main">
        <span class="pats-xtrain-kicker">CHAT BOT · Entrenamiento rápido</span>
        <h1>Entrenador Express PATS</h1>
        <p>
          Corrige preguntas reales sin tocar SQL. Elige si la duda necesita un sinónimo,
          una respuesta para el usuario o una ruta interna para el personal.
        </p>
      </div>

      <div class="pats-xtrain-hero-actions">
        <div class="pats-xtrain-status">
          <span class="pats-xtrain-dot"></span>
          <div>
            <strong>Supervisor activo</strong>
            <small><?= htmlspecialchars($adminrol, ENT_QUOTES, 'UTF-8') ?></small>
          </div>
        </div>

        <button type="button" class="pats-xtrain-back" onclick="window.location.href='catbot.php'">
          Volver al CHAT BOT
        </button>
      </div>
    </div>

    <div class="pats-xtrain-shell">

      <aside class="pats-xtrain-side">

        <div class="pats-xtrain-user">
          <div class="pats-xtrain-avatar">
            <?= htmlspecialchars($avatarLetter, ENT_QUOTES, 'UTF-8') ?>
          </div>
          <div>
            <strong><?= htmlspecialchars($displayName ?: 'Usuario', ENT_QUOTES, 'UTF-8') ?></strong>
            <span>
              <?= htmlspecialchars($adminrol, ENT_QUOTES, 'UTF-8') ?>
              ·
              <?= htmlspecialchars($userUnidad ?: 'SIN UNIDAD', ENT_QUOTES, 'UTF-8') ?>
            </span>
          </div>
        </div>

        <div class="pats-xtrain-panel">
          <h3>Filtros rápidos</h3>

          <div class="pats-xtrain-tabs" id="xtrainQuickTabs">
            <button type="button" class="is-active" data-estado="PENDIENTE" data-motivo="">
              Pendientes
            </button>
            <button type="button" data-estado="PENDIENTE" data-motivo="SIN_RESULTADO">
              Sin resultado
            </button>
            <button type="button" data-estado="PENDIENTE" data-motivo="BAJA_CONFIANZA">
              Baja confianza
            </button>
            <button type="button" data-estado="PENDIENTE" data-motivo="INCORRECTO">
              Incorrectas
            </button>
          </div>

          <input
            type="search"
            id="xtrainSearch"
            class="pats-xtrain-search"
            placeholder="Buscar pregunta..."
            autocomplete="off"
          >

          <button type="button" class="pats-xtrain-primary" id="xtrainReload">
            Actualizar
          </button>
        </div>

        <div class="pats-xtrain-manual is-collapsed" data-xmanual>
          <button type="button" class="pats-xtrain-manual-head" data-xmanual-toggle aria-expanded="false">
            <span>
              <strong>Guía rápida</strong>
              <small>Cómo entrenar sin complicarte</small>
            </span>
            <i>
              <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M7.4 8.8 12 13.4l4.6-4.6L18 10.2l-6 6-6-6 1.4-1.4Z"></path>
              </svg>
            </i>
          </button>

          <div class="pats-xtrain-manual-body" data-xmanual-body hidden>
            <div>
              <b>Sinónimo</b>
              <p>Úsalo cuando el bot ya sabe la respuesta, pero no entendió cómo la escribieron.</p>
              <em>Ejemplo: “pasaporte venido” = “pasaporte vencido”.</em>
            </div>

            <div>
              <b>Respuesta usuario</b>
              <p>Úsalo para crear una explicación clara que Concierge/Admisión/Caja puede decir.</p>
              <em>Ejemplo: qué explicar si PATS está vencido.</em>
            </div>

            <div>
              <b>Ruta interna</b>
              <p>Úsalo cuando el personal necesita pasos: validar, consultar módulo, escalar.</p>
              <em>Ejemplo: revisar PATS Vigencias y comprobante.</em>
            </div>

            <div class="is-warning">
              <b>Regla crítica</b>
              <p>No publiques precios, vigencias, beneficios o reactivaciones si no están validados.</p>
            </div>
          </div>
        </div>

      </aside>

      <main class="pats-xtrain-main">

        <div class="pats-xtrain-main-head">
          <div>
            <span>Cola de aprendizaje</span>
            <h2>Corrección rápida</h2>
            <p>
              Cada tarjeta representa una pregunta real. Elige una acción y el sistema publica,
              prueba y marca el aprendizaje.
            </p>
          </div>
        </div>

        <div class="pats-xtrain-summary" id="xtrainSummary">
          <div><strong>0</strong><span>Pendientes</span></div>
          <div><strong>0</strong><span>Sin resultado</span></div>
          <div><strong>0</strong><span>Baja confianza</span></div>
          <div><strong>0</strong><span>Feedback negativo</span></div>
        </div>

        <div class="pats-xtrain-list" id="xtrainList">
          <div class="pats-xtrain-empty">Cargando preguntas pendientes...</div>
        </div>

      </main>

    </div>

  </section>

</content>

<div class="pats-xtrain-toast" id="xtrainToast" aria-live="polite"></div>

<script>
window.PATS_XTRAIN_CONFIG = {
  listEndpoint: 'endpoints/catbot_training_list.php',
  synonymEndpoint: 'endpoints/catbot_training_save_synonym.php',
  manualEndpoint: 'endpoints/catbot_training_publish_manual.php',
  opsEndpoint: 'endpoints/catbot_training_publish_ops.php',
  resolveEndpoint: 'endpoints/catbot_training_resolve.php',
  discardEndpoint: 'endpoints/catbot_training_discard.php',
  askEndpoint: 'endpoints/catbot_ask.php',
  role: <?= json_encode($adminrol, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
  user: <?= json_encode($userLog, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
  region: <?= json_encode($userRegion, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
  unidad: <?= json_encode($userUnidad, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
};
</script>

<script src="js/catbot_training_express.js?v=<?= $ver ?>"></script>

</body>
</html>