<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CheColore — Accedi</title>
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <style>
        :root {
            --rosso: #ab3324;
            --rosso-dark: #862719;
            --ink: #202a3b;
            --muted: #4b5563;
            --bg: #fffdf8;
            --card: #ffffff;
            --border: #e1dbc7;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, "Segoe UI", "Helvetica Neue", Arial, sans-serif;
            background: var(--bg);
            color: var(--ink);
            padding: 16px;
        }
        .panel {
            width: 100%;
            max-width: 360px;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 32px 28px;
            box-shadow: 0 8px 28px rgba(32, 42, 59, 0.08);
        }
        .brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            margin-bottom: 24px;
        }
        .swatches { display: flex; gap: 4px; margin-bottom: 4px; }
        .dot { width: 12px; height: 12px; border-radius: 50%; }
        .dot.rosso { background: #c1503f; }
        .dot.verde { background: #3e7f5b; }
        .dot.bianco { background: #fbfaf6; border: 1.5px solid var(--ink); }
        .dot.blu { background: #2e5e8c; }
        .dot.arancio { background: #d98a34; }
        .brand h1 { margin: 0; font-size: 1.25rem; }
        .brand p { margin: 0; color: var(--muted); font-size: 0.85rem; }

        label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; }
        input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 0.95rem;
            margin-bottom: 16px;
            font-family: inherit;
            background: var(--bg);
            color: var(--ink);
        }
        input:focus { outline: none; border-color: var(--rosso); }

        button {
            width: 100%;
            padding: 11px;
            border: none;
            border-radius: 8px;
            background: var(--rosso);
            color: #fff;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: background 120ms ease;
        }
        button:hover { background: var(--rosso-dark); }

        .error {
            background: rgba(171, 51, 36, 0.08);
            color: var(--rosso-dark);
            border: 1px solid rgba(171, 51, 36, 0.25);
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 0.85rem;
            margin-bottom: 16px;
        }

        .foot { display: flex; justify-content: space-between; margin-top: 18px; font-size: 0.85rem; }
        .foot a { color: var(--muted); text-decoration: none; }
        .foot a:hover { color: var(--rosso); }

        @media (prefers-color-scheme: dark) {
            :root {
                --ink: #ede9dd;
                --muted: #c7cfc4;
                --bg: #1b2420;
                --card: #232f29;
                --border: #3a483f;
            }
        }
    </style>
</head>
<body>
    <div class="panel">
        <div class="brand">
            <div class="swatches">
                <span class="dot rosso"></span><span class="dot verde"></span><span class="dot bianco"></span><span class="dot blu"></span><span class="dot arancio"></span>
            </div>
            <h1>CheColore</h1>
            <p>Accedi al tuo account</p>
        </div>

        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <a href="/auth/google" style="display:flex; align-items:center; justify-content:center; gap:0.6rem; background:#fff; color:#1f1f1f; border:1px solid var(--border); border-radius:8px; padding:11px; font-weight:600; font-size:0.95rem; margin-bottom:16px; text-decoration:none;">
            <svg width="18" height="18" viewBox="0 0 18 18"><path fill="#4285F4" d="M17.64 9.2c0-.64-.06-1.25-.16-1.84H9v3.48h4.84a4.14 4.14 0 0 1-1.8 2.72v2.26h2.9c1.7-1.57 2.7-3.88 2.7-6.62z"/><path fill="#34A853" d="M9 18c2.43 0 4.47-.8 5.96-2.18l-2.9-2.26c-.8.54-1.84.86-3.06.86-2.35 0-4.34-1.59-5.05-3.72H.98v2.33A9 9 0 0 0 9 18z"/><path fill="#FBBC05" d="M3.95 10.7A5.4 5.4 0 0 1 3.67 9c0-.59.1-1.16.28-1.7V4.97H.98A9 9 0 0 0 0 9c0 1.45.35 2.83.98 4.03l2.97-2.33z"/><path fill="#EA4335" d="M9 3.58c1.32 0 2.51.46 3.44 1.35l2.58-2.58C13.46.89 11.43 0 9 0A9 9 0 0 0 .98 4.97L3.95 7.3C4.66 5.17 6.65 3.58 9 3.58z"/></svg>
            Continua con Google
        </a>

        <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:16px; color:var(--muted); font-size:0.8rem;">
            <div style="flex:1; height:1px; background:var(--border);"></div>
            oppure
            <div style="flex:1; height:1px; background:var(--border);"></div>
        </div>

        <form method="POST" action="/login">
            @csrf
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" autofocus autocomplete="username">

            <label for="password">Password</label>
            <input type="password" id="password" name="password" autocomplete="current-password">

            <button type="submit">Accedi</button>
        </form>

        <div class="foot">
            <a href="/">&larr; Torna alla home</a>
            <a href="/register">Crea un account</a>
        </div>
    </div>
</body>
</html>
