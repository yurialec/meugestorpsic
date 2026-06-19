@extends('layouts.app')

@section('content')
    <div class="hero-wrap">
  <div class="hero">
    <div>
      <div class="hero-badge">
        <span class="hero-badge-dot"></span>
        Feito para psicólogos recém-formados
      </div>
      <h1>Organize sua clínica<br><em>desde o primeiro</em><br><strong>paciente.</strong></h1>
      <p class="hero-sub">Sistema simples e seguro para psicólogos(as) iniciantes cuidarem de pacientes, prontuários e financeiro em um só lugar.</p>
      <div class="hero-btns">
        <a href="{{route('form.cadastro')}}" class="btn-hero-primary">Testar grátis por 10 dias</a>
        <a href="#funcoes" class="btn-hero-ghost">Ver como funciona</a>
      </div>
      <div class="hero-social-proof">
        <div class="hero-avatars">
          <div class="hero-avatar" style="background:#1a3f7a">AM</div>
          <div class="hero-avatar" style="background:#3ec97a">RC</div>
          <div class="hero-avatar" style="background:#2558a8">JS</div>
          <div class="hero-avatar" style="background:#25a85e">BF</div>
        </div>
        <p class="hero-proof-text"><strong>+2.000 psicólogos</strong><br>já organizam sua rotina aqui</p>
      </div>
    </div>
    <div class="app-mockup">
      <div class="mockup-topbar">
        <div class="mockup-greeting">
          Bom dia 👋
          <strong>Dra. Juliana</strong>
        </div>
        <span class="mockup-day-tag">Hoje · 30 abr</span>
      </div>
      <div class="agenda-items">
        <div class="agenda-item">
          <span class="agenda-dot dot-verde"></span>
          <span class="agenda-time">08h00</span>
          <span class="agenda-name">Mariana S.</span>
          <span class="agenda-tag tag-confirmado">Confirmado</span>
        </div>
        <div class="agenda-item">
          <span class="agenda-dot dot-azul"></span>
          <span class="agenda-time">10h00</span>
          <span class="agenda-name">Carlos R.</span>
          <span class="agenda-tag tag-online">Online</span>
        </div>
        <div class="agenda-item">
          <span class="agenda-dot dot-verde"></span>
          <span class="agenda-time">14h00</span>
          <span class="agenda-name">Beatriz N.</span>
          <span class="agenda-tag tag-confirmado">Confirmado</span>
        </div>
        <div class="agenda-item">
          <span class="agenda-dot dot-prata"></span>
          <span class="agenda-time">16h00</span>
          <span class="agenda-name">Paulo M.</span>
          <span class="agenda-tag tag-pendente">Pendente</span>
        </div>
      </div>
      <div class="mockup-stats">
        <div class="stat-box">
          <span class="stat-num">4</span>
          <span class="stat-label">sessões hoje</span>
        </div>
        <div class="stat-box">
          <span class="stat-num verde">R$680</span>
          <span class="stat-label">a receber</span>
        </div>
        <div class="stat-box">
          <span class="stat-num">0</span>
          <span class="stat-label">faltas no mês</span>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- TRUST BAR -->
<div class="trust-bar">
  <div class="trust-item"><span class="trust-check">✓</span>Sem cartão de crédito no teste</div>
  <div class="trust-item"><span class="trust-check">✓</span>Cancele quando quiser</div>
  <div class="trust-item"><span class="trust-check">✓</span>Protegido pela LGPD</div>
  <div class="trust-item"><span class="trust-check">✓</span>Suporte 100% humano</div>
  <div class="trust-item"><span class="trust-check">✓</span>Em conformidade com o CFP</div>
</div>

