CREATE TABLE messages (
   N int(11) NOT NULL AUTO_INCREMENT,
   time TIMESTAMP NOT NULL,
   nickname varchar(255) NOT NULL,
   message  varchar(255),
   PRIMARY KEY(N)
);
