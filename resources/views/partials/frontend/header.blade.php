<!-- ===== Header Start ===== -->
<header class="g s r vd ya cj" :class="{ 'hh sm _k dj bl ll' : stickyMenu }"
  @scroll.window="stickyMenu = (window.pageYOffset > 20) ? true : false">
  <div class="bb ze ki xn 2xl:ud-px-0 oo wf yf i">
    <div class="vd to/4 tc wf yf">
      <a href="{{ route('frontend.index') }}" class="site-brand" aria-label="Kita Membantu Sesama">
        <!-- Inlined logo SVG for styling via classes -->
        <svg class="logo" width="45" height="45" viewBox="0 0 2500 1800" role="img" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
          <g id="Group 2" class="logo">
            <path id="Object 3" class="logo-primary" d="m1542.9 1648.8c62-31 82.1-69 91.8-96.6 29.1-83.3 3.9-215.7-70-375.7 43-84.5 91-166.4 142.9-245.7 110.1 195.9 171.4 373.2 177.9 515.6 3.7 179.8-260.6 245-342.6 202.4z"/>
            <path id="Object 4" class="logo-primary" d="m595.7 1421.9c6.6-142.4 63.9-310.7 174-506.6 51.9 79.3 99.9 161.2 142.9 245.6-73.9 160.1-95.2 283.5-66 366.7 9.6 27.7 29.7 65.7 91.7 96.7-81.9 42.6-346.3-22.7-342.6-202.4z"/>
            <path id="Object 5" class="logo-accent-1" d="m1746.5 389.7c22.7-25.9 55.6-40.8 90-40.8 34.5 0 67.4 14.9 90.1 40.8 60.4 68.8 588.3 684.3 545.8 1116.1-6.4 65.9-65 113.8-130.9 107.5-65.9-6.5-114-65.1-107.6-130.9 22.8-231.6-228.2-608.7-397-824.8-43.9 56.3-99.2 130.6-154.6 214.3q0 0.1 0.1 0.3c-51.2 77.8-99.5 157.7-141.2 241q-0.1-0.2-0.2-0.3c-91 181.8-123.4 327-91.5 418.1 9.6 27.6 29.7 65.6 91.5 96.6 81.9 42.6 346.3-22.7 342.5-202.4-5-111-41-237.7-108.7-380.9 108.9 230.2 135.9 417.8 84.1 565.7-29.6 84.6-101.8 198.3-278.2 260.3-12.9 4.5-26.3 6.8-39.7 6.8-13.5 0-27-2.3-39.9-6.8-176.1-62-248.3-175.7-277.8-260.2-63.8-182.2-4.5-430.3 176.4-738.2 45.2-77.9 89.4-144.3 141.3-217.9 107.8-152.5 200.4-258.5 205.5-264.3z"/>
            <path id="Object 6" fill-rule="evenodd" class="logo-accent-2" d="m938.3 650.7c51.8 73.6 96.1 140.1 141.3 217.9 73.8 125.6 127.3 241.3 160.6 346.6-49.2 154.2-55 286.3-16.9 394.9 4.2 12.2 9.4 24.9 15.6 38.1-37.5 77.3-112.1 166.4-260.7 218.8-13 4.5-26.4 6.8-39.9 6.8-13.4 0-26.8-2.2-39.7-6.8-176.4-62-248.7-175.7-278.2-260.2-46.8-133.4-29.4-299.1 54.4-499.2-48.6 116.3-74.8 220.9-79.1 314.3-3.7 179.7 260.6 245 342.6 202.4 61.8-31 81.8-69 91.5-96.6 31.9-91.1-0.5-236.3-91.5-418.1q-0.1 0.2-0.2 0.3c-41.7-83.3-90-163.2-141.2-241q0-0.2 0.1-0.3c-55.4-83.7-110.7-158-154.6-214.2-168.8 216-419.8 593.1-397.1 824.7 6.5 65.8-41.6 124.5-107.5 131-65.9 6.3-124.5-41.7-131-107.5-42.5-431.9 485.5-1047.4 545.9-1116.2 22.7-25.9 55.5-40.8 90-40.8 34.5 0 67.3 14.9 90.1 40.8 5.1 5.8 97.7 111.8 205.5 264.3zm-249 423.4q7.5-16.9 15.7-34.2c-5.4 11.5-10.7 22.9-15.7 34.2z"/>
            <path id="Object 7" class="logo-primary" d="m1504.7 632.6c-50.1 71.2-93.2 136.2-137.2 212q-2.4 4.2-4.8 8.3c-55.1-84.4-90-131.5-139-194.6-35.9 45.9-81.5 106.9-126.3 171.9-46.5-78.1-95.3-151.1-141.2-215.4 94.1-129.3 172.7-219.5 177.2-224.7 22.8-26 55.6-40.8 90.1-40.8 34.5 0 67.3 14.8 90.1 40.7 22.8 26.1 90.6 99.5 191.1 242.6z"/>
            <path id="Object 8" class="logo-accent-3" d="m648.3 305.6q-139.8-13.1-152.8-152.8 13-139.7 152.8-152.7 139.7 13 152.7 152.7-13 139.7-152.7 152.8z"/>
            <path id="Object 9" class="logo-primary" d="m1242.5 314.6q-139.8-13-152.8-152.8 13-139.7 152.8-152.7 139.7 13 152.8 152.7-13.1 139.8-152.8 152.8z"/>
            <path id="Object 10" class="logo-accent-1" d="m1811.6 313q-139.8-13-152.8-152.7 13-139.7 152.8-152.8 139.7 13.1 152.8 152.8-13.1 139.7-152.8 152.7z"/>
          </g>
        </svg>
        <span class="site-brand-text">
          <span>kita</span>
          <span>membantu</span>
          <span>sesama</span>
        </span>
      </a>

      <!-- Hamburger Toggle BTN -->
      <button class="po rc" @click="navigationOpen = !navigationOpen">
        <span class="rc i pf re pd">
          <span class="du-block h q vd yc">
            <span class="rc i r s eh um tg te rd eb ml jl dl" :class="{ 'ue el': !navigationOpen }"></span>
            <span class="rc i r s eh um tg te rd eb ml jl fl" :class="{ 'ue qr': !navigationOpen }"></span>
            <span class="rc i r s eh um tg te rd eb ml jl gl" :class="{ 'ue hl': !navigationOpen }"></span>
          </span>
          <span class="du-block h q vd yc lf">
            <span class="rc eh um tg ml jl el h na r ve yc" :class="{ 'sd dl': !navigationOpen }"></span>
            <span class="rc eh um tg ml jl qr h s pa vd rd" :class="{ 'sd rr': !navigationOpen }"></span>
          </span>
        </span>
      </button>
      <!-- Hamburger Toggle BTN -->
    </div>

    <div class="vd wo/4 sd qo f ho oo wf yf" :class="{ 'd hh rm sr td ud qg ug jc yh': navigationOpen }">
      <nav>
        <ul class="tc _o sf yo cg ep">
          <li><a href="{{ route('frontend.index') }}" class="xl" :class="{ 'mk': page === 'home' }">Beranda</a></li>
          <li><a href="{{ route('frontend.index') }}#tentang-kami" class="xl">Tentang Kami</a></li>
          <li><a href="{{ route('frontend.index') }}#program" class="xl">Program</a></li>
          <li><a href="{{ route('frontend.index') }}#cara-donasi" class="xl">Cara Berdonasi</a></li>
          <li><a href="{{ route('frontend.index') }}#galeri" class="xl">Galeri</a></li>
          <li><a href="{{ route('frontend.index') }}#transparansi" class="xl">Transparansi</a></li>
          <li><a href="{{ route('frontend.index') }}#kontak" class="xl">Kontak</a></li>
        </ul>
      </nav>

      <div class="tc wf ig pb no">
        <div class="pc h io pa ra" :class="navigationOpen ? '!-ud-visible' : 'd'">
          <label class="rc ab i">
            <input type="checkbox" :value="darkMode" @change="darkMode = !darkMode" class="pf vd yc uk h r za ab" />
            <!-- Icon Sun -->
            <svg :class="{ 'wn' : page === 'home', 'xh' : page === 'home' && stickyMenu }" class="th om" width="25"
              height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                d="M12.0908 18.6363C10.3549 18.6363 8.69 17.9467 7.46249 16.7192C6.23497 15.4916 5.54537 13.8268 5.54537 12.0908C5.54537 10.3549 6.23497 8.69 7.46249 7.46249C8.69 6.23497 10.3549 5.54537 12.0908 5.54537C13.8268 5.54537 15.4916 6.23497 16.7192 7.46249C17.9467 8.69 18.6363 10.3549 18.6363 12.0908C18.6363 13.8268 17.9467 15.4916 16.7192 16.7192C15.4916 17.9467 13.8268 18.6363 12.0908 18.6363ZM12.0908 16.4545C13.2481 16.4545 14.358 15.9947 15.1764 15.1764C15.9947 14.358 16.4545 13.2481 16.4545 12.0908C16.4545 10.9335 15.9947 9.8236 15.1764 9.00526C14.358 8.18692 13.2481 7.72718 12.0908 7.72718C10.9335 7.72718 9.8236 8.18692 9.00526 9.00526C8.18692 9.8236 7.72718 10.9335 7.72718 12.0908C7.72718 13.2481 8.18692 14.358 9.00526 15.1764C9.8236 15.9947 10.9335 16.4545 12.0908 16.4545ZM10.9999 0.0908203H13.1817V3.36355H10.9999V0.0908203ZM10.9999 20.8181H13.1817V24.0908H10.9999V20.8181ZM2.83446 4.377L4.377 2.83446L6.69082 5.14828L5.14828 6.69082L2.83446 4.37809V4.377ZM17.4908 19.0334L19.0334 17.4908L21.3472 19.8046L19.8046 21.3472L17.4908 19.0334ZM19.8046 2.83337L21.3472 4.377L19.0334 6.69082L17.4908 5.14828L19.8046 2.83446V2.83337ZM5.14828 17.4908L6.69082 19.0334L4.377 21.3472L2.83446 19.8046L5.14828 17.4908ZM24.0908 10.9999V13.1817H20.8181V10.9999H24.0908ZM3.36355 10.9999V13.1817H0.0908203V10.9999H3.36355Z"
                fill="" />
            </svg>
            <!-- Icon Sun -->
            <img class="xc nm" src="{{ asset('frontend/images/icon-moon.svg') }}" alt="Moon" />
          </label>
        </div>

        <!-- Sign In / Sign Up removed per request -->
      </div>
    </div>
  </div>
</header>
<!-- ===== Header End ===== -->




