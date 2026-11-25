# MRZ Pop Up Exp

Plugin WordPress de **pop-up très simple** : un texte HTML, une image à gauche facultative et des règles d'affichage. L'apparence s'adapte au site via le CSS du thème.

- Repo : https://github.com/m0r3z/mrz-pop-up-exp
- Auteur : [Morez.co](https://morez.co)
- Licence : GPLv3 or later

## Fonctionnalités

- Texte HTML (shortcodes acceptés) et image de la médiathèque.
- Largeur, couleur et opacité de l'overlay.
- Dates de début et de fin, choix des pages, masquage sur mobile.
- 8 déclencheurs : immédiat, secondes, pixels, mi-page, bas de page, minutes sur le site, pages vues en 90 jours, intention de sortie.
- 6 fréquences : plus jamais, pas pendant la session, 7, 30 ou 90 jours, toujours.
- Ouverture par un lien : `<a href="#mrzpop-ID">…</a>`.
- Aperçu depuis l'admin.

## Prérequis

WordPress 6.3+, PHP 7.4+. Aucune dépendance.

## Variables CSS

| Variable | Défaut |
|---|---|
| `--mrzpop-background` | `#fff` |
| `--mrzpop-padding` | `3rem 2rem 2rem` |
| `--mrzpop-image-width` | `40%` |
| `--mrzpop-z-index` | `99990` |

## Confidentialité

Aucun cookie, aucun appel externe, aucune télémétrie. Les pop-ups vues ou cliquées, et les compteurs de pages et de temps s'ils sont utilisés, restent dans le navigateur du visiteur.
