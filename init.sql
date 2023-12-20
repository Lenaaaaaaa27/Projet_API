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

-- Add users
INSERT INTO "USER" (mail, password, role, token) VALUES

    ('admin1@example.com', '4550a29e060f3afdf188820681cfb80aeb74ab15ff8ec9a803a53522eb45dea3', 3, NULL), -- password : admin1
    ('admin2@example.com', '0d1816ef512c48774b1d4ba1bb8fb1e322b2014a7ff6b530f802ccd941e07eed', 3, NULL), -- password : admin2
    ('intern1@example.com', 'ab595b772069682ff10b3c1f513dfe8d09e9ad5c03c4c087964a198c17fff63b', 2, NULL), -- password : intern1
    ('intern2@example.com', '068bf63679445b4b5c2cfdfde96eaad4f4a3b2a07a582563aceaafdb0782fc91', 2, NULL), -- password : intern2
    ('propriétaire1@example.com', 'd8ed1c2dd23018e4902516bb21502621dfd368aaab5849511d39b1a49abad21c', 1, NULL), -- password : propriétaire1
    ('propriétaire2@example.com', '6c2b1c28709107ef7be1286f50ab1918699ee4131bf09eb83e6b3126405b728a', 1, NULL), -- password : propriétaire2
    ('utilisateur1@example.com', 'b67993f186b67de6f64ba551fcb40f0d3d03149c122fbcf6f2ef9b100fa44686', 0, NULL), -- password : utilisateur1
    ('utilisateur2@example.com', '3d828fd621fadbfd4749db46e8b600bc81aa111ec246e3b4a6ea2899eaa9107e', 0, NULL), -- password : utilisateur2
    ('utilisateur3@example.com', '27407d2a06b8b97f2b7e7b0fe5ca5b8056f6e417fd0da1d4065356ee0f85b9e7', 0, NULL), -- password : utilisateur3
    ('utilisateur4@example.com', '96bbbd0cfe601afd59b96fabe82a924324a7524fb1ede91b7d96d5534a0ae73b', 0, NULL); -- password : utilisateur4

-- Add apartments
INSERT INTO APARTMENT (area, capacity, address, disponibility, price, owner) VALUES
    (100, 2, '742 Evergreen Terrace', TRUE, 120.00, 3),
    (135, 3, '124 Conch Street', TRUE, 150.00, 3),
    (65, 1, '221B Baker Street', TRUE, 85.00, 4),
    (253, 5, '4 Privet Drive', FALSE, 265.00, 4),
    (184, 3, '12334 Cantura Street', TRUE, 165.00, 3);

-- Add reservations
INSERT INTO RESERVATION (start_date, end_date, price, renter, apartment) VALUES
    ('2023-01-01', '2023-01-10', 1200, 7, 1),
    ('2023-02-13', '2023-02-20', 595, 7, 3),
    ('2023-03-05', '2023-03-13', 1200, 8, 2),
    ('2023-04-12', '2023-04-15', 495, 9, 5),
    ('2023-04-20', '2023-04-25', 825, 10, 5);