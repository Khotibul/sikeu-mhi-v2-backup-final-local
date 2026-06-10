@php
    $faviconFile = 'images/logo-mhi.png';
    $faviconPath = public_path($faviconFile);
    $faviconVersion = file_exists($faviconPath) ? filemtime($faviconPath) : time();
    $faviconUrl = asset($faviconFile) . '?v=' . $faviconVersion;
@endphp

<link rel="icon" type="image/png" href="{{ $faviconUrl }}">
<link rel="shortcut icon" type="image/png" href="{{ $faviconUrl }}">
<link rel="apple-touch-icon" href="{{ $faviconUrl }}">
<meta name="theme-color" content="#0f766e">
