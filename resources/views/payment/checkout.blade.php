@extends('layouts.public')

@section('content')
<style>
body { background:#0B1D33; }
.payment-card { background:#142C4C; border:1px solid rgba(255,255,255,0.06); border-radius:0.75rem; padding:2rem; }
.payment-card h4 { color:#fff; }
.payment-card p { color:#E6E6E6; opacity:0.8; }
.payment-card .form-control { background:#0B1D33; border:1px solid rgba(255,255,255,0.08); color:#fff; border-radius:0.5rem; padding:0.75rem 1rem; }
.payment-card .form-control:focus { border-color:#D4A017; box-shadow:0 0 0 3px rgba(212,160,23,0.08); }
.payment-card .form-control::placeholder { color:#E6E6E6; opacity:0.4; }
.payment-card .btn-primary { background:#D4A017; border:none; color:#0B1D33; font-weight:700; padding:0.75rem; border-radius:0.5rem; width:100%; }
.payment-card .btn-primary:hover { background:#e3b239; }
.payment-card .btn-primary:disabled { opacity:0.5; }
.payment-card .btn-outline-light { border-color:rgba(255,255,255,0.12); color:#E6E6E6; }
.payment-card .btn-outline-light:hover { border-color:#D4A017; color:#D4A017; }
.payment-card .btn-outline-light.active { border-color:#D4A017; background:rgba(212,160,23,0.08); color:#D4A017; }
.alert-danger { background:rgba(198,69,69,0.1); border:1px solid rgba(198,69,69,0.2); color:#C64545; }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">

            @if(session('error'))
            <div class="alert alert-danger mb-3">{{ session('error') }}</div>
            @endif

            <div class="payment-card">
                <h4 class="mb-1">{{ $payment->title }}</h4>
                <p class="mb-4">Valor: <strong style="color:#D4A017;font-size:1.3rem;">R$ {{ number_format($payment->price, 2, ',', '.') }}</strong></p>

                <div class="d-flex gap-2 mb-4">
                    <button class="btn btn-outline-light btn-sm flex-fill active" onclick="showForm('pix')" id="tabPix">PIX</button>
                    <button class="btn btn-outline-light btn-sm flex-fill" onclick="showForm('card')" id="tabCard">Cartão</button>
                    <button class="btn btn-outline-light btn-sm flex-fill" onclick="showForm('boleto')" id="tabBoleto">Boleto</button>
                </div>

                {{-- PIX --}}
                <div id="formPix">
                    {{ Form::open(['route' => ['payment.pix', $payment->id]]) }}
                    <input type="text" name="name" class="form-control mb-2" placeholder="Nome completo" value="{{ old('name') }}" required>
                    <input type="email" name="email" class="form-control mb-2" placeholder="Email" value="{{ old('email') }}" required>
                    <input type="text" name="cpf" class="form-control mb-3" placeholder="CPF" value="{{ old('cpf') }}" required>
                    <button type="submit" class="btn btn-primary">Pagar com PIX</button>
                    {{ Form::close() }}
                </div>

                {{-- Cartão --}}
                <div id="formCard" style="display:none">
                    <form id="cardForm" method="POST" action="{{ route('payment.card', $payment->id) }}">
                        @csrf
                        <input type="hidden" name="token" id="cardToken">
                        <input type="hidden" name="installments" id="installmentsInput" value="1">

                        <div id="cardError" class="alert-danger" style="display:none;padding:0.5rem 1rem;border-radius:0.5rem;margin-bottom:0.75rem;font-size:0.85rem;"></div>

                        <input type="text" name="name" class="form-control mb-2" placeholder="Nome do titular" required>
                        <input type="text" id="cardNumber" class="form-control mb-2" placeholder="Número do cartão" required>
                        <div class="row g-2 mb-2">
                            <div class="col-6"><input type="text" id="cardExpiry" class="form-control" placeholder="MM/AA" required></div>
                            <div class="col-6"><input type="text" id="cardCvv" class="form-control" placeholder="CVV" required></div>
                        </div>
                        <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
                        <input type="text" name="cpf" class="form-control mb-2" placeholder="CPF do titular" required>
                        <select id="installmentsSelect" class="form-control mb-3">
                            <option value="1">1x de R$ {{ number_format($payment->price, 2, ',', '.') }}</option>
                            <option value="2">2x de R$ {{ number_format($payment->price/2, 2, ',', '.') }}</option>
                            <option value="3">3x de R$ {{ number_format($payment->price/3, 2, ',', '.') }}</option>
                        </select>
                        <button type="submit" class="btn btn-primary" id="cardSubmit">Pagar com Cartão</button>
                    </form>
                </div>

                {{-- Boleto --}}
                <div id="formBoleto" style="display:none">
                    {{ Form::open(['route' => ['payment.boleto', $payment->id]]) }}
                    <input type="text" name="name" class="form-control mb-2" placeholder="Nome completo" required>
                    <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
                    <input type="text" name="cpf" class="form-control mb-3" placeholder="CPF" required>
                    <button type="submit" class="btn btn-primary">Gerar Boleto</button>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://sdk.mercadopago.com/js/v2"></script>
<script>
const mp = new MercadoPago('{{ config("services.mercadopago.public_key") }}');

function showForm(type) {
    ['formPix','formCard','formBoleto'].forEach(id => document.getElementById(id).style.display = 'none');
    ['tabPix','tabCard','tabBoleto'].forEach(id => document.getElementById(id).classList.remove('active'));
    document.getElementById('form'+type.charAt(0).toUpperCase()+type.slice(1)).style.display = 'block';
    document.getElementById('tab'+type.charAt(0).toUpperCase()+type.slice(1)).classList.add('active');
}

// Installments
document.getElementById('installmentsSelect').addEventListener('change', function() {
    document.getElementById('installmentsInput').value = this.value;
});

// Card tokenization
document.getElementById('cardForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const btn = document.getElementById('cardSubmit');
    const errDiv = document.getElementById('cardError');
    btn.disabled = true;
    btn.textContent = 'Processando...';
    errDiv.style.display = 'none';

    const [expMonth, expYear] = document.getElementById('cardExpiry').value.split('/');

    mp.createCardToken({
        cardNumber: document.getElementById('cardNumber').value.replace(/\s/g,''),
        cardholderName: document.querySelector('#cardForm input[name="name"]').value,
        cardExpirationMonth: expMonth,
        cardExpirationYear: '20' + expYear,
        securityCode: document.getElementById('cardCvv').value,
        identificationType: 'CPF',
        identificationNumber: document.querySelector('#cardForm input[name="cpf"]').value,
    }).then(function(cardToken) {
        document.getElementById('cardToken').value = cardToken.id;
        document.getElementById('cardForm').submit();
    }).catch(function(error) {
        errDiv.textContent = error.error || 'Erro ao processar cartão. Verifique os dados.';
        errDiv.style.display = 'block';
        btn.disabled = false;
        btn.textContent = 'Pagar com Cartão';
    });
});
</script>
@endsection
