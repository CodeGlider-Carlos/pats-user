<?php
/*
ez/patsbot/catbot_training.php
CAT BOT PATS — Panel de entrenamiento supervisado
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

/*
|------------------------------------------------------------
| ROLES AUTORIZADOS PARA ENTRENAR
|------------------------------------------------------------
| Este panel sí debe ser más restringido que el CAT BOT normal.
*/
$rolesTraining = [
  'ADMIN',
  'ADMINPATS'
];

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
  <title>Entrenamiento CAT BOT PATS</title>

  <link rel="icon" type="image/x-icon" href="../../img/ez.ico" />

  <link rel="stylesheet" href="../../css/index.css?v=<?= $ver ?>">
  <link rel="stylesheet" href="../../css/gloval_responsive.css?v=<?= $ver ?>">
  <link rel="stylesheet" href="css/catbot.css?v=<?= $ver ?>">
  <link rel="stylesheet" href="css/catbot_training.css?v=<?= $ver ?>">
  <link rel="stylesheet" href="css/pats_responsive.css?v=<?= $ver ?>">
</head>

<body
  data-role="<?= htmlspecialchars($adminrol, ENT_QUOTES, 'UTF-8') ?>"
  data-region="<?= htmlspecialchars($userRegion, ENT_QUOTES, 'UTF-8') ?>"
  data-unidad="<?= htmlspecialchars($userUnidad, ENT_QUOTES, 'UTF-8') ?>"
>

<?php require_once '../../loglog/contador.php'; ?>

<div id="cabecera">
  <div class="bienvenido">ENTRENAMIENTO CHAT BOT</div>
  <?php require_once '../nav/noti_user.php'; ?>
</div>

<?php require_once '../nav/nav_mod.php'; ?>

