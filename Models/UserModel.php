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
	 * @return bool True si se creó correctamente, false en caso contrario.
	 */
	public function create($full_name, $email, $password)
	{
		// Hashear la contraseña antes de guardarla por seguridad
		$hashedPassword = password_hash($password, PASSWORD_BCRYPT);
		$defaultRole = 1;

		try {
			$stmt = $this->db->prepare(
				"INSERT INTO users (full_name, email, password, role_id) VALUES (:full_name, :email, :password, :role_id)"
			);
			$stmt->execute([
				':full_name' => $full_name,
				':email' => $email,
				':password' => $hashedPassword,
				':role_id' => $defaultRole
			]);
			return $stmt->rowCount() > 0;
		} catch (PDOException $e) {
			// Manejar el error (ej: email duplicado)
			return false;
		}
	}
}
