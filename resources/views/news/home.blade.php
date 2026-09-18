@php
$articles = include resource_path('views/news/_articles.php');
$byCategory = collect($articles)->groupBy('category');
$featured   = collect($articles)->firstWhere('isFeatured', true);
$heroSide   = collect($articles)->where('isFeatured', false)->where('isTrending', '<=', 5)->take(3)->values();
$trending   = collect($articles)->where('isTrending', '!=', null)->sortBy('isTrending')->take(5)->values();
$latest     = collect($articles)->sortByDesc('publishedAt')->take(6)->values();
$opini      = collect($articles)->where('isOpinion', true)->take(3)->values();
$brief      = collect($articles)->sortByDesc('publishedAt')->take(4)->values();
$longform   = $articles[6];
$tabCategories = ['Ekonomi', 'Tekno', 'Regional', 'Global', 'Travel', 'Olahraga'];
$tabs = $tabCategories; // Travel has 0 — render empty state
$navCats = ['Terkini','Nasional','Politik','Ekonomi','Regional','Global','Tekno','Sains','Kesehatan','Pendidikan','Lingkungan','Travel','Olahraga','Opini'];
$fmt = fn(string $iso) => \Illuminate\Support\Carbon::parse($iso)->startOfDay()->diffForHumans(null, true);
$dateline = fn(array $a) => strtoupper($a['city']).', MATATINTA — '.\Illuminate\Support\Carbon::parse($a['publishedAt'])->format('d/m/Y');
$slug = fn(array $a) => '/berita/'.$a['slug'];
$seed = fn(string $s) => (int) crc32($s) % 12;
$img = fn(array $a) => 'https://picsum.photos/seed/'.md5($a['slug']).'/800/500';
$avatar = fn(string $name) => 'https://i.pravatar.cc/96?u='.urlencode($name);
@endphp
<!doctype html>
<html lang="id" data-theme="light" data-theme-ready="true">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="MataTinta — portal berita Indonesia dan dunia. Politik, ekonomi, teknologi, regional, opini, dan sains dengan standar redaksi yang ketat.">
<title>MataTinta — Berita Terkini, Kabar Akurat, Redaksi Tajam</title>
<link rel="icon" href="/favicon.ico">
@vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="page-grain">
<a class="skip-link" href="#konten-utama">Lewati ke konten</a>

{{-- 1. Utility bar --}}
<div class="utilbar">
  <div class="shell utilbar-inner">
    <div class="util-left">
      <span class="util-group">
        <span class="util-label">Tema</span>
        <button class="icon-btn" type="button" data-theme-toggle aria-pressed="false" aria-label="Ganti tema gelap/terang" title="Ganti tema">
          <svg class="icon-sun" viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
          <svg class="icon-moon" viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" aria-hidden="true"><path d="M20 14.2A8.2 8.2 0 0 1 9.8 4a8.5 8.5 0 1 0 10.2 10.2Z"/></svg>
        </button>
      </span>
      <span class="util-live" aria-label="Status siaran langsung">
        <span class="live-dot" aria-hidden="true"></span> LANGSUNG · <span class="util-mono js-clock" data-now="{{ now()->toIso8601String() }}">--:--</span> WIB
      </span>
    </div>
    <div class="util-right">
      <span class="util-group util-mono"><span class="util-label">Jakarta</span> 31°C Cerah</span>
      <span class="util-group util-mono"><span class="util-label">Emas</span> 1.72M /gr</span>
      <a class="util-link--strong" href="#newsletter">Langganan MataTinta+</a>
    </div>
  </div>
</div>

