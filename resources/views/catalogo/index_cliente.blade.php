
  <!DOCTYPE html>
  <html lang="es">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
      .producto-card { transition: transform .2s; cursor: pointer; }
      .producto-card:hover { transform: translateY(-5px); }
      .cart-badge { position: absolute; top: -8px; right: -8px; }
      #cartSidebar { position: fixed; top:0; right:-400px; width:400px; height:100vh;
                     background:#fff; box-shadow:-2px 0 5px rgba(0,0,0,.1);
                     transition:right .3s; z-index:1050; }
      #cartSidebar.show { right:0; }
      .loading-overlay { position:fixed; top:0; left:0; width:100%; height:100%;
                         background:rgba(255,255,255,.9); z-index:9999;
                         display:flex; align-items:center; justify-content:center; }
      
      /* Contenedor de imagen con fondo para mejor visualización */
      .producto-imagen-container {
        height: 200px;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        border-bottom: 1px solid #dee2e6;
      }
      
      .producto-imagen {
        height: 100%;
        width: 100%;
        object-fit: contain;
        object-position: center;
      }
      
      /* Estilos para la navegación del carrusel */
      .carousel-pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 0;
        width: 100%;
      }
      
      .carousel-nav-btn {
        background: #87ceeb; /* Azul claro */
        border: none;
        color: #333;
        border-radius: 50%;
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 1.2rem;
        box-shadow: 0 2px 8px rgba(135,206,235,0.3);
      }
      
      .carousel-nav-btn:hover {
        background: #4682b4;
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(70,130,180,0.4);
      }
      
      .carousel-nav-btn:disabled {
        background: #d3d3d3; /* Gris más claro */
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
        color: #888;
      }
      
      .carousel-info {
        text-align: center;
        color: #6c757d;
        font-size: 0.9rem;
        flex-grow: 1;
        margin: 0 1rem;
      }
      
      /* Mejora para productos en vista carrusel */
      .productos-carousel .col-md-4 {
        flex: 0 0 33.333333%;
        max-width: 33.333333%;
      }
      
      .productos-grid .col-md-3 {
        flex: 0 0 25%;
        max-width: 25%;
      }

      #itemsPerPageSelect { width: auto; display: inline-block; margin-left: 0.5rem; }
    </style>
  </head>
  <body class="bg-light">


<div class="{{ $enlace ? 'container-fluid' : 'py-6' }}">
  <div class="{{ $enlace ? '' : 'max-w-7xl mx-auto sm:px-6 lg:px-8' }}">
    {{-- Header --}}
    <div class="bg-white shadow-sm rounded-lg overflow-hidden mb-4">
      <div class="p-4 row align-items-center">
        <div class="col-md-4">
          <h4 class="mb-0">
            @if($enlace)
              Catálogo de Productos
            @else
              <a href="{{ route('catalogo') }}" class="text-decoration-none">
                <i class="bi bi-arrow-left"></i> Cambiar Cliente
              </a>
            @endif
          </h4>
          {{-- Información del cliente movida aquí --}}
          <div class="mt-2">
            <p class="text-muted mb-0 small">
              Cliente: <strong>{{ $cliente->nombre_contacto }}</strong><br>
              Lista de Precios: <strong>{{ $cliente->listaPrecio?->nombre ?? 'Sin lista asignada' }}</strong>
              @if($enlace && !$enlace->mostrar_precios)
                <br><span class="text-warning"><i class="bi bi-eye-slash"></i> Los precios no están visibles</span>
              @endif
            </p>
          </div>
        </div>
        <div class="col-md-8">
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" id="busquedaProducto" placeholder="Buscar por nombre o referencia...">
          </div>
        </div>
      </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white shadow-sm rounded-lg overflow-hidden mb-4">
      <div class="p-4 row align-items-center">
        <div class="col-md-4">
          <label class="form-label">Filtrar por Categoría</label>
          <select class="form-select" id="filtroCategoria">
            <option value="">Todas las categorías</option>
            @foreach($categorias as $categoria)
              <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-8 text-end">
          <button class="btn btn-outline-secondary me-2" id="btnToggleView">
            <i class="bi bi-view-list"></i> Ver como carrusel
          </button>
          <select class="form-select form-select-sm d-none me-2" id="itemsPerPageSelect" style="width: auto; display: inline-block;">
            <option value="3" selected>Mostrar 3</option>
            <option value="6">Mostrar 6</option>
            <option value="9">Mostrar 9</option>
            <option value="12">Mostrar 12</option>
          </select>
          <button class="btn btn-primary position-relative" id="btnCarrito">
            <i class="bi bi-cart"></i> Carrito
            <span class="badge rounded-pill bg-danger cart-badge" id="cartCount" style="display:none;">0</span>
          </button>
        </div>
      </div>
    </div>

    {{-- Productos --}}
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
      <div class="p-4">
        <div id="productosContainer" class="row productos-grid">
          <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Cargando productos...</span>
            </div>
          </div>
        </div>
        <div id="paginacionContainer" class="mt-4"></div>
      </div>
    </div>
  </div>
