<?php
require_once __DIR__ . '/../Core/Database.php';

class UserModel
{
	private $db;

	public function __construct()
	{
		$this->db = Database::getInstance();
	}

	/**
	 * Busca un usuario por su email.
	 * @param string $email
	 * @return mixed El usuario si se encuentra, de lo contrario false.
	 */
	public function findByEmail($email)
	{
		try {
			$stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
			$stmt->execute([':email' => $email]);
			return $stmt->fetch();
		} catch (PDOException $e) {
			// Manejar el error apropiadamente
			return false;
		}
	}

	/**
	 * Crea un nuevo usuario en la base de datos.
	 * @param string $full_name
	 * @param string $email
	 * @param string $password
	 * @param int $role_id (Optional) Default 1 (User)
	 * @return bool True si se creó correctamente, false en caso contrario.
	 */
	public function create($full_name, $email, $password, $role_id = 1)
	{
		// Hashear la contraseña antes de guardarla por seguridad
		$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

		try {
			$stmt = $this->db->prepare(
				"INSERT INTO users (full_name, email, password, role_id) VALUES (:full_name, :email, :password, :role_id)"
			);
			$stmt->execute([
				':full_name' => $full_name,
				':email' => $email,
				':password' => $hashedPassword,
				':role_id' => $role_id
			]);
			return $stmt->rowCount() > 0;
		} catch (PDOException $e) {
			// Manejar el error (ej: email duplicado)
			return false;
		}
	}

	/**
	 * Obtiene todos los usuarios.
	 * @return array Lista de usuarios o array vacío.
	 */
	public function findAll()
	{
		try {
			$stmt = $this->db->prepare("SELECT user_id as id, full_name as username, email, 
										CASE WHEN role_id = 2 THEN 'admin' ELSE 'user' END as user_type 
										FROM users ORDER BY user_id");
			$stmt->execute();
			return $stmt->fetchAll();
		} catch (PDOException $e) {
			return [];
		}
	}

	/**
	 * Elimina un usuario por su ID.
	 * @param int $id
	 * @return bool True si se eliminó correctamente.
	 */
	public function deleteById($id)
	{
		try {
			$stmt = $this->db->prepare("DELETE FROM users WHERE user_id = :id");
			$stmt->execute([':id' => $id]);
			return $stmt->rowCount() > 0;
		} catch (PDOException $e) {
			return false;
		}
	}

	/**
	 * Busca un usuario por su ID.
	 * @param int $id
	 * @return array|null
	 */
	public function findById($id)
	{
		try {
			$stmt = $this->db->prepare("SELECT * FROM users WHERE user_id = :id");
			$stmt->execute([':id' => $id]);
			return $stmt->fetch();
		} catch (PDOException $e) {
			return null;
		}
	}

	/**
	 * Actualiza la contraseña de un usuario.
	 * @param int $user_id
	 * @param string $new_password
	 * @return bool
	 */
	public function updatePassword($user_id, $new_password)
	{
		$hashedPassword = password_hash($new_password, PASSWORD_BCRYPT);
		try {
			$stmt = $this->db->prepare("UPDATE users SET password = :password WHERE user_id = :user_id");
			$stmt->execute([
				':password' => $hashedPassword,
				':user_id' => $user_id
			]);
			return $stmt->rowCount() > 0;
		} catch (PDOException $e) {
			return false;
		}
	}
}
