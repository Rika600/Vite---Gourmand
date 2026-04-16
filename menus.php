<?php
require_once 'src/Database.php';
require_once 'src/Models/Menu.php';

$pdo = Database::getConnection(); /*crée la connexion a la base de  données* $pdo=variable qui contient la connection */
$menuModel = new Menu($pdo);
$menus = $menuModel->getAll();
?>

<h1>Nos Menus</h1>

<?php foreach ($menus as $menu): ?>

    <h2><?= htmlspecialchars($menu['titre']) ?></h2>
   

    <img 
     src="<?=  htmlspecialchars($menu['image_princiaple']) ?>
     alt="image menu"
     style="width:300px";
     >

     <p>Prix : <?= htmlspecialchars($menu['prix-min']) / $menu['nombre_personnes_min']; ?></p>
     <?php $prixParPersonne = $menu['prix-min'] / $menu['nombre_personnes_min']; ?>
     <p>Prix par personne :<?= round($prixParPersonne, 2) ?> €</p>
     


 <?php endforeach; ?>