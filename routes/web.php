<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('news.home');
})->name('home');

Route::get('/berita/{slug}', function (string $slug) {
    $articles = include resource_path('views/news/_articles.php');
    $article = collect($articles)->firstWhere('slug', $slug);
    abort_unless($article, 404);

    $all = collect($articles);
    $related = $all->where('category', $article['category'])
        ->where('slug', '!=', $slug)
        ->take(4)
        ->values();
    $latest = $all->where('slug', '!=', $slug)->sortByDesc('publishedAt')->take(5)->values();

    $carbon = fn (string $iso) => \Illuminate\Support\Carbon::parse($iso);
    $dateline = strtoupper($article['city']).', MATATINTA — '.$carbon($article['publishedAt'])->format('d/m/Y');
    $img = 'https://picsum.photos/seed/'.md5($article['slug']).'/1200/675';
    $avatar = 'https://i.pravatar.cc/96?u='.urlencode($article['author']);

    return view('news.article', compact('article', 'related', 'latest', 'dateline', 'img', 'avatar', 'carbon'));
})->name('article.show');
