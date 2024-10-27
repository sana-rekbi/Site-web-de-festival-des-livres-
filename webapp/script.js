// JavaScript pour agrandir la barre de recherche au survol
const searchBar = document.querySelector('.search-input');

searchBar.addEventListener('focus', () => {
    searchBar.style.width = '250px';
});

searchBar.addEventListener('blur', () => {
    searchBar.style.width = '200px';
});



document.getElementById('inscription-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Empêche l'envoi du formulaire
 
});


