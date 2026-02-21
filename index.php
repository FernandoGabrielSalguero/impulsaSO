<?php
$loginError = $_GET['login_error'] ?? '';
$loginMessage = '';

if ($loginError === 'inactive') {
    $loginMessage = 'No tenes permiso para acceder, contactate con el administrador.';
} elseif ($loginError === 'invalid') {
    $loginMessage = 'Usuario o contrasena incorrectos.';
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImpulsaSO | Plataforma Integral</title>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=IBM+Plex+Sans:wght@300;400;500;600&display=swap">
    <style>
        :root {
            color-scheme: light;
            --bg: #0a0b12;
            --bg-soft: #111321;
            --surface: #15182a;
            --surface-strong: #1a1f36;
            --ink: #f5f7ff;
            --muted: #a7b0c5;
            --accent: #59f2e8;
            --accent-2: #ff6b6b;
            --accent-3: #f5c542;
            --stroke: rgba(255, 255, 255, 0.08);
            --shadow: 0 18px 60px rgba(10, 12, 25, 0.55);
            --radius: 18px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'IBM Plex Sans', 'Segoe UI', sans-serif;
            background: radial-gradient(1200px 700px at 70% -20%, #242955 0%, transparent 70%),
                radial-gradient(900px 600px at 20% 10%, #1f2449 0%, transparent 68%),
                var(--bg);
            color: var(--ink);
            min-height: 100vh;
            overflow-x: hidden;
        }

        body::before,
        body::after {
            content: '';
            position: fixed;
            width: 420px;
            height: 420px;
            border-radius: 50%;
            filter: blur(0);
            opacity: 0.5;
            z-index: 0;
            pointer-events: none;
        }

        body::before {
            left: -160px;
            top: 10%;
            background: radial-gradient(circle at 30% 30%, rgba(89, 242, 232, 0.35), transparent 70%);
        }

        body::after {
            right: -200px;
            bottom: -80px;
            background: radial-gradient(circle at 30% 30%, rgba(255, 107, 107, 0.35), transparent 70%);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .page {
            position: relative;
            z-index: 1;
        }

        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 24px 6vw;
            gap: 24px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-family: 'Space Grotesk', 'Segoe UI', sans-serif;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            display: grid;
            place-items: center;
            color: #05070f;
            font-weight: 700;
        }

        .nav-links {
            display: flex;
            gap: 20px;
            color: var(--muted);
            font-size: 14px;
        }

        .nav-cta {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 18px;
            border-radius: 999px;
            border: 1px solid var(--stroke);
            background: rgba(255, 255, 255, 0.02);
            color: var(--ink);
            font-weight: 500;
            transition: transform 0.2s ease, background 0.2s ease;
        }

        .nav-cta:hover {
            transform: translateY(-1px);
            background: rgba(255, 255, 255, 0.08);
        }

        .hero {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 40px;
            padding: 40px 6vw 80px;
            align-items: center;
        }

        .hero h1 {
            font-family: 'Space Grotesk', 'Segoe UI', sans-serif;
            font-size: clamp(2.4rem, 4vw, 4.2rem);
            line-height: 1.02;
            margin: 0 0 18px;
        }

        .hero h1 span {
            color: var(--accent);
        }

        .hero p {
            color: var(--muted);
            font-size: 1.05rem;
            margin: 0 0 24px;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
        }

        .btn {
            padding: 12px 22px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.95rem;
            border: 1px solid transparent;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent), #54d6ff);
            color: #05070f;
            box-shadow: 0 12px 30px rgba(89, 242, 232, 0.35);
        }

        .btn-secondary {
            background: transparent;
            color: var(--ink);
            border-color: var(--stroke);
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .hero-card {
            background: linear-gradient(160deg, rgba(255, 255, 255, 0.06), rgba(255, 255, 255, 0));
            border-radius: var(--radius);
            border: 1px solid var(--stroke);
            padding: 24px;
            box-shadow: var(--shadow);
        }

        .hero-card h3 {
            font-family: 'Space Grotesk', 'Segoe UI', sans-serif;
            font-size: 1.2rem;
            margin: 0 0 14px;
        }

        .metrics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 18px;
        }

        .metric {
            background: var(--surface);
            border-radius: 14px;
            border: 1px solid var(--stroke);
            padding: 16px;
        }

        .metric strong {
            display: block;
            font-size: 1.3rem;
        }

        .sections {
            padding: 0 6vw 80px;
            display: grid;
            gap: 32px;
        }

        .section {
            background: var(--surface-strong);
            border-radius: var(--radius);
            border: 1px solid var(--stroke);
            padding: 28px;
        }

        .section h2 {
            font-family: 'Space Grotesk', 'Segoe UI', sans-serif;
            margin: 0 0 14px;
            font-size: 1.6rem;
        }

        .tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            color: var(--muted);
            font-size: 0.9rem;
        }

        .tag {
            padding: 6px 12px;
            border-radius: 999px;
            border: 1px solid var(--stroke);
            background: rgba(255, 255, 255, 0.03);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
        }

        .card {
            background: var(--surface);
            border-radius: 16px;
            border: 1px solid var(--stroke);
            padding: 18px;
            min-height: 150px;
        }

        .card h3 {
            margin: 0 0 10px;
            font-family: 'Space Grotesk', 'Segoe UI', sans-serif;
        }

        .card p {
            margin: 0;
            color: var(--muted);
            font-size: 0.95rem;
        }

        .footer {
            padding: 24px 6vw 40px;
            color: var(--muted);
            border-top: 1px solid var(--stroke);
        }

        .modal {
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(4, 5, 12, 0.6);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
            z-index: 10;
            padding: 24px;
        }

        .modal.is-open {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-dialog {
            width: min(460px, 100%);
            background: var(--surface-strong);
            border-radius: 20px;
            border: 1px solid var(--stroke);
            padding: 24px;
            box-shadow: var(--shadow);
            position: relative;
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .modal-header h3 {
            margin: 0;
            font-family: 'Space Grotesk', 'Segoe UI', sans-serif;
        }

        .close-btn {
            border: none;
            background: transparent;
            color: var(--muted);
            font-size: 1.2rem;
            cursor: pointer;
        }

        .field {
            display: grid;
            gap: 8px;
            margin-bottom: 14px;
        }

        .field label {
            font-size: 0.9rem;
            color: var(--muted);
        }

        .field input {
            padding: 12px 14px;
            border-radius: 12px;
            border: 1px solid var(--stroke);
            background: rgba(255, 255, 255, 0.04);
            color: var(--ink);
            outline: none;
        }

        .field input:focus {
            border-color: rgba(89, 242, 232, 0.6);
            box-shadow: 0 0 0 3px rgba(89, 242, 232, 0.15);
        }

        .error {
            padding: 10px 12px;
            border-radius: 12px;
            background: rgba(255, 107, 107, 0.15);
            border: 1px solid rgba(255, 107, 107, 0.3);
            color: #ffdede;
            margin-bottom: 12px;
            font-size: 0.9rem;
        }

        .modal-actions {
            display: grid;
            gap: 12px;
        }

        .helper {
            color: var(--muted);
            font-size: 0.85rem;
        }

        .password-wrap {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: var(--accent);
            cursor: pointer;
            font-size: 0.85rem;
        }

        @media (max-width: 720px) {
            .nav {
                flex-direction: column;
                align-items: flex-start;
            }

            .nav-links {
                flex-wrap: wrap;
            }
        }
    </style>
</head>

<body>
    <div class="page">
        <nav class="nav">
            <div class="brand">
                <div class="brand-mark">SO</div>
                <div>
                    Impulsa<span style="color: var(--accent);">SO</span>
                </div>
            </div>
            <div class="nav-links">
                <a href="#plataforma">Plataforma</a>
                <a href="#modulos">Módulos</a>
                <a href="#comunidad">Comunidad</a>
            </div>
            <button class="nav-cta" type="button" data-open-modal>
                Ingresar
            </button>
        </nav>

        <section class="hero">
            <div>
                <h1>Impulsa tus proyectos y emprendimientos con una plataforma <span>integral</span>.</h1>
                <p>ImpulsaSO conecta equipos, métricas y ejecución en un mismo espacio. Diseñada para crear foco, ordenar
                    iniciativas y acelerar resultados reales.</p>
                <div class="hero-actions">
                    <button class="btn btn-primary" type="button" data-open-modal>Ingresar</button>
                    <a class="btn btn-secondary" href="#plataforma">Ver cómo funciona</a>
                </div>
            </div>
            <div class="hero-card">
                <h3>Un tablero vivo para hacer que las ideas pasen</h3>
                <div class="metrics">
                    <div class="metric">
                        <strong>+32%</strong>
                        Avance promedio por sprint
                    </div>
                    <div class="metric">
                        <strong>6X</strong>
                        Menos fricción operativa
                    </div>
                    <div class="metric">
                        <strong>24/7</strong>
                        Visibilidad total del equipo
                    </div>
                </div>
            </div>
        </section>

        <section class="sections">
            <div class="section" id="plataforma">
                <h2>Plataforma hecha para ejecutar</h2>
                <p>ImpulsaSO integra planeación, seguimiento, finanzas y comunidad. Todo en un flujo simple y claro, para
                    equipos que quieren operar con ritmo y precisión.</p>
                <div class="tags">
                    <div class="tag">Gestión de proyectos</div>
                    <div class="tag">Finanzas inteligentes</div>
                    <div class="tag">KPIs en tiempo real</div>
                    <div class="tag">Comunidad y soporte</div>
                </div>
            </div>
            <div class="section" id="modulos">
                <h2>Módulos que se adaptan a tu forma de crecer</h2>
                <div class="grid">
                    <div class="card">
                        <h3>Mapa de iniciativas</h3>
                        <p>Organiza proyectos, responsables y entregables con una vista única.</p>
                    </div>
                    <div class="card">
                        <h3>Radar de inversión</h3>
                        <p>Controla presupuestos y flujos con alertas claras para decidir rápido.</p>
                    </div>
                    <div class="card">
                        <h3>Laboratorio de emprendimiento</h3>
                        <p>Acompaña el crecimiento con herramientas y mentorías en un solo lugar.</p>
                    </div>
                    <div class="card">
                        <h3>Comunidad activa</h3>
                        <p>Conecta con socios, aliados y oportunidades mientras ejecutas.</p>
                    </div>
                </div>
            </div>
            <div class="section" id="comunidad">
                <h2>Una invitación abierta a construir</h2>
                <p>Sumate a ImpulsaSO para ordenar la operación, escalar tu impacto y mantener tu visión encendida.</p>
                <div class="hero-actions">
                    <button class="btn btn-primary" type="button" data-open-modal>Ingresar</button>
                    <a class="btn btn-secondary" href="mailto:hola@impulsaso.com">Hablar con el equipo</a>
                </div>
            </div>
        </section>

        <footer class="footer">
            ImpulsaSO · Plataforma integral para la gestión de proyectos y emprendimientos.
        </footer>
    </div>

    <div class="modal" data-modal aria-hidden="true">
        <div class="modal-dialog" role="dialog" aria-modal="true" aria-labelledby="login-title">
            <div class="modal-header">
                <h3 id="login-title">Ingresar a ImpulsaSO</h3>
                <button class="close-btn" type="button" aria-label="Cerrar" data-close-modal>✕</button>
            </div>
            <?php if ($loginMessage): ?>
                <div class="error"><?= htmlspecialchars($loginMessage, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <form action="/auth/login.php" method="POST">
                <div class="field">
                    <label for="usuario">Usuario</label>
                    <input type="text" id="usuario" name="usuario" placeholder="Ingresá tu usuario" required>
                </div>
                <div class="field password-wrap">
                    <label for="contrasena">Contraseña</label>
                    <input type="password" id="contrasena" name="contrasena" placeholder="Ingresá tu contraseña" required>
                    <button class="toggle-password" type="button" aria-label="Mostrar contraseña" data-toggle-password>Mostrar</button>
                </div>
                <div class="modal-actions">
                    <button class="btn btn-primary" type="submit">Entrar</button>
                    <span class="helper">¿Problemas para entrar? Escribinos desde el panel de soporte.</span>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.querySelector('[data-modal]');
        const openButtons = document.querySelectorAll('[data-open-modal]');
        const closeButtons = document.querySelectorAll('[data-close-modal]');
        const togglePassword = document.querySelector('[data-toggle-password]');
        const passwordField = document.getElementById('contrasena');
        const hasError = <?= $loginMessage ? 'true' : 'false' ?>;

        const openModal = () => {
            if (!modal) return;
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            const firstInput = modal.querySelector('input');
            if (firstInput) {
                firstInput.focus();
            }
        };

        const closeModal = () => {
            if (!modal) return;
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
        };

        openButtons.forEach(btn => btn.addEventListener('click', openModal));
        closeButtons.forEach(btn => btn.addEventListener('click', closeModal));

        if (modal) {
            modal.addEventListener('click', event => {
                if (event.target === modal) {
                    closeModal();
                }
            });
        }

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape' && modal?.classList.contains('is-open')) {
                closeModal();
            }
        });

        if (togglePassword && passwordField) {
            togglePassword.addEventListener('click', () => {
                const isPassword = passwordField.getAttribute('type') === 'password';
                passwordField.setAttribute('type', isPassword ? 'text' : 'password');
                togglePassword.textContent = isPassword ? 'Ocultar' : 'Mostrar';
                togglePassword.setAttribute('aria-label', isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');
            });
        }

        if (hasError) {
            openModal();
        }
    </script>
</body>

</html>
