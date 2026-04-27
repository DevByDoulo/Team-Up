<?php
/**
 * Page d'accueil de l'application Team Up
 * Architecture MVC - Logique séparée de la présentation
 */

require_once 'controllers/HomeController.php';

// Initialisation du contrôleur
$homeController = new HomeController();

// Traitement de la logique métier
$theme = $homeController->handleUserProfile();
$navbar_class = $homeController->getNavbarClass($theme);
$menu_items = $homeController->getMenuItems();
$stats = $homeController->getDashboardStats();
$theme_label = $homeController->getThemeLabel($theme);

// Inclusion de la vue
require_once 'views/home.php';
?>
