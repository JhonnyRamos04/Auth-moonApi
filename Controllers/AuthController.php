<?php
require_once __DIR__ . '/../Models/UserModel.php';

class AuthController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Maneja la petición POST /register
     */
    public function register()
    {
        // Obtener los datos del cuerpo de la petición (JSON)
        $data = json_decode(file_get_contents("php://input"));

        // Validaciones básicas
        if (!isset($data->username) || !isset($data->email) || !isset($data->password)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(['message' => 'Faltan datos requeridos.']);
            return;
        }
        if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(['message' => 'Formato de email inválido.']);
            return;
        }

        // Verificar si el usuario ya existe
        if ($this->userModel->findByEmail($data->email)) {
            header("HTTP/1.1 409 Conflict");
            echo json_encode(['message' => 'El email ya está registrado.']);
            return;
        }

        // Intentar crear el usuario
        if ($this->userModel->create($data->username, $data->email, $data->password)) {
            header("HTTP/1.1 201 Created");
            echo json_encode(['message' => 'Usuario creado exitosamente.']);
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(['message' => 'Error al crear el usuario.']);
        }
    }

    /**
     * Maneja la petición POST /login
     */
    public function login()
    {
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->email) || !isset($data->password)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(['message' => 'Email y contraseña son requeridos.']);
            return;
        }

        $user = $this->userModel->findByEmail($data->email);

        // Verificar si el usuario existe y si la contraseña es correcta
        if ($user && password_verify($data->password, $user['password'])) {
            // Contraseña correcta, aquí generaríamos un token (JWT)
            // Por simplicidad, por ahora solo retornamos un mensaje de éxito.
            // TODO: Implementar JWT
            echo json_encode([
                'message' => 'Inicio de sesión exitoso.',
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email']
                ],
                'token' => 'AQUI_VA_UN_JWT_GENERADO' // Placeholder
            ]);
        } else {
            header("HTTP/1.1 401 Unauthorized");
            echo json_encode(['message' => 'Credenciales inválidas.']);
        }
    }
}
