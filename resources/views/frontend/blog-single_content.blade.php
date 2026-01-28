
    <!-- ===== Blog Single Start ===== -->
    <section class="gj  hj rp hr">
      <div class="bb ze ki xn 2xl:ud-px-0">
        <div class="tc sf yo zf kq">
          <div class="ro">
            <div
              class="animate_top rounded-md shadow-solid-13 bg-white dark:bg-blacksection border border-stroke dark:border-strokedark p-7.5 md:p-10">
              @if(!empty($kegiatan))
                @php
                  $imgs = [];
                  if (!empty($kegiatan->images)) {
                    $imgs = json_decode($kegiatan->images, true) ?: [];
                    if (!is_array($imgs)) $imgs = [];
                  }
                  $mainImg = asset('frontend/images/blog-big.png');
                  if (!empty($imgs) && !empty($imgs[0])) {
                    $mainImg = preg_match('/^https?:\/\//', $imgs[0]) ? $imgs[0] : asset('storage/' . $imgs[0]);
                  }
                  // Per-page SEO meta
                  $metaTitle = ($kegiatan->title ? $kegiatan->title . ' - ' : '') . config('app.name', 'KMS');
                  $metaDescription = strip_tags($kegiatan->description ?? '');
                  $metaDescription = strlen($metaDescription) > 160 ? substr($metaDescription, 0, 157) . '...' : $metaDescription;
                  $metaImage = $mainImg ?? asset('frontend/images/og-default.png');
                  $canonical = url('/kegiatan/' . ($kegiatan->slug ?? \Illuminate\Support\Str::slug($kegiatan->title)));
                  $date = $kegiatan->activity_date ? \Carbon\Carbon::parse($kegiatan->activity_date)->format('d M, Y') : '';
                  $location = collect([
                      $kegiatan->village,
                      $kegiatan->district,
                      $kegiatan->city,
                      $kegiatan->province
                  ])->filter()->join(', ');
                @endphp

                <img src="{{ $mainImg }}" alt="{{ $kegiatan->title }}" class="w-full h-auto rounded-lg mb-6" />

                <h1 class="ek vj 2xl:ud-text-title-lg kk wm nb gb">{{ $kegiatan->title }}</h1>

                <div class="rounded-md shadow-solid-12 bg-white dark:bg-blacksection border border-stroke dark:border-strokedark p-5 mb-6">
                  <ul class="tc uf flex-wrap cg 2xl:ud-gap-15 fb gap-4 md:gap-8 md:flex-nowrap">
                    <li class="flex items-center gap-2 md:flex-1">
                    <span class="rc kk wm">Tanggal: {{ $date }}</span> 
                  </li>
                  @if(!empty($location))
                    <li class="flex items-center gap-2 md:flex-1">
                    <span class="rc kk wm">Lokasi: {{ $location }}</span>
                  </li>
                  @endif
                  @if($kegiatan->number_of_recipients > 0)
                    <li class="flex items-center gap-2 md:flex-1">
                    <span class="rc kk wm">Penerima Manfaat: {{ number_format($kegiatan->number_of_recipients) }} orang</span>
                  </li>
                  @endif
                  </ul>
                </div>

                @php
                  $rawDesc = $kegiatan->description ?? '';
                  $cleanDesc = html_entity_decode($rawDesc, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                  $cleanDesc = preg_replace('/\x{00A0}/u', ' ', $cleanDesc);
                  $cleanDesc = preg_replace('/\s+/u', ' ', trim($cleanDesc));
                @endphp
                <div class="prose max-w-none mb-4">{!! $cleanDesc !!}</div>

                @if(count($imgs) > 1)
                  <h3 class="text-xl font-semibold mb-4">Aksi Bantu Sesama</h3>
                  <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6 kegiatan-gallery">
                    @foreach(array_slice($imgs, 1) as $si)
                      @php $simg = preg_match('/^https?:\/\//', $si) ? $si : asset('storage/' . $si); @endphp
                      <a href="{{ $simg }}" class="block rounded-lg overflow-hidden" data-caption="{{ $kegiatan->title }}">
                        <img src="{{ $simg }}" alt="{{ $kegiatan->title }}" class="w-full h-48 object-cover rounded-lg" />
                      </a>
                    @endforeach
                  </div>

                  @push('scripts')
                  <script>
                    (function(){
                      // Initialize baguetteBox for the kegiatan gallery; safe to call multiple times
                      try {
                        if (typeof baguetteBox !== 'undefined') {
                          baguetteBox.run('.kegiatan-gallery', { animation: 'slideIn', captions: true });
                        } else {
                          const iv = setInterval(() => { if (typeof baguetteBox !== 'undefined') { clearInterval(iv); baguetteBox.run('.kegiatan-gallery', { animation: 'slideIn', captions: true }); } }, 200);
                        }
                      } catch (e) {
                        console.error('Failed to init kegiatan gallery lightbox', e)
                      }
                    })();
                  </script>
                  @endpush
                @endif

              @else
                <img src="{{ asset("frontend/images/blog-big.png") }}" alt="Blog" />

                <h2 class="ek vj 2xl:ud-text-title-lg kk wm nb gb">Kobe Steel plant that supplied</h2>

                <ul class="tc uf cg 2xl:ud-gap-15 fb">
                  <li><span class="rc kk wm">Author: </span> Devid Cleriya</li>
                  <li><span class="rc kk wm">Published On: </span> April 16, 2025</li>
                  <li><span class="rc kk wm">Category: </span> Events</li>
                </ul>

                <p>
                  Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc quis nibh lorem. Duis sed odio lorem. In a
                  efficitur leo. Ut venenatis rhoncus quam sed condimentum. Curabitur vel turpis in dolor volutpat
                  imperdiet in ut mi. Integer non volutpat nulla. Nunc elementum elit viverra, tempus quam non, interdum
                  ipsum.
                </p>

                <p class="ob">
                  Aenean augue ex, condimentum vel metus vitae, aliquam porta elit. Quisque non metus ac orci mollis
                  posuere. Mauris vel ipsum a diam interdum ultricies sed vitae neque. Nulla
                  porttitor quam vitae pulvinar placerat. Nulla fringilla elit sit amet justo feugiat sodales. Morbi
                  eleifend, enim non eleifend laoreet, odio libero lobortis lectus, non porttitor sem
                  urna sit amet metus. In sollicitudin quam est, pellentesque consectetur felis fermentum vitae.
                </p>

                <div class="wc qf pn dg cb">
                  <img src="{{ asset("frontend/images/blog-04.png") }}" alt="Blog" />
                  <img src="{{ asset("frontend/images/blog-05.png") }}" alt="Blog" />
                </div>

                <h2 class="ek vj 2xl:ud-text-title-lg kk wm nb qb">The powerful force of humanity</h2>
                <p>
                  Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc quis nibh lorem. Duis sed odio lorem. In a
                  efficitur leo. Ut venenatis rhoncus quam sed condimentum. Curabitur vel
                  turpis in dolor volutpat imperdiet in ut mi. Integer non volutpat nulla. Nunc elementum elit viverra,
                  tempus quam non, interdum ipsum.
                </p>
              @endif

              <br>
                <div class="mt-10 mb-6">
                  <a id="donate-now" href="https://wa.me/62895621093500?text=Saya+ingin+berdonasi+untuk+kegiatan%3A+{{ urlencode($kegiatan->title ?? 'Sedekah Buka Puasa') }}" target="_blank" rel="noopener" role="button" class="vc rg lk gh ml il hi gi _l inline-flex items-center">
                    Donasi Sekarang
                  </a>
                </div>

              <ul class="tc wf bg sb mt-8">
                <li>
                  <p class="sj kk wm tb">Bagikan:</p>
                </li>
                <li>
                  <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" class="tc wf xf yd ad rg ml il ih wk">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <g clip-path="url(#clip0_47_28)">
                        <path
                          d="M11.6663 11.25H13.7497L14.583 7.91663H11.6663V6.24996C11.6663 5.39163 11.6663 4.58329 13.333 4.58329H14.583V1.78329C14.3113 1.74746 13.2855 1.66663 12.2022 1.66663C9.93967 1.66663 8.33301 3.04746 8.33301 5.58329V7.91663H5.83301V11.25H8.33301V18.3333H11.6663V11.25Z"
                          fill="white" />
                      </g>
                      <defs>
                        <clipPath id="clip0_47_28">
                          <rect width="20" height="20" fill="white" />
                        </clipPath>
                      </defs>
                    </svg>
                  </a>
                </li>
                <li>
                  <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($kegiatan->title ?? '') }}" target="_blank" class="tc wf xf yd ad rg ml il jh wk">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <g clip-path="url(#clip0_47_47)">
                        <path
                          d="M18.4683 4.71327C17.8321 4.99468 17.1574 5.1795 16.4666 5.26161C17.1947 4.82613 17.7397 4.14078 17.9999 3.33327C17.3166 3.73994 16.5674 4.02494 15.7866 4.17911C15.2621 3.61792 14.5669 3.24574 13.809 3.12043C13.0512 2.99511 12.2732 3.12368 11.596 3.48615C10.9187 3.84862 10.3802 4.42468 10.0642 5.12477C9.74812 5.82486 9.67221 6.60976 9.84825 7.35744C8.46251 7.28798 7.10686 6.92788 5.86933 6.30049C4.63179 5.67311 3.54003 4.79248 2.66492 3.71577C2.35516 4.24781 2.19238 4.85263 2.19326 5.46827C2.19326 6.67661 2.80826 7.74411 3.74326 8.36911C3.18993 8.35169 2.64878 8.20226 2.16492 7.93327V7.97661C2.16509 8.78136 2.44356 9.56129 2.95313 10.1842C3.46269 10.807 4.17199 11.2345 4.96075 11.3941C4.4471 11.5333 3.90851 11.5538 3.38576 11.4541C3.60814 12.1468 4.04159 12.7526 4.62541 13.1867C5.20924 13.6208 5.9142 13.8614 6.64159 13.8749C5.91866 14.4427 5.0909 14.8624 4.20566 15.1101C3.32041 15.3577 2.39503 15.4285 1.48242 15.3183C3.0755 16.3428 4.93 16.8867 6.82409 16.8849C13.2349 16.8849 16.7408 11.5741 16.7408 6.96827C16.7408 6.81827 16.7366 6.66661 16.7299 6.51827C17.4123 6.02508 18.0013 5.41412 18.4691 4.71411L18.4683 4.71327Z"
                          fill="white" />
                      </g>
                      <defs>
                        <clipPath id="clip0_47_47">
                          <rect width="20" height="20" fill="white" />
                        </clipPath>
                      </defs>
                    </svg>
                  </a>
                </li>
                <li>
                  <a href="https://wa.me/?text={{ urlencode(($kegiatan->title ?? '') . ' - ' . request()->url()) }}" target="_blank" class="tc wf xf yd ad rg ml il lh wk">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M17.472 14.382C17.227 14.259 16.015 13.662 15.791 13.577C15.567 13.493 15.402 13.451 15.237 13.696C15.072 13.942 14.591 14.498 14.446 14.663C14.301 14.828 14.156 14.849 13.911 14.726C13.666 14.603 12.86 14.34 11.909 13.49C11.169 12.83 10.677 12.014 10.532 11.769C10.387 11.524 10.517 11.389 10.64 11.266C10.752 11.154 10.885 10.979 11.008 10.834C11.131 10.689 11.173 10.586 11.257 10.421C11.341 10.256 11.299 10.111 11.236 9.988C11.173 9.865 10.682 8.652 10.478 8.162C10.279 7.687 10.077 7.753 9.925 7.746C9.78 7.739 9.615 7.738 9.45 7.738C9.285 7.738 9.019 7.8 8.795 8.045C8.571 8.29 7.933 8.887 7.933 10.1C7.933 11.313 8.816 12.484 8.939 12.649C9.062 12.814 10.677 15.294 13.158 16.366C13.718 16.614 14.155 16.764 14.496 16.876C15.057 17.049 15.566 17.024 15.97 16.96C16.418 16.889 17.406 16.387 17.61 15.833C17.814 15.279 17.814 14.808 17.751 14.705C17.688 14.602 17.717 14.505 17.472 14.382ZM12.012 21.785H12.009C10.265 21.785 8.556 21.298 7.073 20.382L6.718 20.172L2.905 21.145L3.893 17.42L3.663 17.053C2.655 15.52 2.121 13.733 2.122 11.897C2.124 6.444 6.558 2.01 12.015 2.01C14.666 2.011 17.158 3.046 19.034 4.924C20.91 6.802 21.943 9.295 21.942 11.945C21.94 17.398 17.506 21.785 12.012 21.785ZM20.455 3.468C18.258 1.268 15.289 0.048 12.013 0.047C5.447 0.047 0.11 5.383 0.107 11.948C0.106 14.046 0.666 16.095 1.726 17.895L0 24L6.235 22.308C8.001 23.269 10.001 23.777 12.008 23.778H12.013C18.579 23.778 23.916 18.442 23.919 11.877C23.92 8.601 22.653 5.668 20.455 3.468Z" fill="white"/>
                    </svg>
                  </a>
                </li>
                <li>
                  <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->url()) }}&title={{ urlencode($kegiatan->title ?? '') }}" target="_blank" class="tc wf xf yd ad rg ml il kh wk">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <g clip-path="url(#clip0_47_53)">
                        <path
                          d="M5.78353 4.16665C5.78331 4.60867 5.6075 5.03251 5.29478 5.34491C4.98207 5.65732 4.55806 5.8327 4.11603 5.83248C3.674 5.83226 3.25017 5.65645 2.93776 5.34373C2.62536 5.03102 2.44997 4.60701 2.4502 4.16498C2.45042 3.72295 2.62622 3.29912 2.93894 2.98671C3.25166 2.67431 3.67567 2.49892 4.1177 2.49915C4.55972 2.49937 4.98356 2.67517 5.29596 2.98789C5.60837 3.30061 5.78375 3.72462 5.78353 4.16665ZM5.83353 7.06665H2.5002V17.5H5.83353V7.06665ZM11.1002 7.06665H7.78353V17.5H11.0669V12.025C11.0669 8.97498 15.0419 8.69165 15.0419 12.025V17.5H18.3335V10.8916C18.3335 5.74998 12.4502 5.94165 11.0669 8.46665L11.1002 7.06665Z"
                          fill="white" />
                      </g>
                      <defs>
                        <clipPath id="clip0_47_53">
                          <rect width="20" height="20" fill="white" />
                        </clipPath>
                      </defs>
                    </svg>
                  </a>
                </li>
              </ul>

            </div>
          </div>

          <div class="jn/2 so">
            <div class="animate_top">
              <h4 class="tj kk wm qb">Kegiatan Lainnya</h4>

              <div>
                @forelse($relatedKegiatans ?? [] as $related)
                  @php
                    $relatedSlug = \Illuminate\Support\Str::slug($related->title);
                    $relatedExcerpt = strip_tags($related->description ?? '');
                    $relatedExcerpt = html_entity_decode($relatedExcerpt, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $relatedExcerpt = preg_replace('/\x{00A0}/u', ' ', $relatedExcerpt);
                    $relatedExcerpt = preg_replace('/\s+/u', ' ', trim($relatedExcerpt));
                    $relatedExcerpt = \Illuminate\Support\Str::limit($relatedExcerpt, 110);
                  @endphp
                  <div class="mb-5 pb-5 border-b border-stroke dark:border-strokedark last:border-0 last:pb-0 last:mb-0">
                    <p class="text-xs uppercase tracking-wide text-brand-500 mb-1">{{ $related->activity_date ? \Carbon\Carbon::parse($related->activity_date)->format('d M, Y') : 'Kegiatan' }}</p>
                    <h5 class="wj kk wm xl bn ml il mb-2">
                      <a href="{{ route('frontend.blog-single', ['slug' => $relatedSlug]) }}" class="hover:text-brand-500">
                        {{ $related->title }}
                      </a>
                    </h5>
                    @if(!empty($relatedExcerpt))
                      <p class="text-sm text-gray-500 dark:text-gray-400">{{ $relatedExcerpt }}</p>
                    @endif
                  </div>
                @empty
                  <p class="text-sm text-gray-500">Tidak ada kegiatan lainnya.</p>
                @endforelse
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- ===== Blog Single End ===== -->

   

    <!-- ===== CTA End ===== -->
  