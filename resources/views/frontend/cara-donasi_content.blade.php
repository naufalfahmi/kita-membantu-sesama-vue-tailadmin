<!-- ===== Cara Berdonasi Start ===== -->
<section id="cara-donasi" class="i pg fh rm ji gp uq">
  <!-- Bg Shapes -->
  <!-- <img src="{{ asset('frontend/images/shape-06.svg') }}" alt="Shape" class="h aa y" /> -->
  <img src="{{ asset('frontend/images/shape-03.svg') }}" alt="Shape" class="h ca u" />
  <img src="{{ asset('frontend/images/shape-07.svg') }}" alt="Shape" class="h w da ee" />
  <img src="{{ asset('frontend/images/shape-12.svg') }}" alt="Shape" class="h p s" />
  <img src="{{ asset('frontend/images/shape-13.svg') }}" alt="Shape" class="h r q" />

  <!-- Section Title Start -->
  <div class="animate_top bb ze rj ki xn vq">
    <h2 class="fk vj pr kk wm on/5 gq/2 bb _b">Cara Berdonasi</h2>
    <p class="bb on/5 wo/5 hq">Berbagai cara mudah untuk Anda berpartisipasi membantu sesama melalui donasi.</p>
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
