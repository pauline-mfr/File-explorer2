<link rel="stylesheet" type="text/css" href="main.css">

<?php
//GESTION LECTURE DES FICHIERS
$my_item = $_GET["file_name"]; // cwd prend la valeur passée dans l'url (donc le lien du fichier sélectionné)

$file_scan = new SplFileInfo($my_item ); // objet PHP, SplFileInfo va "scanner" le fichier pour fournir informations sur celui-ci
$file_ext = ($file_scan->getExtension()); // on vient sélectionner l'extension dans liste des infos fournies par SplFileInfo

//SI TEXT

if($file_ext=="txt"){ // si c'est un fichier text
$file_open = fopen($my_item, "r") or die("Unable to open file!"); // on l'ouvre avec fopen(chemin vers le fichier, mode de lecture)  __ Sinon affiche un message d'erreur
   echo fread($file_open,filesize($my_item)); // fread lit le fichier ouvert dans sa totalité (filesize de $my_item)
   fclose($file_open); //fonction qui ferme le fichier
 }

// SI IMG
 else {
   $cwd_for_img = explode(DIRECTORY_SEPARATOR, $my_item, 6); // cwd transformé en tableau + reste du cwd divisé en 6 indexs
   //Array ( [0] => C: [1] => wamp64 [2] => www [3] => pauline [4] => File-explorer  [5] => start\start1\fichier.png )
   $src_img = $cwd_for_img[5]; // tout le cwd après le "File explorer"
    echo '<img src="'.$src_img.'" />'; // affiche l'img avec le chemin raccourci comme src
 }
