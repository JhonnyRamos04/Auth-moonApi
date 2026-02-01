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
        if (!isset($data->full_name) || !isset($data->email) || !isset($data->password)) {
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

        // Determinar Role ID (1=User, 2=Admin)
        $role_id = 1;
        if (isset($data->user_type) && $data->user_type === 'admin') {
            $role_id = 2;
        }

        // Intentar crear el usuario
        if ($this->userModel->create($data->full_name, $data->email, $data->password, $role_id)) {
            header("HTTP/1.1 201 Created");
            echo json_encode(['success' => true, 'message' => 'Usuario creado exitosamente.']);
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(['success' => false, 'message' => 'Error al crear el usuario.']);
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
            // Determinar tipo de usuario basado en role_id
            $userType = ($user['role_id'] == 2) ? 'admin' : 'user';

            echo json_encode([
                'message' => 'Inicio de sesión exitoso.',
                'user' => [
                    'id' => $user['user_id'],
                    'full_name' => $user['full_name'],
                    'email' => $user['email'],
                    'user_type' => $userType
                ],
                'token' => 'AQUI_VA_UN_JWT_GENERADO' // Placeholder
            ]);
        } else {
            header("HTTP/1.1 401 Unauthorized");
            echo json_encode(['message' => 'Credenciales inválidas.']);
        }
    }

    /**
     * Maneja la petición GET /get_users
     * Retorna la lista de todos los usuarios
     */
    public function getUsers()
    {
        $users = $this->userModel->findAll();

        // Mapear campos para que coincidan con lo que espera el frontend
        $formattedUsers = array_map(function ($user) {
            return [
                'id' => $user['user_id'],
                'username' => $user['full_name'],
                'email' => $user['email'],
                'user_type' => ($user['role_id'] == 2) ? 'admin' : 'user'
            ];
        }, $users ? $users : []);

        echo json_encode($formattedUsers);
    }

    /**
     * Maneja la petición DELETE /delete_user?id=X
     * Elimina un usuario por su ID
     */
    public function deleteUser()
    {
        $id = isset($_GET['id']) ? intval($_GET['id']) : null;

        if (!$id) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(['success' => false, 'message' => 'ID de usuario requerido.']);
            return;
        }

        if ($this->userModel->deleteById($id)) {
            echo json_encode(['success' => true, 'message' => 'Usuario eliminado exitosamente.']);
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(['success' => false, 'message' => 'Error al eliminar el usuario.']);
        }
    }

    /**
     * Maneja la petición POST /change_password
     * Cambia la contraseña de un usuario
     */
    public function changePassword()
    {
        $data = json_decode(file_get_contents("php://input"));

        // Validaciones
        if (!isset($data->user_id) || !isset($data->current_password) || !isset($data->new_password)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos.']);
            return;
        }

        // Buscar usuario por ID
        $user = $this->userModel->findById($data->user_id);
        if (!$user) {
            header("HTTP/1.1 404 Not Found");
            echo json_encode(['success' => false, 'message' => 'Usuario no encontrado.']);
            return;
        }

        // Verificar contraseña actual
        if (!password_verify($data->current_password, $user['password'])) {
            header("HTTP/1.1 401 Unauthorized");
            echo json_encode(['success' => false, 'message' => 'Contraseña actual incorrecta.']);
            return;
        }

        // Actualizar contraseña
        if ($this->userModel->updatePassword($data->user_id, $data->new_password)) {
            echo json_encode(['success' => true, 'message' => 'Contraseña actualizada exitosamente.']);
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(['success' => false, 'message' => 'Error al actualizar la contraseña.']);
        }
    }
}
