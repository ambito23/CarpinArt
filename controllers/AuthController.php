<?php

class AuthController {
    public function login(): void {
        if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            Session::setFlash('error', 'Sesión expirada. Inténtalo de nuevo.');
            header('Location: ' . $this->back());
            exit;
        }

        $email = Security::sanitizeEmail($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$email || $password === '') {
            Session::setFlash('error', 'Correo y contraseña son obligatorios.');
            header('Location: ' . $this->back());
            exit;
        }

        $usuario = (new Usuario())->buscarPorEmail($email);
        if (!$usuario || !(int) $usuario['activo'] || !Security::verifyPassword($password, $usuario['password_hash'])) {
            Session::setFlash('error', 'Correo o contraseña incorrectos.');
            header('Location: ' . $this->back());
            exit;
        }

        Session::login($usuario);

        if (($usuario['rol_nombre'] ?? '') === 'administrador') {
            header('Location: ' . url('admin'));
            exit;
        }

        Session::setFlash('success', 'Sesión iniciada.');
        header('Location: ' . $this->back());
        exit;
    }

    public function register(): void {
        if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            Session::setFlash('error', 'Sesión expirada. Inténtalo de nuevo.');
            header('Location: ' . $this->back());
            exit;
        }

        $nombre = Security::sanitizeString($_POST['nombre'] ?? '');
        $apellido = Security::sanitizeString($_POST['apellido'] ?? '');
        $email = Security::sanitizeEmail($_POST['email'] ?? '');
        $telefono = Security::sanitizeString($_POST['telefono'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirm'] ?? '';

        if ($nombre === '' || $apellido === '' || !$email || strlen($password) < 8 || $password !== $confirm) {
            Session::setFlash('error', 'Revisa los datos. La contraseña debe tener 8 caracteres y coincidir.');
            header('Location: ' . $this->back());
            exit;
        }

        $model = new Usuario();
        if ($model->buscarPorEmail($email)) {
            Session::setFlash('error', 'Ese correo ya tiene una cuenta.');
            header('Location: ' . $this->back());
            exit;
        }

        $model->crear([
            'rol_id'        => $model->idRolCliente(),
            'nombre'        => $nombre,
            'apellido'      => $apellido,
            'email'         => $email,
            'password_hash' => Security::hashPassword($password),
            'telefono'      => $telefono !== '' ? $telefono : null,
        ]);

        $usuario = $model->buscarPorEmail($email);
        Session::login($usuario);
        Session::setFlash('success', 'Cuenta creada.');
        header('Location: ' . $this->back());
        exit;
    }

    public function logout(): void {
        Session::logout();
        header('Location: ' . url());
        exit;
    }

    private function back(): string {
        $redirect = $_POST['redirect'] ?? '';
        if ($redirect && str_starts_with($redirect, BASE_URL)) {
            return $redirect;
        }
        return url();
    }
}
