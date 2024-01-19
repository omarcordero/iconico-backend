CREATE TABLE plates (
    plateId INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    categoryId INT,
    name VARCHAR(250),
    description TEXT,
    price DECIMAL(11,2),
    available CHAR(1)
);

CREATE TABLE plate_complement(
    platetId INT,
    complementId INT
);

CREATE TABLE complement(
    complementId INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    name VARCHAR(250),
    price DECIMAL(11,2),
    cantMax INT,
    cantMin INT
);

CREATE TABLE coupon(
    couponId INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    typeCouponId INT,
    valueCoupon INT,
    description TEXT,
    dateExpired DATETIME
);

CREATE TABLE typeCoupon(
    typeCouponId INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    nameType VARCHAR(250)
);

ALTER TABLE plate_complement ADD FOREIGN KEY (platetId) REFERENCES plates(plateId);
ALTER TABLE plate_complement ADD FOREIGN KEY (complementId) REFERENCES complement(complementId);


ALTER TABLE coupon ADD FOREIGN KEY (typeCouponId) REFERENCES typeCoupon(typeCouponId);