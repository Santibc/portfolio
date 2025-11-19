@extends('tienda.recife_layout')

@section('title', $producto->nombre . ' - ' . $empresa->nombre)
@section('description', $producto->descripcion)

@section('content')
@php
    // Buscar descuentos activos para este producto
    $descuentoActivo = null;
    $textoDescuento = null;
    $precioConDescuento = $producto->precio_actual;
    $montoDescuento = 0;

    if (isset($descuentosActivos)) {
        foreach ($descuentosActivos as $desc) {
            $aplica = false;

            if ($desc->aplica_a === 'orden' || $desc->aplica_a === 'carrito') {
                $aplica = true;
            } elseif ($desc->aplica_a === 'producto' && in_array($producto->id, $desc->productos_aplicables ?? [])) {
                $aplica = true;
            } elseif ($desc->aplica_a === 'categoria' && in_array($producto->categoria_id, $desc->categorias_aplicables ?? [])) {
                $aplica = true;
            }

            if ($aplica) {
                $descuentoActivo = $desc;
                if ($desc->tipo === 'porcentaje') {
                    $montoDescuento = ($producto->precio_actual * $desc->valor) / 100;
                    $textoDescuento = round($desc->valor) . '% OFF';
                } else {
                    $montoDescuento = $desc->valor;
                    $textoDescuento = '$' . number_format($desc->valor, 0, ',', '.') . ' OFF';
                }
                $precioConDescuento = $producto->precio_actual - $montoDescuento;
                break;
            }
        }
    }
    $stockInfo = $producto->getStockInfo();
@endphp

        <div id="single-product" class="js-has-new-shipping js-product-detail js-product-container js-shipping-calculator-container pb-4 pt-md-4 pb-md-3" data-variants="[{&quot;product_id&quot;:184819186,&quot;price_short&quot;:&quot;$47.000,00&quot;,&quot;price_long&quot;:&quot;$47.000,00 ARS&quot;,&quot;price_number&quot;:47000,&quot;price_number_raw&quot;:4700000,&quot;price_with_payment_discount_short&quot;:&quot;$42.300,00&quot;,&quot;price_without_taxes&quot;:&quot;$38.842,98&quot;,&quot;compare_at_price_short&quot;:null,&quot;compare_at_price_long&quot;:null,&quot;compare_at_price_number&quot;:null,&quot;compare_at_price_number_raw&quot;:null,&quot;has_promotional_price&quot;:false,&quot;stock&quot;:null,&quot;sku&quot;:null,&quot;available&quot;:true,&quot;is_visible&quot;:true,&quot;contact&quot;:false,&quot;option0&quot;:&quot;Bord\u00f3&quot;,&quot;option1&quot;:null,&quot;option2&quot;:null,&quot;id&quot;:722079781,&quot;image&quot;:538793924,&quot;image_url&quot;:&quot;\/\/dcdn-us.mitiendanube.com\/stores\/003\/765\/060\/products\/producto-311-fd1e7cf0deb1868b5516958276344139-1024-1024.webp&quot;,&quot;installments_data&quot;:&quot;{\&quot;Pago Nube\&quot;:{\&quot;1\&quot;:{\&quot;installment_value\&quot;:47000,\&quot;installment_value_cents\&quot;:4700000,\&quot;interest\&quot;:0,\&quot;total_value\&quot;:47000,\&quot;without_interests\&quot;:true,\&quot;collector_is_third_party\&quot;:false}},\&quot;Pagos Personalizados\&quot;:{\&quot;1\&quot;:{\&quot;installment_value\&quot;:47000,\&quot;installment_value_cents\&quot;:4700000,\&quot;interest\&quot;:0,\&quot;total_value\&quot;:47000,\&quot;without_interests\&quot;:true,\&quot;collector_is_third_party\&quot;:false}}}&quot;},{&quot;product_id&quot;:184819186,&quot;price_short&quot;:&quot;$47.000,00&quot;,&quot;price_long&quot;:&quot;$47.000,00 ARS&quot;,&quot;price_number&quot;:47000,&quot;price_number_raw&quot;:4700000,&quot;price_with_payment_discount_short&quot;:&quot;$42.300,00&quot;,&quot;price_without_taxes&quot;:&quot;$38.842,98&quot;,&quot;compare_at_price_short&quot;:null,&quot;compare_at_price_long&quot;:null,&quot;compare_at_price_number&quot;:null,&quot;compare_at_price_number_raw&quot;:null,&quot;has_promotional_price&quot;:false,&quot;stock&quot;:null,&quot;sku&quot;:null,&quot;available&quot;:true,&quot;is_visible&quot;:true,&quot;contact&quot;:false,&quot;option0&quot;:&quot;Gris&quot;,&quot;option1&quot;:null,&quot;option2&quot;:null,&quot;id&quot;:722079786,&quot;image&quot;:538793924,&quot;image_url&quot;:&quot;\/\/dcdn-us.mitiendanube.com\/stores\/003\/765\/060\/products\/producto-311-fd1e7cf0deb1868b5516958276344139-1024-1024.webp&quot;,&quot;installments_data&quot;:&quot;{\&quot;Pago Nube\&quot;:{\&quot;1\&quot;:{\&quot;installment_value\&quot;:47000,\&quot;installment_value_cents\&quot;:4700000,\&quot;interest\&quot;:0,\&quot;total_value\&quot;:47000,\&quot;without_interests\&quot;:true,\&quot;collector_is_third_party\&quot;:false}},\&quot;Pagos Personalizados\&quot;:{\&quot;1\&quot;:{\&quot;installment_value\&quot;:47000,\&quot;installment_value_cents\&quot;:4700000,\&quot;interest\&quot;:0,\&quot;total_value\&quot;:47000,\&quot;without_interests\&quot;:true,\&quot;collector_is_third_party\&quot;:false}}}&quot;},{&quot;product_id&quot;:184819186,&quot;price_short&quot;:&quot;$47.000,00&quot;,&quot;price_long&quot;:&quot;$47.000,00 ARS&quot;,&quot;price_number&quot;:47000,&quot;price_number_raw&quot;:4700000,&quot;price_with_payment_discount_short&quot;:&quot;$42.300,00&quot;,&quot;price_without_taxes&quot;:&quot;$38.842,98&quot;,&quot;compare_at_price_short&quot;:null,&quot;compare_at_price_long&quot;:null,&quot;compare_at_price_number&quot;:null,&quot;compare_at_price_number_raw&quot;:null,&quot;has_promotional_price&quot;:false,&quot;stock&quot;:null,&quot;sku&quot;:null,&quot;available&quot;:true,&quot;is_visible&quot;:true,&quot;contact&quot;:false,&quot;option0&quot;:&quot;Negro&quot;,&quot;option1&quot;:null,&quot;option2&quot;:null,&quot;id&quot;:722079789,&quot;image&quot;:538793924,&quot;image_url&quot;:&quot;\/\/dcdn-us.mitiendanube.com\/stores\/003\/765\/060\/products\/producto-311-fd1e7cf0deb1868b5516958276344139-1024-1024.webp&quot;,&quot;installments_data&quot;:&quot;{\&quot;Pago Nube\&quot;:{\&quot;1\&quot;:{\&quot;installment_value\&quot;:47000,\&quot;installment_value_cents\&quot;:4700000,\&quot;interest\&quot;:0,\&quot;total_value\&quot;:47000,\&quot;without_interests\&quot;:true,\&quot;collector_is_third_party\&quot;:false}},\&quot;Pagos Personalizados\&quot;:{\&quot;1\&quot;:{\&quot;installment_value\&quot;:47000,\&quot;installment_value_cents\&quot;:4700000,\&quot;interest\&quot;:0,\&quot;total_value\&quot;:47000,\&quot;without_interests\&quot;:true,\&quot;collector_is_third_party\&quot;:false}}}&quot;}]" data-store="product-detail">
    <div class="container pt-md-1">
        <div class="row">
            <div class="col-md-7 pb-4">
                	
