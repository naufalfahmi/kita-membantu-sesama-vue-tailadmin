    <!-- ===== Contact Start ===== -->
    <section id="kontak" class="i pg fh rm ji gp uq">
      <!-- Bg Shapes -->
      <!-- <img src="{{ asset("frontend/images/shape-06.svg") }}" alt="Shape" class="h aa y" /> -->
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
              @php
                $addressesRaw = $lp->address ?? null;
                $addresses = null;
                if ($addressesRaw) {
                  if (is_array($addressesRaw)) {
                    $addresses = $addressesRaw;
                  } else {
                    $decoded = json_decode($addressesRaw, true);
                    $addresses = is_array($decoded) ? $decoded : null;
                  }
                }
              @endphp

              @if(is_array($addresses) && count($addresses) > 0)
                <ul class="list-disc list-inside">
                  @foreach($addresses as $addr)
                    @php
                      if (is_array($addr)) {
                        $label = $addr['label'] ?? null;
                        $value = $addr['value'] ?? ($addr['address'] ?? null);
                      } else {
                        $label = null;
                        $value = (string)$addr;
                      }
                      $value = $value ?? '';
                    @endphp
                    <li>
                      @if($label)
                        <strong>{{ $label }}</strong>: 
                      @endif
                      {{ $value }}
                    </li>
                  @endforeach
                </ul>
              @else
                <p>{{ $addressText }}</p>
              @endif
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
