# Imágenes de la landing (provisionales)

Fotos genéricas de **Unsplash** (licencia Unsplash: uso comercial gratuito, sin atribución obligatoria;
ninguna es Unsplash+). Se reemplazan por fotos reales del local cuando el cliente las envíe.

Están en `public/images/landing/`, en WebP y en dos anchos (`-600` / `-1000`, `-640` / `-1200`, etc.) para `srcset`.
Para cambiar una foto basta con dejar archivos con el mismo nombre y anchos (o cambiar el nombre en
`config/panaderia.php` → `productos.*.imagen`, o en el parcial de la sección).

| Archivo | Sección | Foto en Unsplash | Autor |
|---|---|---|---|
| `hero-*.webp`, `og-1200.jpg` | Hero / vista previa al compartir | `photo-1509440159596-0249088772ff` | — |
| `hero-croissant-*.webp` | Tarjeta flotante del hero | `photo-1555507036-ab1f4038808a` | — |
| `historia-*.webp` | Nuestra historia | `photo-1530610476181-d83430b64dcd` | — |
| `pan-artesanal-*.webp` | Productos | `photo-1586444248902-2f64eddc13df` | — |
| `pan-tradicional-*.webp` | Productos | `photo-1608198093002-ad4e005484ec` | — |
| `pasteleria-*.webp` | Productos | `photo-1483695028939-5bb13f8648b0` | — |
| `tortas-*.webp` | Productos | `photo-1578985545062-69928b1d9587` | — |
| `desayunos-*.webp` | Productos | `photo-1676362536932-53df8ad0015a` | Raghavendra Mithare |
| `galletas-*.webp` | Productos | `photo-1558961363-fa8fdf82db35` | — |
| `paso-ingredientes-*.webp` | Del horno a tu mesa | `photo-1576186726580-a816e8b12896` | No Revisions |
| `paso-amasado-*.webp` | Del horno a tu mesa | `photo-1517686469429-8bdb88b9f907` | — |
| `paso-horneado-*.webp` | Del horno a tu mesa | `photo-1722635044899-5a48fa65117f` | Kamala Bright |
| `paso-mesa-*.webp` | Del horno a tu mesa | `photo-1704283735270-f973e7e9c14e` | Jakub Żerdzicki |
| `encargos-*.webp` | Banda de encargos | `photo-1565958011703-44f9829ba187` | — |
| `galeria-local-*.webp` | Galería | `photo-1517433670267-08bbd4be890f` | — |
| `galeria-vitrina-*.webp` | Galería | `photo-1568254183919-78a4f43a2877` | Yeh Xintong |
| `galeria-canela-*.webp` | Galería | `photo-1509365465985-25d11c17e812` | — |
| `galeria-baguettes-*.webp` | Galería | `photo-1595535873420-a599195b3f4a` | — |
| `galeria-tajado-*.webp` | Galería | `photo-1549931319-a545dcf3bc73` | — |

URL de cada foto: `https://images.unsplash.com/<id>` (admite `?w=…&h=…&fit=crop&fm=webp`).

## Espacios reservados para fotos del cliente

Componente `<x-landing.foto-pendiente texto="…" />`: "Foto del local" (Nuestra historia) y
"Fachada del local", "Nuestro equipo", "Interior del local" (Galería). Al recibir las fotos se cambia el
componente por un `<img>` o se pone la foto en el arreglo `$galeria` de `landing/partials/galeria.blade.php`.
