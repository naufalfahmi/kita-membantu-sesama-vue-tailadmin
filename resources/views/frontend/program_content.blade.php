<!-- ===== Program Section Start ===== -->
<section id="program" class="ji gp uq">
  <div class="bb ze ki xn 2xl:ud-px-0 jb">
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
            $excerpt = html_entity_decode($excerpt, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $excerpt = preg_replace('/\s+/', ' ', trim($excerpt));
            $excerpt = \Illuminate\Support\Str::limit($excerpt, 120);
          @endphp

          <div class="animate_top sg vk rm xm">
            <div class="c rc i z-1 pg">
              <a href="{{ $image }}" class="rc" data-caption="{{ $p->name }}">
                @php $pSlug = $p->slug ?? \Illuminate\Support\Str::slug($p->name); @endphp
                <a href="/program/{{ $pSlug }}">
                  <img class="w-full program-image" src="{{ $image }}" alt="{{ $p->name }}" />
                </a>
              </a>
            </div>

            <div class="yh">
              <h4 class="ek tj ml il kk wm xl eq lb">
                @php $pSlug = $p->slug ?? \Illuminate\Support\Str::slug($p->name); @endphp
                <a href="/program/{{ $pSlug }}">{{ $p->name }}</a>
              </h4>
              <p>{{ $excerpt }}</p>
            </div>
          </div>
        @endforeach
      </div>

      @if(!empty($landingProgramsTotal) && $landingProgramsTotal > 12)
        <div class="tc mt-6">
          <button id="load-more-programs" class="ek rg ml il vi mi">Load more</button>
        </div>
      @endif
    </div>
  </div>
</section>
<!-- ===== Program Section End ===== -->

@push('scripts')
<script>
  (function(){
    // Initialize baguetteBox for program gallery
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

    if (!initProgramGallery()) {
      const ib = setInterval(() => { if (initProgramGallery()) clearInterval(ib) }, 200)
    }

    let currentPage = 1;
    const perPage = 12;
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
              const slugify = function(str){
                return String(str).toLowerCase().trim().replace(/[^a-z0-9\s-_]/g,'').replace(/[\s_]+/g,'-').replace(/^-+|-+$/g,'');
              };
              const slug = p.slug || slugify(p.name || p.id || 'program');
              div.innerHTML = `<div class="c rc i z-1 pg"><a href="${img}" class="rc" data-caption="${p.name}"><img class="w-full program-image" src="${img}" alt="${p.name}" /></a></div><div class="yh"><h4 class="ek tj ml il kk wm xl eq lb"><a href="/program/${slug}">${p.name}</a></h4><p>${excerpt}</p></div>`;
              container.appendChild(div);
            });
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
