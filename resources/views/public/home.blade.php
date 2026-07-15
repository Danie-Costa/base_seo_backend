@extends('layouts.public')

@section('content')
<style>
:root {
    --accent: #D4A017;
    --accent-dim: rgba(212,160,23,0.08);
    --navy-dark: #0B1D33;
    --navy: #142C4C;
    --gray-light: #E6E6E6;
    --white: #FFFFFF;
}

* { margin:0; padding:0; box-sizing:border-box; }

/* ── Navbar ── */
.navbar {
    background: rgba(11,29,51,0.95) !important;
    backdrop-filter: blur(12px);
    border-bottom: 1px solid rgba(255,255,255,0.04);
    padding: 0.75rem 0;
}
.navbar-brand { font-weight:700; font-size:1.5rem; color:var(--white) !important; letter-spacing:-0.03em; }
.navbar-brand span { color:var(--accent); }
.nav-link { color:var(--gray-light) !important; font-weight:500; font-size:0.9rem; padding:0.5rem 1rem !important; transition:color 0.15s; }
.nav-link:hover { color:var(--accent) !important; }
.navbar .btn-outline-light { border-color:rgba(255,255,255,0.12); color:var(--gray-light); font-size:0.85rem; }
.navbar .btn-outline-light:hover { background:var(--accent); border-color:var(--accent); color:var(--navy-dark); }
.social-nav a { color:var(--gray-light); font-size:1rem; opacity:0.6; transition:opacity 0.15s; }
.social-nav a:hover { opacity:1; color:var(--accent); }

/* ── Hero ── */
.hero {
    background: linear-gradient(135deg, var(--navy-dark) 0%, #0a1f3a 50%, var(--navy-dark) 100%);
    padding: 6rem 0 4rem;
    overflow:hidden;
    position:relative;
}
.hero::before {
    content:''; position:absolute; top:-30%; right:-20%;
    width:700px; height:700px; border-radius:50%;
    background: radial-gradient(circle, rgba(212,160,23,0.04) 0%, transparent 60%);
    pointer-events:none;
}
.hero h1 { font-size:3.2rem; font-weight:800; line-height:1.1; letter-spacing:-0.03em; color:var(--white); }
.hero h1 span { color:var(--accent); }
.hero p { font-size:1.1rem; color:var(--gray-light); opacity:0.85; line-height:1.6; max-width:540px; }
.hero .btn-primary { background:var(--accent); border:none; color:var(--navy-dark); font-weight:700; padding:0.85rem 2rem; border-radius:0.5rem; font-size:0.95rem; }
.hero .btn-primary:hover { background:#e3b239; }
.hero .btn-outline { border:1px solid rgba(255,255,255,0.12); color:var(--gray-light); padding:0.85rem 2rem; border-radius:0.5rem; font-size:0.95rem; text-decoration:none; }
.hero .btn-outline:hover { border-color:var(--accent); color:var(--accent); }
.hero .rating { color:var(--accent); font-weight:700; font-size:1.1rem; }
.hero .hero-img { width:100%; max-width:480px; border-radius:0.75rem; box-shadow:0 20px 60px rgba(0,0,0,0.4); }
.hero .play-btn { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);
    width:60px; height:60px; border-radius:50%; background:var(--accent);
    display:flex; align-items:center; justify-content:center; color:var(--navy-dark); font-size:1.5rem;
    cursor:pointer; transition:transform 0.2s; }
.hero .play-btn:hover { transform:translate(-50%,-50%) scale(1.1); }
.hero .stat-box { text-align:center; padding:1.5rem; }
.hero .stat-box .num { font-size:2rem; font-weight:800; color:var(--accent); }
.hero .stat-box .label { font-size:0.85rem; color:var(--gray-light); opacity:0.7; }

/* ── Seções ── */
section { padding:5rem 0; }
.section-tag { display:inline-block; font-size:0.75rem; font-weight:600; letter-spacing:0.08em; color:var(--accent); margin-bottom:0.75rem; }
.section-title { font-size:2.2rem; font-weight:700; letter-spacing:-0.02em; color:var(--white); margin-bottom:1rem; }
.section-desc { color:var(--gray-light); opacity:0.8; max-width:600px; font-size:1rem; line-height:1.6; }
.bg-navy { background:var(--navy-dark); }
.bg-card { background:var(--navy); }

/* ── About ── */
.about-grid { display:grid; grid-template-columns:1fr 1fr; gap:3rem; align-items:center; }
.about-img { width:100%; border-radius:0.75rem; }
.about-item { display:flex; gap:1rem; align-items:flex-start; padding:1rem 0; border-bottom:1px solid rgba(255,255,255,0.04); }
.about-item:last-child { border:none; }
.about-item i { color:var(--accent); font-size:1.2rem; margin-top:0.15rem; min-width:1.5rem; }
.about-item strong { color:var(--white); display:block; font-size:0.95rem; }
.about-item span { color:var(--gray-light); opacity:0.75; font-size:0.85rem; }

/* ── Services ── */
.services-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
.service-card { padding:2rem; border-radius:0.75rem; background:var(--navy); border:1px solid rgba(255,255,255,0.04); transition:all 0.2s; cursor:pointer; }
.service-card:hover { border-color:rgba(212,160,23,0.15); background:rgba(20,44,76,0.8); }
.service-card .num { font-size:0.8rem; font-weight:600; color:var(--accent); opacity:0.6; margin-bottom:0.75rem; }
.service-card h3 { font-size:1.15rem; font-weight:600; color:var(--white); margin-bottom:0.5rem; }
.service-card p { font-size:0.9rem; color:var(--gray-light); opacity:0.75; line-height:1.5; margin:0; }

/* ── Tags ── */
.tags-row { overflow:hidden; padding:2rem 0; }
.tags-track { display:flex; gap:1.5rem; animation:scrollTags 30s linear infinite; white-space:nowrap; }
.tags-track span { font-size:0.85rem; color:var(--gray-light); opacity:0.5; }
@keyframes scrollTags { 0%{transform:translateX(0)} 100%{transform:translateX(-50%)} }

/* ── Projects/Cases ── */
.cases-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:1.5rem; }
.case-card { border-radius:0.75rem; overflow:hidden; background:var(--navy); border:1px solid rgba(255,255,255,0.04); transition:all 0.2s; }
.case-card:hover { border-color:rgba(212,160,23,0.15); transform:translateY(-3px); }
.case-card img { width:100%; height:200px; object-fit:cover; }
.case-card-body { padding:1.25rem; }
.case-card-body h4 { font-size:1rem; font-weight:600; color:var(--white); margin-bottom:0.75rem; }
.case-tags { display:flex; gap:0.4rem; flex-wrap:wrap; }
.case-tags span { font-size:0.7rem; padding:0.2rem 0.6rem; border-radius:999px; background:rgba(212,160,23,0.08); color:var(--accent); }