<div class="row" data-store="product-image-{{ $producto->id }}"> 
				<div class="col-md product-with-one-image pr-md-3">
			<div class="js-swiper-product swiper-container product-detail-slider swiper-container-initialized swiper-container-horizontal" data-product-images-amount="{{ $producto->imagenes->count() }}">
                

















	<div class="labels js-labels-floating-group labels-absolute">
		@if(!$stockInfo['hay_stock'] && $stockInfo['stock_limitado'])
			<div class="js-stock-label-private label js-stock-label label label-default label-big" data-store="product-item-label-stock">
				Sin stock
			</div>
		@endif

		@if($producto->info_envio && stripos($producto->info_envio, 'gratis') !== false)
			<div class="js-shipping-label-private label label label-accent label-big" data-store="product-item-label-shipping">
				{{ $producto->info_envio }}
			</div>
		@endif
	</div>
<span class="hidden" data-store="stock-product-{{ $producto->id }}-infinite"></span>
				<div class="swiper-wrapper">
																		<div class="js-product-slide w-100 swiper-slide product-slide slider-slide swiper-slide-active" data-image="538793924" data-image-position="0">
																	<a href="{{ $producto->url_imagen_principal ?? asset('assets/img/product/placeholder.webp') }}" data-fancybox="product-gallery" class="js-product-slide-link d-block position-relative" style="padding-bottom: 139.89071038251%;">
								
									
																												
									<img fetchpriority="high" src="{{ $producto->url_imagen_principal ?? asset('assets/img/product/placeholder.webp') }}" class="js-product-slide-img product-slider-image img-absolute img-absolute-centered " width="1464" height="2048" alt="{{ $producto->nombre }}">
																	</a>
															</div>
																															</div>
			</div>
					</div>
	</div>            </div>
            <div class="col" data-store="product-info-{{ $producto->id }}">
                <div class="pt-md-3 px-md-3">

    
            

		<section class="page-header  " data-store="page-title">
				
    <div class="breadcrumbs ">
        <a class="crumb" href="{{ route('tienda.empresa', $empresa->slug) }}" title="{{ $empresa->nombre }}">Inicio</a>
        <span class="separator">.</span>
        @if($producto->categoria)
            <a class="crumb" href="{{ route('tienda.categorias', [$empresa->slug, 'categoria' => $producto->categoria_id]) }}" title="{{ $producto->categoria->nombre }}">{{ $producto->categoria->nombre }}</a>
            <span class="separator">.</span>
        @endif
        <span class="crumb active">{{ $producto->nombre }}</span>
    </div>
				<h1 class="h2 h1-md js-product-name mb-3" data-store="product-name-{{ $producto->id }}">{{ $producto->nombre }}</h1>
		</section>
    
    
    
    
    <div class="price-container" data-store="product-price-{{ $producto->id }}">
        <div class="js-price-container mb-3">
            <span class="d-inline-block mr-1">
            	@if($descuentoActivo)
            		<div class="text-muted" style="text-decoration: line-through; font-size: 1.2rem;">${{ number_format($producto->precio_actual, 0, ',', '.') }}</div>
            		<div class="js-price-display h3 font-huge" id="price_display" data-product-price="{{ $precioConDescuento * 100 }}">${{ number_format($precioConDescuento, 0, ',', '.') }}</div>
            	@else
            		<div class="js-price-display h3 font-huge" id="price_display" data-product-price="{{ $producto->precio_actual * 100 }}">${{ number_format($producto->precio_actual, 0, ',', '.') }}</div>
            	@endif
            </span>
            

















	<div class="labels d-inline-block align-text-top" data-store="product-item-labels">
		@if($descuentoActivo)
			<div class="label js-offer-label label label-inline label-big" data-store="product-item-offer-label">
				<span class="label-offer-percentage">{{ $textoDescuento }}</span>
			</div>
		@endif
		
		
		
		
		
		
		
	</div>
