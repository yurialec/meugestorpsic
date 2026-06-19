<nav>
  <a href="{{route('index.site')}}" class="nav-logo">
    <div class="logo-icon">
      <img src="{{ asset('images/logo.png') }}" alt="Logo" width="40" height="40">
      </svg>
    </div>
    <div class="nav-brand">
      <span class="nav-brand-name">MeuGestorPsic</span>
      <span class="nav-brand-tag">gestão para psicólogos</span>
    </div>
  </a>
  <ul class="nav-links">
    <li><a href="#funcoes">Funcionalidades</a></li>
    <li><a href="#planos">Planos</a></li>
    <li><a href="#depoimentos">Depoimentos</a></li>
    <li><a href="#faq">FAQ</a></li>
  </ul>
  <div class="nav-actions">
    <a href="{{route('tenant.login.form')}}" class="btn-login">Entrar</a>
    <a href="{{route('form.cadastro')}}" class="btn-cta" style="background:var(--verde);font-size:13px;font-weight:600;padding:10px 22px;letter-spacing:.01em;">
      <i class="bi bi-gift"></i> 10 dias grátis
    </a>
  </div>
</nav>