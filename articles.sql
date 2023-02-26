CREATE TABLE articles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT NOT NULL,
    contenu_html TEXT NOT NULL,
    date_creation DATETIME NOT NULL
);
