<?php
class User {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function register($username, $email, $password, $fullName, $phone = null, $address = null) {
        try {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO Users (username, email, password_hash, full_name, phone_number, address) 
                    VALUES (:username, :email, :password_hash, :full_name, :phone, :address)";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password_hash' => $password_hash,
                'full_name' => $fullName,
                'phone' => $phone,
                'address' => $address
            ]);
        } catch(PDOException $e) {
            return false;
        }
    }
    
    public function login($username, $password) {
        try {
            $sql = "SELECT * FROM Users WHERE username = :username AND is_active = 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['username' => $username]);
            
            if($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if(password_verify($password, $user['password_hash'])) {
                    return $user;
                }
            }
            return false;
        } catch(PDOException $e) {
            return false;
        }
    }

    public function verifyPassword($userId, $password) {
        try {
            $sql = "SELECT password_hash FROM Users WHERE user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['user_id' => $userId]);
            
            if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return password_verify($password, $user['password_hash']);
            }
            return false;
        } catch(PDOException $e) {
            return false;
        }
    }

    public function updateProfile($userId, $fullName, $email, $phone, $address, $newPassword = '') {
        try {
            $sql = "UPDATE Users SET 
                    full_name = :full_name,
                    email = :email,
                    phone_number = :phone,
                    address = :address";
            
            $params = [
                'user_id' => $userId,
                'full_name' => $fullName,
                'email' => $email,
                'phone' => $phone,
                'address' => $address
            ];

            if (!empty($newPassword)) {
                $sql .= ", password_hash = :password_hash";
                $params['password_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
            }

            $sql .= " WHERE user_id = :user_id";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch(PDOException $e) {
            return false;
        }
    }

    public function getUserById($userId) {
        try {
            $sql = "SELECT user_id, username, email, full_name, phone_number, registration_date, address, account_balance 
                    FROM Users WHERE user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['user_id' => $userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }
}
