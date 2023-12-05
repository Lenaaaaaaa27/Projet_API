CREATE TABLE USER(
    id serial PRIMARY KEY,
    mail VARCHAR(255),
    password TEXT,
    role INTEGER
);

CREATE TABLE APARTMENT(
    id serial PRIMARY KEY,
    area INTEGER,
    capacity INTEGER,
    address VARCHAR(255),
    disponibility BOOLEAN,
    price FLOAT,
    owner INTEGER REFERENCES USER(id) 
);

CREATE TABLE RESERVATION(
    id serial PRIMARY KEY,
    start_date DATE,
    end_date DATE,
    price FLOAT,
    renter INTEGER REFERENCES USER(id),
    apartment INTEGER REFERENCES APARTMENT(id)
);
