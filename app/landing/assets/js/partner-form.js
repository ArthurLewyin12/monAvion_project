// assets/js/partner-form.js

document.addEventListener("DOMContentLoaded", function () {
  const partnerForm = document.getElementById("partnerForm");
  if (!partnerForm) return;

  const submitBtn = partnerForm.querySelector(".partner-button");
  const successMessage = document.getElementById("successMessage");

  // Récupérer tous les inputs
  const inputs = partnerForm.querySelectorAll(".form-input");

  // === VALIDATION FUNCTIONS ===
  function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
  }

  function validatePhone(phone) {
    if (!phone) return false;
    const cleanPhone = phone.replace(/\D/g, "");
    return cleanPhone.length >= 10;
  }

  function validateText(text, minLength = 2) {
    return text.trim().length >= minLength;
  }

  function validateIATA(code) {
    // Code IATA: exactement 2 lettres majuscules
    return /^[A-Z]{2}$/.test(code);
  }

  // === ERROR HANDLING ===
  function showError(input, message) {
    const formGroup = input.closest(".form-group");

    // Supprimer l'ancienne erreur
    const oldError = formGroup.querySelector(".error-message");
    if (oldError) {
      oldError.remove();
    }

    // Ajouter classe d'erreur
    input.classList.add("error");

    // Créer message d'erreur
    const errorDiv = document.createElement("div");
    errorDiv.className = "error-message";
    errorDiv.innerHTML = `
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8" x2="12" y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
      ${message}
    `;
    formGroup.appendChild(errorDiv);
  }

  function clearError(input) {
    const formGroup = input.closest(".form-group");
    const errorMessage = formGroup.querySelector(".error-message");

    if (errorMessage) {
      errorMessage.remove();
    }

    input.classList.remove("error");
  }

  // === REAL-TIME VALIDATION ===
  inputs.forEach((input) => {
    // Validation au blur
    input.addEventListener("blur", function () {
      const type = this.type;
      const id = this.id;
      const value = this.value.trim();

      if (!value && this.hasAttribute("required")) {
        return; // Ne pas valider si vide et requis (sera géré au submit)
      }

      if (value) {
        // Email
        if (type === "email" && !validateEmail(value)) {
          showError(this, "Veuillez entrer une adresse email valide");
        }
        // Téléphone
        else if (type === "tel" && !validatePhone(value)) {
          showError(this, "Veuillez entrer un numéro de téléphone valide");
        }
        // Code IATA
        else if (id === "iata_code" && !validateIATA(value.toUpperCase())) {
          showError(this, "Le code IATA doit contenir exactement 2 lettres");
        }
        // Texte
        else if (type === "text" && !validateText(value)) {
          showError(this, "Ce champ doit contenir au moins 2 caractères");
        }
        // Textarea
        else if (this.tagName === "TEXTAREA" && !validateText(value, 10)) {
          showError(this, "Ce champ doit contenir au moins 10 caractères");
        } else {
          clearError(this);
        }
      }
    });

    // Supprimer l'erreur à la saisie
    input.addEventListener("input", function () {
      if (this.classList.contains("error")) {
        clearError(this);
      }

      // Forcer majuscules pour code IATA
      if (this.id === "iata_code") {
        this.value = this.value.toUpperCase();
      }
    });
  });

  // === FORM SUBMISSION ===
  partnerForm.addEventListener("submit", function (e) {
    e.preventDefault();

    // Cacher message de succès
    successMessage.style.display = "none";

    // Réinitialiser les erreurs
    inputs.forEach(clearError);

    let hasError = false;

    // Valider tous les champs requis
    inputs.forEach((input) => {
      const value = input.value.trim();
      const type = input.type;
      const id = input.id;

      // Champs requis vides
      if (input.hasAttribute("required") && !value) {
        showError(input, "Ce champ est requis");
        hasError = true;
        return;
      }

      // Validation selon le type
      if (value) {
        if (type === "email" && !validateEmail(value)) {
          showError(input, "Veuillez entrer une adresse email valide");
          hasError = true;
        } else if (type === "tel" && !validatePhone(value)) {
          showError(input, "Veuillez entrer un numéro de téléphone valide");
          hasError = true;
        } else if (id === "iata_code" && !validateIATA(value)) {
          showError(
            input,
            "Le code IATA doit contenir exactement 2 lettres majuscules"
          );
          hasError = true;
        } else if (type === "text" && !validateText(value)) {
          showError(input, "Ce champ doit contenir au moins 2 caractères");
          hasError = true;
        } else if (input.tagName === "TEXTAREA" && !validateText(value, 10)) {
          showError(input, "Ce champ doit contenir au moins 10 caractères");
          hasError = true;
        }
      }
    });

    // Si pas d'erreur, soumettre
    if (!hasError) {
      // État de chargement
      submitBtn.classList.add("loading");

      // Simuler requête serveur
      setTimeout(() => {
        // Retirer loading
        submitBtn.classList.remove("loading");

        // Afficher message de succès
        successMessage.style.display = "flex";

        // Réinitialiser formulaire
        partnerForm.reset();

        // Scroller vers le message
        successMessage.scrollIntoView({
          behavior: "smooth",
          block: "center",
        });

        // Masquer après 10 secondes
        setTimeout(() => {
          successMessage.style.display = "none";
        }, 10000);

        // Ici : appel AJAX réel
        // partnerForm.submit();
      }, 1500);
    } else {
      // Scroller vers première erreur
      const firstError = partnerForm.querySelector(".error");
      if (firstError) {
        firstError.scrollIntoView({ behavior: "smooth", block: "center" });
        firstError.focus();
      }
    }
  });

  // Animation au focus
  inputs.forEach((input) => {
    input.addEventListener("focus", function () {
      const wrapper = this.closest(".input-wrapper");
      if (wrapper) {
        wrapper.style.transform = "scale(1.005)";
        wrapper.style.transition = "transform 0.2s ease";
      }
    });

    input.addEventListener("blur", function () {
      const wrapper = this.closest(".input-wrapper");
      if (wrapper) {
        wrapper.style.transform = "scale(1)";
      }
    });
  });

  // Auto-resize textarea
  const textareas = partnerForm.querySelectorAll(".form-textarea");
  textareas.forEach((textarea) => {
    textarea.addEventListener("input", function () {
      this.style.height = "auto";
      this.style.height = this.scrollHeight + "px";
    });
  });
});
