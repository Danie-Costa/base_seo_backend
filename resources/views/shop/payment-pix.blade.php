@extends('layouts.public')

@section('content')
<style>
body { background:#0B1D33; }
.pix-card { background:#142C4C; border:1px solid rgba(255,255,255,0.06); border-radius:0.75rem; padding:2.5rem; max-width:500px; margin:0 auto; text-align:center; }
.pix-card h4 { color:#fff; font-weight:700; }
.pix-card .qr-wrap { background:#fff; border-radius:0.5rem; padding:1rem; display:inline-block; margin:1.5rem 0; }
.pix-card .qr-wrap img { width:220px; height:220px; }
.pix-card .pix-code { background:#0B1D33; border:1px solid rgba(255,255,255,0.08); color:#fff; border-radius:0.5rem; padding:0.75rem; font-size:0.8rem; text-align:center; word-break:break-all; }
.pix-card .btn-primary { background:#D4A017; border:none; color:#0B1D33; font-weight:700; border-radius:0.5rem; padding:0.75rem 2rem; }
.pix-card .btn-primary:hover { background:#e3b239; }
.pix-card .btn-outline-light { border-color:rgba(255,255,255,0.12); color:#E6E6E6; font-size:0.85rem; }
</style>

<div class="container py-5">
    <div class="pix-card">
        <div style="font-size:2.5rem;margin-bottom:0.5rem;">📱</div>
        <h4>Pague com PIX</h4>
        <p style="color:#E6E6E6;opacity:0.8;font-size:0.9rem;">Escaneie o QR Code ou copie o código abaixo</p>

        @if($qr_code_base64)
        <div class="qr-wrap">
            <img src="data:image/png;base64,{{ $qr_code_base64 }}" alt="QR Code PIX">
        </div>
        @endif

        @if($qr_code)
        <div class="mb-3">
            <input type="text" value="{{ $qr_code }}" id="pixCode" class="pix-code w-100" readonly>
            <button class="btn btn-outline-light btn-sm mt-2" onclick="copyPix()">Copiar código</button>
        </div>
        @endif

        <p style="color:#E6E6E6;opacity:0.5;font-size:0.8rem;">O pagamento será confirmado automaticamente.</p>
        <a href="{{ route('shop.products') }}" class="btn btn-primary mt-2">Continuar comprando</a>
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
