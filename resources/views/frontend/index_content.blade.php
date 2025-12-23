
    <!-- ===== Hero Start ===== -->
    <section class="gj do ir hj sp jr i pg">
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
              <img src="{{ asset("frontend/images/icon-01.svg") }}" alt="Icon" />
            </div>
            <div>
              <h4 class="ek yj go kk wm xb">24/7 Support</h4>
              <p>Lorem ipsum dolor sit amet conse adipiscing elit.</p>
            </div>
          </div>

          <!-- Small Features Item -->
          <div class="animate_top kn to/3 tc cg oq">
            <div class="tc wf xf cf ae cd rg nh">
              <img src="{{ asset("frontend/images/icon-02.svg") }}" alt="Icon" />
            </div>
            <div>
              <h4 class="ek yj go kk wm xb">Take Ownership</h4>
              <p>Lorem ipsum dolor sit amet conse adipiscing elit.</p>
            </div>
          </div>

          <!-- Small Features Item -->
          <div class="animate_top kn to/3 tc cg oq">
            <div class="tc wf xf cf ae cd rg oh">
              <img src="{{ asset("frontend/images/icon-03.svg") }}" alt="Icon" />
            </div>
            <div>
              <h4 class="ek yj go kk wm xb">Team Work</h4>
              <p>Lorem ipsum dolor sit amet conse adipiscing elit.</p>
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
            <div>
              <img src="{{ asset("frontend/images/shape-05.svg") }}" alt="Shape" class="h -ud-left-5 x" />
              <img src="{{ asset("frontend/images/about-01.png") }}" alt="About" class="ib" />
              <img src="{{ asset("frontend/images/about-02.png") }}" alt="About" />
            </div>
            <div>
              <img src="{{ asset("frontend/images/shape-06.svg") }}" alt="Shape" />
              <img src="{{ asset("frontend/images/about-03.png") }}" alt="About" class="ob gb" />
              <img src="{{ asset("frontend/images/shape-07.svg") }}" alt="Shape" class="bb" />
            </div>
          </div>

          <!-- About Content -->
          <div class="animate_right jn/2">
            <h4 class="ek yj mk gb">Visi</h4>
            <li>
              <p class="uo">Menjadi Organisasi Sosial Kemanusiaan Internasional yang unggul dan profesional untuk kehidupan Kemanusiaan yang lebih baik.</p>  
            </li>
            <br>
            <h4 class="ek yj mk gb">Misi</h4>
            <li>
              <p class="uo">Mengembangkan organisasi sosial kemanusiaan yang humanis dan profesional.</p>  
            </li>
            <li>
              <p class="uo">Mengembangkan potensi kebaikan masyarakat menjadi potensi perubahan yang lebih baik</p>
            </li>
          </div>
        </div>
      </div>
    </section>
    <!-- ===== About End ===== -->

    <!-- ===== Team Start ===== -->
    <section class="i pg ji gp uq">
      <!-- Bg Shapes -->
      <span class="rc h s r vd fd/5 fh rm"></span>
      <img src="{{ asset("frontend/images/shape-08.svg") }}" alt="Shape Bg" class="h q r" />
      <img src="{{ asset("frontend/images/shape-09.svg") }}" alt="Shape" class="of h y z/2" />
      <img src="{{ asset("frontend/images/shape-10.svg") }}" alt="Shape" class="h _ aa" />
      <img src="{{ asset("frontend/images/shape-11.svg") }}" alt="Shape" class="of h m ba" />

      <!-- Section Title Start -->
      <div
        x-data="{ sectionTitle: `Pendiri Kita Membantu Sesama`, sectionTitleText: `Pendiri kami adalah individu yang berpengalaman dan berdedikasi, berkomitmen untuk membangun organisasi yang memberikan bantuan serta solusi berkelanjutan bagi masyarakat.` }">
        <div class="animate_top bb ze rj ki xn vq">
          <h2 x-text="sectionTitle" class="fk vj pr kk wm on/5 gq/2 bb _b">
          </h2>
          <p class="bb on/5 wo/5 hq" x-text="sectionTitleText"></p>
        </div>


      </div>
      <!-- Section Title End -->

      <div class="bb ze i va ki xn xq jb jo">
        <div class="wc qf pn xo gg cp">
          <!-- Team Item -->
          <div class="animate_top rj">
            <div class="c i pg z-1">
              <img class="vd" src="{{ asset("frontend/images/team-01.png") }}" alt="Team" />

              <div class="ef im nl il">
                <span class="h -ud-left-5 -ud-bottom-21 rc de gd gh if wa"></span>
                <span class="h s p rc vd hd mh va"></span>
                <div class="h s p vd ij jj xa">
                  <ul class="tc xf wf gg">
                    <li>
                      <a href="#!">
                        <svg class="uh vl ml il" width="10" height="18" viewBox="0 0 10 18" fill="none"
                          xmlns="http://www.w3.org/2000/svg">
                          <path
                            d="M6.66634 10.25H8.74968L9.58301 6.91669H6.66634V5.25002C6.66634 4.39169 6.66634 3.58335 8.33301 3.58335H9.58301V0.783354C9.31134 0.74752 8.28551 0.666687 7.20218 0.666687C4.93968 0.666687 3.33301 2.04752 3.33301 4.58335V6.91669H0.833008V10.25H3.33301V17.3334H6.66634V10.25Z"
                            fill="" />
                        </svg>
                      </a>
                    </li>
                    <li>
                      <a href="#!">
                        <svg class="uh vl ml il" width="18" height="14" viewBox="0 0 18 14" fill="none"
                          xmlns="http://www.w3.org/2000/svg">
                          <path
                            d="M17.4683 1.71333C16.8321 1.99475 16.1574 2.17956 15.4666 2.26167C16.1947 1.82619 16.7397 1.14085 16.9999 0.333333C16.3166 0.74 15.5674 1.025 14.7866 1.17917C14.2621 0.617982 13.5669 0.245803 12.809 0.120487C12.0512 -0.00482822 11.2732 0.123742 10.596 0.486211C9.91875 0.848679 9.38024 1.42474 9.06418 2.12483C8.74812 2.82492 8.67221 3.60982 8.84825 4.3575C7.46251 4.28805 6.10686 3.92794 4.86933 3.30055C3.63179 2.67317 2.54003 1.79254 1.66492 0.715833C1.35516 1.24788 1.19238 1.85269 1.19326 2.46833C1.19326 3.67667 1.80826 4.74417 2.74326 5.36917C2.18993 5.35175 1.64878 5.20232 1.16492 4.93333V4.97667C1.16509 5.78142 1.44356 6.56135 1.95313 7.18422C2.46269 7.80709 3.17199 8.23456 3.96075 8.39417C3.4471 8.53337 2.90851 8.55388 2.38576 8.45417C2.60814 9.14686 3.04159 9.75267 3.62541 10.1868C4.20924 10.6209 4.9142 10.8615 5.64159 10.875C4.91866 11.4428 4.0909 11.8625 3.20566 12.1101C2.32041 12.3578 1.39503 12.4285 0.482422 12.3183C2.0755 13.3429 3.93 13.8868 5.82409 13.885C12.2349 13.885 15.7408 8.57417 15.7408 3.96833C15.7408 3.81833 15.7366 3.66667 15.7299 3.51833C16.4123 3.02514 17.0013 2.41418 17.4691 1.71417L17.4683 1.71333Z"
                            fill="" />
                        </svg>
                      </a>
                    </li>
                    <li>
                      <a href="#!">
                        <svg class="uh vl ml il" width="17" height="16" viewBox="0 0 17 16" fill="none"
                          xmlns="http://www.w3.org/2000/svg">
                          <path
                            d="M3.78353 2.16665C3.78331 2.60867 3.6075 3.03251 3.29478 3.34491C2.98207 3.65732 2.55806 3.8327 2.11603 3.83248C1.674 3.83226 1.25017 3.65645 0.937761 3.34373C0.625357 3.03102 0.449975 2.60701 0.450196 2.16498C0.450417 1.72295 0.626223 1.29912 0.93894 0.986712C1.25166 0.674307 1.67567 0.498925 2.1177 0.499146C2.55972 0.499367 2.98356 0.675173 3.29596 0.98789C3.60837 1.30061 3.78375 1.72462 3.78353 2.16665V2.16665ZM3.83353 5.06665H0.500195V15.5H3.83353V5.06665ZM9.1002 5.06665H5.78353V15.5H9.06686V10.025C9.06686 6.97498 13.0419 6.69165 13.0419 10.025V15.5H16.3335V8.89165C16.3335 3.74998 10.4502 3.94165 9.06686 6.46665L9.1002 5.06665V5.06665Z"
                            fill="" />
                        </svg>
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            <h4 class="yj go kk wm ob zb">Olivia Andrium</h4>
            <p>Product Manager</p>
          </div>

          <!-- Team Item -->
          <div class="animate_top rj">
            <div class="c i pg z-1">
              <img class="vd" src="{{ asset("frontend/images/team-02.png") }}" alt="Team" />

              <div class="ef im nl il">
                <span class="h -ud-left-5 -ud-bottom-21 rc de gd gh if wa"></span>
                <span class="h s p rc vd hd mh va"></span>
                <div class="h s p vd ij jj xa">
                  <ul class="tc xf wf gg">
                    <li>
                      <a href="#!">
                        <svg class="uh vl ml il" width="10" height="18" viewBox="0 0 10 18" fill="none"
                          xmlns="http://www.w3.org/2000/svg">
                          <path
                            d="M6.66634 10.25H8.74968L9.58301 6.91669H6.66634V5.25002C6.66634 4.39169 6.66634 3.58335 8.33301 3.58335H9.58301V0.783354C9.31134 0.74752 8.28551 0.666687 7.20218 0.666687C4.93968 0.666687 3.33301 2.04752 3.33301 4.58335V6.91669H0.833008V10.25H3.33301V17.3334H6.66634V10.25Z"
                            fill="" />
                        </svg>
                      </a>
                    </li>
                    <li>
                      <a href="#!">
                        <svg class="uh vl ml il" width="18" height="14" viewBox="0 0 18 14" fill="none"
                          xmlns="http://www.w3.org/2000/svg">
                          <path
                            d="M17.4683 1.71333C16.8321 1.99475 16.1574 2.17956 15.4666 2.26167C16.1947 1.82619 16.7397 1.14085 16.9999 0.333333C16.3166 0.74 15.5674 1.025 14.7866 1.17917C14.2621 0.617982 13.5669 0.245803 12.809 0.120487C12.0512 -0.00482822 11.2732 0.123742 10.596 0.486211C9.91875 0.848679 9.38024 1.42474 9.06418 2.12483C8.74812 2.82492 8.67221 3.60982 8.84825 4.3575C7.46251 4.28805 6.10686 3.92794 4.86933 3.30055C3.63179 2.67317 2.54003 1.79254 1.66492 0.715833C1.35516 1.24788 1.19238 1.85269 1.19326 2.46833C1.19326 3.67667 1.80826 4.74417 2.74326 5.36917C2.18993 5.35175 1.64878 5.20232 1.16492 4.93333V4.97667C1.16509 5.78142 1.44356 6.56135 1.95313 7.18422C2.46269 7.80709 3.17199 8.23456 3.96075 8.39417C3.4471 8.53337 2.90851 8.55388 2.38576 8.45417C2.60814 9.14686 3.04159 9.75267 3.62541 10.1868C4.20924 10.6209 4.9142 10.8615 5.64159 10.875C4.91866 11.4428 4.0909 11.8625 3.20566 12.1101C2.32041 12.3578 1.39503 12.4285 0.482422 12.3183C2.0755 13.3429 3.93 13.8868 5.82409 13.885C12.2349 13.885 15.7408 8.57417 15.7408 3.96833C15.7408 3.81833 15.7366 3.66667 15.7299 3.51833C16.4123 3.02514 17.0013 2.41418 17.4691 1.71417L17.4683 1.71333Z"
                            fill="" />
                        </svg>
                      </a>
                    </li>
                    <li>
                      <a href="#!">
                        <svg class="uh vl ml il" width="17" height="16" viewBox="0 0 17 16" fill="none"
                          xmlns="http://www.w3.org/2000/svg">
                          <path
                            d="M3.78353 2.16665C3.78331 2.60867 3.6075 3.03251 3.29478 3.34491C2.98207 3.65732 2.55806 3.8327 2.11603 3.83248C1.674 3.83226 1.25017 3.65645 0.937761 3.34373C0.625357 3.03102 0.449975 2.60701 0.450196 2.16498C0.450417 1.72295 0.626223 1.29912 0.93894 0.986712C1.25166 0.674307 1.67567 0.498925 2.1177 0.499146C2.55972 0.499367 2.98356 0.675173 3.29596 0.98789C3.60837 1.30061 3.78375 1.72462 3.78353 2.16665V2.16665ZM3.83353 5.06665H0.500195V15.5H3.83353V5.06665ZM9.1002 5.06665H5.78353V15.5H9.06686V10.025C9.06686 6.97498 13.0419 6.69165 13.0419 10.025V15.5H16.3335V8.89165C16.3335 3.74998 10.4502 3.94165 9.06686 6.46665L9.1002 5.06665V5.06665Z"
                            fill="" />
                        </svg>
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            <h4 class="yj go kk wm ob zb">Jemse Kemorun</h4>
            <p>Product Designer</p>
          </div>

          <!-- Team Item -->
          <div class="animate_top rj">
            <div class="c i pg z-1">
              <img class="vd" src="{{ asset("frontend/images/team-03.png") }}" alt="Team" />

              <div class="ef im nl il">
                <span class="h -ud-left-5 -ud-bottom-21 rc de gd gh if wa"></span>
                <span class="h s p rc vd hd mh va"></span>
                <div class="h s p vd ij jj xa">
                  <ul class="tc xf wf gg">
                    <li>
                      <a href="#!">
                        <svg class="uh vl ml il" width="10" height="18" viewBox="0 0 10 18" fill="none"
                          xmlns="http://www.w3.org/2000/svg">
                          <path
                            d="M6.66634 10.25H8.74968L9.58301 6.91669H6.66634V5.25002C6.66634 4.39169 6.66634 3.58335 8.33301 3.58335H9.58301V0.783354C9.31134 0.74752 8.28551 0.666687 7.20218 0.666687C4.93968 0.666687 3.33301 2.04752 3.33301 4.58335V6.91669H0.833008V10.25H3.33301V17.3334H6.66634V10.25Z"
                            fill="" />
                        </svg>
                      </a>
                    </li>
                    <li>
                      <a href="#!">
                        <svg class="uh vl ml il" width="18" height="14" viewBox="0 0 18 14" fill="none"
                          xmlns="http://www.w3.org/2000/svg">
                          <path
                            d="M17.4683 1.71333C16.8321 1.99475 16.1574 2.17956 15.4666 2.26167C16.1947 1.82619 16.7397 1.14085 16.9999 0.333333C16.3166 0.74 15.5674 1.025 14.7866 1.17917C14.2621 0.617982 13.5669 0.245803 12.809 0.120487C12.0512 -0.00482822 11.2732 0.123742 10.596 0.486211C9.91875 0.848679 9.38024 1.42474 9.06418 2.12483C8.74812 2.82492 8.67221 3.60982 8.84825 4.3575C7.46251 4.28805 6.10686 3.92794 4.86933 3.30055C3.63179 2.67317 2.54003 1.79254 1.66492 0.715833C1.35516 1.24788 1.19238 1.85269 1.19326 2.46833C1.19326 3.67667 1.80826 4.74417 2.74326 5.36917C2.18993 5.35175 1.64878 5.20232 1.16492 4.93333V4.97667C1.16509 5.78142 1.44356 6.56135 1.95313 7.18422C2.46269 7.80709 3.17199 8.23456 3.96075 8.39417C3.4471 8.53337 2.90851 8.55388 2.38576 8.45417C2.60814 9.14686 3.04159 9.75267 3.62541 10.1868C4.20924 10.6209 4.9142 10.8615 5.64159 10.875C4.91866 11.4428 4.0909 11.8625 3.20566 12.1101C2.32041 12.3578 1.39503 12.4285 0.482422 12.3183C2.0755 13.3429 3.93 13.8868 5.82409 13.885C12.2349 13.885 15.7408 8.57417 15.7408 3.96833C15.7408 3.81833 15.7366 3.66667 15.7299 3.51833C16.4123 3.02514 17.0013 2.41418 17.4691 1.71417L17.4683 1.71333Z"
                            fill="" />
                        </svg>
                      </a>
                    </li>
                    <li>
                      <a href="#!">
                        <svg class="uh vl ml il" width="17" height="16" viewBox="0 0 17 16" fill="none"
                          xmlns="http://www.w3.org/2000/svg">
                          <path
                            d="M3.78353 2.16665C3.78331 2.60867 3.6075 3.03251 3.29478 3.34491C2.98207 3.65732 2.55806 3.8327 2.11603 3.83248C1.674 3.83226 1.25017 3.65645 0.937761 3.34373C0.625357 3.03102 0.449975 2.60701 0.450196 2.16498C0.450417 1.72295 0.626223 1.29912 0.93894 0.986712C1.25166 0.674307 1.67567 0.498925 2.1177 0.499146C2.55972 0.499367 2.98356 0.675173 3.29596 0.98789C3.60837 1.30061 3.78375 1.72462 3.78353 2.16665V2.16665ZM3.83353 5.06665H0.500195V15.5H3.83353V5.06665ZM9.1002 5.06665H5.78353V15.5H9.06686V10.025C9.06686 6.97498 13.0419 6.69165 13.0419 10.025V15.5H16.3335V8.89165C16.3335 3.74998 10.4502 3.94165 9.06686 6.46665L9.1002 5.06665V5.06665Z"
                            fill="" />
                        </svg>
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            <h4 class="yj go kk wm ob zb">Avi Pestarica</h4>
            <p>Web Designer</p>
          </div>
        </div>
      </div>
    </section>
    <!-- ===== Team End ===== -->

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
      <div class="bb ze ki xn 2xl:ud-px-0 jb">
        <div class="animate_top bb ze rj ki xn vq">
          <h2 class="fk vj pr kk wm on/5 gq/2 bb _b">Program Kami</h2>
          <p class="bb on/5 wo/5 hq">Program-program unggulan kami meliputi pendidikan, kesehatan, dan bantuan darurat yang dirancang untuk menciptakan dampak berkelanjutan bagi komunitas.</p>
        </div>

        <div class="bb ze ki xn 2xl:ud-px-0 jb">
          <div id="programs" class="projects-wrapper tc -ud-mx-5">
            <div class="project-sizer"></div>
            @foreach($landingPrograms ?? [] as $p)
              @php
                $image = asset('frontend/images/project-01.png');
                if (!empty($p->image_url)) {
                    $image = preg_match('/^https?:\/\//', $p->image_url) ? $p->image_url : asset('storage/' . $p->image_url);
                }
                $excerpt = strip_tags($p->description ?? '');
                if (strlen($excerpt) > 120) $excerpt = substr($excerpt, 0, 117) . '...';
              @endphp

              <div class="project-item wi fb vd jn/2 to/3">
                <div class="c i pg sg z-1">
                  <img src="{{ $image }}" alt="{{ $p->name }}" />

                  <div class="h s r df nl kl im tc sf wf xf vd yc sg al hh/20 z-10">
                    <h4 class="ek tj kk hc">{{ $p->name }}</h4>
                    <p>{{ $excerpt }}</p>
                  </div>
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
                    div.className = 'project-item wi fb vd jn/2 to/3';
                    const img = p.image_url && /^https?:\/\//.test(p.image_url) ? p.image_url : (p.image_url ? `/storage/${p.image_url}` : '/frontend/images/project-01.png');
                    const excerpt = (p.description || '').replace(/<[^>]+>/g, '').slice(0, 117) + (p.description && p.description.length > 120 ? '...' : '');
                    div.innerHTML = `<div class="c i pg sg z-1"><img src="${img}" alt="${p.name}" /><div class="h s r df nl kl im tc sf wf xf vd yc sg al hh/20 z-10"><h4 class="ek tj kk hc">${p.name}</h4><p>${excerpt}</p></div></div>`;
                    container.appendChild(div);
                    if (container.querySelectorAll('.project-item').length > 6) {
                      container.classList.add('projects-grid-fallback');
                    }
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

          // Fallbacks and forced grid for >3 items
          (function(){
            try {
              const itemCount = container ? container.querySelectorAll('.project-item').length : 0;

              // If more than 3 items, force CSS grid fallback to avoid overlap
              if (itemCount > 3) {
                container.classList.add('projects-grid-fallback');
              }

              // Also re-check after images load / window load in case Isotope initialized too early
              window.addEventListener('load', function(){
                try {
                  const h = container.clientHeight || 0;
                  if (h < 200) {
                    container.classList.add('projects-grid-fallback');
                  }
                } catch (e) {
                  console.error('programs: fallback check failed on load', e);
                }
              });

              // re-check shortly after (images may take time to load)
              setTimeout(function(){
                try {
                  const h = container.clientHeight || 0;
                  if (h < 200) {
                    container.classList.add('projects-grid-fallback');
                  }
                } catch (e) {
                  console.error('programs: fallback check failed', e);
                }
              }, 600);
            } catch (e) {
              console.error('programs: init fallback failed', e);
            }
          })();
        })();
      </script>
      <style>
        .projects-grid-fallback{display:grid;gap:1rem;align-items:start;grid-template-columns:repeat(2,1fr)}
        @media (min-width: 768px){
          .projects-grid-fallback{grid-template-columns:repeat(3,1fr)}
        }
        @media (min-width: 1024px){
          .projects-grid-fallback{grid-template-columns:repeat(4,1fr)}
        }
        .projects-grid-fallback .project-item{position:relative;display:block;margin-bottom:1rem}
        /* ensure next sections clear any floats */
        .projects-wrapper::after{content:'';display:block;clear:both}
        /* Fix right-side gap: neutralize negative margins and prevent horizontal overflow */
        .projects-wrapper.-ud-mx-5{margin-left:0;margin-right:0;padding-left:1.25rem;padding-right:1.25rem;box-sizing:border-box;overflow:hidden}
        .projects-wrapper .project-item img{display:block;width:100%;height:auto}
      </style>
      @endpush
