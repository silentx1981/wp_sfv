document.addEventListener('DOMContentLoaded', function () {
    var carouselItems = document.querySelectorAll('.carousel-item');
    var maxHeight = 0;

    carouselItems.forEach(function (item) {
        // Temporär das Element sichtbar machen
        item.style.display = 'block';
        var itemHeight = item.offsetHeight;

        // Überprüfe, ob die Höhe größer ist
        if (itemHeight > maxHeight) {
            maxHeight = itemHeight;
        }

        // Nach dem Messen wieder verstecken, wenn es nicht das aktive Element ist
        item.style.display = '';
    });

    // Setze die maximale Höhe auf alle Elemente
    carouselItems.forEach(function (item) {
        item.style.minHeight = maxHeight + 'px';
    });
});