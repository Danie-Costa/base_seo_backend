# Mercado Pago Checkout Pro — Plano de Implementação

> **Para executar:** Usar subagent-driven-development, uma task por vez.
> **Branch:** `pagamento`

**Objetivo:** Substituir a integração atual (Payment API direta) pelo **Mercado Pago Checkout Pro** (preferences), adicionar login de cliente, loja pública com carrinho, descontos por método de pagamento, assinaturas recorrentes e dashboard de vendas.

**Arquitetura Geral:**
- Checkout Pro usando `preference` (backend cria, frontend redireciona ou usa brick)
- Client vira autenticável (guard separado `client`)
- Carrinho → Ordem → Preferência MP → Webhook confirma → Página de sucesso
- Descontos configuráveis por método de pagamento (ex: PIX 5% off, cartão 0%)
- Admin vê no dashboard: volume de vendas, top produtos, ordens pendentes

**Stack:** Laravel 10, PHP 8.2, MySQL 8, Mercado Pago SDK (`mercadopago/dx-php`), Bootstrap 5

---

## ANÁLISE DO QUE JÁ EXISTE

### O que funciona (branch `pagamento`):
- `mercadopago/dx-php` v3 instalado no composer.json
- `PaymentService` com métodos `createPix`, `createCard`, `createBoleto`, `processWebhook`
- `PaymentController` com checkout, processPix, processCard, processBoleto, success, failure
- `WebhookController` que recebe notificações MP
- `Company/PlanController` que cria Payment para planos e ativa na Company
- `Company/OrderController` que lista Payments da empresa
- Models: `Payment`, `Order`, `Plan`, `Product`, `Client`, `Company`, `User`
- `Company` com campos de plano (plan_id, plan_status, plan_started_at, plan_expires_at, etc.)
- `Plan` com campo `interval` (monthly, semiannual, annual)
- Views: `payment/checkout`, `payment/pix`, `payment/success`, `payment/failure`, `payment/boleto`, `company/plans/*`, `company/orders/*`
- Rotas de admin/company CRUD para produtos, planos, clientes

### Problemas identificados:
1. **Usa Payment API direta**, NÃO Checkout Pro (preferences)
2. **cart_items SEM `product_id`** — não dá pra saber qual produto está no carrinho
3. **Nenhum Model Cart/CartItem** — models faltando
4. **Client não autenticável** — sem senha, sem login, sem guard
5. **Sem página pública de produtos** — só admin CRUD
6. **Sem carrinho frontend** — sem add-to-cart, sem minicart
7. **Sem desconto por método de pagamento**
8. **Sem assinatura recorrente** — pagamentos únicos só
9. **Dashboard admin só com contagens** — sem analytics de vendas
10. **Ordem (Order) não ligada ao fluxo de compra de produtos**

---

## ETAPAS DE IMPLEMENTAÇÃO

---

### Task 1: Correção do schema — adicionar `product_id` à `cart_items` + Models Cart/CartItem

**Objetivo:** Adicionar chave estrangeira `product_id` na tabela `cart_items` e criar os Models Eloquent que estão faltando.

**Arquivos:**
- Criar: `database/migrations/2024_02_10_000041_add_product_id_to_cart_items_table.php`
- Criar: `app/Models/Cart.php`
- Criar: `app/Models/CartItem.php`
- Modificar: `database/migrations/2024_01_13_000013_create_cart_items_table.php` — não mexer, usar migration nova

**Migration nova:**
```php
Schema::table('cart_items', function (Blueprint $table) {
    $table->foreignId('product_id')->after('cart_id')->constrained()->onDelete('cascade');
});
```

**Cart.php:**
```php
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
class CartItem extends Model
{
    protected $fillable = ['cart_id', 'product_id', 'quantity', 'price'];
    public function cart() { return $this->belongsTo(Cart::class); }
    public function product() { return $this->belongsTo(Product::class); }
}
```

**Verificar:** `php artisan migrate` + `php artisan db:show`

---

### Task 2: Client autenticável — adicionar senha e guard `client`

**Objetivo:** Cliente poder se registrar e logar com email+senha no site público.

