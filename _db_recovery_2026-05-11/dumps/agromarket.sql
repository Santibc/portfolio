-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: agromarket
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `agromarket`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `agromarket` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `agromarket`;

--
-- Table structure for table `actividades_prospecto`
--

DROP TABLE IF EXISTS `actividades_prospecto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `actividades_prospecto` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `prospecto_id` bigint(20) unsigned NOT NULL,
  `usuario_id` bigint(20) unsigned NOT NULL,
  `tipo_actividad` enum('llamada','email','reunion','whatsapp','visita','nota','otro') NOT NULL,
  `asunto` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_actividad` date NOT NULL,
  `hora_actividad` time DEFAULT NULL,
  `resultado` enum('exitoso','sin_respuesta','reagendar','no_interesado','pendiente') DEFAULT NULL,
  `fecha_seguimiento` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `actividades_prospecto_prospecto_id_index` (`prospecto_id`),
  KEY `actividades_prospecto_usuario_id_index` (`usuario_id`),
  KEY `actividades_prospecto_tipo_actividad_index` (`tipo_actividad`),
  KEY `actividades_prospecto_fecha_actividad_index` (`fecha_actividad`),
  CONSTRAINT `actividades_prospecto_prospecto_id_foreign` FOREIGN KEY (`prospecto_id`) REFERENCES `prospectos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `actividades_prospecto_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `actividades_prospecto`
--

