<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>File Explorer2</title>
  </head>

  <body>
    <?php
    session_start();
      // Ouvrir start au lancement du site
      if(!is_dir('start')) {  // si dossier start n'existe pas
      mkdir('start'); // le créer
      }

    // variable contenant le current work directory
    if(!isset($_GET['cwd'])) {
      $cwd = getcwd().DIRECTORY_SEPARATOR. 'start';
    } else {
      $cwd = $_GET['cwd'];
    }

    $start = getcwd().DIRECTORY_SEPARATOR. 'start';
    echo $cwd."<br>";  // affiche le répertoire en cours
    $ariane = (explode(DIRECTORY_SEPARATOR,$cwd));
    $path = "";

    // INPUT NAV + AFFICHAGE OPTIONS
    echo"<label for='create_dir'></label>
    <input type='hidden' name='cwd' form='ch_cwd' value='".$cwd."'>
    <input type='text' placeholder= 'your new directory' name='create_dir' form='ch_cwd'>
    <button type='submit' form='ch_cwd'>Create</button><br>
    <input type='text' placeholder= 'your new file' name='create_file' form='ch_cwd'>
    <button type='submit' form='ch_cwd'>Create</button><br>
    <label for='display_hidden_files'>Display hidden files</label>
    <input type='checkbox' name='display_hidden_files' form='ch_cwd' value='checked'>
    <button type='submit' form='ch_cwd'>Ok</button>
    <button type='submit' form='ch_cwd' name='action' value='paste'>Paste</button>";

    // GESTION CHECKBOX
    $hidden_files = NULL;
    $hidden_files = isset($_GET['display_hidden_files']);

    // CREER NOUVEAU DOSSIER
      if(isset($_GET['create_dir']) && $_GET['create_dir']!=NULL){
        $new_dir = $_GET['create_dir'];
        $wheretocreate = $_GET['cwd'];
        if (!is_dir($new_dir)) {
          mkdir($wheretocreate.DIRECTORY_SEPARATOR.$new_dir, 0777);
        } else {
          echo "Ce dossier existe déjà";
        }
      }

      // CREER NOUVEAU FICHIER
      if(isset($_GET['create_file']) && $_GET['create_file']!=NULL){
        $new_dir = $_GET['create_file'];
        $wheretocreate = $_GET['cwd'];
        chdir($wheretocreate);
        if (!is_file($new_dir)) {
          fopen($new_dir, 'c+b');
        } else {
          echo "Ce fichier existe déjà";
        }
      }

      //VARIABLE DE SESSION FICHIER A COPIER
      if(isset($_GET['item_to_copy'])){
        $_SESSION['copied'] = $_GET['item_to_copy'];
      }

      // COPIER/COLLER UN FICHIER
      if (isset($_GET['action'])) {
        if(!empty($_SESSION['copied'])){
        $my_item = $_SESSION['copied'];
        $my_item_name = explode(DIRECTORY_SEPARATOR,$my_item);
        $my_item_name = end($my_item_name);
        $file_copy = $cwd.DIRECTORY_SEPARATOR.$my_item_name."(copie)";
        copy($my_item, $file_copy);
        session_destroy();
        }
        else{
          echo "Rien à coller";
        }
      }


      // RENOMMER
      if(isset($_GET['item_to_rename'])) { // si lien rename est cliqué
        echo "<form method='POST'>";
        echo "<input type='text' placeholder='new name' name='renaming'>";
        echo "<button type='submit'>Ok</button>";
        echo "</form>";
      }
        if (isset($_POST['renaming']) ) { // si input validé
          $my_item = $_GET['item_to_rename'];
          $rename_file = $_POST['renaming']; // récupère la valeur de l'input
          rename($my_item, $cwd.DIRECTORY_SEPARATOR.$rename_file);
        }

      // MASQUER UN FICHIER
      if (isset($_GET['item_to_hide'])) {
        $my_item = $_GET['item_to_hide'];
        $hide_item = explode(DIRECTORY_SEPARATOR, $my_item);
        $editing_name = "." . end($hide_item);
        if ($editing_name == ".TRASH") {
          echo "Vous ne pouvez pas masquer cet élément";
        } else {
        rename($my_item, $cwd.DIRECTORY_SEPARATOR.$editing_name);
      }
    }

    // SUPPRIMER UN FICHIER
    if (isset($_GET ['item_to_delete'])) {
    $my_item = $_GET['item_to_delete'];
    $my_item_name = explode(DIRECTORY_SEPARATOR,$my_item);
    $my_item_name = end($my_item_name);
    $trash = $start.DIRECTORY_SEPARATOR.'TRASH'.DIRECTORY_SEPARATOR.$my_item_name;
    if (filetype($my_item) == "file") {
      copy($my_item, $trash);
      unlink($my_item);
      echo "L'élément a bien été supprimé !";
    } else {
      echo "impossible de supprimer un dossier pour le moment";
  }
}

    //FORM GLOBAL
    echo "<form method='GET' id='ch_cwd'>";

    // FIL D'ARIANE

    foreach ($ariane as $value) {
    $path .= $value.DIRECTORY_SEPARATOR;
    if(strstr($path, 'start')){ // afficher chemin à partir de start
    echo "<button type='submit' form='ch_cwd' class='crumbs' name='cwd' value='". substr($path, 0, -1) ."'>"; // echo path sous forme de btn
    echo $value;
    echo "</button>";
    }
  } // END FOREACH ARIANE


    $content = scandir($cwd); // afficher le contenu du dossier (ordre asendant par défaut)
    foreach ($content as &$value) {  // masquer . et ..
      // MASQUER REPERTOIRE + FICHIERS CACHES
      if($value == '.' || $value == '..') { //si nom du fichier = . ou ..
        echo ' ';
      } elseif($hidden_files == NULL && $value[0] == '.') { // si checkbox pas cochée et fichier commence par un .
        echo '';  //n'affiche pas le fichier caché
      } else {
        $my_item = $cwd.DIRECTORY_SEPARATOR.$value ; // récupère le cwd
        // AFFICHAGE DOSSIERS/FICHIERS
        if(filetype($my_item) == "file") { //FICHIERS
          echo "<br>" . "<a href='display.php?file_name=".$my_item."' target='blank'><img src='file.png'><br>".$value."</a>";
        } elseif ($value === "TRASH") { //CORBEILLE
          echo "<br>" . "<button type='submit' name='cwd' value='".$cwd.DIRECTORY_SEPARATOR.$value."'><img src='trash.png'<br>" . $value. "</button>";
        } else { //DOSSIERS
        echo "<br>" . "<button type='submit' name='cwd' value='".$cwd.DIRECTORY_SEPARATOR.$value."'><img src='dir.png'<br>" . $value. "</button>";
      }
      // AFFICHAGE ACTIONS
      if ($value === "TRASH") {
        echo '';
      } else {
        echo "<br><button type='submit' name='item_to_copy' value='".$my_item."' form='ch_cwd' class='action'>copy</button>
        <button type='submit' name='item_to_rename' value='".$my_item."'
        form='ch_cwd' class='action'>rename</button>
        <button type='submit' name='item_to_hide' value='".$my_item."' form='ch_cwd' class='action'>hide</button>
        <button type='submit' name='item_to_delete' value='".$my_item."' form='ch_cwd' class='action'>delete</button>";
    }

}
    } //END OF FOREACH
    echo "</form>";


     ?>
  </body>
</html>
