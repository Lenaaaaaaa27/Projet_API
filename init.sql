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
    ('admin@example.com', '50f456c2dcd24e763fb49c5ec2fd5eaf7b57714015bdf94074f5ef40192c3bb2', 3, NULL), -- password : admin
    ('user2@example.com', 'dbcbf2eb12f36623e78c51c672acb590b43e606203a8c8c151346582b31f7d5a', 2, NULL), -- password : password2
    ('user3@example.com', 'a8a4c021e56d9b770907b42c9e4b867d8e78e3d12cdc5f1f575d7313d1731d85', 1, NULL), -- password : password3
    ('user4@example.com', '2acd1f0d60b3ae479ad13c1ae9f61384f7db318bc26bb2a39ba840a01106527c', 2, NULL), -- password : password4
    ('user5@example.com', '91a22c7ec7b6f79d200a665cb3a84bbdbb54200a42015befcad313873bd2edf9', 0, NULL); -- password : password5

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
    ('2023-04-15', '2023-04-25', 800, 5, 5);