<content class="back_content">

  <section class="pats-train-page">

    <!-- =====================================================
         HERO
    ====================================================== -->
    <div class="pats-train-hero">
      <div class="pats-train-hero-main">
        <span class="pats-train-kicker">CAT BOT · Entrenamiento Supervisado</span>

        <h1>Entrenar Asistente PATS</h1>

        <p>
          Revisa preguntas reales sin respuesta, baja confianza o feedback negativo.
          Desde aquí puedes convertir dudas del personal en conocimiento del Manual,
          operación interna o sinónimos.
        </p>
      </div>

      <div class="pats-train-hero-actions">
        <div class="pats-train-status">
          <span class="pats-train-dot"></span>
          <div>
            <strong>Supervisor activo</strong>
            <small>ADMIN / ADMINPATS</small>
          </div>
        </div>

        <button
          type="button"
          class="pats-train-back-btn"
          onclick="window.location.href='catbot.php'"
        >
          Volver al CHAT BOT
        </button>
      </div>
    </div>

    <!-- =====================================================
         GRID PRINCIPAL
    ====================================================== -->
    <div class="pats-train-grid">

      <!-- =====================================================
           SIDEBAR
      ====================================================== -->
      <aside class="pats-train-sidebar">

        <section class="pats-train-profile">
          <div class="profile-user">
            <div class="pats-train-avatar">
              <?= htmlspecialchars($avatarLetter, ENT_QUOTES, 'UTF-8') ?>
            </div>

            <div>
              <strong><?= htmlspecialchars($displayName ?: 'Usuario', ENT_QUOTES, 'UTF-8') ?></strong>
              <small>
                <?= htmlspecialchars($adminrol, ENT_QUOTES, 'UTF-8') ?>
                ·
                <?= htmlspecialchars($userUnidad ?: 'SIN UNIDAD', ENT_QUOTES, 'UTF-8') ?>
              </small>
            </div>
          </div>
        </section>

        <section class="pats-train-sidebox">
          <h3>Filtros</h3>

          <div class="pats-train-filters">
            <select id="trainFilterMotivo">
              <option value="">Todos los motivos</option>
              <option value="SIN_RESULTADO">Sin resultado</option>
              <option value="BAJA_CONFIANZA">Baja confianza</option>
              <option value="INCORRECTO">Incorrecto</option>
              <option value="CONFUSO">Confuso</option>
              <option value="NO_UTIL">No útil</option>
              <option value="REPETIDA">Repetida</option>
            </select>

            <select id="trainFilterEstado">
              <option value="PENDIENTE">Pendientes</option>
              <option value="EN_REVISION">En revisión</option>
              <option value="RESUELTO">Resueltos</option>
              <option value="DESCARTADO">Descartados</option>
              <option value="">Todos los estados</option>
            </select>

            <input
              type="search"
              id="trainFilterQ"
              placeholder="Buscar pregunta..."
              autocomplete="off"
            >

            <button type="button" class="pats-train-btn is-primary" id="trainReloadBtn">
              Actualizar
            </button>
          </div>
        </section>

       <section class="pats-train-manual" data-train-manual>
  <button type="button" class="pats-train-manual-toggle" data-train-manual-toggle aria-expanded="false">
    <span>
      <strong>Manual de entrenamiento</strong>
      <small>Guía paso a paso para enseñar al CAT BOT</small>
    </span>

    <i class="pats-train-chevron" aria-hidden="true">
      <svg viewBox="0 0 24 24">
        <path d="M7.4 8.8 12 13.4l4.6-4.6L18 10.2l-6 6-6-6 1.4-1.4Z"></path>
      </svg>
    </i>
  </button>

  <div class="pats-train-manual-body" data-train-manual-body hidden>

    <div class="pats-train-manual-block is-intro">
      <h4>Objetivo</h4>
      <p>
        Este panel sirve para convertir preguntas reales del personal en conocimiento útil para el CAT BOT PATS.
        El bot no debe aprender solo sin supervisión: ADMIN o ADMINPATS revisa, corrige y publica.
      </p>
    </div>

    <div class="pats-train-manual-step">
      <span class="pats-train-step-num">1</span>
      <div>
        <h4>Revisa la cola de entrenamiento</h4>
        <p>
          Aquí aparecen preguntas que el bot no pudo responder, respuestas con baja confianza o respuestas marcadas como incorrectas, confusas o no útiles.
        </p>
        <ul>
          <li><b>SIN_RESULTADO:</b> el bot no encontró coincidencia.</li>
          <li><b>BAJA_CONFIANZA:</b> respondió, pero con seguridad baja.</li>
          <li><b>INCORRECTO / CONFUSO / NO_UTIL:</b> el personal calificó mal la respuesta.</li>
        </ul>
      </div>
    </div>

    <div class="pats-train-manual-step">
      <span class="pats-train-step-num">2</span>
      <div>
        <h4>Lee la pregunta detectada</h4>
        <p>
          Revisa cómo escribió realmente el personal. No corrijas de inmediato: primero identifica si es una duda del usuario, una ruta operativa o solo una forma distinta de preguntar.
        </p>
      </div>
    </div>

    <div class="pats-train-manual-step">
      <span class="pats-train-step-num">3</span>
      <div>
        <h4>Elige el tipo de entrenamiento</h4>
        <p>Usa la opción correcta según el caso:</p>
        <div class="pats-train-mini-grid">
          <div>
            <b>Manual</b>
            <span>Para respuestas que se pueden decir al usuario.</span>
          </div>
          <div>
            <b>Operativo</b>
            <span>Para pasos internos, validaciones, módulos y escalamiento.</span>
          </div>
          <div>
            <b>Sinónimo</b>
            <span>Para errores de escritura, jerga o frases equivalentes.</span>
          </div>
        </div>
      </div>
    </div>

    <div class="pats-train-manual-step">
      <span class="pats-train-step-num">4</span>
      <div>
        <h4>Cuándo crear una respuesta Manual</h4>
        <p>
          Usa <b>Crear respuesta Manual</b> cuando la respuesta sirva para orientar al usuario.
        </p>
        <p class="pats-train-example">
          Ejemplo: “¿Qué hacer si mi PATS está vencido?” → respuesta clara para explicar que primero debe validarse vigencia y reactivación.
        </p>
      </div>
    </div>

    <div class="pats-train-manual-step">
      <span class="pats-train-step-num">5</span>
      <div>
        <h4>Cómo llenar una respuesta Manual</h4>
        <ul>
          <li><b>Categoría:</b> tema general, por ejemplo “Vigencia”.</li>
          <li><b>Subcategoría:</b> tema específico, por ejemplo “Pasaporte vencido”.</li>
          <li><b>Intent:</b> clave corta, por ejemplo “falta_pago”.</li>
          <li><b>Pregunta base:</b> pregunta aprobada y clara.</li>
          <li><b>Respuesta:</b> texto que Concierge/Admisión puede decir al usuario.</li>
          <li><b>Keywords:</b> palabras que ayudan a encontrarla.</li>
          <li><b>Prioridad:</b> usa 900 o más si es una respuesta importante.</li>
        </ul>
      </div>
    </div>

    <div class="pats-train-manual-step">
      <span class="pats-train-step-num">6</span>
      <div>
        <h4>Cuándo crear una respuesta Operativa</h4>
        <p>
          Usa <b>Crear respuesta Operativa</b> cuando el personal necesita saber qué hacer internamente.
        </p>
        <p class="pats-train-example">
          Ejemplo: “Paciente llega con pasaporte vencido” → validar identidad, consultar PATS Vigencias, revisar comprobante, no aplicar beneficios sin vigencia activa y escalar si hay inconsistencia.
        </p>
      </div>
    </div>

    <div class="pats-train-manual-step">
      <span class="pats-train-step-num">7</span>
      <div>
        <h4>Cómo llenar una respuesta Operativa</h4>
        <ul>
          <li><b>Proceso:</b> nombre del flujo interno.</li>
          <li><b>Fase:</b> etapa de atención.</li>
          <li><b>Tipo de caso:</b> clave operativa, por ejemplo “PASAPORTE_VENCIDO”.</li>
          <li><b>Respuesta sugerida:</b> frase segura para el usuario.</li>
          <li><b>Pasos operativos:</b> lista numerada de acciones.</li>
          <li><b>Validaciones:</b> qué debe revisar el personal antes de confirmar.</li>
          <li><b>Módulos:</b> PATS Vigencias, PATS MONEY, LEAD+, PATS DROP, etc.</li>
          <li><b>Cuándo escalar:</b> cuándo pasa a ADMINPATS o soporte.</li>
          <li><b>Errores a evitar:</b> qué nunca debe prometerse o hacerse.</li>
        </ul>
      </div>
    </div>

    <div class="pats-train-manual-step">
      <span class="pats-train-step-num">8</span>
      <div>
        <h4>Cuándo guardar un sinónimo</h4>
        <p>
          Usa <b>Guardar sinónimo</b> cuando el bot ya sabe la respuesta, pero no entiende cómo la escribió el personal.
        </p>
        <p class="pats-train-example">
          Ejemplo: “pasaporte venido” debe entenderse como “pasaporte vencido”.
        </p>
      </div>
    </div>

    <div class="pats-train-manual-step">
      <span class="pats-train-step-num">9</span>
      <div>
        <h4>Verifica que aprendió</h4>
        <p>
          Después de publicar una respuesta o guardar un sinónimo, vuelve al CAT BOT y pregunta exactamente lo mismo.
        </p>
        <ul>
          <li>Si responde con <b>MANUAL_PATS</b>, aprendió como manual.</li>
          <li>Si responde con <b>OPERATIVO_PATS</b>, aprendió como operativo.</li>
          <li>Si ya no entra como <b>SIN_RESULTADO</b>, la mejora funcionó.</li>
        </ul>
      </div>
    </div>

    <div class="pats-train-manual-block is-warning">
      <h4>Reglas críticas</h4>
      <ul>
        <li>No publiques información no validada.</li>
        <li>No confirmes precios de memoria.</li>
        <li>No indiques que PATS es seguro médico.</li>
        <li>No prometas beneficios sin validar vigencia.</li>
        <li>No publiques respuestas ambiguas o incompletas.</li>
      </ul>
    </div>

    <div class="pats-train-manual-block is-done">
      <h4>Ciclo correcto</h4>
      <p>
        Pregunta real → revisar → elegir Manual / Operativo / Sinónimo → publicar → probar en CAT BOT → confirmar en logs → marcar resuelto.
      </p>
    </div>

  </div>