<span class="hidden" data-store="stock-product-{{ $producto->id }}-infinite"></span>
            <span class="d-block font-big title-font-family mt-1">
               <div id="compare_price_display" class="js-compare-price-display price-compare " style="display:none;"></div>
            </span>
            

             
            

        </div>

        

                                        
                    <div data-toggle="#installments-modal" data-modal-url="modal-fullscreen-payments" class="js-modal-open js-fullscreen-modal-open js-product-payments-container mb-3 col-md-8 px-0">
                            
	
	
                    @if($descuentoActivo)
                    <div class="js-product-discount-container mb-2 font-small">
                        <span class="text-accent">{{ $textoDescuento }}</span>
                        @if($descuentoActivo->descripcion)
                            {{ $descuentoActivo->descripcion }}
                        @endif
                        @if(!$descuentoActivo->es_acumulable)
                            <div class="js-product-discount-disclaimer font-small mt-1 opacity-60">
                                No acumulable con otras promociones
                            </div>
                        @endif
                    </div>
                    @endif
                        <a id="btn-installments" class="font-small" href="#">
                    <svg class="icon-inline icon-lg svg-icon-text"><use xlink:href="#credit-card"></use></svg>
                                            Ver más detalles
                                    </a>
            </div>
        
        
        

                    @if($producto->info_envio || $producto->dias_devolucion || $producto->garantia)
                    <div class="recife-benefits-list mb-4">
                        @if($producto->info_envio)
                        <div class="js-free-shipping-minimum-message free-shipping-message mb-2">
                            <span class="float-left mr-1">
                                <svg class="icon-inline svg-icon-accent icon-lg"><use xlink:href="#truck"></use></svg>
                            </span>
                            <span class="font-small">
                                <span class="text-accent">{{ $producto->info_envio }}</span>
                            </span>
                        </div>
                        @endif

                        @if($producto->dias_devolucion)
                        <div class="free-shipping-message mb-2">
                            <span class="float-left mr-1">
                                <svg class="icon-inline svg-icon-accent icon-lg"><use xlink:href="#refresh"></use></svg>
                            </span>
                            <span class="font-small">
                                <span class="text-accent">{{ $producto->dias_devolucion }}</span>
                            </span>
                        </div>
                        @endif

                        @if($producto->garantia)
                        <div class="free-shipping-message mb-2">
                            <span class="float-left mr-1">
                                <svg class="icon-inline svg-icon-accent icon-lg"><use xlink:href="#shield-check"></use></svg>
                            </span>
                            <span class="font-small">
                                <span class="text-accent">{{ $producto->garantia }}</span>
                            </span>
                        </div>
                        @endif
                    </div>
                    @endif
            </div>

    


    
     <form id="product_form" class="js-product-form mt-4" method="post" action="{{ route('tienda.carrito.agregar', $empresa->slug) }}" data-store="product-form-{{ $producto->id }}">
        @csrf
        <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                                                <div class="js-product-variants  form-row mb-2">
								
				
		
        @if($producto->tiene_variantes && $producto->variantes->count() > 0)
        <div class="col-12 mb-3">
            <label class="form-label">Variantes Disponibles: <strong class="js-selected-variant-label">Seleccione una opción</strong></label>
            <div class="d-flex flex-wrap" style="gap: 8px;">
                @foreach($producto->variantes as $index => $variante)
                    @php
                        $varianteStockInfo = $producto->getStockInfo($variante->id);
                        $tieneStockDisponible = $varianteStockInfo['hay_stock'];
                        $nombreVariante = $variante->nombre_variante;
                        $isDisabled = !$tieneStockDisponible && $producto->controlar_stock && !$producto->permitir_venta_sin_stock;
                    @endphp
                    <a href="#"
                       class="js-recife-variant btn btn-variant {{ $index === 0 ? 'selected' : '' }} {{ $isDisabled ? 'disabled' : '' }} p-0"
                       data-variante-id="{{ $variante->id }}"
                       data-variante-name="{{ $nombreVariante }}"
                       data-stock-disponible="{{ $varianteStockInfo['stock_disponible'] }}"
                       data-puede-agregar-sin-stock="{{ $varianteStockInfo['puede_agregar_sin_stock'] ? 'true' : 'false' }}"
                       {{ $isDisabled ? 'disabled' : '' }}
                       title="{{ $nombreVariante ?: 'Sin especificar' }}">
                        <span class="btn-variant-content" style="padding: 8px 16px; border: 1px solid #ddd; border-radius: 4px;">
                            {{ $nombreVariante ?: 'Sin especificar' }}
                        </span>
                    </a>
                @endforeach
            </div>
            <input type="hidden" name="variante_id" id="variante_id" value="{{ $producto->variantes->first()->id ?? '' }}">
        </div>
        @endif
		</div>        
                    <div class="js-last-product text-accent mb-3" style="display: none;">
                ¡No te lo pierdas, es el último!
            </div>
        
        <div class="row no-gutters mb-4 ">
                                        

<div class="col-4 col-md-3 mx-neg-1">
    
<div class="form-group js-quantity">
                <div class="form-quantity d-flex form-row m-0 align-items-center" data-component="product.quantity">
            <span class="js-quantity-down form-quantity-icon btn icon-30px ml-1" data-component="product.quantity.minus">
                <svg class="icon-inline"><use xlink:href="#minus"></use></svg>
            </span>
                <div class="form-control-container col px-0" data-component="product.adding-amount">
                <input type="number" class=" form-control js-quantity-input form-control-big form-control-inline" autocorrect="off" autocapitalize="off" pattern="\d*" name="cantidad" value="1" min="1" aria-label="Cambiar cantidad" data-component="adding-amount.value">
                                </div>
                    <span class="js-quantity-up form-quantity-icon btn icon-30px mr-1" data-component="product.quantity.plus">
                <svg class="icon-inline"><use xlink:href="#plus"></use></svg>
            </span>
        </div>
                    </div>


    </div>            
            


            
                        
                        
            <div class="col-8 col-md-9 ">

                
                <input type="submit" class="js-addtocart btn-add-to-cart btn btn-primary btn-big w-100 cart" value="Agregar al carrito"  >

                
                <div class="js-addtocart js-addtocart-placeholder btn  btn-primary btn-block btn-transition btn-big w-100 disabled" style="display: none;">
    <div class="d-inline-block">
        <span class="js-addtocart-text">
                            Agregar al carrito
                    </span>
        <span class="js-addtocart-success transition-container">
            ¡Listo!
        </span>
        <div class="js-addtocart-adding js-addtocart-adding-text transition-container">
            Agregando...
        </div>
    </div>
