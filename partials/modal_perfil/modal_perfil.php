<?php
// Requiere que $perfil esté definido en el scope del archivo que incluye este partial.
// Fuente: modelos de cada dashboard (obtenerPerfil).
?>

<style>
    .perfil-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.45);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s ease;
        padding: 24px;
    }
    .perfil-overlay.is-open {
        opacity: 1;
        pointer-events: auto;
    }
    .perfil-box {
        background: #fff;
        border-radius: 18px;
        padding: 26px;
        width: min(440px, 100%);
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.15);
        max-height: 90vh;
        overflow-y: auto;
    }
    .perfil-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    .perfil-header h3 { margin: 0; font-size: 17px; font-weight: 600; }

    .perfil-section {
        font-size: 11px;
        font-weight: 700;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin: 22px 0 12px;
    }

    .perfil-field { margin-bottom: 14px; }

    /* Labels de texto (excluye el label-toggle) */
    .perfil-field > label:not(.perfil-toggle) {
        display: block;
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 6px;
    }

    /* Inputs de texto / tel / date */
    .perfil-field input[type="text"],
    .perfil-field input[type="tel"],
    .perfil-field input[type="date"] {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        font-size: 14px;
        outline: none;
        box-sizing: border-box;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .perfil-field input[type="text"]:focus,
    .perfil-field input[type="tel"]:focus,
    .perfil-field input[type="date"]:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
    }

    .perfil-hint {
        display: block;
        font-size: 12px;
        color: #9ca3af;
        margin-top: 5px;
    }

    /* Toggle switch */
    .perfil-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 11px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        cursor: pointer;
        user-select: none;
        color: inherit;
        font-size: inherit;
        margin-bottom: 0;
    }
    .perfil-toggle:hover { background: #f9fafb; }
    .perfil-toggle-text  { font-size: 14px; color: #374151; }

    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 40px;
        height: 22px;
        flex-shrink: 0;
    }
    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
        position: absolute;
    }
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
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.18);
    }
    .toggle-switch input:checked + .toggle-slider              { background: #6366f1; }
    .toggle-switch input:checked + .toggle-slider::before      { transform: translateX(18px); }

    /* Feedback */
    .perfil-feedback {
        display: none;
        padding: 10px 12px;
        border-radius: 10px;
        font-size: 13px;
        margin-bottom: 14px;
    }
    .perfil-feedback.ok    { background: #dcfce7; color: #15803d; }
    .perfil-feedback.error { background: #fee2e2; color: #b91c1c; }
</style>

<div class="perfil-overlay" id="modal-perfil" aria-hidden="true">
    <div class="perfil-box" role="dialog" aria-labelledby="perfil-title">

        <div class="perfil-header">
            <h3 id="perfil-title">Mi perfil</h3>
            <button class="btn-icon" id="btn-cerrar-perfil" aria-label="Cerrar">
                <span class="material-icons">close</span>
            </button>
        </div>

        <div class="perfil-feedback" id="perfil-feedback"></div>

        <form id="form-perfil" novalidate>

            <p class="perfil-section">Información personal</p>

            <div class="perfil-field">
                <label for="p-nombre">Nombre</label>
                <input id="p-nombre" type="text" name="nombre"
                    value="<?= htmlspecialchars($perfil['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="Tu nombre">
            </div>
            <div class="perfil-field">
                <label for="p-apellido">Apellido</label>
                <input id="p-apellido" type="text" name="apellido"
                    value="<?= htmlspecialchars($perfil['apellido'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="Tu apellido">
            </div>
            <div class="perfil-field">
                <label for="p-apodo">Apodo</label>
                <input id="p-apodo" type="text" name="apodo"
                    value="<?= htmlspecialchars($perfil['apodo'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="Apodo o nombre de marca">
            </div>
            <div class="perfil-field">
                <label for="p-fecha">Fecha de nacimiento</label>
                <input id="p-fecha" type="date" name="fecha_nacimiento"
                    value="<?= htmlspecialchars($perfil['fecha_nacimiento'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <p class="perfil-section">Contacto</p>

            <!-- Correo (solo lectura) con estado de verificación -->
            <div class="perfil-field">
                <label>Correo electrónico</label>
                <div style="display:flex;align-items:center;gap:10px;padding:10px 12px;border:1px solid #e5e7eb;border-radius:10px;background:#f9fafb">
                    <span style="font-size:14px;color:#374151;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                        <?= htmlspecialchars($perfil['correo'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <?php if (!empty($perfil['check_correo'])): ?>
                        <span style="display:inline-flex;align-items:center;gap:3px;font-size:12px;font-weight:600;color:#15803d;white-space:nowrap;flex-shrink:0">
                            <span class="material-icons" style="font-size:15px">verified</span>
                            Verificado
                        </span>
                    <?php else: ?>
                        <span style="display:inline-flex;align-items:center;gap:3px;font-size:12px;font-weight:600;color:#b45309;white-space:nowrap;flex-shrink:0">
                            <span class="material-icons" style="font-size:15px">warning</span>
                            Sin verificar
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- WhatsApp -->
            <div class="perfil-field">
                <label for="p-whatsapp">WhatsApp</label>
                <input id="p-whatsapp" type="tel" name="whatsapp"
                    value="<?= htmlspecialchars($perfil['whatsapp'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="+54911XXXXXXXX">
                <span class="perfil-hint">
                    <span class="material-icons" style="font-size:12px;vertical-align:middle">info</span>
                    Formato internacional con código de país · ej: +54911XXXXXXXX
                </span>
            </div>

            <p class="perfil-section">Notificaciones</p>

            <div class="perfil-field">
                <label class="perfil-toggle">
                    <span class="perfil-toggle-text">Recibir correos de Impulsa</span>
                    <span class="toggle-switch">
                        <input type="checkbox" name="permison_correo" value="1"
                            <?= !empty($perfil['permison_correo']) ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </span>
                </label>
            </div>
            <div class="perfil-field">
                <label class="perfil-toggle">
                    <span class="perfil-toggle-text">Recibir mensajes de WhatsApp</span>
                    <span class="toggle-switch">
                        <input type="checkbox" name="permison_whatsapp" value="1"
                            <?= !empty($perfil['permison_whatsapp']) ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </span>
                </label>
            </div>

            <button class="btn btn-aceptar" type="submit" id="btn-guardar-perfil" style="width:100%;margin-top:20px">
                Guardar cambios
            </button>

        </form>
    </div>
</div>

<script>
    const modalPerfil    = document.getElementById('modal-perfil');
    const btnPerfil      = document.getElementById('btn-perfil');
    const btnCerrar      = document.getElementById('btn-cerrar-perfil');
    const formPerfil     = document.getElementById('form-perfil');
    const perfilFeedback = document.getElementById('perfil-feedback');
    const btnGuardar     = document.getElementById('btn-guardar-perfil');

    const abrirModal = () => {
        modalPerfil.classList.add('is-open');
        modalPerfil.setAttribute('aria-hidden', 'false');
        document.getElementById('p-nombre').focus();
    };

    const cerrarModal = () => {
        modalPerfil.classList.remove('is-open');
        modalPerfil.setAttribute('aria-hidden', 'true');
        perfilFeedback.className = 'perfil-feedback';
        perfilFeedback.style.display = 'none';
    };

    btnPerfil.addEventListener('click', abrirModal);
    btnCerrar.addEventListener('click', cerrarModal);
    modalPerfil.addEventListener('click', (e) => { if (e.target === modalPerfil) cerrarModal(); });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modalPerfil.classList.contains('is-open')) cerrarModal();
    });

    // Normaliza WhatsApp a E.164: strips espacios/guiones/parens, reemplaza 00 → +
    function normalizarWhatsapp(raw) {
        if (!raw) return '';
        let n = raw.replace(/[\s\-\(\)\.]/g, '');
        if (n.startsWith('00')) n = '+' + n.slice(2);
        return n;
    }

    const WA_REGEX = /^\+\d{7,15}$/; // E.164: + seguido de 7–15 dígitos

    formPerfil.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Validar y normalizar WhatsApp antes de enviar
        const waInput = document.getElementById('p-whatsapp');
        const waNorm  = normalizarWhatsapp(waInput.value.trim());

        if (waNorm !== '' && !WA_REGEX.test(waNorm)) {
            perfilFeedback.className = 'perfil-feedback error';
            perfilFeedback.textContent = 'El número de WhatsApp debe estar en formato internacional: +[código de país][número] · ej: +54911XXXXXXXX';
            perfilFeedback.style.display = 'block';
            waInput.focus();
            return;
        }

        // Actualizar el campo con el valor normalizado para que FormData lo envíe limpio
        waInput.value = waNorm;

        btnGuardar.disabled = true;
        btnGuardar.textContent = 'Guardando...';
        perfilFeedback.style.display = 'none';

        try {
            const res  = await fetch('/partials/modal_perfil/modal_perfilController.php', {
                method: 'POST',
                body: new FormData(formPerfil),
            });
            const data = await res.json();

            if (data.ok) {
                perfilFeedback.className = 'perfil-feedback ok';
                perfilFeedback.textContent = 'Perfil guardado correctamente.';
                perfilFeedback.style.display = 'block';
                setTimeout(cerrarModal, 1400);
            } else {
                perfilFeedback.className = 'perfil-feedback error';
                perfilFeedback.textContent = data.error ?? 'Error al guardar.';
                perfilFeedback.style.display = 'block';
            }
        } catch {
            perfilFeedback.className = 'perfil-feedback error';
            perfilFeedback.textContent = 'Error de conexión. Intentá de nuevo.';
            perfilFeedback.style.display = 'block';
        } finally {
            btnGuardar.disabled = false;
            btnGuardar.textContent = 'Guardar cambios';
        }
    });
</script>
