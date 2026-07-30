@extends('layouts.public')

@section('content')
<style>
body { background:#0B1D33; }
.checkout-card { background:#142C4C; border:1px solid rgba(255,255,255,0.06); border-radius:0.75rem; padding:2rem; }
.checkout-card h4 { color:#fff; }
.checkout-card .form-control { background:#0B1D33; border:1px solid rgba(255,255,255,0.08); color:#fff; border-radius:0.5rem; padding:0.75rem 1rem; }
.checkout-card .form-control:focus { border-color:#D4A017; box-shadow:0 0 0 3px rgba(212,160,23,0.08); }
.checkout-card .form-control::placeholder { color:#E6E6E6; opacity:0.4; }
.checkout-card select.form-control { color:#fff; }
.checkout-card select.form-control option { background:#0B1D33; color:#fff; }
.checkout-card .btn-primary { background:#D4A017; border:none; color:#0B1D33; font-weight:700; padding:0.75rem; border-radius:0.5rem; width:100%; }
.checkout-card .btn-primary:hover { background:#e3b239; }
.checkout-card .btn-primary:disabled { opacity:0.5; }
.checkout-card .btn-outline-light { border-color:rgba(255,255,255,0.12); color:#E6E6E6; }
.checkout-card .btn-outline-light:hover { border-color:#D4A017; color:#D4A017; }
.checkout-card .btn-outline-light.active { border-color:#D4A017; background:rgba(212,160,23,0.08); color:#D4A017; }
.method-card { background:#0B1D33; border-radius:0.5rem; padding:0.75rem 1rem; cursor:pointer; border:1px solid rgba(255,255,255,0.06); transition:all .2s; }
.method-card:hover { border-color:#D4A017; }
.method-card.selected { border-color:#D4A017; background:rgba(212,160,23,0.06); }
.method-card .method-name { color:#fff; font-weight:600; font-size:0.9rem; }
.method-card .method-discount { color:#1E9E63; font-size:0.8rem; }
.method-card .method-final { color:#D4A017; font-weight:700; }
.item-row { border-bottom:1px solid rgba(255,255,255,0.06); padding:0.5rem 0; display:flex; align-items:center; gap:0.75rem; font-size:0.9rem; }
.item-row:last-child { border-bottom:none; }
.item-row .item-img { width:40px; height:40px; object-fit:cover; border-radius:0.3rem; background:#0B1D33; }
.item-row .item-name { color:#fff; flex-grow:1; }
.item-row .item-qty { color:#E6E6E6; opacity:0.6; }
.item-row .item-price { color:#D4A017; font-weight:600; }
.summary-row { display:flex; justify-content:space-between; padding:0.25rem 0; color:#E6E6E6; font-size:0.9rem; }
.summary-row.total { border-top:1px solid rgba(255,255,255,0.1); margin-top:0.5rem; padding-top:0.75rem; font-weight:700; color:#fff; font-size:1.1rem; }
.summary-row .value { color:#D4A017; }
.summary-row.discount .value { color:#1E9E63; }
.alert-danger { background:rgba(198,69,69,0.1); border:1px solid rgba(198,69,69,0.2); color:#C64545; padding:0.75rem; border-radius:0.5rem; margin-bottom:1rem; font-size:0.85rem; }
</style>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0" style="color:#fff;">Finalizar Pedido</h4>
        <a href="{{ route('shop.cart') }}" style="color:#D4A017;text-decoration:none;font-size:0.85rem;">
            <i class="fa fa-arrow-left"></i> Voltar ao carrinho
        </a>
    </div>

    @if(session('error'))
    <div class="alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row g-4">
        {{-- Itens do carrinho --}}
        <div class="col-lg-7">
            <div class="checkout-card">
                <h4 class="mb-3" style="font-size:1.1rem;">Itens</h4>
                @foreach($cart->items as $item)
                <div class="item-row">
                    @php $img = $item->product->images->first(); @endphp
                    <img src="{{ $img ? asset('storage/' . $img->path) : 'https://via.placeholder.com/40?text=+' }}" class="item-img">
                    <span class="item-name">{{ $item->product->name }}</span>
                    <span class="item-qty">x{{ $item->quantity }}</span>
                    <span class="item-price">R$ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}</span>
                </div>
                @endforeach

                <hr style="border-color:rgba(255,255,255,0.08);">

                <h4 class="mb-3 mt-4" style="font-size:1.1rem;">Forma de Pagamento</h4>

                <div class="d-flex flex-column gap-2 mb-4" id="methodOptions">
                    @foreach($methods as $key => $m)
                    @if($m['active'])
                    <div class="method-card d-flex justify-content-between align-items-center"
                         onclick="selectMethod('{{ $key }}')" data-method="{{ $key }}">
                        <div>
                            <div class="method-name">{{ $m['label'] }}</div>
                            @if($m['discount_percent'] > 0)
                            <div class="method-discount">{{ $m['discount_percent'] }}% de desconto</div>
                            @endif
                        </div>
                        <div class="text-end">
                            @if($m['discount'] > 0)
                            <div style="color:#E6E6E6;opacity:0.5;font-size:0.75rem;text-decoration:line-through;">
                                R$ {{ number_format($total, 2, ',', '.') }}
                            </div>
                            @endif
                            <div class="method-final">R$ {{ number_format($m['final'], 2, ',', '.') }}</div>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>

                {{-- Form PIX --}}
                <div id="formPix" style="display:none;">
                    <p style="color:#E6E6E6;opacity:0.7;font-size:0.85rem;">Você será redirecionado para pagamento via PIX.</p>
                </div>

                {{-- Form Cartão --}}
                <div id="formCard" style="display:none;">
                    <div id="cardError" class="alert-danger" style="display:none;"></div>
                    <div class="row g-2">
                        <div class="col-12">
                            <input type="text" id="cardholderName" class="form-control" placeholder="Nome do titular" required>
                        </div>
                        <div class="col-12">
                            <input type="text" id="cardNumber" class="form-control" placeholder="Número do cartão" required>
                        </div>
                        <div class="col-6">
                            <input type="text" id="cardExpiry" class="form-control" placeholder="MM/AA" required>
                        </div>
                        <div class="col-6">
                            <input type="text" id="cardCvv" class="form-control" placeholder="CVV" required>
                        </div>
                        <div class="col-12">
                            <select id="installmentsSelect" class="form-control">
                                @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}">{{ $i }}x de R$ {{ number_format($selectedMethod['final'] ?? $total / $i, 2, ',', '.') }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>

                <button id="payButton" class="btn btn-primary mt-3" disabled onclick="processPayment()">
                    Pagar
                </button>
            </div>
        </div>

        {{-- Resumo --}}
        <div class="col-lg-5">
            <div class="checkout-card">
                <h4 class="mb-3" style="font-size:1.1rem;">Resumo</h4>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>R$ {{ number_format($total, 2, ',', '.') }}</span>
                </div>
                <div class="summary-row discount" id="discountRow" style="display:none;">
                    <span>Desconto (<span id="discountLabel">PIX</span>)</span>
                    <span class="value">- R$ <span id="discountValue">0,00</span></span>
                </div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span class="value">R$ <span id="totalValue">{{ number_format($total, 2, ',', '.') }}</span></span>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="paymentForm" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="payment_method" id="inputMethod">
    <input type="hidden" name="token" id="inputToken">
    <input type="hidden" name="installments" id="inputInstallments" value="1">
    <input type="hidden" name="payment_method_id" id="inputPaymentMethodId">
</form>

<script src="https://sdk.mercadopago.com/js/v2"></script>
<script>
const mp = new MercadoPago('{{ config("services.mercadopago.public_key") }}');
const methods = @json($methods);
let selectedMethod = null;

function selectMethod(method) {
    selectedMethod = method;
    document.querySelectorAll('.method-card').forEach(c => c.classList.remove('selected'));
    document.querySelector(`[data-method="${method}"]`).classList.add('selected');

    document.getElementById('formPix').style.display = method === 'pix' ? 'block' : 'none';
    document.getElementById('formCard').style.display = (method === 'credit_card' || method === 'debit_card') ? 'block' : 'none';

    const m = methods[method];
    document.getElementById('payButton').disabled = false;
    document.getElementById('payButton').textContent = method === 'pix'
        ? 'Pagar com PIX'
        : 'Pagar com Cartão';

    // Atualiza resumo
    if (m.discount > 0) {
        document.getElementById('discountRow').style.display = 'flex';
        document.getElementById('discountLabel').textContent = m.label;
        document.getElementById('discountValue').textContent = m.discount.toFixed(2).replace('.', ',');
    } else {
        document.getElementById('discountRow').style.display = 'none';
    }
    document.getElementById('totalValue').textContent = m.final.toFixed(2).replace('.', ',');
    document.getElementById('inputMethod').value = method;
}

function processPayment() {
    const method = selectedMethod;
    if (!method) return;

    const form = document.getElementById('paymentForm');
    const btn = document.getElementById('payButton');
    btn.disabled = true;

    if (method === 'pix') {
        form.action = '{{ route("shop.payment.pay") }}';
        form.submit();
        return;
    }

    // Cartão — tokenizar
    const errDiv = document.getElementById('cardError');
    errDiv.style.display = 'none';

    const [expMonth, expYear] = document.getElementById('cardExpiry').value.split('/');

    mp.createCardToken({
        cardNumber: document.getElementById('cardNumber').value.replace(/\s/g, ''),
        cardholderName: document.getElementById('cardholderName').value,
        cardExpirationMonth: expMonth,
        cardExpirationYear: '20' + expYear,
        securityCode: document.getElementById('cardCvv').value,
        identificationType: 'CPF',
        identificationNumber: '{{ Auth::guard("client")->user()->cnpj }}',
    }).then(function(cardToken) {
        document.getElementById('inputToken').value = cardToken.id;
        document.getElementById('inputInstallments').value = document.getElementById('installmentsSelect').value;

        // Detecta bandeira
        const firstDigits = document.getElementById('cardNumber').value.replace(/\s/g, '').substring(0, 6);
        const brandMap = { '4': 'visa', '51':'master','52':'master','53':'master','54':'master','55':'master' };
        const brand = brandMap[firstDigits[0]] || 'master';
        document.getElementById('inputPaymentMethodId').value = brand;

        form.action = '{{ route("shop.payment.pay") }}';
        form.submit();
    }).catch(function(error) {
        errDiv.textContent = error.error || 'Erro ao processar cartão. Verifique os dados.';
        errDiv.style.display = 'block';
        btn.disabled = false;
    });
}
</script>
@endsection
