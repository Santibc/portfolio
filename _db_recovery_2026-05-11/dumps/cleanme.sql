-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: cleanme
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
-- Current Database: `cleanme`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `cleanme` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `cleanme`;

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `actualizaciones_precios`
--

LOCK TABLES `actualizaciones_precios` WRITE;
/*!40000 ALTER TABLE `actualizaciones_precios` DISABLE KEYS */;
INSERT INTO `actualizaciones_precios` VALUES (2,1,'completado','orao.xlsx','actualizaciones_precios/1754604744_orao.xlsx',18,13,5,'[{\"fila\":2,\"referencia\":\"lap-azul\",\"mensaje\":\"No se encontraron precios v\\u00e1lidos para actualizar\",\"timestamp\":\"2025-08-07T22:12:25.045281Z\"},{\"fila\":3,\"referencia\":\"lap-arojo\",\"mensaje\":\"No se encontraron precios v\\u00e1lidos para actualizar\",\"timestamp\":\"2025-08-07T22:12:25.047218Z\"},{\"fila\":5,\"referencia\":\"lap-azul2\",\"mensaje\":\"No se encontraron precios v\\u00e1lidos para actualizar\",\"timestamp\":\"2025-08-07T22:12:25.057926Z\"},{\"fila\":6,\"referencia\":\"lap-azul9\",\"mensaje\":\"No se encontraron precios v\\u00e1lidos para actualizar\",\"timestamp\":\"2025-08-07T22:12:25.059919Z\"},{\"fila\":7,\"referencia\":\"lap-azul3423\",\"mensaje\":\"No se encontraron precios v\\u00e1lidos para actualizar\",\"timestamp\":\"2025-08-07T22:12:25.061973Z\"}]','[{\"fila\":4,\"referencia\":\"lapmorado\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"100000.00\",\"precio_nuevo\":100000,\"timestamp\":\"2025-08-07T22:12:25.056026Z\"},{\"fila\":8,\"referencia\":\"Rojo\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"1000.00\",\"precio_nuevo\":1000,\"timestamp\":\"2025-08-07T22:12:25.067516Z\"},{\"fila\":8,\"referencia\":\"Rojo\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"2000.00\",\"precio_nuevo\":2000,\"timestamp\":\"2025-08-07T22:12:25.072690Z\"},{\"fila\":8,\"referencia\":\"Rojo\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"3000.00\",\"precio_nuevo\":3000,\"timestamp\":\"2025-08-07T22:12:25.078252Z\"},{\"fila\":9,\"referencia\":\"lap-azul3\",\"lista_precio\":\"Export 1\",\"precio_anterior\":null,\"precio_nuevo\":99999,\"timestamp\":\"2025-08-07T22:12:25.099226Z\"},{\"fila\":9,\"referencia\":\"lap-azul3\",\"lista_precio\":\"Export 2\",\"precio_anterior\":null,\"precio_nuevo\":88888,\"timestamp\":\"2025-08-07T22:12:25.106654Z\"},{\"fila\":9,\"referencia\":\"lap-azul3\",\"lista_precio\":\"Local 1\",\"precio_anterior\":null,\"precio_nuevo\":777777,\"timestamp\":\"2025-08-07T22:12:25.116647Z\"},{\"fila\":10,\"referencia\":\"Negro\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"2222222.00\",\"precio_nuevo\":111111,\"timestamp\":\"2025-08-07T22:12:25.130873Z\"},{\"fila\":10,\"referencia\":\"Negro\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"333333.00\",\"precio_nuevo\":1111111,\"timestamp\":\"2025-08-07T22:12:25.143167Z\"},{\"fila\":10,\"referencia\":\"Negro\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"444444.00\",\"precio_nuevo\":111111,\"timestamp\":\"2025-08-07T22:12:25.155305Z\"},{\"fila\":10,\"referencia\":\"Negro\",\"lista_precio\":\"Local 2\",\"precio_anterior\":\"6666.00\",\"precio_nuevo\":111111,\"timestamp\":\"2025-08-07T22:12:25.169176Z\"},{\"fila\":10,\"referencia\":\"Negro\",\"lista_precio\":\"Local 3\",\"precio_anterior\":null,\"precio_nuevo\":111111,\"timestamp\":\"2025-08-07T22:12:25.180548Z\"},{\"fila\":10,\"referencia\":\"Negro\",\"lista_precio\":\"Local 4\",\"precio_anterior\":\"111111.00\",\"precio_nuevo\":111111,\"timestamp\":\"2025-08-07T22:12:25.190625Z\"},{\"fila\":11,\"referencia\":\"Prueba sin variante con valor\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"100.00\",\"precio_nuevo\":100,\"timestamp\":\"2025-08-07T22:12:25.201888Z\"},{\"fila\":11,\"referencia\":\"Prueba sin variante con valor\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"1002.00\",\"precio_nuevo\":1002,\"timestamp\":\"2025-08-07T22:12:25.210259Z\"},{\"fila\":11,\"referencia\":\"Prueba sin variante con valor\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"1003.00\",\"precio_nuevo\":1003,\"timestamp\":\"2025-08-07T22:12:25.219028Z\"},{\"fila\":11,\"referencia\":\"Prueba sin variante con valor\",\"lista_precio\":\"Local 2\",\"precio_anterior\":\"1004.00\",\"precio_nuevo\":1004,\"timestamp\":\"2025-08-07T22:12:25.229034Z\"},{\"fila\":11,\"referencia\":\"Prueba sin variante con valor\",\"lista_precio\":\"Local 3\",\"precio_anterior\":\"1005.00\",\"precio_nuevo\":1005,\"timestamp\":\"2025-08-07T22:12:25.238123Z\"},{\"fila\":11,\"referencia\":\"Prueba sin variante con valor\",\"lista_precio\":\"Local 4\",\"precio_anterior\":\"1006.00\",\"precio_nuevo\":1006,\"timestamp\":\"2025-08-07T22:12:25.248008Z\"},{\"fila\":12,\"referencia\":\"Prueba con variante con valor\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"1.00\",\"precio_nuevo\":1,\"timestamp\":\"2025-08-07T22:12:25.261553Z\"},{\"fila\":12,\"referencia\":\"Prueba con variante con valor\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"2.00\",\"precio_nuevo\":2,\"timestamp\":\"2025-08-07T22:12:25.272020Z\"},{\"fila\":12,\"referencia\":\"Prueba con variante con valor\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"3.00\",\"precio_nuevo\":3,\"timestamp\":\"2025-08-07T22:12:25.282215Z\"},{\"fila\":12,\"referencia\":\"Prueba con variante con valor\",\"lista_precio\":\"Local 2\",\"precio_anterior\":\"4.00\",\"precio_nuevo\":4,\"timestamp\":\"2025-08-07T22:12:25.293801Z\"},{\"fila\":12,\"referencia\":\"Prueba con variante con valor\",\"lista_precio\":\"Local 3\",\"precio_anterior\":\"5.00\",\"precio_nuevo\":5,\"timestamp\":\"2025-08-07T22:12:25.304042Z\"},{\"fila\":12,\"referencia\":\"Prueba con variante con valor\",\"lista_precio\":\"Local 4\",\"precio_anterior\":\"6.00\",\"precio_nuevo\":6,\"timestamp\":\"2025-08-07T22:12:25.313016Z\"},{\"fila\":13,\"referencia\":\"producto con dos imagenes\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"1.00\",\"precio_nuevo\":1,\"timestamp\":\"2025-08-07T22:12:25.323078Z\"},{\"fila\":13,\"referencia\":\"producto con dos imagenes\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"2.00\",\"precio_nuevo\":2,\"timestamp\":\"2025-08-07T22:12:25.334014Z\"},{\"fila\":13,\"referencia\":\"producto con dos imagenes\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"3.00\",\"precio_nuevo\":3,\"timestamp\":\"2025-08-07T22:12:25.344460Z\"},{\"fila\":13,\"referencia\":\"producto con dos imagenes\",\"lista_precio\":\"Local 2\",\"precio_anterior\":\"5.00\",\"precio_nuevo\":5,\"timestamp\":\"2025-08-07T22:12:25.354922Z\"},{\"fila\":13,\"referencia\":\"producto con dos imagenes\",\"lista_precio\":\"Local 3\",\"precio_anterior\":\"7.00\",\"precio_nuevo\":7,\"timestamp\":\"2025-08-07T22:12:25.366279Z\"},{\"fila\":13,\"referencia\":\"producto con dos imagenes\",\"lista_precio\":\"Local 4\",\"precio_anterior\":\"8.00\",\"precio_nuevo\":8,\"timestamp\":\"2025-08-07T22:12:25.374482Z\"},{\"fila\":14,\"referencia\":\"aaaaa\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"1.00\",\"precio_nuevo\":1,\"timestamp\":\"2025-08-07T22:12:25.384907Z\"},{\"fila\":14,\"referencia\":\"aaaaa\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"2.00\",\"precio_nuevo\":2,\"timestamp\":\"2025-08-07T22:12:25.393257Z\"},{\"fila\":14,\"referencia\":\"aaaaa\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"3.00\",\"precio_nuevo\":3,\"timestamp\":\"2025-08-07T22:12:25.402892Z\"},{\"fila\":14,\"referencia\":\"aaaaa\",\"lista_precio\":\"Local 2\",\"precio_anterior\":\"4.00\",\"precio_nuevo\":4,\"timestamp\":\"2025-08-07T22:12:25.413377Z\"},{\"fila\":14,\"referencia\":\"aaaaa\",\"lista_precio\":\"Local 3\",\"precio_anterior\":\"5.00\",\"precio_nuevo\":5,\"timestamp\":\"2025-08-07T22:12:25.423100Z\"},{\"fila\":14,\"referencia\":\"aaaaa\",\"lista_precio\":\"Local 4\",\"precio_anterior\":\"6.00\",\"precio_nuevo\":6,\"timestamp\":\"2025-08-07T22:12:25.433398Z\"},{\"fila\":15,\"referencia\":\"xxxxxxxxxxxxxxxxx\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"1.00\",\"precio_nuevo\":1,\"timestamp\":\"2025-08-07T22:12:25.445746Z\"},{\"fila\":15,\"referencia\":\"xxxxxxxxxxxxxxxxx\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"2.00\",\"precio_nuevo\":2,\"timestamp\":\"2025-08-07T22:12:25.454978Z\"},{\"fila\":15,\"referencia\":\"xxxxxxxxxxxxxxxxx\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"3.00\",\"precio_nuevo\":3,\"timestamp\":\"2025-08-07T22:12:25.466458Z\"},{\"fila\":15,\"referencia\":\"xxxxxxxxxxxxxxxxx\",\"lista_precio\":\"Local 2\",\"precio_anterior\":\"4.00\",\"precio_nuevo\":4,\"timestamp\":\"2025-08-07T22:12:25.476246Z\"},{\"fila\":15,\"referencia\":\"xxxxxxxxxxxxxxxxx\",\"lista_precio\":\"Local 3\",\"precio_anterior\":\"6.00\",\"precio_nuevo\":6,\"timestamp\":\"2025-08-07T22:12:25.485367Z\"},{\"fila\":15,\"referencia\":\"xxxxxxxxxxxxxxxxx\",\"lista_precio\":\"Local 4\",\"precio_anterior\":\"5.00\",\"precio_nuevo\":5,\"timestamp\":\"2025-08-07T22:12:25.496932Z\"},{\"fila\":16,\"referencia\":\"pppppppppp\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"1.00\",\"precio_nuevo\":1,\"timestamp\":\"2025-08-07T22:12:25.509220Z\"},{\"fila\":16,\"referencia\":\"pppppppppp\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"2.00\",\"precio_nuevo\":2,\"timestamp\":\"2025-08-07T22:12:25.519061Z\"},{\"fila\":16,\"referencia\":\"pppppppppp\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"3.00\",\"precio_nuevo\":3,\"timestamp\":\"2025-08-07T22:12:25.530263Z\"},{\"fila\":16,\"referencia\":\"pppppppppp\",\"lista_precio\":\"Local 2\",\"precio_anterior\":\"4.00\",\"precio_nuevo\":4,\"timestamp\":\"2025-08-07T22:12:25.541628Z\"},{\"fila\":16,\"referencia\":\"pppppppppp\",\"lista_precio\":\"Local 3\",\"precio_anterior\":\"5.00\",\"precio_nuevo\":5,\"timestamp\":\"2025-08-07T22:12:25.552146Z\"},{\"fila\":16,\"referencia\":\"pppppppppp\",\"lista_precio\":\"Local 4\",\"precio_anterior\":\"6.00\",\"precio_nuevo\":6,\"timestamp\":\"2025-08-07T22:12:25.562400Z\"},{\"fila\":17,\"referencia\":\"lap-azul944587\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"1.00\",\"precio_nuevo\":1,\"timestamp\":\"2025-08-07T22:12:25.575999Z\"},{\"fila\":17,\"referencia\":\"lap-azul944587\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"2.00\",\"precio_nuevo\":2,\"timestamp\":\"2025-08-07T22:12:25.586678Z\"},{\"fila\":17,\"referencia\":\"lap-azul944587\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"3.00\",\"precio_nuevo\":3,\"timestamp\":\"2025-08-07T22:12:25.597114Z\"},{\"fila\":17,\"referencia\":\"lap-azul944587\",\"lista_precio\":\"Local 2\",\"precio_anterior\":\"4.00\",\"precio_nuevo\":4,\"timestamp\":\"2025-08-07T22:12:25.609743Z\"},{\"fila\":17,\"referencia\":\"lap-azul944587\",\"lista_precio\":\"Local 3\",\"precio_anterior\":\"5.00\",\"precio_nuevo\":5,\"timestamp\":\"2025-08-07T22:12:25.620071Z\"},{\"fila\":17,\"referencia\":\"lap-azul944587\",\"lista_precio\":\"Local 4\",\"precio_anterior\":\"6.00\",\"precio_nuevo\":6,\"timestamp\":\"2025-08-07T22:12:25.630432Z\"},{\"fila\":18,\"referencia\":\"controla_permitir_sin_variantes\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"12.00\",\"precio_nuevo\":12,\"timestamp\":\"2025-08-07T22:12:25.643026Z\"},{\"fila\":18,\"referencia\":\"controla_permitir_sin_variantes\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"123.00\",\"precio_nuevo\":123,\"timestamp\":\"2025-08-07T22:12:25.652468Z\"},{\"fila\":18,\"referencia\":\"controla_permitir_sin_variantes\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"1231.00\",\"precio_nuevo\":1231,\"timestamp\":\"2025-08-07T22:12:25.662962Z\"},{\"fila\":18,\"referencia\":\"controla_permitir_sin_variantes\",\"lista_precio\":\"Local 2\",\"precio_anterior\":\"124.00\",\"precio_nuevo\":124,\"timestamp\":\"2025-08-07T22:12:25.673946Z\"},{\"fila\":18,\"referencia\":\"controla_permitir_sin_variantes\",\"lista_precio\":\"Local 3\",\"precio_anterior\":\"412.00\",\"precio_nuevo\":412,\"timestamp\":\"2025-08-07T22:12:25.683774Z\"},{\"fila\":18,\"referencia\":\"controla_permitir_sin_variantes\",\"lista_precio\":\"Local 4\",\"precio_anterior\":\"5323.00\",\"precio_nuevo\":5323,\"timestamp\":\"2025-08-07T22:12:25.693522Z\"},{\"fila\":19,\"referencia\":\"controla_permitir_con_variantes\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"1.00\",\"precio_nuevo\":1,\"timestamp\":\"2025-08-07T22:12:25.707019Z\"},{\"fila\":19,\"referencia\":\"controla_permitir_con_variantes\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"2.00\",\"precio_nuevo\":2,\"timestamp\":\"2025-08-07T22:12:25.717681Z\"},{\"fila\":19,\"referencia\":\"controla_permitir_con_variantes\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"11.00\",\"precio_nuevo\":11,\"timestamp\":\"2025-08-07T22:12:25.727802Z\"},{\"fila\":19,\"referencia\":\"controla_permitir_con_variantes\",\"lista_precio\":\"Local 2\",\"precio_anterior\":\"22.00\",\"precio_nuevo\":22,\"timestamp\":\"2025-08-07T22:12:25.738123Z\"},{\"fila\":19,\"referencia\":\"controla_permitir_con_variantes\",\"lista_precio\":\"Local 3\",\"precio_anterior\":\"33.00\",\"precio_nuevo\":33,\"timestamp\":\"2025-08-07T22:12:25.748122Z\"},{\"fila\":19,\"referencia\":\"controla_permitir_con_variantes\",\"lista_precio\":\"Local 4\",\"precio_anterior\":\"44.00\",\"precio_nuevo\":44,\"timestamp\":\"2025-08-07T22:12:25.758091Z\"}]','2025-08-07 22:12:24','2025-08-07 22:12:25'),(3,1,'completado','orao.xlsx','actualizaciones_precios/1754605772_orao.xlsx',18,14,4,'[{\"fila\":3,\"referencia\":\"lap-arojo\",\"mensaje\":\"No se encontraron precios v\\u00e1lidos para actualizar\",\"timestamp\":\"2025-08-07T22:29:33.003158Z\"},{\"fila\":5,\"referencia\":\"lap-azul2\",\"mensaje\":\"No se encontraron precios v\\u00e1lidos para actualizar\",\"timestamp\":\"2025-08-07T22:29:33.011002Z\"},{\"fila\":6,\"referencia\":\"lap-azul9\",\"mensaje\":\"No se encontraron precios v\\u00e1lidos para actualizar\",\"timestamp\":\"2025-08-07T22:29:33.012933Z\"},{\"fila\":7,\"referencia\":\"lap-azul3423\",\"mensaje\":\"No se encontraron precios v\\u00e1lidos para actualizar\",\"timestamp\":\"2025-08-07T22:29:33.014640Z\"}]','[{\"fila\":2,\"referencia\":\"lap-azul\",\"lista_precio\":\"Export 1\",\"precio_anterior\":null,\"precio_nuevo\":1,\"timestamp\":\"2025-08-07T22:29:32.975789Z\"},{\"fila\":2,\"referencia\":\"lap-azul\",\"lista_precio\":\"Export 2\",\"precio_anterior\":null,\"precio_nuevo\":2,\"timestamp\":\"2025-08-07T22:29:32.981017Z\"},{\"fila\":2,\"referencia\":\"lap-azul\",\"lista_precio\":\"Local 1\",\"precio_anterior\":null,\"precio_nuevo\":3,\"timestamp\":\"2025-08-07T22:29:32.985787Z\"},{\"fila\":2,\"referencia\":\"lap-azul\",\"lista_precio\":\"Local 2\",\"precio_anterior\":null,\"precio_nuevo\":4,\"timestamp\":\"2025-08-07T22:29:32.991111Z\"},{\"fila\":2,\"referencia\":\"lap-azul\",\"lista_precio\":\"Local 3\",\"precio_anterior\":null,\"precio_nuevo\":5,\"timestamp\":\"2025-08-07T22:29:32.996054Z\"},{\"fila\":2,\"referencia\":\"lap-azul\",\"lista_precio\":\"Local 4\",\"precio_anterior\":null,\"precio_nuevo\":6,\"timestamp\":\"2025-08-07T22:29:33.001455Z\"},{\"fila\":4,\"referencia\":\"lapmorado\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"100000.00\",\"precio_nuevo\":100000,\"timestamp\":\"2025-08-07T22:29:33.009277Z\"},{\"fila\":8,\"referencia\":\"Rojo\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"1000.00\",\"precio_nuevo\":1000,\"timestamp\":\"2025-08-07T22:29:33.020208Z\"},{\"fila\":8,\"referencia\":\"Rojo\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"2000.00\",\"precio_nuevo\":2000,\"timestamp\":\"2025-08-07T22:29:33.024594Z\"},{\"fila\":8,\"referencia\":\"Rojo\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"3000.00\",\"precio_nuevo\":3000,\"timestamp\":\"2025-08-07T22:29:33.029948Z\"},{\"fila\":9,\"referencia\":\"lap-azul3\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"99999.00\",\"precio_nuevo\":99999,\"timestamp\":\"2025-08-07T22:29:33.037356Z\"},{\"fila\":9,\"referencia\":\"lap-azul3\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"88888.00\",\"precio_nuevo\":88888,\"timestamp\":\"2025-08-07T22:29:33.042750Z\"},{\"fila\":9,\"referencia\":\"lap-azul3\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"777777.00\",\"precio_nuevo\":777777,\"timestamp\":\"2025-08-07T22:29:33.048806Z\"},{\"fila\":10,\"referencia\":\"Negro\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"111111.00\",\"precio_nuevo\":111111,\"timestamp\":\"2025-08-07T22:29:33.060584Z\"},{\"fila\":10,\"referencia\":\"Negro\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"1111111.00\",\"precio_nuevo\":1111111,\"timestamp\":\"2025-08-07T22:29:33.067488Z\"},{\"fila\":10,\"referencia\":\"Negro\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"111111.00\",\"precio_nuevo\":111111,\"timestamp\":\"2025-08-07T22:29:33.073445Z\"},{\"fila\":10,\"referencia\":\"Negro\",\"lista_precio\":\"Local 2\",\"precio_anterior\":\"111111.00\",\"precio_nuevo\":111111,\"timestamp\":\"2025-08-07T22:29:33.079314Z\"},{\"fila\":10,\"referencia\":\"Negro\",\"lista_precio\":\"Local 3\",\"precio_anterior\":\"111111.00\",\"precio_nuevo\":111111,\"timestamp\":\"2025-08-07T22:29:33.085249Z\"},{\"fila\":10,\"referencia\":\"Negro\",\"lista_precio\":\"Local 4\",\"precio_anterior\":\"111111.00\",\"precio_nuevo\":111111,\"timestamp\":\"2025-08-07T22:29:33.090839Z\"},{\"fila\":11,\"referencia\":\"Prueba sin variante con valor\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"100.00\",\"precio_nuevo\":100,\"timestamp\":\"2025-08-07T22:29:33.099113Z\"},{\"fila\":11,\"referencia\":\"Prueba sin variante con valor\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"1002.00\",\"precio_nuevo\":1002,\"timestamp\":\"2025-08-07T22:29:33.105883Z\"},{\"fila\":11,\"referencia\":\"Prueba sin variante con valor\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"1003.00\",\"precio_nuevo\":1003,\"timestamp\":\"2025-08-07T22:29:33.112491Z\"},{\"fila\":11,\"referencia\":\"Prueba sin variante con valor\",\"lista_precio\":\"Local 2\",\"precio_anterior\":\"1004.00\",\"precio_nuevo\":1004,\"timestamp\":\"2025-08-07T22:29:33.119244Z\"},{\"fila\":11,\"referencia\":\"Prueba sin variante con valor\",\"lista_precio\":\"Local 3\",\"precio_anterior\":\"1005.00\",\"precio_nuevo\":1005,\"timestamp\":\"2025-08-07T22:29:33.125872Z\"},{\"fila\":11,\"referencia\":\"Prueba sin variante con valor\",\"lista_precio\":\"Local 4\",\"precio_anterior\":\"1006.00\",\"precio_nuevo\":1006,\"timestamp\":\"2025-08-07T22:29:33.132835Z\"},{\"fila\":12,\"referencia\":\"Prueba con variante con valor\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"1.00\",\"precio_nuevo\":1,\"timestamp\":\"2025-08-07T22:29:33.140257Z\"},{\"fila\":12,\"referencia\":\"Prueba con variante con valor\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"2.00\",\"precio_nuevo\":2,\"timestamp\":\"2025-08-07T22:29:33.146453Z\"},{\"fila\":12,\"referencia\":\"Prueba con variante con valor\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"3.00\",\"precio_nuevo\":3,\"timestamp\":\"2025-08-07T22:29:33.152494Z\"},{\"fila\":12,\"referencia\":\"Prueba con variante con valor\",\"lista_precio\":\"Local 2\",\"precio_anterior\":\"4.00\",\"precio_nuevo\":4,\"timestamp\":\"2025-08-07T22:29:33.158285Z\"},{\"fila\":12,\"referencia\":\"Prueba con variante con valor\",\"lista_precio\":\"Local 3\",\"precio_anterior\":\"5.00\",\"precio_nuevo\":5,\"timestamp\":\"2025-08-07T22:29:33.164224Z\"},{\"fila\":12,\"referencia\":\"Prueba con variante con valor\",\"lista_precio\":\"Local 4\",\"precio_anterior\":\"6.00\",\"precio_nuevo\":6,\"timestamp\":\"2025-08-07T22:29:33.170675Z\"},{\"fila\":13,\"referencia\":\"producto con dos imagenes\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"1.00\",\"precio_nuevo\":1,\"timestamp\":\"2025-08-07T22:29:33.177920Z\"},{\"fila\":13,\"referencia\":\"producto con dos imagenes\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"2.00\",\"precio_nuevo\":2,\"timestamp\":\"2025-08-07T22:29:33.183943Z\"},{\"fila\":13,\"referencia\":\"producto con dos imagenes\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"3.00\",\"precio_nuevo\":3,\"timestamp\":\"2025-08-07T22:29:33.189523Z\"},{\"fila\":13,\"referencia\":\"producto con dos imagenes\",\"lista_precio\":\"Local 2\",\"precio_anterior\":\"5.00\",\"precio_nuevo\":5,\"timestamp\":\"2025-08-07T22:29:33.195565Z\"},{\"fila\":13,\"referencia\":\"producto con dos imagenes\",\"lista_precio\":\"Local 3\",\"precio_anterior\":\"7.00\",\"precio_nuevo\":7,\"timestamp\":\"2025-08-07T22:29:33.203659Z\"},{\"fila\":13,\"referencia\":\"producto con dos imagenes\",\"lista_precio\":\"Local 4\",\"precio_anterior\":\"8.00\",\"precio_nuevo\":8,\"timestamp\":\"2025-08-07T22:29:33.210439Z\"},{\"fila\":14,\"referencia\":\"aaaaa\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"1.00\",\"precio_nuevo\":1,\"timestamp\":\"2025-08-07T22:29:33.218488Z\"},{\"fila\":14,\"referencia\":\"aaaaa\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"2.00\",\"precio_nuevo\":2,\"timestamp\":\"2025-08-07T22:29:33.224270Z\"},{\"fila\":14,\"referencia\":\"aaaaa\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"3.00\",\"precio_nuevo\":3,\"timestamp\":\"2025-08-07T22:29:33.230451Z\"},{\"fila\":14,\"referencia\":\"aaaaa\",\"lista_precio\":\"Local 2\",\"precio_anterior\":\"4.00\",\"precio_nuevo\":4,\"timestamp\":\"2025-08-07T22:29:33.237348Z\"},{\"fila\":14,\"referencia\":\"aaaaa\",\"lista_precio\":\"Local 3\",\"precio_anterior\":\"5.00\",\"precio_nuevo\":5,\"timestamp\":\"2025-08-07T22:29:33.243308Z\"},{\"fila\":14,\"referencia\":\"aaaaa\",\"lista_precio\":\"Local 4\",\"precio_anterior\":\"6.00\",\"precio_nuevo\":6,\"timestamp\":\"2025-08-07T22:29:33.249542Z\"},{\"fila\":15,\"referencia\":\"xxxxxxxxxxxxxxxxx\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"1.00\",\"precio_nuevo\":1,\"timestamp\":\"2025-08-07T22:29:33.257060Z\"},{\"fila\":15,\"referencia\":\"xxxxxxxxxxxxxxxxx\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"2.00\",\"precio_nuevo\":2,\"timestamp\":\"2025-08-07T22:29:33.265037Z\"},{\"fila\":15,\"referencia\":\"xxxxxxxxxxxxxxxxx\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"3.00\",\"precio_nuevo\":3,\"timestamp\":\"2025-08-07T22:29:33.271908Z\"},{\"fila\":15,\"referencia\":\"xxxxxxxxxxxxxxxxx\",\"lista_precio\":\"Local 2\",\"precio_anterior\":\"4.00\",\"precio_nuevo\":4,\"timestamp\":\"2025-08-07T22:29:33.277899Z\"},{\"fila\":15,\"referencia\":\"xxxxxxxxxxxxxxxxx\",\"lista_precio\":\"Local 3\",\"precio_anterior\":\"6.00\",\"precio_nuevo\":6,\"timestamp\":\"2025-08-07T22:29:33.284135Z\"},{\"fila\":15,\"referencia\":\"xxxxxxxxxxxxxxxxx\",\"lista_precio\":\"Local 4\",\"precio_anterior\":\"5.00\",\"precio_nuevo\":5,\"timestamp\":\"2025-08-07T22:29:33.290095Z\"},{\"fila\":16,\"referencia\":\"pppppppppp\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"1.00\",\"precio_nuevo\":1,\"timestamp\":\"2025-08-07T22:29:33.298033Z\"},{\"fila\":16,\"referencia\":\"pppppppppp\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"2.00\",\"precio_nuevo\":2,\"timestamp\":\"2025-08-07T22:29:33.304480Z\"},{\"fila\":16,\"referencia\":\"pppppppppp\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"3.00\",\"precio_nuevo\":3,\"timestamp\":\"2025-08-07T22:29:33.310701Z\"},{\"fila\":16,\"referencia\":\"pppppppppp\",\"lista_precio\":\"Local 2\",\"precio_anterior\":\"4.00\",\"precio_nuevo\":4,\"timestamp\":\"2025-08-07T22:29:33.316963Z\"},{\"fila\":16,\"referencia\":\"pppppppppp\",\"lista_precio\":\"Local 3\",\"precio_anterior\":\"5.00\",\"precio_nuevo\":5,\"timestamp\":\"2025-08-07T22:29:33.323162Z\"},{\"fila\":16,\"referencia\":\"pppppppppp\",\"lista_precio\":\"Local 4\",\"precio_anterior\":\"6.00\",\"precio_nuevo\":6,\"timestamp\":\"2025-08-07T22:29:33.329370Z\"},{\"fila\":17,\"referencia\":\"lap-azul944587\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"1.00\",\"precio_nuevo\":1,\"timestamp\":\"2025-08-07T22:29:33.338483Z\"},{\"fila\":17,\"referencia\":\"lap-azul944587\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"2.00\",\"precio_nuevo\":2,\"timestamp\":\"2025-08-07T22:29:33.345323Z\"},{\"fila\":17,\"referencia\":\"lap-azul944587\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"3.00\",\"precio_nuevo\":3,\"timestamp\":\"2025-08-07T22:29:33.352195Z\"},{\"fila\":17,\"referencia\":\"lap-azul944587\",\"lista_precio\":\"Local 2\",\"precio_anterior\":\"4.00\",\"precio_nuevo\":4,\"timestamp\":\"2025-08-07T22:29:33.359272Z\"},{\"fila\":17,\"referencia\":\"lap-azul944587\",\"lista_precio\":\"Local 3\",\"precio_anterior\":\"5.00\",\"precio_nuevo\":5,\"timestamp\":\"2025-08-07T22:29:33.366716Z\"},{\"fila\":17,\"referencia\":\"lap-azul944587\",\"lista_precio\":\"Local 4\",\"precio_anterior\":\"6.00\",\"precio_nuevo\":6,\"timestamp\":\"2025-08-07T22:29:33.373473Z\"},{\"fila\":18,\"referencia\":\"controla_permitir_sin_variantes\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"12.00\",\"precio_nuevo\":12,\"timestamp\":\"2025-08-07T22:29:33.382214Z\"},{\"fila\":18,\"referencia\":\"controla_permitir_sin_variantes\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"123.00\",\"precio_nuevo\":123,\"timestamp\":\"2025-08-07T22:29:33.389413Z\"},{\"fila\":18,\"referencia\":\"controla_permitir_sin_variantes\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"1231.00\",\"precio_nuevo\":1231,\"timestamp\":\"2025-08-07T22:29:33.396981Z\"},{\"fila\":18,\"referencia\":\"controla_permitir_sin_variantes\",\"lista_precio\":\"Local 2\",\"precio_anterior\":\"124.00\",\"precio_nuevo\":124,\"timestamp\":\"2025-08-07T22:29:33.404943Z\"},{\"fila\":18,\"referencia\":\"controla_permitir_sin_variantes\",\"lista_precio\":\"Local 3\",\"precio_anterior\":\"412.00\",\"precio_nuevo\":412,\"timestamp\":\"2025-08-07T22:29:33.412024Z\"},{\"fila\":18,\"referencia\":\"controla_permitir_sin_variantes\",\"lista_precio\":\"Local 4\",\"precio_anterior\":\"5323.00\",\"precio_nuevo\":5323,\"timestamp\":\"2025-08-07T22:29:33.419128Z\"},{\"fila\":19,\"referencia\":\"controla_permitir_con_variantes\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"1.00\",\"precio_nuevo\":1,\"timestamp\":\"2025-08-07T22:29:33.427666Z\"},{\"fila\":19,\"referencia\":\"controla_permitir_con_variantes\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"2.00\",\"precio_nuevo\":2,\"timestamp\":\"2025-08-07T22:29:33.435589Z\"},{\"fila\":19,\"referencia\":\"controla_permitir_con_variantes\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"11.00\",\"precio_nuevo\":11,\"timestamp\":\"2025-08-07T22:29:33.442658Z\"},{\"fila\":19,\"referencia\":\"controla_permitir_con_variantes\",\"lista_precio\":\"Local 2\",\"precio_anterior\":\"22.00\",\"precio_nuevo\":22,\"timestamp\":\"2025-08-07T22:29:33.449747Z\"},{\"fila\":19,\"referencia\":\"controla_permitir_con_variantes\",\"lista_precio\":\"Local 3\",\"precio_anterior\":\"33.00\",\"precio_nuevo\":33,\"timestamp\":\"2025-08-07T22:29:33.456534Z\"},{\"fila\":19,\"referencia\":\"controla_permitir_con_variantes\",\"lista_precio\":\"Local 4\",\"precio_anterior\":\"44.00\",\"precio_nuevo\":44,\"timestamp\":\"2025-08-07T22:29:33.464389Z\"}]','2025-08-07 22:29:32','2025-08-07 22:29:33'),(4,1,'completado','orao.xlsx','uploads/actualizaciones_precios/1754606550_orao.xlsx',18,14,4,'[{\"fila\":3,\"referencia\":\"lap-arojo\",\"mensaje\":\"No se encontraron precios v\\u00e1lidos para actualizar\",\"timestamp\":\"2025-08-07T22:42:31.057647Z\"},{\"fila\":5,\"referencia\":\"lap-azul2\",\"mensaje\":\"No se encontraron precios v\\u00e1lidos para actualizar\",\"timestamp\":\"2025-08-07T22:42:31.065665Z\"},{\"fila\":6,\"referencia\":\"lap-azul9\",\"mensaje\":\"No se encontraron precios v\\u00e1lidos para actualizar\",\"timestamp\":\"2025-08-07T22:42:31.067356Z\"},{\"fila\":7,\"referencia\":\"lap-azul3423\",\"mensaje\":\"No se encontraron precios v\\u00e1lidos para actualizar\",\"timestamp\":\"2025-08-07T22:42:31.068877Z\"}]','[{\"fila\":2,\"referencia\":\"lap-azul\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"1.00\",\"precio_nuevo\":1,\"timestamp\":\"2025-08-07T22:42:31.031552Z\"},{\"fila\":2,\"referencia\":\"lap-azul\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"2.00\",\"precio_nuevo\":2,\"timestamp\":\"2025-08-07T22:42:31.036522Z\"},{\"fila\":2,\"referencia\":\"lap-azul\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"3.00\",\"precio_nuevo\":3,\"timestamp\":\"2025-08-07T22:42:31.041357Z\"},{\"fila\":2,\"referencia\":\"lap-azul\",\"lista_precio\":\"Local 2\",\"precio_anterior\":\"4.00\",\"precio_nuevo\":4,\"timestamp\":\"2025-08-07T22:42:31.046852Z\"},{\"fila\":2,\"referencia\":\"lap-azul\",\"lista_precio\":\"Local 3\",\"precio_anterior\":\"5.00\",\"precio_nuevo\":5,\"timestamp\":\"2025-08-07T22:42:31.051316Z\"},{\"fila\":2,\"referencia\":\"lap-azul\",\"lista_precio\":\"Local 4\",\"precio_anterior\":\"6.00\",\"precio_nuevo\":6,\"timestamp\":\"2025-08-07T22:42:31.055855Z\"},{\"fila\":4,\"referencia\":\"lapmorado\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"100000.00\",\"precio_nuevo\":100000,\"timestamp\":\"2025-08-07T22:42:31.063635Z\"},{\"fila\":8,\"referencia\":\"Rojo\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"1000.00\",\"precio_nuevo\":1000,\"timestamp\":\"2025-08-07T22:42:31.074464Z\"},{\"fila\":8,\"referencia\":\"Rojo\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"2000.00\",\"precio_nuevo\":2000,\"timestamp\":\"2025-08-07T22:42:31.080211Z\"},{\"fila\":8,\"referencia\":\"Rojo\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"3000.00\",\"precio_nuevo\":3000,\"timestamp\":\"2025-08-07T22:42:31.085406Z\"},{\"fila\":9,\"referencia\":\"lap-azul3\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"99999.00\",\"precio_nuevo\":99999,\"timestamp\":\"2025-08-07T22:42:31.092109Z\"},{\"fila\":9,\"referencia\":\"lap-azul3\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"88888.00\",\"precio_nuevo\":88888,\"timestamp\":\"2025-08-07T22:42:31.097703Z\"},{\"fila\":9,\"referencia\":\"lap-azul3\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"777777.00\",\"precio_nuevo\":777777,\"timestamp\":\"2025-08-07T22:42:31.103861Z\"},{\"fila\":9,\"referencia\":\"lap-azul3\",\"lista_precio\":\"Local 2\",\"precio_anterior\":null,\"precio_nuevo\":2222222,\"timestamp\":\"2025-08-07T22:42:31.111099Z\"},{\"fila\":10,\"referencia\":\"Negro\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"111111.00\",\"precio_nuevo\":111111,\"timestamp\":\"2025-08-07T22:42:31.117464Z\"},{\"fila\":10,\"referencia\":\"Negro\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"1111111.00\",\"precio_nuevo\":1111111,\"timestamp\":\"2025-08-07T22:42:31.122599Z\"},{\"fila\":10,\"referencia\":\"Negro\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"111111.00\",\"precio_nuevo\":111111,\"timestamp\":\"2025-08-07T22:42:31.127159Z\"},{\"fila\":10,\"referencia\":\"Negro\",\"lista_precio\":\"Local 2\",\"precio_anterior\":\"111111.00\",\"precio_nuevo\":111111,\"timestamp\":\"2025-08-07T22:42:31.132786Z\"},{\"fila\":10,\"referencia\":\"Negro\",\"lista_precio\":\"Local 3\",\"precio_anterior\":\"111111.00\",\"precio_nuevo\":111111,\"timestamp\":\"2025-08-07T22:42:31.138812Z\"},{\"fila\":10,\"referencia\":\"Negro\",\"lista_precio\":\"Local 4\",\"precio_anterior\":\"111111.00\",\"precio_nuevo\":111111,\"timestamp\":\"2025-08-07T22:42:31.145086Z\"},{\"fila\":11,\"referencia\":\"Prueba sin variante con valor\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"100.00\",\"precio_nuevo\":100,\"timestamp\":\"2025-08-07T22:42:31.150409Z\"},{\"fila\":11,\"referencia\":\"Prueba sin variante con valor\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"1002.00\",\"precio_nuevo\":1002,\"timestamp\":\"2025-08-07T22:42:31.154972Z\"},{\"fila\":11,\"referencia\":\"Prueba sin variante con valor\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"1003.00\",\"precio_nuevo\":1003,\"timestamp\":\"2025-08-07T22:42:31.159364Z\"},{\"fila\":11,\"referencia\":\"Prueba sin variante con valor\",\"lista_precio\":\"Local 2\",\"precio_anterior\":\"1004.00\",\"precio_nuevo\":1004,\"timestamp\":\"2025-08-07T22:42:31.163839Z\"},{\"fila\":11,\"referencia\":\"Prueba sin variante con valor\",\"lista_precio\":\"Local 3\",\"precio_anterior\":\"1005.00\",\"precio_nuevo\":1005,\"timestamp\":\"2025-08-07T22:42:31.168306Z\"},{\"fila\":11,\"referencia\":\"Prueba sin variante con valor\",\"lista_precio\":\"Local 4\",\"precio_anterior\":\"1006.00\",\"precio_nuevo\":1006,\"timestamp\":\"2025-08-07T22:42:31.172643Z\"},{\"fila\":12,\"referencia\":\"Prueba con variante con valor\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"1.00\",\"precio_nuevo\":1,\"timestamp\":\"2025-08-07T22:42:31.179529Z\"},{\"fila\":12,\"referencia\":\"Prueba con variante con valor\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"2.00\",\"precio_nuevo\":2,\"timestamp\":\"2025-08-07T22:42:31.184059Z\"},{\"fila\":12,\"referencia\":\"Prueba con variante con valor\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"3.00\",\"precio_nuevo\":3,\"timestamp\":\"2025-08-07T22:42:31.188464Z\"},{\"fila\":12,\"referencia\":\"Prueba con variante con valor\",\"lista_precio\":\"Local 2\",\"precio_anterior\":\"4.00\",\"precio_nuevo\":4,\"timestamp\":\"2025-08-07T22:42:31.193950Z\"},{\"fila\":12,\"referencia\":\"Prueba con variante con valor\",\"lista_precio\":\"Local 3\",\"precio_anterior\":\"5.00\",\"precio_nuevo\":5,\"timestamp\":\"2025-08-07T22:42:31.199315Z\"},{\"fila\":12,\"referencia\":\"Prueba con variante con valor\",\"lista_precio\":\"Local 4\",\"precio_anterior\":\"6.00\",\"precio_nuevo\":6,\"timestamp\":\"2025-08-07T22:42:31.203753Z\"},{\"fila\":13,\"referencia\":\"producto con dos imagenes\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"1.00\",\"precio_nuevo\":1,\"timestamp\":\"2025-08-07T22:42:31.210251Z\"},{\"fila\":13,\"referencia\":\"producto con dos imagenes\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"2.00\",\"precio_nuevo\":2,\"timestamp\":\"2025-08-07T22:42:31.215411Z\"},{\"fila\":13,\"referencia\":\"producto con dos imagenes\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"3.00\",\"precio_nuevo\":3,\"timestamp\":\"2025-08-07T22:42:31.219907Z\"},{\"fila\":13,\"referencia\":\"producto con dos imagenes\",\"lista_precio\":\"Local 2\",\"precio_anterior\":\"5.00\",\"precio_nuevo\":5,\"timestamp\":\"2025-08-07T22:42:31.224668Z\"},{\"fila\":13,\"referencia\":\"producto con dos imagenes\",\"lista_precio\":\"Local 3\",\"precio_anterior\":\"7.00\",\"precio_nuevo\":7,\"timestamp\":\"2025-08-07T22:42:31.229155Z\"},{\"fila\":13,\"referencia\":\"producto con dos imagenes\",\"lista_precio\":\"Local 4\",\"precio_anterior\":\"8.00\",\"precio_nuevo\":8,\"timestamp\":\"2025-08-07T22:42:31.233681Z\"},{\"fila\":14,\"referencia\":\"aaaaa\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"1.00\",\"precio_nuevo\":1,\"timestamp\":\"2025-08-07T22:42:31.239380Z\"},{\"fila\":14,\"referencia\":\"aaaaa\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"2.00\",\"precio_nuevo\":2,\"timestamp\":\"2025-08-07T22:42:31.245910Z\"},{\"fila\":14,\"referencia\":\"aaaaa\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"3.00\",\"precio_nuevo\":3,\"timestamp\":\"2025-08-07T22:42:31.250788Z\"},{\"fila\":14,\"referencia\":\"aaaaa\",\"lista_precio\":\"Local 2\",\"precio_anterior\":\"4.00\",\"precio_nuevo\":4,\"timestamp\":\"2025-08-07T22:42:31.255734Z\"},{\"fila\":14,\"referencia\":\"aaaaa\",\"lista_precio\":\"Local 3\",\"precio_anterior\":\"5.00\",\"precio_nuevo\":5,\"timestamp\":\"2025-08-07T22:42:31.261584Z\"},{\"fila\":14,\"referencia\":\"aaaaa\",\"lista_precio\":\"Local 4\",\"precio_anterior\":\"6.00\",\"precio_nuevo\":6,\"timestamp\":\"2025-08-07T22:42:31.266354Z\"},{\"fila\":15,\"referencia\":\"xxxxxxxxxxxxxxxxx\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"1.00\",\"precio_nuevo\":1,\"timestamp\":\"2025-08-07T22:42:31.272121Z\"},{\"fila\":15,\"referencia\":\"xxxxxxxxxxxxxxxxx\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"2.00\",\"precio_nuevo\":2,\"timestamp\":\"2025-08-07T22:42:31.278062Z\"},{\"fila\":15,\"referencia\":\"xxxxxxxxxxxxxxxxx\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"3.00\",\"precio_nuevo\":3,\"timestamp\":\"2025-08-07T22:42:31.283225Z\"},{\"fila\":15,\"referencia\":\"xxxxxxxxxxxxxxxxx\",\"lista_precio\":\"Local 2\",\"precio_anterior\":\"4.00\",\"precio_nuevo\":4,\"timestamp\":\"2025-08-07T22:42:31.287997Z\"},{\"fila\":15,\"referencia\":\"xxxxxxxxxxxxxxxxx\",\"lista_precio\":\"Local 3\",\"precio_anterior\":\"6.00\",\"precio_nuevo\":6,\"timestamp\":\"2025-08-07T22:42:31.292679Z\"},{\"fila\":15,\"referencia\":\"xxxxxxxxxxxxxxxxx\",\"lista_precio\":\"Local 4\",\"precio_anterior\":\"5.00\",\"precio_nuevo\":5,\"timestamp\":\"2025-08-07T22:42:31.297478Z\"},{\"fila\":16,\"referencia\":\"pppppppppp\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"1.00\",\"precio_nuevo\":1,\"timestamp\":\"2025-08-07T22:42:31.303220Z\"},{\"fila\":16,\"referencia\":\"pppppppppp\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"2.00\",\"precio_nuevo\":2,\"timestamp\":\"2025-08-07T22:42:31.308443Z\"},{\"fila\":16,\"referencia\":\"pppppppppp\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"3.00\",\"precio_nuevo\":3,\"timestamp\":\"2025-08-07T22:42:31.314586Z\"},{\"fila\":16,\"referencia\":\"pppppppppp\",\"lista_precio\":\"Local 2\",\"precio_anterior\":\"4.00\",\"precio_nuevo\":4,\"timestamp\":\"2025-08-07T22:42:31.319397Z\"},{\"fila\":16,\"referencia\":\"pppppppppp\",\"lista_precio\":\"Local 3\",\"precio_anterior\":\"5.00\",\"precio_nuevo\":5,\"timestamp\":\"2025-08-07T22:42:31.324753Z\"},{\"fila\":16,\"referencia\":\"pppppppppp\",\"lista_precio\":\"Local 4\",\"precio_anterior\":\"6.00\",\"precio_nuevo\":6,\"timestamp\":\"2025-08-07T22:42:31.329966Z\"},{\"fila\":17,\"referencia\":\"lap-azul944587\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"1.00\",\"precio_nuevo\":1,\"timestamp\":\"2025-08-07T22:42:31.336562Z\"},{\"fila\":17,\"referencia\":\"lap-azul944587\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"2.00\",\"precio_nuevo\":2,\"timestamp\":\"2025-08-07T22:42:31.341673Z\"},{\"fila\":17,\"referencia\":\"lap-azul944587\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"3.00\",\"precio_nuevo\":3,\"timestamp\":\"2025-08-07T22:42:31.347771Z\"},{\"fila\":17,\"referencia\":\"lap-azul944587\",\"lista_precio\":\"Local 2\",\"precio_anterior\":\"4.00\",\"precio_nuevo\":4,\"timestamp\":\"2025-08-07T22:42:31.352921Z\"},{\"fila\":17,\"referencia\":\"lap-azul944587\",\"lista_precio\":\"Local 3\",\"precio_anterior\":\"5.00\",\"precio_nuevo\":5,\"timestamp\":\"2025-08-07T22:42:31.357754Z\"},{\"fila\":17,\"referencia\":\"lap-azul944587\",\"lista_precio\":\"Local 4\",\"precio_anterior\":\"6.00\",\"precio_nuevo\":6,\"timestamp\":\"2025-08-07T22:42:31.363858Z\"},{\"fila\":18,\"referencia\":\"controla_permitir_sin_variantes\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"12.00\",\"precio_nuevo\":12,\"timestamp\":\"2025-08-07T22:42:31.370641Z\"},{\"fila\":18,\"referencia\":\"controla_permitir_sin_variantes\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"123.00\",\"precio_nuevo\":123,\"timestamp\":\"2025-08-07T22:42:31.376394Z\"},{\"fila\":18,\"referencia\":\"controla_permitir_sin_variantes\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"1231.00\",\"precio_nuevo\":1231,\"timestamp\":\"2025-08-07T22:42:31.381999Z\"},{\"fila\":18,\"referencia\":\"controla_permitir_sin_variantes\",\"lista_precio\":\"Local 2\",\"precio_anterior\":\"124.00\",\"precio_nuevo\":124,\"timestamp\":\"2025-08-07T22:42:31.388474Z\"},{\"fila\":18,\"referencia\":\"controla_permitir_sin_variantes\",\"lista_precio\":\"Local 3\",\"precio_anterior\":\"412.00\",\"precio_nuevo\":412,\"timestamp\":\"2025-08-07T22:42:31.395200Z\"},{\"fila\":18,\"referencia\":\"controla_permitir_sin_variantes\",\"lista_precio\":\"Local 4\",\"precio_anterior\":\"5323.00\",\"precio_nuevo\":5323,\"timestamp\":\"2025-08-07T22:42:31.400492Z\"},{\"fila\":19,\"referencia\":\"controla_permitir_con_variantes\",\"lista_precio\":\"Export 1\",\"precio_anterior\":\"1.00\",\"precio_nuevo\":1,\"timestamp\":\"2025-08-07T22:42:31.407526Z\"},{\"fila\":19,\"referencia\":\"controla_permitir_con_variantes\",\"lista_precio\":\"Export 2\",\"precio_anterior\":\"2.00\",\"precio_nuevo\":2,\"timestamp\":\"2025-08-07T22:42:31.414057Z\"},{\"fila\":19,\"referencia\":\"controla_permitir_con_variantes\",\"lista_precio\":\"Local 1\",\"precio_anterior\":\"11.00\",\"precio_nuevo\":11,\"timestamp\":\"2025-08-07T22:42:31.419724Z\"},{\"fila\":19,\"referencia\":\"controla_permitir_con_variantes\",\"lista_precio\":\"Local 2\",\"precio_anterior\":\"22.00\",\"precio_nuevo\":22,\"timestamp\":\"2025-08-07T22:42:31.424922Z\"},{\"fila\":19,\"referencia\":\"controla_permitir_con_variantes\",\"lista_precio\":\"Local 3\",\"precio_anterior\":\"33.00\",\"precio_nuevo\":33,\"timestamp\":\"2025-08-07T22:42:31.430759Z\"},{\"fila\":19,\"referencia\":\"controla_permitir_con_variantes\",\"lista_precio\":\"Local 4\",\"precio_anterior\":\"44.00\",\"precio_nuevo\":44,\"timestamp\":\"2025-08-07T22:42:31.436546Z\"}]','2025-08-07 22:42:30','2025-08-07 22:42:31');
/*!40000 ALTER TABLE `actualizaciones_precios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_categories`
--

DROP TABLE IF EXISTS `blog_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blog_categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_categories`
--

LOCK TABLES `blog_categories` WRITE;
/*!40000 ALTER TABLE `blog_categories` DISABLE KEYS */;
INSERT INTO `blog_categories` VALUES (1,'Consejos Forestales','consejos-forestales','Consejos y recomendaciones para el cuidado de arboles y zonas forestales',1,'2026-03-17 04:00:12','2026-03-17 04:00:12'),(2,'Noticias','noticias','Novedades y noticias de Manzer Agroforestal',1,'2026-03-17 04:00:12','2026-03-17 04:00:12'),(3,'Prevencion','prevencion','Articulos sobre prevencion de incendios y seguridad forestal',1,'2026-03-17 04:00:12','2026-03-17 04:00:12'),(4,'Medio Ambiente','medio-ambiente','Articulos sobre sostenibilidad y medio ambiente',1,'2026-03-17 04:00:12','2026-03-17 04:00:12');
/*!40000 ALTER TABLE `blog_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_post_tag`
--

DROP TABLE IF EXISTS `blog_post_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog_post_tag` (
  `blog_post_id` bigint(20) unsigned NOT NULL,
  `blog_tag_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`blog_post_id`,`blog_tag_id`),
  KEY `blog_post_tag_blog_tag_id_foreign` (`blog_tag_id`),
  CONSTRAINT `blog_post_tag_blog_post_id_foreign` FOREIGN KEY (`blog_post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `blog_post_tag_blog_tag_id_foreign` FOREIGN KEY (`blog_tag_id`) REFERENCES `blog_tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_post_tag`
--

LOCK TABLES `blog_post_tag` WRITE;
/*!40000 ALTER TABLE `blog_post_tag` DISABLE KEYS */;
INSERT INTO `blog_post_tag` VALUES (1,1),(1,2),(1,5),(2,2),(2,3),(2,7),(2,8),(3,1),(3,2),(3,4),(4,6),(4,7),(5,1),(5,6);
/*!40000 ALTER TABLE `blog_post_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_posts`
--

DROP TABLE IF EXISTS `blog_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog_posts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `body` longtext NOT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `featured_image_alt` varchar(255) DEFAULT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `author_id` bigint(20) unsigned NOT NULL,
  `status` enum('draft','published','scheduled') NOT NULL DEFAULT 'draft',
  `published_at` timestamp NULL DEFAULT NULL,
  `page_id` bigint(20) unsigned DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `views_count` int(11) NOT NULL DEFAULT 0,
  `reading_time` int(11) DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blog_posts_slug_unique` (`slug`),
  KEY `blog_posts_category_id_foreign` (`category_id`),
  KEY `blog_posts_author_id_foreign` (`author_id`),
  KEY `blog_posts_page_id_foreign` (`page_id`),
  CONSTRAINT `blog_posts_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `blog_posts_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `blog_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `blog_posts_page_id_foreign` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_posts`
--

LOCK TABLES `blog_posts` WRITE;
/*!40000 ALTER TABLE `blog_posts` DISABLE KEYS */;
INSERT INTO `blog_posts` VALUES (1,'Cuando es necesario talar un arbol: senales que debes conocer','cuando-talar-arbol-senales','Aprende a identificar las senales que indican que un arbol necesita ser talado por seguridad. Te explicamos los criterios profesionales que utilizamos.','<p>Los arboles son elementos fundamentales de nuestro entorno, pero en ocasiones pueden representar un <strong>riesgo para la seguridad</strong> de personas e infraestructuras. Saber identificar cuando un arbol necesita ser talado es crucial.</p>\n\n<h2>Senales de alerta</h2>\n\n<h3>1. Inclinacion excesiva</h3>\n<p>Un arbol que se inclina mas de 15 grados respecto a la vertical puede indicar problemas en las raices o en el suelo. Si la inclinacion es reciente o progresiva, es una senal clara de peligro.</p>\n\n<h3>2. Ramas secas en la copa</h3>\n<p>Cuando mas del 50% de la copa presenta ramas secas, el arbol puede estar muriendo. Las ramas secas son fragiles y pueden caer sin aviso, especialmente durante tormentas o vientos fuertes.</p>\n\n<h3>3. Hongos en la base del tronco</h3>\n<p>La presencia de hongos (setas) en la base del tronco o en las raices superficiales suele indicar <strong>pudricion interna</strong>. Esto debilita la estructura del arbol y aumenta el riesgo de caida.</p>\n\n<h3>4. Cavidades y huecos</h3>\n<p>Los huecos en el tronco reducen la resistencia mecanica del arbol. Aunque algunos arboles pueden sobrevivir con cavidades, un profesional debe evaluar si la estructura es segura.</p>\n\n<h3>5. Danos en las raices</h3>\n<p>Obras cercanas, compactacion del suelo o cortes de raices pueden comprometer la estabilidad del arbol. Si se han realizado obras en un radio de 3-5 metros del tronco, es recomendable una evaluacion profesional.</p>\n\n<h2>¿Que hacer si detectas alguna de estas senales?</h2>\n<p>Lo mas importante es <strong>no intentar talar el arbol por tu cuenta</strong>. La tala de arboles es un trabajo peligroso que requiere equipamiento y formacion especifica. Contacta con profesionales como Manzer Agroforestal para una evaluacion gratuita.</p>\n\n<blockquote>\n<p>Un arbol evaluado a tiempo puede evitar accidentes graves. No esperes a que sea demasiado tarde.</p>\n</blockquote>\n\n<p>En Manzer Agroforestal realizamos evaluaciones de riesgo de arboles y, cuando es necesario, procedemos a la tala controlada con los maximos estandares de seguridad.</p>','images/gallery/tala-en-altura.jpg','Profesional realizando tala en altura',1,1,'published','2026-03-12 04:00:12',11,1,0,2,0,'2026-03-17 04:00:12','2026-03-17 04:00:12',NULL),(2,'Prevencion de incendios forestales: guia practica para propietarios','prevencion-incendios-forestales-guia','Descubre las medidas esenciales que todo propietario de terreno forestal debe tomar para prevenir incendios. Normativa, obligaciones y consejos practicos.','<p>La prevencion de incendios forestales no es solo responsabilidad de las administraciones publicas. Los <strong>propietarios de terrenos forestales y parcelas</strong> tienen obligaciones legales y una responsabilidad directa en la proteccion del monte.</p>\n\n<h2>Obligaciones legales en Cataluna</h2>\n<p>La legislacion catalana establece que los propietarios de fincas forestales y urbanas colindantes con zonas de bosque deben mantener una <strong>franja de proteccion</strong> alrededor de sus edificaciones:</p>\n<ul>\n<li>Franja de 25 metros alrededor de edificaciones en zona forestal</li>\n<li>Limpieza de vegetacion seca y matorral</li>\n<li>Poda de arboles hasta 1/3 de su altura</li>\n<li>Eliminacion de ramas que esten a menos de 3 metros de una edificacion</li>\n</ul>\n\n<h2>Medidas preventivas recomendadas</h2>\n\n<h3>Gestion del sotobosque</h3>\n<p>El sotobosque es el principal combustible en un incendio forestal. Mantenerlo limpio y controlado reduce drasticamente el riesgo de propagacion.</p>\n\n<h3>Creacion de cortafuegos</h3>\n<p>Las franjas cortafuegos son zonas desprovistas de vegetacion que actuan como barrera contra el avance del fuego. Su anchura y ubicacion deben ser estudiadas por profesionales.</p>\n\n<h3>Poda de arboles</h3>\n<p>La poda de las ramas bajas evita la <strong>continuidad vertical</strong> del fuego, impidiendo que las llamas trepen desde el suelo hasta las copas de los arboles.</p>\n\n<h2>¿Como podemos ayudarte?</h2>\n<p>En Manzer Agroforestal realizamos todos los trabajos de prevencion de incendios: desbroces, cortafuegos, podas y gestion de restos vegetales. Trabajamos con ayuntamientos y particulares en toda la provincia de Lleida.</p>','images/gallery/bosque-panoramica.jpg','Bosque gestionado para prevencion de incendios',3,1,'published','2026-03-05 04:00:12',12,0,1,2,0,'2026-03-17 04:00:12','2026-03-17 13:35:53',NULL),(3,'La poda en altura: tecnicas y seguridad en el trabajo arboreo','poda-altura-tecnicas-seguridad','Conoce las tecnicas profesionales de poda en altura y por que es fundamental contar con profesionales cualificados para este tipo de trabajos.','<p>La poda en altura es una de las disciplinas mas exigentes dentro de la arboricultura. Requiere <strong>formacion especializada</strong>, equipamiento de seguridad certificado y una profunda comprension de la biologia de los arboles.</p>\n\n<h2>Tecnicas de trepa</h2>\n<p>Existen diferentes tecnicas de acceso a la copa de los arboles:</p>\n\n<h3>Trepa con cuerda (SRT y DRT)</h3>\n<p>El sistema de trepa con cuerda es el metodo mas versatil y menos agresivo con el arbol. Permite acceder a cualquier punto de la copa sin necesidad de maquinaria pesada.</p>\n<ul>\n<li><strong>SRT (Single Rope Technique):</strong> Ascenso por cuerda simple, ideal para arboles altos y rectos</li>\n<li><strong>DRT (Double Rope Technique):</strong> Cuerda doble, ofrece mayor seguridad y versatilidad de movimiento</li>\n</ul>\n\n<h3>Cesta elevadora</h3>\n<p>Para arboles en zonas accesibles, la cesta elevadora permite un posicionamiento rapido y comodo. Sin embargo, no siempre es posible utilizarla por limitaciones de acceso.</p>\n\n<h2>Equipamiento de seguridad</h2>\n<p>Todo arborista profesional debe utilizar:</p>\n<ul>\n<li>Arnes de trepa certificado EN 813</li>\n<li>Casco con proteccion auditiva y facial</li>\n<li>Mosquetones y conectores certificados</li>\n<li>Cuerdas semistaticas de arboricultura</li>\n<li>Motosierra con dispositivo anticorte</li>\n</ul>\n\n<p>En Manzer Agroforestal todos nuestros arboristas estan formados y certificados para trabajos en altura. La seguridad es nuestra prioridad absoluta.</p>','images/gallery/poda-en-altura.jpg','Arborista realizando poda en altura con sistema de trepa',1,1,'published','2026-02-25 04:00:12',13,0,1,1,0,'2026-03-17 04:00:12','2026-03-17 04:01:33',NULL),(4,'Manzer Agroforestal amplia sus servicios en la comarca del Segria','manzer-amplia-servicios-segria','Nos complace anunciar que ampliamos nuestra zona de actuacion y servicios para dar cobertura a mas municipios de la comarca del Segria.','<p>En Manzer Agroforestal estamos en constante crecimiento. Nos complace anunciar que <strong>ampliamos nuestros servicios</strong> para dar cobertura a mas municipios de la comarca del Segria y comarcas limotrofes.</p>\n\n<h2>Nuevos servicios disponibles</h2>\n<p>Ademas de nuestros servicios habituales de tala, poda y desbroces, ahora ofrecemos:</p>\n<ul>\n<li>Gestion integral de zonas verdes municipales</li>\n<li>Planes de prevencion de incendios para urbanizaciones</li>\n<li>Mantenimiento de jardines y parques publicos</li>\n<li>Asesoramiento tecnico en arboricultura urbana</li>\n</ul>\n\n<h2>Compromiso con la comarca</h2>\n<p>Desde nuestra sede en <strong>Menarguens</strong>, servimos a toda la provincia de Lleida y zonas limotrofes. Nuestro conocimiento del terreno y las especies locales nos permite ofrecer soluciones adaptadas a cada situacion.</p>\n\n<p>Si necesitas servicios forestales o de mantenimiento de zonas verdes, no dudes en contactarnos. Estaremos encantados de atenderte y prepararte un presupuesto sin compromiso.</p>','images/gallery/trabajo-forestal-2.jpg','Equipo Manzer Agroforestal en campo',2,1,'published','2026-03-14 04:00:12',14,1,0,1,0,'2026-03-17 04:00:12','2026-03-17 04:00:12',NULL),(5,'Sostenibilidad en trabajos forestales: nuestro compromiso con el medio ambiente','sostenibilidad-trabajos-forestales','En Manzer Agroforestal la sostenibilidad no es una palabra, es una forma de trabajar. Descubre como minimizamos nuestro impacto ambiental.','<p>La sostenibilidad es uno de los pilares fundamentales de Manzer Agroforestal. Cada trabajo que realizamos se ejecuta pensando en el <strong>impacto ambiental</strong> y en la conservacion del ecosistema.</p>\n\n<h2>Nuestras practicas sostenibles</h2>\n\n<h3>Gestion de residuos</h3>\n<p>Los restos vegetales de nuestros trabajos se gestionan de forma responsable. Siempre que es posible, realizamos el <strong>triturado in situ</strong> para su aprovechamiento como acolchado o biomasa.</p>\n\n<h3>Maquinaria eficiente</h3>\n<p>Utilizamos maquinaria de ultima generacion con <strong>motores de bajas emisiones</strong> y mantenimiento preventivo para minimizar el consumo de combustible y las emisiones contaminantes.</p>\n\n<h3>Respeto por la biodiversidad</h3>\n<p>Antes de cada intervencion, evaluamos la presencia de <strong>fauna protegida</strong> (nidos, refugios) y especies vegetales de interes. Adaptamos nuestro calendario de trabajo para respetar los periodos de nidificacion y reproduccion.</p>\n\n<h3>Formacion continua</h3>\n<p>Nuestro equipo recibe formacion continua en <strong>buenas practicas ambientales</strong> y en las ultimas tecnicas de arboricultura sostenible.</p>\n\n<blockquote>\n<p>Creemos que el trabajo forestal bien hecho es la mejor herramienta para la conservacion de nuestros bosques.</p>\n</blockquote>','images/gallery/trabajo-forestal-3.jpg','Trabajo forestal sostenible',4,1,'published','2026-03-09 04:00:12',15,0,1,1,0,'2026-03-17 04:00:12','2026-03-17 15:41:56',NULL);
/*!40000 ALTER TABLE `blog_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_tags`
--

DROP TABLE IF EXISTS `blog_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog_tags` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blog_tags_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_tags`
--

LOCK TABLES `blog_tags` WRITE;
/*!40000 ALTER TABLE `blog_tags` DISABLE KEYS */;
INSERT INTO `blog_tags` VALUES (1,'arboricultura','arboricultura','2026-03-17 04:00:12','2026-03-17 04:00:12'),(2,'seguridad','seguridad','2026-03-17 04:00:12','2026-03-17 04:00:12'),(3,'incendios','incendios','2026-03-17 04:00:12','2026-03-17 04:00:12'),(4,'poda','poda','2026-03-17 04:00:12','2026-03-17 04:00:12'),(5,'tala','tala','2026-03-17 04:00:12','2026-03-17 04:00:12'),(6,'sostenibilidad','sostenibilidad','2026-03-17 04:00:12','2026-03-17 04:00:12'),(7,'Lleida','lleida','2026-03-17 04:00:12','2026-03-17 04:00:12'),(8,'normativa','normativa','2026-03-17 04:00:12','2026-03-17 04:00:12');
/*!40000 ALTER TABLE `blog_tags` ENABLE KEYS */;
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
  `ultima_actividad` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `carritos_empresa_id_foreign` (`empresa_id`),
  KEY `carritos_session_id_empresa_id_index` (`session_id`,`empresa_id`),
  KEY `carritos_ultima_actividad_index` (`ultima_actividad`),
  CONSTRAINT `carritos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carritos`
