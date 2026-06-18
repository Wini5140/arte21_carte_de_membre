/**
 * ARTE21 – app.js
 * Amélioration progressive (pas de dépendances externes)
 */

document.addEventListener('DOMContentLoaded', () => {

    /* ---- Prévisualisation de la date de validité ---- */
    const dateInscription = document.getElementById('date_inscription');
    const dureeValidite   = document.getElementById('duree_validite');
    const preview         = document.getElementById('date_validite_preview');

    function updatePreview() {
        if (!dateInscription || !dureeValidite || !preview) {
            return;
        }
        const dateVal = dateInscription.value;
        const duree   = parseInt(dureeValidite.value, 10);
        if (!dateVal || isNaN(duree)) {
            preview.value = '';
            return;
        }
        const d = new Date(dateVal);
        if (isNaN(d.getTime())) {
            preview.value = '';
            return;
        }
        d.setFullYear(d.getFullYear() + duree);
        // Format d/m/Y
        const dd = String(d.getDate()).padStart(2, '0');
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const yy = d.getFullYear();
        preview.value = dd + '/' + mm + '/' + yy;
    }

    if (dateInscription) {
        dateInscription.addEventListener('input', updatePreview);
    }
    if (dureeValidite) {
        dureeValidite.addEventListener('change', updatePreview);
    }
    updatePreview(); // initial

    /* ---- Prévisualisation de la photo ---------------- */
    const photoInput   = document.getElementById('photo');
    const photoPreview = document.getElementById('photoPreview');

    function setPreviewText(text, isError) {
        photoPreview.innerHTML = '';
        const span = document.createElement('span');
        span.textContent = text;
        if (isError) {
            span.style.color = '#c0392b';
        }
        photoPreview.appendChild(span);
    }

    if (photoInput && photoPreview) {
        photoInput.addEventListener('change', () => {
            const file = photoInput.files[0];
            if (!file) {
                setPreviewText('Aucune photo sélectionnée', false);
                return;
            }

            // Vérification taille côté client (2 Mo)
            if (file.size > 2 * 1024 * 1024) {
                setPreviewText('⚠ Fichier trop volumineux (max 2 Mo)', true);
                photoInput.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = e => {
                photoPreview.innerHTML = '';
                const img = document.createElement('img');
                img.src = e.target.result;
                img.alt = 'Prévisualisation';
                const span = document.createElement('span');
                span.textContent = file.name + ' (' + (file.size / 1024).toFixed(0) + ' Ko)';
                photoPreview.appendChild(img);
                photoPreview.appendChild(span);
            };
            reader.readAsDataURL(file);
        });
    }

});
