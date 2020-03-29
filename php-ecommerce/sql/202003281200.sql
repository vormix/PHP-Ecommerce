
CREATE TABLE special_treatment_type
(
  code VARCHAR(50) NOT NULL
  , description VARCHAR(255) NOT NULL
  , special_treatment_name VARCHAR(255) NOT NULL
);
INSERT INTO special_treatment_type (code, description, special_treatment_name) VALUES ('extra-discount', 'Extra Sconto', 'Percentuale');
INSERT INTO special_treatment_type (code, description, special_treatment_name) VALUES ('delayed-payment', 'Pagamento Ritardato', 'Giorni');

CREATE TABLE special_treatment
(
  id INT AUTO_INCREMENT
  , type_code VARCHAR(50) NOT NULL
  , name VARCHAR(255) NOT NULL
  , special_treatment_value VARCHAR(255) NOT NULL
  , PRIMARY KEY (id)
);

CREATE TABLE profile
(
  id INT AUTO_INCREMENT
  , name VARCHAR(255) NOT NULL
  , PRIMARY KEY (id)
);

CREATE TABLE profile_treatments
(
  profile_id INT NOT NULL
  , special_treatment_id INT NOT NULL
);
ALTER TABLE user ADD profile_id INT NULL;