/* ── Differentials ── */
.diff-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:2rem; }
.diff-card { text-align:center; padding:2rem; }
.diff-card img { width:100%; border-radius:0.75rem; margin-bottom:1rem; }
.diff-card h4 { font-size:1rem; font-weight:600; color:var(--white); margin-bottom:0.5rem; }
.diff-card p { font-size:0.85rem; color:var(--gray-light); opacity:0.75; margin:0; }

/* ── Testimonials ── */
.testimonial-card { background:var(--navy); border-radius:0.75rem; padding:2rem; border:1px solid rgba(255,255,255,0.04); }

/* ── Contact Form ── */
.contact-grid { display:grid; grid-template-columns:1fr 1fr; gap:3rem; align-items:start; }
.contact-form .form-control { background:var(--navy-dark); border:1px solid rgba(255,255,255,0.08); color:var(--white); padding:0.85rem 1rem; border-radius:0.5rem; font-size:0.9rem; }
.contact-form .form-control:focus { border-color:var(--accent); box-shadow:0 0 0 3px var(--accent-dim); }
.contact-form .form-control::placeholder { color:var(--gray-light); opacity:0.4; }
.contact-form .btn-primary { background:var(--accent); border:none; color:var(--navy-dark); font-weight:700; padding:0.85rem; border-radius:0.5rem; width:100%; }
.contact-form .btn-primary:hover { background:#e3b239; }
.contact-info p { color:var(--gray-light); opacity:0.75; font-size:0.9rem; margin-bottom:0.5rem; }
.contact-info i { color:var(--accent); width:1.5rem; }

/* ── Footer ── */
footer { background:var(--navy-dark); border-top:1px solid rgba(255,255,255,0.04); padding:3rem 0 1.5rem; }
footer h5 { color:var(--white); font-weight:600; font-size:0.95rem; margin-bottom:1rem; }
footer a { color:var(--gray-light); opacity:0.7; text-decoration:none; font-size:0.85rem; display:block; margin-bottom:0.4rem; transition:opacity 0.15s; }
footer a:hover { opacity:1; color:var(--accent); }
footer .social a { display:inline; margin-right:1rem; font-size:1.1rem; }
footer hr { border-color:rgba(255,255,255,0.04); margin:2rem 0; }
footer .copy { color:var(--gray-light); opacity:0.5; font-size:0.8rem; text-align:center; }

/* ── Responsivo ── */
@media (max-width:991px) {
    .about-grid, .contact-grid { grid-template-columns:1fr; }
    .cases-grid { grid-template-columns:repeat(2,1fr); }
    .diff-grid { grid-template-columns:1fr; }
    .hero h1 { font-size:2.2rem; }
}
@media (max-width:575px) {
    .services-grid { grid-template-columns:1fr; }
    .cases-grid { grid-template-columns:1fr; }
    .hero h1 { font-size:1.8rem; }
    .hero { padding:4rem 0 2rem; }
    section { padding:3rem 0; }
    .section-title { font-size:1.6rem; }
}
</style>

{{-- Navbar --}}
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="#">Atlas<span>Labs</span></a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav mx-auto gap-1">
                <li class="nav-item"><a class="nav-link" href="#">Início</a></li>
                <li class="nav-item"><a class="nav-link" href="#about">Sobre</a></li>
                <li class="nav-item"><a class="nav-link" href="#services">Serviços</a></li>
                <li class="nav-item"><a class="nav-link" href="#cases">Projetos</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">Contato</a></li>
            </ul>
            <div class="d-flex align-items-center gap-3">
                <div class="social-nav d-flex gap-2">
                    <a href="#"><i class="fa fa-twitter"></i></a>
                    <a href="#"><i class="fa fa-facebook"></i></a>
                    <a href="#"><i class="fa fa-instagram"></i></a>
                </div>
                <a href="#contact" class="btn btn-outline-light btn-sm rounded-pill px-3">Fale conosco</a>
            </div>
        </div>
    </div>
</nav>

{{-- Hero --}}
<section class="hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <h1>EXPERTS EM SEO<br>E SITES DE ALTA<br><span>PERFORMANCE</span></h1>
                <p class="mt-3">A Atlas Labs desenvolve sites rápidos, escaláveis e otimizados para dominar as primeiras posições do Google.</p>
                <div class="d-flex gap-3 mt-4 flex-wrap">
                    <a href="#contact" class="btn btn-primary">Começar agora</a>
                    <a href="#services" class="btn btn-outline">Nossos serviços</a>
                </div>
                <div class="d-flex align-items-center gap-2 mt-4">
                    <span class="rating">4.9</span>
                    <span style="color:var(--accent);font-size:0.9rem;">★★★★★</span>
                    <span style="color:var(--gray-light);opacity:0.6;font-size:0.8rem;">Baseado em 240+ avaliações</span>
                </div>
            </div>
            <div class="col-lg-6 position-relative text-center">
                <div style="position:relative;display:inline-block;">
                    <div style="width:100%;max-width:480px;height:320px;border-radius:0.75rem;background:linear-gradient(135deg,var(--navy),var(--navy-dark));display:flex;align-items:center;justify-content:center;border:1px solid rgba(255,255,255,0.06);">
                        <i class="fa fa-users" style="font-size:4rem;color:var(--accent);opacity:0.3;"></i>
                    </div>
                    <div class="play-btn"><i class="fa fa-play"></i></div>
                </div>
                <div class="row mt-4 justify-content-center">
                    <div class="col-4 stat-box">
                        <div class="num">21+</div>
                        <div class="label">Anos de experiência</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- About --}}
