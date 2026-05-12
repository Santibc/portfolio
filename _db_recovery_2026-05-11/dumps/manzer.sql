-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: manzer
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
-- Current Database: `manzer`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `manzer` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `manzer`;

--
-- Table structure for table `alerta_configuraciones`
--

DROP TABLE IF EXISTS `alerta_configuraciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alerta_configuraciones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tipo` varchar(100) NOT NULL COMMENT 'formacion, epi, itv, seguro, contrato, etc.',
  `dias_antelacion` int(11) NOT NULL COMMENT 'Días antes de caducidad para alertar',
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alerta_configuraciones`
--

LOCK TABLES `alerta_configuraciones` WRITE;
/*!40000 ALTER TABLE `alerta_configuraciones` DISABLE KEYS */;
INSERT INTO `alerta_configuraciones` VALUES (1,'formacion',30,1,'2025-12-23 14:13:27','2025-12-23 14:13:27'),(2,'epi',30,1,'2025-12-23 14:13:27','2025-12-23 14:13:27'),(3,'itv',30,1,'2025-12-23 14:13:27','2025-12-23 14:13:27'),(4,'seguro',45,1,'2025-12-23 14:13:27','2025-12-23 14:13:27'),(5,'contrato',60,1,'2025-12-23 14:13:27','2025-12-23 14:13:27'),(6,'documento_cae',30,1,'2025-12-23 14:13:27','2025-12-23 14:13:27'),(7,'apto_medico',30,1,'2025-12-23 14:13:27','2025-12-23 14:13:27'),(8,'documento_trabajador',30,1,'2026-01-19 17:13:39','2026-01-19 17:13:39'),(9,'epi_caducidad',30,1,'2026-01-19 17:13:39','2026-01-19 17:13:39'),(10,'epi_revision',15,1,'2026-01-19 17:13:39','2026-01-19 17:13:39'),(11,'seguro_vehiculo',45,1,'2026-01-19 17:13:39','2026-01-19 17:13:39'),(12,'documento_vehiculo',30,1,'2026-01-19 17:13:39','2026-01-19 17:13:39'),(13,'contrato_vencimiento',60,1,'2026-01-19 17:13:39','2026-01-19 17:13:39'),(14,'contrato_garantia',30,1,'2026-01-19 17:13:39','2026-01-19 17:13:39'),(15,'caducidad_general',45,1,'2026-01-19 17:13:39','2026-01-19 17:13:39');
/*!40000 ALTER TABLE `alerta_configuraciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alertas`
--

DROP TABLE IF EXISTS `alertas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alertas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tipo` varchar(100) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `mensaje` text NOT NULL,
  `prioridad` enum('baja','media','alta','critica') NOT NULL DEFAULT 'media',
  `alertable_type` varchar(255) NOT NULL COMMENT 'Modelo: Trabajador, Vehiculo, etc.',
  `alertable_id` bigint(20) unsigned NOT NULL,
  `para_roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '["admin", "rrhh"]' CHECK (json_valid(`para_roles`)),
  `para_usuario_id` bigint(20) unsigned DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL COMMENT 'Fecha del vencimiento que genera la alerta',
  `leida` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_lectura` datetime DEFAULT NULL,
  `resuelta` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_resolucion` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `alertas_para_usuario_id_foreign` (`para_usuario_id`),
  KEY `alertas_alertable_type_alertable_id_index` (`alertable_type`,`alertable_id`),
  CONSTRAINT `alertas_para_usuario_id_foreign` FOREIGN KEY (`para_usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alertas`
--

LOCK TABLES `alertas` WRITE;
/*!40000 ALTER TABLE `alertas` DISABLE KEYS */;
INSERT INTO `alertas` VALUES (1,'formacion','Formación próxima a caducar: Motosierra','La formación \'Motosierra\' del trabajador Santi Bellaizan caduca el 01/01/2026','critica','App\\Models\\TrabajadorFormacion',1,'[\"Administrador\",\"RRHH\"]',NULL,'2026-01-01',1,'2026-01-19 12:34:29',0,NULL,'2026-01-19 17:14:41'),(2,'caducidad_general','Caducidad empresa: Certificación ISO 9001:2015','La Certificación ISO 9001:2015 caduca el 29/01/2026. Sistema de gestión de calidad','media','App\\Models\\CaducidadGeneral',1,'[\"Administrador\"]',NULL,'2026-01-29',1,'2026-01-22 10:52:35',0,NULL,'2026-01-19 17:14:58'),(3,'caducidad_general','Caducidad empresa: Seguro Responsabilidad Civil','La Seguro Responsabilidad Civil caduca el 24/01/2026. Póliza anual de RC','alta','App\\Models\\CaducidadGeneral',2,'[\"Administrador\"]',NULL,'2026-01-24',1,'2026-01-22 10:52:35',0,NULL,'2026-01-19 17:14:58'),(4,'caducidad_general','Caducidad empresa: Licencia de Actividad Municipal','La Licencia de Actividad Municipal caduca el 16/01/2026','critica','App\\Models\\CaducidadGeneral',3,'[\"Administrador\"]',NULL,'2026-01-16',1,'2026-01-19 12:18:36',1,'2026-01-19 12:19:50','2026-01-19 17:14:58');
/*!40000 ALTER TABLE `alertas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auditoria`
--

DROP TABLE IF EXISTS `auditoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auditoria` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `accion` enum('crear','editar','eliminar','ver','login','logout','otro') NOT NULL,
  `tabla` varchar(100) NOT NULL,
  `registro_id` bigint(20) unsigned DEFAULT NULL,
  `datos_anteriores` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos_anteriores`)),
  `datos_nuevos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos_nuevos`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `auditoria_tabla_registro_id_index` (`tabla`,`registro_id`),
  KEY `auditoria_user_id_index` (`user_id`),
  KEY `auditoria_created_at_index` (`created_at`),
  CONSTRAINT `auditoria_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auditoria`
--

LOCK TABLES `auditoria` WRITE;
/*!40000 ALTER TABLE `auditoria` DISABLE KEYS */;
INSERT INTO `auditoria` VALUES (1,1,'editar','trabajadores',11,'{\"id\":11,\"user_id\":8,\"tipo_relacion\":\"propio\",\"nombre\":\"Santi\",\"apellidos\":\"Bellaizan\",\"dni\":\"12312312321\",\"email\":\"vblogsanti@gmail.com\",\"telefono\":\"3202230467\",\"direccion\":\"Calle 69 #10-15\",\"fecha_nacimiento\":\"1999-06-25T05:00:00.000000Z\",\"fecha_alta\":\"2025-12-23T05:00:00.000000Z\",\"fecha_baja\":null,\"categoria_convenio\":\"Peon\",\"salario_bruto_mensual\":\"20000.00\",\"coste_empresa_dia\":\"234324.00\",\"coste_hora\":\"343.00\",\"vacaciones_anuales\":22,\"vacaciones_acumuladas\":\"0.00\",\"antiguedad\":\"2025-12-23T05:00:00.000000Z\",\"subcontrata_id\":null,\"activo\":true,\"created_at\":\"2025-12-23T15:28:14.000000Z\",\"updated_at\":\"2026-01-13T16:31:01.000000Z\",\"deleted_at\":null}','{\"id\":11,\"user_id\":8,\"tipo_relacion\":\"propio\",\"nombre\":\"Santi\",\"apellidos\":\"Bellaizan\",\"dni\":\"12312312321\",\"email\":\"vblogsanti@gmail.com\",\"telefono\":\"3202230467\",\"direccion\":\"Calle 69 #10-15\",\"fecha_nacimiento\":\"1999-06-25T05:00:00.000000Z\",\"fecha_alta\":\"2025-12-23T05:00:00.000000Z\",\"fecha_baja\":null,\"categoria_convenio\":\"Pe\\u00f3n Forestal\",\"salario_bruto_mensual\":\"20000.00\",\"coste_empresa_dia\":\"234324.00\",\"coste_hora\":\"343.00\",\"vacaciones_anuales\":22,\"vacaciones_acumuladas\":\"0.00\",\"antiguedad\":\"2025-12-23T05:00:00.000000Z\",\"subcontrata_id\":null,\"activo\":true,\"created_at\":\"2025-12-23T15:28:14.000000Z\",\"updated_at\":\"2026-01-19T23:09:17.000000Z\",\"deleted_at\":null}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-19 23:09:17'),(2,1,'crear','clientes',6,NULL,'{\"tipo\":\"privado\",\"nombre_comercial\":\"Empresa Test MCP\",\"razon_social\":\"Empresa Test MCP S.L.\",\"cif\":\"B98765432\",\"direccion\":\"Calle Principal 123\",\"codigo_postal\":\"28001\",\"ciudad\":\"Madrid\",\"provincia\":\"Madrid\",\"pais\":\"Espa\\u00f1a\",\"telefono\":\"912345678\",\"email\":\"info@empresatest.com\",\"persona_contacto\":\"Juan P\\u00e9rez\",\"telefono_contacto\":\"666555444\",\"email_contacto\":\"juan.perez@empresatest.com\",\"condiciones_pago\":\"30 d\\u00edas\",\"retencion_porcentaje\":\"0.00\",\"notas\":null,\"activo\":true,\"updated_at\":\"2026-01-21T21:38:58.000000Z\",\"created_at\":\"2026-01-21T21:38:58.000000Z\",\"id\":6}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-21 21:38:58'),(3,1,'editar','clientes',6,'{\"id\":6,\"tipo\":\"privado\",\"nombre_comercial\":\"Empresa Test MCP\",\"razon_social\":\"Empresa Test MCP S.L.\",\"cif\":\"B98765432\",\"direccion\":\"Calle Principal 123\",\"codigo_postal\":\"28001\",\"ciudad\":\"Madrid\",\"provincia\":\"Madrid\",\"pais\":\"Espa\\u00f1a\",\"telefono\":\"912345678\",\"email\":\"info@empresatest.com\",\"persona_contacto\":\"Juan P\\u00e9rez\",\"telefono_contacto\":\"666555444\",\"email_contacto\":\"juan.perez@empresatest.com\",\"condiciones_pago\":\"30 d\\u00edas\",\"retencion_porcentaje\":\"0.00\",\"notas\":null,\"activo\":true,\"created_at\":\"2026-01-21T21:38:58.000000Z\",\"updated_at\":\"2026-01-21T21:38:58.000000Z\",\"deleted_at\":null}','{\"id\":6,\"tipo\":\"privado\",\"nombre_comercial\":\"Empresa Test MCP Modificada\",\"razon_social\":\"Empresa Test MCP S.L.\",\"cif\":\"B98765432\",\"direccion\":\"Calle Principal 123\",\"codigo_postal\":\"28001\",\"ciudad\":\"Madrid\",\"provincia\":\"Madrid\",\"pais\":\"Espa\\u00f1a\",\"telefono\":\"912345678\",\"email\":\"info@empresatest.com\",\"persona_contacto\":\"Juan P\\u00e9rez\",\"telefono_contacto\":\"666555444\",\"email_contacto\":\"juan.perez@empresatest.com\",\"condiciones_pago\":\"30 d\\u00edas\",\"retencion_porcentaje\":\"0.00\",\"notas\":null,\"activo\":true,\"created_at\":\"2026-01-21T21:38:58.000000Z\",\"updated_at\":\"2026-01-21T21:39:24.000000Z\",\"deleted_at\":null}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-21 21:39:24'),(4,1,'eliminar','clientes',6,'{\"id\":6,\"tipo\":\"privado\",\"nombre_comercial\":\"Empresa Test MCP Modificada\",\"razon_social\":\"Empresa Test MCP S.L.\",\"cif\":\"B98765432\",\"direccion\":\"Calle Principal 123\",\"codigo_postal\":\"28001\",\"ciudad\":\"Madrid\",\"provincia\":\"Madrid\",\"pais\":\"Espa\\u00f1a\",\"telefono\":\"912345678\",\"email\":\"info@empresatest.com\",\"persona_contacto\":\"Juan P\\u00e9rez\",\"telefono_contacto\":\"666555444\",\"email_contacto\":\"juan.perez@empresatest.com\",\"condiciones_pago\":\"30 d\\u00edas\",\"retencion_porcentaje\":\"0.00\",\"notas\":null,\"activo\":true,\"created_at\":\"2026-01-21T21:38:58.000000Z\",\"updated_at\":\"2026-01-21T21:39:24.000000Z\",\"deleted_at\":null}',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-21 21:39:47'),(5,1,'crear','vehiculos',2,NULL,'{\"vehiculo_tipo_id\":\"2\",\"matricula\":\"1233AVB\",\"marca\":\"Ford\",\"modelo\":\"Fiesta\",\"numero_bastidor\":\"123\",\"fecha_matriculacion\":\"2026-01-22T05:00:00.000000Z\",\"fecha_compra\":\"2026-01-22T05:00:00.000000Z\",\"fecha_ultima_itv\":\"2026-01-23T05:00:00.000000Z\",\"fecha_proxima_itv\":\"2026-01-30T05:00:00.000000Z\",\"compania_seguro\":\"Mapfre\",\"numero_poliza\":\"1345641\",\"fecha_vencimiento_seguro\":\"2026-01-30T05:00:00.000000Z\",\"coste_adquisicion\":\"2000.00\",\"coste_dia\":\"20.00\",\"kilometraje_actual\":\"2000\",\"conductor_habitual_id\":\"11\",\"notas\":\"Nota\",\"estado\":\"operativo\",\"updated_at\":\"2026-01-22T15:14:30.000000Z\",\"created_at\":\"2026-01-22T15:14:30.000000Z\",\"id\":2}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-22 15:14:30'),(6,1,'crear','subcontratas',4,NULL,'{\"nombre\":\"Subcontrata\",\"razon_social\":\"Subcontrata\",\"cif\":\"a123\",\"direccion\":\"Calle 69 #10-15\\r\\ncasa\",\"telefono\":\"3202230467\",\"email\":\"4324@gmail.com\",\"persona_contacto\":\"Santiago\",\"tarifa_hora\":\"20.00\",\"tarifa_dia\":\"20.00\",\"notas\":\"Nota\",\"activa\":true,\"homologada\":false,\"updated_at\":\"2026-01-22T15:15:53.000000Z\",\"created_at\":\"2026-01-22T15:15:53.000000Z\",\"id\":4}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-22 15:15:53'),(7,1,'crear','trabajadores',12,NULL,'{\"tipo_relacion\":\"subcontrata\",\"nombre\":\"traba sub\",\"apellidos\":\"traba sub\",\"dni\":\"23423423\",\"email\":\"trabasub@gmail.com\",\"telefono\":\"320223990\",\"direccion\":\"Calle 69 #10-15\",\"fecha_nacimiento\":\"2026-01-22T05:00:00.000000Z\",\"fecha_alta\":\"2026-01-22T05:00:00.000000Z\",\"categoria_convenio\":\"Peon\",\"salario_bruto_mensual\":null,\"coste_empresa_dia\":null,\"coste_hora\":null,\"vacaciones_anuales\":\"22\",\"subcontrata_id\":\"4\",\"activo\":true,\"vacaciones_acumuladas\":\"0.00\",\"antiguedad\":\"2026-01-22T05:00:00.000000Z\",\"updated_at\":\"2026-01-22T15:19:31.000000Z\",\"created_at\":\"2026-01-22T15:19:31.000000Z\",\"id\":12}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-22 15:19:31'),(8,1,'crear','contratos',2,NULL,'{\"contrato_tipo_id\":\"1\",\"codigo\":\"CTR-2026-00014\",\"titulo\":\"El mejor\",\"descripcion\":\"El mejor\",\"cliente_id\":\"5\",\"subcontrata_id\":null,\"fecha_inicio\":\"2026-01-22T05:00:00.000000Z\",\"fecha_fin\":\"2026-01-31T05:00:00.000000Z\",\"fecha_firma\":\"2026-01-22T05:00:00.000000Z\",\"importe\":\"20.00\",\"iva_porcentaje\":\"21.00\",\"tiene_retencion\":true,\"retencion_porcentaje\":\"5.00\",\"importe_retenido\":\"1.00\",\"fecha_liberacion_garantia\":\"2026-01-23T05:00:00.000000Z\",\"estado\":\"borrador\",\"notas\":\"Notas\",\"renovacion_automatica\":true,\"dias_preaviso_vencimiento\":\"30\",\"estado_garantia\":\"pendiente\",\"updated_at\":\"2026-01-22T15:28:14.000000Z\",\"created_at\":\"2026-01-22T15:28:14.000000Z\",\"id\":2,\"documento_path\":\"uploads\\/contratos\\/2\\/contrato_1769095694.pdf\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-22 15:28:14'),(9,1,'crear','ingresos',2,NULL,'{\"obra_id\":\"4\",\"cliente_id\":\"5\",\"concepto\":\"Bueno\",\"descripcion\":null,\"importe\":\"232.00\",\"iva_porcentaje\":\"21.00\",\"iva_importe\":\"48.72\",\"retencion_porcentaje\":\"10.00\",\"retencion_importe\":\"23.20\",\"importe_total\":\"257.52\",\"fecha\":\"2026-01-22T05:00:00.000000Z\",\"fecha_prevista_cobro\":\"2026-01-30T05:00:00.000000Z\",\"forma_pago\":\"Transferencia\",\"notas\":\"Nota\",\"estado\":\"pendiente\",\"updated_at\":\"2026-01-22T15:30:13.000000Z\",\"created_at\":\"2026-01-22T15:30:13.000000Z\",\"id\":2}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-22 15:30:13'),(10,1,'crear','gastos',2,NULL,'{\"gasto_categoria_id\":\"4\",\"obra_id\":\"4\",\"proveedor\":\"Bueno\",\"concepto\":\"Bueno\",\"descripcion\":\"Bueno\",\"importe\":\"12321.00\",\"iva_porcentaje\":\"21.00\",\"iva_importe\":\"2587.41\",\"importe_total\":\"14908.41\",\"fecha\":\"2026-01-22T05:00:00.000000Z\",\"fecha_vencimiento\":\"2026-01-23T05:00:00.000000Z\",\"forma_pago\":\"Efectivo\",\"notas\":\"Notas\",\"estado\":\"pendiente\",\"updated_at\":\"2026-01-22T15:32:01.000000Z\",\"created_at\":\"2026-01-22T15:32:01.000000Z\",\"id\":2}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-22 15:32:01'),(11,1,'crear','facturas',5,NULL,'{\"id\":5,\"numero\":null,\"serie\":\"F\",\"cliente_id\":5,\"obra_id\":4,\"fecha_emision\":\"2026-01-22T05:00:00.000000Z\",\"fecha_vencimiento\":\"2026-01-30T05:00:00.000000Z\",\"base_imponible\":\"36.00\",\"iva_porcentaje\":\"21.00\",\"iva_importe\":\"7.56\",\"retencion_porcentaje\":\"10.00\",\"retencion_importe\":\"3.60\",\"total\":\"39.96\",\"estado\":\"borrador\",\"fecha_cobro\":null,\"pdf_path\":null,\"email_enviado\":0,\"email_enviado_at\":null,\"notas\":\"Notas\",\"created_at\":\"2026-01-22T15:33:38.000000Z\",\"updated_at\":\"2026-01-22T15:33:38.000000Z\",\"lineas\":[{\"id\":6,\"factura_id\":5,\"concepto\":\"Tala\",\"descripcion\":\"Tala\",\"cantidad\":\"2.00\",\"precio_unitario\":\"20.00\",\"descuento_porcentaje\":\"10.00\",\"importe\":\"36.00\",\"orden\":0,\"created_at\":\"2026-01-22 10:33:38\"}]}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-22 15:33:38'),(12,1,'crear','epi_inventario',4,NULL,'{\"epi_catalogo_id\":\"18\",\"numero_serie\":\"216351\",\"fecha_compra\":\"2026-01-22T05:00:00.000000Z\",\"fecha_caducidad\":\"2026-01-31T05:00:00.000000Z\",\"coste\":\"123.00\",\"notas\":null,\"estado\":\"disponible\",\"updated_at\":\"2026-01-22T15:38:02.000000Z\",\"created_at\":\"2026-01-22T15:38:02.000000Z\",\"id\":4}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-22 15:38:02'),(13,1,'editar','epi_inventario',4,'{\"id\":4,\"epi_catalogo_id\":18,\"numero_serie\":\"216351\",\"fecha_compra\":\"2026-01-22T05:00:00.000000Z\",\"fecha_caducidad\":\"2026-01-31T05:00:00.000000Z\",\"coste\":\"123.00\",\"estado\":\"asignado\",\"notas\":null,\"created_at\":\"2026-01-22T15:38:02.000000Z\",\"updated_at\":\"2026-01-22T15:39:32.000000Z\"}','{\"id\":4,\"epi_catalogo_id\":44,\"numero_serie\":\"216351\",\"fecha_compra\":\"2026-01-22T05:00:00.000000Z\",\"fecha_caducidad\":\"2026-01-31T05:00:00.000000Z\",\"coste\":\"123.00\",\"estado\":\"asignado\",\"notas\":null,\"created_at\":\"2026-01-22T15:38:02.000000Z\",\"updated_at\":\"2026-01-22T15:41:05.000000Z\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-22 15:41:05'),(14,1,'crear','facturas',6,NULL,'{\"id\":6,\"numero\":null,\"serie\":\"F\",\"cliente_id\":5,\"obra_id\":4,\"fecha_emision\":\"2026-01-22T05:00:00.000000Z\",\"fecha_vencimiento\":null,\"base_imponible\":\"34.00\",\"iva_porcentaje\":\"21.00\",\"iva_importe\":\"7.14\",\"retencion_porcentaje\":\"10.00\",\"retencion_importe\":\"3.40\",\"total\":\"37.74\",\"estado\":\"borrador\",\"fecha_cobro\":null,\"pdf_path\":null,\"email_enviado\":0,\"email_enviado_at\":null,\"notas\":\"notas\",\"created_at\":\"2026-01-22T15:59:54.000000Z\",\"updated_at\":\"2026-01-22T15:59:54.000000Z\",\"lineas\":[{\"id\":7,\"factura_id\":6,\"concepto\":\"Tala\",\"descripcion\":\"Tala\",\"cantidad\":\"1.00\",\"precio_unitario\":\"34.00\",\"descuento_porcentaje\":\"0.00\",\"importe\":\"34.00\",\"orden\":0,\"created_at\":\"2026-01-22 10:59:54\"}]}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-22 15:59:54'),(15,1,'crear','trabajadores',13,NULL,'{\"tipo_relacion\":\"propio\",\"nombre\":\"sebas\",\"apellidos\":\"sebas\",\"dni\":\"34234532\",\"email\":\"anonyb001@hotmail.com\",\"telefono\":\"3202230462\",\"direccion\":\"Calle 69 #10-15\",\"fecha_nacimiento\":null,\"fecha_alta\":\"2026-02-04T05:00:00.000000Z\",\"categoria_convenio\":\"Peon\",\"salario_bruto_mensual\":\"51.00\",\"coste_empresa_dia\":\"654.00\",\"coste_hora\":\"654.00\",\"vacaciones_anuales\":\"22\",\"subcontrata_id\":null,\"activo\":true,\"vacaciones_acumuladas\":\"0.00\",\"antiguedad\":\"2026-02-04T05:00:00.000000Z\",\"updated_at\":\"2026-02-04T18:54:54.000000Z\",\"created_at\":\"2026-02-04T18:54:54.000000Z\",\"id\":13}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-04 18:54:54'),(16,1,'crear','trabajadores',14,NULL,'{\"tipo_relacion\":\"propio\",\"nombre\":\"aaaa\",\"apellidos\":\"aaaa\",\"dni\":\"111111\",\"email\":\"santiagobc001@outlook.es\",\"telefono\":\"235432\",\"direccion\":\"Calle 69 #10-15\",\"fecha_nacimiento\":\"2003-06-10T05:00:00.000000Z\",\"fecha_alta\":\"2026-02-04T05:00:00.000000Z\",\"categoria_convenio\":\"Peon\",\"salario_bruto_mensual\":\"54.00\",\"coste_empresa_dia\":\"984.00\",\"coste_hora\":\"85.00\",\"vacaciones_anuales\":\"22\",\"subcontrata_id\":null,\"activo\":true,\"vacaciones_acumuladas\":\"0.00\",\"antiguedad\":\"2026-02-04T05:00:00.000000Z\",\"updated_at\":\"2026-02-04T19:14:18.000000Z\",\"created_at\":\"2026-02-04T19:14:18.000000Z\",\"id\":14,\"user_id\":10}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-04 19:14:30'),(17,1,'editar','trabajadores',14,'{\"id\":14,\"user_id\":10,\"tipo_relacion\":\"propio\",\"nombre\":\"aaaa\",\"apellidos\":\"aaaa\",\"dni\":\"111111\",\"email\":\"santiagobc001@outlook.es\",\"telefono\":\"235432\",\"direccion\":\"Calle 69 #10-15\",\"fecha_nacimiento\":\"2003-06-10T05:00:00.000000Z\",\"fecha_alta\":\"2026-02-04T05:00:00.000000Z\",\"fecha_baja\":null,\"categoria_convenio\":\"Peon\",\"salario_bruto_mensual\":\"54.00\",\"coste_empresa_dia\":\"984.00\",\"coste_hora\":\"85.00\",\"vacaciones_anuales\":22,\"vacaciones_acumuladas\":\"0.00\",\"antiguedad\":\"2026-02-04T05:00:00.000000Z\",\"subcontrata_id\":null,\"activo\":true,\"created_at\":\"2026-02-04T19:14:18.000000Z\",\"updated_at\":\"2026-02-04T19:14:18.000000Z\",\"deleted_at\":null}','{\"id\":14,\"user_id\":10,\"tipo_relacion\":\"subcontrata\",\"nombre\":\"aaaa\",\"apellidos\":\"aaaa\",\"dni\":\"111111\",\"email\":\"santiagobc001@outlook.es\",\"telefono\":\"235432\",\"direccion\":\"Calle 69 #10-15\",\"fecha_nacimiento\":\"2003-06-10T05:00:00.000000Z\",\"fecha_alta\":\"2026-02-04T05:00:00.000000Z\",\"fecha_baja\":null,\"categoria_convenio\":\"Peon\",\"salario_bruto_mensual\":\"54.00\",\"coste_empresa_dia\":\"984.00\",\"coste_hora\":\"85.00\",\"vacaciones_anuales\":22,\"vacaciones_acumuladas\":\"0.00\",\"antiguedad\":\"2026-02-04T05:00:00.000000Z\",\"subcontrata_id\":4,\"activo\":true,\"created_at\":\"2026-02-04T19:14:18.000000Z\",\"updated_at\":\"2026-02-04T19:16:56.000000Z\",\"deleted_at\":null}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-04 19:16:56'),(18,1,'crear','facturas',7,NULL,'{\"id\":7,\"numero\":null,\"serie\":\"F\",\"cliente_id\":5,\"obra_id\":4,\"fecha_emision\":\"2026-02-12T05:00:00.000000Z\",\"fecha_vencimiento\":null,\"base_imponible\":\"3313.70\",\"iva_porcentaje\":\"21.00\",\"iva_importe\":\"695.88\",\"retencion_porcentaje\":\"10.00\",\"retencion_importe\":\"331.37\",\"total\":\"3678.21\",\"estado\":\"borrador\",\"fecha_cobro\":null,\"pdf_path\":null,\"email_enviado\":0,\"email_enviado_at\":null,\"notas\":null,\"footer_text\":\"MANZER AGROFORESTAL, S.R.L.U. | CIF: B12345678 | Inscrita en el Registro Mercantil de Barcelona\",\"created_at\":\"2026-02-12T16:50:38.000000Z\",\"updated_at\":\"2026-02-12T16:50:38.000000Z\",\"lineas\":[{\"id\":8,\"factura_id\":7,\"concepto\":\"a\",\"descripcion\":\"a\",\"cantidad\":\"1.00\",\"precio_unitario\":\"32.00\",\"descuento_porcentaje\":\"0.00\",\"importe\":\"32.00\",\"orden\":0,\"grupo\":\"l22\",\"created_at\":\"2026-02-12 11:50:38\"},{\"id\":9,\"factura_id\":7,\"concepto\":\"b\",\"descripcion\":\"b\",\"cantidad\":\"1.00\",\"precio_unitario\":\"20.00\",\"descuento_porcentaje\":\"0.00\",\"importe\":\"20.00\",\"orden\":1,\"grupo\":\"l22\",\"created_at\":\"2026-02-12 11:50:38\"},{\"id\":10,\"factura_id\":7,\"concepto\":\"c\",\"descripcion\":\"c\",\"cantidad\":\"1.00\",\"precio_unitario\":\"33.00\",\"descuento_porcentaje\":\"10.00\",\"importe\":\"29.70\",\"orden\":2,\"grupo\":\"l23\",\"created_at\":\"2026-02-12 11:50:38\"},{\"id\":11,\"factura_id\":7,\"concepto\":\"d\",\"descripcion\":\"d\",\"cantidad\":\"1.00\",\"precio_unitario\":\"3232.00\",\"descuento_porcentaje\":\"0.00\",\"importe\":\"3232.00\",\"orden\":3,\"grupo\":\"l23\",\"created_at\":\"2026-02-12 11:50:38\"}]}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-02-12 16:50:38'),(19,1,'editar','trabajadores',11,'{\"id\":11,\"user_id\":8,\"tipo_relacion\":\"propio\",\"nombre\":\"Santi\",\"apellidos\":\"Bellaizan\",\"dni\":\"12312312321\",\"email\":\"vblogsanti@gmail.com\",\"telefono\":\"3202230467\",\"direccion\":\"Calle 69 #10-15\",\"iban\":null,\"fecha_nacimiento\":\"1999-06-25T05:00:00.000000Z\",\"fecha_alta\":\"2025-12-23T05:00:00.000000Z\",\"fecha_baja\":null,\"categoria_convenio\":\"Pe\\u00f3n Forestal\",\"salario_bruto_mensual\":\"20000.00\",\"coste_empresa_dia\":\"234324.00\",\"coste_hora\":\"343.00\",\"vacaciones_anuales\":22,\"vacaciones_acumuladas\":\"0.00\",\"antiguedad\":\"2025-12-23T05:00:00.000000Z\",\"subcontrata_id\":null,\"activo\":true,\"created_at\":\"2025-12-23T15:28:14.000000Z\",\"updated_at\":\"2026-01-19T23:09:17.000000Z\",\"deleted_at\":null}','{\"id\":11,\"user_id\":8,\"tipo_relacion\":\"propio\",\"nombre\":\"Santi\",\"apellidos\":\"Bellaizan\",\"dni\":\"12312312321\",\"email\":\"vblogsanti@gmail.com\",\"telefono\":\"3202230467\",\"direccion\":\"Calle 69 #10-15\",\"iban\":\"ES6621000418401234567891\",\"fecha_nacimiento\":\"1999-06-25T05:00:00.000000Z\",\"fecha_alta\":\"2025-12-23T05:00:00.000000Z\",\"fecha_baja\":null,\"categoria_convenio\":\"Pe\\u00f3n Forestal\",\"salario_bruto_mensual\":\"20000.00\",\"coste_empresa_dia\":\"234324.00\",\"coste_hora\":\"343.00\",\"vacaciones_anuales\":22,\"vacaciones_acumuladas\":\"0.00\",\"antiguedad\":\"2025-12-23T05:00:00.000000Z\",\"subcontrata_id\":null,\"activo\":true,\"created_at\":\"2025-12-23T15:28:14.000000Z\",\"updated_at\":\"2026-03-02T21:58:16.000000Z\",\"deleted_at\":null}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','2026-03-02 21:58:16');
/*!40000 ALTER TABLE `auditoria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `caducidades_generales`
--

DROP TABLE IF EXISTS `caducidades_generales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `caducidades_generales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tipo` varchar(100) NOT NULL COMMENT 'seguro_rc, iso, certificacion, etc.',
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_emision` date DEFAULT NULL,
  `fecha_caducidad` date NOT NULL,
  `documento_path` varchar(500) DEFAULT NULL,
  `alerta_activa` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caducidades_generales`
--

LOCK TABLES `caducidades_generales` WRITE;
/*!40000 ALTER TABLE `caducidades_generales` DISABLE KEYS */;
INSERT INTO `caducidades_generales` VALUES (1,'iso','Certificación ISO 9001:2015','Sistema de gestión de calidad','2025-01-19','2026-01-29',NULL,1,'2026-01-19 17:14:52','2026-01-19 17:14:52'),(2,'seguro_rc','Seguro Responsabilidad Civil','Póliza anual de RC','2025-01-19','2026-01-24',NULL,1,'2026-01-19 17:14:52','2026-01-19 17:14:52'),(3,'licencia','Licencia de Actividad Municipal',NULL,NULL,'2026-01-16',NULL,1,'2026-01-19 17:14:52','2026-01-19 17:14:52'),(4,'iso','Iso 2000','Descrip','2026-01-22','2026-01-29','uploads/caducidades/2026/1769097249_1.pdf',1,'2026-01-22 15:54:09','2026-01-22 15:54:09');
/*!40000 ALTER TABLE `caducidades_generales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cliente_emails_adicionales`
--

DROP TABLE IF EXISTS `cliente_emails_adicionales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cliente_emails_adicionales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cliente_id` bigint(20) unsigned NOT NULL,
  `email` varchar(255) NOT NULL,
  `nombre` varchar(150) DEFAULT NULL COMMENT 'Nombre del contacto',
  `cargo` varchar(150) DEFAULT NULL COMMENT 'Cargo/Puesto',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `enviar_facturas_por_defecto` tinyint(1) NOT NULL DEFAULT 0,
  `notas` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cliente_emails_adicionales_cliente_id_activo_index` (`cliente_id`,`activo`),
  KEY `cliente_emails_adicionales_email_index` (`email`),
  CONSTRAINT `cliente_emails_adicionales_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cliente_emails_adicionales`
--

LOCK TABLES `cliente_emails_adicionales` WRITE;
/*!40000 ALTER TABLE `cliente_emails_adicionales` DISABLE KEYS */;
/*!40000 ALTER TABLE `cliente_emails_adicionales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clientes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tipo` enum('publico','privado') NOT NULL,
  `nombre_comercial` varchar(255) NOT NULL,
  `razon_social` varchar(255) DEFAULT NULL,
  `cif` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `codigo_postal` varchar(10) DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `provincia` varchar(100) DEFAULT NULL,
  `pais` varchar(100) NOT NULL DEFAULT 'España',
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `persona_contacto` varchar(150) DEFAULT NULL,
  `telefono_contacto` varchar(20) DEFAULT NULL,
  `email_contacto` varchar(255) DEFAULT NULL,
  `condiciones_pago` varchar(100) DEFAULT NULL COMMENT 'Ej: 30 días, 60 días',
  `retencion_porcentaje` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT '% de retención en obras',
  `notas` text DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (1,'publico','ADIF Alta Velocidad','Administrador de Infraestructuras Ferroviarias','Q2801660H','Calle Sor Ángela de la Cruz, 3','28020','Madrid','Madrid','España','912 007 000','info@adif.es','Pedro Sánchez',NULL,NULL,'60 días',5.00,NULL,1,'2025-12-23 14:13:27','2025-12-23 14:13:27',NULL),(2,'publico','Ayuntamiento de Manresa','Ajuntament de Manresa','P0811500J','Plaça Major, 1','08241','Manresa','Barcelona','España','938 782 300','ajuntament@manresa.cat','Marta Vila',NULL,NULL,'30 días',0.00,NULL,1,'2025-12-23 14:13:27','2025-12-23 14:13:27',NULL),(3,'privado','Forestal Catalunya','Forestal Catalunya SL','B12345678','Carrer Major, 45','08600','Berga','Barcelona','España','938 210 000','info@forestalcatalunya.com','Joan Puig',NULL,NULL,'30 días',0.00,NULL,1,'2025-12-23 14:13:27','2025-12-23 14:13:27',NULL),(4,'publico','Diputación de Barcelona','Diputació de Barcelona','P0800000B','Rambla de Catalunya, 126','08008','Barcelona','Barcelona','España','934 022 222','info@diba.cat','Laura Costa',NULL,NULL,'45 días',0.00,NULL,1,'2025-12-23 14:13:27','2025-12-23 14:13:27',NULL),(5,'privado','Santi ltda','Santi SA','a123','Calle 69 #10-15','4234','Bogotá','Distrito Capital','España','3202230467','vblogsanti@gmail.com','Santiago','Bc','vblogsanti@gmail.com','30 días',10.00,'Buena',1,'2025-12-26 22:23:59','2025-12-26 22:23:59',NULL),(6,'privado','Empresa Test MCP Modificada','Empresa Test MCP S.L.','B98765432','Calle Principal 123','28001','Madrid','Madrid','España','912345678','info@empresatest.com','Juan Pérez','666555444','juan.perez@empresatest.com','30 días',0.00,NULL,1,'2026-01-21 21:38:58','2026-01-21 21:39:47','2026-01-21 21:39:47');
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contrato_liberaciones`
--

DROP TABLE IF EXISTS `contrato_liberaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contrato_liberaciones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `contrato_id` bigint(20) unsigned NOT NULL,
  `porcentaje_liberado` tinyint(3) unsigned NOT NULL,
  `importe_liberado` decimal(12,2) NOT NULL,
  `fecha_liberacion` date NOT NULL,
  `notas` text DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contrato_liberaciones_user_id_foreign` (`user_id`),
  KEY `contrato_liberaciones_contrato_id_fecha_liberacion_index` (`contrato_id`,`fecha_liberacion`),
  CONSTRAINT `contrato_liberaciones_contrato_id_foreign` FOREIGN KEY (`contrato_id`) REFERENCES `contratos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `contrato_liberaciones_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contrato_liberaciones`
--

LOCK TABLES `contrato_liberaciones` WRITE;
/*!40000 ALTER TABLE `contrato_liberaciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `contrato_liberaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contrato_tipos`
--

DROP TABLE IF EXISTS `contrato_tipos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contrato_tipos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL COMMENT 'Fijo, Esporádico, Servicios, Salud',
  `descripcion` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contrato_tipos`
--

LOCK TABLES `contrato_tipos` WRITE;
/*!40000 ALTER TABLE `contrato_tipos` DISABLE KEYS */;
INSERT INTO `contrato_tipos` VALUES (1,'Fijo','Contrato de servicios fijo','2025-12-23 14:13:27'),(2,'Esporádico','Trabajos puntuales','2025-12-23 14:13:27'),(3,'Servicios','Contrato de servicios profesionales','2025-12-23 14:13:27'),(4,'Mantenimiento','Contrato de mantenimiento periódico','2025-12-23 14:13:27');
/*!40000 ALTER TABLE `contrato_tipos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contratos`
--

DROP TABLE IF EXISTS `contratos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contratos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `contrato_tipo_id` bigint(20) unsigned NOT NULL,
  `codigo` varchar(50) DEFAULT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `cliente_id` bigint(20) unsigned DEFAULT NULL,
  `subcontrata_id` bigint(20) unsigned DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `fecha_firma` date DEFAULT NULL,
  `importe` decimal(14,2) DEFAULT NULL,
  `iva_porcentaje` decimal(5,2) NOT NULL DEFAULT 21.00,
  `tiene_retencion` tinyint(1) NOT NULL DEFAULT 0,
  `retencion_porcentaje` decimal(5,2) DEFAULT NULL,
  `importe_retenido` decimal(12,2) DEFAULT NULL,
  `fecha_liberacion_garantia` date DEFAULT NULL,
  `estado_garantia` enum('pendiente','retenida','parcialmente_liberada','liberada') DEFAULT NULL,
  `fecha_liberacion_real` date DEFAULT NULL,
  `porcentaje_total_liberado` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `importe_total_liberado` decimal(12,2) NOT NULL DEFAULT 0.00,
  `estado` enum('borrador','activo','vencido','cancelado') NOT NULL DEFAULT 'borrador',
  `responsable_id` bigint(20) unsigned DEFAULT NULL,
  `documento_path` varchar(500) DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `renovacion_automatica` tinyint(1) NOT NULL DEFAULT 0,
  `dias_preaviso_vencimiento` smallint(5) unsigned NOT NULL DEFAULT 30,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `contratos_codigo_unique` (`codigo`),
  KEY `contratos_contrato_tipo_id_foreign` (`contrato_tipo_id`),
  KEY `contratos_cliente_id_foreign` (`cliente_id`),
  KEY `contratos_subcontrata_id_foreign` (`subcontrata_id`),
  KEY `contratos_responsable_id_foreign` (`responsable_id`),
  CONSTRAINT `contratos_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `contratos_contrato_tipo_id_foreign` FOREIGN KEY (`contrato_tipo_id`) REFERENCES `contrato_tipos` (`id`),
  CONSTRAINT `contratos_responsable_id_foreign` FOREIGN KEY (`responsable_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `contratos_subcontrata_id_foreign` FOREIGN KEY (`subcontrata_id`) REFERENCES `subcontratas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contratos`
--

LOCK TABLES `contratos` WRITE;
/*!40000 ALTER TABLE `contratos` DISABLE KEYS */;
INSERT INTO `contratos` VALUES (1,1,'CTR-2026-0001','Contrato de Prueba MCP - Editado',NULL,1,NULL,NULL,NULL,NULL,10000.00,21.00,1,5.00,500.00,NULL,'liberada','2026-01-18',0,0.00,'activo',NULL,NULL,NULL,0,30,'2026-01-19 00:19:09','2026-01-19 00:21:27','2026-01-19 00:21:27'),(2,1,'CTR-2026-00014','El mejor','El mejor',5,NULL,'2026-01-22','2026-01-31','2026-01-22',20.00,21.00,1,5.00,1.00,'2026-01-23','retenida','2026-01-22',0,0.00,'activo',NULL,'uploads/contratos/2/contrato_1769095694.pdf','Notas',1,30,'2026-01-22 15:28:14','2026-01-22 15:29:22',NULL);
/*!40000 ALTER TABLE `contratos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cuadrilla_trabajadores`
--

DROP TABLE IF EXISTS `cuadrilla_trabajadores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cuadrilla_trabajadores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cuadrilla_id` bigint(20) unsigned NOT NULL,
  `trabajador_id` bigint(20) unsigned NOT NULL,
  `fecha_incorporacion` date NOT NULL,
  `fecha_salida` date DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_cuadrilla_trabajador_activo` (`cuadrilla_id`,`trabajador_id`,`activo`),
  KEY `cuadrilla_trabajadores_trabajador_id_foreign` (`trabajador_id`),
  CONSTRAINT `cuadrilla_trabajadores_cuadrilla_id_foreign` FOREIGN KEY (`cuadrilla_id`) REFERENCES `cuadrillas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cuadrilla_trabajadores_trabajador_id_foreign` FOREIGN KEY (`trabajador_id`) REFERENCES `trabajadores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cuadrilla_trabajadores`
--

LOCK TABLES `cuadrilla_trabajadores` WRITE;
/*!40000 ALTER TABLE `cuadrilla_trabajadores` DISABLE KEYS */;
INSERT INTO `cuadrilla_trabajadores` VALUES (1,1,1,'2025-06-23','2025-12-23',0,'2025-12-23 14:13:27','2025-12-23 14:13:27'),(2,1,2,'2025-06-23',NULL,1,'2025-12-23 14:13:27','2025-12-23 14:13:27'),(3,1,3,'2025-06-23',NULL,1,'2025-12-23 14:13:27','2025-12-23 14:13:27'),(4,1,4,'2025-06-23','2025-12-23',0,'2025-12-23 14:13:27','2025-12-23 14:13:27'),(5,1,5,'2025-06-23',NULL,1,'2025-12-23 14:13:27','2025-12-23 14:13:27'),(6,2,7,'2025-09-23',NULL,1,'2025-12-23 14:13:27','2025-12-23 14:13:27'),(7,2,8,'2025-09-23',NULL,1,'2025-12-23 14:13:27','2025-12-23 14:13:27'),(8,2,9,'2025-09-23',NULL,1,'2025-12-23 14:13:27','2025-12-23 14:13:27'),(9,2,10,'2025-09-23',NULL,1,'2025-12-23 14:13:27','2025-12-23 14:13:27'),(10,1,11,'2025-12-23',NULL,1,'2025-12-23 15:28:14','2025-12-23 15:28:14'),(11,3,1,'2025-12-23','2025-12-23',0,'2025-12-23 15:44:12','2025-12-23 15:44:24'),(12,3,4,'2025-12-23',NULL,1,'2025-12-23 15:44:31','2025-12-23 15:44:31'),(13,2,12,'2026-01-22',NULL,1,'2026-01-22 15:19:31','2026-01-22 15:19:31'),(14,3,13,'2026-02-04',NULL,1,'2026-02-04 18:54:54','2026-02-04 18:54:54'),(15,3,14,'2026-02-04',NULL,1,'2026-02-04 19:14:30','2026-02-04 19:14:30');
/*!40000 ALTER TABLE `cuadrilla_trabajadores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cuadrillas`
--

DROP TABLE IF EXISTS `cuadrillas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cuadrillas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `capataz_id` bigint(20) unsigned DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cuadrillas_capataz_id_foreign` (`capataz_id`),
  CONSTRAINT `cuadrillas_capataz_id_foreign` FOREIGN KEY (`capataz_id`) REFERENCES `trabajadores` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cuadrillas`
--

LOCK TABLES `cuadrillas` WRITE;
/*!40000 ALTER TABLE `cuadrillas` DISABLE KEYS */;
INSERT INTO `cuadrillas` VALUES (1,'Cuadrilla Alpha',1,'Cuadrilla principal de desbroce',1,'2025-12-23 14:13:27','2025-12-23 15:44:31'),(2,'Cuadrilla Beta',7,'Cuadrilla de tala y poda',1,'2025-12-23 14:13:27','2025-12-23 14:13:27'),(3,'Limones',11,'mejor',1,'2025-12-23 15:29:39','2025-12-23 15:29:39'),(4,'JOHANNA ANDREA CASAS GOMEZ',7,NULL,1,'2026-02-03 20:24:35','2026-02-03 20:24:35');
/*!40000 ALTER TABLE `cuadrillas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cumpleanos_configuracion`
--

DROP TABLE IF EXISTS `cumpleanos_configuracion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cumpleanos_configuracion` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `activa` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Activar/desactivar envio de emails de cumpleanos',
  `asunto` varchar(255) NOT NULL DEFAULT '¡Feliz Cumpleaños, {nombre}!' COMMENT 'Asunto del email, soporta placeholders',
  `cuerpo` text NOT NULL COMMENT 'Cuerpo HTML del email, soporta placeholders',
  `adjunto_path` varchar(255) DEFAULT NULL COMMENT 'Ruta al archivo adjunto',
  `adjunto_nombre_original` varchar(255) DEFAULT NULL COMMENT 'Nombre original del archivo adjunto',
  `hora_envio` varchar(5) NOT NULL DEFAULT '08:00' COMMENT 'Hora del dia para enviar HH:MM',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cumpleanos_configuracion`
--

LOCK TABLES `cumpleanos_configuracion` WRITE;
/*!40000 ALTER TABLE `cumpleanos_configuracion` DISABLE KEYS */;
INSERT INTO `cumpleanos_configuracion` VALUES (1,0,'¡Feliz Cumpleaños, {nombre}!','<p>Querido/a <strong>{nombre_completo}</strong>,</p><p>Desde Manzer Agroforestal queremos desearte un muy feliz cumpleaños. 🎂</p><p>Esperamos que pases un día estupendo rodeado/a de los tuyos.</p><p>¡Un fuerte abrazo de todo el equipo!</p><p><strong>Manzer Agroforestal, S.R.L.U.</strong></p>',NULL,NULL,'08:00','2026-03-02 23:03:02','2026-03-02 23:03:02');
/*!40000 ALTER TABLE `cumpleanos_configuracion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documento_lecturas`
--

DROP TABLE IF EXISTS `documento_lecturas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documento_lecturas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `documento_id` bigint(20) unsigned NOT NULL,
  `trabajador_id` bigint(20) unsigned NOT NULL,
  `fecha_lectura` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `aceptado` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `documento_lecturas_documento_id_foreign` (`documento_id`),
  KEY `documento_lecturas_trabajador_id_foreign` (`trabajador_id`),
  CONSTRAINT `documento_lecturas_documento_id_foreign` FOREIGN KEY (`documento_id`) REFERENCES `trabajador_documentos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documento_lecturas_trabajador_id_foreign` FOREIGN KEY (`trabajador_id`) REFERENCES `trabajadores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documento_lecturas`
--

LOCK TABLES `documento_lecturas` WRITE;
/*!40000 ALTER TABLE `documento_lecturas` DISABLE KEYS */;
/*!40000 ALTER TABLE `documento_lecturas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documentos_empresa`
--

DROP TABLE IF EXISTS `documentos_empresa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documentos_empresa` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `categoria` enum('legal','fiscal','laboral','certificaciones','seguros','contratos','procedimientos','otro') NOT NULL DEFAULT 'otro',
  `archivo_path` varchar(500) NOT NULL,
  `archivo_nombre_original` varchar(255) NOT NULL,
  `archivo_extension` varchar(10) NOT NULL,
  `archivo_tamaño` bigint(20) unsigned DEFAULT NULL,
  `fecha_documento` date DEFAULT NULL,
  `fecha_caducidad` date DEFAULT NULL,
  `visible_solo_admin` tinyint(1) NOT NULL DEFAULT 1,
  `notas` text DEFAULT NULL,
  `subido_por` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documentos_empresa_subido_por_foreign` (`subido_por`),
  KEY `documentos_empresa_categoria_created_at_index` (`categoria`,`created_at`),
  KEY `documentos_empresa_fecha_caducidad_index` (`fecha_caducidad`),
  CONSTRAINT `documentos_empresa_subido_por_foreign` FOREIGN KEY (`subido_por`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documentos_empresa`
--

LOCK TABLES `documentos_empresa` WRITE;
/*!40000 ALTER TABLE `documentos_empresa` DISABLE KEYS */;
/*!40000 ALTER TABLE `documentos_empresa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_logs`
--

DROP TABLE IF EXISTS `email_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tipo` varchar(100) NOT NULL,
  `destinatario_email` varchar(255) NOT NULL,
  `destinatarios` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`destinatarios`)),
  `destinatario_id` bigint(20) unsigned DEFAULT NULL,
  `asunto` varchar(255) NOT NULL,
  `emailable_type` varchar(255) DEFAULT NULL,
  `emailable_id` bigint(20) unsigned DEFAULT NULL,
  `estado` enum('enviado','fallido','pendiente') NOT NULL DEFAULT 'pendiente',
  `error_message` text DEFAULT NULL,
  `enviado_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email_logs_destinatario_id_foreign` (`destinatario_id`),
  KEY `email_logs_emailable_type_emailable_id_index` (`emailable_type`,`emailable_id`),
  KEY `email_logs_tipo_estado_index` (`tipo`,`estado`),
  KEY `email_logs_destinatario_email_index` (`destinatario_email`),
  KEY `email_logs_created_at_index` (`created_at`),
  CONSTRAINT `email_logs_destinatario_id_foreign` FOREIGN KEY (`destinatario_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_logs`
--

LOCK TABLES `email_logs` WRITE;
/*!40000 ALTER TABLE `email_logs` DISABLE KEYS */;
INSERT INTO `email_logs` VALUES (1,'factura','vblogsanti@gmail.com',NULL,NULL,'Factura F-2026-00004 - Manzer Agroforestal','App\\Models\\Factura',5,'enviado',NULL,'2026-01-22 15:35:17','2026-01-22 15:35:17','2026-01-22 15:35:17'),(2,'factura','vblogsanti@gmail.com','[\"vblogsanti@gmail.com\"]',NULL,'Factura F-2026-00005 - Manzer Agroforestal','App\\Models\\Factura',7,'enviado',NULL,'2026-02-12 16:50:56','2026-02-12 16:50:56','2026-02-12 16:50:56');
/*!40000 ALTER TABLE `email_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `epi_catalogo`
--

DROP TABLE IF EXISTS `epi_catalogo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `epi_catalogo` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `categoria` varchar(100) DEFAULT NULL COMMENT 'Ej: Protección cabeza, Protección altura',
  `tiene_caducidad` tinyint(1) NOT NULL DEFAULT 0,
  `requiere_revision` tinyint(1) NOT NULL DEFAULT 0,
  `periodicidad_revision_meses` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `epi_catalogo`
--

LOCK TABLES `epi_catalogo` WRITE;
/*!40000 ALTER TABLE `epi_catalogo` DISABLE KEYS */;
INSERT INTO `epi_catalogo` VALUES (1,'Casco forestal','Protección cabeza',1,1,12,'2025-12-23 14:13:27','2025-12-23 14:13:27'),(2,'Protector auditivo','Protección auditiva',0,1,6,'2025-12-23 14:13:27','2025-12-23 14:13:27'),(3,'Gafas de seguridad','Protección ocular',0,0,NULL,'2025-12-23 14:13:27','2025-12-23 14:13:27'),(4,'Pantalla facial','Protección facial',0,1,12,'2025-12-23 14:13:27','2025-12-23 14:13:27'),(5,'Guantes anticorte','Protección manos',0,0,NULL,'2025-12-23 14:13:27','2025-12-23 14:13:27'),(6,'Botas anticorte','Protección pies',0,1,12,'2025-12-23 14:13:27','2025-12-23 14:13:27'),(7,'Pantalón anticorte','Protección piernas',0,1,12,'2025-12-23 14:13:27','2025-12-23 14:13:27'),(8,'Arnés anticaídas','Protección altura',1,1,12,'2025-12-23 14:13:27','2025-12-23 14:13:27'),(9,'Chaleco alta visibilidad','Visibilidad',0,0,NULL,'2025-12-23 14:13:27','2025-12-23 14:13:27'),(10,'Casco de seguridad','Proteccion de la cabeza',1,0,NULL,'2026-01-19 04:07:55','2026-01-19 04:07:55'),(11,'Casco forestal con pantalla y orejeras','Proteccion de la cabeza',1,1,12,'2026-01-19 04:07:55','2026-01-19 04:07:55'),(12,'Gafas de proteccion','Proteccion ocular y facial',0,0,NULL,'2026-01-19 04:07:55','2026-01-19 04:07:55'),(13,'Gafas de sol polarizadas','Proteccion ocular y facial',0,0,NULL,'2026-01-19 04:07:55','2026-01-19 04:07:55'),(14,'Tapones auditivos reutilizables','Proteccion auditiva',1,0,NULL,'2026-01-19 04:07:55','2026-01-19 04:07:55'),(15,'Tapones auditivos desechables','Proteccion auditiva',1,0,NULL,'2026-01-19 04:07:55','2026-01-19 04:07:55'),(16,'Orejeras','Proteccion auditiva',0,0,NULL,'2026-01-19 04:07:55','2026-01-19 04:07:55'),(17,'Mascarilla FFP2','Proteccion respiratoria',1,0,NULL,'2026-01-19 04:07:55','2026-01-19 04:07:55'),(18,'Mascarilla FFP3','Proteccion respiratoria',1,0,NULL,'2026-01-19 04:07:55','2026-01-19 04:07:55'),(19,'Mascara con filtros intercambiables','Proteccion respiratoria',1,1,6,'2026-01-19 04:07:55','2026-01-19 04:07:55'),(20,'Guantes anticorte (motosierra)','Proteccion de manos',0,1,6,'2026-01-19 04:07:55','2026-01-19 04:07:55'),(21,'Guantes de trabajo mecanico','Proteccion de manos',0,0,NULL,'2026-01-19 04:07:55','2026-01-19 04:07:55'),(22,'Guantes de nitrilo','Proteccion de manos',1,0,NULL,'2026-01-19 04:07:55','2026-01-19 04:07:55'),(23,'Guantes aislantes electricos','Proteccion de manos',1,1,12,'2026-01-19 04:07:55','2026-01-19 04:07:55'),(24,'Guantes anticorte nivel 5','Proteccion de manos',0,1,6,'2026-01-19 04:07:55','2026-01-19 04:07:55'),(25,'Botas de seguridad S3','Proteccion de pies',0,1,12,'2026-01-19 04:07:55','2026-01-19 04:07:55'),(26,'Botas anticorte (motosierra)','Proteccion de pies',0,1,6,'2026-01-19 04:07:55','2026-01-19 04:07:55'),(27,'Botas de agua','Proteccion de pies',0,0,NULL,'2026-01-19 04:07:55','2026-01-19 04:07:55'),(28,'Polainas anticorte','Proteccion de pies',0,1,6,'2026-01-19 04:07:55','2026-01-19 04:07:55'),(29,'Cuerda de seguridad','Proteccion contra caidas',1,1,12,'2026-01-19 04:07:55','2026-01-19 04:07:55'),(30,'Absorbedor de energia','Proteccion contra caidas',1,1,12,'2026-01-19 04:07:55','2026-01-19 04:07:55'),(31,'Conector/Mosqueton','Proteccion contra caidas',1,1,12,'2026-01-19 04:07:55','2026-01-19 04:07:55'),(32,'Bloqueador anticaidas','Proteccion contra caidas',1,1,12,'2026-01-19 04:07:55','2026-01-19 04:07:55'),(33,'Linea de vida portatil','Proteccion contra caidas',1,1,12,'2026-01-19 04:07:55','2026-01-19 04:07:55'),(34,'Pantalon anticorte clase 1','Ropa de proteccion',0,1,12,'2026-01-19 04:07:55','2026-01-19 04:07:55'),(35,'Pantalon anticorte clase 2','Ropa de proteccion',0,1,12,'2026-01-19 04:07:55','2026-01-19 04:07:55'),(36,'Chaleco reflectante','Ropa de proteccion',0,0,NULL,'2026-01-19 04:07:55','2026-01-19 04:07:55'),(37,'Chaqueta de alta visibilidad','Ropa de proteccion',0,0,NULL,'2026-01-19 04:07:55','2026-01-19 04:07:55'),(38,'Traje de agua','Ropa de proteccion',0,0,NULL,'2026-01-19 04:07:55','2026-01-19 04:07:55'),(39,'Mono de trabajo','Ropa de proteccion',0,0,NULL,'2026-01-19 04:07:55','2026-01-19 04:07:55'),(40,'Traje fitosanitario','Proteccion quimica',1,0,NULL,'2026-01-19 04:07:55','2026-01-19 04:07:55'),(41,'Guantes de aplicador','Proteccion quimica',1,0,NULL,'2026-01-19 04:07:55','2026-01-19 04:07:55'),(42,'Botas de aplicador','Proteccion quimica',0,0,NULL,'2026-01-19 04:07:55','2026-01-19 04:07:55'),(44,'Casco','Categoria',1,1,12,'2026-01-22 15:37:27','2026-01-22 15:37:27');
/*!40000 ALTER TABLE `epi_catalogo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `epi_entregas`
--

DROP TABLE IF EXISTS `epi_entregas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `epi_entregas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `epi_inventario_id` bigint(20) unsigned NOT NULL,
  `trabajador_id` bigint(20) unsigned NOT NULL,
  `fecha_entrega` date NOT NULL,
  `fecha_devolucion` date DEFAULT NULL,
  `motivo_devolucion` varchar(255) DEFAULT NULL,
  `firma_trabajador_path` varchar(500) DEFAULT NULL,
  `entregado_por` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `epi_entregas_epi_inventario_id_foreign` (`epi_inventario_id`),
  KEY `epi_entregas_trabajador_id_foreign` (`trabajador_id`),
  KEY `epi_entregas_entregado_por_foreign` (`entregado_por`),
  CONSTRAINT `epi_entregas_entregado_por_foreign` FOREIGN KEY (`entregado_por`) REFERENCES `users` (`id`),
  CONSTRAINT `epi_entregas_epi_inventario_id_foreign` FOREIGN KEY (`epi_inventario_id`) REFERENCES `epi_inventario` (`id`),
  CONSTRAINT `epi_entregas_trabajador_id_foreign` FOREIGN KEY (`trabajador_id`) REFERENCES `trabajadores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `epi_entregas`
--

LOCK TABLES `epi_entregas` WRITE;
/*!40000 ALTER TABLE `epi_entregas` DISABLE KEYS */;
INSERT INTO `epi_entregas` VALUES (1,1,2,'2026-01-18','2026-01-18','Fin de uso','uploads/epis/firmas/firma_2_1768797649.png',1,'2026-01-19 04:40:49','2026-01-19 04:42:00'),(2,4,11,'2026-01-22','2026-01-22','Caducidad','uploads/epis/firmas/firma_11_1769096334.png',1,'2026-01-22 15:38:54','2026-01-22 15:39:22'),(3,4,11,'2026-01-22',NULL,NULL,'uploads/epis/firmas/firma_11_1769096372.png',1,'2026-01-22 15:39:32','2026-01-22 15:39:32');
/*!40000 ALTER TABLE `epi_entregas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `epi_inventario`
--

DROP TABLE IF EXISTS `epi_inventario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `epi_inventario` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `epi_catalogo_id` bigint(20) unsigned NOT NULL,
  `numero_serie` varchar(100) DEFAULT NULL,
  `fecha_compra` date DEFAULT NULL,
  `fecha_caducidad` date DEFAULT NULL,
  `coste` decimal(10,2) DEFAULT NULL,
  `estado` enum('disponible','asignado','en_revision','baja') NOT NULL DEFAULT 'disponible',
  `notas` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `epi_inventario_epi_catalogo_id_foreign` (`epi_catalogo_id`),
  CONSTRAINT `epi_inventario_epi_catalogo_id_foreign` FOREIGN KEY (`epi_catalogo_id`) REFERENCES `epi_catalogo` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `epi_inventario`
--

LOCK TABLES `epi_inventario` WRITE;
/*!40000 ALTER TABLE `epi_inventario` DISABLE KEYS */;
INSERT INTO `epi_inventario` VALUES (1,1,'CF-2026-001','2026-01-15',NULL,85.50,'baja',NULL,'2026-01-19 04:30:34','2026-01-19 04:42:26'),(3,25,NULL,NULL,NULL,NULL,'disponible',NULL,'2026-01-19 04:46:46','2026-01-19 04:46:46'),(4,44,'216351','2026-01-22','2026-01-31',123.00,'asignado',NULL,'2026-01-22 15:38:02','2026-01-22 15:41:05');
/*!40000 ALTER TABLE `epi_inventario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `epi_inventario_documentos`
--

DROP TABLE IF EXISTS `epi_inventario_documentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `epi_inventario_documentos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `epi_inventario_id` bigint(20) unsigned NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `archivo_path` varchar(500) NOT NULL,
  `subido_por` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `epi_inventario_documentos_epi_inventario_id_foreign` (`epi_inventario_id`),
  KEY `epi_inventario_documentos_subido_por_foreign` (`subido_por`),
  CONSTRAINT `epi_inventario_documentos_epi_inventario_id_foreign` FOREIGN KEY (`epi_inventario_id`) REFERENCES `epi_inventario` (`id`) ON DELETE CASCADE,
  CONSTRAINT `epi_inventario_documentos_subido_por_foreign` FOREIGN KEY (`subido_por`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `epi_inventario_documentos`
--

LOCK TABLES `epi_inventario_documentos` WRITE;
/*!40000 ALTER TABLE `epi_inventario_documentos` DISABLE KEYS */;
/*!40000 ALTER TABLE `epi_inventario_documentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `epi_revisiones`
--

DROP TABLE IF EXISTS `epi_revisiones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `epi_revisiones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `epi_inventario_id` bigint(20) unsigned NOT NULL,
  `fecha_revision` date NOT NULL,
  `proxima_revision` date DEFAULT NULL,
  `resultado` enum('apto','no_apto','requiere_reparacion') NOT NULL,
  `observaciones` text DEFAULT NULL,
  `realizado_por` bigint(20) unsigned NOT NULL,
  `documento_path` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `epi_revisiones_epi_inventario_id_foreign` (`epi_inventario_id`),
  KEY `epi_revisiones_realizado_por_foreign` (`realizado_por`),
  CONSTRAINT `epi_revisiones_epi_inventario_id_foreign` FOREIGN KEY (`epi_inventario_id`) REFERENCES `epi_inventario` (`id`),
  CONSTRAINT `epi_revisiones_realizado_por_foreign` FOREIGN KEY (`realizado_por`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `epi_revisiones`
--

LOCK TABLES `epi_revisiones` WRITE;
/*!40000 ALTER TABLE `epi_revisiones` DISABLE KEYS */;
INSERT INTO `epi_revisiones` VALUES (1,1,'2026-01-18','2027-01-18','apto','Revisión inicial. EPI en perfecto estado, sin daños visibles.',1,NULL,'2026-01-19 04:39:52'),(2,4,'2026-01-22','2026-01-30','apto',NULL,1,'uploads/epis/revisiones/revision_4_1769096543.pdf','2026-01-22 15:42:23');
/*!40000 ALTER TABLE `epi_revisiones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `factura_lineas`
--

DROP TABLE IF EXISTS `factura_lineas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `factura_lineas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `factura_id` bigint(20) unsigned NOT NULL,
  `concepto` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `cantidad` decimal(10,2) NOT NULL DEFAULT 1.00,
  `precio_unitario` decimal(12,2) NOT NULL,
  `descuento_porcentaje` decimal(5,2) NOT NULL DEFAULT 0.00,
  `importe` decimal(14,2) NOT NULL,
  `orden` int(11) NOT NULL DEFAULT 0,
  `grupo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `factura_lineas_factura_id_foreign` (`factura_id`),
  CONSTRAINT `factura_lineas_factura_id_foreign` FOREIGN KEY (`factura_id`) REFERENCES `facturas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `factura_lineas`
--

LOCK TABLES `factura_lineas` WRITE;
/*!40000 ALTER TABLE `factura_lineas` DISABLE KEYS */;
INSERT INTO `factura_lineas` VALUES (1,1,'Desbroce herbáceo zona norte',NULL,25.00,150.50,0.00,3762.50,0,NULL,'2026-01-19 01:49:13'),(4,3,'Mantenimiento preventivo',NULL,5.00,200.00,0.00,1000.00,0,NULL,'2026-01-19 01:54:27'),(5,4,'Test Fuentes PDF',NULL,1.00,100.00,0.00,100.00,0,NULL,'2026-01-19 02:25:48'),(6,5,'Tala','Tala',2.00,20.00,10.00,36.00,0,NULL,'2026-01-22 15:33:38'),(7,6,'Tala','Tala',1.00,34.00,0.00,34.00,0,NULL,'2026-01-22 15:59:54'),(8,7,'a','a',1.00,32.00,0.00,32.00,0,'l22','2026-02-12 16:50:38'),(9,7,'b','b',1.00,20.00,0.00,20.00,1,'l22','2026-02-12 16:50:38'),(10,7,'c','c',1.00,33.00,10.00,29.70,2,'l23','2026-02-12 16:50:38'),(11,7,'d','d',1.00,3232.00,0.00,3232.00,3,'l23','2026-02-12 16:50:38');
/*!40000 ALTER TABLE `factura_lineas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facturas`
--

DROP TABLE IF EXISTS `facturas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `facturas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `numero` varchar(50) DEFAULT NULL,
  `serie` varchar(10) NOT NULL DEFAULT 'F',
  `cliente_id` bigint(20) unsigned NOT NULL,
  `obra_id` bigint(20) unsigned DEFAULT NULL,
  `fecha_emision` date NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `base_imponible` decimal(14,2) NOT NULL,
  `iva_porcentaje` decimal(5,2) NOT NULL DEFAULT 21.00,
  `iva_importe` decimal(12,2) NOT NULL,
  `retencion_porcentaje` decimal(5,2) NOT NULL DEFAULT 0.00,
  `retencion_importe` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(14,2) NOT NULL,
  `estado` enum('borrador','emitida','enviada','cobrada','anulada') NOT NULL DEFAULT 'borrador',
  `fecha_cobro` date DEFAULT NULL,
  `pdf_path` varchar(500) DEFAULT NULL,
  `email_enviado` tinyint(1) NOT NULL DEFAULT 0,
  `email_enviado_at` timestamp NULL DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `footer_text` text DEFAULT 'MANZER AGROFORESTAL, S.R.L.U. | CIF: B12345678 | Inscrita en el Registro Mercantil de Barcelona' COMMENT 'Texto personalizado para el pie de página del PDF',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `facturas_numero_unique` (`numero`),
  KEY `facturas_cliente_id_foreign` (`cliente_id`),
  KEY `facturas_obra_id_foreign` (`obra_id`),
  CONSTRAINT `facturas_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `facturas_obra_id_foreign` FOREIGN KEY (`obra_id`) REFERENCES `obras` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facturas`
--

LOCK TABLES `facturas` WRITE;
/*!40000 ALTER TABLE `facturas` DISABLE KEYS */;
INSERT INTO `facturas` VALUES (1,'F-2026-00001','F',1,NULL,'2026-01-18',NULL,3762.50,21.00,790.13,5.00,188.13,4364.50,'cobrada','2026-01-18','uploads/facturas/2026/01/factura_F-2026-00001_1768789497.pdf',0,NULL,NULL,'MANZER AGROFORESTAL, S.R.L.U. | CIF: B12345678 | Inscrita en el Registro Mercantil de Barcelona','2026-01-19 01:49:13','2026-01-19 02:24:58'),(3,'F-2026-00002','F',3,NULL,'2026-01-18',NULL,1000.00,21.00,210.00,0.00,0.00,1210.00,'anulada',NULL,NULL,0,NULL,NULL,'MANZER AGROFORESTAL, S.R.L.U. | CIF: B12345678 | Inscrita en el Registro Mercantil de Barcelona','2026-01-19 01:54:27','2026-01-19 01:55:03'),(4,'F-2026-00003','F',3,NULL,'2026-01-18',NULL,100.00,21.00,21.00,0.00,0.00,121.00,'emitida',NULL,'uploads/facturas/2026/01/factura_F-2026-00003_1768789670.pdf',0,NULL,NULL,'MANZER AGROFORESTAL, S.R.L.U. | CIF: B12345678 | Inscrita en el Registro Mercantil de Barcelona','2026-01-19 02:25:48','2026-01-19 02:27:50'),(5,'F-2026-00004','F',5,4,'2026-01-22','2026-01-30',36.00,21.00,7.56,10.00,3.60,39.96,'enviada',NULL,'uploads/facturas/2026/01/factura_F-2026-00004_1769096037.pdf',0,NULL,'Notas','MANZER AGROFORESTAL, S.R.L.U. | CIF: B12345678 | Inscrita en el Registro Mercantil de Barcelona','2026-01-22 15:33:38','2026-01-22 15:35:17'),(6,NULL,'F',5,4,'2026-01-22',NULL,34.00,21.00,7.14,10.00,3.40,37.74,'borrador',NULL,NULL,0,NULL,'notas','MANZER AGROFORESTAL, S.R.L.U. | CIF: B12345678 | Inscrita en el Registro Mercantil de Barcelona','2026-01-22 15:59:54','2026-01-22 15:59:54'),(7,'F-2026-00005','F',5,4,'2026-02-12',NULL,3313.70,21.00,695.88,10.00,331.37,3678.21,'enviada',NULL,'uploads/facturas/2026/02/factura_F-2026-00005_1770915052.pdf',0,NULL,NULL,'MANZER AGROFORESTAL, S.R.L.U. | CIF: B12345678 | Inscrita en el Registro Mercantil de Barcelona','2026-02-12 16:50:38','2026-02-12 16:50:56');
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
-- Table structure for table `fichajes`
--

DROP TABLE IF EXISTS `fichajes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fichajes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `trabajador_id` bigint(20) unsigned NOT NULL,
  `obra_id` bigint(20) unsigned DEFAULT NULL,
  `fecha` date NOT NULL,
  `hora_entrada` time DEFAULT NULL,
  `latitud_entrada` decimal(10,8) DEFAULT NULL,
  `longitud_entrada` decimal(11,8) DEFAULT NULL,
  `hora_salida` time DEFAULT NULL,
  `latitud_salida` decimal(10,8) DEFAULT NULL,
  `longitud_salida` decimal(11,8) DEFAULT NULL,
  `horas_trabajadas` decimal(5,2) DEFAULT NULL,
  `horas_extra` decimal(5,2) NOT NULL DEFAULT 0.00,
  `validado` tinyint(1) NOT NULL DEFAULT 0,
  `validado_por` bigint(20) unsigned DEFAULT NULL,
  `fecha_validacion` datetime DEFAULT NULL,
  `corregido` tinyint(1) NOT NULL DEFAULT 0,
  `corregido_por` bigint(20) unsigned DEFAULT NULL,
  `motivo_correccion` text DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fichajes_obra_id_foreign` (`obra_id`),
  KEY `fichajes_validado_por_foreign` (`validado_por`),
  KEY `fichajes_corregido_por_foreign` (`corregido_por`),
  KEY `idx_fichajes_trabajador_id` (`trabajador_id`),
  CONSTRAINT `fichajes_corregido_por_foreign` FOREIGN KEY (`corregido_por`) REFERENCES `users` (`id`),
  CONSTRAINT `fichajes_obra_id_foreign` FOREIGN KEY (`obra_id`) REFERENCES `obras` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fichajes_trabajador_id_foreign` FOREIGN KEY (`trabajador_id`) REFERENCES `trabajadores` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fichajes_validado_por_foreign` FOREIGN KEY (`validado_por`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fichajes`
--

LOCK TABLES `fichajes` WRITE;
/*!40000 ALTER TABLE `fichajes` DISABLE KEYS */;
INSERT INTO `fichajes` VALUES (1,11,4,'2025-12-27','00:19:00',NULL,NULL,'23:18:00',NULL,NULL,8.00,14.98,1,1,'2025-12-27 09:18:49',0,NULL,NULL,'ya','2025-12-27 14:16:54','2025-12-27 14:18:49'),(2,5,4,'2025-12-27','10:00:00',NULL,NULL,'22:00:00',NULL,NULL,8.00,4.00,1,1,'2025-12-27 10:00:28',0,NULL,NULL,NULL,'2025-12-27 15:00:14','2025-12-27 15:00:28'),(3,1,4,'2026-01-10','18:18:00',NULL,NULL,'18:23:00',NULL,NULL,0.08,0.00,1,1,'2026-01-10 18:18:27',0,NULL,NULL,NULL,'2026-01-10 23:18:19','2026-01-10 23:18:27'),(4,11,4,'2026-01-13','11:36:00',NULL,NULL,'11:42:00',4.11059930,-73.63722230,0.10,0.00,1,1,'2026-01-13 12:01:54',0,NULL,NULL,NULL,'2026-01-13 16:36:40','2026-01-13 17:01:54'),(5,11,NULL,'2026-01-13','14:00:00',NULL,NULL,'18:00:00',NULL,NULL,4.00,0.00,0,NULL,NULL,0,NULL,NULL,NULL,'2026-01-17 00:21:08','2026-01-17 00:21:08'),(6,11,NULL,'2026-01-16','19:26:00',NULL,NULL,'19:27:00',NULL,NULL,0.02,0.00,0,NULL,NULL,0,NULL,NULL,NULL,'2026-01-17 00:27:11','2026-01-17 00:27:28'),(7,11,NULL,'2026-01-16','19:27:00',NULL,NULL,'19:38:00',NULL,NULL,0.18,0.00,0,NULL,NULL,0,NULL,NULL,NULL,'2026-01-17 00:27:58','2026-01-17 00:38:28'),(8,11,1,'2026-01-16','19:38:00',4.11001290,-73.63696364,'19:39:00',4.11001290,-73.63696364,0.02,0.00,1,1,'2026-01-16 19:39:31',0,NULL,NULL,NULL,'2026-01-17 00:39:01','2026-01-17 00:39:31'),(9,1,NULL,'2026-01-22','11:09:00',4.11013320,-73.63699727,'11:09:00',4.11013762,-73.63699304,0.00,0.00,0,NULL,NULL,0,NULL,NULL,NULL,'2026-01-22 16:09:19','2026-01-22 16:09:38'),(10,11,1,'2026-03-05','11:22:00',4.10994140,-73.63700915,NULL,NULL,NULL,NULL,0.00,0,NULL,NULL,0,NULL,NULL,'ok','2026-03-05 16:24:16','2026-03-05 16:24:16');
/*!40000 ALTER TABLE `fichajes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `formacion_tipos`
--

DROP TABLE IF EXISTS `formacion_tipos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formacion_tipos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `duracion_horas` int(11) DEFAULT NULL,
  `periodicidad_meses` int(11) DEFAULT NULL COMMENT 'Cada cuántos meses caduca (NULL = no caduca)',
  `obligatoria` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `formacion_tipos`
--

LOCK TABLES `formacion_tipos` WRITE;
/*!40000 ALTER TABLE `formacion_tipos` DISABLE KEYS */;
INSERT INTO `formacion_tipos` VALUES (1,'PRL Básico 60h','Prevención de Riesgos Laborales nivel básico',60,NULL,1,'2025-12-23 14:13:27','2025-12-23 14:13:27'),(2,'Trabajos en Altura','Formación para trabajos en altura',8,24,1,'2025-12-23 14:13:27','2025-12-23 14:13:27'),(3,'Motosierra','Uso seguro de motosierra',16,36,1,'2025-12-23 14:13:27','2025-12-23 14:13:27'),(4,'Desbrozadora','Uso seguro de desbrozadora',8,36,1,'2025-12-23 14:13:27','2025-12-23 14:13:27'),(5,'Aplicador Fitosanitarios','Carnet de aplicador de productos fitosanitarios',25,120,0,'2025-12-23 14:13:27','2025-12-23 14:13:27'),(6,'Primeros Auxilios','Formación en primeros auxilios',8,24,0,'2025-12-23 14:13:27','2025-12-23 14:13:27'),(7,'Espacios Confinados','Trabajos en espacios confinados',8,24,0,'2025-12-23 14:13:27','2025-12-23 14:13:27'),(8,'PRL Básico (60h)','Curso básico de Prevención de Riesgos Laborales de 60 horas según convenio de la construcción',60,NULL,1,'2026-01-19 16:07:10','2026-01-19 16:07:10'),(9,'PRL Específico Jardinería','Formación específica en prevención de riesgos para trabajos de jardinería',20,48,1,'2026-01-19 16:07:10','2026-01-19 16:07:10'),(10,'PRL Trabajos Forestales','Formación específica en prevención de riesgos para trabajos forestales',20,48,1,'2026-01-19 16:07:10','2026-01-19 16:07:10'),(11,'PRL Trabajos en Altura','Formación para trabajos en altura (poda de árboles, plataformas elevadoras)',8,24,1,'2026-01-19 16:07:10','2026-01-19 16:07:10'),(12,'Carnet Carretilla Elevadora','Formación para manejo de carretillas elevadoras',20,60,0,'2026-01-19 16:07:10','2026-01-19 16:07:10'),(13,'Carnet Plataforma Elevadora (PEMP)','Formación para manejo de plataformas elevadoras móviles de personal',8,60,0,'2026-01-19 16:07:10','2026-01-19 16:07:10'),(14,'Operador Motosierra','Formación para uso profesional de motosierra',16,36,0,'2026-01-19 16:07:10','2026-01-19 16:07:10'),(15,'Operador Desbrozadora','Formación para uso profesional de desbrozadoras',8,36,0,'2026-01-19 16:07:10','2026-01-19 16:07:10'),(16,'Operador Retroexcavadora','Formación para manejo de retroexcavadoras y miniexcavadoras',20,60,0,'2026-01-19 16:07:10','2026-01-19 16:07:10'),(17,'Carnet Fitosanitario Básico','Carnet de aplicador de productos fitosanitarios nivel básico',25,120,0,'2026-01-19 16:07:10','2026-01-19 16:07:10'),(18,'Carnet Fitosanitario Cualificado','Carnet de aplicador de productos fitosanitarios nivel cualificado',60,120,0,'2026-01-19 16:07:10','2026-01-19 16:07:10'),(19,'Extinción de Incendios','Formación en prevención y extinción de incendios',4,24,0,'2026-01-19 16:07:10','2026-01-19 16:07:10'),(20,'Poda de Árboles Ornamentales','Técnicas de poda y mantenimiento de árboles ornamentales',16,NULL,0,'2026-01-19 16:07:10','2026-01-19 16:07:10'),(21,'Instalación de Riego','Instalación y mantenimiento de sistemas de riego',20,NULL,0,'2026-01-19 16:07:10','2026-01-19 16:07:10'),(22,'Tratamientos Fitosanitarios','Aplicación de tratamientos fitosanitarios en jardinería',16,NULL,0,'2026-01-19 16:07:10','2026-01-19 16:07:10'),(23,'Trepa de Árboles','Técnicas de trepa y trabajo en árboles con cuerdas',40,36,0,'2026-01-19 16:07:10','2026-01-19 16:07:10'),(24,'Trabajos Verticales','Formación en técnicas de acceso por cuerdas',40,36,0,'2026-01-19 16:07:10','2026-01-19 16:07:10'),(26,'Alturas','Alturas',20,50,1,'2026-01-22 15:46:13','2026-01-22 15:46:13');
/*!40000 ALTER TABLE `formacion_tipos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gasto_categorias`
--

DROP TABLE IF EXISTS `gasto_categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gasto_categorias` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `codigo` varchar(20) DEFAULT NULL,
  `tipo` enum('directo','indirecto') NOT NULL DEFAULT 'directo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gasto_categorias`
--

LOCK TABLES `gasto_categorias` WRITE;
/*!40000 ALTER TABLE `gasto_categorias` DISABLE KEYS */;
INSERT INTO `gasto_categorias` VALUES (1,'Personal propio','PERS','directo','2025-12-23 14:13:27'),(2,'Subcontratas','SUBC','directo','2025-12-23 14:13:27'),(3,'Maquinaria','MAQ','directo','2025-12-23 14:13:27'),(4,'Combustible','COMB','directo','2025-12-23 14:13:27'),(5,'Mantenimiento','MANT','directo','2025-12-23 14:13:27'),(6,'EPIs','EPI','directo','2025-12-23 14:13:27'),(7,'Gestoría / Seguros','GEST','indirecto','2025-12-23 14:13:27'),(8,'Penalizaciones','PEN','directo','2025-12-23 14:13:27'),(9,'Otros','OTRO','indirecto','2025-12-23 14:13:27');
/*!40000 ALTER TABLE `gasto_categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gastos`
--

DROP TABLE IF EXISTS `gastos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gastos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gasto_categoria_id` bigint(20) unsigned NOT NULL,
  `obra_id` bigint(20) unsigned DEFAULT NULL,
  `proveedor` varchar(255) DEFAULT NULL,
  `concepto` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `importe` decimal(14,2) NOT NULL,
  `iva_porcentaje` decimal(5,2) NOT NULL DEFAULT 21.00,
  `iva_importe` decimal(12,2) DEFAULT NULL,
  `importe_total` decimal(14,2) NOT NULL,
  `fecha` date NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `fecha_pago` date DEFAULT NULL,
  `estado` enum('pendiente','pagado') NOT NULL DEFAULT 'pendiente',
  `forma_pago` varchar(100) DEFAULT NULL,
  `documento_path` varchar(500) DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gastos_gasto_categoria_id_foreign` (`gasto_categoria_id`),
  KEY `gastos_obra_id_foreign` (`obra_id`),
  CONSTRAINT `gastos_gasto_categoria_id_foreign` FOREIGN KEY (`gasto_categoria_id`) REFERENCES `gasto_categorias` (`id`),
  CONSTRAINT `gastos_obra_id_foreign` FOREIGN KEY (`obra_id`) REFERENCES `obras` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gastos`
--

LOCK TABLES `gastos` WRITE;
/*!40000 ALTER TABLE `gastos` DISABLE KEYS */;
INSERT INTO `gastos` VALUES (1,4,1,'Repsol S.A.','Gasoil maquinaria enero',NULL,500.00,21.00,105.00,605.00,'2026-01-18',NULL,'2026-01-18','pagado',NULL,NULL,NULL,'2026-01-18 17:03:30','2026-01-18 17:08:49'),(2,4,4,'Bueno','Bueno','Bueno',12321.00,21.00,2587.41,14908.41,'2026-01-22','2026-01-23','2026-01-22','pagado','Efectivo','uploads/gastos/2026/01/gasto_2_1769095921.pdf','Notas','2026-01-22 15:32:01','2026-01-22 15:32:05');
/*!40000 ALTER TABLE `gastos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ingresos`
--

DROP TABLE IF EXISTS `ingresos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ingresos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `obra_id` bigint(20) unsigned DEFAULT NULL,
  `cliente_id` bigint(20) unsigned NOT NULL,
  `factura_id` bigint(20) unsigned DEFAULT NULL,
  `concepto` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `importe` decimal(14,2) NOT NULL,
  `iva_porcentaje` decimal(5,2) NOT NULL DEFAULT 21.00,
  `iva_importe` decimal(12,2) DEFAULT NULL,
  `retencion_porcentaje` decimal(5,2) NOT NULL DEFAULT 0.00,
  `retencion_importe` decimal(12,2) DEFAULT NULL,
  `importe_total` decimal(14,2) NOT NULL COMMENT 'Importe + IVA - Retención',
  `fecha` date NOT NULL,
  `fecha_prevista_cobro` date DEFAULT NULL,
  `fecha_cobro` date DEFAULT NULL,
  `estado` enum('pendiente','parcial','cobrado') NOT NULL DEFAULT 'pendiente',
  `forma_pago` varchar(100) DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ingresos_obra_id_foreign` (`obra_id`),
  KEY `ingresos_cliente_id_foreign` (`cliente_id`),
  KEY `ingresos_factura_id_foreign` (`factura_id`),
  CONSTRAINT `ingresos_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `ingresos_factura_id_foreign` FOREIGN KEY (`factura_id`) REFERENCES `facturas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ingresos_obra_id_foreign` FOREIGN KEY (`obra_id`) REFERENCES `obras` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ingresos`
--

LOCK TABLES `ingresos` WRITE;
/*!40000 ALTER TABLE `ingresos` DISABLE KEYS */;
INSERT INTO `ingresos` VALUES (1,1,1,NULL,'Certificación mensual enero 2026 - MODIFICADO',NULL,10000.00,21.00,2100.00,5.00,500.00,11600.00,'2026-01-18',NULL,NULL,'pendiente',NULL,NULL,'2026-01-18 17:02:21','2026-01-18 17:08:28'),(2,4,5,NULL,'Bueno',NULL,232.00,21.00,48.72,10.00,23.20,257.52,'2026-01-22','2026-01-30','2026-01-22','cobrado','Transferencia','Nota','2026-01-22 15:30:13','2026-01-22 15:30:33');
/*!40000 ALTER TABLE `ingresos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lead_interacciones`
--

DROP TABLE IF EXISTS `lead_interacciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lead_interacciones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` bigint(20) unsigned DEFAULT NULL,
  `cliente_id` bigint(20) unsigned DEFAULT NULL,
  `tipo` enum('llamada','email','reunion','visita','otro') NOT NULL,
  `fecha` datetime NOT NULL,
  `descripcion` text NOT NULL,
  `proximo_paso` text DEFAULT NULL,
  `fecha_proximo_contacto` date DEFAULT NULL,
  `registrado_por` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `lead_interacciones_lead_id_foreign` (`lead_id`),
  KEY `lead_interacciones_cliente_id_foreign` (`cliente_id`),
  KEY `lead_interacciones_registrado_por_foreign` (`registrado_por`),
  CONSTRAINT `lead_interacciones_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lead_interacciones_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lead_interacciones_registrado_por_foreign` FOREIGN KEY (`registrado_por`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lead_interacciones`
--

LOCK TABLES `lead_interacciones` WRITE;
/*!40000 ALTER TABLE `lead_interacciones` DISABLE KEYS */;
INSERT INTO `lead_interacciones` VALUES (1,NULL,1,'llamada','2026-01-10 18:19:00','descrip','hacer algo','2026-01-21',1,'2026-01-10 23:19:54'),(2,NULL,1,'reunion','2026-01-10 18:19:00','reu','reu','2026-01-11',1,'2026-01-10 23:20:14');
/*!40000 ALTER TABLE `lead_interacciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leads`
--

DROP TABLE IF EXISTS `leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leads` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cliente_id` bigint(20) unsigned DEFAULT NULL,
  `nombre_empresa` varchar(255) NOT NULL,
  `persona_contacto` varchar(150) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `origen` enum('contacto_directo','recomendacion','licitacion','web','otro') NOT NULL,
  `descripcion` text DEFAULT NULL,
  `importe_estimado` decimal(12,2) DEFAULT NULL,
  `probabilidad` int(11) NOT NULL DEFAULT 50 COMMENT 'Porcentaje 0-100',
  `temperatura` enum('frio','tibio','caliente') NOT NULL DEFAULT 'tibio',
  `capacidad_economica_percibida` enum('baja','media','alta') DEFAULT NULL,
  `fecha_estimada_cierre` date DEFAULT NULL,
  `estado` enum('nuevo','contactado','propuesta_enviada','negociacion','ganado','perdido') NOT NULL DEFAULT 'nuevo',
  `motivo_perdida` text DEFAULT NULL,
  `asignado_a` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leads_cliente_id_foreign` (`cliente_id`),
  KEY `leads_asignado_a_foreign` (`asignado_a`),
  CONSTRAINT `leads_asignado_a_foreign` FOREIGN KEY (`asignado_a`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leads_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leads`
--

LOCK TABLES `leads` WRITE;
/*!40000 ALTER TABLE `leads` DISABLE KEYS */;
/*!40000 ALTER TABLE `leads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maquinaria`
--

DROP TABLE IF EXISTS `maquinaria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `maquinaria` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `maquinaria_tipo_id` bigint(20) unsigned NOT NULL,
  `codigo_interno` varchar(50) DEFAULT NULL,
  `marca` varchar(100) DEFAULT NULL,
  `modelo` varchar(100) DEFAULT NULL,
  `numero_serie` varchar(100) DEFAULT NULL,
  `numero_bastidor` varchar(100) DEFAULT NULL,
  `fecha_compra` date DEFAULT NULL,
  `coste_adquisicion` decimal(12,2) DEFAULT NULL,
  `vida_util_meses` int(11) DEFAULT NULL,
  `amortizacion_dia` decimal(8,2) DEFAULT NULL COMMENT '€/día',
  `coste_hora` decimal(8,2) DEFAULT NULL,
  `estado` enum('operativa','en_reparacion','baja') NOT NULL DEFAULT 'operativa',
  `obra_asignada_id` bigint(20) unsigned DEFAULT NULL,
  `trabajador_asignado_id` bigint(20) unsigned DEFAULT NULL,
  `tiene_marcado_ce` tinyint(1) NOT NULL DEFAULT 1,
  `tiene_manual` tinyint(1) NOT NULL DEFAULT 1,
  `notas` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `maquinaria_codigo_interno_unique` (`codigo_interno`),
  KEY `maquinaria_maquinaria_tipo_id_foreign` (`maquinaria_tipo_id`),
  KEY `maquinaria_trabajador_asignado_id_foreign` (`trabajador_asignado_id`),
  KEY `maquinaria_obra_asignada_id_foreign` (`obra_asignada_id`),
  CONSTRAINT `maquinaria_maquinaria_tipo_id_foreign` FOREIGN KEY (`maquinaria_tipo_id`) REFERENCES `maquinaria_tipos` (`id`),
  CONSTRAINT `maquinaria_obra_asignada_id_foreign` FOREIGN KEY (`obra_asignada_id`) REFERENCES `obras` (`id`) ON DELETE SET NULL,
  CONSTRAINT `maquinaria_trabajador_asignado_id_foreign` FOREIGN KEY (`trabajador_asignado_id`) REFERENCES `trabajadores` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maquinaria`
--

LOCK TABLES `maquinaria` WRITE;
/*!40000 ALTER TABLE `maquinaria` DISABLE KEYS */;
INSERT INTO `maquinaria` VALUES (1,1,'MAQ-0001','STIHL','MS 461 R','SN-2024-001234',NULL,NULL,1500.00,60,0.83,NULL,'operativa',4,11,1,1,'Motosierra profesional para corte de árboles de gran diámetro. Revisión completa realizada.','2026-01-18 18:16:45','2026-01-22 15:08:50',NULL);
/*!40000 ALTER TABLE `maquinaria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maquinaria_asignaciones`
--

DROP TABLE IF EXISTS `maquinaria_asignaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `maquinaria_asignaciones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `maquinaria_id` bigint(20) unsigned NOT NULL,
  `obra_id` bigint(20) unsigned NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `maquinaria_asignaciones_maquinaria_id_foreign` (`maquinaria_id`),
  KEY `maquinaria_asignaciones_obra_id_foreign` (`obra_id`),
  CONSTRAINT `maquinaria_asignaciones_maquinaria_id_foreign` FOREIGN KEY (`maquinaria_id`) REFERENCES `maquinaria` (`id`) ON DELETE CASCADE,
  CONSTRAINT `maquinaria_asignaciones_obra_id_foreign` FOREIGN KEY (`obra_id`) REFERENCES `obras` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maquinaria_asignaciones`
--

LOCK TABLES `maquinaria_asignaciones` WRITE;
/*!40000 ALTER TABLE `maquinaria_asignaciones` DISABLE KEYS */;
INSERT INTO `maquinaria_asignaciones` VALUES (1,1,1,'2026-01-18','2026-01-18',NULL,'2026-01-18 18:17:16','2026-01-18 18:22:04'),(2,1,4,'2026-01-22',NULL,NULL,'2026-01-22 15:08:50','2026-01-22 15:08:50');
/*!40000 ALTER TABLE `maquinaria_asignaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maquinaria_checklist_items`
--

DROP TABLE IF EXISTS `maquinaria_checklist_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `maquinaria_checklist_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `plantilla_id` bigint(20) unsigned NOT NULL,
  `categoria` varchar(100) DEFAULT NULL COMMENT 'Documentación, Seguridad, Pictogramas, etc.',
  `descripcion` text NOT NULL,
  `orden` int(11) NOT NULL DEFAULT 0,
  `obligatorio` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `maquinaria_checklist_items_plantilla_id_foreign` (`plantilla_id`),
  CONSTRAINT `maquinaria_checklist_items_plantilla_id_foreign` FOREIGN KEY (`plantilla_id`) REFERENCES `maquinaria_checklist_plantillas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maquinaria_checklist_items`
--

LOCK TABLES `maquinaria_checklist_items` WRITE;
/*!40000 ALTER TABLE `maquinaria_checklist_items` DISABLE KEYS */;
INSERT INTO `maquinaria_checklist_items` VALUES (1,1,'Seguridad','Freno de cadena funciona correctamente',1,1,'2026-01-18 17:47:12'),(2,1,'Seguridad','Protector de mano delantero en buen estado',2,1,'2026-01-18 17:47:12'),(3,1,'Seguridad','Captor de cadena presente y en buen estado',3,1,'2026-01-18 17:47:12'),(4,1,'Seguridad','Interruptor de parada funciona',4,1,'2026-01-18 17:47:12'),(5,1,'Motor','Nivel de combustible adecuado',5,0,'2026-01-18 17:47:12'),(6,1,'Motor','Nivel de aceite de cadena adecuado',6,1,'2026-01-18 17:47:12'),(7,1,'Motor','Filtro de aire limpio',7,0,'2026-01-18 17:47:12'),(8,1,'Motor','Arranque suave sin tirones',8,0,'2026-01-18 17:47:12'),(9,1,'Cadena y Espada','Tension de cadena correcta',9,1,'2026-01-18 17:47:12'),(10,1,'Cadena y Espada','Cadena afilada',10,1,'2026-01-18 17:47:12'),(11,1,'Cadena y Espada','Espada sin deformaciones',11,1,'2026-01-18 17:47:12'),(12,1,'General','Sin fugas de combustible o aceite',12,1,'2026-01-18 17:47:12'),(13,1,'General','Mangos y empunaduras en buen estado',13,0,'2026-01-18 17:47:12'),(14,1,'General','Silenciador sin danos visibles',14,0,'2026-01-18 17:47:12'),(15,2,'Seguridad','Protector de disco/hilo en buen estado',1,1,'2026-01-18 17:47:12'),(16,2,'Seguridad','Interruptor de parada funciona',2,1,'2026-01-18 17:47:12'),(17,2,'Seguridad','Arnes de sujecion en buen estado',3,1,'2026-01-18 17:47:12'),(18,2,'Motor','Nivel de combustible adecuado',4,0,'2026-01-18 17:47:12'),(19,2,'Motor','Filtro de aire limpio',5,0,'2026-01-18 17:47:12'),(20,2,'Motor','Arranque correcto',6,0,'2026-01-18 17:47:12'),(21,2,'Cabezal','Disco/cuchilla sin grietas o danos',7,1,'2026-01-18 17:47:12'),(22,2,'Cabezal','Fijacion del cabezal segura',8,1,'2026-01-18 17:47:12'),(23,2,'Cabezal','Hilo de nylon con longitud adecuada',9,0,'2026-01-18 17:47:12'),(24,2,'General','Barra de transmision sin vibraciones anormales',10,0,'2026-01-18 17:47:12'),(25,2,'General','Mangos y empunaduras firmes',11,0,'2026-01-18 17:47:12'),(26,3,'Seguridad','Protector de cuchillas en buen estado',1,1,'2026-01-18 17:47:12'),(27,3,'Seguridad','Interruptor de parada funciona',2,1,'2026-01-18 17:47:12'),(28,3,'Motor','Nivel de combustible adecuado',3,0,'2026-01-18 17:47:12'),(29,3,'Motor','Filtro de aire limpio',4,0,'2026-01-18 17:47:12'),(30,3,'Cuchillas','Cuchillas afiladas',5,1,'2026-01-18 17:47:12'),(31,3,'Cuchillas','Cuchillas sin danos o mellas',6,1,'2026-01-18 17:47:12'),(32,3,'General','Mangos en buen estado',7,0,'2026-01-18 17:47:12'),(33,4,'Seguridad','Parada de emergencia funciona',1,1,'2026-01-18 17:47:12'),(34,4,'Seguridad','Barandillas y portezuelas en buen estado',2,1,'2026-01-18 17:47:12'),(35,4,'Seguridad','Anclajes para arnes disponibles',3,1,'2026-01-18 17:47:12'),(36,4,'Seguridad','Alarmas de inclinacion funcionan',4,1,'2026-01-18 17:47:12'),(37,4,'Sistemas','Sistema hidraulico sin fugas',5,1,'2026-01-18 17:47:12'),(38,4,'Sistemas','Nivel de aceite hidraulico correcto',6,1,'2026-01-18 17:47:12'),(39,4,'Sistemas','Controles de elevacion funcionan',7,1,'2026-01-18 17:47:12'),(40,4,'Sistemas','Controles de desplazamiento funcionan',8,1,'2026-01-18 17:47:12'),(41,4,'Estructura','Estructura sin grietas o danos visibles',9,1,'2026-01-18 17:47:12'),(42,4,'Estructura','Ruedas/orugas en buen estado',10,1,'2026-01-18 17:47:12'),(43,4,'Estructura','Estabilizadores funcionan correctamente',11,1,'2026-01-18 17:47:12'),(44,4,'Electrico','Bateria cargada',12,1,'2026-01-18 17:47:12'),(45,4,'Electrico','Luces de trabajo funcionan',13,0,'2026-01-18 17:47:12'),(46,5,'Seguridad','Dispositivos de seguridad funcionan',1,1,'2026-01-18 17:47:12'),(47,5,'Seguridad','Parada de emergencia operativa',2,1,'2026-01-18 17:47:12'),(48,5,'Estado General','Sin danos visibles en la estructura',3,1,'2026-01-18 17:47:12'),(49,5,'Estado General','Sin fugas de fluidos',4,1,'2026-01-18 17:47:12'),(50,5,'Estado General','Controles funcionan correctamente',5,1,'2026-01-18 17:47:12'),(51,5,'Mantenimiento','Niveles de fluidos correctos',6,0,'2026-01-18 17:47:12'),(52,5,'Mantenimiento','Limpieza general adecuada',7,0,'2026-01-18 17:47:12');
/*!40000 ALTER TABLE `maquinaria_checklist_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maquinaria_checklist_plantillas`
--

DROP TABLE IF EXISTS `maquinaria_checklist_plantillas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `maquinaria_checklist_plantillas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `maquinaria_tipo_id` bigint(20) unsigned DEFAULT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `maquinaria_checklist_plantillas_maquinaria_tipo_id_foreign` (`maquinaria_tipo_id`),
  CONSTRAINT `maquinaria_checklist_plantillas_maquinaria_tipo_id_foreign` FOREIGN KEY (`maquinaria_tipo_id`) REFERENCES `maquinaria_tipos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maquinaria_checklist_plantillas`
--

LOCK TABLES `maquinaria_checklist_plantillas` WRITE;
/*!40000 ALTER TABLE `maquinaria_checklist_plantillas` DISABLE KEYS */;
INSERT INTO `maquinaria_checklist_plantillas` VALUES (1,1,'Inspeccion Diaria Motosierra','Checklist de seguridad antes del uso diario',1,'2026-01-18 17:47:12','2026-01-18 17:47:12'),(2,2,'Inspeccion Diaria Desbrozadora','Checklist de seguridad para desbrozadoras',1,'2026-01-18 17:47:12','2026-01-18 17:47:12'),(3,7,'Inspeccion Diaria Cortasetos','Checklist de seguridad para cortasetos',1,'2026-01-18 17:47:12','2026-01-18 17:47:12'),(4,11,'Inspeccion Pre-Uso Plataforma','Checklist obligatorio antes de usar plataforma elevadora',1,'2026-01-18 17:47:12','2026-01-18 17:47:12'),(5,NULL,'Inspeccion Generica','Checklist basico aplicable a cualquier maquinaria',1,'2026-01-18 17:47:12','2026-01-18 17:47:12');
/*!40000 ALTER TABLE `maquinaria_checklist_plantillas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maquinaria_documentos`
--

DROP TABLE IF EXISTS `maquinaria_documentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `maquinaria_documentos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `maquinaria_id` bigint(20) unsigned NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `archivo_path` varchar(500) NOT NULL,
  `subido_por` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `maquinaria_documentos_maquinaria_id_foreign` (`maquinaria_id`),
  KEY `maquinaria_documentos_subido_por_foreign` (`subido_por`),
  CONSTRAINT `maquinaria_documentos_maquinaria_id_foreign` FOREIGN KEY (`maquinaria_id`) REFERENCES `maquinaria` (`id`) ON DELETE CASCADE,
  CONSTRAINT `maquinaria_documentos_subido_por_foreign` FOREIGN KEY (`subido_por`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maquinaria_documentos`
--

LOCK TABLES `maquinaria_documentos` WRITE;
/*!40000 ALTER TABLE `maquinaria_documentos` DISABLE KEYS */;
/*!40000 ALTER TABLE `maquinaria_documentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maquinaria_inspeccion_items`
--

DROP TABLE IF EXISTS `maquinaria_inspeccion_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `maquinaria_inspeccion_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `inspeccion_id` bigint(20) unsigned NOT NULL,
  `checklist_item_id` bigint(20) unsigned NOT NULL,
  `cumple` tinyint(1) DEFAULT NULL,
  `observacion` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `maquinaria_inspeccion_items_inspeccion_id_foreign` (`inspeccion_id`),
  KEY `maquinaria_inspeccion_items_checklist_item_id_foreign` (`checklist_item_id`),
  CONSTRAINT `maquinaria_inspeccion_items_checklist_item_id_foreign` FOREIGN KEY (`checklist_item_id`) REFERENCES `maquinaria_checklist_items` (`id`),
  CONSTRAINT `maquinaria_inspeccion_items_inspeccion_id_foreign` FOREIGN KEY (`inspeccion_id`) REFERENCES `maquinaria_inspecciones` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maquinaria_inspeccion_items`
--

LOCK TABLES `maquinaria_inspeccion_items` WRITE;
/*!40000 ALTER TABLE `maquinaria_inspeccion_items` DISABLE KEYS */;
INSERT INTO `maquinaria_inspeccion_items` VALUES (1,1,1,1,NULL,'2026-01-18 18:18:05'),(2,1,2,1,NULL,'2026-01-18 18:18:05'),(3,1,3,1,NULL,'2026-01-18 18:18:05'),(4,1,4,1,NULL,'2026-01-18 18:18:05'),(5,1,5,1,NULL,'2026-01-18 18:18:05'),(6,1,6,1,NULL,'2026-01-18 18:18:05'),(7,1,7,1,NULL,'2026-01-18 18:18:05'),(8,1,8,1,NULL,'2026-01-18 18:18:05'),(9,1,9,1,NULL,'2026-01-18 18:18:05'),(10,1,10,0,NULL,'2026-01-18 18:18:05'),(11,1,11,1,NULL,'2026-01-18 18:18:05'),(12,1,12,1,NULL,'2026-01-18 18:18:05'),(13,1,13,1,NULL,'2026-01-18 18:18:05'),(14,1,14,1,NULL,'2026-01-18 18:18:05'),(15,2,46,1,'Funciona','2026-01-22 15:10:20'),(16,2,47,1,'Funciona','2026-01-22 15:10:20'),(17,2,48,1,'Funciona','2026-01-22 15:10:20'),(18,2,49,1,'Funciona','2026-01-22 15:10:20'),(19,2,50,1,'Funciona','2026-01-22 15:10:20'),(20,2,51,1,'Funciona','2026-01-22 15:10:20'),(21,2,52,1,'Funciona','2026-01-22 15:10:20');
/*!40000 ALTER TABLE `maquinaria_inspeccion_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maquinaria_inspecciones`
--

DROP TABLE IF EXISTS `maquinaria_inspecciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `maquinaria_inspecciones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `maquinaria_id` bigint(20) unsigned NOT NULL,
  `plantilla_id` bigint(20) unsigned NOT NULL,
  `fecha_inspeccion` date NOT NULL,
  `fecha_proxima_inspeccion` date DEFAULT NULL,
  `resultado` enum('apto','no_apto') NOT NULL,
  `observaciones` text DEFAULT NULL,
  `realizado_por` bigint(20) unsigned NOT NULL,
  `firma_path` varchar(500) DEFAULT NULL,
  `documento_path` varchar(500) DEFAULT NULL COMMENT 'PDF generado',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `maquinaria_inspecciones_maquinaria_id_foreign` (`maquinaria_id`),
  KEY `maquinaria_inspecciones_plantilla_id_foreign` (`plantilla_id`),
  KEY `maquinaria_inspecciones_realizado_por_foreign` (`realizado_por`),
  CONSTRAINT `maquinaria_inspecciones_maquinaria_id_foreign` FOREIGN KEY (`maquinaria_id`) REFERENCES `maquinaria` (`id`) ON DELETE CASCADE,
  CONSTRAINT `maquinaria_inspecciones_plantilla_id_foreign` FOREIGN KEY (`plantilla_id`) REFERENCES `maquinaria_checklist_plantillas` (`id`),
  CONSTRAINT `maquinaria_inspecciones_realizado_por_foreign` FOREIGN KEY (`realizado_por`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maquinaria_inspecciones`
--

LOCK TABLES `maquinaria_inspecciones` WRITE;
/*!40000 ALTER TABLE `maquinaria_inspecciones` DISABLE KEYS */;
INSERT INTO `maquinaria_inspecciones` VALUES (1,1,1,'2026-01-18',NULL,'no_apto',NULL,1,NULL,NULL,'2026-01-18 18:18:05'),(2,1,5,'2026-01-22',NULL,'apto','Funciona',1,NULL,NULL,'2026-01-22 15:10:20');
/*!40000 ALTER TABLE `maquinaria_inspecciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maquinaria_mantenimientos`
--

DROP TABLE IF EXISTS `maquinaria_mantenimientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `maquinaria_mantenimientos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `maquinaria_id` bigint(20) unsigned NOT NULL,
  `tipo` enum('preventivo','correctivo') NOT NULL,
  `fecha` date NOT NULL,
  `descripcion` text NOT NULL,
  `coste` decimal(10,2) DEFAULT NULL,
  `proveedor` varchar(255) DEFAULT NULL,
  `realizado_por` varchar(255) DEFAULT NULL,
  `proxima_revision` date DEFAULT NULL,
  `documento_path` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `maquinaria_mantenimientos_maquinaria_id_foreign` (`maquinaria_id`),
  CONSTRAINT `maquinaria_mantenimientos_maquinaria_id_foreign` FOREIGN KEY (`maquinaria_id`) REFERENCES `maquinaria` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maquinaria_mantenimientos`
--

LOCK TABLES `maquinaria_mantenimientos` WRITE;
/*!40000 ALTER TABLE `maquinaria_mantenimientos` DISABLE KEYS */;
INSERT INTO `maquinaria_mantenimientos` VALUES (1,1,'correctivo','2026-01-18','Afilado de cadena',25.00,'Taller STIHL Barcelona',NULL,NULL,NULL,'2026-01-18 18:18:41','2026-01-18 18:18:41');
/*!40000 ALTER TABLE `maquinaria_mantenimientos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maquinaria_tipos`
--

DROP TABLE IF EXISTS `maquinaria_tipos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `maquinaria_tipos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL COMMENT 'Motosierra, Sopladora, Desbrozadora, etc.',
  `descripcion` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maquinaria_tipos`
--

LOCK TABLES `maquinaria_tipos` WRITE;
/*!40000 ALTER TABLE `maquinaria_tipos` DISABLE KEYS */;
INSERT INTO `maquinaria_tipos` VALUES (1,'Motosierra','Motosierra profesional','2025-12-23 14:13:27','2025-12-23 14:13:27'),(2,'Desbrozadora','Desbrozadora de mochila o ruedas','2025-12-23 14:13:27','2025-12-23 14:13:27'),(3,'Sopladora','Sopladora de hojas','2025-12-23 14:13:27','2025-12-23 14:13:27'),(4,'Motocultor','Motocultor agrícola','2025-12-23 14:13:27','2025-12-23 14:13:27'),(5,'Pulverizador','Equipo de pulverización de herbicidas','2025-12-23 14:13:27','2025-12-23 14:13:27'),(6,'Trituradora','Trituradora de ramas','2025-12-23 14:13:27','2025-12-23 14:13:27'),(7,'Cortasetos','Herramienta para poda de setos y arbustos','2026-01-18 17:37:24','2026-01-18 17:37:24'),(8,'Motoguadaña','Desbrozadora profesional de alta potencia','2026-01-18 17:37:24','2026-01-18 17:37:24'),(9,'Biotrituradora','Maquina para triturar restos vegetales','2026-01-18 17:37:24','2026-01-18 17:37:24'),(10,'Ahoyadora','Herramienta para hacer hoyos en el suelo','2026-01-18 17:37:24','2026-01-18 17:37:24'),(11,'Plataforma Elevadora','Equipo de elevacion para trabajos en altura','2026-01-18 17:37:24','2026-01-18 17:37:24'),(12,'Mini Excavadora','Excavadora compacta para trabajos forestales','2026-01-18 17:37:24','2026-01-18 17:37:24'),(13,'Dumper','Vehiculo de carga para transporte de materiales','2026-01-18 17:37:24','2026-01-18 17:37:24'),(14,'Grupo Electrogeno','Generador electrico portatil','2026-01-18 17:37:24','2026-01-18 17:37:24'),(15,'Compresor','Equipo para aire comprimido','2026-01-18 17:37:24','2026-01-18 17:37:24'),(16,'Martillo Hidraulico','Herramienta de percusion para demolicion','2026-01-18 17:37:24','2026-01-18 17:37:24');
/*!40000 ALTER TABLE `maquinaria_tipos` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_resets_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',1),(5,'2025_06_18_195318_create_permission_tables',1),(6,'2025_06_20_000001_create_formacion_tipos_table',1),(7,'2025_06_20_000002_create_epi_catalogo_table',1),(8,'2025_06_20_000003_create_obra_tipos_table',1),(9,'2025_06_20_000004_create_maquinaria_tipos_table',1),(10,'2025_06_20_000005_create_vehiculo_tipos_table',1),(11,'2025_06_20_000006_create_contrato_tipos_table',1),(12,'2025_06_20_000007_create_gasto_categorias_table',1),(13,'2025_06_20_000008_create_alerta_configuraciones_table',1),(14,'2025_06_20_000009_create_caducidades_generales_table',1),(15,'2025_06_20_000010_create_subcontratas_table',1),(16,'2025_06_20_000011_create_clientes_table',1),(17,'2025_06_20_000012_create_trabajadores_table',1),(18,'2025_06_20_000013_create_contratos_table',1),(19,'2025_06_20_000014_create_cuadrillas_table',1),(20,'2025_06_20_000015_create_leads_table',1),(21,'2025_06_20_000016_create_trabajador_documentos_table',1),(22,'2025_06_20_000017_create_documento_lecturas_table',1),(23,'2025_06_20_000018_create_trabajador_historial_disciplinario_table',1),(24,'2025_06_20_000019_create_trabajador_formaciones_table',1),(25,'2025_06_20_000020_create_epi_inventario_table',1),(26,'2025_06_20_000021_create_epi_entregas_table',1),(27,'2025_06_20_000022_create_epi_revisiones_table',1),(28,'2025_06_20_000023_create_cuadrilla_trabajadores_table',1),(29,'2025_06_20_000024_create_lead_interacciones_table',1),(30,'2025_06_20_000025_create_subcontrata_documentos_cae_table',1),(31,'2025_06_20_000026_create_vehiculos_table',1),(32,'2025_06_20_000027_create_vehiculo_documentos_table',1),(33,'2025_06_20_000028_create_maquinaria_table',1),(34,'2025_06_20_000029_create_obras_table',1),(35,'2025_06_20_000030_create_obra_hitos_table',1),(36,'2025_06_20_000031_create_obra_trabajadores_table',1),(37,'2025_06_20_000032_create_obra_cuadrillas_table',1),(38,'2025_06_20_000033_create_obra_documentos_table',1),(39,'2025_06_20_000034_create_obra_historial_table',1),(40,'2025_06_20_000035_create_obra_subcontratas_table',1),(41,'2025_06_20_000036_create_subcontrata_documentos_obra_table',1),(42,'2025_06_20_000037_create_maquinaria_asignaciones_table',1),(43,'2025_06_20_000038_create_maquinaria_checklist_plantillas_table',1),(44,'2025_06_20_000039_create_fichajes_table',1),(45,'2025_06_20_000040_create_partes_diarios_table',1),(46,'2025_06_20_000041_create_parte_diario_trabajadores_table',1),(47,'2025_06_20_000042_create_parte_diario_lineas_table',1),(48,'2025_06_20_000043_create_parte_diario_herbicidas_table',1),(49,'2025_06_20_000044_create_maquinaria_checklist_items_table',1),(50,'2025_06_20_000045_create_maquinaria_inspecciones_table',1),(51,'2025_06_20_000046_create_maquinaria_inspeccion_items_table',1),(52,'2025_06_20_000047_create_maquinaria_mantenimientos_table',1),(53,'2025_06_20_000048_create_facturas_table',1),(54,'2025_06_20_000049_create_factura_lineas_table',1),(55,'2025_06_20_000050_create_ingresos_table',1),(56,'2025_06_20_000051_create_gastos_table',1),(57,'2025_06_20_000052_create_prima_configuraciones_table',1),(58,'2025_06_20_000053_create_primas_trabajador_table',1),(59,'2025_06_20_000054_create_alertas_table',1),(60,'2025_06_20_000055_create_auditoria_table',1),(61,'2026_01_10_190941_create_obra_conceptos_produccion_table',2),(62,'2026_01_10_191023_create_parte_diario_producciones_table',2),(63,'2026_01_10_191108_create_obra_discrepancias_valoracion_table',2),(64,'2026_01_10_191142_create_trabajador_bonos_table',2),(65,'2026_01_10_191151_add_importe_total_to_partes_diarios_table',2),(66,'2026_01_10_191201_add_importe_fields_to_obras_table',2),(67,'2026_01_16_000000_drop_unique_obra_periodo_from_discrepancias',3),(68,'2026_01_16_000001_drop_unique_parte_obra_fecha',4),(69,'2026_01_16_000002_drop_unique_fichaje_dia',5),(70,'2026_01_18_000000_add_fields_to_contratos_table',6),(71,'2026_01_18_204800_make_facturas_numero_nullable',7),(72,'2026_01_19_000001_create_email_logs_table',8),(73,'2026_01_19_000002_add_email_fields_to_facturas_table',8),(74,'2025_01_29_000001_create_epi_inventario_documentos_table',9),(75,'2025_06_20_000050_create_maquinaria_documentos_table',9),(76,'2026_01_29_000001_add_fechas_facturacion_to_obras_table',9),(77,'2026_01_30_000001_create_contrato_liberaciones_table',9),(78,'2026_01_30_000002_modify_contratos_for_partial_releases',9),(79,'2026_01_30_000003_add_footer_text_to_facturas_table',9),(80,'2026_01_30_000005_create_cliente_emails_adicionales_table',9),(81,'2026_01_30_000006_add_multiple_recipients_to_email_logs',9),(82,'2026_01_30_120000_add_horas_to_trabajador_bonos',9),(83,'2026_01_31_000001_add_profile_photo_to_users_table',9),(84,'2026_01_31_100000_create_documentos_empresa_table',9),(85,'2026_02_12_000001_add_grupo_to_factura_lineas_table',9),(86,'2026_02_12_114740_create_parte_diario_documentos_table',10),(87,'2026_02_13_000001_add_tipo_fecha_fin_to_partes_diarios_table',11),(88,'2026_03_02_000001_create_tableros_table',12),(89,'2026_03_02_000002_create_tablero_miembros_table',12),(90,'2026_03_02_000003_create_tablero_columnas_table',12),(91,'2026_03_02_000004_create_tablero_etiquetas_table',12),(92,'2026_03_02_000005_create_tarjetas_table',12),(93,'2026_03_02_000006_create_tarjeta_usuarios_table',12),(94,'2026_03_02_000007_create_tarjeta_etiquetas_table',12),(95,'2026_03_02_000008_create_tarjeta_checklists_table',12),(96,'2026_03_02_000009_create_tarjeta_checklist_items_table',12),(97,'2026_03_02_000010_create_tarjeta_comentarios_table',12),(98,'2026_03_02_000011_create_tarjeta_adjuntos_table',12),(99,'2026_03_02_200000_add_iban_to_trabajadores_table',13),(100,'2026_03_03_000001_create_cumpleanos_configuracion_table',14);
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
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1),(1,'App\\Models\\User',9),(2,'App\\Models\\User',2),(3,'App\\Models\\User',3),(4,'App\\Models\\User',4),(6,'App\\Models\\User',5),(6,'App\\Models\\User',6),(6,'App\\Models\\User',7),(6,'App\\Models\\User',8),(6,'App\\Models\\User',10);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `obra_conceptos_produccion`
--

DROP TABLE IF EXISTS `obra_conceptos_produccion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `obra_conceptos_produccion` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `obra_id` bigint(20) unsigned NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `categoria` enum('desbroce','limpieza','herbicida','tala','poda','otro') NOT NULL,
  `unidad` enum('m2','unidades','hectareas','jornal') NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `orden` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_obra_codigo` (`obra_id`,`codigo`),
  KEY `idx_obra_activo` (`obra_id`,`activo`),
  CONSTRAINT `obra_conceptos_produccion_obra_id_foreign` FOREIGN KEY (`obra_id`) REFERENCES `obras` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `obra_conceptos_produccion`
--

LOCK TABLES `obra_conceptos_produccion` WRITE;
/*!40000 ALTER TABLE `obra_conceptos_produccion` DISABLE KEYS */;
INSERT INTO `obra_conceptos_produccion` VALUES (1,4,'110231','Tala amplia','descrip','desbroce','m2',100.00,1,0,'2026-01-13 15:46:33','2026-01-13 15:52:53'),(2,4,'l220','P5','Con dificultad','desbroce','m2',0.04,1,0,'2026-01-13 17:27:11','2026-01-13 17:27:11');
/*!40000 ALTER TABLE `obra_conceptos_produccion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `obra_cuadrillas`
--

DROP TABLE IF EXISTS `obra_cuadrillas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `obra_cuadrillas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `obra_id` bigint(20) unsigned NOT NULL,
  `cuadrilla_id` bigint(20) unsigned NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `obra_cuadrillas_obra_id_foreign` (`obra_id`),
  KEY `obra_cuadrillas_cuadrilla_id_foreign` (`cuadrilla_id`),
  CONSTRAINT `obra_cuadrillas_cuadrilla_id_foreign` FOREIGN KEY (`cuadrilla_id`) REFERENCES `cuadrillas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `obra_cuadrillas_obra_id_foreign` FOREIGN KEY (`obra_id`) REFERENCES `obras` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `obra_cuadrillas`
--

LOCK TABLES `obra_cuadrillas` WRITE;
/*!40000 ALTER TABLE `obra_cuadrillas` DISABLE KEYS */;
INSERT INTO `obra_cuadrillas` VALUES (1,1,1,'2025-11-28',NULL,1,'2025-12-23 14:13:27','2025-12-23 14:13:27'),(2,4,2,'2026-01-13','2026-02-04',0,'2026-01-13 17:13:40','2026-02-04 19:24:05'),(3,4,3,'2026-02-04',NULL,1,'2026-02-04 19:23:59','2026-02-04 19:23:59');
/*!40000 ALTER TABLE `obra_cuadrillas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `obra_discrepancias_valoracion`
--

DROP TABLE IF EXISTS `obra_discrepancias_valoracion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `obra_discrepancias_valoracion` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `obra_id` bigint(20) unsigned NOT NULL,
  `periodo_mes` varchar(7) NOT NULL,
  `importe_producido_manzer` decimal(14,2) NOT NULL,
  `importe_validado_cuadrilla` decimal(14,2) DEFAULT NULL,
  `importe_aceptado_cliente` decimal(14,2) DEFAULT NULL,
  `fecha_respuesta_cliente` date DEFAULT NULL,
  `importe_pendiente` decimal(14,2) NOT NULL,
  `estado` enum('pendiente','parcial','resuelto') NOT NULL DEFAULT 'pendiente',
  `notas` text DEFAULT NULL,
  `documento_valoracion_path` varchar(500) DEFAULT NULL,
  `registrado_por` bigint(20) unsigned NOT NULL,
  `fecha_resolucion` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `obra_discrepancias_valoracion_registrado_por_foreign` (`registrado_por`),
  KEY `idx_obra_estado` (`obra_id`,`estado`),
  CONSTRAINT `obra_discrepancias_valoracion_obra_id_foreign` FOREIGN KEY (`obra_id`) REFERENCES `obras` (`id`) ON DELETE CASCADE,
  CONSTRAINT `obra_discrepancias_valoracion_registrado_por_foreign` FOREIGN KEY (`registrado_por`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `obra_discrepancias_valoracion`
--

LOCK TABLES `obra_discrepancias_valoracion` WRITE;
/*!40000 ALTER TABLE `obra_discrepancias_valoracion` DISABLE KEYS */;
INSERT INTO `obra_discrepancias_valoracion` VALUES (1,4,'2026-01',50000.00,4000.00,500.00,'2026-01-13',49500.00,'parcial','mal','obras/4/discrepancias/valoracion_4_2026-01.pdf',1,NULL,'2026-01-13 15:47:50','2026-01-13 15:48:27'),(2,4,'2026-01',1815.04,1500.00,1200.00,NULL,615.04,'parcial','Segunda discrepancia de prueba para el mismo período',NULL,1,NULL,'2026-01-16 23:46:12','2026-01-16 23:46:12');
/*!40000 ALTER TABLE `obra_discrepancias_valoracion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `obra_documentos`
--

DROP TABLE IF EXISTS `obra_documentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `obra_documentos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `obra_id` bigint(20) unsigned NOT NULL,
  `tipo` enum('contrato','plano','permiso','acta','foto','informe','otro') NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `archivo_path` varchar(500) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_documento` date DEFAULT NULL,
  `subido_por` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `obra_documentos_obra_id_foreign` (`obra_id`),
  KEY `obra_documentos_subido_por_foreign` (`subido_por`),
  CONSTRAINT `obra_documentos_obra_id_foreign` FOREIGN KEY (`obra_id`) REFERENCES `obras` (`id`) ON DELETE CASCADE,
  CONSTRAINT `obra_documentos_subido_por_foreign` FOREIGN KEY (`subido_por`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `obra_documentos`
--

LOCK TABLES `obra_documentos` WRITE;
/*!40000 ALTER TABLE `obra_documentos` DISABLE KEYS */;
INSERT INTO `obra_documentos` VALUES (1,4,'plano','Plano','uploads/obras/4/documentos/1768087236_1.pdf',NULL,'2026-01-10',1,'2026-01-10 23:20:36','2026-01-10 23:20:36');
/*!40000 ALTER TABLE `obra_documentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `obra_historial`
--

DROP TABLE IF EXISTS `obra_historial`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `obra_historial` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `obra_id` bigint(20) unsigned NOT NULL,
  `estado_anterior` varchar(50) DEFAULT NULL,
  `estado_nuevo` varchar(50) NOT NULL,
  `comentario` text DEFAULT NULL,
  `cambiado_por` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `obra_historial_obra_id_foreign` (`obra_id`),
  KEY `obra_historial_cambiado_por_foreign` (`cambiado_por`),
  CONSTRAINT `obra_historial_cambiado_por_foreign` FOREIGN KEY (`cambiado_por`) REFERENCES `users` (`id`),
  CONSTRAINT `obra_historial_obra_id_foreign` FOREIGN KEY (`obra_id`) REFERENCES `obras` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `obra_historial`
--

LOCK TABLES `obra_historial` WRITE;
/*!40000 ALTER TABLE `obra_historial` DISABLE KEYS */;
INSERT INTO `obra_historial` VALUES (1,4,NULL,'presentada','Obra creada',1,'2025-12-27 14:11:28'),(2,4,'presentada','aprobada',NULL,1,'2025-12-27 14:16:13');
/*!40000 ALTER TABLE `obra_historial` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `obra_hitos`
--

DROP TABLE IF EXISTS `obra_hitos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `obra_hitos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `obra_id` bigint(20) unsigned NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `porcentaje_obra` int(11) DEFAULT NULL COMMENT 'Ej: 30 = 30% completado',
  `fecha_prevista` date DEFAULT NULL,
  `fecha_completado` date DEFAULT NULL,
  `importe_cobro` decimal(12,2) DEFAULT NULL COMMENT 'Cobro parcial asociado al hito',
  `completado` tinyint(1) NOT NULL DEFAULT 0,
  `orden` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `obra_hitos_obra_id_foreign` (`obra_id`),
  CONSTRAINT `obra_hitos_obra_id_foreign` FOREIGN KEY (`obra_id`) REFERENCES `obras` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `obra_hitos`
--

LOCK TABLES `obra_hitos` WRITE;
/*!40000 ALTER TABLE `obra_hitos` DISABLE KEYS */;
INSERT INTO `obra_hitos` VALUES (1,4,'Hito prueba',NULL,10,'2025-05-15',NULL,NULL,0,1,'2026-01-10 23:15:51','2026-01-10 23:15:51');
/*!40000 ALTER TABLE `obra_hitos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `obra_subcontratas`
--

DROP TABLE IF EXISTS `obra_subcontratas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `obra_subcontratas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `obra_id` bigint(20) unsigned NOT NULL,
  `subcontrata_id` bigint(20) unsigned NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `importe_contratado` decimal(12,2) DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `obra_subcontratas_obra_id_foreign` (`obra_id`),
  KEY `obra_subcontratas_subcontrata_id_foreign` (`subcontrata_id`),
  CONSTRAINT `obra_subcontratas_obra_id_foreign` FOREIGN KEY (`obra_id`) REFERENCES `obras` (`id`) ON DELETE CASCADE,
  CONSTRAINT `obra_subcontratas_subcontrata_id_foreign` FOREIGN KEY (`subcontrata_id`) REFERENCES `subcontratas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `obra_subcontratas`
--

LOCK TABLES `obra_subcontratas` WRITE;
/*!40000 ALTER TABLE `obra_subcontratas` DISABLE KEYS */;
/*!40000 ALTER TABLE `obra_subcontratas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `obra_tipos`
--

DROP TABLE IF EXISTS `obra_tipos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `obra_tipos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL COMMENT 'desbroce, tala, poda, emergencia, mixto',
  `descripcion` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `obra_tipos`
--

LOCK TABLES `obra_tipos` WRITE;
/*!40000 ALTER TABLE `obra_tipos` DISABLE KEYS */;
INSERT INTO `obra_tipos` VALUES (1,'Desbroce','Limpieza y desbroce de vegetación','2025-12-23 14:13:27','2025-12-23 14:13:27'),(2,'Tala','Tala de árboles','2025-12-23 14:13:27','2025-12-23 14:13:27'),(3,'Poda','Poda de árboles y arbustos','2025-12-23 14:13:27','2025-12-23 14:13:27'),(4,'Herbicida','Aplicación de herbicidas','2025-12-23 14:13:27','2025-12-23 14:13:27'),(5,'Emergencia','Trabajos de emergencia','2025-12-23 14:13:27','2025-12-23 14:13:27'),(6,'Mixto','Trabajos combinados','2025-12-23 14:13:27','2025-12-23 14:13:27');
/*!40000 ALTER TABLE `obra_tipos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `obra_trabajadores`
--

DROP TABLE IF EXISTS `obra_trabajadores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `obra_trabajadores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `obra_id` bigint(20) unsigned NOT NULL,
  `trabajador_id` bigint(20) unsigned NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `rol` varchar(100) DEFAULT NULL COMMENT 'Ej: operario, capataz, aplicador',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `obra_trabajadores_obra_id_foreign` (`obra_id`),
  KEY `obra_trabajadores_trabajador_id_foreign` (`trabajador_id`),
  CONSTRAINT `obra_trabajadores_obra_id_foreign` FOREIGN KEY (`obra_id`) REFERENCES `obras` (`id`) ON DELETE CASCADE,
  CONSTRAINT `obra_trabajadores_trabajador_id_foreign` FOREIGN KEY (`trabajador_id`) REFERENCES `trabajadores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `obra_trabajadores`
--

LOCK TABLES `obra_trabajadores` WRITE;
/*!40000 ALTER TABLE `obra_trabajadores` DISABLE KEYS */;
INSERT INTO `obra_trabajadores` VALUES (1,4,11,'2026-01-10',NULL,NULL,1,'2026-01-10 23:14:29','2026-01-10 23:14:29'),(2,4,3,'2026-01-10',NULL,NULL,1,'2026-01-10 23:15:32','2026-01-10 23:15:32'),(3,4,9,'2026-01-13',NULL,NULL,1,'2026-01-13 17:13:45','2026-01-13 17:13:45'),(4,4,12,'2026-01-22',NULL,'sub',1,'2026-01-22 15:19:54','2026-01-22 15:19:54'),(5,4,14,'2026-02-04',NULL,'sub',1,'2026-02-04 19:23:51','2026-02-04 19:23:51');
/*!40000 ALTER TABLE `obra_trabajadores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `obras`
--

DROP TABLE IF EXISTS `obras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `obras` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) NOT NULL COMMENT 'Código interno',
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `cliente_id` bigint(20) unsigned NOT NULL,
  `obra_tipo_id` bigint(20) unsigned DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `localidad` varchar(150) DEFAULT NULL,
  `provincia` varchar(100) DEFAULT NULL,
  `codigo_postal` varchar(10) DEFAULT NULL,
  `coordenadas_lat` decimal(10,8) DEFAULT NULL,
  `coordenadas_lng` decimal(11,8) DEFAULT NULL,
  `linea` varchar(100) DEFAULT NULL COMMENT 'Ej: L220 E1',
  `trayecto` varchar(255) DEFAULT NULL COMMENT 'Ej: Calaf - Manresa',
  `pk_inicio` varchar(20) DEFAULT NULL,
  `pk_fin` varchar(20) DEFAULT NULL,
  `gerencia_jefatura` varchar(50) DEFAULT NULL COMMENT 'BCN, ZGZ, etc.',
  `distrito` varchar(100) DEFAULT NULL,
  `fecha_inicio_prevista` date DEFAULT NULL,
  `fecha_fin_prevista` date DEFAULT NULL,
  `fecha_inicio_real` date DEFAULT NULL,
  `fecha_fin_real` date DEFAULT NULL,
  `fecha_facturacion_inicio` date DEFAULT NULL,
  `fecha_facturacion_fin` date DEFAULT NULL,
  `presupuesto` decimal(14,2) DEFAULT NULL,
  `coste_estimado` decimal(14,2) DEFAULT NULL,
  `margen_previsto` decimal(14,2) DEFAULT NULL,
  `importe_producido_acumulado` decimal(14,2) NOT NULL DEFAULT 0.00,
  `importe_pendiente_acumulado` decimal(14,2) NOT NULL DEFAULT 0.00,
  `estado` enum('presentada','aprobada','en_curso','pausada','finalizada','cancelada') NOT NULL DEFAULT 'presentada',
  `riesgo_operativo` enum('bajo','medio','alto') NOT NULL DEFAULT 'bajo',
  `tiene_penalizaciones` tinyint(1) NOT NULL DEFAULT 0,
  `importe_penalizacion_prevista` decimal(12,2) DEFAULT NULL,
  `contrato_id` bigint(20) unsigned DEFAULT NULL,
  `centro_coste` varchar(50) DEFAULT NULL,
  `encargado_id` bigint(20) unsigned DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `obras_codigo_unique` (`codigo`),
  KEY `obras_cliente_id_foreign` (`cliente_id`),
  KEY `obras_obra_tipo_id_foreign` (`obra_tipo_id`),
  KEY `obras_contrato_id_foreign` (`contrato_id`),
  KEY `obras_encargado_id_foreign` (`encargado_id`),
  CONSTRAINT `obras_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `obras_contrato_id_foreign` FOREIGN KEY (`contrato_id`) REFERENCES `contratos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `obras_encargado_id_foreign` FOREIGN KEY (`encargado_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `obras_obra_tipo_id_foreign` FOREIGN KEY (`obra_tipo_id`) REFERENCES `obra_tipos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `obras`
--

LOCK TABLES `obras` WRITE;
/*!40000 ALTER TABLE `obras` DISABLE KEYS */;
INSERT INTO `obras` VALUES (1,'OBR-2025-001','Desbroce L220 Calaf-Manresa','Trabajos de desbroce y control de vegetación en línea ferroviaria L220',1,1,NULL,'Calaf','Barcelona',NULL,NULL,NULL,'L220 E1','Calaf - Manresa','262+000','280+000','BCN',NULL,'2025-11-23','2026-02-21','2025-11-28',NULL,NULL,NULL,85000.00,65000.00,NULL,0.00,0.00,'en_curso','bajo',0,NULL,NULL,NULL,3,NULL,'2025-12-23 14:13:27','2025-12-23 14:13:27',NULL),(2,'OBR-2025-002','Poda urbana Manresa','Poda de arbolado urbano en diversos puntos del municipio',2,3,NULL,'Manresa','Barcelona',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-07','2026-02-06',NULL,NULL,NULL,NULL,28000.00,22000.00,NULL,0.00,0.00,'aprobada','bajo',0,NULL,NULL,NULL,3,NULL,'2025-12-23 14:13:27','2025-12-23 14:13:27',NULL),(3,'OBR-2025-003','Tala selectiva Berguedà','Tala selectiva de pinos afectados por procesionaria',3,2,NULL,'Berga','Barcelona',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-24','2025-12-13','2025-10-29','2025-12-18',NULL,NULL,45000.00,38000.00,NULL,0.00,0.00,'finalizada','bajo',0,NULL,NULL,NULL,3,NULL,'2025-12-23 14:13:27','2025-12-23 14:13:27',NULL),(4,'OBR-2025--002','obra prueba','obra p',5,6,'Calle 69 #10-15','Bogotá','Distrito Capital','42342',42.00000000,34.00000000,'das','calaf','123','133','bcn','fijo','2025-12-28','2026-01-10',NULL,NULL,NULL,NULL,10000.00,200000.00,-190000.00,1815.04,50115.04,'aprobada','medio',1,342343.00,NULL,'3000000',3,'Mucho','2025-12-27 14:11:28','2026-01-16 23:46:12',NULL);
/*!40000 ALTER TABLE `obras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parte_diario_documentos`
--

DROP TABLE IF EXISTS `parte_diario_documentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parte_diario_documentos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parte_diario_id` bigint(20) unsigned NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `archivo_path` varchar(500) NOT NULL,
  `archivo_nombre_original` varchar(255) NOT NULL,
  `subido_por` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parte_diario_documentos_parte_diario_id_foreign` (`parte_diario_id`),
  KEY `parte_diario_documentos_subido_por_foreign` (`subido_por`),
  CONSTRAINT `parte_diario_documentos_parte_diario_id_foreign` FOREIGN KEY (`parte_diario_id`) REFERENCES `partes_diarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `parte_diario_documentos_subido_por_foreign` FOREIGN KEY (`subido_por`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parte_diario_documentos`
--

LOCK TABLES `parte_diario_documentos` WRITE;
/*!40000 ALTER TABLE `parte_diario_documentos` DISABLE KEYS */;
INSERT INTO `parte_diario_documentos` VALUES (1,15,'manzer-login','uploads/partes-diarios/15/1770915357_manzer-login.png','manzer-login.png',1,'2026-02-12 16:55:57','2026-02-12 16:55:57'),(2,17,'Screenshot 2026-01-20 114412','uploads/partes-diarios/17/1770931051_Screenshot 2026-01-20 114412.png','Screenshot 2026-01-20 114412.png',1,'2026-02-12 21:17:31','2026-02-12 21:17:31'),(3,18,'Screenshot 2026-01-20 114412','uploads/partes-diarios/18/1770931052_Screenshot 2026-01-20 114412.png','Screenshot 2026-01-20 114412.png',1,'2026-02-12 21:17:32','2026-02-12 21:17:32');
/*!40000 ALTER TABLE `parte_diario_documentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parte_diario_herbicidas`
--

DROP TABLE IF EXISTS `parte_diario_herbicidas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parte_diario_herbicidas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parte_diario_id` bigint(20) unsigned NOT NULL,
  `producto` varchar(255) NOT NULL,
  `numero_registro` varchar(100) DEFAULT NULL,
  `dosificacion` varchar(100) DEFAULT NULL,
  `cantidad` decimal(10,2) DEFAULT NULL,
  `unidad` varchar(20) DEFAULT NULL COMMENT 'litros, kg, etc.',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `parte_diario_herbicidas_parte_diario_id_foreign` (`parte_diario_id`),
  CONSTRAINT `parte_diario_herbicidas_parte_diario_id_foreign` FOREIGN KEY (`parte_diario_id`) REFERENCES `partes_diarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parte_diario_herbicidas`
--

LOCK TABLES `parte_diario_herbicidas` WRITE;
/*!40000 ALTER TABLE `parte_diario_herbicidas` DISABLE KEYS */;
/*!40000 ALTER TABLE `parte_diario_herbicidas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parte_diario_lineas`
--

DROP TABLE IF EXISTS `parte_diario_lineas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parte_diario_lineas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parte_diario_id` bigint(20) unsigned NOT NULL,
  `herbicida` tinyint(1) NOT NULL DEFAULT 0,
  `desbroce` tinyint(1) NOT NULL DEFAULT 0,
  `poda` tinyint(1) NOT NULL DEFAULT 0,
  `tala` tinyint(1) NOT NULL DEFAULT 0,
  `limpieza` tinyint(1) NOT NULL DEFAULT 0,
  `pk_inicio` varchar(20) DEFAULT NULL,
  `pk_fin` varchar(20) DEFAULT NULL,
  `margen_izquierda` tinyint(1) NOT NULL DEFAULT 0,
  `ancho_izquierda` decimal(8,2) DEFAULT NULL,
  `margen_derecha` tinyint(1) NOT NULL DEFAULT 0,
  `ancho_derecha` decimal(8,2) DEFAULT NULL,
  `unidades` int(11) DEFAULT NULL COMMENT 'Para talas/podas',
  `metros_cuadrados` decimal(12,2) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `orden` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `parte_diario_lineas_parte_diario_id_foreign` (`parte_diario_id`),
  CONSTRAINT `parte_diario_lineas_parte_diario_id_foreign` FOREIGN KEY (`parte_diario_id`) REFERENCES `partes_diarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parte_diario_lineas`
--

LOCK TABLES `parte_diario_lineas` WRITE;
/*!40000 ALTER TABLE `parte_diario_lineas` DISABLE KEYS */;
/*!40000 ALTER TABLE `parte_diario_lineas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parte_diario_producciones`
--

DROP TABLE IF EXISTS `parte_diario_producciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parte_diario_producciones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parte_diario_id` bigint(20) unsigned NOT NULL,
  `concepto_produccion_id` bigint(20) unsigned NOT NULL,
  `cantidad` decimal(12,2) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `importe_calculado` decimal(14,2) NOT NULL,
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_parte_concepto` (`parte_diario_id`,`concepto_produccion_id`),
  KEY `parte_diario_producciones_concepto_produccion_id_foreign` (`concepto_produccion_id`),
  KEY `idx_parte_importe` (`parte_diario_id`,`importe_calculado`),
  CONSTRAINT `parte_diario_producciones_concepto_produccion_id_foreign` FOREIGN KEY (`concepto_produccion_id`) REFERENCES `obra_conceptos_produccion` (`id`) ON DELETE CASCADE,
  CONSTRAINT `parte_diario_producciones_parte_diario_id_foreign` FOREIGN KEY (`parte_diario_id`) REFERENCES `partes_diarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parte_diario_producciones`
--

LOCK TABLES `parte_diario_producciones` WRITE;
/*!40000 ALTER TABLE `parte_diario_producciones` DISABLE KEYS */;
INSERT INTO `parte_diario_producciones` VALUES (1,3,1,8.00,100.00,800.00,NULL,'2026-01-13 15:53:22','2026-01-13 15:53:22'),(2,4,2,25376.00,0.04,1015.04,NULL,'2026-01-13 17:31:22','2026-01-13 17:31:22'),(3,10,2,25376.00,0.04,1015.04,NULL,'2026-01-17 00:01:00','2026-01-17 00:01:00'),(4,11,2,25376.00,0.04,1015.04,NULL,'2026-01-17 00:05:49','2026-01-17 00:05:49'),(5,12,1,2.00,100.00,200.00,NULL,'2026-02-12 16:21:35','2026-02-12 16:21:35'),(6,12,2,3.00,0.04,0.12,NULL,'2026-02-12 16:21:36','2026-02-12 16:21:36'),(7,14,1,3.00,100.00,300.00,NULL,'2026-02-12 16:41:45','2026-02-12 16:41:45'),(8,16,2,500.00,0.04,20.00,NULL,'2026-02-12 17:24:19','2026-02-12 17:24:19'),(9,17,1,23.00,100.00,2300.00,NULL,'2026-02-12 21:17:31','2026-02-12 21:17:31'),(10,18,1,23.00,100.00,2300.00,NULL,'2026-02-12 21:17:32','2026-02-12 21:17:32');
/*!40000 ALTER TABLE `parte_diario_producciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parte_diario_trabajadores`
--

DROP TABLE IF EXISTS `parte_diario_trabajadores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parte_diario_trabajadores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parte_diario_id` bigint(20) unsigned NOT NULL,
  `trabajador_id` bigint(20) unsigned NOT NULL,
  `es_aplicador` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Aplicador de herbicida',
  `dni_aplicador` varchar(20) DEFAULT NULL COMMENT 'Solo si es aplicador',
  `horas_trabajadas` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `parte_diario_trabajadores_parte_diario_id_foreign` (`parte_diario_id`),
  KEY `parte_diario_trabajadores_trabajador_id_foreign` (`trabajador_id`),
  CONSTRAINT `parte_diario_trabajadores_parte_diario_id_foreign` FOREIGN KEY (`parte_diario_id`) REFERENCES `partes_diarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `parte_diario_trabajadores_trabajador_id_foreign` FOREIGN KEY (`trabajador_id`) REFERENCES `trabajadores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parte_diario_trabajadores`
--

LOCK TABLES `parte_diario_trabajadores` WRITE;
/*!40000 ALTER TABLE `parte_diario_trabajadores` DISABLE KEYS */;
INSERT INTO `parte_diario_trabajadores` VALUES (1,1,9,0,NULL,NULL,'2025-12-27 14:20:01'),(2,1,11,0,NULL,NULL,'2025-12-27 14:20:01'),(3,2,1,0,NULL,NULL,'2026-01-10 23:17:10'),(4,3,1,0,NULL,NULL,'2026-01-13 15:53:22'),(5,3,7,0,NULL,NULL,'2026-01-13 15:53:22'),(6,4,10,0,NULL,NULL,'2026-01-13 17:31:22'),(7,4,9,0,NULL,NULL,'2026-01-13 17:31:22'),(8,4,11,0,NULL,NULL,'2026-01-13 17:31:22'),(9,5,10,0,NULL,NULL,'2026-01-13 17:32:03'),(10,5,9,0,NULL,NULL,'2026-01-13 17:32:03'),(11,5,11,0,NULL,NULL,'2026-01-13 17:32:03'),(12,6,10,0,NULL,NULL,'2026-01-13 17:32:37'),(13,6,9,0,NULL,NULL,'2026-01-13 17:32:37'),(14,6,11,0,NULL,NULL,'2026-01-13 17:32:37'),(15,7,10,0,NULL,NULL,'2026-01-16 23:51:04'),(16,7,9,0,NULL,NULL,'2026-01-16 23:51:04'),(17,7,11,0,NULL,NULL,'2026-01-16 23:51:04'),(18,8,10,0,NULL,NULL,'2026-01-16 23:51:29'),(19,8,9,0,NULL,NULL,'2026-01-16 23:51:29'),(20,8,11,0,NULL,NULL,'2026-01-16 23:51:29'),(21,9,10,0,NULL,NULL,'2026-01-16 23:53:00'),(22,9,9,0,NULL,NULL,'2026-01-16 23:53:00'),(23,9,11,0,NULL,NULL,'2026-01-16 23:53:00'),(24,10,10,0,NULL,NULL,'2026-01-17 00:01:00'),(25,10,9,0,NULL,NULL,'2026-01-17 00:01:00'),(26,10,11,0,NULL,NULL,'2026-01-17 00:01:00'),(27,11,10,0,NULL,NULL,'2026-01-17 00:05:49'),(28,11,9,0,NULL,NULL,'2026-01-17 00:05:49'),(29,11,11,0,NULL,NULL,'2026-01-17 00:05:49'),(30,12,14,0,NULL,NULL,'2026-02-12 16:21:35'),(31,13,14,0,NULL,NULL,'2026-02-12 16:39:23'),(32,13,4,0,NULL,NULL,'2026-02-12 16:39:23'),(33,13,13,0,NULL,NULL,'2026-02-12 16:39:23'),(34,14,14,0,NULL,NULL,'2026-02-12 16:41:45'),(35,14,13,0,NULL,NULL,'2026-02-12 16:41:45'),(36,14,7,0,NULL,NULL,'2026-02-12 16:41:45'),(37,17,14,0,NULL,NULL,'2026-02-12 21:17:31'),(38,17,4,0,NULL,NULL,'2026-02-12 21:17:31'),(39,17,13,0,NULL,NULL,'2026-02-12 21:17:31'),(40,18,14,0,NULL,NULL,'2026-02-12 21:17:32'),(41,18,4,0,NULL,NULL,'2026-02-12 21:17:32'),(42,18,13,0,NULL,NULL,'2026-02-12 21:17:32');
/*!40000 ALTER TABLE `parte_diario_trabajadores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `partes_diarios`
--

DROP TABLE IF EXISTS `partes_diarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `partes_diarios` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `obra_id` bigint(20) unsigned NOT NULL,
  `fecha` date NOT NULL,
  `tipo` enum('diario','mensual') NOT NULL DEFAULT 'diario',
  `fecha_fin` date DEFAULT NULL,
  `jornada` varchar(255) DEFAULT NULL,
  `linea` varchar(100) DEFAULT NULL,
  `trayecto` varchar(255) DEFAULT NULL,
  `gerencia_jefatura` varchar(50) DEFAULT NULL,
  `distrito` varchar(100) DEFAULT NULL,
  `brigada` varchar(100) DEFAULT NULL COMMENT 'MANZER, subcontrata, etc.',
  `desbroce_total_m2` decimal(12,2) NOT NULL DEFAULT 0.00,
  `desbroce_p5_m2` decimal(12,2) NOT NULL DEFAULT 0.00,
  `desbroce_p6_m2` decimal(12,2) NOT NULL DEFAULT 0.00,
  `limpieza_p8_m2` decimal(12,2) NOT NULL DEFAULT 0.00,
  `herbicida_p4_m2` decimal(12,2) NOT NULL DEFAULT 0.00,
  `talas_unidades` int(11) NOT NULL DEFAULT 0,
  `podas_unidades` int(11) NOT NULL DEFAULT 0,
  `observaciones` text DEFAULT NULL,
  `incidencias` text DEFAULT NULL,
  `importe_total_calculado` decimal(14,2) NOT NULL DEFAULT 0.00,
  `encargado_firma` varchar(255) DEFAULT NULL,
  `encargado_nombre` varchar(150) DEFAULT NULL,
  `cliente_firma` varchar(255) DEFAULT NULL COMMENT 'ADIF u otro',
  `cliente_nombre` varchar(150) DEFAULT NULL,
  `estado` enum('borrador','completado','validado') NOT NULL DEFAULT 'borrador',
  `creado_por` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `partes_diarios_creado_por_foreign` (`creado_por`),
  KEY `idx_partes_obra_id` (`obra_id`),
  KEY `idx_partes_obra_tipo_fechas` (`obra_id`,`tipo`,`fecha`,`fecha_fin`),
  CONSTRAINT `partes_diarios_creado_por_foreign` FOREIGN KEY (`creado_por`) REFERENCES `users` (`id`),
  CONSTRAINT `partes_diarios_obra_id_foreign` FOREIGN KEY (`obra_id`) REFERENCES `obras` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `partes_diarios`
--

LOCK TABLES `partes_diarios` WRITE;
/*!40000 ALTER TABLE `partes_diarios` DISABLE KEYS */;
INSERT INTO `partes_diarios` VALUES (1,4,'2025-12-27','diario',NULL,'diurna','das','calaf','BCN',NULL,'MANZER',12.00,12.00,12.00,12.00,12.00,2,2,'ninguna','ninguna',0.00,NULL,NULL,NULL,NULL,'validado',1,'2025-12-27 14:20:01','2025-12-27 14:21:06'),(2,4,'2026-01-10','diario',NULL,'diurna','das','calaf','BCN',NULL,'MANZER',25.00,15.00,56.00,0.00,0.00,0,0,'observacion','incidencia',0.00,NULL,NULL,NULL,NULL,'validado',1,'2026-01-10 23:17:10','2026-01-10 23:17:17'),(3,4,'2026-01-13','diario',NULL,'diurna','das','calaf','BCN',NULL,'MANZER',0.00,0.00,0.00,0.00,0.00,0,0,NULL,NULL,800.00,NULL,NULL,NULL,NULL,'validado',1,'2026-01-13 15:53:22','2026-01-13 17:20:05'),(4,4,'2026-01-14','diario',NULL,'diurna','l220','calaf',NULL,NULL,'MANZER',0.00,0.00,0.00,0.00,0.00,0,0,NULL,NULL,1015.04,NULL,NULL,NULL,NULL,'validado',1,'2026-01-13 17:31:22','2026-01-13 17:31:37'),(5,4,'2026-01-15','diario',NULL,'diurna','l220','calaf',NULL,NULL,'MANZER',0.00,0.00,0.00,0.00,0.00,0,0,NULL,NULL,1015.04,NULL,NULL,NULL,NULL,'borrador',1,'2026-01-13 17:32:03','2026-01-13 17:32:03'),(6,4,'2026-01-30','diario',NULL,'diurna','l220','calaf',NULL,NULL,'MANZER',0.00,0.00,0.00,0.00,0.00,0,0,NULL,NULL,1015.04,NULL,NULL,NULL,NULL,'borrador',1,'2026-01-13 17:32:37','2026-01-13 17:32:37'),(7,4,'2026-01-16','diario',NULL,'diurna','l220','calaf',NULL,NULL,'MANZER',0.00,0.00,0.00,0.00,0.00,0,0,NULL,NULL,1015.04,NULL,NULL,NULL,NULL,'borrador',1,'2026-01-16 23:51:04','2026-01-16 23:51:04'),(8,4,'2026-01-17','diario',NULL,'diurna','l220','calaf',NULL,NULL,'MANZER',0.00,0.00,0.00,0.00,0.00,0,0,NULL,NULL,1015.04,NULL,NULL,NULL,NULL,'borrador',1,'2026-01-16 23:51:29','2026-01-16 23:51:29'),(9,4,'2026-01-21','diario',NULL,'diurna','l220','calaf',NULL,NULL,'MANZER',0.00,0.00,0.00,0.00,0.00,0,0,NULL,NULL,1015.04,NULL,NULL,NULL,NULL,'borrador',1,'2026-01-16 23:53:00','2026-01-16 23:53:00'),(10,4,'2026-01-14','diario',NULL,'diurna','l220','calaf',NULL,NULL,'MANZER',0.00,0.00,0.00,0.00,0.00,0,0,NULL,NULL,1015.04,NULL,NULL,NULL,NULL,'borrador',1,'2026-01-17 00:01:00','2026-01-17 00:01:00'),(11,4,'2026-01-21','diario',NULL,'diurna','l220','calaf',NULL,NULL,'MANZER',0.00,0.00,0.00,0.00,0.00,0,0,NULL,NULL,1015.04,NULL,NULL,NULL,NULL,'borrador',1,'2026-01-17 00:05:49','2026-01-17 00:05:49'),(12,4,'2026-02-12','diario',NULL,'diurna','das','calaf','asdsa',NULL,'MANZER',0.00,0.00,0.00,0.00,0.00,0,0,NULL,NULL,200.12,NULL,NULL,NULL,NULL,'validado',1,'2026-02-12 16:21:35','2026-02-12 16:21:40'),(13,4,'2026-02-11','diario',NULL,'diurna','das','calaf',NULL,NULL,'MANZER',0.00,0.00,0.00,0.00,0.00,0,0,NULL,NULL,0.00,NULL,NULL,NULL,NULL,'borrador',1,'2026-02-12 16:39:23','2026-02-12 16:39:23'),(14,4,'2026-02-12','diario',NULL,'diurna','das','calaf',NULL,NULL,'MANZER',0.00,0.00,0.00,0.00,0.00,0,0,NULL,NULL,300.00,NULL,NULL,NULL,NULL,'borrador',1,'2026-02-12 16:41:45','2026-02-12 16:41:45'),(15,4,'2026-02-12','diario',NULL,'diurna','das','calaf',NULL,NULL,'MANZER',0.00,0.00,0.00,0.00,0.00,0,0,NULL,NULL,0.00,NULL,NULL,NULL,NULL,'borrador',1,'2026-02-12 16:55:57','2026-02-12 16:55:57'),(16,4,'2026-02-01','mensual','2026-02-28','diurna','das','calaf',NULL,NULL,'MANZER',0.00,0.00,0.00,0.00,0.00,0,0,NULL,NULL,20.00,NULL,NULL,NULL,NULL,'borrador',1,'2026-02-12 17:24:19','2026-02-12 17:24:19'),(17,4,'2026-02-01','mensual','2026-02-12','diurna','das','calaf','BCN',NULL,'MANZER',0.00,0.00,0.00,0.00,0.00,0,0,NULL,NULL,2300.00,NULL,NULL,NULL,NULL,'borrador',1,'2026-02-12 21:17:31','2026-02-12 21:17:31'),(18,4,'2026-02-01','mensual','2026-02-12','diurna','das','calaf','BCN',NULL,'MANZER',0.00,0.00,0.00,0.00,0.00,0,0,NULL,NULL,2300.00,NULL,NULL,NULL,NULL,'validado',1,'2026-02-12 21:17:32','2026-02-12 21:17:57');
/*!40000 ALTER TABLE `partes_diarios` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'ver_usuarios','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(2,'crear_usuarios','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(3,'editar_usuarios','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(4,'eliminar_usuarios','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(5,'ver_trabajadores','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(6,'crear_trabajadores','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(7,'editar_trabajadores','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(8,'eliminar_trabajadores','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(9,'ver_cuadrillas','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(10,'crear_cuadrillas','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(11,'editar_cuadrillas','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(12,'eliminar_cuadrillas','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(13,'ver_clientes','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(14,'crear_clientes','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(15,'editar_clientes','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(16,'eliminar_clientes','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(17,'ver_leads','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(18,'crear_leads','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(19,'editar_leads','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(20,'eliminar_leads','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(21,'ver_obras','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(22,'crear_obras','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(23,'editar_obras','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(24,'eliminar_obras','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(25,'ver_rentabilidad_obras','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(26,'ver_fichajes','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(27,'crear_fichajes','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(28,'editar_fichajes','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(29,'eliminar_fichajes','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(30,'validar_fichajes','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(31,'ver_partes','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(32,'crear_partes','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(33,'editar_partes','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(34,'eliminar_partes','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(35,'validar_partes','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(36,'ver_maquinaria','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(37,'crear_maquinaria','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(38,'editar_maquinaria','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(39,'eliminar_maquinaria','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(40,'ver_vehiculos','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(41,'crear_vehiculos','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(42,'editar_vehiculos','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(43,'eliminar_vehiculos','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(44,'ver_subcontratas','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(45,'crear_subcontratas','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(46,'editar_subcontratas','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(47,'eliminar_subcontratas','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(48,'ver_contratos','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(49,'crear_contratos','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(50,'editar_contratos','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(51,'eliminar_contratos','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(52,'ver_facturas','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(53,'crear_facturas','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(54,'editar_facturas','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(55,'eliminar_facturas','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(56,'ver_finanzas','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(57,'crear_finanzas','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(58,'editar_finanzas','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(59,'eliminar_finanzas','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(60,'ver_epis','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(61,'crear_epis','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(62,'editar_epis','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(63,'eliminar_epis','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(64,'ver_formaciones','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(65,'crear_formaciones','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(66,'editar_formaciones','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(67,'eliminar_formaciones','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(68,'ver_primas','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(69,'crear_primas','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(70,'editar_primas','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(71,'ver_alertas','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(72,'gestionar_alertas','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(73,'ver_dashboard_admin','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(74,'ver_dashboard_encargado','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(75,'ver_dashboard_trabajador','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(76,'ver_auditoria','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(77,'gestionar_configuracion','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(78,'subir_documentos_maquinaria','web','2026-03-02 20:32:38','2026-03-02 20:32:38'),(79,'ver_tableros','web','2026-03-02 20:32:38','2026-03-02 20:32:38'),(80,'crear_tableros','web','2026-03-02 20:32:38','2026-03-02 20:32:38'),(81,'editar_tableros','web','2026-03-02 20:32:38','2026-03-02 20:32:38'),(82,'eliminar_tableros','web','2026-03-02 20:32:38','2026-03-02 20:32:38'),(83,'ver_tarjetas','web','2026-03-02 20:32:38','2026-03-02 20:32:38'),(84,'crear_tarjetas','web','2026-03-02 20:32:38','2026-03-02 20:32:38'),(85,'editar_tarjetas','web','2026-03-02 20:32:38','2026-03-02 20:32:38'),(86,'eliminar_tarjetas','web','2026-03-02 20:32:38','2026-03-02 20:32:38'),(87,'comentar_tarjetas','web','2026-03-02 20:32:38','2026-03-02 20:32:38');
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
-- Table structure for table `prima_configuraciones`
--

DROP TABLE IF EXISTS `prima_configuraciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prima_configuraciones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `obra_tipo_id` bigint(20) unsigned DEFAULT NULL,
  `unidad_medida` enum('m2','unidades','hectareas') NOT NULL,
  `minimo_por_trabajador` decimal(10,2) NOT NULL COMMENT 'Ej: 2500 m²/trabajador',
  `tramo_prima` decimal(10,2) NOT NULL COMMENT 'Cada X unidades extra = prima',
  `importe_prima_por_trabajador` decimal(8,2) NOT NULL COMMENT '€ por trabajador por tramo',
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `prima_configuraciones_obra_tipo_id_foreign` (`obra_tipo_id`),
  CONSTRAINT `prima_configuraciones_obra_tipo_id_foreign` FOREIGN KEY (`obra_tipo_id`) REFERENCES `obra_tipos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prima_configuraciones`
--

LOCK TABLES `prima_configuraciones` WRITE;
/*!40000 ALTER TABLE `prima_configuraciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `prima_configuraciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `primas_trabajador`
--

DROP TABLE IF EXISTS `primas_trabajador`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `primas_trabajador` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `trabajador_id` bigint(20) unsigned NOT NULL,
  `obra_id` bigint(20) unsigned NOT NULL,
  `parte_diario_id` bigint(20) unsigned DEFAULT NULL,
  `prima_configuracion_id` bigint(20) unsigned NOT NULL,
  `fecha` date NOT NULL,
  `produccion_equipo` decimal(12,2) NOT NULL COMMENT 'Total producido por el equipo',
  `trabajadores_equipo` int(11) NOT NULL,
  `minimo_requerido` decimal(12,2) NOT NULL COMMENT 'minimo_por_trabajador * trabajadores_equipo',
  `excedente` decimal(12,2) NOT NULL COMMENT 'produccion_equipo - minimo_requerido',
  `tramos_conseguidos` int(11) NOT NULL COMMENT 'excedente / tramo_prima',
  `importe_prima` decimal(10,2) NOT NULL COMMENT 'tramos * importe_por_trabajador',
  `pagada` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_pago` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `primas_trabajador_trabajador_id_foreign` (`trabajador_id`),
  KEY `primas_trabajador_obra_id_foreign` (`obra_id`),
  KEY `primas_trabajador_parte_diario_id_foreign` (`parte_diario_id`),
  KEY `primas_trabajador_prima_configuracion_id_foreign` (`prima_configuracion_id`),
  CONSTRAINT `primas_trabajador_obra_id_foreign` FOREIGN KEY (`obra_id`) REFERENCES `obras` (`id`) ON DELETE CASCADE,
  CONSTRAINT `primas_trabajador_parte_diario_id_foreign` FOREIGN KEY (`parte_diario_id`) REFERENCES `partes_diarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `primas_trabajador_prima_configuracion_id_foreign` FOREIGN KEY (`prima_configuracion_id`) REFERENCES `prima_configuraciones` (`id`),
  CONSTRAINT `primas_trabajador_trabajador_id_foreign` FOREIGN KEY (`trabajador_id`) REFERENCES `trabajadores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `primas_trabajador`
--

LOCK TABLES `primas_trabajador` WRITE;
/*!40000 ALTER TABLE `primas_trabajador` DISABLE KEYS */;
/*!40000 ALTER TABLE `primas_trabajador` ENABLE KEYS */;
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
INSERT INTO `role_has_permissions` VALUES (1,1),(1,2),(1,4),(1,5),(2,1),(3,1),(4,1),(5,1),(5,2),(5,3),(5,4),(5,5),(6,1),(6,4),(7,1),(7,4),(8,1),(8,4),(9,1),(9,3),(9,4),(9,5),(10,1),(10,4),(11,1),(11,3),(11,4),(12,1),(12,4),(13,1),(13,2),(13,5),(14,1),(14,2),(15,1),(15,2),(16,1),(17,1),(17,2),(17,5),(18,1),(18,2),(19,1),(19,2),(20,1),(21,1),(21,2),(21,3),(21,5),(22,1),(23,1),(24,1),(25,1),(25,5),(26,1),(26,2),(26,3),(26,4),(26,5),(26,6),(27,1),(27,3),(27,6),(28,1),(28,3),(29,1),(30,1),(31,1),(31,2),(31,3),(31,4),(31,5),(32,1),(32,3),(33,1),(33,3),(34,1),(35,1),(36,1),(36,2),(36,3),(36,4),(36,5),(37,1),(38,1),(39,1),(40,1),(40,2),(40,3),(40,4),(40,5),(41,1),(42,1),(43,1),(44,1),(44,2),(44,4),(44,5),(45,1),(45,2),(46,1),(46,2),(47,1),(48,1),(48,2),(48,3),(48,4),(48,5),(49,1),(49,2),(50,1),(50,2),(51,1),(52,1),(52,2),(52,5),(53,1),(53,2),(54,1),(54,2),(55,1),(56,1),(56,2),(56,3),(56,5),(57,1),(57,2),(57,3),(58,1),(58,2),(58,3),(59,1),(59,3),(60,1),(60,2),(60,3),(60,4),(60,5),(60,6),(61,1),(61,3),(61,4),(62,1),(62,3),(62,4),(63,1),(63,3),(64,1),(64,2),(64,3),(64,4),(64,5),(64,6),(65,1),(65,4),(66,1),(66,4),(67,1),(68,1),(68,2),(68,5),(68,6),(69,1),(70,1),(71,1),(71,2),(71,3),(71,4),(71,5),(71,6),(72,1),(72,4),(73,1),(73,5),(74,1),(74,3),(75,1),(75,6),(76,1),(76,2),(76,4),(76,5),(77,1),(78,1),(78,3),(79,1),(79,2),(79,3),(79,4),(79,5),(79,6),(80,1),(80,2),(80,3),(80,4),(81,1),(81,2),(81,3),(81,4),(82,1),(83,1),(83,2),(83,3),(83,4),(83,5),(83,6),(84,1),(84,2),(84,3),(84,4),(85,1),(85,2),(85,3),(85,4),(86,1),(87,1),(87,2),(87,3),(87,4),(87,6);
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Administrador','web','2025-12-23 14:13:25','2025-12-23 14:13:25'),(2,'Contabilidad','web','2025-12-23 14:13:26','2025-12-23 14:13:26'),(3,'Encargado','web','2025-12-23 14:13:26','2025-12-23 14:13:26'),(4,'RRHH','web','2025-12-23 14:13:26','2025-12-23 14:13:26'),(5,'Auditor','web','2025-12-23 14:13:26','2025-12-23 14:13:26'),(6,'Trabajador','web','2025-12-23 14:13:26','2025-12-23 14:13:26');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subcontrata_documentos_cae`
--

DROP TABLE IF EXISTS `subcontrata_documentos_cae`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subcontrata_documentos_cae` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subcontrata_id` bigint(20) unsigned NOT NULL,
  `tipo` varchar(100) NOT NULL COMMENT 'TC1, TC2, Seguro RC, etc.',
  `nombre` varchar(255) NOT NULL,
  `archivo_path` varchar(500) NOT NULL,
  `fecha_documento` date DEFAULT NULL,
  `fecha_caducidad` date DEFAULT NULL,
  `verificado` tinyint(1) NOT NULL DEFAULT 0,
  `verificado_por` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subcontrata_documentos_cae_subcontrata_id_foreign` (`subcontrata_id`),
  KEY `subcontrata_documentos_cae_verificado_por_foreign` (`verificado_por`),
  CONSTRAINT `subcontrata_documentos_cae_subcontrata_id_foreign` FOREIGN KEY (`subcontrata_id`) REFERENCES `subcontratas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subcontrata_documentos_cae_verificado_por_foreign` FOREIGN KEY (`verificado_por`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subcontrata_documentos_cae`
--

LOCK TABLES `subcontrata_documentos_cae` WRITE;
/*!40000 ALTER TABLE `subcontrata_documentos_cae` DISABLE KEYS */;
/*!40000 ALTER TABLE `subcontrata_documentos_cae` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subcontrata_documentos_obra`
--

DROP TABLE IF EXISTS `subcontrata_documentos_obra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subcontrata_documentos_obra` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subcontrata_id` bigint(20) unsigned NOT NULL,
  `obra_id` bigint(20) unsigned NOT NULL,
  `tipo` varchar(100) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `archivo_path` varchar(500) NOT NULL,
  `fecha_documento` date DEFAULT NULL,
  `fecha_caducidad` date DEFAULT NULL,
  `obligatorio` tinyint(1) NOT NULL DEFAULT 1,
  `verificado` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `subcontrata_documentos_obra_subcontrata_id_foreign` (`subcontrata_id`),
  KEY `subcontrata_documentos_obra_obra_id_foreign` (`obra_id`),
  CONSTRAINT `subcontrata_documentos_obra_obra_id_foreign` FOREIGN KEY (`obra_id`) REFERENCES `obras` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subcontrata_documentos_obra_subcontrata_id_foreign` FOREIGN KEY (`subcontrata_id`) REFERENCES `subcontratas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subcontrata_documentos_obra`
--

LOCK TABLES `subcontrata_documentos_obra` WRITE;
/*!40000 ALTER TABLE `subcontrata_documentos_obra` DISABLE KEYS */;
/*!40000 ALTER TABLE `subcontrata_documentos_obra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subcontratas`
--

DROP TABLE IF EXISTS `subcontratas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subcontratas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `razon_social` varchar(255) DEFAULT NULL,
  `cif` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `persona_contacto` varchar(150) DEFAULT NULL,
  `tarifa_hora` decimal(8,2) DEFAULT NULL,
  `tarifa_dia` decimal(10,2) DEFAULT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  `homologada` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_homologacion` date DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subcontratas`
--

LOCK TABLES `subcontratas` WRITE;
/*!40000 ALTER TABLE `subcontratas` DISABLE KEYS */;
INSERT INTO `subcontratas` VALUES (1,'Trabajos Forestales del Pirineo','Trabajos Forestales del Pirineo SL','B87654321','Av. Pirineus, 23','973 350 000','info@tfpirineo.com','Carlos Roca',18.50,140.00,1,1,'2024-01-15',NULL,'2025-12-23 14:13:27','2025-12-23 14:13:27',NULL),(2,'Jardinería Integral BCN','Jardinería Integral BCN SL','B11223344','Carrer Indústria, 78','932 100 000','contacto@jardineriabcn.com','Miguel Torres',16.00,120.00,1,0,NULL,NULL,'2025-12-23 14:13:27','2026-01-22 15:23:01',NULL),(3,'Servicios Forestales Test SL','Servicios Forestales Test Sociedad Limitada','B99887766','Calle del Bosque, 123, Madrid','912345678','contacto@forestalestest.com','Juan García López',25.50,180.00,1,1,'2026-01-18','Subcontrata de prueba creada para testing del módulo','2026-01-18 23:32:07','2026-01-18 23:37:40','2026-01-18 23:37:40'),(4,'Subcontrata','Subcontrata','a123','Calle 69 #10-15\r\ncasa','3202230467','4324@gmail.com','Santiago',20.00,20.00,1,1,'2026-01-22','Nota','2026-01-22 15:15:53','2026-01-22 15:16:14',NULL);
/*!40000 ALTER TABLE `subcontratas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tablero_columnas`
--

DROP TABLE IF EXISTS `tablero_columnas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tablero_columnas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tablero_id` bigint(20) unsigned NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `posicion` int(10) unsigned NOT NULL DEFAULT 0,
  `archivada` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tablero_columnas_tablero_id_foreign` (`tablero_id`),
  CONSTRAINT `tablero_columnas_tablero_id_foreign` FOREIGN KEY (`tablero_id`) REFERENCES `tableros` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tablero_columnas`
--

LOCK TABLES `tablero_columnas` WRITE;
/*!40000 ALTER TABLE `tablero_columnas` DISABLE KEYS */;
INSERT INTO `tablero_columnas` VALUES (1,1,'Por hacer',0,0,'2026-03-02 20:39:47','2026-03-02 20:39:47'),(2,1,'En progreso',1,0,'2026-03-02 20:39:47','2026-03-02 20:39:47'),(3,1,'Completado',2,0,'2026-03-02 20:39:47','2026-03-02 20:39:47'),(5,2,'Por hacer',0,0,'2026-03-02 20:57:11','2026-03-02 20:57:11'),(6,2,'En progreso',1,0,'2026-03-02 20:57:11','2026-03-02 20:57:11'),(7,2,'Completado',2,0,'2026-03-02 20:57:11','2026-03-02 20:57:11'),(8,3,'Por hacer',0,0,'2026-03-02 20:59:32','2026-03-02 20:59:32'),(9,3,'En progreso',1,0,'2026-03-02 20:59:32','2026-03-02 20:59:32'),(10,3,'Completado',2,0,'2026-03-02 20:59:32','2026-03-02 20:59:32'),(11,4,'Por hacer',0,0,'2026-03-02 21:00:32','2026-03-02 21:00:32'),(12,4,'En progreso',1,0,'2026-03-02 21:00:32','2026-03-02 21:00:32'),(13,4,'Completado',2,0,'2026-03-02 21:00:32','2026-03-02 21:00:32'),(14,5,'Por hacer',0,0,'2026-03-02 21:01:18','2026-03-02 21:01:18'),(15,5,'En progreso',1,0,'2026-03-02 21:01:18','2026-03-02 21:01:18'),(16,5,'Completado',2,0,'2026-03-02 21:01:18','2026-03-02 21:01:18'),(17,6,'Por hacer',0,0,'2026-03-02 21:02:35','2026-03-02 21:02:35'),(18,6,'En progreso',1,0,'2026-03-02 21:02:35','2026-03-02 21:02:35'),(19,6,'Completado',2,0,'2026-03-02 21:02:35','2026-03-02 21:02:35'),(20,7,'Por hacer',0,0,'2026-03-02 21:31:03','2026-03-02 21:31:03'),(21,7,'En progreso',1,0,'2026-03-02 21:31:03','2026-03-02 21:31:03'),(22,7,'Completado',2,0,'2026-03-02 21:31:03','2026-03-02 21:31:03'),(23,6,'Test lista nueva',3,0,'2026-03-08 19:57:00','2026-03-08 19:57:00'),(24,6,'hoila',4,0,'2026-03-08 19:59:31','2026-03-08 19:59:31');
/*!40000 ALTER TABLE `tablero_columnas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tablero_etiquetas`
--

DROP TABLE IF EXISTS `tablero_etiquetas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tablero_etiquetas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tablero_id` bigint(20) unsigned NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `color` varchar(7) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tablero_etiquetas_tablero_id_foreign` (`tablero_id`),
  CONSTRAINT `tablero_etiquetas_tablero_id_foreign` FOREIGN KEY (`tablero_id`) REFERENCES `tableros` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tablero_etiquetas`
--

LOCK TABLES `tablero_etiquetas` WRITE;
/*!40000 ALTER TABLE `tablero_etiquetas` DISABLE KEYS */;
INSERT INTO `tablero_etiquetas` VALUES (1,1,'Urgente','#ef4444','2026-03-02 20:39:47','2026-03-02 20:39:47'),(2,1,'Importante','#f59e0b','2026-03-02 20:39:47','2026-03-02 20:39:47'),(3,1,'Normal','#3b82f6','2026-03-02 20:39:47','2026-03-02 20:39:47'),(4,1,'Baja','#6b7280','2026-03-02 20:39:47','2026-03-02 20:39:47'),(5,1,'Bug','#dc2626','2026-03-02 20:39:47','2026-03-02 20:39:47'),(6,1,'Mejora','#10b981','2026-03-02 20:39:47','2026-03-02 20:39:47'),(7,2,'Urgente','#ef4444','2026-03-02 20:57:11','2026-03-02 20:57:11'),(8,2,'Importante','#f59e0b','2026-03-02 20:57:11','2026-03-02 20:57:11'),(9,2,'Normal','#3b82f6','2026-03-02 20:57:11','2026-03-02 20:57:11'),(10,2,'Baja','#6b7280','2026-03-02 20:57:11','2026-03-02 20:57:11'),(11,2,'Bug','#dc2626','2026-03-02 20:57:11','2026-03-02 20:57:11'),(12,2,'Mejora','#10b981','2026-03-02 20:57:11','2026-03-02 20:57:11'),(13,3,'Urgente','#ef4444','2026-03-02 20:59:32','2026-03-02 20:59:32'),(14,3,'Importante','#f59e0b','2026-03-02 20:59:32','2026-03-02 20:59:32'),(15,3,'Normal','#3b82f6','2026-03-02 20:59:32','2026-03-02 20:59:32'),(16,3,'Baja','#6b7280','2026-03-02 20:59:32','2026-03-02 20:59:32'),(17,3,'Bug','#dc2626','2026-03-02 20:59:32','2026-03-02 20:59:32'),(18,3,'Mejora','#10b981','2026-03-02 20:59:32','2026-03-02 20:59:32'),(19,4,'Urgente','#ef4444','2026-03-02 21:00:32','2026-03-02 21:00:32'),(20,4,'Importante','#f59e0b','2026-03-02 21:00:32','2026-03-02 21:00:32'),(21,4,'Normal','#3b82f6','2026-03-02 21:00:32','2026-03-02 21:00:32'),(22,4,'Baja','#6b7280','2026-03-02 21:00:32','2026-03-02 21:00:32'),(23,4,'Bug','#dc2626','2026-03-02 21:00:32','2026-03-02 21:00:32'),(24,4,'Mejora','#10b981','2026-03-02 21:00:32','2026-03-02 21:00:32'),(25,5,'Urgente','#ef4444','2026-03-02 21:01:18','2026-03-02 21:01:18'),(26,5,'Importante','#f59e0b','2026-03-02 21:01:18','2026-03-02 21:01:18'),(27,5,'Normal','#3b82f6','2026-03-02 21:01:18','2026-03-02 21:01:18'),(28,5,'Baja','#6b7280','2026-03-02 21:01:18','2026-03-02 21:01:18'),(29,5,'Bug','#dc2626','2026-03-02 21:01:18','2026-03-02 21:01:18'),(30,5,'Mejora','#10b981','2026-03-02 21:01:18','2026-03-02 21:01:18'),(31,6,'Urgente','#ef4444','2026-03-02 21:02:35','2026-03-02 21:02:35'),(32,6,'Importante','#f59e0b','2026-03-02 21:02:35','2026-03-02 21:02:35'),(33,6,'Normal','#3b82f6','2026-03-02 21:02:35','2026-03-02 21:02:35'),(34,6,'Baja','#6b7280','2026-03-02 21:02:35','2026-03-02 21:02:35'),(35,6,'Bug','#dc2626','2026-03-02 21:02:35','2026-03-02 21:02:35'),(36,6,'Mejora','#10b981','2026-03-02 21:02:35','2026-03-02 21:02:35'),(37,7,'Urgente','#ef4444','2026-03-02 21:31:03','2026-03-02 21:31:03'),(38,7,'Importante','#f59e0b','2026-03-02 21:31:03','2026-03-02 21:31:03'),(39,7,'Normal','#3b82f6','2026-03-02 21:31:03','2026-03-02 21:31:03'),(40,7,'Baja','#6b7280','2026-03-02 21:31:03','2026-03-02 21:31:03'),(41,7,'Bug','#dc2626','2026-03-02 21:31:03','2026-03-02 21:31:03'),(42,7,'Mejora','#10b981','2026-03-02 21:31:03','2026-03-02 21:31:03');
/*!40000 ALTER TABLE `tablero_etiquetas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tablero_miembros`
--

DROP TABLE IF EXISTS `tablero_miembros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tablero_miembros` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tablero_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `rol` enum('propietario','editor','observador') NOT NULL DEFAULT 'editor',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tablero_miembros_tablero_id_user_id_unique` (`tablero_id`,`user_id`),
  KEY `tablero_miembros_user_id_foreign` (`user_id`),
  CONSTRAINT `tablero_miembros_tablero_id_foreign` FOREIGN KEY (`tablero_id`) REFERENCES `tableros` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tablero_miembros_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tablero_miembros`
--

LOCK TABLES `tablero_miembros` WRITE;
/*!40000 ALTER TABLE `tablero_miembros` DISABLE KEYS */;
INSERT INTO `tablero_miembros` VALUES (1,1,1,'propietario','2026-03-02 20:39:47','2026-03-02 20:39:47'),(2,2,1,'propietario','2026-03-02 20:57:11','2026-03-02 20:57:11'),(3,2,2,'editor','2026-03-02 20:57:44','2026-03-02 20:57:44'),(4,3,1,'propietario','2026-03-02 20:59:32','2026-03-02 20:59:32'),(5,4,1,'propietario','2026-03-02 21:00:32','2026-03-02 21:00:32'),(6,5,1,'propietario','2026-03-02 21:01:18','2026-03-02 21:01:18'),(7,6,1,'propietario','2026-03-02 21:02:35','2026-03-02 21:02:35'),(8,7,1,'propietario','2026-03-02 21:31:03','2026-03-02 21:31:03'),(9,7,3,'editor','2026-03-02 21:31:03','2026-03-02 21:31:03'),(10,7,9,'editor','2026-03-02 21:31:03','2026-03-02 21:31:03'),(11,7,2,'editor','2026-03-02 21:32:40','2026-03-02 21:32:40');
/*!40000 ALTER TABLE `tablero_miembros` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tableros`
--

DROP TABLE IF EXISTS `tableros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tableros` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `color_fondo` varchar(7) NOT NULL DEFAULT '#1e40af',
  `imagen_fondo` varchar(255) DEFAULT NULL,
  `visibilidad` enum('todos','roles','miembros') NOT NULL DEFAULT 'miembros',
  `roles_visibles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`roles_visibles`)),
  `archivado` tinyint(1) NOT NULL DEFAULT 0,
  `creado_por` bigint(20) unsigned DEFAULT NULL,
  `obra_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tableros_creado_por_foreign` (`creado_por`),
  KEY `tableros_obra_id_foreign` (`obra_id`),
  CONSTRAINT `tableros_creado_por_foreign` FOREIGN KEY (`creado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tableros_obra_id_foreign` FOREIGN KEY (`obra_id`) REFERENCES `obras` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tableros`
--

LOCK TABLES `tableros` WRITE;
/*!40000 ALTER TABLE `tableros` DISABLE KEYS */;
INSERT INTO `tableros` VALUES (1,'PEPITO PEREZ','des','#1e40af',NULL,'miembros',NULL,0,1,NULL,'2026-03-02 20:39:47','2026-03-02 20:39:47',NULL),(2,'Tablero de Testing','Tablero creado para validacion','#1e40af',NULL,'todos',NULL,0,1,NULL,'2026-03-02 20:57:11','2026-03-02 20:57:11',NULL),(3,'obra','sdas','#475569',NULL,'miembros',NULL,0,1,4,'2026-03-02 20:59:32','2026-03-02 20:59:32',NULL),(4,'uusdf','gdfgfd','#1e40af',NULL,'roles','[\"Administrador\",\"Encargado\",\"Trabajador\"]',0,1,1,'2026-03-02 21:00:32','2026-03-02 21:01:00',NULL),(5,'hjkkhj','khjk','#1e40af',NULL,'roles','[\"Contabilidad\",\"Encargado\",\"Trabajador\"]',0,1,4,'2026-03-02 21:01:18','2026-03-02 21:01:18',NULL),(6,'hjkkhj','khjk','#1e40af',NULL,'miembros','[\"Contabilidad\",\"Encargado\",\"Trabajador\"]',0,1,4,'2026-03-02 21:02:35','2026-03-02 21:02:35',NULL),(7,'Tablero Prueba Blanco',NULL,'#ffffff',NULL,'roles','[\"Administrador\"]',0,1,1,'2026-03-02 21:31:03','2026-03-02 21:31:03',NULL);
/*!40000 ALTER TABLE `tableros` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tarjeta_adjuntos`
--

DROP TABLE IF EXISTS `tarjeta_adjuntos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tarjeta_adjuntos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tarjeta_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `nombre_original` varchar(255) NOT NULL,
  `ruta_archivo` varchar(500) NOT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `tamano` bigint(20) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tarjeta_adjuntos_tarjeta_id_foreign` (`tarjeta_id`),
  KEY `tarjeta_adjuntos_user_id_foreign` (`user_id`),
  CONSTRAINT `tarjeta_adjuntos_tarjeta_id_foreign` FOREIGN KEY (`tarjeta_id`) REFERENCES `tarjetas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tarjeta_adjuntos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tarjeta_adjuntos`
--

LOCK TABLES `tarjeta_adjuntos` WRITE;
/*!40000 ALTER TABLE `tarjeta_adjuntos` DISABLE KEYS */;
INSERT INTO `tarjeta_adjuntos` VALUES (1,3,1,'Screenshot 2026-01-20 114412.png','tableros/6/3/1772999276_Screenshot 2026-01-20 114412.png','image/png',350251,'2026-03-08 19:47:56','2026-03-08 19:47:56');
/*!40000 ALTER TABLE `tarjeta_adjuntos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tarjeta_checklist_items`
--

DROP TABLE IF EXISTS `tarjeta_checklist_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tarjeta_checklist_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `checklist_id` bigint(20) unsigned NOT NULL,
  `texto` varchar(500) NOT NULL,
  `completado` tinyint(1) NOT NULL DEFAULT 0,
  `completado_por` bigint(20) unsigned DEFAULT NULL,
  `fecha_completado` datetime DEFAULT NULL,
  `posicion` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tarjeta_checklist_items_checklist_id_foreign` (`checklist_id`),
  KEY `tarjeta_checklist_items_completado_por_foreign` (`completado_por`),
  CONSTRAINT `tarjeta_checklist_items_checklist_id_foreign` FOREIGN KEY (`checklist_id`) REFERENCES `tarjeta_checklists` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tarjeta_checklist_items_completado_por_foreign` FOREIGN KEY (`completado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tarjeta_checklist_items`
--

LOCK TABLES `tarjeta_checklist_items` WRITE;
/*!40000 ALTER TABLE `tarjeta_checklist_items` DISABLE KEYS */;
INSERT INTO `tarjeta_checklist_items` VALUES (1,1,'Paso 1: Hacer algo',1,1,'2026-03-02 15:54:47',0,'2026-03-02 20:54:27','2026-03-02 20:54:47');
/*!40000 ALTER TABLE `tarjeta_checklist_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tarjeta_checklists`
--

DROP TABLE IF EXISTS `tarjeta_checklists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tarjeta_checklists` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tarjeta_id` bigint(20) unsigned NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `posicion` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tarjeta_checklists_tarjeta_id_foreign` (`tarjeta_id`),
  CONSTRAINT `tarjeta_checklists_tarjeta_id_foreign` FOREIGN KEY (`tarjeta_id`) REFERENCES `tarjetas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tarjeta_checklists`
--

LOCK TABLES `tarjeta_checklists` WRITE;
/*!40000 ALTER TABLE `tarjeta_checklists` DISABLE KEYS */;
INSERT INTO `tarjeta_checklists` VALUES (1,1,'Test checklist directo',0,'2026-03-02 20:54:02','2026-03-02 20:54:02'),(2,1,'Checklist de prueba real',1,'2026-03-02 21:13:59','2026-03-02 21:13:59'),(3,1,'Checklist via SweetAlert',2,'2026-03-02 21:16:50','2026-03-02 21:16:50');
/*!40000 ALTER TABLE `tarjeta_checklists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tarjeta_comentarios`
--

DROP TABLE IF EXISTS `tarjeta_comentarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tarjeta_comentarios` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tarjeta_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `contenido` text NOT NULL,
  `tipo` enum('comentario','actividad') NOT NULL DEFAULT 'comentario',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tarjeta_comentarios_tarjeta_id_foreign` (`tarjeta_id`),
  KEY `tarjeta_comentarios_user_id_foreign` (`user_id`),
  CONSTRAINT `tarjeta_comentarios_tarjeta_id_foreign` FOREIGN KEY (`tarjeta_id`) REFERENCES `tarjetas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tarjeta_comentarios_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tarjeta_comentarios`
--

LOCK TABLES `tarjeta_comentarios` WRITE;
/*!40000 ALTER TABLE `tarjeta_comentarios` DISABLE KEYS */;
INSERT INTO `tarjeta_comentarios` VALUES (1,1,1,'Este es un comentario de prueba','comentario','2026-03-02 20:55:00','2026-03-02 20:55:00',NULL),(2,1,1,'asignó a Administrador','actividad','2026-03-02 20:55:12','2026-03-02 20:55:12',NULL),(3,2,1,'movió esta tarjeta de En progreso a Completado','actividad','2026-03-02 21:18:00','2026-03-02 21:18:00',NULL);
/*!40000 ALTER TABLE `tarjeta_comentarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tarjeta_etiquetas`
--

DROP TABLE IF EXISTS `tarjeta_etiquetas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tarjeta_etiquetas` (
  `tarjeta_id` bigint(20) unsigned NOT NULL,
  `etiqueta_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`tarjeta_id`,`etiqueta_id`),
  KEY `tarjeta_etiquetas_etiqueta_id_foreign` (`etiqueta_id`),
  CONSTRAINT `tarjeta_etiquetas_etiqueta_id_foreign` FOREIGN KEY (`etiqueta_id`) REFERENCES `tablero_etiquetas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tarjeta_etiquetas_tarjeta_id_foreign` FOREIGN KEY (`tarjeta_id`) REFERENCES `tarjetas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tarjeta_etiquetas`
--

LOCK TABLES `tarjeta_etiquetas` WRITE;
/*!40000 ALTER TABLE `tarjeta_etiquetas` DISABLE KEYS */;
INSERT INTO `tarjeta_etiquetas` VALUES (1,1);
/*!40000 ALTER TABLE `tarjeta_etiquetas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tarjeta_usuarios`
--

DROP TABLE IF EXISTS `tarjeta_usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tarjeta_usuarios` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tarjeta_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tarjeta_usuarios_tarjeta_id_user_id_unique` (`tarjeta_id`,`user_id`),
  KEY `tarjeta_usuarios_user_id_foreign` (`user_id`),
  CONSTRAINT `tarjeta_usuarios_tarjeta_id_foreign` FOREIGN KEY (`tarjeta_id`) REFERENCES `tarjetas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tarjeta_usuarios_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tarjeta_usuarios`
--

LOCK TABLES `tarjeta_usuarios` WRITE;
/*!40000 ALTER TABLE `tarjeta_usuarios` DISABLE KEYS */;
INSERT INTO `tarjeta_usuarios` VALUES (1,1,1,'2026-03-02 20:55:12','2026-03-02 20:55:12');
/*!40000 ALTER TABLE `tarjeta_usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tarjetas`
--

DROP TABLE IF EXISTS `tarjetas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tarjetas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `columna_id` bigint(20) unsigned NOT NULL,
  `titulo` varchar(500) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `posicion` int(10) unsigned NOT NULL DEFAULT 0,
  `fecha_vencimiento` datetime DEFAULT NULL,
  `fecha_completada` datetime DEFAULT NULL,
  `prioridad` enum('alta','media','baja') NOT NULL DEFAULT 'media',
  `color_portada` varchar(7) DEFAULT NULL,
  `archivada` tinyint(1) NOT NULL DEFAULT 0,
  `creado_por` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tarjetas_columna_id_foreign` (`columna_id`),
  KEY `tarjetas_creado_por_foreign` (`creado_por`),
  CONSTRAINT `tarjetas_columna_id_foreign` FOREIGN KEY (`columna_id`) REFERENCES `tablero_columnas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tarjetas_creado_por_foreign` FOREIGN KEY (`creado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tarjetas`
--

LOCK TABLES `tarjetas` WRITE;
/*!40000 ALTER TABLE `tarjetas` DISABLE KEYS */;
INSERT INTO `tarjetas` VALUES (1,1,'Tarea de prueba 1',NULL,0,'2026-03-05 14:00:00',NULL,'media','#3b82f6',0,1,'2026-03-02 20:46:07','2026-03-02 21:18:29',NULL),(2,3,'Tarea en progreso',NULL,0,NULL,NULL,'media',NULL,0,1,'2026-03-02 20:46:22','2026-03-02 21:18:00',NULL),(3,17,'Hola',NULL,0,NULL,NULL,'media',NULL,0,1,'2026-03-08 19:47:36','2026-03-08 19:47:36',NULL);
/*!40000 ALTER TABLE `tarjetas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trabajador_bonos`
--

DROP TABLE IF EXISTS `trabajador_bonos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trabajador_bonos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `trabajador_id` bigint(20) unsigned NOT NULL,
  `obra_id` bigint(20) unsigned DEFAULT NULL,
  `tipo` enum('prima_produccion','bono_especial','plus_nocturnidad','horas','otro') DEFAULT NULL,
  `concepto` varchar(255) NOT NULL,
  `fecha` date NOT NULL,
  `importe` decimal(10,2) NOT NULL,
  `horas` decimal(5,2) DEFAULT NULL,
  `pagado` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_pago` date DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `registrado_por` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `trabajador_bonos_obra_id_foreign` (`obra_id`),
  KEY `trabajador_bonos_registrado_por_foreign` (`registrado_por`),
  KEY `idx_trabajador_pagado` (`trabajador_id`,`pagado`),
  KEY `idx_fecha` (`fecha`),
  CONSTRAINT `trabajador_bonos_obra_id_foreign` FOREIGN KEY (`obra_id`) REFERENCES `obras` (`id`) ON DELETE SET NULL,
  CONSTRAINT `trabajador_bonos_registrado_por_foreign` FOREIGN KEY (`registrado_por`) REFERENCES `users` (`id`),
  CONSTRAINT `trabajador_bonos_trabajador_id_foreign` FOREIGN KEY (`trabajador_id`) REFERENCES `trabajadores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trabajador_bonos`
--

LOCK TABLES `trabajador_bonos` WRITE;
/*!40000 ALTER TABLE `trabajador_bonos` DISABLE KEYS */;
INSERT INTO `trabajador_bonos` VALUES (1,11,4,'prima_produccion','Bueno','2026-01-13',20.00,NULL,1,'2026-01-13',NULL,1,'2026-01-13 16:04:30'),(2,11,4,'prima_produccion','Bueno','2026-01-22',200.00,NULL,1,'2026-01-22',NULL,1,'2026-01-22 15:51:23');
/*!40000 ALTER TABLE `trabajador_bonos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trabajador_documentos`
--

DROP TABLE IF EXISTS `trabajador_documentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trabajador_documentos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `trabajador_id` bigint(20) unsigned NOT NULL,
  `tipo` enum('contrato','nomina','dni','ss','certificado_formacion','apto_medico','otro') NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `archivo_path` varchar(500) NOT NULL,
  `fecha_documento` date DEFAULT NULL,
  `fecha_caducidad` date DEFAULT NULL,
  `visible_trabajador` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Si el trabajador puede verlo',
  `requiere_lectura` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Si requiere confirmación de lectura',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `trabajador_documentos_trabajador_id_foreign` (`trabajador_id`),
  CONSTRAINT `trabajador_documentos_trabajador_id_foreign` FOREIGN KEY (`trabajador_id`) REFERENCES `trabajadores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trabajador_documentos`
--

LOCK TABLES `trabajador_documentos` WRITE;
/*!40000 ALTER TABLE `trabajador_documentos` DISABLE KEYS */;
INSERT INTO `trabajador_documentos` VALUES (1,11,'contrato','Limones','uploads/trabajadores/11/documentos/1766506029_1.pdf','2025-12-23',NULL,1,0,'2025-12-23 16:07:09','2025-12-23 16:07:09'),(2,11,'contrato','PRueba','uploads/trabajadores/11/documentos/1766846144_Cultivo.pdf','2025-12-27',NULL,1,1,'2025-12-27 14:35:44','2025-12-27 14:35:44'),(3,1,'nomina','Limones','uploads/trabajadores/1/documentos/1768087057_1.pdf','2026-01-10',NULL,1,0,'2026-01-10 23:17:37','2026-01-10 23:17:37');
/*!40000 ALTER TABLE `trabajador_documentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trabajador_formaciones`
--

DROP TABLE IF EXISTS `trabajador_formaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trabajador_formaciones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `trabajador_id` bigint(20) unsigned NOT NULL,
  `formacion_tipo_id` bigint(20) unsigned NOT NULL,
  `fecha_realizacion` date NOT NULL,
  `fecha_caducidad` date DEFAULT NULL,
  `centro_formacion` varchar(255) DEFAULT NULL,
  `certificado_path` varchar(500) DEFAULT NULL COMMENT 'Solo visible para admin',
  `notas` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `trabajador_formaciones_trabajador_id_foreign` (`trabajador_id`),
  KEY `trabajador_formaciones_formacion_tipo_id_foreign` (`formacion_tipo_id`),
  CONSTRAINT `trabajador_formaciones_formacion_tipo_id_foreign` FOREIGN KEY (`formacion_tipo_id`) REFERENCES `formacion_tipos` (`id`),
  CONSTRAINT `trabajador_formaciones_trabajador_id_foreign` FOREIGN KEY (`trabajador_id`) REFERENCES `trabajadores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trabajador_formaciones`
--

LOCK TABLES `trabajador_formaciones` WRITE;
/*!40000 ALTER TABLE `trabajador_formaciones` DISABLE KEYS */;
INSERT INTO `trabajador_formaciones` VALUES (1,11,3,'2025-12-23','2026-01-01','moto','uploads/trabajadores/11/formaciones/1766506113_1.pdf',NULL,'2025-12-23 16:08:33','2025-12-23 16:08:33'),(2,1,8,'2023-05-15',NULL,'FOREM Andalucía',NULL,'Curso completado satisfactoriamente','2026-01-19 16:43:42','2026-01-19 16:43:42'),(3,11,26,'2026-01-22','2026-01-31','alturas.com','uploads/trabajadores/11/formaciones/1769096974_1.pdf','Notas','2026-01-22 15:49:34','2026-01-22 15:49:34');
/*!40000 ALTER TABLE `trabajador_formaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trabajador_historial_disciplinario`
--

DROP TABLE IF EXISTS `trabajador_historial_disciplinario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trabajador_historial_disciplinario` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `trabajador_id` bigint(20) unsigned NOT NULL,
  `fecha` date NOT NULL,
  `tipo` enum('amonestacion_verbal','amonestacion_escrita','sancion_leve','sancion_grave','sancion_muy_grave') NOT NULL,
  `descripcion` text NOT NULL,
  `documento_path` varchar(500) DEFAULT NULL,
  `registrado_por` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `trabajador_historial_disciplinario_trabajador_id_foreign` (`trabajador_id`),
  KEY `trabajador_historial_disciplinario_registrado_por_foreign` (`registrado_por`),
  CONSTRAINT `trabajador_historial_disciplinario_registrado_por_foreign` FOREIGN KEY (`registrado_por`) REFERENCES `users` (`id`),
  CONSTRAINT `trabajador_historial_disciplinario_trabajador_id_foreign` FOREIGN KEY (`trabajador_id`) REFERENCES `trabajadores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trabajador_historial_disciplinario`
--

LOCK TABLES `trabajador_historial_disciplinario` WRITE;
/*!40000 ALTER TABLE `trabajador_historial_disciplinario` DISABLE KEYS */;
INSERT INTO `trabajador_historial_disciplinario` VALUES (1,11,'2025-12-23','amonestacion_verbal','malo','uploads/trabajadores/11/historial/1766506137_1.pdf',1,'2025-12-23 16:08:57'),(2,11,'2025-12-23','amonestacion_verbal','mallo','uploads/trabajadores/11/historial/1766506157_1.pdf',1,'2025-12-23 16:09:17'),(3,11,'2025-12-23','amonestacion_verbal','mall','uploads/trabajadores/11/historial/1766512091_1.pdf',1,'2025-12-23 17:48:11'),(4,11,'2025-12-23','amonestacion_escrita','ghfgh','uploads/trabajadores/11/historial/1766512464_1.pdf',1,'2025-12-23 17:54:24');
/*!40000 ALTER TABLE `trabajador_historial_disciplinario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trabajadores`
--

DROP TABLE IF EXISTS `trabajadores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trabajadores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `tipo_relacion` enum('propio','subcontrata') NOT NULL DEFAULT 'propio',
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(150) NOT NULL,
  `dni` varchar(20) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `iban` varchar(34) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `fecha_alta` date NOT NULL,
  `fecha_baja` date DEFAULT NULL,
  `categoria_convenio` varchar(100) DEFAULT NULL,
  `salario_bruto_mensual` decimal(10,2) DEFAULT NULL,
  `coste_empresa_dia` decimal(10,2) DEFAULT NULL COMMENT 'Calculado: salario + SS + indirectos',
  `coste_hora` decimal(8,2) DEFAULT NULL,
  `vacaciones_anuales` int(11) NOT NULL DEFAULT 22 COMMENT 'Días de vacaciones al año',
  `vacaciones_acumuladas` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Días acumulados pendientes',
  `antiguedad` date DEFAULT NULL COMMENT 'Fecha inicio antigüedad',
  `subcontrata_id` bigint(20) unsigned DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `trabajadores_dni_unique` (`dni`),
  KEY `trabajadores_user_id_foreign` (`user_id`),
  KEY `trabajadores_subcontrata_id_foreign` (`subcontrata_id`),
  CONSTRAINT `trabajadores_subcontrata_id_foreign` FOREIGN KEY (`subcontrata_id`) REFERENCES `subcontratas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `trabajadores_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trabajadores`
--

LOCK TABLES `trabajadores` WRITE;
/*!40000 ALTER TABLE `trabajadores` DISABLE KEYS */;
INSERT INTO `trabajadores` VALUES (1,5,'propio','Antonio','García López','12345678A','antonio.garcía@manzer.com','600000001',NULL,NULL,NULL,'2024-06-23',NULL,'Oficial 1ª',2200.00,135.00,18.50,22,0.00,NULL,NULL,1,'2025-12-23 14:13:27','2025-12-23 14:13:27',NULL),(2,6,'propio','Francisco','Martínez Ruiz','23456789B','francisco.martínez@manzer.com','600000002',NULL,NULL,NULL,'2023-02-23',NULL,'Oficial 1ª',2200.00,135.00,18.50,22,0.00,NULL,NULL,1,'2025-12-23 14:13:27','2025-12-23 14:13:27',NULL),(3,7,'propio','José','Rodríguez Fernández','34567890C','josé.rodríguez@manzer.com','600000003',NULL,NULL,NULL,'2023-02-23',NULL,'Oficial 2ª',1900.00,116.59,16.00,22,0.00,NULL,NULL,1,'2025-12-23 14:13:27','2025-12-23 14:13:27',NULL),(4,NULL,'propio','Manuel','López García','45678901D','manuel.lópez@manzer.com','600000004',NULL,NULL,NULL,'2024-10-23',NULL,'Oficial 2ª',1900.00,116.59,16.00,22,0.00,NULL,NULL,1,'2025-12-23 14:13:27','2025-12-23 14:13:27',NULL),(5,NULL,'propio','David','Hernández Martín','56789012E','david.hernández@manzer.com','600000005',NULL,NULL,NULL,'2024-10-23',NULL,'Peón',1600.00,98.18,14.00,22,0.00,NULL,NULL,1,'2025-12-23 14:13:27','2025-12-23 14:13:27',NULL),(6,NULL,'propio','Javier','Sánchez Pérez','67890123F','javier.sánchez@manzer.com','600000006',NULL,NULL,NULL,'2025-06-23',NULL,'Peón',1600.00,98.18,14.00,22,0.00,NULL,NULL,1,'2025-12-23 14:13:27','2025-12-23 14:13:27',NULL),(7,NULL,'propio','Carlos','Gómez Sánchez','78901234G','carlos.gómez@manzer.com','600000007',NULL,NULL,NULL,'2022-12-23',NULL,'Oficial 1ª',2200.00,135.00,18.50,22,0.00,NULL,NULL,1,'2025-12-23 14:13:27','2025-12-23 14:13:27',NULL),(8,NULL,'propio','Miguel','Díaz González','89012345H','miguel.díaz@manzer.com','600000008',NULL,NULL,NULL,'2023-01-23',NULL,'Peón',1600.00,98.18,14.00,22,0.00,NULL,NULL,1,'2025-12-23 14:13:27','2025-12-23 14:13:27',NULL),(9,NULL,'propio','Rafael','Muñoz Álvarez','90123456I','rafael.muñoz@manzer.com','600000009',NULL,NULL,NULL,'2025-03-23',NULL,'Oficial 2ª',1900.00,116.59,16.00,22,0.00,NULL,NULL,1,'2025-12-23 14:13:27','2025-12-23 14:13:27',NULL),(10,NULL,'propio','Pedro','Romero Jiménez','01234567J','pedro.romero@manzer.com','600000010',NULL,NULL,NULL,'2023-09-23',NULL,'Peón',1600.00,98.18,14.00,22,0.00,NULL,NULL,1,'2025-12-23 14:13:27','2025-12-23 14:13:27',NULL),(11,8,'propio','Santi','Bellaizan','12312312321','vblogsanti@gmail.com','3202230467','Calle 69 #10-15','ES6621000418401234567891','1999-06-25','2025-12-23',NULL,'Peón Forestal',20000.00,234324.00,343.00,22,0.00,'2025-12-23',NULL,1,'2025-12-23 15:28:14','2026-03-02 21:58:16',NULL),(12,NULL,'subcontrata','traba sub','traba sub','23423423','trabasub@gmail.com','320223990','Calle 69 #10-15',NULL,'2026-01-22','2026-01-22',NULL,'Peon',NULL,NULL,NULL,22,0.00,'2026-01-22',4,1,'2026-01-22 15:19:31','2026-01-22 15:19:31',NULL),(13,NULL,'propio','sebas','sebas','34234532','anonyb001@hotmail.com','3202230462','Calle 69 #10-15',NULL,NULL,'2026-02-04',NULL,'Peon',51.00,654.00,654.00,22,0.00,'2026-02-04',NULL,1,'2026-02-04 18:54:54','2026-02-04 18:54:54',NULL),(14,10,'subcontrata','aaaa','aaaa','111111','santiagobc001@outlook.es','235432','Calle 69 #10-15',NULL,'2003-06-10','2026-02-04',NULL,'Peon',54.00,984.00,85.00,22,0.00,'2026-02-04',4,1,'2026-02-04 19:14:18','2026-02-04 19:16:56',NULL);
/*!40000 ALTER TABLE `trabajadores` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrador','admin@manzer.com',NULL,NULL,'$2y$10$Fqk9fWtrAKX58ClCB2LH/OGakm8WKFqOrpdGHYFNa7PL0RpyZ99y2',NULL,'2025-12-23 14:13:26','2026-01-22 15:56:49'),(2,'María García (Contabilidad)','contabilidad@manzer.com',NULL,NULL,'$2y$10$Fqk9fWtrAKX58ClCB2LH/OGakm8WKFqOrpdGHYFNa7PL0RpyZ99y2',NULL,'2025-12-23 14:13:26','2026-01-22 15:56:49'),(3,'Juan Martínez (Encargado)','encargado@manzer.com',NULL,NULL,'$2y$10$Fqk9fWtrAKX58ClCB2LH/OGakm8WKFqOrpdGHYFNa7PL0RpyZ99y2',NULL,'2025-12-23 14:13:26','2026-01-22 15:56:49'),(4,'Ana López (RRHH)','rrhh@manzer.com',NULL,NULL,'$2y$10$Fqk9fWtrAKX58ClCB2LH/OGakm8WKFqOrpdGHYFNa7PL0RpyZ99y2',NULL,'2025-12-23 14:13:27','2026-01-22 15:56:49'),(5,'Antonio García López','antonio@manzer.com',NULL,NULL,'$2y$10$Fqk9fWtrAKX58ClCB2LH/OGakm8WKFqOrpdGHYFNa7PL0RpyZ99y2',NULL,'2025-12-23 14:13:27','2026-01-22 15:56:49'),(6,'Francisco Martínez Ruiz','francisco.martínez@manzer.com',NULL,NULL,'$2y$10$Fqk9fWtrAKX58ClCB2LH/OGakm8WKFqOrpdGHYFNa7PL0RpyZ99y2',NULL,'2025-12-23 14:13:27','2026-01-22 15:56:49'),(7,'José Rodríguez Fernández','josé.rodríguez@manzer.com',NULL,NULL,'$2y$10$Fqk9fWtrAKX58ClCB2LH/OGakm8WKFqOrpdGHYFNa7PL0RpyZ99y2',NULL,'2025-12-23 14:13:27','2026-01-22 15:56:49'),(8,'Santi Bellaizan','vblogsanti@gmail.com',NULL,NULL,'$2y$10$Fqk9fWtrAKX58ClCB2LH/OGakm8WKFqOrpdGHYFNa7PL0RpyZ99y2',NULL,'2026-01-13 16:31:01','2026-01-22 15:56:49'),(9,'Santiago Admin','santiagobc456001@outlook.es',NULL,NULL,'$2y$10$Fqk9fWtrAKX58ClCB2LH/OGakm8WKFqOrpdGHYFNa7PL0RpyZ99y2',NULL,'2026-01-19 17:14:02','2026-02-04 19:13:34'),(10,'aaaa aaaa','santiagobc001@outlook.es',NULL,NULL,'$2y$10$BgJMAZ6Y7H9T8iH5eJLk7.hoTnZWv1p8IDBrsyZWDIIgMZp10HjGG',NULL,'2026-02-04 19:14:18','2026-02-04 19:14:18');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehiculo_documentos`
--

DROP TABLE IF EXISTS `vehiculo_documentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vehiculo_documentos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `vehiculo_id` bigint(20) unsigned NOT NULL,
  `tipo` enum('ficha_tecnica','permiso_circulacion','seguro','itv','otro') NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `archivo_path` varchar(500) NOT NULL,
  `fecha_documento` date DEFAULT NULL,
  `fecha_caducidad` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `vehiculo_documentos_vehiculo_id_foreign` (`vehiculo_id`),
  CONSTRAINT `vehiculo_documentos_vehiculo_id_foreign` FOREIGN KEY (`vehiculo_id`) REFERENCES `vehiculos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehiculo_documentos`
--

LOCK TABLES `vehiculo_documentos` WRITE;
/*!40000 ALTER TABLE `vehiculo_documentos` DISABLE KEYS */;
/*!40000 ALTER TABLE `vehiculo_documentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehiculo_tipos`
--

DROP TABLE IF EXISTS `vehiculo_tipos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vehiculo_tipos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL COMMENT 'Furgoneta, Camión, Tractor, etc.',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehiculo_tipos`
--

LOCK TABLES `vehiculo_tipos` WRITE;
/*!40000 ALTER TABLE `vehiculo_tipos` DISABLE KEYS */;
INSERT INTO `vehiculo_tipos` VALUES (1,'Furgoneta','2025-12-23 14:13:27','2025-12-23 14:13:27'),(2,'Camión','2025-12-23 14:13:27','2025-12-23 14:13:27'),(3,'Tractor','2025-12-23 14:13:27','2025-12-23 14:13:27'),(4,'Todoterreno','2025-12-23 14:13:27','2025-12-23 14:13:27'),(5,'Remolque','2025-12-23 14:13:27','2025-12-23 14:13:27');
/*!40000 ALTER TABLE `vehiculo_tipos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehiculos`
--

DROP TABLE IF EXISTS `vehiculos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vehiculos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `vehiculo_tipo_id` bigint(20) unsigned NOT NULL,
  `matricula` varchar(20) NOT NULL,
  `marca` varchar(100) DEFAULT NULL,
  `modelo` varchar(100) DEFAULT NULL,
  `numero_bastidor` varchar(100) DEFAULT NULL,
  `fecha_matriculacion` date DEFAULT NULL,
  `fecha_compra` date DEFAULT NULL,
  `fecha_ultima_itv` date DEFAULT NULL,
  `fecha_proxima_itv` date DEFAULT NULL,
  `compania_seguro` varchar(150) DEFAULT NULL,
  `numero_poliza` varchar(100) DEFAULT NULL,
  `fecha_vencimiento_seguro` date DEFAULT NULL,
  `coste_adquisicion` decimal(12,2) DEFAULT NULL,
  `coste_dia` decimal(8,2) DEFAULT NULL,
  `estado` enum('operativo','en_taller','baja') NOT NULL DEFAULT 'operativo',
  `kilometraje_actual` int(11) DEFAULT NULL,
  `conductor_habitual_id` bigint(20) unsigned DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehiculos_matricula_unique` (`matricula`),
  KEY `vehiculos_vehiculo_tipo_id_foreign` (`vehiculo_tipo_id`),
  KEY `vehiculos_conductor_habitual_id_foreign` (`conductor_habitual_id`),
  CONSTRAINT `vehiculos_conductor_habitual_id_foreign` FOREIGN KEY (`conductor_habitual_id`) REFERENCES `trabajadores` (`id`) ON DELETE SET NULL,
  CONSTRAINT `vehiculos_vehiculo_tipo_id_foreign` FOREIGN KEY (`vehiculo_tipo_id`) REFERENCES `vehiculo_tipos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehiculos`
--

LOCK TABLES `vehiculos` WRITE;
/*!40000 ALTER TABLE `vehiculos` DISABLE KEYS */;
INSERT INTO `vehiculos` VALUES (1,1,'1234 ABC','Ford','Transit','WF0XXXGCDX1234567','2023-01-15','2023-01-10','2025-06-01','2026-02-01','Mapfre','POL-2023-12345','2026-01-25',25000.00,50.00,'en_taller',48000,1,NULL,'2026-01-18 22:23:19','2026-01-18 22:28:33','2026-01-18 22:28:33'),(2,2,'1233AVB','Ford','Fiesta','123','2026-01-22','2026-01-22','2026-01-23','2026-01-30','Mapfre','1345641','2026-01-30',2000.00,20.00,'operativo',2000,11,'Nota','2026-01-22 15:14:30','2026-01-22 15:14:30',NULL);
/*!40000 ALTER TABLE `vehiculos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'manzer'
--

--
-- Dumping routines for database 'manzer'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-11 15:10:28
