<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terima Kasih · {{ $applicationName }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --forest: #176246; --forest-dark: #104936; --mint: #eaf7ef; --ink: #19372d; --muted: #71857c; }
        body { min-height: 100vh; background: linear-gradient(135deg, #f5faf7, #edf7f0); color: var(--ink); }
        .page-shell { max-width: 760px; margin: 0 auto; padding: 1.5rem 1rem 4rem; }
        .topbar { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 2.5rem; }
        .brand { display: flex; min-width: 0; align-items: center; gap: .65rem; color: var(--forest); }
        .brand i { display: inline-flex; width: 36px; height: 36px; flex: 0 0 36px; align-items: center; justify-content: center; border-radius: 11px; color: #fff; background: var(--forest); box-shadow: 0 5px 12px rgba(23, 98, 70, .16); }
        .brand-copy { min-width: 0; }
        .brand-name { display: block; font-size: 1.05rem; font-weight: 800; letter-spacing: -.03em; line-height: 1.15; }
        .brand-tagline { display: block; margin-top: .2rem; overflow: hidden; color: var(--muted); font-size: .7rem; line-height: 1.2; text-overflow: ellipsis; white-space: nowrap; }
        .token-pill { padding: .55rem .8rem; border: 1px solid #d7eadf; border-radius: 999px; color: var(--forest); background: #fff; font-size: .72rem; font-weight: 700; }
        .thanks-card { padding: clamp(2rem, 6vw, 4rem) clamp(1.25rem, 6vw, 4rem); border: 1px solid #deece3; border-radius: 24px; background: #fff; box-shadow: 0 18px 45px rgba(30, 76, 57, .09); text-align: center; }
        .thanks-icon { display: inline-flex; width: 76px; height: 76px; align-items: center; justify-content: center; margin-bottom: 1.5rem; border-radius: 24px; color: #fff; background: var(--forest); box-shadow: 0 12px 24px rgba(23, 98, 70, .2); font-size: 2.1rem; }
        .thanks-card h1 { margin-bottom: .75rem; color: var(--ink); font-size: clamp(1.7rem, 4vw, 2.35rem); font-weight: 800; letter-spacing: -.04em; }
        .thanks-card > p { max-width: 500px; margin: 0 auto; color: var(--muted); font-size: .9rem; line-height: 1.7; }
        .summary { display: flex; justify-content: center; gap: .75rem; margin: 1.75rem 0; }
        .summary-item { min-width: 130px; padding: .85rem 1rem; border-radius: 14px; background: var(--mint); }
        .summary-item small { display: block; color: var(--muted); font-size: .68rem; }
        .summary-item strong { display: block; margin-top: .2rem; color: var(--forest); font-size: .9rem; }
        .btn-continue { padding: .75rem 1.1rem; border: 0; border-radius: 10px; color: #fff; background: var(--forest); font-size: .82rem; font-weight: 700; }
        .btn-continue:hover { color: #fff; background: var(--forest-dark); }
        @media (max-width: 520px) { .topbar { align-items: flex-start; } .token-pill { max-width: 42%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; } .summary { flex-direction: column; } }
    </style>
</head>
<body>
<main class="page-shell">
    <header class="topbar">
        <div class="brand"><i class="bi bi-tree-fill"></i><span class="brand-copy"><span class="brand-name">{{ $applicationName }}</span><span class="brand-tagline">{{ $applicationTagline }}</span></span></div>
        <span class="token-pill"><i class="bi bi-shield-check me-1"></i>{{ $accessToken->label }}</span>
    </header>

    <section class="thanks-card">
        <div class="thanks-icon"><i class="bi bi-check-lg"></i></div>
        <h1>Terima kasih telah berpartisipasi!</h1>
        <p>Data penanaman Anda berhasil dicatat. Kontribusi ini menjadi bagian dari gerakan penghijauan dan upaya menjaga lingkungan bersama.</p>
        <div class="summary">
            <div class="summary-item"><small>Nama peserta</small><strong>{{ $plantingSummary['name'] }}</strong></div>
            <div class="summary-item"><small>Jumlah pohon</small><strong>{{ $plantingSummary['treeCount'] }} pohon</strong></div>
            <div class="summary-item"><small>Jenis tanaman</small><strong>{{ $plantingSummary['plantType'] }}</strong></div>
        </div>
        <a href="{{ route('planting.form', ['token' => request()->route('token')]) }}" class="btn btn-continue"><i class="bi bi-plus-circle me-1"></i>Catat kontribusi lain</a>
    </section>
</main>
</body>
</html>