<section id="about">
    <div class="container">
        <div class="about-grid">
            <div>
                <div style="width:100%;height:400px;border-radius:0.75rem;background:linear-gradient(135deg,var(--navy),var(--navy-dark));display:flex;align-items:center;justify-content:center;border:1px solid rgba(255,255,255,0.06);">
                    <i class="fa fa-rocket" style="font-size:5rem;color:var(--accent);opacity:0.2;"></i>
                </div>
            </div>
            <div>
                <div class="section-tag">SOBRE NÓS</div>
                <h2 class="section-title">Impulsionando o crescimento com inovação, dados e excelência</h2>
                <p class="section-desc">Somos uma agência focada em performance. Combinamos engenharia de software de ponta com estratégias de SEO comprovadas para construir sites que carregam rápido, convertem mais e escalam sem limites.</p>
                <div class="mt-4">
                    <div class="about-item"><i class="fa fa-bar-chart"></i><div><strong>Revolucionamos negócios</strong><span>Soluções orientadas por dados que transformam resultados.</span></div></div>
                    <div class="about-item"><i class="fa fa-lightbulb-o"></i><div><strong>Ajudamos empresas</strong><span>A tomar as decisões técnicas certas para crescer.</span></div></div>
                    <div class="about-item"><i class="fa fa-line-chart"></i><div><strong>Entregamos soluções</strong><span>Que antecipam tendências futuras do mercado digital.</span></div></div>
                    <div class="about-item"><i class="fa fa-handshake-o"></i><div><strong>Vamos construir</strong><span>Sua estratégia de SEO juntos, do planejamento à execução.</span></div></div>
                </div>
                <a href="#contact" class="btn btn-primary mt-4">Entre em contato</a>
            </div>
        </div>
    </div>
