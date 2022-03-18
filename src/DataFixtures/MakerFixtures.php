<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Difficulty;
use App\Entity\Marker;


class MakerFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [DifficultyFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadMarker($manager);
    }

    private function loadMarker(ObjectManager $manager)
    {
        $userRepository = $manager->getRepository(User::class);
        $user = $userRepository->findOneBy(['username' => 'admin']);

        $difficultyRepository = $manager->getRepository(Difficulty::class);
        $difficulty1 = $difficultyRepository->findOneBy(['name' => 'Грунтовка']);
        $difficulty2 = $difficultyRepository->findOneBy(['name' => 'Разбитая грунтовка']);
        $difficulty3 = $difficultyRepository->findOneBy(['name' => 'Тропа']);

        $list = [
            [
                'name' => "klk овраг",
                'description' => "В сухую погоду заехать можно на любой технике. В сырую погоду заехать тоже не проблема, выехать - не факт",
                'difficulty' => $difficulty2,
                'latitude' => "57.21580177225551",
                'longitude' => "65.2834322914913",
                
                'user' => $user,
            ],
            [
                'name' => "Заброшенная больничка",
                'description' => "Проблем с проездом нет",
                'difficulty' => $difficulty1,
                'latitude' => "57.093147",
                'longitude' => "65.708431",
                
                'user' => $user,
            ],
        ];

        foreach($list as $item) {
            $marker = new Marker();
            $marker->setName($item['name']);
            $marker->setDescription($item['description']);
            $marker->setDifficulty($item['difficulty']);
            $marker->setLatitude($item['latitude']);
            $marker->setLongitude($item['longitude']);
            $marker->setCreatedBy($item['user']);
            $marker->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($marker);   
        }
        $manager->flush();
    }
}
