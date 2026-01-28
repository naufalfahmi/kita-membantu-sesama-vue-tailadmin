<!-- ===== Galeri Kegiatan Start ===== -->
<section id="galeri" class="ji gp uq">
  <!-- Section Title Start -->
  <div class="animate_top bb ze rj ki xn vq">
    <h2 class="fk vj pr kk wm on/5 gq/2 bb _b">Aksi Bantu Sesama</h2>
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
          $excerpt = html_entity_decode($excerpt, ENT_QUOTES | ENT_HTML5, 'UTF-8');
          $excerpt = preg_replace('/\s+/', ' ', trim($excerpt));
          $excerpt = \Illuminate\Support\Str::limit($excerpt, 120);
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

    @if(!empty($landingKegiatanTotal) && $landingKegiatanTotal > 12)
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
    const perPage = 12;
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
              const slugify = function(str){ return String(str).toLowerCase().trim().replace(/[^a-z0-9\s-_]/g,'').replace(/[\s_]+/g,'-').replace(/^-+|-+$/g,''); };
              const slug = slugify(k.title || k.id || 'kegiatan');
              div.innerHTML = `<div class="c rc i z-1 pg"><a href="/kegiatan/${slug}" class="rc"><img class="w-full program-image" src="${img}" alt="${k.title}" /></a><div class="im h r s df vd yc wg tc wf xf al hh/20 nl il z-10"><a href="/kegiatan/${slug}" class="vc ek rg lk gh sl ml il gi hi">View</a></div></div><div class="yh"><h4 class="ek tj ml il kk wm xl eq lb"><a href="/kegiatan/${slug}">${k.title}</a></h4><p>${excerpt}</p></div>`;
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
