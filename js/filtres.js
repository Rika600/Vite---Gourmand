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
                grille.textContent = '';

                if (menus.length === 0) {
                    var p = document.createElement('p');
                    p.className = 'text-center';
                    p.textContent = 'Aucun menu trouvé.';
                    grille.appendChild(p);
                } else {
                    for (var i = 0; i < menus.length; i++) {
                        var m = menus[i];

                        var wrapper = document.createElement('div');
                        wrapper.className = 'menu-wrapper';

                        var h2 = document.createElement('h2');
                        h2.className = 'text-center theme-titre';
                        h2.textContent = m.theme;
                        wrapper.appendChild(h2);

                        var hr1 = document.createElement('hr');
                        hr1.className = 'theme-line';
                        wrapper.appendChild(hr1);

                        var card = document.createElement('div');
                        card.className = 'menu-card menu-liste';

                        var top = document.createElement('div');
                        top.className = 'menu-top';

                        var left = document.createElement('div');
                        left.className = 'menu-left';

                        var imgWrapper = document.createElement('div');
                        imgWrapper.className = 'menu-image-wrapper';

                        var img = document.createElement('img');
                        img.src = BASE_URL + m.image_principale;
                        img.alt = m.titre;
                        img.className = 'menu-image';
                        imgWrapper.appendChild(img);

                        var overlay = document.createElement('a');
                        overlay.href = BASE_URL + 'pages/detail-menus.php?id=' + m.menu_id;
                        overlay.className = 'menu-overlay';
                        var overlaySpan = document.createElement('span');
                        overlaySpan.className = 'overlay-button';
                        overlaySpan.textContent = 'Voir le détail';
                        overlay.appendChild(overlaySpan);
                        imgWrapper.appendChild(overlay);

                        left.appendChild(imgWrapper);
                        top.appendChild(left);

                        var infos = document.createElement('div');
                        infos.className = 'menu-infos';

                        var h3 = document.createElement('h3');
                        h3.className = 'menu-titre';
                        h3.textContent = m.titre;
                        infos.appendChild(h3);

                        var hr2 = document.createElement('hr');
                        hr2.className = 'plat-line mb-5';
                        infos.appendChild(hr2);

                        var desc = document.createElement('p');
                        desc.className = 'plat-nom';
                        desc.textContent = m.description;
                        infos.appendChild(desc);

                        var prix = document.createElement('p');
                        prix.className = 'prix';
                        prix.textContent = m.prix_personne + ' € par personne, ' + m.nombre_personnes_min + ' personnes minimum.';
                        infos.appendChild(prix);

                        var btn = document.createElement('a');
                        btn.href = BASE_URL + 'pages/detail-menus.php?id=' + m.menu_id;
                        btn.className = 'btn btn-dark';
                        btn.textContent = 'Voir le détail';
                        infos.appendChild(btn);

                        top.appendChild(infos);
                        card.appendChild(top);
                        wrapper.appendChild(card);
                        grille.appendChild(wrapper);
                    }
                }
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