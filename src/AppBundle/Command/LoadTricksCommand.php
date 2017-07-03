<?php

namespace AppBundle\Command;

use AppBundle\Entity\TrickPost;
use AppBundle\Entity\TrickTag;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class LoadTricksCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:load-tricks')

            // the short description shown while running "php bin/console list"
            ->setDescription('Loads tricks into the database, from a yaml file.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('
            Loads tricks into the database, from a yaml file. The file should be called "tricks.yml" and located at the root of the project.
            
            Each trick should be described as follow :
            
            trick_unique_key:
                author: (email) the author\'s email
                name: (string) the trick\'s unique name
                introduction: (string) a quick introduction about the trick
                descritpion: (string) the full description of the trick
                tags: (array of tags names) ["tag_1", "tag_2", ...]
                
            The possible tags are : Regular, Goofy, Switch, Grab, Rotation, Flip, Rotation désaxée, Slide, One foot and Old school.
            ')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Executing app:load-tricks...');
        $output->writeln('================================================');
        $output->writeln('================================================');
        // ...

        // access the container using getContainer()
        $root_dir = $this->getContainer()->get('kernel')->getRootDir();
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $validator = $this->getContainer()->get('validator');

        $filename = $root_dir . "/../tricks.yml";

        if (!file_exists($filename)) {
            $output->writeln('ERROR : cannot find the file '. $filename);
        } else {
            $tricks = Yaml::parse(file_get_contents($filename));

            foreach ($tricks as $key => $trick) {
                $creationData = time();
                $author = isset($trick["author"]) ? $trick["author"] : null;
                $name = isset($trick["name"]) ? $trick["name"] : null;
                $introduction = isset($trick["introduction"]) ? $trick["introduction"] : null;
                $description = isset($trick["description"]) ? $trick["description"] : null;
                $tags = isset($trick["tags"]) ? $trick["tags"] : null;

                if ($author === null
                    || $name === null
                    || $introduction === null
                    || $description === null
                    || empty($tags)) {
                    $output->writeln('Trick : '. $key .' - ERROR: the author, name, introduction, description and tags cannot be null or empty.');
                } else {

                    $trickEntity = new TrickPost();
                    $trickEntity->setName($name);
                    $trickEntity->setIntroduction($introduction);
                    $trickEntity->setDescription($description);

                    $user = $em->getRepository("AppBundle:User")->findOneBy(["email" => $author]);
                    if (!$user instanceof User) {
                        $output->writeln('Trick : '. $key .' - ERROR: the user '. $author .' does not exist. An email is asked for this field.');
                    } else {

                        $trickEntity->setAuthor($user);

                        $tagsEntities = [];
                        $error = false;

                        foreach ($tags as $tag) {
                            $tagEntity = $em->getRepository("AppBundle:TrickTag")->findOneBy(["name" => $tag]);
                            if (!$tagEntity instanceof TrickTag) {
                                $output->writeln('Trick : '. $key .' - ERROR: the tag '. $tag .' does not exist. An tag name is asked for this field.');
                                $error = true;
                            } else {
                                $tagsEntities[] = $tagEntity;
                            }
                        }

                        if (!$error) {

                            $trickEntity->setTags($tagsEntities);
                            $errors = $validator->validate($trickEntity);

                            if (count($errors)) {
                                $output->writeln('Trick : '. $key .' - ERROR: the trick couldn\'t be created.');
                            } else {
                                $em->persist($trickEntity);
                                $em->flush();

                                $output->writeln('Trick : '. $key .' - SUCCESS!');
                            }
                        }
                    }
                }

                $output->writeln("================================================");
            }

            $output->writeln("================================================");
            $output->writeln('Job\'s done !');
        }
    }
}