<!-- DORES -->
<div class="dores-bg">
  <div class="section">
    <div class="section-header">
      <div class="section-label">Você se identifica?</div>
      <h2 class="section-title">Os desafios de quem está<br><em>começando a carreira</em></h2>
      <p class="section-sub">O MeuGestorPsic nasceu a partir da realidade de psicólogos recém-formados. Sabemos o que trava.</p>
    </div>
    <div class="dores-grid">
      <div class="dor-card">
        <div class="dor-icon"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="16" y1="2" x2="16" y2="6"/></svg></div>
        <h3>Agenda desorganizada</h3>
        <p>Conflitos de horário, confirmações pelo WhatsApp e pacientes que desmarcam sem aviso geram stress desnecessário desde o primeiro mês.</p>
      </div>
      <div class="dor-card">
        <div class="dor-icon"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></div>
        <h3>Prontuários em papel</h3>
        <p>Cadernos, folhas soltas e arquivos espalhados. Risco de perda, falta de privacidade e horas desperdiçadas procurando anotações antigas.</p>
      </div>
      <div class="dor-card">
        <div class="dor-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="5"/><path d="M3 21v-2a7 7 0 0 1 14 0v2"/></svg></div>
        <h3>Não saber como se posicionar</h3>
        <p>Como atrair pacientes? O que divulgar? Quanto cobrar? Essas dúvidas travam quem está no início e não tem referência de quem passou por isso.</p>
      </div>
      <div class="dor-card">
        <div class="dor-icon"><svg viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
        <h3>Financeiro no escuro</h3>
        <p>Inadimplência sem controle, cobranças constrangedoras e nenhuma clareza sobre quanto está entrando — ou saindo — no mês.</p>
      </div>
    </div>
  </div>
</div>

<!-- FUNCIONALIDADES -->
<div class="funcoes-bg" id="funcoes">
  <div class="section">
    <div class="funcoes-layout">
      <div>
        <div class="section-header">
          <div class="section-label">Funcionalidades</div>
          <h2 class="section-title">Tudo para a<br><em>sua rotina clínica</em></h2>
          <p class="section-sub">Cada recurso foi pensado para poupar tempo e reduzir a carga mental de quem está construindo a carreira.</p>
        </div>
        <div class="funcoes-list">
          <div class="funcao-item">
            <div class="funcao-num">01</div>
            <div class="funcao-content">
              <div class="funcao-tag">Agenda</div>
              <h3>Calendário para organização do seu dia a dia</h3>
              <p>Tenha uma visão ampla de quantos atendimentos foram marcados e quais horários estão disponíveis — tudo de forma simples e visual.</p>
            </div>
          </div>
          <div class="funcao-item">
            <div class="funcao-num">02</div>
            <div class="funcao-content">
              <div class="funcao-tag verde">Lembretes</div>
              <h3>Lembretes automáticos</h3>
              <p>Confirmações de sessão enviadas por e-mail e mensagens personalizadas enviadas sem você precisar lembrar de nada. Reduza faltas em até 87%.</p>
            </div>
          </div>
          <div class="funcao-item">
            <div class="funcao-num">03</div>
            <div class="funcao-content">
              <div class="funcao-tag">Prontuário</div>
              <h3>Registros clínicos seguros e organizados</h3>
              <p>Anamnese estruturada, evolução por sessão e arquivos com criptografia. Em conformidade com as resoluções do CFP sobre prontuários digitais.</p>
            </div>
          </div>
          <div class="funcao-item">
            <div class="funcao-num">04</div>
            <div class="funcao-content">
              <div class="funcao-tag verde">Financeiro</div>
              <h3>Controle de pagamentos sem planilha</h3>
              <p>Veja em tempo real quem pagou, quem está pendente e quanto vai entrar no mês.</p>
            </div>
          </div>
          <div class="funcao-item">
            <div class="funcao-num">05</div>
            <div class="funcao-content">
              <div class="funcao-tag">Posicionamento</div>
              <h3>Consultoria de carreira para recém-formados</h3>
              <p>Orientações práticas sobre nicho, precificação e presença digital. Exclusivo no MeuGestorPsic — aprenda a atrair os pacientes certos para o seu perfil.</p>
            </div>
          </div>
          <div class="funcao-item">
            <div class="funcao-num">06</div>
            <div class="funcao-content">
              <div class="funcao-tag verde">Suporte</div>
              <h3>Atendimento humano desde o primeiro dia</h3>
              <p>Nenhum robô. Desde o período de teste você fala com pessoas reais que entendem a psicologia e te ajudam em cada etapa da configuração.</p>
            </div>
          </div>
        </div>
      </div>
      <div class="funcoes-sticky">
        <div class="funcoes-card-main">
          <h4>Sua agenda diária ☀️</h4>
          <p>Visão de um resumo do dia antes do primeiro atendimento. Organize-se com tranquilidade para verificar a sua agenda diária.</p>
          <div class="whatsapp-chip">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="#fff"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.555 4.115 1.524 5.843L.057 23.928l6.248-1.637A11.942 11.942 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.875 0-3.63-.487-5.148-1.34l-.366-.213-3.714.974 1.001-3.618-.239-.386A9.96 9.96 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
            Lembretes automáticos
          </div>
        </div>
        <div class="mini-stats">
          <div class="mini-stat">
            <span class="mini-stat-num">87%</span>
            <span class="mini-stat-label">menos faltas</span>
          </div>
          <div class="mini-stat">
            <span class="mini-stat-num verde">3h</span>
            <span class="mini-stat-label">economizadas/semana</span>
          </div>
          <div class="mini-stat">
            <span class="mini-stat-num">+2k</span>
            <span class="mini-stat-label">psicólogos ativos</span>
          </div>
          <div class="mini-stat">
            <span class="mini-stat-num verde">4.9</span>
            <span class="mini-stat-label">avaliação média</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- DIFERENCIAIS -->