{{-- 2. Masthead --}}
<header class="site-header">
  <div class="shell header-inner">
    <a class="brand" href="/" aria-label="MataTinta, beranda">
      <span class="brand-mark">Mata<em>Tinta</em></span>
      <span class="brand-tagline">Redaksi Independen · Jakarta</span>
    </a>
    <div class="search" role="search">
      <form class="search-field" action="{{ route('home') }}" method="get" onsubmit="return false;">
        <label class="sr-only" for="site-search">Cari berita</label>
        <input class="search-input" id="site-search" type="search" name="q" placeholder="Cari topik, nama, kota…">
        <button type="button" class="sr-only" data-search-clear hidden>Bersihkan</button>
        <button class="search-submit" type="submit">Cari</button>
      </form>
    </div>
    <div class="header-actions">
      <a class="ink-link ink-link--hide" href="#kolom">Kolom</a>
      <a class="ink-link" href="#masuk">Masuk</a>
      <button class="nav-toggle" type="button" data-nav-toggle aria-expanded="false" aria-controls="navigasi-mobile">Menu</button>
    </div>
  </div>
</header>

{{-- 3. Primary nav (sticky) --}}
<nav class="mainnav" id="navigasi-mobile" aria-label="Navigasi utama" hidden>
  <div class="shell mainnav-inner">
    @foreach($navCats as $c)
      <a class="nav-trigger {{ $loop->first ? '' : '' }}" href="{{ $loop->first ? '#' : '#'.mb_strtolower($c) }}" {{ $loop->first ? 'aria-current="page"' : '' }}>{{ $c }}</a>
    @endforeach
  </div>
</nav>
@push('sticky')
<nav class="mainnav" aria-label="Navigasi kategori" id="nav-sticky" style="position:sticky;top:0;z-index:50;">
  <div class="shell mainnav-inner">
    @foreach($navCats as $c)
      <a class="nav-trigger" href="{{ $loop->first ? '#' : '#'.mb_strtolower($c) }}" {{ $loop->first ? 'aria-current="page"' : '' }}>{{ $c }}</a>
    @endforeach
  </div>
</nav>
@endpush

{{-- 4. Breaking ticker --}}
<div class="ticker" data-paused="0" aria-label="Berita terkini berjalan">
  <div class="shell ticker-inner">
    <span class="ticker-label"><span class="live-dot" aria-hidden="true"></span> Terkini</span>
    <div class="ticker-viewport">
      <div class="ticker-track" aria-live="polite">
        @foreach($brief as $b)
          <a class="ticker-item searchable" href="{{ $slug($b) }}"><time datetime="{{ $b['publishedAt'] }}">{{ $fmt($b['publishedAt']) }}</time> {{ $b['title'] }}</a>
        @endforeach
        @foreach($brief as $b)
          <a class="ticker-item" href="{{ $slug($b) }}" aria-hidden="true" tabindex="-1"><time>{{ $fmt($b['publishedAt']) }}</time> {{ $b['title'] }}</a>
        @endforeach
      </div>
    </div>
    <button class="ticker-btn" type="button" data-ticker-toggle aria-pressed="false" aria-label="Jeda atau lanjutkan ticker">
      <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" aria-hidden="true"><rect x="6" y="5" width="4" height="14"/><rect x="14" y="5" width="4" height="14"/></svg>
    </button>
  </div>
</div>

