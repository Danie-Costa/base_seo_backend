# Mercado Pago Checkout Transparente (via API Orders) — Plano de Implementação

> **Branch:** `pagamento`
> **SDK:** `mercadopago/dx-php` v3 — já possui `OrderClient` nativo

**Objetivo:** Migrar da API de Pagamentos (PaymentClient) para a **API Orders** do Checkout Transparente. Tudo no site do cliente (sem redirect), com PIX, cartão crédito/débito e assinatura recorrente mensal.

---

## Como a API Orders funciona

Diferente da implementação atual (que chama `PaymentClient` separado para cada método), a API Orders cria **uma única Order** com múltiplas transações:

```
POST /v1/orders
{
  "type": "online",
  "processing_mode": "automatic",
  "external_reference": "pedido_123",
  "total_amount": "150.00",
  "payer": { "email": "cliente@email.com" },
  "transactions": {
    "payments": [
      {
        "amount": "150.00",
        "payment_method": {
          "type": "pix" | "credit_card" | "debit_card",
          "token": "card_token_123",       // só para cartão
          "installments": 1,               // só para cartão
          "id": "master"                    // só para cartão
        }
      }
    ]
  }
}
```

- **PIX**: Não precisa de token. Resposta já vem com QR code.
- **Cartão**: Precisa de token gerado pelo frontend (MP SDK js v2) + `id` da bandeira.
- **Notificações**: Configuradas no painel do MP (não via `notification_url` no código).
- **Descontos**: Suporta `discounts` por método de pagamento.

---

## ETAPAS DE IMPLEMENTAÇÃO (10 tasks)

---

### Task 1: Schema — add `product_id` à `cart_items` + Models Cart/CartItem

**Arquivos:**
- Criar: `database/migrations/2024_02_10_000041_add_product_id_to_cart_items_table.php`
- Criar: `app/Models/Cart.php`
- Criar: `app/Models/CartItem.php`

**Migration:**
```php
Schema::table('cart_items', function (Blueprint $table) {
    $table->foreignId('product_id')->after('cart_id')->constrained()->onDelete('cascade');
});
```

**Cart.php:**
```php
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['project_id', 'client_id', 'external_reference'];

    public function items() { return $this->hasMany(CartItem::class); }
    public function client() { return $this->belongsTo(Client::class); }
    public function order() { return $this->hasOne(Order::class); }
}
```

**CartItem.php:**
```php
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['cart_id', 'product_id', 'quantity', 'price'];
    public function cart() { return $this->belongsTo(Cart::class); }
    public function product() { return $this->belongsTo(Product::class); }
}
```

**Verificar:** `php artisan migrate` sem erros. Rodar e testar.

---

### Task 2: Client autenticável — guard `client` com login/register

**Arquivos:**
- Criar: `database/migrations/2024_02_11_000042_add_auth_fields_to_clients_table.php`
- Criar: `app/Http/Controllers/Auth/ClientAuthController.php`
- Criar: `resources/views/auth/client/login.blade.php`
- Criar: `resources/views/auth/client/register.blade.php`
- Modificar: `app/Models/Client.php` (estender Authenticatable)
- Modificar: `config/auth.php` (guard + provider `clients`)
- Modificar: `routes/web.php`

**Migration:**
```php
Schema::table('clients', function (Blueprint $table) {
    $table->string('password')->after('email')->nullable();
    $table->rememberToken();
    $table->dropUnique(['email']);
    $table->unique(['company_id', 'email']);
});
```

**Client.php:** Deve usar:
```php
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Client extends Authenticatable
{
    use Notifiable;
    protected $guard = 'client';
    protected $fillable = ['company_id', 'project_id', 'name', 'email', 'cnpj', 'password', 'external_reference'];
    protected $hidden = ['password', 'remember_token'];
```

**config/auth.php:**
```php
'guards' => [
    'client' => ['driver' => 'session', 'provider' => 'clients'],
],
'providers' => [
    'clients' => ['driver' => 'eloquent', 'model' => App\Models\Client::class],
],
```