</div>

{{-- Sidebar del Carrito --}}
<div id="cartSidebar">
  <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Carrito de Compras</h5>
    <button class="btn-close" id="closeCart"></button>
  </div>
  <div class="p-4" style="height:calc(100vh-200px); overflow-y:auto;">
    <div id="cartItems">
      <p class="text-muted text-center">El carrito está vacío</p>
    </div>
  </div>
  <div class="p-4 border-top">
    <div class="d-flex justify-content-between mb-3">
      <strong>Total:</strong>
      <strong id="cartTotal">$0.00</strong>
    </div>
    <button class="btn btn-success w-100" id="btnFinalizarSolicitud" disabled>
      <i class="bi bi-check-circle"></i> Finalizar Solicitud
    </button>
  </div>
</div>

{{-- Modal Producto --}}
<div class="modal fade" id="modalProducto" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalProductoTitle">Detalle del Producto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="modalProductoContent">
        <div class="text-center"><div class="spinner-border" role="status"></div></div>
      </div>
    </div>
  </div>
</div>

{{-- Modal Confirmar --}}
<div class="modal fade" id="modalConfirmarSolicitud" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirmar Solicitud de Cotización</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Notas adicionales (opcional)</label>
          <textarea class="form-control" id="notasSolicitud" rows="3"
                    placeholder="Ingrese cualquier comentario o requerimiento especial..."></textarea>
        </div>
        <div class="alert alert-info">
          <i class="bi bi-info-circle"></i> Al confirmar, se enviará la solicitud de cotización con los productos seleccionados.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnConfirmarSolicitud">
          <i class="bi bi-send"></i> Enviar Solicitud
        </button>
      </div>
    </div>
  </div>
</div>

{{-- Loading --}}
<div class="loading-overlay" id="loadingOverlay" style="display:none;">
  <div class="text-center">
    <div class="spinner-border text-primary mb-3" role="status"></div>
    <p>Procesando solicitud...</p>
  </div>
</div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


