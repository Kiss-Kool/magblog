CREATE TABLE commentaires (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pseudo VARCHAR(255) NOT NULL,
    commentaire TEXT NOT NULL,
    date_creation DATETIME NOT NULL,
    id_article INT NOT NULL,
    FOREIGN KEY (id_article) REFERENCES articles(id)
);

