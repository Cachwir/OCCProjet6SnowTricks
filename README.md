# OCCProjet6SnowTricks

Projet n°6 du parcours Développeur PHP/Symfony : Creation of a snowboard tricks directory with a communication specificity

Version 1.0

Author: Cachwir

### how to install

- Pull the project (git clone https://github.com/Cachwir/OCCProjet6SnowTricks.git)
- you can rename the folder or leave it as it is.
- cd OCCProjet6SnowTricks or whatever you named it
- run composer install to install the dependancies
- follow this guide for permissions depending on your os : http://symfony.com/doc/current/setup/file_permissions.html (add some add some chmod -R 777) to the following folders :
   - var
   - web/assets/img/avatars
   - web/assets/img/trickImages
- cd app/config and copy parameters.yml.dist to parameters.yml
- feel parameters.yml with your own config
- create your database using ./bin/console doctrine:schema:create
- (optionnal) add default data using ./bin/console doctrine:fixtures:load
- configure your virtual server if you need it. It needs to point to the web folder at the root of the project.
- enjoy~