<div class="diferenciais-bg">
  <div class="section">
    <div class="section-header" style="text-align:center">
      <div class="section-label" style="justify-content:center">Por que o MeuGestorPsic</div>
      <h2 class="section-title" style="text-align:center">Feito para quem está<br><em>começando</em>, não para grandes clínicas</h2>
    </div>
    <div class="diferenciais-grid">
      <div class="dif-card destaque">
        <div class="dif-label">MeuGestorPsic</div>
        <div class="dif-item"><div class="dif-icon-ok">✓</div><div class="dif-item-text">Preço a partir de R$29,90 — acessível desde o início (somente o sistema)</div></div>
        <div class="dif-item"><div class="dif-icon-ok">✓</div><div class="dif-item-text">Consultoria de posicionamento exclusiva para recém-formados + sistema a partir de R$59,90</div></div>
        <div class="dif-item"><div class="dif-icon-ok">✓</div><div class="dif-item-text">Suporte + consultoria de posicionamento + atendimento por psicólogos experientes a partir de R$449,90</div></div>
        <div class="dif-item"><div class="dif-icon-ok">✓</div><div class="dif-item-text">Onboarding guiado: configure tudo em menos de 10 minutos</div></div>
        <div class="dif-item"><div class="dif-icon-ok">✓</div><div class="dif-item-text">10 dias grátis sem cartão de crédito</div></div>
      </div>
      <div class="dif-card normal">
        <div class="dif-label">Outros sistemas do mercado</div>
        <div class="dif-item"><div class="dif-icon-no">✕</div><div class="dif-item-text">Planos a partir de R$59–R$80, pesados para o início</div></div>
        <div class="dif-item"><div class="dif-icon-no">✕</div><div class="dif-item-text">Sem foco em posicionamento ou crescimento de carreira</div></div>
        <div class="dif-item"><div class="dif-icon-no">✕</div><div class="dif-item-text">Suporte genérico por chatbot ou ticket</div></div>
        <div class="dif-item"><div class="dif-icon-no">✕</div><div class="dif-item-text">Curva de aprendizado longa, pensada para clínicas estabelecidas</div></div>
        <div class="dif-item"><div class="dif-icon-no">✕</div><div class="dif-item-text">Teste curto (7 dias) ou exige cartão de crédito</div></div>
      </div>
    </div>
  </div>
</div>

