<?php
$tienePass = !empty($config['smtp_pass']);
$smtpHost = $config['smtp_host'] ?? 'smtp.gmail.com';
$smtpPort = $config['smtp_port'] ?? '587';
$smtpSecure = $config['smtp_secure'] ?? 'tls';
?>
<section class="admin-card">
    <h1>Ajustes</h1>
    <form method="POST" action="<?= e(url('admin/configuracion/guardar')); ?>">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken); ?>">

        <h2>Taller</h2>
        <label>WhatsApp (con código de país, sin +)
            <input class="field" name="whatsapp_numero" value="<?= e($config['whatsapp_numero'] ?? ''); ?>">
        </label>
        <label>Correo del taller (recibe avisos internos)
            <input class="field" type="email" name="correo_empresa" value="<?= e($config['correo_empresa'] ?? ''); ?>">
        </label>
        <label>Instagram
            <input class="field" name="instagram" value="<?= e($config['instagram'] ?? ''); ?>">
        </label>
        <label>Horarios
            <input class="field" name="horarios" value="<?= e($config['horarios'] ?? ''); ?>">
        </label>

        <h2>Correo SMTP (PHPMailer)</h2>
        <p>Para Gmail usa el servidor <code>smtp.gmail.com</code>, puerto 587, cifrado TLS y una <b>contraseña de aplicación</b> (no la clave normal de la cuenta).</p>
        <div class="grid-2">
            <label>Servidor SMTP
                <input class="field" name="smtp_host" value="<?= e($smtpHost); ?>" placeholder="smtp.gmail.com">
            </label>
            <label>Puerto
                <input class="field" type="number" name="smtp_port" value="<?= e((string) $smtpPort); ?>">
            </label>
            <label>Cifrado
                <select class="field" name="smtp_secure">
                    <option value="tls" <?= $smtpSecure === 'tls' ? 'selected' : ''; ?>>TLS (587)</option>
                    <option value="ssl" <?= $smtpSecure === 'ssl' ? 'selected' : ''; ?>>SSL (465)</option>
                </select>
            </label>
            <label>Usuario / correo SMTP
                <input class="field" type="email" name="smtp_user" value="<?= e($config['smtp_user'] ?? ''); ?>" placeholder="tucorreo@gmail.com">
            </label>
        </div>
        <label>Contraseña de aplicación
            <input class="field" type="password" name="smtp_pass" autocomplete="new-password" placeholder="<?= $tienePass ? 'Dejar en blanco para no cambiar la guardada' : 'xxxx xxxx xxxx xxxx'; ?>">
        </label>
        <?php if ($tienePass): ?>
            <p><small>Ya hay una contraseña guardada. Solo escríbela otra vez si quieres reemplazarla.</small></p>
        <?php endif; ?>
        <div class="grid-2">
            <label>Remitente (From)
                <input class="field" type="email" name="smtp_from" value="<?= e($config['smtp_from'] ?? ''); ?>" placeholder="mismo correo SMTP">
            </label>
            <label>Nombre del remitente
                <input class="field" name="smtp_from_name" value="<?= e($config['smtp_from_name'] ?? 'CarpinArt'); ?>">
            </label>
        </div>

        <button class="btn" type="submit">Guardar</button>
    </form>
</section>

<section class="admin-card">
    <h2>Probar envío</h2>
    <form method="POST" action="<?= e(url('admin/configuracion/probar')); ?>">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken); ?>">
        <label>Enviar un correo de prueba a
            <input class="field" type="email" name="correo_prueba" value="<?= e($config['correo_empresa'] ?? ''); ?>">
        </label>
        <button class="btn btn-ghost" type="submit">Enviar prueba</button>
    </form>
</section>
