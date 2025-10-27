@extends('landing_page.layout')

@section('content')

    <section id="pricing-calculator" style="padding: 160px 0 100px 0;" class="section">

      <!-- Section Title -->
      <div class="container section-title">
        <h2>Pricing Calculator</h2>
        <p>Get an instant quote for your cleaning needs</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row">

          <!-- Calculator Form -->
          <div class="col-lg-8">
            <div class="card shadow-sm">
              <div class="card-body p-4">

                <!-- Step 1: Square Footage -->
                <div class="mb-4">
                  <h4 class="mb-3">1. Select Your Square Footage</h4>
                  <select id="square-footage" class="form-select form-select-lg">
                    <option value="">Choose your square footage...</option>
                    @foreach($pricingRanges as $range)
                      <option value="{{ $range->id }}"
                              data-sq-min="{{ $range->sq_ft_min }}"
                              data-sq-max="{{ $range->sq_ft_max }}"
                              data-initial="{{ $range->initial_clean }}"
                              data-weekly="{{ $range->weekly }}"
                              data-biweekly="{{ $range->biweekly }}"
                              data-monthly="{{ $range->monthly }}"
                              data-deep="{{ $range->deep_clean }}"
                              data-moveout="{{ $range->move_out_clean }}">
                        {{ $range->sq_ft_min }} - {{ $range->sq_ft_max }} sq ft
                      </option>
                    @endforeach
                    <option value="custom">More than 5,000 sq ft (Contact Us)</option>
                  </select>
                </div>

                <div id="services-section" style="display: none;">

                  <!-- Step 2: Service Type -->
                  <div class="mb-4">
                    <h4 class="mb-3">2. Select Service Type</h4>
                    <div class="row g-3">

                      <!-- Recurring Services -->
                      <div class="col-md-6">
                        <div class="service-type-card">
                          <h5 class="mb-3">Recurring Service</h5>
                          <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="serviceType" id="initial" value="initial">
                            <label class="form-check-label" for="initial">
                              Initial Clean - <span class="price-display" id="price-initial">$0.00</span>
                            </label>
                          </div>
                          <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="serviceType" id="weekly" value="weekly">
                            <label class="form-check-label" for="weekly">
                              Weekly (20% discount) - <span class="price-display" id="price-weekly">$0.00</span>
                            </label>
                          </div>
                          <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="serviceType" id="biweekly" value="biweekly">
                            <label class="form-check-label" for="biweekly">
                              Bi-Weekly (15% discount) - <span class="price-display" id="price-biweekly">$0.00</span>
                            </label>
                          </div>
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="serviceType" id="monthly" value="monthly">
                            <label class="form-check-label" for="monthly">
                              Monthly - <span class="price-display" id="price-monthly">$0.00</span>
                            </label>
                          </div>
                        </div>
                      </div>

                      <!-- One-Time Services -->
                      <div class="col-md-6">
                        <div class="service-type-card">
                          <h5 class="mb-3">One-Time Service</h5>
                          <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="serviceType" id="deep-clean" value="deep_clean">
                            <label class="form-check-label" for="deep-clean">
                              Deep Clean - <span class="price-display" id="price-deep">$0.00</span>
                            </label>
                          </div>
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="serviceType" id="move-out" value="move_out">
                            <label class="form-check-label" for="move-out">
                              Move Out Clean - <span class="price-display" id="price-moveout">$0.00</span>
                            </label>
                          </div>
                        </div>
                      </div>

                    </div>
                  </div>

                  <!-- Step 3: Extra Services -->
                  <div class="mb-4">
                    <h4 class="mb-3">3. Add Extra Services (Optional)</h4>
                    <div class="row g-3">

                      <div class="col-md-6">
                        <div class="form-check">
                          <input class="form-check-input extra-service" type="checkbox" id="extra-heavy"
                                 data-price="{{ $pricingConfig->extra_heavy_duty }}">
                          <label class="form-check-label" for="extra-heavy">
                            Extra Heavy Duty (+${{ $pricingConfig->extra_heavy_duty }})
                          </label>
                        </div>
                      </div>

                      <div class="col-md-6">
                        <div class="form-check">
                          <input class="form-check-input extra-service" type="checkbox" id="inside-fridge"
                                 data-price="{{ $pricingConfig->inside_fridge_ea }}">
                          <label class="form-check-label" for="inside-fridge">
                            Inside Fridge (+${{ $pricingConfig->inside_fridge_ea }})
                          </label>
                        </div>
                      </div>

                      <div class="col-md-6">
                        <div class="form-check">
                          <input class="form-check-input extra-service" type="checkbox" id="inside-oven"
                                 data-price="{{ $pricingConfig->inside_oven_ea }}">
                          <label class="form-check-label" for="inside-oven">
                            Inside Oven (+${{ $pricingConfig->inside_oven_ea }})
                          </label>
                        </div>
                      </div>

                      <div class="col-md-6">
                        <div class="d-flex align-items-center">
                          <div class="form-check flex-grow-1">
                            <input class="form-check-input extra-service-sqft" type="checkbox" id="post-const-gov">
                            <label class="form-check-label" for="post-const-gov">
                              Post-Construction Government
                            </label>
                          </div>
                          <input type="number" class="form-control form-control-sm ms-2" style="width: 100px;"
                                 id="post-const-gov-sqft" placeholder="Sq Ft" min="0" disabled
                                 data-price-per-sqft="{{ $pricingConfig->post_construction_government }}">
                        </div>
                      </div>

                      <div class="col-md-6">
                        <div class="d-flex align-items-center">
                          <div class="form-check flex-grow-1">
                            <input class="form-check-input extra-service-sqft" type="checkbox" id="post-const-priv">
                            <label class="form-check-label" for="post-const-priv">
                              Post-Construction Private
                            </label>
                          </div>
                          <input type="number" class="form-control form-control-sm ms-2" style="width: 100px;"
                                 id="post-const-priv-sqft" placeholder="Sq Ft" min="0" disabled
                                 data-price-per-sqft="{{ $pricingConfig->post_construction_private }}">
                        </div>
                      </div>

                      <div class="col-md-6">
                        <div class="d-flex align-items-center">
                          <div class="form-check flex-grow-1">
                            <input class="form-check-input extra-service-panes" type="checkbox" id="window-interior">
                            <label class="form-check-label" for="window-interior">
                              Window Clean (Interior)
                            </label>
                          </div>
                          <input type="number" class="form-control form-control-sm ms-2" style="width: 100px;"
                                 id="window-interior-panes" placeholder="Panes" min="0" disabled
                                 data-price-per-pane="{{ $pricingConfig->window_clean_interior }}">
                        </div>
                      </div>

                      <div class="col-md-6">
                        <div class="d-flex align-items-center">
                          <div class="form-check flex-grow-1">
                            <input class="form-check-input extra-service-panes" type="checkbox" id="window-exterior">
                            <label class="form-check-label" for="window-exterior">
                              Window Clean (Exterior)
                            </label>
                          </div>
                          <input type="number" class="form-control form-control-sm ms-2" style="width: 100px;"
                                 id="window-exterior-panes" placeholder="Panes" min="0" disabled
                                 data-price-per-pane="{{ $pricingConfig->window_clean_exterior }}">
                        </div>
                      </div>

                    </div>
                  </div>

                  <!-- Step 4: Contact Info -->
                  <div class="mb-4" id="contact-section" style="display: none;">
                    <h4 class="mb-3">4. Your Information</h4>
                    <div class="row g-3">
                      <div class="col-md-6">
                        <input type="text" class="form-control" id="customer-name" placeholder="First Name" required>
                      </div>
                      <div class="col-md-6">
                        <input type="text" class="form-control" id="customer-lastname" placeholder="Last Name" required>
                      </div>
                      <div class="col-md-6">
                        <input type="email" class="form-control" id="customer-email" placeholder="Email" required>
                      </div>
                      <div class="col-md-6">
                        <input type="tel" class="form-control" id="customer-phone" placeholder="Phone" required>
                      </div>
                    </div>
                  </div>

                </div>

              </div>
            </div>
          </div>

          <!-- Summary Card -->
          <div class="col-lg-4">
            <div class="card shadow-sm sticky-top" style="top: 100px;">
              <div class="card-header text-white" style="background-color: var(--accent-color);">
                <h5 class="mb-0">Quote Summary</h5>
              </div>
              <div class="card-body">

                <div id="summary-empty" class="text-center text-muted py-4">
                  <i class="bi bi-calculator" style="font-size: 3rem;"></i>
                  <p class="mt-3">Select your options to see pricing</p>
                </div>

                <div id="summary-content" style="display: none;">

                  <div class="mb-3">
                    <small class="text-muted">Square Footage</small>
                    <div id="summary-sqft" class="fw-bold">-</div>
                  </div>

                  <div class="mb-3">
                    <small class="text-muted">Service Type</small>
                    <div id="summary-service" class="fw-bold">-</div>
                    <div id="summary-service-price" class="text-primary">$0.00</div>
                  </div>

                  <div id="summary-extras" style="display: none;" class="mb-3">
                    <small class="text-muted">Extra Services</small>
                    <div id="summary-extras-list"></div>
                    <div id="summary-extras-price" class="text-primary">+$0.00</div>
                  </div>

                  <hr>

                  <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Total</h5>
                    <h4 class="mb-0 text-primary" id="total-price">$0.00</h4>
                  </div>

                  <button type="button" class="btn-primary w-100 mb-2" id="send-whatsapp-btn" style="display: block; text-align: center;">
                    <i class="bi bi-whatsapp"></i> Send to WhatsApp
                  </button>

                  <small class="text-muted d-block text-center">
                    <i class="bi bi-info-circle"></i> Final pricing may vary based on condition
                  </small>

                </div>

              </div>
            </div>
          </div>

        </div>

      </div>

    </section>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const squareFootageSelect = document.getElementById('square-footage');
    const servicesSection = document.getElementById('services-section');
    const contactSection = document.getElementById('contact-section');
    const summaryEmpty = document.getElementById('summary-empty');
    const summaryContent = document.getElementById('summary-content');
    const sendWhatsappBtn = document.getElementById('send-whatsapp-btn');

    let currentRange = null;
    let basePrice = 0;
    let extrasTotal = 0;
    let selectedService = null;

    // Square footage selection
    squareFootageSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];

        if (this.value === 'custom') {
            alert('For properties over 5,000 sq ft, please contact us directly for a custom quote.');
            servicesSection.style.display = 'none';
            return;
        }

        if (this.value) {
            currentRange = {
                min: selectedOption.dataset.sqMin,
                max: selectedOption.dataset.sqMax,
                initial: parseFloat(selectedOption.dataset.initial),
                weekly: parseFloat(selectedOption.dataset.weekly),
                biweekly: parseFloat(selectedOption.dataset.biweekly),
                monthly: parseFloat(selectedOption.dataset.monthly),
                deep: parseFloat(selectedOption.dataset.deep),
                moveout: parseFloat(selectedOption.dataset.moveout)
            };

            updatePriceDisplays();
            servicesSection.style.display = 'block';
        } else {
            servicesSection.style.display = 'none';
            resetCalculator();
        }
    });

    function updatePriceDisplays() {
        document.getElementById('price-initial').textContent = '$' + currentRange.initial.toFixed(2);
        document.getElementById('price-weekly').textContent = '$' + currentRange.weekly.toFixed(2);
        document.getElementById('price-biweekly').textContent = '$' + currentRange.biweekly.toFixed(2);
        document.getElementById('price-monthly').textContent = '$' + currentRange.monthly.toFixed(2);
        document.getElementById('price-deep').textContent = '$' + currentRange.deep.toFixed(2);
        document.getElementById('price-moveout').textContent = '$' + currentRange.moveout.toFixed(2);
    }

    // Service type selection
    document.querySelectorAll('input[name="serviceType"]').forEach(radio => {
        radio.addEventListener('change', function() {
            selectedService = this.value;

            switch(this.value) {
                case 'initial': basePrice = currentRange.initial; break;
                case 'weekly': basePrice = currentRange.weekly; break;
                case 'biweekly': basePrice = currentRange.biweekly; break;
                case 'monthly': basePrice = currentRange.monthly; break;
                case 'deep_clean': basePrice = currentRange.deep; break;
                case 'move_out': basePrice = currentRange.moveout; break;
            }

            updateSummary();
            contactSection.style.display = 'block';
        });
    });

    // Extra services
    document.querySelectorAll('.extra-service').forEach(checkbox => {
        checkbox.addEventListener('change', calculateExtras);
    });

    document.querySelectorAll('.extra-service-sqft').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const inputId = this.id + '-sqft';
            document.getElementById(inputId).disabled = !this.checked;
            if (!this.checked) document.getElementById(inputId).value = '';
            calculateExtras();
        });
    });

    document.querySelectorAll('.extra-service-panes').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const inputId = this.id + '-panes';
            document.getElementById(inputId).disabled = !this.checked;
            if (!this.checked) document.getElementById(inputId).value = '';
            calculateExtras();
        });
    });

    document.querySelectorAll('input[type="number"]').forEach(input => {
        input.addEventListener('input', calculateExtras);
    });

    function calculateExtras() {
        extrasTotal = 0;

        // Simple checkboxes
        document.querySelectorAll('.extra-service:checked').forEach(checkbox => {
            extrasTotal += parseFloat(checkbox.dataset.price);
        });

        // Square footage based
        const postConstGov = document.getElementById('post-const-gov');
        if (postConstGov.checked) {
            const sqft = parseFloat(document.getElementById('post-const-gov-sqft').value) || 0;
            const pricePerSqft = parseFloat(document.getElementById('post-const-gov-sqft').dataset.pricePerSqft);
            extrasTotal += sqft * pricePerSqft;
        }

        const postConstPriv = document.getElementById('post-const-priv');
        if (postConstPriv.checked) {
            const sqft = parseFloat(document.getElementById('post-const-priv-sqft').value) || 0;
            const pricePerSqft = parseFloat(document.getElementById('post-const-priv-sqft').dataset.pricePerSqft);
            extrasTotal += sqft * pricePerSqft;
        }

        // Window panes
        const windowInt = document.getElementById('window-interior');
        if (windowInt.checked) {
            const panes = parseFloat(document.getElementById('window-interior-panes').value) || 0;
            const pricePerPane = parseFloat(document.getElementById('window-interior-panes').dataset.pricePerPane);
            extrasTotal += panes * pricePerPane;
        }

        const windowExt = document.getElementById('window-exterior');
        if (windowExt.checked) {
            const panes = parseFloat(document.getElementById('window-exterior-panes').value) || 0;
            const pricePerPane = parseFloat(document.getElementById('window-exterior-panes').dataset.pricePerPane);
            extrasTotal += panes * pricePerPane;
        }

        updateSummary();
    }

    function updateSummary() {
        if (!selectedService) {
            summaryEmpty.style.display = 'block';
            summaryContent.style.display = 'none';
            return;
        }

        summaryEmpty.style.display = 'none';
        summaryContent.style.display = 'block';

        // Square footage
        document.getElementById('summary-sqft').textContent = currentRange.min + ' - ' + currentRange.max + ' sq ft';

        // Service type
        const serviceName = document.querySelector('input[name="serviceType"]:checked').parentElement.textContent.split('-')[0].trim();
        document.getElementById('summary-service').textContent = serviceName;
        document.getElementById('summary-service-price').textContent = '$' + basePrice.toFixed(2);

        // Extras
        const extrasList = [];
        document.querySelectorAll('.extra-service:checked').forEach(checkbox => {
            extrasList.push(checkbox.parentElement.textContent.trim());
        });

        if (document.getElementById('post-const-gov').checked) {
            const sqft = document.getElementById('post-const-gov-sqft').value || 0;
            extrasList.push(`Post-Construction Government (${sqft} sq ft)`);
        }

        if (document.getElementById('post-const-priv').checked) {
            const sqft = document.getElementById('post-const-priv-sqft').value || 0;
            extrasList.push(`Post-Construction Private (${sqft} sq ft)`);
        }

        if (document.getElementById('window-interior').checked) {
            const panes = document.getElementById('window-interior-panes').value || 0;
            extrasList.push(`Window Clean Interior (${panes} panes)`);
        }

        if (document.getElementById('window-exterior').checked) {
            const panes = document.getElementById('window-exterior-panes').value || 0;
            extrasList.push(`Window Clean Exterior (${panes} panes)`);
        }

        if (extrasList.length > 0) {
            document.getElementById('summary-extras').style.display = 'block';
            document.getElementById('summary-extras-list').innerHTML = extrasList.map(e => '<small>• ' + e + '</small>').join('<br>');
            document.getElementById('summary-extras-price').textContent = '+$' + extrasTotal.toFixed(2);
        } else {
            document.getElementById('summary-extras').style.display = 'none';
        }

        // Total
        const total = basePrice + extrasTotal;
        document.getElementById('total-price').textContent = '$' + total.toFixed(2);
    }

    // Send to WhatsApp
    sendWhatsappBtn.addEventListener('click', function() {
        const name = document.getElementById('customer-name').value.trim();
        const lastname = document.getElementById('customer-lastname').value.trim();
        const email = document.getElementById('customer-email').value.trim();
        const phone = document.getElementById('customer-phone').value.trim();

        if (!name || !lastname || !email || !phone) {
            alert('Please fill in all your contact information');
            return;
        }

        // Build message
        let message = `*New Quote Request*\n\n`;
        message += `*Customer Info:*\n`;
        message += `Name: ${name} ${lastname}\n`;
        message += `Email: ${email}\n`;
        message += `Phone: ${phone}\n\n`;
        message += `*Service Details:*\n`;
        message += `Square Footage: ${currentRange.min} - ${currentRange.max} sq ft\n`;
        message += `Service: ${document.getElementById('summary-service').textContent}\n`;
        message += `Base Price: $${basePrice.toFixed(2)}\n`;

        if (extrasTotal > 0) {
            message += `\n*Extra Services:*\n`;
            const extrasText = document.getElementById('summary-extras-list').textContent;
            message += extrasText + `\n`;
            message += `Extras Total: +$${extrasTotal.toFixed(2)}\n`;
        }

        message += `\n*TOTAL: $${(basePrice + extrasTotal).toFixed(2)}*`;

        const whatsappNumber = '{{ $pricingConfig->whatsapp_number }}'.replace(/\D/g, '');
        const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(message)}`;
        window.open(whatsappUrl, '_blank');
    });

    function resetCalculator() {
        currentRange = null;
        basePrice = 0;
        extrasTotal = 0;
        selectedService = null;
        summaryEmpty.style.display = 'block';
        summaryContent.style.display = 'none';
        contactSection.style.display = 'none';
    }
});
</script>

<style>
.service-type-card {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 1rem;
    height: 100%;
}

.price-display {
    font-weight: 600;
    color: var(--accent-color);
}

.form-check-input:checked ~ .form-check-label {
    font-weight: 600;
}

.sticky-top {
    position: sticky;
}

#summary-extras-list small {
    display: block;
    margin-bottom: 0.25rem;
}
</style>
@endpush
