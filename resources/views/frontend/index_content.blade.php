
<!-- ===== Program List Start ===== -->
<section id="program-list" class="ji gp uq">
  <div class="bb ze ki xn 2xl:ud-px-0 jb">
    <div class="wc qf pn xo zf iq">
      @if($highlightProgram)
        @php
          $p = $highlightProgram;
          $image = asset('frontend/images/project-01.png');
          if (!empty($p->image_url)) {
              $image = preg_match('/^data:|^https?:\/\//', $p->image_url) ? $p->image_url : asset('storage/' . $p->image_url);
          }
          $excerpt = strip_tags($p->description ?? '');
          $excerpt = html_entity_decode($excerpt, ENT_QUOTES | ENT_HTML5, 'UTF-8');
          $excerpt = preg_replace('/\s+/', ' ', trim($excerpt));
          $excerpt = \Illuminate\Support\Str::limit($excerpt, 120);
          $pSlug = $p->slug ?? \Illuminate\Support\Str::slug($p->name);
        @endphp

        <div class="animate_top sg vk rm xm relative">
         
          
          <div class="c rc i z-1 pg">
            <a href="{{ route('frontend.program-single', $pSlug) }}">
              <img class="w-full program-image aspect-video object-cover" src="{{ $image }}" alt="{{ $p->name }}" />
            </a>
          </div>

          <div class="yh">
            <h4 class="ek tj ml il kk wm xl eq lb">
              <a href="{{ route('frontend.program-single', $pSlug) }}">{{ $p->name }}</a>
            </h4>
            <p>{{ $excerpt }}</p>
          </div>
        </div>
      @endif

      @forelse($landingPrograms ?? [] as $p)
        @php
          $image = asset('frontend/images/project-01.png');
          if (!empty($p->image_url)) {
              $image = preg_match('/^data:|^https?:\/\//', $p->image_url) ? $p->image_url : asset('storage/' . $p->image_url);
          }
          $excerpt = strip_tags($p->description ?? '');
          $excerpt = html_entity_decode($excerpt, ENT_QUOTES | ENT_HTML5, 'UTF-8');
          $excerpt = preg_replace('/\s+/', ' ', trim($excerpt));
          $excerpt = \Illuminate\Support\Str::limit($excerpt, 120);
          $pSlug = $p->slug ?? \Illuminate\Support\Str::slug($p->name);
        @endphp

        <div class="animate_top sg vk rm xm">
          <div class="c rc i z-1 pg">
            <a href="{{ route('frontend.program-single', $pSlug) }}">
              <img class="w-full program-image aspect-video object-cover" src="{{ $image }}" alt="{{ $p->name }}" />
            </a>
          </div>

          <div class="yh">
            <h4 class="ek tj ml il kk wm xl eq lb">
              <a href="{{ route('frontend.program-single', $pSlug) }}">{{ $p->name }}</a>
            </h4>
            <p>{{ $excerpt }}</p>
          </div>
        </div>
      @empty
        @if(!$highlightProgram)
          <div class="col-span-full text-center py-20 grayscale opacity-50">
            <p>Belum ada program yang tersedia saat ini.</p>
          </div>
        @endif
      @endforelse
    </div>

    
    <br>
    @if(($totalLandingPrograms ?? 0) > 9)
    <div class="tc mt-12">
      <a href="{{ route('frontend.program') }}" class="vc rg lk gh ml il hi gi _l inline-flex items-center">
        Lihat Semua Program
      </a>
    </div>
    @endif
  </div>
</section>
<!-- ===== Program List End ===== -->

<!-- Hidden elements to prevent Isotope JS errors in bundle.js -->
<div class="projects-wrapper" style="display: none !important;">
  <div class="project-sizer"></div>
</div>
