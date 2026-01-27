
    <!-- ===== Hero Start ===== -->
    <section id="home" class="gj do ir hj sp jr i pg">
      <!-- Hero Images -->
      <div class="xc fn zd/2 2xl:ud-w-187.5 bd 2xl:ud-h-171.5 h q r">
        <img src="{{ asset("frontend/images/shape-01.svg") }}" alt="shape" class="xc 2xl:ud-block h t -ud-left-[10%] ua" />
        <img src="{{ asset("frontend/images/shape-02.svg") }}" alt="shape" class="xc 2xl:ud-block h u p va" />
        <img src="{{ asset("frontend/images/shape-03.svg") }}" alt="shape" class="xc 2xl:ud-block h v w va" />
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
            <p class="fq">{{ $lp && !empty($lp->hero_description) ? $lp->hero_description : 'menjadi organisasi sosial kemanusiaan internasional yang unggul dan profesional untuk kehidupan kemanusiaan yang lebih baik' }}</p>

            <div class="tc tf yo zf mb">
              @php
                $heroButtonActive = $lp && !empty($lp->hero_button_active);
                $heroButtonLink = $lp && !empty($lp->hero_button_link) ? $lp->hero_button_link : route('frontend.program');

                $heroPhone = $lp && !empty($lp->hero_whatsapp_active) && !empty($lp->hero_whatsapp_number) ? $lp->hero_whatsapp_number : ($lp && !empty($lp->phone_number) ? $lp->phone_number : '+62 895-6210-93500');
                $heroPhoneStr = is_array($heroPhone) ? implode('', array_filter($heroPhone)) : $heroPhone;
                $heroPhoneDigits = preg_replace('/\D+/', '', $heroPhoneStr);
                $heroWa = $heroPhoneDigits ? 'https://wa.me/'.$heroPhoneDigits : 'https://wa.me/62895621093500';
              @endphp

              @if($heroButtonActive)
                <a href="{{ $heroButtonLink }}" class="ek jk lk gh gi hi rg ml il vc _d _l vc items-center inline-flex">Lihat Program Kami</a>
              @else
                <a href="{{ route('frontend.program') }}" class="ek jk lk gh gi hi rg ml il vc _d _l vc items-center inline-flex">Lihat Program Kami</a>
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
                <!-- Generic feature icon -->
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

@if(($landingBulletinsTotal ?? 0) > 0)
    <!-- ===== Bulletins Start ===== -->
    <section class="lj tp kr">
      <!-- Section Title Start -->
      <div class="animate_top bb ze rj ki xn vq">
        <h2 class="fk vj pr kk wm on/5 gq/2 bb _b">Bulletin</h2>
        <p class="bb on/5 wo/5 hq">Bulletin berisi pengumuman, artikel, dan informasi terbaru mengenai kegiatan serta program kami untuk masyarakat.</p>
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
          const slugify = function(str){
            return String(str).toLowerCase().trim()
              .replace(/[^a-z0-9\s-_]/g, '')
              .replace(/[\s_]+/g, '-')
              .replace(/^-+|-+$/g, '');
          };
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
    <!-- ===== Bulletins End ===== -->
@endif

<!-- ===== CTA Start ===== -->
    <section class="i pg gh ji">
      <!-- Bg Shape -->
      <img class="h p q" src="{{ asset("frontend/images/shape-16.svg") }}" alt="Bg Shape" />

      <div class="bb ye i z-10 ki xn dr">
        <div class="tc uf sn tn un gg">
          <div class="animate_left to/2">
            <h2 class="fk vj zp pr lk ac">
              Bergabunglah Dengan Ribuan Donatur
            </h2>
            <p class="lk">
              Mari bersama-sama membantu sesama dan menciptakan perubahan positif untuk masyarakat yang membutuhkan.
            </p>
          </div>
          <!-- <div class="animate_right bf">
            <a href="/#cara-donasi" class="vc ek kk hh rg ol il cm gi hi">
              Donasi Sekarang
            </a>
          </div> -->
        </div>
      </div>
    </section>

    <!-- ===== CTA End ===== -->
