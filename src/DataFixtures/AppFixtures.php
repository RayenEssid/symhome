
<?php


use App\Entity\Categorie;
use App\Entity\Meuble;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            ['Séjour',  'Canapés, tables basses, meubles TV'],
            ['Chambre', 'Lits, armoires, tables de chevet'],
            ['Bureau',  'Bureaux, chaises ergonomiques, étagères'],
            ['Cuisine', 'Tables, chaises et éléments de cuisine'],
        ];

        $meubles = [
            'Séjour'  => [['Canapé 3 places', 899.99, 10], ['Table basse chêne', 249.99, 25], ['Meuble TV 150cm', 349.99, 15]],
            'Chambre' => [['Lit 160x200', 699.99, 8],       ['Armoire 3 portes', 549.99, 12], ['Table de chevet', 129.99, 30]],
            'Bureau'  => [['Bureau 120cm', 399.99, 20],     ['Chaise ergonomique', 299.99, 18], ['Étagère murale', 89.99, 40]],
            'Cuisine' => [['Table à manger', 459.99, 10],   ['Chaise de cuisine', 99.99, 50],  ['Élément bas 60cm', 199.99, 15]],
        ];

        foreach ($categories as [$nom, $desc]) {
            $cat = new Categorie();
            $cat->setNom($nom)->setDescription($desc);
            $manager->persist($cat);

            foreach ($meubles[$nom] as [$mNom, $prix, $stock]) {
                $meuble = new Meuble();
                $meuble->setNom($mNom)
                    ->setDescription('Description de '.$mNom)
                    ->setPrix($prix)
                    ->setStock($stock)
                    ->setCategorie($cat);
                $manager->persist($meuble);
            }
        }

        $manager->flush();
    }
}