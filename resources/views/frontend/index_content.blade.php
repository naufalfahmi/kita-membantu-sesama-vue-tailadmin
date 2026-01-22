
    <!-- ===== Hero Start ===== -->
    <section id="home" class="gj do ir hj sp jr i pg">
      <!-- Hero Images -->
      <div class="xc fn zd/2 2xl:ud-w-187.5 bd 2xl:ud-h-171.5 h q r">
        <img src="{{ asset("frontend/images/shape-01.svg") }}" alt="shape" class="xc 2xl:ud-block h t -ud-left-[10%] ua" />
        <img src="{{ asset("frontend/images/shape-02.svg") }}" alt="shape" class="xc 2xl:ud-block h u p va" />
        <img src="{{ asset("frontend/images/shape-03.svg") }}" alt="shape" class="xc 2xl:ud-block h v w va" />
        {{-- shape-04.svg removed per request --}}
        @php
          $lp = $landingProfile ?? null;
          $heroImage = $lp && !empty($lp->hero_image) ? $lp->hero_image : null;
          if ($heroImage) {
            $heroImageUrl = preg_match('/^data:|^https?:\/\//', $heroImage) ? $heroImage : asset('storage/' . $heroImage);
          } else {
            $heroImageUrl = asset("frontend/images/people-who-support-svgrepo-com.svg");
          }
        @endphp
        <img src="{{ $heroImageUrl }}" alt="Hero Image" class="h q r ua object-cover" />
      </div>

      <!-- Hero Content -->
      <div class="bb ze ki xn 2xl:ud-px-0">
        <div class="tc _o">
          <div class="animate_left jn/2">
            <h1 class="fk vj zp or kk wm wb">{{ $lp && !empty($lp->hero_title) ? $lp->hero_title : 'Kita Membantu Sesama' }}</h1>
            <p class="fq">{{ $lp && !empty($lp->hero_description) ? $lp->hero_description : 'menjadi organisasi sosial kemanusiaan internasional yang unggul dan profesional untuk kehidupan\n              kemanusiaan yang lebih baik' }}</p>

            <div class="tc tf yo zf mb">
              @php
                $heroButtonActive = $lp && !empty($lp->hero_button_active);
                $heroButtonLink = $lp && !empty($lp->hero_button_link) ? $lp->hero_button_link : '#program';

                $heroPhone = $lp && !empty($lp->hero_whatsapp_active) && !empty($lp->hero_whatsapp_number) ? $lp->hero_whatsapp_number : ($lp && !empty($lp->phone_number) ? $lp->phone_number : '+62 895-6210-93500');
                $heroPhoneStr = is_array($heroPhone) ? implode('', array_filter($heroPhone)) : $heroPhone;
                $heroPhoneDigits = preg_replace('/\D+/', '', $heroPhoneStr);
                $heroWa = $heroPhoneDigits ? 'https://wa.me/'.$heroPhoneDigits : 'https://wa.me/62895621093500';
              @endphp

              @if($heroButtonActive)
                <a href="{{ $heroButtonLink }}" class="ek jk lk gh gi hi rg ml il vc _d _l vc items-center inline-flex">Lihat Program Kami</a>
              @else
                <a href="#program" class="ek jk lk gh gi hi rg ml il vc _d _l vc items-center inline-flex">Lihat Program Kami</a>
              @endif

              <span class="tc sf">
                <a href="{{ $heroWa }}" aria-label="Kontak via WhatsApp {{ $heroPhone }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 ek xj kk wm">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" class="sc mr-2" aria-hidden="true">
                    <path d="M20.52 3.48A11.89 11.89 0 0012.02.12C6.08.12 1.19 4.99 1.19 10.93c0 1.93.5 3.82 1.44 5.5L.12 23.88l7.61-2.01a11.8 11.8 0 005.29 1.21h.01c5.94 0 10.83-4.89 10.83-10.83 0-3-1.17-5.83-3.65-7.57z" fill="#25D366" />
                    <path d="M17.2 14.54c-.3-.15-1.76-.86-2.03-.96-.27-.1-.47-.15-.67.15-.2.3-.76.96-.93 1.16-.17.2-.35.22-.65.07-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.48-1.74-1.65-2.04-.17-.3-.02-.46.13-.61.13-.13.3-.36.45-.54.15-.18.2-.3.3-.5.1-.2 0-.37-.02-.52-.02-.15-.67-1.6-.92-2.19-.24-.57-.49-.49-.67-.5l-.57-.01c-.2 0-.52.07-.79.37-.27.3-1.03 1.01-1.03 2.46 0 1.45 1.05 2.85 1.2 3.05.15.2 2.08 3.17 5.04 4.44 2.96 1.27 2.96.85 3.49.8.53-.05 1.73-.7 1.98-1.37.25-.66.25-1.22.17-1.37-.07-.15-.27-.24-.57-.39z" fill="#FFF" />
                  </svg>
                  WhatsApp {{ $heroPhone }}
                </a>
                <span class="inline-block">untuk berbicara dengan anggota tim kami</span>
              </span>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- ===== Hero End ===== -->

    <!-- ===== Small Features Start ===== -->
    <section id="features">
      <div class="bb ze ki yn 2xl:ud-px-12.5">
        <div class="tc uf zo xf ap zf bp mq">
          @php
            $lp = $landingProfile ?? null;
            $features = $lp && is_array($lp->features) && count($lp->features) ? $lp->features : [
              ['title' => 'Transparansi', 'description' => 'Keterbukaan penuh dalam pengelolaan dana dan program untuk membangun kepercayaan publik.'],
              ['title' => 'Amanah', 'description' => 'Menjalankan tanggung jawab dengan penuh integritas dan akuntabilitas kepada donatur.'],
              ['title' => 'Profesional', 'description' => 'Dikelola oleh tim berpengalaman dengan standar tata kelola organisasi yang baik.'],
            ];
            $iconClasses = ['mh','nh','oh'];
          @endphp

          @foreach($features as $feat)
            <div class="animate_top kn to/3 tc cg oq">
              <div class="tc wf xf cf ae cd rg {{ $iconClasses[$loop->index % count($iconClasses)] }}">
                <!-- Generic feature icon (keeps consistent style) -->
                <svg class="w-12 h-12" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <circle cx="24" cy="24" r="20" fill="#E8F5E9"/>
                  <path d="M24 12C17.4 12 12 17.4 12 24C12 30.6 17.4 36 24 36C30.6 36 36 30.6 36 24C36 17.4 30.6 12 24 12ZM24 33C19 33 15 29 15 24C15 19 19 15 24 15C29 15 33 19 33 24C33 29 29 33 24 33Z" fill="#4CAF50"/>
                  <path d="M22 28L18 24L19.4 22.6L22 25.2L28.6 18.6L30 20L22 28Z" fill="#4CAF50"/>
                </svg>
              </div>
              <div>
                <h4 class="ek yj go kk wm xb">{{ $feat['title'] }}</h4>
                <p>{{ $feat['description'] }}</p>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </section>
    <!-- ===== Small Features End ===== -->

    <!-- ===== About Start ===== -->
    <section id="tentang-kami" class="ji gp uq 2xl:ud-py-35 pg">
      <div class="bb ze ki xn wq">
        <div class="tc wf gg qq">
          <!-- About Images -->
          <div class="animate_left xc gn gg jn/2 i">
            <div class="relative">
              <img src="{{ asset("frontend/images/shape-05.svg") }}" alt="Shape" class="h -ud-left-5 x" />
              @php
                $lp = $landingProfile ?? null;
                $vmImage = $lp && !empty($lp->vision_mission_image) ? $lp->vision_mission_image : null;
                if ($vmImage) {
                  $vmImageUrl = preg_match('/^data:|^https?:\/\//', $vmImage) ? $vmImage : asset('storage/' . $vmImage);
                }
              @endphp

              @if(!empty($vmImageUrl))
                <img src="{{ $vmImageUrl }}" alt="Vision & Mission" class="h q r ua object-cover" />
              @else
                <!-- SVG Illustration -->
               <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.dev/svgjs" width="700" height="600" preserveAspectRatio="none" viewBox="0 0 700 600">
    <g mask="url(&quot;#SvgjsMask1143&quot;)" fill="none">
        <g mask="url(&quot;#SvgjsMask1144&quot;)">
            <path d="M319 231L297 253L297 275L297 297L297 319M341 77L319 99L319 121L319 143L319 165L297 187L297 209M297 407L297 429L275 451L275 473M319 297L341 319M319 363L319 385L319 407L341 429L341 451L319 473M341 -11L341 11L341 33L341 55L341 77L341 99L341 121L341 143L341 165L319 187L319 209L319 231L319 253L319 275L319 297L319 319L319 341L319 363L297 385L297 407L319 429" stroke="rgba(55, 159, 208, 1)" stroke-width="3.67"></path>
            <path d="M313.5 429 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM291.5 319 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM291.5 209 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM269.5 473 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM335.5 319 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM313.5 473 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0z" fill="rgba(55, 159, 208, 1)"></path>
            <path d="M363 253L341 275L341 297M341 209L363 231M363 165L363 187L385 209L385 231M363 341L341 363M363 -11L363 11L363 33L363 55L363 77L363 99L363 121L363 143L363 165L341 187L341 209L341 231L363 253L363 275L363 297L363 319L363 341L363 363" stroke="rgba(55, 159, 208, 1)" stroke-width="3.67"></path>
            <path d="M357.5 363 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM335.5 297 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM357.5 231 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM379.5 231 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM335.5 363 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0z" fill="rgba(55, 159, 208, 1)"></path>
            <path d="M319 55L319 77M319 -11L319 11L319 33L319 55L297 77L297 99" stroke="rgba(255, 98, 0, 1)" stroke-width="3.67"></path>
            <path d="M291.5 99 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM313.5 77 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0z" fill="rgba(255, 98, 0, 1)"></path>
            <path d="M385 77L385 99L385 121L385 143L385 165M407 231L407 253L407 275L407 297L407 319L407 341M363 407L385 429M363 451L385 473M363 517L385 539M385 55L407 77M385 -11L385 11L385 33L385 55L385 77L407 99L407 121L407 143L407 165L407 187L407 209L407 231L385 253L385 275L385 297L385 319L385 341L385 363L385 385L363 407L363 429L363 451L363 473L363 495L363 517L363 539" stroke="rgba(55, 159, 208, 1)" stroke-width="3.67"></path>
            <path d="M357.5 539 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM379.5 165 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM401.5 341 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM379.5 429 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM379.5 473 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM379.5 539 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM401.5 77 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0z" fill="rgba(55, 159, 208, 1)"></path>
            <path d="M275 143L297 165M275 297L253 319L253 341L253 363L253 385M275 121L297 143M275 275L253 297M297 -11L297 11L297 33L297 55L275 77L275 99L275 121L275 143L275 165L275 187L275 209L275 231L275 253L275 275L275 297L275 319L275 341" stroke="rgba(0, 171, 79, 1)" stroke-width="3.67"></path>
            <path d="M269.5 341 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM291.5 165 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM247.5 385 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM291.5 143 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM247.5 297 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0z" fill="rgba(0, 171, 79, 1)"></path>
            <path d="M407 -11L407 11" stroke="rgba(0, 171, 79, 1)" stroke-width="3.67"></path>
            <path d="M401.5 11 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0z" fill="rgba(0, 171, 79, 1)"></path>
            <path d="M231 253L209 275L209 297L209 319M253 187L231 209L209 231M231 231L253 253L253 275M253 209L253 231M275 -11L275 11L275 33L275 55L253 77L253 99L253 121L253 143L253 165L253 187L253 209L231 231L231 253L231 275L231 297L231 319" stroke="rgba(55, 159, 208, 1)" stroke-width="3.67"></path>
            <path d="M225.5 319 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM203.5 319 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM203.5 231 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM247.5 275 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM247.5 231 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0z" fill="rgba(55, 159, 208, 1)"></path>
            <path d="M429 253L429 275L429 297L429 319L429 341L429 363L429 385L429 407M429 187L451 209L451 231L473 253M429 99L451 121L451 143M473 385L451 407M429 77L451 99M429 165L451 187M429 -11L429 11L429 33L429 55L429 77L429 99L429 121L429 143L429 165L429 187L429 209L429 231L429 253L451 275L451 297L451 319L451 341L451 363L473 385L473 407L473 429L473 451L473 473L473 495" stroke="rgba(0, 171, 79, 1)" stroke-width="3.67"></path>
            <path d="M467.5 495 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM423.5 407 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM467.5 253 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM445.5 143 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM445.5 407 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM445.5 99 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM445.5 187 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0z" fill="rgba(0, 171, 79, 1)"></path>
            <path d="M187 319L187 341L187 363L187 385L187 407M187 539L209 561M165 341L143 363L143 385L143 407L143 429M209 187L209 209M187 275L165 297L165 319L143 341L121 363L121 385L121 407L121 429M165 429L187 451L209 473L209 495L209 517M253 -11L253 11L253 33L253 55L231 77L231 99L231 121L231 143L209 165L209 187L187 209L187 231L187 253L187 275L187 297L187 319L165 341L165 363L165 385L165 407L165 429L165 451L187 473L187 495L187 517L187 539L187 561" stroke="rgba(255, 98, 0, 1)" stroke-width="3.67"></path>
            <path d="M181.5 561 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM181.5 407 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM203.5 561 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM137.5 429 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM203.5 209 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM115.5 429 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM203.5 517 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0z" fill="rgba(255, 98, 0, 1)"></path>
            <path d="M451 11L451 33L451 55L451 77M473 187L495 209M473 143L495 165L495 187M451 -11L451 11L473 33L473 55L473 77L473 99L473 121L473 143L473 165L473 187L473 209" stroke="rgba(0, 171, 79, 1)" stroke-width="3.67"></path>
            <path d="M467.5 209 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM445.5 77 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM489.5 209 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM489.5 187 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0z" fill="rgba(0, 171, 79, 1)"></path>
            <path d="M187 121L187 143L187 165M187 99L209 121L209 143M209 77L209 99M231 -11L231 11L231 33L231 55L209 77L187 99L187 121L165 143L165 165L165 187L165 209L165 231L165 253" stroke="rgba(255, 98, 0, 1)" stroke-width="3.67"></path>
            <path d="M159.5 253 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM181.5 165 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM203.5 143 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM203.5 99 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0z" fill="rgba(255, 98, 0, 1)"></path>
            <path d="M473 -11L473 11L495 33L495 55L495 77L495 99L495 121" stroke="rgba(0, 171, 79, 1)" stroke-width="3.67"></path>
            <path d="M489.5 121 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0z" fill="rgba(0, 171, 79, 1)"></path>
            <path d="M209 11L187 33L187 55L165 77M209 -11L209 11L209 33L209 55L187 77" stroke="rgba(255, 98, 0, 1)" stroke-width="3.67"></path>
            <path d="M181.5 77 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM159.5 77 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0z" fill="rgba(255, 98, 0, 1)"></path>
            <path d="M517 187L539 209L539 231L539 253M517 121L539 143L539 165L539 187M495 231L517 253M517 99L539 121M495 -11L495 11L517 33L517 55L517 77L517 99L517 121L517 143L517 165L517 187L517 209L495 231L495 253L495 275L495 297L495 319L495 341" stroke="rgba(55, 159, 208, 1)" stroke-width="3.67"></path>
            <path d="M489.5 341 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM533.5 253 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM533.5 187 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM511.5 253 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM533.5 121 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0z" fill="rgba(55, 159, 208, 1)"></path>
            <path d="M143 99L121 121L121 143L121 165M143 33L121 55L121 77M143 187L121 209L121 231L121 253M187 -11L165 11L143 33L143 55L143 77L143 99L143 121L143 143L143 165L143 187L143 209L143 231L143 253" stroke="rgba(0, 171, 79, 1)" stroke-width="3.67"></path>
            <path d="M137.5 253 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM115.5 165 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM115.5 77 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM115.5 253 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0z" fill="rgba(0, 171, 79, 1)"></path>
        </g>
    </g>
    <defs>
        <mask id="SvgjsMask1143">
            <rect width="700" height="600" fill="#ffffff"></rect>
        </mask>
        <mask id="SvgjsMask1144">
            <rect width="700" height="600" fill="white"></rect>
            <path d="M316.25 429 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM294.25 319 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM294.25 209 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM272.25 473 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM338.25 319 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM316.25 473 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0z" fill="black"></path>
            <path d="M360.25 363 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM338.25 297 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM360.25 231 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM382.25 231 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM338.25 363 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0z" fill="black"></path>
            <path d="M294.25 99 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM316.25 77 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0z" fill="black"></path>
            <path d="M360.25 539 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM382.25 165 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM404.25 341 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM382.25 429 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM382.25 473 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM382.25 539 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM404.25 77 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0z" fill="black"></path>
            <path d="M272.25 341 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM294.25 165 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM250.25 385 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM294.25 143 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM250.25 297 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0z" fill="black"></path>
            <path d="M404.25 11 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0z" fill="black"></path>
            <path d="M228.25 319 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM206.25 319 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM206.25 231 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM250.25 275 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM250.25 231 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0z" fill="black"></path>
            <path d="M470.25 495 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM426.25 407 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM470.25 253 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM448.25 143 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM448.25 407 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM448.25 99 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM448.25 187 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0z" fill="black"></path>
            <path d="M184.25 561 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM184.25 407 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM206.25 561 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM140.25 429 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM206.25 209 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM118.25 429 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM206.25 517 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0z" fill="black"></path>
            <path d="M470.25 209 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM448.25 77 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM492.25 209 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM492.25 187 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0z" fill="black"></path>
            <path d="M162.25 253 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM184.25 165 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM206.25 143 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM206.25 99 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0z" fill="black"></path>
            <path d="M492.25 121 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0z" fill="black"></path>
            <path d="M184.25 77 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM162.25 77 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0z" fill="black"></path>
            <path d="M492.25 341 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM536.25 253 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM536.25 187 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM514.25 253 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM536.25 121 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0z" fill="black"></path>
            <path d="M140.25 253 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM118.25 165 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM118.25 77 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0zM118.25 253 a2.75 2.75 0 1 0 5.5 0 a2.75 2.75 0 1 0 -5.5 0z" fill="black"></path>
        </mask>
    </defs>
