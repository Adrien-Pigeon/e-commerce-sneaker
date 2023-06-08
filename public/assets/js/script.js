// Sélectionnez le bouton "AJOUTER AU PANIER"
const addToCartButton = document.querySelector('.offset');

// Sélectionnez le lien du panier et le compteur du nombre d'articles
const cartLink = document.querySelector('.cart a');
const cartItemCount = document.createElement('span');
cartItemCount.classList.add('item-count');

// Initialisez le nombre d'articles dans le panier à 0
let itemCount = 0;

// Ajoutez un écouteur d'événement au bouton "AJOUTER AU PANIER"
addToCartButton.addEventListener('click', function() {
  // Incrémentez le nombre d'articles
  itemCount++;

  // Mettez à jour le contenu du compteur du nombre d'articles
  cartItemCount.textContent = itemCount;

  // Vérifiez si le compteur du nombre d'articles est déjà ajouté au panier
  if (!cartItemCount.parentNode) {
    // Ajoutez le compteur du nombre d'articles au lien du panier
    cartLink.appendChild(cartItemCount);
  }
});
