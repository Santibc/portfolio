<x-app-layout>
    <x-slot name="header">Inicio</x-slot>

    @php
        $meses = [1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'];
        $fmt = fn($v) => '$ ' . number_format((float) $v, 0, ',', '.');
    @endphp

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- ========================= DASHBOARD VENDEDOR ========================= --}}
        @if ($rol === 'vendedor')
          <div class="mb-3">
            <h4 class="text-2xl font-semibold">Hola, {{ auth()->user()->name }}</h4>
            <p class="text-muted mb-0">Resumen de {{ $meses[$mes] }} {{ $anio }}</p>
          </div>

          {{-- Card compacta de la sede --}}
          <div class="card border-0 shadow-sm mb-3">
            <div class="card-body py-3">
              <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary flex-shrink-0"
                     style="width:56px; height:56px; font-size:1.5rem;">
                  <i class="bi bi-building"></i>
                </div>
                <div class="flex-grow-1">
                  @if ($almacen)
                    <div class="text-muted small text-uppercase">Tu sede</div>
                    <div class="fs-5 fw-semibold mb-0">{{ $almacen->nombre }}</div>
                    <div class="small text-muted">
                      Código <code>{{ $almacen->codigo }}</code>
                      @if ($almacen->direccion) · {{ $almacen->direccion }} @endif
                      @if ($almacen->telefono) · <i class="bi bi-telephone"></i> {{ $almacen->telefono }} @endif
                    </div>
                  @else
                    <div class="text-muted small text-uppercase">Sede</div>
                    <div class="fs-5 fw-semibold text-muted">Sin sede asignada</div>
                    <div class="small text-muted">Contacta al administrador para que te asigne un almacén.</div>
                  @endif
                </div>
              </div>
            </div>
          </div>

          <div class="row g-3">
            <div class="col-md-3">
              <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small text-uppercase">Ventas del mes</span>
                    <i class="bi bi-cash-coin text-success fs-4"></i>
                  </div>
                  <div class="h3 mb-0">{{ $fmt($ventas_mes) }}</div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small text-uppercase">Cotizado del mes</span>
                    <i class="bi bi-clipboard-data text-info fs-4"></i>
                  </div>
                  <div class="h3 mb-0">{{ $fmt($cotizado_mes) }}</div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small text-uppercase">Meta del mes</span>
                    <i class="bi bi-bullseye text-warning fs-4"></i>
                  </div>
                  <div class="h3 mb-0">{{ $meta_mes > 0 ? $fmt($meta_mes) : 'Sin meta' }}</div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small text-uppercase">% Cumplimiento</span>
                    <i class="bi bi-graph-up-arrow text-primary fs-4"></i>
                  </div>
                  <div class="h3 mb-2">{{ number_format($pct_cumplimiento, 1) }} %</div>
                  <div class="progress" style="height: 8px;">
                    @php
                      $pctBar = min(100, $pct_cumplimiento);
                      $color = $pct_cumplimiento >= 100 ? 'bg-success' : ($pct_cumplimiento >= 60 ? 'bg-warning' : 'bg-danger');
                    @endphp
                    <div class="progress-bar {{ $color }}" role="progressbar"
                         style="width: {{ $pctBar }}%"
                         aria-valuenow="{{ $pct_cumplimiento }}" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          @if ($meta_mes == 0)
            <div class="alert alert-info mt-4">
              <i class="bi bi-info-circle"></i> Aún no tienes meta asignada para este mes. Contacta al administrador.
            </div>
          @endif

          {{-- ============= CUADRO DE SEGUIMIENTO DEL LOCAL ============= --}}
          @if ($almacen)
            <div class="mt-4 mb-3">
              <h5 class="fw-semibold mb-0">
                <i class="bi bi-shop"></i> Cuadro de seguimiento — {{ $almacen->nombre }}
              </h5>
              <small class="text-muted">Consolidado del local para {{ $meses[$mes] }} {{ $anio }}</small>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                  <div class="card-body">
                    <div class="text-muted small text-uppercase mb-1">Meta del local</div>
                    <div class="h4 mb-0">{{ $meta_sede > 0 ? $fmt($meta_sede) : 'Sin meta' }}</div>
                    <small class="text-muted">Suma de metas del equipo</small>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                  <div class="card-body">
                    <div class="text-muted small text-uppercase mb-1">Venta a la fecha</div>
                    <div class="h4 mb-0 text-success">{{ $fmt($ventas_sede_mes) }}</div>
                    <small class="text-muted">Total del local este mes</small>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                  <div class="card-body">
                    <div class="text-muted small text-uppercase mb-1">Venta faltante</div>
                    <div class="h4 mb-0 {{ $faltante_sede > 0 ? 'text-danger' : 'text-success' }}">
                      {{ $meta_sede > 0 ? $fmt($faltante_sede) : '—' }}
                    </div>
                    <small class="text-muted">Para cumplir la meta</small>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                  <div class="card-body">
                    <div class="text-muted small text-uppercase mb-1">Cumplimiento local</div>
                    <div class="h4 mb-2">{{ number_format($pct_sede, 1) }} %</div>
                    <div class="progress" style="height: 8px;">
                      @php
                        $pctSedeBar = min(100, $pct_sede);
                        $colorSede = $pct_sede >= 100 ? 'bg-success' : ($pct_sede >= 60 ? 'bg-warning' : 'bg-danger');
                      @endphp
                      <div class="progress-bar {{ $colorSede }}" role="progressbar"
                           style="width: {{ $pctSedeBar }}%"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {{-- Tabla venta por comercial --}}
            <div class="card border-0 shadow-sm mb-3">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h6 class="mb-0"><i class="bi bi-people"></i> Venta por comercial</h6>
                  <small class="text-muted">{{ $companeros->count() }} vendedor(es) en la sede</small>
                </div>
                <div class="table-responsive">
                  <table class="table table-sm align-middle mb-0">
                    <thead class="text-muted small text-uppercase">
                      <tr>
                        <th>Vendedor</th>
                        <th class="text-end">Meta</th>
                        <th class="text-end">Venta</th>
                        <th class="text-end">Faltante</th>
                        <th style="width: 180px;">Cumplimiento</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse ($companeros as $c)
                        @php
                          $cBarPct = min(100, $c->pct);
                          $cColor = $c->pct >= 100 ? 'bg-success' : ($c->pct >= 60 ? 'bg-warning' : 'bg-danger');
                        @endphp
                        <tr class="{{ $c->es_actual ? 'table-primary' : '' }}">
                          <td>
                            {{ $c->nombre }}
                            @if ($c->es_actual)
                              <span class="badge bg-primary ms-1">Tú</span>
                            @endif
                          </td>
                          <td class="text-end">{{ $c->meta > 0 ? $fmt($c->meta) : '—' }}</td>
                          <td class="text-end">{{ $fmt($c->vendido) }}</td>
                          <td class="text-end {{ $c->faltante > 0 && $c->meta > 0 ? 'text-danger' : '' }}">
                            {{ $c->meta > 0 ? $fmt($c->faltante) : '—' }}
                          </td>
                          <td>
                            @if ($c->meta > 0)
                              <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height: 6px;">
                                  <div class="progress-bar {{ $cColor }}" style="width: {{ $cBarPct }}%"></div>
                                </div>
                                <small class="text-muted" style="min-width: 45px;">{{ number_format($c->pct, 1) }}%</small>
                              </div>
                            @else
                              <span class="text-muted small">Sin meta</span>
                            @endif
                          </td>
                        </tr>
                      @empty
                        <tr><td colspan="5" class="text-center text-muted">No hay vendedores en la sede.</td></tr>
                      @endforelse
                      @if ($ventas_sin_vendedor > 0)
                        <tr class="table-warning">
                          <td><span class="text-muted"><i class="bi bi-question-circle"></i> Sin vendedor</span></td>
                          <td class="text-end">—</td>
                          <td class="text-end">{{ $fmt($ventas_sin_vendedor) }}</td>
                          <td class="text-end">—</td>
                          <td class="text-muted small">Ventas sin comercial asignado</td>
                        </tr>
                      @endif
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          @endif
        @endif

        {{-- ========================= DASHBOARD ADMIN ========================= --}}
        @if ($rol === 'admin')
          <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
            <div>
              <h4 class="text-2xl font-semibold mb-0">Panel de Ventas</h4>
              <p class="text-muted mb-0">
                @if ($almacen_seleccionado)
                  Vista de la sede <strong>{{ $almacen_seleccionado->nombre }}</strong> — {{ $meses[$mes] }} {{ $anio }}
                @else
                  Consolidado de todas las sedes — {{ $meses[$mes] }} {{ $anio }}
                @endif
              </p>
            </div>

            {{-- Filtro de sede --}}
            <form method="GET" action="{{ route('dashboard') }}" class="d-flex align-items-center gap-2">
              <label class="text-muted small text-uppercase mb-0">Sede:</label>
              <select name="almacen_id" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 220px;">
                <option value="">Todas las sedes</option>
                @foreach ($almacenes_activos as $a)
                  <option value="{{ $a->id }}" {{ $almacen_seleccionado && $almacen_seleccionado->id == $a->id ? 'selected' : '' }}>
                    {{ $a->nombre }} ({{ $a->codigo }})
                  </option>
                @endforeach
              </select>
              @if ($almacen_seleccionado)
                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary" title="Quitar filtro">
                  <i class="bi bi-x-lg"></i>
                </a>
              @endif
            </form>
          </div>

          {{-- ============ CUADRO DE SEGUIMIENTO ============ --}}
          <div class="mb-2">
            <h6 class="fw-semibold mb-0">
              <i class="bi bi-shop"></i>
              Cuadro de seguimiento
              @if ($almacen_seleccionado)
                — {{ $almacen_seleccionado->nombre }}
              @else
                (consolidado global)
              @endif
            </h6>
          </div>
          <div class="row g-3 mb-4">
            <div class="col-md-3">
              <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start mb-1">
                    <span class="text-muted small text-uppercase">Meta {{ $almacen_seleccionado ? 'del local' : 'total' }}</span>
                    <i class="bi bi-bullseye text-warning fs-4"></i>
                  </div>
                  <div class="h4 mb-0">{{ $meta_total > 0 ? $fmt($meta_total) : 'Sin meta' }}</div>
                  <small class="text-muted">Suma de metas del periodo</small>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start mb-1">
                    <span class="text-muted small text-uppercase">Venta a la fecha</span>
                    <i class="bi bi-cash-coin text-success fs-4"></i>
                  </div>
                  <div class="h4 mb-0 text-success">{{ $fmt($ventas_total) }}</div>
                  <small class="text-muted">Total del periodo</small>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start mb-1">
                    <span class="text-muted small text-uppercase">Venta faltante</span>
                    <i class="bi bi-hourglass-bottom text-danger fs-4"></i>
                  </div>
                  <div class="h4 mb-0 {{ $faltante_total > 0 ? 'text-danger' : 'text-success' }}">
                    {{ $meta_total > 0 ? $fmt($faltante_total) : '—' }}
                  </div>
                  <small class="text-muted">Para cumplir la meta</small>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start mb-1">
                    <span class="text-muted small text-uppercase">Cumplimiento</span>
                    <i class="bi bi-graph-up-arrow text-primary fs-4"></i>
                  </div>
                  <div class="h4 mb-2">{{ number_format($pct_total, 1) }} %</div>
                  <div class="progress" style="height: 8px;">
                    @php
                      $pctTotBar = min(100, $pct_total);
                      $colorTot = $pct_total >= 100 ? 'bg-success' : ($pct_total >= 60 ? 'bg-warning' : 'bg-danger');
                    @endphp
                    <div class="progress-bar {{ $colorTot }}" style="width: {{ $pctTotBar }}%"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {{-- Fila: Resumen cotizaciones --}}
          <div class="mb-2">
            <h6 class="fw-semibold mb-0"><i class="bi bi-clipboard-data"></i> Cotizaciones del mes</h6>
          </div>
          <div class="row g-3 mb-4">
            <div class="col-md-4">
              <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small text-uppercase">Total</span>
                    <i class="bi bi-clipboard-data text-info fs-4"></i>
                  </div>
                  <div class="h3 mb-0">{{ $cot_resumen['total'] }}</div>
                  <small class="text-muted">{{ $fmt($cot_resumen['monto_total']) }}</small>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small text-uppercase">Pendientes</span>
                    <i class="bi bi-hourglass-split text-warning fs-4"></i>
                  </div>
                  <div class="h3 mb-0">{{ $cot_resumen['pendientes'] }}</div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small text-uppercase">Aplicadas</span>
                    <i class="bi bi-check-circle text-success fs-4"></i>
                  </div>
                  <div class="h3 mb-0">{{ $cot_resumen['aplicadas'] }}</div>
                </div>
              </div>
            </div>
          </div>

          {{-- Fila 2: Cumplimiento por vendedor --}}
          <div class="row g-3 mb-4">
            <div class="col-md-8">
              <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">
                      Cumplimiento por vendedor
                      @if ($almacen_seleccionado)
                        — {{ $almacen_seleccionado->nombre }}
                      @endif
                    </h5>
                    <a href="{{ route('metas') }}" class="btn btn-sm btn-outline-primary">
                      <i class="bi bi-bullseye"></i> Gestionar metas
                    </a>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-sm align-middle">
                      <thead class="text-muted small text-uppercase">
                        <tr>
                          <th>Vendedor</th>
                          <th class="text-center">Cotizaciones<br><small>Pend/Aplic/Tot</small></th>
                          <th class="text-end">Cotizado</th>
                          <th class="text-end">Vendido</th>
                          <th class="text-end">Meta</th>
                          <th class="text-end">Faltante</th>
                          <th style="width: 180px;">Cumplimiento</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse ($cumplimiento as $c)
                          @php
                            $barPct = min(100, $c->pct);
                            $barColor = $c->pct >= 100 ? 'bg-success' : ($c->pct >= 60 ? 'bg-warning' : 'bg-danger');
                          @endphp
                          <tr>
                            <td>{{ $c->nombre }}</td>
                            <td class="text-center">
                              <span class="badge bg-warning-subtle text-warning-emphasis">{{ $c->cot_pendientes }}</span>
                              <span class="badge bg-success-subtle text-success-emphasis">{{ $c->cot_aplicadas }}</span>
                              <span class="badge bg-secondary-subtle text-secondary-emphasis">{{ $c->cot_total }}</span>
                            </td>
                            <td class="text-end">{{ $fmt($c->cot_monto) }}</td>
                            <td class="text-end">{{ $fmt($c->ventas) }}</td>
                            <td class="text-end">{{ $c->meta > 0 ? $fmt($c->meta) : '—' }}</td>
                            <td class="text-end {{ $c->faltante > 0 && $c->meta > 0 ? 'text-danger' : '' }}">
                              {{ $c->meta > 0 ? $fmt($c->faltante) : '—' }}
                            </td>
                            <td>
                              @if ($c->meta > 0)
                                <div class="d-flex align-items-center gap-2">
                                  <div class="progress flex-grow-1" style="height: 8px;">
                                    <div class="progress-bar {{ $barColor }}" style="width: {{ $barPct }}%"></div>
                                  </div>
                                  <small class="text-muted" style="min-width: 45px;">{{ number_format($c->pct, 1) }}%</small>
                                </div>
                              @else
                                <span class="text-muted small">Sin meta</span>
                              @endif
                            </td>
                          </tr>
                        @empty
                          <tr><td colspan="7" class="text-center text-muted">
                            @if ($almacen_seleccionado)
                              Esta sede aún no tiene vendedores asignados.
                            @else
                              No hay vendedores registrados.
                            @endif
                          </td></tr>
                        @endforelse
                        @if ($ventas_sin_vendedor > 0)
                          <tr class="table-warning">
                            <td><span class="text-muted"><i class="bi bi-question-circle"></i> Sin vendedor</span></td>
                            <td colspan="2"></td>
                            <td class="text-end">{{ $fmt($ventas_sin_vendedor) }}</td>
                            <td colspan="3" class="text-muted small">Ventas del local sin comercial asignado</td>
                          </tr>
                        @endif
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            {{-- Consolidado por almacén --}}
            <div class="col-md-4">
              <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Ventas por almacén</h5>
                    <a href="{{ route('almacenes') }}" class="btn btn-sm btn-outline-primary">
                      <i class="bi bi-building"></i>
                    </a>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-sm align-middle">
                      <thead class="text-muted small text-uppercase">
                        <tr>
                          <th>Almacén</th>
                          <th class="text-end">Ventas</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse ($por_almacen as $a)
                          <tr>
                            <td>
                              <div>{{ $a->nombre }}</div>
                              <small class="text-muted">{{ $a->codigo }} · {{ $a->vendedores }} vendedor(es)</small>
                            </td>
                            <td class="text-end">{{ $fmt($a->ventas) }}</td>
                          </tr>
                        @empty
                          <tr><td colspan="2" class="text-center text-muted">Sin almacenes activos.</td></tr>
                        @endforelse
                        @if ($sin_almacen > 0)
                          <tr>
                            <td><span class="text-muted">Sin almacén</span></td>
                            <td class="text-end text-muted">{{ $fmt($sin_almacen) }}</td>
                          </tr>
                        @endif
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        @endif
      </div>
    </div>
</x-app-layout>
