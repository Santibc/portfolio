# Módulo "Página web" — modelo de datos

Contenido editable de la landing pública (`/`). Solo lo administra el rol `admin`.

## Entidades

| Tabla | Qué guarda | Cardinalidad |
|---|---|---|
| `landing_ajustes` | Datos del negocio: nombre, propietaria, contacto, dirección, redes | **una sola fila** (id = 1, la crea la migración) |
| `landing_textos` | Textos de la página por `clave` (enum `TextoLanding`) | 0..1 por clave; si no hay fila se usa el texto por defecto del enum |
| `landing_horarios` | Franjas de horario (días + horas) | N, ordenadas |
| `landing_cifras` | Cifras de "Nuestra historia" (valor, prefijo, sufijo, texto) | N, ordenadas |
| `landing_valores` | Tarjetas de "Por qué elegirnos" (icono enum `IconoLanding`, título, texto) | N, ordenadas |
| `landing_productos` | Categorías de productos (título, descripción, imagen, activo) | N, ordenadas |
| `landing_pasos` | Pasos de "Del horno a tu mesa" (título, texto, imagen) | N, ordenados |
| `landing_galeria_fotos` | Galería (título/alt, imagen opcional = espacio reservado) | N, ordenadas |
| `landing_fotos` | Fotos fijas por `slot` (enum `FotoLanding`: hero, hero_mini, historia, local, encargos) | 1 por slot (las crea la migración) |

## Mini-ERD

```
users (id) ──┬─< landing_ajustes.updated_by   (nullable, nullOnDelete)
             └─< landing_textos.updated_by    (nullable, nullOnDelete)

landing_textos.clave           UNIQUE   (valores = TextoLanding)
landing_fotos.slot             UNIQUE   (valores = FotoLanding)
landing_*.orden                INDEX    (listas ordenadas)
landing_productos.activo       INDEX
```

## Normalización

- **1NF:** sin listas en columnas. Horario, cifras, valores, productos, pasos y galería son tablas propias
  (antes eran arreglos en `config/panaderia.php`). Los párrafos de la historia son un solo bloque de texto
  (contenido, no se filtra por párrafo).
- **2NF:** todas las PK son simples (`id`).
- **3NF:** los datos del negocio viven solo en `landing_ajustes`; los textos solo en `landing_textos`;
  las listas no repiten datos del negocio. La marquesina no se guarda: se arma con los títulos de los
  productos activos + el lema.
- Las claves de texto, los slots de foto y los iconos son **enums de PHP** (conjunto cerrado que define el
  diseño de la página), no tablas lookup: agregar uno implica tocar la vista igualmente.

## Imágenes (RULE 6)

`public/uploads/landing/`, nombre `{prefijo}_{id}_{time}.webp` + variante móvil `…-800.webp`.
`App\Services\ImagenLandingService` corrige la orientación EXIF, redimensiona (máx. 1600 px) y convierte a WebP.
El seeder `LandingSeeder` copia las fotos provisionales de `public/images/landing/` a esa carpeta.
