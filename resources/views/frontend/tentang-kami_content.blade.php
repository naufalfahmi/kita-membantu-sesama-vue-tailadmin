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
            <!-- Default SVG Illustration -->
           <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.dev/svgjs" width="700" height="600" preserveAspectRatio="none" viewBox="0 0 700 600">
    <g mask="url(&quot;#SvgjsMask1143&quot;)" fill="none">
        <g mask="url(&quot;#SvgjsMask1144&quot;)">
            <path d="M319 231L297 253L297 275L297 297L297 319M341 77L319 99L319 121L319 143L319 165L297 187L297 209M297 407L297 429L275 451L275 473M319 297L341 319M319 363L319 385L319 407L341 429L341 451L319 473M341 -11L341 11L341 33L341 55L341 77L341 99L341 121L341 143L341 165L319 187L319 209L319 231L319 253L319 275L319 297L319 319L319 341L319 363L297 385L297 407L319 429" stroke="rgba(55, 159, 208, 1)" stroke-width="3.67"></path>
            <path d="M313.5 429 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM291.5 319 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM291.5 209 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM269.5 473 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM335.5 319 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM313.5 473 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0z" fill="rgba(55, 159, 208, 1)"></path>
            <path d="M363 253L341 275L341 297M341 209L363 231M363 165L363 187L385 209L385 231M363 341L341 363M363 -11L363 11L363 33L363 55L363 77L363 99L363 121L363 143L363 165L341 187L341 209L341 231L363 253L363 275L363 297L363 319L363 341L363 363" stroke="rgba(55, 159, 208, 1)" stroke-width="3.67"></path>
            <path d="M357.5 363 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM335.5 297 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM357.5 231 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM379.5 231 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0zM335.5 363 a5.5 5.5 0 1 0 11 0 a5.5 5.5 0 1 0 -11 0z" fill="rgba(55, 159, 208, 1)"></path>
        </g>
    </g>
    <defs>
        <mask id="SvgjsMask1143">
            <rect width="700" height="600" fill="#ffffff"></rect>
        </mask>
        <mask id="SvgjsMask1144">
            <rect width="700" height="600" fill="white"></rect>
        </mask>
    </defs>
</svg>
          @endif
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
        <p class="uo">{{ $aboutTitle }}</p>
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
