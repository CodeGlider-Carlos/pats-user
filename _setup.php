<?php

$pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$APP = 'pats_db';
$MODEL = 'dcommx1_ezsystem_modelpats';

// Mimic: PatsAcceso(id_pasaporte=1) -> pats_pasaportes.code_pasaporte
$code = $pdo->query("SELECT code_pasaporte FROM `$APP`.`pats_pasaportes` WHERE id_pasaporte=1")->fetchColumn();
echo 'user code_pasaporte = '.var_export($code, true)."\n";

// Mimic TarjetaMisional::pendientes(code)
$st = $pdo->prepare("SELECT id, modelo, activo, reviewed FROM `$MODEL`.`tarjetas_misional`
                     WHERE code_pasaporte=? AND activo=0 AND reviewed IS NULL ORDER BY id DESC");
$st->execute([$code]);
$rows = $st->fetchAll(PDO::FETCH_ASSOC);
echo 'pending tarjetas: '.count($rows)."\n";
foreach ($rows as $r) {
    echo "  id={$r['id']}  activo={$r['activo']}  reviewed=".var_export($r['reviewed'], true)."  modelo=\"{$r['modelo']}\"\n";
}

echo count($rows) === 1 ? "OK -> modal will show (HOSPITAL variant)\n" : "CHECK -> unexpected count\n";