**Rotas:**
```php
Route::prefix('cliente')->name('client.')->group(function () {
    Route::get('/login', [ClientAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [ClientAuthController::class, 'login']);
    Route::get('/register', [ClientAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [ClientAuthController::class, 'register']);
    Route::post('/logout', [ClientAuthController::class, 'logout'])->name('logout');
});
```

---

### Task 3: Página pública de produtos + Catálogo

**Arquivos:**
- Criar: `app/Http/Controllers/ShopController.php`
- Criar: `resources/views/shop/products.blade.php`
- Criar: `resources/views/shop/product-detail.blade.php`
- Modificar: `routes/web.php`

**ShopController:**
```php
class ShopController extends Controller
{
    // Precisa saber qual empresa (subdomain ou hardcoded para single-tenant)
    public function products() {
        $products = Product::where('active', true)->with('images', 'categories')->get();
        return view('shop.products', compact('products'));
    }

    public function detail($slug) {
        $product = Product::where('slug', $slug)->where('active', true)->with('images', 'categories')->firstOrFail();
        return view('shop.product-detail', compact('product'));
    }
}
```

**Botão "Comprar"** aparece só se `Auth::guard('client')->check()`. Senão, mostra "Faça login para comprar".

---

### Task 4: Carrinho de compras (frontend + backend)

**Arquivos:**
- Criar: `app/Http/Controllers/CartController.php`
- Criar: `resources/views/shop/cart.blade.php`
- Modificar: `routes/web.php`

**CartController** — middleware `auth:client` em todo o controller:
- `index()` — mostra carrinho com itens
- `add(Product $product)` — adiciona ao carrinho (cria se não existir)
- `remove(CartItem $item)` — remove item
- `getOrCreateCart()` — helper que pega ou cria cart para o client logado

---

### Task 5: Checkout — seleção de método + desconto

**Arquivos:**
- Criar: `app/Http/Controllers/CheckoutController.php`
- Criar: `resources/views/shop/checkout.blade.php`
- Criar: `database/migrations/2024_02_12_000043_create_payment_configs_table.php`
- Criar: `app/Models/PaymentConfig.php`
- Criar: `app/Http/Controllers/Company/PaymentConfigController.php`
- Criar: `resources/views/company/payment-config/index.blade.php`
- Modificar: `config/permissions.php`, `routes/web.php`

**PaymentConfig** — desconto por método de pagamento:
```php
Schema::create('payment_configs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained()->onDelete('cascade');
    $table->enum('method', ['pix', 'credit_card', 'debit_card']);
    $table->decimal('discount_percent', 5, 2)->default(0);
    $table->boolean('active')->default(true);
    $table->timestamps();
    $table->unique(['company_id', 'method']);
});
```

**CheckoutController:**
- `index()` — mostra resumo do carrinho + seleção de método + descontos
- `pay(Request $request)` — processa o pagamento (Task 6)

---

### Task 6: [CORE] OrderService — integração com API Orders

**Arquivos:**
- Criar: `app/Services/OrderService.php`
- Modificar: `app/Http/Controllers/CheckoutController.php`
- Modificar: `app/Models/Order.php`
- Modificar: `app/Models/Payment.php`
- Criar: `database/migrations/2024_02_13_000044_add_order_fields.php`