**Arquivos:**
- Criar: `database/migrations/2024_02_11_000042_add_auth_fields_to_clients_table.php`
- Criar: `app/Http/Controllers/Auth/ClientAuthController.php`
- Criar: `resources/views/auth/client/login.blade.php`
- Criar: `resources/views/auth/client/register.blade.php`
- Modificar: `app/Models/Client.php`
- Modificar: `config/auth.php`
- Modificar: `routes/web.php`

**Migration:**
```php
Schema::table('clients', function (Blueprint $table) {
    $table->string('password')->after('email')->nullable();
    $table->rememberToken();
    $table->dropUnique(['email']);
    $table->unique(['company_id', 'email']); // email único por empresa
});
```

**Client.php — adicionar:**
- `use Illuminate\Foundation\Auth\User as Authenticatable` (trocar extends)
- `use Notifiable`
- `protected $hidden = ['password', 'remember_token']`

**config/auth.php — adicionar guard:**
```php
'guards' => [
    'client' => [
        'driver' => 'session',
        'provider' => 'clients',
    ],
],
'providers' => [
    'clients' => [
        'driver' => 'eloquent',
        'model' => App\Models\Client::class,
    ],
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

**Verificar:** Acessar `/cliente/login` e `/cliente/register` — fluxo completo de cadastro e login.

---

### Task 3: Página pública de produtos

**Objetivo:** Listar produtos ativos da empresa em página pública com botão "Comprar" (visível só para clientes logados).

**Arquivos:**
- Criar: `app/Http/Controllers/ShopController.php`
- Criar: `resources/views/shop/products.blade.php`
- Criar: `resources/views/shop/product-detail.blade.php`
- Modificar: `routes/web.php`

**ShopController:**
```php
class ShopController extends Controller
{
    public function products() {
        $company = (new CompanyService)->myCompany(); // ou da subdomain
        $products = Product::where('company_id', $company->id)->where('active', true)->with('images', 'categories')->get();
        return view('shop.products', compact('company', 'products'));
    }

    public function detail($slug) {
        $product = Product::where('slug', $slug)->where('active', true)->with('images', 'categories')->firstOrFail();
        return view('shop.product-detail', compact('product'));
    }
}
```

**Rotas:**
```php
Route::get('/produtos', [ShopController::class, 'products'])->name('shop.products');
Route::get('/produtos/{slug}', [ShopController::class, 'detail'])->name('shop.product-detail');
```

**Verificar:** Acessar `/produtos` — ver grid de produtos. Ao clicar em um, ver detalhe. Botão "Comprar" só aparece para clientes logados (guard `client`).

---

### Task 4: Carrinho de compras frontend + backend

**Objetivo:** Cliente logado pode adicionar produtos ao carrinho, ver carrinho, remover itens, e ir para checkout.

**Arquivos:**
- Criar: `app/Http/Controllers/CartController.php`
- Criar: `resources/views/shop/cart.blade.php`
- Modificar: `routes/web.php`

**CartController:**
```php
class CartController extends Controller
{
    public function __construct() {
        $this->middleware('auth:client');
    }

    public function index() {
        $cart = $this->getOrCreateCart();
        $cart->load('items.product.images');
        return view('shop.cart', compact('cart'));
    }

    public function add(Request $request, Product $product) {
        $cart = $this->getOrCreateCart();
        $item = $cart->items()->firstOrNew(['product_id' => $product->id]);
        $item->quantity = ($item->exists ? $item->quantity : 0) + ($request->qty ?? 1);
        $item->price = $product->price;
        $item->save();
        return redirect()->route('shop.cart')->with('success', 'Produto adicionado ao carrinho!');
    }

    public function remove(CartItem $item) {
        $item->delete();
        return back()->with('success', 'Item removido.');
    }

