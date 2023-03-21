<?php

namespace App\DataFixtures;

use App\Entity\Page;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Contracts\Translation\TranslatorInterface;

class PageFixtures extends Fixture
{
    private $translator;
    
    public function __construct(TranslatorInterface $translator){
        $this->translator = $translator;
    }
    public function load(ObjectManager $manager): void
    {
        $home = new Page();
        $home->setTitle($this->translator->trans("Home"));
        $home->setText("<h1>" . $this->translator->trans("Home") . "</h1>");

        $manager->persist($home);

        $manager->flush();
    }
}