**OrderService.php** — usa o `OrderClient` do SDK:
```php
<?php
namespace App\Services;

use MercadoPago\Client\Order\OrderClient;
use MercadoPago\MercadoPagoConfig;
use App\Models\Cart;
use App\Models\PaymentConfig;
use App\Models\Order as OrderModel;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class OrderService
{
    private OrderClient $client;

    public function __construct()
    {
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
        $this->client = new OrderClient();
    }

    /**
     * Cria Order no MP, Payment + OrderModel no DB.
     *
     * @param Cart $cart Carrinho com items carregados
     * @param string $paymentMethod 'pix' | 'credit_card' | 'debit_card'
     * @param array $cardData ['token' => '...', 'installments' => 1, 'payment_method_id' => 'master'] (opcional para cartão)
     * @param float $discount Valor do desconto
     */
    public function createOrder(Cart $cart, string $paymentMethod, array $cardData = [], float $discount = 0): array
    {
        $client = Auth::guard('client')->user();
        $total = $cart->items->sum(fn($i) => $i->price * $i->quantity);
        $finalAmount = $total - $discount;

        // Monta transação
        $payment = [
            'amount' => (string) number_format($finalAmount, 2, '.', ''),
            'payment_method' => [],
        ];

        if (in_array($paymentMethod, ['credit_card', 'debit_card'])) {
            $payment['payment_method'] = [
                'id' => $cardData['payment_method_id'],
                'type' => $paymentMethod,
                'token' => $cardData['token'],
                'installments' => (int) ($cardData['installments'] ?? 1),
            ];
        } else {
            // PIX — sem token
            $payment['payment_method'] = [
                'type' => 'pix',
            ];
        }

        $orderData = [
            'type' => 'online',
            'processing_mode' => 'automatic',
            'external_reference' => (string) Str::uuid(),
            'total_amount' => (string) number_format($finalAmount, 2, '.', ''),
            'payer' => [
                'email' => $client->email,
            ],
            'transactions' => [
                'payments' => [$payment],
            ],
            'items' => $cart->items->map(fn($i) => [
                'title' => $i->product->name,
                'quantity' => $i->quantity,
                'unit_price' => $i->price,
            ])->toArray(),
        ];

        // Desconto
        if ($discount > 0) {
            $orderData['discounts'] = [
                ['value' => $discount, 'description' => "Desconto {$paymentMethod}"],
            ];
        }

        try {
            Log::info('MP Order create', ['payload' => $orderData]);
            $mpOrder = $this->client->create($orderData);
            Log::info('MP Order created', ['id' => $mpOrder->id, 'status' => $mpOrder->status]);

            // Salva no DB
            $orderModel = OrderModel::create([
                'cart_id' => $cart->id,
                'project_id' => $cart->project_id,
                'external_reference' => $orderData['external_reference'],
                'total' => $total,
                'total_discount' => $discount,
                'status' => $this->normalizeOrderStatus($mpOrder->status),
            ]);

            // Pega dados do primeiro paymentTransaction
            $tx = $mpOrder->transactions->payments[0] ?? null;

            Payment::create([
                'order_id' => $orderModel->id,
                'project_id' => $cart->project_id,
                'title' => 'Pedido #' . $orderModel->id,
                'price' => $finalAmount,
                'fee' => 0,
                'price_fee' => $finalAmount,
                'status' => $this->normalizePaymentStatus($mpOrder->status),
                'return_type' => 'webhook',
                'external_reference' => $orderData['external_reference'],
                'payment_id' => $tx?->id,
                'payment_type' => $paymentMethod,
                'payment_method_id' => $tx?->payment_method->id ?? null,
                'qr_code' => $mpOrder->type_response?->qr_code ?? null,
                'qr_code_base64' => $mpOrder->type_response?->qr_code_base64 ?? null,
            ]);

            return [
                'mp_order_id' => $mpOrder->id,
                'status' => $mpOrder->status,
                'status_detail' => $mpOrder->status_detail,
                'qr_code' => $mpOrder->type_response?->qr_code ?? null,
                'qr_code_base64' => $mpOrder->type_response?->qr_code_base64 ?? null,
                'payment_id' => $tx?->id,
            ];

        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            $error = $e->getApiResponse()->getContent();
            Log::error('MP Order error', ['error' => $error]);
            throw new \RuntimeException('Erro MP: ' . ($error['message'] ?? 'erro'));
        }
    }

    public function getOrder(string $mpOrderId): \MercadoPago\Resources\Order
    {
        return $this->client->get($mpOrderId);
    }

    private function normalizeOrderStatus(?string $status): string
    {
        return match ($status) {
            'processed', 'accredited' => 'paid',
            'opened' => 'pending',
            default => 'canceled',
        };
    }

    private function normalizePaymentStatus(?string $status): string
    {
        return match ($status) {
            'processed', 'accredited' => 'approved',
            'opened' => 'pending',
            default => 'failure',
        };
    }
}
```

