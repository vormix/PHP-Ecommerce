CREATE TABLE email
(
    id INT AUTO_INCREMENT
    , subject VARCHAR(255) NOT NULL
    , message TEXT NOT NULL
    , PRIMARY KEY (id) 
);
CREATE TABLE email_recipients
(
  email_id INT NOT NULL
  , recipient_id INT NOT NULL  
);