Version française du fichier readme.txt (WordPress.org ne lit que readme.txt, en anglais).

=== Fand Vendor Store Blocks for WCFM ===
Contributors: fandevelop
Donate link: https://fan-develop.fr
Tags: wcfm, woocommerce, blocks, marketplace, vendor
Requires at least: 7.1
Tested up to: 7.1
Requires PHP: 8.2
Stable tag: 1.0.2
Requires Plugins: woocommerce, wc-frontend-manager
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Personnalisez entièrement les pages boutique de vos vendeurs WCFM avec des blocs Gutenberg natifs et modulaires.

== Description ==

**Fand Vendor Store Blocks for WCFM** transforme la personnalisation des boutiques vendeurs sur WCFM Marketplace en remplaçant les modèles rigides par des blocs Gutenberg natifs, flexibles et entièrement stylables.

Conçu pour s'intégrer naturellement à l'éditeur de blocs de WordPress (Gutenberg / FSE), ce plugin permet aux administrateurs de la marketplace de créer des mises en page personnalisées pour les boutiques de leurs vendeurs, sans écrire une seule ligne de code.

= Blocs inclus (18 blocs) =

**En-tête et identité de la boutique**
- **Bannière de la boutique (`fand-storefront/store-banner`)** : affiche l'image de couverture de la boutique (ordinateur et mobile) avec repli automatique sur la bannière WCFM par défaut et prise en charge d'un contenu intérieur.
- **Logo du vendeur (`fand-storefront/store-logo`)** : affiche l'avatar / logo du vendeur avec une taille en pixels personnalisable.
- **Nom de la boutique (`fand-storefront/store-name`)** : affiche le nom commercial de la boutique sous forme de titre `<h1>`.
- **Badges du vendeur (`fand-storefront/store-badges`)** : met en avant les badges de vérification et les distinctions attribués au vendeur.
- **Note et avis (`fand-storefront/store-rating`)** : affiche la note moyenne en étoiles du vendeur lorsque les avis sont activés dans WCFM.
- **Boutons d'action (`fand-storefront/store-actions`)** : affiche les boutons d'action natifs de WCFM « Question » et « Suivre ».

**Contact et présentation**
- **Description de la boutique (`fand-storefront/store-description`)** : affiche la présentation détaillée de la boutique rédigée par le vendeur.
- **Adresse de la boutique (`fand-storefront/store-address`)** : affiche l'adresse postale du vendeur précédée d'une icône personnalisable.
- **Téléphone de la boutique (`fand-storefront/store-phone`)** : affiche le numéro de téléphone cliquable (`tel:`) avec une icône personnalisable.
- **Email de la boutique (`fand-storefront/store-email`)** : affiche l'adresse email protégée contre le spam (`antispambot`) avec une icône personnalisable.
- **Réseaux sociaux (`fand-storefront/store-social`)** : affiche les liens et icônes vers les profils sociaux du vendeur.

**Navigation et catalogue**
- **Onglets de la boutique (`fand-storefront/store-tabs`)** : affiche la barre d'onglets WCFM (Produits, À propos, Politiques, Avis, Suiveurs, Articles…).
- **Contenu de l'onglet actif (`fand-storefront/store-content`)** : affiche dynamiquement le contenu correspondant à l'onglet choisi par le visiteur.
- **Produits de la boutique (`fand-storefront/store-products`)** : affiche la grille de produits du vendeur.

**Widgets de la barre latérale**
- **Recherche dans la boutique (`fand-storefront/store-search`)** : affiche un moteur de recherche interne qui ne filtre que les produits du vendeur (avec un titre personnalisable).
- **Catégories de la boutique (`fand-storefront/store-category`)** : affiche les catégories de produits du vendeur sous forme de liste déroulante compacte (avec un titre personnalisable).
- **Horaires de la boutique (`fand-storefront/store-hours`)** : affiche les horaires d'ouverture hebdomadaires du vendeur dans un format clair et compact.
- **Localisation de la boutique (`fand-storefront/store-location`)** : affiche la carte géographique de la boutique via le widget WCFM (avec un titre personnalisable).

= Fonctionnalités principales =

- **Contrôles Gutenberg natifs** : prise en charge complète des réglages natifs de WordPress (couleurs, arrière-plans, espacements, bordures côté par côté et angles arrondis indépendants).
- **Respect de la confidentialité WCFM** : chaque bloc vérifie automatiquement les autorisations et les règles d'affichage définies par l'administrateur et le vendeur dans WCFM.
- **Détection automatique du contexte** : les blocs détectent automatiquement le vendeur affiché sur la page courante et sont extensibles par des add-ons (par exemple pages d'événements ou de branches).
- **Rendu côté serveur (PHP)** : performances optimales et compatibilité complète avec les thèmes Full Site Editing (FSE) et les constructeurs de pages.

WCFM et WooCommerce sont des marques de leurs propriétaires respectifs. Ce plugin est un add-on indépendant, non affilié à ces marques, ni approuvé ou sponsorisé par elles.

== Installation ==

1. Vérifiez que **WooCommerce** et **WCFM - WooCommerce Frontend Manager** sont installés et activés.
2. Envoyez le dossier `fand-vendor-store-blocks-for-wcfm` dans le répertoire `/wp-content/plugins/` de votre site WordPress, ou installez l'archive `.zip` depuis **Extensions > Ajouter**.
3. Activez l'extension depuis le menu **Extensions** de WordPress.
4. Ouvrez le modèle de votre page boutique dans l'éditeur Gutenberg et insérez les blocs de la catégorie **Fand Vendor Store (WCFM)**.
5. Rendez-vous dans **Réglages > Lecture** et sélectionnez votre page modèle dans le champ **Store template page (Fand Vendor Store Blocks)** (et optionnellement pour les pages branches ou événements).

== Questions fréquentes ==

= Ce plugin nécessite-t-il WCFM Marketplace ? =
Oui. **Fand Vendor Store Blocks for WCFM** est conçu spécifiquement pour fonctionner avec WooCommerce et WCFM (`wc-frontend-manager`).

= Les blocs sont-ils compatibles avec les thèmes FSE (Full Site Editing) ? =
Oui, tous les blocs utilisent les APIs natives de Gutenberg (`useBlockProps`, `get_block_wrapper_attributes()`) et héritent des palettes de couleurs et typographies de votre thème.

= Comment styliser les bordures et les arrondis d'un bloc ? =
Sélectionnez le bloc dans l'éditeur, ouvrez l'onglet **Style** dans la barre latérale droite, puis utilisez le panneau natif **Bordures** pour régler chaque côté et chaque angle séparément.

= Le plugin est-il traduit ? =
Le plugin est écrit en anglais et prêt pour la traduction. Le français est disponible ; d'autres traductions peuvent être proposées sur translate.wordpress.org.

== Captures d'écran ==

1. Construction d'une page boutique WCFM dans l'éditeur Gutenberg avec les blocs modulaires.
2. Personnalisation des styles natifs (couleurs, bordures, espacements) dans la barre latérale.

== Journal des modifications ==

= 1.0.2 =
- Ajout url adresse

= 1.0.1 =
- Ajout reglage de page

= 1.0.0 =
- Version initiale publiée avec les 18 blocs modulaires WCFM Storefront et le support natif complet de Gutenberg.