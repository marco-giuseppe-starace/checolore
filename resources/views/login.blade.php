<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CheColore — Accedi</title>
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
