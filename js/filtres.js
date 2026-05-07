document.addEventListener('DOMContentLoaded', function() {

    document.getElementById('btn-toggle-filtres').addEventListener('click', function() {
        var panel = document.getElementById('filtres-panel');
        panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
    });

    document.getElementById('btn-filtrer').addEventListener('click', function() {
        var theme = document.getElementById('filtre-theme').value;
        var regime = document.getElementById('filtre-regime').value;
        var prixMax = document.getElementById('filtre-prix-max').value;
        var prixMin = document.getElementById('filtre-prix-min').value;
        var personnes = document.getElementById('filtre-personnes').value;

        var url = BASE_URL + 'api/filtrer-menus.php?';
        if (theme) url += 'theme=' + theme + '&';
        if (regime) url += 'regime=' + regime + '&';
        if (prixMax) url += 'prix_max=' + prixMax + '&';
        if (prixMin) url += 'prix_min=' + prixMin + '&';
        if (personnes) url += 'personnes=' + personnes + '&';

        fetch(url)
            .then(function(response) { return response.json(); })
            .then(function(menus) {
                var grille = document.getElementById('menus-grid');
                var html = '';

                if (menus.length === 0) {
                    html = '<p class="text-center">Aucun menu trouvé.</p>';
                } else {
                    for (var i = 0; i < menus.length; i++) {
                        var m = menus[i];
                        html += '<div class="menu-wrapper">';
                        html += '<h2 class="text-center theme-titre">' + m.theme + '</h2>';
                        html += '<hr class="theme-line">';
                        html += '<div class="menu-card menu-liste"><div class="menu-top">';
                        html += '<div class="menu-left"><div class="menu-image-wrapper">';
                        html += '<img src="' + m.image_principale + '" alt="' + m.titre + '" class="menu-image">';
                        html += '<a href="' + BASE_URL + 'detail-menus.php?id=' + m.menu_id + '" class="menu-overlay">';
                        html += '<span class="overlay-button">Voir le détail</span></a>';
                        html += '</div></div>';
                        html += '<div class="menu-infos">';
                        html += '<h3 class="menu-titre">' + m.titre + '</h3>';
                        html += '<hr class="plat-line mb-5">';
                        html += '<p class="plat-nom">' + m.description + '</p>';
                        html += '<p class="prix">' + m.prix_personne + ' € par personne,<br>' + m.nombre_personnes_min + ' personnes minimum.</p>';
                        html += '<a href="' + BASE_URL + 'detail-menus.php?id=' + m.menu_id + '" class="btn btn-dark">Voir le détail</a>';
                        html += '</div></div></div></div>';
                    }
                }

                grille.innerHTML = html;
            });
    });

    document.getElementById('btn-reset').addEventListener('click', function() {
        document.getElementById('filtre-theme').value = '';
        document.getElementById('filtre-regime').value = '';
        document.getElementById('filtre-prix-max').value = '';
        document.getElementById('filtre-prix-min').value = '';
        document.getElementById('filtre-personnes').value = '';
        document.getElementById('btn-filtrer').click();
    });

});