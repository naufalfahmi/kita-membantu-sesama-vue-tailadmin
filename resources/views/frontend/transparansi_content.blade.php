<!-- ===== Counter Start ===== -->
<section id="transparansi" class="ji gp uq">

  @php
    $lp = $landingProfile ?? null;
    $title = optional($lp)->transparency_title ?? 'Transparansi';
    $description = optional($lp)->transparency_description ?? 'Transparansi adalah prinsip yang harus diikuti oleh setiap organisasi untuk memastikan bahwa informasi yang diberikan kepada masyarakat adalah akurat, terpercaya, dan dapat diandalkan.';
    
    $kantorCabangText = optional($lp)->transparency_kantor_cabang_text ?? 'Kantor Cabang';
    $kantorCabangVal = ($lp && $lp->transparency_kantor_cabang_total > 0) ? $lp->transparency_kantor_cabang_total : ($kantorCabangCount ?? 0);
    
    $donaturText = optional($lp)->transparency_donatur_text ?? 'Donatur';
    $donaturVal = ($lp && $lp->transparency_donatur_total > 0) ? $lp->transparency_donatur_total : ($donaturCount ?? 0);
    
    $fundraiserText = optional($lp)->transparency_fundraiser_text ?? 'Fundraiser';
    $fundraiserVal = ($lp && $lp->transparency_fundraiser_total > 0) ? $lp->transparency_fundraiser_total : ($fundraiserCount ?? 0);
    
    $penggalanganDanaText = optional($lp)->transparency_penggalangan_dana_text ?? 'Penggalangan Dana';
    $penggalanganDanaVal = ($lp && $lp->transparency_penggalangan_dana_total > 0) ? $lp->transparency_penggalangan_dana_total : ($penggalanganDanaCount ?? 0);
  @endphp

  <!-- Section Title Start -->
  <div class="animate_top bb ze rj ki xn vq">
    <h2 class="fk vj pr kk wm on/5 gq/2 bb _b">{{ $title }}</h2>
    <p class="bb on/5 wo/5 hq">{{ $description }}</p>
  </div>
  <!-- Section Title End -->

  <div class="bb ze i va ki xn br">
    <div class="tc uf sn tn xf un gg">
      <div class="animate_top me/5 ln rj">
        <h2 class="gk vj zp or kk wm hc">{{ number_format($kantorCabangVal) }}</h2>
        <p class="ek bk aq">{{ $kantorCabangText }}</p>
      </div>
      <div class="animate_top me/5 ln rj">
        <h2 class="gk vj zp or kk wm hc">{{ number_format($donaturVal) }}</h2>
        <p class="ek bk aq">{{ $donaturText }}</p>
      </div>
      <div class="animate_top me/5 ln rj">
        <h2 class="gk vj zp or kk wm hc">{{ number_format($fundraiserVal) }}</h2>
        <p class="ek bk aq">{{ $fundraiserText }}</p>
      </div>
      <div class="animate_top me/5 ln rj">
        <h2 class="gk vj zp or kk wm hc">{{ number_format($penggalanganDanaVal) }}</h2>
        <p class="ek bk aq">{{ $penggalanganDanaText }}</p>
      </div>
    </div>
  </div>
</section>
<!-- ===== Counter End ===== -->