LOCK TABLES `actividades_prospecto` WRITE;
/*!40000 ALTER TABLE `actividades_prospecto` DISABLE KEYS */;
/*!40000 ALTER TABLE `actividades_prospecto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `actualizaciones_proyecto`
--

DROP TABLE IF EXISTS `actualizaciones_proyecto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `actualizaciones_proyecto` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `proyecto_id` bigint(20) unsigned NOT NULL,
  `autor_id` bigint(20) unsigned NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `contenido` text NOT NULL,
  `tipo` enum('informativo','hito','alerta','financiero') NOT NULL DEFAULT 'informativo',
  `visible_inversores` tinyint(1) NOT NULL DEFAULT 1,
  `publicado_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `actualizaciones_proyecto_autor_id_foreign` (`autor_id`),
  KEY `actualizaciones_proyecto_proyecto_id_index` (`proyecto_id`),
  KEY `actualizaciones_proyecto_proyecto_id_publicado_at_index` (`proyecto_id`,`publicado_at`),
  CONSTRAINT `actualizaciones_proyecto_autor_id_foreign` FOREIGN KEY (`autor_id`) REFERENCES `users` (`id`),
  CONSTRAINT `actualizaciones_proyecto_proyecto_id_foreign` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `actualizaciones_proyecto`
--

LOCK TABLES `actualizaciones_proyecto` WRITE;
/*!40000 ALTER TABLE `actualizaciones_proyecto` DISABLE KEYS */;
/*!40000 ALTER TABLE `actualizaciones_proyecto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `billeteras`
--

DROP TABLE IF EXISTS `billeteras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `billeteras` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` bigint(20) unsigned NOT NULL,
  `saldo_disponible` decimal(15,2) NOT NULL DEFAULT 0.00,
  `saldo_bloqueado` decimal(15,2) NOT NULL DEFAULT 0.00,
  `saldo_invertido` decimal(15,2) NOT NULL DEFAULT 0.00,
  `retornos_acumulados` decimal(15,2) NOT NULL DEFAULT 0.00,
  `dividendos_pendientes` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `billeteras_usuario_id_unique` (`usuario_id`),
  KEY `billeteras_usuario_id_index` (`usuario_id`),
  CONSTRAINT `billeteras_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `billeteras`
--

LOCK TABLES `billeteras` WRITE;
/*!40000 ALTER TABLE `billeteras` DISABLE KEYS */;
INSERT INTO `billeteras` VALUES (1,4,4000008.22,0.00,6001000.00,400008.22,670912.34,'2025-12-02 21:03:07','2025-12-22 02:21:23'),(2,5,0.00,0.00,0.00,0.00,0.00,'2025-12-20 22:57:02','2025-12-20 22:57:02'),(3,11,0.00,0.00,0.00,0.00,0.00,'2025-12-20 22:57:02','2025-12-20 22:57:02');
/*!40000 ALTER TABLE `billeteras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorias_proyecto`
--

DROP TABLE IF EXISTS `categorias_proyecto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categorias_proyecto` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `duracion_minima_meses` int(11) DEFAULT NULL,
  `duracion_maxima_meses` int(11) DEFAULT NULL,
  `roi_minimo` decimal(5,2) DEFAULT NULL,
  `roi_maximo` decimal(5,2) DEFAULT NULL,
  `inversion_minima` decimal(15,2) NOT NULL DEFAULT 0.00,
  `inversion_maxima` decimal(15,2) DEFAULT NULL,
  `permite_retiro_anticipado` tinyint(1) NOT NULL DEFAULT 0,
  `permite_trading` tinyint(1) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `orden` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categorias_proyecto_codigo_unique` (`codigo`),
  KEY `categorias_proyecto_codigo_index` (`codigo`),
  KEY `categorias_proyecto_activo_index` (`activo`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias_proyecto`
--

LOCK TABLES `categorias_proyecto` WRITE;
/*!40000 ALTER TABLE `categorias_proyecto` DISABLE KEYS */;
INSERT INTO `categorias_proyecto` VALUES (1,'STAKING','Staking Agrícola','Inversión a plazo fijo con retornos garantizados. Capital bloqueado durante el período acordado.',3,24,8.00,15.00,100000.00,50000000.00,0,0,1,1,'2025-12-03 02:34:32','2025-12-16 02:58:09'),(2,'TRADING','Trading de Inversiones','Posibilidad de comprar y vender inversiones en el mercado secundario.',6,12,10.00,25.00,500000.00,100000000.00,1,1,1,2,'2025-12-03 02:34:32','2025-12-16 02:58:09'),(3,'EAR','Retiro Anticipado con Penalización','Permite retiro anticipado aplicando penalizaciones según el tiempo transcurrido.',6,18,12.00,20.00,200000.00,75000000.00,1,0,1,3,'2025-12-03 02:34:32','2025-12-16 02:58:09'),(4,'FUTUROS','Contratos a Futuro','Inversión en proyectos con cosecha programada. Retornos al finalizar el ciclo agrícola.',4,12,15.00,35.00,1000000.00,150000000.00,0,1,1,4,'2025-12-16 02:58:09','2025-12-16 02:58:09'),(5,'CROSS_FUND','Fondo Diversificado','Inversión distribuida en múltiples proyectos para minimizar riesgos.',6,18,10.00,18.00,300000.00,100000000.00,1,0,1,5,'2025-12-16 02:58:09','2025-12-16 02:58:09'),(6,'FARMING','Farming - Asociaciones Agrícolas','Inversión en asociaciones y cooperativas agrícolas para exportación de commodities (café, cacao). Retornos a partir de 24 meses con ganancias trimestrales por 15-20 años.',24,240,35.00,50.00,5000000.00,500000000.00,0,0,1,6,'2025-12-16 02:58:09','2025-12-16 02:58:09');
/*!40000 ALTER TABLE `categorias_proyecto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `compras_cross_fund`
--

DROP TABLE IF EXISTS `compras_cross_fund`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `compras_cross_fund` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codigo_compra` varchar(50) NOT NULL,
  `usuario_id` bigint(20) unsigned NOT NULL,
  `paquete_id` bigint(20) unsigned NOT NULL,
  `monto_total` decimal(15,2) NOT NULL,
  `roi_ponderado` decimal(5,2) NOT NULL,
  `duracion_promedio` int(11) NOT NULL,
  `fecha_compra` date NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `estado` enum('activa','vencida','cancelada') NOT NULL DEFAULT 'activa',
  `contrato_id` bigint(20) unsigned DEFAULT NULL,
  `contrato_aceptado` tinyint(1) NOT NULL DEFAULT 0,
  `contrato_aceptado_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `compras_cross_fund_codigo_compra_unique` (`codigo_compra`),
  KEY `compras_cross_fund_contrato_id_foreign` (`contrato_id`),
  KEY `compras_cross_fund_codigo_compra_index` (`codigo_compra`),
  KEY `compras_cross_fund_usuario_id_index` (`usuario_id`),
  KEY `compras_cross_fund_paquete_id_index` (`paquete_id`),
  KEY `compras_cross_fund_estado_index` (`estado`),
  CONSTRAINT `compras_cross_fund_contrato_id_foreign` FOREIGN KEY (`contrato_id`) REFERENCES `plantillas_contrato` (`id`) ON DELETE SET NULL,
  CONSTRAINT `compras_cross_fund_paquete_id_foreign` FOREIGN KEY (`paquete_id`) REFERENCES `paquetes_cross_fund` (`id`),
  CONSTRAINT `compras_cross_fund_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compras_cross_fund`
--

LOCK TABLES `compras_cross_fund` WRITE;
/*!40000 ALTER TABLE `compras_cross_fund` DISABLE KEYS */;
/*!40000 ALTER TABLE `compras_cross_fund` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configuraciones_sistema`
--

DROP TABLE IF EXISTS `configuraciones_sistema`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configuraciones_sistema` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `clave` varchar(100) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `valor` text DEFAULT NULL,
  `tipo` enum('texto','numero','decimal','booleano','fecha','json','archivo') NOT NULL,
  `grupo` varchar(100) NOT NULL DEFAULT 'general',
  `descripcion` text DEFAULT NULL,
  `editable` tinyint(1) NOT NULL DEFAULT 1,
  `modificado_por` bigint(20) unsigned DEFAULT NULL,
  `modificado_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `configuraciones_sistema_clave_unique` (`clave`),
  KEY `configuraciones_sistema_modificado_por_foreign` (`modificado_por`),
  KEY `configuraciones_sistema_clave_index` (`clave`),
  KEY `configuraciones_sistema_grupo_index` (`grupo`),
  CONSTRAINT `configuraciones_sistema_modificado_por_foreign` FOREIGN KEY (`modificado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configuraciones_sistema`
--

LOCK TABLES `configuraciones_sistema` WRITE;
/*!40000 ALTER TABLE `configuraciones_sistema` DISABLE KEYS */;
/*!40000 ALTER TABLE `configuraciones_sistema` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cuentas_bancarias`
--

DROP TABLE IF EXISTS `cuentas_bancarias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cuentas_bancarias` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` bigint(20) unsigned NOT NULL,
  `banco` varchar(100) NOT NULL,
  `tipo_cuenta` enum('ahorros','corriente','nequi','daviplata') NOT NULL,
  `numero_cuenta` text NOT NULL,
  `titular` varchar(200) NOT NULL,
  `documento_titular` varchar(50) NOT NULL,
  `es_principal` tinyint(1) NOT NULL DEFAULT 0,
  `verificada` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_verificacion` date DEFAULT NULL,
  `verificada_por` bigint(20) unsigned DEFAULT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cuentas_bancarias_verificada_por_foreign` (`verificada_por`),
  KEY `cuentas_bancarias_usuario_id_index` (`usuario_id`),
  KEY `cuentas_bancarias_es_principal_index` (`es_principal`),
  KEY `cuentas_bancarias_verificada_index` (`verificada`),
  CONSTRAINT `cuentas_bancarias_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cuentas_bancarias_verificada_por_foreign` FOREIGN KEY (`verificada_por`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cuentas_bancarias`
--

LOCK TABLES `cuentas_bancarias` WRITE;
/*!40000 ALTER TABLE `cuentas_bancarias` DISABLE KEYS */;
/*!40000 ALTER TABLE `cuentas_bancarias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `depositos`
--

DROP TABLE IF EXISTS `depositos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `depositos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codigo_deposito` varchar(50) NOT NULL,
  `usuario_id` bigint(20) unsigned NOT NULL,
  `monto` decimal(15,2) NOT NULL,
  `metodo_pago` enum('transferencia_bancaria','pse','tarjeta_credito','nequi','daviplata','efectivo','otro') NOT NULL,
  `referencia_pago` varchar(200) DEFAULT NULL,
  `comprobante` varchar(500) DEFAULT NULL,
  `fecha_deposito` date NOT NULL,
  `estado` enum('pendiente','verificado','rechazado') NOT NULL DEFAULT 'pendiente',
  `verificado_por` bigint(20) unsigned DEFAULT NULL,
  `verificado_at` timestamp NULL DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `depositos_codigo_deposito_unique` (`codigo_deposito`),
  KEY `depositos_verificado_por_foreign` (`verificado_por`),
  KEY `depositos_codigo_deposito_index` (`codigo_deposito`),
  KEY `depositos_usuario_id_index` (`usuario_id`),
  KEY `depositos_estado_index` (`estado`),
  CONSTRAINT `depositos_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`),
  CONSTRAINT `depositos_verificado_por_foreign` FOREIGN KEY (`verificado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `depositos`
--

LOCK TABLES `depositos` WRITE;
/*!40000 ALTER TABLE `depositos` DISABLE KEYS */;
INSERT INTO `depositos` VALUES (1,'DEP-2025-00001',4,5000000.00,'transferencia_bancaria','123456789',NULL,'2025-10-03','verificado',1,'2025-10-04 02:34:32',NULL,'2025-12-03 02:34:32','2025-12-03 02:34:32'),(2,'DEP-2025-00002',4,3000000.00,'pse','PSE-987654321',NULL,'2025-11-02','verificado',1,'2025-11-03 02:35:14',NULL,'2025-12-03 02:35:14','2025-12-03 02:35:14');
/*!40000 ALTER TABLE `depositos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dividendos`
--

DROP TABLE IF EXISTS `dividendos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dividendos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codigo_dividendo` varchar(50) NOT NULL,
  `inversion_id` bigint(20) unsigned NOT NULL,
  `proyecto_id` bigint(20) unsigned NOT NULL,
  `usuario_id` bigint(20) unsigned NOT NULL,
  `numero_periodo` int(11) NOT NULL,
  `monto` decimal(15,2) NOT NULL,
  `fecha_programada` date NOT NULL,
  `fecha_pagada` date DEFAULT NULL,
  `estado` enum('programado','pagado','atrasado','cancelado') NOT NULL DEFAULT 'programado',
  `pagado_por` bigint(20) unsigned DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dividendos_codigo_dividendo_unique` (`codigo_dividendo`),
  KEY `dividendos_proyecto_id_foreign` (`proyecto_id`),
  KEY `dividendos_pagado_por_foreign` (`pagado_por`),
  KEY `dividendos_codigo_dividendo_index` (`codigo_dividendo`),
  KEY `dividendos_inversion_id_index` (`inversion_id`),
  KEY `dividendos_usuario_id_index` (`usuario_id`),
  KEY `dividendos_estado_index` (`estado`),
  KEY `dividendos_fecha_programada_index` (`fecha_programada`),
  CONSTRAINT `dividendos_inversion_id_foreign` FOREIGN KEY (`inversion_id`) REFERENCES `inversiones` (`id`),
  CONSTRAINT `dividendos_pagado_por_foreign` FOREIGN KEY (`pagado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `dividendos_proyecto_id_foreign` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`),
  CONSTRAINT `dividendos_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dividendos`
--

LOCK TABLES `dividendos` WRITE;
/*!40000 ALTER TABLE `dividendos` DISABLE KEYS */;
INSERT INTO `dividendos` VALUES (1,'DIV-2025-00001',1,1,4,1,140000.00,'2025-11-27','2025-11-27','pagado',1,NULL,'2025-12-03 02:34:32','2025-12-03 02:34:32'),(2,'DIV-2025-00002',1,1,4,2,140000.00,'2026-02-25',NULL,'programado',NULL,NULL,'2025-12-03 02:34:32','2025-12-03 02:34:32'),(3,'DIV-2025--00001',3,5,4,1,8.22,'2026-01-23','2025-12-21','pagado',1,NULL,'2025-12-22 00:32:18','2025-12-22 00:47:00'),(4,'DIV-2025-000000',3,5,4,2,8.22,'2026-02-22',NULL,'programado',NULL,NULL,'2025-12-22 00:32:18','2025-12-22 00:32:18'),(5,'DIV-2025-000001',3,5,4,3,8.22,'2026-03-24',NULL,'programado',NULL,NULL,'2025-12-22 00:32:18','2025-12-22 00:32:18'),(6,'DIV-2025-000002',3,5,4,4,8.22,'2026-04-23',NULL,'programado',NULL,NULL,'2025-12-22 00:32:18','2025-12-22 00:32:18'),(7,'DIV-2025-000003',3,5,4,5,8.22,'2026-05-23',NULL,'programado',NULL,NULL,'2025-12-22 00:32:18','2025-12-22 00:32:18'),(8,'DIV-2025-000004',3,5,4,6,8.22,'2026-06-22',NULL,'programado',NULL,NULL,'2025-12-22 00:32:18','2025-12-22 00:32:18'),(9,'DIV-2025-000005',3,5,4,7,8.22,'2026-07-22',NULL,'programado',NULL,NULL,'2025-12-22 00:32:18','2025-12-22 00:32:18'),(10,'DIV-2025-000006',3,5,4,8,8.22,'2026-08-21',NULL,'programado',NULL,NULL,'2025-12-22 00:32:18','2025-12-22 00:32:18'),(11,'DIV-2025-000007',3,5,4,9,8.22,'2026-09-20',NULL,'programado',NULL,NULL,'2025-12-22 00:32:18','2025-12-22 00:32:18'),(12,'DIV-2025-000008',3,5,4,10,8.22,'2026-10-20',NULL,'programado',NULL,NULL,'2025-12-22 00:32:18','2025-12-22 00:32:18'),(13,'DIV-2025-000009',3,5,4,11,8.22,'2026-11-19',NULL,'programado',NULL,NULL,'2025-12-22 00:32:18','2025-12-22 00:32:18'),(14,'DIV-2025-000010',3,5,4,12,8.22,'2026-12-19',NULL,'programado',NULL,NULL,'2025-12-22 00:32:18','2025-12-22 00:32:18'),(15,'DIV-2025-000011',2,2,4,1,105205.48,'2026-02-05',NULL,'programado',NULL,NULL,'2025-12-22 00:33:05','2025-12-22 00:33:05'),(16,'DIV-2025-000012',2,2,4,2,105205.48,'2026-06-05',NULL,'programado',NULL,NULL,'2025-12-22 00:33:05','2025-12-22 00:33:05'),(17,'DIV-2025-000013',2,2,4,3,105205.48,'2026-10-03',NULL,'programado',NULL,NULL,'2025-12-22 00:33:05','2025-12-22 00:33:05'),(18,'DIV-2025-000014',2,2,4,4,105205.48,'2027-01-31',NULL,'programado',NULL,NULL,'2025-12-22 00:33:05','2025-12-22 00:33:05');
/*!40000 ALTER TABLE `dividendos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documentos_kyc`
--

DROP TABLE IF EXISTS `documentos_kyc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documentos_kyc` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` bigint(20) unsigned NOT NULL,
  `tipo_documento` enum('cedula_frontal','cedula_trasera','rut','camara_comercio','extracto_bancario','prueba_domicilio','selfie','otro') NOT NULL,
  `nombre_archivo` varchar(500) NOT NULL,
  `ruta_archivo` varchar(1000) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `tamanio_kb` int(11) NOT NULL,
  `fecha_subida` date NOT NULL,
  `estado` enum('pendiente_revision','aprobado','rechazado','requiere_reemplazo') NOT NULL DEFAULT 'pendiente_revision',
  `revisado_por` bigint(20) unsigned DEFAULT NULL,
  `revisado_at` timestamp NULL DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documentos_kyc_revisado_por_foreign` (`revisado_por`),
  KEY `documentos_kyc_usuario_id_index` (`usuario_id`),
  KEY `documentos_kyc_tipo_documento_index` (`tipo_documento`),
  KEY `documentos_kyc_estado_index` (`estado`),
  CONSTRAINT `documentos_kyc_revisado_por_foreign` FOREIGN KEY (`revisado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `documentos_kyc_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documentos_kyc`
--

LOCK TABLES `documentos_kyc` WRITE;
/*!40000 ALTER TABLE `documentos_kyc` DISABLE KEYS */;
INSERT INTO `documentos_kyc` VALUES (1,4,'cedula_frontal','cedula_frontal.jpg','kyc/inversionista/cedula_frontal.jpg','image/jpeg',245,'2025-11-29','pendiente_revision',NULL,NULL,NULL,'2025-12-03 02:35:14','2025-12-03 02:35:14'),(2,11,'cedula_frontal','1.pdf','uploads/kyc/11/1766187266_documento_frente.pdf','application/pdf',14,'2025-12-19','aprobado',1,'2025-12-19 23:45:07','Mal','2025-12-19 23:34:26','2025-12-19 23:45:07'),(3,11,'cedula_trasera','1.pdf','uploads/kyc/11/1766187266_documento_reverso.pdf','application/pdf',14,'2025-12-19','aprobado',1,'2025-12-19 23:45:07','Mal','2025-12-19 23:34:26','2025-12-19 23:45:07'),(4,11,'selfie','Captura.PNG','uploads/kyc/11/1766187266_selfie.PNG','image/png',1269,'2025-12-19','aprobado',1,'2025-12-19 23:45:07','Mal','2025-12-19 23:34:26','2025-12-19 23:45:07'),(5,11,'prueba_domicilio','Captura.PNG','uploads/kyc/11/1766187266_comprobante_domicilio.PNG','image/png',1269,'2025-12-19','aprobado',1,'2025-12-19 23:45:07','Mal','2025-12-19 23:34:26','2025-12-19 23:45:07'),(6,11,'cedula_frontal','1.pdf','uploads/kyc/11/1766187666_documento_frente.pdf','application/pdf',14,'2025-12-19','aprobado',1,'2025-12-19 23:45:07',NULL,'2025-12-19 23:41:06','2025-12-19 23:45:07'),(7,11,'cedula_trasera','1.pdf','uploads/kyc/11/1766187666_documento_reverso.pdf','application/pdf',14,'2025-12-19','aprobado',1,'2025-12-19 23:45:07',NULL,'2025-12-19 23:41:06','2025-12-19 23:45:07'),(8,11,'selfie','Captura.PNG','uploads/kyc/11/1766187666_selfie.PNG','image/png',1269,'2025-12-19','aprobado',1,'2025-12-19 23:45:07',NULL,'2025-12-19 23:41:06','2025-12-19 23:45:07'),(9,11,'prueba_domicilio','Captura.PNG','uploads/kyc/11/1766187666_comprobante_domicilio.PNG','image/png',1269,'2025-12-19','aprobado',1,'2025-12-19 23:45:07',NULL,'2025-12-19 23:41:06','2025-12-19 23:45:07');
/*!40000 ALTER TABLE `documentos_kyc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documentos_proyecto`
--

DROP TABLE IF EXISTS `documentos_proyecto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documentos_proyecto` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `proyecto_id` bigint(20) unsigned NOT NULL,
  `tipo_documento` enum('escritura','certificado_camara','cedula_catastral','plan_cultivo','estudio_suelos','licencia_ambiental','poliza_seguro','contrato_compra','foto_terreno','documento_tenencia','certificado_agricola','certificaciones_asociacion','otro') NOT NULL,
  `nombre_archivo` varchar(255) NOT NULL,
  `ruta_archivo` varchar(500) NOT NULL,
  `tipo_mime` varchar(100) NOT NULL,
  `tamano_bytes` bigint(20) unsigned NOT NULL,
  `descripcion` text DEFAULT NULL,
  `verificado` tinyint(1) NOT NULL DEFAULT 0,
  `verificado_por` bigint(20) unsigned DEFAULT NULL,
  `verificado_at` timestamp NULL DEFAULT NULL,
  `subido_por` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documentos_proyecto_verificado_por_foreign` (`verificado_por`),
  KEY `documentos_proyecto_subido_por_foreign` (`subido_por`),
  KEY `documentos_proyecto_proyecto_id_index` (`proyecto_id`),
  KEY `documentos_proyecto_tipo_documento_index` (`tipo_documento`),
  KEY `documentos_proyecto_verificado_index` (`verificado`),
  CONSTRAINT `documentos_proyecto_proyecto_id_foreign` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documentos_proyecto_subido_por_foreign` FOREIGN KEY (`subido_por`) REFERENCES `users` (`id`),
  CONSTRAINT `documentos_proyecto_verificado_por_foreign` FOREIGN KEY (`verificado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documentos_proyecto`
--

LOCK TABLES `documentos_proyecto` WRITE;
/*!40000 ALTER TABLE `documentos_proyecto` DISABLE KEYS */;
INSERT INTO `documentos_proyecto` VALUES (2,7,'plan_cultivo','1.pdf','uploads/proyectos/7/documentos/plan_cultivo_20251203_163425_xLQPLzCZ.pdf','application/pdf',13755,NULL,0,NULL,NULL,3,'2025-12-03 21:34:25','2025-12-03 21:34:25',NULL),(3,9,'plan_cultivo','1.pdf','uploads/proyectos/9/documentos/plan_cultivo_20251203_172722_sZvBijBc.pdf','application/pdf',13755,NULL,0,NULL,NULL,3,'2025-12-03 22:27:22','2025-12-03 22:27:22',NULL),(4,11,'cedula_catastral','1.pdf','uploads/proyectos/11/documentos/cedula_catastral_20251215_234843_MC4bPwyr.pdf','application/pdf',13755,'sda',0,NULL,NULL,1,'2025-12-16 04:48:43','2025-12-16 04:48:43',NULL),(5,13,'poliza_seguro','1.pdf','uploads/proyectos/13/documentos/poliza_seguro_20251216_151307_WjHgmhUB.pdf','application/pdf',13755,'ok',0,NULL,NULL,9,'2025-12-16 20:13:07','2025-12-16 20:13:07',NULL),(6,14,'certificado_camara','1.pdf','uploads/proyectos/14/documentos/certificado_camara_20251216_172004_9DkUKckv.pdf','application/pdf',13755,NULL,0,NULL,NULL,1,'2025-12-16 22:20:04','2025-12-16 22:20:04',NULL),(7,14,'cedula_catastral','1.pdf','uploads/proyectos/14/documentos/cedula_catastral_20251216_172017_QyIndFsT.pdf','application/pdf',13755,NULL,0,NULL,NULL,1,'2025-12-16 22:20:17','2025-12-16 22:20:17',NULL);
/*!40000 ALTER TABLE `documentos_proyecto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `familia_agricultor`
--

DROP TABLE IF EXISTS `familia_agricultor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `familia_agricultor` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `agricultor_id` bigint(20) unsigned NOT NULL,
  `parentesco` enum('esposa','esposo','hijo','hija','padre','madre','hermano','hermana','otro') NOT NULL DEFAULT 'otro',
  `nombre` varchar(255) NOT NULL,
  `edad` int(11) DEFAULT NULL,
  `nivel_educativo` enum('ninguno','primaria','secundaria','tecnico','profesional','posgrado') DEFAULT NULL,
  `estudia_actualmente` enum('si','no','estudio_aplazado') DEFAULT NULL,
  `trabaja_en_cultivo` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `familia_agricultor_agricultor_id_index` (`agricultor_id`),
  CONSTRAINT `familia_agricultor_agricultor_id_foreign` FOREIGN KEY (`agricultor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `familia_agricultor`
--

LOCK TABLES `familia_agricultor` WRITE;
/*!40000 ALTER TABLE `familia_agricultor` DISABLE KEYS */;
INSERT INTO `familia_agricultor` VALUES (9,9,'esposa','Maria',25,'primaria','si',1,'2025-12-16 22:18:15','2025-12-16 22:18:15'),(10,9,'hijo','Raul',12,'primaria','si',0,'2025-12-16 22:18:15','2025-12-16 22:18:15');
/*!40000 ALTER TABLE `familia_agricultor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `imagenes_proyecto`
--

DROP TABLE IF EXISTS `imagenes_proyecto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `imagenes_proyecto` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `proyecto_id` bigint(20) unsigned NOT NULL,
  `ruta_imagen` varchar(500) NOT NULL,
  `thumbnail` varchar(500) DEFAULT NULL,
  `titulo` varchar(200) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `es_principal` tinyint(1) NOT NULL DEFAULT 0,
  `orden` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `imagenes_proyecto_proyecto_id_index` (`proyecto_id`),
  KEY `imagenes_proyecto_proyecto_id_es_principal_index` (`proyecto_id`,`es_principal`),
  CONSTRAINT `imagenes_proyecto_proyecto_id_foreign` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `imagenes_proyecto`
--

LOCK TABLES `imagenes_proyecto` WRITE;
/*!40000 ALTER TABLE `imagenes_proyecto` DISABLE KEYS */;
INSERT INTO `imagenes_proyecto` VALUES (3,7,'uploads/proyectos/7/imagenes/fe5a4326-f22e-4487-96e3-fc4422f13b2e.jpg','uploads/proyectos/7/imagenes/thumbnails/fe5a4326-f22e-4487-96e3-fc4422f13b2e_thumb.jpg','campos-de-cultivo.jpg',NULL,0,1,'2025-12-03 21:28:48','2025-12-03 21:35:54'),(5,7,'uploads/proyectos/7/imagenes/093044fa-f774-4cf2-9240-182ec11ea63d.jpg','uploads/proyectos/7/imagenes/thumbnails/093044fa-f774-4cf2-9240-182ec11ea63d_thumb.jpg','cultivo-de-maiz.jpg',NULL,1,2,'2025-12-03 21:34:02','2025-12-03 21:35:54'),(6,9,'uploads/proyectos/9/imagenes/89e1c3bf-7e6b-4630-aff6-f4824ecfcf45.jpg','uploads/proyectos/9/imagenes/thumbnails/89e1c3bf-7e6b-4630-aff6-f4824ecfcf45_thumb.jpg','cultivo-de-maiz.jpg',NULL,1,1,'2025-12-03 22:26:52','2025-12-03 22:26:58'),(7,9,'uploads/proyectos/9/imagenes/ec965429-de6e-4be5-96d9-29ae770dd8b7.jpg','uploads/proyectos/9/imagenes/thumbnails/ec965429-de6e-4be5-96d9-29ae770dd8b7_thumb.jpg','campos-de-cultivo.jpg',NULL,0,2,'2025-12-03 22:26:55','2025-12-03 22:26:58'),(8,11,'uploads/proyectos/11/imagenes/041b9daa-d9fd-4ad6-b03b-594ecb411ca0.webp','uploads/proyectos/11/imagenes/thumbnails/041b9daa-d9fd-4ad6-b03b-594ecb411ca0_thumb.webp','Recibo Admitido','cultivo',1,1,'2025-12-16 04:48:14','2025-12-16 04:48:14'),(9,13,'uploads/proyectos/13/imagenes/5630158f-9f73-4203-991c-bc6db83c6a4c.png','uploads/proyectos/13/imagenes/thumbnails/5630158f-9f73-4203-991c-bc6db83c6a4c_thumb.png','Recibo Admitido','ok',1,1,'2025-12-16 20:12:35','2025-12-16 20:12:51'),(10,13,'uploads/proyectos/13/imagenes/14f98cff-ad21-4af3-8c27-e4367ffa7239.jpg','uploads/proyectos/13/imagenes/thumbnails/14f98cff-ad21-4af3-8c27-e4367ffa7239_thumb.jpg','El mejor','ui',0,2,'2025-12-16 20:12:48','2025-12-16 20:12:51');
/*!40000 ALTER TABLE `imagenes_proyecto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inversiones`
--

DROP TABLE IF EXISTS `inversiones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inversiones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codigo_inversion` varchar(50) NOT NULL,
  `usuario_id` bigint(20) unsigned NOT NULL,
  `proyecto_id` bigint(20) unsigned NOT NULL,
  `compra_cross_fund_id` bigint(20) unsigned DEFAULT NULL,
  `monto_invertido` decimal(15,2) NOT NULL,
  `valor_actual` decimal(15,2) NOT NULL,
  `ganancia_acumulada` decimal(15,2) NOT NULL DEFAULT 0.00,
  `dividendos_acumulados` decimal(15,2) NOT NULL DEFAULT 0.00,
  `fecha_inversion` date NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `fecha_retiro` date DEFAULT NULL,
  `estado` enum('pendiente_pago','activa','en_trading','vendida','vencida','retirada_anticipada','cancelada') NOT NULL DEFAULT 'pendiente_pago',
  `disponible_trading` tinyint(1) NOT NULL DEFAULT 0,
  `precio_venta_sugerido` decimal(15,2) DEFAULT NULL,
  `contrato_id` bigint(20) unsigned DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inversiones_codigo_inversion_unique` (`codigo_inversion`),
  KEY `inversiones_compra_cross_fund_id_foreign` (`compra_cross_fund_id`),
  KEY `inversiones_contrato_id_foreign` (`contrato_id`),
  KEY `inversiones_codigo_inversion_index` (`codigo_inversion`),
  KEY `inversiones_usuario_id_index` (`usuario_id`),
  KEY `inversiones_proyecto_id_index` (`proyecto_id`),
  KEY `inversiones_estado_index` (`estado`),
  KEY `inversiones_disponible_trading_index` (`disponible_trading`),
  KEY `inversiones_fecha_vencimiento_index` (`fecha_vencimiento`),
  CONSTRAINT `inversiones_compra_cross_fund_id_foreign` FOREIGN KEY (`compra_cross_fund_id`) REFERENCES `compras_cross_fund` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inversiones_contrato_id_foreign` FOREIGN KEY (`contrato_id`) REFERENCES `plantillas_contrato` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inversiones_proyecto_id_foreign` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`),
  CONSTRAINT `inversiones_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inversiones`
--

LOCK TABLES `inversiones` WRITE;
/*!40000 ALTER TABLE `inversiones` DISABLE KEYS */;
INSERT INTO `inversiones` VALUES (1,'INV-2025-00001',4,1,NULL,2000000.00,2000000.00,280000.00,280000.00,'2025-11-07','2027-06-02',NULL,'activa',0,NULL,NULL,NULL,'2025-12-03 02:34:32','2025-12-03 02:34:32',NULL),(2,'INV-2025-00002',4,2,NULL,1000000.00,1000000.00,170000.00,170000.00,'2025-10-08','2027-04-02',NULL,'activa',1,NULL,NULL,NULL,'2025-12-03 02:34:32','2025-12-03 02:34:32',NULL),(3,'INV-2025-000003',4,5,NULL,1000.00,1000.00,0.00,8.22,'2025-12-20','2026-12-20',NULL,'activa',0,NULL,1,NULL,'2025-12-21 00:53:02','2025-12-22 00:47:00',NULL);
/*!40000 ALTER TABLE `inversiones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs_auditoria`
--

DROP TABLE IF EXISTS `logs_auditoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logs_auditoria` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` bigint(20) unsigned DEFAULT NULL,
  `accion` enum('crear','actualizar','eliminar','login','logout','ver','exportar','aprobar','rechazar','otro') NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `modelo_id` bigint(20) unsigned DEFAULT NULL,
  `descripcion` text NOT NULL,
  `datos_anteriores` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos_anteriores`)),
  `datos_nuevos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos_nuevos`)),
  `ip` varchar(50) NOT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `fecha_hora` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `logs_auditoria_usuario_id_index` (`usuario_id`),
  KEY `logs_auditoria_accion_index` (`accion`),
  KEY `logs_auditoria_modelo_index` (`modelo`),
  KEY `logs_auditoria_modelo_id_modelo_index` (`modelo_id`,`modelo`),
  KEY `logs_auditoria_fecha_hora_index` (`fecha_hora`),
  CONSTRAINT `logs_auditoria_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs_auditoria`
--

LOCK TABLES `logs_auditoria` WRITE;
/*!40000 ALTER TABLE `logs_auditoria` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs_auditoria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mensajes`
--

DROP TABLE IF EXISTS `mensajes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mensajes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `remitente_id` bigint(20) unsigned NOT NULL,
  `destinatario_id` bigint(20) unsigned NOT NULL,
  `asunto` varchar(200) NOT NULL,
  `contenido` text NOT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT 0,
  `leido_at` timestamp NULL DEFAULT NULL,
  `archivado_remitente` tinyint(1) NOT NULL DEFAULT 0,
  `archivado_destinatario` tinyint(1) NOT NULL DEFAULT 0,
  `mensaje_padre_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mensajes_remitente_id_index` (`remitente_id`),
  KEY `mensajes_destinatario_id_index` (`destinatario_id`),
  KEY `mensajes_leido_index` (`leido`),
  KEY `mensajes_mensaje_padre_id_index` (`mensaje_padre_id`),
  KEY `mensajes_created_at_index` (`created_at`),
  CONSTRAINT `mensajes_destinatario_id_foreign` FOREIGN KEY (`destinatario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mensajes_mensaje_padre_id_foreign` FOREIGN KEY (`mensaje_padre_id`) REFERENCES `mensajes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `mensajes_remitente_id_foreign` FOREIGN KEY (`remitente_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mensajes`
--

LOCK TABLES `mensajes` WRITE;
/*!40000 ALTER TABLE `mensajes` DISABLE KEYS */;
/*!40000 ALTER TABLE `mensajes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_resets_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',1),(5,'2025_06_18_195318_create_permission_tables',1),(6,'2025_11_28_113120_cleanup_database_remove_unused_tables',1),(7,'2025_12_01_150542_add_agromarket_fields_to_users_table',1),(8,'2025_12_01_150755_create_categorias_proyecto_table',1),(9,'2025_12_01_150832_create_reglas_penalizacion_table',1),(10,'2025_12_01_150840_create_billeteras_table',1),(11,'2025_12_01_150848_create_proyectos_table',1),(12,'2025_12_01_150856_create_documentos_proyecto_table',1),(13,'2025_12_01_150915_create_imagenes_proyecto_table',1),(14,'2025_12_01_150923_create_actualizaciones_proyecto_table',1),(15,'2025_12_01_150930_create_plantillas_contrato_table',1),(16,'2025_12_01_150938_create_paquetes_cross_fund_table',1),(17,'2025_12_01_150945_create_proyectos_cross_fund_table',1),(18,'2025_12_01_150953_create_compras_cross_fund_table',1),(19,'2025_12_01_151000_create_inversiones_table',1),(20,'2025_12_01_151008_create_transacciones_inversion_table',1),(21,'2025_12_01_151015_create_transacciones_billetera_table',1),(22,'2025_12_01_151023_create_dividendos_table',1),(23,'2025_12_01_151031_create_retiros_table',1),(24,'2025_12_01_151038_create_depositos_table',1),(25,'2025_12_01_151045_create_documentos_kyc_table',1),(26,'2025_12_01_151053_create_cuentas_bancarias_table',1),(27,'2025_12_01_151100_create_aceptaciones_contrato_table',1),(28,'2025_12_01_151108_create_notificaciones_table',1),(29,'2025_12_01_151116_create_mensajes_table',1),(30,'2025_12_01_151123_create_prospectos_table',1),(31,'2025_12_01_151130_create_actividades_prospecto_table',1),(32,'2025_12_01_151138_create_logs_auditoria_table',1),(33,'2025_12_01_151145_create_configuraciones_sistema_table',1),(34,'2025_12_01_151153_create_reportes_table',1),(35,'2025_12_03_150844_create_notifications_table',2),(36,'2025_12_15_000001_create_perfiles_agricultor_table',3),(37,'2025_12_15_000002_create_familia_agricultor_table',3),(38,'2025_12_15_000003_add_v2_fields_to_users_table',3),(39,'2025_12_15_000004_add_v2_fields_to_proyectos_table',3),(40,'2025_12_15_000005_update_tipo_documento_enum',3),(41,'2025_12_15_230038_modify_pais_column_in_users_table',4),(42,'2025_12_15_235500_add_document_types_to_documentos_proyecto',5);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',4),(1,'App\\Models\\User',5),(1,'App\\Models\\User',11),(2,'App\\Models\\User',3),(2,'App\\Models\\User',8),(2,'App\\Models\\User',9),(3,'App\\Models\\User',1),(4,'App\\Models\\User',2),(5,'App\\Models\\User',6);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notificaciones`
--

DROP TABLE IF EXISTS `notificaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notificaciones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` bigint(20) unsigned NOT NULL,
  `tipo` enum('sistema','proyecto','inversion','dividendo','retiro','deposito','mensaje','alerta','marketing') NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `contenido` text NOT NULL,
  `url_accion` varchar(500) DEFAULT NULL,
  `leida` tinyint(1) NOT NULL DEFAULT 0,
  `leida_at` timestamp NULL DEFAULT NULL,
  `prioridad` enum('baja','media','alta','urgente') NOT NULL DEFAULT 'media',
  `referencia_id` bigint(20) unsigned DEFAULT NULL,
  `referencia_type` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notificaciones_usuario_id_index` (`usuario_id`),
  KEY `notificaciones_leida_index` (`leida`),
  KEY `notificaciones_tipo_index` (`tipo`),
  KEY `notificaciones_referencia_id_referencia_type_index` (`referencia_id`,`referencia_type`),
  KEY `notificaciones_created_at_index` (`created_at`),
  CONSTRAINT `notificaciones_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notificaciones`
--

LOCK TABLES `notificaciones` WRITE;
/*!40000 ALTER TABLE `notificaciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `notificaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES ('122f9ade-2ed8-4050-ac79-f98346429a21','App\\Notifications\\FarmerWelcomeNotification','App\\Models\\User',9,'{\"titulo\":\"\\u00a1Bienvenido a AgroMarket!\",\"mensaje\":\"Tu cuenta de agricultor ha sido creada exitosamente. Revisa tu correo para obtener tus credenciales de acceso.\",\"tipo\":\"bienvenida_agricultor\",\"farmer_id\":9,\"farmer_name\":\"Santi\",\"farmer_email\":\"vblogsanti@gmail.com\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/agricultor\\/dashboard\"}',NULL,'2025-12-16 04:27:13','2025-12-16 04:27:13'),('14cf521e-886f-4ccc-ae93-73e138c00b42','App\\Notifications\\ProyectoRechazadoNotification','App\\Models\\User',3,'{\"titulo\":\"Proyecto rechazado\",\"mensaje\":\"Tu proyecto \'Producci\\u00f3n de Pl\\u00e1tano - Quind\\u00edo\' (STK-2025-004) ha sido rechazado.\",\"tipo\":\"proyecto_rechazado\",\"proyecto_id\":4,\"proyecto_codigo\":\"STK-2025-004\",\"proyecto_nombre\":\"Producci\\u00f3n de Pl\\u00e1tano - Quind\\u00edo\",\"motivo_rechazo\":\"mal\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/agricultor\\/projects\\/4\"}',NULL,'2025-12-03 20:33:07','2025-12-03 20:33:07'),('1b13efcf-67da-429b-9766-0aa661313823','App\\Notifications\\DividendoPagadoNotification','App\\Models\\User',4,'{\"titulo\":\"Dividendo Pagado\",\"mensaje\":\"Se ha acreditado un dividendo de $8 a tu billetera del proyecto \'Limon Colombiano\'.\",\"tipo\":\"dividendo_pagado\",\"dividendo_id\":3,\"dividendo_codigo\":\"DIV-2025--00001\",\"monto\":\"8.22\",\"monto_formateado\":\"$8\",\"numero_periodo\":1,\"proyecto_id\":5,\"proyecto_nombre\":\"Limon Colombiano\",\"inversion_id\":3,\"inversion_codigo\":\"INV-2025-000003\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/inversionista\\/dividendos\"}',NULL,'2025-12-22 00:47:02','2025-12-22 00:47:02'),('214c86e0-95d1-432a-b76a-75be94a76532','App\\Notifications\\ProyectoRechazadoNotification','App\\Models\\User',3,'{\"titulo\":\"Proyecto rechazado\",\"mensaje\":\"Tu proyecto \'Limon Colombiano\' (STAKING-2025-001) ha sido rechazado.\",\"tipo\":\"proyecto_rechazado\",\"proyecto_id\":5,\"proyecto_codigo\":\"STAKING-2025-001\",\"proyecto_nombre\":\"Limon Colombiano\",\"motivo_rechazo\":\"mal\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/agricultor\\/projects\\/5\"}',NULL,'2025-12-03 20:33:52','2025-12-03 20:33:52'),('2496fab7-5c29-4561-be03-bc4db4030730','App\\Notifications\\ProyectoEnRevisionNotification','App\\Models\\User',1,'{\"titulo\":\"Nuevo proyecto pendiente de revisi\\u00f3n\",\"mensaje\":\"El proyecto \'Limon Colombiano\' (STAKING-2025-001) ha sido enviado a revisi\\u00f3n por Carlos Agricultor Ram\\u00edrez.\",\"tipo\":\"proyecto_revision\",\"proyecto_id\":5,\"proyecto_codigo\":\"STAKING-2025-001\",\"proyecto_nombre\":\"Limon Colombiano\",\"agricultor_id\":3,\"agricultor_nombre\":\"Carlos Agricultor Ram\\u00edrez\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/proyectos\\/revision\\/5\"}',NULL,'2025-12-03 20:40:04','2025-12-03 20:40:04'),('29e8df88-6767-4242-9a3d-ef4b422dd9c3','App\\Notifications\\ProyectoEnRevisionNotification','App\\Models\\User',1,'{\"titulo\":\"Nuevo proyecto pendiente de revisi\\u00f3n\",\"mensaje\":\"El proyecto \'Limon Colombiano\' (STAKING-2025-001) ha sido enviado a revisi\\u00f3n por Carlos Agricultor Ram\\u00edrez.\",\"tipo\":\"proyecto_revision\",\"proyecto_id\":5,\"proyecto_codigo\":\"STAKING-2025-001\",\"proyecto_nombre\":\"Limon Colombiano\",\"agricultor_id\":3,\"agricultor_nombre\":\"Carlos Agricultor Ram\\u00edrez\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/proyectos\\/revision\\/5\"}',NULL,'2025-12-03 20:34:45','2025-12-03 20:34:45'),('2ae76fed-40de-493a-bb5a-b1ca0521943f','App\\Notifications\\ProyectoEnRevisionNotification','App\\Models\\User',1,'{\"titulo\":\"Nuevo proyecto pendiente de revisi\\u00f3n\",\"mensaje\":\"El proyecto \'Pera\' (STAKING-2025-003) ha sido enviado a revisi\\u00f3n por Carlos Agricultor Ram\\u00edrez.\",\"tipo\":\"proyecto_revision\",\"proyecto_id\":7,\"proyecto_codigo\":\"STAKING-2025-003\",\"proyecto_nombre\":\"Pera\",\"agricultor_id\":3,\"agricultor_nombre\":\"Carlos Agricultor Ram\\u00edrez\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/proyectos\\/revision\\/7\"}',NULL,'2025-12-03 21:36:04','2025-12-03 21:36:04'),('30fead25-c1ad-4bf4-9c82-cd65a6239c4c','App\\Notifications\\ProyectoEnRevisionNotification','App\\Models\\User',1,'{\"titulo\":\"Nuevo proyecto pendiente de revisi\\u00f3n\",\"mensaje\":\"El proyecto \'Limon Colombiano\' (STAKING-2025-001) ha sido enviado a revisi\\u00f3n por Carlos Agricultor Ram\\u00edrez.\",\"tipo\":\"proyecto_revision\",\"proyecto_id\":5,\"proyecto_codigo\":\"STAKING-2025-001\",\"proyecto_nombre\":\"Limon Colombiano\",\"agricultor_id\":3,\"agricultor_nombre\":\"Carlos Agricultor Ram\\u00edrez\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/proyectos\\/revision\\/5\"}',NULL,'2025-12-03 20:33:40','2025-12-03 20:33:40'),('4b7163b4-e733-4a87-9cb6-df41a501e939','App\\Notifications\\ProyectoEnRevisionNotification','App\\Models\\User',1,'{\"titulo\":\"Nuevo proyecto pendiente de revisi\\u00f3n\",\"mensaje\":\"El proyecto \'lulo2\' (STAKING-2025-008) ha sido enviado a revisi\\u00f3n por Santi.\",\"tipo\":\"proyecto_revision\",\"proyecto_id\":13,\"proyecto_codigo\":\"STAKING-2025-008\",\"proyecto_nombre\":\"lulo2\",\"agricultor_id\":9,\"agricultor_nombre\":\"Santi\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/proyectos\\/revision\\/13\"}',NULL,'2025-12-16 20:23:00','2025-12-16 20:23:00'),('5b8e3038-d974-46b0-be82-37f1c35a1241','App\\Notifications\\ProyectoRechazadoNotification','App\\Models\\User',3,'{\"titulo\":\"Proyecto rechazado\",\"mensaje\":\"Tu proyecto \'Limon Colombiano\' (STAKING-2025-001) ha sido rechazado.\",\"tipo\":\"proyecto_rechazado\",\"proyecto_id\":5,\"proyecto_codigo\":\"STAKING-2025-001\",\"proyecto_nombre\":\"Limon Colombiano\",\"motivo_rechazo\":\"empieza otro dia\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/agricultor\\/projects\\/5\"}',NULL,'2025-12-03 20:37:55','2025-12-03 20:37:55'),('66907e64-d7bd-424a-8983-238eb434c80e','App\\Notifications\\ProyectoEnRevisionNotification','App\\Models\\User',1,'{\"titulo\":\"Nuevo proyecto pendiente de revisi\\u00f3n\",\"mensaje\":\"El proyecto \'Limon Colombiano\' (STAKING-2025-006) ha sido enviado a revisi\\u00f3n por Santi.\",\"tipo\":\"proyecto_revision\",\"proyecto_id\":11,\"proyecto_codigo\":\"STAKING-2025-006\",\"proyecto_nombre\":\"Limon Colombiano\",\"agricultor_id\":9,\"agricultor_nombre\":\"Santi\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/proyectos\\/revision\\/11\"}',NULL,'2025-12-16 04:27:41','2025-12-16 04:27:41'),('6c68bf36-860c-48ba-8ae4-cc67a1e68175','App\\Notifications\\ProyectoAprobadoNotification','App\\Models\\User',3,'{\"titulo\":\"\\u00a1Proyecto aprobado!\",\"mensaje\":\"Tu proyecto \'Fresas\' (STAKING-2025-005) ha sido aprobado y est\\u00e1 ahora en recaudaci\\u00f3n.\",\"tipo\":\"proyecto_aprobado\",\"proyecto_id\":9,\"proyecto_codigo\":\"STAKING-2025-005\",\"proyecto_nombre\":\"Fresas\",\"aprobado_por\":\"Administrador Principal\",\"notas_aprobacion\":null,\"url\":\"http:\\/\\/127.0.0.1:8000\\/agricultor\\/projects\\/9\"}',NULL,'2025-12-03 22:28:58','2025-12-03 22:28:58'),('6d738c13-03ea-4635-bc68-f35c051c30e9','App\\Notifications\\ProyectoAprobadoNotification','App\\Models\\User',9,'{\"titulo\":\"\\u00a1Proyecto aprobado!\",\"mensaje\":\"Tu proyecto \'lulo2\' (STAKING-2025-008) ha sido aprobado y est\\u00e1 ahora en recaudaci\\u00f3n.\",\"tipo\":\"proyecto_aprobado\",\"proyecto_id\":13,\"proyecto_codigo\":\"STAKING-2025-008\",\"proyecto_nombre\":\"lulo2\",\"aprobado_por\":\"Administrador Principal\",\"notas_aprobacion\":null,\"url\":\"http:\\/\\/127.0.0.1:8000\\/agricultor\\/projects\\/13\"}',NULL,'2025-12-16 20:23:28','2025-12-16 20:23:28'),('6f900ad1-7823-42ea-9d71-886ddf3d33a2','App\\Notifications\\ProyectoRechazadoNotification','App\\Models\\User',3,'{\"titulo\":\"Proyecto rechazado\",\"mensaje\":\"Tu proyecto \'Limon Colombiano\' (STAKING-2025-001) ha sido rechazado.\",\"tipo\":\"proyecto_rechazado\",\"proyecto_id\":5,\"proyecto_codigo\":\"STAKING-2025-001\",\"proyecto_nombre\":\"Limon Colombiano\",\"motivo_rechazo\":\"inversion minima muuy mala\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/agricultor\\/projects\\/5\"}',NULL,'2025-12-03 20:26:09','2025-12-03 20:26:09'),('7df7db86-f891-4635-83ec-e39c63342d62','App\\Notifications\\ProyectoAprobadoNotification','App\\Models\\User',3,'{\"titulo\":\"\\u00a1Proyecto aprobado!\",\"mensaje\":\"Tu proyecto \'Limon Colombiano\' (STAKING-2025-001) ha sido aprobado y est\\u00e1 ahora en recaudaci\\u00f3n.\",\"tipo\":\"proyecto_aprobado\",\"proyecto_id\":5,\"proyecto_codigo\":\"STAKING-2025-001\",\"proyecto_nombre\":\"Limon Colombiano\",\"aprobado_por\":\"Administrador Principal\",\"notas_aprobacion\":\"Ahora si\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/agricultor\\/projects\\/5\"}',NULL,'2025-12-03 20:40:18','2025-12-03 20:40:18'),('86da105e-af6a-4c73-a6b8-4038ced93581','App\\Notifications\\ProyectoEnRevisionNotification','App\\Models\\User',1,'{\"titulo\":\"Nuevo proyecto pendiente de revisi\\u00f3n\",\"mensaje\":\"El proyecto \'Limon Colombiano\' (STAKING-2025-001) ha sido enviado a revisi\\u00f3n por Carlos Agricultor Ram\\u00edrez.\",\"tipo\":\"proyecto_revision\",\"proyecto_id\":5,\"proyecto_codigo\":\"STAKING-2025-001\",\"proyecto_nombre\":\"Limon Colombiano\",\"agricultor_id\":3,\"agricultor_nombre\":\"Carlos Agricultor Ram\\u00edrez\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/proyectos\\/revision\\/5\"}',NULL,'2025-12-03 20:21:04','2025-12-03 20:21:04'),('8bf983c4-86cb-477e-b7c1-37081e72cd59','App\\Notifications\\ProyectoEnRevisionNotification','App\\Models\\User',1,'{\"titulo\":\"Nuevo proyecto pendiente de revisi\\u00f3n\",\"mensaje\":\"El proyecto \'Fresas\' (STAKING-2025-005) ha sido enviado a revisi\\u00f3n por Carlos Agricultor Ram\\u00edrez.\",\"tipo\":\"proyecto_revision\",\"proyecto_id\":9,\"proyecto_codigo\":\"STAKING-2025-005\",\"proyecto_nombre\":\"Fresas\",\"agricultor_id\":3,\"agricultor_nombre\":\"Carlos Agricultor Ram\\u00edrez\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/proyectos\\/revision\\/9\"}',NULL,'2025-12-03 22:28:08','2025-12-03 22:28:08'),('91c9a1da-ae43-44f5-82c4-31fbb90aa4b2','App\\Notifications\\ProyectoEnRevisionNotification','App\\Models\\User',1,'{\"titulo\":\"Nuevo proyecto pendiente de revisi\\u00f3n\",\"mensaje\":\"El proyecto \'Limon Colombiano\' (STAKING-2025-001) ha sido enviado a revisi\\u00f3n por Carlos Agricultor Ram\\u00edrez.\",\"tipo\":\"proyecto_revision\",\"proyecto_id\":5,\"proyecto_codigo\":\"STAKING-2025-001\",\"proyecto_nombre\":\"Limon Colombiano\",\"agricultor_id\":3,\"agricultor_nombre\":\"Carlos Agricultor Ram\\u00edrez\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/proyectos\\/revision\\/5\"}',NULL,'2025-12-03 20:11:34','2025-12-03 20:11:34'),('c8662d6b-1270-4fa2-8d99-4ac94e11ff50','App\\Notifications\\ProyectoRechazadoNotification','App\\Models\\User',3,'{\"titulo\":\"Proyecto rechazado\",\"mensaje\":\"Tu proyecto \'Limon Colombiano\' (STAKING-2025-001) ha sido rechazado.\",\"tipo\":\"proyecto_rechazado\",\"proyecto_id\":5,\"proyecto_codigo\":\"STAKING-2025-001\",\"proyecto_nombre\":\"Limon Colombiano\",\"motivo_rechazo\":\"muy grande ese roy\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/agricultor\\/projects\\/5\"}',NULL,'2025-12-03 20:14:54','2025-12-03 20:14:54');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paquetes_cross_fund`
--

DROP TABLE IF EXISTS `paquetes_cross_fund`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paquetes_cross_fund` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text NOT NULL,
  `monto_paquete` decimal(15,2) NOT NULL,
  `roi_ponderado` decimal(5,2) NOT NULL,
  `duracion_promedio_meses` int(11) NOT NULL,
  `estado` enum('borrador','activo','agotado','cerrado') NOT NULL DEFAULT 'borrador',
  `cantidad_disponible` int(11) NOT NULL DEFAULT 0,
  `cantidad_vendida` int(11) NOT NULL DEFAULT 0,
  `destacado` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_inicio_venta` date NOT NULL,
  `fecha_fin_venta` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `paquetes_cross_fund_codigo_unique` (`codigo`),
  KEY `paquetes_cross_fund_codigo_index` (`codigo`),
  KEY `paquetes_cross_fund_estado_index` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paquetes_cross_fund`
--

LOCK TABLES `paquetes_cross_fund` WRITE;
/*!40000 ALTER TABLE `paquetes_cross_fund` DISABLE KEYS */;
/*!40000 ALTER TABLE `paquetes_cross_fund` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `perfiles_agricultor`
--

DROP TABLE IF EXISTS `perfiles_agricultor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `perfiles_agricultor` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `tipo_persona` enum('natural','juridica') NOT NULL DEFAULT 'natural',
  `nombre_empresa` varchar(255) DEFAULT NULL,
  `nit` varchar(50) DEFAULT NULL,
  `representante_legal` varchar(255) DEFAULT NULL,
  `direccion_finca` text DEFAULT NULL,
  `cultivo_asegurado` tinyint(1) NOT NULL DEFAULT 0,
  `anos_experiencia` int(11) DEFAULT NULL,
  `formacion_capacitaciones` text DEFAULT NULL,
  `cantidad_cosechas` int(11) DEFAULT NULL,
  `produccion_promedio` text DEFAULT NULL,
  `num_personas_trabajando` int(11) DEFAULT NULL,
  `familia_trabaja_cultivo` tinyint(1) NOT NULL DEFAULT 0,
  `roles_principales` text DEFAULT NULL,
  `nivel_tecnificacion` enum('manual','semi_tecnificado','tecnificado') DEFAULT NULL,
  `tiene_riego` tinyint(1) NOT NULL DEFAULT 0,
  `tiene_bodega` tinyint(1) NOT NULL DEFAULT 0,
  `tiene_transformacion` tinyint(1) NOT NULL DEFAULT 0,
  `tiene_transporte` tinyint(1) NOT NULL DEFAULT 0,
  `accesibilidad` text DEFAULT NULL,
  `riesgos_naturales` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `perfiles_agricultor_user_id_unique` (`user_id`),
  CONSTRAINT `perfiles_agricultor_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `perfiles_agricultor`
--

LOCK TABLES `perfiles_agricultor` WRITE;
/*!40000 ALTER TABLE `perfiles_agricultor` DISABLE KEYS */;
INSERT INTO `perfiles_agricultor` VALUES (3,9,'juridica','santiag2','900123154','Santiago2','direccion',1,52,'Curso',102,'502',5,1,'Admin, jornaleros','semi_tecnificado',1,1,1,1,'Toda','A veces','2025-12-16 04:19:57','2025-12-16 22:15:52');
/*!40000 ALTER TABLE `perfiles_agricultor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'ver_proyectos','web','2025-12-02 21:02:59','2025-12-02 21:02:59'),(2,'crear_proyectos','web','2025-12-02 21:02:59','2025-12-02 21:02:59'),(3,'editar_proyectos','web','2025-12-02 21:02:59','2025-12-02 21:02:59'),(4,'aprobar_proyectos','web','2025-12-02 21:02:59','2025-12-02 21:02:59'),(5,'eliminar_proyectos','web','2025-12-02 21:02:59','2025-12-02 21:02:59'),(6,'ver_inversiones','web','2025-12-02 21:02:59','2025-12-02 21:02:59'),(7,'crear_inversiones','web','2025-12-02 21:02:59','2025-12-02 21:02:59'),(8,'gestionar_inversiones','web','2025-12-02 21:02:59','2025-12-02 21:02:59'),(9,'ver_usuarios','web','2025-12-02 21:02:59','2025-12-02 21:02:59'),(10,'crear_usuarios','web','2025-12-02 21:02:59','2025-12-02 21:02:59'),(11,'editar_usuarios','web','2025-12-02 21:02:59','2025-12-02 21:02:59'),(12,'eliminar_usuarios','web','2025-12-02 21:02:59','2025-12-02 21:02:59'),(13,'revisar_kyc','web','2025-12-02 21:02:59','2025-12-02 21:02:59'),(14,'aprobar_kyc','web','2025-12-02 21:02:59','2025-12-02 21:02:59'),(15,'ver_transacciones','web','2025-12-02 21:02:59','2025-12-02 21:02:59'),(16,'aprobar_depositos','web','2025-12-02 21:02:59','2025-12-02 21:02:59'),(17,'aprobar_retiros','web','2025-12-02 21:02:59','2025-12-02 21:02:59'),(18,'procesar_pagos','web','2025-12-02 21:02:59','2025-12-02 21:02:59'),(19,'gestionar_dividendos','web','2025-12-02 21:02:59','2025-12-02 21:02:59'),(20,'pagar_dividendos','web','2025-12-02 21:02:59','2025-12-02 21:02:59'),(21,'gestionar_prospectos','web','2025-12-02 21:02:59','2025-12-02 21:02:59'),(22,'ver_reportes','web','2025-12-02 21:02:59','2025-12-02 21:02:59'),(23,'generar_reportes','web','2025-12-02 21:03:00','2025-12-02 21:03:00'),(24,'gestionar_configuracion','web','2025-12-02 21:03:00','2025-12-02 21:03:00');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plantillas_contrato`
--

DROP TABLE IF EXISTS `plantillas_contrato`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plantillas_contrato` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `contenido` text NOT NULL,
  `tipo_contrato` enum('inversion_staking','inversion_ear','inversion_futuros','inversion_cross_fund','proyecto_agricultor','terminos_servicio','politica_privacidad') NOT NULL,
  `version` varchar(20) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_vigencia` date NOT NULL,
  `fecha_expiracion` date DEFAULT NULL,
  `variables_requeridas` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plantillas_contrato_codigo_unique` (`codigo`),
  KEY `plantillas_contrato_codigo_index` (`codigo`),
  KEY `plantillas_contrato_tipo_contrato_index` (`tipo_contrato`),
  KEY `plantillas_contrato_activo_fecha_vigencia_index` (`activo`,`fecha_vigencia`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plantillas_contrato`
--

LOCK TABLES `plantillas_contrato` WRITE;
/*!40000 ALTER TABLE `plantillas_contrato` DISABLE KEYS */;
INSERT INTO `plantillas_contrato` VALUES (1,'CONT-STAK-514E','Contrato de Inversión - Staking Agrícola','<div class=\"contract-content\">\r\n    <h2 class=\"text-center mb-4\">CONTRATO DE INVERSIÓN AGRÍCOLA</h2>\r\n\r\n    <p class=\"text-justify\">\r\n        En la ciudad de Bogotá D.C., a los {{fecha_actual_letras}}, entre {{nombre_plataforma}}\r\n        (en adelante \"LA PLATAFORMA\") y <strong>{{nombre_inversionista}}</strong>, identificado(a) con\r\n        documento de identidad número <strong>{{documento_inversionista}}</strong> (en adelante \"EL INVERSIONISTA\"),\r\n        se celebra el presente contrato de inversión agrícola.\r\n    </p>\r\n\r\n    <h4 class=\"mt-4\">PRIMERA: OBJETO DEL CONTRATO</h4>\r\n    <p class=\"text-justify\">\r\n        EL INVERSIONISTA declara su voluntad de invertir en el proyecto agrícola denominado\r\n        <strong>\"{{nombre_proyecto}}\"</strong> (Código: {{codigo_proyecto}}), ubicado en {{ubicacion_proyecto}},\r\n        administrado a través de LA PLATAFORMA.\r\n    </p>\r\n\r\n    <h4 class=\"mt-4\">SEGUNDA: MONTO DE LA INVERSIÓN</h4>\r\n    <p class=\"text-justify\">\r\n        EL INVERSIONISTA aporta la suma de <strong>{{monto_inversion}}</strong> ({{monto_inversion_letras}})\r\n        para participar en el proyecto mencionado.\r\n    </p>\r\n\r\n    <h4 class=\"mt-4\">TERCERA: RENDIMIENTOS</h4>\r\n    <p class=\"text-justify\">\r\n        El proyecto ofrece un rendimiento anual estimado del <strong>{{roi_anual}}</strong>.\r\n        Los dividendos se pagarán cada {{periodo_dividendos}}, estimándose un retorno mensual\r\n        de aproximadamente {{retorno_mensual_estimado}}.\r\n    </p>\r\n\r\n    <h4 class=\"mt-4\">CUARTA: DURACIÓN</h4>\r\n    <p class=\"text-justify\">\r\n        La inversión tiene una duración de <strong>{{duracion_meses}}</strong>, contados a partir\r\n        de la fecha de aceptación del presente contrato. La fecha estimada de vencimiento es\r\n        el <strong>{{fecha_vencimiento}}</strong>.\r\n    </p>\r\n\r\n    <h4 class=\"mt-4\">QUINTA: DECLARACIONES</h4>\r\n    <p class=\"text-justify\">\r\n        EL INVERSIONISTA declara que:\r\n    </p>\r\n    <ul>\r\n        <li>Los fondos invertidos son de origen lícito.</li>\r\n        <li>Ha leído y comprende los riesgos asociados a la inversión agrícola.</li>\r\n        <li>La información proporcionada en su perfil es verídica y actualizada.</li>\r\n        <li>Acepta los términos y condiciones de LA PLATAFORMA.</li>\r\n    </ul>\r\n\r\n    <h4 class=\"mt-4\">SEXTA: RIESGOS</h4>\r\n    <p class=\"text-justify\">\r\n        EL INVERSIONISTA reconoce que toda inversión conlleva riesgos, incluyendo pero no\r\n        limitándose a: condiciones climáticas adversas, fluctuaciones del mercado, plagas\r\n        y enfermedades de cultivos. LA PLATAFORMA no garantiza los rendimientos proyectados.\r\n    </p>\r\n\r\n    <h4 class=\"mt-4\">SÉPTIMA: ACEPTACIÓN DIGITAL</h4>\r\n    <p class=\"text-justify\">\r\n        Las partes acuerdan que la firma digital mediante el ingreso del nombre completo\r\n        del inversionista en la plataforma tiene la misma validez legal que una firma\r\n        manuscrita, conforme a la Ley 527 de 1999 de Colombia.\r\n    </p>\r\n\r\n    <div class=\"mt-5 pt-4 border-top\">\r\n        <p class=\"mb-1\"><strong>Fecha de aceptación:</strong> {{fecha_actual}}</p>\r\n        <p class=\"mb-1\"><strong>Inversionista:</strong> {{nombre_inversionista}}</p>\r\n        <p class=\"mb-1\"><strong>Email:</strong> {{email_inversionista}}</p>\r\n    </div>\r\n</div>','inversion_staking','1.0',1,'2025-12-20',NULL,'\"[\\\"nombre_inversionista\\\",\\\"email_inversionista\\\",\\\"documento_inversionista\\\",\\\"nombre_proyecto\\\",\\\"codigo_proyecto\\\",\\\"monto_inversion\\\",\\\"roi_anual\\\",\\\"duracion_meses\\\",\\\"fecha_actual\\\",\\\"fecha_vencimiento\\\"]\"','2025-12-21 00:50:15','2025-12-21 00:50:15');
/*!40000 ALTER TABLE `plantillas_contrato` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prospectos`
--

DROP TABLE IF EXISTS `prospectos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prospectos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codigo_prospecto` varchar(50) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `email` varchar(200) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `tipo` enum('inversionista','agricultor','otro') NOT NULL,
  `estado` enum('nuevo','contactado','interesado','negociacion','convertido','descartado') NOT NULL DEFAULT 'nuevo',
  `origen` enum('web','referido','evento','redes_sociales','telemarketing','otro') NOT NULL,
  `asignado_a` bigint(20) unsigned DEFAULT NULL,
  `fecha_contacto` date DEFAULT NULL,
  `fecha_conversion` date DEFAULT NULL,
  `usuario_convertido_id` bigint(20) unsigned DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `datos_adicionales` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos_adicionales`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `prospectos_codigo_prospecto_unique` (`codigo_prospecto`),
  KEY `prospectos_usuario_convertido_id_foreign` (`usuario_convertido_id`),
  KEY `prospectos_codigo_prospecto_index` (`codigo_prospecto`),
  KEY `prospectos_email_index` (`email`),
  KEY `prospectos_telefono_index` (`telefono`),
  KEY `prospectos_estado_index` (`estado`),
  KEY `prospectos_asignado_a_index` (`asignado_a`),
  CONSTRAINT `prospectos_asignado_a_foreign` FOREIGN KEY (`asignado_a`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `prospectos_usuario_convertido_id_foreign` FOREIGN KEY (`usuario_convertido_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prospectos`
--

LOCK TABLES `prospectos` WRITE;
/*!40000 ALTER TABLE `prospectos` DISABLE KEYS */;
INSERT INTO `prospectos` VALUES (1,'PROS-2025-00001','María García','maria@example.com','3001234567','inversionista','interesado','redes_sociales',6,'2025-11-27',NULL,NULL,NULL,NULL,'2025-12-03 02:35:14','2025-12-03 02:35:14',NULL),(2,'PROS-2025-00002','Carlos Rodríguez','carlos@example.com','3107654321','inversionista','contactado','web',6,'2025-11-22',NULL,NULL,NULL,NULL,'2025-12-03 02:36:08','2025-12-03 02:36:08',NULL),(3,'PROS-2025-00003','Ana Martínez','ana.martinez@example.com','3159876543','inversionista','convertido','referido',6,'2025-10-19','2025-11-03',4,NULL,NULL,'2025-12-03 11:07:41','2025-12-03 11:07:41',NULL),(4,'PROS-2025-00004','Luis Fernández','luis.f@example.com','3124567890','inversionista','negociacion','evento',6,'2025-11-25',NULL,NULL,NULL,NULL,'2025-12-03 11:07:41','2025-12-03 11:07:41',NULL),(5,'PROS-2025-00005','Patricia Gómez','patricia.gomez@example.com','3187654321','inversionista','nuevo','web',6,'2025-12-01',NULL,NULL,NULL,NULL,'2025-12-03 11:07:41','2025-12-03 11:07:41',NULL),(6,'PROS-2025-00006','Roberto Silva','roberto.silva@example.com','3145678901','agricultor','interesado','telemarketing',6,'2025-11-21',NULL,NULL,NULL,NULL,'2025-12-03 11:07:41','2025-12-03 11:07:41',NULL),(7,'PROS-2025-00007','Sandra López','sandra.lopez@example.com','3165432109','inversionista','descartado','redes_sociales',6,'2025-11-13',NULL,NULL,NULL,NULL,'2025-12-03 11:07:41','2025-12-03 11:07:41',NULL),(8,'PROS-2025-00008','Diego Ramírez','diego.ramirez@example.com','3198765432','inversionista','negociacion','referido',6,'2025-11-27',NULL,NULL,NULL,NULL,'2025-12-03 11:07:41','2025-12-03 11:07:41',NULL);
/*!40000 ALTER TABLE `prospectos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proyectos`
--

DROP TABLE IF EXISTS `proyectos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proyectos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) NOT NULL,
  `categoria_id` bigint(20) unsigned NOT NULL,
  `agricultor_id` bigint(20) unsigned NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text NOT NULL,
  `tipo_cultivo` varchar(100) DEFAULT NULL,
  `area_hectareas` decimal(10,2) DEFAULT NULL,
  `etapa_cultivo` enum('siembra','crecimiento','cosecha','transformacion','otro') DEFAULT NULL,
  `ano_inicio_cultivo` int(11) DEFAULT NULL,
  `ubicacion` text NOT NULL,
  `coordenadas` varchar(100) DEFAULT NULL,
  `monto_objetivo` decimal(15,2) NOT NULL,
  `monto_recaudado` decimal(15,2) NOT NULL DEFAULT 0.00,
  `inversion_minima` decimal(15,2) NOT NULL,
  `inversion_maxima` decimal(15,2) DEFAULT NULL,
  `roi_anual` decimal(5,2) NOT NULL,
  `duracion_meses` int(11) NOT NULL,
  `periodo_cosecha_meses` int(11) DEFAULT NULL,
  `periodo_dividendos_dias` int(11) NOT NULL DEFAULT 30,
  `fecha_inicio_recaudacion` date NOT NULL,
  `fecha_cierre_recaudacion` date NOT NULL,
  `fecha_inicio_proyecto` date DEFAULT NULL,
  `fecha_fin_proyecto` date DEFAULT NULL,
  `fecha_primer_dividendo` date DEFAULT NULL,
  `estado` enum('borrador','en_revision','rechazado','aprobado','en_recaudacion','fondeado','en_ejecucion','en_cosecha','finalizado','cancelado') NOT NULL DEFAULT 'borrador',
  `aprobado_por` bigint(20) unsigned DEFAULT NULL,
  `aprobado_at` timestamp NULL DEFAULT NULL,
  `notas_aprobacion` text DEFAULT NULL,
  `motivo_rechazo` text DEFAULT NULL,
  `nivel_riesgo` enum('bajo','medio','alto') NOT NULL DEFAULT 'medio',
  `verificado` tinyint(1) NOT NULL DEFAULT 0,
  `destacado` tinyint(1) NOT NULL DEFAULT 0,
  `orden_destacado` int(11) NOT NULL DEFAULT 0,
  `datos_adicionales` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos_adicionales`)),
  `objetivo_proyecto` text DEFAULT NULL,
  `detalle_proceso_productivo` text DEFAULT NULL,
  `cronograma_estimado` text DEFAULT NULL,
  `datos_financieros` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos_financieros`)),
  `datos_earn` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos_earn`)),
  `datos_futuros` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos_futuros`)),
  `datos_farming` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos_farming`)),
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `creado_por_admin` tinyint(1) NOT NULL DEFAULT 0,
  `admin_creador_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `proyectos_codigo_unique` (`codigo`),
  KEY `proyectos_aprobado_por_foreign` (`aprobado_por`),
  KEY `proyectos_codigo_index` (`codigo`),
  KEY `proyectos_categoria_id_index` (`categoria_id`),
  KEY `proyectos_agricultor_id_index` (`agricultor_id`),
  KEY `proyectos_estado_index` (`estado`),
  KEY `proyectos_fecha_cierre_recaudacion_index` (`fecha_cierre_recaudacion`),
  KEY `proyectos_destacado_orden_destacado_index` (`destacado`,`orden_destacado`),
  KEY `proyectos_admin_creador_id_foreign` (`admin_creador_id`),
  KEY `proyectos_creado_por_admin_index` (`creado_por_admin`),
  KEY `proyectos_tipo_cultivo_index` (`tipo_cultivo`),
  CONSTRAINT `proyectos_admin_creador_id_foreign` FOREIGN KEY (`admin_creador_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `proyectos_agricultor_id_foreign` FOREIGN KEY (`agricultor_id`) REFERENCES `users` (`id`),
  CONSTRAINT `proyectos_aprobado_por_foreign` FOREIGN KEY (`aprobado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `proyectos_categoria_id_foreign` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_proyecto` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proyectos`
--

LOCK TABLES `proyectos` WRITE;
/*!40000 ALTER TABLE `proyectos` DISABLE KEYS */;
INSERT INTO `proyectos` VALUES (1,'STK-2025-001',1,3,'Cultivo de Aguacate Hass - Valle del Cauca','Proyecto de cultivo de aguacate Hass en 10 hectáreas con tecnología de riego por goteo.',NULL,NULL,NULL,NULL,'Valle del Cauca, Colombia','3.4516,-76.5320',50000000.00,35000000.00,500000.00,5000000.00,28.00,18,NULL,90,'2025-11-02','2026-01-01',NULL,NULL,NULL,'en_recaudacion',1,'2025-10-29 02:34:32',NULL,NULL,'medio',1,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,0,NULL,'2025-12-03 02:34:32','2025-12-03 02:34:32',NULL),(2,'TRA-2025-002',2,3,'Producción de Café Especial - Antioquia','Cultivo de café especial de altura en 8 hectáreas con certificación orgánica.',NULL,NULL,NULL,NULL,'Antioquia, Colombia','6.2442,-75.5812',30000000.00,30000000.00,300000.00,3000000.00,32.00,16,NULL,120,'2025-10-03','2025-11-22',NULL,NULL,NULL,'fondeado',1,'2025-09-29 02:34:32',NULL,NULL,'bajo',1,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,0,NULL,'2025-12-03 02:34:32','2025-12-03 02:34:32',NULL),(3,'EAR-2025-003',3,3,'Cultivo de Limón Tahití - Santander','Proyecto de limón tahití para exportación, 12 hectáreas.',NULL,NULL,NULL,NULL,'Santander, Colombia','7.1301,-73.1197',40000000.00,15000000.00,400000.00,4000000.00,25.00,20,NULL,90,'2025-11-17','2026-01-16',NULL,NULL,NULL,'en_recaudacion',1,'2025-11-13 02:34:32',NULL,NULL,'medio',1,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,0,NULL,'2025-12-03 02:34:32','2025-12-03 02:34:32',NULL),(4,'STK-2025-004',1,3,'Producción de Plátano - Quindío','Proyecto de producción de plátano tradicional en 15 hectáreas con sistema de riego automatizado.',NULL,NULL,NULL,NULL,'Quindío, Colombia','4.4611,-75.6679',45000000.00,0.00,450000.00,4500000.00,24.00,14,NULL,90,'2026-03-02','2026-05-01',NULL,NULL,NULL,'rechazado',NULL,NULL,NULL,'mal','medio',0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,'2025-12-03 02:48:05','2025-12-03 20:33:07',NULL),(5,'STAKING-2025-001',1,3,'Limon Colombiano','El mejor limon',NULL,NULL,NULL,NULL,'Cali colombia','15,16',10000000.00,1000.00,1000.00,20000.00,10.00,12,6,30,'2025-12-04','2025-12-18','2025-12-24','2026-01-11','2026-01-23','en_recaudacion',1,'2025-12-03 20:40:18','Ahora si',NULL,'medio',1,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,0,NULL,'2025-12-03 15:48:30','2025-12-21 00:53:02',NULL),(6,'STAKING-2025-002',1,3,'lulo','Mejor lulo',NULL,NULL,NULL,NULL,'Cali colombia','15,16',500000.00,0.00,2000.00,10000.00,20.00,12,15,30,'2025-12-04','2025-12-19','2025-12-31','2026-01-11','2026-01-23','borrador',NULL,NULL,NULL,NULL,'bajo',0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,0,NULL,'2025-12-03 21:23:32','2025-12-03 21:23:32',NULL),(7,'STAKING-2025-003',1,3,'Pera','Mejor pera',NULL,NULL,NULL,NULL,'Cali colombia','15,16',10000000.00,0.00,10000.00,200000.00,10.00,12,6,30,'2025-12-04','2025-12-06','2025-12-17','2025-12-31','2026-01-10','en_revision',NULL,NULL,NULL,NULL,'medio',0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,0,NULL,'2025-12-03 21:28:02','2025-12-03 21:36:04',NULL),(8,'STAKING-2025-004',1,3,'Fresas','Mejores fresas',NULL,NULL,NULL,NULL,'Cali colombia','15,16',1000000.00,0.00,1000.00,500000.00,20.00,12,6,30,'2025-12-04','2025-12-06','2025-12-31','2026-01-11','2025-12-25','borrador',NULL,NULL,NULL,NULL,'medio',0,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,0,NULL,'2025-12-03 22:26:07','2025-12-03 22:26:07',NULL),(9,'STAKING-2025-005',1,3,'Fresas','Mejores fresas',NULL,NULL,NULL,NULL,'Cali colombia','15,16',1000000.00,0.00,1000.00,500000.00,20.00,12,6,30,'2025-12-04','2025-12-06','2025-12-31','2026-01-11','2025-12-25','en_recaudacion',1,'2025-12-03 22:28:58',NULL,NULL,'medio',1,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,0,NULL,'2025-12-03 22:26:08','2025-12-03 22:28:58',NULL),(11,'STAKING-2025-006',1,9,'Limon Colombiano','Limon','Limon',10.00,'siembra',2025,'Cali colombia',NULL,1000000.00,0.00,100000.00,NULL,15.00,12,NULL,30,'2025-12-15','2026-01-14',NULL,NULL,NULL,'en_revision',NULL,NULL,NULL,NULL,'medio',0,0,0,NULL,'Sacar limon','Largo','En un año','{\"inversion_solicitada\":{\"insumos\":\"500000\",\"mano_obra\":\"500\",\"equipos\":\"200\",\"transporte\":\"300\",\"certificaciones\":\"120\",\"empaques\":\"5000\",\"marketing\":\"60000\"},\"proyecciones\":{\"produccion_estimada\":\"100 de limon\",\"precio_venta_estimado\":\"200000\",\"canales_venta_actuales\":\"vecinos\",\"canales_venta_deseados\":\"todos\",\"proyeccion_ingresos\":\"muchois\",\"punto_equilibrio\":\"12 meses\",\"margen_ganancia\":\"25\"},\"riesgos\":{\"plagas\":\"Poco\",\"clima\":\"Medio\",\"competencia\":\"Mucha\",\"acceso_mercados\":\"masomenos\",\"regulaciones\":\"Ninguno\"}}',NULL,NULL,NULL,1,1,1,'2025-12-16 04:19:57','2025-12-16 04:27:41',NULL),(12,'STAKING-2025-007',1,9,'Camisetas Jakop🚀','hgfd','Limon',54.00,'cosecha',2025,'Cali colombia',NULL,121423.00,0.00,100000.00,NULL,23.00,31,NULL,30,'2025-12-15','2026-01-14',NULL,NULL,NULL,'borrador',NULL,NULL,NULL,NULL,'medio',0,0,0,NULL,'bfhgjgfh','jgh','jgh',NULL,NULL,NULL,NULL,1,1,1,'2025-12-16 04:53:19','2025-12-16 04:53:52',NULL),(13,'STAKING-2025-008',1,9,'lulo2','Descrip2','lulo2',10.00,'siembra',2025,'Cali colombia',NULL,100000000.00,0.00,100000.00,NULL,20.00,12,NULL,30,'2025-12-16','2026-01-15',NULL,NULL,NULL,'en_recaudacion',1,'2025-12-16 20:23:28',NULL,NULL,'medio',1,0,0,NULL,'mejorar','lulo','fecha','{\"inversion_solicitada\":{\"insumos\":\"64562\",\"mano_obra\":\"6475642\",\"equipos\":\"64562\",\"transporte\":\"64562\",\"certificaciones\":\"64562\",\"empaques\":\"64562\",\"marketing\":\"6452\"},\"proyecciones\":{\"produccion_estimada\":\"100 de limon\",\"precio_venta_estimado\":\"645\",\"canales_venta_actuales\":\"ni\",\"canales_venta_deseados\":\"nhg\",\"proyeccion_ingresos\":\"fdg\",\"punto_equilibrio\":\"gdf\",\"margen_ganancia\":\"20\"},\"riesgos\":{\"plagas\":\"gfd\",\"clima\":\"gdf\",\"competencia\":\"gfd\",\"acceso_mercados\":\"gdf\",\"regulaciones\":\"gdf\"}}',NULL,NULL,NULL,1,0,NULL,'2025-12-16 19:58:06','2025-12-16 20:23:28',NULL),(14,'STAKING-2025-009',1,9,'Limones','Descripcion','Limon',10.00,'siembra',2025,'Cali colombia','15,16',100000.00,0.00,100000.00,NULL,12.00,12,NULL,30,'2025-12-16','2026-01-15',NULL,NULL,NULL,'borrador',NULL,NULL,NULL,NULL,'medio',0,0,0,NULL,'Objetivo','Detalle','corno','{\"inversion_solicitada\":{\"insumos\":\"123\",\"mano_obra\":\"3213\",\"equipos\":\"321\",\"transporte\":\"321\",\"certificaciones\":\"321\",\"empaques\":\"131\",\"marketing\":\"321\"},\"proyecciones\":{\"produccion_estimada\":\"645\",\"precio_venta_estimado\":\"6456\",\"canales_venta_actuales\":\"654\",\"canales_venta_deseados\":\"56+\",\"proyeccion_ingresos\":\"+56\",\"punto_equilibrio\":\"+56\",\"margen_ganancia\":\"45\"},\"riesgos\":{\"plagas\":\"ttre\",\"clima\":\"tre\",\"competencia\":\"tre\",\"acceso_mercados\":\"rew\",\"regulaciones\":\"ter\"}}',NULL,NULL,NULL,1,1,1,'2025-12-16 22:10:23','2025-12-16 22:16:44',NULL);
/*!40000 ALTER TABLE `proyectos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proyectos_cross_fund`
--

DROP TABLE IF EXISTS `proyectos_cross_fund`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proyectos_cross_fund` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `paquete_id` bigint(20) unsigned NOT NULL,
  `proyecto_id` bigint(20) unsigned NOT NULL,
  `porcentaje_asignacion` decimal(5,2) NOT NULL,
  `monto_asignado` decimal(15,2) NOT NULL,
  `orden` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `proyectos_cross_fund_paquete_id_proyecto_id_unique` (`paquete_id`,`proyecto_id`),
  KEY `proyectos_cross_fund_proyecto_id_foreign` (`proyecto_id`),
  KEY `proyectos_cross_fund_paquete_id_index` (`paquete_id`),
  CONSTRAINT `proyectos_cross_fund_paquete_id_foreign` FOREIGN KEY (`paquete_id`) REFERENCES `paquetes_cross_fund` (`id`) ON DELETE CASCADE,
  CONSTRAINT `proyectos_cross_fund_proyecto_id_foreign` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proyectos_cross_fund`
--

LOCK TABLES `proyectos_cross_fund` WRITE;
/*!40000 ALTER TABLE `proyectos_cross_fund` DISABLE KEYS */;
/*!40000 ALTER TABLE `proyectos_cross_fund` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reglas_penalizacion`
--

DROP TABLE IF EXISTS `reglas_penalizacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reglas_penalizacion` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `categoria_id` bigint(20) unsigned NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo_penalizacion` enum('porcentaje_fijo','porcentaje_variable','dias_retencion') NOT NULL,
  `valor` decimal(5,2) NOT NULL,
  `aplica_desde_mes` int(11) NOT NULL,
  `aplica_hasta_mes` int(11) NOT NULL,
  `pierde_capital` tinyint(1) NOT NULL DEFAULT 0,
  `pierde_dividendos` tinyint(1) NOT NULL DEFAULT 1,
  `permite_venta_posicion` tinyint(1) NOT NULL DEFAULT 1,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `orden` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reglas_penalizacion_categoria_id_index` (`categoria_id`),
  KEY `reglas_penalizacion_activo_index` (`activo`),
  CONSTRAINT `reglas_penalizacion_categoria_id_foreign` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_proyecto` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reglas_penalizacion`
--

LOCK TABLES `reglas_penalizacion` WRITE;
/*!40000 ALTER TABLE `reglas_penalizacion` DISABLE KEYS */;
/*!40000 ALTER TABLE `reglas_penalizacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reportes`
--

DROP TABLE IF EXISTS `reportes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reportes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codigo_reporte` varchar(50) NOT NULL,
  `generado_por` bigint(20) unsigned NOT NULL,
  `tipo_reporte` enum('inversiones','proyectos','usuarios','dividendos','transacciones','financiero','kyc','comercial','personalizado') NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `filtros` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`filtros`)),
  `columnas` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`columnas`)),
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `formato` varchar(20) NOT NULL,
  `ruta_archivo` varchar(1000) DEFAULT NULL,
  `estado` enum('generando','completado','error') NOT NULL DEFAULT 'generando',
  `mensaje_error` text DEFAULT NULL,
  `generado_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `expira_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reportes_codigo_reporte_unique` (`codigo_reporte`),
  KEY `reportes_codigo_reporte_index` (`codigo_reporte`),
  KEY `reportes_generado_por_index` (`generado_por`),
  KEY `reportes_tipo_reporte_index` (`tipo_reporte`),
  KEY `reportes_estado_index` (`estado`),
  KEY `reportes_generado_at_index` (`generado_at`),
  CONSTRAINT `reportes_generado_por_foreign` FOREIGN KEY (`generado_por`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reportes`
--

LOCK TABLES `reportes` WRITE;
/*!40000 ALTER TABLE `reportes` DISABLE KEYS */;
/*!40000 ALTER TABLE `reportes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `retiros`
--

DROP TABLE IF EXISTS `retiros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `retiros` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codigo_retiro` varchar(50) NOT NULL,
  `usuario_id` bigint(20) unsigned NOT NULL,
  `monto_solicitado` decimal(15,2) NOT NULL,
  `monto_aprobado` decimal(15,2) DEFAULT NULL,
  `comision` decimal(15,2) NOT NULL DEFAULT 0.00,
  `monto_neto` decimal(15,2) DEFAULT NULL,
  `metodo_pago` enum('transferencia_bancaria','nequi','daviplata','otro') NOT NULL,
  `datos_pago` text NOT NULL,
  `fecha_solicitud` date NOT NULL,
  `fecha_aprobacion` date DEFAULT NULL,
  `fecha_rechazo` date DEFAULT NULL,
  `fecha_pago` date DEFAULT NULL,
  `estado` enum('pendiente','en_revision','aprobado','rechazado','pagado','cancelado') NOT NULL DEFAULT 'pendiente',
  `aprobado_por` bigint(20) unsigned DEFAULT NULL,
  `pagado_por` bigint(20) unsigned DEFAULT NULL,
  `notas_aprobacion` text DEFAULT NULL,
  `motivo_rechazo` text DEFAULT NULL,
  `comprobante_pago` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `retiros_codigo_retiro_unique` (`codigo_retiro`),
  KEY `retiros_aprobado_por_foreign` (`aprobado_por`),
  KEY `retiros_pagado_por_foreign` (`pagado_por`),
  KEY `retiros_codigo_retiro_index` (`codigo_retiro`),
  KEY `retiros_usuario_id_index` (`usuario_id`),
  KEY `retiros_estado_index` (`estado`),
  KEY `retiros_fecha_solicitud_index` (`fecha_solicitud`),
  CONSTRAINT `retiros_aprobado_por_foreign` FOREIGN KEY (`aprobado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `retiros_pagado_por_foreign` FOREIGN KEY (`pagado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `retiros_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `retiros`
--

LOCK TABLES `retiros` WRITE;
/*!40000 ALTER TABLE `retiros` DISABLE KEYS */;
INSERT INTO `retiros` VALUES (1,'RET-2025-00001',4,500000.00,500000.00,0.00,500000.00,'transferencia_bancaria','{\"banco\":\"Bancolombia\",\"tipo_cuenta\":\"ahorros\",\"numero_cuenta\":\"****5678\",\"titular\":\"Inversionista Test\"}','2025-11-30',NULL,'2025-12-21',NULL,'rechazado',1,NULL,NULL,'esta mal el compro',NULL,'2025-12-03 02:35:14','2025-12-22 02:14:06',NULL),(2,'RET-2025-00002',4,300000.00,300000.00,0.00,300000.00,'transferencia_bancaria','{\"banco\":\"Bancolombia\",\"tipo_cuenta\":\"ahorros\",\"numero_cuenta\":\"****5678\",\"titular\":\"Inversionista Test\"}','2025-11-27','2025-11-28','2025-12-21',NULL,'rechazado',1,NULL,NULL,'sdfsfsdf daqsda',NULL,'2025-12-03 02:35:14','2025-12-22 02:14:39',NULL),(3,'RET-20251221-00001',4,50000.00,NULL,0.00,NULL,'transferencia_bancaria','{\"titular\":\"Mar\\u00eda Inversionista L\\u00f3pez\",\"numero_cuenta\":\"1231321313213\",\"banco\":\"Bancolombia\",\"tipo_cuenta\":\"ahorros\"}','2025-12-21',NULL,'2025-12-21',NULL,'rechazado',1,NULL,'llegue','ddfgdfgdfgdf',NULL,'2025-12-22 02:15:37','2025-12-22 02:17:13',NULL),(4,'RET-20251221-00002',4,50000.00,50000.00,0.00,50000.00,'transferencia_bancaria','{\"titular\":\"Mar\\u00eda Inversionista L\\u00f3pez\",\"numero_cuenta\":\"1231321313213\",\"banco\":\"Bancolombia\",\"tipo_cuenta\":\"ahorros\"}','2025-12-21','2025-12-21',NULL,'2025-12-21','pagado',1,1,'listo',NULL,'uploads/retiros/4/comprobante_1766370001.pdf','2025-12-22 02:19:15','2025-12-22 02:20:01',NULL),(5,'RET-20251221-00003',4,848000.00,848000.00,0.00,848000.00,'nequi','{\"titular\":\"Mar\\u00eda Inversionista L\\u00f3pez\",\"numero_cuenta\":\"3202230467\"}','2025-12-21','2025-12-21',NULL,'2025-12-21','pagado',1,1,'ya',NULL,'uploads/retiros/5/comprobante_1766370083.pdf','2025-12-22 02:21:02','2025-12-22 02:21:23',NULL);
/*!40000 ALTER TABLE `retiros` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES (1,1),(1,2),(1,3),(1,4),(2,2),(2,3),(3,2),(3,3),(4,3),(4,4),(5,3),(6,1),(6,3),(6,4),(7,1),(7,3),(8,3),(8,4),(9,3),(9,5),(10,3),(10,5),(11,3),(12,3),(13,3),(13,4),(14,3),(14,4),(15,3),(15,4),(16,3),(16,4),(17,3),(17,4),(18,3),(19,3),(19,4),(20,3),(20,4),(21,3),(21,5),(22,3),(22,4),(23,3),(23,4),(24,3);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Inversionista','web','2025-12-02 21:02:59','2025-12-02 21:02:59'),(2,'Agricultor','web','2025-12-02 21:02:59','2025-12-02 21:02:59'),(3,'Administrador','web','2025-12-02 21:02:59','2025-12-02 21:02:59'),(4,'Supervisor','web','2025-12-02 21:02:59','2025-12-02 21:02:59'),(5,'Vendedor','web','2025-12-02 21:02:59','2025-12-02 21:02:59');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transacciones_billetera`
--

DROP TABLE IF EXISTS `transacciones_billetera`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transacciones_billetera` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codigo_transaccion` varchar(50) NOT NULL,
  `billetera_id` bigint(20) unsigned NOT NULL,
  `usuario_id` bigint(20) unsigned NOT NULL,
  `tipo` enum('deposito','retiro','inversion','dividendo','retorno_capital','venta_trading','compra_trading','comision','reversa','ajuste') NOT NULL,
  `monto` decimal(15,2) NOT NULL,
  `naturaleza` enum('credito','debito') NOT NULL,
  `saldo_anterior` decimal(15,2) NOT NULL,
  `saldo_posterior` decimal(15,2) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `referencia_externa` varchar(100) DEFAULT NULL,
  `referencia_id` bigint(20) unsigned DEFAULT NULL,
  `referencia_type` varchar(100) DEFAULT NULL,
  `procesado_por` bigint(20) unsigned DEFAULT NULL,
  `fecha_transaccion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transacciones_billetera_codigo_transaccion_unique` (`codigo_transaccion`),
  KEY `transacciones_billetera_procesado_por_foreign` (`procesado_por`),
  KEY `transacciones_billetera_codigo_transaccion_index` (`codigo_transaccion`),
  KEY `transacciones_billetera_billetera_id_index` (`billetera_id`),
  KEY `transacciones_billetera_usuario_id_index` (`usuario_id`),
  KEY `transacciones_billetera_tipo_index` (`tipo`),
  KEY `transacciones_billetera_fecha_transaccion_index` (`fecha_transaccion`),
  KEY `transacciones_billetera_referencia_id_referencia_type_index` (`referencia_id`,`referencia_type`),
  CONSTRAINT `transacciones_billetera_billetera_id_foreign` FOREIGN KEY (`billetera_id`) REFERENCES `billeteras` (`id`),
  CONSTRAINT `transacciones_billetera_procesado_por_foreign` FOREIGN KEY (`procesado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transacciones_billetera_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transacciones_billetera`
--

LOCK TABLES `transacciones_billetera` WRITE;
/*!40000 ALTER TABLE `transacciones_billetera` DISABLE KEYS */;
INSERT INTO `transacciones_billetera` VALUES (1,'TRX-20251105-A7145',1,4,'deposito',5000000.00,'credito',0.00,5000000.00,'Depósito inicial - Transferencia bancaria',NULL,NULL,NULL,NULL,'2025-11-05 23:03:57','2025-11-05 23:03:57','2025-11-05 23:03:57'),(2,'TRX-20251110-A9653',1,4,'inversion',2000000.00,'debito',5000000.00,3000000.00,'Inversión en Proyecto Café Premium Huila',NULL,NULL,NULL,NULL,'2025-11-10 23:03:57','2025-11-10 23:03:57','2025-11-10 23:03:57'),(3,'TRX-20251115-AA261',1,4,'deposito',3000000.00,'credito',3000000.00,6000000.00,'Depósito - PSE Bancolombia',NULL,NULL,NULL,NULL,'2025-11-15 23:03:57','2025-11-15 23:03:57','2025-11-15 23:03:57'),(4,'TRX-20251120-B2DC6',1,4,'inversion',1500000.00,'debito',6000000.00,4500000.00,'Inversión en Proyecto Aguacate Hass Antioquia',NULL,NULL,NULL,NULL,'2025-11-20 23:03:57','2025-11-20 23:03:57','2025-11-20 23:03:57'),(5,'TRX-20251125-B3AE7',1,4,'dividendo',125000.00,'credito',4500000.00,4625000.00,'Dividendo Q4 - Café Premium Huila',NULL,NULL,NULL,NULL,'2025-11-25 23:03:57','2025-11-25 23:03:57','2025-11-25 23:03:57'),(6,'TRX-20251130-B472B',1,4,'inversion',2500000.00,'debito',4625000.00,2125000.00,'Inversión en Proyecto Cacao Tumaco',NULL,NULL,NULL,NULL,'2025-11-30 23:03:57','2025-11-30 23:03:57','2025-11-30 23:03:57'),(7,'TRX-20251205-B547F',1,4,'deposito',2000000.00,'credito',2125000.00,4125000.00,'Depósito - Nequi',NULL,NULL,NULL,NULL,'2025-12-05 23:03:57','2025-12-05 23:03:57','2025-12-05 23:03:57'),(8,'TRX-20251210-B6226',1,4,'dividendo',95000.00,'credito',4125000.00,4220000.00,'Dividendo mensual - Aguacate Hass',NULL,NULL,NULL,NULL,'2025-12-10 23:03:57','2025-12-10 23:03:57','2025-12-10 23:03:57'),(9,'TRX-20251213-B6DE3',1,4,'retiro',500000.00,'debito',4220000.00,3720000.00,'Retiro a cuenta bancaria ****4532',NULL,NULL,NULL,NULL,'2025-12-13 23:03:57','2025-12-13 23:03:57','2025-12-13 23:03:57'),(10,'TRX-20251215-B79AE',1,4,'dividendo',180000.00,'credito',3720000.00,3900000.00,'Dividendo extraordinario - Cacao Tumaco',NULL,NULL,NULL,NULL,'2025-12-15 23:03:57','2025-12-15 23:03:57','2025-12-15 23:03:57'),(11,'TRX-20251218-B8410',1,4,'deposito',1000000.00,'credito',3900000.00,4900000.00,'Depósito - Daviplata',NULL,NULL,NULL,NULL,'2025-12-18 23:03:57','2025-12-18 23:03:57','2025-12-18 23:03:57'),(12,'TRX-20251220-EE4F8',1,4,'inversion',1000.00,'debito',4900000.00,4899000.00,'Inversión en Limon Colombiano - INV-2025-000003',NULL,3,'App\\Models\\Inversion',NULL,'2025-12-21 00:53:02','2025-12-21 00:53:02','2025-12-21 00:53:02'),(13,'TRX-20251221-792B6',1,4,'dividendo',8.22,'credito',4898000.00,4898008.22,'Dividendo período 1 - Limon Colombiano',NULL,3,'App\\Models\\Dividendo',NULL,'2025-12-22 00:47:00','2025-12-22 00:47:00','2025-12-22 00:47:00'),(14,'TRX-20251221-AD6EF',1,4,'retiro',50000.00,'debito',50000.00,0.00,'Retiro RET-20251221-00002 - transferencia_bancaria',NULL,4,'App\\Models\\Retiro',1,'2025-12-22 02:20:01','2025-12-22 02:20:01','2025-12-22 02:20:01'),(15,'TRX-20251221-A0A1D',1,4,'retiro',848000.00,'debito',848000.00,0.00,'Retiro RET-20251221-00003 - nequi',NULL,5,'App\\Models\\Retiro',1,'2025-12-22 02:21:23','2025-12-22 02:21:23','2025-12-22 02:21:23');
/*!40000 ALTER TABLE `transacciones_billetera` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transacciones_inversion`
--

DROP TABLE IF EXISTS `transacciones_inversion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transacciones_inversion` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codigo_transaccion` varchar(50) NOT NULL,
  `inversion_id` bigint(20) unsigned NOT NULL,
  `vendedor_id` bigint(20) unsigned NOT NULL,
  `comprador_id` bigint(20) unsigned NOT NULL,
  `monto_venta` decimal(15,2) NOT NULL,
  `valor_libro` decimal(15,2) NOT NULL,
  `ganancia_perdida` decimal(15,2) NOT NULL,
  `comision_plataforma` decimal(15,2) NOT NULL,
  `fecha_transaccion` date NOT NULL,
  `estado` enum('pendiente','completada','cancelada') NOT NULL DEFAULT 'pendiente',
  `notas` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transacciones_inversion_codigo_transaccion_unique` (`codigo_transaccion`),
  KEY `transacciones_inversion_inversion_id_index` (`inversion_id`),
  KEY `transacciones_inversion_vendedor_id_index` (`vendedor_id`),
  KEY `transacciones_inversion_comprador_id_index` (`comprador_id`),
  KEY `transacciones_inversion_fecha_transaccion_index` (`fecha_transaccion`),
  CONSTRAINT `transacciones_inversion_comprador_id_foreign` FOREIGN KEY (`comprador_id`) REFERENCES `users` (`id`),
  CONSTRAINT `transacciones_inversion_inversion_id_foreign` FOREIGN KEY (`inversion_id`) REFERENCES `inversiones` (`id`),
  CONSTRAINT `transacciones_inversion_vendedor_id_foreign` FOREIGN KEY (`vendedor_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transacciones_inversion`
--

LOCK TABLES `transacciones_inversion` WRITE;
/*!40000 ALTER TABLE `transacciones_inversion` DISABLE KEYS */;
/*!40000 ALTER TABLE `transacciones_inversion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `ultimo_login` timestamp NULL DEFAULT NULL,
  `documento_identidad` varchar(50) DEFAULT NULL,
  `tipo_documento` enum('CC','CE','NIT','PASSPORT','DNI') DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `pais` varchar(100) DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `foto_perfil` varchar(500) DEFAULT NULL,
  `kyc_status` enum('pendiente','en_revision','aprobado','rechazado') NOT NULL DEFAULT 'pendiente',
  `kyc_aprobado_at` timestamp NULL DEFAULT NULL,
  `kyc_aprobado_por` bigint(20) unsigned DEFAULT NULL,
  `kyc_notas` text DEFAULT NULL,
  `codigo_referido` varchar(20) DEFAULT NULL,
  `referido_por` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `creado_por_admin` tinyint(1) NOT NULL DEFAULT 0,
  `admin_creador_id` bigint(20) unsigned DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_documento_identidad_unique` (`documento_identidad`),
  UNIQUE KEY `users_codigo_referido_unique` (`codigo_referido`),
  KEY `users_activo_index` (`activo`),
  KEY `users_kyc_status_index` (`kyc_status`),
  KEY `users_codigo_referido_index` (`codigo_referido`),
  KEY `users_referido_por_index` (`referido_por`),
  KEY `users_kyc_aprobado_por_foreign` (`kyc_aprobado_por`),
  KEY `users_admin_creador_id_foreign` (`admin_creador_id`),
  KEY `users_creado_por_admin_index` (`creado_por_admin`),
  CONSTRAINT `users_admin_creador_id_foreign` FOREIGN KEY (`admin_creador_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_kyc_aprobado_por_foreign` FOREIGN KEY (`kyc_aprobado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_referido_por_foreign` FOREIGN KEY (`referido_por`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrador Principal','admin@agromarket.com','3001234567',1,'2025-12-21 23:57:48','1000000001','CC',NULL,'CO','Bogotá',NULL,NULL,'aprobado','2025-12-02 21:03:07',NULL,NULL,NULL,NULL,NULL,0,NULL,'2025-12-02 21:03:07','$2y$10$nZS77kA.7f.Jut7bhQQts.KFskX8.cMAel7A/3CU/ejDFaiig5roW',NULL,'2025-12-02 21:03:07','2025-12-21 23:57:48'),(2,'Supervisor de Proyectos','supervisor@agromarket.com','3001234568',1,'2025-12-03 21:51:41','1000000002','CC',NULL,'CO','Medellín',NULL,NULL,'aprobado','2025-12-02 21:03:07',NULL,NULL,NULL,NULL,NULL,0,NULL,'2025-12-02 21:03:07','$2y$10$.JpJsAJGsTPvn4fXE51vTOlRmpW8kr2NOCCCV7/.2ZNZf9ohSwe4.',NULL,'2025-12-02 21:03:07','2025-12-03 21:51:41'),(3,'Carlos Agricultor Ramírez','agricultor@agromarket.com','3001234569',1,'2025-12-03 22:19:03','1000000003','CC',NULL,'CO','Cali',NULL,NULL,'aprobado','2025-12-02 21:03:07',NULL,NULL,NULL,NULL,NULL,0,NULL,'2025-12-02 21:03:07','$2y$10$.KSSjVSLTAU86NMHloOCbuEcpxQHb2DGIypjv201qqZ0y4f8.OoJ.',NULL,'2025-12-02 21:03:07','2025-12-03 22:19:03'),(4,'María Inversionista López','inversionista@agromarket.com','3001234570',1,'2025-12-21 23:47:26','1000000004','CC','1985-05-15','CO','Barranquilla','Calle 100 # 20-30',NULL,'aprobado','2025-12-02 21:03:07',1,NULL,NULL,NULL,NULL,0,NULL,'2025-12-02 21:03:07','$2y$10$wB7jDW4bV7/GVKzfpkY2wea2vIP6F/o6UaGR3Nv9.swM4.QTt4pT2',NULL,'2025-12-02 21:03:07','2025-12-21 23:47:26'),(5,'Juan Inversionista Sin KYC','inversionista.pendiente@agromarket.com','3001234571',1,NULL,'1000000005','CC',NULL,'CO','Cartagena',NULL,NULL,'pendiente',NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,'2025-12-02 21:03:07','$2y$10$W8eqw2RXJ4Gnlx9Rcf.0P.LrAZkyz6idtqtGqEq9jxa7lOsQcFwU6',NULL,'2025-12-02 21:03:07','2025-12-02 21:03:07'),(6,'Pedro Vendedor García','vendedor@agromarket.com','3001234572',1,'2025-12-03 22:33:31','1000000006','CC',NULL,'CO','Bucaramanga',NULL,NULL,'aprobado','2025-12-02 21:03:07',NULL,NULL,NULL,NULL,NULL,0,NULL,'2025-12-02 21:03:07','$2y$10$W.xb8C91d3hEOi8.M2USNuWe054f0P4JegCex44MOmX0VX0LlMCpu',NULL,'2025-12-02 21:03:07','2025-12-03 22:33:31'),(9,'Santi','vblogsanti@gmail.com','+573202230467',1,'2025-12-16 19:45:46','1121945370','CC','1997-05-17','Colombia','Bogotá','Calle 69 #10-15\r\ncasa',NULL,'pendiente',NULL,NULL,NULL,NULL,NULL,NULL,1,1,NULL,'$2y$10$gl48sYAixfGDWLmm5.o6j.B0pSUj6PsPqUNL6oM.nSUjyUb5yOgWO',NULL,'2025-12-16 04:19:57','2025-12-16 19:45:46'),(11,'Santi Bellaizan','anonyb001@hotmail.com',NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'aprobado','2025-12-19 23:45:07',1,'Mal',NULL,NULL,NULL,0,NULL,NULL,'$2y$10$e2jo7v/VoSnEByuhO9jTVeObwBGJ0ZiD7HIw/FjBtUPs0xTQVVsdS',NULL,'2025-12-19 22:52:59','2025-12-19 23:45:07');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'agromarket'
--

--
-- Dumping routines for database 'agromarket'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-11 15:10:59
