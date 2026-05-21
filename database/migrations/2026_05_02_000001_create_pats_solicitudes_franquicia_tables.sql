-- ============================================================
-- Migración: tablas para Solicitudes de Franquicia
-- Ejecutar en MySQL. Compatible con el módulo distribuidor.
-- ============================================================

-- 1. Solicitudes de franquicia
CREATE TABLE IF NOT EXISTS `pats_solicitudes_franquicia` (
  `id_solicitud`              int(11)                     NOT NULL AUTO_INCREMENT,
  `id_franquicia`             int(11)                     NOT NULL DEFAULT '0',
  `id_gestor`                 bigint(20) UNSIGNED         NOT NULL DEFAULT '0',
  `id_franquicia_generada`    int(11)                     DEFAULT NULL,
  `user_solicita`             int(11)                     DEFAULT NULL,
  `user_valida`               int(11)                     DEFAULT NULL,
  `user_autoriza`             int(11)                     DEFAULT NULL,
  `pais`                      varchar(120)                NOT NULL,
  `region`                    varchar(120)                NOT NULL,
  `zona`                      varchar(120)                DEFAULT NULL,
  `unidad`                    varchar(120)                DEFAULT NULL,
  `nombre`                    varchar(180)                NOT NULL,
  `tipo_persona`              enum('FISICA','MORAL')      NOT NULL DEFAULT 'FISICA',
  `razon_social`              varchar(220)                DEFAULT NULL,
  `rfc`                       varchar(30)                 DEFAULT NULL,
  `telefono`                  varchar(20)                 NOT NULL,
  `correo`                    varchar(180)                NOT NULL,
  `direccion`                 text                        DEFAULT NULL,
  `banco`                     varchar(150)                DEFAULT NULL,
  `numero_cuenta`             varchar(60)                 DEFAULT NULL,
  `clabe`                     varchar(30)                 DEFAULT NULL,
  `titular_cuenta`            varchar(180)                DEFAULT NULL,
  `modalidad_pago`            enum('CONTADO','DIFERIDO')  NOT NULL DEFAULT 'CONTADO',
  `valor_total`               decimal(12,2)               NOT NULL DEFAULT '0.00',
  `enganche`                  decimal(12,2)               NOT NULL DEFAULT '0.00',
  `saldo_financiado`          decimal(12,2)               NOT NULL DEFAULT '0.00',
  `plazo_meses`               smallint(5) UNSIGNED        NOT NULL DEFAULT '0',
  `periodicidad`              enum('MENSUAL','QUINCENAL','SEMANAL','UNICA') NOT NULL DEFAULT 'MENSUAL',
  `fecha_inicio`              date                        DEFAULT NULL,
  `fecha_primer_vencimiento`  date                        DEFAULT NULL,
  `esquema_pagos_json`        longtext                    DEFAULT NULL,
  `stripe_payment_intent_id`  varchar(120)                DEFAULT NULL,
  `stripe_payment_status`     varchar(30)                 DEFAULT NULL,
  `contrato_admin_path`       varchar(255)                DEFAULT NULL,
  `contrato_firmado_path`     varchar(255)                DEFAULT NULL,
  `estatus`                   varchar(50)                 NOT NULL DEFAULT 'BORRADOR',
  `motivo_rechazo`            text                        DEFAULT NULL,
  `observaciones_admin`       text                        DEFAULT NULL,
  `observaciones_franquicia`  text                        DEFAULT NULL,
  `fecha_envio_contrato`      datetime                    DEFAULT NULL,
  `fecha_carga_firmado`       datetime                    DEFAULT NULL,
  `fecha_autorizacion`        datetime                    DEFAULT NULL,
  `fecha_conversion_alta`     datetime                    DEFAULT NULL,
  `activo`                    tinyint(1)                  NOT NULL DEFAULT '1',
  `created_at`                datetime                    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`                datetime                    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_solicitud`),
  UNIQUE KEY `uq_psf_stripe_intent` (`stripe_payment_intent_id`),
  KEY `idx_psf_franquicia`           (`id_franquicia`),
  KEY `idx_psf_estatus`              (`estatus`),
  KEY `idx_psf_correo`               (`correo`),
  KEY `idx_psf_user_solicita`        (`user_solicita`),
  KEY `idx_psf_correo_estatus`       (`correo`, `estatus`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Documentos de solicitudes de franquicia
CREATE TABLE IF NOT EXISTS `pats_solicitudes_franquicia_documentos` (
  `id_documento_solicitud`    int(11)       NOT NULL AUTO_INCREMENT,
  `id_solicitud`              int(11)       NOT NULL,
  `tipo_documento`            varchar(60)   NOT NULL,
  `archivo_path`              varchar(255)  NOT NULL,
  `archivo_nombre_original`   varchar(255)  NOT NULL,
  `mime_type`                 varchar(120)  DEFAULT NULL,
  `size_kb`                   int(11)       NOT NULL DEFAULT '0',
  `vigente`                   tinyint(1)    NOT NULL DEFAULT '1',
  `observaciones`             text          DEFAULT NULL,
  `user_alta`                 int(11)       DEFAULT NULL,
  `created_at`                datetime      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`                datetime      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_documento_solicitud`),
  KEY `idx_psfd_solicitud` (`id_solicitud`),
  KEY `idx_psfd_tipo`      (`tipo_documento`),
  KEY `idx_psfd_vigente`   (`vigente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Historial de solicitudes de franquicia
CREATE TABLE IF NOT EXISTS `pats_solicitudes_franquicia_historial` (
  `id_historial_solicitud`    int(11)       NOT NULL AUTO_INCREMENT,
  `id_solicitud`              int(11)       NOT NULL,
  `evento_tipo`               varchar(60)   NOT NULL,
  `estatus_anterior`          varchar(50)   DEFAULT NULL,
  `estatus_nuevo`             varchar(50)   DEFAULT NULL,
  `payload_json`              longtext      DEFAULT NULL,
  `user_evento`               int(11)       DEFAULT NULL,
  `fecha_evento`              datetime      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at`                datetime      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_historial_solicitud`),
  KEY `idx_psfh_solicitud` (`id_solicitud`),
  KEY `idx_psfh_evento`    (`evento_tipo`),
  KEY `idx_psfh_fecha`     (`fecha_evento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Preview biométrico de franquicia
CREATE TABLE IF NOT EXISTS `pats_preview_franq` (
  `id`            bigint(20) UNSIGNED   NOT NULL AUTO_INCREMENT,
  `id_solicitud`  bigint(20) UNSIGNED   NOT NULL,
  `selfie_path`   varchar(500)          DEFAULT NULL,
  `contrato_path` varchar(500)          DEFAULT NULL,
  `firma_path`    varchar(500)          DEFAULT NULL,
  `selfie_mime`   varchar(60)           DEFAULT NULL,
  `contrato_mime` varchar(60)           DEFAULT NULL,
  `firma_mime`    varchar(60)           DEFAULT NULL,
  `selfie_kb`     int(10) UNSIGNED      DEFAULT NULL,
  `contrato_kb`   int(10) UNSIGNED      DEFAULT NULL,
  `firma_kb`      int(10) UNSIGNED      DEFAULT NULL,
  `created_at`    timestamp             NULL DEFAULT NULL,
  `updated_at`    timestamp             NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Precio de franquicia en el catálogo (si no existe)
INSERT IGNORE INTO `pats_cat_precios` (`tipo`, `precio`, `activo`)
SELECT 'franquicia', 50000.00, 1
WHERE NOT EXISTS (
  SELECT 1 FROM `pats_cat_precios`
  WHERE LOWER(TRIM(tipo)) = 'franquicia' AND activo = 1
);