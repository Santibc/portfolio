================================================================================
                        DOCUMENTACION DEL SISTEMA
================================================================================

Este directorio contiene la documentacion completa del sistema en 2 PDFs.

================================================================================
ARCHIVOS DISPONIBLES
================================================================================

1. DOCUMENTACION_TECNICA.pdf (195 KB)
   - Contenido: PARTE 1 - Documentacion Tecnica (Secciones 1-8)
   - Para: Desarrolladores y administradores tecnicos
   - Color tema: Azul
   - Incluye:
     * Arquitectura del sistema
     * Esquema de base de datos (40+ tablas)
     * 12 modulos del sistema
     * API y endpoints
     * Seguridad y autenticacion
     * Workflows tecnicos
     * Comandos de desarrollo

2. MANUAL_USUARIO.pdf (96 KB)
   - Contenido: PARTE 2 - Manual de Usuario (Secciones 1-7)
   - Para: Usuarios finales del sistema
   - Color tema: Verde
   - Incluye:
     * INDICE (tabla de contenidos)
     * 1. Introduccion para Usuarios
     * 2. Guia de Configuracion Inicial (10 pasos)
     * 3. Modulos de Servicio Tecnico
     * 4. Operaciones Diarias
     * 5. Flujos de Trabajo Completos
     * 6. Preguntas Frecuentes
     * 7. Glosario de Terminos

================================================================================
ACCESO WEB
================================================================================

http://localhost/documentacion/DOCUMENTACION_TECNICA.pdf
http://localhost/documentacion/MANUAL_USUARIO.pdf

================================================================================
MEJORAS APLICADAS
================================================================================

OK Margenes correctos (30mm en todos los lados)
OK Sin caracteres especiales (???) - Todo convertido a ASCII
OK Separacion clara: Parte 1 (Tecnico) y Parte 2 (Usuario)
OK Estilos profesionales con colores distintivos
OK Solo 2 PDFs (tecnico y usuario)
OK Generacion via HTML directo (NO mediante CSS @page defectuoso)

================================================================================
ACTUALIZACION
================================================================================

Para regenerar ambos PDFs despues de editar:

1. Edita: DOCUMENTACION_SISTEMA.md (en la raiz del proyecto)
2. Ejecuta: php generar-ambos-pdfs.php
3. Los 2 PDFs se actualizaran automaticamente

SCRIPTS AUXILIARES:
- extract-part1.php: Extrae Parte 1 del markdown
- extract-part2.php: Extrae Parte 2 del markdown
- generar-doc-tecnica-completa.php: Genera solo PDF tecnico
- generar-manual-usuario-completo.php: Genera solo PDF usuario
- generar-ambos-pdfs.php: Genera ambos PDFs (RECOMENDADO)

================================================================================

Sistema: Portfolio B2B + Servicio Tecnico
Framework: Laravel 9.52
Version: 1.0
Fecha: 25/11/2024

================================================================================
