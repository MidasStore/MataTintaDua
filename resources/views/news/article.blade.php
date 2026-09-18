@php
$slugUrl = fn(array $a) => '/berita/'.$a['slug'];
$fmt = fn(\Illuminate\Support\Carbon $c) => $c->startOfDay()->diffForHumans(null, true);
$articleImg = fn(array $a) => 'https://picsum.photos/seed/'.md5($a['slug']).'/800/500';
@endphp
<!doctype html>
<html lang="id" data-theme="light" data-theme-ready="true">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="{{ $article['excerpt'] }}">
<title>{{ $article['title'] }} — MataTinta</title>
<link rel="icon" href="/favicon.ico">
@vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="page-grain">
<a class="skip-link" href="#konten-utama">Lewati ke konten</a>

<div class="utilbar">
  <div class="shell utilbar-inner">
    <div class="util-left">
      <span class="util-group">
        <span class="util-label">Tema</span>
        <button class="icon-btn" type="button" data-theme-toggle aria-pressed="false" aria-label="Ganti tema gelap/terang">
          <svg class="icon-sun" viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
          <svg class="icon-moon" viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" aria-hidden="true"><path d="M20 14.2A8.2 8.2 0 0 1 9.8 4a8.5 8.5 0 1 0 10.2 10.2Z"/></svg>
        </button>
      </span>
      <span class="util-live"><span class="live-dot" aria-hidden="true"></span> LANGSUNG · <span class="util-mono">{{ now()->format('H:i') }}</span> WIB</span>
    </div>
    <div class="util-right">
      <a class="util-link--strong" href="/#newsletter">Langganan MataTinta+</a>
    </div>
  </div>
</div>

<header class="site-header">
  <div class="shell header-inner">
    <a class="brand" href="/" aria-label="MataTinta, beranda">
      <span class="brand-mark">Mata<em>Tinta</em></span>
      <span class="brand-tagline">Redaksi Independen · Jakarta</span>
    </a>
    <div class="header-actions">
      <a class="ink-link" href="/#kolom">Kolom</a>
      <a class="ink-link" href="#masuk">Masuk</a>
    </div>
  </div>
</header>

<nav class="mainnav" aria-label="Navigasi kategori">
  <div class="shell mainnav-inner">
    @foreach(['Terkini','Nasional','Politik','Ekonomi','Regional','Global','Tekno','Sains','Kesehatan','Pendidikan','Lingkungan','Travel','Olahraga','Opini'] as $i => $c)
      <a class="nav-trigger" href="/" {{ $i === 0 ? 'aria-current="page"' : '' }}>{{ $c }}</a>
    @endforeach
  </div>
</nav>