</div>
            </div>

                            <div class="col-12">
                    <div class="js-added-to-cart-product-message font-small my-3" style="display: none;">
                        <svg class="icon-inline icon-lg svg-icon-text"><use xlink:href="#check"></use></svg>
                        <span>
                            Ya agregaste este producto.<a href="#" class="js-modal-open js-open-cart js-fullscreen-modal-open btn-link font-small ml-1" data-toggle="#modal-cart" data-modal-url="modal-fullscreen-cart">Ver carrito</a>
                        </span>
                    </div>
                </div>
            
            
            
            
                    </div>

        
            
                         </form>
</div>

       

    

<div id="installments-modal" class="js-modal js-fullscreen-modal  modal modal-bottom-md modal-overflow-none modal-flex-column modal-right modal-centered-md modal--md transition-slide modal-centered transition-soft " style="display: none;" data-modal-url="">
                        <div class="js-modal-close js-fullscreen-modal-close  modal-header  ">
                                            <div class="row no-gutters align-items-center">
                            <div class="col p-3">
                                            Medios de pago
                                    </div>
                            <div class="col-auto">
                                <a class="js-modal-close modal-close ">
                                    <svg class="icon-inline svg-icon-text"><use xlink:href="#times"></use></svg>
                                </a>
                            </div>
                        </div>
                                    </div>
                <div class="modal-body modal-scrollable pt-0">
                    
            <div class="modal-scrollable modal-scrollable-area">

                
                
