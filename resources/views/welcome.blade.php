<!doctype html>
<html lang="id" data-theme="light" data-theme-ready="true">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>MataTinta</title>
@vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="page-grain">
<script>
  // Runs before paint. Gating class + persisted theme, no flash.
  try {
    if (localStorage.getItem('mt-dark') === '1') document.documentElement.setAttribute('data-theme', 'dark');
  } catch (e) {}
  document.documentElement.setAttribute('data-theme-ready', 'true');
</script>
<a class="skip-link" href="#konten-utama">Lewati ke konten</a>
</body>
</html>