    private function getOrCreateCart() {
        $client = Auth::guard('client')->user();
        $company = (new CompanyService)->myCompany();
        return Cart::firstOrCreate([
            'client_id' => $client->id,
            'project_id' => $company->project_id, // ou project relacionado
        ], ['external_reference' => (string) Str::uuid()]);
    }
}
```

**Rotas:**
```php
Route::middleware('auth:client')->group(function () {
    Route::get('/carrinho', [CartController::class, 'index'])->name('shop.cart');
    Route::post('/carrinho/adicionar/{product}', [CartController::class, 'add'])->name('shop.cart.add');
    Route::delete('/carrinho/remover/{item}', [CartController::class, 'remove'])->name('shop.cart.remove');
});
```

**Verificar:** Logar como cliente, acessar `/produtos`, clicar "Comprar", ver item no carrinho em `/carrinho`.

---

### Task 5: Checkout Pro — Preference + Order

**Objetivo:** Substituir o fluxo atual de pagamento direto pelo Checkout Pro. O backend cria uma **Preference** com os itens do carrinho e redireciona o cliente.

**Arquivos:**
- Criar: `app/Services/CheckoutProService.php`
- Modificar: `app/Http/Controllers/CartController.php` (adicionar método `checkout`)
- Criar: `resources/views/shop/checkout.blade.php`
- Modificar: `routes/web.php`

**CheckoutProService.php:**
```php
class CheckoutProService
{
    public function __construct() {
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
    }

    public function createPreference(Cart $cart, string $paymentMethod, ?float $discount): array
    {
        $client = Auth::guard('client')->user();
        $items = [];
        foreach ($cart->items as $item) {
            $items[] = [
                'id' => (string) $item->product_id,
                'title' => $item->product->name,
                'quantity' => (int) $item->quantity,
                'unit_price' => (float) $item->price,
                'currency_id' => 'BRL',
            ];
        }

        // Calcula desconto
        $total = $cart->items->sum(fn($i) => $i->price * $i->quantity);

        $preferenceData = [
            'items' => $items,
            'payer' => [
                'name' => $client->name,
                'email' => $client->email,
            ],
            'back_urls' => [
                'success' => route('shop.order.success'),
                'failure' => route('shop.order.failure'),
                'pending' => route('shop.order.pending'),
            ],
            'auto_return' => 'approved',
            'external_reference' => $cart->external_reference,
            'notification_url' => config('services.mercadopago.webhook_url'),
        ];

        // Desconto como item separado (negativo)
        if ($discount > 0) {
            $preferenceData['items'][] = [
                'title' => 'Desconto ' . strtoupper($paymentMethod),
                'quantity' => 1,
                'unit_price' => -$discount,
                'currency_id' => 'BRL',
            ];
        }

        // Para assinatura mensal de plano, usar preapproval
        // Para produtos avulsos, preference normal

        $client = new PreferenceClient();
        $preference = $client->create($preferenceData);

        // Cria Order + Payment
        $order = Order::create([
            'cart_id' => $cart->id,
            'project_id' => $cart->project_id,
            'external_reference' => $cart->external_reference,
            'total' => $total,
            'total_discount' => $discount,
            'status' => 'pending',
        ]);

        Payment::create([
            'order_id' => $order->id,
            'company_id' => $cart->project->company_id ?? null,
            'project_id' => $cart->project_id,
            'title' => 'Pedido #' . $order->id,
            'price' => $total - $discount,
            'fee' => 0,
            'price_fee' => $total - $discount,
            'status' => 'pending',
            'return_type' => 'webhook',
            'external_reference' => $cart->external_reference,
            'preference_id' => $preference->id,
            'payment_type' => $paymentMethod,
            'redirect_success_url' => route('shop.order.success'),
            'redirect_failure_url' => route('shop.order.failure'),
            'redirect_pending_url' => route('shop.order.pending'),
            'webhook_url' => config('services.mercadopago.webhook_url'),
        ]);

        return [
            'preference_id' => $preference->id,
            'init_point' => $preference->init_point,
            'sandbox_init_point' => $preference->sandbox_init_point,
        ];
    }
}
```

**CartController@checkout:**
```php
public function checkout(Request $request) {
    $cart = $this->getOrCreateCart();
    if ($cart->items->isEmpty()) return redirect()->route('shop.cart')->with('error', 'Carrinho vazio.');

    $method = $request->payment_method ?? 'pix'; // pix, credit_card, debit_card
    $discount = $this->calculateDiscount($method, $cart);

    $service = new CheckoutProService();
    $pref = $service->createPreference($cart, $method, $discount);

    return redirect($pref['init_point']);
}

