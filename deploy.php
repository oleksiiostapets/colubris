<?php
/**
 * Created by Vadym Radvansky
 * Date: 4/10/14 2:39 PM
 */
include '../deploy_tool/lib/include.php';

$conf = ConfigReader::getInstance()->getConfig();


$profiles = array(
	'local'=>array(),
	'miracle'=>array(),
);

// local


// miracle
$server_deploy_folder = time();
$base_path = '/home/vadym/web/agiletech.ie/colubris43';

$commands = array(
    array('command' => "
        cd $base_path;
        mkdir releases/$server_deploy_folder;
        git clone https://rvadym@github.com/alexeyostapets/colubris.git releases/$server_deploy_folder;
        cd releases/$server_deploy_folder;
        git fetch;
        git checkout -b 4.3 origin/4.3;
        git pull;
        ln -s ../../shared/composer.phar composer.phar;
        composer.phar self-update;
        php composer.phar install;
        rmdir addons;
        mkdir addons;
        cd addons;
        git clone git@github.com:atk4/autocomplete.git;
        git clone git@github.com:KonstantinKolodnitsky/kk_xls.git;
        git clone git@github.com:rvadym/x_bread_crumb.git;
        git clone git@github.com:rvadym/x_breakpoint.git;
        cd ..;
        git clone git@github.com:atk4/atk4-addons.git;
        ln -s ../../shared/config.php config.php;
        ln -s ./vendor/atk4/agiletoolkit-css/framework/font public/font;
        ln -s ../../shared/.htaccess public/.htaccess;
        cd $base_path;
        rm current;
        ln -s ./releases/$server_deploy_folder/ current;
    "),
);
$profiles['miracle']['commands'] = $commands;


//var_dump($profiles[$conf['deploy_profile']]);

$updater = new Updater();
$updater->run($profiles);
unset($updater);

/*


        mkdir $base_path/releases/$server_deploy_folder;
        git clone https://rvadym@github.com/alexeyostapets/colubris.git $base_path/releases/$server_deploy_folder;
        cd $base_path/releases/$server_deploy_folder;
        ln -s $base_path/shared/composer.phar $base_path/releases/$server_deploy_folder/composer.phar;
        php composer.phar install;
        rmdir $base_path/releases/addons;
        git clone git@github.com:atk4/autocomplete.git $base_path/releases/addons/$server_deploy_folder;
        ln -s $base_path/shared/config.php $base_path/releases/$server_deploy_folder/config.php;
        ln -s $base_path/releases/vendor/atk4/agiletoolkit-css/framework/font $base_path/releases/$server_deploy_folder/public/font;
        rm $base_path/current;
        ln -s $base_path/releases/$server_deploy_folder $base_path/current;




*/