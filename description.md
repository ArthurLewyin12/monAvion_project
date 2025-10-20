# Plateforme de Réservation de Billets d'Avions

Plateforme de réservation de billets d'avions pour des agences de voyage, conçue pour être facilement utilisable.

## Compagnies Aériennes

- **Programme de vols**: Avoir le programme de vols de chaque compagnie.
- **Flotte (types d'avions)**:
  - Capacité (sièges)
  - First Class
  - Business Class
  - Eco
- **Logo des compagnies**
- **Destinations**
- **Horaires de vol**
- **Tarif**

## Réservation

- Inscription / Connexion
- Proposition des différentes compagnies pour une date donnée.
- Visualisation des types d'avions.
- Visualisation des places disponibles.

### Détails de la réservation

- Compagnies qui opèrent sur la ligne.
- Places disponibles selon la classe.
- Horaires de vols.
- Possibilité d'imprimer le billet d'avion.

### Schéma du Billet d'Avion

```text
+-------------------------------------------------------------------------------------------------+
| **AGENCE DE VOYAGE**                                                                            |
| Contact: contact@agence.com                                                                     |
|                                                                **Réf. Dossier:** XXXXXXX        |
|                                                                **Date Réservation:** JJ/MM/AAAA |
|                                                                **Client:** NOM Prénom           |
+-------------------------------------------------------------------------------------------------+
| **VOL: AB123**                                                                                  |
|                                                                                                 |
| **DEPART**                               | **ARRIVEE**                                          |
| ---------------------------------------- | ---------------------------------------------------- |
| **Aéroport de Départ**                   | **Aéroport d'Arrivée**                               |
| **Date:** JJ/MM/AAAA                     | **Date:** JJ/MM/AAAA                                 |
| **Heure Décollage:** HH:MM               | **Heure Atterrissage:** HH:MM                        |
|                                          |                                                      |
| *Escale: Aéroport Escale (si applicable)*| **Durée du vol:** XXh YYm                            |
+-------------------------------------------------------------------------------------------------+
```

## Architecture

- Espace Compagnie Aérienne => Plateforme
- Espace Agence de voyage => Plateforme
- Espace Voyageur => Plateforme

## Principe

Le voyageur consulte l'agence de voyage pour réserver un vol. L'agence de voyage réserve ensuite le vol (et tous les autres éléments demandés) auprès de la compagnie aérienne pour le client.

Les agences voyages et compagnies aeriennes ne s'inscrive pas directement (en gros on aura pas un formulaire d'inscription pour eux par soucis de sécurité, ils recevront un identifiant - mot de pass par l'administrateur)