</section>

{{-- Services --}}
<section id="services" class="bg-navy">
    <div class="container">
        <div class="section-tag">SERVIÇOS</div>
        <h2 class="section-title">O que oferecemos aos nossos clientes</h2>
        <div class="services-grid mt-4">
            <div class="service-card"><div class="num">(01)//</div><h3>Auditoria & Estratégia de SEO</h3><p>Analisamos cada detalhe técnico do seu site e traçamos um plano claro para escalar seu tráfego orgânico.</p></div>
            <div class="service-card"><div class="num">(02)//</div><h3>Criação de Conteúdo</h3><p>Produzimos conteúdo estratégico e otimizado que atrai, engaja e converte o seu público-alvo.</p></div>
            <div class="service-card"><div class="num">(03)//</div><h3>Otimização de SEO Local</h3><p>Colocamos o seu negócio no topo das buscas da sua região e no Google Maps.</p></div>
            <div class="service-card"><div class="num">(04)//</div><h3>Link Building de Alta Qualidade</h3><p>Construímos autoridade de domínio com backlinks relevantes e sustentáveis a longo prazo.</p></div>
        </div>
    </div>
</section>

{{-- Tags --}}
<div class="tags-row bg-navy" style="padding-top:0;">
    <div class="tags-track">
        <span>Criação de conteúdo • Marketing digital • SEO para E-commerce • Análise de concorrência • Otimização de SEO • Link building •</span>
        <span>Criação de conteúdo • Marketing digital • SEO para E-commerce • Análise de concorrência • Otimização de SEO • Link building •</span>
    </div>
</div>

{{-- Cases --}}
<section id="cases">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
            <div>
                <div class="section-tag">CASES</div>
                <h2 class="section-title mb-0">Já entregamos mais de 1030+ projetos premiados</h2>
            </div>
            <a href="#" class="btn btn-outline-light btn-sm rounded-pill px-3" style="border-color:rgba(255,255,255,0.12);color:var(--gray-light);text-decoration:none;font-size:0.85rem;">Ver todos os cases</a>
        </div>
        <div class="cases-grid">
            <div class="case-card">
                <div style="height:200px;background:linear-gradient(135deg,var(--navy),var(--navy-dark));display:flex;align-items:center;justify-content:center;"><i class="fa fa-google" style="font-size:3rem;color:var(--accent);opacity:0.2;"></i></div>
                <div class="case-card-body"><h4>Projeto Keyword Climb</h4><div class="case-tags"><span>Agência</span><span>SEO</span><span>Auditoria</span></div></div>
            </div>
            <div class="case-card">
                <div style="height:200px;background:linear-gradient(135deg,var(--navy-dark),var(--navy));display:flex;align-items:center;justify-content:center;"><i class="fa fa-line-chart" style="font-size:3rem;color:var(--accent);opacity:0.2;"></i></div>
                <div class="case-card-body"><h4>Escalada de Palavras-chave</h4><div class="case-tags"><span>Agência</span><span>SEO</span><span>Auditoria</span></div></div>
            </div>
            <div class="case-card">
                <div style="height:200px;background:linear-gradient(135deg,var(--navy),var(--navy-dark));display:flex;align-items:center;justify-content:center;"><i class="fa fa-rocket" style="font-size:3rem;color:var(--accent);opacity:0.2;"></i></div>
                <div class="case-card-body"><h4>Campanha Traffic Boost</h4><div class="case-tags"><span>Agência</span><span>SEO</span><span>Auditoria</span></div></div>
            </div>
            <div class="case-card">
                <div style="height:200px;background:linear-gradient(135deg,var(--navy-dark),var(--navy));display:flex;align-items:center;justify-content:center;"><i class="fa fa-map-marker" style="font-size:3rem;color:var(--accent);opacity:0.2;"></i></div>
                <div class="case-card-body"><h4>Sucesso em SEO Local</h4><div class="case-tags"><span>Agência</span><span>SEO</span><span>Auditoria</span></div></div>
            </div>
        </div>
    </div>