<!-- DEPOIMENTOS -->
<div class="testi-bg" id="depoimentos">
  <div class="section">
    <div class="section-header">
      <div class="section-label">Depoimentos</div>
      <h2 class="section-title">Quem usa,<br><em>recomenda</em></h2>
    </div>
    <div class="testi-grid">
      <div class="testi-card">
        <div class="testi-stars">★★★★★</div>
        <p class="testi-text">Antes eu vivia perdida entre caderno, WhatsApp e planilha. Agora tudo está em um lugar só e me sinto muito mais profissional desde o começo.</p>
        <div class="testi-author">
          <div class="testi-av" style="background:var(--azul)">AM</div>
          <div>
            <div class="testi-name">Ana Meireles</div>
            <div class="testi-role">Psicóloga clínica · formada em 2023</div>
          </div>
        </div>
      </div>
      <div class="testi-card">
        <div class="testi-stars">★★★★★</div>
        <p class="testi-text">A consultoria de posicionamento foi o diferencial. Em três meses a agenda estava quase cheia. Não esperava resultado tão rápido logo no começo da carreira.</p>
        <div class="testi-author">
          <div class="testi-av" style="background:var(--verde-escuro)">RC</div>
          <div>
            <div class="testi-name">Rafael Costa</div>
            <div class="testi-role">Psicólogo · 1 ano de consultório próprio</div>
          </div>
        </div>
      </div>
      <div class="testi-card">
        <div class="testi-stars">★★★★★</div>
        <p class="testi-text">Os lembretes automáticos acabaram com as faltas sem aviso. E o suporte é incrível — você fala com gente de verdade que entende nosso trabalho.</p>
        <div class="testi-author">
          <div class="testi-av" style="background:var(--azul-medio)">JS</div>
          <div>
            <div class="testi-name">Juliana Souza</div>
            <div class="testi-role">Psicóloga · recém-formada pela UFMG</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- PREÇOS -->
<div class="preco-bg" id="planos">
  <div class="section">
    <div class="section-header" style="text-align:center">
      <div class="section-label" style="justify-content:center">Planos</div>
      <h2 class="section-title" style="text-align:center">Simples, justo<br>e <em>acessível</em></h2>
      <p class="section-sub" style="margin:0 auto 32px">Sem taxas escondidas. Tudo incluso. Cancele quando quiser.</p>
    </div>
    <div class="planos-grid">
      <!-- BASIC -->
      <div class="plano-card">
        <div class="plano-nome">BASIC</div>
        <div class="plano-preco"><sup>R$</sup>29<span>,90/mês</span></div>
        <p class="plano-desc">Para quem está abrindo o primeiro consultório e precisa organizar o básico.</p>
        <hr class="plano-divider">
        <ul class="plano-items">
          <li><span class="plano-check"></span>Agenda ilimitada</li>
          <li><span class="plano-check"></span>Prontuário digital ilimitado</li>
          <li><span class="plano-check"></span>Anamnese estruturada</li>
          <li><span class="plano-check"></span>Controle financeiro básico</li>
        </ul>
        <a href="https://meugestorpsic.com.br/cadastre-se" class="btn-plano-outline" style="display:block;text-align:center;text-decoration:none;line-height:2.4">Começar grátis</a>
      </div>
      <!-- CONSULT -->
      <div class="plano-card destaque">
        <div class="plano-popular">Mais popular</div>
        <div class="plano-nome">CONSULT + Sistema</div>
        <div class="plano-preco"><sup>R$</sup>59<span>,90/mês</span></div>
        <p class="plano-desc">Mensal · Sistema completo + consultoria de posicionamento para decolar na carreira.</p>
        <hr class="plano-divider">
        <ul class="plano-items">
          <li><span class="plano-check"></span>Agenda ilimitada</li>
          <li><span class="plano-check"></span>Prontuário digital ilimitado</li>
          <li><span class="plano-check"></span>Anamnese estruturada</li>
          <li><span class="plano-check"></span>Controle financeiro básico</li>
          <li><span class="plano-check"></span>Consultoria de posicionamento</li>
        </ul>
        <a href="https://meugestorpsic.com.br/cadastre-se" class="btn-plano-solid" style="display:block;text-align:center;text-decoration:none;line-height:2.4">Assinar agora</a>
      </div>
      <!-- ULTRA -->
      <div class="plano-card">
        <div class="plano-nome">ULTRA</div>
        <div class="plano-preco"><sup>R$</sup>449<span>,90/mês</span></div>
        <p class="plano-desc">Sistema + CONSULT + Psicóloga para Psi. O pacote completo para quem quer crescer com suporte especializado.</p>
        <hr class="plano-divider">
        <ul class="plano-items">
          <li><span class="plano-check"></span>Agenda ilimitada</li>
          <li><span class="plano-check"></span>Prontuário digital ilimitado</li>
          <li><span class="plano-check"></span>Anamnese estruturada</li>
          <li><span class="plano-check"></span>Controle financeiro básico</li>
          <li><span class="plano-check"></span>Consultoria de posicionamento</li>
          <li><span class="plano-check"></span>Atendimento com psicóloga</li>
        </ul>
        <a href="https://meugestorpsic.com.br/faca-parte" class="btn-plano-outline" style="display:block;text-align:center;text-decoration:none;line-height:2.4">Quero o Ultra</a>
      </div>
    </div>
  </div>
