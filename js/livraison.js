document.addEventListener('DOMContentLoaded', function() {

    var menuSelect = document.getElementById('menu_choisi');
    var nbPersonnes = document.getElementById('nb_personnes');
    var ville = document.getElementById('ville_livraison');

    function calculerPrix() {
        var menuId = menuSelect.value;
        var nb = nbPersonnes.value;
        var villeLivraison = ville.value;

        if (!menuId || !nb) return;

        var url = BASE_URL + 'api/calculer-prix.php?menu_id=' + menuId + '&nb_personnes=' + nb + '&ville=' + encodeURIComponent(villeLivraison);

        fetch(url)
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.erreur) {
                    document.getElementById('prix_menu').value = '';
                    document.getElementById('prix_livraison').value = '';
                    document.getElementById('reduction').value = '';
                    document.getElementById('total_ttc').value = data.erreur;
                } else {
                    document.getElementById('prix_menu').value = data.prix_menu + ' €';
                    document.getElementById('prix_livraison').value = data.prix_livraison + ' €';
                    document.getElementById('reduction').value = data.reduction + ' €';
                    document.getElementById('total_ttc').value = data.total + ' €';
                }
            });
    }

    menuSelect.addEventListener('change', calculerPrix);
    nbPersonnes.addEventListener('input', calculerPrix);
    ville.addEventListener('input', calculerPrix);

});