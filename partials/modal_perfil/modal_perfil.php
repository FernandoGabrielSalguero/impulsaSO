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
    }
    .perfil-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    .perfil-header h3 { margin: 0; font-size: 17px; font-weight: 600; }
    .perfil-field { margin-bottom: 16px; }
    .perfil-field label {
        display: block;
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 6px;
    }
    .perfil-field input {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        font-size: 14px;
        outline: none;
        box-sizing: border-box;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .perfil-field input:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
    }
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
            <button class="btn btn-primary" type="submit" id="btn-guardar-perfil" style="width:100%">
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

    formPerfil.addEventListener('submit', async (e) => {
        e.preventDefault();
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