<main class="shell" id="konten-utama" data-section="newsfront">
  <div class="layout">

    {{-- LEFT 7fr --}}
    <div class="layout-main">

      {{-- 5. Hero / headline board --}}
      <section class="hero searchable" aria-label="Berita utama" data-reveal>
        <article class="hero-main">
          <span class="badge">{{ $featured['category'] }}</span>
          <figure class="hero-figure">
            <img src="{{ $img($featured) }}" alt="{{ $featured['title'] }}" width="1200" height="675" fetchpriority="high">
          </figure>
          <h1 class="hero-title"><a href="{{ $slug($featured) }}">{{ $featured['title'] }}</a></h1>
          <p class="hero-standfirst">{{ $featured['excerpt'] }}</p>
          <p class="dateline"><strong>{{ $dateline($featured) }}</strong> · {{ $featured['author'] }}, {{ $featured['role'] }} · {{ $featured['readTime'] }} mnt baca · {{ $featured['credit'] }}</p>
        </article>
        <div class="hero-side">
          @foreach($heroSide as $a)
          <article class="hero-side-item searchable">
            <figure><img src="{{ $img($a) }}" alt="{{ $a['title'] }}" loading="lazy"></figure>
            <div>
              <span class="badge">{{ $a['category'] }}</span>
              <h2 class="hero-side-title" style="margin-top:6px;"><a href="{{ $slug($a) }}">{{ $a['title'] }}</a></h2>
              <p class="dateline" style="margin-top:6px;">{{ $dateline($a) }}</p>
            </div>
          </article>
          @endforeach
        </div>
      </section>

      {{-- 7. Brief update / digest --}}
      <section class="brief" aria-label="Ringkasan redaksi" data-reveal>
        <div class="brief-head">
          <span class="brief-title">Brief Update — Ringkasan Redaksi</span>
          <span class="util-mono">{{ now()->format('d M Y') }}</span>
        </div>
        <div class="brief-list">
          @foreach($brief as $b)
            <div class="brief-item searchable">
              <span class="brief-cat">{{ $b['category'] }}</span>
              <a href="{{ $slug($b) }}">{{ $b['title'] }}</a>
            </div>
          @endforeach
        </div>
      </section>

      {{-- 8. Berita Terkini grid --}}
      <section aria-labelledby="lbl-terkini" data-reveal>
        <div class="section-head">
          <div>
            <span class="section-kicker">Rubrik Utama</span>
            <h2 class="section-title" id="lbl-terkini">Berita Terkini</h2>
          </div>
          <a class="section-link" href="#terkini">Lihat semua →</a>
        </div>
        <div class="grid3" id="terkini">
          @foreach($latest as $a)
            <article class="card searchable">
              <figure><img src="{{ $img($a) }}" alt="{{ $a['title'] }}" loading="lazy"></figure>
              <span class="badge">{{ $a['category'] }}</span>
              <h3 class="card-title"><a href="{{ $slug($a) }}">{{ $a['title'] }}</a></h3>
              <p class="card-excerpt">{{ $a['excerpt'] }}</p>
              <p class="card-meta">{{ $dateline($a) }} · {{ $a['readTime'] }} mnt</p>
            </article>
          @endforeach
        </div>
      </section>

      {{-- 10. Sorotan --}}
      <section class="sorotan" aria-label="Sorotan redaksi" data-reveal>
        <div class="section-head" style="border-bottom-color: var(--hairline-solid);">
          <div>
            <span class="section-kicker">Sorotan</span>
            <h2 class="section-title">Utama Pukul Ini</h2>
          </div>
        </div>
        <div class="sorotan-grid">
          @foreach(collect($articles)->where('isFeatured', true)->take(2)->values() as $a)
            <article class="sorotan-item searchable">
              <figure style="overflow:hidden;border:1px solid var(--hairline-solid);">
                <img src="{{ $img($a) }}" alt="{{ $a['title'] }}" loading="lazy">
              </figure>
              <span class="badge">{{ $a['category'] }}</span>
              <h3 class="sorotan-title"><a href="{{ $slug($a) }}">{{ $a['title'] }}</a></h3>
              <p class="card-excerpt">{{ $a['excerpt'] }}</p>
              <p class="dateline">{{ $dateline($a) }}</p>
            </article>
          @endforeach
        </div>
      </section>

      {{-- 11. Category tabs --}}
      <section aria-label="Kategori" data-reveal id="kategori">
        <div class="tabs" role="tablist" aria-label="Kategori berita">
          @foreach($tabs as $i => $t)
            <button class="tab" role="tab" aria-selected="{{ $i === 0 ? 'true' : 'false' }}" aria-controls="tabpanel-{{ $i }}" id="tab-{{ $i }}">{{ $t }}</button>
          @endforeach
        </div>
        @foreach($tabs as $i => $t)
          <div class="tab-panel" role="tabpanel" id="tabpanel-{{ $i }}" aria-labelledby="tab-{{ $i }}">
            @php($items = $byCategory->get($t, collect()))
            @if($items->isNotEmpty())
              <div class="grid3" style="margin-bottom:0;">
                @foreach($items->take(3) as $a)
                  <article class="card">
                    <figure><img src="{{ $img($a) }}" alt="{{ $a['title'] }}" loading="lazy"></figure>
                    <span class="badge">{{ $a['category'] }}</span>
                    <h3 class="card-title"><a href="{{ $slug($a) }}">{{ $a['title'] }}</a></h3>
                    <p class="card-excerpt">{{ $a['excerpt'] }}</p>
                    <p class="card-meta">{{ $dateline($a) }}</p>
                  </article>
                @endforeach
              </div>
            @else
              <p class="dateline" style="padding:14px 0;">Belum ada berita terbaru untuk kategori {{ $t }}. Redaksi akan segera memperbarui.</p>
            @endif
          </div>
        @endforeach
      </section>

      {{-- 12. Opini & kolumnis --}}
      <section aria-labelledby="lbl-opini" id="kolom" data-reveal style="margin-bottom:40px;">
        <div class="section-head">
          <div>
            <span class="section-kicker">Suara Redaksi</span>
            <h2 class="section-title" id="lbl-opini">Opini &amp; Kolumnis</h2>
          </div>
          <a class="section-link" href="#opini">Semua opini →</a>
        </div>
        <div class="opini" id="opini">
          @foreach($opini as $o)
            <article class="opini-item">
              <img class="avatar" src="{{ $avatar($o['author']) }}" alt="Foto {{ $o['author'] }}" width="46" height="46" loading="lazy">
              <div>
                <p class="opini-name">{{ $o['author'] }}</p>
                <p class="opini-role">{{ $o['role'] }}</p>
                <h3 class="opini-title" style="margin-top:6px;"><a href="{{ $slug($o) }}">{{ $o['title'] }}</a></h3>
                <p class="dateline" style="margin-top:6px;">{{ $dateline($o) }}</p>
              </div>
            </article>
          @endforeach
        </div>
      </section>

      {{-- 14. Long-form --}}
      <section class="longform" aria-label="Bacaan panjang" data-reveal>
        <div>
          <span class="section-kicker">Fitur</span>
          <h2 class="longform-title" style="margin:6px 0 12px;"><a href="{{ $slug($longform) }}">{{ $longform['title'] }}</a></h2>
          <div class="longform-body">
            <p class="dropcap">{{ $longform['excerpt'] }} {{ $longform['body'] }}</p>
            <blockquote class="pullquote">"Kebijakan yang baik diukur dari dampaknya pada petani dan pedagang, bukan dari angka makro di atas kertas."</blockquote>
          </div>
          <p class="dateline" style="margin-top:12px;"><strong>{{ $dateline($longform) }}</strong> · {{ $longform['author'] }}, {{ $longform['role'] }} · {{ $longform['credit'] }}</p>
        </div>
        <figure>
          <img src="{{ $img($longform) }}" alt="{{ $longform['title'] }}" loading="lazy" style="width:100%;border:1px solid var(--hairline-solid);">
          <figcaption class="dateline" style="margin-top:8px;">{{ $longform['credit'] }} — Ilustrasi untuk artikel ini.</figcaption>
        </figure>
      </section>

      {{-- 15. Newsletter --}}
      <section class="newsletter" id="newsletter" aria-labelledby="lbl-nl" data-reveal>
        <h2 id="lbl-nl">Rangkuman Harian MataTinta</h2>
        <p>Satu email setiap pagi. Enam berita terpenting, diringkas redaksi. Tanpa spam.</p>
        <form class="newsletter-form" id="newsletter-form" novalidate>
          <label class="sr-only" for="nl-email">Email</label>
          <input class="newsletter-input" id="nl-email" type="email" name="email" placeholder="nama@email.com" required>
          <button class="button" type="submit">Daftar</button>
        </form>
      </section>

    </div>

    {{-- RIGHT 3fr: sidebar rail --}}
    <aside class="rail" aria-label="Rail editorial">

      <div class="rail-box" data-reveal>
        <h2 class="rail-title">Terpopuler</h2>
        <ol class="rank-list" style="list-style:none;padding:0;margin:0;">
          @foreach($trending as $t)
            <li class="rank-item">
              <span class="rank-num" aria-hidden="true"></span>
              <div class="rank-title searchable">
                <a href="{{ $slug($t) }}">{{ $t['title'] }}</a>
                <p class="dateline" style="margin-top:4px;">{{ $t['category'] }} · {{ $fmt($t['publishedAt']) }}</p>
              </div>
            </li>
          @endforeach
        </ol>
      </div>

      <div class="rail-box" data-reveal>
        <h2 class="rail-title">Kilas</h2>
        @foreach(collect($articles)->sortByDesc('publishedAt')->take(6) as $k)
          <div class="rail-item searchable">
            <a href="{{ $slug($k) }}">{{ $k['title'] }}</a>
            <time datetime="{{ $k['publishedAt'] }}">{{ $fmt($k['publishedAt']) }}</time>
          </div>
        @endforeach
      </div>

      <div class="rail-box" data-reveal>
        <h2 class="rail-title">Tag Terpopuler</h2>
        <p class="dateline" style="font-size:13px;">
          @foreach(collect($articles)->pluck('tags')->flatten()->countBy()->sortDesc()->keys()->take(8) as $tag)
            <a href="#" class="badge" style="margin:0 4px 6px 0;cursor:pointer;position:relative;">{{ $tag }}</a>
          @endforeach
        </p>
      </div>

    </aside>
  </div>