--

LOCK TABLES `carritos` WRITE;
/*!40000 ALTER TABLE `carritos` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,NULL,'Lapiz 1','lapiz-1','Lapices 1',NULL,1,1,'2025-07-29 05:13:45','2025-07-29 05:13:55'),(2,NULL,'Lapiz 3','lapiz-3','khjkhk',NULL,1,0,'2025-08-07 19:18:52','2025-08-07 19:18:52');
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
-- Table structure for table `cleaner_hour_prices`
--

DROP TABLE IF EXISTS `cleaner_hour_prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cleaner_hour_prices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `num_cleaners` int(11) NOT NULL,
  `num_hours` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cleaner_hour_prices`
--

LOCK TABLES `cleaner_hour_prices` WRITE;
/*!40000 ALTER TABLE `cleaner_hour_prices` DISABLE KEYS */;
/*!40000 ALTER TABLE `cleaner_hour_prices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cleaning_order_transactions`
--

DROP TABLE IF EXISTS `cleaning_order_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cleaning_order_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cleaning_order_id` bigint(20) unsigned NOT NULL,
  `stripe_session_id` varchar(255) DEFAULT NULL,
  `stripe_payment_intent_id` varchar(255) DEFAULT NULL,
  `stripe_charge_id` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'USD',
  `status` enum('pending','processing','succeeded','failed','cancelled','refunded') NOT NULL DEFAULT 'pending',
  `payment_method_type` varchar(255) DEFAULT NULL,
  `payment_method_brand` varchar(255) DEFAULT NULL,
  `payment_method_last4` varchar(255) DEFAULT NULL,
  `stripe_session_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`stripe_session_data`)),
  `stripe_payment_intent_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`stripe_payment_intent_data`)),
  `error_code` varchar(255) DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `webhook_events` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`webhook_events`)),
  `session_created_at` timestamp NULL DEFAULT NULL,
  `payment_succeeded_at` timestamp NULL DEFAULT NULL,
  `payment_failed_at` timestamp NULL DEFAULT NULL,
  `refunded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cleaning_order_transactions_stripe_session_id_unique` (`stripe_session_id`),
  KEY `cleaning_order_transactions_stripe_session_id_index` (`stripe_session_id`),
  KEY `cleaning_order_transactions_stripe_payment_intent_id_index` (`stripe_payment_intent_id`),
  KEY `cleaning_order_transactions_cleaning_order_id_status_index` (`cleaning_order_id`,`status`),
  KEY `cleaning_order_transactions_status_index` (`status`),
  CONSTRAINT `cleaning_order_transactions_cleaning_order_id_foreign` FOREIGN KEY (`cleaning_order_id`) REFERENCES `cleaning_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cleaning_order_transactions`
--

LOCK TABLES `cleaning_order_transactions` WRITE;
/*!40000 ALTER TABLE `cleaning_order_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `cleaning_order_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cleaning_orders`
--

DROP TABLE IF EXISTS `cleaning_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cleaning_orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `street_address` varchar(255) NOT NULL,
  `district_id` bigint(20) unsigned DEFAULT NULL,
  `unit_apt` varchar(255) DEFAULT NULL,
  `preferred_date` date NOT NULL,
  `preferred_time` varchar(255) NOT NULL,
  `date_flexible` tinyint(1) NOT NULL DEFAULT 0,
  `time_flexible` tinyint(1) NOT NULL DEFAULT 0,
  `num_bathrooms` int(11) DEFAULT NULL,
  `num_bedrooms` int(11) DEFAULT NULL,
  `num_kitchens` int(11) DEFAULT NULL,
  `other_rooms` varchar(255) DEFAULT NULL,
  `num_other_rooms` int(11) DEFAULT NULL,
  `other_rooms_desc` varchar(255) DEFAULT NULL,
  `num_cleaners` int(11) DEFAULT NULL,
  `num_hours` int(11) DEFAULT NULL,
  `parking` varchar(255) DEFAULT NULL,
  `property_access` varchar(255) DEFAULT NULL,
  `access_notes` text DEFAULT NULL,
  `square_footage_range` varchar(255) DEFAULT NULL,
  `service_type` varchar(255) DEFAULT NULL,
  `base_price` decimal(10,2) NOT NULL,
  `service_type_price` decimal(10,2) DEFAULT NULL,
  `extras_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'USD',
  `coupon_id` bigint(20) unsigned DEFAULT NULL,
  `coupon_code` varchar(255) DEFAULT NULL,
  `extras` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`extras`)),
  `status` enum('pending','processing','paid','confirmed','scheduled','in_progress','completed','cancelled','refunded') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cleaning_orders_order_number_unique` (`order_number`),
  KEY `cleaning_orders_district_id_foreign` (`district_id`),
  KEY `cleaning_orders_coupon_id_foreign` (`coupon_id`),
  KEY `cleaning_orders_order_number_index` (`order_number`),
  KEY `cleaning_orders_status_created_at_index` (`status`,`created_at`),
  KEY `cleaning_orders_email_index` (`email`),
  KEY `cleaning_orders_preferred_date_index` (`preferred_date`),
  CONSTRAINT `cleaning_orders_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cleaning_orders_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cleaning_orders`
--

LOCK TABLES `cleaning_orders` WRITE;
/*!40000 ALTER TABLE `cleaning_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `cleaning_orders` ENABLE KEYS */;
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
INSERT INTO `clientes` VALUES (1,NULL,'1234567','Cliente','vblogsanti@gmail.com','3202230467',1,817,5,6,1,'2025-07-28 03:59:31','2025-08-07 14:24:58'),(2,NULL,'12345678','Cliente 2','vblogsant2i@gmail.com','3202304672',1,4,5,2,1,'2025-07-31 07:49:52','2025-07-31 07:49:52'),(3,NULL,'123456789','Cliente 3','cliente3@gmail.com','321321213',1,137,6,5,1,'2025-08-07 19:18:22','2025-08-07 19:18:22'),(4,NULL,'12345674','Cliente','vblogsantif@gmail.com','+573202230467',1,161,1,1,1,'2025-08-18 23:59:40','2025-08-18 23:59:40');
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
  KEY `comisiones_empresa_id_estado_created_at_index` (`empresa_id`,`estado`,`created_at`),
  KEY `comisiones_compra_id_index` (`compra_id`),
  CONSTRAINT `comisiones_compra_id_foreign` FOREIGN KEY (`compra_id`) REFERENCES `compras` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comisiones_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comisiones`
--

LOCK TABLES `comisiones` WRITE;
/*!40000 ALTER TABLE `comisiones` DISABLE KEYS */;
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
  `nombre_cliente` varchar(255) NOT NULL,
  `email_cliente` varchar(255) NOT NULL,
  `telefono_cliente` varchar(255) NOT NULL,
  `direccion_envio` varchar(255) DEFAULT NULL,
  `ciudad_id` bigint(20) unsigned DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `impuestos` decimal(12,2) NOT NULL DEFAULT 0.00,
  `costo_envio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL,
  `estado` enum('pendiente','procesando','pagada','enviada','entregada','cancelada','reembolsada') NOT NULL DEFAULT 'pendiente',
  `notas` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `compras_numero_compra_unique` (`numero_compra`),
  KEY `compras_ciudad_id_foreign` (`ciudad_id`),
  KEY `compras_empresa_id_estado_created_at_index` (`empresa_id`,`estado`,`created_at`),
  KEY `compras_numero_compra_index` (`numero_compra`),
  CONSTRAINT `compras_ciudad_id_foreign` FOREIGN KEY (`ciudad_id`) REFERENCES `ciudades` (`id`) ON DELETE SET NULL,
  CONSTRAINT `compras_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compras`