<div class="js-tab-container">
    <ul class="js-payments-tab-group tab-group">
                                                        <li id="method_pago-nube" class="js-refresh-installment-data js-installments-gw-tab js-tab tab  active " data-code="Pago Nube">
                <a href="#installment_pago-nube" class="js-tab-link tab-link">
                    Pago Nube
                                        <span class="js-modal-tab-discount text-accent " style="display: none">
                        <strong>Hasta 0% OFF</strong>
                    </span>
                </a>
            </li>
                                                        <li id="method_pagos-personalizados" class="js-refresh-installment-data js-installments-gw-tab js-tab tab " data-code="Pagos Personalizados">
                <a href="#installment_pagos-personalizados" class="js-tab-link tab-link">
                    Pagos Personalizados
                                        <span class="js-modal-tab-discount text-accent " style="">
                        <strong>10% OFF</strong>
                    </span>
                </a>
            </li>
            </ul>

    
    <div class="js-tabs-content tab-content">
                                            <div id="installment_pago-nube" class="js-tab-panel tab-panel  active  js-gw-tab-pane">
                
                    
                                        
                        
                        




                    

        <div class="js-payment-method-title js-payment-method-title-credit_card h6 font-body mb-3">Tarjetas de crédito</div>

        <div id="info-payment-method-credit_card" class="js-info-payment-method-container card p-3">
                
                <div class="js-payment-method-discount mb-3" style="display: none">
            <span><strong>0% de descuento</strong> pagando con Tarjetas de crédito</span>
        </div>

                <div class="js-payment-method-total">
            <span class="font-big mb-3">
                                    <span>Total en 1 pago: </span>
                
                <span style="display: none" class="js-installments-one-payment js-compare-price-display price-compare">$47.000,00</span>
                <strong style="display: none" class="js-price-with-discount" data-payment-discount="0">$47.000,00</strong>

                                <strong class="js-installments-one-payment js-installments-no-discount" style="">$47.000,00</strong>
            </span>
                            <span class="mt-1">con todas las tarjetas.</span>
                    </div>

        <div class="js-discount-explanation font-small mt-1" style="display: none">El descuento se aplicará sobre el precio del producto (sin envío) al elegir el medio de pago.</div>

                
        <div class="js-discount-disclaimer font-small opacity-60 mt-1" style="display: none">No acumulable con otras promociones</div>

                            <div class="js-payment-method-installments-table-title mt-3">
                <strong>O pagá en</strong>
            </div>

                                            <table class="js-payments-table table mt-3 mb-3">
                    <tbody>
                                                    <tr id="installment_Pago_Nube_1" class="js-payment-provider-installments-row">
                                                                <td>
                                    <strong><span class="js-installment-amount">1</span></strong>
                                    <span>cuota</span>
                                    <span>de</span>
                                    <strong><span class="js-installment-price">$47.000,00</span></strong>
                                                                            sin interés
                                                                    </td>

                                                                <td>
                                    <small>CFT: 0,00% | TEA: 0,00%</small>
                                </td>

                                                                <td class="text-right">
                                    <strong><span class="mr-1">Total </span></strong>
                                    <span class="js-installment-total-price">$47.000,00</span>
                                </td>
                            </tr>
                                            </tbody>
                </table>

                                                    <div class="mt-2 ">
                                                    <span>
                                <img src="//dcdn-us.mitiendanube.com/assets/themes/recife/static/images/empty-placeholder.png" data-src="//d26lpennugtm8s.cloudfront.net/assets/common/img/logos/payment/new_logos_payment/visa@2x.png" class="lazyload card-img card-img-medium" alt="">
                            </span>
                                                    <span>
                                <img src="//dcdn-us.mitiendanube.com/assets/themes/recife/static/images/empty-placeholder.png" data-src="//d26lpennugtm8s.cloudfront.net/assets/common/img/logos/payment/new_logos_payment/mastercard@2x.png" class="lazyload card-img card-img-medium" alt="">
                            </span>
                                                    <span>
                                <img src="//dcdn-us.mitiendanube.com/assets/themes/recife/static/images/empty-placeholder.png" data-src="//d26lpennugtm8s.cloudfront.net/assets/common/img/logos/payment/new_logos_payment/amex@2x.png" class="lazyload card-img card-img-medium" alt="">
                            </span>
                                                    <span>
                                <img src="//dcdn-us.mitiendanube.com/assets/themes/recife/static/images/empty-placeholder.png" data-src="//d26lpennugtm8s.cloudfront.net/assets/common/img/logos/payment/new_logos_payment/ar/falabella@2x.png" class="lazyload card-img card-img-medium" alt="">
                            </span>
                                                    <span>
                                <img src="//dcdn-us.mitiendanube.com/assets/themes/recife/static/images/empty-placeholder.png" data-src="//d26lpennugtm8s.cloudfront.net/assets/common/img/logos/payment/new_logos_payment/ar/tarjeta-naranja@2x.png" class="lazyload card-img card-img-medium" alt="">
                            </span>
                                                    <span>
                                <img src="//dcdn-us.mitiendanube.com/assets/themes/recife/static/images/empty-placeholder.png" data-src="//d26lpennugtm8s.cloudfront.net/assets/common/img/logos/payment/new_logos_payment/ar/tarjeta-shopping@2x.png" class="lazyload card-img card-img-medium" alt="">
                            </span>
                                                    <span>
                                <img src="//dcdn-us.mitiendanube.com/assets/themes/recife/static/images/empty-placeholder.png" data-src="//d26lpennugtm8s.cloudfront.net/assets/common/img/logos/payment/new_logos_payment/nativa@2x.png" class="lazyload card-img card-img-medium" alt="">
                            </span>
                                                    <span>
                                <img src="//dcdn-us.mitiendanube.com/assets/themes/recife/static/images/empty-placeholder.png" data-src="//d26lpennugtm8s.cloudfront.net/assets/common/img/logos/payment/new_logos_payment/ar/cencosud@2x.png" class="lazyload card-img card-img-medium" alt="">
                            </span>
                                                    <span>
                                <img src="//dcdn-us.mitiendanube.com/assets/themes/recife/static/images/empty-placeholder.png" data-src="//d26lpennugtm8s.cloudfront.net/assets/common/img/logos/payment/new_logos_payment/ar/cabal@2x.png" class="lazyload card-img card-img-medium" alt="">
                            </span>
                                                    <span>
                                <img src="//dcdn-us.mitiendanube.com/assets/themes/recife/static/images/empty-placeholder.png" data-src="//d26lpennugtm8s.cloudfront.net/assets/common/img/logos/payment/new_logos_payment/ar/argencard@2x.png" class="lazyload card-img card-img-medium" alt="">
                            </span>
                                            </div>
                                        </div>
                    

        <div class="js-payment-method-title js-payment-method-title-debit_card h6 font-body mb-3">Tarjetas de débito</div>

        <div id="info-payment-method-debit_card" class="js-info-payment-method-container card p-3">
                            <div class="mb-2">
                                    <img src="//dcdn-us.mitiendanube.com/assets/themes/recife/static/images/empty-placeholder.png" data-src="//d26lpennugtm8s.cloudfront.net/assets/common/img/logos/payment/new_logos_payment/visa@2x.png" class="lazyload card-img card-img-medium" alt="">
                                    <img src="//dcdn-us.mitiendanube.com/assets/themes/recife/static/images/empty-placeholder.png" data-src="//d26lpennugtm8s.cloudfront.net/assets/common/img/logos/payment/new_logos_payment/mastercard@2x.png" class="lazyload card-img card-img-medium" alt="">
                                    <img src="//dcdn-us.mitiendanube.com/assets/themes/recife/static/images/empty-placeholder.png" data-src="//d26lpennugtm8s.cloudfront.net/assets/common/img/logos/payment/new_logos_payment/ar/cabaldebit@2x.png" class="lazyload card-img card-img-medium" alt="">
                            </div>
        
                <div class="js-payment-method-discount mb-3" style="display: none">
            <span><strong>0% de descuento</strong> pagando con Tarjetas de débito</span>
        </div>

                <div class="js-payment-method-total">
            <span class="font-big mb-3">
                                    <span>Total: </span>
                
                <span style="display: none" class="js-installments-one-payment js-compare-price-display price-compare">$47.000,00</span>
                <strong style="display: none" class="js-price-with-discount" data-payment-discount="0">$47.000,00</strong>

                                <strong class="js-installments-one-payment js-installments-no-discount" style="">$47.000,00</strong>
            </span>
                    </div>

        <div class="js-discount-explanation font-small mt-1" style="display: none">El descuento se aplicará sobre el precio del producto (sin envío) al elegir el medio de pago.</div>

                
        <div class="js-discount-disclaimer font-small opacity-60 mt-1" style="display: none">No acumulable con otras promociones</div>

                    </div>
                        

                            </div>
                                            <div id="installment_pagos-personalizados" class="js-tab-panel tab-panel  js-gw-tab-pane">
                
                    
                                        
                        
                        




                    

        <div class="js-payment-method-title js-payment-method-title-cash h6 font-body mb-3">Efectivo</div>

        <div id="info-payment-method-cash" class="js-info-payment-method-container card p-3">
                            <div class="mb-2">
                                    <img src="//dcdn-us.mitiendanube.com/assets/themes/recife/static/images/empty-placeholder.png" data-src="//d26lpennugtm8s.cloudfront.net/assets/common/img/logos/payment/new_logos_payment/cash@2x.png" class="lazyload card-img card-img-medium" alt="">
                            </div>
        
                <div class="js-payment-method-discount mb-3" style="">
            <span><strong>10% de descuento</strong> pagando con Efectivo</span>
        </div>

                <div class="js-payment-method-total">
            <span class="font-big mb-3">
                                    <span>Total: </span>
                
                <span style="" class="js-installments-one-payment js-compare-price-display price-compare">$47.000,00</span>
                <strong style="" class="js-price-with-discount" data-payment-discount="10">$42.300,00</strong>

                                <strong class="js-installments-one-payment js-installments-no-discount" style="display: none">$47.000,00</strong>
            </span>
                    </div>

        <div class="js-discount-explanation font-small mt-1" style="">El descuento se aplicará sobre el precio del producto (sin envío) al elegir el medio de pago.</div>

                
        <div class="js-discount-disclaimer font-small opacity-60 mt-1" style="display: none">No acumulable con otras promociones</div>

                    </div>
                    

        <div class="js-payment-method-title js-payment-method-title-wire_transfer h6 font-body mb-3">Transferencia o depósito bancario</div>

        <div id="info-payment-method-wire_transfer" class="js-info-payment-method-container card p-3">
                            <div class="mb-2">
                                    <img src="//dcdn-us.mitiendanube.com/assets/themes/recife/static/images/empty-placeholder.png" data-src="//d26lpennugtm8s.cloudfront.net/assets/common/img/logos/payment/new_logos_payment/custom@2x.png" class="lazyload card-img card-img-medium" alt="">
                            </div>
        
                <div class="js-payment-method-discount mb-3" style="">
            <span><strong>10% de descuento</strong> pagando con Transferencia o depósito bancario</span>
        </div>

                <div class="js-payment-method-total">
            <span class="font-big mb-3">
                                    <span>Total: </span>
                
                <span style="" class="js-installments-one-payment js-compare-price-display price-compare">$47.000,00</span>
                <strong style="" class="js-price-with-discount" data-payment-discount="10">$42.300,00</strong>

                                <strong class="js-installments-one-payment js-installments-no-discount" style="display: none">$47.000,00</strong>
            </span>
                    </div>

        <div class="js-discount-explanation font-small mt-1" style="">El descuento se aplicará sobre el precio del producto (sin envío) al elegir el medio de pago.</div>

                
        <div class="js-discount-disclaimer font-small opacity-60 mt-1" style="display: none">No acumulable con otras promociones</div>

                    </div>
                    

        <div class="js-payment-method-title js-payment-method-title-other h6 font-body mb-3">A convenir</div>

        <div id="info-payment-method-other" class="js-info-payment-method-container card p-3">
                            <div class="mb-2">
                                    <img src="//dcdn-us.mitiendanube.com/assets/themes/recife/static/images/empty-placeholder.png" data-src="//d26lpennugtm8s.cloudfront.net/assets/common/img/logos/payment/new_logos_payment/other@2x.png" class="lazyload card-img card-img-medium" alt="">
                            </div>
        
                <div class="js-payment-method-discount mb-3" style="display: none">
            <span><strong>0% de descuento</strong> pagando con A convenir</span>
        </div>

                <div class="js-payment-method-total">
            <span class="font-big mb-3">
                                    <span>Total: </span>
                
                <span style="display: none" class="js-installments-one-payment js-compare-price-display price-compare">$47.000,00</span>
                <strong style="display: none" class="js-price-with-discount" data-payment-discount="0">$47.000,00</strong>

                                <strong class="js-installments-one-payment js-installments-no-discount" style="">$47.000,00</strong>
            </span>
                    </div>

        <div class="js-discount-explanation font-small mt-1" style="display: none">El descuento se aplicará sobre el precio del producto (sin envío) al elegir el medio de pago.</div>

                
        <div class="js-discount-disclaimer font-small opacity-60 mt-1" style="display: none">No acumulable con otras promociones</div>

                    </div>
                        

                            </div>
            </div>
