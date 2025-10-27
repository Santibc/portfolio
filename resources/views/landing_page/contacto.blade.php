@extends('landing_page.layout')

@section('content')
    <section id="contact" style="padding: 160px 0 100px 0;" class="contact section">

      <!-- Section Title -->
      <div class="container section-title">
        <h2>Contact Us</h2>
        <p>{{ $contactInfo->description ?? 'Get in touch with us for a free estimate. We are here to help you with all your cleaning needs.' }}</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4 mb-5">

          <div class="col-lg-4 col-md-6">
            <div class="info-item d-flex flex-column justify-content-center align-items-center">
              <i class="bi bi-envelope"></i>
              <h3>Email</h3>
              <p>{{ $contactInfo->email ?? 'info@cleanme.com' }}</p>
            </div>
          </div><!-- End Info Item -->

          <div class="col-lg-4 col-md-6">
            <div class="info-item d-flex flex-column justify-content-center align-items-center">
              <i class="bi bi-telephone"></i>
              <h3>Phone</h3>
              <p>{{ $contactInfo->phone ?? '+1 (555) 000-0000' }}</p>
            </div>
          </div><!-- End Info Item -->

          <div class="col-lg-4 col-md-6">
            <div class="info-item d-flex flex-column justify-content-center align-items-center">
              <i class="bi bi-geo-alt"></i>
              <h3>Location</h3>
              <p>{{ $contactInfo->address ?? 'Wisconsin, USA' }}</p>
            </div>
          </div><!-- End Info Item -->

        </div>

        <div class="row gy-4">

          <div class="col-lg-6">
            @if($contactInfo && $contactInfo->google_maps_embed)
              {!! $contactInfo->google_maps_embed !!}
            @else
              <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d11803956.043533528!2d-97.56479313457797!3d43.99247806310578!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x52b3e2054e52eec5%3A0xf2bd6e2bec09889d!2sWisconsin%2C%20USA!5e0!3m2!1sen!2s!4v1699999999999!5m2!1sen!2s" frameborder="0" style="border:0; width: 100%; height: 400px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            @endif
          </div><!-- End Google Maps -->

          <div class="col-lg-6">
            <form id="contactForm" class="php-email-form">
              @csrf
              <div class="row gy-4">

                <div class="col-md-6">
                  <input type="text" name="name" class="form-control" placeholder="First Name" required="">
                </div>

                <div class="col-md-6">
                  <input type="text" name="lastname" class="form-control" placeholder="Last Name" required="">
                </div>

                <div class="col-md-12">
                  <input type="email" class="form-control" name="email" placeholder="Email" required="">
                </div>

                <div class="col-md-12">
                  <input type="tel" class="form-control" name="phone" placeholder="Phone" required="">
                </div>

                <div class="col-md-12">
                  <textarea class="form-control" name="message" rows="6" placeholder="How can we help you? (Optional)"></textarea>
                </div>

                <div class="col-md-12 text-center">
                  <div class="loading" style="display: none;">Sending...</div>
                  <div class="error-message" style="display: none;"></div>
                  <div class="sent-message" style="display: none;">Your message has been sent successfully!</div>

                  <button type="submit" class="btn-primary">Send Message</button>
                </div>

              </div>
            </form>
          </div><!-- End Contact Form -->

        </div>

      </div>

    </section><!-- /Contact Section -->

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');

    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const loadingDiv = this.querySelector('.loading');
            const errorDiv = this.querySelector('.error-message');
            const successDiv = this.querySelector('.sent-message');
            const submitBtn = this.querySelector('button[type="submit"]');

            // Reset states
            loadingDiv.style.display = 'block';
            errorDiv.style.display = 'none';
            successDiv.style.display = 'none';
            submitBtn.disabled = true;

            fetch('{{ route("contact.send") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                loadingDiv.style.display = 'none';
                submitBtn.disabled = false;

                if (data.success) {
                    successDiv.style.display = 'block';
                    contactForm.reset();
                    setTimeout(() => {
                        successDiv.style.display = 'none';
                    }, 5000);
                } else {
                    errorDiv.textContent = data.error || 'Error sending message';
                    errorDiv.style.display = 'block';
                }
            })
            .catch(error => {
                loadingDiv.style.display = 'none';
                submitBtn.disabled = false;
                errorDiv.textContent = 'Error sending message';
                errorDiv.style.display = 'block';
            });
        });
    }
});
</script>
@endpush
