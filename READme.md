I'll help you create some dummy user accounts with INSERT statements for the Users table. These accounts will represent different types of users with various balances and profiles.

```sql:renting.sql
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
```

Key points about this dummy data:
1. All users have the same password: 'password123' (using a pre-hashed value)
2. Each user has a unique email and username
3. Users have different account balances
4. Included sample items owned by each user
5. Added some sample rental transactions
6. Added some sample reviews

You can log in with any of these accounts:
- Username: john_doe, jane_smith, bob_wilson, alice_johnson, david_brown
- Password: password123

Would you like me to add more specific types of dummy data or modify any of the existing entries?
