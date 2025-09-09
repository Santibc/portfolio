@extends('landing_page.layout')

@section('content')
    <section style="padding: 160px 0 100px 0;" id="team" class="team section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <span>Equipo</span>
        <h2>Nuestro Equipo</h2>
        <p>Conoce a los profesionales que conforman nuestro equipo de trabajo</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row">
          @if($teamMembers && $teamMembers->count() > 0)
            @foreach($teamMembers as $index => $member)
              <div class="col-lg-4 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="{{ 100 + ($index * 100) }}">
                <div class="member">
                  @if($member->image_path)
                    <img src="{{ asset($member->image_path) }}" class="img-fluid" alt="{{ $member->name }}">
                  @else
                    <img src="{{ asset('montano_assets/assets/img/team/team-1.jpg') }}" class="img-fluid" alt="{{ $member->name }}">
                  @endif
                  <div class="member-content">
                    <h4>{{ $member->name }}</h4>
                    <span>{{ $member->position }}</span>
                    <p>
                      {{ $member->description }}
                    </p>
                    @if($member->twitter_url || $member->facebook_url || $member->instagram_url || $member->linkedin_url)
                      <div class="social">
                        @if($member->twitter_url)
                          <a href="{{ $member->twitter_url }}" target="_blank"><i class="bi bi-twitter-x"></i></a>
                        @endif
                        @if($member->facebook_url)
                          <a href="{{ $member->facebook_url }}" target="_blank"><i class="bi bi-facebook"></i></a>
                        @endif
                        @if($member->instagram_url)
                          <a href="{{ $member->instagram_url }}" target="_blank"><i class="bi bi-instagram"></i></a>
                        @endif
                        @if($member->linkedin_url)
                          <a href="{{ $member->linkedin_url }}" target="_blank"><i class="bi bi-linkedin"></i></a>
                        @endif
                      </div>
                    @endif
                  </div>
                </div>
              </div><!-- End Team Member -->
            @endforeach
          @else
            <div class="col-12 text-center">
              <p>No hay miembros del equipo para mostrar en este momento.</p>
            </div>
          @endif
        </div>

      </div>

    </section><!-- /Team Section -->
    


@endsection