@endif

        </div>
      </div>
    </section>
    <!-- ===== Projects End ===== -->

    <!-- ===== Counter Start ===== -->
    <section class="i pg qh rm ji hp">
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

    <!-- ===== Clients Start ===== -->
    <section class="pj vp mr">
      <!-- Section Title Start -->
      <div
        x-data="{ sectionTitle: `Mitra Kami`, sectionTitleText: `Mitra kami terdiri dari organisasi, perusahaan, dan individu yang bekerja sama untuk mendukung dan memperkuat program-program kemanusiaan kami.` }">
        <div class="animate_top bb ze rj ki xn vq">
          <h2 x-text="sectionTitle" class="fk vj pr kk wm on/5 gq/2 bb _b">
          </h2>
          <p class="bb on/5 wo/5 hq" x-text="sectionTitleText"></p>
        </div>
      </div>
      <!-- Section Title End -->

      <div class="bb ze ah ch pm hj xp ki xn 2xl:ud-px-49 bc">
        <div class="wc rf qn zf cp kq xf wf">
          <a href="#!" class="rc animate_top">
            <img class="th wl ml il zl om" src="{{ asset("frontend/images/brand-light-01.svg") }}" alt="Clients" />
            <img class="xc sk ml il zl nm" src="{{ asset("frontend/images/brand-dark-01.svg") }}" alt="Clients" />
          </a>
          <a href="#!" class="rc animate_top">
            <img class="tk ml il zl om" src="{{ asset("frontend/images/brand-light-02.svg") }}" alt="Clients" />
            <img class="xc sk ml il zl nm" src="{{ asset("frontend/images/brand-dark-02.svg") }}" alt="Clients" />
          </a>
          <a href="#!" class="rc animate_top">
            <img class="tk ml il zl om" src="{{ asset("frontend/images/brand-light-03.svg") }}" alt="Clients" />
            <img class="xc sk ml il zl nm" src="{{ asset("frontend/images/brand-dark-03.svg") }}" alt="Clients" />
          </a>
          <a href="#!" class="rc animate_top">
            <img class="tk ml il zl om" src="{{ asset("frontend/images/brand-light-04.svg") }}" alt="Clients" />
            <img class="xc sk ml il zl nm" src="{{ asset("frontend/images/brand-dark-04.svg") }}" alt="Clients" />
          </a>
          <a href="#!" class="rc animate_top">
            <img class="tk ml il zl om" src="{{ asset("frontend/images/brand-light-05.svg") }}" alt="Clients" />
            <img class="xc sk ml il zl nm" src="{{ asset("frontend/images/brand-dark-05.svg") }}" alt="Clients" />
          </a>
          <a href="#!" class="rc animate_top">
            <img class="tk ml il zl om" src="{{ asset("frontend/images/brand-light-06.svg") }}" alt="Clients" />
            <img class="xc sk ml il zl nm" src="{{ asset("frontend/images/brand-dark-06.svg") }}" alt="Clients" />
          </a>
        </div>
      </div>
    </section>
    <!-- ===== Clients End ===== -->

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
            @endphp

            <div class="animate_top sg vk rm xm">
              <div class="c rc i z-1 pg">
                <img class="w-full" src="{{ $img }}" alt="Kegiatan" />

                <div class="im h r s df vd yc wg tc wf xf al hh/20 nl il z-10">
                  <a href="{{ route('frontend.blog-single') }}" class="vc ek rg lk gh sl ml il gi hi">Read More</a>
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
                  <a href="{{ route('frontend.blog-single') }}">{{ $k->title }}</a>
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
                    div.innerHTML = `<div class="c rc i z-1 pg"><img class="w-full" src="${img}" alt="Kegiatan" /><div class="im h r s df vd yc wg tc wf xf al hh/20 nl il z-10"><a href="/blog-single" class="vc ek rg lk gh sl ml il gi hi">Read More</a></div></div><div class="yh"><div class="tc uf wf ag jq"><div class="tc wf ag"><img src="/frontend/images/icon-man.svg" alt="User" /><p>${k.organizer || 'Team'}</p></div><div class="tc wf ag"><img src="/frontend/images/icon-calender.svg" alt="Calender" /><p>${date}</p></div></div><h4 class="ek tj ml il kk wm xl eq lb"><a href="/blog-single">${k.title}</a></h4><p>${excerpt}</p></div>`;
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
    <section id="support" class="i pg fh rm ji gp uq">
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
  