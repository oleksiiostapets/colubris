<?php
/**
 * Created by Vadym Radvansky
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

$migr = new Service_AgileToolkit_Migrator('mysql -uvadym -p1 -hlocalhost colubris');
$migr->setMigrationPath($base_path.'/releases/'.$server_deploy_folder.'/doc/dbupdates');
$migr->setMigrationStatusesPath($base_path.'/shared/db');

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
$s1->task('cmd',"git clone git@github.com:alexeyostapets/colubris.git ./releases/$server_deploy_folder");
$s1->task('cmd',"cd releases/$server_deploy_folder");
$s1->task('cmd',"git fetch");
$s1->task('cmd',"git checkout -b 4.3 origin/4.3");
$s1->task('cmd',"git pull");

// addons
$s1->task('cmd',"mkdir addons");
$s1->task('cmd',"cd addons");
$s1->task('cmd',"git clone git@github.com:atk4/autocomplete.git");
$s1->task('cmd',"git clone git@github.com:KonstantinKolodnitsky/kk_xls.git");
$s1->task('cmd',"git clone git@github.com:rvadym/x_bread_crumb.git");
$s1->task('cmd',"git clone git@github.com:rvadym/x_breakpoint.git");

// logs
$s1->task('cmd',"cd $base_path/shared/");
$s1->task('cmd',"mkdir -m 777 logs");

// composer
$s1->task('cmd',"rm composer.phar");
$s1->task('cmd',"curl -sS https://getcomposer.org/installer | php");
$s1->task('cmd',"cd $base_path/releases/$server_deploy_folder");
$s1->task('cmd',"ln -s ../../shared/composer.phar composer.phar");
$s1->task('cmd',"php composer.phar install");

// atk4-addons
$s1->task('cmd',"cd $base_path/releases/$server_deploy_folder");
$s1->task('cmd',"git clone git@github.com:atk4/atk4-addons.git");

// links
$s1->task('cmd',"cd $base_path/releases/$server_deploy_folder");
$s1->task('cmd',"ln -s ../../shared/logs logs");
$s1->task('cmd',"ln -s ../../shared/upload upload");
$s1->task('cmd',"ln -s ../../../shared/upload public/upload");
$s1->task('cmd',"ln -s ../../shared/config.php config.php");
$s1->task('cmd',"ln -s ../../../shared/config.php api/config.php");

// final
$s1->task('cmd',"cd $base_path");
$s1->task('cmd',"rm current");
$s1->task('cmd',"ln -s ./releases/$server_deploy_folder/ current");


// ---------------------------------------------------------------------------------------------------
//
//                celestial
//
// ---------------------------------------------------------------------------------------------------

$t = new Transport_SSH();
//$t->setConnectedSSH($ssh);
$t
    ->setHost('celestial.agile55.com')
    ->setUsername($config['ssh_username'])
    ->setPrivateKeyPath($config['ssh_key_path'])
;

$server_deploy_folder = time();
$base_path = '/www/colubris43.agile55.com';

//$migr = new Service_AgileToolkit_Migrator('mysql -uvadym -p1 -hlocalhost colubris');
//$migr->setMigrationPath($base_path.'/releases/'.$server_deploy_folder.'/doc/dbupdates');
//$migr->setMigrationStatusesPath($base_path.'/shared/db');

$s2 = new Server('Celestial Colubris43 project installation');
$s2->setTransport($t);
$s2->setBasePath($base_path);

// shared
$s2->task('cmd','mkdir -m 755 ./shared');
$s2->task('cmd','mkdir -m 755 ./shared/db');
$s2->task('cmd','mkdir -m 777 ./shared/upload');

// releases
$s2->task('cmd','mkdir -m 755 ./releases');
$s2->task('cmd',"mkdir releases/$server_deploy_folder");
$s2->task('cmd',"ls -la releases/");
$s2->task('git',"clone -b 4.3 https://".$config['github_username']."@github.com/alexeyostapets/colubris.git ./releases/$server_deploy_folder",array('pass'=>$config['ssh_password']));
$s2->task('cmd',"cd releases/$server_deploy_folder");
//$s2->task('cmd',"git fetch");
//$s2->task('cmd',"git checkout -b 4.3 origin/4.3");
//$s2->task('cmd',"git pull");

// addons
$s2->task('cmd',"mkdir -m 775 addons");
$s2->task('cmd',"cd addons");
$s2->task('cmd',"mkdir -m 775 ./atk4");
$s2->task('cmd',"cd ./atk4");
$s2->task('git',"clone https://github.com/atk4/agiletoolkit-css.git");
$s2->task('cmd',"cd ..");
$s2->task('git',"clone https://".$config['github_username']."@github.com/atk4/autocomplete.git",array('pass'=>$config['ssh_password']));
$s2->task('git',"clone https://".$config['github_username']."@github.com/KonstantinKolodnitsky/kk_xls.git",array('pass'=>$config['ssh_password']));
$s2->task('git',"clone https://".$config['github_username']."@github.com/rvadym/x_bread_crumb.git",array('pass'=>$config['ssh_password']));
$s2->task('git',"clone https://".$config['github_username']."@github.com/rvadym/x_breakpoint.git",array('pass'=>$config['ssh_password']));
$s2->task('cmd',"cd ../public");
$s2->task('cmd',"rm font");
$s2->task('cmd',"ln -s ../vendor/atk4/atk4/public/atk4/font font");

// atk4-addons
$s2->task('cmd',"cd $base_path/releases/$server_deploy_folder");
$s2->task('git',"clone https://".$config['github_username']."@github.com/atk4/atk4-addons.git",array('pass'=>$config['ssh_password']));
//$s2->task('git',"clone git@github.com:atk4/atk4-addons.git");

// logs
$s2->task('cmd',"cd $base_path/shared/");
$s2->task('cmd',"mkdir -m 777 logs");

// composer
$s2->task('cmd',"rm composer.phar");
$s2->task('cmd',"curl -sS https://getcomposer.org/installer | php");
$s2->task('cmd',"cd $base_path/releases/$server_deploy_folder");
$s2->task('cmd',"ln -s ../../shared/composer.phar composer.phar");
$s2->task('cmd',"php composer.phar install");

// links
$s2->task('cmd',"cd $base_path/releases/$server_deploy_folder");
$s2->task('cmd',"cd public");
$s2->task('cmd',"ln -s ../api api");
$s2->task('cmd',"cd ..");
$s2->task('cmd',"ln -s ../../shared/logs logs");
$s2->task('cmd',"ln -s ../../shared/upload upload");
$s2->task('cmd',"ln -s ../../../shared/upload public/upload");
$s2->task('cmd',"ln -s ../../shared/config.php config.php");
$s2->task('cmd',"cd api");
$s2->task('cmd',"ln -s ../../../shared/config.php config.php");
$s2->task('cmd',"cd ..");
$s2->task('cmd',"cd $base_path/releases/$server_deploy_folder/public/");
$s2->task('cmd',"ln -s ../vendor/atk4/atk4/public/atk4/");

// final
$s2->task('cmd',"cd $base_path");
$s2->task('cmd',"rm current");
$s2->task('cmd',"ln -s ./releases/$server_deploy_folder/ current");




// ---------------------------------------------------------------------------------------------------
//
//                deployer
//
// ---------------------------------------------------------------------------------------------------

$d = new Deployer();
//$d->addServer($s1); // local
$d->addServer($s2);   // celestial
$d->run();

// deployer --------------------------------------------------------------------------------------------

