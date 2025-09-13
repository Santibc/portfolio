@extends('landing_page.layout')

@section('content')
    <section id="contact" style="padding: 160px 0 100px 0;" class="contact section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <span>Contacto</span>
        <h2>Contacto</h2>
        <p>{{ $contactInfo->description ?? 'Estamos aquí para ayudarte. Contáctanos y resolveremos todas tus dudas.' }}</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-4">

          <div class="col-lg-6">
            <div class="info-item d-flex flex-column justify-content-center align-items-center" data-aos="fade-up" data-aos-delay="200">
              <i class="bi bi-geo-alt"></i>
              <h3>Dirección</h3>
              <p>{{ $contactInfo->address ?? 'A108 Adam Street, New York, NY 535022' }}</p>
            </div>
          </div><!-- End Info Item -->

          <div class="col-lg-3 col-md-6">
            <div class="info-item d-flex flex-column justify-content-center align-items-center" data-aos="fade-up" data-aos-delay="300">
              <i class="bi bi-telephone"></i>
              <h3>Teléfono </h3>
              <p>{{ $contactInfo->phone ?? '+1 5589 55488 55' }}</p>
            </div>
          </div><!-- End Info Item -->

          <div class="col-lg-3 col-md-6">
            <div class="info-item d-flex flex-column justify-content-center align-items-center" data-aos="fade-up" data-aos-delay="400">
              <i class="bi bi-envelope"></i>
              <h3>Correo</h3>
              <p>{{ $contactInfo->email ?? 'info@example.com' }}</p>
            </div>
          </div><!-- End Info Item -->

        </div>

        <div class="row gy-4 mt-1">
          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="300">
            @if($contactInfo && $contactInfo->google_maps_embed)
              {!! $contactInfo->google_maps_embed !!}
            @else
              <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d48389.78314118045!2d-74.006138!3d40.710059!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25a22a3bda30d%3A0xb89d1fe6bc499443!2sDowntown%20Conference%20Center!5e0!3m2!1sen!2sus!4v1676961268712!5m2!1sen!2sus" frameborder="0" style="border:0; width: 100%; height: 400px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            @endif
          </div><!-- End Google Maps -->

          <div class="col-lg-6">
            <form id="contactForm" class="php-email-form" data-aos="fade-up" data-aos-delay="400">
              @csrf
              <div class="row gy-4">

                <div class="col-md-6">
                  <input type="text" name="name" class="form-control" placeholder="Nombre" required="">
                </div>

                <div class="col-md-6 ">
                  <input type="email" class="form-control" name="email" placeholder="Correo" required="">
                </div>

                <div class="col-md-12">
                  <input type="text" class="form-control" name="subject" placeholder="Asunto" required="">
                </div>

                <div class="col-md-12">
                  <textarea class="form-control" name="message" rows="6" placeholder="Mensaje" required=""></textarea>
                </div>

                <div class="col-md-12 text-center">
                  <div class="loading" style="display: none;">Enviando...</div>
                  <div class="error-message" style="display: none;"></div>
                  <div class="sent-message" style="display: none;">Tu mensaje ha sido enviado correctamente</div>

                  <button type="submit">Enviar</button>
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
                    errorDiv.textContent = data.error || 'Error al enviar el mensaje';
                    errorDiv.style.display = 'block';
                }
            })
            .catch(error => {
                loadingDiv.style.display = 'none';
                submitBtn.disabled = false;
                errorDiv.textContent = 'Error al enviar el mensaje';
                errorDiv.style.display = 'block';
            });
        });
    }
});
</script>
@endpush
