@extends('landing_page.layout')

@section('content')

    <section id="pricing-calculator" style="padding: 160px 0 100px 0;" class="section">

      <!-- Section Title -->
      <div class="container section-title">
        <h2>Get Your Quote</h2>
        <p>Complete the steps below to receive an instant quote</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row">

          <!-- Multi-Step Form -->
          <div class="col-lg-8">
            <div class="card shadow-sm">
              <div class="card-body p-4">

                <!-- Progress Indicator -->
                <div class="mb-4">
                  <div class="d-flex justify-content-between mb-2">
                    <small class="text-muted">Step <span id="current-step-num">1</span> of 9</small>
                    <small class="text-muted"><span id="progress-percentage">11</span>% Complete</small>
                  </div>
                  <div class="progress" style="height: 8px;">
                    <div class="progress-bar" id="progress-bar" role="progressbar" style="width: 11%; background-color: var(--accent-color);" aria-valuenow="11" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                </div>

                <!-- Step 1: Personal Information -->
                <div class="calculator-step" id="step-1">
                  <h4 class="mb-3">Personal Information</h4>
                  <p class="text-muted mb-4">Let's start with your contact details</p>
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label for="first-name" class="form-label">First Name <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="first-name" required>
                    </div>
                    <div class="col-md-6">
                      <label for="last-name" class="form-label">Last Name <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="last-name" required>
                    </div>
                    <div class="col-md-6">
                      <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                      <input type="email" class="form-control" id="email" required>
                    </div>
                    <div class="col-md-6">
                      <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                      <input type="tel" class="form-control" id="phone" required>
                    </div>
                  </div>
                </div>

                <!-- Step 2: Service Location -->
                <div class="calculator-step" id="step-2" style="display: none;">
                  <h4 class="mb-3">Service Location</h4>
                  <p class="text-muted mb-4">Where should we provide the service?</p>
                  <div class="row g-3">
                    <div class="col-12">
                      <label for="street-address" class="form-label">Street Address <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="street-address" placeholder="123 Main Street" required>
                    </div>
                    <div class="col-md-6">
                      <label for="district" class="form-label">District/Suburb <span class="text-danger">*</span></label>
                      <select class="form-select" id="district" required>
                        <option value="">Select a district...</option>
                        @foreach($districts as $district)
                          <option value="{{ $district->id }}" data-state="{{ $district->state }}" data-postcode="{{ $district->postcode }}">
                            {{ $district->name }} ({{ $district->state }} {{ $district->postcode }})
                          </option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-md-6">
                      <label for="unit-apt" class="form-label">Unit/Apartment (Optional)</label>
                      <input type="text" class="form-control" id="unit-apt" placeholder="Unit 5B">
                    </div>
                  </div>
                </div>

                <!-- Step 3: Date & Time -->
                <div class="calculator-step" id="step-3" style="display: none;">
                  <h4 class="mb-3">Preferred Date & Time</h4>
                  <p class="text-muted mb-4">When would you like us to service your property?</p>
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label for="preferred-date" class="form-label">Preferred Date <span class="text-danger">*</span></label>
                      <input type="date" class="form-control" id="preferred-date" required>
                    </div>
                    <div class="col-md-6">
                      <label for="preferred-time" class="form-label">Preferred Time <span class="text-danger">*</span></label>
                      <select class="form-select" id="preferred-time" required>
                        <option value="">Select time...</option>
                        <option value="12:00 AM">12:00 AM</option>
                        <option value="1:00 AM">1:00 AM</option>
                        <option value="2:00 AM">2:00 AM</option>
                        <option value="3:00 AM">3:00 AM</option>
                        <option value="4:00 AM">4:00 AM</option>
                        <option value="5:00 AM">5:00 AM</option>
                        <option value="6:00 AM">6:00 AM</option>
                        <option value="7:00 AM">7:00 AM</option>
                        <option value="8:00 AM">8:00 AM</option>
                        <option value="9:00 AM">9:00 AM</option>
                        <option value="10:00 AM">10:00 AM</option>
                        <option value="11:00 AM">11:00 AM</option>
                        <option value="12:00 PM">12:00 PM</option>
                        <option value="1:00 PM">1:00 PM</option>
                        <option value="2:00 PM">2:00 PM</option>
                        <option value="3:00 PM">3:00 PM</option>
                        <option value="4:00 PM">4:00 PM</option>
                        <option value="5:00 PM">5:00 PM</option>
                        <option value="6:00 PM">6:00 PM</option>
                        <option value="7:00 PM">7:00 PM</option>
                        <option value="8:00 PM">8:00 PM</option>
                        <option value="9:00 PM">9:00 PM</option>
                        <option value="10:00 PM">10:00 PM</option>
                        <option value="11:00 PM">11:00 PM</option>
                      </select>
                    </div>
                  </div>
                </div>

                <!-- Step 4: Parking -->
                <div class="calculator-step" id="step-4" style="display: none;">
                  <h4 class="mb-3">Parking Arrangement</h4>
                  <p class="text-muted mb-4">Where will our staff park during the service?</p>
                  <select class="form-select form-select-lg" id="parking" required>
                    <option value="">Select parking option...</option>
                    <option value="Driveway">Driveway</option>
                    <option value="Street Parking">Street Parking</option>
                    <option value="Garage">Garage</option>
                    <option value="Parking Lot">Parking Lot</option>
                    <option value="Visitor Parking">Visitor Parking</option>
                    <option value="No Parking Available">No Parking Available</option>
                  </select>
                </div>

                <!-- Step 5: Property Access -->
                <div class="calculator-step" id="step-5" style="display: none;">
                  <h4 class="mb-3">Property Access</h4>
                  <p class="text-muted mb-4">How will our staff access your property?</p>
                  <select class="form-select form-select-lg" id="property-access" required>
                    <option value="">Select access method...</option>
                    <option value="Someone will be home">Someone will be home</option>
                    <option value="Key provided">Key provided</option>
                    <option value="Lockbox">Lockbox</option>
                    <option value="Door code">Door code</option>
                    <option value="Concierge/Building manager">Concierge/Building manager</option>
                    <option value="Other">Other (will specify)</option>
                  </select>
                  <div class="mt-3" id="access-notes-container" style="display: none;">
                    <label for="access-notes" class="form-label">Access Details</label>
                    <textarea class="form-control" id="access-notes" rows="2" placeholder="Please provide additional details..."></textarea>
                  </div>
                </div>

                <!-- Step 6: Schedule Flexibility -->
                <div class="calculator-step" id="step-6" style="display: none;">
                  <h4 class="mb-3">Schedule Flexibility</h4>
                  <p class="text-muted mb-4">Are you flexible with your preferred schedule?</p>
                  <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="date-flexible">
                    <label class="form-check-label" for="date-flexible">
                      I'm flexible with the date (±2 days)
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="time-flexible">
                    <label class="form-check-label" for="time-flexible">
                      I'm flexible with the time (±2 hours)
                    </label>
                  </div>
                </div>

                <!-- Step 7: Square Footage -->
                <div class="calculator-step" id="step-7" style="display: none;">
                  <h4 class="mb-3">Property Size</h4>
                  <p class="text-muted mb-4">What is the square footage of the area to be cleaned?</p>
                  <select id="square-footage" class="form-select form-select-lg" required>
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

                <!-- Step 8: Service Type -->
                <div class="calculator-step" id="step-8" style="display: none;">
                  <h4 class="mb-3">Service Type</h4>
                  <p class="text-muted mb-4">What type of cleaning service do you need?</p>
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
                            Weekly (20% off) - <span class="price-display" id="price-weekly">$0.00</span>
                          </label>
                        </div>
                        <div class="form-check mb-2">
                          <input class="form-check-input" type="radio" name="serviceType" id="biweekly" value="biweekly">
                          <label class="form-check-label" for="biweekly">
                            Bi-Weekly (15% off) - <span class="price-display" id="price-biweekly">$0.00</span>
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

                <!-- Step 9: Extra Services -->
                <div class="calculator-step" id="step-9" style="display: none;">
                  <h4 class="mb-3">Extra Services (Optional)</h4>
                  <p class="text-muted mb-4">Would you like to add any extra services?</p>
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

                <!-- Navigation Buttons -->
                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                  <button type="button" class="btn btn-outline-secondary" id="prev-btn" style="display: none;">
                    <i class="bi bi-arrow-left"></i> Previous
                  </button>
                  <button type="button" class="btn-primary ms-auto" id="next-btn">
                    Next <i class="bi bi-arrow-right"></i>
                  </button>
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
                  <p class="mt-3">Complete the steps to see your quote</p>
                </div>

                <div id="summary-content" style="display: none;">

                  <!-- Contact Info -->
                  <div class="mb-3" id="summary-contact-section" style="display: none;">
                    <small class="text-muted">Contact</small>
                    <div id="summary-contact" class="fw-bold"></div>
                  </div>

                  <!-- Address -->
                  <div class="mb-3" id="summary-address-section" style="display: none;">
                    <small class="text-muted">Service Location</small>
                    <div id="summary-address" class="fw-bold"></div>
                  </div>

                  <!-- Date & Time -->
                  <div class="mb-3" id="summary-datetime-section" style="display: none;">
                    <small class="text-muted">Preferred Date & Time</small>
                    <div id="summary-datetime" class="fw-bold"></div>
                  </div>

                  <!-- Square Footage -->
                  <div class="mb-3" id="summary-sqft-section" style="display: none;">
                    <small class="text-muted">Square Footage</small>
                    <div id="summary-sqft" class="fw-bold"></div>
                  </div>

                  <!-- Service Type -->
                  <div class="mb-3" id="summary-service-section" style="display: none;">
                    <small class="text-muted">Service Type</small>
                    <div id="summary-service" class="fw-bold"></div>
                    <div id="summary-service-price" class="text-primary">$0.00</div>
                  </div>

                  <!-- Extra Services -->
                  <div id="summary-extras" style="display: none;" class="mb-3">
                    <small class="text-muted">Extra Services</small>
                    <div id="summary-extras-list"></div>
                    <div id="summary-extras-price" class="text-primary">+$0.00</div>
                  </div>

                  <hr id="summary-divider" style="display: none;">

                  <!-- Subtotal -->
                  <div class="d-flex justify-content-between mb-2" id="summary-subtotal-section" style="display: none;">
                    <span>Subtotal</span>
                    <span id="subtotal-price" class="fw-bold">$0.00</span>
                  </div>

                  <!-- Coupon -->
                  <div class="mb-3" id="coupon-section" style="display: none;">
                    <label for="coupon-code" class="form-label small text-muted">Have a coupon code?</label>
                    <div class="input-group input-group-sm">
                      <input type="text" class="form-control" id="coupon-code" placeholder="Enter code">
                      <button class="btn btn-outline-secondary" type="button" id="apply-coupon-btn">Apply</button>
                    </div>
                    <div id="coupon-message" class="small mt-1"></div>
                  </div>

                  <!-- Discount -->
                  <div class="d-flex justify-content-between mb-2 text-success" id="discount-section" style="display: none;">
                    <span>Discount (<span id="discount-code"></span>)</span>
                    <span id="discount-amount">-$0.00</span>
                  </div>

                  <!-- Total -->
                  <div class="d-flex justify-content-between align-items-center mb-4" id="summary-total-section" style="display: none;">
                    <h5 class="mb-0">Total</h5>
                    <h4 class="mb-0 text-primary" id="total-price">$0.00</h4>
                  </div>

                  <button type="button" class="btn-primary w-100 mb-2" id="proceed-payment-btn" style="display: none; text-align: center;">
                    <i class="bi bi-credit-card"></i> Proceed to Payment
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
    let currentStep = 1;
    const totalSteps = 9;

    // Form data storage
    let formData = {
        firstName: '',
        lastName: '',
        email: '',
        phone: '',
        streetAddress: '',
        district: '',
        districtName: '',
        unitApt: '',
        preferredDate: '',
        preferredTime: '',
        parking: '',
        propertyAccess: '',
        accessNotes: '',
        dateFlexible: false,
        timeFlexible: false,
        squareFootage: null,
        serviceType: null,
        extras: []
    };

    // Pricing data
    let currentRange = null;
    let basePrice = 0;
    let extrasTotal = 0;
    let subtotal = 0;
    let discountAmount = 0;
    let appliedCoupon = null;

    // Navigation
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');

    nextBtn.addEventListener('click', function() {
        if (validateStep(currentStep)) {
            saveStepData(currentStep);
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
                updateProgress();
                updateSummary();
            }
        }
    });

    prevBtn.addEventListener('click', function() {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
            updateProgress();
        }
    });

    function showStep(step) {
        document.querySelectorAll('.calculator-step').forEach(s => s.style.display = 'none');
        document.getElementById('step-' + step).style.display = 'block';

        prevBtn.style.display = step === 1 ? 'none' : 'block';

        if (step === totalSteps) {
            nextBtn.style.display = 'none';
        } else {
            nextBtn.style.display = 'block';
            nextBtn.innerHTML = 'Next <i class="bi bi-arrow-right"></i>';
        }
    }

    function updateProgress() {
        const percentage = Math.round((currentStep / totalSteps) * 100);
        document.getElementById('current-step-num').textContent = currentStep;
        document.getElementById('progress-percentage').textContent = percentage;
        document.getElementById('progress-bar').style.width = percentage + '%';
        document.getElementById('progress-bar').setAttribute('aria-valuenow', percentage);
    }

    function validateStep(step) {
        let isValid = true;
        let message = '';

        switch(step) {
            case 1:
                if (!document.getElementById('first-name').value.trim()) {
                    message = 'Please enter your first name';
                    isValid = false;
                } else if (!document.getElementById('last-name').value.trim()) {
                    message = 'Please enter your last name';
                    isValid = false;
                } else if (!document.getElementById('email').value.trim()) {
                    message = 'Please enter your email';
                    isValid = false;
                } else if (!document.getElementById('phone').value.trim()) {
                    message = 'Please enter your phone number';
                    isValid = false;
                }
                break;

            case 2:
                if (!document.getElementById('street-address').value.trim()) {
                    message = 'Please enter your street address';
                    isValid = false;
                } else if (!document.getElementById('district').value) {
                    message = 'Please select a district';
                    isValid = false;
                }
                break;

            case 3:
                if (!document.getElementById('preferred-date').value) {
                    message = 'Please select a preferred date';
                    isValid = false;
                } else if (!document.getElementById('preferred-time').value) {
                    message = 'Please select a preferred time';
                    isValid = false;
                }
                break;

            case 4:
                if (!document.getElementById('parking').value) {
                    message = 'Please select a parking option';
                    isValid = false;
                }
                break;

            case 5:
                if (!document.getElementById('property-access').value) {
                    message = 'Please select how we can access the property';
                    isValid = false;
                }
                break;

            case 7:
                const sqft = document.getElementById('square-footage').value;
                if (!sqft) {
                    message = 'Please select your square footage';
                    isValid = false;
                } else if (sqft === 'custom') {
                    alert('For properties over 5,000 sq ft, please contact us directly for a custom quote.');
                    window.location.href = '{{ route("contacto") }}';
                    isValid = false;
                }
                break;

            case 8:
                if (!document.querySelector('input[name="serviceType"]:checked')) {
                    message = 'Please select a service type';
                    isValid = false;
                }
                break;
        }

        if (!isValid && message) {
            alert(message);
        }

        return isValid;
    }

    function saveStepData(step) {
        switch(step) {
            case 1:
                formData.firstName = document.getElementById('first-name').value.trim();
                formData.lastName = document.getElementById('last-name').value.trim();
                formData.email = document.getElementById('email').value.trim();
                formData.phone = document.getElementById('phone').value.trim();
                break;

            case 2:
                formData.streetAddress = document.getElementById('street-address').value.trim();
                formData.district = document.getElementById('district').value;
                const selectedDistrict = document.getElementById('district').options[document.getElementById('district').selectedIndex];
                formData.districtName = selectedDistrict.text;
                formData.unitApt = document.getElementById('unit-apt').value.trim();
                break;

            case 3:
                formData.preferredDate = document.getElementById('preferred-date').value;
                formData.preferredTime = document.getElementById('preferred-time').value;
                break;

            case 4:
                formData.parking = document.getElementById('parking').value;
                break;

            case 5:
                formData.propertyAccess = document.getElementById('property-access').value;
                formData.accessNotes = document.getElementById('access-notes').value.trim();
                break;

            case 6:
                formData.dateFlexible = document.getElementById('date-flexible').checked;
                formData.timeFlexible = document.getElementById('time-flexible').checked;
                break;

            case 7:
                const selectedOption = document.getElementById('square-footage').options[document.getElementById('square-footage').selectedIndex];
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
                break;

            case 8:
                const serviceRadio = document.querySelector('input[name="serviceType"]:checked');
                formData.serviceType = serviceRadio.value;

                switch(serviceRadio.value) {
                    case 'initial': basePrice = currentRange.initial; break;
                    case 'weekly': basePrice = currentRange.weekly; break;
                    case 'biweekly': basePrice = currentRange.biweekly; break;
                    case 'monthly': basePrice = currentRange.monthly; break;
                    case 'deep_clean': basePrice = currentRange.deep; break;
                    case 'move_out': basePrice = currentRange.moveout; break;
                }
                break;

            case 9:
                calculateExtras();
                break;
        }
    }

    function updatePriceDisplays() {
        document.getElementById('price-initial').textContent = '$' + currentRange.initial.toFixed(2);
        document.getElementById('price-weekly').textContent = '$' + currentRange.weekly.toFixed(2);
        document.getElementById('price-biweekly').textContent = '$' + currentRange.biweekly.toFixed(2);
        document.getElementById('price-monthly').textContent = '$' + currentRange.monthly.toFixed(2);
        document.getElementById('price-deep').textContent = '$' + currentRange.deep.toFixed(2);
        document.getElementById('price-moveout').textContent = '$' + currentRange.moveout.toFixed(2);
    }

    // Extra services handlers
    document.querySelectorAll('.extra-service').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (currentStep === 9) {
                calculateExtras();
                updateSummary();
            }
        });
    });

    document.querySelectorAll('.extra-service-sqft').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const inputId = this.id + '-sqft';
            document.getElementById(inputId).disabled = !this.checked;
            if (!this.checked) document.getElementById(inputId).value = '';
            if (currentStep === 9) {
                calculateExtras();
                updateSummary();
            }
        });
    });

    document.querySelectorAll('.extra-service-panes').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const inputId = this.id + '-panes';
            document.getElementById(inputId).disabled = !this.checked;
            if (!this.checked) document.getElementById(inputId).value = '';
            if (currentStep === 9) {
                calculateExtras();
                updateSummary();
            }
        });
    });

    document.querySelectorAll('input[type="number"]').forEach(input => {
        input.addEventListener('input', function() {
            if (currentStep === 9) {
                calculateExtras();
                updateSummary();
            }
        });
    });

    // Property access change handler
    document.getElementById('property-access').addEventListener('change', function() {
        const accessNotesContainer = document.getElementById('access-notes-container');
        if (this.value === 'Door code' || this.value === 'Lockbox' || this.value === 'Other') {
            accessNotesContainer.style.display = 'block';
        } else {
            accessNotesContainer.style.display = 'none';
            document.getElementById('access-notes').value = '';
        }
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
    }

    function updateSummary() {
        // Show content, hide empty state
        if (currentStep >= 1) {
            document.getElementById('summary-empty').style.display = 'none';
            document.getElementById('summary-content').style.display = 'block';
        }

        // Contact info
        if (currentStep >= 1 && formData.firstName) {
            document.getElementById('summary-contact-section').style.display = 'block';
            document.getElementById('summary-contact').textContent = `${formData.firstName} ${formData.lastName}`;
        }

        // Address
        if (currentStep >= 2 && formData.streetAddress) {
            document.getElementById('summary-address-section').style.display = 'block';
            let addressText = formData.streetAddress;
            if (formData.unitApt) addressText = formData.unitApt + ', ' + addressText;
            addressText += ', ' + formData.districtName;
            document.getElementById('summary-address').textContent = addressText;
        }

        // Date & Time
        if (currentStep >= 3 && formData.preferredDate) {
            document.getElementById('summary-datetime-section').style.display = 'block';
            const dateObj = new Date(formData.preferredDate + 'T00:00:00');
            const dateStr = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            document.getElementById('summary-datetime').textContent = `${dateStr} at ${formData.preferredTime}`;
        }

        // Square footage
        if (currentStep >= 7 && currentRange) {
            document.getElementById('summary-sqft-section').style.display = 'block';
            document.getElementById('summary-sqft').textContent = currentRange.min + ' - ' + currentRange.max + ' sq ft';
        }

        // Service type
        if (currentStep >= 8 && formData.serviceType) {
            document.getElementById('summary-service-section').style.display = 'block';
            const serviceName = document.querySelector('input[name="serviceType"]:checked').parentElement.textContent.split('-')[0].trim();
            document.getElementById('summary-service').textContent = serviceName;
            document.getElementById('summary-service-price').textContent = '$' + basePrice.toFixed(2);
        }

        // Extras
        if (currentStep >= 9) {
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
        }

        // Show pricing sections when we have a base price
        if (currentStep >= 8 && basePrice > 0) {
            document.getElementById('summary-divider').style.display = 'block';
            document.getElementById('summary-subtotal-section').style.display = 'flex';
            document.getElementById('coupon-section').style.display = 'block';
            document.getElementById('summary-total-section').style.display = 'flex';
            document.getElementById('proceed-payment-btn').style.display = 'block';

            subtotal = basePrice + extrasTotal;
            document.getElementById('subtotal-price').textContent = '$' + subtotal.toFixed(2);

            // Recalculate discount if coupon is applied
            if (appliedCoupon) {
                discountAmount = appliedCoupon.discount_amount;
                if (appliedCoupon.discount_type === 'percentage') {
                    discountAmount = (subtotal * appliedCoupon.discount_value) / 100;
                }
            }

            const total = subtotal - discountAmount;
            document.getElementById('total-price').textContent = '$' + total.toFixed(2);
        }
    }

    // Coupon functionality
    document.getElementById('apply-coupon-btn').addEventListener('click', function() {
        const code = document.getElementById('coupon-code').value.trim().toUpperCase();
        if (!code) {
            showCouponMessage('Please enter a coupon code', 'danger');
            return;
        }

        // Call API to validate coupon
        fetch('/api/coupon/validate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                code: code,
                subtotal: subtotal
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.valid) {
                appliedCoupon = data.coupon;
                discountAmount = data.coupon.discount_amount;

                document.getElementById('discount-section').style.display = 'flex';
                document.getElementById('discount-code').textContent = code;
                document.getElementById('discount-amount').textContent = '-$' + discountAmount.toFixed(2);

                const total = subtotal - discountAmount;
                document.getElementById('total-price').textContent = '$' + total.toFixed(2);

                showCouponMessage(data.message, 'success');
                document.getElementById('coupon-code').disabled = true;
                this.textContent = 'Remove';
                this.classList.remove('btn-outline-secondary');
                this.classList.add('btn-outline-danger');
            } else {
                showCouponMessage(data.message, 'danger');
            }
        })
        .catch(error => {
            showCouponMessage('Error validating coupon', 'danger');
        });
    });

    // Handle coupon removal
    document.getElementById('apply-coupon-btn').addEventListener('click', function() {
        if (this.textContent === 'Remove') {
            appliedCoupon = null;
            discountAmount = 0;
            document.getElementById('discount-section').style.display = 'none';
            document.getElementById('total-price').textContent = '$' + subtotal.toFixed(2);
            document.getElementById('coupon-code').value = '';
            document.getElementById('coupon-code').disabled = false;
            this.textContent = 'Apply';
            this.classList.remove('btn-outline-danger');
            this.classList.add('btn-outline-secondary');
            showCouponMessage('', '');
        }
    });

    function showCouponMessage(message, type) {
        const messageEl = document.getElementById('coupon-message');
        messageEl.textContent = message;
        messageEl.className = 'small mt-1';
        if (type === 'success') messageEl.classList.add('text-success');
        if (type === 'danger') messageEl.classList.add('text-danger');
    }

    // Proceed to Stripe Payment
    document.getElementById('proceed-payment-btn').addEventListener('click', function() {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';

        // Prepare order data
        const orderData = {
            first_name: formData.firstName,
            last_name: formData.lastName,
            email: formData.email,
            phone: formData.phone,
            street_address: formData.streetAddress,
            district_id: formData.district,
            unit_apt: formData.unitApt,
            preferred_date: formData.preferredDate,
            preferred_time: formData.preferredTime,
            date_flexible: formData.dateFlexible,
            time_flexible: formData.timeFlexible,
            parking: formData.parking,
            property_access: formData.propertyAccess,
            access_notes: formData.accessNotes,
            square_footage_range: `${currentRange.min} - ${currentRange.max}`,
            service_type: formData.serviceType,
            base_price: basePrice,
            extras_total: extrasTotal,
            coupon_code: appliedCoupon ? appliedCoupon.code : null,
            extras: collectExtrasData()
        };

        // Send to backend
        fetch('{{ route("cleaning-order.checkout") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(orderData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.session_url) {
                // Redirect to Stripe Checkout
                window.location.href = data.session_url;
            } else {
                // Show detailed error messages
                let errorMessage = data.message || 'Failed to create payment session.';

                // If validation errors exist, show them
                if (data.errors) {
                    errorMessage += '\n\nValidation errors:\n';
                    Object.keys(data.errors).forEach(field => {
                        errorMessage += `- ${field}: ${data.errors[field].join(', ')}\n`;
                    });
                }

                console.error('Payment error:', data);
                alert(errorMessage);
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-credit-card"></i> Proceed to Payment';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-credit-card"></i> Proceed to Payment';
        });
    });

    // Helper function to collect extras data
    function collectExtrasData() {
        const extras = [];

        // Simple checkboxes
        document.querySelectorAll('.extra-service:checked').forEach(checkbox => {
            const price = parseFloat(checkbox.dataset.price);
            extras.push({
                type: checkbox.id,
                name: checkbox.parentElement.textContent.trim(),
                price: price
            });
        });

        // Square footage based
        if (document.getElementById('post-const-gov').checked) {
            const sqft = parseFloat(document.getElementById('post-const-gov-sqft').value) || 0;
            const pricePerSqft = parseFloat(document.getElementById('post-const-gov-sqft').dataset.pricePerSqft);
            extras.push({
                type: 'post-const-gov',
                name: 'Post-Construction Government',
                square_feet: sqft,
                price_per_sqft: pricePerSqft,
                price: sqft * pricePerSqft
            });
        }

        if (document.getElementById('post-const-priv').checked) {
            const sqft = parseFloat(document.getElementById('post-const-priv-sqft').value) || 0;
            const pricePerSqft = parseFloat(document.getElementById('post-const-priv-sqft').dataset.pricePerSqft);
            extras.push({
                type: 'post-const-priv',
                name: 'Post-Construction Private',
                square_feet: sqft,
                price_per_sqft: pricePerSqft,
                price: sqft * pricePerSqft
            });
        }

        // Window panes
        if (document.getElementById('window-interior').checked) {
            const panes = parseFloat(document.getElementById('window-interior-panes').value) || 0;
            const pricePerPane = parseFloat(document.getElementById('window-interior-panes').dataset.pricePerPane);
            extras.push({
                type: 'window-interior',
                name: 'Window Clean (Interior)',
                panes: panes,
                price_per_pane: pricePerPane,
                price: panes * pricePerPane
            });
        }

        if (document.getElementById('window-exterior').checked) {
            const panes = parseFloat(document.getElementById('window-exterior-panes').value) || 0;
            const pricePerPane = parseFloat(document.getElementById('window-exterior-panes').dataset.pricePerPane);
            extras.push({
                type: 'window-exterior',
                name: 'Window Clean (Exterior)',
                panes: panes,
                price_per_pane: pricePerPane,
                price: panes * pricePerPane
            });
        }

        return extras;
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

.calculator-step {
    min-height: 300px;
}
</style>
@endpush
