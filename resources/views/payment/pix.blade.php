@extends('layouts.public')

@section('content')
<div class="container py-5 text-center">
    <div style="max-width:500px;margin:0 auto;background:#142C4C;border-radius:0.75rem;padding:2.5rem;border:1px solid rgba(255,255,255,0.06);">
        <div style="font-size:3rem;color:#D4A017;margin-bottom:1rem;">📱</div>
        <h4 style="color:#fff;font-weight:700;">Pague com PIX</h4>
        <p style="color:#E6E6E6;opacity:0.8;">Escaneie o QR Code ou copie o código abaixo</p>

        @if($qr_code_base64)
        <div style="background:#fff;border-radius:0.5rem;padding:1rem;display:inline-block;margin:1rem 0;">
            <img src="data:image/png;base64,{{ $qr_code_base64 }}" style="width:200px;height:200px;">
        </div>
        @endif

        @if($qr_code)
        <div class="mb-3">
            <input type="text" value="{{ $qr_code }}" id="pixCode" class="form-control" readonly style="background:#0B1D33;border:1px solid rgba(255,255,255,0.08);color:#fff;text-align:center;font-size:0.8rem;padding:0.75rem;">
            <button class="btn btn-sm btn-outline-light mt-2" onclick="copyPix()">Copiar código</button>
        </div>
        @endif

        <p style="color:#E6E6E6;opacity:0.6;font-size:0.85rem;">O pagamento será confirmado automaticamente.</p>
        <a href="{{ route('payment.success', $payment->id) }}" class="btn btn-primary" style="background:#D4A017;border:none;color:#0B1D33;font-weight:700;">Já paguei</a>
    </div>
</div>
<script>
function copyPix() {
    const input = document.getElementById('pixCode');
    input.select();
    document.execCommand('copy');
    alert('Código PIX copiado!');
}
</script>
@endsection
