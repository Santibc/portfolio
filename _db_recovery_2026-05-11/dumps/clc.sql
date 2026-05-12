-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: clc
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
-- Current Database: `clc`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `clc` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `clc`;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clientes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tipo` enum('nacional','internacional') NOT NULL DEFAULT 'nacional',
  `tipo_identificacion` varchar(20) DEFAULT NULL,
  `identificacion` varchar(50) DEFAULT NULL,
  `nombre` varchar(200) NOT NULL,
  `nombre_comercial` varchar(200) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `telefono` varchar(40) DEFAULT NULL,
  `direccion_facturacion` varchar(255) DEFAULT NULL,
  `direccion_envio` varchar(255) DEFAULT NULL,
  `pais` varchar(80) NOT NULL DEFAULT 'Colombia',
  `ciudad` varchar(100) DEFAULT NULL,
  `moneda_preferida_id` bigint(20) unsigned DEFAULT NULL,
  `incoterm_id` bigint(20) unsigned DEFAULT NULL,
  `puerto_id` bigint(20) unsigned DEFAULT NULL,
  `tipo_pago_id` bigint(20) unsigned DEFAULT NULL,
  `idioma_documento` enum('es','en') NOT NULL DEFAULT 'es',
  `plantilla_factura_id` bigint(20) unsigned DEFAULT NULL,
  `datos_bancarios_destino` text DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `siigo_id` varchar(100) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `clientes_moneda_preferida_id_foreign` (`moneda_preferida_id`),
  KEY `clientes_incoterm_id_foreign` (`incoterm_id`),
  KEY `clientes_puerto_id_foreign` (`puerto_id`),
  KEY `clientes_tipo_pago_id_foreign` (`tipo_pago_id`),
  KEY `clientes_tipo_index` (`tipo`),
  KEY `clientes_activo_tipo_index` (`activo`,`tipo`),
  KEY `clientes_identificacion_index` (`identificacion`),
  KEY `clientes_plantilla_factura_id_foreign` (`plantilla_factura_id`),
  CONSTRAINT `clientes_incoterm_id_foreign` FOREIGN KEY (`incoterm_id`) REFERENCES `incoterms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `clientes_moneda_preferida_id_foreign` FOREIGN KEY (`moneda_preferida_id`) REFERENCES `monedas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `clientes_plantilla_factura_id_foreign` FOREIGN KEY (`plantilla_factura_id`) REFERENCES `plantillas_factura` (`id`) ON DELETE SET NULL,
  CONSTRAINT `clientes_puerto_id_foreign` FOREIGN KEY (`puerto_id`) REFERENCES `puertos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `clientes_tipo_pago_id_foreign` FOREIGN KEY (`tipo_pago_id`) REFERENCES `tipos_pago` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (1,'internacional','NIT','654654654','Santi','Santi ltda','vblogsanti@gmail.com','3202230467','Cl. 69 #10-15','Cl. 69 #10-15','Colombia','Bogotá',1,1,1,1,'en',NULL,'Banco','Notas',NULL,1,'2026-04-21 21:44:04','2026-04-21 21:44:04');
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configuraciones`
--

DROP TABLE IF EXISTS `configuraciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configuraciones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `clave` varchar(150) NOT NULL,
  `valor` text DEFAULT NULL,
  `tipo` enum('string','integer','boolean','json','text') NOT NULL DEFAULT 'string',
  `grupo` varchar(60) NOT NULL DEFAULT 'general',
  `descripcion` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `configuraciones_clave_unique` (`clave`),
  KEY `configuraciones_grupo_clave_index` (`grupo`,`clave`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configuraciones`
--

LOCK TABLES `configuraciones` WRITE;
/*!40000 ALTER TABLE `configuraciones` DISABLE KEYS */;
INSERT INTO `configuraciones` VALUES (1,'empresa.razon_social','CLC & CIA S.A.S.','string','empresa',NULL,'2026-04-21 21:22:34','2026-04-21 21:35:02'),(2,'empresa.nit','901249576-9','string','empresa',NULL,'2026-04-21 21:22:34','2026-04-21 21:35:02'),(3,'empresa.direccion','Cr 4 No. 4-43 oficina 302','string','empresa',NULL,'2026-04-21 21:22:34','2026-04-21 21:35:02'),(4,'empresa.telefono','89 36 527','string','empresa',NULL,'2026-04-21 21:22:34','2026-04-21 21:35:02'),(5,'empresa.email','jsrojas@caladelacruz','string','empresa',NULL,'2026-04-21 21:22:34','2026-04-21 21:35:02'),(6,'empresa.sitio_web','www.caladelacruz.com','string','empresa',NULL,'2026-04-21 21:22:34','2026-04-21 21:35:02'),(7,'empresa.logo_path','uploads/empresa/logo-7c9604f8-a593-4fca-80da-fc044640d211.png','string','empresa',NULL,'2026-04-21 21:22:34','2026-04-21 21:35:03'),(8,'empresa.regimen_tributario','IVA RÉGIMEN COMÚN – NO SOMOS RETENEDORES DE IVA – NO SOMOS GRANDES CONTRIBUYENTES','text','empresa',NULL,'2026-04-21 21:22:34','2026-04-21 21:35:02'),(9,'dian.resolucion_texto_clc','Factura Electrónica de Venta código 4, prefijo CLC desde 1 hasta 3000 vigencia 24 tipo de solicitud autorización código 1 Resolución 18764084396801 de 2024/11/29','text','dian',NULL,'2026-04-21 21:22:34','2026-04-21 21:35:03'),(10,'dian.resolucion_texto_fv','Factura Electrónica de Venta código 4, prefijo FV desde 1 hasta 1500 vigencia 24 tipo de solicitud autorización código 1 Resolución 18764088482186 de 2025/02/06','text','dian',NULL,'2026-04-21 21:22:34','2026-04-21 21:35:03'),(11,'banco.nombre','BANCOLOMBIA','string','banco',NULL,'2026-04-21 21:22:34','2026-04-21 21:35:03'),(12,'banco.pais','COLOMBIA','string','banco',NULL,'2026-04-21 21:22:34','2026-04-21 21:35:03'),(13,'banco.direccion','Avenida 8 Norte # 12 - 43','string','banco',NULL,'2026-04-21 21:22:34','2026-04-21 21:35:03'),(14,'banco.titular','CLC Y CIA SAS','string','banco',NULL,'2026-04-21 21:22:34','2026-04-21 21:35:03'),(15,'banco.moneda','PESOS COLOMBIANOS','string','banco',NULL,'2026-04-21 21:22:34','2026-04-21 21:35:03'),(16,'banco.swift','COLOCOBM, COLOCOBMXXX','string','banco',NULL,'2026-04-21 21:22:34','2026-04-21 21:35:03'),(17,'banco.numero_cuenta','96700000418','string','banco',NULL,'2026-04-21 21:22:34','2026-04-21 21:35:03'),(18,'contacto_financiero.nombre','Juan Sebastián Rojas','string','contacto',NULL,'2026-04-21 21:22:34','2026-04-21 21:35:03'),(19,'contacto_financiero.email','jsrojas@caladelacruz','string','contacto',NULL,'2026-04-21 21:22:34','2026-04-21 21:35:03'),(20,'contacto_financiero.telefono','+57 302 2285789','string','contacto',NULL,'2026-04-21 21:22:34','2026-04-21 21:35:03'),(21,'facturacion.prefijo_interno','REM','string','facturacion','Prefijo del consecutivo interno para remisiones (facturas no electrónicas)','2026-04-21 21:22:34','2026-04-21 21:22:34'),(22,'facturacion.consecutivo_interno','0','integer','facturacion','Último consecutivo interno asignado','2026-04-21 21:22:34','2026-04-21 21:22:34');
/*!40000 ALTER TABLE `configuraciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `factura_items`
--

DROP TABLE IF EXISTS `factura_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `factura_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `factura_id` bigint(20) unsigned NOT NULL,
  `producto_id` bigint(20) unsigned DEFAULT NULL,
  `referencia` varchar(40) NOT NULL,
  `descripcion` varchar(200) NOT NULL,
  `color` varchar(60) DEFAULT NULL,
  `composicion` varchar(255) DEFAULT NULL,
  `codigo_pa` varchar(20) DEFAULT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `precio_unitario` decimal(14,2) NOT NULL,
  `descuento` decimal(14,2) NOT NULL DEFAULT 0.00,
  `impuesto_porcentaje` decimal(5,2) NOT NULL DEFAULT 0.00,
  `total_linea` decimal(16,2) NOT NULL,
  `tallas_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tallas_json`)),
  `orden` smallint(6) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `factura_items_producto_id_foreign` (`producto_id`),
  KEY `factura_items_factura_id_index` (`factura_id`),
  CONSTRAINT `factura_items_factura_id_foreign` FOREIGN KEY (`factura_id`) REFERENCES `facturas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `factura_items_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `factura_items`