</div>

<!-- FAQ -->
<div class="faq-bg" id="faq">
  <div class="section">
    <div class="section-header" style="text-align:center">
      <div class="section-label" style="justify-content:center">Dúvidas</div>
      <h2 class="section-title" style="text-align:center">Perguntas que a<br>gente <em>ouve sempre</em></h2>
    </div>
    <div class="faq-wrap">
      <div class="faq-item">
        <button class="faq-q" onclick="toggleFaq(this)">Preciso ser formado há quanto tempo para usar?<span class="faq-q-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button>
        <div class="faq-a">Não há exigência de tempo de formação. O sistema foi pensado para quem está no começo — recém-formados, pós-graduandos ou quem está abrindo o primeiro consultório.</div>
      </div>
      <div class="faq-item">
        <button class="faq-q" onclick="toggleFaq(this)">Preciso de cartão de crédito para o teste?<span class="faq-q-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button>
        <div class="faq-a">Não. Você tem 10 dias gratuitos para testar tudo sem compromisso e sem precisar cadastrar nenhum dado de pagamento.</div>
      </div>
      <div class="faq-item">
        <button class="faq-q" onclick="toggleFaq(this)">Os dados dos meus pacientes estão seguros?<span class="faq-q-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button>
        <div class="faq-a">Sim. Todos os dados são armazenados com criptografia e o sistema segue as diretrizes da LGPD e as resoluções do CFP sobre sigilo profissional. Apenas você tem acesso aos registros clínicos.</div>
      </div>
      <div class="faq-item">
        <button class="faq-q" onclick="toggleFaq(this)">O sistema é aprovado pelo CFP?<span class="faq-q-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button>
        <div class="faq-a">O MeuGestorPsic segue as resoluções do CFP sobre uso de tecnologia, sigilo e prontuários digitais. Os registros atendem às exigências éticas da profissão.</div>
      </div>
      <div class="faq-item">
        <button class="faq-q" onclick="toggleFaq(this)">Posso cancelar a qualquer momento?<span class="faq-q-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button>
        <div class="faq-a">Sim, sem multa e sem burocracia. Você cancela quando quiser pelo painel e seu acesso continua ativo até o fim do período pago.</div>
      </div>
      <div class="faq-item">
        <button class="faq-q" onclick="toggleFaq(this)">Como funciona a consultoria de posicionamento?<span class="faq-q-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span></button>
        <div class="faq-a">Nos planos CONSULT e ULTRA, você tem acesso a orientações práticas sobre como definir seu nicho, precificar seus atendimentos e construir presença profissional — além de acesso a psicólogos experientes para tirar dúvidas de carreira.</div>
      </div>
    </div>
  </div>
</div>

<!-- CTA FINAL -->
<div class="cta-final">
  <div class="cta-logo-mark">
    <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M8 62 L8 22 L28 48 L40 30 L52 48 L72 22 L72 62" stroke="rgba(255,255,255,0.6)" stroke-width="9" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
      <path d="M8 22 Q24 10 40 30 Q56 50 72 22" stroke="#3ec97a" stroke-width="5" stroke-linecap="round" fill="none"/>
      <text x="32" y="68" font-size="14" fill="rgba(255,255,255,0.3)" font-family="serif">Ψ</text>
    </svg>
  </div>
  <h2>Comece sua carreira<br><em>com o pé direito.</em></h2>
  <p>10 dias grátis. Sem cartão de crédito. Configure em menos de 10 minutos.</p>
  <a href="{{route('form.cadastro')}}" class="btn-cta-final btn-pulse"><i class="bi bi-rocket-takeoff"></i> Criar minha conta grátis — 10 dias sem custo</a>
  <p class="cta-disclaimer">Já utilizado por mais de 2.000 psicólogos em todo o Brasil</p>
</div>
@endsection