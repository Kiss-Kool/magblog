<?php
require_once 'config.php';

define('TABLE_ARTICLES', 'articles');
define('CHAMP_ID', 'id');

// Récupère l'ID de l'article à afficher
$id = $_GET['id'] ?? null;

// Vérifie si l'ID est valide
if (!$id) {
    header('Location: index.php');
    exit;
}

// Récupère l'article correspondant
$stmt = $pdo->prepare('SELECT id, titre, contenu_html, date_creation FROM articles WHERE id = ?');
$stmt->execute([$id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérifie si l'article a été trouvé
if (!$article) {
    header('Location: index.php');
    exit;
}

// Traitement du formulaire de commentaire
if (isset($_POST['submit'])) {
    $pseudo = $_POST['pseudo'];
    $commentaire = $_POST['commentaire'];

    // Vérifie si les champs sont valides
    if ($pseudo && $commentaire) {
        // Ajoute le commentaire dans la base de données avec la date actuelle
        $stmt = $pdo->prepare('INSERT INTO commentaires (pseudo, commentaire, id_article, date_creation) VALUES (?, ?, ?, NOW())');
        $stmt->execute([$pseudo, $commentaire, $id]);

        // Rafraîchit la page pour afficher le commentaire ajouté
        header('Location: article.php?id=' . $id);
        exit;
    }
}

// Récupère tous les commentaires pour cet article
$stmt = $pdo->prepare('SELECT pseudo, commentaire, date_creation FROM commentaires WHERE id_article = ? ORDER BY date_creation DESC');
$stmt->execute([$id]);
$commentaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>magblog</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet"> 
    <link href="https://fonts.googleapis.com/css2?family=Sofia+Sans+Extra+Condensed:wght@300&display=swap" rel="stylesheet"> 
   <link rel="stylesheet" href="css/article.css">
  </head>

  <body>
    <header>
     <h1><a href="index.php">magblog</a></h1>
    </header>

    <nav>
      <ul>
        <li><a href="index.php">Accueil</a></li>
        <li><a href="#">Information</a></li>
        <li><a href="#">Contact</a></li>
      </ul>
    </nav>
    
    <main>
      
      <article class="article">
       <h2><?= htmlspecialchars($article['titre']); ?></h2>
        <div class="date"><?= htmlspecialchars($article['date_creation']); ?></div>
        <div class="contenu"><p><?= $article['contenu_html']; ?></p></div>


</form>
        <h4>Commentaires</h4>
        <?php foreach ($commentaires as $commentaire): ?>
          <article class="commentaire">
            <div class="pseudo"><?= htmlspecialchars($commentaire['pseudo']); ?></div>
            <div class="date"><?= htmlspecialchars($commentaire['date_creation']); ?></div>
            <div class="contenu"><?= htmlspecialchars($commentaire['commentaire']); ?></div>
          </article>
   
        <?php endforeach; ?>
        <h4>Ajouter un commentaire</h4>
        <form method="post">
          <div class="form-group">
            <label for="pseudo">Pseudo :</label>
            <input type="text" id="pseudo" name="pseudo" class="form-control">
          </div>
          <div class="form-group">
            <label for="commentaire">Commentaire :</label>
            <textarea id="commentaire" name="commentaire" class="form-control"></textarea>
          </div>
          <button type="submit" name="submit" class="btn btn-primary">Ajouter</button>
        </form>
      </article>
    <section>
<?php include "includes/widget.php" ?>
 </section>
    </main>

    <!-- Et voici notre pied de page utilisé sur toutes les pages du site -->
    <footer>
      <p>©Copyright 2023 magblog</p>
    </footer>


  </body>
</html>