</svg>
              @endif
              <!-- End SVG Illustration -->
            </div>
          </div>

          <!-- About Content -->
          <div class="animate_right jn/2">
            @php
              $lp = $landingProfile ?? null;
              $aboutTitle = $lp && !empty($lp->vision_title) ? $lp->vision_title : '';
              $aboutDesc = $lp && !empty($lp->vision_description) ? $lp->vision_description : '';
              $missions = [];
              if ($lp && !empty($lp->mission_description)) {
                $missions = preg_split('/\r\n|\r|\n/', trim($lp->mission_description));
                $missions = array_filter(array_map('trim', $missions));
              }
              if (empty($missions)) {
                $missions = [
                  'Memberikan bantuan kemanusiaan yang tepat sasaran',
                  'Memberdayakan masyarakat untuk hidup mandiri',
                  'Membangun kemitraan strategis dengan berbagai pihak',
                  'Mengelola organisasi secara profesional dan transparan',
                ];
              }
            @endphp

            <h4 class="ek yj mk gb">Tentang Kita Membantu Sesama</h4>
            <h5 class="ek zj kk wm mb-5">{{ $aboutTitle }}</h5>
            <!-- <p class="uo mb-4"></p> -->
            <br>
            <div class="mb-6">
              <h5 class="ek zj kk wm mb-5">Visi Kami</h5>
              <p class="uo">{!! nl2br(e($aboutDesc)) !!}</p>
            </div>
            <br>
            <div class="mb-6">
              <h5 class="ek zj kk wm mb-3">Misi Kami</h5>
              <ul class="list-disc list-inside space-y-2">
                @foreach($missions as $m)
                  <li>{{ $m }}</li>
                @endforeach
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- ===== About End ===== -->

