<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\Meuble;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // ── Utilisateurs ──
        $users = [];
        $regularUsers = [];
        
        // Utilisateurs standards conserves pour les tests.
        $userEmails = [
            ['ahmed@example.com',      'Karim',    'Ahmed'],
            ['fatma@example.com',      'Morales',  'Fatma'],
            ['aziz@example.com',       'User',     'Aziz'],
        ];

        foreach ($userEmails as [$email, $nom, $prenom]) {
            $user = new User();
            $user->setEmail($email)
                 ->setNom($nom)
                 ->setPrenom($prenom)
                 ->setIsVerified(true)
                 ->setPassword('password123');
            $manager->persist($user);
            $users[$email] = $user;
            $regularUsers[] = $user;
        }

        // Administrateurs de demonstration.
        $adminAccounts = [
            ['rayenessid15@gmail.com', 'Essid', 'Rayen', 'essid'],
            ['adem.essid@example.com', 'Essid', 'Adem', 'essid'],
            ['ameni.wesleti@example.com', 'Wesleti', 'Ameni', 'wesleti'],
        ];

        foreach ($adminAccounts as [$email, $nom, $prenom, $password]) {
            $admin = new User();
            $admin->setEmail($email)
                  ->setNom($nom)
                  ->setPrenom($prenom)
                  ->setRoles(['ROLE_ADMIN'])
                  ->setIsVerified(true)
                  ->setPassword($password);
            $manager->persist($admin);
            $users[$email] = $admin;
        }

        $imageBySeed = [
            'canape' => 'canape.jpg',
            'canape-angle' => 'canape.jpg',
            'table' => 'table.jpg',
            'table-marbre' => 'table.jpg',
            'meuble-tv' => 'meuble-tv.jpg',
            'meuble-tv-2' => 'meuble-tv.jpg',
            'fauteuil' => 'fauteuil.jpg',
            'fauteuil-relax' => 'fauteuil.jpg',
            'lampadaire' => 'table.jpg',
            'etageres' => 'bibliotheque.jpg',
            'pouf' => 'fauteuil.jpg',
            'tapis' => 'table.jpg',
            'lustre' => 'table.jpg',
            'console' => 'commode.jpg',
            'lit' => 'lit.jpg',
            'lit-140' => 'lit.jpg',
            'lit-bateau' => 'lit.jpg',
            'armoire' => 'armoire.jpg',
            'armoire-2' => 'armoire.jpg',
            'commode' => 'commode.jpg',
            'commode-scand' => 'commode.jpg',
            'chevet' => 'chevet.jpg',
            'chevet-pied' => 'chevet.jpg',
            'miroir' => 'armoire.jpg',
            'tete-lit' => 'lit.jpg',
            'housse' => 'lit.jpg',
            'parure' => 'lit.jpg',
            'pouf-ottoman' => 'commode.jpg',
            'bureau' => 'bureau.jpg',
            'bureau-droit' => 'bureau.jpg',
            'bureau-gaming' => 'bureau.jpg',
            'bureau-debout' => 'bureau.jpg',
            'chaise-bureau' => 'chaise-bureau.jpg',
            'chaise-gaming' => 'chaise-bureau.jpg',
            'bibliotheque' => 'bibliotheque.jpg',
            'bibliotheque-3' => 'bibliotheque.jpg',
            'caisson' => 'caisson.jpg',
            'caisson-2' => 'caisson.jpg',
            'lampe-bureau' => 'bureau.jpg',
            'rangement-mural' => 'bibliotheque.jpg',
            'tapis-sol' => 'bureau.jpg',
            'boites-rangement' => 'caisson.jpg',
            'table-cuisine' => 'table-cuisine.jpg',
            'table-6places' => 'table-cuisine.jpg',
            'table-ronde' => 'table-cuisine.jpg',
            'chaise-bar' => 'chaise-bar.jpg',
            'chaise-bar-4' => 'chaise-bar.jpg',
            'chaise-cuisine' => 'chaise-bar.jpg',
            'ilot' => 'ilot.jpg',
            'ilot-compact' => 'ilot.jpg',
            'buffet' => 'buffet.jpg',
            'buffet-haut' => 'buffet.jpg',
            'desserte' => 'ilot.jpg',
            'etagere-cuisine' => 'buffet.jpg',
            'horloge' => 'table-cuisine.jpg',
            'suspensions' => 'table-cuisine.jpg',
        ];

        // ── Catégories & meubles ──
        // Format : [nom, description, prix, stock, seed_image]
        $data = [
            'Séjour' => [
                ['Canapé 3 places',         'Canapé confortable en tissu gris anthracite, pieds en bois', 899.99, 15,  'canape'],
                ['Canapé d\'angle',         'Canapé d\'angle convertible en lin naturel, très confortable', 1299.00, 8, 'canape-angle'],
                ['Table basse chêne',       'Table basse en bois massif de chêne, finition naturelle',    299.00, 12, 'table'],
                ['Table basse marbre',      'Table basse avec plateau en marbre blanc et pieds laiton',   549.00, 6,  'table-marbre'],
                ['Meuble TV 160cm',         'Meuble TV avec rangements, portes coulissantes en bois',     449.00, 10, 'meuble-tv'],
                ['Meuble TV 200cm',         'Grand meuble TV avec niches ouvertes et portes fermées',      699.00, 5,  'meuble-tv-2'],
                ['Fauteuil lounge',         'Fauteuil design scandinave en tissu beige',                  349.99, 12, 'fauteuil'],
                ['Fauteuil relax',          'Fauteuil inclinable électrique avec chauffage intégré',       799.00, 4,  'fauteuil-relax'],
                ['Lampadaire Arc',          'Grand lampadaire arc design, abat-jour en lin',              199.00, 20, 'lampadaire'],
                ['Bibliothèque murale',     'Ensemble de 3 étagères flottantes en chêne naturel',        149.00, 18, 'etageres'],
                ['Pouf XXL',                'Grand pouf carré en microfibre gris, très moelleux',         129.00, 25, 'pouf'],
                ['Tapis 200x300',           'Tapis design géométrique en laine naturelle, gris et blanc',  349.00, 9,  'tapis'],
                ['Lustre suspension',       'Lustre design moderne en métal noir, 3 ampoules',           189.00, 14, 'lustre'],
                ['Console d\'entrée',       'Console élégante en bois clair avec miroir intégré',        279.00, 11, 'console'],
            ],
            'Chambre' => [
                ['Lit 160x200',             'Lit double avec tête de lit capitonnée en velours gris',     799.00, 8,  'lit'],
                ['Lit 140x190',             'Lit simple premium avec sommier à lattes inclus',            599.00, 10, 'lit-140'],
                ['Lit bateau',              'Lit bateau avec tiroirs de rangement, coloris blanc',        699.00, 6,  'lit-bateau'],
                ['Armoire 3 portes',        'Grande armoire avec miroir central et rangements intérieurs', 649.00, 5,  'armoire'],
                ['Armoire 2 portes',        'Armoire compacte en bois blanc, 2 portes coulissantes',      449.00, 7,  'armoire-2'],
                ['Commode 5 tiroirs',       'Commode en bois blanc laqué, poignées dorées',               279.00, 12, 'commode'],
                ['Commode style scandinave','Commode en chêne clair, style nordique épuré',               329.00, 8,  'commode-scand'],
                ['Chevet flottant',         'Table de chevet murale en bois naturel avec tiroir',          89.00, 20, 'chevet'],
                ['Chevet pied',             'Table de chevet traditionnelle, 1 tiroir et 1 niche ouverte', 119.00, 15, 'chevet-pied'],
                ['Miroir chambre',          'Grand miroir mural rectangulaire cadre chêne doré',          199.00, 14, 'miroir'],
                ['Tête de lit capitonnée',  'Tête de lit autonome en velours bleu canard, H 120cm',      249.00, 11, 'tete-lit'],
                ['Housse de couette',       'Housse de couette satin blanc, 240x220cm + 2 taies',        89.00, 30, 'housse'],
                ['Parure de lit lin',       'Parure complète en lin blanc naturel, ultra douce',          129.00, 22, 'parure'],
                ['Pouf ottoman',            'Pouf de lit carré en cuir blanc, très confortable',          159.00, 13, 'pouf-ottoman'],
            ],
            'Bureau' => [
                ['Bureau d\'angle',         'Bureau ergonomique 160×120cm avec retour, finition chêne', 399.00, 7,  'bureau'],
                ['Bureau droit 120cm',      'Bureau simple pieds métal noir, plateau bois clair',         249.00, 16, 'bureau-droit'],
                ['Bureau gaming',           'Bureau gamer 160cm avec porte-casque et rangements',         479.00, 8,  'bureau-gaming'],
                ['Bureau debout réglable',  'Bureau avec plateau à hauteur variable électronique',        599.00, 5,  'bureau-debout'],
                ['Chaise de bureau',        'Chaise ergonomique réglable, accoudoirs rembourrés',        249.00, 14, 'chaise-bureau'],
                ['Chaise gaming',           'Chaise gaming rouge et noir avec dossier haut support lombaire', 329.00, 10, 'chaise-gaming'],
                ['Bibliothèque 5 étages',   'Bibliothèque haute en bois blanc, 5 étagères ajustables',   319.00, 9,  'bibliotheque'],
                ['Bibliothèque 3 étages',   'Bibliothèque compacte en chêne naturel, 3 étages',          199.00, 13, 'bibliotheque-3'],
                ['Caisson à tiroirs',       'Caisson mobile 3 tiroirs, compatible avec tous les bureaux',  129.00, 20, 'caisson'],
                ['Caisson à roulettes',     'Caisson bas 1 tiroir + 1 niche, couleur blanche laque',      99.00, 18, 'caisson-2'],
                ['Lampe de bureau',         'Lampe LED réglable, bras articulé, coloris gris',           69.00, 25, 'lampe-bureau'],
                ['Rangement mural',         'Ensemble 4 niches murales en bois, pour organiser l\'espace', 179.00, 12, 'rangement-mural'],
                ['Tapis de sol',            'Tapis roulant pour chaise de bureau, protection parquet',     59.00, 30, 'tapis-sol'],
                ['Accessoires rangement',   'Set de 3 boîtes de rangement design, empilables',            49.00, 40, 'boites-rangement'],
            ],
            'Cuisine' => [
                ['Table de repas 4 places', 'Table extensible 4-6 places en verre trempé et acier',       349.00, 10, 'table-cuisine'],
                ['Table de repas 6 places', 'Table en bois massif noyer, 6 places, très robuste',        599.00, 6,  'table-6places'],
                ['Table ronde haute',       'Table ronde 110cm en verre avec pied central acier',        299.00, 8,  'table-ronde'],
                ['Chaises de bar ×2',       'Lot de 2 chaises de bar en métal noir, assise rembourrée',   189.00, 18, 'chaise-bar'],
                ['Chaises de bar ×4',       'Lot de 4 chaises de bar design en similicuir blanc',         299.00, 12, 'chaise-bar-4'],
                ['Chaises cuisine ×4',      'Ensemble 4 chaises design couleur taupe avec pieds métal',   199.00, 20, 'chaise-cuisine'],
                ['Ilot central',            'Ilot de cuisine avec plan de travail granit et rangements',   999.00, 3,  'ilot'],
                ['Ilot compact',            'Petit ilot mobile en bois blanc, 2 tiroirs et 2 étagères',   349.00, 7,  'ilot-compact'],
                ['Buffet de cuisine',       'Buffet 2 portes avec tiroirs, finition bois blanc',         429.00, 8,  'buffet'],
                ['Buffet haut',             'Grand buffet 4 portes + étagères, style rustique chêne',     599.00, 5,  'buffet-haut'],
                ['Desserte cuisine',        'Desserte mobile avec 3 paniers en osier, plateau bois',      129.00, 16, 'desserte'],
                ['Étagère cuisine',         'Étagère murale 2 niveaux acier inox, très pratique',        99.00, 22, 'etagere-cuisine'],
                ['Horloge murale',          'Horloge design moderne en bois clair, 40cm diamètre',       79.00, 28, 'horloge'],
                ['Suspensions LED',         'Lot de 3 suspensions LED dimmables pour cuisine',           159.00, 14, 'suspensions'],
            ],
        ];

        $allMeubles = [];
        foreach ($data as $nomCategorie => $meubles) {
            $categorie = new Categorie();
            $categorie->setNom($nomCategorie);
            $categorie->setDescription('Meubles pour votre ' . strtolower($nomCategorie));
            $manager->persist($categorie);

            foreach ($meubles as [$nom, $desc, $prix, $stock, $seed]) {
                $meuble = new Meuble();
                $meuble->setNom($nom)
                       ->setDescription($desc)
                       ->setPrix((string) $prix)
                       ->setStock($stock)
                       ->setCategorie($categorie)
                       ->setImage('/images/' . ($imageBySeed[$seed] ?? 'meuble-tv.jpg'));
                $manager->persist($meuble);
                $allMeubles[] = $meuble;
            }
        }

        $manager->flush();

        // ── Commandes existantes ──
        $meubleCount = count($allMeubles);
        
        for ($i = 0; $i < 20; $i++) {
            // Selectionner un utilisateur standard aleatoire.
            $randomUser = $regularUsers[rand(0, count($regularUsers) - 1)];
            
            $commande = new Commande();
            $commande->setUser($randomUser);
            $commande->setAdresseLivraison('123 Rue de la Paix, 75001 Paris');
            $commande->setStatut(['en_attente', 'en_cours', 'completee', 'annulee'][rand(0, 3)]);
            
            // Date de commande aléatoire dans les 3 derniers mois
            $dateCommande = new \DateTimeImmutable();
            $dateCommande = $dateCommande->modify('-' . rand(1, 90) . ' days');
            $commande->setCreatedAt($dateCommande);
            
            $montantTotal = 0;
            
            // Ajouter 1-4 articles par commande
            $nbArticles = rand(1, 4);
            for ($j = 0; $j < $nbArticles; $j++) {
                $randomMeuble = $allMeubles[rand(0, $meubleCount - 1)];
                $quantite = rand(1, 3);
                
                $ligneCom = new LigneCommande();
                $ligneCom->setMeuble($randomMeuble);
                $ligneCom->setQuantite($quantite);
                $ligneCom->setPrixUnitaire($randomMeuble->getPrix());
                $ligneCom->setCommande($commande);
                
                $montantTotal += floatval($randomMeuble->getPrix()) * $quantite;
                
                $manager->persist($ligneCom);
            }
            
            $commande->setTotal((string) $montantTotal);
            $manager->persist($commande);
        }

        $manager->flush();
    }
}