**Fluxo do CheckoutController@pay:**
1. Pega carrinho do cliente logado
2. Calcula desconto baseado no PaymentConfig + método escolhido
3. Chama `OrderService::createOrder()`
4. Se PIX: renderiza view com QR code (task 7)
5. Se cartão: redireciona para success/failure baseado no status

---

### Task 7: Views de pagamento (PIX + Cartão + Resultado)

**Arquivos:**
- Criar: `resources/views/shop/payment-pix.blade.php`
- Criar: `resources/views/shop/payment-card.blade.php`
- Criar: `resources/views/shop/payment-success.blade.php`
- Criar: `resources/views/shop/payment-failure.blade.php`
- Criar: `resources/views/shop/payment-pending.blade.php`
- Modificar: `resources/views/shop/checkout.blade.php`

**checkout.blade.php:**
- Tabs: PIX / Cartão de Crédito / Cartão de Débito
- Mostra desconto aplicado dinamicamente
- Form cartão: fields + integração MP SDK js v2 para tokenize
- PIX: só nome + email + CPF

**payment-pix.blade.php:**
- QR code (base64) + código copia-e-cola
- Botão "Já paguei" + espera webhook

**payment-success.blade.php:**
- Confirmação + resumo do pedido

---

### Task 8: Webhook — notificações de Orders

**Arquivos:**
- Modificar: `app/Http/Controllers/WebhookController.php`
- Modificar: `app/Services/OrderService.php` (add método `processWebhook`)

**Notificações:** Na API Orders, as notificações são configuradas no painel do Mercado Pago (Suas Integrações → Webhooks), não via `notification_url` no código. O webhook envia:

```json
{
  "type": "order",
  "action": "payment.created",
  "data": { "id": "order_mp_id" }
}
```

**WebhookController:**
```php
public function mercadopago(Request $request)
{
    $type = $request->input('type');
    $resourceId = $request->input('data.id');

    if ($type !== 'order' || !$resourceId) {
        return response()->json(['received' => true]);
    }

    $service = new OrderService();
    $mpOrder = $service->getOrder($resourceId);

    // Busca payment pelo external_reference
    $payment = Payment::where('external_reference', $mpOrder->external_reference)->first();
    if (!$payment) return response()->json(['received' => true]);

    // Atualiza status
    $paymentStatus = match ($mpOrder->status) {
        'processed', 'accredited' => 'approved',
        'opened' => 'pending',
        default => 'failure',
    };

    $payment->update([
        'status' => $paymentStatus,
        'payment_id' => $mpOrder->transactions->payments[0]?->id ?? $payment->payment_id,
    ]);

    if ($payment->order) {
        $orderStatus = $paymentStatus === 'approved' ? 'paid' : 'pending';
        $payment->order->update(['status' => $orderStatus]);
    }

    return response()->json(['received' => true]);
}
```

---

### Task 9: Assinatura recorrente mensal (planos)

**Arquivos:**
- Criar: `app/Services/SubscriptionService.php`
- Modificar: `app/Http/Controllers/Company/PlanController.php` (usar API Orders)
- Criar: `database/migrations/2024_02_14_000045_add_subscription_id_to_companies_table.php`

Para assinatura recorrente, a API Orders NÃO suporta subscriptions nativamente. Usamos o **Preapproval API** (já disponível no SDK como `PreapprovalClient`).

**SubscriptionService.php:**
```php
use MercadoPago\Client\Preapproval\PreapprovalClient;

class SubscriptionService
{
    public function createSubscription(Plan $plan, Client $client): array
    {
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
        $preapprovalClient = new PreapprovalClient();

        $preapproval = $preapprovalClient->create([
            'payer_email' => $client->email,
            'reason' => "Plano {$plan->name}",
            'external_reference' => (string) Str::uuid(),
            'auto_recurring' => [
                'frequency' => 1,
                'frequency_type' => 'months',
                'transaction_amount' => (float) $plan->price,
                'currency_id' => 'BRL',
            ],
            'back_url' => route('company.plans.success', $plan->id),
            'notification_url' => config('services.mercadopago.webhook_url'),
        ]);

        return [
            'preapproval_id' => $preapproval->id,
            'init_point' => $preapproval->init_point,
        ];
    }
}
```

