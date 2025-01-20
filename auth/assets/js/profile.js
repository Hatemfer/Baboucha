// Fonctions pour gérer les modales
function openEditModal() {
  document.getElementById("editModal").style.display = "block";
}

function closeEditModal() {
  document.getElementById("editModal").style.display = "none";
}

function openAddItemModal() {
  document.getElementById("addItemModal").style.display = "block";
}

function closeAddItemModal() {
  document.getElementById("addItemModal").style.display = "none";
}

// Gestion des onglets
function openTab(tabName) {
  var tabContents = document.getElementsByClassName("tab-content");
  for (var i = 0; i < tabContents.length; i++) {
    tabContents[i].style.display = "none";
  }
  document.getElementById(tabName).style.display = "block";
}

// Prévisualisation de l'image
function previewImage(event) {
  const file = event.target.files[0];
  const avatarPreview = document.getElementById("avatarPreview");

  if (file && file.type.startsWith("image/")) {
    const reader = new FileReader();
    reader.onload = function () {
      avatarPreview.src = reader.result; // Mettre à jour l'aperçu de l'image
    };
    reader.readAsDataURL(file);
  } else {
    alert("Veuillez sélectionner une image valide.");
    event.target.value = ""; // Réinitialiser l'input
  }
}

// Fonctions pour gérer la modale de modification d'article
function openEditArticleModal(articleId) {
  // Charger les catégories
  fetch("get_categories.php")
    .then((response) => response.json())
    .then((categories) => {
      const categorieSelect = document.getElementById("editCategorie");
      categorieSelect.innerHTML =
        '<option value="">Sélectionnez une catégorie</option>';
      categories.forEach((category) => {
        const option = document.createElement("option");
        option.value = category.id;
        option.textContent = category.name;
        categorieSelect.appendChild(option);
      });

      // Charger les données de l'article
      fetch(`get_article.php?id=${articleId}`)
        .then((response) => response.json())
        .then((article) => {
          document.getElementById("editArticleId").value = article.id;
          document.getElementById("editTitle").value = article.title;
          document.getElementById("editDescription").value =
            article.description;
          document.getElementById("editPrix").value = article.prix;
          document.getElementById("editCategorie").value = article.category_id;
          document.getElementById("editPreviewImage").src = article.image_path;
          document.getElementById("editPreviewImage").style.display = "block";

          // Définir la valeur de l'état
          const etatSelect = document.getElementById("editEtat");
          etatSelect.value = article.etat; // Toujours définir la valeur de l'état

          // Charger les sous-catégories
          loadEditSubcategories(article.category_id, article.subcategory_id);
        });
    });

  // Afficher la modale
  document.getElementById("editArticleModal").style.display = "block";
}

function loadEditSubcategories(categoryId, subcategoryId = null) {
  fetch(`get_subcategories.php?category_id=${categoryId}`)
    .then((response) => response.json())
    .then((subcategories) => {
      const subcategorieSelect = document.getElementById("editSubcategorie");
      subcategorieSelect.innerHTML =
        '<option value="">Sélectionnez une sous-catégorie</option>';
      subcategories.forEach((subcategory) => {
        const option = document.createElement("option");
        option.value = subcategory.id;
        option.textContent = subcategory.name;
        if (subcategoryId && subcategory.id === subcategoryId) {
          option.selected = true;
        }
        subcategorieSelect.appendChild(option);
      });
    });
}

function closeEditArticleModal() {
  document.getElementById("editArticleModal").style.display = "none";
}

// Gestion de l'upload d'image dans la modale
function triggerEditImageUpload() {
  document.getElementById("editImage").click();
}

const editImageInput = document.getElementById("editImage");
const editPreviewImage = document.getElementById("editPreviewImage");
editImageInput.addEventListener("change", (event) => {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      editPreviewImage.src = e.target.result;
      editPreviewImage.style.display = "block";
    };
    reader.readAsDataURL(file);
  }
});

// Ajouter des écouteurs d'événements pour les boutons de modification
document.addEventListener("DOMContentLoaded", function () {
  const editButtons = document.querySelectorAll(".btn-action.edit");

  editButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const articleId = this.getAttribute("data-article-id");
      openEditArticleModal(articleId);
    });
  });
});

// Fonctions pour gérer la suppression d'un article
function openDeleteArticleModal(articleId) {
  document.getElementById("deleteArticleId").value = articleId;
  document.getElementById("deleteArticleModal").style.display = "block";
}

function closeDeleteArticleModal() {
  document.getElementById("deleteArticleModal").style.display = "none";
}

// Ajouter des écouteurs d'événements pour les boutons de suppression
document.addEventListener("DOMContentLoaded", function () {
  const deleteButtons = document.querySelectorAll(".btn-action.delete");

  deleteButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const articleId = this.getAttribute("data-article-id");
      openDeleteArticleModal(articleId);
    });
  });
});
