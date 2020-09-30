<?php
if (PHP_SAPI != 'cli') die('cli only');  // php-cli only

/*******************************************************************
  Description:
  - update OpenEdition Journals editorial model to add extra specific options (required by various Edinum projects)
   
  Install:
  - copy or make a symbolic link of the file in the root directory of the Lodel install
  
  Execute:
  - cd PATH_TO_ROOT_LODEL_DIRECTORY
  - php extra_me_upgrade.php mysite # update the site "mysite"
    or
    php nova_me_upgrade.php all # update all sites (excepted site listed in the array $exclude. See below)
  - after execution, this file should be removed from Lodel root directory
 *******************************************************************/

require_once('lodel/install/scripts/me_manipulation_func.php');

define('DO_NOT_DIE', true); // only die of a server error
// define('QUIET', true);   // no output
$exclude = array();         // the $exclude array may contain site names to be excluded from processing at execution with the  parameter "all"

$sites = new ME_sites_iterator($argv, 'errors'); // 'errors' display only errors ot the function ->m()
while ($siteName = $sites->fetch()) {
  if (in_array($siteName, $exclude)) continue;

  print "Creation du groupe d'options 'extra'\n";

  // Vérification de l'existence du groupe d'options
  $extra_group = OG::get('extra');
  if (!$extra_group->error) {
    print "Le groupe d'options 'extra' existe déjà.\n";
  } else {
    // Sinon création du groupe
    $extra_group = OG::create('extra', "Extra");

    if ($extra_group->error) {
      print "Erreur lors de la création du groupe d'options 'extra'.\n";
    } else {
      print "Le groupe d'options 'extra' a été créé.\n";
    }
  }

  // Création des options 

  $options = [
    'doi_prefixe' => ['title'=>'Préfixe des DOI', 'type'=>'tinytext', 'edition'=>'editable', 'editionparams' => '', 'defaultvalue' => '', 'value' => ''],
    'portail_nom' => ['title'=>'Nom du portail', 'type'=>'tinytext', 'edition'=>'editable', 'editionparams' => '', 'defaultvalue' => '', 'value' => ''],
    'portail_url' => ['title'=>'URL du portail', 'type'=>'tinytext', 'edition'=>'editable', 'editionparams' => '', 'defaultvalue' => '', 'value' => ''],
    'oai_id' => ['title'=>'Identifiant dans le serveur OAI-PMH', 'type'=>'tinytext', 'edition'=>'editable', 'editionparams' => '', 'defaultvalue' => '', 'value' => ''],
    'openaire_access_level' => ['title'=>'OpenAIRE Access Level', 'type'=>'list', 'edition'=>'editable', 'editionparams' => 'openAccess, embargoedAccess, restrictedAccess', 'defaultvalue' => 'openAccess', 'value' => 'openAccess'],
  ];

  foreach ($options as $name => $infos) {
    print "Creation de l'option '$name' dans le groupe 'extra'\n";

    // Vérification de l'existence de l'option
    $opt = O::get('extra', $name);
    if (!$opt->error) {
      print "L'option '$name' existe déjà.\n";
    } else {
      // Sinon création du champ
      $opt = O::create('extra', $name, $infos['type'], $infos['title'], [
        'edition'=>$infos['edition'],
        'editionparams'=>$infos['editionparams'],
        'defaultvalue'=>$infos['defaultvalue'],
        'value'=>$infos['value']
      ]);

      if ($opt->error) {
        print "Erreur lors de la création de l'option '$name'.\n";
      } else {
        print "L'option '$name' a été créée.\n";
      }
    }
  }
}