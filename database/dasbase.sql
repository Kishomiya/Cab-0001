-- -- Create the database
-- CREATE DATABASE taxi_reservation;

-- -- Use the database
-- USE taxi_reservation;
-- -- Database: taxi_reservation_system
-- CREATE TABLE IF NOT EXISTS admins (
--     Username VARCHAR(50) NOT NULL PRIMARY KEY,
--     Password VARCHAR(255) NOT NULL
-- );
-- -- Table structure for passengers
-- CREATE TABLE IF NOT EXISTS passengers (
--     PID INT AUTO_INCREMENT PRIMARY KEY,
--     Name VARCHAR(100) NOT NULL,
--     Address TEXT NOT NULL,
--     Contact VARCHAR(15) NOT NULL,
--     Email VARCHAR(100) NOT NULL,
--     Username VARCHAR(50) NOT NULL,
--     Password VARCHAR(255) NOT NULL
-- );

-- CREATE TABLE IF NOT EXISTS drivers (
--     DID INT AUTO_INCREMENT PRIMARY KEY,
--     Name VARCHAR(100) NOT NULL,
--     Address TEXT NOT NULL,         -- Added to match the registration form
--     Contact VARCHAR(15) NOT NULL,
--     Status ENUM('Available', 'Busy') NOT NULL,  -- Added to match the registration form
--     Location VARCHAR(255) NOT NULL,  -- Added to match the registration form
--     Email VARCHAR(100) NOT NULL,   -- Added to store email addresses
--     Username VARCHAR(50) NOT NULL, -- Added to store usernames
--     Password VARCHAR(255) NOT NULL -- Added to store hashed passwords
-- );
-- -- Table structure for vehicles
-- CREATE TABLE IF NOT EXISTS vehicles (
--     VID INT AUTO_INCREMENT PRIMARY KEY,
--     DID INT NOT NULL,
--     Brand VARCHAR(100) NOT NULL,
--     Model VARCHAR(100) NOT NULL,
--     Color VARCHAR(50) NOT NULL,
--     RegistrationNo VARCHAR(50) NOT NULL UNIQUE,
--     FOREIGN KEY (DID) REFERENCES drivers(DID)
-- );
-- CREATE TABLE vehicle_documentation (
--     VID INT PRIMARY KEY,
--     InsuranceInfo TEXT,
--     RegistrationInfo TEXT,
--     FOREIGN KEY (VID) REFERENCES vehicles(VID)
-- );


-- -- Table structure for taxi_reservations
-- CREATE TABLE IF NOT EXISTS taxi_reservations (
--     TID INT AUTO_INCREMENT PRIMARY KEY,
--     PID INT NOT NULL,
--     DID INT NOT NULL,
--     StartPlace VARCHAR(100) NOT NULL,
--     EndPlace VARCHAR(100) NOT NULL,
--     Fee DECIMAL(10, 2) NOT NULL,
--     Rating ENUM('Excellent', 'Good', 'Average', 'Bad') DEFAULT 'Good',
--     ReservationDate DATETIME DEFAULT CURRENT_TIMESTAMP,
--     FOREIGN KEY (PID) REFERENCES passengers(PID),
--     FOREIGN KEY (DID) REFERENCES drivers(DID)
-- );

-- -- Table structure for payments
-- CREATE TABLE IF NOT EXISTS payments (
--     PayID INT AUTO_INCREMENT PRIMARY KEY,
--     Method ENUM('Cash', 'Card', 'Online') NOT NULL,
--     Fee DECIMAL(10, 2) NOT NULL,
--     PID INT NOT NULL,
--     DID INT NOT NULL,
--     FOREIGN KEY (PID) REFERENCES passengers(PID),
--     FOREIGN KEY (DID) REFERENCES drivers(DID)
-- );

-- -- Table structure for driver_list (view available drivers)
-- CREATE TABLE IF NOT EXISTS driver_list (
--     DLID INT AUTO_INCREMENT PRIMARY KEY,
--     PID INT NOT NULL,
--     DID INT NOT NULL,
--     NotificationStatus ENUM('Sent', 'Pending') DEFAULT 'Pending',
--     FOREIGN KEY (PID) REFERENCES passengers(PID),
--     FOREIGN KEY (DID) REFERENCES drivers(DID)
-- );

