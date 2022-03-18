<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Difficulty;


class DifficultyFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [UserFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadDifficulty($manager);
    }

    private function loadDifficulty(ObjectManager $manager)
    {
        $list = [
            "Асфальт",
            "Разбитый асфальт",
            "Грунтовка",
            "Разбитая грунтовка",
            "Тропа",
            "Бездорожье",
            "Эндуро",
            "Хард",
            "Триал",
        ];

        foreach($list as $item) {
            $difficulty = new Difficulty();
            $difficulty->setName($item);
            $manager->persist($difficulty);   
        }
        $manager->flush();
    }

    private function loadMarker(ObjectManager $manager)
    {
        $list = [
            "Асфальт",
            "Разбитый асфальт",
            "Грунтовка",
            "Разбитая грунтовка",
            "Тропа",
            "Бездорожье",
            "Эндуро",
            "Хард",
            "Триал",
        ];

        foreach($list as $item) {
            $difficulty = new Marker();
            $difficulty->setName($item);
            $manager->persist($difficulty);   
        }
        $manager->flush();
    }
}