</div>
            </div>

                        </div>
                    <div class="modal-footer text-right d-md-block ">
                            <div class="text-right">
                <span class="js-modal-close js-fullscreen-modal-close btn-link pull-right">Volver al producto</span>
            </div>
                    </div>
            
    </div>
<div class="js-modal-overlay modal-overlay " data-modal-id="#installments-modal" style="display: none;"></div>

                                    <div class=" px-md-3 pb-md-4" data-store="product-description-{{ $producto->id }}">

            <h6 class="font-body mb-4">Descripción</h6>
        <div class="user-content font-small mb-4">
            {!! $producto->descripcion ?: '<p>No hay descripción disponible para este producto.</p>' !!}
        </div>
    
        <div id="reviewsapp"></div>
    
    
<div class="social-share ">
	<span class="position-relative d-none d-md-block">
		<span class="js-tooltip-open font-small">
			<svg class="icon-inline icon-lg svg-icon-text mr-1"><use xlink:href="#share"></use></svg>
			<span class="btn-link">Compartir</span>
		</span>
		<div class="js-tooltip tooltip" style="display: none;">
			<span class="tooltip-arrow tooltip-arrow-up"></span>
			

@php
	$productUrl = route('tienda.producto', [$empresa->slug, $producto->id]);
	$productUrlEncoded = urlencode($productUrl);
	$productTitle = urlencode($producto->nombre);
	$productImage = urlencode($producto->url_imagen_principal ?? asset('assets/img/product/placeholder.webp'));
@endphp

<a class="social-share-button d-inline-block d-md-none" data-network="whatsapp" target="_blank" href="whatsapp://send?text={{ $productUrl }}" title="Compartir en WhatsApp" aria-label="Compartir en WhatsApp">
	<svg class="icon-inline font-big svg-icon-text"><use xlink:href="#whatsapp"></use></svg>
</a>

<a class="social-share-button" data-network="facebook" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u={{ $productUrlEncoded }}" title="Compartir en Facebook" aria-label="Compartir en Facebook">
	<svg class="icon-inline font-big svg-icon-text"><use xlink:href="#facebook-f"></use></svg>
</a>

<a class="social-share-button" data-network="twitter" target="_blank" href="https://twitter.com/share?url={{ $productUrlEncoded }}&text={{ $productTitle }}" title="Compartir en Twitter" aria-label="Compartir en Twitter">
	<svg class="icon-inline font-big svg-icon-text"><use xlink:href="#twitter"></use></svg>
</a>

<a class="js-pinterest-share social-share-button" data-network="pinterest" target="_blank" href="http://pinterest.com/pin/create/button/?url={{ $productUrlEncoded }}&media={{ $productImage }}&description={{ $productTitle }}" title="Compartir en Pinterest" aria-label="Compartir en Pinterest">
	<svg class="icon-inline font-big svg-icon-text"><use xlink:href="#pinterest"></use></svg>
</a>
			<a class="js-tooltip-close ml-2" href="#">
				<svg class="icon-inline svg-icon-text"><use xlink:href="#times"></use></svg>
			</a>
		</div>
	</span>

	<a data-toggle="#social-share-modal" data-modal-url="modal-fullscreen-social-share" class="js-modal-open mb-3 d-md-none font-small">
		<svg class="icon-inline icon-lg svg-icon-text mr-1"><use xlink:href="#share"></use></svg>
		<span class="btn-link">Compartir</span>
	</a>

	

<div id="social-share-modal" class="js-modal js-fullscreen-modal  modal modal-bottom-sheet modal-bottom modal--md transition-slide modal-centered transition-soft " style="display: none;" data-modal-url="">
                        <div class="js-modal-close js-fullscreen-modal-close  modal-header  ">
                                            <div class="row no-gutters align-items-center">
                            <div class="col p-3">
                                			Compartir
		                            </div>
                            <div class="col-auto">
                                <a class="js-modal-close modal-close ">
                                    <svg class="icon-inline svg-icon-text"><use xlink:href="#times"></use></svg>
                                </a>
                            </div>
                        </div>
                                    </div>
                <div class="modal-body ">
                    			