-- -- Table structure for pickup_location
-- CREATE TABLE IF NOT EXISTS pickup_location (
--     PLID INT AUTO_INCREMENT PRIMARY KEY,
--     PID INT NOT NULL,
--     Location VARCHAR(255) NOT NULL,
--     Timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
--     FOREIGN KEY (PID) REFERENCES passengers(PID)
-- );

-- -- Table structure for trip_history
-- CREATE TABLE IF NOT EXISTS trip_history (
--     THID INT AUTO_INCREMENT PRIMARY KEY,
--     PID INT NOT NULL,
--     TID INT NOT NULL,
--     Fare DECIMAL(10, 2) NOT NULL,
--     Rating ENUM('Excellent', 'Good', 'Average', 'Bad') DEFAULT 'Good',
--     TripDate DATETIME DEFAULT CURRENT_TIMESTAMP,
--     FOREIGN KEY (PID) REFERENCES passengers(PID),
--     FOREIGN KEY (TID) REFERENCES taxi_reservations(TID)
-- );

-- -- Table structure for fare_estimation
-- CREATE TABLE IF NOT EXISTS fare_estimation (
--     FEID INT AUTO_INCREMENT PRIMARY KEY,
--     PID INT NOT NULL,
--     Distance DECIMAL(10, 2) NOT NULL,
--     Time DECIMAL(10, 2) NOT NULL,
--     EstimatedFare DECIMAL(10, 2) AS (Distance * 10 + Time * 5) STORED,
--     Timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
--     FOREIGN KEY (PID) REFERENCES passengers(PID)
-- );

-- -- Table structure for job_assignments
-- CREATE TABLE IF NOT EXISTS job_assignments (
--     JID INT AUTO_INCREMENT PRIMARY KEY,
--     PID INT NOT NULL,
--     DID INT NOT NULL,
--     Status ENUM('Available', 'Assigned', 'Completed') DEFAULT 'Available',
--     RequestDate DATETIME DEFAULT CURRENT_TIMESTAMP,
--     FOREIGN KEY (PID) REFERENCES passengers(PID),
--     FOREIGN KEY (DID) REFERENCES drivers(DID)
-- );

-- -- Table structure for job_history
-- CREATE TABLE IF NOT EXISTS job_history (
--     JHID INT AUTO_INCREMENT PRIMARY KEY,
--     JID INT NOT NULL,
--     DID INT NOT NULL,
--     Status ENUM('Assigned', 'Completed') NOT NULL,
--     CompletionDate DATETIME DEFAULT CURRENT_TIMESTAMP,
--     FOREIGN KEY (JID) REFERENCES job_assignments(JID),
--     FOREIGN KEY (DID) REFERENCES drivers(DID)
-- );

-- INSERT INTO admins (Username, Password) VALUES ('admin', PASSWORD('admin123'));

-- CREATE TABLE trips (
--     TripID INT AUTO_INCREMENT PRIMARY KEY,
--     DriverID INT NOT NULL,
--     PickupLocation VARCHAR(255) NOT NULL,
--     DropoffLocation VARCHAR(255) NOT NULL,
--     Fare DECIMAL(10, 2) NOT NULL,
--     Date DATETIME NOT NULL,
--     -- You can add more fields as needed, such as passenger info, trip status, etc.
--     FOREIGN KEY (DriverID) REFERENCES drivers(DID)
-- );


-- CREATE TABLE settlements (
--     SettlementID INT AUTO_INCREMENT PRIMARY KEY,
--     TripID INT NOT NULL,
--     SettlementAmount DECIMAL(10, 2) NOT NULL,
--     SettlementDate DATETIME NOT NULL,
--     FOREIGN KEY (TripID) REFERENCES trips(TripID)
-- );


-- ALTER TABLE drivers
-- ADD Latitude DECIMAL(9, 6) NULL,
-- ADD Longitude DECIMAL(9, 6) NULL;


-- -- Insert into trips
-- INSERT INTO trips (DriverID, PickupLocation, DropoffLocation, Fare, Date)
-- VALUES 
-- (1, '123 Main St', '456 Elm St', 25.00, '2024-08-25 14:00:00'),
-- (1, '789 Oak St', '321 Pine St', 30.00, '2024-08-26 10:30:00');

-- -- Insert into settlements
-- INSERT INTO settlements (TripID, SettlementAmount, SettlementDate)
-- VALUES 
-- (1, 20.00, '2024-08-26 15:00:00'),
-- (2, 25.00, '2024-08-27 11:00:00');


-- Create the database
CREATE DATABASE taxi_reservation;

