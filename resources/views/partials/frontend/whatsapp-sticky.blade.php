@php
  $lp = $landingProfile ?? null;
  $waPhone = $lp && !empty($lp->hero_whatsapp_active) && !empty($lp->hero_whatsapp_number) 
    ? $lp->hero_whatsapp_number 
    : ($lp && !empty($lp->phone_number) ? $lp->phone_number : '+62 895-6210-93500');
  $waPhoneStr = is_array($waPhone) ? implode('', array_filter($waPhone)) : $waPhone;
  $waPhoneDigits = preg_replace('/\D+/', '', $waPhoneStr);
  $waUrl = $waPhoneDigits ? 'https://wa.me/'.$waPhoneDigits : 'https://wa.me/62895621093500';
@endphp

<!-- WhatsApp Sticky Button -->
<a 
  href="{{ $waUrl }}" 
  target="_blank" 
  rel="noopener noreferrer"
  class="whatsapp-sticky"
  aria-label="Chat via WhatsApp"
  title="Chat dengan kami di WhatsApp"
>
  <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none">
    <path d="M20.52 3.48A11.89 11.89 0 0012.02.12C6.08.12 1.19 4.99 1.19 10.93c0 1.93.5 3.82 1.44 5.5L.12 23.88l7.61-2.01a11.8 11.8 0 005.29 1.21h.01c5.94 0 10.83-4.89 10.83-10.83 0-3-1.17-5.83-3.65-7.57z" fill="#25D366" />
    <path d="M17.2 14.54c-.3-.15-1.76-.86-2.03-.96-.27-.1-.47-.15-.67.15-.2.3-.76.96-.93 1.16-.17.2-.35.22-.65.07-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.48-1.74-1.65-2.04-.17-.3-.02-.46.13-.61.13-.13.3-.36.45-.54.15-.18.2-.3.3-.5.1-.2 0-.37-.02-.52-.02-.15-.67-1.6-.92-2.19-.24-.57-.49-.49-.67-.5l-.57-.01c-.2 0-.52.07-.79.37-.27.3-1.03 1.01-1.03 2.46 0 1.45 1.05 2.85 1.2 3.05.15.2 2.08 3.17 5.04 4.44 2.96 1.27 2.96.85 3.49.8.53-.05 1.73-.7 1.98-1.37.25-.66.25-1.22.17-1.37-.07-.15-.27-.24-.57-.39z" fill="#FFF" />
  </svg>
</a>

<style>
  .whatsapp-sticky {
    position: fixed;
    bottom: 24px;
    right: 24px;
    width: 60px;
    height: 60px;
    background: #25D366;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);
    z-index: 9999;
    transition: all 0.3s ease;
    cursor: pointer;
  }

  .whatsapp-sticky:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(37, 211, 102, 0.6);
  }

  .whatsapp-sticky:active {
    transform: scale(0.95);
  }

  /* Pulse animation */
  @keyframes pulse {
    0% {
      box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);
    }
    50% {
      box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4), 0 0 0 10px rgba(37, 211, 102, 0.1);
    }
    100% {
      box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);
    }
  }

  .whatsapp-sticky {
    animation: pulse 2s infinite;
  }

  /* Mobile responsive */
  @media (max-width: 768px) {
    .whatsapp-sticky {
      width: 56px;
      height: 56px;
      bottom: 20px;
      right: 20px;
    }
    
    .whatsapp-sticky svg {
      width: 28px;
      height: 28px;
    }
  }
</style>
