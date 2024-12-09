<?php
class Item {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getFeaturedItems($limit = 6) {
        $sql = "SELECT * FROM Items WHERE availability_status = 'AVAILABLE' 
                ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getLatestItems($limit = 8) {
        $sql = "SELECT * FROM Items WHERE availability_status = 'AVAILABLE' 
                ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addItem($data) {
        try {
            $sql = "INSERT INTO Items (owner_id, name, description, category, daily_rate, 
                    deposit_amount, location, `condition`, insurance_required, max_rental_duration) 
                    VALUES (:owner_id, :name, :description, :category, :daily_rate, 
                    :deposit_amount, :location, :condition, :insurance_required, :max_rental_duration)";
            
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                'owner_id' => $data['owner_id'],
                'name' => $data['name'],
                'description' => $data['description'],
                'category' => $data['category'],
                'daily_rate' => $data['daily_rate'],
                'deposit_amount' => $data['deposit_amount'],
                'location' => $data['location'],
                'condition' => $data['condition'],
                'insurance_required' => $data['insurance_required'],
                'max_rental_duration' => $data['max_rental_duration']
            ]);
        } catch(PDOException $e) {
            return false;
        }
    }

    public function getItemById($id) {
        try {
            $sql = "SELECT * FROM Items WHERE item_id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }

    public function updateItem($id, $data) {
        try {
            $sql = "UPDATE Items SET 
                    name = :name,
                    description = :description,
                    category = :category,
                    daily_rate = :daily_rate,
                    deposit_amount = :deposit_amount,
                    location = :location,
                    `condition` = :condition,
                    insurance_required = :insurance_required,
                    max_rental_duration = :max_rental_duration,
                    availability_status = :availability_status
                    WHERE item_id = :item_id";
            
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                'item_id' => $id,
                'name' => $data['name'],
                'description' => $data['description'],
                'category' => $data['category'],
                'daily_rate' => $data['daily_rate'],
                'deposit_amount' => $data['deposit_amount'],
                'location' => $data['location'],
                'condition' => $data['condition'],
                'insurance_required' => $data['insurance_required'],
                'max_rental_duration' => $data['max_rental_duration'],
                'availability_status' => $data['availability_status']
            ]);
        } catch(PDOException $e) {
            return false;
        }
    }

    public function deleteItem($id, $owner_id) {
        try {
            $sql = "DELETE FROM Items 
                    WHERE item_id = :id AND owner_id = :owner_id 
                    AND NOT EXISTS (
                        SELECT 1 FROM Rental_Transactions 
                        WHERE item_id = :id AND status = 'ACTIVE'
                    )";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                'id' => $id,
                'owner_id' => $owner_id
            ]);
        } catch(PDOException $e) {
            return false;
        }
    }
}
