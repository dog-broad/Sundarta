Create DATABASE users_db;\
USE users_db;
                        
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15) NOT NULL, 
    password VARCHAR(255) NOT NULL
);