<script>
$(function(){
  const clienteId    = {{ $cliente->id }};
  const enlaceToken  = '{{ $enlace?->token }}';
  const mostrarPrecios = {{ ($enlace && !$enlace->mostrar_precios) ? 'false' : 'true' }};
  
  // Variables para el carrusel
  let viewType = 'grid';
  let itemsPerPage = 3;
  let carouselPage = 1;
  let totalCarouselPages = 1;
  let productosCarousel = [];
  
  let carrito = JSON.parse(localStorage.getItem('carrito_'+clienteId) || '[]')
                  .map(i=>({...i, precio: parseFloat(i.precio)||0}));
  let productosCargados = {};

  function actualizarCarrito(){
    let total=0, itemsHtml='';
    if(!carrito.length){
      itemsHtml = '<p class="text-muted text-center">El carrito está vacío</p>';
      $('#btnFinalizarSolicitud').prop('disabled',true);
    } else {
      carrito.forEach((item,i)=>{
        const precioUnit = parseFloat(item.precio)||0;
        const subtotal   = mostrarPrecios ? precioUnit*item.cantidad : 0;
        total += subtotal;
        itemsHtml += `
          <div class="card mb-2">
            <div class="card-body p-2">
              <div class="d-flex justify-content-between">
                <div>
                  <h6 class="mb-0">${item.nombre}</h6>
                  <small class="text-muted">Ref: ${item.referencia}</small>
                  ${item.variante?`<br><small class="text-info">${item.variante}</small>`:''}
                  ${(mostrarPrecios && !isNaN(precioUnit))
                    ?`<br><small>${precioUnit.toFixed(2)} c/u</small>`
                    :''}
                </div>
                <button class="btn btn-sm btn-outline-danger" onclick="eliminarDelCarrito(${i})">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
              <div class="mt-2 d-flex align-items-center">
                <button class="btn btn-sm btn-outline-secondary" onclick="cambiarCantidad(${i},-1)">-</button>
                <input type="number" class="form-control form-control-sm mx-2 text-center"
                       style="width:60px" value="${item.cantidad}"
                       onchange="actualizarCantidad(${i},this.value)">
                <button class="btn btn-sm btn-outline-secondary" onclick="cambiarCantidad(${i},1)">+</button>
                ${mostrarPrecios
                  ?`<span class="ms-auto">${subtotal.toFixed(2)}</span>`
                  :''}
              </div>
            </div>
          </div>`;
      });
      $('#btnFinalizarSolicitud').prop('disabled',false);
    }
    $('#cartItems').html(itemsHtml);
    $('#cartTotal').text(mostrarPrecios? total.toFixed(2):'N/A');
    $('#cartCount').text(carrito.reduce((s,i)=>s+i.cantidad,0))
                   .toggle(!!carrito.length);
    localStorage.setItem('carrito_'+clienteId, JSON.stringify(carrito));
  }

  window.eliminarDelCarrito = i=>{
    carrito.splice(i,1); actualizarCarrito();
  };
  window.cambiarCantidad = (i,delta)=>{
    const n = carrito[i].cantidad+delta;
    if(n>0){ carrito[i].cantidad=n; actualizarCarrito(); }
  };
  window.actualizarCantidad = (i,val)=>{
    val = parseInt(val)||0;
    if(val>0){ carrito[i].cantidad=val; actualizarCarrito(); }
  };

  function agregarAlCarrito(producto,cantidad,variante=null){
    const precioRaw   = variante? variante.precio_final: producto.precio;
    const precioUnit = parseFloat(precioRaw)||0;
    const idx = carrito.findIndex(it=>
      it.producto_id===producto.id &&
      it.variante_id === (variante?.id||null)
    );
    if(idx > -1){
      carrito[idx].cantidad += cantidad;
    } else {
      carrito.push({
        producto_id: producto.id,
        variante_id: variante?.id||null,
        referencia: producto.referencia,
        nombre: producto.nombre,
        variante: variante? `${variante.talla||''} ${variante.color||''}`.trim():null,
        precio: precioUnit,
        cantidad
      });
    }
    actualizarCarrito();
  }

  window.agregarProductoSimple = id=>{
    const cnt = parseInt($('#cantidadProducto').val())||0;
    if(cnt>0){
      const prod = window.productoActual || productosCargados[id];
      agregarAlCarrito(prod,cnt);
      $('#modalProducto').modal('hide');
      mostrarNotificacion('Producto agregado al carrito','success');
    }
  };

  // Funciones para toggle de vista
  function toggleView(){
    viewType = (viewType === 'grid' ? 'carousel' : 'grid');
    $('#itemsPerPageSelect').toggleClass('d-none', viewType !== 'carousel');
    $('#btnToggleView i').toggleClass('bi-view-list bi-grid-3x3-gap');
    $('#btnToggleView').contents().last()[0].textContent = viewType === 'carousel' ? ' Ver en cuadrícula' : ' Ver como carrusel';
    carouselPage = 1;
    cargarProductos(1);
  }

  $('#btnToggleView').click(toggleView);
  $('#itemsPerPageSelect').change(function(){
    itemsPerPage = parseInt($(this).val()) || 3;
    carouselPage = 1; // Reset to first page
    // Recalcular total de páginas inmediatamente
    totalCarouselPages = Math.ceil(productosCarousel.length / itemsPerPage) || 1;
    renderCarousel(); // Renderizar inmediatamente con nuevos parámetros
  });

  function cargarProductos(page=1){
    $.post('{{route("catalogo.productos")}}',{
      _token:'{{csrf_token()}}',
      page, busqueda:$('#busquedaProducto').val(),
      categoria_id:$('#filtroCategoria').val(),
      cliente_id:clienteId, enlace_token:enlaceToken
    },resp=>{
      const prods = resp.productos.data;
      productosCarousel = prods; // Guardar para carrusel
      
      if (viewType === 'grid') {
        renderGrid(prods, resp);
      } else {
        totalCarouselPages = Math.ceil(prods.length / itemsPerPage) || 1;
        renderCarousel();
      }
    });
  }

  function renderGrid(prods, resp) {
    $('#productosContainer').removeClass('productos-carousel').addClass('productos-grid');
    
    let html = !prods.length
      ?'<div class="col-12 text-center py-5"><p class="text-muted">No se encontraron productos</p></div>'
      : prods.map(p=>{
          productosCargados[p.id]=p;
          return buildCard(p, 'col-12 col-sm-4 col-md-3 col-lg-2');
        }).join('');

    $('#productosContainer').html(html);
    $('#paginacionContainer').html(buildPagination(resp));
  }

  function renderCarousel() {
  // 1) Elimina las navegaciones viejas
  $('.carousel-pagination').remove();

  // 2) Renderiza los productos en grid/carousel
  $('#productosContainer').removeClass('productos-grid').addClass('productos-carousel');
  
  // Recalcular páginas cuando cambia itemsPerPage
  totalCarouselPages = Math.ceil(productosCarousel.length / itemsPerPage) || 1;
  
  // Verificar que la página actual no exceda el total
  if (carouselPage > totalCarouselPages) {
    carouselPage = totalCarouselPages;
  }
  
  const start = (carouselPage - 1) * itemsPerPage;
  const pageItems = productosCarousel.slice(start, start + itemsPerPage);
  
  // Clases responsivas que funcionan bien
  let colClass = 'col-12 col-sm-4 col-md-4'; // Para 3 items: 1-3-3 por fila
  if (itemsPerPage === 6) colClass = 'col-12 col-sm-4 col-md-2'; // 6 items: 1-3-6 por fila
  else if (itemsPerPage === 9) colClass = 'col-12 col-sm-4 col-md-3 col-lg-2'; // 9 items: 1-3-4-6 por fila (mostrando 6 visualmente)
  else if (itemsPerPage === 12) colClass = 'col-12 col-sm-4 col-md-3 col-lg-2'; // 12 items: 1-3-4-6 por fila
  
  const html = pageItems.map(p => {
    productosCargados[p.id] = p;
    return buildCard(p, colClass);
  }).join('');
  
  $('#productosContainer').html(html);

  // 3) Inyecta la navegación **solo una vez** arriba y abajo
  $('#productosContainer').before(buildCarouselNavigation());
  $('#productosContainer').after(buildCarouselNavigation());
}

  function buildCard(p, colClass = 'col-12 col-sm-4 col-md-3 col-lg-2') {
    const img = p.imagen_principal
      ? `{{asset('')}}${p.imagen_principal.ruta_imagen}`
      : '{{asset("images/no-image.png")}}';
    const raw = p.precio, num = parseFloat(raw);
    const precioTag = (mostrarPrecios && raw!=null && !isNaN(num))
      ?`<br><strong>${num.toFixed(2)}</strong>`:'';
    return `
      <div class="${colClass} mb-4">
        <div class="card producto-card h-100" onclick="verProducto(${p.id})">
          <div class="producto-imagen-container">
            <img src="${img}" class="producto-imagen" alt="${p.nombre}">
          </div>
          <div class="card-body">
            <h6 class="card-title">${p.nombre}</h6>
            <p class="card-text">
              <small class="text-muted">Ref: ${p.referencia}</small><br>
              <small class="text-muted">${p.categoria.nombre}</small>
              ${precioTag}
            </p>
          </div>
        </div>
      </div>`;
  }

  function buildPagination(resp) {
    let pgHtml='';
    if(resp.productos.last_page>1){
      pgHtml+='<nav><ul class="pagination justify-content-center">';
      const cur = resp.productos.current_page, last= resp.productos.last_page;
      if(cur>1) pgHtml+=`<li class="page-item"><a class="page-link" href="#" onclick="cargarProductos(${cur-1});return false"><i class="bi bi-chevron-left"></i> Anterior</a></li>`;
      for(let i=1;i<=last;i++){
        if(i===cur) pgHtml+=`<li class="page-item active"><span class="page-link">${i}</span></li>`;
        else if(i===1||i===last||Math.abs(i-cur)<=2)
          pgHtml+=`<li class="page-item"><a class="page-link" href="#" onclick="cargarProductos(${i});return false">${i}</a></li>`;
        else if(Math.abs(i-cur)===3) pgHtml+='<li class="page-item disabled"><span class="page-link">...</span></li>';
      }
      if(cur<last) pgHtml+=`<li class="page-item"><a class="page-link" href="#" onclick="cargarProductos(${cur+1});return false">Siguiente <i class="bi bi-chevron-right"></i></a></li>`;
      pgHtml+='</ul></nav>';
    }
    return pgHtml;
  }

  function buildCarouselNavigation() {
    return `
      <div class="carousel-pagination">
        <button class="carousel-nav-btn" id="prevCarouselBtn" ${carouselPage <= 1 ? 'disabled' : ''}>
          <i class="bi bi-chevron-left"></i>
        </button>
        <div class="carousel-info">
          Página ${carouselPage} de ${totalCarouselPages}<br>
          (${productosCarousel.length} productos total)
        </div>
        <button class="carousel-nav-btn" id="nextCarouselBtn" ${carouselPage >= totalCarouselPages ? 'disabled' : ''}>
          <i class="bi bi-chevron-right"></i>
        </button>
      </div>`;
  }

  function renderProductos() {
    if (viewType === 'grid') {
      cargarProductos(1);
    } else {
      renderCarousel();
    }
  }

  // Event delegation para botones del carrusel
  $(document).on('click', '#prevCarouselBtn', function() {
    if (carouselPage > 1) {
      carouselPage--;
      renderCarousel();
    }
  });

  $(document).on('click', '#nextCarouselBtn', function() {
    if (carouselPage < totalCarouselPages) {
      carouselPage++;
      renderCarousel();
    }
  });

  window.verProducto = id=>{
    $('#modalProducto').modal('show');
    $('#modalProductoContent').html('<div class="text-center"><div class="spinner-border"></div></div>');
    $.get(`{{route("catalogo.producto.detalle","")}}/${id}`,{cliente_id:clienteId,enlace_token:enlaceToken},resp=>{
      const p=resp.producto;
      window.productoActual=p;
      let html='<div class="row">';
      // Imágenes...
      html+='<div class="col-md-6">';
      if(p.imagenes?.length){
        html+='<div id="carouselProducto" class="carousel slide" data-bs-ride="carousel"><div class="carousel-inner">';
        p.imagenes.forEach((img,i)=>{
          html+=`<div class="carousel-item ${i===0?"active":""}"><img src="{{asset("")}}${img.ruta_imagen}" class="d-block w-100" style="height:400px;object-fit:contain;background-color:#f8f9fa;"></div>`;
        });
        html+='</div>';
        if(p.imagenes.length>1){
          html+=`<button class="carousel-control-prev" type="button" data-bs-target="#carouselProducto" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>`;
          html+=`<button class="carousel-control-next" type="button" data-bs-target="#carouselProducto" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>`;
        }
        html+='</div>';
      } else {
        html+='<img src="{{asset("images/no-image.png")}}" class="img-fluid" style="object-fit:contain;background-color:#f8f9fa;">';
      }
      html+='</div>';

      // Info...
      html+='<div class="col-md-6">';
      html+=`<h4>${p.nombre}</h4><p class="text-muted">Referencia: ${p.referencia}</p><p>${p.descripcion||""}</p>`;
      if(mostrarPrecios){
        const raw=p.precio, num=parseFloat(raw);
        if(raw!=null&&!isNaN(num)) html+=`<h5 class="text-primary">${num.toFixed(2)}</h5>`;
      }
      // variantes o cantidad
      if(p.tiene_variantes&&p.variantes?.length){
        html+='<hr><h6>Seleccione las variantes:</h6><div class="table-responsive"><table class="table table-sm"><thead><tr><th>Variante</th><th>SKU</th>';
        if(mostrarPrecios) html+='<th>Precio</th>';
        html+='<th>Cantidad</th></tr></thead><tbody>';
        p.variantes.forEach((v,i)=>{
          html+='<tr>';
          html+=`<td>${v.nombre_variante||"Estándar"}</td><td><small>${v.sku}</small></td>`;
          if(mostrarPrecios) html+=`<td>${(v.precio_final||0).toFixed(2)}</td>`;
          html+=`<td><input type="number" class="form-control form-control-sm variante-cantidad" data-variante-index="${i}" min="0" value="0"></td>`;
          html+='</tr>';
        });
        html+='</tbody></table></div>';
        html+=`<button class="btn btn-primary w-100" onclick="agregarVariantesAlCarrito(${p.id})">Agregar al Carrito</button>`;
      } else {
        html+='<hr><div class="mb-3"><label class="form-label">Cantidad:</label>';
        html+='<input type="number" class="form-control" id="cantidadProducto" min="1" value="1"></div>';
        html+=`<button class="btn btn-primary w-100" onclick="agregarProductoSimple(${p.id})">Agregar al Carrito</button>`;
      }
      html+='</div></div>';
      $('#modalProductoContent').html(html);
    });
  };

  window.agregarVariantesAlCarrito = id=>{
    const prod = window.productoActual || productosCargados[id];
    let ok=false;
    $('.variante-cantidad').each(function(){
      const cnt=parseInt($(this).val())||0;
      if(cnt>0){
        const idx=$(this).data('variante-index'), v=prod.variantes[idx];
        agregarAlCarrito(prod,cnt,v);
        ok=true;
      }
    });
    if(ok){
      $('#modalProducto').modal('hide');
      mostrarNotificacion('Variantes agregadas al carrito','success');
    } else {
      mostrarNotificacion('Seleccione al menos una cantidad','warning');
    }
  };

  $('#btnFinalizarSolicitud').click(()=>{
    if(!carrito.length) return;
    $('#modalConfirmarSolicitud').modal('show');
  });
  $('#btnConfirmarSolicitud').click(()=>{
    const notas = $('#notasSolicitud').val();
    $('#loadingOverlay').show();
    const items = carrito.map(i=>({producto_id:i.producto_id,variante_id:i.variante_id,cantidad:i.cantidad}));
    $.post('{{route("catalogo.solicitud.guardar")}}',{
      _token:'{{csrf_token()}}',cliente_id:clienteId,
      enlace_token:enlaceToken,items,notas_cliente:notas
    },r=>{
      $('#loadingOverlay').hide();
      $('#modalConfirmarSolicitud').modal('hide');
      if(r.success){
        carrito=[]; actualizarCarrito();
        const msg=`<div class="alert alert-success alert-dismissible fade show">
          <h5>¡Solicitud Enviada!</h5><p>Su solicitud ha sido registrada.</p><hr>
          <p>Número de solicitud: <strong>${r.numero_solicitud}</strong></p>
          <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>`;
        @if($enlace)
          $('#productosContainer').before(msg);
        @else
          $('.max-w-7xl').prepend(msg);
        @endif
        window.scrollTo(0,0);
      }
    }).fail(xhr=>{
      $('#loadingOverlay').hide();
      mostrarNotificacion(xhr.responseJSON?.mensaje||'Error al procesar','danger');
    });
  });

  $('#btnCarrito').click(()=>$('#cartSidebar').addClass('show'));
  $('#closeCart').click(()=>$('#cartSidebar').removeClass('show'));
  $('#busquedaProducto').on('keyup',debounce(()=>cargarProductos(1),500));
  $('#filtroCategoria').change(()=>cargarProductos(1));

  function mostrarNotificacion(msg,t='info'){
    const $t = $(`<div class="toast" role="alert" style="position:fixed;top:20px;right:20px;z-index:1060">
      <div class="toast-body bg-${t} text-white">${msg}</div>
    </div>`);
    $('body').append($t);
    new bootstrap.Toast($t[0]).show();
    setTimeout(()=>$t.remove(),3000);
  }
  function debounce(fn,ms){
    let t;
    return function(...a){
      clearTimeout(t);
      t=setTimeout(()=>fn.apply(this,a),ms);
    };
  }

  actualizarCarrito();
  cargarProductos();
});
</script>

  </body>
  </html>