</section>

{{-- Differentials --}}
<section class="bg-navy">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-tag">NOSSOS DIFERENCIAIS</div>
            <h2 class="section-title">Juntos, damos vida à sua visão</h2>
        </div>
        <div class="diff-grid">
            <div class="diff-card">
                <div style="height:200px;border-radius:0.75rem;background:linear-gradient(135deg,var(--navy),var(--navy-dark));display:flex;align-items:center;justify-content:center;margin-bottom:1rem;border:1px solid rgba(255,255,255,0.04);">
                    <i class="fa fa-pencil-square-o" style="font-size:3rem;color:var(--accent);opacity:0.2;"></i>
                </div>
                <h4>Estratégias de SEO sob medida</h4>
                <p>Cada projeto recebe um plano exclusivo, alinhado aos objetivos e ao mercado do seu negócio.</p>
            </div>
            <div class="diff-card">
                <div style="height:200px;border-radius:0.75rem;background:linear-gradient(135deg,var(--navy-dark),var(--navy));display:flex;align-items:center;justify-content:center;margin-bottom:1rem;border:1px solid rgba(255,255,255,0.04);">
                    <i class="fa fa-file-text-o" style="font-size:3rem;color:var(--accent);opacity:0.2;"></i>
                </div>
                <h4>Transparência e integridade</h4>
                <p>Relatórios claros e comunicação honesta em cada etapa da jornada.</p>
            </div>
            <div class="diff-card">
                <div style="height:200px;border-radius:0.75rem;background:linear-gradient(135deg,var(--navy),var(--navy-dark));display:flex;align-items:center;justify-content:center;margin-bottom:1rem;border:1px solid rgba(255,255,255,0.04);">
                    <i class="fa fa-trophy" style="font-size:3rem;color:var(--accent);opacity:0.2;"></i>
                </div>
                <h4>Expertise comprovada em SEO</h4>
                <p>Anos de resultados reais colocando marcas no topo do Google.</p>
            </div>
        </div>
    </div>
</section>

{{-- Testimonials --}}
<section>
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-tag">DEPOIMENTOS</div>
            <h2 class="section-title">O que nossos clientes dizem sobre nós</h2>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="testimonial-card text-center">
                    <div style="font-size:2rem;color:var(--accent);margin-bottom:0.5rem;">★★★★★</div>
                    <div style="font-size:1.5rem;font-weight:700;color:var(--accent);">4.8</div>
                    <div style="color:var(--gray-light);opacity:0.6;font-size:0.85rem;margin-bottom:1.5rem;">(1k+) avaliações de clientes</div>
                    <p style="color:var(--gray-light);font-size:1rem;line-height:1.6;max-width:500px;margin:0 auto;">"Entregamos soluções inteligentes para empresas e startups de alta performance. A Atlas Labs transformou completamente nossa presença digital."</p>
                    <div style="margin-top:1.5rem;font-size:0.75rem;font-weight:600;letter-spacing:0.1em;color:var(--accent);">ATLAS LABS</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Contact --}}