--

LOCK TABLES `compras` WRITE;
/*!40000 ALTER TABLE `compras` DISABLE KEYS */;
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
  `pasarela` varchar(255) NOT NULL DEFAULT 'wompi',
  `public_key` varchar(255) DEFAULT NULL,
  `private_key` varchar(255) DEFAULT NULL,
  `event_key` varchar(255) DEFAULT NULL,
  `webhook_url` varchar(255) DEFAULT NULL,
  `modo_prueba` tinyint(1) NOT NULL DEFAULT 1,
  `configuracion_adicional` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`configuracion_adicional`)),
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `configuracion_pasarela_pasarela_index` (`pasarela`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configuracion_pasarela`
--

LOCK TABLES `configuracion_pasarela` WRITE;
/*!40000 ALTER TABLE `configuracion_pasarela` DISABLE KEYS */;
/*!40000 ALTER TABLE `configuracion_pasarela` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coupons` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL DEFAULT 'percentage',
  `discount_value` decimal(10,2) NOT NULL,
  `min_order_amount` decimal(10,2) DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `usage_count` int(11) NOT NULL DEFAULT 0,
  `start_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `coupons_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupons`
--

LOCK TABLES `coupons` WRITE;
/*!40000 ALTER TABLE `coupons` DISABLE KEYS */;
/*!40000 ALTER TABLE `coupons` ENABLE KEYS */;
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
-- Table structure for table `districts`
--

DROP TABLE IF EXISTS `districts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `districts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `state` varchar(255) DEFAULT NULL,
  `postcode` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `districts`
--

LOCK TABLES `districts` WRITE;
/*!40000 ALTER TABLE `districts` DISABLE KEYS */;
/*!40000 ALTER TABLE `districts` ENABLE KEYS */;
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
  `twitter_url` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(255) DEFAULT NULL,
  `horario_atencion` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`horario_atencion`)),
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `porcentaje_comision` decimal(5,2) NOT NULL DEFAULT 10.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `empresas_usuario_id_unique` (`usuario_id`),
  UNIQUE KEY `empresas_slug_unique` (`slug`),
  KEY `empresas_slug_activo_index` (`slug`,`activo`),
  CONSTRAINT `empresas_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresas`
--

LOCK TABLES `empresas` WRITE;
/*!40000 ALTER TABLE `empresas` DISABLE KEYS */;
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
INSERT INTO `enlaces_acceso` VALUES (1,2,NULL,1,'176CHVNSJ3RhAgrfNrVcv21Y85tdE3rQ',1,1,1,'2025-08-02 20:30:32',0,7,'2025-08-01 22:11:53','lololol','2025-08-02 01:30:32','2025-08-02 03:30:26'),(2,2,NULL,1,'eHuaPay9pzBLis24Ww8o4rjkArXfp0TG',2,0,1,'2025-08-03 20:31:22',1,48,'2025-08-01 21:59:20','asasdas','2025-08-02 01:31:22','2025-08-02 02:59:20'),(3,2,NULL,1,'HKwmaSVDafdTQyI53os8rtrNUtILVBMs',1,1,1,'2025-08-03 12:55:58',0,1,'2025-08-02 12:57:06','kgfvkg','2025-08-02 17:55:58','2025-08-06 22:23:42'),(4,1,NULL,1,'sK0rh2MwLHqc3tvW9txrY5920aPkjnsw',7,0,1,'2025-08-11 17:17:49',1,2,'2025-08-04 12:26:23','sdasd','2025-08-04 22:17:49','2025-08-04 17:26:23'),(5,1,NULL,1,'KlAOCwzO0hnDVwUNShyhHa9Sf5bLJPzh',5,1,1,'2025-08-12 10:49:28',1,1,'2025-08-07 20:20:35','fsdfs','2025-08-07 15:49:28','2025-08-08 01:20:35'),(6,1,NULL,1,'isQ5ktVaN8U7mCpvWNTWy9lVX2J9pYMq',3,1,0,'2025-08-10 10:49:56',1,1,'2025-08-07 20:18:50','zfsdf','2025-08-07 15:49:56','2025-08-08 01:18:50'),(7,1,NULL,1,'UyN9iKgbna1P0zwFEluvm3JGhrGtJMXM',7,0,0,'2025-08-14 20:26:10',1,1,'2025-08-07 20:26:28','Le toca ingresar','2025-08-08 01:26:10','2025-08-08 01:26:28'),(8,1,NULL,1,'NXm0QmfntkbzqVKnxvuNwRjZEzj1COk9',31,1,1,'2025-09-07 20:29:43',1,3,'2025-08-07 20:30:54',NULL,'2025-08-08 01:29:43','2025-08-08 01:30:54');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `envios`
--

LOCK TABLES `envios` WRITE;
/*!40000 ALTER TABLE `envios` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `imagenes_productos`
--

LOCK TABLES `imagenes_productos` WRITE;
/*!40000 ALTER TABLE `imagenes_productos` DISABLE KEYS */;
INSERT INTO `imagenes_productos` VALUES (2,2,'imagenes/productos/2/1753753584_60621f1cf3fae096532233.webp',NULL,1,0,'2025-07-29 06:46:24','2025-07-29 06:46:24'),(3,1,'imagenes/productos/1/1753754014_pencil-146388_960_720.png',NULL,1,0,'2025-07-29 06:53:34','2025-07-29 06:53:34'),(4,3,'imagenes/productos/3/1753754196_pencil-146388_960_720.png',NULL,1,0,'2025-07-29 06:56:36','2025-07-29 06:56:36'),(5,4,'imagenes/productos/4/1753793093_pencil-146388_960_720.png',NULL,1,0,'2025-07-29 17:44:53','2025-07-29 17:44:53'),(6,1,'imagenes/productos/1/1753793568_60621f1cf3fae096532233.webp',NULL,2,0,'2025-07-29 17:52:48','2025-07-29 17:52:48'),(10,8,'imagenes/productos/8/1753796437_6888cf55be401_pencil-146388_960_720.png','PEPITO PEREZ',1,1,'2025-07-29 18:40:37','2025-07-29 18:40:37'),(11,8,'imagenes/productos/8/1753796437_6888cf55bf14c_60621f1cf3fae096532233.webp','PEPITO PEREZ',2,0,'2025-07-29 18:40:37','2025-07-29 18:40:37'),(12,9,'imagenes/productos/9/1753797448_6888d3484f0a4_Captura de pantalla 2025-07-28 202008.png','Negro color',1,1,'2025-07-29 18:57:28','2025-07-29 18:57:58'),(13,9,'imagenes/productos/9/1753797448_6888d3484fffd_60621f1cf3fae096532233.webp','Negro color',2,0,'2025-07-29 18:57:28','2025-07-29 18:57:58'),(14,11,'imagenes/productos/11/1753931210_688addca8a0ec_logo.png','Prueba sin variante con valor',1,1,'2025-07-31 08:06:50','2025-07-31 08:06:50'),(15,12,'imagenes/productos/12/1753931293_688ade1d332cd_bg02.jpg','Prueba con variante con valor',1,1,'2025-07-31 08:08:13','2025-07-31 08:08:13'),(16,13,'imagenes/productos/13/1753931579_688adf3bd2867_bg01.jpg','producto con dos imagenes',1,0,'2025-07-31 08:12:59','2025-07-31 08:12:59'),(17,13,'imagenes/productos/13/1753931579_688adf3bd4275_bg02.jpg','producto con dos imagenes',2,0,'2025-07-31 08:12:59','2025-07-31 08:12:59'),(18,13,'imagenes/productos/13/1753931579_688adf3bd683a_bg03.jpg','producto con dos imagenes',3,1,'2025-07-31 08:12:59','2025-07-31 08:12:59'),(19,14,'imagenes/productos/14/1754452715_6892d2eb077e7_60621f1cf3fae096532233.webp','aaaaaaaa',1,1,'2025-08-06 03:58:35','2025-08-06 03:58:35'),(20,15,'imagenes/productos/15/1754493929_689373e9e1c68_60621f1cf3fae096532233.webp','xxxxxxxxxxxxx',1,1,'2025-08-06 15:25:29','2025-08-06 15:25:29'),(21,16,'imagenes/productos/16/1754494224_6893751029569_ChatGPT Image 4 ago 2025, 09_41_43 a.m..png','ppppppppp',1,1,'2025-08-06 15:30:24','2025-08-06 15:31:01'),(22,17,'imagenes/productos/17/1754518950_6893d5a6091e8_pencil-146388_960_720.png','Rojo',1,1,'2025-08-06 22:22:30','2025-08-06 22:22:30'),(23,17,'imagenes/productos/17/1754518950_6893d5a60a00b_60621f1cf3fae096532233.webp','Rojo',2,0,'2025-08-06 22:22:30','2025-08-06 22:22:30'),(24,18,'imagenes/productos/18/1754582447_6894cdafdcf33_Captura de pantalla 2025-07-21 094808.png','controla_permitir_sin_variantes',1,1,'2025-08-07 16:00:47','2025-08-07 17:12:01'),(25,19,'imagenes/productos/19/1754594578_6894fd124d0a9_Captura de pantalla 2025-07-21 094944.png','controla_permitir_con_variantes',1,1,'2025-08-07 19:22:58','2025-08-07 19:22:58'),(26,19,'imagenes/productos/19/1754594578_6894fd124e272_Captura de pantalla 2025-07-21 095130.png','controla_permitir_con_variantes',2,0,'2025-08-07 19:22:58','2025-08-07 19:22:58'),(27,20,'imagenes/productos/20/1754953465_689a76f9ec215_Logo_Energy_transparente_4x.png','no_variant',1,1,'2025-08-11 23:04:25','2025-08-11 23:04:25'),(28,21,'imagenes/productos/21/variantes/28/1754953535_689a773f7c550_imagen-no-disponible-energy (1).png','mediano - Azul - variant',0,1,'2025-08-11 23:05:35','2025-08-11 23:05:35'),(29,21,'imagenes/productos/21/variantes/29/1754953535_689a773f7e5dc_imagen-no-disponible-energy-1200.png','das - Azul - variant',0,1,'2025-08-11 23:05:35','2025-08-11 23:05:35'),(30,22,'imagenes/productos/22/variantes/30/1754953610_689a778aa18d9_ChatGPT Image 4 ago 2025, 09_41_43 a.m. (1).png','Grande - Variante prueba - variant_no_prin',0,1,'2025-08-11 23:06:50','2025-08-11 23:06:50'),(33,23,'imagenes/productos/23/1754955255_689a7df751057_Logo_Energy.jpg','variant_fotos',1,1,'2025-08-11 23:34:15','2025-08-11 23:37:43'),(34,23,'imagenes/productos/23/1754955255_689a7df751d6b_pencil-146388_960_720.png','variant_fotos',2,0,'2025-08-11 23:34:15','2025-08-11 23:37:43'),(39,23,'imagenes/productos/23/variantes/42/1754955463_689a7ec714cec_LETRAS ENERGY.png','Grande - Variante prueba - variant_fotos',0,1,'2025-08-11 23:37:43','2025-08-11 23:37:43');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items_compra`
--

LOCK TABLES `items_compra` WRITE;
/*!40000 ALTER TABLE `items_compra` DISABLE KEYS */;
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
INSERT INTO `items_solicitud_cotizacion` VALUES (1,1,12,17,1,2.00,2.00,'Prueba con variante con valor','Prueba con variante con valor','Grande - Negro','2025-07-31 08:35:19','2025-07-31 08:35:19'),(2,1,11,NULL,3,1002.00,3006.00,'Prueba sin variante con valor','Prueba sin variante con valor',NULL,'2025-07-31 08:35:19','2025-07-31 08:35:19'),(3,1,13,NULL,4,2.00,8.00,'producto con dos imagenes','producto con dos imagenes',NULL,'2025-07-31 08:35:19','2025-07-31 08:35:19'),(4,2,11,NULL,1,1002.00,1002.00,'Prueba sin variante con valor','Prueba sin variante con valor',NULL,'2025-07-31 08:35:36','2025-07-31 08:35:36'),(5,3,9,15,1,333333.00,333333.00,'Negro','Negro color','Grande - Negro','2025-07-31 16:46:14','2025-07-31 16:46:14'),(6,3,9,16,2,333333.00,666666.00,'Negro','Negro color','Peque - Ne','2025-07-31 16:46:14','2025-07-31 16:46:14'),(7,4,11,NULL,2,1002.00,2004.00,'Prueba sin variante con valor','Prueba sin variante con valor',NULL,'2025-07-31 18:22:56','2025-07-31 18:22:56'),(8,5,11,NULL,1,1002.00,1002.00,'Prueba sin variante con valor','Prueba sin variante con valor',NULL,'2025-07-31 18:31:41','2025-07-31 18:31:41'),(9,5,12,17,3,2.00,6.00,'Prueba con variante con valor','Prueba con variante con valor','Grande - Negro','2025-07-31 18:31:41','2025-07-31 18:31:41'),(10,5,13,NULL,4,2.00,8.00,'producto con dos imagenes','producto con dos imagenes',NULL,'2025-07-31 18:31:41','2025-07-31 18:31:41'),(11,6,9,15,2,333333.00,666666.00,'Negro','Negro color','Grande - Negro','2025-07-31 19:15:29','2025-07-31 19:15:29'),(12,6,9,16,3,333333.00,999999.00,'Negro','Negro color','Peque - Ne','2025-07-31 19:15:29','2025-07-31 19:15:29'),(13,7,9,15,2,111111.00,222222.00,'Negro','Negro color','Grande - Negro','2025-07-31 19:31:18','2025-07-31 19:31:18'),(14,7,9,16,1,111111.00,111111.00,'Negro','Negro color','Peque - Ne','2025-07-31 19:31:18','2025-07-31 19:31:18'),(15,8,9,15,2,111111.00,222222.00,'Negro','Negro color','Grande - Negro','2025-07-31 19:58:31','2025-07-31 19:58:31'),(16,9,9,15,1,111111.00,111111.00,'Negro','Negro color','Grande - Negro','2025-07-31 20:05:20','2025-07-31 20:05:20'),(17,10,9,15,1,333333.00,333333.00,'Negro','Negro color','Grande - Negro','2025-08-02 02:59:36','2025-08-02 02:59:36'),(18,10,8,NULL,2,0.00,0.00,'lap-azul3','PEPITO PEREZ',NULL,'2025-08-02 02:59:36','2025-08-02 02:59:36'),(19,10,9,16,1,333333.00,333333.00,'Negro','Negro color','Peque - Ne','2025-08-02 02:59:36','2025-08-02 02:59:36'),(20,11,9,15,1,333333.00,333333.00,'Negro','Negro color','Grande - Negro','2025-08-02 03:02:30','2025-08-02 03:02:30'),(21,11,13,NULL,1,2.00,2.00,'producto con dos imagenes','producto con dos imagenes',NULL,'2025-08-02 03:02:30','2025-08-02 03:02:30'),(22,12,8,NULL,1,0.00,0.00,'lap-azul3','PEPITO PEREZ',NULL,'2025-08-02 17:53:56','2025-08-02 17:53:56'),(23,12,11,NULL,1,1006.00,1006.00,'Prueba sin variante con valor','Prueba sin variante con valor',NULL,'2025-08-02 17:53:56','2025-08-02 17:53:56'),(24,12,12,17,1,6.00,6.00,'Prueba con variante con valor','Prueba con variante con valor','Grande - Negro','2025-08-02 17:53:56','2025-08-02 17:53:56'),(25,13,9,15,1,111111.00,111111.00,'Negro','Negro color','Grande - Negro','2025-08-04 22:19:18','2025-08-04 22:19:18'),(26,13,9,16,2,111111.00,222222.00,'Negro','Negro color','Peque - Ne','2025-08-04 22:19:18','2025-08-04 22:19:18'),(27,13,13,NULL,1,8.00,8.00,'producto con dos imagenes','producto con dos imagenes',NULL,'2025-08-04 22:19:18','2025-08-04 22:19:18'),(28,14,8,NULL,1,0.00,0.00,'lap-azul3','PEPITO PEREZ',NULL,'2025-08-04 17:26:30','2025-08-04 17:26:30'),(29,15,18,NULL,2,5323.00,10646.00,'controla_permitir_sin_variantes','controla_permitir_sin_variantes',NULL,'2025-08-07 16:03:23','2025-08-07 16:03:23'),(30,19,18,NULL,6,5323.00,31938.00,'controla_permitir_sin_variantes','controla_permitir_sin_variantes',NULL,'2025-08-07 17:12:16','2025-08-07 17:12:16'),(31,23,1,26,3,6.00,18.00,'lap-azul','Lapiz Azul','Azul','2025-08-08 01:31:10','2025-08-08 01:31:10'),(32,23,1,27,3,6.00,18.00,'lap-azul','Lapiz Azul','Grande - Azul','2025-08-08 01:31:10','2025-08-08 01:31:10');
/*!40000 ALTER TABLE `items_solicitud_cotizacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `landing_about`
--

DROP TABLE IF EXISTS `landing_about`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `landing_about` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `page_title` varchar(255) NOT NULL DEFAULT 'Nosotros',
  `page_subtitle` varchar(255) DEFAULT NULL,
  `main_image_path` varchar(255) DEFAULT NULL,
  `purpose_title` varchar(255) NOT NULL DEFAULT 'Propósito',
  `purpose_content` text NOT NULL,
  `mission_title` varchar(255) NOT NULL DEFAULT 'Misión',
  `mission_content` text NOT NULL,
  `vision_title` varchar(255) NOT NULL DEFAULT 'Visión',
  `vision_content` text NOT NULL,
  `stats_years_experience` int(11) NOT NULL DEFAULT 16,
  `stats_happy_clients` int(11) NOT NULL DEFAULT 500,
  `stats_client_satisfaction` int(11) NOT NULL DEFAULT 100,
  `value1_icon` varchar(255) NOT NULL DEFAULT 'bi bi-award',
  `value1_title` varchar(255) NOT NULL DEFAULT 'Quality Assurance',
  `value1_description` text DEFAULT NULL,
  `value2_icon` varchar(255) NOT NULL DEFAULT 'bi bi-people',
  `value2_title` varchar(255) NOT NULL DEFAULT 'Customer Focus',
  `value2_description` text DEFAULT NULL,
  `value3_icon` varchar(255) NOT NULL DEFAULT 'bi bi-clock-history',
  `value3_title` varchar(255) NOT NULL DEFAULT 'Reliability',
  `value3_description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landing_about`
--

LOCK TABLES `landing_about` WRITE;
/*!40000 ALTER TABLE `landing_about` DISABLE KEYS */;
/*!40000 ALTER TABLE `landing_about` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `landing_carousel_images`
--

DROP TABLE IF EXISTS `landing_carousel_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `landing_carousel_images` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `image_path` varchar(255) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landing_carousel_images`
--

LOCK TABLES `landing_carousel_images` WRITE;
/*!40000 ALTER TABLE `landing_carousel_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `landing_carousel_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `landing_configuracion`
--

DROP TABLE IF EXISTS `landing_configuracion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `landing_configuracion` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) NOT NULL,
  `company_description` text NOT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(255) DEFAULT NULL,
  `contact_address` varchar(255) DEFAULT NULL,
  `google_maps_embed` varchar(255) DEFAULT NULL,
  `services_button_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landing_configuracion`
--

LOCK TABLES `landing_configuracion` WRITE;
/*!40000 ALTER TABLE `landing_configuracion` DISABLE KEYS */;
INSERT INTO `landing_configuracion` VALUES (2,'CLEAN ME','Somos un despacho orientado a resultados...',NULL,NULL,NULL,NULL,'#services',1,'2025-10-10 04:26:19','2025-10-10 04:26:19');
/*!40000 ALTER TABLE `landing_configuracion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `landing_contact_info`
--

DROP TABLE IF EXISTS `landing_contact_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `landing_contact_info` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `address` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `receive_messages_email` varchar(255) NOT NULL,
  `google_maps_embed` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landing_contact_info`
--

LOCK TABLES `landing_contact_info` WRITE;
/*!40000 ALTER TABLE `landing_contact_info` DISABLE KEYS */;
/*!40000 ALTER TABLE `landing_contact_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `landing_gallery_images`
--

DROP TABLE IF EXISTS `landing_gallery_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `landing_gallery_images` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `image_path` varchar(255) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landing_gallery_images`
--

LOCK TABLES `landing_gallery_images` WRITE;
/*!40000 ALTER TABLE `landing_gallery_images` DISABLE KEYS */;
INSERT INTO `landing_gallery_images` VALUES (1,'images/gallery/tala-en-altura.jpg','Tala en altura con sistema de trepa','Tala controlada en zona urbana','tala',1,1,'2026-03-17 04:00:12','2026-03-17 04:00:12'),(2,'images/gallery/poda-en-altura.jpg','Poda en altura mediante trepa','Poda de mantenimiento en arbol centenario','poda',2,1,'2026-03-17 04:00:12','2026-03-17 04:00:12'),(3,'images/gallery/desbroce-talud.jpg','Desbroce en talud con sistema de seguridad','Desbroce en pendiente pronunciada','desbroce',3,1,'2026-03-17 04:00:12','2026-03-17 04:00:12'),(4,'images/gallery/trabajo-carretera.jpg','Limpieza de margenes de carretera','Mantenimiento de cunetas en carretera comarcal','carreteras',4,1,'2026-03-17 04:00:12','2026-03-17 04:00:12'),(5,'images/gallery/bosque-panoramica.jpg','Vista panoramica de bosque gestionado','Bosque tras trabajos de prevencion','desbroce',5,1,'2026-03-17 04:00:12','2026-03-17 04:00:12'),(6,'images/gallery/trabajo-forestal-1.jpg','Equipo de trabajo en operacion de tala','Operacion con grua en zona residencial','tala',6,1,'2026-03-17 04:00:12','2026-03-17 04:00:12'),(7,'images/gallery/trabajo-forestal-2.jpg','Arborista en altura con equipo de seguridad','Trepa profesional con arneses certificados','poda',7,1,'2026-03-17 04:00:12','2026-03-17 04:00:12'),(8,'images/gallery/trabajo-forestal-3.jpg','Trabajo de poda con motosierra profesional','Corte de precision en rama de gran diametro','poda',8,1,'2026-03-17 04:00:12','2026-03-17 04:00:12'),(9,'images/gallery/trabajo-forestal-4.jpg','Operacion de tala controlada','Tala con sistema de control direccional','tala',9,1,'2026-03-17 04:00:12','2026-03-17 04:00:12'),(10,'images/gallery/trabajo-forestal-5.jpg','Trabajo forestal en altura','Intervencion en arbol de gran porte','tala',10,1,'2026-03-17 04:00:12','2026-03-17 04:00:12'),(11,'images/gallery/trabajo-forestal-6.jpg','Equipo de trabajo Manzer Agroforestal','Nuestro equipo en accion','poda',11,1,'2026-03-17 04:00:12','2026-03-17 04:00:12');
/*!40000 ALTER TABLE `landing_gallery_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `landing_hero_values`
--

DROP TABLE IF EXISTS `landing_hero_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `landing_hero_values` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `icon_class` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landing_hero_values`
--

LOCK TABLES `landing_hero_values` WRITE;
/*!40000 ALTER TABLE `landing_hero_values` DISABLE KEYS */;
/*!40000 ALTER TABLE `landing_hero_values` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `landing_home_configs`
--

DROP TABLE IF EXISTS `landing_home_configs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `landing_home_configs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `hero_title` varchar(255) NOT NULL DEFAULT 'CLEAN ME',
  `hero_subtitle` varchar(255) NOT NULL DEFAULT 'Top Quality Guaranteed',
  `hero_description` text DEFAULT NULL,
  `hero_image_path` varchar(255) DEFAULT NULL,
  `hero_services_button_url` varchar(255) NOT NULL DEFAULT '/servicios',
  `hero_estimate_button_url` varchar(255) NOT NULL DEFAULT '#contact',
  `about_title` varchar(255) NOT NULL DEFAULT 'WE ARE CLEAN ME',
  `about_lead` text DEFAULT NULL,
  `about_description` text DEFAULT NULL,
  `about_image_path` varchar(255) DEFAULT NULL,
  `about_years_experience` int(11) NOT NULL DEFAULT 16,
  `about_happy_clients` int(11) NOT NULL DEFAULT 500,
  `about_client_satisfaction` int(11) NOT NULL DEFAULT 100,
  `facebook_url` varchar(255) DEFAULT NULL,
  `instagram_url` varchar(255) DEFAULT NULL,
  `linkedin_url` varchar(255) DEFAULT NULL,
  `youtube_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landing_home_configs`
--

LOCK TABLES `landing_home_configs` WRITE;
/*!40000 ALTER TABLE `landing_home_configs` DISABLE KEYS */;
/*!40000 ALTER TABLE `landing_home_configs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `landing_layout_config`
--

DROP TABLE IF EXISTS `landing_layout_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `landing_layout_config` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `site_title` varchar(255) NOT NULL DEFAULT 'Montano&Co',
  `topbar_email` varchar(255) NOT NULL DEFAULT 'contacto@ejemplo.com',
  `topbar_phone` varchar(255) NOT NULL DEFAULT '+57 310 000 0000',
  `twitter_url` varchar(255) DEFAULT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  `instagram_url` varchar(255) DEFAULT NULL,
  `linkedin_url` varchar(255) DEFAULT NULL,
  `whatsapp_url` varchar(255) DEFAULT NULL,
  `tiktok_url` varchar(255) DEFAULT NULL,
  `footer_address` varchar(255) NOT NULL DEFAULT 'Calle 108 #10-20',
  `footer_city` varchar(255) NOT NULL DEFAULT 'Bogotá, Colombia',
  `footer_phone` varchar(255) NOT NULL DEFAULT '+57 310 000 0000',
  `footer_email` varchar(255) NOT NULL DEFAULT 'info@ejemplo.com',
  `copyright_company` varchar(255) NOT NULL DEFAULT 'Montano&Co.',
  `footer_description` text DEFAULT NULL,
  `footer_logo_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landing_layout_config`
--

LOCK TABLES `landing_layout_config` WRITE;
/*!40000 ALTER TABLE `landing_layout_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `landing_layout_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `landing_pricing_config`
--

DROP TABLE IF EXISTS `landing_pricing_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `landing_pricing_config` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cleaner_price` decimal(10,2) NOT NULL DEFAULT 30.00,
  `hour_price` decimal(10,2) NOT NULL DEFAULT 30.00,
  `normal_service_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `deep_service_price` decimal(10,2) NOT NULL DEFAULT 50.00,
  `whatsapp_number` varchar(255) NOT NULL DEFAULT '3202230467',
  `extra_heavy_duty` decimal(8,2) NOT NULL DEFAULT 150.00,
  `inside_fridge_ea` decimal(8,2) NOT NULL DEFAULT 50.00,
  `inside_oven_ea` decimal(8,2) NOT NULL DEFAULT 50.00,
  `post_construction_government` decimal(8,2) NOT NULL DEFAULT 0.90,
  `post_construction_private` decimal(8,2) NOT NULL DEFAULT 0.60,
  `window_clean_interior` decimal(8,2) NOT NULL DEFAULT 8.00,
  `window_clean_exterior` decimal(8,2) NOT NULL DEFAULT 10.00,
  `recurring_weekly_discount` int(11) NOT NULL DEFAULT 20,
  `recurring_biweekly_discount` int(11) NOT NULL DEFAULT 15,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landing_pricing_config`
--

LOCK TABLES `landing_pricing_config` WRITE;
/*!40000 ALTER TABLE `landing_pricing_config` DISABLE KEYS */;
INSERT INTO `landing_pricing_config` VALUES (1,30.00,30.00,0.00,50.00,'573202230467',150.00,50.00,50.00,0.90,0.60,8.00,10.00,20,15,'2025-10-10 03:48:00','2025-10-10 04:33:22');
/*!40000 ALTER TABLE `landing_pricing_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `landing_pricing_ranges`
--

DROP TABLE IF EXISTS `landing_pricing_ranges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `landing_pricing_ranges` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sq_ft_min` int(11) NOT NULL,
  `sq_ft_max` int(11) NOT NULL,
  `initial_clean` decimal(8,2) NOT NULL,
  `weekly` decimal(8,2) NOT NULL,
  `biweekly` decimal(8,2) NOT NULL,
  `monthly` decimal(8,2) NOT NULL,
  `deep_clean` decimal(8,2) NOT NULL,
  `move_out_clean` decimal(8,2) NOT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landing_pricing_ranges`
--

LOCK TABLES `landing_pricing_ranges` WRITE;
/*!40000 ALTER TABLE `landing_pricing_ranges` DISABLE KEYS */;
INSERT INTO `landing_pricing_ranges` VALUES (1,0,1000,280.00,144.00,153.00,180.00,400.00,500.00,1,'2025-10-10 03:48:00','2025-10-10 03:48:00'),(2,1001,1500,300.00,160.00,170.00,200.00,450.00,550.00,2,'2025-10-10 03:48:00','2025-10-10 03:48:00'),(3,1501,2000,340.00,192.00,204.00,240.00,500.00,600.00,3,'2025-10-10 03:48:00','2025-10-10 03:48:00'),(4,2001,2500,400.00,240.00,255.00,300.00,600.00,750.00,4,'2025-10-10 03:48:00','2025-10-10 03:48:00'),(5,2501,3000,460.00,288.00,306.00,360.00,720.00,900.00,5,'2025-10-10 03:48:00','2025-10-10 03:48:00'),(6,3001,3500,520.00,336.00,357.00,420.00,840.00,1050.00,6,'2025-10-10 03:48:00','2025-10-10 03:48:00'),(7,3501,4000,580.00,384.00,408.00,480.00,960.00,1200.00,7,'2025-10-10 03:48:00','2025-10-10 03:48:00'),(8,4001,4500,640.00,432.00,459.00,540.00,1080.00,1350.00,8,'2025-10-10 03:48:00','2025-10-10 03:48:00'),(9,4501,5000,700.00,480.00,510.00,600.00,1200.00,1500.00,9,'2025-10-10 03:48:00','2025-10-10 03:48:00');
/*!40000 ALTER TABLE `landing_pricing_ranges` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `landing_services`
--

DROP TABLE IF EXISTS `landing_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `landing_services` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `icon_class` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `short_description` text DEFAULT NULL,
  `long_description` longtext DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `gallery_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gallery_images`)),
  `featured_image_alt` varchar(255) DEFAULT NULL,
  `page_id` bigint(20) unsigned DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `landing_services_slug_unique` (`slug`),
  KEY `landing_services_page_id_foreign` (`page_id`),
  CONSTRAINT `landing_services_page_id_foreign` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landing_services`
--

LOCK TABLES `landing_services` WRITE;
/*!40000 ALTER TABLE `landing_services` DISABLE KEYS */;
INSERT INTO `landing_services` VALUES (4,'bi bi-tree','Tala en Altura','tala-en-altura','Talamos empleando sistemas de control de caida para evitar roturas y danos materiales por posibles caidas.','Sistemas profesionales de control de caida para tala segura de arboles en zonas urbanas y rurales.','<h3>Tala profesional con maxima seguridad</h3>\n<p>En Manzer Agroforestal realizamos trabajos de tala en altura empleando las tecnicas mas avanzadas del sector. Nuestro equipo de arboristas certificados utiliza <strong>sistemas de control de caida</strong> que garantizan la seguridad tanto del personal como del entorno.</p>\n<h4>¿Cuando es necesaria la tala en altura?</h4>\n<ul>\n<li>Arboles que suponen un riesgo para edificaciones o infraestructuras</li>\n<li>Arboles enfermos o muertos con peligro de caida</li>\n<li>Arboles que interfieren con lineas electricas o de comunicaciones</li>\n<li>Clareo de masas forestales para prevencion de incendios</li>\n<li>Urbanizaciones y zonas residenciales con acceso limitado</li>\n</ul>\n<h4>Nuestro metodo de trabajo</h4>\n<p>Utilizamos tecnicas de <strong>trepa con cuerda</strong> y equipos de corte profesionales. Cada operacion se planifica meticulosamente, evaluando la direccion de caida, los obstaculos del entorno y las condiciones meteorologicas.</p>\n<p>Contamos con gruas y cestas elevadoras para los trabajos que lo requieran, garantizando siempre el minimo impacto en el entorno.</p>','images/gallery/tala-en-altura.jpg',NULL,NULL,5,1,1,'2026-03-17 04:00:12','2026-03-17 04:00:12'),(5,'bi bi-scissors','Poda en Altura','poda-en-altura','Mediante sistema de trepa. Donde las cestas y elevadoras no pueden acceder, empleamos sistemas de control de caida.','Poda tecnica mediante trepa con cuerda en arboles de gran porte y dificil acceso.','<h3>Poda tecnica en altura</h3>\n<p>La poda en altura es una de nuestras especialidades. Mediante <strong>sistema de trepa con cuerda</strong>, nuestros arboristas acceden a las copas de los arboles mas altos para realizar podas de formacion, mantenimiento y saneamiento.</p>\n<h4>Tipos de poda que realizamos</h4>\n<ul>\n<li><strong>Poda de formacion:</strong> Para guiar el crecimiento del arbol desde su juventud</li>\n<li><strong>Poda de mantenimiento:</strong> Eliminacion de ramas secas, cruzadas o mal orientadas</li>\n<li><strong>Poda de seguridad:</strong> Reduccion de peso en ramas que suponen un riesgo</li>\n<li><strong>Poda de aclareo:</strong> Mejora de la aireacion y la entrada de luz</li>\n</ul>\n<p>Trabajamos con herramientas profesionales y seguimos los protocolos de <strong>arboricultura moderna</strong>, respetando siempre la biologia del arbol para garantizar su salud a largo plazo.</p>','images/gallery/poda-en-altura.jpg',NULL,NULL,6,2,1,'2026-03-17 04:00:12','2026-03-17 04:00:12'),(6,'bi bi-hurricane','Desbroces','desbroces','Desbroces en taludes con sistemas de anclaje y linea de vida para trabajos en pendientes pronunciadas.','Desbroces mecanicos y manuales en taludes, parcelas y terrenos forestales con sistemas de seguridad.','<h3>Desbroces profesionales</h3>\n<p>Realizamos desbroces en todo tipo de terrenos, desde parcelas urbanas hasta taludes de dificil acceso. Nuestro equipo cuenta con <strong>sistemas de anclaje y linea de vida</strong> para trabajar con total seguridad en pendientes pronunciadas.</p>\n<h4>Servicios de desbroce</h4>\n<ul>\n<li>Desbroce mecanico con desbrozadora profesional</li>\n<li>Desbroce en taludes con sistemas de seguridad</li>\n<li>Limpieza de parcelas y terrenos abandonados</li>\n<li>Desbroce selectivo respetando especies protegidas</li>\n<li>Desbroce para prevencion de incendios forestales</li>\n</ul>\n<p>Utilizamos equipos de ultima generacion que permiten un trabajo rapido y eficiente, minimizando el impacto sobre el terreno y la vegetacion que se desea conservar.</p>','images/gallery/desbroce-talud.jpg',NULL,NULL,7,3,1,'2026-03-17 04:00:12','2026-03-17 04:00:12'),(7,'bi bi-fire','Prevencion de Incendios','prevencion-de-incendios','Limpieza del sotobosque y creacion de cortafuegos y podas para evitar la continuidad vertical de los arboles.','Creacion de cortafuegos, fajas auxiliares y limpieza del sotobosque para la prevencion de incendios forestales.','<h3>Prevencion de incendios forestales</h3>\n<p>La prevencion de incendios es una de las actividades mas importantes que realizamos. Trabajamos en la <strong>creacion de cortafuegos</strong>, la limpieza del sotobosque y la poda de arboles para evitar la continuidad vertical del fuego.</p>\n<h4>Actuaciones que realizamos</h4>\n<ul>\n<li><strong>Cortafuegos:</strong> Apertura y mantenimiento de franjas cortafuegos</li>\n<li><strong>Fajas auxiliares:</strong> Zonas de transicion con vegetacion controlada</li>\n<li><strong>Limpieza de sotobosque:</strong> Eliminacion de matorral y vegetacion baja</li>\n<li><strong>Poda de arboles:</strong> Eliminacion de ramas bajas para evitar continuidad vertical</li>\n<li><strong>Gestion de restos:</strong> Triturado y eliminacion de restos vegetales</li>\n</ul>\n<p>Colaboramos con ayuntamientos y entidades publicas en planes de prevencion de incendios, adaptando nuestras actuaciones a las normativas vigentes.</p>','images/gallery/bosque-panoramica.jpg',NULL,NULL,8,4,1,'2026-03-17 04:00:12','2026-03-17 04:00:12'),(8,'bi bi-signpost-2','Trabajo en Carreteras','trabajo-en-carreteras','Limpieza de carreteras y cunetas para saneamiento y prevencion de incendios en vias publicas.','Mantenimiento de margenes de carretera, limpieza de cunetas y gestion de vegetacion en vias publicas.','<h3>Mantenimiento de carreteras y vias</h3>\n<p>Realizamos trabajos de <strong>limpieza y mantenimiento de margenes de carretera</strong>, cunetas y zonas adyacentes a vias publicas. Estos trabajos son esenciales para la seguridad vial y la prevencion de incendios.</p>\n<h4>Trabajos que realizamos</h4>\n<ul>\n<li>Limpieza de cunetas y drenajes</li>\n<li>Desbroce de margenes de carretera</li>\n<li>Tala y poda de arboles junto a vias</li>\n<li>Retirada de arboles caidos por temporales</li>\n<li>Mantenimiento de zonas ajardinadas en rotondas y medianas</li>\n</ul>\n<p>Contamos con la senalizacion vial necesaria y cumplimos todas las normativas de seguridad para trabajos en carreteras.</p>','images/gallery/trabajo-carretera.jpg',NULL,NULL,9,5,1,'2026-03-17 04:00:12','2026-03-17 04:00:12'),(9,'bi bi-x-diamond','Retirada de Arboles','retirada-de-arboles','Retirada de arboles muertos con riesgo de caida en zonas urbanas y forestales.','Retirada segura de arboles muertos, danados o con riesgo de caida en entornos urbanos y naturales.','<h3>Retirada segura de arboles</h3>\n<p>Los arboles muertos o gravemente danados representan un <strong>peligro real</strong> para personas, vehiculos e infraestructuras. En Manzer Agroforestal realizamos la retirada de estos arboles con total seguridad.</p>\n<h4>Situaciones en las que intervenimos</h4>\n<ul>\n<li>Arboles muertos con riesgo de caida inminente</li>\n<li>Arboles danados por tormentas o temporales</li>\n<li>Arboles afectados por enfermedades o plagas</li>\n<li>Arboles que interfieren con obras o construcciones</li>\n<li>Emergencias por caida de arboles</li>\n</ul>\n<p>Evaluamos cada situacion de forma individual, determinando la mejor tecnica de retirada para garantizar la seguridad del entorno. Disponemos de servicio de <strong>emergencia 24 horas</strong> para situaciones criticas.</p>','images/gallery/trabajo-forestal-1.jpg',NULL,NULL,10,6,1,'2026-03-17 04:00:12','2026-03-17 04:00:12');
/*!40000 ALTER TABLE `landing_services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `landing_steps`
--

DROP TABLE IF EXISTS `landing_steps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `landing_steps` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `step_number` int(11) NOT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landing_steps`
--

LOCK TABLES `landing_steps` WRITE;
/*!40000 ALTER TABLE `landing_steps` DISABLE KEYS */;
/*!40000 ALTER TABLE `landing_steps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `landing_team_members`
--

DROP TABLE IF EXISTS `landing_team_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `landing_team_members` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `twitter_url` varchar(255) DEFAULT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  `instagram_url` varchar(255) DEFAULT NULL,
  `linkedin_url` varchar(255) DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landing_team_members`
--

LOCK TABLES `landing_team_members` WRITE;
/*!40000 ALTER TABLE `landing_team_members` DISABLE KEYS */;
/*!40000 ALTER TABLE `landing_team_members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `landing_testimonials`
--

DROP TABLE IF EXISTS `landing_testimonials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `landing_testimonials` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_name` varchar(255) NOT NULL,
  `client_role` varchar(255) DEFAULT NULL,
  `testimonial` text NOT NULL,
  `client_image_path` varchar(255) DEFAULT NULL,
  `rating` int(11) NOT NULL DEFAULT 5,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `landing_testimonials`
--

LOCK TABLES `landing_testimonials` WRITE;
/*!40000 ALTER TABLE `landing_testimonials` DISABLE KEYS */;
/*!40000 ALTER TABLE `landing_testimonials` ENABLE KEYS */;
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
INSERT INTO `listas_precios` VALUES (1,'Export 1','export1','Lista de precios exportación 1',1,1,NULL,NULL),(2,'Export 2','export2','Lista de precios exportación 2',1,2,NULL,NULL),(3,'Local 1','local1','Lista de precios local 1',1,3,NULL,NULL),(4,'Local 2','local2','Lista de precios local 2',1,4,NULL,NULL),(5,'Local 3','local3','Lista de precios local 3',1,5,NULL,NULL),(6,'Local 4','local4','Lista de precios local 4',1,6,NULL,NULL);
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs_transacciones`
--

LOCK TABLES `logs_transacciones` WRITE;
/*!40000 ALTER TABLE `logs_transacciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs_transacciones` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_resets_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',1),(5,'2025_06_18_195318_create_permission_tables',1),(6,'2025_06_27_195741_create_parametros_table',1),(7,'2025_07_10_150656_create_logs_table',1),(8,'2025_07_24_001402_crear_tabla_categorias',2),(9,'2025_07_24_001457_crear_tabla_productos',2),(10,'2025_07_24_001504_crear_tabla_variantes_producto',2),(11,'2025_07_24_215052_crear_tabla_imagenes_producto',2),(12,'2025_07_24_215102_crear_tabla_listas_precios',2),(13,'2025_07_24_215111_crear_tabla_precios_producto',2),(14,'2025_07_24_225859_crear_tabla_precios_variantes',2),(15,'2025_07_24_225929_crear_tabla_clientes',2),(16,'2025_07_24_225957_crear_enlaces',2),(17,'2025_07_24_230023_crear_cotizacion',2),(18,'2025_07_24_230042_crear_cotizacion_items',2),(19,'2025_07_24_230110_crear_actualizacion_precio',2),(20,'2025_07_25_003515_crear_precios_productos',3),(21,'2025_07_25_011901_crear_add_user',4),(22,'2025_07_27_221651_create_pais_table',5),(23,'2025_07_27_221714_create_departamento_table',5),(24,'2025_07_27_221745_create_ciudades_table',5),(25,'2025_07_27_224058_create_update_clientes_add_pais_ciudad_table',6),(26,'2025_07_31_024154_create_update_clientes_add_nullable_solicitud_table',7),(27,'2025_08_01_195011_create_update_enlace_table',8),(30,'2025_08_06_100715_agregar_stock_22',9),(31,'2025_08_07_101807_agregar_stock_enlace',10),(32,'2025_08_07_161015_create_update_actualizaciones_precios_table',11),(34,'2025_08_14_151256_create_empresas_table',12),(35,'2025_08_14_151351_create_carrusel_empresas_table',12),(36,'2025_08_14_151422_add_empresa_id_to_productos_table',12),(37,'2025_08_14_151557_create_compras_table',12),(38,'2025_08_14_151651_create_items_compra_table',12),(39,'2025_08_14_151711_create_transacciones_pago_table',12),(40,'2025_08_14_151744_create_comisiones_table',12),(41,'2025_08_14_151808_create_configuracion_pasarela_table',12),(42,'2025_08_14_151831_create_logs_transacciones_table',12),(43,'2025_08_14_151853_create_pagos_empresas_table',12),(44,'2025_08_14_151913_create_envios_table',12),(45,'2025_08_14_151932_create_carritos_table',12),(46,'2025_08_14_152005_add_empresa_fields_to_clientes_table',12),(47,'2025_08_14_152027_add_empresa_id_to_enlaces_acceso_table',12),(48,'2025_08_14_152054_add_empresa_id_to_solicitudes_cotizacion_table',12),(49,'2025_08_16_154940_make_referencia_unique_per_empresa',13),(50,'2025_08_16_155023_make_sku_unique_per_product',13),(51,'2025_08_18_114652_add_empresa_id_to_categorias',14),(52,'2025_08_18_220341_add_imagen_to_categorias_table',15),(53,'2025_09_09_155923_create_landing_configuracion_table',16),(54,'2025_09_09_160128_create_landing_carousel_images_table',16),(55,'2025_09_09_160229_create_landing_services_table',16),(56,'2025_09_09_160241_create_landing_steps_table',16),(57,'2025_09_09_160254_create_landing_contact_info_table',16),(58,'2025_09_09_170851_create_landing_about_table',17),(59,'2025_09_09_170904_create_landing_team_members_table',17),(60,'2025_09_09_170916_create_landing_layout_config_table',17),(63,'2025_09_09_175404_create_pages_table',18),(64,'2025_09_09_175405_create_seo_table',18),(65,'2025_09_13_140109_add_description_to_landing_contact_info_table',19),(66,'2025_10_09_164017_create_landing_pricing_config_table',20),(67,'2025_10_25_125730_create_districts_table',21),(68,'2025_10_25_125930_create_coupons_table',21),(69,'2025_10_25_191521_create_cleaning_orders_table',21),(70,'2025_10_25_191545_create_cleaning_order_transactions_table',21),(71,'2025_10_27_190029_add_footer_fields_to_landing_layout_config_table',21),(72,'2025_10_27_191150_create_landing_home_configs_table',21),(73,'2025_10_27_192728_add_additional_fields_to_landing_about_table',21),(74,'2025_10_29_162105_create_landing_hero_values_table',21),(75,'2025_10_29_162319_create_landing_testimonials_table',21),(76,'2025_10_29_182544_add_room_details_to_cleaning_orders_table',21),(77,'2025_10_29_182600_create_service_extras_table',21),(78,'2025_10_29_182615_create_cleaner_hour_prices_table',21),(79,'2025_10_29_182630_create_room_type_prices_table',21),(80,'2025_10_30_094932_add_simplified_pricing_to_landing_pricing_config_table',21),(81,'2025_10_30_095251_update_cleaning_orders_for_simplified_pricing',21),(82,'2025_10_30_102744_make_square_footage_range_nullable_in_cleaning_orders',22),(83,'2026_03_16_000001_enhance_landing_services_table',23),(84,'2026_03_16_000002_create_blog_categories_table',23),(85,'2026_03_16_000003_create_blog_posts_table',23),(86,'2026_03_16_000004_create_blog_tags_table',23),(87,'2026_03_16_000005_create_landing_gallery_images_table',23),(88,'2026_03_16_000006_add_og_schema_to_seo_table',23),(89,'2026_03_16_000007_add_social_fields_to_layout_config',23);
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
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1),(2,'App\\Models\\User',5),(2,'App\\Models\\User',6);
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `movimientos_stock`
--

LOCK TABLES `movimientos_stock` WRITE;
/*!40000 ALTER TABLE `movimientos_stock` DISABLE KEYS */;
INSERT INTO `movimientos_stock` VALUES (1,16,NULL,'entrada',10,0,10,NULL,'ajuste_inventario','Stock inicial',1,NULL,'2025-08-06 15:30:24','2025-08-06 15:30:24'),(2,16,NULL,'ajuste',2,10,12,NULL,'ajuste_inventario','Llego mas',1,NULL,'2025-08-06 15:32:54','2025-08-06 15:32:54'),(3,16,NULL,'ajuste',-4,12,8,NULL,'ajuste_inventario','robaron',1,NULL,'2025-08-06 15:33:36','2025-08-06 15:33:36'),(4,16,NULL,'salida',3,8,5,'fsdf','venta','fsdf',1,NULL,'2025-08-06 15:34:33','2025-08-06 15:34:33'),(5,16,NULL,'entrada',2,5,7,'ad','compra','dasd',1,NULL,'2025-08-06 15:34:53','2025-08-06 15:34:53'),(6,18,NULL,'entrada',4,0,4,NULL,'ajuste_inventario','Stock inicial',1,NULL,'2025-08-07 16:00:47','2025-08-07 16:00:47'),(7,18,NULL,'salida',6,4,-2,'SC-20250807121216-QP8G','venta','Venta aplicada desde solicitud de cotización (permite stock negativo)',1,19,'2025-08-07 17:12:26','2025-08-07 17:12:26'),(11,8,NULL,'entrada',1,0,1,NULL,'compra',NULL,6,NULL,'2025-08-07 19:27:40','2025-08-07 19:27:40'),(12,1,26,'salida',3,0,-3,'SC-20250807203110-TI6K','venta','Venta aplicada desde solicitud de cotización (permite stock negativo)',1,23,'2025-08-08 01:34:02','2025-08-08 01:34:02'),(13,1,27,'salida',3,0,-3,'SC-20250807203110-TI6K','venta','Venta aplicada desde solicitud de cotización (permite stock negativo)',1,23,'2025-08-08 01:34:02','2025-08-08 01:34:02');
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
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `url_path` varchar(255) NOT NULL,
  `page_type` varchar(50) DEFAULT 'landing',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pages_slug_unique` (`slug`),
  KEY `pages_slug_index` (`slug`),
  KEY `pages_page_type_index` (`page_type`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` VALUES (1,'Inicio','home','/','landing',1,'2025-09-10 03:52:05','2025-09-10 03:52:05'),(2,'Nosotros','nosotros','/nosotros','landing',1,'2025-09-10 03:52:05','2025-09-10 03:52:05'),(3,'Equipo','equipo','/equipo','landing',1,'2025-09-10 03:52:05','2025-09-10 03:52:05'),(4,'Contacto','contacto','/contacto','landing',1,'2025-09-10 03:52:05','2025-09-10 03:52:05'),(5,'Tala en Altura','servicio-tala-en-altura','/servicios/tala-en-altura','service',1,'2026-03-17 04:00:12','2026-03-17 04:00:12'),(6,'Poda en Altura','servicio-poda-en-altura','/servicios/poda-en-altura','service',1,'2026-03-17 04:00:12','2026-03-17 04:00:12'),(7,'Desbroces','servicio-desbroces','/servicios/desbroces','service',1,'2026-03-17 04:00:12','2026-03-17 04:00:12'),(8,'Prevencion de Incendios','servicio-prevencion-de-incendios','/servicios/prevencion-de-incendios','service',1,'2026-03-17 04:00:12','2026-03-17 04:00:12'),(9,'Trabajo en Carreteras','servicio-trabajo-en-carreteras','/servicios/trabajo-en-carreteras','service',1,'2026-03-17 04:00:12','2026-03-17 04:00:12'),(10,'Retirada de Arboles','servicio-retirada-de-arboles','/servicios/retirada-de-arboles','service',1,'2026-03-17 04:00:12','2026-03-17 04:00:12'),(11,'Cuando es necesario talar un arbol: senales que debes conocer','blog-cuando-talar-arbol-senales','/blog/cuando-talar-arbol-senales','blog',1,'2026-03-17 04:00:12','2026-03-17 04:00:12'),(12,'Prevencion de incendios forestales: guia practica para propietarios','blog-prevencion-incendios-forestales-guia','/blog/prevencion-incendios-forestales-guia','blog',1,'2026-03-17 04:00:12','2026-03-17 04:00:12'),(13,'La poda en altura: tecnicas y seguridad en el trabajo arboreo','blog-poda-altura-tecnicas-seguridad','/blog/poda-altura-tecnicas-seguridad','blog',1,'2026-03-17 04:00:12','2026-03-17 04:00:12'),(14,'Manzer Agroforestal amplia sus servicios en la comarca del Segria','blog-manzer-amplia-servicios-segria','/blog/manzer-amplia-servicios-segria','blog',1,'2026-03-17 04:00:12','2026-03-17 04:00:12'),(15,'Sostenibilidad en trabajos forestales: nuestro compromiso con el medio ambiente','blog-sostenibilidad-trabajos-forestales','/blog/sostenibilidad-trabajos-forestales','blog',1,'2026-03-17 04:00:12','2026-03-17 04:00:12'),(16,'Servicios','servicios','/servicios','landing',1,'2026-03-17 13:32:14','2026-03-17 13:32:14'),(17,'Blog','blog','/blog','landing',1,'2026-03-17 13:32:14','2026-03-17 13:32:14');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagos_empresas`
--

LOCK TABLES `pagos_empresas` WRITE;
/*!40000 ALTER TABLE `pagos_empresas` DISABLE KEYS */;
/*!40000 ALTER TABLE `pagos_empresas` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=133 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `precios_productos`
--

LOCK TABLES `precios_productos` WRITE;
/*!40000 ALTER TABLE `precios_productos` DISABLE KEYS */;
INSERT INTO `precios_productos` VALUES (1,3,1,100000.00,1,'2025-07-29 06:56:49','2025-07-29 06:56:49'),(2,7,1,1000.00,1,'2025-07-29 18:38:50','2025-07-29 18:38:50'),(3,7,2,2000.00,1,'2025-07-29 18:38:50','2025-07-29 18:38:50'),(4,7,3,3000.00,1,'2025-07-29 18:38:50','2025-07-29 18:38:50'),(5,9,1,111111.00,1,'2025-07-29 18:57:28','2025-08-07 22:12:25'),(6,9,2,1111111.00,1,'2025-07-29 18:57:28','2025-08-07 22:12:25'),(7,9,3,111111.00,1,'2025-07-29 18:57:28','2025-08-07 22:12:25'),(8,9,4,111111.00,1,'2025-07-29 18:57:28','2025-08-07 22:12:25'),(9,9,6,111111.00,1,'2025-07-29 18:57:28','2025-07-29 18:57:28'),(15,11,1,100.00,1,'2025-07-31 08:06:50','2025-07-31 08:06:50'),(16,11,2,1002.00,1,'2025-07-31 08:06:50','2025-07-31 08:06:50'),(17,11,3,1003.00,1,'2025-07-31 08:06:50','2025-07-31 08:06:50'),(18,11,4,1004.00,1,'2025-07-31 08:06:50','2025-07-31 08:06:50'),(19,11,5,1005.00,1,'2025-07-31 08:06:50','2025-07-31 08:06:50'),(20,11,6,1006.00,1,'2025-07-31 08:06:50','2025-07-31 08:06:50'),(21,12,1,1.00,1,'2025-07-31 08:08:13','2025-07-31 08:08:13'),(22,12,2,2.00,1,'2025-07-31 08:08:13','2025-07-31 08:08:13'),(23,12,3,3.00,1,'2025-07-31 08:08:13','2025-07-31 08:08:13'),(24,12,4,4.00,1,'2025-07-31 08:08:13','2025-07-31 08:08:13'),(25,12,5,5.00,1,'2025-07-31 08:08:13','2025-07-31 08:08:13'),(26,12,6,6.00,1,'2025-07-31 08:08:13','2025-07-31 08:08:13'),(27,13,1,1.00,1,'2025-07-31 08:12:59','2025-07-31 08:12:59'),(28,13,2,2.00,1,'2025-07-31 08:12:59','2025-07-31 08:12:59'),(29,13,3,3.00,1,'2025-07-31 08:12:59','2025-07-31 08:12:59'),(30,13,4,5.00,1,'2025-07-31 08:12:59','2025-07-31 08:12:59'),(31,13,5,7.00,1,'2025-07-31 08:12:59','2025-07-31 08:12:59'),(32,13,6,8.00,1,'2025-07-31 08:12:59','2025-07-31 08:12:59'),(33,14,1,1.00,1,'2025-08-06 03:58:35','2025-08-06 03:58:35'),(34,14,2,2.00,1,'2025-08-06 03:58:35','2025-08-06 03:58:35'),(35,14,3,3.00,1,'2025-08-06 03:58:35','2025-08-06 03:58:35'),(36,14,4,4.00,1,'2025-08-06 03:58:35','2025-08-06 03:58:35'),(37,14,5,5.00,1,'2025-08-06 03:58:35','2025-08-06 03:58:35'),(38,14,6,6.00,1,'2025-08-06 03:58:35','2025-08-06 03:58:35'),(39,15,1,1.00,1,'2025-08-06 15:25:29','2025-08-06 15:25:29'),(40,15,2,2.00,1,'2025-08-06 15:25:29','2025-08-06 15:25:29'),(41,15,3,3.00,1,'2025-08-06 15:25:29','2025-08-06 15:25:29'),(42,15,4,4.00,1,'2025-08-06 15:25:29','2025-08-06 15:25:29'),(43,15,5,6.00,1,'2025-08-06 15:25:29','2025-08-06 15:25:29'),(44,15,6,5.00,1,'2025-08-06 15:25:29','2025-08-06 15:25:29'),(45,16,1,1.00,1,'2025-08-06 15:30:24','2025-08-06 15:30:24'),(46,16,2,2.00,1,'2025-08-06 15:30:24','2025-08-06 15:30:24'),(47,16,3,3.00,1,'2025-08-06 15:30:24','2025-08-06 15:30:24'),(48,16,4,4.00,1,'2025-08-06 15:30:24','2025-08-06 15:30:24'),(49,16,5,5.00,1,'2025-08-06 15:30:24','2025-08-06 15:30:24'),(50,16,6,6.00,1,'2025-08-06 15:30:24','2025-08-06 15:30:24'),(51,17,1,1.00,1,'2025-08-06 22:22:30','2025-08-06 22:22:30'),(52,17,2,2.00,1,'2025-08-06 22:22:30','2025-08-06 22:22:30'),(53,17,3,3.00,1,'2025-08-06 22:22:30','2025-08-06 22:22:30'),(54,17,4,4.00,1,'2025-08-06 22:22:30','2025-08-06 22:22:30'),(55,17,5,5.00,1,'2025-08-06 22:22:30','2025-08-06 22:22:30'),(56,17,6,6.00,1,'2025-08-06 22:22:30','2025-08-06 22:22:30'),(57,18,1,12.00,1,'2025-08-07 16:00:47','2025-08-07 16:00:47'),(58,18,2,123.00,1,'2025-08-07 16:00:47','2025-08-07 16:00:47'),(59,18,3,1231.00,1,'2025-08-07 16:00:47','2025-08-07 16:00:47'),(60,18,4,124.00,1,'2025-08-07 16:00:47','2025-08-07 16:00:47'),(61,18,5,412.00,1,'2025-08-07 16:00:47','2025-08-07 16:00:47'),(62,18,6,5323.00,1,'2025-08-07 16:00:47','2025-08-07 16:00:47'),(63,19,1,1.00,1,'2025-08-07 19:22:58','2025-08-07 19:22:58'),(64,19,2,2.00,1,'2025-08-07 19:22:58','2025-08-07 19:22:58'),(65,19,3,11.00,1,'2025-08-07 19:22:58','2025-08-07 19:22:58'),(66,19,4,22.00,1,'2025-08-07 19:22:58','2025-08-07 19:22:58'),(67,19,5,33.00,1,'2025-08-07 19:22:58','2025-08-07 19:22:58'),(68,19,6,44.00,1,'2025-08-07 19:22:58','2025-08-07 19:22:58'),(69,8,1,99999.00,1,'2025-08-07 22:12:25','2025-08-07 22:12:25'),(70,8,2,88888.00,1,'2025-08-07 22:12:25','2025-08-07 22:12:25'),(71,8,3,777777.00,1,'2025-08-07 22:12:25','2025-08-07 22:12:25'),(72,9,5,111111.00,1,'2025-08-07 22:12:25','2025-08-07 22:12:25'),(73,1,1,1.00,1,'2025-08-07 22:29:32','2025-08-07 22:29:32'),(74,1,2,2.00,1,'2025-08-07 22:29:32','2025-08-07 22:29:32'),(75,1,3,3.00,1,'2025-08-07 22:29:32','2025-08-07 22:29:32'),(76,1,4,4.00,1,'2025-08-07 22:29:32','2025-08-07 22:29:32'),(77,1,5,5.00,1,'2025-08-07 22:29:32','2025-08-07 22:29:32'),(78,1,6,6.00,1,'2025-08-07 22:29:32','2025-08-07 22:29:32'),(79,8,4,2222222.00,1,'2025-08-07 22:42:31','2025-08-07 22:42:31'),(80,20,1,1.00,1,'2025-08-11 23:04:25','2025-08-11 23:04:25'),(81,20,2,2.00,1,'2025-08-11 23:04:25','2025-08-11 23:04:25'),(82,20,3,3.00,1,'2025-08-11 23:04:25','2025-08-11 23:04:25'),(83,20,4,4.00,1,'2025-08-11 23:04:25','2025-08-11 23:04:25'),(84,20,5,5.00,1,'2025-08-11 23:04:25','2025-08-11 23:04:25'),(85,20,6,6.00,1,'2025-08-11 23:04:25','2025-08-11 23:04:25'),(86,21,1,2.00,1,'2025-08-11 23:05:35','2025-08-11 23:05:35'),(87,21,2,3.00,1,'2025-08-11 23:05:35','2025-08-11 23:05:35'),(88,21,3,4.00,1,'2025-08-11 23:05:35','2025-08-11 23:05:35'),(89,21,4,5.00,1,'2025-08-11 23:05:35','2025-08-11 23:05:35'),(90,21,5,6.00,1,'2025-08-11 23:05:35','2025-08-11 23:05:35'),(91,21,6,7.00,1,'2025-08-11 23:05:35','2025-08-11 23:05:35'),(92,22,1,54.00,1,'2025-08-11 23:06:50','2025-08-11 23:06:50'),(93,22,2,43.00,1,'2025-08-11 23:06:50','2025-08-11 23:06:50'),(94,22,3,333.00,1,'2025-08-11 23:06:50','2025-08-11 23:06:50'),(95,23,1,2312.00,1,'2025-08-11 23:34:15','2025-08-11 23:34:15'),(96,23,2,312.00,1,'2025-08-11 23:34:15','2025-08-11 23:34:15'),(97,23,3,312.00,1,'2025-08-11 23:34:15','2025-08-11 23:34:15'),(98,24,1,432423.00,1,'2025-08-11 23:40:15','2025-08-11 23:40:15');
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
  `tiene_variantes` tinyint(1) NOT NULL DEFAULT 0,
  `controlar_stock` tinyint(1) NOT NULL DEFAULT 1,
  `permitir_venta_sin_stock` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `productos_empresa_referencia_unique` (`empresa_id`,`referencia`),
  KEY `productos_categoria_id_foreign` (`categoria_id`),
  KEY `productos_activo_categoria_id_index` (`activo`,`categoria_id`),
  KEY `productos_referencia_index` (`referencia`),
  KEY `productos_empresa_id_activo_index` (`empresa_id`,`activo`),
  CONSTRAINT `productos_categoria_id_foreign` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE,
  CONSTRAINT `productos_empresa_id_foreign` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (1,NULL,'lap-azul','Lapiz Azul','LApiz','Caja','Pallet','Azul',1,1,1,1,1,'2025-07-29 06:12:35','2025-08-08 01:30:50'),(2,NULL,'lap-arojo','Lapiz Rojo','Rojo','Caja','Pallet','Rojo',1,1,1,1,0,'2025-07-29 06:43:51','2025-07-29 06:43:51'),(3,NULL,'lapmorado','morado','morado','c','Pallet','Azul',1,1,0,1,0,'2025-07-29 06:55:49','2025-07-29 06:55:49'),(4,NULL,'lap-azul2','Lapiz Azul','asddas','Caja','Pallet','Azul',1,1,0,1,0,'2025-07-29 17:43:00','2025-07-29 17:43:00'),(5,NULL,'lap-azul9','Lapiz Azul','jh','Caja','Pallet','Rojo',1,1,1,1,0,'2025-07-29 18:06:35','2025-07-29 18:07:02'),(6,NULL,'lap-azul3423','PEPITO PEREZ','dfsdf','Caja','dsfds','Azul',1,1,1,1,0,'2025-07-29 18:11:26','2025-07-29 18:11:26'),(7,NULL,'Rojo','Rojo','dscjldfhsdj','ca','pall','rojo',1,1,1,1,0,'2025-07-29 18:38:50','2025-07-29 18:38:50'),(8,NULL,'lap-azul3','PEPITO PEREZ','adfas','Caja','Pallet','Azul',1,1,0,1,0,'2025-07-29 18:39:26','2025-07-29 18:40:37'),(9,NULL,'Negro','Negro color','Color negro','Caja','Pallet','Rojo',1,1,1,1,0,'2025-07-29 18:57:28','2025-07-29 18:57:28'),(11,NULL,'Prueba sin variante con valor','Prueba sin variante con valor','Prueba sin variante con valor','Caja','Pallet','Azul',1,1,0,1,0,'2025-07-31 08:06:50','2025-07-31 08:06:50'),(12,NULL,'Prueba con variante con valor','Prueba con variante con valor','Prueba con variante con valor','Caja','pall','Rojo',1,1,1,1,0,'2025-07-31 08:08:13','2025-07-31 08:08:13'),(13,NULL,'producto con dos imagenes','producto con dos imagenes','producto con dos imagenes','Caja','pall','Azul',1,1,0,1,0,'2025-07-31 08:12:59','2025-07-31 08:12:59'),(14,NULL,'aaaaa','aaaaaaaa','sdfsd','Caja','Pallet','Azul',1,1,1,1,0,'2025-08-06 03:58:34','2025-08-06 03:58:34'),(15,NULL,'xxxxxxxxxxxxxxxxx','xxxxxxxxxxxxx','xxxxxxxxx','Caja','Pallet','Azul',1,1,1,1,1,'2025-08-06 15:25:29','2025-08-06 15:25:29'),(16,NULL,'pppppppppp','ppppppppp','ppppppppppppp','ppppp','pppp','pppp',1,1,0,1,1,'2025-08-06 15:30:24','2025-08-06 15:31:01'),(17,NULL,'lap-azul944587','Rojo','kbhhk','ca','dsfds','Rojo',1,1,1,1,1,'2025-08-06 22:22:29','2025-08-06 22:22:29'),(18,NULL,'controla_permitir_sin_variantes','controla_permitir_sin_variantes','controla_permitir_sin_variantes','controla_permitir_sin_variantes','controla_permitir_sin_variantes','controla_permitir_sin_variantes',1,1,0,1,1,'2025-08-07 16:00:47','2025-08-07 17:12:01'),(19,NULL,'controla_permitir_con_variantes','controla_permitir_con_variantes','controla_permitir_con_variantes','controla_permitir_con_variantes','controla_permitir_con_variantes','controla_permitir_con_variantes',2,1,1,1,0,'2025-08-07 19:22:58','2025-08-07 19:22:58'),(20,NULL,'no_variant','no_variant','no_variant','Caja','dsfds','rojo',1,1,0,0,0,'2025-08-11 23:04:25','2025-08-11 23:04:25'),(21,NULL,'variant','variant','variant','variant','variant','variant',2,1,1,0,0,'2025-08-11 23:05:35','2025-08-11 23:05:35'),(22,NULL,'variant_no_prin','variant_no_prin','variant_no_prin','variant_no_prin','variant_no_prin','variant_no_prin',2,1,1,1,0,'2025-08-11 23:06:50','2025-08-11 23:06:50'),(23,NULL,'variant_fotos','variant_fotos','variant_fotos','variant_fotos','variant_fotos','variant_fotos',1,1,1,1,0,'2025-08-11 23:34:15','2025-08-11 23:34:15'),(24,NULL,'variantaxxx','variantaxxx','variantaxxx','variantaxxx','variantaxxx','variantaxxx',1,1,1,1,0,'2025-08-11 23:40:15','2025-08-11 23:40:15');
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'admin','web',NULL,NULL),(2,'vendedor','web',NULL,NULL),(3,'empresa','web',NULL,NULL);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `room_type_prices`
--

DROP TABLE IF EXISTS `room_type_prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `room_type_prices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `room_type` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `room_type_prices`
--

LOCK TABLES `room_type_prices` WRITE;
/*!40000 ALTER TABLE `room_type_prices` DISABLE KEYS */;
/*!40000 ALTER TABLE `room_type_prices` ENABLE KEYS */;
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
  `focus_keyword` varchar(100) DEFAULT NULL,
  `og_title` varchar(150) DEFAULT NULL,
  `og_description` text DEFAULT NULL,
  `og_image` varchar(255) DEFAULT NULL,
  `og_type` varchar(50) NOT NULL DEFAULT 'website',
  `schema_type` varchar(255) DEFAULT NULL,
  `schema_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`schema_data`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `seo_page_id_index` (`page_id`),
  KEY `seo_is_active_index` (`is_active`),
  KEY `seo_focus_keyword_index` (`focus_keyword`),
  CONSTRAINT `seo_page_id_foreign` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seo`
--

LOCK TABLES `seo` WRITE;
/*!40000 ALTER TABLE `seo` DISABLE KEYS */;
/*!40000 ALTER TABLE `seo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_extras`
--

DROP TABLE IF EXISTS `service_extras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_extras` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `icon_class` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_extras`
--

LOCK TABLES `service_extras` WRITE;
/*!40000 ALTER TABLE `service_extras` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_extras` ENABLE KEYS */;
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
INSERT INTO `solicitudes_cotizacion` VALUES (1,'SC-20250731033519-QK7F',2,NULL,NULL,'aplicada',3016.00,'Que le rinda','Buena la rata','2025-07-31 03:37:00',1,'2025-07-31 08:35:19','2025-07-31 08:37:00'),(2,'SC-20250731033536-XF6A',2,NULL,NULL,'pendiente',1002.00,'zonas',NULL,NULL,NULL,'2025-07-31 08:35:36','2025-07-31 08:35:37'),(3,'SC-20250731114614-E331',2,NULL,NULL,'pendiente',999999.00,'bo',NULL,NULL,NULL,'2025-07-31 16:46:14','2025-07-31 16:46:14'),(4,'SC-20250731132256-BQGA',2,NULL,NULL,'pendiente',2004.00,'ads',NULL,NULL,NULL,'2025-07-31 18:22:56','2025-07-31 18:22:56'),(5,'SC-20250731133141-F8D4',2,NULL,NULL,'pendiente',1016.00,'sads',NULL,NULL,NULL,'2025-07-31 18:31:41','2025-07-31 18:31:41'),(6,'SC-20250731141529-AXRY',2,NULL,NULL,'pendiente',1666665.00,'esfsd',NULL,NULL,NULL,'2025-07-31 19:15:29','2025-07-31 19:15:29'),(7,'SC-20250731143118-CMJ0',1,NULL,NULL,'aplicada',333333.00,'sjdfklsdhfh','kjhjhkgjkhgh','2025-07-31 14:33:25',1,'2025-07-31 19:31:18','2025-07-31 19:33:25'),(8,'SC-20250731145831-27PQ',1,NULL,NULL,'pendiente',222222.00,'fghg',NULL,NULL,NULL,'2025-07-31 19:58:31','2025-07-31 19:58:31'),(9,'SC-20250731150520-L2VY',1,NULL,NULL,'pendiente',111111.00,'dad',NULL,NULL,NULL,'2025-07-31 20:05:20','2025-07-31 20:05:20'),(10,'SC-20250801215936-RC56',2,NULL,2,'pendiente',666666.00,'buenaaa',NULL,NULL,NULL,'2025-08-02 02:59:36','2025-08-02 02:59:36'),(11,'SC-20250801220230-ABNZ',2,NULL,1,'pendiente',333335.00,'dasdsada',NULL,NULL,NULL,'2025-08-02 03:02:30','2025-08-02 03:02:30'),(12,'SC-20250802125356-ZSG7',1,NULL,NULL,'aplicada',1012.00,'jgfjg','jfdhf','2025-08-02 12:54:47',1,'2025-08-02 17:53:56','2025-08-02 17:54:47'),(13,'SC-20250804171918-0REE',1,NULL,4,'aplicada',333341.00,'asdasdas','assad','2025-08-04 17:21:47',1,'2025-08-04 22:19:18','2025-08-04 22:21:47'),(14,'SC-20250804122630-VRKU',1,NULL,4,'pendiente',0.00,NULL,NULL,NULL,NULL,'2025-08-04 17:26:30','2025-08-04 17:26:30'),(15,'SC-20250807110323-ILP5',1,NULL,NULL,'aplicada',10646.00,NULL,NULL,'2025-08-07 11:03:33',1,'2025-08-07 16:03:23','2025-08-07 16:03:33'),(19,'SC-20250807121216-QP8G',1,NULL,NULL,'aplicada',31938.00,'asdas','\n\nMovimientos de stock procesados:\ncontrola_permitir_sin_variantes - Descontado: 6 unidades (stock resultante: -2)','2025-08-07 12:12:26',1,'2025-08-07 17:12:16','2025-08-07 17:12:26'),(23,'SC-20250807203110-TI6K',1,NULL,8,'aplicada',36.00,NULL,'\n\nMovimientos de stock procesados:\nLapiz Azul - Azul - Descontado: 3 unidades (stock resultante: -3)\nLapiz Azul - Grande - Azul - Descontado: 3 unidades (stock resultante: -3)','2025-08-07 20:34:02',1,'2025-08-08 01:31:10','2025-08-08 01:34:02');
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
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_productos`
--

LOCK TABLES `stock_productos` WRITE;
/*!40000 ALTER TABLE `stock_productos` DISABLE KEYS */;
INSERT INTO `stock_productos` VALUES (3,2,4,0,0,0,NULL,NULL,NULL,1,'2025-08-06 15:22:38','2025-08-06 15:22:38'),(4,2,5,0,0,0,NULL,NULL,NULL,1,'2025-08-06 15:22:38','2025-08-06 15:22:38'),(5,3,NULL,0,0,0,NULL,NULL,NULL,1,'2025-08-06 15:22:38','2025-08-06 15:22:38'),(6,4,NULL,0,0,0,NULL,NULL,NULL,1,'2025-08-06 15:22:38','2025-08-06 15:22:38'),(7,6,6,0,0,0,NULL,NULL,NULL,1,'2025-08-06 15:22:38','2025-08-06 15:22:38'),(8,7,11,0,0,0,NULL,NULL,NULL,1,'2025-08-06 15:22:38','2025-08-06 15:22:38'),(9,7,12,0,0,0,NULL,NULL,NULL,1,'2025-08-06 15:22:38','2025-08-06 15:22:38'),(10,8,NULL,1,0,0,NULL,NULL,NULL,1,'2025-08-06 15:22:38','2025-08-07 19:27:40'),(11,9,15,0,0,0,NULL,NULL,NULL,1,'2025-08-06 15:22:38','2025-08-06 15:22:38'),(12,9,16,0,0,0,NULL,NULL,NULL,1,'2025-08-06 15:22:38','2025-08-06 15:22:38'),(13,11,NULL,0,0,0,NULL,NULL,NULL,1,'2025-08-06 15:22:38','2025-08-06 15:22:38'),(14,12,17,0,0,0,NULL,NULL,NULL,1,'2025-08-06 15:22:38','2025-08-06 15:22:38'),(15,13,NULL,0,0,0,NULL,NULL,NULL,1,'2025-08-06 15:22:38','2025-08-06 15:22:38'),(16,14,18,0,0,0,NULL,NULL,NULL,1,'2025-08-06 15:22:38','2025-08-06 15:22:38'),(17,15,19,0,0,0,NULL,NULL,NULL,1,'2025-08-06 15:25:29','2025-08-06 15:25:29'),(18,15,20,0,0,0,NULL,NULL,NULL,1,'2025-08-06 15:25:29','2025-08-06 15:25:29'),(19,16,NULL,7,0,2,6,'dfsdfs','asdas',1,'2025-08-06 15:30:24','2025-08-06 15:34:53'),(20,17,21,0,0,0,NULL,NULL,NULL,1,'2025-08-06 22:22:29','2025-08-06 22:22:29'),(21,18,NULL,-2,0,1,NULL,'asda',NULL,1,'2025-08-07 16:00:47','2025-08-07 17:12:26'),(22,19,22,0,0,0,NULL,NULL,NULL,1,'2025-08-07 19:22:58','2025-08-07 19:22:58'),(23,19,23,0,0,0,NULL,NULL,NULL,1,'2025-08-07 19:22:58','2025-08-07 19:22:58'),(24,1,26,-3,0,0,NULL,NULL,NULL,1,'2025-08-08 01:30:50','2025-08-08 01:34:02'),(25,1,27,-3,0,0,NULL,NULL,NULL,1,'2025-08-08 01:30:50','2025-08-08 01:34:02'),(26,22,30,0,0,0,NULL,NULL,NULL,1,'2025-08-11 23:06:50','2025-08-11 23:06:50'),(27,22,31,0,0,0,NULL,NULL,NULL,1,'2025-08-11 23:06:50','2025-08-11 23:06:50'),(38,23,42,0,0,0,NULL,NULL,NULL,1,'2025-08-11 23:37:43','2025-08-11 23:37:43'),(39,23,43,0,0,0,NULL,NULL,NULL,1,'2025-08-11 23:37:43','2025-08-11 23:37:43'),(43,24,47,0,0,0,NULL,NULL,NULL,1,'2025-08-11 23:40:50','2025-08-11 23:40:50'),(44,24,48,0,0,0,NULL,NULL,NULL,1,'2025-08-11 23:40:50','2025-08-11 23:40:50'),(45,24,49,0,0,0,NULL,NULL,NULL,1,'2025-08-11 23:40:50','2025-08-11 23:40:50');
/*!40000 ALTER TABLE `stock_productos` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transacciones_pago`
--

LOCK TABLES `transacciones_pago` WRITE;
/*!40000 ALTER TABLE `transacciones_pago` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','admin@admin.com',NULL,NULL,1,'$2y$10$EJrs9i9uCMwzi07EfXlyqeh0hM2Ds7MgtaV9g3/zc9/jzbuoofOIi','wpkVrYufMEE0l1MwVAfhdrtJvbGuOVsblgKQGXzsddBACQh1tn0XVt4OzOFt',NULL,NULL,NULL),(5,'Vendedor','ven@gmail.com',NULL,NULL,1,'$2y$10$OjS1AJVZJToV/XGP6aOw4u8MnIOJAfdHZRctENcLrSSkl.qadqDpW',NULL,NULL,'2025-07-28 02:13:56','2025-07-28 02:13:56'),(6,'Vendedor 3','vendedor3@gmail.com',NULL,NULL,1,'$2y$10$.ODrBEo0J3eVHfwG8HwbhuWlCqGgmUZtoHNOQK8aDPTXOVtJ5cCFy',NULL,NULL,'2025-08-07 19:15:54','2025-08-07 19:24:44');
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
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `variantes_productos`
--

LOCK TABLES `variantes_productos` WRITE;
/*!40000 ALTER TABLE `variantes_productos` DISABLE KEYS */;
INSERT INTO `variantes_productos` VALUES (4,2,'Grande','Rojo','lap-arojo-GRANDE-ROJO',1,'2025-07-29 06:44:13','2025-07-29 06:44:13'),(5,2,'Pequeno','Rojo','lap-arojo-PEQUENO-ROJO',1,'2025-07-29 06:44:21','2025-07-29 06:44:21'),(6,6,'Grande','Azul','lap-azul3423-GRANDE-AZUL',1,'2025-07-29 18:11:31','2025-07-29 18:11:31'),(11,7,'Grande','Rojo','Rojo-GRANDE-ROJO',1,'2025-07-29 18:40:09','2025-07-29 18:40:09'),(12,7,'mediano','Rojo','Rojo-PEQUE-ROJO',1,'2025-07-29 18:40:09','2025-07-29 18:40:09'),(15,9,'Grande','Negro','Negro-GRANDE-NEGRO',1,'2025-07-29 18:57:58','2025-07-29 18:57:58'),(16,9,'Peque','Ne','nnnnnn',1,'2025-07-29 18:57:58','2025-07-29 18:57:58'),(17,12,'Grande','Negro','Prueba con variante con valor-GRANDE-NEGRO',1,'2025-07-31 08:08:13','2025-07-31 08:08:13'),(18,14,'d','s','aaaaa-D-S',1,'2025-08-06 03:58:35','2025-08-06 03:58:35'),(19,15,'bbbbbbbbb','bbbbb','xxxxxxxxxxxxxxxxx-BBBBBBBBB-BBBBB',1,'2025-08-06 15:25:29','2025-08-06 15:25:29'),(20,15,'ccccccccc','ccccccccccccccc','xxxxxxxxxxxxxxxxx-CCCCCCCCC-CCCCCCCCCCCCCCC',1,'2025-08-06 15:25:29','2025-08-06 15:25:29'),(21,17,'peque','Negro','lap-azul944587-PEQUE-NEGRO',1,'2025-08-06 22:22:29','2025-08-06 22:22:29'),(22,19,'Grande','Negro','controla_permitir_con_variantes-GRANDE-NEGRO',1,'2025-08-07 19:22:58','2025-08-07 19:22:58'),(23,19,'mediano','Ne','controla_permitir_con_variantes-MEDIANO-NE',1,'2025-08-07 19:22:58','2025-08-07 19:22:58'),(26,1,NULL,'Azul','lap-azul-AZUL',1,'2025-08-08 01:30:50','2025-08-08 01:30:50'),(27,1,'Grande','Azul','Azul',1,'2025-08-08 01:30:50','2025-08-08 01:30:50'),(28,21,'mediano','Azul','variant-MEDIANO-AZUL',1,'2025-08-11 23:05:35','2025-08-11 23:05:35'),(29,21,'das','Azul','variant-DAS-AZUL',1,'2025-08-11 23:05:35','2025-08-11 23:05:35'),(30,22,'Grande','Variante prueba','variant_no_prin-GRANDE-VARIANTEPRUEBA',1,'2025-08-11 23:06:50','2025-08-11 23:06:50'),(31,22,'Grande','s','variant_no_prin-GRANDE-S',1,'2025-08-11 23:06:50','2025-08-11 23:06:50'),(42,23,'Grande','Variante prueba','variant_fotos-GRANDE-VARIANTEPRUEBA',1,'2025-08-11 23:37:43','2025-08-11 23:37:43'),(43,23,'mediano','Azul','variant_fotos-MEDIANO-AZUL',1,'2025-08-11 23:37:43','2025-08-11 23:37:43'),(47,24,'d','s','variantaxxx-D-S',1,'2025-08-11 23:40:50','2025-08-11 23:40:50'),(48,24,'Grande','Ne','variantaxxx-GRANDE-NE',1,'2025-08-11 23:40:50','2025-08-11 23:40:50'),(49,24,'mediano','Negro','variantaxxx-MEDIANO-NEGRO',1,'2025-08-11 23:40:50','2025-08-11 23:40:50');
/*!40000 ALTER TABLE `variantes_productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'cleanme'
--

--
-- Dumping routines for database 'cleanme'
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