</section>

      </aside>

      <!-- =====================================================
           MAIN
      ====================================================== -->
      <main class="pats-train-main">

        <div class="pats-train-main-head">
          <div>
            <span class="pats-train-section-kicker">Cola de aprendizaje</span>
            <h2>Cola de entrenamiento</h2>
            <p>
              Preguntas reales detectadas por el CAT BOT PATS.
              Aprueba solo información validada para evitar respuestas incorrectas.
            </p>
          </div>
        </div>

        <div class="pats-train-summary" id="trainSummary">
          <div class="pats-train-kpi">
            <strong>0</strong>
            <span>Pendientes</span>
          </div>

          <div class="pats-train-kpi">
            <strong>0</strong>
            <span>Sin resultado</span>
          </div>

          <div class="pats-train-kpi">
            <strong>0</strong>
            <span>Baja confianza</span>
          </div>

          <div class="pats-train-kpi">
            <strong>0</strong>
            <span>Feedback negativo</span>
          </div>

          <div class="pats-train-kpi">
            <strong>0</strong>
            <span>Repetidas</span>
          </div>
        </div>

        <div class="pats-train-list" id="trainList">
          <div class="pats-train-empty">
            Cargando cola de entrenamiento...
          </div>
        </div>

      </main>

    </div>

  </section>

</content>

<div class="pats-train-toast" id="trainToast" aria-live="polite"></div>

<script>
window.PATS_TRAINING_CONFIG = {
  listEndpoint: 'endpoints/catbot_training_list.php',
  role: <?= json_encode($adminrol, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
  user: <?= json_encode($userLog, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
  region: <?= json_encode($userRegion, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
  unidad: <?= json_encode($userUnidad, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
};
</script>

<script src="js/catbot_training.js?v=<?= $ver ?>"></script>

</body>
</html>