<section id="contact" class="bg-navy">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-tag">CONTATO</div>
            <h2 class="section-title">Inovação para o crescimento do seu negócio com a Atlas Labs</h2>
        </div>
        @if(session('success'))
        <div class="alert alert-success text-center mb-4" style="max-width:600px;margin:0 auto;background:rgba(30,158,99,0.1);border:1px solid rgba(30,158,99,0.2);color:#1E9E63;">{{ session('success') }}</div>
        @endif
        <div class="contact-grid">
            <div>
                {{ Form::open(['route' => 'lead.store', 'class' => 'contact-form']) }}
                <div class="row g-3">
                    <div class="col-md-6"><input type="text" name="name" class="form-control" placeholder="Nome" value="{{ old('name') }}" required>@error('name')<small class="text-danger">{{ $message }}</small>@enderror</div>
                    <div class="col-md-6"><input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}" required>@error('email')<small class="text-danger">{{ $message }}</small>@enderror</div>
                    <div class="col-md-6"><input type="text" name="phone" class="form-control" placeholder="Telefone" value="{{ old('phone') }}"></div>
                    <div class="col-md-6"><input type="text" name="company_name" class="form-control" placeholder="Empresa" value="{{ old('company_name') }}"></div>
                    <div class="col-12"><textarea name="message" rows="4" class="form-control" placeholder="Conte sobre seu projeto">{{ old('message') }}</textarea></div>
                    <div class="col-12"><button type="submit" class="btn btn-primary">Enviar mensagem</button></div>
                </div>
                {{ Form::close() }}
            </div>
            <div class="contact-info">
                <div style="width:100%;height:250px;border-radius:0.75rem;background:linear-gradient(135deg,var(--navy),var(--navy-dark));display:flex;flex-direction:column;align-items:center;justify-content:center;border:1px solid rgba(255,255,255,0.04);margin-bottom:2rem;">
                    <i class="fa fa-envelope-o" style="font-size:2.5rem;color:var(--accent);margin-bottom:0.5rem;"></i>
                    <p style="margin:0;font-size:0.9rem;">contato@atlaslabs.com.br</p>
                    <p style="margin:0;font-size:0.9rem;">(11) 99999-8888</p>
                </div>
                <div class="d-flex gap-3 mt-3" style="font-size:1.3rem;">
                    <a href="#" style="color:var(--gray-light);opacity:0.6;"><i class="fa fa-instagram"></i></a>
                    <a href="#" style="color:var(--gray-light);opacity:0.6;"><i class="fa fa-linkedin-square"></i></a>
                    <a href="#" style="color:var(--gray-light);opacity:0.6;"><i class="fa fa-facebook-square"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Footer --}}
<footer>
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <h5><i class="fa fa-diamond text-primary me-1"></i> Atlas Labs</h5>
                <p style="color:var(--gray-light);opacity:0.6;font-size:0.85rem;">Posicionamento digital, SEO e tecnologia para levar seu negócio ao próximo nível.</p>
            </div>
            <div class="col-md-2">
                <h5>Navegação</h5>
                <a href="#">Início</a>
                <a href="#about">Sobre</a>
                <a href="#services">Serviços</a>
                <a href="#cases">Projetos</a>
                <a href="#contact">Contato</a>
            </div>
            <div class="col-md-3">
                <h5>Contato</h5>
                <a href="#">contato@atlaslabs.com.br</a>
                <a href="#">(11) 99999-8888</a>
                <div class="social mt-2">
                    <a href="#"><i class="fa fa-instagram"></i></a>
                    <a href="#"><i class="fa fa-linkedin-square"></i></a>
                    <a href="#"><i class="fa fa-facebook-square"></i></a>
                </div>
            </div>
            <div class="col-md-3">
                <h5>Serviços</h5>
                <a href="#">Auditoria SEO</a>
                <a href="#">Criação de Conteúdo</a>
                <a href="#">SEO Local</a>
                <a href="#">Link Building</a>
                <a href="#">Criação de Sites</a>
            </div>
        </div>
        <hr>
        <p class="copy">&copy; {{ date('Y') }} Atlas Labs. Todos os direitos reservados.</p>
    </div>
</footer>

<script>
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', function(e) {
        e.preventDefault();
        const el = document.querySelector(this.getAttribute('href'));
        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
});
</script>
@endsection
