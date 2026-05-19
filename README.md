# SymHome — E-commerce Meubles & Décoration

> Mini-projet Symfony — Binôme évaluation

## Stack technique

- Symfony 7.0
- MySQL 8.0 (Doctrine ORM)
- Bootstrap 5.3
- Stripe (paiement)
- EasyAdmin 4 (back-office)
- VichUploaderBundle (images)

---

## 🚀 Installation complète

### 1. Cloner / décompresser le projet

```bash
cd /chemin/vers/le/projet
```

### 2. Installer les dépendances

```bash
composer install
```

### 3. Configurer le `.env`

Copier le `.env` fourni et modifier :

```dotenv
APP_SECRET=   # php -r "echo bin2hex(random_bytes(16));"
DATABASE_URL="mysql://root:@127.0.0.1:3306/symhome?serverVersion=8.0&charset=utf8mb4"
STRIPE_SECRET_KEY=sk_test_VOTRE_CLE
STRIPE_PUBLIC_KEY=pk_test_VOTRE_CLE
```

### 4. Créer la base de données

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
# OU directement :
php bin/console doctrine:schema:create
```

### 5. Charger les données de test

```bash
php bin/console doctrine:fixtures:load
```

### 6. Créer le dossier uploads

```bash
mkdir -p public/uploads/meubles
```

### 7. Lancer le serveur

```bash
symfony server:start
# OU
php -S localhost:8000 -t public/
```

---

## 👤 Comptes de test

| Role        | Email                    | Mot de passe |
| ----------- | ------------------------ | ------------ |
| Utilisateur | ahmed@example.com        | password123  |
| Utilisateur | fatma@example.com        | password123  |
| Utilisateur | aziz@example.com         | password123  |
| Admin       | rayenessid15@gmail.com   | essid        |
| Admin       | adem.essid@example.com   | essid        |
| Admin       | ameni.wesleti@example.com | wesleti      |

---

## 📁 Structure du projet

```
src/
├── Controller/
│   ├── Admin/
│   │   ├── DashboardController.php   ← EasyAdmin dashboard + stats
│   │   ├── MeubleCrudController.php
│   │   ├── CommandeCrudController.php
│   │   ├── CategorieCrudController.php
│   │   └── UserCrudController.php
│   ├── HomeController.php            ← Accueil
│   ├── MeubleController.php          ← Catalogue + recherche/filtre
│   ├── PanierController.php          ← Panier (session)
│   ├── CommandeController.php        ← Checkout + Stripe + historique
│   ├── SecurityController.php        ← Login/logout
│   └── RegistrationController.php    ← Inscription
├── Entity/
│   ├── User.php
│   ├── Categorie.php
│   ├── Meuble.php
│   ├── Commande.php
│   └── LigneCommande.php
├── Service/
│   └── PanierService.php             ← Gestion panier en session
├── Twig/
│   └── PanierExtension.php           ← Variable globale panier_count
└── DataFixtures/
    └── AppFixtures.php               ← 4 catégories × 4 meubles + 2 users
```

---

## 🗺️ Routes principales

| Route                   | URL                    | Description                  |
| ----------------------- | ---------------------- | ---------------------------- |
| app_home                | /                      | Page d'accueil               |
| app_meuble_index        | /catalogue/            | Catalogue + filtres          |
| app_meuble_show         | /catalogue/{id}        | Fiche produit                |
| app_panier_index        | /panier/               | Panier                       |
| app_panier_ajouter      | /panier/ajouter/{id}   | Ajouter au panier            |
| app_commande_checkout   | /commande/checkout     | Récapitulatif avant paiement |
| app_commande_paiement   | /commande/paiement     | Redirection Stripe           |
| app_commande_success    | /commande/success/{id} | Confirmation                 |
| app_commande_historique | /commande/historique   | Mes commandes                |
| app_login               | /connexion             | Connexion                    |
| app_register            | /inscription           | Inscription                  |
| admin                   | /admin                 | Espace admin                 |

---

## 💳 Configuration Stripe

1. Créer un compte sur https://stripe.com
2. Récupérer les clés de test dans le Dashboard Stripe
3. Les renseigner dans `.env` :

```dotenv
STRIPE_SECRET_KEY=sk_test_...
STRIPE_PUBLIC_KEY=pk_test_...
```

4. Pour tester le paiement : carte `4242 4242 4242 4242`, exp. future, CVC quelconque

---

## 🔧 Commandes utiles

```bash
# Migrations
php bin/console make:migration
php bin/console doctrine:migrations:migrate

# Vider le cache
php bin/console cache:clear

# Créer un admin manuellement
php bin/console app:create-admin

# Debug routes
php bin/console debug:router

# Debug container
php bin/console debug:container
```

---

## 📌 Fonctionnalités implémentées

- [x] Catalogue avec recherche et filtres (catégorie, prix)
- [x] Fiche produit détaillée
- [x] Panier en session (ajouter, modifier, supprimer, vider)
- [x] Authentification (inscription, connexion, déconnexion)
- [x] Paiement Stripe (Checkout Sessions)
- [x] Historique des commandes
- [x] Gestion du stock (décrémentation après paiement)
- [x] Espace admin EasyAdmin (CRUD meubles, catégories, commandes, users)
- [x] Dashboard admin avec Chart.js (CA mensuel)
- [x] Fixtures (données de test)

## 📌 Bonus possibles

- [ ] OAuth2 Google/Facebook (KnpUOAuth2ClientBundle)
- [ ] Système d'avis/notes
- [ ] Docker (docker-compose.yml)
- [ ] Emails transactionnels (confirmation commande)
- [ ] Pagination du catalogue
