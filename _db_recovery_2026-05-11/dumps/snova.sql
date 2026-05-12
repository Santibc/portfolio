-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: snova
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
-- Current Database: `snova`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `snova` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `snova`;

--
-- Table structure for table `actualizaciones_precios`
--

DROP TABLE IF EXISTS `actualizaciones_precios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `actualizaciones_precios` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` bigint(20) unsigned NOT NULL,
  `estado` enum('procesando','completado','error') NOT NULL DEFAULT 'procesando',
  `nombre_archivo` varchar(255) NOT NULL,
  `ruta_archivo` varchar(255) NOT NULL,
  `total_filas` int(11) NOT NULL,
  `actualizaciones_exitosas` int(11) NOT NULL,
  `actualizaciones_fallidas` int(11) NOT NULL,
  `errores` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`errores`)),
  `detalles_procesados` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `actualizaciones_precios_usuario_id_created_at_index` (`usuario_id`,`created_at`),
  CONSTRAINT `actualizaciones_precios_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `actualizaciones_precios`
--

LOCK TABLES `actualizaciones_precios` WRITE;
/*!40000 ALTER TABLE `actualizaciones_precios` DISABLE KEYS */;
/*!40000 ALTER TABLE `actualizaciones_precios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `calificaciones_productos`
--

DROP TABLE IF EXISTS `calificaciones_productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calificaciones_productos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `producto_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `compra_id` bigint(20) unsigned DEFAULT NULL,
  `item_compra_id` bigint(20) unsigned DEFAULT NULL,
  `nombre_visitante` varchar(100) DEFAULT NULL,
  `estrellas` tinyint(3) unsigned NOT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `comentario` text DEFAULT NULL,
  `imagen` varchar(500) DEFAULT NULL,
  `verificada` tinyint(1) NOT NULL DEFAULT 1,
  `aprobada` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `calificaciones_productos_producto_id_foreign` (`producto_id`),
  KEY `calificaciones_productos_compra_id_foreign` (`compra_id`),
  KEY `calificaciones_productos_item_compra_id_foreign` (`item_compra_id`),
  KEY `calificaciones_productos_parent_id_index` (`parent_id`),
  CONSTRAINT `calificaciones_productos_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `calificaciones_productos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `calificaciones_productos_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calificaciones_productos`
--

