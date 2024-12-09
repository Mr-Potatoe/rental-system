-- Users Table
CREATE TABLE Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone_number VARCHAR(20),
    address TEXT,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,
    account_balance DECIMAL(10,2) DEFAULT 0.00
);

-- Items Table
CREATE TABLE Items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    owner_id INT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    category VARCHAR(50),
    daily_rate DECIMAL(10,2) NOT NULL,
    deposit_amount DECIMAL(10,2) NOT NULL,
    availability_status VARCHAR(20) DEFAULT 'AVAILABLE',
    location VARCHAR(100),
    `condition` VARCHAR(50),
    insurance_required TINYINT(1) DEFAULT 0,
    max_rental_duration INT, -- in days
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_rented_date TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (owner_id) REFERENCES Users(user_id)
);

-- Rental Transactions Table
CREATE TABLE Rental_Transactions (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT,
    renter_id INT,
    start_date TIMESTAMP NOT NULL,
    end_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    total_cost DECIMAL(10,2) NOT NULL,
    deposit_paid DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) DEFAULT 'ACTIVE', -- ACTIVE, COMPLETED, CANCELLED, DISPUTED
    pickup_location VARCHAR(100),
    return_location VARCHAR(100),
    additional_notes TEXT,
    insurance_purchased TINYINT(1) DEFAULT 0,
    FOREIGN KEY (item_id) REFERENCES Items(item_id),
    FOREIGN KEY (renter_id) REFERENCES Users(user_id)
);

-- Reviews and Ratings Table
CREATE TABLE Reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT,
    reviewer_id INT,
    reviewee_id INT,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    review_text TEXT,
    review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaction_id) REFERENCES Rental_Transactions(transaction_id),
    FOREIGN KEY (reviewer_id) REFERENCES Users(user_id),
    FOREIGN KEY (reviewee_id) REFERENCES Users(user_id)
);

-- Damage Claims Table
CREATE TABLE Damage_Claims (
    claim_id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT,
    claimer_id INT,
    claim_amount DECIMAL(10,2) NOT NULL,
    claim_description TEXT NOT NULL,
    evidence_urls TEXT,
    status VARCHAR(20) DEFAULT 'PENDING', -- PENDING, APPROVED, REJECTED
    resolution_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaction_id) REFERENCES Rental_Transactions(transaction_id),
    FOREIGN KEY (claimer_id) REFERENCES Users(user_id)
);

-- Payment Transactions Table
CREATE TABLE Payment_Transactions (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    transaction_type VARCHAR(20) NOT NULL, -- DEPOSIT, RENTAL_FEE, DAMAGE_CLAIM, REFUND
    amount DECIMAL(10,2) NOT NULL,
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    related_transaction_id INT,
    payment_method VARCHAR(50),
    status VARCHAR(20) DEFAULT 'COMPLETED',
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

-- Indexes for Performance Optimization
CREATE INDEX idx_items_owner ON Items(owner_id);
CREATE INDEX idx_rental_item ON Rental_Transactions(item_id);
CREATE INDEX idx_rental_renter ON Rental_Transactions(renter_id);
CREATE INDEX idx_reviews_transaction ON Reviews(transaction_id);

-- Dummy Users Data
INSERT INTO Users 
(username, email, password_hash, full_name, phone_number, address, account_balance) 
VALUES 
-- Password for all accounts is 'password123'
('john_doe', 'john@example.com', 
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
 'John Doe', 
 '+1-555-123-4567', 
 '123 Main St, New York, NY 10001', 
 1000.00),

('jane_smith', 'jane@example.com', 
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
 'Jane Smith', 
 '+1-555-234-5678', 
 '456 Oak Ave, Los Angeles, CA 90001', 
 750.50),

('bob_wilson', 'bob@example.com', 
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
 'Bob Wilson', 
 '+1-555-345-6789', 
 '789 Pine Rd, Chicago, IL 60601', 
 250.75),

('alice_johnson', 'alice@example.com', 
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
 'Alice Johnson', 
 '+1-555-456-7890', 
 '321 Elm St, Houston, TX 77001', 
 1500.25),

('david_brown', 'david@example.com', 
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
 'David Brown', 
 '+1-555-567-8901', 
 '654 Maple Dr, Miami, FL 33101', 
 800.00);

-- Add some test items for these users
INSERT INTO Items 
(owner_id, name, description, category, daily_rate, deposit_amount, location, `condition`, insurance_required, max_rental_duration) 
VALUES 
(1, 'Professional Camera', 'Canon EOS R5 with 24-70mm lens', 'Electronics', 
 75.00, 1000.00, 'New York', 'Excellent', 1, 14),

(2, 'Mountain Bike', 'Trek Fuel EX 8 29er', 'Sports', 
 45.00, 500.00, 'Los Angeles', 'Good', 1, 7),

(3, 'Party Tent', '20x20 Wedding Tent with Sides', 'Events', 
 120.00, 300.00, 'Chicago', 'Very Good', 0, 5),

(4, 'Power Tools Set', 'DeWalt 20V MAX Premium Tool Kit', 'Tools', 
 35.00, 250.00, 'Houston', 'Good', 1, 7),

(5, 'Kayak', 'Perception Pescador Pro 12', 'Water Sports', 
 55.00, 400.00, 'Miami', 'Excellent', 1, 5);

-- Add some sample rental transactions
INSERT INTO Rental_Transactions 
(item_id, renter_id, start_date, end_date, total_cost, deposit_paid, status, pickup_location, return_location) 
VALUES 
(1, 2, NOW(), DATE_ADD(NOW(), INTERVAL 3 DAY), 225.00, 1000.00, 'ACTIVE', 
 'New York Camera Shop', 'New York Camera Shop'),

(2, 3, DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY), 
 135.00, 500.00, 'COMPLETED', 'LA Bikes', 'LA Bikes'),

(3, 4, DATE_ADD(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 3 DAY), 
 240.00, 300.00, 'ACTIVE', 'Chicago Event Center', 'Chicago Event Center');

-- Add some reviews
INSERT INTO Reviews 
(transaction_id, reviewer_id, reviewee_id, rating, review_text) 
VALUES 
(2, 3, 2, 5, 'Great bike, well maintained. Owner was very helpful!'),
(2, 2, 3, 4, 'Responsible renter, returned the bike in good condition.');