<?php
require_once 'config.php';
// Constantes pour les noms des tables et des champs de la base de données
define('TABLE_ARTICLES', 'articles');
define('CHAMP_ID', 'id');
define('CHAMP_TITRE', 'titre');
define('CHAMP_CONTENU', 'contenu');
define('CHAMP_CONTENU_HTML', 'contenu_html');
define('CHAMP_DATE_CREATION', 'date_creation');

// Vérifie si un nouvel article a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Valide les données saisies par l'utilisateur
    $titre = trim($_POST['titre']);
    $contenu = trim($_POST['contenu']);
    $bbcode_enabled = ($_POST['bbcode'] === '1');
    $date_creation = date('Y-m-d H:i:s');
    
    if (empty($titre) || empty($contenu)) {
        die('Titre et contenu obligatoires');
    }
    
    // Convertit le BBCode en HTML
    $contenu_html = bbcode2html($contenu, $bbcode_enabled);
    
    // Insère le nouvel article dans la base de données
    $stmt = $pdo->prepare('INSERT INTO ' . TABLE_ARTICLES . ' (' . CHAMP_TITRE . ', ' . CHAMP_CONTENU . ', ' . CHAMP_CONTENU_HTML . ', ' . CHAMP_DATE_CREATION . ') VALUES (:titre, :contenu, :contenu_html, :date_creation)');
    $stmt->execute([
        ':titre' => $titre,
        ':contenu' => $contenu,
        ':contenu_html' => $contenu_html,
        ':date_creation' => $date_creation
    ]);
   // Redirige l'utilisateur vers la page d'accueil
header('Location: index.php');
exit;
 
}
// Nombre d'articles par page
$articles_par_page = 5;

// Numéro de la page courante (défaut: 1)
$page_courante = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Calculer l'offset de la première ligne à récupérer dans la base de données
$offset = ($page_courante - 1) * $articles_par_page;

// Récupérer les articles de la page courante
$stmt = $pdo->prepare('SELECT * FROM ' . TABLE_ARTICLES . ' ORDER BY ' . CHAMP_DATE_CREATION . ' DESC LIMIT :limit OFFSET :offset');
$stmt->bindValue(':limit', $articles_par_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Compter le nombre total d'articles
$stmt = $pdo->prepare('SELECT COUNT(*) FROM ' . TABLE_ARTICLES);
$stmt->execute();
$nombre_total_articles = $stmt->fetchColumn();

// Calculer le nombre total de pages
$nombre_total_pages = ceil($nombre_total_articles / $articles_par_page);

// Fonction pour convertir le BBCode en HTML
function bbcode2html($bbcode, $bbcode_enabled) {
    // Échapper les caractères spéciaux pour éviter les injections de code
    $html = htmlspecialchars($bbcode);
    
    // Remplacer les balises BBCode par des balises HTML avec des styles de texte enrichi
    $html = preg_replace('/\[b\](.*?)\[\/b\]/s', '<strong>$1</strong>', $html);
    $html = preg_replace('/\[i\](.*?)\[\/i\]/s', '<em>$1</em>', $html);
    $html = preg_replace('/\[u\](.*?)\[\/u\]/s', '<u>$1</u>', $html);
    
    // Gérer les liens, les images et les blocs de code
    $html = preg_replace('/\[url=(.*?)\](.*?)\[\/url\]/s', '<a href="$1">$2</a>', $html);
    
    // Limiter le nombre d'images affichées à 1
    $html = preg_replace_callback('/\[img\](.*?)\[\/img\]/s', function($match) use (&$image_count) {
        $image_count++;
        return ($image_count <= 1) ? '<img src="' . $match[1] . '">' : '';
    }, $html);
    
    // Limiter le nombre de mots de texte affichés à 1000
    $words = str_word_count(strip_tags($html), 2);
    if (count($words) > 800) {
        $word_limit = array_keys($words)[799];
        $html = substr($html, 0, $word_limit);
        $html = substr($html, 0, strrpos($html, ' ')) . '...';
    }

    // Renvoyer le texte HTML converti, avec les limites d'images et de texte respectées
    return $html;
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>magblog</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet"> 
    <link href="https://fonts.googleapis.com/css2?family=Sofia+Sans+Extra+Condensed:wght@300&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="css/style.css">
  </head>

  <body>
    <header>
      <h1>Magblog</h1>
    </header>

    <nav>
      <ul>
        <li><a href="#">Accueil</a></li>
        <li><a href="#">Information</a></li>
        <li><a href="#">Contact</a></li>
      </ul>
</nav>
        

          <div class="accordion">
    <input type="checkbox" id="section1" class="accordion__input">
    <label for="section1" class="accordion__label">Publier un l'article</label>
    <div class="accordion__content">
  <form method="post" autocomplete="off">
  <label for="titre">Titre de l'article :</label>
  <input type="text" name="titre" id="titre">
  <label for="contenu">Contenu de l'article :</label>
  <textarea name="contenu" id="contenu">[img]Lien de l'image[/img]</textarea>
  <button type="submit">Publier l'article</button>
  </form>
  </div></div>


    <main>
 
      <article>
     <?php foreach ($articles as $article): ?>
        <div class="article">
          <h2><a href="article.php?id=<?= $article['id'] ?>"><?= $article['titre'] ?></a></h2>
          <div class="date"><?= $article['date_creation'] ?></div>
          <div class="contenu">
                      <?php 
    // Vérifie si la variable $article est définie et contient les informations d'un article valide
    if (isset($article) && is_array($article) && array_key_exists('contenu_html', $article)) {
        
        // Vérifie si la longueur du contenu est supérieure à 300 caractères
        if (strlen($article['contenu_html']) > 300) {
           
            // Si oui, affiche les 200 premiers caractères avec un lien "Lire la suite"
            $excerpt = substr($article['contenu_html'], 0, 300) . '...';
            $link = '<div id="read-more"><a href="article.php?id=' . $article['id'] . '">Lire la suite</a></div>';
            echo $excerpt . $link;
            
        } else {
            // Sinon, affiche tout le contenu
            echo $article['contenu_html'];
        }
    }
?>

          </div>
        </div>
      <?php endforeach; ?>
    </div>
      </article>

 <section>
<?php include "includes/widget.php" ?>
 </section>
    </main>
 <?php include "includes/pagination.php" ?>
    <footer>
      <p>©Copyright 2023 magblog</p>
    </footer>
  </body>
</html>
