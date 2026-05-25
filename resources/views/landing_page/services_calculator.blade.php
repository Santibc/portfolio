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
                    <small class="text-muted">Step <span id="current-step-num">1</span> of <span id="total-steps-num">10</span></small>
                    <small class="text-muted"><span id="progress-percentage">10</span>% Complete</small>
                  </div>
                  <div class="progress" style="height: 8px;">
                    <div class="progress-bar" id="progress-bar" role="progressbar" style="width: 10%; background-color: var(--accent-color);" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
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
                  <p class="text-muted mb-4">Start typing your address — we use Google Maps to confirm we service your area.</p>
                  <div class="row g-3">
                    <div class="col-12">
                      <label for="street-address-container" class="form-label">Street Address <span class="text-danger">*</span></label>
                      {{-- Google Maps PlaceAutocompleteElement (Places API New) se monta aquí dinámicamente --}}
                      <div id="street-address-container" class="gmp-autocomplete-wrap"></div>
                      {{-- Mantenemos un input fallback para que validateStep funcione con o sin SDK cargado --}}
                      <input type="hidden" id="street-address">
                      <small class="text-muted"><i class="bi bi-info-circle"></i> Pick one of the suggestions to validate the location.</small>
                    </div>

                    <div class="col-12" id="detected-location-wrap" style="display:none;">
                      <div class="alert alert-light border d-flex align-items-start gap-2 mb-0">
                        <i class="bi bi-geo-alt-fill text-success fs-5"></i>
                        <div>
                          <strong>Detected location:</strong>
                          <div id="detected-location" class="text-muted small"></div>
                        </div>
                      </div>
                    </div>

                    <div class="col-12" id="location-error" style="display:none;">
                      <div class="alert alert-danger d-flex align-items-start gap-2 mb-0">
                        <i class="bi bi-exclamation-octagon-fill"></i>
                        <div id="location-error-text">We currently don't service this area.</div>
                      </div>
                    </div>

                    <div class="col-md-12">
                      <label for="unit-apt" class="form-label">Unit / Apartment (Optional)</label>
                      <input type="text" class="form-control" id="unit-apt" placeholder="Unit 5B">
                    </div>

                    <input type="hidden" id="loc-latitude">
                    <input type="hidden" id="loc-longitude">
                    <input type="hidden" id="loc-formatted-address">
                    <input type="hidden" id="loc-place-id">
                    <input type="hidden" id="loc-postcode">
                    <input type="hidden" id="loc-suburb">
                    <input type="hidden" id="loc-state">
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
                        @foreach($bookingTimeSlots as $slot)
                          <option value="{{ $slot }}">{{ $slot }}</option>
                        @endforeach
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
                      I'm flexible with the date (2 days)
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="time-flexible">
                    <label class="form-check-label" for="time-flexible">
                      I'm flexible with the time (2 hours)
                    </label>
                  </div>
                </div>

                <!-- Step 7: Room Details -->
                <div class="calculator-step" id="step-7" style="display: none;">
                  <h4 class="mb-3">Room Details</h4>
                  <p class="text-muted mb-4">Please specify the number of rooms</p>
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label for="num-bathrooms" class="form-label">Number of Bathrooms <span class="text-danger">*</span></label>
                      <input type="number" class="form-control room-input" id="num-bathrooms" min="0" value="1" required>
                      <small class="text-muted">Price: $<span id="bathroom-price">{{ $roomTypePrices->where('room_type', 'bathroom')->first()->price ?? 50 }}</span> each</small>
                    </div>
                    <div class="col-md-6">
                      <label for="num-bedrooms" class="form-label">Number of Bedrooms <span class="text-danger">*</span></label>
                      <input type="number" class="form-control room-input" id="num-bedrooms" min="0" value="2" required>
                      <small class="text-muted">Price: $<span id="bedroom-price">{{ $roomTypePrices->where('room_type', 'bedroom')->first()->price ?? 60 }}</span> each</small>
                    </div>
                    <div class="col-md-6">
                      <label for="num-kitchens" class="form-label">Number of Kitchens <span class="text-danger">*</span></label>
                      <input type="number" class="form-control room-input" id="num-kitchens" min="0" value="1" required>
                      <small class="text-muted">Price: $<span id="kitchen-price">{{ $roomTypePrices->where('room_type', 'kitchen')->first()->price ?? 70 }}</span> each</small>
                    </div>
                    <div class="col-md-6">
                      <label for="other-rooms-desc" class="form-label">Other Description (Optional)</label>
                      <input type="text" class="form-control mb-2" id="other-rooms-desc" placeholder="Living room, dining room, etc.">
                      <label for="num-other-rooms" class="form-label">Quantity</label>
                      <input type="number" class="form-control room-input" id="num-other-rooms" min="0" value="0">
                      <small class="text-muted">Price: $<span id="other-price">{{ $roomTypePrices->where('room_type', 'other')->first()->price ?? 40 }}</span> each</small>
                    </div>
                  </div>
                </div>

                <!-- Step 7.5: Cleaners and Hours -->
                <div class="calculator-step" id="step-7-5" style="display: none;">
                  <h4 class="mb-3">Cleaners & Hours</h4>
                  <p class="text-muted mb-4">How many cleaners and for how long?</p>
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label for="num-cleaners" class="form-label">Number of Cleaners <span class="text-danger">*</span></label>
                      <input type="number" class="form-control form-control-lg cleaner-hour-input" id="num-cleaners" min="1" max="10" value="1" required>
                      <small class="text-muted">Price per cleaner: $<span id="price-per-cleaner">{{ $cleanerPrice ?? 30 }}</span></small>
                    </div>
                    <div class="col-md-6">
                      <label for="num-hours" class="form-label">Number of Hours <span class="text-danger">*</span></label>
                      <input type="number" class="form-control form-control-lg cleaner-hour-input" id="num-hours" min="1" max="24" value="2" required>
                      <small class="text-muted">Price per hour: $<span id="price-per-hour">{{ $hourPrice ?? 30 }}</span></small>
                    </div>
                  </div>
                  <div id="cleaner-price-display" class="alert alert-info mt-3">
                    <strong>Total Service Price:</strong> $<span id="cleaner-hour-price">0.00</span>
                    <br><small class="text-muted">(<span id="num-cleaners-display">1</span> cleaner × <span id="num-hours-display">2</span> hours × $<span id="base-rate-display">30</span>)</small>
                  </div>
                </div>

                <!-- Step 8: Service Type -->
                <div class="calculator-step" id="step-8" style="display: none;">
                  <h4 class="mb-3">Service Type</h4>
                  <p class="text-muted mb-4">What type of cleaning service do you need?</p>
                  <div class="row g-3 justify-content-center">

                    <!-- Normal Clean -->
                    <div class="col-md-5">
                      <div class="service-type-card h-100 text-center">
                        <div class="form-check">
                          <input class="form-check-input service-type-radio" type="radio" name="serviceType" id="normal-clean" value="normal" data-multiplier="{{ $normalMultiplier ?? 1 }}">
                          <label class="form-check-label w-100" for="normal-clean">
                            <div class="mb-3">
                              <i class="bi bi-house-check" style="font-size: 3rem; color: var(--accent-color);"></i>
                            </div>
                            <h5>Normal Clean</h5>
                            <p class="text-muted small mb-2">Standard cleaning service</p>
                            <div class="price-display fs-4" id="price-normal">
                              +$<span id="normal-multiplier-price">{{ $normalMultiplier ?? 0 }}</span>
                            </div>
                          </label>
                        </div>
                      </div>
                    </div>

                    <!-- Deep Clean -->
                    <div class="col-md-5">
                      <div class="service-type-card h-100 text-center">
                        <div class="form-check">
                          <input class="form-check-input service-type-radio" type="radio" name="serviceType" id="deep-clean" value="deep" data-multiplier="{{ $deepMultiplier ?? 1.5 }}">
                          <label class="form-check-label w-100" for="deep-clean">
                            <div class="mb-3">
                              <i class="bi bi-stars" style="font-size: 3rem; color: var(--accent-color);"></i>
                            </div>
                            <h5>Deep Clean</h5>
                            <p class="text-muted small mb-2">Thorough deep cleaning</p>
                            <div class="price-display fs-4" id="price-deep">
                              +$<span id="deep-multiplier-price">{{ $deepMultiplier ?? 0 }}</span>
                            </div>
                          </label>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>

                <!-- Step 9: Extra Services -->
                <div class="calculator-step" id="step-9" style="display: none;">
                  <h4 class="mb-3">Extra Services (Optional)</h4>
                  <p class="text-muted mb-4">Select any additional services you need</p>
                  <div class="row g-3">
                    @foreach($serviceExtras as $extra)
                    <div class="col-6 col-md-4 col-lg-3">
                      <div class="extra-service-card">
                        <input type="checkbox" class="extra-checkbox" id="extra-{{ $extra->id }}"
                               value="{{ $extra->id }}" data-price="{{ $extra->price }}" data-name="{{ $extra->name }}">
                        <label for="extra-{{ $extra->id }}" class="extra-label">
                          <div class="extra-icon">
                            <i class="{{ $extra->icon_class }} fa-2x"></i>
                          </div>
                          <div class="extra-name">{{ $extra->name }}</div>
                          <div class="extra-price">${{ number_format($extra->price, 2) }}</div>
                        </label>
                      </div>
                    </div>
                    @endforeach
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
                    <i class="bi bi-calendar-check"></i> Submit Booking Request
                  </button>

                  <small class="text-muted d-block text-center">
                    <i class="bi bi-info-circle"></i> No payment required. We'll contact you to confirm date and pricing.
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
    const totalSteps = 10;

    // Form data storage
    let formData = {
        firstName: '',
        lastName: '',
        email: '',
        phone: '',
        streetAddress: '',
        unitApt: '',
        latitude: null,
        longitude: null,
        formattedAddress: '',
        placeId: '',
        postcode: '',
        suburb: '',
        state: '',
        locationConfirmed: false,
        preferredDate: '',
        preferredTime: '',
        parking: '',
        propertyAccess: '',
        accessNotes: '',
        dateFlexible: false,
        timeFlexible: false,
        numBathrooms: 0,
        numBedrooms: 0,
        numKitchens: 0,
        numOtherRooms: 0,
        otherRoomsDesc: '',
        numCleaners: null,
        numHours: null,
        serviceType: null,
        extras: []
    };

    // Pricing data
    let roomTypePrices = @json($roomTypePrices);
    let basePrice = 0;
    let roomsPrice = 0;
    let serviceTypeMultiplier = 0;
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

        // Map step 8 to step-7-5, and adjust steps 9-10 accordingly
        let stepId;
        if (step <= 7) {
            stepId = 'step-' + step;
        } else if (step === 8) {
            stepId = 'step-7-5';
        } else {
            // step 9 becomes step 8, step 10 becomes step 9 in HTML
            stepId = 'step-' + (step - 1);
        }

        document.getElementById(stepId).style.display = 'block';

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
                } else if (!document.getElementById('loc-latitude').value || !document.getElementById('loc-longitude').value) {
                    message = 'Please pick your address from the suggestion list so we can validate the location.';
                    isValid = false;
                } else if (!formData.locationConfirmed) {
                    message = 'Sorry, we don\'t service this area. Please use a different address.';
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
                const numBathrooms = document.getElementById('num-bathrooms').value;
                const numBedrooms = document.getElementById('num-bedrooms').value;
                const numKitchens = document.getElementById('num-kitchens').value;

                if (numBathrooms === '' || parseInt(numBathrooms) < 0) {
                    message = 'Please enter a valid number of bathrooms';
                    isValid = false;
                } else if (numBedrooms === '' || parseInt(numBedrooms) < 0) {
                    message = 'Please enter a valid number of bedrooms';
                    isValid = false;
                } else if (numKitchens === '' || parseInt(numKitchens) < 0) {
                    message = 'Please enter a valid number of kitchens';
                    isValid = false;
                }
                break;

            case 8:
                // Step 8 is now cleaners/hours (step-7-5)
                const numCleanersVal = parseInt(document.getElementById('num-cleaners').value);
                const numHoursVal = parseInt(document.getElementById('num-hours').value);

                if (!numCleanersVal || numCleanersVal < 1) {
                    message = 'Please enter at least 1 cleaner';
                    isValid = false;
                } else if (!numHoursVal || numHoursVal < 1) {
                    message = 'Please enter at least 1 hour';
                    isValid = false;
                }
                break;

            case 9:
                // Step 9 is now service type
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
                formData.unitApt = document.getElementById('unit-apt').value.trim();
                formData.latitude = parseFloat(document.getElementById('loc-latitude').value) || null;
                formData.longitude = parseFloat(document.getElementById('loc-longitude').value) || null;
                formData.formattedAddress = document.getElementById('loc-formatted-address').value;
                formData.placeId = document.getElementById('loc-place-id').value;
                formData.postcode = document.getElementById('loc-postcode').value;
                formData.suburb = document.getElementById('loc-suburb').value;
                formData.state = document.getElementById('loc-state').value;
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
                // Room details - save to formData
                formData.numBathrooms = parseInt(document.getElementById('num-bathrooms').value) || 0;
                formData.numBedrooms = parseInt(document.getElementById('num-bedrooms').value) || 0;
                formData.numKitchens = parseInt(document.getElementById('num-kitchens').value) || 0;
                formData.numOtherRooms = parseInt(document.getElementById('num-other-rooms').value) || 0;
                formData.otherRoomsDesc = document.getElementById('other-rooms-desc').value;

                calculateRoomsPrice();
                break;

            case 8:
                // Cleaners and hours - save to formData
                formData.numCleaners = parseInt(document.getElementById('num-cleaners').value) || 0;
                formData.numHours = parseInt(document.getElementById('num-hours').value) || 0;

                updateCleanerHourPrice();
                break;

            case 9:
                // Service type
                const selectedType = document.querySelector('input[name="serviceType"]:checked');
                if (selectedType) {
                    formData.serviceType = selectedType.value;
                    calculateServiceTypePrice();
                }
                break;

            case 10:
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

    // Extra services handlers (new icon-based checkboxes)
    document.querySelectorAll('.extra-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (currentStep === 10) {
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

    // Pricing configuration
    const pricePerCleaner = {{ $cleanerPrice ?? 30 }};
    const pricePerHour = {{ $hourPrice ?? 30 }};
    const normalMultiplier = {{ $normalMultiplier ?? 0 }};
    const deepMultiplier = {{ $deepMultiplier ?? 50 }};

    // Real-time calculation for rooms
    document.querySelectorAll('.room-input').forEach(input => {
        input.addEventListener('input', function() {
            calculateRoomsPrice();
            updateSummary();
        });
    });

    // Real-time calculation for cleaners and hours
    document.querySelectorAll('.cleaner-hour-input').forEach(input => {
        input.addEventListener('input', function() {
            updateCleanerHourPrice();
            updateSummary();
        });
    });

    // Real-time calculation for service type
    document.querySelectorAll('.service-type-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            calculateServiceTypePrice();
            updateSummary();
        });
    });

    function calculateRoomsPrice() {
        const bathroomPrice = parseFloat(document.getElementById('bathroom-price').textContent);
        const bedroomPrice = parseFloat(document.getElementById('bedroom-price').textContent);
        const kitchenPrice = parseFloat(document.getElementById('kitchen-price').textContent);
        const otherPrice = parseFloat(document.getElementById('other-price').textContent);

        const numBathrooms = parseInt(document.getElementById('num-bathrooms').value) || 0;
        const numBedrooms = parseInt(document.getElementById('num-bedrooms').value) || 0;
        const numKitchens = parseInt(document.getElementById('num-kitchens').value) || 0;
        const numOther = parseInt(document.getElementById('num-other-rooms').value) || 0;

        roomsPrice = (bathroomPrice * numBathrooms) +
                     (bedroomPrice * numBedrooms) +
                     (kitchenPrice * numKitchens) +
                     (otherPrice * numOther);

        return roomsPrice;
    }

    function updateCleanerHourPrice() {
        const numCleaners = parseInt(document.getElementById('num-cleaners').value) || 0;
        const numHours = parseInt(document.getElementById('num-hours').value) || 0;

        if (numCleaners > 0 && numHours > 0) {
            const totalPrice = numCleaners * numHours * pricePerCleaner;

            document.getElementById('cleaner-hour-price').textContent = totalPrice.toFixed(2);
            document.getElementById('num-cleaners-display').textContent = numCleaners;
            document.getElementById('num-hours-display').textContent = numHours;
            document.getElementById('base-rate-display').textContent = pricePerCleaner.toFixed(2);

            basePrice = totalPrice;
        } else {
            document.getElementById('cleaner-hour-price').textContent = '0.00';
            basePrice = 0;
        }
    }

    function calculateServiceTypePrice() {
        const selectedType = document.querySelector('input[name="serviceType"]:checked');

        if (selectedType) {
            serviceTypeMultiplier = parseFloat(selectedType.dataset.multiplier) || 0;

            // Update display
            if (selectedType.value === 'normal') {
                document.getElementById('normal-multiplier-price').textContent = normalMultiplier.toFixed(2);
            } else if (selectedType.value === 'deep') {
                document.getElementById('deep-multiplier-price').textContent = deepMultiplier.toFixed(2);
            }
        } else {
            serviceTypeMultiplier = 0;
        }
    }

    function calculateExtras() {
        extrasTotal = 0;

        // New extra checkboxes with .extra-checkbox class
        document.querySelectorAll('.extra-checkbox:checked').forEach(checkbox => {
            extrasTotal += parseFloat(checkbox.dataset.price);
        });
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
            let addressText = formData.formattedAddress || formData.streetAddress;
            if (formData.unitApt) addressText = formData.unitApt + ', ' + addressText;
            document.getElementById('summary-address').textContent = addressText;
        }

        // Date & Time
        if (currentStep >= 3 && formData.preferredDate) {
            document.getElementById('summary-datetime-section').style.display = 'block';
            const dateObj = new Date(formData.preferredDate + 'T00:00:00');
            const dateStr = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            document.getElementById('summary-datetime').textContent = `${dateStr} at ${formData.preferredTime}`;
        }

        // Room details
        if (currentStep >= 7) {
            const numBathrooms = parseInt(document.getElementById('num-bathrooms')?.value) || 0;
            const numBedrooms = parseInt(document.getElementById('num-bedrooms')?.value) || 0;
            const numKitchens = parseInt(document.getElementById('num-kitchens')?.value) || 0;
            const numOther = parseInt(document.getElementById('num-other-rooms')?.value) || 0;
            const otherDesc = document.getElementById('other-rooms-desc')?.value || '';

            if (numBathrooms || numBedrooms || numKitchens || numOther) {
                document.getElementById('summary-sqft-section').style.display = 'block';
                const roomDetails = [];
                if (numBathrooms > 0) roomDetails.push(`${numBathrooms} Bathroom${numBathrooms > 1 ? 's' : ''}`);
                if (numBedrooms > 0) roomDetails.push(`${numBedrooms} Bedroom${numBedrooms > 1 ? 's' : ''}`);
                if (numKitchens > 0) roomDetails.push(`${numKitchens} Kitchen${numKitchens > 1 ? 's' : ''}`);
                if (numOther > 0) roomDetails.push(`${numOther} Other${otherDesc ? ' (' + otherDesc + ')' : ''}`);
                document.getElementById('summary-sqft').textContent = roomDetails.join(', ');
            }
        }

        // Cleaners and hours
        if (currentStep >= 8) {
            const numCleaners = document.getElementById('num-cleaners')?.value;
            const numHours = document.getElementById('num-hours')?.value;
            if (numCleaners && numHours) {
                // Could add a summary section for this if needed
            }
        }

        // Service type
        if (currentStep >= 9 && formData.serviceType) {
            const summaryServiceSection = document.getElementById('summary-service-section');
            const selectedService = document.querySelector('input[name="serviceType"]:checked');

            if (summaryServiceSection && selectedService) {
                summaryServiceSection.style.display = 'block';
                const serviceName = selectedService.parentElement?.textContent?.split('-')[0]?.trim() || formData.serviceType;
                document.getElementById('summary-service').textContent = serviceName;
                document.getElementById('summary-service-price').textContent = '$' + (basePrice + serviceTypeMultiplier).toFixed(2);
            }
        }

        // Extras
        if (currentStep >= 10) {
            const extrasList = [];

            document.querySelectorAll('.extra-checkbox:checked').forEach(checkbox => {
                extrasList.push(checkbox.dataset.name);
            });

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

            subtotal = roomsPrice + basePrice + serviceTypeMultiplier + extrasTotal;
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

    // Submit booking request (no payment)
    document.getElementById('proceed-payment-btn').addEventListener('click', function() {
        const btn = this;
        const originalHtml = '<i class="bi bi-calendar-check"></i> Submit Booking Request';
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Sending...';

        const orderData = {
            first_name: formData.firstName,
            last_name: formData.lastName,
            email: formData.email,
            phone: formData.phone,
            street_address: formData.streetAddress,
            unit_apt: formData.unitApt,
            latitude: formData.latitude,
            longitude: formData.longitude,
            formatted_address: formData.formattedAddress,
            place_id: formData.placeId,
            postcode: formData.postcode,
            suburb: formData.suburb,
            state: formData.state,
            preferred_date: formData.preferredDate,
            preferred_time: formData.preferredTime,
            date_flexible: formData.dateFlexible,
            time_flexible: formData.timeFlexible,
            parking: formData.parking,
            property_access: formData.propertyAccess,
            access_notes: formData.accessNotes,
            num_bathrooms: formData.numBathrooms,
            num_bedrooms: formData.numBedrooms,
            num_kitchens: formData.numKitchens,
            num_other_rooms: formData.numOtherRooms,
            other_rooms_desc: formData.otherRoomsDesc,
            num_cleaners: formData.numCleaners,
            num_hours: formData.numHours,
            service_type: formData.serviceType,
            rooms_price: roomsPrice,
            base_price: basePrice,
            service_type_price: serviceTypeMultiplier,
            extras_total: extrasTotal,
            coupon_code: appliedCoupon ? appliedCoupon.code : null,
            extras: collectExtrasData()
        };

        fetch('{{ route("cleaning-order.booking") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(orderData)
        })
        .then(response => response.json().then(data => ({ status: response.status, data })))
        .then(({ status, data }) => {
            if (data.success && data.redirect_url) {
                window.location.href = data.redirect_url;
                return;
            }

            let errorMessage = data.message || 'Could not submit your booking. Please try again.';
            if (data.errors) {
                errorMessage += '\n\nValidation errors:\n';
                Object.keys(data.errors).forEach(field => {
                    errorMessage += `- ${field}: ${data.errors[field].join(', ')}\n`;
                });
            }
            console.error('Booking error:', data);
            alert(errorMessage);
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        });
    });

    // Helper function to collect extras data
    function collectExtrasData() {
        const extras = [];

        // New icon-based checkboxes
        document.querySelectorAll('.extra-checkbox:checked').forEach(checkbox => {
            const price = parseFloat(checkbox.dataset.price);
            const name = checkbox.dataset.name;
            const extraId = checkbox.value;
            extras.push({
                id: extraId,
                name: name,
                price: price
            });
        });

        return extras;
    }

    // ===== Google Maps Places Autocomplete (Places API New) + service availability =====
    function resetLocationFields() {
        ['loc-latitude','loc-longitude','loc-formatted-address','loc-place-id','loc-postcode','loc-suburb','loc-state']
            .forEach(id => document.getElementById(id).value = '');
        document.getElementById('street-address').value = '';
        formData.locationConfirmed = false;
        document.getElementById('detected-location-wrap').style.display = 'none';
        document.getElementById('location-error').style.display = 'none';
    }

    function showLocationError(msg) {
        const wrap = document.getElementById('location-error');
        document.getElementById('location-error-text').textContent = msg;
        wrap.style.display = 'block';
        document.getElementById('detected-location-wrap').style.display = 'none';
        formData.locationConfirmed = false;
    }

    function showLocationOk(formattedAddress, suburb, state, postcode) {
        document.getElementById('location-error').style.display = 'none';
        const detected = [suburb, state, postcode].filter(Boolean).join(' ');
        document.getElementById('detected-location').textContent = formattedAddress + (detected ? ' — ' + detected : '');
        document.getElementById('detected-location-wrap').style.display = 'block';
        formData.locationConfirmed = true;
    }

    window.initServicesCalculatorMap = async function() {
        const container = document.getElementById('street-address-container');
        if (!container) return;

        try {
            const { PlaceAutocompleteElement } = await google.maps.importLibrary('places');

            const placeAutocomplete = new PlaceAutocompleteElement({
                includedRegionCodes: ['{{ $layoutConfig->google_maps_country ?? 'au' }}'],
            });
            placeAutocomplete.id = 'gmp-street-address';
            placeAutocomplete.style.width = '100%';

            container.appendChild(placeAutocomplete);

            placeAutocomplete.addEventListener('gmp-select', async (event) => {
                try {
                    const place = event.placePrediction.toPlace();
                    await place.fetchFields({
                        fields: ['displayName', 'formattedAddress', 'location', 'addressComponents', 'id']
                    });

                    if (!place.location) {
                        resetLocationFields();
                        showLocationError('Could not get the location of this address. Please try another.');
                        return;
                    }

                    const lat = place.location.lat();
                    const lng = place.location.lng();
                    let postcode = '', suburb = '', state = '';

                    (place.addressComponents || []).forEach(c => {
                        const types = c.types || [];
                        if (types.includes('postal_code')) postcode = c.longText || c.shortText;
                        if (types.includes('locality')) suburb = c.longText || c.shortText;
                        if (!suburb && types.includes('sublocality_level_1')) suburb = c.longText || c.shortText;
                        if (types.includes('administrative_area_level_1')) state = c.shortText || c.longText;
                    });

                    document.getElementById('street-address').value = place.formattedAddress || '';
                    document.getElementById('loc-latitude').value = lat;
                    document.getElementById('loc-longitude').value = lng;
                    document.getElementById('loc-formatted-address').value = place.formattedAddress || '';
                    document.getElementById('loc-place-id').value = place.id || '';
                    document.getElementById('loc-postcode').value = postcode;
                    document.getElementById('loc-suburb').value = suburb;
                    document.getElementById('loc-state').value = state;

                    const r = await fetch('{{ route('api.service.availability') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ latitude: lat, longitude: lng, postcode, suburb })
                    });
                    const data = await r.json();
                    if (data.allowed) {
                        showLocationOk(place.formattedAddress || '', suburb, state, postcode);
                    } else {
                        showLocationError(data.message || "We don't service this area.");
                    }
                } catch (err) {
                    console.error('Place fetch error:', err);
                    showLocationError('Could not validate the address right now. Please try again.');
                }
            });
        } catch (err) {
            console.error('Failed to load Google Maps Places:', err);
        }
    };
});
</script>
@if(!empty($layoutConfig->google_maps_api_key))
{{-- Bootstrap loader recomendado por Google para la API moderna (Places API New) --}}
<script>
(g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})({
    key: "{{ $layoutConfig->google_maps_api_key }}",
    v: "weekly"
});
window.addEventListener('load', () => window.initServicesCalculatorMap && window.initServicesCalculatorMap());
</script>
<style>
/* Ajustar el PlaceAutocompleteElement para integrarlo con Bootstrap */
.gmp-autocomplete-wrap gmp-place-autocomplete {
    width: 100%;
    display: block;
}
.gmp-autocomplete-wrap gmp-place-autocomplete::part(input) {
    width: 100%;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    line-height: 1.5;
    color: #212529;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}
.gmp-autocomplete-wrap gmp-place-autocomplete::part(input):focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    outline: none;
}
</style>
@else
<script>console.warn('Google Maps API key is not configured. Set it in /admin/landing → Layout tab.');</script>
@endif

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

/* Extra Service Card Styling */
.extra-service-card {
    position: relative;
    height: 100%;
}

.extra-checkbox {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.extra-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border: 2px solid #dee2e6;
    border-radius: 12px;
    padding: 20px 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    background-color: #fff;
    height: 100%;
    text-align: center;
}

.extra-label:hover {
    border-color: var(--accent-color);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.extra-checkbox:checked + .extra-label {
    border-color: var(--accent-color);
    background-color: rgba(69, 162, 158, 0.1);
    box-shadow: 0 0 0 3px rgba(69, 162, 158, 0.2);
}

.extra-icon {
    margin-bottom: 12px;
    color: var(--accent-color);
    font-size: 2rem;
}

.extra-checkbox:checked + .extra-label .extra-icon {
    transform: scale(1.1);
}

.extra-name {
    font-weight: 600;
    font-size: 0.9rem;
    margin-bottom: 8px;
    min-height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1.3;
}

.extra-price {
    color: #28a745;
    font-weight: bold;
    font-size: 1.1em;
}
</style>
@endpush
