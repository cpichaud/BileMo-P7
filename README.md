# BileMo-P7

![PHP Version](https://img.shields.io/badge/PHP-%3E%3D8-8892BF)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/d73fed0a5c0148d29183806223d9e0af)](https://app.codacy.com/gh/cpichaud/BileMo-P7/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

## Table des matières

- [Description des besoins](#Description)
- [Prérequis](#prérequis)
- [Installation](#installation)

------
## Description des besoins

BileMo, entreprise spécialisée dans les téléphones mobiles haut de gamme, a pour projet de développer une vitrine B2B via une API. Cette interface permettra aux plateformes partenaires d'accéder à son catalogue. L'API doit permettre la consultation et la gestion de produits et d'utilisateurs associés aux clients BileMo.

- consulter la liste des produits BileMo ;
- consulter les détails d’un produit BileMo ;
- consulter la liste des utilisateurs inscrits liés à un client sur le site web ;
- consulter le détail d’un utilisateur inscrit lié à un client ;
- ajouter un nouvel utilisateur lié à un client ;
- supprimer un utilisateur ajouté par un client.
------
## Prérequis

Quels sont les prérequis nécessaires pour exécuter votre projet? Par exemple:

- PHP 8 ou supérieur
- Composer
- Symfony CLI
- MySQL
------
## Installation

Expliquez étape par étape comment installer votre projet. Par exemple:

1. Clonez le dépôt GitHub dans le répertoire souhaité:
   `git clone https://github.com/cpichaud/BileMo-P7.git`
2. Accédez au répertoire du projet:
   `cd BileMo-P7`
3. Installez les dépendances:
   `composer install`
4. Configurez les variables d'environnement dans
   `.env.local`
5. Créez la base de données:
    `php bin/console doctrine:database:create`
6. Exécutez les migrations:
   `php bin/console doctrine:migrations:migrate`
7. Télécharger les fixtures
    `php bin/console doctrine:fixture:load`
