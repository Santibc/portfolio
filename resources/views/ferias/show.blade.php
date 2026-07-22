<x-app-layout>
  <x-slot name="header">Feria: {{ $feria->nombre }}</x-slot>

  <div class="py-6">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
      @endif
      @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
      @endif

      <div class="bg-white shadow-sm rounded-lg overflow-hidden mb-4">
        <div class="p-6">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
              <h4 class="text-2xl font-semibold mb-1">{{ $feria->nombre }} {!! $feria->estadoBadge() !!}</h4>
              <div class="text-muted small">
                {{ $feria->fecha_inicio?->format('d/m/Y') ?? 'Sin fecha' }}@if($feria->fecha_fin) → {{ $feria->fecha_fin->format('d/m/Y') }}@endif
              </div>
            </div>
            <div class="d-flex gap-2">
              <a href="{{ route('ferias.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Volver</a>
              @if(!$feria->estaActiva() && !$feria->estaCerrada())
                <form action="{{ route('ferias.activar', $feria->id) }}" method="POST" onsubmit="return confirm('¿Activar la feria? Ya se podrá vender con sus precios.')">
                  @csrf
                  <button id="btnActivarFeria" class="btn btn-success btn-sm" {{ ($tieneInventario ?? true) ? '' : 'disabled' }} title="{{ ($tieneInventario ?? true) ? 'Activar la feria' : 'Carga al menos un producto al stand para poder activar' }}"><i class="bi bi-play-fill"></i> Activar feria</button>
                </form>
              @endif
              @if(!$feria->estaCerrada())
                <form action="{{ route('ferias.cerrar', $feria->id) }}" method="POST" onsubmit="return confirm('¿Cerrar la feria?')">
                  @csrf
                  <button class="btn btn-outline-danger btn-sm"><i class="bi bi-stop-fill"></i> Cerrar</button>
                </form>
              @endif
            </div>
          </div>

          <div class="row g-3">
            <div class="col-md-3"><div class="text-muted small">Ubicación</div><div>{{ $feria->ubicacion?->nombre ?? '—' }}</div></div>
            <div class="col-md-3"><div class="text-muted small">Lista de precios (feria)</div><div>{{ $feria->listaPrecio?->nombre ?? '—' }}</div></div>
            <div class="col-md-3"><div class="text-muted small">Copiada de</div><div>{{ $feria->listaPrecioBase?->nombre ?? '—' }}</div></div>
            <div class="col-md-3"><div class="text-muted small">Caja POS</div><div>{{ $feria->caja?->nombre ?? '—' }}</div></div>
          </div>

          <div class="alert alert-info mt-3 mb-0 small">
            <i class="bi bi-info-circle"></i>
            <strong>Preparar inventario:</strong> lleva mercancía a «{{ $feria->ubicacion?->nombre }}» desde
            <a href="{{ route('traslados') }}">Traslados</a>. Si reusas una ubicación, puedes reiniciar su stock desde
            <a href="/ubicaciones">Ubicaciones</a>. La caja de la feria ya cobra con la lista de precios de la feria.
          </div>
        </div>
      </div>

      {{-- F3: Preparar inventario del stand --}}
      <div class="bg-white shadow-sm rounded-lg overflow-hidden mb-4">
        <div class="p-6">
          <h5 class="font-semibold mb-1">Preparar inventario del stand</h5>
          <p class="text-muted small">Carga mercancía desde la <strong>Bodega Principal (CEDI)</strong> al stand de la feria. Queda lista al instante (sale del CEDI y entra al stand en un paso).</p>

          {{-- Traslados en tránsito hacia la feria, pendientes de recibir --}}
          <div id="bloqueTrasladosPendientes" style="display:none;" class="mb-4">
            <div class="alert alert-warning py-2 mb-2"><i class="bi bi-truck"></i> <strong>Traslados en camino hacia el stand</strong> (recíbelos para que el stock entre a la feria):</div>
            <div class="table-responsive">
              <table class="table table-sm align-middle mb-0">
                <thead class="table-light">
                  <tr><th>Traslado</th><th>Origen</th><th>Productos</th><th style="width:120px;"></th></tr>
                </thead>
                <tbody id="cuerpoTrasladosPendientes"></tbody>
              </table>
            </div>
            <hr class="my-3">
          </div>

          {{-- Buscar y agregar productos a cargar --}}
          <div class="position-relative mb-2" style="max-width: 560px;">
            <input type="text" id="buscarBodega" class="form-control" placeholder="Buscar producto en la Bodega Principal..." autocomplete="off">
            <div id="resultadosBodega" class="position-absolute bg-white border rounded shadow-sm w-100" style="z-index:1050; max-height:300px; overflow-y:auto; display:none;"></div>
          </div>

          <div class="table-responsive">
            <table class="table table-sm align-middle">
              <thead class="table-light">
                <tr>
                  <th>Producto a cargar</th>
                  <th style="width:130px;">Disp. en CEDI</th>
                  <th style="width:150px;">Cantidad</th>
                  <th style="width:60px;"></th>
                </tr>
              </thead>
              <tbody id="cuerpoCargar">
                <tr id="filaVaciaCargar"><td colspan="4" class="text-center text-muted py-3">Busca productos de la bodega para cargarlos al stand.</td></tr>
              </tbody>
            </table>
          </div>
          <div class="d-flex justify-content-end">
            <button type="button" id="btnCargarStand" class="btn btn-primary" disabled><i class="bi bi-box-arrow-in-down"></i> Cargar al stand</button>
          </div>

          <hr class="my-4">

          {{-- Inventario actual en el stand --}}
          <h6 class="font-semibold mb-2">Inventario actual en el stand</h6>
          <div class="table-responsive">
            <table class="table table-sm align-middle">
              <thead class="table-light">
                <tr>
                  <th>Producto</th>
                  <th style="width:120px;">En el stand</th>
                  <th style="width:120px;">Reservado</th>
                  <th style="width:120px;"></th>
                </tr>
              </thead>
              <tbody id="cuerpoInventario">
                <tr><td colspan="4" class="text-center text-muted py-3">Cargando…</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- Precios de la feria (unificado) --}}
      <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6">
          <h5 class="font-semibold mb-1">Precios de la feria</h5>
          <p class="text-muted small">Busca y selecciona productos, tonos o <strong>«todos los tonos»</strong> y ajústales el precio <strong>solo para esta feria</strong>: <strong>precio fijo</strong>, <strong>% de descuento</strong> o <strong>% de aumento</strong>. No afecta las listas regulares.</p>

          {{-- Buscar y agregar a la selección --}}
          <div class="position-relative mb-2" style="max-width: 560px;">
            <input type="text" id="buscarMasivo" class="form-control" placeholder="Buscar producto o tono (ej: Esmalte semipermanente)..." autocomplete="off">
            <div id="resultadosMasivo" class="position-absolute bg-white border rounded shadow-sm w-100" style="z-index:1050; max-height:300px; overflow-y:auto; display:none;"></div>
          </div>
          <div class="d-flex gap-2 mb-3 flex-wrap">
            <button type="button" id="btnAgregarTodos" class="btn btn-sm btn-outline-primary" disabled><i class="bi bi-plus-square"></i> Agregar todos los resultados</button>
            <button type="button" id="btnLimpiarSel" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-circle"></i> Limpiar selección</button>
            <span class="align-self-center small text-muted" id="contadorSel">0 seleccionados</span>
          </div>

          {{-- Operación --}}
          <div class="row g-2 align-items-end mb-3">
            <div class="col-auto">
              <label class="form-label small fw-semibold mb-1">Tipo</label>
              <select id="tipoMasivo" class="form-select form-select-sm">
                <option value="fijo">Precio fijo ($)</option>
                <option value="descuento_pct">Descuento (%)</option>
                <option value="aumento_pct">Aumento (%)</option>
              </select>
            </div>
            <div class="col-auto">
              <label class="form-label small fw-semibold mb-1" id="lblValor">Valor ($)</label>
              <input type="number" id="valorMasivo" class="form-control form-control-sm" min="0" step="1" style="width:140px;">
            </div>
            <div class="col-auto">
              <button type="button" id="btnPrevia" class="btn btn-sm btn-outline-dark"><i class="bi bi-eye"></i> Ver previa</button>
            </div>
            <div class="col-auto">
              <button type="button" id="btnAplicarMasivo" class="btn btn-primary btn-sm" disabled><i class="bi bi-tags"></i> Aplicar promoción</button>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-sm align-middle">
              <thead class="table-light">
                <tr>
                  <th>Producto / tono</th>
                  <th style="width:140px;">Precio actual</th>
                  <th style="width:140px;">Precio nuevo</th>
                  <th style="width:50px;"></th>
                </tr>
              </thead>
              <tbody id="cuerpoMasivo">
                <tr id="filaVaciaMasivo"><td colspan="4" class="text-center text-muted py-3">Busca y agrega productos/tonos para aplicarles una promoción.</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
  {{-- F3: preparar inventario --}}
  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const CSRF = '{{ csrf_token() }}';
    const urlBuscarBodega = '{{ route('ferias.buscar-bodega', $feria->id) }}';
    const urlInventario   = '{{ route('ferias.inventario', $feria->id) }}';
    const urlCargar       = '{{ route('ferias.inventario.cargar', $feria->id) }}';
    const urlDevolver     = '{{ route('ferias.inventario.devolver', $feria->id) }}';
    const urlTraslados    = '{{ route('ferias.traslados-pendientes', $feria->id) }}';
    const urlRecibirBase  = '{{ url('ferias/'.$feria->id.'/traslados') }}';

    const input = document.getElementById('buscarBodega');
    const resultados = document.getElementById('resultadosBodega');
    const cuerpoCargar = document.getElementById('cuerpoCargar');
    const cuerpoInv = document.getElementById('cuerpoInventario');
    const btnCargar = document.getElementById('btnCargarStand');
    let timeout = null;

    const esc = s => String(s ?? '').replace(/"/g,'&quot;').replace(/</g,'&lt;');
    const num = n => Number(n).toLocaleString('es-CO');

    function toggleBtnCargar() {
      btnCargar.disabled = cuerpoCargar.querySelectorAll('tr.fila-cargar').length === 0;
    }

    // ---- buscar en bodega ----
    input.addEventListener('input', function() {
      clearTimeout(timeout);
      const q = this.value.trim();
      if (q.length < 2) { resultados.style.display='none'; return; }
      timeout = setTimeout(() => buscar(q), 300);
    });
    document.addEventListener('click', e => { if (!input.contains(e.target) && !resultados.contains(e.target)) resultados.style.display='none'; });

    function buscar(q) {
      fetch(`${urlBuscarBodega}?q=${encodeURIComponent(q)}`, {headers:{'Accept':'application/json'}})
        .then(r => r.json())
        .then(p => {
          const items = p.data || [];
          resultados.innerHTML = items.length ? items.map(it => `
            <div class="p-2 border-bottom res-b" style="cursor:pointer"
                 data-pid="${it.producto_id}" data-vid="${it.variante_producto_id ?? ''}"
                 data-nombre="${esc(it.nombre)}" data-disp="${it.disponible_bodega}">
              <div class="fw-semibold">${esc(it.nombre)}</div>
              <small class="text-muted">Ref: ${esc(it.referencia) || '—'} · Disp. CEDI: <strong>${num(it.disponible_bodega)}</strong></small>
            </div>`).join('') : '<div class="p-3 text-muted text-center">Sin resultados</div>';
          resultados.style.display='block';
          resultados.querySelectorAll('.res-b').forEach(el => el.addEventListener('click', () => {
            agregarCargar(el.dataset.pid, el.dataset.vid || null, el.dataset.nombre, parseInt(el.dataset.disp||'0',10));
            input.value=''; resultados.style.display='none';
          }));
        });
    }

    function agregarCargar(pid, vid, nombre, disp) {
      const key = pid + '-' + (vid || '');
      if (cuerpoCargar.querySelector(`tr[data-key="${key}"]`)) return;
      const vacia = document.getElementById('filaVaciaCargar'); if (vacia) vacia.remove();
      const tr = document.createElement('tr');
      tr.className = 'fila-cargar'; tr.dataset.key = key;
      tr.dataset.pid = pid; tr.dataset.vid = vid || '';
      tr.innerHTML = `
        <td>${esc(nombre)}</td>
        <td>${num(disp)}</td>
        <td><input type="number" class="form-control form-control-sm cant-cargar" min="1" max="${disp}" step="1" value="1"></td>
        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-quitar"><i class="bi bi-x-lg"></i></button></td>`;
      cuerpoCargar.appendChild(tr);
      tr.querySelector('.btn-quitar').addEventListener('click', () => { tr.remove(); if(!cuerpoCargar.querySelector('tr.fila-cargar')){ cuerpoCargar.innerHTML = '<tr id="filaVaciaCargar"><td colspan="4" class="text-center text-muted py-3">Busca productos de la bodega para cargarlos al stand.</td></tr>'; } toggleBtnCargar(); });
      toggleBtnCargar();
    }

    // ---- cargar al stand ----
    btnCargar.addEventListener('click', () => {
      const filas = [...cuerpoCargar.querySelectorAll('tr.fila-cargar')];
      const items = [];
      for (const tr of filas) {
        const cant = parseInt(tr.querySelector('.cant-cargar').value || '0', 10);
        if (!cant || cant < 1) { Swal.fire('Cantidad inválida','Revisa las cantidades.','warning'); return; }
        items.push({ producto_id: parseInt(tr.dataset.pid,10), variante_producto_id: tr.dataset.vid || null, cantidad: cant });
      }
      btnCargar.disabled = true;
      fetch(urlCargar, { method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json','Accept':'application/json'}, body: JSON.stringify({items}) })
        .then(async r => { const d = await r.json(); if(!r.ok) throw new Error(d.error || d.mensaje || 'Error'); return d; })
        .then(d => {
          Swal.fire('Cargado', d.mensaje, 'success');
          cuerpoCargar.innerHTML = '<tr id="filaVaciaCargar"><td colspan="4" class="text-center text-muted py-3">Busca productos de la bodega para cargarlos al stand.</td></tr>';
          toggleBtnCargar();
          cargarInventario();
        })
        .catch(e => { btnCargar.disabled=false; Swal.fire('Error', e.message, 'error'); });
    });

    // ---- inventario actual ----
    function cargarInventario() {
      fetch(urlInventario, {headers:{'Accept':'application/json'}})
        .then(r => r.json())
        .then(p => {
          const rows = p.data || [];
          // Habilitar "Activar feria" solo si hay algo cargado para vender.
          const hayStock = rows.some(r => Number(r.disponible) > 0);
          const btnAct = document.getElementById('btnActivarFeria');
          if (btnAct) {
            btnAct.disabled = !hayStock;
            btnAct.title = hayStock ? 'Activar la feria' : 'Carga al menos un producto al stand para poder activar';
          }
          if (!rows.length) { cuerpoInv.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">El stand aún no tiene inventario.</td></tr>'; return; }
          cuerpoInv.innerHTML = rows.map(r => `
            <tr>
              <td>${esc(r.nombre)}</td>
              <td><strong>${num(r.disponible)}</strong></td>
              <td>${num(r.reservado)}</td>
              <td><button type="button" class="btn btn-sm btn-outline-secondary btn-devolver"
                    data-pid="${r.producto_id}" data-vid="${r.variante_producto_id ?? ''}"
                    data-nombre="${esc(r.nombre)}" data-disp="${r.disponible}"><i class="bi bi-arrow-return-left"></i> Devolver</button></td>
            </tr>`).join('');
          cuerpoInv.querySelectorAll('.btn-devolver').forEach(b => b.addEventListener('click', () => devolver(b.dataset)));
        });
    }

    function devolver(d) {
      Swal.fire({
        title: 'Devolver a la Bodega Principal',
        html: `Producto: <strong>${esc(d.nombre)}</strong><br>En el stand: ${num(d.disp)}`,
        input: 'number', inputLabel: 'Cantidad a devolver', inputValue: d.disp,
        inputAttributes: { min: 1, max: d.disp, step: 1 },
        showCancelButton: true, confirmButtonText: 'Devolver'
      }).then(res => {
        if (!res.isConfirmed) return;
        const cant = parseInt(res.value || '0', 10);
        if (!cant || cant < 1) { Swal.fire('Cantidad inválida','',''); return; }
        fetch(urlDevolver, { method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json','Accept':'application/json'},
          body: JSON.stringify({ producto_id: d.pid, variante_producto_id: d.vid || null, cantidad: cant }) })
          .then(async r => { const j = await r.json(); if(!r.ok) throw new Error(j.error || 'Error'); return j; })
          .then(j => { Swal.fire('Devuelto', j.mensaje, 'success'); cargarInventario(); })
          .catch(e => Swal.fire('Error', e.message, 'error'));
      });
    }

    // ---- traslados en tránsito pendientes de recibir ----
    const bloqueTr = document.getElementById('bloqueTrasladosPendientes');
    const cuerpoTr = document.getElementById('cuerpoTrasladosPendientes');

    function cargarTrasladosPendientes() {
      fetch(urlTraslados, {headers:{'Accept':'application/json'}})
        .then(r => r.json())
        .then(p => {
          const rows = p.data || [];
          if (!rows.length) { bloqueTr.style.display = 'none'; cuerpoTr.innerHTML=''; return; }
          bloqueTr.style.display = 'block';
          cuerpoTr.innerHTML = rows.map(t => {
            const prods = t.items.map(i => `${esc(i.nombre)} <span class="badge bg-secondary">x${i.cantidad}</span>`).join('<br>');
            return `<tr>
              <td><strong>${esc(t.numero)}</strong><div class="small text-muted">${esc(t.enviado_en) || ''}</div></td>
              <td>${esc(t.origen)}</td>
              <td class="small">${prods}</td>
              <td><button type="button" class="btn btn-sm btn-success btn-recibir" data-id="${t.id}"><i class="bi bi-box-arrow-in-down"></i> Recibir</button></td>
            </tr>`;
          }).join('');
          cuerpoTr.querySelectorAll('.btn-recibir').forEach(b => b.addEventListener('click', () => recibirTraslado(b.dataset.id, b)));
        });
    }

    function recibirTraslado(trasladoId, btn) {
      btn.disabled = true;
      fetch(`${urlRecibirBase}/${trasladoId}/recibir`, {
        method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json','Accept':'application/json'}, body:'{}'
      })
      .then(async r => { const d = await r.json(); if(!r.ok) throw new Error(d.error || 'Error'); return d; })
      .then(d => { Swal.fire('Recibido', d.mensaje, 'success'); cargarTrasladosPendientes(); cargarInventario(); })
      .catch(e => { btn.disabled=false; Swal.fire('Error', e.message, 'error'); });
    }

    cargarInventario();
    cargarTrasladosPendientes();
  });
  </script>

  {{-- F2: precios masivos / promociones --}}
  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const CSRF = '{{ csrf_token() }}';
    const urlBuscar = '{{ route('ferias.buscar-productos', $feria->id) }}';
    const urlMasivo = '{{ route('ferias.precios-masivos', $feria->id) }}';

    const input = document.getElementById('buscarMasivo');
    const resultados = document.getElementById('resultadosMasivo');
    const cuerpo = document.getElementById('cuerpoMasivo');
    const tipoSel = document.getElementById('tipoMasivo');
    const valorInp = document.getElementById('valorMasivo');
    const lblValor = document.getElementById('lblValor');
    const btnTodos = document.getElementById('btnAgregarTodos');
    const btnAplicar = document.getElementById('btnAplicarMasivo');
    const contador = document.getElementById('contadorSel');
    let timeout = null, ultimosResultados = [];

    const esc = s => String(s ?? '').replace(/"/g,'&quot;').replace(/</g,'&lt;');
    const fmt = n => (n === null || n === undefined || n === '') ? '—' : '$' + Number(n).toLocaleString('es-CO');

    function filas() { return [...cuerpo.querySelectorAll('tr.fila-masivo')]; }
    function refrescarEstado() {
      const n = filas().length;
      contador.textContent = n + ' seleccionado' + (n === 1 ? '' : 's');
      btnAplicar.disabled = n === 0;
      const vacia = document.getElementById('filaVaciaMasivo');
      if (n > 0 && vacia) vacia.remove();
      if (n === 0 && !vacia) cuerpo.innerHTML = '<tr id="filaVaciaMasivo"><td colspan="4" class="text-center text-muted py-3">Busca y agrega productos/tonos para aplicarles una promoción.</td></tr>';
    }

    tipoSel.addEventListener('change', () => {
      lblValor.textContent = tipoSel.value === 'fijo' ? 'Valor ($)' : 'Valor (%)';
      valorInp.value = '';
    });

    // ---- búsqueda ----
    input.addEventListener('input', function() {
      clearTimeout(timeout);
      const q = this.value.trim();
      if (q.length < 2) { resultados.style.display='none'; btnTodos.disabled=true; return; }
      timeout = setTimeout(() => buscar(q), 300);
    });
    document.addEventListener('click', e => { if (!input.contains(e.target) && !resultados.contains(e.target)) resultados.style.display='none'; });

    function buscar(q) {
      fetch(`${urlBuscar}?q=${encodeURIComponent(q)}`, {headers:{'Accept':'application/json'}})
        .then(r => r.json())
        .then(p => {
          ultimosResultados = p.data || [];
          btnTodos.disabled = ultimosResultados.length === 0;
          resultados.innerHTML = ultimosResultados.length ? ultimosResultados.map((it,i) => `
            <div class="p-2 border-bottom res-m" style="cursor:pointer" data-i="${i}">
              <div class="fw-semibold">${esc(it.nombre)} ${it.todas_variantes ? '<span class="badge bg-info text-dark">todos los tonos</span>' : ''}</div>
              <small class="text-muted">Ref: ${esc(it.referencia) || '—'} · Actual: ${fmt(it.precio)}</small>
            </div>`).join('') : '<div class="p-3 text-muted text-center">Sin resultados</div>';
          resultados.style.display='block';
          resultados.querySelectorAll('.res-m').forEach(el => el.addEventListener('click', () => {
            agregar(ultimosResultados[el.dataset.i]); resultados.style.display='none'; input.value='';
          }));
        });
    }

    btnTodos.addEventListener('click', () => { ultimosResultados.forEach(agregar); resultados.style.display='none'; input.value=''; });
    document.getElementById('btnLimpiarSel').addEventListener('click', () => { cuerpo.innerHTML=''; refrescarEstado(); });

    function agregar(it) {
      if (!it) return;
      const todas = !!it.todas_variantes;
      const key = it.producto_id + '-' + (it.variante_producto_id || '') + (todas ? '-all' : '');
      if (cuerpo.querySelector(`tr[data-key="${key}"]`)) return;
      const tr = document.createElement('tr');
      tr.className = 'fila-masivo'; tr.dataset.key = key;
      tr.dataset.pid = it.producto_id; tr.dataset.vid = it.variante_producto_id || '';
      tr.dataset.todas = todas ? '1' : '0';
      tr.dataset.actual = (it.precio ?? 0);
      tr.innerHTML = `
        <td>${esc(it.nombre)} ${todas ? '<span class="badge bg-info text-dark">todos los tonos</span>' : ''}</td>
        <td class="p-actual">${todas ? '<span class="text-muted">varios</span>' : fmt(it.precio)}</td>
        <td class="p-nuevo text-muted">—</td>
        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-quitar-m"><i class="bi bi-x-lg"></i></button></td>`;
      cuerpo.appendChild(tr);
      tr.querySelector('.btn-quitar-m').addEventListener('click', () => { tr.remove(); refrescarEstado(); });
      refrescarEstado();
    }

    // ---- previa ----
    function calcularNuevo(actual) {
      const v = parseFloat(valorInp.value || '');
      if (isNaN(v)) return null;
      if (tipoSel.value === 'fijo') return Math.max(0, Math.round(v));
      if (tipoSel.value === 'descuento_pct') return Math.max(0, Math.round(actual * (1 - v/100)));
      return Math.max(0, Math.round(actual * (1 + v/100)));
    }
    document.getElementById('btnPrevia').addEventListener('click', () => {
      const v = parseFloat(valorInp.value || '');
      if (isNaN(v) || v < 0) { Swal.fire('Valor requerido','Ingresa el valor de la promoción.','warning'); return; }
      filas().forEach(tr => {
        const cel = tr.querySelector('.p-nuevo');
        cel.classList.remove('text-muted'); cel.classList.add('fw-semibold','text-success');
        if (tr.dataset.todas === '1') {
          // Cada tono se recalcula sobre su propio precio.
          if (tipoSel.value === 'fijo') cel.textContent = fmt(Math.max(0, Math.round(v)));
          else cel.textContent = (tipoSel.value === 'descuento_pct' ? '−' : '+') + v + '% a cada tono';
        } else {
          cel.textContent = fmt(calcularNuevo(parseFloat(tr.dataset.actual || '0')));
        }
      });
    });

    // ---- aplicar ----
    btnAplicar.addEventListener('click', () => {
      const v = parseFloat(valorInp.value || '');
      if (isNaN(v) || v < 0) { Swal.fire('Valor requerido','Ingresa el valor de la promoción.','warning'); return; }
      if (tipoSel.value === 'descuento_pct' && v > 100) { Swal.fire('Descuento inválido','El descuento no puede superar 100%.','warning'); return; }
      const items = filas().map(tr => ({
        producto_id: parseInt(tr.dataset.pid,10),
        variante_producto_id: (tr.dataset.todas === '1' ? null : (tr.dataset.vid || null)),
        todas_variantes: tr.dataset.todas === '1' ? 1 : 0,
      }));
      if (!items.length) return;

      Swal.fire({
        title: '¿Aplicar la promoción?',
        text: `Se actualizará el precio de ${items.length} producto(s) en la feria.`,
        icon: 'question', showCancelButton: true, confirmButtonText: 'Aplicar'
      }).then(res => {
        if (!res.isConfirmed) return;
        btnAplicar.disabled = true;
        fetch(urlMasivo, { method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json','Accept':'application/json'},
          body: JSON.stringify({ tipo: tipoSel.value, valor: v, items }) })
          .then(async r => { const d = await r.json(); if(!r.ok) throw new Error(d.error || d.mensaje || 'Error'); return d; })
          .then(d => {
            // Filas por variante/producto individual: actualizar con el precio aplicado.
            (d.aplicados || []).forEach(a => {
              const key = a.producto_id + '-' + (a.variante_producto_id || '');
              const tr = cuerpo.querySelector(`tr[data-key="${key}"]`);
              if (tr) { tr.dataset.actual = a.precio; tr.querySelector('.p-actual').textContent = fmt(a.precio); }
            });
            // Filas "todos los tonos": si fue precio fijo, todos quedaron igual; si fue %, varían.
            filas().filter(tr => tr.dataset.todas === '1').forEach(tr => {
              tr.querySelector('.p-actual').innerHTML = (tipoSel.value === 'fijo')
                ? fmt(Math.max(0, Math.round(v)))
                : '<span class="text-muted">varios</span>';
            });
            filas().forEach(tr => { const c = tr.querySelector('.p-nuevo'); c.textContent = '—'; c.className = 'p-nuevo text-muted'; });
            btnAplicar.disabled = false;
            Swal.fire('Listo', d.mensaje, 'success');
          })
          .catch(e => { btnAplicar.disabled = false; Swal.fire('Error', e.message, 'error'); });
      });
    });
  });
  </script>
  @endpush
</x-app-layout>
