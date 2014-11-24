<?php
/**
 * It doesn't created by Vadym Radvansky
 * Date: 4/10/14 2:39 PM
 */
include './deployer.phar';
include './config.php';

// ---------------------------------------------------------------------------------------------------
//
//                local
//
// ---------------------------------------------------------------------------------------------------

$t = new Transport_Local();

$server_deploy_folder = time();
$base_path = getcwd();

$s1 = new Server('My local computer Colubris project installation');
$s1->setTransport($t);
$s1->setBasePath($base_path);

// shared
$s1->task('cmd','mkdir -m 755 ./shared');
$s1->task('cmd','mkdir -m 755 ./shared/db');
$s1->task('cmd','mkdir -m 777 ./shared/upload');

// releases
$s1->task('cmd','mkdir -m 755 ./releases');
$s1->task('cmd',"mkdir releases/$server_deploy_folder");
$s1->task('cmd',"ls -la releases/");
$s1->task('cmd',"git clone -b 4.3 git@github.com:alexeyostapets/colubris.git ./releases/$server_deploy_folder");
$s1->task('cmd',"cd releases/$server_deploy_folder");
//$s1->task('cmd',"git fetch");
//$s1->task('cmd',"git checkout -b 4.3 origin/4.3");
//$s1->task('cmd',"git pull");

// addons
$s1->task('cmd',"cd releases/$server_deploy_folder");
$s1->task('cmd',"mkdir -m 775 addons");
$s1->task('cmd',"cd addons");
$s1->task('cmd',"mkdir -m 775 ./atk4");
$s1->task('cmd',"cd atk4");
$s1->task('cmd',"git clone git@github.com:atk4/agiletoolkit-css.git");
$s1->task('cmd',"cd ..");
$s1->task('cmd',"mkdir -m 775 ./rvadym");
$s1->task('cmd',"cd rvadym");
$s1->task('cmd',"git clone git@github.com:rvadym/cms.git");
$s1->task('cmd',"cd ..");
$s1->task('cmd',"mkdir -m 775 ./rvadym");
$s1->task('cmd',"cd ./rvadym");
$s1->task('cmd',"git clone https://git@github.com/rvadym/tinymce.git");
$s1->task('cmd',"cd ..");
$s1->task('cmd',"git clone git@github.com:atk4/autocomplete.git");
$s1->task('cmd',"git clone git@github.com:KonstantinKolodnitsky/kk_xls.git");
$s1->task('cmd',"git clone git@github.com:rvadym/x_bread_crumb.git");
$s1->task('cmd',"git clone git@github.com:rvadym/x_breakpoint.git");
$s1->task('cmd',"cd ../public");
$s1->task('cmd',"rm font");
$s1->task('cmd',"ln -s ../vendor/atk4/atk4/public/atk4/font font");

// atk4-addons
$s1->task('cmd',"cd $base_path/releases/$server_deploy_folder");
$s1->task('cmd',"git clone git@github.com:atk4/atk4-addons.git");
//$s1->task('git',"clone git@github.com:atk4/atk4-addons.git");

// logs
$s1->task('cmd',"cd $base_path/shared/");
$s1->task('cmd',"mkdir -m 777 ./logs-front");
$s1->task('cmd',"mkdir -m 777 ./logs-api");

// composer
$s1->task('cmd',"rm composer.phar");
$s1->task('cmd',"curl -sS https://getcomposer.org/installer | php");
$s1->task('cmd',"cd $base_path/releases/$server_deploy_folder");
$s1->task('cmd',"ln -s ../../shared/composer.phar composer.phar");
$s1->task('cmd',"php composer.phar install");

// links
$s1->task('cmd',"cd $base_path/releases/$server_deploy_folder");
$s1->task('cmd',"cd public");
$s1->task('cmd',"ln -s ../api api");
$s1->task('cmd',"mkdir rvadym");
$s1->task('cmd',"cd rvadym");
$s1->task('cmd',"ln -sf ../../addons/rvadym/cms/public/ cms");
$s1->task('cmd',"ln -sf ../../addons/rvadym/tinymce/public/ tinymce");
$s1->task('cmd',"cd $base_path/releases/$server_deploy_folder");
$s1->task('cmd',"ln -s ../../shared/logs-front logs");
$s1->task('cmd',"ln -s ../../../shared/logs-api api/logs");
$s1->task('cmd',"ln -s ../../shared/upload upload");
$s1->task('cmd',"ln -s ../../../shared/upload public/upload");
$s1->task('cmd',"ln -s ../../shared/config-frontend.php config.php");
$s1->task('cmd',"cd api");
$s1->task('cmd',"ln -s ../../../shared/config-api.php config.php");
$s1->task('cmd',"cd ..");
$s1->task('cmd',"cd $base_path/releases/$server_deploy_folder/public/");
$s1->task('cmd',"ln -s ../vendor/atk4/atk4/public/atk4/");

// create CURRENT symlink
$s1->task('cmd',"cd $base_path");
$s1->task('cmd',"rm current");
$s1->task('cmd',"ln -s ./releases/$server_deploy_folder/ current");

// ---------------------------------------------------------------------------------------------------
//
//                deployer
//
// ---------------------------------------------------------------------------------------------------

$d = new Deployer();
$d->addServer($s1); // local
$d->run();

// deployer --------------------------------------------------------------------------------------------