@if(($landingBulletinsTotal ?? 0) > 0)
    <!-- ===== Services Start ===== -->
    <section class="lj tp kr">
      <!-- Section Title Start -->
      <div
        x-data="{ sectionTitle: `Bulletin`, sectionTitleText: `Bulletin berisi pengumuman, artikel, dan informasi terbaru mengenai kegiatan serta program kami untuk masyarakat.`}">
        <div class="animate_top bb ze rj ki xn vq">
          <h2 x-text="sectionTitle" class="fk vj pr kk wm on/5 gq/2 bb _b">
          </h2>
          <p class="bb on/5 wo/5 hq" x-text="sectionTitleText"></p>
        </div>
      </div>
      <!-- Section Title End -->

      <div class="bb ze ki xn yq mb en">
        <div id="bulletins" class="wc qf pn xo ng">
          @foreach($landingBulletins ?? [] as $b)
            <div class="animate_top sg oi pi zq ml il am cn _m bulletin-item">
              <img src="{{ asset('frontend/images/icon-04.svg') }}" alt="Bulletin" />
              @php
                $fileUrl = '#';
                if (!empty($b->file)) {
                    $fileUrl = preg_match('/^https?:\/\//', $b->file) ? $b->file : asset('storage/' . $b->file);
                }
              @endphp
              <h4 class="ek zj kk wm nb _b"><a href="{{ $fileUrl }}" target="_blank" rel="noopener noreferrer">{{ $b->name }}</a></h4>
              <p class="text-sm text-gray-600">{{ $b->date->format('d M Y') }}</p>
            </div>
          @endforeach
        </div>

        @if(!empty($landingBulletinsTotal) && $landingBulletinsTotal > 6)
          <div class="tc mt-6">
            <button id="load-more-bulletins" class="ek rg ml il vi mi">Load more</button>
          </div>
        @endif
      </div>

      @push('scripts')
      <script>
        (function(){
          let currentPage = 1;
          const perPage = 6;
          const btn = document.getElementById('load-more-bulletins');
          const container = document.getElementById('bulletins');

          if (btn) {
            btn.addEventListener('click', async function(){
              currentPage++;
              btn.disabled = true;
              btn.textContent = 'Loading...';
              try {
                const res = await fetch(`/api/landing-bulletins?page=${currentPage}&per_page=${perPage}`);
                const json = await res.json();
                if (json.success && json.data.length) {
                  json.data.forEach(function(b){
                    const div = document.createElement('div');
                    div.className = 'animate_top sg oi pi zq ml il am cn _m bulletin-item';
                    const file = b.file && /^https?:\/\//.test(b.file) ? b.file : (b.file ? `/storage/${b.file}` : '#');
                    const date = new Date(b.date).toLocaleDateString('id-ID',{day:'2-digit', month:'short', year:'numeric'});
                    div.innerHTML = `<img src="/frontend/images/icon-04.svg" alt="Bulletin" /><h4 class="ek zj kk wm nb _b"><a href="${file}" target="_blank" rel="noopener noreferrer">${b.name}</a></h4><p class="text-sm text-gray-600">${date}</p>`;
                    container.appendChild(div);
                  });
                  if (!json.has_more) {
                    btn.remove();
                  } else {
                    btn.disabled = false;
                    btn.textContent = 'Load more';
                  }
                } else {
                  btn.remove();
                }
              } catch (e) {
                console.error(e);
                btn.disabled = false;
                btn.textContent = 'Load more';
              }
            });
          }
        })();
      </script>
      @endpush
    </section>
    <!-- ===== Services End ===== -->