--

LOCK TABLES `factura_items` WRITE;
/*!40000 ALTER TABLE `factura_items` DISABLE KEYS */;
INSERT INTO `factura_items` VALUES (1,1,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,0,'2026-04-21 21:48:00','2026-04-21 21:48:00'),(2,1,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',2.00,100000.00,0.00,19.00,238000.00,NULL,1,'2026-04-21 21:48:00','2026-04-21 21:48:00'),(5,2,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',3.00,100000.00,0.00,0.00,300000.00,NULL,0,'2026-04-22 02:55:11','2026-04-22 02:55:11'),(6,2,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,20.00,120000.00,NULL,1,'2026-04-22 02:55:11','2026-04-22 02:55:11'),(7,3,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,0,'2026-04-22 02:56:56','2026-04-22 02:56:56'),(8,4,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,0,'2026-04-22 14:23:58','2026-04-22 14:23:58'),(9,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,0,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(10,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,1,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(11,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,2,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(12,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,3,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(13,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,4,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(14,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,5,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(15,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,6,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(16,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,7,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(17,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,8,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(18,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,9,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(19,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,10,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(20,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,11,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(21,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,12,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(22,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,13,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(23,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,14,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(24,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,15,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(25,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,16,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(26,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,17,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(27,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,18,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(28,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,19,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(29,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,20,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(30,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,21,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(31,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,22,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(32,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,23,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(33,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,24,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(34,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,25,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(35,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,26,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(36,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,27,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(37,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,28,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(38,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,29,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(39,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,30,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(40,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,31,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(41,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,32,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(42,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,33,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(43,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,34,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(44,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,35,'2026-04-22 14:36:22','2026-04-22 14:36:22'),(45,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,36,'2026-04-22 14:36:23','2026-04-22 14:36:23'),(46,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,37,'2026-04-22 14:36:23','2026-04-22 14:36:23'),(47,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,38,'2026-04-22 14:36:23','2026-04-22 14:36:23'),(48,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,39,'2026-04-22 14:36:23','2026-04-22 14:36:23'),(49,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,40,'2026-04-22 14:36:23','2026-04-22 14:36:23'),(50,5,1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',1.00,100000.00,0.00,19.00,119000.00,NULL,41,'2026-04-22 14:36:23','2026-04-22 14:36:23');
/*!40000 ALTER TABLE `factura_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facturas`
--

DROP TABLE IF EXISTS `facturas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `facturas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `numero_interno` varchar(30) NOT NULL,
  `numero_siigo` varchar(30) DEFAULT NULL,
  `cufe` varchar(150) DEFAULT NULL,
  `qr_html` text DEFAULT NULL,
  `qr_url` varchar(500) DEFAULT NULL,
  `siigo_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`siigo_response`)),
  `stamp_status` varchar(30) DEFAULT NULL,
  `siigo_id` varchar(64) DEFAULT NULL,
  `xml_firmado_path` varchar(255) DEFAULT NULL,
  `fecha` date NOT NULL,
  `vencimiento` date DEFAULT NULL,
  `cliente_id` bigint(20) unsigned NOT NULL,
  `moneda_id` bigint(20) unsigned NOT NULL,
  `tasa_cambio` decimal(14,4) DEFAULT NULL,
  `subtotal` decimal(16,2) NOT NULL DEFAULT 0.00,
  `descuento_total` decimal(16,2) NOT NULL DEFAULT 0.00,
  `iva_total` decimal(16,2) NOT NULL DEFAULT 0.00,
  `flete` decimal(16,2) NOT NULL DEFAULT 0.00,
  `seguro` decimal(16,2) NOT NULL DEFAULT 0.00,
  `total` decimal(16,2) NOT NULL DEFAULT 0.00,
  `total_cop` decimal(16,2) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `po_numero` varchar(60) DEFAULT NULL,
  `awb` varchar(60) DEFAULT NULL,
  `shipper` varchar(100) DEFAULT NULL,
  `estado` enum('borrador','emitida','enviada','pagada','anulada') NOT NULL DEFAULT 'borrador',
  `es_electronica` tinyint(1) NOT NULL DEFAULT 0,
  `plantilla_factura_id` bigint(20) unsigned DEFAULT NULL,
  `pdf_path` varchar(255) DEFAULT NULL,
  `token_publico` varchar(64) NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `emitida_at` timestamp NULL DEFAULT NULL,
  `enviada_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `facturas_numero_interno_unique` (`numero_interno`),
  UNIQUE KEY `facturas_token_publico_unique` (`token_publico`),
  KEY `facturas_moneda_id_foreign` (`moneda_id`),
  KEY `facturas_plantilla_factura_id_foreign` (`plantilla_factura_id`),
  KEY `facturas_created_by_foreign` (`created_by`),
  KEY `facturas_estado_index` (`estado`),
  KEY `facturas_fecha_index` (`fecha`),
  KEY `facturas_cliente_id_estado_index` (`cliente_id`,`estado`),
  KEY `facturas_es_electronica_index` (`es_electronica`),
  CONSTRAINT `facturas_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `facturas_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `facturas_moneda_id_foreign` FOREIGN KEY (`moneda_id`) REFERENCES `monedas` (`id`),
  CONSTRAINT `facturas_plantilla_factura_id_foreign` FOREIGN KEY (`plantilla_factura_id`) REFERENCES `plantillas_factura` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facturas`
--

LOCK TABLES `facturas` WRITE;
/*!40000 ALTER TABLE `facturas` DISABLE KEYS */;
INSERT INTO `facturas` VALUES (1,'REM-2026-0001',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-21','2026-04-23',1,1,NULL,300000.00,0.00,57000.00,50000.00,1000.00,408000.00,408000.00,'Observaciones',NULL,NULL,NULL,'emitida',0,1,'uploads/facturas/REM-2026-0001.pdf','558RoSq2PSWCNMZIykeaNojfGSJvECdVDZDpUA2T',1,'2026-04-21 21:48:09',NULL,'2026-04-21 21:48:00','2026-04-21 21:48:29'),(2,'REM-2026-0002',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-21','2026-04-23',1,1,NULL,400000.00,0.00,20000.00,5000.00,10000.00,435000.00,435000.00,'Observaciones Observaciones',NULL,NULL,NULL,'emitida',0,3,'uploads/facturas/REM-2026-0002.pdf','DrjlqNVdJd25bCpAjE2uOYGtr8h8PeIwTFDz7mUe',1,'2026-04-22 02:55:17',NULL,'2026-04-22 02:53:47','2026-04-22 02:55:40'),(3,'REM-2026-0003',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-21','2026-04-23',1,1,NULL,100000.00,0.00,19000.00,0.00,0.00,119000.00,119000.00,NULL,NULL,NULL,NULL,'borrador',0,1,NULL,'cGFYaegYtDS9jOSFrZmphmH9ekL9TTE8StPmt3db',1,NULL,NULL,'2026-04-22 02:56:56','2026-04-22 02:56:56'),(4,'REM-2026-0004',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-22','2026-04-23',1,1,NULL,100000.00,0.00,19000.00,10000.00,10000.00,139000.00,139000.00,'Observaciones','po','123','dhl','emitida',0,3,'uploads/facturas/REM-2026-0004.pdf','k5WQf9NZuM70KNzhdIiUftqM8SXerGaGEXnKlyKU',1,'2026-04-22 14:25:09',NULL,'2026-04-22 14:23:58','2026-04-22 14:26:01'),(5,'REM-2026-0005',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-22','2026-04-23',1,1,NULL,4200000.00,0.00,798000.00,0.00,0.00,4998000.00,4998000.00,'ok',NULL,NULL,NULL,'borrador',0,1,'uploads/facturas/REM-2026-0005.pdf','uMCDIbv6ftdzmCD7Pk5w9Pj3wzJgEqqsoi7GaMYM',1,NULL,NULL,'2026-04-22 14:36:22','2026-04-22 14:36:29');
/*!40000 ALTER TABLE `facturas` ENABLE KEYS */;
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
-- Table structure for table `impuestos`
--

DROP TABLE IF EXISTS `impuestos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `impuestos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) NOT NULL,
  `porcentaje` decimal(5,2) NOT NULL,
  `tipo` enum('iva','retencion','otro') NOT NULL DEFAULT 'iva',
  `codigo_siigo` varchar(50) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `impuestos_tipo_activo_index` (`tipo`,`activo`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `impuestos`
--

LOCK TABLES `impuestos` WRITE;
/*!40000 ALTER TABLE `impuestos` DISABLE KEYS */;
INSERT INTO `impuestos` VALUES (1,'IVA 19%',19.00,'iva',NULL,1,'2026-04-21 21:22:34','2026-04-21 21:22:34'),(2,'IVA 5%',5.00,'iva',NULL,1,'2026-04-21 21:22:34','2026-04-21 21:22:34'),(3,'IVA Exento (0%)',0.00,'iva',NULL,1,'2026-04-21 21:22:34','2026-04-21 21:22:34'),(4,'ReteIVA 15%',15.00,'retencion',NULL,1,'2026-04-21 21:22:34','2026-04-21 21:22:34'),(5,'ReteFuente Servicios 4%',4.00,'retencion',NULL,1,'2026-04-21 21:22:34','2026-04-21 21:22:34');
/*!40000 ALTER TABLE `impuestos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `incoterms`
--

DROP TABLE IF EXISTS `incoterms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `incoterms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(4) NOT NULL,
  `descripcion` varchar(180) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `incoterms_codigo_unique` (`codigo`),
  KEY `incoterms_activo_index` (`activo`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `incoterms`
--

LOCK TABLES `incoterms` WRITE;
/*!40000 ALTER TABLE `incoterms` DISABLE KEYS */;
INSERT INTO `incoterms` VALUES (1,'DDP','Delivered Duty Paid — entregado con derechos pagados',1,'2026-04-21 21:22:34','2026-04-21 21:22:34'),(2,'FOB','Free On Board — libre a bordo',1,'2026-04-21 21:22:34','2026-04-21 21:22:34'),(3,'CIF','Cost, Insurance and Freight — costo, seguro y flete',1,'2026-04-21 21:22:34','2026-04-21 21:22:34'),(4,'EXW','Ex Works — en fábrica',1,'2026-04-21 21:22:34','2026-04-21 21:22:34'),(5,'DAP','Delivered At Place — entregado en lugar',1,'2026-04-21 21:22:34','2026-04-21 21:22:34'),(6,'CPT','Carriage Paid To — transporte pagado hasta',1,'2026-04-21 21:22:34','2026-04-21 21:22:34'),(7,'DPU','Delivered At Place Unloaded — entregado descargado',1,'2026-04-21 21:22:34','2026-04-21 21:22:34');
/*!40000 ALTER TABLE `incoterms` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_resets_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',1),(5,'2025_06_18_195318_create_permission_tables',1),(6,'2026_01_31_000001_add_profile_photo_to_users_table',1),(7,'2026_04_21_000001_create_monedas_table',1),(8,'2026_04_21_000002_create_impuestos_table',1),(9,'2026_04_21_000003_create_tipos_descuento_table',1),(10,'2026_04_21_000004_create_incoterms_table',1),(11,'2026_04_21_000005_create_puertos_table',1),(12,'2026_04_21_000006_create_tipos_pago_table',1),(13,'2026_04_21_000007_create_configuraciones_table',1),(14,'2026_04_21_000008_create_siigo_config_table',1),(15,'2026_04_21_000009_create_productos_table',1),(16,'2026_04_21_000010_create_producto_tallas_table',1),(17,'2026_04_21_000011_create_clientes_table',1),(18,'2026_04_21_000012_create_plantillas_factura_table',1),(19,'2026_04_21_000013_create_facturas_table',1),(20,'2026_04_22_000001_add_qr_and_siigo_response_to_facturas_table',2),(21,'2026_04_22_000002_add_facturacion_fields_to_siigo_config_table',3),(22,'2026_04_22_000003_add_datos_envio_to_facturas_table',4);
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
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `monedas`
--

DROP TABLE IF EXISTS `monedas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `monedas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(3) NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `simbolo` varchar(8) NOT NULL,
  `es_predeterminada` tinyint(1) NOT NULL DEFAULT 0,
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `monedas_codigo_unique` (`codigo`),
  KEY `monedas_activa_index` (`activa`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `monedas`
--

LOCK TABLES `monedas` WRITE;
/*!40000 ALTER TABLE `monedas` DISABLE KEYS */;
INSERT INTO `monedas` VALUES (1,'COP','Peso Colombiano','$',1,1,'2026-04-21 21:22:34','2026-04-21 21:22:34'),(2,'USD','Dólar Estadounidense','US$',0,1,'2026-04-21 21:22:34','2026-04-21 21:22:34'),(3,'EUR','Euro','€',0,1,'2026-04-21 21:22:34','2026-04-21 21:22:34');
/*!40000 ALTER TABLE `monedas` ENABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
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
-- Table structure for table `plantillas_factura`
--

DROP TABLE IF EXISTS `plantillas_factura`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plantillas_factura` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `html_content` longtext NOT NULL,
  `css_content` text DEFAULT NULL,
  `es_default` tinyint(1) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `plantillas_factura_activo_index` (`activo`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plantillas_factura`
--

LOCK TABLES `plantillas_factura` WRITE;
/*!40000 ALTER TABLE `plantillas_factura` DISABLE KEYS */;
INSERT INTO `plantillas_factura` VALUES (1,'Plantilla genérica','Factura estándar para cualquier cliente. Marcada como predeterminada.','<div class=\"factura\">\r\n<table class=\"header\">\r\n<tbody>\r\n<tr>\r\n<td>\r\n<h1>{{empresa.razon_social}}</h1>\r\n<p>NIT {{empresa.nit}} &middot; {{empresa.direccion}}</p>\r\n<p>Tel. {{empresa.telefono}} &middot; {{empresa.email}}</p>\r\n</td>\r\n<td class=\"right\">\r\n<h2>FACTURA {{factura.numero}}</h2>\r\n<p>Fecha: {{factura.fecha}}</p>\r\n<p>Vencimiento: {{factura.vencimiento}}</p>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<table class=\"grid-2\">\r\n<tbody>\r\n<tr>\r\n<td>\r\n<div class=\"section\">\r\n<div class=\"section-title\">Cliente</div>\r\n<p><strong>{{cliente.nombre}}</strong></p>\r\n<p>ID: {{cliente.identificacion}}</p>\r\n<p>{{cliente.direccion_facturacion}}</p>\r\n<p>{{cliente.email}} &middot; {{cliente.telefono}}</p>\r\n</div>\r\n</td>\r\n<td>\r\n<div class=\"section\">\r\n<div class=\"section-title\">Detalles</div>\r\n<p>Moneda: {{factura.moneda}}</p>\r\n<p>CUFE:</p>\r\n<p class=\"cufe\">{{factura.cufe}}</p>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<table class=\"items\">\r\n<thead>\r\n<tr>\r\n<th>#</th>\r\n<th>Referencia</th>\r\n<th>Descripci&oacute;n</th>\r\n<th>Cantidad</th>\r\n<th>Precio</th>\r\n<th>Total</th>\r\n</tr>\r\n</thead>\r\n<tbody>\r\n<tr data-loop=\"items\">\r\n<td>{{@index}}</td>\r\n<td>{{referencia}}</td>\r\n<td>{{descripcion}}</td>\r\n<td>{{cantidad}}</td>\r\n<td>{{precio_unitario}}</td>\r\n<td>{{total}}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p></p>\r\n<table class=\"totales\">\r\n<tbody>\r\n<tr>\r\n<td>Subtotal</td>\r\n<td class=\"right\">{{totales.subtotal}}</td>\r\n</tr>\r\n<tr>\r\n<td>IVA</td>\r\n<td class=\"right\">{{totales.iva}}</td>\r\n</tr>\r\n<tr>\r\n<td>Descuento</td>\r\n<td class=\"right\">{{totales.descuento}}</td>\r\n</tr>\r\n<tr class=\"total-final\">\r\n<td>TOTAL {{factura.moneda}}</td>\r\n<td class=\"right\">{{totales.total}}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div class=\"observaciones\">\r\n<div class=\"section-title\">Observaciones</div>\r\n<p>{{factura.observaciones}}</p>\r\n</div>\r\n</div>','body { font-family: Arial, sans-serif; font-size: 11px; color: #1f2937; margin: 0; padding: 0; }\r\n.factura { max-width: 900px; margin: 0 auto; padding: 24px; background: #ffffff; }\r\n\r\n/* Header: tabla invisible con 2 celdas — empresa a la izquierda, factura a la derecha */\r\ntable.header { width: 100%; border-collapse: collapse; border-bottom: 2px solid #f97316; margin-bottom: 20px; }\r\ntable.header td { vertical-align: top; padding: 0 0 12px 0; border: 0; }\r\ntable.header td.right { text-align: right; }\r\ntable.header h1 { color: #f97316; font-size: 22px; margin: 0 0 4px 0; }\r\ntable.header h2 { font-size: 16px; margin: 0; color: #111827; }\r\ntable.header p { margin: 2px 0; font-size: 11px; color: #4b5563; }\r\n\r\n/* Sección con título resaltado */\r\n.section { margin-bottom: 16px; }\r\n.section-title { background: #fef3c7; padding: 6px 10px; font-weight: bold; border-left: 3px solid #f97316; margin-bottom: 6px; }\r\n\r\n/* Layout de 2 columnas (cliente / detalles) con tabla */\r\ntable.grid-2 { width: 100%; border-collapse: collapse; margin-bottom: 10px; }\r\ntable.grid-2 td { vertical-align: top; padding: 0 8px; border: 0; width: 50%; }\r\ntable.grid-2 td:first-child { padding-left: 0; }\r\ntable.grid-2 td:last-child { padding-right: 0; }\r\n\r\n/* Tabla de items (productos) */\r\ntable.items { width: 100%; border-collapse: collapse; margin-top: 8px; }\r\ntable.items th, table.items td { border: 1px solid #e5e7eb; padding: 6px 8px; text-align: left; font-size: 10px; }\r\ntable.items th { background: #f3f4f6; font-weight: bold; color: #374151; }\r\n\r\n/* Totales alineados a la derecha */\r\ntable.totales { margin-top: 12px; margin-left: auto; width: 280px; border-collapse: collapse; }\r\ntable.totales td { padding: 4px 10px; font-size: 11px; border-bottom: 1px solid #e5e7eb; }\r\ntable.totales .total-final td { font-weight: bold; background: #fff7ed; color: #9a3412; }\r\n\r\n.right { text-align: right; }\r\n.banco { background: #fff7ed; padding: 12px; border: 1px dashed #fb923c; font-size: 10px; margin-top: 20px; line-height: 1.6; }\r\n.banco strong { color: #9a3412; }\r\n.cufe { font-size: 8px; color: #6b7280; margin: 6px 0 12px 0; word-break: break-all; }\r\n.observaciones { margin-top: 24px; }',1,1,'2026-04-21 21:22:34','2026-04-22 02:30:52'),(2,'Mytheresa (EUR, internacional)','Plantilla para clientes tipo Mytheresa, con separación SOLD TO / SHIP TO, EUR, taxes/insurance/freight separados y bloque bancario.','<div class=\"factura\">\n    <div class=\"header\">\n        <div>\n            <h1>{{empresa.razon_social}}</h1>\n            <p>NIT {{empresa.nit}} · {{empresa.direccion}}</p>\n            <p>Phone: {{empresa.telefono}} · {{empresa.sitio_web}}</p>\n        </div>\n        <div style=\"text-align:right;\">\n            <h2>INVOICE: {{factura.numero}}</h2>\n            <p>DATE: {{factura.fecha}}</p>\n            <p>EXPIRES: {{factura.vencimiento}}</p>\n        </div>\n    </div>\n\n    <p class=\"cufe\">CUFE: {{factura.cufe}}</p>\n\n    <div class=\"grid-2\">\n        <div class=\"section\">\n            <p><strong>SOLD TO</strong><br>{{cliente.nombre}}</p>\n            <p><strong>ADDRESS</strong><br>{{cliente.direccion_facturacion}}</p>\n            <p><strong>MAIL CONTACT</strong>: {{cliente.email}}</p>\n            <p><strong>PHONE</strong>: {{cliente.telefono}}</p>\n        </div>\n        <div class=\"section\">\n            <p><strong>SHIP TO</strong><br>{{cliente.direccion_envio}}</p>\n            <p><strong>INCOTERMS</strong>: {{cliente.incoterm}}</p>\n            <p><strong>SHIPPING PORT</strong>: {{cliente.puerto}}</p>\n            <p><strong>CURRENCY</strong>: {{factura.moneda}}</p>\n            <p><strong>ORIGIN</strong>: COLOMBIA</p>\n        </div>\n    </div>\n\n    <table class=\"items\">\n        <thead>\n            <tr>\n                <th>REFERENCE</th>\n                <th>DESCRIPTION</th>\n                <th>COLOR</th>\n                <th>QTY</th>\n                <th>UNIT PRICE</th>\n                <th>TOTAL AMOUNT</th>\n            </tr>\n        </thead>\n        <tbody>\n            <tr data-loop=\"items\">\n                <td>{{referencia}}</td>\n                <td>{{descripcion}}</td>\n                <td>{{color}}</td>\n                <td>{{cantidad}}</td>\n                <td>{{factura.simbolo}}{{precio_unitario}}</td>\n                <td>{{factura.simbolo}}{{total}}</td>\n            </tr>\n        </tbody>\n    </table>\n\n    <table class=\"totales\">\n        <tr><td>SUBTOTAL</td><td class=\"right\">{{factura.simbolo}}{{totales.subtotal}}</td></tr>\n        <tr><td>TAXES</td><td class=\"right\">{{factura.simbolo}}{{totales.iva}}</td></tr>\n        <tr><td>INSURANCE</td><td class=\"right\">{{factura.simbolo}}{{totales.seguro}}</td></tr>\n        <tr><td>FREIGHT</td><td class=\"right\">{{factura.simbolo}}{{totales.flete}}</td></tr>\n        <tr><td>DISCOUNT</td><td class=\"right\">{{factura.simbolo}}{{totales.descuento}}</td></tr>\n        <tr class=\"total-final\"><td>TOTAL</td><td class=\"right\">{{factura.simbolo}}{{totales.total}}</td></tr>\n    </table>\n\n    <div class=\"banco\">\n        <strong>Beneficiary Bank Name:</strong> {{banco.nombre}}<br>\n        <strong>Beneficiary Bank Country:</strong> {{banco.pais}}<br>\n        <strong>Beneficiary Bank Address:</strong> {{banco.direccion}}<br>\n        <strong>Beneficiary Bank Account Name:</strong> {{banco.titular}}<br>\n        <strong>Beneficiary Bank Account Currency:</strong> {{banco.moneda}}<br>\n        <strong>Beneficiary Bank SWIFT/BIC:</strong> {{banco.swift}}<br>\n        <strong>Beneficiary Bank Account Number:</strong> {{banco.numero_cuenta}}<br><br>\n        <strong>Finance Contact Name:</strong> {{contacto.nombre}}<br>\n        <strong>Finance Contact Email:</strong> {{contacto.email}}<br>\n        <strong>Finance Contact Phone:</strong> {{contacto.telefono}}\n    </div>\n</div>','body { font-family: Arial, sans-serif; font-size: 11px; color: #1f2937; margin: 0; padding: 0; }\n.factura { max-width: 900px; margin: 0 auto; padding: 24px; background: #ffffff; }\n.header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #f97316; padding-bottom: 12px; margin-bottom: 20px; }\n.header h1 { color: #f97316; font-size: 22px; margin: 0 0 4px 0; }\n.header h2 { font-size: 16px; margin: 0; color: #111827; }\n.header p { margin: 2px 0; font-size: 11px; color: #4b5563; }\n.section { margin-bottom: 16px; }\n.section-title { background: #fef3c7; padding: 6px 10px; font-weight: bold; border-left: 3px solid #f97316; margin-bottom: 6px; }\n.grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }\ntable { width: 100%; border-collapse: collapse; margin-top: 8px; }\ntable.items th, table.items td { border: 1px solid #e5e7eb; padding: 6px 8px; text-align: left; font-size: 10px; }\ntable.items th { background: #f3f4f6; font-weight: bold; color: #374151; }\n.totales { margin-top: 12px; margin-left: auto; width: 280px; }\n.totales td { padding: 4px 10px; font-size: 11px; border-bottom: 1px solid #e5e7eb; }\n.totales .total-final td { font-weight: bold; background: #fff7ed; color: #9a3412; }\n.right { text-align: right; }\n.banco { background: #fff7ed; padding: 12px; border: 1px dashed #fb923c; font-size: 10px; margin-top: 20px; line-height: 1.6; }\n.banco strong { color: #9a3412; }\n.cufe { font-size: 8px; color: #6b7280; margin: 6px 0 12px 0; word-break: break-all; }\n.observaciones { margin-top: 24px; }',0,1,'2026-04-21 21:22:34','2026-04-22 02:30:52'),(3,'Consumidor final internacional (USD + TRM)','Factura para consumidor final fuera de Colombia con tasa representativa y total COP.','<style>\n/* Estilos específicos para la plantilla Siigo-style (no afectan otras plantillas) */\n.siigo { font-family: Arial, sans-serif; font-size: 9px; color: #000; }\n.siigo .header-top { width: 100%; border-collapse: collapse; margin-bottom: 8px; }\n.siigo .header-top td { vertical-align: top; padding: 0; border: 0; }\n.siigo .col-qr { width: 15%; }\n.siigo .col-logo { width: 50%; text-align: center; }\n.siigo .col-logo img { max-width: 140px; height: auto; }\n.siigo .col-invoice { width: 35%; text-align: right; font-size: 10px; }\n.siigo .col-invoice h2 { margin: 0 0 6px 0; font-size: 13px; font-weight: bold; }\n.siigo .invoice-box { border: 1px solid #000; width: 100%; border-collapse: collapse; margin-top: 4px; font-size: 9px; }\n.siigo .invoice-box td { border: 1px solid #000; padding: 3px 6px; text-align: left; }\n.siigo .invoice-box td:first-child { font-weight: normal; width: 45%; }\n.siigo .invoice-box td:last-child { text-align: right; }\n.siigo .empresa-info { text-align: center; font-size: 10px; margin: 6px 0; line-height: 1.35; }\n.siigo .empresa-info strong { font-size: 11px; }\n.siigo .legal { font-size: 7.5px; color: #000; margin: 4px 0; text-align: center; line-height: 1.3; position: relative; padding-right: 30px; }\n.siigo .legal .version { position: absolute; right: 0; top: 50%; transform: translateY(-50%); font-size: 8px; font-weight: bold; }\n.siigo .cufe { font-size: 7.5px; color: #000; margin: 8px 0; word-break: break-all; }\n.siigo .cajas { width: 100%; border-collapse: collapse; margin: 6px 0; }\n.siigo .cajas > tbody > tr > td { vertical-align: top; padding: 0; width: 50%; border: 0; }\n.siigo .cajas .caja { border: 1px solid #000; padding: 6px 8px; font-size: 9px; }\n.siigo .cajas .caja table { width: 100%; border-collapse: collapse; }\n.siigo .cajas .caja table td { padding: 2px 4px; border: 0; vertical-align: top; }\n.siigo .cajas .caja table td.label { width: 40%; font-weight: normal; }\n.siigo .cajas td:first-child .caja { margin-right: 4px; }\n.siigo .cajas td:last-child .caja { margin-left: 4px; }\n.siigo table.items-siigo { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 9px; }\n.siigo table.items-siigo th, .siigo table.items-siigo td { border: 1px solid #000; padding: 5px 6px; text-align: center; }\n.siigo table.items-siigo th { background: #fff; font-weight: normal; }\n.siigo .footer-siigo { width: 100%; border-collapse: collapse; margin-top: 0; }\n.siigo .footer-siigo > tbody > tr > td { vertical-align: top; padding: 0; border: 0; }\n.siigo .obs-col { width: 60%; border: 1px solid #000; border-top: 0; padding: 8px; }\n.siigo .obs-col .obs-title { font-weight: bold; margin-bottom: 20px; }\n.siigo .obs-col .trm { margin-top: 20px; }\n.siigo .obs-col .trm strong { display: block; }\n.siigo .totales-col { width: 40%; }\n.siigo table.totales-siigo { width: 100%; border-collapse: collapse; font-size: 9px; }\n.siigo table.totales-siigo td { border: 1px solid #000; padding: 5px 8px; }\n.siigo table.totales-siigo td.label { text-align: center; font-weight: bold; }\n.siigo table.totales-siigo td.valor { text-align: right; }\n.siigo table.totales-siigo tr.final td { font-weight: bold; }\n.siigo .qr img { max-width: 100px; height: auto; }\n.siigo .qr-placeholder { width: 100px; height: 100px; border: 1px dashed #999; font-size: 7px; color: #999; text-align: center; padding: 40px 4px; box-sizing: border-box; }\n</style>\n\n<div class=\"factura siigo\">\n    <table class=\"header-top\">\n        <tr>\n            <td class=\"col-qr\">\n                <div class=\"qr\">\n                    {{factura.qr_html}}\n                </div>\n            </td>\n            <td class=\"col-logo\">\n                <img src=\"{{empresa.logo}}\" alt=\"Logo\">\n            </td>\n            <td class=\"col-invoice\">\n                <h2>COMMERCIAL INVOICE</h2>\n                <table class=\"invoice-box\">\n                    <tr><td>INVOICE:</td><td>{{factura.numero}}</td></tr>\n                    <tr><td>DATE</td><td>{{factura.fecha}}</td></tr>\n                    <tr><td>EXPIRES</td><td>{{factura.vencimiento}}</td></tr>\n                    <tr><td>PO#</td><td>{{factura.po}}</td></tr>\n                    <tr><td>AWB</td><td>{{factura.awb}}</td></tr>\n                    <tr><td>SHIPPER</td><td>{{factura.shipper}}</td></tr>\n                </table>\n            </td>\n        </tr>\n    </table>\n\n    <div class=\"empresa-info\">\n        <strong>{{empresa.razon_social}}. NIT {{empresa.nit}}</strong><br>\n        Address: {{empresa.direccion}}<br>\n        Phone: {{empresa.telefono}}<br>\n        {{empresa.sitio_web}}\n    </div>\n\n    <div class=\"legal\">\n        {{empresa.regimen}}<br>\n        {{empresa.resolucion_clc}}<br>\n        {{empresa.resolucion_fv}}\n        <span class=\"version\">{{factura.version}}</span>\n    </div>\n\n    <p class=\"cufe\">CUFE: {{factura.cufe}}</p>\n\n    <table class=\"cajas\">\n        <tr>\n            <td>\n                <div class=\"caja\">\n                    <table>\n                        <tr><td class=\"label\">SOLD TO</td><td>{{cliente.nombre}}</td></tr>\n                        <tr><td class=\"label\">SHIP TO</td><td>{{cliente.direccion_envio}}</td></tr>\n                        <tr><td class=\"label\">ID</td><td>{{cliente.identificacion}}</td></tr>\n                        <tr><td class=\"label\">MAIL CONTACT</td><td>{{cliente.email}}</td></tr>\n                        <tr><td class=\"label\">PHONE</td><td>{{cliente.telefono}}</td></tr>\n                        <tr><td class=\"label\">ADDRESS</td><td>{{cliente.direccion_facturacion}}</td></tr>\n                        <tr><td class=\"label\">INCOTERMS</td><td>{{cliente.incoterm}}</td></tr>\n                        <tr><td class=\"label\">SHIPPING PORT</td><td>{{cliente.puerto}}</td></tr>\n                    </table>\n                </div>\n            </td>\n            <td>\n                <div class=\"caja\">\n                    <table>\n                        <tr><td class=\"label\">ORIGIN</td><td>{{cliente.origen}}</td><td class=\"label\">Cod</td><td>{{factura.cod}}</td></tr>\n                        <tr><td class=\"label\">CURRENCY</td><td>{{factura.moneda}}</td><td class=\"label\">Remisión</td><td>{{factura.remision}}</td></tr>\n                        <tr><td class=\"label\">DESTINATION</td><td>{{cliente.destino}}</td><td></td><td></td></tr>\n                        <tr><td class=\"label\">PAYMENT TERMS</td><td colspan=\"3\">{{factura.payment_terms}}</td></tr>\n                    </table>\n                </div>\n            </td>\n        </tr>\n    </table>\n\n    <table class=\"items-siigo\">\n        <thead>\n            <tr>\n                <th>REFERENCE</th>\n                <th>DESCRIPTION</th>\n                <th>COLOR</th>\n                <th>SIZE</th>\n                <th>COMPOSITION</th>\n                <th>QTY</th>\n                <th>#PA</th>\n                <th>UNIT PRICE</th>\n                <th>TOTAL AMOUNT</th>\n            </tr>\n        </thead>\n        <tbody>\n            <tr data-loop=\"items\">\n                <td>{{referencia}}</td>\n                <td>{{descripcion}}</td>\n                <td>{{color}}</td>\n                <td>{{size}}</td>\n                <td>{{composition}}</td>\n                <td>{{cantidad}}</td>\n                <td>{{codigo_pa}}</td>\n                <td>${{precio_unitario}}</td>\n                <td>${{total}}</td>\n            </tr>\n        </tbody>\n    </table>\n\n    <table class=\"footer-siigo\">\n        <tr>\n            <td class=\"obs-col\">\n                <div class=\"obs-title\">OBSERVATIONS:</div>\n                <div>{{factura.observaciones}}</div>\n                <div class=\"trm\">\n                    <strong>Tasa Representativa</strong>\n                    {{factura.tasa_cambio}}\n                    <br><br>\n                    <strong>Total COP</strong>\n                    ${{totales.total_cop}}\n                </div>\n            </td>\n            <td class=\"totales-col\">\n                <table class=\"totales-siigo\">\n                    <tr><td class=\"label\">SUBTOTAL</td><td class=\"valor\">${{totales.subtotal}}</td></tr>\n                    <tr><td class=\"label\">TAXES</td><td class=\"valor\">${{totales.iva}}</td></tr>\n                    <tr><td class=\"label\">FREIGHT</td><td class=\"valor\">${{totales.flete}}</td></tr>\n                    <tr><td class=\"label\">INSURANCE</td><td class=\"valor\">${{totales.seguro}}</td></tr>\n                    <tr><td class=\"label\">DISCOUNT</td><td class=\"valor\">${{totales.descuento}}</td></tr>\n                    <tr class=\"final\"><td class=\"label\">TOTAL</td><td class=\"valor\">${{totales.total}}</td></tr>\n                </table>\n            </td>\n        </tr>\n    </table>\n</div>','body { font-family: Arial, sans-serif; font-size: 11px; color: #1f2937; margin: 0; padding: 0; }\n.factura { max-width: 900px; margin: 0 auto; padding: 24px; background: #ffffff; }\n\n/* Header: tabla invisible con 2 celdas — empresa a la izquierda, factura a la derecha */\ntable.header { width: 100%; border-collapse: collapse; border-bottom: 2px solid #f97316; margin-bottom: 20px; }\ntable.header td { vertical-align: top; padding: 0 0 12px 0; border: 0; }\ntable.header td.right { text-align: right; }\ntable.header h1 { color: #f97316; font-size: 22px; margin: 0 0 4px 0; }\ntable.header h2 { font-size: 16px; margin: 0; color: #111827; }\ntable.header p { margin: 2px 0; font-size: 11px; color: #4b5563; }\n\n/* Sección con título resaltado */\n.section { margin-bottom: 16px; }\n.section-title { background: #fef3c7; padding: 6px 10px; font-weight: bold; border-left: 3px solid #f97316; margin-bottom: 6px; }\n\n/* Layout de 2 columnas (cliente / detalles) con tabla */\ntable.grid-2 { width: 100%; border-collapse: collapse; margin-bottom: 10px; }\ntable.grid-2 td { vertical-align: top; padding: 0 8px; border: 0; width: 50%; }\ntable.grid-2 td:first-child { padding-left: 0; }\ntable.grid-2 td:last-child { padding-right: 0; }\n\n/* Tabla de items (productos) */\ntable.items { width: 100%; border-collapse: collapse; margin-top: 8px; }\ntable.items th, table.items td { border: 1px solid #e5e7eb; padding: 6px 8px; text-align: left; font-size: 10px; }\ntable.items th { background: #f3f4f6; font-weight: bold; color: #374151; }\n\n/* Totales alineados a la derecha */\ntable.totales { margin-top: 12px; margin-left: auto; width: 280px; border-collapse: collapse; }\ntable.totales td { padding: 4px 10px; font-size: 11px; border-bottom: 1px solid #e5e7eb; }\ntable.totales .total-final td { font-weight: bold; background: #fff7ed; color: #9a3412; }\n\n.right { text-align: right; }\n.banco { background: #fff7ed; padding: 12px; border: 1px dashed #fb923c; font-size: 10px; margin-top: 20px; line-height: 1.6; }\n.banco strong { color: #9a3412; }\n.cufe { font-size: 8px; color: #6b7280; margin: 6px 0 12px 0; word-break: break-all; }\n.observaciones { margin-top: 24px; }',0,1,'2026-04-21 21:22:34','2026-04-22 02:41:19');
/*!40000 ALTER TABLE `plantillas_factura` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `producto_tallas`
--

DROP TABLE IF EXISTS `producto_tallas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `producto_tallas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `producto_id` bigint(20) unsigned NOT NULL,
  `talla` varchar(20) NOT NULL,
  `stock` int(10) unsigned NOT NULL DEFAULT 0,
  `orden` smallint(5) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `producto_tallas_producto_id_talla_unique` (`producto_id`,`talla`),
  CONSTRAINT `producto_tallas_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `producto_tallas`
--

LOCK TABLES `producto_tallas` WRITE;
/*!40000 ALTER TABLE `producto_tallas` DISABLE KEYS */;
/*!40000 ALTER TABLE `producto_tallas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `referencia` varchar(40) NOT NULL,
  `descripcion` varchar(150) NOT NULL,
  `color` varchar(60) DEFAULT NULL,
  `composicion` varchar(255) DEFAULT NULL,
  `codigo_pa` varchar(20) DEFAULT NULL,
  `precio_unitario` decimal(14,2) NOT NULL,
  `moneda_id` bigint(20) unsigned NOT NULL,
  `impuesto_id` bigint(20) unsigned DEFAULT NULL,
  `unidad_medida` varchar(20) NOT NULL DEFAULT 'Und',
  `imagen_path` varchar(255) DEFAULT NULL,
  `es_prenda` tinyint(1) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `siigo_id` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `productos_referencia_unique` (`referencia`),
  KEY `productos_moneda_id_foreign` (`moneda_id`),
  KEY `productos_impuesto_id_foreign` (`impuesto_id`),
  KEY `productos_descripcion_index` (`descripcion`),
  KEY `productos_activo_index` (`activo`),
  KEY `productos_activo_es_prenda_index` (`activo`,`es_prenda`),
  CONSTRAINT `productos_impuesto_id_foreign` FOREIGN KEY (`impuesto_id`) REFERENCES `impuestos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `productos_moneda_id_foreign` FOREIGN KEY (`moneda_id`) REFERENCES `monedas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (1,'PROD-T44UZY6Y','Descripcion pr','Rojo','algodon','6145',100000.00,1,1,'Und','uploads/productos/prod-548ee707-0022-47be-9547-7fd618d14ce4.png',1,1,NULL,'2026-04-21 21:42:02','2026-04-21 21:42:02');
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `puertos`
--

DROP TABLE IF EXISTS `puertos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `puertos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) NOT NULL,
  `pais` varchar(80) NOT NULL DEFAULT 'Colombia',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `puertos_pais_activo_index` (`pais`,`activo`),
  KEY `puertos_nombre_index` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `puertos`
--

LOCK TABLES `puertos` WRITE;
/*!40000 ALTER TABLE `puertos` DISABLE KEYS */;
INSERT INTO `puertos` VALUES (1,'Cali','Colombia',1,'2026-04-21 21:22:34','2026-04-21 21:22:34'),(2,'Buenaventura','Colombia',1,'2026-04-21 21:22:34','2026-04-21 21:22:34'),(3,'Cartagena','Colombia',1,'2026-04-21 21:22:34','2026-04-21 21:22:34'),(4,'Barranquilla','Colombia',1,'2026-04-21 21:22:34','2026-04-21 21:22:34'),(5,'Bogotá','Colombia',1,'2026-04-21 21:22:34','2026-04-21 21:22:34'),(6,'Medellín','Colombia',1,'2026-04-21 21:22:34','2026-04-21 21:22:34');
/*!40000 ALTER TABLE `puertos` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Administrador','web','2026-04-21 21:22:34','2026-04-21 21:22:34');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `siigo_catalogos`
--

DROP TABLE IF EXISTS `siigo_catalogos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `siigo_catalogos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tipo` varchar(40) NOT NULL,
  `codigo` varchar(80) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `siigo_catalogos_tipo_codigo_unique` (`tipo`,`codigo`),
  KEY `siigo_catalogos_tipo_index` (`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `siigo_catalogos`
--

LOCK TABLES `siigo_catalogos` WRITE;
/*!40000 ALTER TABLE `siigo_catalogos` DISABLE KEYS */;
/*!40000 ALTER TABLE `siigo_catalogos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `siigo_config`
--

DROP TABLE IF EXISTS `siigo_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `siigo_config` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(150) DEFAULT NULL,
  `access_key` text DEFAULT NULL,
  `partner_id` varchar(100) DEFAULT NULL,
  `nit_emisor` varchar(30) DEFAULT NULL,
  `tipo_documento_id` bigint(20) unsigned DEFAULT NULL,
  `seller_id` bigint(20) unsigned DEFAULT NULL,
  `payment_type_id` bigint(20) unsigned DEFAULT NULL,
  `ambiente` enum('sandbox','produccion') NOT NULL DEFAULT 'sandbox',
  `token_cache` text DEFAULT NULL,
  `token_expires_at` timestamp NULL DEFAULT NULL,
  `sync_catalogos_at` timestamp NULL DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `siigo_config`
--

LOCK TABLES `siigo_config` WRITE;
/*!40000 ALTER TABLE `siigo_config` DISABLE KEYS */;
INSERT INTO `siigo_config` VALUES (1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'sandbox',NULL,NULL,NULL,0,'2026-04-21 21:34:43','2026-04-21 21:34:43');
/*!40000 ALTER TABLE `siigo_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipos_descuento`
--

DROP TABLE IF EXISTS `tipos_descuento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipos_descuento` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) NOT NULL,
  `alcance` enum('linea','global') NOT NULL DEFAULT 'linea',
  `modalidad` enum('porcentaje','valor_fijo') NOT NULL DEFAULT 'porcentaje',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tipos_descuento_alcance_activo_index` (`alcance`,`activo`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_descuento`
--

LOCK TABLES `tipos_descuento` WRITE;
/*!40000 ALTER TABLE `tipos_descuento` DISABLE KEYS */;
INSERT INTO `tipos_descuento` VALUES (1,'Descuento Comercial','linea','porcentaje',1,'2026-04-21 21:22:34','2026-04-21 21:22:34'),(2,'Descuento por Volumen','global','porcentaje',1,'2026-04-21 21:22:34','2026-04-21 21:22:34'),(3,'Descuento Pronto Pago','global','porcentaje',1,'2026-04-21 21:22:34','2026-04-21 21:22:34'),(4,'Descuento Valor Fijo','global','valor_fijo',1,'2026-04-21 21:22:34','2026-04-21 21:22:34'),(5,'Descuento Línea Valor Fijo','linea','valor_fijo',1,'2026-04-21 21:22:34','2026-04-21 21:22:34');
/*!40000 ALTER TABLE `tipos_descuento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipos_pago`
--

DROP TABLE IF EXISTS `tipos_pago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipos_pago` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) NOT NULL,
  `dias_credito` smallint(5) unsigned NOT NULL DEFAULT 0,
  `codigo_siigo` varchar(50) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tipos_pago_activo_index` (`activo`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_pago`
--

LOCK TABLES `tipos_pago` WRITE;
/*!40000 ALTER TABLE `tipos_pago` DISABLE KEYS */;
INSERT INTO `tipos_pago` VALUES (1,'Contado',0,NULL,1,'2026-04-21 21:22:34','2026-04-21 21:22:34'),(2,'Crédito 14 días',14,NULL,1,'2026-04-21 21:22:34','2026-04-21 21:22:34'),(3,'Crédito 30 días',30,NULL,1,'2026-04-21 21:22:34','2026-04-21 21:22:34'),(4,'Crédito 60 días',60,NULL,1,'2026-04-21 21:22:34','2026-04-21 21:22:34'),(5,'Transferencia ACH',0,NULL,1,'2026-04-21 21:22:34','2026-04-21 21:22:34'),(6,'Crédito ACH',14,NULL,1,'2026-04-21 21:22:34','2026-04-21 21:22:34'),(7,'Wire Transfer',0,NULL,1,'2026-04-22 14:13:03','2026-04-22 14:13:03');
/*!40000 ALTER TABLE `tipos_pago` ENABLE KEYS */;
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
  `profile_photo` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrador','admin@admin.com',NULL,'2026-04-21 21:22:34','$2y$10$k5MgyCh3yzObAOzF7M47Xuh5ln6BIb0ujjKzzO3DWv75uPNF5PiEG',NULL,'2026-04-21 21:22:34','2026-04-21 21:22:34');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'clc'
--

--
-- Dumping routines for database 'clc'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-11 15:10:17
