# Your Agile Toolkit Project

Welcome to your new Agile Toolkit project. Modify this file to contain more information about your project.

## Install

Generic help on installing Agile-Toolkit based projects can be found
 
 * http://agiletoolkit.org/doc/install/project


```
rm -rf .git
git init
git add .
git commit -m "Initial Commit"
```

* Install [Composer] [0]

```
$ curl -s https://getcomposer.org/installer | php
```

* Update packages

```
$ php composer.phar update
```

## Windows users
This Agile Toolkit setup requires symlinks to be able to access the default Agile Toolkit resources like images, css files etc. How to use symlinks on the Windows platform is described [here][1]. After, you should create the following symlink:

```
cd public/atk4
mklink ../../vendor/atk4/atk4/templates/ templates
```

[0]: http://www.getcomposer.org/
[1]: http://www.howtogeek.com/howto/16226/complete-guide-to-symbolic-links-symlinks-on-windows-or-linux/