@endif

@if(($landingProgramsTotal ?? 0) > 0)
  <div id="program" class="bb ze ki xn 2xl:ud-px-0 jb">
        <div class="animate_top bb ze rj ki xn vq">
          <h2 class="fk vj pr kk wm on/5 gq/2 bb _b">Program Kami</h2>
          <p class="bb on/5 wo/5 hq">Program-program unggulan kami meliputi pendidikan, kesehatan, dan bantuan darurat yang dirancang untuk menciptakan dampak berkelanjutan bagi komunitas.</p>
        </div>

        <div class="bb ze ki xn 2xl:ud-px-0 jb">
          <div id="programs" class="wc qf pn xo zf iq program-gallery">
            @foreach($landingPrograms ?? [] as $p)
              @php
                $image = asset('frontend/images/project-01.png');
                if (!empty($p->image_url)) {
                    $image = preg_match('/^https?:\/\//', $p->image_url) ? $p->image_url : asset('storage/' . $p->image_url);
                }
                $excerpt = strip_tags($p->description ?? '');
                if (strlen($excerpt) > 120) $excerpt = substr($excerpt, 0, 117) . '...';
              @endphp

              <div class="animate_top sg vk rm xm">
                <div class="c rc i z-1 pg">
                  <a href="{{ $image }}" class="rc" data-caption="{{ $p->name }}">
                    <img class="w-full program-image" src="{{ $image }}" alt="{{ $p->name }}" />
                  </a>
                  <div class="im h r s df vd yc wg tc wf xf al hh/20 nl il z-10">
                    <span class="vc ek rg lk gh sl ml il gi hi" style="cursor: pointer;">Read More</span>
                  </div>
                </div>

                <div class="yh">
                  <h4 class="ek tj ml il kk wm xl eq lb">
                    <a href="#">{{ $p->name }}</a>
                  </h4>
                  <p>{{ $excerpt }}</p>
                </div>
              </div>
            @endforeach
          </div>

          @if(!empty($landingProgramsTotal) && $landingProgramsTotal > 6)
            <div class="tc mt-6">
              <button id="load-more-programs" class="ek rg ml il vi mi">Load more</button>
            </div>
          @endif
        </div>
      </div>

      @push('scripts')
      <script>
        (function(){
          // Initialize baguetteBox for program gallery with retry (safe if script loads later)
          const initProgramGallery = () => {
            if (typeof baguetteBox !== 'undefined') {
              try {
                baguetteBox.run('.program-gallery', {
                  animation: 'slideIn',
                  captions: true
                });
              } catch (e) {
                console.error('baguetteBox.run failed', e)
              }
              return true
            }
            return false
          }

          // Try once, then poll until library available (clears automatically)
          if (!initProgramGallery()) {
            const ib = setInterval(() => { if (initProgramGallery()) clearInterval(ib) }, 200)
          }

          // Attach Read More click handlers to trigger lightbox on corresponding image
          const attachReadMoreHandlers = () => {
            document.querySelectorAll('.program-gallery .vc').forEach((el) => {
              if (el.getAttribute('data-has-handler')) return
              el.setAttribute('data-has-handler', '1')
              el.addEventListener('click', (ev) => {
                ev.preventDefault()
                const anchor = el.closest('.c')?.querySelector('a.rc')
                if (anchor) {
                  // trigger click on anchor which baguetteBox should intercept
                  anchor.click()
                }
              })
            })
          }
          // run once now
          attachReadMoreHandlers()

          let currentPage = 1;
          const perPage = 6;
          const btn = document.getElementById('load-more-programs');
          const container = document.getElementById('programs');

          if (btn) {
            btn.addEventListener('click', async function(){
              currentPage++;
              btn.disabled = true;
              btn.textContent = 'Loading...';
              try {
                const res = await fetch(`/api/landing-programs?page=${currentPage}&per_page=${perPage}`);
                const json = await res.json();
                if (json.success && json.data.length) {
                  json.data.forEach(function(p){
                    const div = document.createElement('div');
                    div.className = 'animate_top sg vk rm xm';
                    const img = p.image_url && /^https?:\/\//.test(p.image_url) ? p.image_url : (p.image_url ? `/storage/${p.image_url}` : '/frontend/images/project-01.png');
                    const excerpt = (p.description || '').replace(/<[^>]+>/g, '').slice(0, 117) + (p.description && p.description.length > 120 ? '...' : '');
                    div.innerHTML = `<div class="c rc i z-1 pg"><a href="${img}" class="rc" data-caption="${p.name}"><img class="w-full program-image" src="${img}" alt="${p.name}" /></a><div class="im h r s df vd yc wg tc wf xf al hh/20 nl il z-10"><span class="vc ek rg lk gh sl ml il gi hi" style="cursor: pointer;">Read More</span></div></div><div class="yh"><h4 class="ek tj ml il kk wm xl eq lb"><a href="#">${p.name}</a></h4><p>${excerpt}</p></div>`;
                    container.appendChild(div);
                  });
                  // Reinitialize baguetteBox after adding new images
                  if (typeof baguetteBox !== 'undefined') {
                    baguetteBox.run('.program-gallery', {
                      animation: 'slideIn',
                      captions: true
                    });
                  }
                  if (!json.has_more) {
                    btn.remove();
                  } else {
                    btn.disabled = false;
                    btn.textContent = 'Load more';
                  }
                } else {
                  btn.remove();
                }
              } catch (e) {
                console.error(e);
                btn.disabled = false;
                btn.textContent = 'Load more';
              }
            });
          }


        })();
      </script>
      @endpush