---

### Task 10: Dashboard admin com analytics

**Arquivos:**
- Modificar: `app/Http/Controllers/DashboardController.php`
- Modificar: `resources/views/admin/dashboard.blade.php`
- Criar: `app/Services/SalesAnalyticsService.php`

**SalesAnalyticsService:**
```php
class SalesAnalyticsService
{
    public function volumeTotal(): float {
        return Payment::where('status', 'approved')->sum('price');
    }

    public function totalOrders(): int {
        return Order::count();
    }

    public function pendingPayments(): int {
        return Payment::where('status', 'pending')->count();
    }

    public function topProducts(int $limit = 5): Collection {
        return DB::table('cart_items')
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->join('carts', 'cart_items.cart_id', '=', 'carts.id')
            ->join('orders', 'carts.id', '=', 'orders.cart_id')
            ->where('orders.status', 'paid')
            ->select('products.name', DB::raw('SUM(cart_items.quantity) as total_qtd'),
                     DB::raw('SUM(cart_items.price * cart_items.quantity) as total_revenue'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qtd')
            ->limit($limit)
            ->get();
    }

    public function revenueByMonth(): Collection {
        return Payment::where('status', 'approved')
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                     DB::raw('SUM(price) as total'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }
}
```

**Dashboard admin** — adicionar cards: Total vendido, Pedidos, Pendentes, Top 5 produtos, Gráfico receita mensal.

---

## ORDEM DE EXECUÇÃO

| # | Task | Depende | Arquivos-chave |
|---|------|---------|---------------|
| 1 | Schema cart_items + Models | — | migration, Cart.php, CartItem.php |
| 2 | Client autenticável | — | Client.php, auth.php, ClientAuthController |
| 3 | Catálogo público | — | ShopController, shop/products.blade |
| 4 | Carrinho | 1, 2, 3 | CartController, cart.blade |
| 5 | Checkout + descontos | 4 | CheckoutController, PaymentConfig |
| 6 | **OrderService (API Orders)** | 5 | OrderService.php (CORE) |
| 7 | Views pagamento | 6 | payment-pix, payment-card, success |
| 8 | Webhook | 6 | WebhookController |
| 9 | Assinatura planos | 2 | SubscriptionService |
| 10 | Dashboard analytics | 8 | SalesAnalyticsService |

---

## DIFERENÇAS CRÍTICAS: O QUE VAI MUDAR NO CÓDIGO ATUAL

| Atual (PaymentClient) | Novo (OrderClient) |
|---|---|
| `PaymentService::createPix()` | `OrderService::createOrder()` com `type: "pix"` |
| `PaymentService::createCard()` | `OrderService::createOrder()` com `type: "credit_card"` + token |
| `PaymentClient` do SDK | `OrderClient` do SDK |
| `notification_url` no payload | Webhook configurado no painel MP |
| Pagamento = 1 chamada por método | 1 chamada = 1 Order com N transações |
| QR code via `point_of_interaction` | QR code via `type_response` |
| Status: `approved/pending/failure` | Status: `processed/opened/closed` |

---

## VERIFICAÇÃO FINAL

- [ ] `php artisan migrate` sem erros
- [ ] `/cliente/login` e `/cliente/register` funcionam
- [ ] `/produtos` mostra produtos com fotos
- [ ] Cliente logado adiciona ao carrinho
- [ ] Checkout com desconto PIX (5%) aparece correto
- [ ] PIX: QR code gerado na tela
- [ ] Cartão: tokenização frontend → Order API → status
- [ ] Webhook atualiza Payment + Order
- [ ] Plano contratado vira assinatura recorrente
- [ ] Dashboard admin mostra vendas, top produtos, pendentes
