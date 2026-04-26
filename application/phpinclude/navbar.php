<?php
/**
 * Barre de navigation Bootstrap 4
 */
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <a class="navbar-brand" href="#">Team Up</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="../index.php">Accueil</a>
            </li>
            <?php
            // Chargement dynamique du menu depuis menu.json
            $menuFile = dirname(__FILE__) . '/menu.json';
            if (file_exists($menuFile)) {
                $menuJson = file_get_contents($menuFile);
                $menuItems = json_decode($menuJson, true);
                if (is_array($menuItems)) {
                    foreach ($menuItems as $item) {
                        echo '<li class="nav-item">';
                        echo '<a class="nav-link" href="' . htmlspecialchars($item['route']) . '">' . htmlspecialchars($item['label']) . '</a>';
                        echo '</li>';
                    }
                }
            }
            ?>
        </ul>
        <form class="form-inline my-2 my-lg-0">
            <input class="form-control mr-sm-2" type="search" placeholder="Rechercher..." aria-label="Search">
            <button class="btn btn-outline-light my-2 my-sm-0" type="submit">Rechercher</button>
        </form>
        <span class="navbar-text ml-3">
            Invité
        </span>
    </div>
</nav>