LOCK TABLES `calificaciones_productos` WRITE;
/*!40000 ALTER TABLE `calificaciones_productos` DISABLE KEYS */;
INSERT INTO `calificaciones_productos` VALUES (1,NULL,38,14,16,16,NULL,4,'El mejor','Lo mejor',NULL,1,1,'2025-12-11 19:39:22','2025-12-11 19:39:22'),(2,NULL,43,NULL,NULL,NULL,'Limones',4,'El mejor','Muy bueno',NULL,0,1,'2026-01-06 04:28:41','2026-01-06 04:29:57'),(6,NULL,43,NULL,NULL,NULL,'Santi',5,'El mejor','MEjor',NULL,0,0,'2026-01-06 04:37:29','2026-01-06 04:37:29'),(7,NULL,43,NULL,NULL,NULL,'dasd',3,'das','dasd',NULL,0,0,'2026-01-06 04:38:02','2026-01-06 04:38:02'),(8,NULL,43,NULL,NULL,NULL,'Santi',4,'El mejor',NULL,NULL,0,1,'2026-01-06 04:39:35','2026-01-06 04:39:48'),(9,NULL,43,1,NULL,NULL,'Santi',5,'El mejor',NULL,NULL,0,1,'2026-01-06 04:45:35','2026-01-06 04:45:35'),(10,9,43,1,NULL,NULL,'Santi',0,NULL,'Res',NULL,0,1,'2026-01-06 05:13:45','2026-01-06 05:13:45');
/*!40000 ALTER TABLE `calificaciones_productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `caracteristicas_productos`
--

DROP TABLE IF EXISTS `caracteristicas_productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `caracteristicas_productos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `producto_id` bigint(20) unsigned NOT NULL,
  `icono` varchar(50) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  `orden` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `caracteristicas_productos_producto_id_orden_index` (`producto_id`,`orden`),
  CONSTRAINT `caracteristicas_productos_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caracteristicas_productos`
--

LOCK TABLES `caracteristicas_productos` WRITE;
/*!40000 ALTER TABLE `caracteristicas_productos` DISABLE KEYS */;
INSERT INTO `caracteristicas_productos` VALUES (1,37,'bi-check-circle','Mejor','el mejor',1,'2025-12-19 23:52:39','2025-12-19 23:52:39'),(2,37,'bi-thermometer','caliente','mucho',2,'2025-12-19 23:52:39','2025-12-19 23:52:39');
/*!40000 ALTER TABLE `caracteristicas_productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carritos`
--

DROP TABLE IF EXISTS `carritos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carritos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` varchar(255) NOT NULL,
  `empresa_id` bigint(20) unsigned NOT NULL,
  `items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`items`)),
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `descuento_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `descuentos_aplicados` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`descuentos_aplicados`)),
  `codigo_descuento` varchar(255) DEFAULT NULL,
  `ultima_actividad` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `carritos_empresa_id_foreign` (`empresa_id`),
  KEY `carritos_session_id_empresa_id_index` (`session_id`,`empresa_id`),
  KEY `carritos_ultima_actividad_index` (`ultima_actividad`),
  CONSTRAINT `carritos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=202 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carritos`
--

LOCK TABLES `carritos` WRITE;
/*!40000 ALTER TABLE `carritos` DISABLE KEYS */;
INSERT INTO `carritos` VALUES (99,'4sISwylJP7jDnxgXtr9opEFGG0iPQupREvUnhoVt',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 07:35:25','2025-12-09 07:35:25','2025-12-09 07:35:25'),(100,'2aJ4APAldv29VnV1Ayk1Xcohz0sqUC459tn9ppwQ',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 07:42:48','2025-12-09 07:42:48','2025-12-09 07:42:48'),(101,'hVLeFFzSjTfQBkj5ws1VPZIXENutw78jEEfUwV9r',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 08:32:23','2025-12-09 08:32:23','2025-12-09 08:32:23'),(102,'QXo6yeQ7n2c64hgVk5EiXHXtVovqVHgyXf4Belxo',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 11:05:40','2025-12-09 11:05:40','2025-12-09 11:05:40'),(103,'DMHVKFEjHdCfJjxY3trhSJYDcWszgHwemTBU4LeO',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 11:30:29','2025-12-09 11:30:29','2025-12-09 11:30:29'),(104,'40qBJgDy86WveHs0SRWv1OxJeyTKQkeuwaeZVoNx',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 11:35:29','2025-12-09 11:35:29','2025-12-09 11:35:29'),(105,'jJ63Qr4KZ636IUlWuB2vVzRGK8j8TIz8CX2rETXf',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 12:15:24','2025-12-09 12:15:24','2025-12-09 12:15:24'),(106,'79t9OyPTmXxrpaguDSiHUXMyNKn9XbyF7qoZWdaG',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 13:06:43','2025-12-09 13:06:43','2025-12-09 13:06:43'),(107,'uH4wBKJzjGrzuWgd0uAzdYCtZXoOaEnuVRtwY7ms',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 13:06:44','2025-12-09 13:06:44','2025-12-09 13:06:44'),(108,'A3y4DBMXfVQ9YeFoZDjuZ54O5OGAnBbHHlYoBCb9',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 13:32:54','2025-12-09 13:32:54','2025-12-09 13:32:54'),(109,'TIWc6IbLAo9gvzzIRCpACkFGhsf1qhArFRAvHSI2',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 14:02:13','2025-12-09 14:02:13','2025-12-09 14:02:13'),(110,'lMll4BviUQVu2A4oXCGs1zTMUqWf6nSYerMm4ZnN',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 14:04:00','2025-12-09 14:04:00','2025-12-09 14:04:00'),(111,'iVTgAQl6emEykk2kU2VHzp7zRsSVjCzEx1UkqYog',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 15:25:00','2025-12-09 15:25:00','2025-12-09 15:25:00'),(112,'F7ov4csnsSUfYqngfLdS0viXWhBuv54m1mMoE1BO',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 16:41:29','2025-12-09 16:41:29','2025-12-09 16:41:29'),(113,'FJKRF83FZzDw8Q3Lyk4jvm4suILfM2MdjBNFMUYu',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 16:41:29','2025-12-09 16:41:29','2025-12-09 16:41:29'),(114,'mNO5sd3fxeaO5j8fsJG5CWrniUbLVX6rcOPDPTnd',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 16:41:41','2025-12-09 16:41:41','2025-12-09 16:41:41'),(115,'ysLeFk5bmy5C0L6LdoRmZ97qkmkRiv4UtFLIqphN',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 16:43:54','2025-12-09 16:43:54','2025-12-09 16:43:54'),(116,'zNGVnhshSYdVtaUXOg62XlO2dWeSgLkBld2yHAPF',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 16:54:27','2025-12-09 16:54:27','2025-12-09 16:54:27'),(117,'1wN4nju26IgogPGuVs3ppl0JrRqAYq2pPzrrlGEx',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 17:26:38','2025-12-09 17:26:38','2025-12-09 17:26:38'),(118,'lOYOCBoaXYgN2qI9HISBkcfqQzdZa5PC1whHWqpR',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 17:40:29','2025-12-09 17:40:29','2025-12-09 17:40:29'),(119,'dJi6LBRpNPXZHaS9M5SkWSnlIqOhPChZlDVqC7SE',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 17:41:17','2025-12-09 17:41:17','2025-12-09 17:41:17'),(120,'QybmqmAMjqnmmj1F4DZ2rJDGbzW1vDB7gZ36JiEF',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 17:41:17','2025-12-09 17:41:17','2025-12-09 17:41:17'),(121,'xEzQadaWlu6EKtGWVb5Zwj1cs1QfVAie2wNuoYDn',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 17:41:18','2025-12-09 17:41:18','2025-12-09 17:41:18'),(122,'xkmwVspJ5PpCpJdanzOyouUt6C2kfxySnd26BhMU',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 17:42:35','2025-12-09 17:42:35','2025-12-09 17:42:35'),(123,'oHn57vj5xAnvOiufvIRXqPAqRn3Vr0rYmGISIwhM',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 17:42:36','2025-12-09 17:42:36','2025-12-09 17:42:36'),(124,'rzPomy3XY6E85IzAesghnDlOzmVT3VYM81SudDM4',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 17:42:39','2025-12-09 17:42:39','2025-12-09 17:42:39'),(125,'AROpHEsotl9z9KyQ4rqEojIGEhUlNgAi8mgmJd3X',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 17:42:39','2025-12-09 17:42:39','2025-12-09 17:42:39'),(126,'FlX4yF6WpixDsyHIyQvjpKHpXPLuykypSCUFY0Pj',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 17:43:49','2025-12-09 17:43:49','2025-12-09 17:43:49'),(127,'7ZuPiMk1marJCXLV1TDCDvLLjc8bsmcY4a866Ste',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 17:43:53','2025-12-09 17:43:53','2025-12-09 17:43:53'),(128,'t54N1wqNRGUDJvxgxFTV0a520aOqKgpbmzbVr8A4',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 17:44:05','2025-12-09 17:44:05','2025-12-09 17:44:05'),(129,'avAiIMXh5YkV3Y4BAEHJ8YGmaOvykZjTMV28xkX7',5,'[]',0.00,0.00,'[]',NULL,'2025-12-09 22:50:09','2025-12-09 17:48:42','2025-12-09 17:50:09'),(130,'YtYf1fmcaaee1CQe1cbProi6JVPH8KlXfzkkxgR2',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 19:00:30','2025-12-09 19:00:30','2025-12-09 19:00:30'),(131,'Bo02fznDCwfrwr6FiFMiY5sLGa0Vwh5a55TSuDn7',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 19:14:10','2025-12-09 19:14:10','2025-12-09 19:14:10'),(132,'w0B3TvTt6wXHDqhabz3d67xHcFTmDrrKaUgEtPpj',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 19:14:13','2025-12-09 19:14:13','2025-12-09 19:14:13'),(133,'9p7KljrUwhEyKmxkXfFVSjwCDsIJYnVInCW8z9nP',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 19:14:15','2025-12-09 19:14:15','2025-12-09 19:14:15'),(134,'ld8fucAvPM6PWvY0DmalMRW7wUsqey4t1mTjg2cd',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 19:15:40','2025-12-09 19:15:40','2025-12-09 19:15:40'),(135,'J5Qco24i0MDlt75JneNsIEoyAPMVomKm7aegAhjE',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 19:19:00','2025-12-09 19:19:00','2025-12-09 19:19:00'),(136,'ALOytqvDYQXYGGTjDLfjreeNvL93BEAi1949XDUB',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 19:19:01','2025-12-09 19:19:01','2025-12-09 19:19:01'),(137,'5019Col2P4FgXvvWErxRM1gynq4ySF9SW3PJ4bm0',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 19:36:43','2025-12-09 19:36:43','2025-12-09 19:36:43'),(138,'6SxYGXZmYACBni5cZZwgRzhhjJW2xwEFe3uApvQz',5,'[]',0.00,0.00,'[]',NULL,'2025-12-10 02:43:14','2025-12-09 21:18:06','2025-12-09 21:43:14'),(139,'6YoQsasbu6eOYidKS9KCdt9iJqObUEuj07q71P7t',5,'[]',0.00,0.00,NULL,NULL,'2025-12-09 23:40:33','2025-12-09 23:40:33','2025-12-09 23:40:33'),(140,'CSaUIcnjFzCUrGyhXg9JCJQ4hkDduH6ESjtYdPjY',5,'[]',0.00,0.00,NULL,NULL,'2025-12-10 00:18:12','2025-12-10 00:18:12','2025-12-10 00:18:12'),(141,'pL99kdA6P9dimTcwkKgYlGv45QFxlo84haxH7iZK',5,'[]',0.00,0.00,NULL,NULL,'2025-12-10 01:15:13','2025-12-10 01:15:13','2025-12-10 01:15:13'),(142,'3GQwbCbxhFpIVnEOIg1h68qfwD7wKOCIFYuoAply',5,'[]',0.00,0.00,NULL,NULL,'2025-12-10 04:44:03','2025-12-10 04:44:03','2025-12-10 04:44:03'),(143,'d2FefzBwPullLqR5GZXphrWjcXv4isy2ye987vpV',5,'[]',0.00,0.00,NULL,NULL,'2025-12-10 05:56:22','2025-12-10 05:56:22','2025-12-10 05:56:22'),(144,'TvEcF0TbN3A1UWRiZwiQmx81ysiY2Npn4KKee1ZK',5,'[]',0.00,0.00,NULL,NULL,'2025-12-10 06:18:27','2025-12-10 06:18:27','2025-12-10 06:18:27'),(145,'r1kHSe2PpoFqDm0AT7OIAd8gXxkGO66d0kl9mFWR',5,'[]',0.00,0.00,NULL,NULL,'2025-12-10 06:27:47','2025-12-10 06:27:47','2025-12-10 06:27:47'),(146,'rhxv4zogptAnhpnf5R0oDA4zOKqnZtsRjmWif8FI',5,'[]',0.00,0.00,NULL,NULL,'2025-12-10 07:25:45','2025-12-10 07:25:45','2025-12-10 07:25:45'),(147,'nSXWWOx8DHOuhsk10Dzmejhfc8L1vEa509Qaqu3Y',5,'[]',0.00,0.00,NULL,NULL,'2025-12-10 07:51:37','2025-12-10 07:51:37','2025-12-10 07:51:37'),(148,'mg3IFJ2UaJU6SIjSETahePLvvQD706BD6SRtvmxz',5,'[]',0.00,0.00,NULL,NULL,'2025-12-10 08:24:19','2025-12-10 08:24:19','2025-12-10 08:24:19'),(149,'lsJOPyQsPwGuHmX3rVS0zkE2QFXzU8Ct5ibxx7dD',5,'[]',0.00,0.00,NULL,NULL,'2025-12-10 08:51:36','2025-12-10 08:51:36','2025-12-10 08:51:36'),(150,'yryGoEe5PEuWRvnAFgaxQlskdeWIfLuvTCEgaM4B',5,'[]',0.00,0.00,NULL,NULL,'2025-12-10 09:06:26','2025-12-10 09:06:26','2025-12-10 09:06:26'),(151,'tuYWMwzMfnJmKrW3LTRrxz6C5BCnGq1MYoHuliA9',5,'[]',0.00,0.00,NULL,NULL,'2025-12-10 09:18:44','2025-12-10 09:18:44','2025-12-10 09:18:44'),(152,'qFVUDMQfAe0JqEUGaYpPKLOsatbHek9kuzAG0dlw',5,'[]',0.00,0.00,NULL,NULL,'2025-12-10 09:19:21','2025-12-10 09:19:21','2025-12-10 09:19:21'),(153,'jjKfqrc6S5LLc4KltQIQoyCzMi1992V5uO88PDO9',5,'[]',0.00,0.00,NULL,NULL,'2025-12-10 09:19:49','2025-12-10 09:19:49','2025-12-10 09:19:49'),(154,'aILk0UNXahygX4nH1mKxquG3Fb7sN8jwCJPKLruR',5,'[]',0.00,0.00,NULL,NULL,'2025-12-10 09:21:47','2025-12-10 09:21:47','2025-12-10 09:21:47'),(155,'KcnIhWHLpqroLaxlSJoTQYEAb0v7kS6ZXCbQRreF',5,'[]',0.00,0.00,NULL,NULL,'2025-12-10 09:30:16','2025-12-10 09:30:16','2025-12-10 09:30:16'),(156,'e0eu8RFTqeglBtyTTm2x8fz0MHjgwZqhlg5pUTiT',5,'[]',0.00,0.00,NULL,NULL,'2025-12-10 09:39:38','2025-12-10 09:39:38','2025-12-10 09:39:38'),(157,'swX3p1VClnGhopNcYRTRki6Vjjsnd9i4YacZ2y0J',5,'[]',0.00,0.00,NULL,NULL,'2025-12-10 09:40:52','2025-12-10 09:40:52','2025-12-10 09:40:52'),(158,'uVS2a7qaO2bFwYAdcguua98vdOCQk8daJoK51MZ8',5,'[]',0.00,0.00,NULL,NULL,'2025-12-10 11:25:44','2025-12-10 11:25:44','2025-12-10 11:25:44'),(159,'xPce649MaFg3BUf7xUrAmi1yabBhvMU1sZZZpTWh',5,'[]',0.00,0.00,NULL,NULL,'2025-12-10 13:45:39','2025-12-10 13:45:39','2025-12-10 13:45:39'),(160,'vzriqVgssLhE0HtrOcmlR3fgHmyBoOOPnERkKblY',5,'[]',0.00,0.00,NULL,NULL,'2025-12-10 14:33:17','2025-12-10 14:33:17','2025-12-10 14:33:17'),(161,'PeXb11TgOvtCwp17L8MlDyw5q2QlFSti9DqtiDjH',5,'[]',0.00,0.00,NULL,NULL,'2025-12-10 14:39:35','2025-12-10 14:39:35','2025-12-10 14:39:35'),(162,'DqWqPhHWtQi4z980jHGuDjgB470Q2DpvnCm5gwHa',5,'[]',0.00,0.00,NULL,NULL,'2025-12-10 14:45:41','2025-12-10 14:45:41','2025-12-10 14:45:41'),(163,'MFhfsxIeIuOyHAmeeLCfAqcjuX7FRuUKtm2atC9l',5,'[]',0.00,0.00,NULL,NULL,'2025-12-10 16:52:42','2025-12-10 16:52:42','2025-12-10 16:52:42'),(164,'cLxL8e6IwfaBubWDhcXVi7zdYjfy37d1BK8yrV5r',5,'[]',0.00,0.00,NULL,NULL,'2025-12-10 18:31:57','2025-12-10 18:31:57','2025-12-10 18:31:57'),(165,'sG6CRHLqaPE4n61mWM6KM87TSPuOsVibPYAfDr9V',5,'[]',0.00,0.00,'[]',NULL,'2025-12-11 00:10:31','2025-12-10 18:54:50','2025-12-10 19:10:31'),(166,'LZEqO2ForfypEwqQ43NzxbIvweWIH7AMXuMxNJD0',5,'[]',0.00,0.00,NULL,NULL,'2025-12-10 18:58:39','2025-12-10 18:58:39','2025-12-10 18:58:39'),(167,'KareivsGcYHgfHewRZjudScWYBApf9wQzbfyxcES',5,'[]',0.00,0.00,NULL,NULL,'2025-12-10 19:10:23','2025-12-10 19:10:23','2025-12-10 19:10:23'),(168,'6iO7GjdzQ2o4Wwawft4Gko8Xk4ezNa0XfnoaqmVG',5,'[]',0.00,0.00,NULL,NULL,'2025-12-10 19:16:24','2025-12-10 19:16:24','2025-12-10 19:16:24'),(169,'t6xvg1tlHKRV5TrNEZJ2e5qZ1wjUJo4WjM7cEoui',5,'[]',0.00,0.00,NULL,NULL,'2025-12-10 19:40:17','2025-12-10 19:40:17','2025-12-10 19:40:17'),(170,'nXMYSdoSpjKJAYpxfWpe0UOmeMSurarysHVp8EJz',5,'{\"42\":{\"producto_id\":\"42\",\"variante_id\":null,\"cantidad\":\"1\",\"precio\":\"185000.00\",\"precio_total\":185000,\"nombre\":\"PLANCHA DE AGUACATE Y MACADAMIA\",\"referencia\":\"S9960\",\"descuentos\":[],\"descuento_total_item\":0}}',185000.00,0.00,'[]',NULL,'2025-12-11 01:56:21','2025-12-10 20:55:16','2025-12-10 20:56:21'),(171,'kFDs8cdDaXLe4AAKCAzu2yJZE4QZ6apNrbX9zATV',5,'[]',0.00,0.00,NULL,NULL,'2025-12-11 00:33:38','2025-12-11 00:33:38','2025-12-11 00:33:38'),(172,'hnaRyVF4QdJ11pOMLVhrydLRc2ggYQdC3LjunSqv',5,'[]',0.00,0.00,NULL,NULL,'2025-12-11 01:11:00','2025-12-11 01:11:00','2025-12-11 01:11:00'),(173,'i3N3leHkUEPfCizLHhv4oWLa1YHtkag3x24TvF9C',5,'[]',0.00,0.00,NULL,NULL,'2025-12-11 04:54:12','2025-12-11 04:54:12','2025-12-11 04:54:12'),(174,'4limn53F5VSaUKJMYiIwJTBcgLCCSJn473e0lnTP',5,'[]',0.00,0.00,NULL,NULL,'2025-12-11 05:20:01','2025-12-11 05:20:01','2025-12-11 05:20:01'),(175,'gYk0w3MlNEiwBA9XEU4ISlchvaUmy3G31nCdn0sf',5,'[]',0.00,0.00,NULL,NULL,'2025-12-11 06:59:39','2025-12-11 06:59:39','2025-12-11 06:59:39'),(176,'hc6fLJsZjemzUOJdEyhpKRHe2GKBCaHusqKnsKEW',5,'[]',0.00,0.00,NULL,NULL,'2025-12-11 07:27:16','2025-12-11 07:27:16','2025-12-11 07:27:16'),(177,'0qPv4VniptXfR09x3WZmhMEhkrTJoGcIeENLRqn5',5,'{\"37\":{\"producto_id\":\"37\",\"variante_id\":null,\"cantidad\":\"1\",\"precio\":\"100000.00\",\"precio_total\":100000,\"nombre\":\"Pantalon Azul\",\"referencia\":\"pantalon\",\"descuentos\":[],\"descuento_total_item\":0}}',100000.00,0.00,'[]',NULL,'2025-12-11 15:57:01','2025-12-11 10:55:36','2025-12-11 10:57:01'),(178,'KHTGXVF5loN1cSLdy1FdLMbNvoWfU4eZDzzO1EnR',5,'[]',0.00,0.00,NULL,NULL,'2025-12-11 10:56:28','2025-12-11 10:56:28','2025-12-11 10:56:28'),(179,'VhgwaYxxNqiChutlPw3CP5tFkiM0UtsXkx4M6hcN',5,'[]',0.00,0.00,NULL,NULL,'2025-12-11 10:57:16','2025-12-11 10:57:16','2025-12-11 10:57:16'),(180,'QvqoNuSd0pMItS0wTtUkhnAs6hvI0vanPON4B41o',5,'[]',0.00,0.00,NULL,NULL,'2025-12-11 10:59:08','2025-12-11 10:59:08','2025-12-11 10:59:08'),(181,'C4O1DMzwfp0EtSAOCYaJC5zRgsswMqkmgj5932rF',5,'[]',0.00,0.00,NULL,NULL,'2025-12-11 11:01:54','2025-12-11 11:01:54','2025-12-11 11:01:54'),(182,'TfWRVtBE2l48NXK9n2GkG3L0A2O3pMG5ReWG3wEE',5,'{\"43\":{\"producto_id\":\"43\",\"variante_id\":null,\"cantidad\":\"1\",\"precio\":\"190000.00\",\"precio_total\":190000,\"nombre\":\"PLANCHA ACEITE DE ARGAN\",\"referencia\":\"S8500A\",\"descuentos\":[],\"descuento_total_item\":0}}',190000.00,0.00,'[]',NULL,'2025-12-11 19:26:52','2025-12-11 19:16:45','2025-12-11 19:26:52'),(183,'CjasvQ9V25vq8O4p7BZGzZMiCKsWD8ajskkrrG2o',5,'{\"43\":{\"producto_id\":\"43\",\"variante_id\":null,\"cantidad\":\"1\",\"precio\":\"190000.00\",\"precio_total\":190000,\"nombre\":\"PLANCHA ACEITE DE ARGAN\",\"referencia\":\"S8500A\",\"descuentos\":[],\"descuento_total_item\":0},\"38\":{\"producto_id\":\"38\",\"variante_id\":null,\"cantidad\":\"1\",\"precio\":\"500000.00\",\"precio_total\":500000,\"nombre\":\"Camiseta roja\",\"referencia\":\"camiseta roja\",\"descuentos\":[],\"descuento_total_item\":0}}',690000.00,0.00,'[]',NULL,'2025-12-11 19:35:10','2025-12-11 19:34:38','2025-12-11 19:35:10'),(184,'mSwrJuReXMzk62GPSDhrj5m7oFU6jHyAuaN9aPZf',5,'[]',0.00,0.00,NULL,NULL,'2025-12-11 19:37:15','2025-12-11 19:37:15','2025-12-11 19:37:15'),(185,'mehi4bECXnZDJLlbQLtKA2TWTVjoUHEBBJsZu2os',5,'[]',0.00,0.00,NULL,NULL,'2025-12-11 19:38:28','2025-12-11 19:38:28','2025-12-11 19:38:28'),(186,'Llij0g6klzQ6E5mNiqIhJ0X3d4DDObitljeLCt2f',5,'[]',0.00,0.00,NULL,NULL,'2025-12-11 19:39:32','2025-12-11 19:39:32','2025-12-11 19:39:32'),(187,'J9MON4mPLJg7T1JEAPqSx69n5KlBIgnSr07RtT49',5,'[]',0.00,0.00,NULL,NULL,'2025-12-11 19:40:04','2025-12-11 19:40:04','2025-12-11 19:40:04'),(188,'uevx9bPhET5QBoMKmlIXi5rvUaJaQVDUEofqxLbD',5,'[]',0.00,0.00,NULL,NULL,'2025-12-11 19:40:30','2025-12-11 19:40:30','2025-12-11 19:40:30'),(189,'yLxpALmVK78xT8sGm3QIgwy9N6VQfU52vDDgkW1B',5,'[]',0.00,0.00,NULL,NULL,'2025-12-11 22:57:17','2025-12-11 22:57:17','2025-12-11 22:57:17'),(190,'HUFRQIWQI2zde7ln0ZdVPiLRiiX0DtY0tsAavjVE',5,'[]',0.00,0.00,NULL,NULL,'2025-12-11 22:57:36','2025-12-11 22:57:36','2025-12-11 22:57:36'),(191,'aof64TlGQE2JEEZmoWDyQKy0dF9uSPDRb4M6mWEd',5,'[]',0.00,0.00,NULL,NULL,'2025-12-19 21:28:24','2025-12-19 21:28:24','2025-12-19 21:28:24'),(192,'lmGPrbQ0tWdVB8B3UjqNfKy0U1nUhmt0pMrfjLJG',5,'[]',0.00,0.00,NULL,NULL,'2025-12-19 22:04:17','2025-12-19 22:04:17','2025-12-19 22:04:17'),(193,'gyIAll6yhXOk2PjkUhaDIqJmFJmMWwXCMJNiIGRr',5,'[]',0.00,0.00,NULL,NULL,'2025-12-19 23:51:22','2025-12-19 23:51:22','2025-12-19 23:51:22'),(194,'GP57Sm4cvBPi1CRXcJvCkBlJECDysLyS0g3v4lWM',5,'[]',0.00,0.00,NULL,NULL,'2025-12-19 23:52:43','2025-12-19 23:52:43','2025-12-19 23:52:43'),(195,'nVvQ44vlEZTKRVcAoXqk0BUuHPVdIl2ckSG7Fjj7',5,'[]',0.00,0.00,NULL,NULL,'2026-01-05 20:42:22','2026-01-05 20:42:22','2026-01-05 20:42:22'),(196,'SHbehhJcYomLlhwfXRvemydSssB6MTgIy3csG5zY',5,'[]',0.00,0.00,'[]',NULL,'2026-01-06 02:10:31','2026-01-06 01:30:56','2026-01-06 02:10:31'),(197,'Y5curbipc1Bwnm6y8X5XxZGVJZ1LAvMtVPoTlBbC',5,'[]',0.00,0.00,NULL,NULL,'2026-01-06 02:11:32','2026-01-06 02:11:32','2026-01-06 02:11:32'),(198,'q3Mta2DEWNEsk5gLxZYZI1q910n4ZBfH1UbhqQbv',5,'[]',0.00,0.00,NULL,NULL,'2026-01-06 02:28:46','2026-01-06 02:28:46','2026-01-06 02:28:46'),(199,'04gWGEy81j5D3xfQBxv108wmAaMDc8OMAbiQ2H9c',5,'[]',0.00,0.00,NULL,NULL,'2026-01-06 04:29:29','2026-01-06 04:29:29','2026-01-06 04:29:29'),(200,'I8FFrRWpHuzBOu0aQjZ8KXbaabNXd7SdB4wa50LR',5,'[]',0.00,0.00,NULL,NULL,'2026-01-06 04:36:51','2026-01-06 04:36:51','2026-01-06 04:36:51'),(201,'irQACJHPrYdapvHOtKMyNZeH1xiN4TNwzb29jzal',5,'[]',0.00,0.00,NULL,NULL,'2026-01-06 04:45:25','2026-01-06 04:45:25','2026-01-06 04:45:25');
/*!40000 ALTER TABLE `carritos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carrusel_empresas`
--

DROP TABLE IF EXISTS `carrusel_empresas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carrusel_empresas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint(20) unsigned NOT NULL,
  `imagen` varchar(255) NOT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `orden` int(11) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_inicio` timestamp NULL DEFAULT NULL,
  `fecha_fin` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `carrusel_empresas_empresa_id_activo_orden_index` (`empresa_id`,`activo`,`orden`),
  CONSTRAINT `carrusel_empresas_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carrusel_empresas`
--

LOCK TABLES `carrusel_empresas` WRITE;
/*!40000 ALTER TABLE `carrusel_empresas` DISABLE KEYS */;
/*!40000 ALTER TABLE `carrusel_empresas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categorias` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint(20) unsigned DEFAULT NULL,
  `nombre` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `orden` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categorias_empresa_id_slug_unique` (`empresa_id`,`slug`),
  UNIQUE KEY `categorias_empresa_id_nombre_unique` (`empresa_id`,`nombre`),
  KEY `categorias_empresa_id_activo_orden_index` (`empresa_id`,`activo`,`orden`),
  CONSTRAINT `categorias_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (16,5,'Ropa','ropa','Ropa','imagenes/categorias/1762473883_690d379bc96e0_GettyImages-1392983528.webp',0,0,'2025-11-07 00:04:43','2025-12-09 21:28:14'),(17,5,'Camisetas','camisetas','Camisetas','imagenes/categorias/1762544406_690e4b16f2a1b_687000a133b2f4e40de2a5cfe5023d1c_l.webp',0,10,'2025-11-07 19:40:06','2025-12-09 21:28:11'),(18,5,'Planchas originales','Planchas originales','Herramientas de calidad profesional hechas para cuidarte. La Originalidad de nuestras planchas es garantizada, deslizan con suavidad, protegen tu cabello y distribuyen el calor de manera uniforme para que cada pasada cuente. Obtén resultados de salón desde casa, sin esfuerzo, sin frizz y con ese brillo que te hace sentir segura y espectacular todos los días.','imagenes/categorias/1765469117_693aebbd3034a_S7740-Plancha-Alisadora-Remington-Triple-Infusion-01-3.jpg',1,20,'2025-11-24 10:45:48','2025-12-11 11:05:17');
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ciudades`
--

DROP TABLE IF EXISTS `ciudades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ciudades` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `departamento_id` bigint(20) unsigned NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ciudades_departamento_id_nombre_unique` (`departamento_id`,`nombre`),
  CONSTRAINT `ciudades_departamento_id_foreign` FOREIGN KEY (`departamento_id`) REFERENCES `departamentos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1104 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ciudades`
--

LOCK TABLES `ciudades` WRITE;
/*!40000 ALTER TABLE `ciudades` DISABLE KEYS */;
INSERT INTO `ciudades` VALUES (1,1,'Leticia','2025-07-28 03:25:20','2025-07-28 03:25:20'),(2,1,'Puerto Nariño','2025-07-28 03:25:20','2025-07-28 03:25:20'),(3,2,'Abejorral','2025-07-28 03:25:20','2025-07-28 03:25:20'),(4,2,'Abriaquí','2025-07-28 03:25:20','2025-07-28 03:25:20'),(5,2,'Alejandría','2025-07-28 03:25:20','2025-07-28 03:25:20'),(6,2,'Amagá','2025-07-28 03:25:20','2025-07-28 03:25:20'),(7,2,'Amalfi','2025-07-28 03:25:20','2025-07-28 03:25:20'),(8,2,'Andes','2025-07-28 03:25:20','2025-07-28 03:25:20'),(9,2,'Angelópolis','2025-07-28 03:25:20','2025-07-28 03:25:20'),(10,2,'Angostura','2025-07-28 03:25:20','2025-07-28 03:25:20'),(11,2,'Anorí','2025-07-28 03:25:20','2025-07-28 03:25:20'),(12,2,'Anzá','2025-07-28 03:25:20','2025-07-28 03:25:20'),(13,2,'Apartadó','2025-07-28 03:25:20','2025-07-28 03:25:20'),(14,2,'Arboletes','2025-07-28 03:25:20','2025-07-28 03:25:20'),(15,2,'Argelia','2025-07-28 03:25:20','2025-07-28 03:25:20'),(16,2,'Armenia','2025-07-28 03:25:20','2025-07-28 03:25:20'),(17,2,'Barbosa','2025-07-28 03:25:20','2025-07-28 03:25:20'),(18,2,'Bello','2025-07-28 03:25:20','2025-07-28 03:25:20'),(19,2,'Belmira','2025-07-28 03:25:20','2025-07-28 03:25:20'),(20,2,'Betania','2025-07-28 03:25:20','2025-07-28 03:25:20'),(21,2,'Betulia','2025-07-28 03:25:20','2025-07-28 03:25:20'),(22,2,'Briceño','2025-07-28 03:25:20','2025-07-28 03:25:20'),(23,2,'Buriticá','2025-07-28 03:25:20','2025-07-28 03:25:20'),(24,2,'Cáceres','2025-07-28 03:25:20','2025-07-28 03:25:20'),(25,2,'Caicedo','2025-07-28 03:25:20','2025-07-28 03:25:20'),(26,2,'Caldas','2025-07-28 03:25:20','2025-07-28 03:25:20'),(27,2,'Campamento','2025-07-28 03:25:20','2025-07-28 03:25:20'),(28,2,'Cañasgordas','2025-07-28 03:25:20','2025-07-28 03:25:20'),(29,2,'Caracolí','2025-07-28 03:25:20','2025-07-28 03:25:20'),(30,2,'Caramanta','2025-07-28 03:25:20','2025-07-28 03:25:20'),(31,2,'Carepa','2025-07-28 03:25:20','2025-07-28 03:25:20'),(32,2,'Carolina del Príncipe','2025-07-28 03:25:20','2025-07-28 03:25:20'),(33,2,'Caucasia','2025-07-28 03:25:20','2025-07-28 03:25:20'),(34,2,'Chigorodó','2025-07-28 03:25:20','2025-07-28 03:25:20'),(35,2,'Cisneros','2025-07-28 03:25:20','2025-07-28 03:25:20'),(36,2,'Ciudad Bolívar','2025-07-28 03:25:20','2025-07-28 03:25:20'),(37,2,'Cocorná','2025-07-28 03:25:20','2025-07-28 03:25:20'),(38,2,'Concepción','2025-07-28 03:25:20','2025-07-28 03:25:20'),(39,2,'Concordia','2025-07-28 03:25:20','2025-07-28 03:25:20'),(40,2,'Copacabana','2025-07-28 03:25:20','2025-07-28 03:25:20'),(41,2,'Dabeiba','2025-07-28 03:25:20','2025-07-28 03:25:20'),(42,2,'Donmatías','2025-07-28 03:25:20','2025-07-28 03:25:20'),(43,2,'Ebéjico','2025-07-28 03:25:20','2025-07-28 03:25:20'),(44,2,'El Bagre','2025-07-28 03:25:20','2025-07-28 03:25:20'),(45,2,'El Carmen de Viboral','2025-07-28 03:25:20','2025-07-28 03:25:20'),(46,2,'El Peñol','2025-07-28 03:25:20','2025-07-28 03:25:20'),(47,2,'El Retiro','2025-07-28 03:25:20','2025-07-28 03:25:20'),(48,2,'El Santuario','2025-07-28 03:25:20','2025-07-28 03:25:20'),(49,2,'Entrerríos','2025-07-28 03:25:20','2025-07-28 03:25:20'),(50,2,'Envigado','2025-07-28 03:25:20','2025-07-28 03:25:20'),(51,2,'Fredonia','2025-07-28 03:25:20','2025-07-28 03:25:20'),(52,2,'Frontino','2025-07-28 03:25:20','2025-07-28 03:25:20'),(53,2,'Giraldo','2025-07-28 03:25:20','2025-07-28 03:25:20'),(54,2,'Girardota','2025-07-28 03:25:20','2025-07-28 03:25:20'),(55,2,'Gómez Plata','2025-07-28 03:25:20','2025-07-28 03:25:20'),(56,2,'Granada','2025-07-28 03:25:20','2025-07-28 03:25:20'),(57,2,'Guadalupe','2025-07-28 03:25:20','2025-07-28 03:25:20'),(58,2,'Guarne','2025-07-28 03:25:20','2025-07-28 03:25:20'),(59,2,'Guatapé','2025-07-28 03:25:20','2025-07-28 03:25:20'),(60,2,'Heliconia','2025-07-28 03:25:20','2025-07-28 03:25:20'),(61,2,'Hispania','2025-07-28 03:25:20','2025-07-28 03:25:20'),(62,2,'Itagüí','2025-07-28 03:25:20','2025-07-28 03:25:20'),(63,2,'Ituango','2025-07-28 03:25:20','2025-07-28 03:25:20'),(64,2,'Jardín','2025-07-28 03:25:20','2025-07-28 03:25:20'),(65,2,'Jericó','2025-07-28 03:25:20','2025-07-28 03:25:20'),(66,2,'La Ceja','2025-07-28 03:25:20','2025-07-28 03:25:20'),(67,2,'La Estrella','2025-07-28 03:25:20','2025-07-28 03:25:20'),(68,2,'La Pintada','2025-07-28 03:25:20','2025-07-28 03:25:20'),(69,2,'La Unión','2025-07-28 03:25:20','2025-07-28 03:25:20'),(70,2,'Liborina','2025-07-28 03:25:20','2025-07-28 03:25:20'),(71,2,'Maceo','2025-07-28 03:25:20','2025-07-28 03:25:20'),(72,2,'Marinilla','2025-07-28 03:25:20','2025-07-28 03:25:20'),(73,2,'Medellín','2025-07-28 03:25:20','2025-07-28 03:25:20'),(74,2,'Montebello','2025-07-28 03:25:20','2025-07-28 03:25:20'),(75,2,'Murindó','2025-07-28 03:25:20','2025-07-28 03:25:20'),(76,2,'Mutatá','2025-07-28 03:25:20','2025-07-28 03:25:20'),(77,2,'Nariño','2025-07-28 03:25:20','2025-07-28 03:25:20'),(78,2,'Nechí','2025-07-28 03:25:20','2025-07-28 03:25:20'),(79,2,'Necoclí','2025-07-28 03:25:20','2025-07-28 03:25:20'),(80,2,'Olaya','2025-07-28 03:25:20','2025-07-28 03:25:20'),(81,2,'Peque','2025-07-28 03:25:20','2025-07-28 03:25:20'),(82,2,'Pueblorrico','2025-07-28 03:25:20','2025-07-28 03:25:20'),(83,2,'Puerto Berrío','2025-07-28 03:25:20','2025-07-28 03:25:20'),(84,2,'Puerto Nare','2025-07-28 03:25:20','2025-07-28 03:25:20'),(85,2,'Puerto Triunfo','2025-07-28 03:25:20','2025-07-28 03:25:20'),(86,2,'Remedios','2025-07-28 03:25:20','2025-07-28 03:25:20'),(87,2,'Rionegro','2025-07-28 03:25:20','2025-07-28 03:25:20'),(88,2,'Sabanalarga','2025-07-28 03:25:20','2025-07-28 03:25:20'),(89,2,'Sabaneta','2025-07-28 03:25:20','2025-07-28 03:25:20'),(90,2,'Salgar','2025-07-28 03:25:20','2025-07-28 03:25:20'),(91,2,'San Andrés de Cuerquia','2025-07-28 03:25:20','2025-07-28 03:25:20'),(92,2,'San Carlos','2025-07-28 03:25:20','2025-07-28 03:25:20'),(93,2,'San Francisco','2025-07-28 03:25:20','2025-07-28 03:25:20'),(94,2,'San Jerónimo','2025-07-28 03:25:20','2025-07-28 03:25:20'),(95,2,'San José de la Montaña','2025-07-28 03:25:20','2025-07-28 03:25:20'),(96,2,'San Juan de Urabá','2025-07-28 03:25:20','2025-07-28 03:25:20'),(97,2,'San Luis','2025-07-28 03:25:20','2025-07-28 03:25:20'),(98,2,'San Pedro de Urabá','2025-07-28 03:25:20','2025-07-28 03:25:20'),(99,2,'San Pedro de los Milagros','2025-07-28 03:25:20','2025-07-28 03:25:20'),(100,2,'San Rafael','2025-07-28 03:25:20','2025-07-28 03:25:20'),(101,2,'San Roque','2025-07-28 03:25:20','2025-07-28 03:25:20'),(102,2,'San Vicente','2025-07-28 03:25:20','2025-07-28 03:25:20'),(103,2,'Santa Bárbara','2025-07-28 03:25:20','2025-07-28 03:25:20'),(104,2,'Santa Fe de Antioquia','2025-07-28 03:25:20','2025-07-28 03:25:20'),(105,2,'Santa Rosa de Osos','2025-07-28 03:25:20','2025-07-28 03:25:20'),(106,2,'Santo Domingo','2025-07-28 03:25:20','2025-07-28 03:25:20'),(107,2,'Segovia','2025-07-28 03:25:20','2025-07-28 03:25:20'),(108,2,'Sonsón','2025-07-28 03:25:20','2025-07-28 03:25:20'),(109,2,'Sopetrán','2025-07-28 03:25:20','2025-07-28 03:25:20'),(110,2,'Támesis','2025-07-28 03:25:20','2025-07-28 03:25:20'),(111,2,'Tarazá','2025-07-28 03:25:20','2025-07-28 03:25:20'),(112,2,'Tarso','2025-07-28 03:25:20','2025-07-28 03:25:20'),(113,2,'Titiribí','2025-07-28 03:25:20','2025-07-28 03:25:20'),(114,2,'Toledo','2025-07-28 03:25:20','2025-07-28 03:25:20'),(115,2,'Turbo','2025-07-28 03:25:20','2025-07-28 03:25:20'),(116,2,'Uramita','2025-07-28 03:25:20','2025-07-28 03:25:20'),(117,2,'Urrao','2025-07-28 03:25:20','2025-07-28 03:25:20'),(118,2,'Valdivia','2025-07-28 03:25:20','2025-07-28 03:25:20'),(119,2,'Valparaíso','2025-07-28 03:25:20','2025-07-28 03:25:20'),(120,2,'Vegachí','2025-07-28 03:25:20','2025-07-28 03:25:20'),(121,2,'Venecia','2025-07-28 03:25:20','2025-07-28 03:25:20'),(122,2,'Vigía del Fuerte','2025-07-28 03:25:20','2025-07-28 03:25:20'),(123,2,'Yalí','2025-07-28 03:25:20','2025-07-28 03:25:20'),(124,2,'Yarumal','2025-07-28 03:25:20','2025-07-28 03:25:20'),(125,2,'Yolombó','2025-07-28 03:25:20','2025-07-28 03:25:20'),(126,2,'Yondó','2025-07-28 03:25:20','2025-07-28 03:25:20'),(127,2,'Zaragoza','2025-07-28 03:25:20','2025-07-28 03:25:20'),(128,3,'Arauca','2025-07-28 03:25:20','2025-07-28 03:25:20'),(129,3,'Arauquita','2025-07-28 03:25:20','2025-07-28 03:25:20'),(130,3,'Cravo Norte','2025-07-28 03:25:20','2025-07-28 03:25:20'),(131,3,'Fortul','2025-07-28 03:25:20','2025-07-28 03:25:20'),(132,3,'Puerto Rondón','2025-07-28 03:25:20','2025-07-28 03:25:20'),(133,3,'Saravena','2025-07-28 03:25:20','2025-07-28 03:25:20'),(134,3,'Tame','2025-07-28 03:25:20','2025-07-28 03:25:20'),(135,4,'Baranoa','2025-07-28 03:25:20','2025-07-28 03:25:20'),(136,4,'Barranquilla','2025-07-28 03:25:20','2025-07-28 03:25:20'),(137,4,'Campo de la Cruz','2025-07-28 03:25:20','2025-07-28 03:25:20'),(138,4,'Candelaria','2025-07-28 03:25:20','2025-07-28 03:25:20'),(139,4,'Galapa','2025-07-28 03:25:20','2025-07-28 03:25:20'),(140,4,'Juan de Acosta','2025-07-28 03:25:20','2025-07-28 03:25:20'),(141,4,'Luruaco','2025-07-28 03:25:20','2025-07-28 03:25:20'),(142,4,'Malambo','2025-07-28 03:25:20','2025-07-28 03:25:20'),(143,4,'Manatí','2025-07-28 03:25:20','2025-07-28 03:25:20'),(144,4,'Palmar de Varela','2025-07-28 03:25:20','2025-07-28 03:25:20'),(145,4,'Piojó','2025-07-28 03:25:20','2025-07-28 03:25:20'),(146,4,'Polonuevo','2025-07-28 03:25:20','2025-07-28 03:25:20'),(147,4,'Ponedera','2025-07-28 03:25:20','2025-07-28 03:25:20'),(148,4,'Puerto Colombia','2025-07-28 03:25:20','2025-07-28 03:25:20'),(149,4,'Repelón','2025-07-28 03:25:20','2025-07-28 03:25:20'),(150,4,'Sabanagrande','2025-07-28 03:25:20','2025-07-28 03:25:20'),(151,4,'Sabanalarga','2025-07-28 03:25:20','2025-07-28 03:25:20'),(152,4,'Santa Lucía','2025-07-28 03:25:20','2025-07-28 03:25:20'),(153,4,'Santo Tomás','2025-07-28 03:25:20','2025-07-28 03:25:20'),(154,4,'Soledad','2025-07-28 03:25:20','2025-07-28 03:25:20'),(155,4,'Suán','2025-07-28 03:25:20','2025-07-28 03:25:20'),(156,4,'Tubará','2025-07-28 03:25:20','2025-07-28 03:25:20'),(157,4,'Usiacurí','2025-07-28 03:25:20','2025-07-28 03:25:20'),(158,5,'Achí','2025-07-28 03:25:20','2025-07-28 03:25:20'),(159,5,'Altos del Rosario','2025-07-28 03:25:20','2025-07-28 03:25:20'),(160,5,'Arenal','2025-07-28 03:25:20','2025-07-28 03:25:20'),(161,5,'Arjona','2025-07-28 03:25:20','2025-07-28 03:25:20'),(162,5,'Arroyohondo','2025-07-28 03:25:20','2025-07-28 03:25:20'),(163,5,'Barranco de Loba','2025-07-28 03:25:20','2025-07-28 03:25:20'),(164,5,'Brazuelo de Papayal','2025-07-28 03:25:20','2025-07-28 03:25:20'),(165,5,'Calamar','2025-07-28 03:25:20','2025-07-28 03:25:20'),(166,5,'Cantagallo','2025-07-28 03:25:20','2025-07-28 03:25:20'),(167,5,'Cartagena de Indias','2025-07-28 03:25:20','2025-07-28 03:25:20'),(168,5,'Cicuco','2025-07-28 03:25:20','2025-07-28 03:25:20'),(169,5,'Clemencia','2025-07-28 03:25:20','2025-07-28 03:25:20'),(170,5,'Córdoba','2025-07-28 03:25:20','2025-07-28 03:25:20'),(171,5,'El Carmen de Bolívar','2025-07-28 03:25:20','2025-07-28 03:25:20'),(172,5,'El Guamo','2025-07-28 03:25:20','2025-07-28 03:25:20'),(173,5,'El Peñón','2025-07-28 03:25:20','2025-07-28 03:25:20'),(174,5,'Hatillo de Loba','2025-07-28 03:25:20','2025-07-28 03:25:20'),(175,5,'Magangué','2025-07-28 03:25:20','2025-07-28 03:25:20'),(176,5,'Mahates','2025-07-28 03:25:21','2025-07-28 03:25:21'),(177,5,'Margarita','2025-07-28 03:25:21','2025-07-28 03:25:21'),(178,5,'María la Baja','2025-07-28 03:25:21','2025-07-28 03:25:21'),(179,5,'Mompós','2025-07-28 03:25:21','2025-07-28 03:25:21'),(180,5,'Montecristo','2025-07-28 03:25:21','2025-07-28 03:25:21'),(181,5,'Morales','2025-07-28 03:25:21','2025-07-28 03:25:21'),(182,5,'Norosí','2025-07-28 03:25:21','2025-07-28 03:25:21'),(183,5,'Pinillos','2025-07-28 03:25:21','2025-07-28 03:25:21'),(184,5,'Regidor','2025-07-28 03:25:21','2025-07-28 03:25:21'),(185,5,'Río Viejo','2025-07-28 03:25:21','2025-07-28 03:25:21'),(186,5,'San Cristóbal','2025-07-28 03:25:21','2025-07-28 03:25:21'),(187,5,'San Estanislao','2025-07-28 03:25:21','2025-07-28 03:25:21'),(188,5,'San Fernando','2025-07-28 03:25:21','2025-07-28 03:25:21'),(189,5,'San Jacinto del Cauca','2025-07-28 03:25:21','2025-07-28 03:25:21'),(190,5,'San Jacinto','2025-07-28 03:25:21','2025-07-28 03:25:21'),(191,5,'San Juan Nepomuceno','2025-07-28 03:25:21','2025-07-28 03:25:21'),(192,5,'San Martín de Loba','2025-07-28 03:25:21','2025-07-28 03:25:21'),(193,5,'San Pablo','2025-07-28 03:25:21','2025-07-28 03:25:21'),(194,5,'Santa Catalina','2025-07-28 03:25:21','2025-07-28 03:25:21'),(195,5,'Santa Rosa','2025-07-28 03:25:21','2025-07-28 03:25:21'),(196,5,'Santa Rosa del Sur','2025-07-28 03:25:21','2025-07-28 03:25:21'),(197,5,'Simití','2025-07-28 03:25:21','2025-07-28 03:25:21'),(198,5,'Soplaviento','2025-07-28 03:25:21','2025-07-28 03:25:21'),(199,5,'Talaigua Nuevo','2025-07-28 03:25:21','2025-07-28 03:25:21'),(200,5,'Tiquisio','2025-07-28 03:25:21','2025-07-28 03:25:21'),(201,5,'Turbaco','2025-07-28 03:25:21','2025-07-28 03:25:21'),(202,5,'Turbaná','2025-07-28 03:25:21','2025-07-28 03:25:21'),(203,5,'Villanueva','2025-07-28 03:25:21','2025-07-28 03:25:21'),(204,5,'Zambrano','2025-07-28 03:25:21','2025-07-28 03:25:21'),(205,6,'Almeida','2025-07-28 03:25:21','2025-07-28 03:25:21'),(206,6,'Aquitania','2025-07-28 03:25:21','2025-07-28 03:25:21'),(207,6,'Arcabuco','2025-07-28 03:25:21','2025-07-28 03:25:21'),(208,6,'Belén','2025-07-28 03:25:21','2025-07-28 03:25:21'),(209,6,'Berbeo','2025-07-28 03:25:21','2025-07-28 03:25:21'),(210,6,'Betéitiva','2025-07-28 03:25:21','2025-07-28 03:25:21'),(211,6,'Boavita','2025-07-28 03:25:21','2025-07-28 03:25:21'),(212,6,'Boyacá','2025-07-28 03:25:21','2025-07-28 03:25:21'),(213,6,'Briceño','2025-07-28 03:25:21','2025-07-28 03:25:21'),(214,6,'Buenavista','2025-07-28 03:25:21','2025-07-28 03:25:21'),(215,6,'Busbanzá','2025-07-28 03:25:21','2025-07-28 03:25:21'),(216,6,'Caldas','2025-07-28 03:25:21','2025-07-28 03:25:21'),(217,6,'Campohermoso','2025-07-28 03:25:21','2025-07-28 03:25:21'),(218,6,'Cerinza','2025-07-28 03:25:21','2025-07-28 03:25:21'),(219,6,'Chinavita','2025-07-28 03:25:21','2025-07-28 03:25:21'),(220,6,'Chiquinquirá','2025-07-28 03:25:21','2025-07-28 03:25:21'),(221,6,'Chíquiza','2025-07-28 03:25:21','2025-07-28 03:25:21'),(222,6,'Chiscas','2025-07-28 03:25:21','2025-07-28 03:25:21'),(223,6,'Chita','2025-07-28 03:25:21','2025-07-28 03:25:21'),(224,6,'Chitaraque','2025-07-28 03:25:21','2025-07-28 03:25:21'),(225,6,'Chivatá','2025-07-28 03:25:21','2025-07-28 03:25:21'),(226,6,'Chivor','2025-07-28 03:25:21','2025-07-28 03:25:21'),(227,6,'Ciénega','2025-07-28 03:25:21','2025-07-28 03:25:21'),(228,6,'Cómbita','2025-07-28 03:25:21','2025-07-28 03:25:21'),(229,6,'Coper','2025-07-28 03:25:21','2025-07-28 03:25:21'),(230,6,'Corrales','2025-07-28 03:25:21','2025-07-28 03:25:21'),(231,6,'Covarachía','2025-07-28 03:25:21','2025-07-28 03:25:21'),(232,6,'Cubará','2025-07-28 03:25:21','2025-07-28 03:25:21'),(233,6,'Cucaita','2025-07-28 03:25:21','2025-07-28 03:25:21'),(234,6,'Cuítiva','2025-07-28 03:25:21','2025-07-28 03:25:21'),(235,6,'Duitama','2025-07-28 03:25:21','2025-07-28 03:25:21'),(236,6,'El Cocuy','2025-07-28 03:25:21','2025-07-28 03:25:21'),(237,6,'El Espino','2025-07-28 03:25:21','2025-07-28 03:25:21'),(238,6,'Firavitoba','2025-07-28 03:25:21','2025-07-28 03:25:21'),(239,6,'Floresta','2025-07-28 03:25:21','2025-07-28 03:25:21'),(240,6,'Gachantivá','2025-07-28 03:25:21','2025-07-28 03:25:21'),(241,6,'Gámeza','2025-07-28 03:25:21','2025-07-28 03:25:21'),(242,6,'Garagoa','2025-07-28 03:25:21','2025-07-28 03:25:21'),(243,6,'Guacamayas','2025-07-28 03:25:21','2025-07-28 03:25:21'),(244,6,'Guateque','2025-07-28 03:25:21','2025-07-28 03:25:21'),(245,6,'Guayatá','2025-07-28 03:25:21','2025-07-28 03:25:21'),(246,6,'Güicán','2025-07-28 03:25:21','2025-07-28 03:25:21'),(247,6,'Iza','2025-07-28 03:25:21','2025-07-28 03:25:21'),(248,6,'Jenesano','2025-07-28 03:25:21','2025-07-28 03:25:21'),(249,6,'Jericó','2025-07-28 03:25:21','2025-07-28 03:25:21'),(250,6,'La Capilla','2025-07-28 03:25:21','2025-07-28 03:25:21'),(251,6,'La Uvita','2025-07-28 03:25:21','2025-07-28 03:25:21'),(252,6,'La Victoria','2025-07-28 03:25:21','2025-07-28 03:25:21'),(253,6,'Labranzagrande','2025-07-28 03:25:21','2025-07-28 03:25:21'),(254,6,'Macanal','2025-07-28 03:25:21','2025-07-28 03:25:21'),(255,6,'Maripí','2025-07-28 03:25:21','2025-07-28 03:25:21'),(256,6,'Miraflores','2025-07-28 03:25:21','2025-07-28 03:25:21'),(257,6,'Mongua','2025-07-28 03:25:21','2025-07-28 03:25:21'),(258,6,'Monguí','2025-07-28 03:25:21','2025-07-28 03:25:21'),(259,6,'Moniquirá','2025-07-28 03:25:21','2025-07-28 03:25:21'),(260,6,'Motavita','2025-07-28 03:25:21','2025-07-28 03:25:21'),(261,6,'Muzo','2025-07-28 03:25:21','2025-07-28 03:25:21'),(262,6,'Nobsa','2025-07-28 03:25:21','2025-07-28 03:25:21'),(263,6,'Nuevo Colón','2025-07-28 03:25:21','2025-07-28 03:25:21'),(264,6,'Oicatá','2025-07-28 03:25:21','2025-07-28 03:25:21'),(265,6,'Otanche','2025-07-28 03:25:21','2025-07-28 03:25:21'),(266,6,'Pachavita','2025-07-28 03:25:21','2025-07-28 03:25:21'),(267,6,'Páez','2025-07-28 03:25:21','2025-07-28 03:25:21'),(268,6,'Paipa','2025-07-28 03:25:21','2025-07-28 03:25:21'),(269,6,'Pajarito','2025-07-28 03:25:21','2025-07-28 03:25:21'),(270,6,'Panqueba','2025-07-28 03:25:21','2025-07-28 03:25:21'),(271,6,'Pauna','2025-07-28 03:25:21','2025-07-28 03:25:21'),(272,6,'Paya','2025-07-28 03:25:21','2025-07-28 03:25:21'),(273,6,'Paz del Río','2025-07-28 03:25:21','2025-07-28 03:25:21'),(274,6,'Pesca','2025-07-28 03:25:21','2025-07-28 03:25:21'),(275,6,'Pisba','2025-07-28 03:25:21','2025-07-28 03:25:21'),(276,6,'Puerto Boyacá','2025-07-28 03:25:21','2025-07-28 03:25:21'),(277,6,'Quípama','2025-07-28 03:25:21','2025-07-28 03:25:21'),(278,6,'Ramiriquí','2025-07-28 03:25:21','2025-07-28 03:25:21'),(279,6,'Ráquira','2025-07-28 03:25:21','2025-07-28 03:25:21'),(280,6,'Rondón','2025-07-28 03:25:21','2025-07-28 03:25:21'),(281,6,'Saboyá','2025-07-28 03:25:21','2025-07-28 03:25:21'),(282,6,'Sáchica','2025-07-28 03:25:21','2025-07-28 03:25:21'),(283,6,'Samacá','2025-07-28 03:25:21','2025-07-28 03:25:21'),(284,6,'San Eduardo','2025-07-28 03:25:21','2025-07-28 03:25:21'),(285,6,'San José de Pare','2025-07-28 03:25:21','2025-07-28 03:25:21'),(286,6,'San Luis de Gaceno','2025-07-28 03:25:21','2025-07-28 03:25:21'),(287,6,'San Mateo','2025-07-28 03:25:21','2025-07-28 03:25:21'),(288,6,'San Miguel de Sema','2025-07-28 03:25:21','2025-07-28 03:25:21'),(289,6,'San Pablo de Borbur','2025-07-28 03:25:21','2025-07-28 03:25:21'),(290,6,'Santa María','2025-07-28 03:25:21','2025-07-28 03:25:21'),(291,6,'Santa Rosa de Viterbo','2025-07-28 03:25:21','2025-07-28 03:25:21'),(292,6,'Santa Sofía','2025-07-28 03:25:21','2025-07-28 03:25:21'),(293,6,'Santana','2025-07-28 03:25:21','2025-07-28 03:25:21'),(294,6,'Sativanorte','2025-07-28 03:25:21','2025-07-28 03:25:21'),(295,6,'Sativasur','2025-07-28 03:25:21','2025-07-28 03:25:21'),(296,6,'Siachoque','2025-07-28 03:25:21','2025-07-28 03:25:21'),(297,6,'Soatá','2025-07-28 03:25:21','2025-07-28 03:25:21'),(298,6,'Socha','2025-07-28 03:25:21','2025-07-28 03:25:21'),(299,6,'Socotá','2025-07-28 03:25:21','2025-07-28 03:25:21'),(300,6,'Sogamoso','2025-07-28 03:25:21','2025-07-28 03:25:21'),(301,6,'Somondoco','2025-07-28 03:25:21','2025-07-28 03:25:21'),(302,6,'Sora','2025-07-28 03:25:21','2025-07-28 03:25:21'),(303,6,'Soracá','2025-07-28 03:25:21','2025-07-28 03:25:21'),(304,6,'Sotaquirá','2025-07-28 03:25:21','2025-07-28 03:25:21'),(305,6,'Susacón','2025-07-28 03:25:21','2025-07-28 03:25:21'),(306,6,'Sutamarchán','2025-07-28 03:25:21','2025-07-28 03:25:21'),(307,6,'Sutatenza','2025-07-28 03:25:21','2025-07-28 03:25:21'),(308,6,'Tasco','2025-07-28 03:25:21','2025-07-28 03:25:21'),(309,6,'Tenza','2025-07-28 03:25:21','2025-07-28 03:25:21'),(310,6,'Tibaná','2025-07-28 03:25:21','2025-07-28 03:25:21'),(311,6,'Tibasosa','2025-07-28 03:25:21','2025-07-28 03:25:21'),(312,6,'Tinjacá','2025-07-28 03:25:21','2025-07-28 03:25:21'),(313,6,'Tipacoque','2025-07-28 03:25:21','2025-07-28 03:25:21'),(314,6,'Toca','2025-07-28 03:25:21','2025-07-28 03:25:21'),(315,6,'Togüí','2025-07-28 03:25:21','2025-07-28 03:25:21'),(316,6,'Tópaga','2025-07-28 03:25:21','2025-07-28 03:25:21'),(317,6,'Tota','2025-07-28 03:25:21','2025-07-28 03:25:21'),(318,6,'Tunja','2025-07-28 03:25:21','2025-07-28 03:25:21'),(319,6,'Tununguá','2025-07-28 03:25:21','2025-07-28 03:25:21'),(320,6,'Turmequé','2025-07-28 03:25:21','2025-07-28 03:25:21'),(321,6,'Tuta','2025-07-28 03:25:21','2025-07-28 03:25:21'),(322,6,'Tutazá','2025-07-28 03:25:21','2025-07-28 03:25:21'),(323,6,'Úmbita','2025-07-28 03:25:21','2025-07-28 03:25:21'),(324,6,'Ventaquemada','2025-07-28 03:25:21','2025-07-28 03:25:21'),(325,6,'Villa de Leyva','2025-07-28 03:25:21','2025-07-28 03:25:21'),(326,6,'Viracachá','2025-07-28 03:25:21','2025-07-28 03:25:21'),(327,6,'Zetaquira','2025-07-28 03:25:21','2025-07-28 03:25:21'),(328,7,'Aguadas','2025-07-28 03:25:21','2025-07-28 03:25:21'),(329,7,'Anserma','2025-07-28 03:25:21','2025-07-28 03:25:21'),(330,7,'Aranzazu','2025-07-28 03:25:21','2025-07-28 03:25:21'),(331,7,'Belalcázar','2025-07-28 03:25:21','2025-07-28 03:25:21'),(332,7,'Chinchiná','2025-07-28 03:25:21','2025-07-28 03:25:21'),(333,7,'Filadelfia','2025-07-28 03:25:21','2025-07-28 03:25:21'),(334,7,'La Dorada','2025-07-28 03:25:21','2025-07-28 03:25:21'),(335,7,'La Merced','2025-07-28 03:25:21','2025-07-28 03:25:21'),(336,7,'Manizales','2025-07-28 03:25:21','2025-07-28 03:25:21'),(337,7,'Manzanares','2025-07-28 03:25:21','2025-07-28 03:25:21'),(338,7,'Marmato','2025-07-28 03:25:21','2025-07-28 03:25:21'),(339,7,'Marquetalia','2025-07-28 03:25:21','2025-07-28 03:25:21'),(340,7,'Marulanda','2025-07-28 03:25:21','2025-07-28 03:25:21'),(341,7,'Neira','2025-07-28 03:25:21','2025-07-28 03:25:21'),(342,7,'Norcasia','2025-07-28 03:25:22','2025-07-28 03:25:22'),(343,7,'Pácora','2025-07-28 03:25:22','2025-07-28 03:25:22'),(344,7,'Palestina','2025-07-28 03:25:22','2025-07-28 03:25:22'),(345,7,'Pensilvania','2025-07-28 03:25:22','2025-07-28 03:25:22'),(346,7,'Riosucio','2025-07-28 03:25:22','2025-07-28 03:25:22'),(347,7,'Risaralda','2025-07-28 03:25:22','2025-07-28 03:25:22'),(348,7,'Salamina','2025-07-28 03:25:22','2025-07-28 03:25:22'),(349,7,'Samaná','2025-07-28 03:25:22','2025-07-28 03:25:22'),(350,7,'San José','2025-07-28 03:25:22','2025-07-28 03:25:22'),(351,7,'Supía','2025-07-28 03:25:22','2025-07-28 03:25:22'),(352,7,'Victoria','2025-07-28 03:25:22','2025-07-28 03:25:22'),(353,7,'Villamaría','2025-07-28 03:25:22','2025-07-28 03:25:22'),(354,7,'Viterbo','2025-07-28 03:25:22','2025-07-28 03:25:22'),(355,8,'Albania','2025-07-28 03:25:22','2025-07-28 03:25:22'),(356,8,'Belén de los Andaquíes','2025-07-28 03:25:22','2025-07-28 03:25:22'),(357,8,'Cartagena del Chairá','2025-07-28 03:25:22','2025-07-28 03:25:22'),(358,8,'Curillo','2025-07-28 03:25:22','2025-07-28 03:25:22'),(359,8,'El Doncello','2025-07-28 03:25:22','2025-07-28 03:25:22'),(360,8,'El Paujil','2025-07-28 03:25:22','2025-07-28 03:25:22'),(361,8,'Florencia','2025-07-28 03:25:22','2025-07-28 03:25:22'),(362,8,'La Montañita','2025-07-28 03:25:22','2025-07-28 03:25:22'),(363,8,'Milán','2025-07-28 03:25:22','2025-07-28 03:25:22'),(364,8,'Morelia','2025-07-28 03:25:22','2025-07-28 03:25:22'),(365,8,'Puerto Rico','2025-07-28 03:25:22','2025-07-28 03:25:22'),(366,8,'San José del Fragua','2025-07-28 03:25:22','2025-07-28 03:25:22'),(367,8,'San Vicente del Caguán','2025-07-28 03:25:22','2025-07-28 03:25:22'),(368,8,'Solano','2025-07-28 03:25:22','2025-07-28 03:25:22'),(369,8,'Solita','2025-07-28 03:25:22','2025-07-28 03:25:22'),(370,8,'Valparaíso','2025-07-28 03:25:22','2025-07-28 03:25:22'),(371,9,'Aguazul','2025-07-28 03:25:22','2025-07-28 03:25:22'),(372,9,'Chámeza','2025-07-28 03:25:22','2025-07-28 03:25:22'),(373,9,'Hato Corozal','2025-07-28 03:25:22','2025-07-28 03:25:22'),(374,9,'La Salina','2025-07-28 03:25:22','2025-07-28 03:25:22'),(375,9,'Maní','2025-07-28 03:25:22','2025-07-28 03:25:22'),(376,9,'Monterrey','2025-07-28 03:25:22','2025-07-28 03:25:22'),(377,9,'Nunchía','2025-07-28 03:25:22','2025-07-28 03:25:22'),(378,9,'Orocué','2025-07-28 03:25:22','2025-07-28 03:25:22'),(379,9,'Paz de Ariporo','2025-07-28 03:25:22','2025-07-28 03:25:22'),(380,9,'Pore','2025-07-28 03:25:22','2025-07-28 03:25:22'),(381,9,'Recetor','2025-07-28 03:25:22','2025-07-28 03:25:22'),(382,9,'Sabanalarga','2025-07-28 03:25:22','2025-07-28 03:25:22'),(383,9,'Sácama','2025-07-28 03:25:22','2025-07-28 03:25:22'),(384,9,'San Luis de Palenque','2025-07-28 03:25:22','2025-07-28 03:25:22'),(385,9,'Támara','2025-07-28 03:25:22','2025-07-28 03:25:22'),(386,9,'Tauramena','2025-07-28 03:25:22','2025-07-28 03:25:22'),(387,9,'Trinidad','2025-07-28 03:25:22','2025-07-28 03:25:22'),(388,9,'Villanueva','2025-07-28 03:25:22','2025-07-28 03:25:22'),(389,9,'Yopal','2025-07-28 03:25:22','2025-07-28 03:25:22'),(390,10,'Almaguer','2025-07-28 03:25:22','2025-07-28 03:25:22'),(391,10,'Argelia','2025-07-28 03:25:22','2025-07-28 03:25:22'),(392,10,'Balboa','2025-07-28 03:25:22','2025-07-28 03:25:22'),(393,10,'Bolívar','2025-07-28 03:25:22','2025-07-28 03:25:22'),(394,10,'Buenos Aires','2025-07-28 03:25:22','2025-07-28 03:25:22'),(395,10,'Cajibío','2025-07-28 03:25:22','2025-07-28 03:25:22'),(396,10,'Caldono','2025-07-28 03:25:22','2025-07-28 03:25:22'),(397,10,'Caloto','2025-07-28 03:25:22','2025-07-28 03:25:22'),(398,10,'Corinto','2025-07-28 03:25:22','2025-07-28 03:25:22'),(399,10,'El Tambo','2025-07-28 03:25:22','2025-07-28 03:25:22'),(400,10,'Florencia','2025-07-28 03:25:22','2025-07-28 03:25:22'),(401,10,'Guachené','2025-07-28 03:25:22','2025-07-28 03:25:22'),(402,10,'Guapí','2025-07-28 03:25:22','2025-07-28 03:25:22'),(403,10,'Inzá','2025-07-28 03:25:22','2025-07-28 03:25:22'),(404,10,'Jambaló','2025-07-28 03:25:22','2025-07-28 03:25:22'),(405,10,'La Sierra','2025-07-28 03:25:22','2025-07-28 03:25:22'),(406,10,'La Vega','2025-07-28 03:25:22','2025-07-28 03:25:22'),(407,10,'López de Micay','2025-07-28 03:25:22','2025-07-28 03:25:22'),(408,10,'Mercaderes','2025-07-28 03:25:22','2025-07-28 03:25:22'),(409,10,'Miranda','2025-07-28 03:25:22','2025-07-28 03:25:22'),(410,10,'Morales','2025-07-28 03:25:22','2025-07-28 03:25:22'),(411,10,'Padilla','2025-07-28 03:25:22','2025-07-28 03:25:22'),(412,10,'Páez','2025-07-28 03:25:22','2025-07-28 03:25:22'),(413,10,'Patía','2025-07-28 03:25:22','2025-07-28 03:25:22'),(414,10,'Piamonte','2025-07-28 03:25:22','2025-07-28 03:25:22'),(415,10,'Piendamó','2025-07-28 03:25:22','2025-07-28 03:25:22'),(416,10,'Popayán','2025-07-28 03:25:22','2025-07-28 03:25:22'),(417,10,'Puerto Tejada','2025-07-28 03:25:22','2025-07-28 03:25:22'),(418,10,'Puracé','2025-07-28 03:25:22','2025-07-28 03:25:22'),(419,10,'Rosas','2025-07-28 03:25:22','2025-07-28 03:25:22'),(420,10,'San Sebastián','2025-07-28 03:25:22','2025-07-28 03:25:22'),(421,10,'Santa Rosa','2025-07-28 03:25:22','2025-07-28 03:25:22'),(422,10,'Santander de Quilichao','2025-07-28 03:25:22','2025-07-28 03:25:22'),(423,10,'Silvia','2025-07-28 03:25:22','2025-07-28 03:25:22'),(424,10,'Sotará','2025-07-28 03:25:22','2025-07-28 03:25:22'),(425,10,'Suárez','2025-07-28 03:25:22','2025-07-28 03:25:22'),(426,10,'Sucre','2025-07-28 03:25:22','2025-07-28 03:25:22'),(427,10,'Timbío','2025-07-28 03:25:22','2025-07-28 03:25:22'),(428,10,'Timbiquí','2025-07-28 03:25:22','2025-07-28 03:25:22'),(429,10,'Toribío','2025-07-28 03:25:22','2025-07-28 03:25:22'),(430,10,'Totoró','2025-07-28 03:25:23','2025-07-28 03:25:23'),(431,10,'Villa Rica','2025-07-28 03:25:23','2025-07-28 03:25:23'),(432,11,'Aguachica','2025-07-28 03:25:23','2025-07-28 03:25:23'),(433,11,'Agustín Codazzi','2025-07-28 03:25:23','2025-07-28 03:25:23'),(434,11,'Astrea','2025-07-28 03:25:23','2025-07-28 03:25:23'),(435,11,'Becerril','2025-07-28 03:25:23','2025-07-28 03:25:23'),(436,11,'Bosconia','2025-07-28 03:25:23','2025-07-28 03:25:23'),(437,11,'Chimichagua','2025-07-28 03:25:23','2025-07-28 03:25:23'),(438,11,'Chiriguaná','2025-07-28 03:25:23','2025-07-28 03:25:23'),(439,11,'Curumaní','2025-07-28 03:25:23','2025-07-28 03:25:23'),(440,11,'El Copey','2025-07-28 03:25:23','2025-07-28 03:25:23'),(441,11,'El Paso','2025-07-28 03:25:23','2025-07-28 03:25:23'),(442,11,'Gamarra','2025-07-28 03:25:23','2025-07-28 03:25:23'),(443,11,'González','2025-07-28 03:25:23','2025-07-28 03:25:23'),(444,11,'La Gloria (Cesar)','2025-07-28 03:25:23','2025-07-28 03:25:23'),(445,11,'La Jagua de Ibirico','2025-07-28 03:25:23','2025-07-28 03:25:23'),(446,11,'La Paz','2025-07-28 03:25:23','2025-07-28 03:25:23'),(447,11,'Manaure Balcón del Cesar','2025-07-28 03:25:23','2025-07-28 03:25:23'),(448,11,'Pailitas','2025-07-28 03:25:23','2025-07-28 03:25:23'),(449,11,'Pelaya','2025-07-28 03:25:23','2025-07-28 03:25:23'),(450,11,'Pueblo Bello','2025-07-28 03:25:23','2025-07-28 03:25:23'),(451,11,'Río de Oro','2025-07-28 03:25:23','2025-07-28 03:25:23'),(452,11,'San Alberto','2025-07-28 03:25:23','2025-07-28 03:25:23'),(453,11,'San Diego','2025-07-28 03:25:23','2025-07-28 03:25:23'),(454,11,'San Martín','2025-07-28 03:25:23','2025-07-28 03:25:23'),(455,11,'Tamalameque','2025-07-28 03:25:23','2025-07-28 03:25:23'),(456,11,'Valledupar','2025-07-28 03:25:23','2025-07-28 03:25:23'),(457,12,'Acandí','2025-07-28 03:25:23','2025-07-28 03:25:23'),(458,12,'Alto Baudó','2025-07-28 03:25:23','2025-07-28 03:25:23'),(459,12,'Bagadó','2025-07-28 03:25:23','2025-07-28 03:25:23'),(460,12,'Bahía Solano','2025-07-28 03:25:23','2025-07-28 03:25:23'),(461,12,'Bajo Baudó','2025-07-28 03:25:23','2025-07-28 03:25:23'),(462,12,'Bojayá','2025-07-28 03:25:23','2025-07-28 03:25:23'),(463,12,'Cantón de San Pablo','2025-07-28 03:25:23','2025-07-28 03:25:23'),(464,12,'Cértegui','2025-07-28 03:25:23','2025-07-28 03:25:23'),(465,12,'Condoto','2025-07-28 03:25:23','2025-07-28 03:25:23'),(466,12,'El Atrato','2025-07-28 03:25:23','2025-07-28 03:25:23'),(467,12,'El Carmen de Atrato','2025-07-28 03:25:23','2025-07-28 03:25:23'),(468,12,'El Carmen del Darién','2025-07-28 03:25:23','2025-07-28 03:25:23'),(469,12,'Istmina','2025-07-28 03:25:23','2025-07-28 03:25:23'),(470,12,'Juradó','2025-07-28 03:25:23','2025-07-28 03:25:23'),(471,12,'Litoral de San Juan','2025-07-28 03:25:23','2025-07-28 03:25:23'),(472,12,'Lloró','2025-07-28 03:25:23','2025-07-28 03:25:23'),(473,12,'Medio Atrato','2025-07-28 03:25:23','2025-07-28 03:25:23'),(474,12,'Medio Baudó','2025-07-28 03:25:23','2025-07-28 03:25:23'),(475,12,'Medio San Juan','2025-07-28 03:25:23','2025-07-28 03:25:23'),(476,12,'Nóvita','2025-07-28 03:25:23','2025-07-28 03:25:23'),(477,12,'Nuquí','2025-07-28 03:25:23','2025-07-28 03:25:23'),(478,12,'Quibdó','2025-07-28 03:25:23','2025-07-28 03:25:23'),(479,12,'Río Iró','2025-07-28 03:25:23','2025-07-28 03:25:23'),(480,12,'Río Quito','2025-07-28 03:25:23','2025-07-28 03:25:23'),(481,12,'Riosucio','2025-07-28 03:25:23','2025-07-28 03:25:23'),(482,12,'San José del Palmar','2025-07-28 03:25:23','2025-07-28 03:25:23'),(483,12,'Sipí','2025-07-28 03:25:23','2025-07-28 03:25:23'),(484,12,'Tadó','2025-07-28 03:25:23','2025-07-28 03:25:23'),(485,12,'Unión Panamericana','2025-07-28 03:25:23','2025-07-28 03:25:23'),(486,12,'Unguía','2025-07-28 03:25:23','2025-07-28 03:25:23'),(487,13,'Agua de Dios','2025-07-28 03:25:23','2025-07-28 03:25:23'),(488,13,'Albán','2025-07-28 03:25:23','2025-07-28 03:25:23'),(489,13,'Anapoima','2025-07-28 03:25:23','2025-07-28 03:25:23'),(490,13,'Anolaima','2025-07-28 03:25:23','2025-07-28 03:25:23'),(491,13,'Apulo','2025-07-28 03:25:23','2025-07-28 03:25:23'),(492,13,'Arbeláez','2025-07-28 03:25:23','2025-07-28 03:25:23'),(493,13,'Beltrán','2025-07-28 03:25:23','2025-07-28 03:25:23'),(494,13,'Bituima','2025-07-28 03:25:23','2025-07-28 03:25:23'),(495,13,'Bogotá','2025-07-28 03:25:23','2025-07-28 03:25:23'),(496,13,'Bojacá','2025-07-28 03:25:23','2025-07-28 03:25:23'),(497,13,'Cabrera','2025-07-28 03:25:23','2025-07-28 03:25:23'),(498,13,'Cachipay','2025-07-28 03:25:23','2025-07-28 03:25:23'),(499,13,'Cajicá','2025-07-28 03:25:23','2025-07-28 03:25:23'),(500,13,'Caparrapí','2025-07-28 03:25:23','2025-07-28 03:25:23'),(501,13,'Cáqueza','2025-07-28 03:25:23','2025-07-28 03:25:23'),(502,13,'Carmen de Carupa','2025-07-28 03:25:23','2025-07-28 03:25:23'),(503,13,'Chaguaní','2025-07-28 03:25:23','2025-07-28 03:25:23'),(504,13,'Chía','2025-07-28 03:25:23','2025-07-28 03:25:23'),(505,13,'Chipaque','2025-07-28 03:25:23','2025-07-28 03:25:23'),(506,13,'Choachí','2025-07-28 03:25:23','2025-07-28 03:25:23'),(507,13,'Chocontá','2025-07-28 03:25:23','2025-07-28 03:25:23'),(508,13,'Cogua','2025-07-28 03:25:23','2025-07-28 03:25:23'),(509,13,'Cota','2025-07-28 03:25:23','2025-07-28 03:25:23'),(510,13,'Cucunubá','2025-07-28 03:25:23','2025-07-28 03:25:23'),(511,13,'El Colegio','2025-07-28 03:25:23','2025-07-28 03:25:23'),(512,13,'El Peñón','2025-07-28 03:25:23','2025-07-28 03:25:23'),(513,13,'El Rosal','2025-07-28 03:25:23','2025-07-28 03:25:23'),(514,13,'Facatativá','2025-07-28 03:25:23','2025-07-28 03:25:23'),(515,13,'Fómeque','2025-07-28 03:25:23','2025-07-28 03:25:23'),(516,13,'Fosca','2025-07-28 03:25:23','2025-07-28 03:25:23'),(517,13,'Funza','2025-07-28 03:25:23','2025-07-28 03:25:23'),(518,13,'Fúquene','2025-07-28 03:25:23','2025-07-28 03:25:23'),(519,13,'Fusagasugá','2025-07-28 03:25:23','2025-07-28 03:25:23'),(520,13,'Gachalá','2025-07-28 03:25:23','2025-07-28 03:25:23'),(521,13,'Gachancipá','2025-07-28 03:25:23','2025-07-28 03:25:23'),(522,13,'Gachetá','2025-07-28 03:25:23','2025-07-28 03:25:23'),(523,13,'Gama','2025-07-28 03:25:23','2025-07-28 03:25:23'),(524,13,'Girardot','2025-07-28 03:25:23','2025-07-28 03:25:23'),(525,13,'Granada','2025-07-28 03:25:23','2025-07-28 03:25:23'),(526,13,'Guachetá','2025-07-28 03:25:23','2025-07-28 03:25:23'),(527,13,'Guaduas','2025-07-28 03:25:23','2025-07-28 03:25:23'),(528,13,'Guasca','2025-07-28 03:25:23','2025-07-28 03:25:23'),(529,13,'Guataquí','2025-07-28 03:25:23','2025-07-28 03:25:23'),(530,13,'Guatavita','2025-07-28 03:25:23','2025-07-28 03:25:23'),(531,13,'Guayabal de Síquima','2025-07-28 03:25:23','2025-07-28 03:25:23'),(532,13,'Guayabetal','2025-07-28 03:25:23','2025-07-28 03:25:23'),(533,13,'Gutiérrez','2025-07-28 03:25:23','2025-07-28 03:25:23'),(534,13,'Jerusalén','2025-07-28 03:25:24','2025-07-28 03:25:24'),(535,13,'Junín','2025-07-28 03:25:24','2025-07-28 03:25:24'),(536,13,'La Calera','2025-07-28 03:25:24','2025-07-28 03:25:24'),(537,13,'La Mesa','2025-07-28 03:25:24','2025-07-28 03:25:24'),(538,13,'La Palma','2025-07-28 03:25:24','2025-07-28 03:25:24'),(539,13,'La Peña','2025-07-28 03:25:24','2025-07-28 03:25:24'),(540,13,'La Vega','2025-07-28 03:25:24','2025-07-28 03:25:24'),(541,13,'Lenguazaque','2025-07-28 03:25:24','2025-07-28 03:25:24'),(542,13,'Machetá','2025-07-28 03:25:24','2025-07-28 03:25:24'),(543,13,'Madrid','2025-07-28 03:25:24','2025-07-28 03:25:24'),(544,13,'Manta','2025-07-28 03:25:24','2025-07-28 03:25:24'),(545,13,'Medina','2025-07-28 03:25:24','2025-07-28 03:25:24'),(546,13,'Mosquera','2025-07-28 03:25:24','2025-07-28 03:25:24'),(547,13,'Nariño','2025-07-28 03:25:24','2025-07-28 03:25:24'),(548,13,'Nemocón','2025-07-28 03:25:24','2025-07-28 03:25:24'),(549,13,'Nilo','2025-07-28 03:25:24','2025-07-28 03:25:24'),(550,13,'Nimaima','2025-07-28 03:25:24','2025-07-28 03:25:24'),(551,13,'Nocaima','2025-07-28 03:25:24','2025-07-28 03:25:24'),(552,13,'Pacho','2025-07-28 03:25:24','2025-07-28 03:25:24'),(553,13,'Paime','2025-07-28 03:25:24','2025-07-28 03:25:24'),(554,13,'Pandi','2025-07-28 03:25:24','2025-07-28 03:25:24'),(555,13,'Paratebueno','2025-07-28 03:25:24','2025-07-28 03:25:24'),(556,13,'Pasca','2025-07-28 03:25:24','2025-07-28 03:25:24'),(557,13,'Puerto Salgar','2025-07-28 03:25:24','2025-07-28 03:25:24'),(558,13,'Pulí','2025-07-28 03:25:24','2025-07-28 03:25:24'),(559,13,'Quebradanegra','2025-07-28 03:25:24','2025-07-28 03:25:24'),(560,13,'Quetame','2025-07-28 03:25:24','2025-07-28 03:25:24'),(561,13,'Quipile','2025-07-28 03:25:24','2025-07-28 03:25:24'),(562,13,'Ricaurte','2025-07-28 03:25:24','2025-07-28 03:25:24'),(563,13,'San Antonio del Tequendama','2025-07-28 03:25:24','2025-07-28 03:25:24'),(564,13,'San Bernardo','2025-07-28 03:25:24','2025-07-28 03:25:24'),(565,13,'San Cayetano','2025-07-28 03:25:24','2025-07-28 03:25:24'),(566,13,'San Francisco','2025-07-28 03:25:24','2025-07-28 03:25:24'),(567,13,'San Juan de Rioseco','2025-07-28 03:25:24','2025-07-28 03:25:24'),(568,13,'Sasaima','2025-07-28 03:25:24','2025-07-28 03:25:24'),(569,13,'Sesquilé','2025-07-28 03:25:24','2025-07-28 03:25:24'),(570,13,'Sibaté','2025-07-28 03:25:24','2025-07-28 03:25:24'),(571,13,'Silvania','2025-07-28 03:25:24','2025-07-28 03:25:24'),(572,13,'Simijaca','2025-07-28 03:25:24','2025-07-28 03:25:24'),(573,13,'Soacha','2025-07-28 03:25:24','2025-07-28 03:25:24'),(574,13,'Sopó','2025-07-28 03:25:24','2025-07-28 03:25:24'),(575,13,'Subachoque','2025-07-28 03:25:24','2025-07-28 03:25:24'),(576,13,'Suesca','2025-07-28 03:25:24','2025-07-28 03:25:24'),(577,13,'Supatá','2025-07-28 03:25:24','2025-07-28 03:25:24'),(578,13,'Susa','2025-07-28 03:25:24','2025-07-28 03:25:24'),(579,13,'Sutatausa','2025-07-28 03:25:24','2025-07-28 03:25:24'),(580,13,'Tabio','2025-07-28 03:25:24','2025-07-28 03:25:24'),(581,13,'Tausa','2025-07-28 03:25:24','2025-07-28 03:25:24'),(582,13,'Tena','2025-07-28 03:25:24','2025-07-28 03:25:24'),(583,13,'Tenjo','2025-07-28 03:25:24','2025-07-28 03:25:24'),(584,13,'Tibacuy','2025-07-28 03:25:24','2025-07-28 03:25:24'),(585,13,'Tibirita','2025-07-28 03:25:24','2025-07-28 03:25:24'),(586,13,'Tocaima','2025-07-28 03:25:24','2025-07-28 03:25:24'),(587,13,'Tocancipá','2025-07-28 03:25:24','2025-07-28 03:25:24'),(588,13,'Topaipí','2025-07-28 03:25:24','2025-07-28 03:25:24'),(589,13,'Ubalá','2025-07-28 03:25:24','2025-07-28 03:25:24'),(590,13,'Ubaque','2025-07-28 03:25:24','2025-07-28 03:25:24'),(591,13,'Ubaté','2025-07-28 03:25:24','2025-07-28 03:25:24'),(592,13,'Une','2025-07-28 03:25:24','2025-07-28 03:25:24'),(593,13,'Útica','2025-07-28 03:25:24','2025-07-28 03:25:24'),(594,13,'Venecia','2025-07-28 03:25:24','2025-07-28 03:25:24'),(595,13,'Vergara','2025-07-28 03:25:24','2025-07-28 03:25:24'),(596,13,'Vianí','2025-07-28 03:25:24','2025-07-28 03:25:24'),(597,13,'Villagómez','2025-07-28 03:25:24','2025-07-28 03:25:24'),(598,13,'Villapinzón','2025-07-28 03:25:24','2025-07-28 03:25:24'),(599,13,'Villeta','2025-07-28 03:25:24','2025-07-28 03:25:24'),(600,13,'Viotá','2025-07-28 03:25:24','2025-07-28 03:25:24'),(601,13,'Yacopí','2025-07-28 03:25:24','2025-07-28 03:25:24'),(602,13,'Zipacón','2025-07-28 03:25:24','2025-07-28 03:25:24'),(603,13,'Zipaquirá','2025-07-28 03:25:24','2025-07-28 03:25:24'),(604,14,'Ayapel','2025-07-28 03:25:24','2025-07-28 03:25:24'),(605,14,'Buenavista','2025-07-28 03:25:24','2025-07-28 03:25:24'),(606,14,'Canalete','2025-07-28 03:25:24','2025-07-28 03:25:24'),(607,14,'Cereté','2025-07-28 03:25:24','2025-07-28 03:25:24'),(608,14,'Chimá','2025-07-28 03:25:24','2025-07-28 03:25:24'),(609,14,'Chinú','2025-07-28 03:25:24','2025-07-28 03:25:24'),(610,14,'Ciénaga de Oro','2025-07-28 03:25:24','2025-07-28 03:25:24'),(611,14,'Cotorra','2025-07-28 03:25:24','2025-07-28 03:25:24'),(612,14,'La Apartada','2025-07-28 03:25:24','2025-07-28 03:25:24'),(613,14,'Lorica','2025-07-28 03:25:24','2025-07-28 03:25:24'),(614,14,'Los Córdobas','2025-07-28 03:25:24','2025-07-28 03:25:24'),(615,14,'Momil','2025-07-28 03:25:24','2025-07-28 03:25:24'),(616,14,'Montelíbano','2025-07-28 03:25:24','2025-07-28 03:25:24'),(617,14,'Montería','2025-07-28 03:25:24','2025-07-28 03:25:24'),(618,14,'Moñitos','2025-07-28 03:25:24','2025-07-28 03:25:24'),(619,14,'Planeta Rica','2025-07-28 03:25:24','2025-07-28 03:25:24'),(620,14,'Pueblo Nuevo','2025-07-28 03:25:24','2025-07-28 03:25:24'),(621,14,'Puerto Escondido','2025-07-28 03:25:24','2025-07-28 03:25:24'),(622,14,'Puerto Libertador','2025-07-28 03:25:24','2025-07-28 03:25:24'),(623,14,'Purísima','2025-07-28 03:25:24','2025-07-28 03:25:24'),(624,14,'Sahagún','2025-07-28 03:25:24','2025-07-28 03:25:24'),(625,14,'San Andrés de Sotavento','2025-07-28 03:25:24','2025-07-28 03:25:24'),(626,14,'San Antero','2025-07-28 03:25:24','2025-07-28 03:25:24'),(627,14,'San Bernardo del Viento','2025-07-28 03:25:24','2025-07-28 03:25:24'),(628,14,'San Carlos','2025-07-28 03:25:24','2025-07-28 03:25:24'),(629,14,'San José de Uré','2025-07-28 03:25:24','2025-07-28 03:25:24'),(630,14,'San Pelayo','2025-07-28 03:25:24','2025-07-28 03:25:24'),(631,14,'Tierralta','2025-07-28 03:25:24','2025-07-28 03:25:24'),(632,14,'Tuchín','2025-07-28 03:25:24','2025-07-28 03:25:24'),(633,14,'Valencia','2025-07-28 03:25:24','2025-07-28 03:25:24'),(634,15,'Inírida','2025-07-28 03:25:24','2025-07-28 03:25:24'),(635,16,'Calamar','2025-07-28 03:25:24','2025-07-28 03:25:24'),(636,16,'El Retorno','2025-07-28 03:25:24','2025-07-28 03:25:24'),(637,16,'Miraflores','2025-07-28 03:25:24','2025-07-28 03:25:24'),(638,16,'San José del Guaviare','2025-07-28 03:25:24','2025-07-28 03:25:24'),(639,17,'Acevedo','2025-07-28 03:25:24','2025-07-28 03:25:24'),(640,17,'Agrado','2025-07-28 03:25:24','2025-07-28 03:25:24'),(641,17,'Aipe','2025-07-28 03:25:24','2025-07-28 03:25:24'),(642,17,'Algeciras','2025-07-28 03:25:24','2025-07-28 03:25:24'),(643,17,'Altamira','2025-07-28 03:25:24','2025-07-28 03:25:24'),(644,17,'Baraya','2025-07-28 03:25:24','2025-07-28 03:25:24'),(645,17,'Campoalegre','2025-07-28 03:25:24','2025-07-28 03:25:24'),(646,17,'Colombia','2025-07-28 03:25:24','2025-07-28 03:25:24'),(647,17,'El Pital','2025-07-28 03:25:24','2025-07-28 03:25:24'),(648,17,'Elías','2025-07-28 03:25:25','2025-07-28 03:25:25'),(649,17,'Garzón','2025-07-28 03:25:25','2025-07-28 03:25:25'),(650,17,'Gigante','2025-07-28 03:25:25','2025-07-28 03:25:25'),(651,17,'Guadalupe','2025-07-28 03:25:25','2025-07-28 03:25:25'),(652,17,'Hobo','2025-07-28 03:25:25','2025-07-28 03:25:25'),(653,17,'Íquira','2025-07-28 03:25:25','2025-07-28 03:25:25'),(654,17,'Isnos','2025-07-28 03:25:25','2025-07-28 03:25:25'),(655,17,'La Argentina','2025-07-28 03:25:25','2025-07-28 03:25:25'),(656,17,'La Plata','2025-07-28 03:25:25','2025-07-28 03:25:25'),(657,17,'Nátaga','2025-07-28 03:25:25','2025-07-28 03:25:25'),(658,17,'Neiva','2025-07-28 03:25:25','2025-07-28 03:25:25'),(659,17,'Oporapa','2025-07-28 03:25:25','2025-07-28 03:25:25'),(660,17,'Paicol','2025-07-28 03:25:25','2025-07-28 03:25:25'),(661,17,'Palermo','2025-07-28 03:25:25','2025-07-28 03:25:25'),(662,17,'Palestina','2025-07-28 03:25:25','2025-07-28 03:25:25'),(663,17,'Pitalito','2025-07-28 03:25:25','2025-07-28 03:25:25'),(664,17,'Rivera','2025-07-28 03:25:25','2025-07-28 03:25:25'),(665,17,'Saladoblanco','2025-07-28 03:25:25','2025-07-28 03:25:25'),(666,17,'San Agustín','2025-07-28 03:25:25','2025-07-28 03:25:25'),(667,17,'Santa María','2025-07-28 03:25:25','2025-07-28 03:25:25'),(668,17,'Suaza','2025-07-28 03:25:25','2025-07-28 03:25:25'),(669,17,'Tarqui','2025-07-28 03:25:25','2025-07-28 03:25:25'),(670,17,'Tello','2025-07-28 03:25:25','2025-07-28 03:25:25'),(671,17,'Teruel','2025-07-28 03:25:25','2025-07-28 03:25:25'),(672,17,'Tesalia','2025-07-28 03:25:25','2025-07-28 03:25:25'),(673,17,'Timaná','2025-07-28 03:25:25','2025-07-28 03:25:25'),(674,17,'Villavieja','2025-07-28 03:25:25','2025-07-28 03:25:25'),(675,17,'Yaguará','2025-07-28 03:25:25','2025-07-28 03:25:25'),(676,18,'Albania','2025-07-28 03:25:25','2025-07-28 03:25:25'),(677,18,'Barrancas','2025-07-28 03:25:25','2025-07-28 03:25:25'),(678,18,'Dibulla','2025-07-28 03:25:25','2025-07-28 03:25:25'),(679,18,'Distracción','2025-07-28 03:25:25','2025-07-28 03:25:25'),(680,18,'El Molino','2025-07-28 03:25:25','2025-07-28 03:25:25'),(681,18,'Fonseca','2025-07-28 03:25:25','2025-07-28 03:25:25'),(682,18,'Hatonuevo','2025-07-28 03:25:25','2025-07-28 03:25:25'),(683,18,'La Jagua del Pilar','2025-07-28 03:25:25','2025-07-28 03:25:25'),(684,18,'Maicao','2025-07-28 03:25:25','2025-07-28 03:25:25'),(685,18,'Manaure','2025-07-28 03:25:25','2025-07-28 03:25:25'),(686,18,'Riohacha','2025-07-28 03:25:25','2025-07-28 03:25:25'),(687,18,'San Juan del Cesar','2025-07-28 03:25:25','2025-07-28 03:25:25'),(688,18,'Uribia','2025-07-28 03:25:25','2025-07-28 03:25:25'),(689,18,'Urumita','2025-07-28 03:25:25','2025-07-28 03:25:25'),(690,18,'Villanueva','2025-07-28 03:25:25','2025-07-28 03:25:25'),(691,19,'Algarrobo','2025-07-28 03:25:25','2025-07-28 03:25:25'),(692,19,'Aracataca','2025-07-28 03:25:25','2025-07-28 03:25:25'),(693,19,'Ariguaní','2025-07-28 03:25:25','2025-07-28 03:25:25'),(694,19,'Cerro de San Antonio','2025-07-28 03:25:25','2025-07-28 03:25:25'),(695,19,'Chibolo','2025-07-28 03:25:25','2025-07-28 03:25:25'),(696,19,'Ciénaga','2025-07-28 03:25:25','2025-07-28 03:25:25'),(697,19,'Concordia','2025-07-28 03:25:25','2025-07-28 03:25:25'),(698,19,'El Banco','2025-07-28 03:25:25','2025-07-28 03:25:25'),(699,19,'El Piñón','2025-07-28 03:25:25','2025-07-28 03:25:25'),(700,19,'El Retén','2025-07-28 03:25:25','2025-07-28 03:25:25'),(701,19,'Fundación','2025-07-28 03:25:25','2025-07-28 03:25:25'),(702,19,'Guamal','2025-07-28 03:25:25','2025-07-28 03:25:25'),(703,19,'Nueva Granada','2025-07-28 03:25:25','2025-07-28 03:25:25'),(704,19,'Pedraza','2025-07-28 03:25:25','2025-07-28 03:25:25'),(705,19,'Pijiño del Carmen','2025-07-28 03:25:25','2025-07-28 03:25:25'),(706,19,'Pivijay','2025-07-28 03:25:25','2025-07-28 03:25:25'),(707,19,'Plato','2025-07-28 03:25:25','2025-07-28 03:25:25'),(708,19,'Pueblo Viejo','2025-07-28 03:25:25','2025-07-28 03:25:25'),(709,19,'Remolino','2025-07-28 03:25:25','2025-07-28 03:25:25'),(710,19,'Sabanas de San Ángel','2025-07-28 03:25:25','2025-07-28 03:25:25'),(711,19,'Salamina','2025-07-28 03:25:25','2025-07-28 03:25:25'),(712,19,'San Sebastián de Buenavista','2025-07-28 03:25:25','2025-07-28 03:25:25'),(713,19,'San Zenón','2025-07-28 03:25:25','2025-07-28 03:25:25'),(714,19,'Santa Ana','2025-07-28 03:25:25','2025-07-28 03:25:25'),(715,19,'Santa Bárbara de Pinto','2025-07-28 03:25:25','2025-07-28 03:25:25'),(716,19,'Santa Marta','2025-07-28 03:25:25','2025-07-28 03:25:25'),(717,19,'Sitionuevo','2025-07-28 03:25:25','2025-07-28 03:25:25'),(718,19,'Tenerife','2025-07-28 03:25:25','2025-07-28 03:25:25'),(719,19,'Zapayán','2025-07-28 03:25:25','2025-07-28 03:25:25'),(720,19,'Zona Bananera','2025-07-28 03:25:25','2025-07-28 03:25:25'),(721,20,'Acacías','2025-07-28 03:25:25','2025-07-28 03:25:25'),(722,20,'Barranca de Upía','2025-07-28 03:25:25','2025-07-28 03:25:25'),(723,20,'Cabuyaro','2025-07-28 03:25:25','2025-07-28 03:25:25'),(724,20,'Castilla la Nueva','2025-07-28 03:25:25','2025-07-28 03:25:25'),(725,20,'Cubarral','2025-07-28 03:25:25','2025-07-28 03:25:25'),(726,20,'Cumaral','2025-07-28 03:25:25','2025-07-28 03:25:25'),(727,20,'El Calvario','2025-07-28 03:25:25','2025-07-28 03:25:25'),(728,20,'El Castillo','2025-07-28 03:25:25','2025-07-28 03:25:25'),(729,20,'El Dorado','2025-07-28 03:25:25','2025-07-28 03:25:25'),(730,20,'Fuente de Oro','2025-07-28 03:25:25','2025-07-28 03:25:25'),(731,20,'Granada','2025-07-28 03:25:25','2025-07-28 03:25:25'),(732,20,'Guamal','2025-07-28 03:25:25','2025-07-28 03:25:25'),(733,20,'La Macarena','2025-07-28 03:25:25','2025-07-28 03:25:25'),(734,20,'La Uribe','2025-07-28 03:25:25','2025-07-28 03:25:25'),(735,20,'Lejanías','2025-07-28 03:25:25','2025-07-28 03:25:25'),(736,20,'Mapiripán','2025-07-28 03:25:25','2025-07-28 03:25:25'),(737,20,'Mesetas','2025-07-28 03:25:25','2025-07-28 03:25:25'),(738,20,'Puerto Concordia','2025-07-28 03:25:25','2025-07-28 03:25:25'),(739,20,'Puerto Gaitán','2025-07-28 03:25:25','2025-07-28 03:25:25'),(740,20,'Puerto Lleras','2025-07-28 03:25:25','2025-07-28 03:25:25'),(741,20,'Puerto López','2025-07-28 03:25:25','2025-07-28 03:25:25'),(742,20,'Puerto Rico','2025-07-28 03:25:25','2025-07-28 03:25:25'),(743,20,'Restrepo','2025-07-28 03:25:25','2025-07-28 03:25:25'),(744,20,'San Carlos de Guaroa','2025-07-28 03:25:25','2025-07-28 03:25:25'),(745,20,'San Juan de Arama','2025-07-28 03:25:25','2025-07-28 03:25:25'),(746,20,'San Juanito','2025-07-28 03:25:25','2025-07-28 03:25:25'),(747,20,'San Martín','2025-07-28 03:25:25','2025-07-28 03:25:25'),(748,20,'Villavicencio','2025-07-28 03:25:25','2025-07-28 03:25:25'),(749,20,'Vista Hermosa','2025-07-28 03:25:25','2025-07-28 03:25:25'),(750,21,'Aldana','2025-07-28 03:25:25','2025-07-28 03:25:25'),(751,21,'Ancuyá','2025-07-28 03:25:25','2025-07-28 03:25:25'),(752,21,'Arboleda','2025-07-28 03:25:25','2025-07-28 03:25:25'),(753,21,'Barbacoas','2025-07-28 03:25:25','2025-07-28 03:25:25'),(754,21,'Belén','2025-07-28 03:25:25','2025-07-28 03:25:25'),(755,21,'Buesaco','2025-07-28 03:25:25','2025-07-28 03:25:25'),(756,21,'Chachagüí','2025-07-28 03:25:25','2025-07-28 03:25:25'),(757,21,'Colón','2025-07-28 03:25:25','2025-07-28 03:25:25'),(758,21,'Consacá','2025-07-28 03:25:25','2025-07-28 03:25:25'),(759,21,'Contadero','2025-07-28 03:25:25','2025-07-28 03:25:25'),(760,21,'Córdoba','2025-07-28 03:25:25','2025-07-28 03:25:25'),(761,21,'Cuaspud','2025-07-28 03:25:25','2025-07-28 03:25:25'),(762,21,'Cumbal','2025-07-28 03:25:25','2025-07-28 03:25:25'),(763,21,'Cumbitara','2025-07-28 03:25:25','2025-07-28 03:25:25'),(764,21,'El Charco','2025-07-28 03:25:25','2025-07-28 03:25:25'),(765,21,'El Peñol','2025-07-28 03:25:25','2025-07-28 03:25:25'),(766,21,'El Rosario','2025-07-28 03:25:25','2025-07-28 03:25:25'),(767,21,'El Tablón','2025-07-28 03:25:25','2025-07-28 03:25:25'),(768,21,'El Tambo','2025-07-28 03:25:25','2025-07-28 03:25:25'),(769,21,'Francisco Pizarro','2025-07-28 03:25:25','2025-07-28 03:25:25'),(770,21,'Funes','2025-07-28 03:25:25','2025-07-28 03:25:25'),(771,21,'Guachucal','2025-07-28 03:25:25','2025-07-28 03:25:25'),(772,21,'Guaitarilla','2025-07-28 03:25:25','2025-07-28 03:25:25'),(773,21,'Gualmatán','2025-07-28 03:25:25','2025-07-28 03:25:25'),(774,21,'Iles','2025-07-28 03:25:25','2025-07-28 03:25:25'),(775,21,'Imués','2025-07-28 03:25:25','2025-07-28 03:25:25'),(776,21,'Ipiales','2025-07-28 03:25:25','2025-07-28 03:25:25'),(777,21,'La Cruz','2025-07-28 03:25:25','2025-07-28 03:25:25'),(778,21,'La Florida','2025-07-28 03:25:25','2025-07-28 03:25:25'),(779,21,'La Llanada','2025-07-28 03:25:25','2025-07-28 03:25:25'),(780,21,'La Tola','2025-07-28 03:25:25','2025-07-28 03:25:25'),(781,21,'La Unión','2025-07-28 03:25:25','2025-07-28 03:25:25'),(782,21,'Leiva','2025-07-28 03:25:25','2025-07-28 03:25:25'),(783,21,'Linares','2025-07-28 03:25:25','2025-07-28 03:25:25'),(784,21,'Los Andes','2025-07-28 03:25:25','2025-07-28 03:25:25'),(785,21,'Magüí Payán','2025-07-28 03:25:25','2025-07-28 03:25:25'),(786,21,'Mallama','2025-07-28 03:25:25','2025-07-28 03:25:25'),(787,21,'Mosquera','2025-07-28 03:25:25','2025-07-28 03:25:25'),(788,21,'Nariño','2025-07-28 03:25:25','2025-07-28 03:25:25'),(789,21,'Olaya Herrera','2025-07-28 03:25:25','2025-07-28 03:25:25'),(790,21,'Ospina','2025-07-28 03:25:25','2025-07-28 03:25:25'),(791,21,'Pasto','2025-07-28 03:25:25','2025-07-28 03:25:25'),(792,21,'Policarpa','2025-07-28 03:25:25','2025-07-28 03:25:25'),(793,21,'Potosí','2025-07-28 03:25:25','2025-07-28 03:25:25'),(794,21,'Providencia','2025-07-28 03:25:25','2025-07-28 03:25:25'),(795,21,'Puerres','2025-07-28 03:25:25','2025-07-28 03:25:25'),(796,21,'Pupiales','2025-07-28 03:25:25','2025-07-28 03:25:25'),(797,21,'Ricaurte','2025-07-28 03:25:25','2025-07-28 03:25:25'),(798,21,'Roberto Payán','2025-07-28 03:25:25','2025-07-28 03:25:25'),(799,21,'Samaniego','2025-07-28 03:25:25','2025-07-28 03:25:25'),(800,21,'San Bernardo','2025-07-28 03:25:25','2025-07-28 03:25:25'),(801,21,'San José de Albán','2025-07-28 03:25:25','2025-07-28 03:25:25'),(802,21,'San Lorenzo','2025-07-28 03:25:25','2025-07-28 03:25:25'),(803,21,'San Pablo','2025-07-28 03:25:25','2025-07-28 03:25:25'),(804,21,'San Pedro de Cartago','2025-07-28 03:25:25','2025-07-28 03:25:25'),(805,21,'Sandoná','2025-07-28 03:25:25','2025-07-28 03:25:25'),(806,21,'Santa Bárbara','2025-07-28 03:25:25','2025-07-28 03:25:25'),(807,21,'Santacruz','2025-07-28 03:25:25','2025-07-28 03:25:25'),(808,21,'Sapuyes','2025-07-28 03:25:25','2025-07-28 03:25:25'),(809,21,'Taminango','2025-07-28 03:25:25','2025-07-28 03:25:25'),(810,21,'Tangua','2025-07-28 03:25:25','2025-07-28 03:25:25'),(811,21,'Tumaco','2025-07-28 03:25:25','2025-07-28 03:25:25'),(812,21,'Túquerres','2025-07-28 03:25:25','2025-07-28 03:25:25'),(813,21,'Yacuanquer','2025-07-28 03:25:25','2025-07-28 03:25:25'),(814,22,'Ábrego','2025-07-28 03:25:25','2025-07-28 03:25:25'),(815,22,'Arboledas','2025-07-28 03:25:25','2025-07-28 03:25:25'),(816,22,'Bochalema','2025-07-28 03:25:25','2025-07-28 03:25:25'),(817,22,'Bucarasica','2025-07-28 03:25:25','2025-07-28 03:25:25'),(818,22,'Cáchira','2025-07-28 03:25:26','2025-07-28 03:25:26'),(819,22,'Cácota','2025-07-28 03:25:26','2025-07-28 03:25:26'),(820,22,'Chinácota','2025-07-28 03:25:26','2025-07-28 03:25:26'),(821,22,'Chitagá','2025-07-28 03:25:26','2025-07-28 03:25:26'),(822,22,'Convención','2025-07-28 03:25:26','2025-07-28 03:25:26'),(823,22,'Cúcuta','2025-07-28 03:25:26','2025-07-28 03:25:26'),(824,22,'Cucutilla','2025-07-28 03:25:26','2025-07-28 03:25:26'),(825,22,'Duranía','2025-07-28 03:25:26','2025-07-28 03:25:26'),(826,22,'El Carmen','2025-07-28 03:25:26','2025-07-28 03:25:26'),(827,22,'El Tarra','2025-07-28 03:25:26','2025-07-28 03:25:26'),(828,22,'El Zulia','2025-07-28 03:25:26','2025-07-28 03:25:26'),(829,22,'Gramalote','2025-07-28 03:25:26','2025-07-28 03:25:26'),(830,22,'Hacarí','2025-07-28 03:25:26','2025-07-28 03:25:26'),(831,22,'Herrán','2025-07-28 03:25:26','2025-07-28 03:25:26'),(832,22,'La Esperanza','2025-07-28 03:25:26','2025-07-28 03:25:26'),(833,22,'La Playa de Belén','2025-07-28 03:25:26','2025-07-28 03:25:26'),(834,22,'Labateca','2025-07-28 03:25:26','2025-07-28 03:25:26'),(835,22,'Los Patios','2025-07-28 03:25:26','2025-07-28 03:25:26'),(836,22,'Lourdes','2025-07-28 03:25:26','2025-07-28 03:25:26'),(837,22,'Mutiscua','2025-07-28 03:25:26','2025-07-28 03:25:26'),(838,22,'Ocaña','2025-07-28 03:25:26','2025-07-28 03:25:26'),(839,22,'Pamplona','2025-07-28 03:25:26','2025-07-28 03:25:26'),(840,22,'Pamplonita','2025-07-28 03:25:26','2025-07-28 03:25:26'),(841,22,'Puerto Santander','2025-07-28 03:25:26','2025-07-28 03:25:26'),(842,22,'Ragonvalia','2025-07-28 03:25:26','2025-07-28 03:25:26'),(843,22,'Salazar de Las Palmas','2025-07-28 03:25:26','2025-07-28 03:25:26'),(844,22,'San Calixto','2025-07-28 03:25:26','2025-07-28 03:25:26'),(845,22,'San Cayetano','2025-07-28 03:25:26','2025-07-28 03:25:26'),(846,22,'Santiago','2025-07-28 03:25:26','2025-07-28 03:25:26'),(847,22,'Santo Domingo de Silos','2025-07-28 03:25:26','2025-07-28 03:25:26'),(848,22,'Sardinata','2025-07-28 03:25:26','2025-07-28 03:25:26'),(849,22,'Teorama','2025-07-28 03:25:26','2025-07-28 03:25:26'),(850,22,'Tibú','2025-07-28 03:25:26','2025-07-28 03:25:26'),(851,22,'Toledo','2025-07-28 03:25:26','2025-07-28 03:25:26'),(852,22,'Villa Caro','2025-07-28 03:25:26','2025-07-28 03:25:26'),(853,22,'Villa del Rosario','2025-07-28 03:25:26','2025-07-28 03:25:26'),(854,23,'Colón','2025-07-28 03:25:26','2025-07-28 03:25:26'),(855,23,'Mocoa','2025-07-28 03:25:26','2025-07-28 03:25:26'),(856,23,'Orito','2025-07-28 03:25:26','2025-07-28 03:25:26'),(857,23,'Puerto Asís','2025-07-28 03:25:26','2025-07-28 03:25:26'),(858,23,'Puerto Caicedo','2025-07-28 03:25:26','2025-07-28 03:25:26'),(859,23,'Puerto Guzmán','2025-07-28 03:25:26','2025-07-28 03:25:26'),(860,23,'Puerto Leguízamo','2025-07-28 03:25:26','2025-07-28 03:25:26'),(861,23,'San Francisco','2025-07-28 03:25:26','2025-07-28 03:25:26'),(862,23,'San Miguel','2025-07-28 03:25:26','2025-07-28 03:25:26'),(863,23,'Santiago','2025-07-28 03:25:26','2025-07-28 03:25:26'),(864,23,'Sibundoy','2025-07-28 03:25:26','2025-07-28 03:25:26'),(865,23,'Valle del Guamuez','2025-07-28 03:25:26','2025-07-28 03:25:26'),(866,23,'Villagarzón','2025-07-28 03:25:26','2025-07-28 03:25:26'),(867,24,'Armenia','2025-07-28 03:25:26','2025-07-28 03:25:26'),(868,24,'Buenavista','2025-07-28 03:25:26','2025-07-28 03:25:26'),(869,24,'Calarcá','2025-07-28 03:25:26','2025-07-28 03:25:26'),(870,24,'Circasia','2025-07-28 03:25:26','2025-07-28 03:25:26'),(871,24,'Córdoba','2025-07-28 03:25:26','2025-07-28 03:25:26'),(872,24,'Filandia','2025-07-28 03:25:26','2025-07-28 03:25:26'),(873,24,'Génova','2025-07-28 03:25:26','2025-07-28 03:25:26'),(874,24,'La Tebaida','2025-07-28 03:25:26','2025-07-28 03:25:26'),(875,24,'Montenegro','2025-07-28 03:25:26','2025-07-28 03:25:26'),(876,24,'Pijao','2025-07-28 03:25:26','2025-07-28 03:25:26'),(877,24,'Quimbaya','2025-07-28 03:25:26','2025-07-28 03:25:26'),(878,24,'Salento','2025-07-28 03:25:26','2025-07-28 03:25:26'),(879,25,'Apía','2025-07-28 03:25:26','2025-07-28 03:25:26'),(880,25,'Balboa','2025-07-28 03:25:26','2025-07-28 03:25:26'),(881,25,'Belén de Umbría','2025-07-28 03:25:26','2025-07-28 03:25:26'),(882,25,'Dosquebradas','2025-07-28 03:25:26','2025-07-28 03:25:26'),(883,25,'Guática','2025-07-28 03:25:26','2025-07-28 03:25:26'),(884,25,'La Celia','2025-07-28 03:25:26','2025-07-28 03:25:26'),(885,25,'La Virginia','2025-07-28 03:25:26','2025-07-28 03:25:26'),(886,25,'Marsella','2025-07-28 03:25:26','2025-07-28 03:25:26'),(887,25,'Mistrató','2025-07-28 03:25:26','2025-07-28 03:25:26'),(888,25,'Pereira','2025-07-28 03:25:26','2025-07-28 03:25:26'),(889,25,'Pueblo Rico','2025-07-28 03:25:26','2025-07-28 03:25:26'),(890,25,'Quinchía','2025-07-28 03:25:26','2025-07-28 03:25:26'),(891,25,'Santa Rosa de Cabal','2025-07-28 03:25:26','2025-07-28 03:25:26'),(892,25,'Santuario','2025-07-28 03:25:26','2025-07-28 03:25:26'),(893,26,'Providencia y Santa Catalina Islas','2025-07-28 03:25:26','2025-07-28 03:25:26'),(894,26,'San Andrés','2025-07-28 03:25:26','2025-07-28 03:25:26'),(895,27,'Aguada','2025-07-28 03:25:26','2025-07-28 03:25:26'),(896,27,'Albania','2025-07-28 03:25:26','2025-07-28 03:25:26'),(897,27,'Aratoca','2025-07-28 03:25:26','2025-07-28 03:25:26'),(898,27,'Barbosa','2025-07-28 03:25:26','2025-07-28 03:25:26'),(899,27,'Barichara','2025-07-28 03:25:26','2025-07-28 03:25:26'),(900,27,'Barrancabermeja','2025-07-28 03:25:26','2025-07-28 03:25:26'),(901,27,'Betulia','2025-07-28 03:25:26','2025-07-28 03:25:26'),(902,27,'Bolívar','2025-07-28 03:25:26','2025-07-28 03:25:26'),(903,27,'Bucaramanga','2025-07-28 03:25:26','2025-07-28 03:25:26'),(904,27,'Cabrera','2025-07-28 03:25:26','2025-07-28 03:25:26'),(905,27,'California','2025-07-28 03:25:26','2025-07-28 03:25:26'),(906,27,'Capitanejo','2025-07-28 03:25:26','2025-07-28 03:25:26'),(907,27,'Carcasí','2025-07-28 03:25:26','2025-07-28 03:25:26'),(908,27,'Cepitá','2025-07-28 03:25:26','2025-07-28 03:25:26'),(909,27,'Cerrito','2025-07-28 03:25:26','2025-07-28 03:25:26'),(910,27,'Charalá','2025-07-28 03:25:26','2025-07-28 03:25:26'),(911,27,'Charta','2025-07-28 03:25:26','2025-07-28 03:25:26'),(912,27,'Chima','2025-07-28 03:25:26','2025-07-28 03:25:26'),(913,27,'Chipatá','2025-07-28 03:25:26','2025-07-28 03:25:26'),(914,27,'Cimitarra','2025-07-28 03:25:26','2025-07-28 03:25:26'),(915,27,'Concepción','2025-07-28 03:25:26','2025-07-28 03:25:26'),(916,27,'Confines','2025-07-28 03:25:26','2025-07-28 03:25:26'),(917,27,'Contratación','2025-07-28 03:25:26','2025-07-28 03:25:26'),(918,27,'Coromoro','2025-07-28 03:25:26','2025-07-28 03:25:26'),(919,27,'Curití','2025-07-28 03:25:26','2025-07-28 03:25:26'),(920,27,'El Carmen de Chucurí','2025-07-28 03:25:26','2025-07-28 03:25:26'),(921,27,'El Guacamayo','2025-07-28 03:25:26','2025-07-28 03:25:26'),(922,27,'El Peñón','2025-07-28 03:25:26','2025-07-28 03:25:26'),(923,27,'El Playón','2025-07-28 03:25:26','2025-07-28 03:25:26'),(924,27,'El Socorro','2025-07-28 03:25:26','2025-07-28 03:25:26'),(925,27,'Encino','2025-07-28 03:25:26','2025-07-28 03:25:26'),(926,27,'Enciso','2025-07-28 03:25:26','2025-07-28 03:25:26'),(927,27,'Florián','2025-07-28 03:25:26','2025-07-28 03:25:26'),(928,27,'Floridablanca','2025-07-28 03:25:26','2025-07-28 03:25:26'),(929,27,'Galán','2025-07-28 03:25:26','2025-07-28 03:25:26'),(930,27,'Gámbita','2025-07-28 03:25:26','2025-07-28 03:25:26'),(931,27,'Girón','2025-07-28 03:25:26','2025-07-28 03:25:26'),(932,27,'Guaca','2025-07-28 03:25:26','2025-07-28 03:25:26'),(933,27,'Guadalupe','2025-07-28 03:25:26','2025-07-28 03:25:26'),(934,27,'Guapotá','2025-07-28 03:25:26','2025-07-28 03:25:26'),(935,27,'Guavatá','2025-07-28 03:25:26','2025-07-28 03:25:26'),(936,27,'Güepsa','2025-07-28 03:25:26','2025-07-28 03:25:26'),(937,27,'Hato','2025-07-28 03:25:26','2025-07-28 03:25:26'),(938,27,'Jesús María','2025-07-28 03:25:26','2025-07-28 03:25:26'),(939,27,'Jordán','2025-07-28 03:25:26','2025-07-28 03:25:26'),(940,27,'La Belleza','2025-07-28 03:25:26','2025-07-28 03:25:26'),(941,27,'La Paz','2025-07-28 03:25:26','2025-07-28 03:25:26'),(942,27,'Landázuri','2025-07-28 03:25:26','2025-07-28 03:25:26'),(943,27,'Lebrija','2025-07-28 03:25:26','2025-07-28 03:25:26'),(944,27,'Los Santos','2025-07-28 03:25:26','2025-07-28 03:25:26'),(945,27,'Macaravita','2025-07-28 03:25:26','2025-07-28 03:25:26'),(946,27,'Málaga','2025-07-28 03:25:26','2025-07-28 03:25:26'),(947,27,'Matanza','2025-07-28 03:25:26','2025-07-28 03:25:26'),(948,27,'Mogotes','2025-07-28 03:25:26','2025-07-28 03:25:26'),(949,27,'Molagavita','2025-07-28 03:25:26','2025-07-28 03:25:26'),(950,27,'Ocamonte','2025-07-28 03:25:26','2025-07-28 03:25:26'),(951,27,'Oiba','2025-07-28 03:25:26','2025-07-28 03:25:26'),(952,27,'Onzaga','2025-07-28 03:25:26','2025-07-28 03:25:26'),(953,27,'Palmar','2025-07-28 03:25:26','2025-07-28 03:25:26'),(954,27,'Palmas del Socorro','2025-07-28 03:25:26','2025-07-28 03:25:26'),(955,27,'Páramo','2025-07-28 03:25:26','2025-07-28 03:25:26'),(956,27,'Piedecuesta','2025-07-28 03:25:26','2025-07-28 03:25:26'),(957,27,'Pinchote','2025-07-28 03:25:26','2025-07-28 03:25:26'),(958,27,'Puente Nacional','2025-07-28 03:25:26','2025-07-28 03:25:26'),(959,27,'Puerto Parra','2025-07-28 03:25:26','2025-07-28 03:25:26'),(960,27,'Puerto Wilches','2025-07-28 03:25:26','2025-07-28 03:25:26'),(961,27,'Rionegro','2025-07-28 03:25:26','2025-07-28 03:25:26'),(962,27,'Sabana de Torres','2025-07-28 03:25:26','2025-07-28 03:25:26'),(963,27,'San Andrés','2025-07-28 03:25:26','2025-07-28 03:25:26'),(964,27,'San Benito','2025-07-28 03:25:26','2025-07-28 03:25:26'),(965,27,'San Gil','2025-07-28 03:25:26','2025-07-28 03:25:26'),(966,27,'San Joaquín','2025-07-28 03:25:26','2025-07-28 03:25:26'),(967,27,'San José de Miranda','2025-07-28 03:25:26','2025-07-28 03:25:26'),(968,27,'San Miguel','2025-07-28 03:25:26','2025-07-28 03:25:26'),(969,27,'San Vicente de Chucurí','2025-07-28 03:25:26','2025-07-28 03:25:26'),(970,27,'Santa Bárbara','2025-07-28 03:25:26','2025-07-28 03:25:26'),(971,27,'Santa Helena del Opón','2025-07-28 03:25:26','2025-07-28 03:25:26'),(972,27,'Simacota','2025-07-28 03:25:26','2025-07-28 03:25:26'),(973,27,'Suaita','2025-07-28 03:25:26','2025-07-28 03:25:26'),(974,27,'Sucre','2025-07-28 03:25:26','2025-07-28 03:25:26'),(975,27,'Suratá','2025-07-28 03:25:26','2025-07-28 03:25:26'),(976,27,'Tona','2025-07-28 03:25:26','2025-07-28 03:25:26'),(977,27,'Valle de San José','2025-07-28 03:25:26','2025-07-28 03:25:26'),(978,27,'Vélez','2025-07-28 03:25:26','2025-07-28 03:25:26'),(979,27,'Vetas','2025-07-28 03:25:26','2025-07-28 03:25:26'),(980,27,'Villanueva','2025-07-28 03:25:26','2025-07-28 03:25:26'),(981,27,'Zapatoca','2025-07-28 03:25:26','2025-07-28 03:25:26'),(982,28,'Buenavista','2025-07-28 03:25:26','2025-07-28 03:25:26'),(983,28,'Caimito','2025-07-28 03:25:26','2025-07-28 03:25:26'),(984,28,'Chalán','2025-07-28 03:25:26','2025-07-28 03:25:26'),(985,28,'Colosó','2025-07-28 03:25:26','2025-07-28 03:25:26'),(986,28,'Corozal','2025-07-28 03:25:26','2025-07-28 03:25:26'),(987,28,'Coveñas','2025-07-28 03:25:26','2025-07-28 03:25:26'),(988,28,'El Roble','2025-07-28 03:25:26','2025-07-28 03:25:26'),(989,28,'Galeras','2025-07-28 03:25:26','2025-07-28 03:25:26'),(990,28,'Guaranda','2025-07-28 03:25:26','2025-07-28 03:25:26'),(991,28,'La Unión','2025-07-28 03:25:26','2025-07-28 03:25:26'),(992,28,'Los Palmitos','2025-07-28 03:25:26','2025-07-28 03:25:26'),(993,28,'Majagual','2025-07-28 03:25:27','2025-07-28 03:25:27'),(994,28,'Morroa','2025-07-28 03:25:27','2025-07-28 03:25:27'),(995,28,'Ovejas','2025-07-28 03:25:27','2025-07-28 03:25:27'),(996,28,'Sampués','2025-07-28 03:25:27','2025-07-28 03:25:27'),(997,28,'San Antonio de Palmito','2025-07-28 03:25:27','2025-07-28 03:25:27'),(998,28,'San Benito Abad','2025-07-28 03:25:27','2025-07-28 03:25:27'),(999,28,'San Juan de Betulia','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1000,28,'San Marcos','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1001,28,'San Onofre','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1002,28,'San Pedro','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1003,28,'Sincé','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1004,28,'Sincelejo','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1005,28,'Sucre','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1006,28,'Tolú','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1007,28,'Tolú Viejo','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1008,29,'Alpujarra','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1009,29,'Alvarado','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1010,29,'Ambalema','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1011,29,'Anzoátegui','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1012,29,'Armero','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1013,29,'Ataco','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1014,29,'Cajamarca','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1015,29,'Carmen de Apicalá','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1016,29,'Casabianca','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1017,29,'Chaparral','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1018,29,'Coello','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1019,29,'Coyaima','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1020,29,'Cunday','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1021,29,'Dolores','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1022,29,'El Espinal','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1023,29,'Falán','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1024,29,'Flandes','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1025,29,'Fresno','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1026,29,'Guamo','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1027,29,'Herveo','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1028,29,'Honda','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1029,29,'Ibagué','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1030,29,'Icononzo','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1031,29,'Lérida','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1032,29,'Líbano','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1033,29,'Mariquita','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1034,29,'Melgar','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1035,29,'Murillo','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1036,29,'Natagaima','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1037,29,'Ortega','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1038,29,'Palocabildo','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1039,29,'Piedras','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1040,29,'Planadas','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1041,29,'Prado','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1042,29,'Purificación','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1043,29,'Rioblanco','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1044,29,'Roncesvalles','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1045,29,'Rovira','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1046,29,'Saldaña','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1047,29,'San Antonio','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1048,29,'San Luis','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1049,29,'Santa Isabel','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1050,29,'Suárez','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1051,29,'Valle de San Juan','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1052,29,'Venadillo','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1053,29,'Villahermosa','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1054,29,'Villarrica','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1055,30,'Alcalá','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1056,30,'Andalucía','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1057,30,'Ansermanuevo','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1058,30,'Argelia','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1059,30,'Bolívar','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1060,30,'Buenaventura','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1061,30,'Buga','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1062,30,'Bugalagrande','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1063,30,'Caicedonia','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1064,30,'Cali','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1065,30,'Calima','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1066,30,'Candelaria','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1067,30,'Cartago','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1068,30,'Dagua','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1069,30,'El Águila','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1070,30,'El Cairo','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1071,30,'El Cerrito','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1072,30,'El Dovio','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1073,30,'Florida','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1074,30,'Ginebra','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1075,30,'Guacarí','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1076,30,'Jamundí','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1077,30,'La Cumbre','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1078,30,'La Unión','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1079,30,'La Victoria','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1080,30,'Obando','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1081,30,'Palmira','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1082,30,'Pradera','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1083,30,'Restrepo','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1084,30,'Riofrío','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1085,30,'Roldanillo','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1086,30,'San Pedro','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1087,30,'Sevilla','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1088,30,'Toro','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1089,30,'Trujillo','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1090,30,'Tuluá','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1091,30,'Ulloa','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1092,30,'Versalles','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1093,30,'Vijes','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1094,30,'Yotoco','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1095,30,'Yumbo','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1096,30,'Zarzal','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1097,31,'Carurú','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1098,31,'Mitú','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1099,31,'Taraira','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1100,32,'Cumaribo','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1101,32,'La Primavera','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1102,32,'Puerto Carreño','2025-07-28 03:25:27','2025-07-28 03:25:27'),(1103,32,'Santa Rosalía','2025-07-28 03:25:27','2025-07-28 03:25:27');
/*!40000 ALTER TABLE `ciudades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clientes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint(20) unsigned DEFAULT NULL,
  `numero_identificacion` varchar(255) NOT NULL,
  `nombre_contacto` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefono` varchar(255) DEFAULT NULL,
  `pais_id` bigint(20) unsigned NOT NULL,
  `ciudad_id` bigint(20) unsigned NOT NULL,
  `vendedor_id` bigint(20) unsigned NOT NULL,
  `lista_precio_id` bigint(20) unsigned NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clientes_numero_identificacion_unique` (`numero_identificacion`),
  KEY `clientes_lista_precio_id_foreign` (`lista_precio_id`),
  KEY `clientes_vendedor_id_activo_index` (`vendedor_id`,`activo`),
  KEY `clientes_email_index` (`email`),
  KEY `clientes_pais_id_foreign` (`pais_id`),
  KEY `clientes_ciudad_id_foreign` (`ciudad_id`),
  KEY `clientes_empresa_id_activo_index` (`empresa_id`,`activo`),
  CONSTRAINT `clientes_ciudad_id_foreign` FOREIGN KEY (`ciudad_id`) REFERENCES `ciudades` (`id`) ON DELETE CASCADE,
  CONSTRAINT `clientes_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `clientes_lista_precio_id_foreign` FOREIGN KEY (`lista_precio_id`) REFERENCES `listas_precios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `clientes_pais_id_foreign` FOREIGN KEY (`pais_id`) REFERENCES `paises` (`id`) ON DELETE CASCADE,
  CONSTRAINT `clientes_vendedor_id_foreign` FOREIGN KEY (`vendedor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comisiones`
--

DROP TABLE IF EXISTS `comisiones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comisiones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint(20) unsigned NOT NULL,
  `compra_id` bigint(20) unsigned NOT NULL,
  `monto_venta` decimal(12,2) NOT NULL,
  `porcentaje_comision` decimal(5,2) NOT NULL,
  `monto_comision` decimal(12,2) NOT NULL,
  `monto_empresa` decimal(12,2) NOT NULL,
  `estado` enum('pendiente','procesada','pagada') NOT NULL DEFAULT 'pendiente',
  `fecha_pago` date DEFAULT NULL,
  `referencia_pago` varchar(255) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `comisiones_compra_id_unique` (`compra_id`),
  KEY `comisiones_empresa_id_estado_created_at_index` (`empresa_id`,`estado`,`created_at`),
  KEY `comisiones_compra_id_index` (`compra_id`),
  CONSTRAINT `comisiones_compra_id_foreign` FOREIGN KEY (`compra_id`) REFERENCES `compras` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comisiones_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comisiones`
--

LOCK TABLES `comisiones` WRITE;
/*!40000 ALTER TABLE `comisiones` DISABLE KEYS */;
INSERT INTO `comisiones` VALUES (9,5,16,500000.00,6.09,31350.00,468650.00,'pendiente',NULL,NULL,'Comisión: 6.09% + $900','2025-11-30 11:38:29','2025-11-30 11:38:29'),(10,5,18,500000.00,6.09,31350.00,468650.00,'pendiente',NULL,NULL,'Comisión: 6.09% + $900','2025-12-01 09:32:16','2025-12-01 09:32:16'),(11,5,23,380000.00,6.09,24042.00,355958.00,'pendiente',NULL,NULL,'Comisión: 6.09% + $900','2026-01-06 02:12:04','2026-01-06 02:12:04');
/*!40000 ALTER TABLE `comisiones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `compras`
--

DROP TABLE IF EXISTS `compras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `compras` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `numero_compra` varchar(255) NOT NULL,
  `empresa_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `nombre_cliente` varchar(255) NOT NULL,
  `email_cliente` varchar(255) NOT NULL,
  `telefono_cliente` varchar(255) NOT NULL,
  `direccion_envio` varchar(255) DEFAULT NULL,
  `ciudad_id` bigint(20) unsigned DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `descuento_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `descuentos_aplicados` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`descuentos_aplicados`)),
  `impuestos` decimal(12,2) NOT NULL DEFAULT 0.00,
  `costo_envio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL,
  `estado` enum('pendiente','procesando','pagada','enviada','entregada','cancelada','reembolsada') NOT NULL DEFAULT 'pendiente',
  `metodo_pago` enum('wompi','otro') NOT NULL DEFAULT 'wompi',
  `mensaje_pago` text DEFAULT NULL,
  `archivo_pago` varchar(255) DEFAULT NULL,
  `motivo_rechazo` text DEFAULT NULL,
  `fecha_revision` timestamp NULL DEFAULT NULL,
  `revisado_por` bigint(20) unsigned DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `compras_numero_compra_unique` (`numero_compra`),
  KEY `compras_ciudad_id_foreign` (`ciudad_id`),
  KEY `compras_empresa_id_estado_created_at_index` (`empresa_id`,`estado`,`created_at`),
  KEY `compras_numero_compra_index` (`numero_compra`),
  KEY `compras_user_id_foreign` (`user_id`),
  KEY `compras_revisado_por_foreign` (`revisado_por`),
  KEY `compras_empresa_id_metodo_pago_estado_index` (`empresa_id`,`metodo_pago`,`estado`),
  CONSTRAINT `compras_ciudad_id_foreign` FOREIGN KEY (`ciudad_id`) REFERENCES `ciudades` (`id`) ON DELETE SET NULL,
  CONSTRAINT `compras_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `compras_revisado_por_foreign` FOREIGN KEY (`revisado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `compras_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compras`
--

LOCK TABLES `compras` WRITE;
/*!40000 ALTER TABLE `compras` DISABLE KEYS */;
INSERT INTO `compras` VALUES (16,'ORD-20251130113754-YBQ0',5,14,'Empresa Usuario 1','vblogsanti@gmail.com','3202230467','Calle 69 #10-15',502,500000.00,0.00,'[]',0.00,0.00,500000.00,'pagada','wompi',NULL,NULL,NULL,NULL,NULL,NULL,'2025-11-30 11:37:54','2025-12-11 19:35:55'),(17,'ORD-20251201092808-8AGC',5,NULL,'michael cardenas','michcardenas001@hotmail.com','8070417','cr',167,500000.00,0.00,'[]',0.00,0.00,500000.00,'pendiente','wompi',NULL,NULL,NULL,NULL,NULL,'que el paquete quede en la esquina de mio casa','2025-12-01 09:28:08','2025-12-01 09:28:08'),(18,'ORD-20251201093131-YN37',5,NULL,'michael cardenas','michcardenas01@hotmail.com','8070417','cr 54 54 54',499,500000.00,0.00,'[]',0.00,0.00,500000.00,'enviada','wompi',NULL,NULL,NULL,NULL,NULL,'Entregar en la esquina','2025-12-01 09:31:31','2025-12-01 09:34:46'),(19,'ORD-20251201221232-IOHP',5,NULL,'jjjjj','mich@mich.com','3024899201','clle45',616,500000.00,0.00,'[]',0.00,0.00,500000.00,'pendiente','wompi',NULL,NULL,NULL,NULL,NULL,'noaas','2025-12-01 22:12:32','2025-12-01 22:12:32'),(20,'ORD-20251209211954-ALZV',5,NULL,'esteban salazar','saljuanes167@gmail.com','3225873014','Cl 48B Cr 50 - 80',48,500000.00,0.00,'[]',0.00,0.00,500000.00,'pendiente','wompi',NULL,NULL,NULL,NULL,NULL,NULL,'2025-12-09 21:19:54','2025-12-09 21:19:54'),(21,'ORD-20251209214314-LXIF',5,NULL,'esteban salazar','saljuanes167@gmail.com','3225873014','Cl 48B Cr 50 - 80',48,210000.00,0.00,'[]',0.00,0.00,210000.00,'pendiente','wompi',NULL,NULL,NULL,NULL,NULL,NULL,'2025-12-09 21:43:14','2025-12-09 21:43:14'),(22,'ORD-20251210191031-ZRWP',5,NULL,'Jesus pana ipuana','chuchopanita@gmail.com','3107424865','Calle 3 sur carra 10 esquina',687,210000.00,0.00,'[]',0.00,0.00,210000.00,'pendiente','wompi',NULL,NULL,NULL,NULL,NULL,'Estación de Policía san Juan','2025-12-10 19:10:31','2025-12-10 19:10:31'),(23,'ORD-20260105211016-OBXF',5,NULL,'PEPITO PEREZ','vblogsanti@gmail.com','3202230467','Calle 69 #10-15',139,380000.00,0.00,'[]',0.00,0.00,380000.00,'pagada','otro','iok','pagos/5/23/1767665416_no-image.png',NULL,'2026-01-06 02:12:04',1,NULL,'2026-01-06 02:10:16','2026-01-06 02:12:04');
/*!40000 ALTER TABLE `compras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configuracion_pasarela`
--

DROP TABLE IF EXISTS `configuracion_pasarela`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configuracion_pasarela` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pasarela` varchar(1000) NOT NULL DEFAULT 'wompi',
  `public_key` varchar(1000) DEFAULT NULL,
  `private_key` varchar(1000) DEFAULT NULL,
  `event_key` varchar(1000) DEFAULT NULL,
  `webhook_url` varchar(1000) DEFAULT NULL,
  `modo_prueba` tinyint(1) NOT NULL DEFAULT 1,
  `configuracion_adicional` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`configuracion_adicional`)),
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `configuracion_pasarela_pasarela_index` (`pasarela`(768))
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configuracion_pasarela`
--

LOCK TABLES `configuracion_pasarela` WRITE;
/*!40000 ALTER TABLE `configuracion_pasarela` DISABLE KEYS */;
INSERT INTO `configuracion_pasarela` VALUES (1,'wompi','pub_prod_euKJtwfrodi5bHn1N1br8rpPMqIIrzkH','prv_prod_6AyU6ZsqFIxrOeDLzBniRyyOlt5YWMxZ','prod_events_zRBsIBVDYUYcFwS23CQWemRx6tCHkZjZ','https://esnovamarket.com/webhooks/wompi',0,'{\"integrity_key\":\"prod_integrity_aNUjR6y6aOdqSsCrsrMdYPH3F5m4rW7G\"}',1,'2025-08-26 00:11:31','2025-08-26 00:11:31');
/*!40000 ALTER TABLE `configuracion_pasarela` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departamentos`
--

DROP TABLE IF EXISTS `departamentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departamentos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pais_id` bigint(20) unsigned NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `departamentos_pais_id_nombre_unique` (`pais_id`,`nombre`),
  CONSTRAINT `departamentos_pais_id_foreign` FOREIGN KEY (`pais_id`) REFERENCES `paises` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departamentos`
--

LOCK TABLES `departamentos` WRITE;
/*!40000 ALTER TABLE `departamentos` DISABLE KEYS */;
INSERT INTO `departamentos` VALUES (1,1,'Amazonas','2025-07-28 03:25:19','2025-07-28 03:25:19'),(2,1,'Antioquia','2025-07-28 03:25:19','2025-07-28 03:25:19'),(3,1,'Arauca','2025-07-28 03:25:19','2025-07-28 03:25:19'),(4,1,'Atlántico','2025-07-28 03:25:19','2025-07-28 03:25:19'),(5,1,'Bolívar','2025-07-28 03:25:19','2025-07-28 03:25:19'),(6,1,'Boyacá','2025-07-28 03:25:19','2025-07-28 03:25:19'),(7,1,'Caldas','2025-07-28 03:25:19','2025-07-28 03:25:19'),(8,1,'Caquetá','2025-07-28 03:25:19','2025-07-28 03:25:19'),(9,1,'Casanare','2025-07-28 03:25:19','2025-07-28 03:25:19'),(10,1,'Cauca','2025-07-28 03:25:19','2025-07-28 03:25:19'),(11,1,'Cesar','2025-07-28 03:25:19','2025-07-28 03:25:19'),(12,1,'Chocó','2025-07-28 03:25:19','2025-07-28 03:25:19'),(13,1,'Cundinamarca','2025-07-28 03:25:19','2025-07-28 03:25:19'),(14,1,'Córdoba','2025-07-28 03:25:19','2025-07-28 03:25:19'),(15,1,'Guainía','2025-07-28 03:25:19','2025-07-28 03:25:19'),(16,1,'Guaviare','2025-07-28 03:25:19','2025-07-28 03:25:19'),(17,1,'Huila','2025-07-28 03:25:19','2025-07-28 03:25:19'),(18,1,'La Guajira','2025-07-28 03:25:19','2025-07-28 03:25:19'),(19,1,'Magdalena','2025-07-28 03:25:19','2025-07-28 03:25:19'),(20,1,'Meta','2025-07-28 03:25:19','2025-07-28 03:25:19'),(21,1,'Nariño','2025-07-28 03:25:19','2025-07-28 03:25:19'),(22,1,'Norte de Santander','2025-07-28 03:25:19','2025-07-28 03:25:19'),(23,1,'Putumayo','2025-07-28 03:25:19','2025-07-28 03:25:19'),(24,1,'Quindío','2025-07-28 03:25:19','2025-07-28 03:25:19'),(25,1,'Risaralda','2025-07-28 03:25:19','2025-07-28 03:25:19'),(26,1,'San Andrés y Providencia','2025-07-28 03:25:19','2025-07-28 03:25:19'),(27,1,'Santander','2025-07-28 03:25:19','2025-07-28 03:25:19'),(28,1,'Sucre','2025-07-28 03:25:19','2025-07-28 03:25:19'),(29,1,'Tolima','2025-07-28 03:25:19','2025-07-28 03:25:19'),(30,1,'Valle del Cauca','2025-07-28 03:25:19','2025-07-28 03:25:19'),(31,1,'Vaupés','2025-07-28 03:25:19','2025-07-28 03:25:19'),(32,1,'Vichada','2025-07-28 03:25:19','2025-07-28 03:25:19');
/*!40000 ALTER TABLE `departamentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `descuento_producto`
--

DROP TABLE IF EXISTS `descuento_producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `descuento_producto` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `descuento_id` bigint(20) unsigned NOT NULL,
  `producto_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `descuento_producto_descuento_id_producto_id_unique` (`descuento_id`,`producto_id`),
  KEY `descuento_producto_producto_id_foreign` (`producto_id`),
  CONSTRAINT `descuento_producto_descuento_id_foreign` FOREIGN KEY (`descuento_id`) REFERENCES `descuentos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `descuento_producto_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `descuento_producto`
--

LOCK TABLES `descuento_producto` WRITE;
/*!40000 ALTER TABLE `descuento_producto` DISABLE KEYS */;
/*!40000 ALTER TABLE `descuento_producto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `descuentos`
--

DROP TABLE IF EXISTS `descuentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `descuentos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint(20) unsigned NOT NULL,
  `codigo` varchar(255) DEFAULT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo` enum('porcentaje','monto_fijo','envio_gratis','producto_gratis','2x1','3x2') NOT NULL DEFAULT 'porcentaje',
  `valor` decimal(10,2) NOT NULL DEFAULT 0.00,
  `descuento_maximo` decimal(10,2) DEFAULT NULL,
  `aplica_a` enum('orden','producto','categoria','carrito') NOT NULL DEFAULT 'orden',
  `productos_aplicables` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`productos_aplicables`)),
  `categorias_aplicables` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`categorias_aplicables`)),
  `monto_minimo_compra` decimal(10,2) DEFAULT NULL,
  `cantidad_minima_productos` int(11) DEFAULT NULL,
  `solo_primera_compra` tinyint(1) NOT NULL DEFAULT 0,
  `limite_usos_total` int(11) DEFAULT NULL,
  `usos_actuales` int(11) NOT NULL DEFAULT 0,
  `limite_usos_por_cliente` int(11) DEFAULT NULL,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `es_acumulable` tinyint(1) NOT NULL DEFAULT 0,
  `prioridad` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `descuentos_codigo_unique` (`codigo`),
  KEY `descuentos_empresa_id_activo_index` (`empresa_id`,`activo`),
  KEY `descuentos_codigo_index` (`codigo`),
  KEY `descuentos_fecha_inicio_fecha_fin_index` (`fecha_inicio`,`fecha_fin`),
  CONSTRAINT `descuentos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `descuentos`
--

LOCK TABLES `descuentos` WRITE;
/*!40000 ALTER TABLE `descuentos` DISABLE KEYS */;
/*!40000 ALTER TABLE `descuentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `descuentos_aplicados`
--

DROP TABLE IF EXISTS `descuentos_aplicados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `descuentos_aplicados` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `descuento_id` bigint(20) unsigned NOT NULL,
  `compra_id` bigint(20) unsigned NOT NULL,
  `email_cliente` varchar(255) DEFAULT NULL,
  `monto_descuento` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `descuentos_aplicados_compra_id_foreign` (`compra_id`),
  KEY `descuentos_aplicados_descuento_id_compra_id_index` (`descuento_id`,`compra_id`),
  KEY `descuentos_aplicados_descuento_id_email_cliente_index` (`descuento_id`,`email_cliente`),
  CONSTRAINT `descuentos_aplicados_compra_id_foreign` FOREIGN KEY (`compra_id`) REFERENCES `compras` (`id`) ON DELETE CASCADE,
  CONSTRAINT `descuentos_aplicados_descuento_id_foreign` FOREIGN KEY (`descuento_id`) REFERENCES `descuentos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `descuentos_aplicados`
--

LOCK TABLES `descuentos_aplicados` WRITE;
/*!40000 ALTER TABLE `descuentos_aplicados` DISABLE KEYS */;
/*!40000 ALTER TABLE `descuentos_aplicados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresas`
--

DROP TABLE IF EXISTS `empresas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `empresas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` bigint(20) unsigned NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `descripcion` varchar(500) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `imagen_portada` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telefono` varchar(255) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `instagram_url` varchar(255) DEFAULT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  `tiktok_url` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(255) DEFAULT NULL,
  `horario_atencion` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`horario_atencion`)),
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `template_tienda_id` bigint(20) unsigned DEFAULT NULL,
  `hero_video_url` varchar(255) DEFAULT NULL COMMENT 'URL del video hero para template Brasilia',
  `hero_video_message` varchar(500) DEFAULT NULL COMMENT 'Mensaje del video hero',
  `hero_video_button_text` varchar(100) DEFAULT NULL COMMENT 'Texto del botón del video hero',
  `hero_video_button_link` varchar(255) DEFAULT NULL COMMENT 'Link del botón del video hero',
  `plan_membresia_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `empresas_usuario_id_unique` (`usuario_id`),
  UNIQUE KEY `empresas_slug_unique` (`slug`),
  KEY `empresas_slug_activo_index` (`slug`,`activo`),
  KEY `empresas_plan_membresia_id_foreign` (`plan_membresia_id`),
  KEY `empresas_template_tienda_id_index` (`template_tienda_id`),
  CONSTRAINT `empresas_plan_membresia_id_foreign` FOREIGN KEY (`plan_membresia_id`) REFERENCES `planes_membresia` (`id`),
  CONSTRAINT `empresas_template_tienda_id_foreign` FOREIGN KEY (`template_tienda_id`) REFERENCES `templates_tienda` (`id`) ON DELETE SET NULL,
  CONSTRAINT `empresas_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresas`
--

LOCK TABLES `empresas` WRITE;
/*!40000 ALTER TABLE `empresas` DISABLE KEYS */;
INSERT INTO `empresas` VALUES (5,1,'Esnova Market','esnova-market','Esnova Market es una tienda enfocada en ofrecer una experiencia cercana y confiable, donde cada cliente recibe una asesoría personalizada según sus necesidades. Nos caracterizamos por seleccionar productos originales, prácticos y de calidad, garantizando que lo que recomendamos realmente funciona. Nuestro compromiso es brindar seguridad en cada compra, atención amable y resultados que generen confianza, para que cada persona sienta que está eligiendo lo mejor para su cuidado y bienestar.','imagenes/empresas/5/logo/1762474274_690d392262150_logo.png','imagenes/empresas/5/portada/1762471461_690d2e252fe7f_bg02.jpg','esnovamarket@gmail.com','3246598065','Cl 48B Cr 50 - 80','https://www.instagram.com/esnovamarket?utm_source=qr&igsh=aWk5aDc4Z3ZtcWN6','https://www.facebook.com/share/1ZiGg4HS3X/','https://mytechsolutionsco.com/','+57 3246598065','{\"lunes\":{\"apertura\":\"01:00\",\"cierre\":\"12:00\",\"cerrado\":false},\"martes\":{\"apertura\":\"01:00\",\"cierre\":\"12:00\",\"cerrado\":false},\"miercoles\":{\"apertura\":\"01:00\",\"cierre\":\"12:00\",\"cerrado\":false},\"jueves\":{\"apertura\":\"01:00\",\"cierre\":\"12:00\",\"cerrado\":false},\"viernes\":{\"apertura\":\"01:00\",\"cierre\":\"12:00\",\"cerrado\":false},\"sabado\":{\"apertura\":\"01:00\",\"cierre\":\"12:00\",\"cerrado\":false},\"domingo\":{\"apertura\":\"01:00\",\"cierre\":\"12:00\",\"cerrado\":false}}',1,1,NULL,NULL,NULL,NULL,1,'2025-09-15 23:53:36','2025-12-09 21:18:03');
/*!40000 ALTER TABLE `empresas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enlaces_acceso`
--

DROP TABLE IF EXISTS `enlaces_acceso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `enlaces_acceso` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cliente_id` bigint(20) unsigned NOT NULL,
  `empresa_id` bigint(20) unsigned DEFAULT NULL,
  `creado_por` bigint(20) unsigned NOT NULL,
  `token` varchar(255) NOT NULL,
  `dias_validos` int(11) NOT NULL,
  `mostrar_precios` tinyint(1) NOT NULL DEFAULT 1,
  `mostrar_stock` tinyint(1) NOT NULL DEFAULT 1,
  `expira_en` datetime NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `visitas` int(11) NOT NULL DEFAULT 0,
  `ultimo_acceso` datetime DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `enlaces_acceso_token_unique` (`token`),
  KEY `enlaces_acceso_cliente_id_foreign` (`cliente_id`),
  KEY `enlaces_acceso_token_activo_index` (`token`,`activo`),
  KEY `enlaces_acceso_expira_en_activo_index` (`expira_en`,`activo`),
  KEY `enlaces_acceso_creado_por_index` (`creado_por`),
  KEY `enlaces_acceso_empresa_id_activo_index` (`empresa_id`,`activo`),
  CONSTRAINT `enlaces_acceso_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `enlaces_acceso_creado_por_foreign` FOREIGN KEY (`creado_por`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `enlaces_acceso_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enlaces_acceso`
--

LOCK TABLES `enlaces_acceso` WRITE;
/*!40000 ALTER TABLE `enlaces_acceso` DISABLE KEYS */;
/*!40000 ALTER TABLE `enlaces_acceso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `envios`
--

DROP TABLE IF EXISTS `envios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `envios` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `compra_id` bigint(20) unsigned NOT NULL,
  `transportadora` varchar(255) DEFAULT NULL,
  `numero_guia` varchar(255) DEFAULT NULL,
  `estado` enum('preparando','enviado','en_transito','entregado','devuelto') NOT NULL DEFAULT 'preparando',
  `fecha_envio` timestamp NULL DEFAULT NULL,
  `fecha_entrega_estimada` timestamp NULL DEFAULT NULL,
  `fecha_entrega` timestamp NULL DEFAULT NULL,
  `url_seguimiento` varchar(255) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `envios_compra_id_estado_index` (`compra_id`,`estado`),
  KEY `envios_numero_guia_index` (`numero_guia`),
  CONSTRAINT `envios_compra_id_foreign` FOREIGN KEY (`compra_id`) REFERENCES `compras` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `envios`
--

LOCK TABLES `envios` WRITE;
/*!40000 ALTER TABLE `envios` DISABLE KEYS */;
INSERT INTO `envios` VALUES (5,18,'servientrega','41564131245','enviado','2025-12-01 09:34:46','2025-12-02 00:00:00',NULL,NULL,NULL,'2025-12-01 09:34:46','2025-12-01 09:34:46');
/*!40000 ALTER TABLE `envios` ENABLE KEYS */;
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
-- Table structure for table `imagenes_productos`
--

DROP TABLE IF EXISTS `imagenes_productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `imagenes_productos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `producto_id` bigint(20) unsigned NOT NULL,
  `ruta_imagen` varchar(255) NOT NULL,
  `texto_alternativo` varchar(255) DEFAULT NULL,
  `orden` int(11) NOT NULL DEFAULT 0,
  `es_principal` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `imagenes_productos_producto_id_orden_index` (`producto_id`,`orden`),
  CONSTRAINT `imagenes_productos_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `imagenes_productos`
--

LOCK TABLES `imagenes_productos` WRITE;
/*!40000 ALTER TABLE `imagenes_productos` DISABLE KEYS */;
INSERT INTO `imagenes_productos` VALUES (62,37,'imagenes/productos/37/1762474039_690d38371a831_240270000.jpg','Pantalon Azul',1,1,'2025-11-07 00:07:19','2025-12-19 23:52:39'),(63,37,'imagenes/productos/37/1762474039_690d38371b13f_jean-de-14-onzas-500x500-agrofarbef.jpg','Pantalon Azul',2,0,'2025-11-07 00:07:19','2025-12-19 23:52:39'),(64,38,'imagenes/productos/38/1762544510_690e4b7ed7920_images.jpg','Camiseta roja',1,1,'2025-11-07 19:41:50','2025-11-24 16:26:06'),(65,44,'imagenes/productos/44/1765468908_693aeaec73f21_S7740-Plancha-Alisadora-Remington-Triple-Infusion-01-3.jpg','PLANCHA TRIPLE INFUSION',1,1,'2025-12-11 11:01:48','2025-12-11 11:01:48');
/*!40000 ALTER TABLE `imagenes_productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `items_compra`
--

DROP TABLE IF EXISTS `items_compra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `items_compra` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `compra_id` bigint(20) unsigned NOT NULL,
  `producto_id` bigint(20) unsigned NOT NULL,
  `variante_producto_id` bigint(20) unsigned DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `descuento` decimal(10,2) NOT NULL DEFAULT 0.00,
  `precio_total` decimal(12,2) NOT NULL,
  `referencia_producto` varchar(255) NOT NULL,
  `nombre_producto` varchar(255) NOT NULL,
  `info_variante` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `items_compra_producto_id_foreign` (`producto_id`),
  KEY `items_compra_variante_producto_id_foreign` (`variante_producto_id`),
  KEY `items_compra_compra_id_index` (`compra_id`),
  CONSTRAINT `items_compra_compra_id_foreign` FOREIGN KEY (`compra_id`) REFERENCES `compras` (`id`) ON DELETE CASCADE,
  CONSTRAINT `items_compra_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `items_compra_variante_producto_id_foreign` FOREIGN KEY (`variante_producto_id`) REFERENCES `variantes_productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items_compra`
--

LOCK TABLES `items_compra` WRITE;
/*!40000 ALTER TABLE `items_compra` DISABLE KEYS */;
INSERT INTO `items_compra` VALUES (16,16,38,NULL,1,500000.00,0.00,500000.00,'camiseta roja','Camiseta roja',NULL,'2025-11-30 11:37:54','2025-11-30 11:37:54'),(17,17,38,NULL,1,500000.00,0.00,500000.00,'camiseta roja','Camiseta roja',NULL,'2025-12-01 09:28:08','2025-12-01 09:28:08'),(18,18,38,NULL,1,500000.00,0.00,500000.00,'camiseta roja','Camiseta roja',NULL,'2025-12-01 09:31:31','2025-12-01 09:31:31'),(19,19,38,NULL,1,500000.00,0.00,500000.00,'camiseta roja','Camiseta roja',NULL,'2025-12-01 22:12:32','2025-12-01 22:12:32'),(20,20,38,NULL,1,500000.00,0.00,500000.00,'camiseta roja','Camiseta roja',NULL,'2025-12-09 21:19:54','2025-12-09 21:19:54'),(21,21,39,NULL,1,210000.00,0.00,210000.00,'SLP-1075','PLANCHA SUPER LOOK PROFESIONAL',NULL,'2025-12-09 21:43:14','2025-12-09 21:43:14'),(22,22,40,NULL,1,210000.00,0.00,210000.00,'S8599','PLANCHA KERATINA Y ACEITE DE ARGÁN',NULL,'2025-12-10 19:10:31','2025-12-10 19:10:31'),(23,23,43,NULL,2,190000.00,0.00,380000.00,'S8500A','PLANCHA ACEITE DE ARGAN',NULL,'2026-01-06 02:10:16','2026-01-06 02:10:16');
/*!40000 ALTER TABLE `items_compra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `items_solicitud_cotizacion`
--

DROP TABLE IF EXISTS `items_solicitud_cotizacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `items_solicitud_cotizacion` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `solicitud_cotizacion_id` bigint(20) unsigned NOT NULL,
  `producto_id` bigint(20) unsigned NOT NULL,
  `variante_producto_id` bigint(20) unsigned DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `precio_total` decimal(12,2) NOT NULL,
  `referencia_producto` varchar(255) NOT NULL,
  `nombre_producto` varchar(255) NOT NULL,
  `info_variante` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `items_solicitud_cotizacion_producto_id_foreign` (`producto_id`),
  KEY `items_solicitud_cotizacion_variante_producto_id_foreign` (`variante_producto_id`),
  KEY `items_solicitud_cotizacion_solicitud_cotizacion_id_index` (`solicitud_cotizacion_id`),
  CONSTRAINT `items_solicitud_cotizacion_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `items_solicitud_cotizacion_solicitud_cotizacion_id_foreign` FOREIGN KEY (`solicitud_cotizacion_id`) REFERENCES `solicitudes_cotizacion` (`id`) ON DELETE CASCADE,
  CONSTRAINT `items_solicitud_cotizacion_variante_producto_id_foreign` FOREIGN KEY (`variante_producto_id`) REFERENCES `variantes_productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items_solicitud_cotizacion`
--

LOCK TABLES `items_solicitud_cotizacion` WRITE;
/*!40000 ALTER TABLE `items_solicitud_cotizacion` DISABLE KEYS */;
/*!40000 ALTER TABLE `items_solicitud_cotizacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `listas_precios`
--

DROP TABLE IF EXISTS `listas_precios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `listas_precios` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `codigo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `orden` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `listas_precios_codigo_unique` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `listas_precios`
--

LOCK TABLES `listas_precios` WRITE;
/*!40000 ALTER TABLE `listas_precios` DISABLE KEYS */;
INSERT INTO `listas_precios` VALUES (1,'Precio','precio','Lista de precio',1,1,NULL,NULL);
/*!40000 ALTER TABLE `listas_precios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_tabla` int(11) DEFAULT NULL,
  `tabla` varchar(255) NOT NULL DEFAULT 'llamadas',
  `detalle` text NOT NULL COMMENT 'Comentario del cambio de estado',
  `tipo_log` varchar(255) NOT NULL DEFAULT '1' COMMENT '1 para cambio de estado',
  `valor_viejo` varchar(255) DEFAULT NULL,
  `valor_nuevo` varchar(255) DEFAULT NULL,
  `id_usuario` bigint(20) unsigned NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `id_archivo` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `logs_id_usuario_foreign` (`id_usuario`),
  CONSTRAINT `logs_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs`
--

LOCK TABLES `logs` WRITE;
/*!40000 ALTER TABLE `logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs_transacciones`
--

DROP TABLE IF EXISTS `logs_transacciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logs_transacciones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `transaccion_pago_id` bigint(20) unsigned NOT NULL,
  `evento` varchar(255) NOT NULL,
  `datos_evento` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos_evento`)),
  `ip_origen` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `logs_transacciones_transaccion_pago_id_created_at_index` (`transaccion_pago_id`,`created_at`),
  CONSTRAINT `logs_transacciones_transaccion_pago_id_foreign` FOREIGN KEY (`transaccion_pago_id`) REFERENCES `transacciones_pago` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs_transacciones`
--

LOCK TABLES `logs_transacciones` WRITE;
/*!40000 ALTER TABLE `logs_transacciones` DISABLE KEYS */;
INSERT INTO `logs_transacciones` VALUES (19,23,'cambio_estado','{\"estado_anterior\":\"pendiente\",\"estado_nuevo\":\"aprobada\"}','2800:e2:a600:d01:f91d:d11d:64bc:30a0','2025-11-30 11:38:29','2025-11-30 11:38:29'),(20,25,'cambio_estado','{\"estado_anterior\":\"pendiente\",\"estado_nuevo\":\"aprobada\"}','186.81.103.19','2025-12-01 09:32:16','2025-12-01 09:32:16');
/*!40000 ALTER TABLE `logs_transacciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `membresias`
--

DROP TABLE IF EXISTS `membresias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `membresias` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint(20) unsigned NOT NULL,
  `plan_membresia_id` bigint(20) unsigned NOT NULL,
  `estado` enum('activa','cancelada','vencida','pendiente') NOT NULL DEFAULT 'pendiente',
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `precio_pagado` decimal(10,2) NOT NULL,
  `transaccion_pago_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `membresias_plan_membresia_id_foreign` (`plan_membresia_id`),
  KEY `membresias_transaccion_pago_id_foreign` (`transaccion_pago_id`),
  KEY `membresias_empresa_id_estado_index` (`empresa_id`,`estado`),
  CONSTRAINT `membresias_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `membresias_plan_membresia_id_foreign` FOREIGN KEY (`plan_membresia_id`) REFERENCES `planes_membresia` (`id`),
  CONSTRAINT `membresias_transaccion_pago_id_foreign` FOREIGN KEY (`transaccion_pago_id`) REFERENCES `transacciones_pago` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `membresias`
--

LOCK TABLES `membresias` WRITE;
/*!40000 ALTER TABLE `membresias` DISABLE KEYS */;
INSERT INTO `membresias` VALUES (18,5,1,'activa','2025-09-15',NULL,0.00,NULL,'2025-09-15 21:04:03','2025-09-15 21:04:03');
/*!40000 ALTER TABLE `membresias` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_resets_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',1),(5,'2025_06_18_195318_create_permission_tables',1),(6,'2025_06_27_195741_create_parametros_table',1),(7,'2025_07_10_150656_create_logs_table',1),(8,'2025_07_24_001402_crear_tabla_categorias',2),(9,'2025_07_24_001457_crear_tabla_productos',2),(10,'2025_07_24_001504_crear_tabla_variantes_producto',2),(11,'2025_07_24_215052_crear_tabla_imagenes_producto',2),(12,'2025_07_24_215102_crear_tabla_listas_precios',2),(13,'2025_07_24_215111_crear_tabla_precios_producto',2),(14,'2025_07_24_225859_crear_tabla_precios_variantes',2),(15,'2025_07_24_225929_crear_tabla_clientes',2),(16,'2025_07_24_225957_crear_enlaces',2),(17,'2025_07_24_230023_crear_cotizacion',2),(18,'2025_07_24_230042_crear_cotizacion_items',2),(19,'2025_07_24_230110_crear_actualizacion_precio',2),(20,'2025_07_25_003515_crear_precios_productos',3),(21,'2025_07_25_011901_crear_add_user',4),(22,'2025_07_27_221651_create_pais_table',5),(23,'2025_07_27_221714_create_departamento_table',5),(24,'2025_07_27_221745_create_ciudades_table',5),(25,'2025_07_27_224058_create_update_clientes_add_pais_ciudad_table',6),(26,'2025_07_31_024154_create_update_clientes_add_nullable_solicitud_table',7),(27,'2025_08_01_195011_create_update_enlace_table',8),(30,'2025_08_06_100715_agregar_stock_22',9),(31,'2025_08_07_101807_agregar_stock_enlace',10),(32,'2025_08_07_161015_create_update_actualizaciones_precios_table',11),(34,'2025_08_14_151256_create_empresas_table',12),(35,'2025_08_14_151351_create_carrusel_empresas_table',12),(36,'2025_08_14_151422_add_empresa_id_to_productos_table',12),(37,'2025_08_14_151557_create_compras_table',12),(38,'2025_08_14_151651_create_items_compra_table',12),(39,'2025_08_14_151711_create_transacciones_pago_table',12),(40,'2025_08_14_151744_create_comisiones_table',12),(41,'2025_08_14_151808_create_configuracion_pasarela_table',12),(42,'2025_08_14_151831_create_logs_transacciones_table',12),(43,'2025_08_14_151853_create_pagos_empresas_table',12),(44,'2025_08_14_151913_create_envios_table',12),(45,'2025_08_14_151932_create_carritos_table',12),(46,'2025_08_14_152005_add_empresa_fields_to_clientes_table',12),(47,'2025_08_14_152027_add_empresa_id_to_enlaces_acceso_table',12),(48,'2025_08_14_152054_add_empresa_id_to_solicitudes_cotizacion_table',12),(49,'2025_08_16_154940_make_referencia_unique_per_empresa',13),(50,'2025_08_16_155023_make_sku_unique_per_product',13),(51,'2025_08_18_114652_add_empresa_id_to_categorias',14),(52,'2025_08_18_220341_add_imagen_to_categorias_table',15),(53,'2025_08_25_193357_make_empresa_comision',16),(54,'2025_08_27_193211_add_unique_compra_id_to_comisiones_table',17),(55,'2025_08_28_094940_create_planes_membresia_table',17),(56,'2025_08_28_095109_create_membresias_table',17),(57,'2025_08_28_095138_add_membresia_fields_to_empresas_table',17),(58,'2025_08_28_095223_create_pagos_membresia_table',17),(59,'2025_09_01_234657_add_benefits_to_productos_table',18),(60,'2025_09_02_082427_add_marca_de_agua_to_planes_membresia_table',18),(61,'2025_09_05_181523_remove_duplicated_fields_from_empresas_table',19),(62,'2025_09_15_093236_create_pages_table',20),(63,'2025_09_15_093631_create_seo_table',20),(64,'2025_10_17_115315_create_templates_tienda_table',21),(65,'2025_10_17_115600_add_template_tienda_id_to_empresas_table',21),(66,'2025_10_20_135410_add_hero_video_fields_to_empresas_table',22),(67,'2025_10_21_003807_create_descuentos_table',23),(69,'2025_10_21_094612_add_discount_fields_to_carritos_and_compras_tables',24),(70,'2025_10_21_101316_allow_null_codigo_in_descuentos_table',25),(71,'2025_12_11_130009_create_calificaciones_productos_table',26),(72,'2025_12_11_130047_add_user_id_to_compras_table',26),(73,'2025_12_11_145409_rename_twitter_url_to_tiktok_url_in_empresas_table',27),(74,'2025_12_19_164315_add_eliminado_to_productos_table',28),(75,'2025_12_19_181758_create_caracteristicas_productos_table',29),(76,'2026_01_04_191817_add_metodo_pago_fields_to_compras_table',30),(77,'2026_01_05_215011_modify_calificaciones_for_public_reviews',31),(78,'2026_01_05_235910_add_imagen_to_calificaciones_productos',32),(79,'2026_01_05_235924_add_parent_id_to_calificaciones_productos',32),(80,'2026_01_05_235941_create_reacciones_calificaciones_table',32);
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
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1),(2,'App\\Models\\User',10),(3,'App\\Models\\User',12),(4,'App\\Models\\User',14);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `movimientos_stock`
--

DROP TABLE IF EXISTS `movimientos_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `movimientos_stock` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `producto_id` bigint(20) unsigned NOT NULL,
  `variante_producto_id` bigint(20) unsigned DEFAULT NULL,
  `tipo_movimiento` enum('entrada','salida','ajuste','reserva','liberacion') NOT NULL,
  `cantidad` int(11) NOT NULL,
  `stock_anterior` int(11) NOT NULL,
  `stock_nuevo` int(11) NOT NULL,
  `referencia_documento` varchar(255) DEFAULT NULL,
  `origen` enum('compra','venta','devolucion','ajuste_inventario','cotizacion','otro') NOT NULL DEFAULT 'otro',
  `motivo` text DEFAULT NULL,
  `usuario_id` bigint(20) unsigned NOT NULL,
  `solicitud_cotizacion_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `movimientos_stock_variante_producto_id_foreign` (`variante_producto_id`),
  KEY `movimientos_stock_usuario_id_foreign` (`usuario_id`),
  KEY `movimientos_stock_solicitud_cotizacion_id_foreign` (`solicitud_cotizacion_id`),
  KEY `movimientos_stock_producto_id_created_at_index` (`producto_id`,`created_at`),
  KEY `movimientos_stock_tipo_movimiento_created_at_index` (`tipo_movimiento`,`created_at`),
  CONSTRAINT `movimientos_stock_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `movimientos_stock_solicitud_cotizacion_id_foreign` FOREIGN KEY (`solicitud_cotizacion_id`) REFERENCES `solicitudes_cotizacion` (`id`) ON DELETE SET NULL,
  CONSTRAINT `movimientos_stock_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`),
  CONSTRAINT `movimientos_stock_variante_producto_id_foreign` FOREIGN KEY (`variante_producto_id`) REFERENCES `variantes_productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `movimientos_stock`
--

LOCK TABLES `movimientos_stock` WRITE;
/*!40000 ALTER TABLE `movimientos_stock` DISABLE KEYS */;
INSERT INTO `movimientos_stock` VALUES (44,38,NULL,'entrada',5,0,5,NULL,'ajuste_inventario','Stock inicial',1,NULL,'2025-11-07 19:41:50','2025-11-07 19:41:50'),(45,38,NULL,'salida',1,5,4,'ORD-20251130113754-YBQ0','venta',NULL,1,NULL,'2025-11-30 11:37:54','2025-11-30 11:37:54'),(46,38,NULL,'salida',1,4,3,'ORD-20251201092808-8AGC','venta',NULL,1,NULL,'2025-12-01 09:28:08','2025-12-01 09:28:08'),(47,38,NULL,'salida',1,3,2,'ORD-20251201093131-YN37','venta',NULL,1,NULL,'2025-12-01 09:31:31','2025-12-01 09:31:31'),(48,38,NULL,'salida',1,2,1,'ORD-20251201221232-IOHP','venta',NULL,1,NULL,'2025-12-01 22:12:32','2025-12-01 22:12:32'),(49,38,NULL,'salida',1,1,0,'ORD-20251209211954-ALZV','venta',NULL,1,NULL,'2025-12-09 21:19:54','2025-12-09 21:19:54'),(50,39,NULL,'entrada',1,0,1,NULL,'ajuste_inventario','Stock inicial',1,NULL,'2025-12-09 21:33:13','2025-12-09 21:33:13'),(51,39,NULL,'salida',1,1,0,'ORD-20251209214314-LXIF','venta',NULL,1,NULL,'2025-12-09 21:43:14','2025-12-09 21:43:14'),(52,38,NULL,'entrada',1,0,1,NULL,'compra',NULL,1,NULL,'2025-12-09 22:46:17','2025-12-09 22:46:17'),(53,40,NULL,'entrada',8,0,8,NULL,'ajuste_inventario','Stock inicial',1,NULL,'2025-12-09 22:52:44','2025-12-09 22:52:44'),(54,41,NULL,'entrada',1,0,1,NULL,'ajuste_inventario','Stock inicial',1,NULL,'2025-12-09 23:01:47','2025-12-09 23:01:47'),(55,42,NULL,'entrada',4,0,4,NULL,'ajuste_inventario','Stock inicial',1,NULL,'2025-12-09 23:07:08','2025-12-09 23:07:08'),(56,43,NULL,'entrada',3,0,3,NULL,'ajuste_inventario','Stock inicial',1,NULL,'2025-12-09 23:11:20','2025-12-09 23:11:20'),(57,44,NULL,'entrada',4,0,4,NULL,'ajuste_inventario','Stock inicial',1,NULL,'2025-12-09 23:17:19','2025-12-09 23:17:19'),(58,40,NULL,'salida',1,8,7,'ORD-20251210191031-ZRWP','venta',NULL,1,NULL,'2025-12-10 19:10:31','2025-12-10 19:10:31'),(59,43,NULL,'salida',2,3,1,'ORD-20260105211016-OBXF','venta',NULL,1,NULL,'2026-01-06 02:10:16','2026-01-06 02:10:16');
/*!40000 ALTER TABLE `movimientos_stock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `title` varchar(200) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`content`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pages_name_unique` (`name`),
  UNIQUE KEY `pages_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` VALUES (1,'welcome','BeTogether - Tu ecosistema de crecimiento','inicio','Plataforma de membresías para emprendedores y fundaciones en Colombia. Tu tienda online, eventos exclusivos y crecimiento sin límites.','{\"hero_title\":\"\\u00a1Hola Colombia!<br>Tu Negocio, <span>Nuestra causa.<\\/span>\",\"hero_subtitle\":\"Te abrimos la puerta a un ecosistema de <strong>crecimiento sin l\\u00edmites.<\\/strong>\",\"hero_benefits\":\"\\ud83d\\uded2 Tienda Online al Instante|\\ud83d\\ude9a Pasarela de pago y Log\\u00edstica integrada|\\ud83c\\udf89 Festivales Exclusivos para nuestros miembros\",\"hero_btn_primary\":\"As\\u00ed lo hacemos posible\",\"hero_btn_secondary\":\"Reg\\u00edstrate ahora\",\"offer_subtitle\":\"\\u00a1ACTIVA TU MARCA EN LO DIGITAL Y PRESENCIAL!\",\"offer_title\":\"\\u00bfEmprendes o lideras una fundaci\\u00f3n?\",\"offer_description\":\"Con nosotros no solo accedes a una plataforma\\u2026 \\u00a1Abres la puerta a un <strong>ecosistema completo<\\/strong> que se enfoca en atraer visitantes y potenciales clientes para ti.\",\"card1_title\":\"Tu Tienda Online \\u00a1Lista para Vender!\",\"card1_description\":\"L\\u00e1nzala en minutos y gestiona pagos seguros, env\\u00edos y estad\\u00edsticas para el control total de tus ventas. \\ud83e\\uddfe\",\"card1_icon\":\"bi-laptop\",\"card2_title\":\"Tu Marca Siempre Visible.\",\"card2_description\":\"\\u00a1No solo vendes, tu marca conecta! \\u2764\\ufe0f Creamos contenido audiovisual en nuestras redes que cuenta la historia de algunos de nuestros miembros, impulsando su reconocimiento. <strong>Somos tu aliado para hacerte conocer.<\\/strong>\",\"card2_icon\":\"fa-solid fa-handshake\",\"card3_title\":\"\\u00a1Brilla en nuestros eventos exclusivos!\",\"card3_description\":\"Lleva tu marca al siguiente nivel en festivales comerciales. Tu tienda digital y f\\u00edsica se fusionan para que solo te preocupes por vender, nosotros nos encargamos del resto y, <strong>\\u00a1nosotros ponemos la infraestructura!<\\/strong> \\ud83c\\udfe0\",\"card3_icon\":\"bi-star-fill\",\"stats_title\":\"\\u00a1\\u00danete a la <strong>primera membres\\u00eda<\\/strong> en Colombia para Emprendedores y <strong>Fundaciones<\\/strong>!\",\"stats_subtitle\":\"Juntos, ya impactamos a m\\u00e1s de:\",\"stats_count\":\"134\",\"stats_label\":\"VISITANTES TOTALES\",\"features_subtitle\":\"UN ECOSISTEMA COMPLETO Y LISTO PARA TI\",\"features_title\":\"Sencillo, R\\u00e1pido y Poderoso\",\"features_intro\":\"Tu tienda,<br \\/>Tus eventos, tu momento<br \\/>Inscr\\u00edbete en la lista de espera \\ud83d\\udea8\",\"step1_title\":\"Reg\\u00edstrate\",\"step1_description\":\"Da el primer paso para impulsar tu marca. Crea tu cuenta <strong>sin costo<\\/strong> y config\\u00farala en minutos.\",\"step1_icon\":\"bi-person-plus\",\"step1_color\":\"pink\",\"step2_title\":\"Activa\",\"step2_description\":\"Elige tu <strong>membres\\u00eda<\\/strong> activ\\u00e1la \\u2705 y accede a m\\u00faltiples beneficios. <strong>\\u00a1As\\u00ed podr\\u00e1s enfocarte solo en vender!<\\/strong>\",\"step2_icon\":\"bi-check-lg\",\"step2_color\":\"blue\",\"step3_title\":\"Ag\\u00e9ndate\",\"step3_description\":\"<strong>Alquila tu espacio<\\/strong> en nuestros festivales de comercio. \\u00a1T\\u00fa solo vende, nosotros hacemos el resto!\",\"step3_icon\":\"bi-calendar\",\"step3_color\":\"pink\",\"bt_subtitle\":\"Nacimos para transformar el emprendimiento y el impacto social en Colombia.\",\"bt_title\":\"\\\"Emprender no debe ser imposible, debe ser accesible.\\\"\",\"bt_description\":\"<strong>Better Together:<\\/strong> <em>Tu ecosistema \\u00fanico<\\/em> y accesible <em>en Colombia.<\\/em> Con nuestra <strong>plataforma y eventos exclusivos<\\/strong>, te damos las herramientas para que solo te enfoques en tu negocio y vender.<br>\\u00a1\\u00danete y transforma tu estrategia!\",\"bt_footer\":\"Somos una inversi\\u00f3n para tu marca\",\"access_subtitle\":\"Acceso exclusivo por inscripci\\u00f3n anticipada\",\"access_title\":\"Todo lo que necesitas para crecer\",\"access_description\":\"El acceso a Better Together es limitado en esta fase inicial.<br><strong>\\u00a1Solo quienes se registren en la lista de espera<\\/strong> podr\\u00e1n ser parte de este grupo!<br><strong>No te quedes por fuera y asegura tu lugar ahora.<\\/strong>\",\"cta_title\":\"\\u00bfListo para llevar tu negocio al siguiente Nivel?\",\"cta_description\":\"Forma parte de nuestra comunidad. <span class=\\\"highlight3\\\">Reg\\u00edstrate en la lista de espera hoy y prep\\u00e1rate para crecer<\\/span> con nosotros.\",\"cta_button_text\":\"Enviar\",\"social_text\":\"S\\u00edguenos en nuestras redes sociales\",\"facebook_url\":\"https:\\/\\/www.facebook.com\\/people\\/BeTogether-Colombia\\/61577098046804\\/\",\"tiktok_url\":\"https:\\/\\/www.tiktok.com\\/@better.togethercol\",\"instagram_url\":\"https:\\/\\/www.instagram.com\\/betogethercol\\/\",\"linkedin_url\":\"https:\\/\\/www.linkedin.com\\/company\\/be-togethercol\\/?viewAsMember=true\",\"whatsapp_url\":\"https:\\/\\/wa.me\\/#\",\"whatsapp_text\":\"Cont\\u00e1ctanos v\\u00eda Whatsapp\",\"benefit1_title\":\"Lanza tu e-commerce en minutos\",\"benefit1_description\":\"gestiona tus pedidos, inventario y potencia tus ingresos\",\"benefit2_title\":\"Pagos r\\u00e1pidos y directos\",\"benefit2_description\":\"Pagos \\u00e1giles y seguros usando nuestra plataforma en lo digital y en festivales.\",\"benefit3_title\":\"Log\\u00edstica Optimizada para ti\",\"benefit3_description\":\"Cotiza y gestiona tus despachos con las transportadoras, \\u00a1todo desde nuestra plataforma!\",\"benefit4_title\":\"Presencia en eventos f\\u00edsicos\",\"benefit4_description\":\"Participa en festivales de comercio con afluencia de p\\u00fablico. Conecta con nuevos clientes e impulsa tus ventas.\",\"benefits_cta_text\":\"\\u00a1EMPIEZA AHORA!\",\"footer_rights\":\"\\u00a9 BETOGETHER.COM.CO - TODOS LOS DERECHOS RESERVADOS\",\"footer_slogan\":\"TECNOLOG\\u00cdA \\u00daTIL, CERCANA Y SIN COMPLICACIONES.\"}',1,'2025-09-15 14:47:31','2025-09-16 09:21:57');
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pagos_empresas`
--

DROP TABLE IF EXISTS `pagos_empresas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pagos_empresas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint(20) unsigned NOT NULL,
  `periodo` varchar(255) NOT NULL,
  `total_ventas` decimal(12,2) NOT NULL,
  `total_comisiones` decimal(12,2) NOT NULL,
  `total_a_pagar` decimal(12,2) NOT NULL,
  `estado` enum('pendiente','pagado','cancelado') NOT NULL DEFAULT 'pendiente',
  `fecha_pago` date DEFAULT NULL,
  `metodo_pago` varchar(255) DEFAULT NULL,
  `referencia_pago` varchar(255) DEFAULT NULL,
  `comprobante_pago` varchar(255) DEFAULT NULL,
  `detalle_comisiones` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`detalle_comisiones`)),
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pagos_empresas_empresa_id_estado_periodo_index` (`empresa_id`,`estado`,`periodo`),
  CONSTRAINT `pagos_empresas_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagos_empresas`
--

LOCK TABLES `pagos_empresas` WRITE;
/*!40000 ALTER TABLE `pagos_empresas` DISABLE KEYS */;
/*!40000 ALTER TABLE `pagos_empresas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pagos_membresia`
--

DROP TABLE IF EXISTS `pagos_membresia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pagos_membresia` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint(20) unsigned NOT NULL,
  `membresia_id` bigint(20) unsigned NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `referencia_pago` varchar(255) NOT NULL,
  `estado` enum('pendiente','aprobado','rechazado','reembolsado') NOT NULL DEFAULT 'pendiente',
  `metodo_pago` varchar(255) DEFAULT NULL,
  `respuesta_pasarela` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`respuesta_pasarela`)),
  `fecha_pago` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pagos_membresia_membresia_id_foreign` (`membresia_id`),
  KEY `pagos_membresia_empresa_id_estado_index` (`empresa_id`,`estado`),
  CONSTRAINT `pagos_membresia_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pagos_membresia_membresia_id_foreign` FOREIGN KEY (`membresia_id`) REFERENCES `membresias` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagos_membresia`
--

LOCK TABLES `pagos_membresia` WRITE;
/*!40000 ALTER TABLE `pagos_membresia` DISABLE KEYS */;
/*!40000 ALTER TABLE `pagos_membresia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paises`
--

DROP TABLE IF EXISTS `paises`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paises` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `paises_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paises`
--

LOCK TABLES `paises` WRITE;
/*!40000 ALTER TABLE `paises` DISABLE KEYS */;
INSERT INTO `paises` VALUES (1,'Colombia','2025-07-28 03:25:18','2025-07-28 03:25:18');
/*!40000 ALTER TABLE `paises` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parametros`
--

DROP TABLE IF EXISTS `parametros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parametros` (
  `id_parametro` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre_parametro` varchar(100) DEFAULT NULL,
  `valor_parametro` text DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `comentario` varchar(255) DEFAULT NULL,
  `reservado` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_parametro`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parametros`
--

LOCK TABLES `parametros` WRITE;
/*!40000 ALTER TABLE `parametros` DISABLE KEYS */;
/*!40000 ALTER TABLE `parametros` ENABLE KEYS */;
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
-- Table structure for table `planes_membresia`
--

DROP TABLE IF EXISTS `planes_membresia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `planes_membresia` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `limite_productos` int(11) NOT NULL,
  `limite_transacciones` int(11) DEFAULT NULL,
  `porcentaje_comision` decimal(5,2) NOT NULL,
  `comision_fija` decimal(10,2) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `caracteristicas` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`caracteristicas`)),
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `marca_de_agua` tinyint(1) NOT NULL DEFAULT 0,
  `orden` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `planes_membresia_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `planes_membresia`
--

LOCK TABLES `planes_membresia` WRITE;
/*!40000 ALTER TABLE `planes_membresia` DISABLE KEYS */;
INSERT INTO `planes_membresia` VALUES (1,'Plan Fundador','plan-fundador',0.00,10,50,6.09,900.00,'Plan gratuito para empezar','[\"10 productos en tu cat\\u00e1logo\",\"Subdominio propio (tumarca.betogether.com.co)\",\"Pasarela de Pagos integrada y segura\"]',1,1,1,NULL,'2025-09-19 09:33:51'),(2,'Emprendedor','emprendedor',85000.00,20,50,5.09,900.00,'Para emprendedores en crecimiento','[\"20 productos en tu tienda\",\"Puntos Colombia para fidelizaci\\u00f3n\",\"Sin marca de agua de BeTogether\",\"Log\\u00edstica prioritaria AM\",\"Programa Embajadores de marca\"]',1,0,2,NULL,'2025-09-05 23:27:57'),(3,'Emprendedor PRO✨','emprendedor-pro',110000.00,50,60,5.09,800.00,'Máximo poder para tu negocio','[\"50 productos en tu tienda\",\"Todo lo del plan Emprendedor +\",\"Prioridad AM y PM en entregas\",\"IA para Creativos - Genera piezas para Instagram y Facebook\",\"IA para Estrategia - Planes de marketing de 15 d\\u00edas\"]',1,0,3,NULL,'2025-09-19 09:54:24'),(4,'Crecimiento🚀','crecimiento',500000.00,200,100,4.09,700.00,'Para marcas establecidas','[\"200 productos en tu tienda\",\"Todo lo del plan PRO +\",\"Embajador de Marca en marketing\",\"Descuento en eventos presenciales\",\"1 Sesi\\u00f3n mensual con profesional\",\"Opci\\u00f3n Pasaporte a Canad\\u00e1\"]',1,0,4,NULL,'2025-09-19 09:53:56'),(6,'🍁Pasaporte -  Oh! Canada','pasaporte-oh-canada',1700000.00,200,200,4.09,700.00,'Este paquete incluye la coordinación del envío de tu primer cajón de inventario a nuestro punto de recepción en Canadá, habilitando tu tienda para el comercio global.\r\n\r\n*Revisar terminos y condiciones del plan','[\"Acceso total a los beneficios de la plataforma\",\"Puedes enviar cantidades minimas de tu producto a nuestro centro de acopio en Toronto - Canada*\",\"Prioridad en recoleccion AM y PM\"]',1,1,5,'2025-09-19 09:50:20','2025-10-06 09:14:32');
/*!40000 ALTER TABLE `planes_membresia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `precios_productos`
--

DROP TABLE IF EXISTS `precios_productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `precios_productos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `producto_id` bigint(20) unsigned NOT NULL,
  `lista_precio_id` bigint(20) unsigned NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `precios_productos_producto_id_lista_precio_id_unique` (`producto_id`,`lista_precio_id`),
  KEY `precios_productos_lista_precio_id_foreign` (`lista_precio_id`),
  KEY `precios_productos_producto_id_activo_index` (`producto_id`,`activo`),
  CONSTRAINT `precios_productos_lista_precio_id_foreign` FOREIGN KEY (`lista_precio_id`) REFERENCES `listas_precios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `precios_productos_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=147 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `precios_productos`
--

LOCK TABLES `precios_productos` WRITE;
/*!40000 ALTER TABLE `precios_productos` DISABLE KEYS */;
INSERT INTO `precios_productos` VALUES (139,37,1,100000.00,1,'2025-11-07 00:07:19','2025-11-07 00:07:19'),(140,38,1,500000.00,1,'2025-11-07 19:41:50','2025-11-07 19:41:50'),(141,39,1,210000.00,1,'2025-12-09 21:33:13','2025-12-09 21:33:13'),(142,40,1,210000.00,1,'2025-12-09 22:52:44','2025-12-09 22:52:44'),(143,41,1,210000.00,1,'2025-12-09 23:01:47','2025-12-09 23:01:47'),(144,42,1,185000.00,1,'2025-12-09 23:07:08','2025-12-09 23:07:08'),(145,43,1,190000.00,1,'2025-12-09 23:11:20','2025-12-09 23:11:20'),(146,44,1,190000.00,1,'2025-12-09 23:17:19','2025-12-09 23:17:19');
/*!40000 ALTER TABLE `precios_productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `precios_variantes`
--

DROP TABLE IF EXISTS `precios_variantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `precios_variantes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `variante_producto_id` bigint(20) unsigned NOT NULL,
  `lista_precio_id` bigint(20) unsigned NOT NULL,
  `ajuste_precio` decimal(8,2) NOT NULL DEFAULT 0.00,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `precios_variantes_variante_producto_id_lista_precio_id_unique` (`variante_producto_id`,`lista_precio_id`),
  KEY `precios_variantes_lista_precio_id_foreign` (`lista_precio_id`),
  KEY `precios_variantes_variante_producto_id_activo_index` (`variante_producto_id`,`activo`),
  CONSTRAINT `precios_variantes_lista_precio_id_foreign` FOREIGN KEY (`lista_precio_id`) REFERENCES `listas_precios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `precios_variantes_variante_producto_id_foreign` FOREIGN KEY (`variante_producto_id`) REFERENCES `variantes_productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `precios_variantes`
--

LOCK TABLES `precios_variantes` WRITE;
/*!40000 ALTER TABLE `precios_variantes` DISABLE KEYS */;
/*!40000 ALTER TABLE `precios_variantes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint(20) unsigned DEFAULT NULL,
  `referencia` varchar(255) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `unidad_venta` varchar(255) NOT NULL,
  `unidad_empaque` varchar(255) NOT NULL,
  `extension` varchar(255) DEFAULT NULL,
  `categoria_id` bigint(20) unsigned NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `eliminado` tinyint(1) NOT NULL DEFAULT 0,
  `tiene_variantes` tinyint(1) NOT NULL DEFAULT 0,
  `controlar_stock` tinyint(1) NOT NULL DEFAULT 1,
  `permitir_venta_sin_stock` tinyint(1) NOT NULL DEFAULT 0,
  `info_envio` varchar(255) DEFAULT NULL,
  `dias_devolucion` varchar(255) DEFAULT NULL,
  `garantia` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `productos_empresa_referencia_unique` (`empresa_id`,`referencia`),
  KEY `productos_categoria_id_foreign` (`categoria_id`),
  KEY `productos_activo_categoria_id_index` (`activo`,`categoria_id`),
  KEY `productos_referencia_index` (`referencia`),
  KEY `productos_empresa_id_activo_index` (`empresa_id`,`activo`),
  KEY `productos_eliminado_index` (`eliminado`),
  CONSTRAINT `productos_categoria_id_foreign` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE,
  CONSTRAINT `productos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (37,5,'pantalon','Pantalon Azul','Comodo','Caja','Caja','Azul',18,1,0,0,0,1,'Se envia a todo el pais','5 dias','1 año','2025-11-07 00:07:19','2025-12-19 23:52:39'),(38,5,'camiseta roja','Camiseta roja','Un texto es una composición de signos codificados en un sistema de escritura que forma una unidad de sentido. También es una composición de caracteres imprimibles (con grafema) generados por un algoritmo de cifrado que, aunque no tienen sentido para cualquier persona, sí puede ser descifrado por su destinatario original. En otras palabras, un texto es un entramado de signos con una intención comunicativa que adquiere sentido en determinado contexto.\r\n\r\nLas ideas que comunica un texto están contenidas en lo que se suele denominar «macroproposiciones», unidades estructurales de nivel superior o global, que otorgan coherencia al texto constituyendo su hilo central, el esqueleto estructural que cohesiona elementos lingüísticos formales de alto nivel, como los títulos y subtítulos, la secuencia de párrafos, etc. En contraste, las «microproposiciones» son los elementos coadyuvantes de la cohesión de un texto, pero a nivel más particular o local. Esta distinción fue realizada por Teun van Dijk en 1980.[1]​\r\n\r\nEl nivel microestructural o local está asociado con el concepto de cohesión. Se refiere a uno de los fenómenos propios de la coherencia, el de las relaciones particulares y locales que se dan entre elementos lingüísticos, tanto los que remiten unos a otros como los que tienen la función de conectar y organizar.','Caja','caja','Roja',17,1,1,0,1,0,'Se le envia',NULL,NULL,'2025-11-07 19:41:50','2025-12-19 22:04:40'),(39,5,'SLP-1075','PLANCHA SUPER LOOK PROFESIONAL','SLP-1075 es potencia, suavidad y cuidado en una sola herramienta. Sus placas flotantes de 7 pulgadas con recubrimiento ionizado alisan en menos pasadas, dejando tu cabello brillante, sedoso y sin frizz. Calienta rapidísimo hasta 500 °F y mantiene la temperatura estable para resultados de salón desde casa.\r\n\r\nCon pantalla digital, voltaje dual, cable giratorio y apagado automático, está pensada para facilitarte la vida. Su tecnología de plasma de larga duración y la garantía que reemplaza por una nueva te aseguran calidad real.\r\nSLP-1075: lista para transformar tu rutina y tu cabello.','UNIDAD','CAJA','NEGRO',18,1,0,0,1,0,'ENVIO GRATIS',NULL,'1 AÑO','2025-12-09 21:33:13','2025-12-09 22:29:59'),(40,5,'S8599','PLANCHA KERATINA Y ACEITE DE ARGÁN','La plancha Remington de Argán y Keratina está diseñada para cuidar tu cabello mientras lo transforma. Sus placas flotantes están infusionadas con una mezcla premium de aceite de argán y keratina, una combinación que no solo alisa, sino que nutre, protege y realza el brillo natural en cada pasada.\r\n\r\nCalienta rápido, mantiene la temperatura estable y distribuye el calor de manera uniforme para que logres un alisado suave, brillante y sin frizz, incluso si tu cabello es rebelde o difícil de manejar. Su tecnología ayuda a sellar la cutícula, reduce el daño por calor y deja una sensación de suavidad real, esa que se nota al instante.\r\n\r\nCuenta con ajuste de temperatura, pantalla digital, placas que se deslizan sin jalar, cable giratorio y un sistema de seguridad que se adapta perfecto a tu rutina. Es una herramienta pensada para facilitarte la vida, para darte resultados de salón desde casa y para cuidar tu cabello mientras lo estilizas.','CAJA','CAJA','DORADO',18,1,0,0,1,0,'ENVIO GRATIS','Si recibes un producto que no es original, te devolvemos el 100% de tu dinero.','2 año','2025-12-09 22:52:44','2025-12-09 22:52:44'),(41,5,'S8510','PLANCHA CONTROL FRIZZ','La Plancha Control Frizz está creada para quienes quieren un cabello suave, brillante y sin frizz desde la primera pasada. Combina tecnología avanzada con una experiencia súper cómoda, logrando un alisado rápido, parejo y sin maltrato. Es una herramienta pensada para el día a día: eficiente, ligera y con resultados visibles desde el primer uso.\r\n\r\nSus placas flotantes con recubrimiento cerámico y microacondicionadores se deslizan con suavidad, evitando tirones y cuidando cada hebra. Esta mezcla de tecnología permite sellar la cutícula, dar un brillo más natural y crear un acabado pulido que se mantiene por más tiempo. Cada pasada es estable y uniforme gracias a su temperatura constante, ideal para proteger el cabello de daños causados por puntos calientes.\r\n\r\nLa tecnología anti-frizz trabaja desde la raíz del problema: reduce la electricidad estática, controla el volumen excesivo y ayuda a que el cabello luzca más manejable, suave y con movimiento. Es perfecta para cabellos que se esponjan con facilidad, reaccionan a la humedad o pierden la forma rápidamente.','CAJA','CAJA','MORADA',18,1,0,0,1,0,'ENVIO GRATIS','Si recibes un producto que no es original, te devolvemos el 100% de tu dinero.','2 año','2025-12-09 23:01:47','2025-12-09 23:01:47'),(42,5,'S9960','PLANCHA DE AGUACATE Y MACADAMIA','Diseñada para aportar nutrición real desde la primera pasada, la Plancha de Aguacate y Macadamia combina una tecnología de calor inteligente con un tratamiento protector integrado que transforma la experiencia de peinado. Sus placas flotantes con recubrimiento cerámico infusionado con aceites de aguacate y macadamia liberan microacondicionadores que se activan con el calor, permitiendo un deslizamiento suave, uniforme y delicado con cada movimiento.\r\n\r\nCada pasada ayuda a sellar la cutícula, controlar el frizz y proteger la fibra capilar, dejando un acabado más brillante, sedoso y manejable. La temperatura se mantiene estable en toda la superficie, evitando puntos calientes que pueden quemar o maltratar el cabello. Esto se traduce en resultados más duraderos y un peinado de aspecto saludable sin sacrificar la integridad del cabello.\r\n\r\nSu tecnología nutritiva trabaja desde adentro hacia afuera: reduce la estática, controla el volumen excesivo, aporta suavidad inmediata. Es ideal para cabellos resecos, maltratados, teñidos o con tendencia a encresparse, ya que aporta una sensación de reparación y control visible desde el primer uso.','CAJA','CAJA','VERDE',18,1,0,0,1,0,'ENVIO GRATIS','Si recibes un producto que no es original, te devolvemos el 100% de tu dinero.','1 AÑO','2025-12-09 23:07:08','2025-12-09 23:07:08'),(43,5,'S8500A','PLANCHA ACEITE DE ARGAN','La Remington S8500A con micro acondicionadores de Argán es una herramienta creada para quienes buscan un acabado realmente pulido sin sacrificar la salud del cabello. Su tecnología avanzada combina placas flotantes de cerámica con micro acondicionadores de Aceite de Argán, logrando un deslizamiento suave, un brillo visible desde la primera pasada y una protección continua contra el frizz y el daño por calor.\r\n\r\nCada placa libera micro acondicionadores que se activan con la temperatura, ayudando a nutrir, suavizar y sellar la cutícula, creando una barrera natural que controla la humedad y mantiene el peinado intacto por más tiempo. El resultado es un cabello más manejable, brillante y resistente al esponjamiento, incluso en climas difíciles.\r\n\r\nLa S8500A mantiene una temperatura uniforme en toda la placa, evitando puntos calientes que suelen dañar o quebrar el cabello. Gracias a su calentamiento rápido y sus múltiples niveles de temperatura, puedes personalizar tu estilizado de acuerdo a tu tipo de cabello: desde uno fino y delicado, hasta uno grueso y resistente.\r\n\r\nPensada para comodidad y precisión, incluye pantalla digital, placas largas para pasadas más rápidas, cable giratorio y un diseño ligero que facilita alisar, moldear o dar forma sin esfuerzo. Su ergonomía está diseñada para uso diario y para quienes desean resultados profesionales desde casa.\r\n\r\nLa Remington S8500A es perfecta para quienes buscan control de frizz, suavidad profunda, brillo natural y protección real mientras estilizan. Una herramienta que combina ciencia, cuidado capilar y rendimiento, logrando un acabado elegante, uniforme y duradero sin comprometer la salud del cabello.','CAJA','CAJA','VERDE',18,1,0,0,1,0,'ENVIO GRATIS','Si recibes un producto que no es original, te devolvemos el 100% de tu dinero.','1 AÑO','2025-12-09 23:11:20','2025-12-09 23:11:20'),(44,5,'S7740','PLANCHA TRIPLE INFUSION','La Triple Infusion es esa herramienta que transforma el cabello sin esfuerzo, combinando tecnología avanzada con un cuidado increíblemente nutritivo. Sus placas recubiertas con cerámica avanzada y una triple mezcla de aceites de aguacate, macadamia y argán liberan microacondicionadores que se activan con el calor, sellan la cutícula y dejan un brillo natural que se nota al instante.\r\n\r\nLas placas flotantes se adaptan a cada mechón para un deslizamiento suave, sin tirones ni maltrato, mientras la tecnología anti-frizz y antiestática reduce la esponjosidad y mantiene el cabello más controlado, incluso en humedad. ¿Resultado? Un acabado más liso, manejable y pulido con menos pasadas.\r\n\r\nLa temperatura regulable permite ajustar la potencia según tu tipo de cabello, evitando daños por exceso de calor, y su calentamiento rápido hace que esté lista en segundos para esas rutinas donde el tiempo siempre falta. Además, su estabilidad térmica mantiene la misma temperatura de raíz a puntas, garantizando un acabado uniforme sin tener que insistir demasiado.\r\n\r\nPensada para hacerte la vida más fácil, incluye un diseño cómodo, cable giratorio, seguridad integrada y la versatilidad necesaria para alisar, crear ondas o estilizar como prefieras, siempre con un acabado brillante, suave y nutrido.','CAJA','CAJA','MORADA',18,1,0,0,1,0,'ENVIO GRATIS','Si recibes un producto que no es original, te devolvemos el 100% de tu dinero.','1 AÑO','2025-12-09 23:17:19','2025-12-09 23:17:19');
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reacciones_calificaciones`
--

DROP TABLE IF EXISTS `reacciones_calificaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reacciones_calificaciones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `calificacion_id` bigint(20) unsigned NOT NULL,
  `visitor_id` varchar(64) NOT NULL,
  `emoji` enum('hearts','wink','kiss','thumbsup') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reaccion_unica` (`calificacion_id`,`visitor_id`,`emoji`),
  CONSTRAINT `reacciones_calificaciones_calificacion_id_foreign` FOREIGN KEY (`calificacion_id`) REFERENCES `calificaciones_productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reacciones_calificaciones`
--

LOCK TABLES `reacciones_calificaciones` WRITE;
/*!40000 ALTER TABLE `reacciones_calificaciones` DISABLE KEYS */;
INSERT INTO `reacciones_calificaciones` VALUES (3,9,'f8ca47cf43ec27c9cd9154b8ee4a72b2ac6a9ed03e7180163f197110b6a03a0e','hearts','2026-01-06 05:13:33','2026-01-06 05:13:33');
/*!40000 ALTER TABLE `reacciones_calificaciones` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'admin','web',NULL,NULL),(2,'vendedor','web',NULL,NULL),(3,'empresa','web',NULL,NULL),(4,'cliente','web','2025-12-11 18:02:17','2025-12-11 18:02:17');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seo`
--

DROP TABLE IF EXISTS `seo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seo` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` bigint(20) unsigned NOT NULL,
  `meta_title` varchar(150) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` varchar(500) DEFAULT NULL,
  `canonical_url` varchar(500) DEFAULT NULL,
  `robots` enum('index,follow','noindex,follow','index,nofollow','noindex,nofollow') NOT NULL DEFAULT 'index,follow',
  `og_title` varchar(150) DEFAULT NULL,
  `og_description` text DEFAULT NULL,
  `og_image` varchar(500) DEFAULT NULL,
  `og_type` enum('website','article','product','business.business') NOT NULL DEFAULT 'website',
  `og_url` varchar(500) DEFAULT NULL,
  `og_site_name` varchar(100) DEFAULT NULL,
  `twitter_card` enum('summary','summary_large_image','app','player') NOT NULL DEFAULT 'summary_large_image',
  `twitter_title` varchar(150) DEFAULT NULL,
  `twitter_description` text DEFAULT NULL,
  `twitter_image` varchar(500) DEFAULT NULL,
  `twitter_site` varchar(50) DEFAULT NULL,
  `twitter_creator` varchar(50) DEFAULT NULL,
  `schema_markup` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`schema_markup`)),
  `focus_keyword` varchar(100) DEFAULT NULL,
  `breadcrumb_title` text DEFAULT NULL,
  `sitemap_include` tinyint(1) NOT NULL DEFAULT 1,
  `sitemap_priority` decimal(2,1) NOT NULL DEFAULT 0.8,
  `sitemap_changefreq` enum('always','hourly','daily','weekly','monthly','yearly','never') NOT NULL DEFAULT 'weekly',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `seo_score` int(11) DEFAULT NULL,
  `seo_analysis` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `seo_page_id_index` (`page_id`),
  KEY `seo_is_active_index` (`is_active`),
  KEY `seo_focus_keyword_index` (`focus_keyword`),
  CONSTRAINT `seo_page_id_foreign` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seo`
--

LOCK TABLES `seo` WRITE;
/*!40000 ALTER TABLE `seo` DISABLE KEYS */;
INSERT INTO `seo` VALUES (1,1,'BeTogether - Emprende, vende, envia  y exporta','Únete a la primera membresía en Colombia para emprendedores y fundaciones. Tienda online, pasarela de pagos, eventos exclusivos  y crecimiento sin límites. ¡Regístrate gratis!','emprendimiento colombia,emprendedores bogota, fundaciones bogota, fundaciones colombia, tienda online, eventos comerciales colombia, festivales colombia,, membresía empresarial, betogether, plataforma emprendedores,puntos colombia, logistica integrada, envios para emprendedores, domicilios para tiendas virtuales','http://localhost','index,follow','BeTogether - Tu ecosistema de crecimiento🚀','Crea tu tienda online, recibe pagos seguros, participa en eventos y exporta a Canadá. BeTogether es el ecosistema para emprendedores y fundaciones en Colombia.','seo/og/M1KyP0q1tIsREKlMGDtw3OlHFGkLBRmBDE3agxgh.png','website','http://localhost','Betogether','summary_large_image','Betogether - Plataforma para Emprendedores 🚀','Únete a BeTogether: plataforma de membresía para emprendedores Vende online, participa en eventos y lleva tus productos a Canadá 🇨🇦','seo/twitter/1zJiGycOVgKaWDbsykGTrWfh9H9aikyvgeeKj0Ed.png',NULL,NULL,NULL,'emprendimiento colombia','Inicio',1,0.8,'weekly',1,NULL,NULL,'2025-09-15 14:47:31','2025-10-06 09:16:33');
/*!40000 ALTER TABLE `seo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `solicitudes_cotizacion`
--

DROP TABLE IF EXISTS `solicitudes_cotizacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `solicitudes_cotizacion` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `numero_solicitud` varchar(255) NOT NULL,
  `cliente_id` bigint(20) unsigned NOT NULL,
  `empresa_id` bigint(20) unsigned DEFAULT NULL,
  `enlace_acceso_id` bigint(20) unsigned DEFAULT NULL,
  `estado` enum('pendiente','aplicada') NOT NULL DEFAULT 'pendiente',
  `monto_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `notas_cliente` text DEFAULT NULL,
  `observaciones_admin` text DEFAULT NULL,
  `aplicada_en` datetime DEFAULT NULL,
  `aplicada_por` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `solicitudes_cotizacion_numero_solicitud_unique` (`numero_solicitud`),
  KEY `solicitudes_cotizacion_enlace_acceso_id_foreign` (`enlace_acceso_id`),
  KEY `solicitudes_cotizacion_aplicada_por_foreign` (`aplicada_por`),
  KEY `solicitudes_cotizacion_estado_created_at_index` (`estado`,`created_at`),
  KEY `solicitudes_cotizacion_cliente_id_estado_index` (`cliente_id`,`estado`),
  KEY `solicitudes_cotizacion_empresa_id_estado_index` (`empresa_id`,`estado`),
  CONSTRAINT `solicitudes_cotizacion_aplicada_por_foreign` FOREIGN KEY (`aplicada_por`) REFERENCES `users` (`id`),
  CONSTRAINT `solicitudes_cotizacion_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `solicitudes_cotizacion_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `solicitudes_cotizacion_enlace_acceso_id_foreign` FOREIGN KEY (`enlace_acceso_id`) REFERENCES `enlaces_acceso` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `solicitudes_cotizacion`
--

LOCK TABLES `solicitudes_cotizacion` WRITE;
/*!40000 ALTER TABLE `solicitudes_cotizacion` DISABLE KEYS */;
/*!40000 ALTER TABLE `solicitudes_cotizacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_productos`
--

DROP TABLE IF EXISTS `stock_productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_productos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `producto_id` bigint(20) unsigned NOT NULL,
  `variante_producto_id` bigint(20) unsigned DEFAULT NULL,
  `cantidad_disponible` int(11) NOT NULL DEFAULT 0,
  `cantidad_reservada` int(11) NOT NULL DEFAULT 0,
  `stock_minimo` int(11) NOT NULL DEFAULT 0,
  `stock_maximo` int(11) DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `alerta_stock_bajo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stock_productos_producto_id_variante_producto_id_unique` (`producto_id`,`variante_producto_id`),
  KEY `stock_productos_variante_producto_id_foreign` (`variante_producto_id`),
  KEY `stock_productos_producto_id_cantidad_disponible_index` (`producto_id`,`cantidad_disponible`),
  CONSTRAINT `stock_productos_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_productos_variante_producto_id_foreign` FOREIGN KEY (`variante_producto_id`) REFERENCES `variantes_productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_productos`
--

LOCK TABLES `stock_productos` WRITE;
/*!40000 ALTER TABLE `stock_productos` DISABLE KEYS */;
INSERT INTO `stock_productos` VALUES (64,38,NULL,1,0,0,NULL,NULL,NULL,1,'2025-11-07 19:41:50','2025-12-09 22:46:17'),(65,39,NULL,0,0,0,10,'1',NULL,1,'2025-12-09 21:33:13','2025-12-09 21:43:14'),(66,40,NULL,7,0,1,10,NULL,NULL,1,'2025-12-09 22:52:44','2025-12-10 19:10:31'),(67,41,NULL,1,0,2,15,NULL,NULL,1,'2025-12-09 23:01:47','2025-12-09 23:01:47'),(68,42,NULL,4,0,1,8,NULL,NULL,1,'2025-12-09 23:07:08','2025-12-09 23:07:08'),(69,43,NULL,1,0,1,4,NULL,NULL,1,'2025-12-09 23:11:20','2026-01-06 02:10:16'),(70,44,NULL,4,0,1,8,NULL,NULL,1,'2025-12-09 23:17:19','2025-12-09 23:17:19');
/*!40000 ALTER TABLE `stock_productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `templates_tienda`
--

DROP TABLE IF EXISTS `templates_tienda`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `templates_tienda` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(255) NOT NULL COMMENT 'Identificador único del template (ej: default, brasilia, minimal)',
  `nombre` varchar(255) NOT NULL COMMENT 'Nombre descriptivo del template',
  `descripcion` text DEFAULT NULL COMMENT 'Descripción del template',
  `vista_index` varchar(255) NOT NULL COMMENT 'Ruta de la vista index (ej: tienda.index)',
  `vista_categoria` varchar(255) NOT NULL COMMENT 'Ruta de la vista de categorías',
  `vista_producto` varchar(255) NOT NULL COMMENT 'Ruta de la vista de producto',
  `layout` varchar(255) NOT NULL COMMENT 'Ruta del layout base (ej: tienda.layout)',
  `preview_image` varchar(255) DEFAULT NULL COMMENT 'Imagen de preview del template',
  `activo` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Estado del template',
  `configuracion` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Configuración adicional (colores, fuentes, etc)' CHECK (json_valid(`configuracion`)),
  `orden` int(11) NOT NULL DEFAULT 0 COMMENT 'Orden de visualización',
  `es_default` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Indica si es el template por defecto',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `templates_tienda_codigo_unique` (`codigo`),
  KEY `templates_tienda_activo_orden_index` (`activo`,`orden`),
  KEY `templates_tienda_es_default_index` (`es_default`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `templates_tienda`
--

LOCK TABLES `templates_tienda` WRITE;
/*!40000 ALTER TABLE `templates_tienda` DISABLE KEYS */;
INSERT INTO `templates_tienda` VALUES (1,'default','Template Clásico','Template elegante y moderno con diseño limpio. Ideal para todo tipo de productos.','tienda.index','tienda.categoria','tienda.producto','tienda.layout','images/templates/default-preview.jpg',1,'{\"color_primario\":\"#0d6efd\",\"fuente_principal\":\"Roboto\"}',1,1,'2025-10-17 17:16:49','2025-10-17 17:16:49'),(2,'brasilia','Template Brasilia','Template dinámico inspirado en tiendas de moda. Con carruseles, animaciones y diseño vibrante.','tienda.brasilia_index','tienda.brasilia_categoria','tienda.brasilia_producto','tienda.brasilia_layout','images/templates/brasilia-preview.jpg',1,'{\"color_primario\":\"#1a1a1a\",\"color_secundario\":\"#78b13f\",\"mostrar_adbars\":true,\"habilitar_animaciones\":true}',2,0,'2025-10-17 17:16:49','2025-10-17 17:16:49'),(3,'lima','Template Lima','Template moderno con header sticky, sliders Swiper y diseño profesional. Ideal para tiendas de indumentaria.','tienda.lima_index','tienda.lima_categoria','tienda.lima_producto','tienda.lima_layout','images/templates/lima-preview.svg',1,'{\"sticky_header\":true,\"show_adbar\":true,\"show_topbar\":true,\"product_grid_columns_mobile\":2,\"product_grid_columns_desktop\":4,\"enable_animations\":true}',3,0,'2025-10-29 01:03:43','2025-10-29 01:03:43');
/*!40000 ALTER TABLE `templates_tienda` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transacciones_pago`
--

DROP TABLE IF EXISTS `transacciones_pago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transacciones_pago` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `compra_id` bigint(20) unsigned NOT NULL,
  `pasarela` varchar(255) NOT NULL DEFAULT 'wompi',
  `referencia_transaccion` varchar(255) NOT NULL,
  `id_transaccion_pasarela` varchar(255) DEFAULT NULL,
  `monto` decimal(12,2) NOT NULL,
  `moneda` varchar(3) NOT NULL DEFAULT 'COP',
  `estado` enum('pendiente','procesando','aprobada','rechazada','error','reembolsada') NOT NULL DEFAULT 'pendiente',
  `metodo_pago` varchar(255) DEFAULT NULL,
  `respuesta_pasarela` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`respuesta_pasarela`)),
  `codigo_autorizacion` varchar(255) DEFAULT NULL,
  `fecha_procesamiento` timestamp NULL DEFAULT NULL,
  `mensaje_error` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transacciones_pago_referencia_transaccion_unique` (`referencia_transaccion`),
  KEY `transacciones_pago_referencia_transaccion_estado_index` (`referencia_transaccion`,`estado`),
  KEY `transacciones_pago_compra_id_estado_index` (`compra_id`,`estado`),
  CONSTRAINT `transacciones_pago_compra_id_foreign` FOREIGN KEY (`compra_id`) REFERENCES `compras` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transacciones_pago`
--

LOCK TABLES `transacciones_pago` WRITE;
/*!40000 ALTER TABLE `transacciones_pago` DISABLE KEYS */;
INSERT INTO `transacciones_pago` VALUES (23,16,'wompi','TRX-20251130113754-5K3GOG','12001030-1764520704-74610',500000.00,'COP','aprobada','CARD','{\"id\":\"12001030-1764520704-74610\",\"created_at\":\"2025-11-30T16:38:26.063Z\",\"finalized_at\":\"2025-11-30T16:38:27.399Z\",\"amount_in_cents\":50000000,\"reference\":\"TRX-20251130113754-5K3GOG\",\"currency\":\"COP\",\"payment_method_type\":\"CARD\",\"payment_method\":{\"type\":\"CARD\",\"extra\":{\"name\":\"VISA-4242\",\"brand\":\"VISA\",\"card_type\":\"CREDIT\",\"last_four\":\"4242\",\"is_three_ds\":true,\"three_ds_auth\":{\"current_step\":\"AUTHENTICATION\",\"current_step_status\":\"COMPLETED\"},\"three_ds_auth_type\":null,\"external_identifier\":\"HmCAQ86avS\",\"processor_response_code\":\"00\"},\"installments\":1,\"is_click_to_pay\":false},\"payment_link_id\":null,\"redirect_url\":\"https:\\/\\/darkorchid-lapwing-261365.hostingersite.com\\/pago\\/confirmacion\\/TRX-20251130113754-5K3GOG?slug=esnova-market\",\"status\":\"APPROVED\",\"status_message\":null,\"merchant\":{\"id\":2001030,\"name\":\"Esnova Market\",\"legal_name\":\"Juan Esteban Salazar Quintero\",\"contact_name\":\"Esteban Salazar\",\"phone_number\":\"3246598065\",\"logo_url\":null,\"legal_id_type\":\"CC\",\"email\":\"esnovamarketempresa@gmail.com\",\"legal_id\":\"1007327015\",\"public_key\":\"pub_test_V5pSXU9YDcB7wRci70kUdtruXPpnWQEx\"},\"taxes\":[],\"tip_in_cents\":null}',NULL,'2025-11-30 11:38:29',NULL,'2025-11-30 11:37:54','2025-11-30 11:38:29'),(24,17,'wompi','TRX-20251201092808-UNX1XD',NULL,500000.00,'COP','pendiente',NULL,NULL,NULL,NULL,NULL,'2025-12-01 09:28:08','2025-12-01 09:28:08'),(25,18,'wompi','TRX-20251201093131-1JA4BQ','12001030-1764599528-83841',500000.00,'COP','aprobada','CARD','{\"id\":\"12001030-1764599528-83841\",\"created_at\":\"2025-12-01T14:32:08.887Z\",\"finalized_at\":\"2025-12-01T14:32:09.235Z\",\"amount_in_cents\":50000000,\"reference\":\"TRX-20251201093131-1JA4BQ\",\"currency\":\"COP\",\"payment_method_type\":\"CARD\",\"payment_method\":{\"type\":\"CARD\",\"extra\":{\"name\":\"VISA-4242\",\"brand\":\"VISA\",\"card_type\":\"CREDIT\",\"last_four\":\"4242\",\"is_three_ds\":true,\"three_ds_auth\":{\"current_step\":\"AUTHENTICATION\",\"current_step_status\":\"COMPLETED\"},\"three_ds_auth_type\":null,\"external_identifier\":\"fWFa6cqS3l\",\"processor_response_code\":\"00\"},\"installments\":1,\"is_click_to_pay\":false},\"payment_link_id\":null,\"redirect_url\":\"https:\\/\\/darkorchid-lapwing-261365.hostingersite.com\\/pago\\/confirmacion\\/TRX-20251201093131-1JA4BQ?slug=esnova-market\",\"status\":\"APPROVED\",\"status_message\":null,\"merchant\":{\"id\":2001030,\"name\":\"Esnova Market\",\"legal_name\":\"Juan Esteban Salazar Quintero\",\"contact_name\":\"Esteban Salazar\",\"phone_number\":\"3246598065\",\"logo_url\":null,\"legal_id_type\":\"CC\",\"email\":\"esnovamarketempresa@gmail.com\",\"legal_id\":\"1007327015\",\"public_key\":\"pub_test_V5pSXU9YDcB7wRci70kUdtruXPpnWQEx\"},\"taxes\":[],\"tip_in_cents\":null}',NULL,'2025-12-01 09:32:16',NULL,'2025-12-01 09:31:31','2025-12-01 09:32:16'),(26,19,'wompi','TRX-20251201221232-INCOKN',NULL,500000.00,'COP','pendiente',NULL,NULL,NULL,NULL,NULL,'2025-12-01 22:12:32','2025-12-01 22:12:32'),(27,20,'wompi','TRX-20251209211954-05T68W',NULL,500000.00,'COP','pendiente',NULL,NULL,NULL,NULL,NULL,'2025-12-09 21:19:54','2025-12-09 21:19:54'),(28,21,'wompi','TRX-20251209214314-HC3LZZ',NULL,210000.00,'COP','pendiente',NULL,NULL,NULL,NULL,NULL,'2025-12-09 21:43:14','2025-12-09 21:43:14'),(29,22,'wompi','TRX-20251210191031-NPVIN8',NULL,210000.00,'COP','pendiente',NULL,NULL,NULL,NULL,NULL,'2025-12-10 19:10:31','2025-12-10 19:10:31'),(30,23,'otro','TRX-20260105211031-PAPDV3',NULL,380000.00,'COP','aprobada','pago_manual',NULL,NULL,'2026-01-06 02:12:04',NULL,'2026-01-06 02:10:31','2026-01-06 02:12:04');
/*!40000 ALTER TABLE `transacciones_pago` ENABLE KEYS */;
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
  `telefono` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `ultimo_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','admin@admin.com',NULL,NULL,1,'$2y$12$bbsBcqwjtCSvgSDE3T9My.u6NayJeL5Y49nWLJn4pshd4qRiT544O','ZVGbF1lSbzBcn3ptnkOgKHDh5a0Mhw3z9bMMsh64JxNNMCCjj4OlkebNsBQG',NULL,NULL,'2025-08-26 02:06:16'),(14,'Santi Bellaizan','vblogsanti@gmail.com',NULL,NULL,1,'$2y$10$JbwOsfwY5Rg6iG10I0Aj..nRutHJ3Y1Psw3gPnm1sR1/VuV8K7dda',NULL,NULL,'2025-12-11 19:35:55','2025-12-11 19:35:55');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `variantes_productos`
--

DROP TABLE IF EXISTS `variantes_productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `variantes_productos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `producto_id` bigint(20) unsigned NOT NULL,
  `talla` varchar(255) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `sku` varchar(255) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `variantes_producto_sku_unique` (`producto_id`,`sku`),
  UNIQUE KEY `variantes_productos_producto_id_talla_color_unique` (`producto_id`,`talla`,`color`),
  KEY `variantes_productos_producto_id_activo_index` (`producto_id`,`activo`),
  CONSTRAINT `variantes_productos_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `variantes_productos`
--

LOCK TABLES `variantes_productos` WRITE;
/*!40000 ALTER TABLE `variantes_productos` DISABLE KEYS */;
/*!40000 ALTER TABLE `variantes_productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'snova'
--

--
-- Dumping routines for database 'snova'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-11 15:10:45