-- Use the database
USE taxi_reservation;

-- Table structure for admins
CREATE TABLE IF NOT EXISTS admins (
    Username VARCHAR(50) NOT NULL PRIMARY KEY,
    Password VARCHAR(255) NOT NULL
);

-- Table structure for passengers
CREATE TABLE IF NOT EXISTS passengers (
    PID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(100) NOT NULL,
    Address TEXT NOT NULL,
    Contact VARCHAR(15) NOT NULL,
    Email VARCHAR(100) NOT NULL,
    Username VARCHAR(50) NOT NULL,
    Password VARCHAR(255) NOT NULL
);

-- Table structure for drivers
CREATE TABLE IF NOT EXISTS drivers (
    DID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(100) NOT NULL,
    Address TEXT NOT NULL,
    Contact VARCHAR(15) NOT NULL,
    Status ENUM('Available', 'Busy') NOT NULL,
    Location VARCHAR(255) NOT NULL,
    Email VARCHAR(100) NOT NULL,
    Username VARCHAR(50) NOT NULL,
    Password VARCHAR(255) NOT NULL
);

-- Table structure for vehicles
CREATE TABLE IF NOT EXISTS vehicles (
    VID INT AUTO_INCREMENT PRIMARY KEY,
    DID INT NOT NULL,
    Brand VARCHAR(100) NOT NULL,
    Model VARCHAR(100) NOT NULL,
    Color VARCHAR(50) NOT NULL,
    RegistrationNo VARCHAR(50) NOT NULL UNIQUE,
    FOREIGN KEY (DID) REFERENCES drivers(DID)
);

-- Table structure for vehicle documentation
CREATE TABLE IF NOT EXISTS vehicle_documentation (
    VID INT PRIMARY KEY,
    InsuranceInfo TEXT,
    RegistrationInfo TEXT,
    FOREIGN KEY (VID) REFERENCES vehicles(VID)
);

-- Table structure for taxi reservations
CREATE TABLE IF NOT EXISTS taxi_reservations (
    TID INT AUTO_INCREMENT PRIMARY KEY,
    PID INT NOT NULL,
    DID INT NOT NULL,
    StartPlace VARCHAR(100) NOT NULL,
    EndPlace VARCHAR(100) NOT NULL,
    Fee DECIMAL(10, 2) NOT NULL DEFAULT 0,
    ReservationDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (PID) REFERENCES passengers(PID),
    FOREIGN KEY (DID) REFERENCES drivers(DID)
);


-- Table structure for payments
CREATE TABLE IF NOT EXISTS payments (
    PayID INT AUTO_INCREMENT PRIMARY KEY,
    Method ENUM('Cash', 'Card', 'Online') NOT NULL,
    Fee DECIMAL(10, 2) NOT NULL,
    PID INT NOT NULL,
    DID INT NOT NULL,
    FOREIGN KEY (PID) REFERENCES passengers(PID),
    FOREIGN KEY (DID) REFERENCES drivers(DID)
);

-- Create the job_assignments table
CREATE TABLE IF NOT EXISTS job_assignments (
    JobID INT AUTO_INCREMENT PRIMARY KEY,
    DriverID INT NOT NULL,
    PassengerID INT NOT NULL,
    PickupLocation VARCHAR(255) NOT NULL,
    Destination VARCHAR(255) NOT NULL,
    PickupTime TIME NOT NULL,
    PickupDate DATE NOT NULL,
    PassengerName VARCHAR(255) NOT NULL,
    PassengerPhone VARCHAR(20) NOT NULL,
    FOREIGN KEY (DriverID) REFERENCES drivers(DID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (PassengerID) REFERENCES passengers(PID) ON DELETE CASCADE ON UPDATE CASCADE
);
ALTER TABLE job_assignments ADD COLUMN Status ENUM('Pending', 'Accepted', 'Canceled') DEFAULT 'Pending';

INSERT INTO admins (Username, Password) VALUES ('admin', PASSWORD('admin123'));

CREATE TABLE IF NOT EXISTS notifications (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    PassengerID INT NOT NULL,
    DriverID INT NOT NULL,
    JobID INT NOT NULL,
    Message TEXT NOT NULL,
    Status ENUM('Unread', 'Read') DEFAULT 'Unread',
    FOREIGN KEY (PassengerID) REFERENCES passengers(PID),
    FOREIGN KEY (DriverID) REFERENCES drivers(DID)
);
