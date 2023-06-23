# BileMo-P7

![PHP Version](https://img.shields.io/badge/PHP-%3E%3D8-8892BF)

BileMo-P7 est un projet qui [description brève de ce que fait le projet].

## Table des matières

- [Prérequis](#prérequis)
- [Installation](#installation)
- [Documentation de l'API](#documentation-de-lapi)

## Prérequis

Quels sont les prérequis nécessaires pour exécuter votre projet? Par exemple:

- PHP 8 ou supérieur
- Composer
- Symfony CLI
- MySQL

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
12. Télécharger les fixtures
    `php bin/console doctrine:fixture:load`

## Documentation de l'API

[Documentation API Bilmo](http://127.0.0.1:8000/api/doc)