<main class="shell" id="konten-utama">
  <div style="max-width:760px;margin-inline:auto;padding:32px 0 56px;">

    {{-- Breadcrumb --}}
    <nav class="dateline" aria-label="Breadcrumb" style="margin-bottom:16px;">
      <a href="/">Beranda</a> › <a href="/#{{ mb_strtolower($article['category']) }}">{{ $article['category'] }}</a> › <span style="color:var(--terracotta-ink);">{{ $article['title'] }}</span>
    </nav>

    <span class="badge">{{ $article['category'] }}</span>
    <h1 style="font-size:clamp(26px,3.2vw,38px);font-weight:600;margin:12px 0 10px;">{{ $article['title'] }}</h1>
    <p style="font-size:17px;color:var(--ink-muted);line-height:1.6;">{{ $article['excerpt'] }}</p>

    {{-- Author + dateline block --}}
    <div style="display:flex;align-items:center;gap:14px;padding:18px 0;margin:18px 0;border-top:1px solid var(--hairline);border-bottom:1px solid var(--hairline);">
      <img class="avatar" src="{{ $avatar }}" alt="Foto {{ $article['author'] }}" width="52" height="52">
      <div>
        <p style="font-family:var(--font-display);font-weight:600;font-size:15px;">{{ $article['author'] }} <span style="font-weight:400;color:var(--ink-muted);font-size:13px;">· {{ $article['role'] }}</span></p>
        <p class="dateline"><strong>{{ $dateline }}</strong> · Diperbarui {{ $fmt($carbon($article['publishedAt'])) }} · {{ $article['readTime'] }} menit baca</p>
      </div>
    </div>

    <figure>
      <img src="{{ $img }}" alt="{{ $article['title'] }}" style="width:100%;border:1px solid var(--hairline);">
      <figcaption class="dateline" style="margin-top:8px;">{{ $article['credit'] }} — Ilustrasi untuk artikel ini.</figcaption>
    </figure>

    <article style="margin-top:22px;">
      <p class="dropcap" style="font-family:var(--font-display);font-size:16px;line-height:1.85;">{{ $article['body'] }}</p>
      @foreach(str_split($article['body'], 300) as $i => $para)
        @if($i > 0)
          <p style="font-family:var(--font-display);font-size:16px;line-height:1.85;margin-top:16px;">{{ $para }}</p>
        @endif
      @endforeach
      <blockquote class="pullquote">"Fakta lebih kuat dari opini; itulah standar yang kami terapkan pada setiap liputan."</blockquote>
      <p style="font-family:var(--font-display);font-size:16px;line-height:1.85;margin-top:16px;">Redaksi MataTinta membuka ruang klarifikasi dari pihak-pihak yang terdampak. Koreksi atas kesalahan yang teridentifikasi akan dipublikasikan di halaman yang sama beserta catatan peninjauannya.</p>
    </article>

    {{-- Share --}}
    <div style="display:flex;gap:10px;flex-wrap:wrap;padding:16px 0;margin-top:22px;border-top:1px solid var(--hairline);">
      <span class="dateline" style="align-self:center;">Bagikan:</span>
      @foreach(['X' => 'https://x.com/intent/tweet?text='.urlencode($article['title']).'&url='.urlencode('https://matatinta.test/berita/'.$article['slug']), 'WhatsApp' => 'https://wa.me/?text='.urlencode($article['title'].' https://matatinta.test/berita/'.$article['slug']), 'Salin tautan' => 'javascript:void(0)'] as $label => $href)
        <a class="badge" style="cursor:pointer;padding:6px 12px;position:relative;" href="{{ $label === 'Salin tautan' ? 'javascript:void(0)' : $href }}" target="_blank" rel="noopener" style2="">{{ $label }}</a>
      @endforeach
    </div>

    {{-- Related --}}
    @if($related->isNotEmpty())
    <section aria-labelledby="lbl-related" style="margin-top:28px;">
      <div class="section-head">
        <div>
          <span class="section-kicker">Terkait</span>
          <h2 class="section-title" id="lbl-related">Bacaan Lanjutan</h2>
        </div>
      </div>
      <div class="grid3" style="grid-template-columns:repeat(2,1fr);">
        @foreach($related as $r)
          <article class="card">
            <figure><img src="{{ $articleImg($r) }}" alt="{{ $r['title'] }}" loading="lazy"></figure>
            <span class="badge">{{ $r['category'] }}</span>
            <h3 class="card-title"><a href="{{ $slugUrl($r) }}">{{ $r['title'] }}</a></h3>
            <p class="card-meta">{{ $r['author'] }} · {{ $r['readTime'] }} mnt</p>
          </article>
        @endforeach
      </div>
    </section>
    @endif

    {{-- Latest --}}
    <section aria-labelledby="lbl-latest" style="margin-top:28px;">
      <div class="section-head">
        <div>
          <span class="section-kicker">Terbaru</span>
          <h2 class="section-title" id="lbl-latest">Kabar Terkini</h2>
        </div>
      </div>
      <ol class="rank-list" style="list-style:none;padding:0;margin:0;">
        @foreach($latest as $l)
          <li class="rank-item">
            <span class="rank-num" aria-hidden="true"></span>
            <div class="rank-title">
              <a href="{{ $slugUrl($l) }}">{{ $l['title'] }}</a>
              <p class="dateline" style="margin-top:4px;">{{ $l['category'] }} · {{ $l['readTime'] }} mnt baca</p>
            </div>
          </li>
        @endforeach
      </ol>
    </section>
  </div>
</main>

<footer class="site-footer" role="contentinfo">
  <div class="shell footer-bottom" style="border-top:0;">
    <span>&copy; {{ date('Y') }} MataTinta Media — Hak cipta dilindungi.</span>
    <div class="footer-bottom-links">
      <a href="#">Kebijakan Privasi</a>
      <a href="#">Pedoman Media Siber</a>
      <a href="#">Kontak</a>
    </div>
  </div>
</footer>
</body>
</html>