<a class="social-share-button d-inline-block d-md-none" data-network="whatsapp" target="_blank" href="whatsapp://send?text={{ $productUrl }}" title="Compartir en WhatsApp" aria-label="Compartir en WhatsApp">
	<svg class="icon-inline social-share-button-icon svg-icon-text"><use xlink:href="#whatsapp"></use></svg>
</a>

<a class="social-share-button" data-network="facebook" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u={{ $productUrlEncoded }}" title="Compartir en Facebook" aria-label="Compartir en Facebook">
	<svg class="icon-inline social-share-button-icon svg-icon-text"><use xlink:href="#facebook-f"></use></svg>
</a>

<a class="social-share-button" data-network="twitter" target="_blank" href="https://twitter.com/share?url={{ $productUrlEncoded }}&text={{ $productTitle }}" title="Compartir en Twitter" aria-label="Compartir en Twitter">
	<svg class="icon-inline social-share-button-icon svg-icon-text"><use xlink:href="#twitter"></use></svg>
</a>

<a class="js-pinterest-share social-share-button" data-network="pinterest" target="_blank" href="http://pinterest.com/pin/create/button/?url={{ $productUrlEncoded }}&media={{ $productImage }}&description={{ $productTitle }}" title="Compartir en Pinterest" aria-label="Compartir en Pinterest">
	<svg class="icon-inline social-share-button-icon svg-icon-text"><use xlink:href="#pinterest"></use></svg>
</a>
		                </div>
                    <div class="modal-footer text-right d-md-block ">
                            </div>
            
    </div>
<div class="js-modal-overlay modal-overlay " data-modal-id="#social-share-modal" style="display: none;"></div>
</div>
</div>
                            </div>
        </div>
    </div>

    
    </div>


                
                
                                                                                                                                                                                                                                                                                                                
            






    


