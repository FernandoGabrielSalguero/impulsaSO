<?php
require_once __DIR__ . '/../../controllers/landing_page_requestController.php';

$val = function(string $key, string $fallback = '') use ($request): string {
    return htmlspecialchars((string)($request[$key] ?? $fallback), ENT_QUOTES, 'UTF-8');
};

$displayName = $_SESSION['apodo'] ?? $_SESSION['nombre'] ?? $_SESSION['correo'] ?? 'Emprendedor';
$displayName = htmlspecialchars((string) $displayName, ENT_QUOTES, 'UTF-8');

$esEdicion      = isset($request['id']);
$espFisico      = !empty($request['espacio_fisico']);
$vendeProductos = !empty($request['vende_productos']);
$vendeServicios = !empty($request['vende_servicios']);
$dominioReg     = !empty($request['dominio_registrado']);
$hostingPropio  = !empty($request['hosting_propio']);
$yaFactura      = !empty($request['ya_factura']);

// Valores guardados para dropdowns
$savedPais      = htmlspecialchars($request['pais']      ?? '', ENT_QUOTES, 'UTF-8');
$savedProvincia = htmlspecialchars($request['provincia'] ?? '', ENT_QUOTES, 'UTF-8');
$savedLocalidad = htmlspecialchars($request['localidad'] ?? '', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Impulsa — Solicitud Landing Page</title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="https://framework.impulsagroup.com/assets/css/framework.css">
    <script src="https://framework.impulsagroup.com/assets/javascript/framework.js" defer></script>

    <style>
        .navbar { justify-content: space-between; }
        .navbar-left { display: flex; align-items: center; gap: 8px; }

        /* Card */
        .lp-form-card {
            background: #fff;
            border-radius: 18px;
            padding: 28px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }

        /* Grid 3 columnas */
        .lp-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 0 28px;
            align-items: start;
        }
        .lp-col-full {
            grid-column: 1 / -1;
        }
        @media (max-width: 900px) {
            .lp-form-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 600px) {
            .lp-form-grid { grid-template-columns: 1fr; }
            .lp-col-full  { grid-column: 1; }
        }

        /* Sección label */
        .lp-section-label {
            font-size: 11px;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin: 0 0 12px;
            padding-bottom: 6px;
            border-bottom: 1px solid #f3f4f6;
        }

        /* Campos */
        .lp-field { margin-bottom: 14px; }
        .lp-field > label:not(.lp-toggle) {
            display: block;
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 6px;
        }

        .lp-field input[type="text"],
        .lp-field input[type="tel"],
        .lp-field input[type="date"],
        .lp-field input[type="number"],
        .lp-field select,
        .lp-field textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            outline: none;
            box-sizing: border-box;
            font-family: inherit;
            background: #fff;
            color: #111827;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
            appearance: none;
            -webkit-appearance: none;
        }
        .lp-field select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%239ca3af' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 32px;
            cursor: pointer;
        }
        .lp-field select:disabled { background-color: #f9fafb; color: #9ca3af; cursor: not-allowed; }
        .lp-field input:focus,
        .lp-field select:focus,
        .lp-field textarea:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
        }
        .lp-field textarea { resize: vertical; min-height: 110px; }

        /* Select loading state */
        .select-loading { position: relative; }
        .select-loading::after {
            content: '';
            position: absolute;
            right: 32px;
            top: 50%;
            transform: translateY(-50%);
            width: 14px;
            height: 14px;
            border: 2px solid #e5e7eb;
            border-top-color: #6366f1;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }
        @keyframes spin { to { transform: translateY(-50%) rotate(360deg); } }

        /* Toggle */
        .lp-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 11px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            cursor: pointer;
            user-select: none;
            margin-bottom: 0;
            transition: background 0.15s;
        }
        .lp-toggle:hover { background: #f9fafb; }
        .lp-toggle-text { font-size: 14px; color: #374151; }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 22px;
            flex-shrink: 0;
        }
        .toggle-switch input { opacity: 0; width: 0; height: 0; position: absolute; }
        .toggle-slider {
            position: absolute;
            inset: 0;
            background: #d1d5db;
            border-radius: 22px;
            transition: background 0.2s;
            cursor: pointer;
        }
        .toggle-slider::before {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            left: 3px;
            top: 3px;
            background: #fff;
            border-radius: 50%;
            transition: transform 0.2s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.18);
        }
        .toggle-switch input:checked + .toggle-slider              { background: #6366f1; }
        .toggle-switch input:checked + .toggle-slider::before      { transform: translateX(18px); }

        /* Checkboxes */
        .lp-check-row { display: flex; gap: 10px; flex-wrap: wrap; }
        .lp-check-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            cursor: pointer;
            font-size: 14px;
            color: #374151;
            user-select: none;
            transition: border-color 0.15s, background 0.15s;
            flex: 1;
            min-width: 100px;
        }
        .lp-check-item input[type="checkbox"] { width: 16px; height: 16px; cursor: pointer; accent-color: #6366f1; }
        .lp-check-item:has(input:checked) {
            border-color: #6366f1;
            background: #eef2ff;
            color: #4338ca;
        }

        /* Bloque dirección */
        .lp-address-block { margin-top: 4px; }

        /* Divider entre col 3 y bloque dirección */
        .lp-dir-divider {
            font-size: 11px;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin: 14px 0 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #f3f4f6;
        }

        /* Mini grid 2 col dentro de col */
        .lp-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        /* Feedback */
        .lp-feedback {
            display: none;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 16px;
            align-items: center;
            gap: 8px;
        }
        .lp-feedback.ok    { display: flex; background: #dcfce7; color: #15803d; }
        .lp-feedback.error { display: flex; background: #fee2e2; color: #b91c1c; }

        /* Hint de API */
        .lp-hint {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 3px;
        }
    </style>
</head>

<body>
<div class="layout">

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <span class="material-icons logo-icon">rocket_launch</span>
            <span class="logo-text">Impulsa</span>
        </div>
        <nav class="sidebar-menu">
            <ul>
                <li onclick="location.href='emprendedor_dashboard.php'">
                    <span class="material-icons" style="color:#6366f1">home</span>
                    <span class="link-text">Inicio</span>
                </li>
                <li class="active" onclick="location.href='landing_page_request.php'">
                    <span class="material-icons" style="color:#6366f1">rocket_launch</span>
                    <span class="link-text">Landing Page</span>
                </li>
                <li onclick="location.href='../../logout.php'">
                    <span class="material-icons" style="color:red">logout</span>
                    <span class="link-text">Salir</span>
                </li>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <button class="btn-icon" onclick="toggleSidebar()">
                <span class="material-icons" id="collapseIcon">chevron_left</span>
            </button>
        </div>
    </aside>

    <!-- MAIN -->
    <div class="main">

        <!-- NAVBAR -->
        <header class="navbar">
            <div class="navbar-left">
                <button class="btn-icon" onclick="toggleSidebar()">
                    <span class="material-icons">menu</span>
                </button>
                <div class="navbar-title">Solicitud Landing Page</div>
            </div>
            <button class="btn-icon" id="btn-perfil" aria-label="Mi perfil" title="Mi perfil">
                <span class="material-icons">account_circle</span>
            </button>
        </header>

        <!-- CONTENIDO -->
        <section class="content">
            <div class="lp-form-card">

                <h2 style="margin:0 0 4px;font-size:18px">
                    <?= $esEdicion ? 'Editá tu solicitud' : 'Completá tu solicitud' ?>
                </h2>
                <p style="margin:0 0 24px;font-size:14px;color:#6b7280">
                    <?= $esEdicion
                        ? 'Tus datos están guardados. Podés actualizarlos cuando quieras.'
                        : 'Completá la información sobre tu emprendimiento para solicitar tu landing page.' ?>
                </p>

                <div class="lp-feedback" id="lp-feedback"></div>

                <form id="form-landing" novalidate>
                <div class="lp-form-grid">

                    <!-- ── COL 1: Datos del emprendimiento ── -->
                    <div>
                        <p class="lp-section-label">Datos del emprendimiento</p>

                        <div class="lp-field">
                            <label for="lp-nombre-emp">Nombre del emprendimiento</label>
                            <input id="lp-nombre-emp" type="text" name="nombre_emprendimiento"
                                value="<?= $val('nombre_emprendimiento') ?>"
                                placeholder="Ej: Café del Sur">
                        </div>

                        <div class="lp-field">
                            <label for="lp-fecha">Fecha de inicio</label>
                            <input id="lp-fecha" type="date" name="fecha_inicio"
                                value="<?= $val('fecha_inicio') ?>">
                        </div>

                        <div class="lp-field">
                            <label for="lp-fundador">Nombre del fundador</label>
                            <input id="lp-fundador" type="text" name="nombre_fundador"
                                value="<?= $val('nombre_fundador', $request['perfil_nombre'] ?? '') ?>"
                                placeholder="Tu nombre completo">
                        </div>

                        <div class="lp-field">
                            <label for="lp-colaboradores">Cantidad de colaboradores</label>
                            <input id="lp-colaboradores" type="number" name="cantidad_colaboradores"
                                value="<?= $val('cantidad_colaboradores', '0') ?>"
                                min="0" placeholder="0">
                        </div>

                        <div class="lp-field">
                            <label for="lp-telefono">Teléfono de contacto</label>
                            <input id="lp-telefono" type="tel" name="telefono_contacto"
                                value="<?= $val('telefono_contacto', $request['perfil_whatsapp'] ?? '') ?>"
                                placeholder="+54911XXXXXXXX">
                        </div>
                    </div>

                    <!-- ── COL 2: Oferta + Situación + Infraestructura ── -->
                    <div>
                        <p class="lp-section-label">¿Qué ofrecés?</p>

                        <div class="lp-field">
                            <div class="lp-check-row">
                                <label class="lp-check-item">
                                    <input type="checkbox" name="vende_productos" value="1"
                                        <?= $vendeProductos ? 'checked' : '' ?>>
                                    <span class="material-icons" style="font-size:18px">inventory_2</span>
                                    Productos
                                </label>
                                <label class="lp-check-item">
                                    <input type="checkbox" name="vende_servicios" value="1"
                                        <?= $vendeServicios ? 'checked' : '' ?>>
                                    <span class="material-icons" style="font-size:18px">handyman</span>
                                    Servicios
                                </label>
                            </div>
                        </div>

                        <p class="lp-section-label" style="margin-top:18px">Situación actual</p>

                        <div class="lp-field">
                            <label class="lp-toggle">
                                <span class="lp-toggle-text">¿Ya facturás?</span>
                                <span class="toggle-switch">
                                    <input type="checkbox" name="ya_factura" value="1"
                                        <?= $yaFactura ? 'checked' : '' ?>>
                                    <span class="toggle-slider"></span>
                                </span>
                            </label>
                        </div>

                        <p class="lp-section-label" style="margin-top:18px">Infraestructura web</p>

                        <div class="lp-field">
                            <label class="lp-toggle">
                                <span class="lp-toggle-text">¿Tenés dominio registrado?</span>
                                <span class="toggle-switch">
                                    <input type="checkbox" name="dominio_registrado" value="1"
                                        <?= $dominioReg ? 'checked' : '' ?>>
                                    <span class="toggle-slider"></span>
                                </span>
                            </label>
                        </div>

                        <div class="lp-field">
                            <label class="lp-toggle">
                                <span class="lp-toggle-text">¿Tenés hosting propio?</span>
                                <span class="toggle-switch">
                                    <input type="checkbox" name="hosting_propio" value="1"
                                        <?= $hostingPropio ? 'checked' : '' ?>>
                                    <span class="toggle-slider"></span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- ── COL 3: Espacio físico + Dirección ── -->
                    <div>
                        <p class="lp-section-label">Espacio físico</p>

                        <div class="lp-field">
                            <label class="lp-toggle">
                                <span class="lp-toggle-text">¿Tenés local o espacio físico?</span>
                                <span class="toggle-switch">
                                    <input type="checkbox" id="toggle-espacio" name="espacio_fisico" value="1"
                                        <?= $espFisico ? 'checked' : '' ?>>
                                    <span class="toggle-slider"></span>
                                </span>
                            </label>
                        </div>

                        <!-- Dirección: visible solo si espacio_fisico = 1 -->
                        <div class="lp-address-block" id="lp-address-block"
                             style="<?= $espFisico ? '' : 'display:none' ?>">

                            <p class="lp-dir-divider">Dirección del local</p>

                            <!-- País -->
                            <div class="lp-field">
                                <label for="lp-pais">País</label>
                                <div class="select-loading" id="wrap-pais">
                                    <select id="lp-pais" name="pais" disabled>
                                        <option value="">Cargando países...</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Provincia / Estado -->
                            <div class="lp-field">
                                <label for="lp-provincia">Provincia / Estado</label>
                                <div id="wrap-provincia">
                                    <select id="lp-provincia" name="provincia" disabled>
                                        <option value="">Seleccioná un país primero</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Localidad / Ciudad -->
                            <div class="lp-field">
                                <label for="lp-localidad">Localidad / Ciudad</label>
                                <div id="wrap-localidad">
                                    <select id="lp-localidad" name="localidad" disabled>
                                        <option value="">Seleccioná una provincia primero</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Calle y Número -->
                            <div class="lp-grid-2">
                                <div class="lp-field">
                                    <label for="lp-calle">Calle</label>
                                    <input id="lp-calle" type="text" name="calle"
                                        value="<?= $val('calle') ?>"
                                        placeholder="Ej: Av. Corrientes">
                                </div>
                                <div class="lp-field">
                                    <label for="lp-numero">Número</label>
                                    <input id="lp-numero" type="text" name="numero"
                                        value="<?= $val('numero') ?>"
                                        placeholder="Ej: 1234">
                                </div>
                            </div>

                            <p class="lp-hint">
                                <span class="material-icons" style="font-size:13px">public</span>
                                Datos geográficos provistos por RestCountries y CountriesNow
                            </p>
                        </div>
                    </div>

                    <!-- ── FILA COMPLETA: Descripción ── -->
                    <div class="lp-col-full" style="margin-top:20px">
                        <p class="lp-section-label">Descripción del emprendimiento</p>
                        <div class="lp-field" style="margin-bottom:0">
                            <textarea id="lp-desc" name="descripcion"
                                placeholder="Contanos de qué trata tu emprendimiento, a quién va dirigido, cuál es su propuesta de valor..."><?= $val('descripcion') ?></textarea>
                        </div>
                    </div>

                    <!-- ── FILA COMPLETA: Botón ── -->
                    <div class="lp-col-full">
                        <button class="btn btn-aceptar" type="submit" id="btn-guardar-lp"
                            style="width:100%;margin-top:20px">
                            <?= $esEdicion ? 'Actualizar solicitud' : 'Enviar solicitud' ?>
                        </button>
                    </div>

                </div><!-- /lp-form-grid -->
                </form>

            </div>
        </section>
    </div>
</div>

<?php require_once __DIR__ . '/../../partials/modal_perfil/modal_perfil.php'; ?>

<script>
    // Sesión en consola
    const sesion = {
        user_id:          <?= json_encode($_SESSION['user_id']          ?? null) ?>,
        correo:           <?= json_encode($_SESSION['correo']           ?? null) ?>,
        rol:              <?= json_encode($_SESSION['rol']              ?? null) ?>,
        nombre:           <?= json_encode($_SESSION['nombre']           ?? null) ?>,
        apellido:         <?= json_encode($_SESSION['apellido']         ?? null) ?>,
        apodo:            <?= json_encode($_SESSION['apodo']            ?? null) ?>,
        fecha_nacimiento: <?= json_encode($_SESSION['fecha_nacimiento'] ?? null) ?>,
    };
    console.group('[Impulsa] Sesión activa');
    console.table(sesion);
    console.groupEnd();

    // ─── Valores guardados desde PHP ───────────────────────────────
    const SAVED_PAIS      = <?= json_encode($savedPais) ?>;
    const SAVED_PROVINCIA = <?= json_encode($savedProvincia) ?>;
    const SAVED_LOCALIDAD = <?= json_encode($savedLocalidad) ?>;

    // ─── Refs ───────────────────────────────────────────────────────
    const toggleEspacio  = document.getElementById('toggle-espacio');
    const addressBlock   = document.getElementById('lp-address-block');
    const selPais        = document.getElementById('lp-pais');
    const selProvincia   = document.getElementById('lp-provincia');
    const selLocalidad   = document.getElementById('lp-localidad');
    const wrapPais       = document.getElementById('wrap-pais');

    // ─── Toggle espacio físico ──────────────────────────────────────
    toggleEspacio.addEventListener('change', () => {
        addressBlock.style.display = toggleEspacio.checked ? 'block' : 'none';
    });

    // ─── Helpers para select ────────────────────────────────────────
    function setLoading(wrap, loading) {
        if (loading) wrap.classList.add('select-loading');
        else         wrap.classList.remove('select-loading');
    }

    function populateSelect(sel, options, placeholder, savedValue) {
        sel.innerHTML = `<option value="">${placeholder}</option>`;
        options.forEach(opt => {
            const o = document.createElement('option');
            o.value = opt;
            o.textContent = opt;
            if (opt === savedValue) o.selected = true;
            sel.appendChild(o);
        });
        sel.disabled = options.length === 0;
    }

    // ─── API: Países (restcountries.com) ────────────────────────────
    async function cargarPaises() {
        setLoading(wrapPais, true);
        try {
            const res  = await fetch('https://restcountries.com/v3.1/all?fields=name&lang=es');
            const data = await res.json();
            const nombres = data
                .map(c => c.name?.common)
                .filter(Boolean)
                .sort((a, b) => a.localeCompare(b, 'es'));
            populateSelect(selPais, nombres, 'Seleccioná un país', SAVED_PAIS);
            selPais.disabled = false;
            if (SAVED_PAIS) cargarProvincias(SAVED_PAIS);
        } catch (e) {
            console.warn('[LP] Error cargando países:', e);
            selPais.innerHTML = '<option value="">Error al cargar. Escribí manualmente.</option>';
            selPais.disabled = false;
        } finally {
            setLoading(wrapPais, false);
        }
    }

    // ─── API: Provincias (countriesnow.space) ───────────────────────
    async function cargarProvincias(pais) {
        selProvincia.innerHTML = '<option value="">Cargando...</option>';
        selProvincia.disabled = true;
        selLocalidad.innerHTML = '<option value="">Seleccioná una provincia primero</option>';
        selLocalidad.disabled = true;

        const wrap = document.getElementById('wrap-provincia');
        setLoading(wrap, true);
        try {
            const res  = await fetch('https://countriesnow.space/api/v0.1/countries/states', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ country: pais }),
            });
            const data = await res.json();
            const estados = (data.data?.states || []).map(s => s.name).sort((a, b) => a.localeCompare(b, 'es'));
            if (estados.length === 0) throw new Error('Sin datos');
            populateSelect(selProvincia, estados, 'Seleccioná una provincia', SAVED_PROVINCIA);
            selProvincia.disabled = false;
            if (SAVED_PROVINCIA) cargarLocalidades(pais, SAVED_PROVINCIA);
        } catch {
            selProvincia.innerHTML = '<option value="">No disponible para este país</option>';
            selProvincia.disabled = false;
        } finally {
            setLoading(wrap, false);
        }
    }

    // ─── API: Localidades (countriesnow.space) ──────────────────────
    async function cargarLocalidades(pais, provincia) {
        selLocalidad.innerHTML = '<option value="">Cargando...</option>';
        selLocalidad.disabled = true;

        const wrap = document.getElementById('wrap-localidad');
        setLoading(wrap, true);
        try {
            const res  = await fetch('https://countriesnow.space/api/v0.1/countries/state/cities', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ country: pais, state: provincia }),
            });
            const data = await res.json();
            const ciudades = (data.data || []).sort((a, b) => a.localeCompare(b, 'es'));
            if (ciudades.length === 0) throw new Error('Sin datos');
            populateSelect(selLocalidad, ciudades, 'Seleccioná una localidad', SAVED_LOCALIDAD);
            selLocalidad.disabled = false;
        } catch {
            selLocalidad.innerHTML = '<option value="">No disponible para esta provincia</option>';
            selLocalidad.disabled = false;
        } finally {
            setLoading(wrap, false);
        }
    }

    // ─── Eventos de cambio en selects ───────────────────────────────
    selPais.addEventListener('change', () => {
        if (selPais.value) cargarProvincias(selPais.value);
        else {
            selProvincia.innerHTML = '<option value="">Seleccioná un país primero</option>';
            selProvincia.disabled = true;
            selLocalidad.innerHTML = '<option value="">Seleccioná una provincia primero</option>';
            selLocalidad.disabled = true;
        }
    });

    selProvincia.addEventListener('change', () => {
        if (selProvincia.value) cargarLocalidades(selPais.value, selProvincia.value);
        else {
            selLocalidad.innerHTML = '<option value="">Seleccioná una provincia primero</option>';
            selLocalidad.disabled = true;
        }
    });

    // Cargar países al abrir la página
    cargarPaises();

    // ─── Submit AJAX ────────────────────────────────────────────────
    const formLanding = document.getElementById('form-landing');
    const feedback    = document.getElementById('lp-feedback');
    const btnGuardar  = document.getElementById('btn-guardar-lp');

    function showFeedback(type, msg) {
        feedback.className = 'lp-feedback ' + type;
        feedback.innerHTML = '<span class="material-icons" style="font-size:18px">'
            + (type === 'ok' ? 'check_circle' : 'error') + '</span>' + msg;
        feedback.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    formLanding.addEventListener('submit', async (e) => {
        e.preventDefault();

        const prodCheck = formLanding.querySelector('input[name="vende_productos"]');
        const servCheck = formLanding.querySelector('input[name="vende_servicios"]');
        if (!prodCheck.checked && !servCheck.checked) {
            showFeedback('error', 'Debés seleccionar al menos una opción: Productos o Servicios');
            return;
        }

        btnGuardar.disabled = true;
        btnGuardar.textContent = 'Guardando...';
        feedback.className = 'lp-feedback';

        try {
            const res  = await fetch('/controllers/landing_page_requestSaveController.php', {
                method: 'POST',
                body: new FormData(formLanding),
            });
            const data = await res.json();

            if (data.ok) {
                showFeedback('ok', 'Guardado correctamente.');
                setTimeout(() => location.reload(), 1400);
            } else {
                showFeedback('error', data.error ?? 'Error al guardar.');
            }
        } catch {
            showFeedback('error', 'Error de conexión. Intentá de nuevo.');
        } finally {
            btnGuardar.disabled = false;
            btnGuardar.textContent = <?= json_encode($esEdicion ? 'Actualizar solicitud' : 'Enviar solicitud') ?>;
        }
    });
</script>
</body>
</html>