</main>

{{-- 16. Footer --}}
<footer class="site-footer" role="contentinfo">
  <div class="shell footer-grid">
    <div>
      <span class="brand-mark" style="font-size:24px;">Mata<em>Tinta</em></span>
      <p class="footer-desc">Portal berita independen dengan standar redaksi yang ketat: verifikasi ganda, transparansi koreksi, dan data yang dapat ditelusuri.</p>
    </div>
    <nav class="footer-col" aria-label="Kanal">
      <h4>Kanal</h4>
      @foreach(['Nasional','Politik','Ekonomi','Regional','Global','Tekno','Kesehatan','Pendidikan','Lingkungan','Olahraga'] as $c)
        <a href="#{{ mb_strtolower($c) }}">{{ $c }}</a>
      @endforeach
    </nav>
    <nav class="footer-col" aria-label="Layanan">
      <h4>Layanan</h4>
      <a href="#newsletter">MataTinta Plus</a>
      <a href="#">MataTinta TV</a>
      <a href="#">Newsletter</a>
      <a href="#">Aplikasi</a>
      <a href="#">RSS / Feeds</a>
    </nav>
    <nav class="footer-col" aria-label="Redaksi dan legal">
      <h4>Redaksi &amp; Legal</h4>
      <a href="#">Tentang Kami</a>
      <a href="#">Redaksi</a>
      <a href="#">Pedoman Media Siber</a>
      <a href="#">Kebijakan Privasi</a>
      <a href="#">Syarat &amp; Ketentuan</a>
      <a href="#">Kontak</a>
    </nav>
  </div>
  <div class="shell footer-bottom">
    <span>&copy; {{ date('Y') }} MataTinta Media — Hak cipta dilindungi.</span>
    <div class="footer-bottom-links">
      <a href="#">Kode Etik</a>
      <a href="#">Karir</a>
      <a href="#">Iklan</a>
    </div>
  </div>
</footer>

</body>
</html>