private function calculateDiscount(string $method, Cart $cart): float {
    // Config: ex: pix=5% off, debit=3% off, credit=0%
    $discounts = [
        'pix' => 0.05,
        'debit_card' => 0.03,
        'credit_card' => 0,
    ];
    $pct = $discounts[$method] ?? 0;
    $total = $cart->items->sum(fn($i) => $i->price * $i->quantity);
    return round($total * $pct, 2);
}
```

**Rotas:**
```php
Route::middleware('auth:client')->group(function () {
    Route::post('/checkout', [CartController::class, 'checkout'])->name('shop.checkout');
    Route::get('/pedido/sucesso', [CartController::class, 'orderSuccess'])->name('shop.order.success');
    Route::get('/pedido/falha', [CartController::class, 'orderFailure'])->name('shop.order.failure');
    Route::get('/pedido/pendente', [CartController::class, 'orderPending'])->name('shop.order.pending');
});
```

**Verificar:** Fluxo completo: logar → add carrinho → selecionar método → redirect MP → pagar → back_url → página de sucesso.

---

### Task 6: Descontos configuráveis por método de pagamento

**Objetivo:** Admin poder configurar % de desconto para cada método de pagamento (PIX, crédito, débito).

**Arquivos:**
- Criar: `database/migrations/2024_02_12_000043_create_payment_configs_table.php`
- Criar: `app/Models/PaymentConfig.php`
- Criar: `app/Http/Controllers/Company/PaymentConfigController.php`
- Criar: `resources/views/company/payment-config/index.blade.php`
- Modificar: `config/permissions.php`
- Modificar: `routes/web.php`

**Migration:**
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

**PaymentConfig.php** — Model simples.

**Verificar:** Admin acessa Config de Pagamento, define PIX=5%, e no checkout o desconto é aplicado.

---

### Task 7: Webhook — confirmação de pagamento e redirect

**Objetivo:** Webhook do MP atualiza status da Order + Payment, e cliente é redirecionado à página de sucesso.

**Arquivos:**
- Modificar: `app/Services/PaymentService.php` (método `processWebhook`)
- Modificar: `app/Http/Controllers/WebhookController.php`
- Criar: `resources/views/shop/order-success.blade.php`
- Criar: `resources/views/shop/order-failure.blade.php`

**WebhookController:**
```php
public function mercadopago(Request $request) {
    $service = new PaymentService();
    $payment = $service->processWebhook($request->all());
    return response()->json(['received' => true]);
}
```

**processWebhook atualizado:** Já existe e funciona. Adicionar lógica para atualizar Order corretamente com base na `external_reference` (que é o `cart.external_reference`).

Quando o webhook confirma, o cliente será redirecionado via back_url do Checkout Pro para `/pedido/sucesso`, que mostra o resumo.

**Verificar:** Simular webhook com `curl -X POST ...` ou pagar de verdade no sandbox.

---

### Task 8: Assinatura recorrente mensal (planos)

**Objetivo:** Usar `preapproval` do Mercado Pago para assinaturas mensais de planos.

**Arquivos:**
- Modificar: `app/Services/CheckoutProService.php` (método `createSubscription`)
- Modificar: `app/Http/Controllers/Company/PlanController.php`
- Criar: `app/Services/SubscriptionService.php`
- Criar: `database/migrations/2024_02_13_000044_add_subscription_fields_to_payments_table.php`

**Checkout Pro Subscriptions:**
```php
use MercadoPago\Client\Preapproval\PreapprovalClient;

