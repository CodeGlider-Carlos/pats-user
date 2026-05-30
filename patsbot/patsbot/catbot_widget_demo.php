<?php
/*
Archivo: ez/patsbot/catbot_widget_demo.php
Módulo: PATS / CAT BOT
Propósito: Pantalla de prueba para validar el widget responsive reutilizable del CAT BOT PATS.
Responsabilidad: Mostrar el componente en modo usuario final simple.
Conexiones: components/catbot_widget.php, css/catbot_widget.css, js/catbot_widget.js y endpoints reales.
Tipo: Específico de prueba / integración.
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

$adminrol = strtoupper(trim($_SESSION['rol'] ?? ''));

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

require_once __DIR__ . '/components/catbot_widget.php';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Widget CAT BOT PATS</title>

  <link rel="icon" type="image/x-icon" href="../../img/ez.ico" />

  <link rel="stylesheet" href="../../css/index.css?v=<?= $ver ?>">
  <link rel="stylesheet" href="../../css/gloval_responsive.css?v=<?= $ver ?>">
  <link rel="stylesheet" href="css/catbot_widget.css?v=<?= $ver ?>">

  <style>
    .pats-widget-demo-page {
      padding: clamp(12px, 3vw, 28px);
      background: #edf2f8;
      min-height: calc(100vh - 90px);
    }

    .pats-widget-demo-wrap {
      max-width: 820px;
      margin: 0 auto;
    }

    .pats-widget-demo-title {
      margin: 0 0 10px;
      color: #112a62;
      font-size: clamp(26px, 5vw, 38px);
      line-height: 1.08;
      font-weight: 950;
    }

    .pats-widget-demo-note {
      max-width: 680px;
      margin: 0 0 18px;
      color: #60708f;
      font-size: 15px;
      line-height: 1.5;
      font-weight: 650;
    }
  </style>
</head>

<body>
<?php require_once '../../loglog/contador.php'; ?>

<div id="cabecera">
  <div class="bienvenido">Bienvenido, <?= $_SESSION['nombre'] ?></div>
  <?php require_once '../nav/noti_user.php'; ?>
</div>

<?php require_once '../nav/nav_mod.php'; ?>

<content class="back_content">
  <section class="pats-widget-demo-page">
    <div class="pats-widget-demo-wrap">
     

      <?php
        pats_catbot_widget([
          'id' => 'catbotWidgetUsuarioFinal',
          'modo' => 'manual',
          'audiencia' => 'usuario',
          'contexto' => 'widget_usuario_final',
          'titulo' => 'Asistente PATS',
          'subtitulo' => 'Resuelve dudas rápidas sobre tu pasaporte.',
          'endpoint_base' => 'endpoints',
          'compacto' => true,
          'mostrar_contexto' => false,
          'mostrar_tabs' => false,
          'mostrar_feedback' => true,
          'mostrar_quick_prompts' => true
        ]);
      ?>
    </div>
  </section>
</content>

<script src="js/catbot_widget.js?v=<?= $ver ?>"></script>
</body>
</html>
