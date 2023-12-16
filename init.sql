CREATE TABLE "USER"(
    id serial PRIMARY KEY,
    mail VARCHAR(255),
    password TEXT,
    role INTEGER,
    token TEXT
);

CREATE TABLE APARTMENT(
    id serial PRIMARY KEY,
    area INTEGER,
    capacity INTEGER,
    address VARCHAR(255),
    disponibility BOOLEAN,
    price FLOAT,
    owner INTEGER REFERENCES "USER"(id) ON DELETE CASCADE
);

CREATE TABLE RESERVATION(
    id serial PRIMARY KEY,
    start_date DATE,
    end_date DATE,
    price FLOAT,
    renter INTEGER REFERENCES "USER"(id) ON DELETE CASCADE,
    apartment INTEGER REFERENCES APARTMENT(id) ON DELETE CASCADE
);

-- Add five users
INSERT INTO "USER" (mail, password, role, token) VALUES
    ('admin@example.com', 'admin', 3, NULL),
    ('user2@example.com', 'password2', 2, NULL),
    ('user3@example.com', 'password3', 1, NULL),
    ('user4@example.com', 'password4', 2, NULL),
    ('user5@example.com', 'password5', 1, NULL);

-- Add five apartments
INSERT INTO APARTMENT (area, capacity, address, disponibility, price, owner) VALUES
    (100, 2, '123 Main St', TRUE, 100.00, 1),
    (150, 4, '456 Oak St', TRUE, 150.00, 2),
    (120, 3, '789 Pine St', TRUE, 120.00, 3),
    (200, 5, '101 Elm St', TRUE, 200.00, 4),
    (80, 1, '202 Maple St', TRUE, 80.00, 5);

-- Add five reservations with calculated prices
INSERT INTO RESERVATION (start_date, end_date, price, renter, apartment) VALUES
    ('2023-01-01', '2023-01-05', 400, 2, 1),
    ('2023-02-15', '2023-02-20', 600, 1, 2),
    ('2023-03-10', '2023-03-13', 360, 3, 3),
    ('2023-04-05', '2023-04-12', 1400, 4, 4),
    ('2023-04-015', '2023-04-25', 800, 5, 5);