@endif

        </div>
      </div>
    </section>
    <!-- ===== Projects End ===== -->

    <!-- ===== Galeri Kegiatan Start ===== -->
    <section id="galeri" class="ji gp uq">
      <!-- Section Title Start -->
      <div class="animate_top bb ze rj ki xn vq">
        <h2 class="fk vj pr kk wm on/5 gq/2 bb _b">Galeri Kegiatan</h2>
        <p class="bb on/5 wo/5 hq">Dokumentasi kegiatan dan program yang telah kami laksanakan untuk membantu sesama.</p>
      </div>
      <!-- Section Title End -->

      <div class="bb ye ki xn vq jb jo">
        <div id="galeri-items" class="wc qf pn xo zf iq">
          @foreach($landingKegiatan ?? [] as $k)
            @php
              $img = asset('frontend/images/project-01.png');
              if (!empty($k->images)) {
                  $imgs = json_decode($k->images, true) ?: [];
                  if (is_array($imgs) && count($imgs) > 0 && $imgs[0]) {
                      $img = preg_match('/^https?:\/\//', $imgs[0]) ? $imgs[0] : asset('storage/' . $imgs[0]);
                  }
              }
              $excerpt = strip_tags($k->description ?? '');
              if (strlen($excerpt) > 120) $excerpt = substr($excerpt, 0, 117) . '...';
              $slug = \Illuminate\Support\Str::slug($k->title);
            @endphp

            <div class="animate_top sg vk rm xm">
              <div class="c rc i z-1 pg">
                <a href="{{ route('frontend.blog-single', ['slug' => $slug]) }}" class="rc">
                  <img class="w-full program-image" src="{{ $img }}" alt="{{ $k->title }}" />
                </a>
                <div class="im h r s df vd yc wg tc wf xf al hh/20 nl il z-10">
                  <a href="{{ route('frontend.blog-single', ['slug' => $slug]) }}" class="vc ek rg lk gh sl ml il gi hi">View</a>
                </div>
              </div>
              <div class="yh">
                <h4 class="ek tj ml il kk wm xl eq lb">
                  <a href="{{ route('frontend.blog-single', ['slug' => $slug]) }}">{{ $k->title }}</a>
                </h4>
                <p>{{ $excerpt }}</p>
              </div>
            </div>
          @endforeach
        </div>

        @if(!empty($landingKegiatanTotal) && $landingKegiatanTotal > 6)
          <div class="tc mt-6">
            <button id="load-more-galeri" class="ek rg ml il vi mi">Load more</button>
          </div>
        @endif
      </div>
    </section>
    <!-- ===== Galeri Kegiatan End ===== -->

    @push('scripts')
    <script>
      (function(){
        let currentPage = 1;
        const perPage = 6;
        const btn = document.getElementById('load-more-galeri');
        const container = document.getElementById('galeri-items');

        if (btn) {
          btn.addEventListener('click', async function(){
            currentPage++;
            btn.disabled = true;
            btn.textContent = 'Loading...';
            try {
              const res = await fetch(`/api/landing-kegiatan?page=${currentPage}&per_page=${perPage}`);
              const json = await res.json();
              if (json.success && json.data.length) {
                json.data.forEach(function(k){
                  const div = document.createElement('div');
                  div.className = 'animate_top sg vk rm xm';
                  const imgs = k.images ? JSON.parse(k.images) : [];
                  const img = imgs.length && imgs[0] && /^https?:\/\//.test(imgs[0]) ? imgs[0] : (imgs.length && imgs[0] ? `/storage/${imgs[0]}` : '/frontend/images/project-01.png');
                  const excerpt = (k.description || '').replace(/<[^>]+>/g, '').slice(0, 117) + (k.description && k.description.length > 120 ? '...' : '');
                  div.innerHTML = `<div class="c rc i z-1 pg"><a href="/blog-single/${k.id}" class="rc"><img class="w-full program-image" src="${img}" alt="${k.title}" /></a><div class="im h r s df vd yc wg tc wf xf al hh/20 nl il z-10"><a href="/blog-single/${k.id}" class="vc ek rg lk gh sl ml il gi hi">View</a></div></div><div class="yh"><h4 class="ek tj ml il kk wm xl eq lb"><a href="/blog-single/${k.id}">${k.title}</a></h4><p>${excerpt}</p></div>`;
                  container.appendChild(div);
                });
                if (!json.has_more) {
                  btn.remove();
                } else {
                  btn.disabled = false;
                  btn.textContent = 'Load more';
                }
              } else {
                btn.remove();
              }
            } catch (e) {
              console.error(e);
              btn.disabled = false;
              btn.textContent = 'Load more';
            }
          });
        }
      })();
    </script>
    @endpush

    <!-- ===== Cara Berdonasi Start ===== -->
    <section id="cara-donasi" class="i pg fh rm ji gp uq">
      <!-- Bg Shapes -->
      <img src="{{ asset('frontend/images/shape-06.svg') }}" alt="Shape" class="h aa y" />
      <img src="{{ asset('frontend/images/shape-03.svg') }}" alt="Shape" class="h ca u" />
      <img src="{{ asset('frontend/images/shape-07.svg') }}" alt="Shape" class="h w da ee" />
      <img src="{{ asset('frontend/images/shape-12.svg') }}" alt="Shape" class="h p s" />
      <img src="{{ asset('frontend/images/shape-13.svg') }}" alt="Shape" class="h r q" />

      <!-- Section Title Start -->
      <div
        x-data="{ sectionTitle: `Cara Berdonasi`, sectionTitleText: `Berbagai cara mudah untuk Anda berpartisipasi membantu sesama melalui donasi.`}">
        <div class="animate_top bb ze rj ki xn vq">
          <h2 x-text="sectionTitle" class="fk vj pr kk wm on/5 gq/2 bb _b">
          </h2>
          <p class="bb on/5 wo/5 hq" x-text="sectionTitleText"></p>
        </div>
      </div>
      <!-- Section Title End -->

      <div class="bb ze ki xn yq mb en">
        <div class="wc qf pn xo ng">
          @php
            $bankAccounts = [];
            if (!empty($landingProfile->bank_account_1)) {
                $bankAccounts = is_string($landingProfile->bank_account_1) 
                    ? json_decode($landingProfile->bank_account_1, true) 
                    : $landingProfile->bank_account_1;
                $bankAccounts = is_array($bankAccounts) ? $bankAccounts : [];
            }
          @endphp

          @foreach($bankAccounts as $account)
            <!-- Donation Method -->
            <div class="animate_top sg oi pi zq ml il am cn _m">
              <div class="tc wf xf ie ld rg ml il mb-4">
                <svg class="th lm ml il" width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M2 5C2 3.89543 2.89543 3 4 3H20C21.1046 3 22 3.89543 22 5V8H2V5Z" fill="currentColor"/>
                  <path d="M2 10H22V19C22 20.1046 21.1046 21 20 21H4C2.89543 21 2 20.1046 2 19V10Z" fill="currentColor"/>
                </svg>
              </div>
              <h4 class="ek zj kk wm nb _b">{{ $account['label'] ?? 'Transfer Bank' }}</h4>
              <div class="text-sm space-y-1">
                <div class="flex items-center justify-between gap-3">
                  <p class="font-semibold flex-1 min-w-0 truncate">{{ $account['value'] ?? '' }} <button
                    onclick="copyToClipboard('{{ $account['value'] ?? '' }}', this)"
                    class="inline-flex items-center justify-center w-7 h-7 rounded border border-gray-200 hover:bg-gray-100 transition-colors flex-shrink-0"
                    title="Copy nomor rekening">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M16 1H4C2.9 1 2 1.9 2 3V17H4V3H16V1ZM19 5H8C6.9 5 6 5.9 6 7V21C6 22.1 6.9 23 8 23H19C20.1 23 21 22.1 21 21V7C21 5.9 20.1 5 19 5ZM19 21H8V7H19V21Z" fill="currentColor"/>
                    </svg>
                  </button></p>
                </div>
                <p>a.n. {{ $landingProfile->organization_name ?? 'Yayasan Kita Membantu Sesama' }}</p>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </section>
    <!-- ===== Cara Berdonasi End ===== -->

    <script>
      function copyToClipboard(text, button) {
        // Check if clipboard API is available
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(text).then(function() {
            const originalHTML = button.innerHTML;
            button.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" fill="green"/></svg>';
            setTimeout(function() {
              button.innerHTML = originalHTML;
            }, 2000);
          }).catch(function(err) {
            alert('Gagal menyalin: ' + err);
          });
        } else {
          // Fallback for older browsers
          const textarea = document.createElement('textarea');
          textarea.value = text;
          textarea.style.position = 'fixed';
          textarea.style.opacity = '0';
          document.body.appendChild(textarea);
          textarea.select();
          try {
            document.execCommand('copy');
            const originalHTML = button.innerHTML;
            button.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" fill="green"/></svg>';
            setTimeout(function() {
              button.innerHTML = originalHTML;
            }, 2000);
          } catch (err) {
            alert('Gagal menyalin: ' + err);
          }
          document.body.removeChild(textarea);
        }
      }
    </script>

    <!-- ===== Counter Start ===== -->
    <section id="transparansi" class="i pg qh rm ji hp">
      <img src="{{ asset("frontend/images/shape-11.svg") }}" alt="Shape" class="of h ga ha ke" />
      <img src="{{ asset("frontend/images/shape-07.svg") }}" alt="Shape" class="h ia o ae jf" />
      <img src="{{ asset("frontend/images/shape-14.svg") }}" alt="Shape" class="h ja ka" />
      <img src="{{ asset("frontend/images/shape-15.svg") }}" alt="Shape" class="h q p" />

      <div class="bb ze i va ki xn br">
        <div class="tc uf sn tn xf un gg">
          <div class="animate_top me/5 ln rj">
            <h2 class="gk vj zp or kk wm hc">{{ number_format($kantorCabangCount ?? 0) }}</h2>
            <p class="ek bk aq">Kantor Cabang</p>
          </div>
          <div class="animate_top me/5 ln rj">
            <h2 class="gk vj zp or kk wm hc">{{ number_format($donaturCount ?? 0) }}</h2>
            <p class="ek bk aq">Donatur</p>
          </div>
          <div class="animate_top me/5 ln rj">
            <h2 class="gk vj zp or kk wm hc">{{ number_format($fundraiserCount ?? 0) }}</h2>
            <p class="ek bk aq">Fundraiser</p>
          </div>
          <div class="animate_top me/5 ln rj">
            <h2 class="gk vj zp or kk wm hc">{{ number_format($penggalanganDanaCount ?? 0) }}</h2>
            <p class="ek bk aq">Penggalangan Dana</p>
          </div>
        </div>
      </div>
    </section>
    <!-- ===== Counter End ===== -->

    <!-- ===== Blog Start ===== -->
    <section class="ji gp uq">
      <!-- Section Title Start -->
      <div
        x-data="{ sectionTitle: `Kegiatan`, sectionTitleText: `Kegiatan kami mencakup agenda, workshop, dan aksi sosial yang bertujuan memberdayakan masyarakat serta mendukung program-program kemanusiaan.` }">
        <div class="animate_top bb ze rj ki xn vq">
          <h2 x-text="sectionTitle" class="fk vj pr kk wm on/5 gq/2 bb _b">
          </h2>
          <p class="bb on/5 wo/5 hq" x-text="sectionTitleText"></p>
        </div>
      </div>
      <!-- Section Title End -->

      <div class="bb ye ki xn vq jb jo">
        <div id="kegiatans" class="wc qf pn xo zf iq">
          @foreach($landingKegiatan ?? [] as $k)
            @php
              $img = asset('frontend/images/blog-01.png');
              if (!empty($k->images)) {
                  $imgs = json_decode($k->images, true) ?: [];
                  if (is_array($imgs) && count($imgs) > 0 && $imgs[0]) {
                      $img = preg_match('/^https?:\/\//', $imgs[0]) ? $imgs[0] : asset('storage/' . $imgs[0]);
                  }
              }
              $date = $k->activity_date ? \Carbon\Carbon::parse($k->activity_date)->format('d M, Y') : '';
              $excerpt = strip_tags($k->description ?? '');
              if (strlen($excerpt) > 120) $excerpt = substr($excerpt, 0, 117) . '...';
              $slug = \Illuminate\Support\Str::slug($k->title);
            @endphp

            <div class="animate_top sg vk rm xm">
              <div class="c rc i z-1 pg">
                <a href="{{ route('frontend.blog-single', ['slug' => $slug]) }}" class="rc">
                  <img class="w-full kegiatan-image" src="{{ $img }}" alt="Kegiatan" />
                </a>

                <div class="im h r s df vd yc wg tc wf xf al hh/20 nl il z-10">
                  <a href="{{ route('frontend.blog-single', ['slug' => $slug]) }}" class="vc ek rg lk gh sl ml il gi hi">Read More</a>
                </div>
              </div>

              <div class="yh">
                <div class="tc uf wf ag jq">
                  <div class="tc wf ag">
                    <img src="{{ asset('frontend/images/icon-man.svg') }}" alt="User" />
                    <p>{{ $k->organizer ?? 'Team' }}</p>
                  </div>
                  <div class="tc wf ag">
                    <img src="{{ asset('frontend/images/icon-calender.svg') }}" alt="Calender" />
                    <p>{{ $date }}</p>
                  </div>
                </div>
                <h4 class="ek tj ml il kk wm xl eq lb">
                  <a href="{{ route('frontend.blog-single', ['slug' => $slug]) }}">{{ $k->title }}</a>
                </h4>
                <p>{{ $excerpt }}</p>
              </div>
            </div>
          @endforeach
        </div>

        @if(!empty($landingKegiatanTotal) && $landingKegiatanTotal > 3)
          <div class="tc mt-6">
            <button id="load-more-kegiatan" class="ek rg ml il vi mi">Load more</button>
          </div>
        @endif
      </div> 

      <style>
        /* Kegiatan & Program image: fixed height and crop to avoid overly tall images */
        .kegiatan-image, .program-image { display: block; width: 100%; height: 220px; object-fit: cover; object-position: center; border-radius: 6px; }
        @media (min-width: 768px) { .kegiatan-image, .program-image { height: 260px; } }
        @media (min-width: 1024px) { .kegiatan-image, .program-image { height: 220px; } }
      </style>

      @push('scripts')
      <script>
        (function(){
          let currentPage = 1;
          const perPage = 3;
          const btn = document.getElementById('load-more-kegiatan');
          const container = document.getElementById('kegiatans');

          if (btn) {
            btn.addEventListener('click', async function(){
              currentPage++;
              btn.disabled = true;
              btn.textContent = 'Loading...';
              try {
                const res = await fetch(`/api/landing-kegiatan?page=${currentPage}&per_page=${perPage}`);
                const json = await res.json();
                if (json.success && json.data.length) {
                  json.data.forEach(function(k){
                    const imgJson = k.images ? (Array.isArray(k.images) ? k.images[0] : (k.images ? JSON.parse(k.images)[0] : null)) : null;
                    const img = imgJson && /^https?:\/\//.test(imgJson) ? imgJson : (imgJson ? `/storage/${imgJson}` : '/frontend/images/blog-01.png');
                    const date = k.activity_date ? new Date(k.activity_date).toLocaleDateString('id-ID',{day:'2-digit', month:'short', year:'numeric'}) : '';
                    const excerpt = (k.description || '').replace(/<[^>]+>/g, '').slice(0, 117) + ((k.description||'').length > 120 ? '...' : '');

                    const div = document.createElement('div');
                    div.className = 'animate_top sg vk rm xm';
                    div.innerHTML = `<div class="c rc i z-1 pg"><a href="/blog-single/${k.id}" class="rc"><img class="w-full kegiatan-image" src="${img}" alt="Kegiatan" /></a><div class="im h r s df vd yc wg tc wf xf al hh/20 nl il z-10"><a href="/blog-single/${k.id}" class="vc ek rg lk gh sl ml il gi hi">Read More</a></div></div><div class="yh"><div class="tc uf wf ag jq"><div class="tc wf ag"><img src="/frontend/images/icon-man.svg" alt="User" /><p>${k.organizer || 'Team'}</p></div><div class="tc wf ag"><img src="/frontend/images/icon-calender.svg" alt="Calender" /><p>${date}</p></div></div><h4 class="ek tj ml il kk wm xl eq lb"><a href="/blog-single/${k.id}">${k.title}</a></h4><p>${excerpt}</p></div>`;
                    container.appendChild(div);
                  });
                  if (!json.has_more) {
                    btn.remove();
                  } else {
                    btn.disabled = false;
                    btn.textContent = 'Load more';
                  }
                } else {
                  btn.remove();
                }
              } catch (e) {
                console.error(e);
                btn.disabled = false;
                btn.textContent = 'Load more';
              }
            });
          }
        })();
      </script>
      @endpush
    </section>
    <!-- ===== Blog End ===== -->

    <!-- ===== Contact Start ===== -->
    <section id="kontak" class="i pg fh rm ji gp uq">
      <!-- Bg Shapes -->
      <img src="{{ asset("frontend/images/shape-06.svg") }}" alt="Shape" class="h aa y" />
      <img src="{{ asset("frontend/images/shape-03.svg") }}" alt="Shape" class="h ca u" />
      <img src="{{ asset("frontend/images/shape-07.svg") }}" alt="Shape" class="h w da ee" />
      <img src="{{ asset("frontend/images/shape-12.svg") }}" alt="Shape" class="h p s" />
      <img src="{{ asset("frontend/images/shape-13.svg") }}" alt="Shape" class="h r q" />

      <!-- Section Title Start -->
      <div
        x-data="{ sectionTitle: `Kontak`, sectionTitleText: `Hubungi kami untuk informasi lebih lanjut, dukungan, atau kolaborasi—tim kami siap membantu.`}">
        <div class="animate_top bb ze rj ki xn vq">
          <h2 x-text="sectionTitle" class="fk vj pr kk wm on/5 gq/2 bb _b">
          </h2>
          <p class="bb on/5 wo/5 hq" x-text="sectionTitleText"></p>
        </div>
      </div>
      <!-- Section Title End -->

      <div class="i va bb ye ki xn wq jb mo">
        <div class="tc uf sn tf rn un zf xl:gap-10">
          <div class="animate_top w-full mn/5 to/3 vk sg hh sm yh rq i pg">
            <!-- Bg Shapes -->
            <img src="{{ asset("frontend/images/shape-03.svg") }}" alt="Shape" class="h la x wd" />
            <img src="{{ asset("frontend/images/shape-06.svg") }}" alt="Shape" class="h la ma ne kf" />

            @php
              $lp = $landingProfile ?? null;
              $contactEmail = $lp && !empty($lp->email) ? $lp->email : 'support@startup.com';
              $contactPhone = $lp && !empty($lp->phone_number) ? $lp->phone_number : '+009 8754 3433 223';
              $contactPhoneStr = is_array($contactPhone) ? implode('', array_filter($contactPhone)) : $contactPhone;
              $contactPhoneDigits = preg_replace('/\\D+/', '', $contactPhoneStr);
              $contactTel = $contactPhoneDigits ? 'tel:'.$contactPhoneDigits : '#';
                if ($lp && !empty($lp->address)) {
                  if (is_array($lp->address)) {
                    $addrItems = array_map(function($a) {
                      if (is_array($a)) {
                        return $a['value'] ?? ($a['label'] ?? null);
                      }
                      return (string)$a;
                    }, $lp->address);
                    $addressText = implode('; ', array_filter($addrItems));
                  } else {
                    $addressText = $lp->address;
                  }
                } else {
                  $addressText = '76/A, Green valle, Califonia USA.';
                }

              $banks = [];
                if ($lp) {
                  if (!empty($lp->bank_account_1)) {
                    $b1 = is_array($lp->bank_account_1) ? $lp->bank_account_1 : json_decode($lp->bank_account_1, true);
                    if ($b1) {
                      // ensure we push individual accounts if bank_account_1 is array of accounts
                      if (array_values($b1) === $b1) {
                        foreach ($b1 as $item) $banks[] = $item;
                      } else {
                        $banks[] = $b1;
                      }
                    }
                  }
                  if (!empty($lp->bank_account_2)) {
                    $b2 = is_array($lp->bank_account_2) ? $lp->bank_account_2 : json_decode($lp->bank_account_2, true);
                    if ($b2) $banks[] = $b2;
                  }
                }
            @endphp

            <div class="fb">
              <h4 class="wj kk wm cc">Alamat Email</h4>
              <p><a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a></p>
            </div>
            <div class="fb">
              <h4 class="wj kk wm cc">Lokasi Kantor</h4>
              <p>{{ $addressText }}</p>
            </div>
            <div class="fb">
              <h4 class="wj kk wm cc">Nomor Telepon</h4>
              <p><a href="{{ $contactTel }}">{{ $contactPhone }}</a></p>
            </div>
            <div class="fb">
              <h4 class="wj kk wm cc">Rekening Bank</h4>
              @if(count($banks) > 0)
                @foreach($banks as $b)
                  @php
                    $bankName = $b['bank_name'] ?? ($b['bank'] ?? ($b['label'] ?? null));
                    $accountNumber = $b['account_number'] ?? ($b['number'] ?? ($b['value'] ?? null));
                    $owner = $b['owner'] ?? ($b['account_name'] ?? null);
                    // if the bank entry is a plain string, show it directly
                    $isString = !is_array($b) && !is_object($b);
                  @endphp
                  @if($isString)
                    <p>{{ $b }}</p>
                  @else
                    <p>{{ $bankName }} - {{ $accountNumber }}@if($owner) (a.n. {{ $owner }})@endif</p>
                  @endif
                @endforeach
              @else
                <p>Belum ada data rekening.</p>
              @endif
            </div>

            <span class="rc nd rh tm lc fb"></span>

          </div>

          <div class="animate_top w-full nn/5 vo/3 vk sg hh sm yh tq">
            <form id="contact-form" method="POST" action="{{ route('frontend.contact.store') }}">
              @csrf
              <div class="tc sf yo ap zf ep qb">
                <div class="vd to/2">
                  <label class="rc ac" for="fullname">Nama lengkap</label>
                  <input type="text" name="fullname" id="fullname" placeholder="Nama Anda"
                    class="vd ph sg zk xm _g ch pm hm dm dn em pl/50 xi mi" />
                </div>

                <div class="vd to/2">
                  <label class="rc ac" for="email">Alamat email</label>
                  <input type="email" name="email" id="email" placeholder="contoh@domain.com"
                    class="vd ph sg zk xm _g ch pm hm dm dn em pl/50 xi mi" />
                </div>
              </div>

              <div class="tc sf yo ap zf ep qb">
                <div class="vd to/2">
                  <label class="rc ac" for="phone">Nomor telepon</label>
                  <input type="text" name="phone" id="phone" placeholder="+009 3342 3432"
                    class="vd ph sg zk xm _g ch pm hm dm dn em pl/50 xi mi" />
                </div>

                <div class="vd to/2">
                  <label class="rc ac" for="subject">Subjek</label>
                  <input type="text" for="subject" id="subject" placeholder="Ketik subjek Anda"
                    class="vd ph sg zk xm _g ch pm hm dm dn em pl/50 xi mi" />
                </div>
              </div>

              <div class="fb">
                <label class="rc ac" for="message">Pesan</label>
                <textarea placeholder="Pesan Anda" rows="4" name="message" id="message"
                  class="vd ph sg zk xm _g ch pm hm dm dn em pl/50 ci"></textarea>
              </div>

                <div class="tc xf">
                <button id="contact-submit" type="submit" class="vc rg lk gh ml il hi gi _l">Kirim Pesan</button>
              </div>
            </form>
            <style>
              .contact-toast-container{position:fixed;top:20px;right:20px;z-index:99999;display:flex;flex-direction:column;gap:10px}
              .contact-toast{min-width:200px;max-width:360px;padding:10px 14px;border-radius:8px;color:#fff;box-shadow:0 6px 18px rgba(0,0,0,0.12);font-size:14px;opacity:0;transform:translateY(-8px);transition:opacity .18s ease, transform .18s ease}
              .contact-toast.show{opacity:1;transform:translateY(0)}
              .contact-toast.success{background:#16a34a}
              .contact-toast.error{background:#dc2626}
            </style>
            <script>
              (function(){
                console.log('contact form script initialized');
                const form = document.getElementById('contact-form');
                const btn = document.getElementById('contact-submit');
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                if (!form) { console.warn('contact form not found'); return; }

                // Define toast utilities here so they're available when handler runs
                function ensureToastContainer() {
                  let c = document.querySelector('.contact-toast-container');
                  if (!c) {
                    c = document.createElement('div');
                    c.className = 'contact-toast-container';
                    Object.assign(c.style, {
                      position: 'fixed',
                      top: '20px',
                      right: '20px',
                      zIndex: 99999,
                      display: 'flex',
                      flexDirection: 'column',
                      gap: '10px'
                    });
                    document.body.appendChild(c);
                  }
                  return c;
                }

                function showToast(type, message, timeout = 5000) {
                  const c = ensureToastContainer();
                  const t = document.createElement('div');
                  t.className = 'contact-toast ' + type;
                  Object.assign(t.style, {
                    minWidth: '200px',
                    maxWidth: '360px',
                    padding: '10px 14px',
                    borderRadius: '8px',
                    color: '#fff',
                    boxShadow: '0 6px 18px rgba(0,0,0,0.12)',
                    fontSize: '14px'
                  });
                  if (type === 'success') t.style.background = '#16a34a';
                  else if (type === 'error') t.style.background = '#dc2626';
                  else t.style.background = '#333';
                  t.textContent = message;
                  c.appendChild(t);
                  // animate in
                  requestAnimationFrame(() => t.classList.add('show'));
                  setTimeout(() => t.remove(), timeout);
                }

                form.addEventListener('submit', async function(e){
                  e.preventDefault();
                  btn.disabled = true;
                  const payload = {
                    name: document.getElementById('fullname').value.trim(),
                    email: document.getElementById('email').value.trim(),
                    phone: document.getElementById('phone').value.trim(),
                    subject: document.getElementById('subject').value.trim(),
                    message: document.getElementById('message').value.trim(),
                  };

                  // Basic client-side validation
                  if (!payload.name || !payload.message) {
                    console.log('validation failed: name/message');
                    showToast('error', 'Nama dan pesan wajib diisi.');
                    btn.disabled = false;
                    return;
                  }

                  try {
                    const res = await fetch(form.action, {
                      method: 'POST',
                      headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest'
                      },
                      credentials: 'same-origin',
                      body: JSON.stringify(payload)
                    });
                    const json = await res.json();
                    if (json.success) {
                      console.log('server: success', json);
                      showToast('success', json.message || 'Pesan terkirim. Terima kasih.');
                      form.reset();
                    } else if (res.status === 429) {
                      const retry = res.headers.get('Retry-After');
                      console.log('server: rate limited', json, 'retry', retry);
                      showToast('error', json.message || `Terlalu banyak percobaan. Coba lagi dalam ${retry || 'beberapa'} detik.`);
                    } else {
                      console.log('server: error', res.status, json);
                      showToast('error', json.message || 'Terjadi kesalahan.');
                    }
                  } catch (err) {
                    console.error(err);
                    showToast('error', 'Gagal mengirim pesan. Coba lagi nanti.');
                  } finally {
                    btn.disabled = false;
                  }
                });
                console.log('contact form submit handler attached');

              })();
            </script>
          </div>
        </div>
      </div>
    </section>
    <!-- ===== Contact End ===== -->

    <!-- ===== CTA Start ===== -->
    <section class="i pg gh ji">
      <!-- Bg Shape -->
      <img class="h p q" src="{{ asset("frontend/images/shape-16.svg") }}" alt="Bg Shape" />

      <div class="bb ye i z-10 ki xn dr">
        <div class="tc uf sn tn un gg">
          @php
            $lp = $landingProfile ?? null;
            $ctaTitle = $lp && !empty($lp->cta_title) ? $lp->cta_title : 'Sudah 353+ donatur mari mulai donasi untuk Membantu Sesama';
            $ctaDesc = $lp && !empty($lp->cta_description) ? $lp->cta_description : 'Bergabunglah untuk mendukung program-program kami — donasi Anda membantu pendidikan, kesehatan, dan respon darurat bagi komunitas yang membutuhkan.';
            $ctaActive = $lp && !empty($lp->cta_button_active);
            $ctaLink = $lp && !empty($lp->cta_button_link) ? $lp->cta_button_link : '#program';
          @endphp

          <div class="animate_left to/2">
            <h2 class="fk vj zp pr lk ac">{{ $ctaTitle }}</h2>
            <p class="lk">{!! nl2br(e($ctaDesc)) !!}</p>
          </div>
          <div class="animate_right bf">
            @if($ctaActive)
              <a href="{{ $ctaLink }}" class="vc ek kk hh rg ol il cm gi hi">Donasi Sekarang</a>
            @else
              <!-- CTA button disabled because not active -->
            @endif
          </div>
        </div>
      </div>
    </section>

    <!-- ===== CTA End ===== -->

    <!-- Dummy element for isotope (prevent console error) -->
    <div class="projects-wrapper" style="display: none;">
      <div class="project-sizer"></div>
    </div>
  