import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

document.addEventListener('DOMContentLoaded', function() {
    var map = L.map('map').setView([53.47470, -2.219028], 15); // Set map center to your coordinates and zoom level

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var marker = L.marker([53.47470, -2.219028]).addTo(map)
        .bindPopup('<h5>Our Location</h5><a href="https://www.google.com/maps/dir/?api=1&destination=53.47470,-2.219028" target="_blank" class="btn btn-info">Get Directions</a>');
});

document.addEventListener('DOMContentLoaded', function() {
    function initMap() {
        var location = {lat: 53.47470, lng: -2.219028};
        var map = new google.maps.Map(document.getElementById('map'), {
            center: location,
            zoom: 15
        });

        var marker = new google.maps.Marker({
            position: location,
            map: map,
            title: 'Our Location'
        });

        var infowindow = new google.maps.InfoWindow({
            content: '<h5>Our Location</h5><a href="https://www.google.com/maps/dir/?api=1&destination=53.47470,-2.219028" target="_blank" class="btn btn-info">Get Directions</a>'
        });

        marker.addListener('click', function() {
            infowindow.open(map, marker);
        });
    }

    // Wait for the Google Maps script to load
    window.addEventListener('load', initMap);
});


