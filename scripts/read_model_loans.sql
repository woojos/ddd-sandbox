CREATE TABLE read_loans (
  id CHAR(36) NOT NULL,
  amount INT NOT NULL,
  remaining INT NOT NULL,
  currency VARCHAR(6) NOT NULL,
  status INT NOT NULL,
  PRIMARY KEY (id)
);