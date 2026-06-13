<?php
/**
 * Template visuel de la carte de membre ARTE21.
 * Inclus depuis card_detail.php – variable $membre disponible.
 */
$is_expired = strtotime($membre['date_validite']) < time();
$photo_src  = (!empty($membre['photo_path']) && file_exists(__DIR__ . '/../' . $membre['photo_path']))
              ? $membre['photo_path']
              : null;
?>
<div class="member-card <?= $is_expired ? 'member-card--expired' : '' ?>">
    <div class="member-card__front">
        <!-- En-tête de la carte -->
        <div class="mc-header">
            <div class="mc-header__brand">
                <span class="mc-logo">ARTE<span>21</span></span>
                <span class="mc-asbl">ASBL</span>
            </div>
            <div class="mc-header__title">Carte de membre</div>
        </div>

        <!-- Corps de la carte -->
        <div class="mc-body">
            <div class="mc-photo">
                <?php if ($photo_src): ?>
                    <img src="<?= h($photo_src) ?>"
                         alt="Photo de <?= h($membre['prenom'] . ' ' . $membre['nom']) ?>">
                <?php else: ?>
                    <div class="mc-photo__placeholder">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                <?php endif; ?>
            </div>

            <div class="mc-info">
                <div class="mc-name">
                    <?= h($membre['prenom']) ?> <?= h($membre['nom']) ?>
                </div>
                <div class="mc-fields">
                    <div class="mc-field">
                        <span class="mc-field__label">Né(e) le</span>
                        <span class="mc-field__value"><?= h(fmt_date($membre['date_naissance'])) ?></span>
                    </div>
                    <div class="mc-field">
                        <span class="mc-field__label">Membre depuis</span>
                        <span class="mc-field__value"><?= h(fmt_date($membre['date_inscription'])) ?></span>
                    </div>
                    <div class="mc-field">
                        <span class="mc-field__label">Valable jusqu'au</span>
                        <span class="mc-field__value <?= $is_expired ? 'expired' : '' ?>">
                            <?= h(fmt_date($membre['date_validite'])) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pied de carte -->
        <div class="mc-footer">
            <span class="mc-ref"><?= h($membre['reference']) ?></span>
            <?php if ($is_expired): ?>
                <span class="mc-status mc-status--expired">EXPIRÉE</span>
            <?php else: ?>
                <span class="mc-status mc-status--valid">VALIDE</span>
            <?php endif; ?>
        </div>
    </div>
</div>