public function createSubscription(Plan $plan, Client $client, string $interval): array
{
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
```

**Verificar:** Fluxo de contratar plano → redirect MP → assinar → webhook → plano ativo na company.

---

### Task 9: Dashboard admin com analytics de vendas

**Objetivo:** Admin vê volume de vendas, top produtos, ordens pendentes, receita por período.

**Arquivos:**
- Modificar: `app/Http/Controllers/DashboardController.php`
- Modificar: `resources/views/admin/dashboard.blade.php`
- Criar: `app/Services/SalesAnalyticsService.php`

**SalesAnalyticsService.php:**
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
            ->select('products.name', DB::raw('SUM(cart_items.quantity) as total_qtd'), DB::raw('SUM(cart_items.price * cart_items.quantity) as total_revenue'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qtd')
            ->limit($limit)
            ->get();
    }

    public function revenueByPeriod(string $period = 'month'): Collection {
        // Agrupa por mês as approved payments
        return Payment::where('status', 'approved')
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('SUM(price) as total'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }
}
```

**Dashboard admin atualizado:**
- Cards: Total vendido, Total pedidos, Pendentes, Produtos mais vendidos (tabela)
- Gráfico simples de receita mensal

**Verificar:** Acessar `/admin/dashboard` — ver dados reais após algumas vendas de teste.

---

### Task 10: Middleware client-auth para rotas de loja

**Objetivo:** Garantir que só clientes logados acessem carrinho e checkout.

**Arquivos:**
- Criar: `app/Http/Middleware/RedirectIfNotClient.php`
- Modificar: `app/Http/Kernel.php`
- Modificar: `routes/web.php` (já usa `auth:client` nas rotas)

Já adiantado nas tasks anteriores. Apenas garantir que:
- `Route::middleware('auth:client')` protege carrinho e checkout
- Produtos públicos (GET) não têm middleware
- Cliente não logado vê botão "Login para comprar" no lugar de "Comprar"

**Verificar:** Sem login, `/carrinho` redireciona para `/cliente/login`.

---

## ORDEM DE EXECUÇÃO

| # | Task | Depende | Arquivos-chave | Testável |
|---|------|---------|---------------|----------|
| 1 | Schema cart_items + Models | — | migration, Cart.php, CartItem.php | migrate |
| 2 | Client autenticável | — | Client.php, auth.php, ClientAuthController | login/register |
| 3 | Página pública de produtos | — | ShopController, shop/products.blade | acessar /produtos |
| 4 | Carrinho backend+frontend | 1, 2, 3 | CartController, cart.blade | add/remove itens |
| 5 | Checkout Pro (preference) | 4 | CheckoutProService, CartController@checkout | redirect MP |
| 6 | Descontos configuráveis | 5 | PaymentConfig, ConfigController | admin configura |
| 7 | Webhook confirmação | 5 | WebhookController, PaymentService | pagar sandbox |
| 8 | Assinatura recorrente (planos) | 2, 5 | SubscriptionService, PlanController | contratar plano |
| 9 | Dashboard analytics | 7 | SalesAnalyticsService, admin.dashboard | ver dados |
| 10 | Middleware client-auth | 2 | RedirectIfNotClient | testar sem login |

---

## PONTOS DE ATENÇÃO

1. **Checkout Pro vs Payment API**: O Checkout Pro usa `PreferenceClient`, não `PaymentClient`. O fluxo é: criar preference → redirect usuário → MP processa → back_url. O `PaymentClient` atual (pix/card/boleto) será substituído.

2. **Webhook URL**: MP bloqueia URLs com localhost/127.0.0.1. Usar ngrok ou deploy real para testar webhooks.

3. **Auto Return**: Configurar `auto_return=approved` no Checkout Pro para redirect automático quando pagamento aprovado.

4. **Subscription**: Mercado Pago `preapproval` não é a mesma coisa que `preference`. Usar `PreapprovalClient` para planos recorrentes.

5. **Client X Company**: Client é autenticável mas pertence a uma Company. O login deve considerar o subdomain/project da empresa.

6. **Segurança**: As rotas de cliente (`auth:client`) são separadas das rotas de admin/company (`auth`).

---

## VERIFICAÇÃO FINAL

- [ ] `php artisan migrate` funciona sem erros
- [ ] `/cliente/login` e `/cliente/register` funcionam
- [ ] `/produtos` mostra produtos ativos
- [ ] Cliente logado pode adicionar ao carrinho
- [ ] Checkout redireciona para Mercado Pago
- [ ] Webhook atualiza status do pagamento
- [ ] Plano contratado vira assinatura recorrente
- [ ] Dashboard admin mostra vendas reais
- [ ] Desconto PIX aplicado corretamente no checkout
