
    <!-- ===== Hero Start ===== -->
    <section id="home" class="gj do ir hj sp jr i pg">
      <!-- Hero Images -->
      <div class="xc fn zd/2 2xl:ud-w-187.5 bd 2xl:ud-h-171.5 h q r">
        <img src="{{ asset("frontend/images/shape-01.svg") }}" alt="shape" class="xc 2xl:ud-block h t -ud-left-[10%] ua" />
        <img src="{{ asset("frontend/images/shape-02.svg") }}" alt="shape" class="xc 2xl:ud-block h u p va" />
        <img src="{{ asset("frontend/images/shape-03.svg") }}" alt="shape" class="xc 2xl:ud-block h v w va" />
        {{-- shape-04.svg removed per request --}}
        <img src="{{ asset("frontend/images/people-who-support-svgrepo-com.svg") }}" alt="People Supporting" class="h q r ua" />
      </div>

      <!-- Hero Content -->
      <div class="bb ze ki xn 2xl:ud-px-0">
        <div class="tc _o">
          <div class="animate_left jn/2">
            <h1 class="fk vj zp or kk wm wb">Kita Membantu Sesama</h1>
            <p class="fq">menjadi organisasi sosial kemanusiaan internasional yang unggul dan profesional untuk kehidupan
              kemanusiaan yang lebih baik</p>

            <div class="tc tf yo zf mb">
              <a href="#program" class="ek jk lk gh gi hi rg ml il vc _d _l vc items-center inline-flex">Lihat Program Kami</a>

              <span class="tc sf">
                @php
                  $lp = $landingProfile ?? null;
                  $heroPhone = $lp && !empty($lp->phone_number) ? $lp->phone_number : '+62 895-6210-93500';
                  $heroPhoneStr = is_array($heroPhone) ? implode('', array_filter($heroPhone)) : $heroPhone;
                  $heroPhoneDigits = preg_replace('/\D+/', '', $heroPhoneStr);
                  $heroWa = $heroPhoneDigits ? 'https://wa.me/'.$heroPhoneDigits : 'https://wa.me/62895621093500';
                @endphp
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
          <!-- Small Features Item -->
          <div class="animate_top kn to/3 tc cg oq">
            <div class="tc wf xf cf ae cd rg mh">
              <svg class="w-12 h-12" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="24" cy="24" r="20" fill="#E8F5E9"/>
                <path d="M24 12C17.4 12 12 17.4 12 24C12 30.6 17.4 36 24 36C30.6 36 36 30.6 36 24C36 17.4 30.6 12 24 12ZM24 33C19 33 15 29 15 24C15 19 19 15 24 15C29 15 33 19 33 24C33 29 29 33 24 33Z" fill="#4CAF50"/>
                <path d="M22 28L18 24L19.4 22.6L22 25.2L28.6 18.6L30 20L22 28Z" fill="#4CAF50"/>
              </svg>
            </div>
            <div>
              <h4 class="ek yj go kk wm xb">Transparansi</h4>
              <p>Keterbukaan penuh dalam pengelolaan dana dan program untuk membangun kepercayaan publik.</p>
            </div>
          </div>

          <!-- Small Features Item -->
          <div class="animate_top kn to/3 tc cg oq">
            <div class="tc wf xf cf ae cd rg nh">
              <svg class="w-12 h-12" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="24" cy="24" r="20" fill="#FFF3E0"/>
                <path d="M24 10L28 18H36L30 24L32 32L24 27L16 32L18 24L12 18H20L24 10Z" fill="#FF9800"/>
              </svg>
            </div>
            <div>
              <h4 class="ek yj go kk wm xb">Amanah</h4>
              <p>Menjalankan tanggung jawab dengan penuh integritas dan akuntabilitas kepada donatur.</p>
            </div>
          </div>

          <!-- Small Features Item -->
          <div class="animate_top kn to/3 tc cg oq">
            <div class="tc wf xf cf ae cd rg oh">
              <svg class="w-12 h-12" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="24" cy="24" r="20" fill="#E3F2FD"/>
                <path d="M20 14H28V18H32V22H28V26H32V30H28V34H20V30H16V26H20V22H16V18H20V14Z" fill="#2196F3"/>
                <circle cx="24" cy="20" r="3" fill="#2196F3"/>
              </svg>
            </div>
            <div>
              <h4 class="ek yj go kk wm xb">Profesional</h4>
              <p>Dikelola oleh tim berpengalaman dengan standar tata kelola organisasi yang baik.</p>
            </div>
          </div>
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
              <!-- SVG Illustration -->
              <svg class="w-full h-auto max-w-lg" viewBox="0 0 500 400" xmlns="http://www.w3.org/2000/svg">
                <!-- Background -->
                <rect x="50" y="50" width="400" height="300" rx="20" fill="#E8F5E9"/>
                
                <!-- People helping illustration -->
                <g>
                  <!-- Person 1 -->
                  <circle cx="150" cy="150" r="35" fill="#4CAF50"/>
                  <rect x="130" y="185" width="40" height="80" rx="8" fill="#66BB6A"/>
                  
                  <!-- Person 2 -->
                  <circle cx="250" cy="140" r="35" fill="#2E7D32"/>
                  <rect x="230" y="175" width="40" height="90" rx="8" fill="#4CAF50"/>
                  
                  <!-- Person 3 -->
                  <circle cx="350" cy="150" r="35" fill="#81C784"/>
                  <rect x="330" y="185" width="40" height="80" rx="8" fill="#A5D6A7"/>
                  
                  <!-- Heart symbol in center -->
                  <path d="M250 200 L260 190 Q270 180 270 170 Q270 155 260 150 Q250 145 250 150 Q250 145 240 150 Q230 155 230 170 Q230 180 240 190 Z" fill="#FF5252"/>
                  
                  <!-- Connecting hands -->
                  <line x1="170" y1="220" x2="230" y2="220" stroke="#4CAF50" stroke-width="4" stroke-linecap="round"/>
                  <line x1="270" y1="220" x2="330" y2="220" stroke="#4CAF50" stroke-width="4" stroke-linecap="round"/>
                </g>
                
                <!-- Text -->
                <text x="250" y="330" text-anchor="middle" fill="#2E7D32" font-size="22" font-weight="bold" font-family="Arial, sans-serif">Kita Membantu Sesama</text>
              </svg>
            </div>
          </div>

          <!-- About Content -->
          <div class="animate_right jn/2">
            <h4 class="ek yj mk gb">Tentang Kita Membantu Sesama</h4>
            <h2 class="fk vj zp pr kk wm qb">Menjadi Organisasi Sosial Kemanusiaan Internasional yang Unggul dan Profesional</h2>
            <p class="uo mb-4">Kita Membantu Sesama (KMS) adalah organisasi sosial kemanusiaan yang berdedikasi untuk membantu masyarakat yang membutuhkan. Kami berkomitmen untuk memberikan bantuan kemanusiaan yang tepat sasaran dan berkelanjutan.</p>
            
            <div class="mb-6">
              <h5 class="ek zj kk wm mb-3">Visi Kami</h5>
              <p class="uo">Menjadi organisasi sosial kemanusiaan internasional yang unggul dan profesional untuk kehidupan kemanusiaan yang lebih baik.</p>
            </div>
            
            <div class="mb-6">
              <h5 class="ek zj kk wm mb-3">Misi Kami</h5>
              <ul class="list-disc list-inside space-y-2">
                <li>Memberikan bantuan kemanusiaan yang tepat sasaran</li>
                <li>Memberdayakan masyarakat untuk hidup mandiri</li>
                <li>Membangun kemitraan strategis dengan berbagai pihak</li>
                <li>Mengelola organisasi secara profesional dan transparan</li>
              </ul>
            </div>

            <a href="#kontak" class="vc ek rg lk gh sl ml il gi hi">Hubungi Kami</a>
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
          <div id="programs" class="wc qf pn xo zf iq">
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
                  <a href="#" class="rc">
                    <img class="w-full program-image" src="{{ $image }}" alt="{{ $p->name }}" />
                  </a>
                  <div class="im h r s df vd yc wg tc wf xf al hh/20 nl il z-10">
                    <a href="#" class="vc ek rg lk gh sl ml il gi hi">Read More</a>
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
                    div.innerHTML = `<div class="c rc i z-1 pg"><a href="#" class="rc"><img class="w-full program-image" src="${img}" alt="${p.name}" /></a><div class="im h r s df vd yc wg tc wf xf al hh/20 nl il z-10"><a href="#" class="vc ek rg lk gh sl ml il gi hi">Read More</a></div></div><div class="yh"><h4 class="ek tj ml il kk wm xl eq lb"><a href="#">${p.name}</a></h4><p>${excerpt}</p></div>`;
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
                <div class="flex items-center gap-2">
                  <p class="font-semibold flex-shrink-0">{{ $account['value'] ?? '' }}</p>
                  <button 
                    onclick="copyToClipboard('{{ $account['value'] ?? '' }}', this)"
                    class="inline-flex items-center justify-center w-6 h-6 rounded hover:bg-gray-100 transition-colors flex-shrink-0"
                    title="Copy nomor rekening">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M16 1H4C2.9 1 2 1.9 2 3V17H4V3H16V1ZM19 5H8C6.9 5 6 5.9 6 7V21C6 22.1 6.9 23 8 23H19C20.1 23 21 22.1 21 21V7C21 5.9 20.1 5 19 5ZM19 21H8V7H19V21Z" fill="currentColor"/>
                    </svg>
                  </button>
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
          <div class="animate_left to/2">
            <h2 class="fk vj zp pr lk ac">
              Sudah 353+ donatur mari mulai donasi untuk Membantu Sesama
            </h2>
            <p class="lk">
              Bergabunglah untuk mendukung program-program kami — donasi Anda membantu pendidikan, kesehatan, dan respon
              darurat bagi komunitas yang membutuhkan.
            </p>
          </div>
          <div class="animate_right bf">
            <a href="#!" class="vc ek kk hh rg ol il cm gi hi">
              Donasi Sekarang
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== CTA End ===== -->

    <!-- Dummy element for isotope (prevent console error) -->
    <div class="projects-wrapper" style="display: none;">
      <div class="project-sizer"></div>
    </div>
  