<!-- Nueva sección de productos relacionados -->
<section class="recife-related-products" style="padding: 60px 0; background: #f9f9f9;">
	<div class="container">
		<h2 style="font-size: 28px; font-weight: 600; margin-bottom: 30px; text-align: center;">Productos relacionados</h2>

		<div class="recife-related-slider">
			<div class="recife-related-slides">
				<!-- Producto 1 -->
			@forelse($relacionados as $relacionado)
				@php
					// Buscar descuentos activos para producto relacionado
					$descuentoRelacionado = null;
					$textoDescuentoRel = null;
					$precioActualRel = $relacionado->precio_actual;
					$precioConDescuentoRel = $precioActualRel;

					if (isset($descuentosActivos) && $precioActualRel) {
						foreach ($descuentosActivos as $desc) {
							$aplica = false;
							if ($desc->aplica_a === 'orden' || $desc->aplica_a === 'carrito') {
								$aplica = true;
							} elseif ($desc->aplica_a === 'producto' && in_array($relacionado->id, $desc->productos_aplicables ?? [])) {
								$aplica = true;
							} elseif ($desc->aplica_a === 'categoria' && in_array($relacionado->categoria_id, $desc->categorias_aplicables ?? [])) {
								$aplica = true;
							}

							if ($aplica) {
								$descuentoRelacionado = $desc;
								if ($desc->tipo === 'porcentaje') {
									$montoDescRel = ($precioActualRel * $desc->valor) / 100;
									$textoDescuentoRel = round($desc->valor) . '% OFF';
								} else {
									$montoDescRel = $desc->valor;
									$textoDescuentoRel = '$' . number_format($desc->valor, 0, ',', '.') . ' OFF';
								}
								$precioConDescuentoRel = $precioActualRel - $montoDescRel;
								break;
							}
						}
					}
				@endphp
				<div class="recife-related-product">
					<a href="{{ route('tienda.producto', [$empresa->slug, $relacionado->id]) }}" class="recife-product-card">
						<div class="recife-product-image">
							<img src="{{ $relacionado->url_imagen_principal ?? asset('assets/img/product/placeholder.webp') }}" alt="{{ $relacionado->nombre }}">
							@if($descuentoRelacionado)
								<span class="recife-product-badge" style="position: absolute; top: 10px; right: 10px; background: #e74c3c; color: white; padding: 5px 10px; border-radius: 4px; font-size: 12px; font-weight: 600;">{{ $textoDescuentoRel }}</span>
							@endif
						</div>
						<div class="recife-product-info">
							<h3>{{ Str::limit($relacionado->nombre, 50) }}</h3>
							@if($precioActualRel)
								@if($descuentoRelacionado)
									<p class="recife-product-price">
										<span style="text-decoration: line-through; color: #999; font-size: 14px; margin-right: 8px;">${{ number_format($precioActualRel, 0, ',', '.') }}</span>
										<span style="color: #e74c3c; font-weight: 700;">${{ number_format($precioConDescuentoRel, 0, ',', '.') }}</span>
									</p>
								@else
									<p class="recife-product-price">${{ number_format($precioActualRel, 0, ',', '.') }}</p>
								@endif
							@else
								<p class="recife-product-price">Precio no disponible</p>
							@endif
						</div>
					</a>
				</div>
			@empty
				<div class="col-12 text-center py-5">
					<p class="text-muted">No hay productos relacionados disponibles.</p>
				</div>
			@endforelse
			</div>

			<!-- Flechas de navegación -->
			<button class="recife-related-arrow recife-related-prev" onclick="recifeRelatedPrev()">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
					<polyline points="15 18 9 12 15 6"></polyline>
				</svg>
			</button>
			<button class="recife-related-arrow recife-related-next" onclick="recifeRelatedNext()">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
					<polyline points="9 18 15 12 9 6"></polyline>
				</svg>
			</button>
		</div>
	</div>

	<style>
		.recife-related-products {
			overflow: hidden;
		}
		.recife-related-slider {
			position: relative;
			max-width: 1400px;
			margin: 0 auto;
		}
		.recife-related-slides {
			display: flex;
			gap: 20px;
			overflow-x: auto;
			scroll-behavior: smooth;
			padding: 20px 10px;
			scrollbar-width: none; /* Firefox */
			-ms-overflow-style: none; /* IE/Edge */
		}
		.recife-related-slides::-webkit-scrollbar {
			display: none; /* Chrome/Safari */
		}
		.recife-related-product {
			flex: 0 0 calc(25% - 15px);
			min-width: 240px;
		}
		.recife-product-card {
			display: block;
			background: white;
			border-radius: 8px;
			overflow: hidden;
			text-decoration: none;
			color: inherit;
			transition: transform 0.3s, box-shadow 0.3s;
			box-shadow: 0 2px 8px rgba(0,0,0,0.1);
		}
		.recife-product-card:hover {
			transform: translateY(-5px);
			box-shadow: 0 4px 16px rgba(0,0,0,0.15);
		}
		.recife-product-image {
			position: relative;
			padding-bottom: 100%;
			overflow: hidden;
			background: #f5f5f5;
		}
		.recife-product-image img {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			object-fit: cover;
		}
		.recife-product-info {
			padding: 15px;
		}
		.recife-product-info h3 {
			font-size: 16px;
			font-weight: 500;
			margin: 0 0 8px 0;
			color: #333;
			overflow: hidden;
			text-overflow: ellipsis;
			white-space: nowrap;
		}
		.recife-product-price {
			font-size: 18px;
			font-weight: 600;
			color: #2d7dd2;
			margin: 0;
		}
		.recife-related-arrow {
			position: absolute;
			top: 50%;
			transform: translateY(-50%);
			background: white;
			border: none;
			border-radius: 50%;
			width: 48px;
			height: 48px;
			display: flex;
			align-items: center;
			justify-content: center;
			cursor: pointer;
			box-shadow: 0 2px 8px rgba(0,0,0,0.15);
			transition: all 0.3s;
			z-index: 10;
		}
		.recife-related-arrow:hover {
			background: #f0f0f0;
			box-shadow: 0 4px 12px rgba(0,0,0,0.2);
		}
		.recife-related-arrow svg {
			color: #333;
		}
		.recife-related-prev {
			left: -20px;
		}
		.recife-related-next {
			right: -20px;
		}

		@media (max-width: 1024px) {
			.recife-related-product {
				flex: 0 0 calc(33.333% - 14px);
			}
		}
		@media (max-width: 768px) {
			.recife-related-product {
				flex: 0 0 calc(50% - 10px);
				min-width: 180px;
			}
			.recife-related-arrow {
				display: none;
			}
		}
	</style>

	<script>
		function recifeRelatedScroll(direction) {
			const container = document.querySelector('.recife-related-slides');
			const scrollAmount = container.offsetWidth * 0.8;
			container.scrollLeft += direction === 'next' ? scrollAmount : -scrollAmount;
		}

		function recifeRelatedNext() {
			recifeRelatedScroll('next');
		}

		function recifeRelatedPrev() {
			recifeRelatedScroll('prev');
		}
	</script>

	<!-- Variant Selection Script -->
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			const variantButtons = document.querySelectorAll('.js-recife-variant');
			const varianteIdInput = document.getElementById('variante_id');
			const variantLabel = document.querySelector('.js-selected-variant-label');

			if (variantButtons.length > 0 && varianteIdInput) {
				// Set first variant as selected by default
				const firstButton = variantButtons[0];
				if (firstButton && variantLabel) {
					variantLabel.textContent = firstButton.dataset.varianteName || 'Seleccionada';
				}

				// Handle variant button clicks
				variantButtons.forEach(button => {
					button.addEventListener('click', function(e) {
						e.preventDefault();

						// Check if button is disabled
						if (this.classList.contains('disabled')) {
							alert('Esta variante no tiene stock disponible');
							return;
						}

						// Remove selected class from all buttons
						variantButtons.forEach(btn => btn.classList.remove('selected'));

						// Add selected class to clicked button
						this.classList.add('selected');

						// Update hidden input
						const varianteId = this.dataset.varianteId;
						if (varianteId && varianteIdInput) {
							varianteIdInput.value = varianteId;
						}

						// Update label
						if (variantLabel) {
							variantLabel.textContent = this.dataset.varianteName || 'Seleccionada';
						}
					});
				});
			}

			// Handle form submission via AJAX
			const productForm = document.getElementById('product_form');
			const submitButton = productForm ? productForm.querySelector('input[type="submit"]') : null;

			if (productForm && submitButton) {
				// Remove linkedstore event handlers
				const newSubmitButton = submitButton.cloneNode(true);
				submitButton.parentNode.replaceChild(newSubmitButton, submitButton);

				productForm.addEventListener('submit', function(e) {
					e.preventDefault();
					e.stopPropagation();

					// Get form data
					const formData = new FormData(productForm);

					// Show loading state
					newSubmitButton.disabled = true;
					newSubmitButton.value = 'Agregando...';

					// Send AJAX request
					fetch(productForm.action, {
						method: 'POST',
						body: formData,
						headers: {
							'X-Requested-With': 'XMLHttpRequest',
						}
					})
					.then(response => response.json())
					.then(data => {
						if (data.success) {
							// Show success message
							newSubmitButton.value = '¡Agregado!';
							newSubmitButton.classList.add('btn-success');

							// Update cart count if exists
                            const cartCount = document.querySelector('.js-cart-widget-amount');
                            if (cartCount && typeof data.total_items !== 'undefined') {
                                cartCount.textContent = data.total_items;
                            }


							// Reset button after 2 seconds
							setTimeout(() => {
								newSubmitButton.disabled = false;
								newSubmitButton.value = 'Agregar al carrito';
								newSubmitButton.classList.remove('btn-success');
							}, 2000);
						} else {
							// Show error
							alert(data.message || data.error || 'Ocurrió un error al agregar el producto');
							newSubmitButton.disabled = false;
							newSubmitButton.value = 'Agregar al carrito';
						}
					})
					.catch(error => {
						console.error('Error:', error);
						alert('Ocurrió un error al agregar el producto al carrito');
						newSubmitButton.disabled = false;
						newSubmitButton.value = 'Agregar al carrito';
					});
				});
			}
		});
	</script>
</section>

@endsection
