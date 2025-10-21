// assets/js/contact-form.js

document.addEventListener("DOMContentLoaded", function () {
  const contactForm = document.getElementById("contactForm");
  const nameInput = document.getElementById("name");
  const emailInput = document.getElementById("email");
  const phoneInput = document.getElementById("phone");
  const subjectSelect = document.getElementById("subject");
  const messageTextarea = document.getElementById("message");
  const charCount = document.getElementById("charCount");
  const submitBtn = contactForm.querySelector(".contact-button");
  const successMessage = document.getElementById("successMessage");

  // === CHARACTER COUNTER ===
  function updateCharCount() {
    const length = messageTextarea.value.length;
    charCount.textContent = length;

    // Changer la couleur si on approche de la limite
    if (length > 450) {
      charCount.style.color = "var(--color-error)";
    } else if (length > 400) {
      charCount.style.color = "oklch(0.60 0.15 85)"; // Orange
    } else {
      charCount.style.color = "var(--color-gray-500)";
    }

    // Limiter à 500 caractères
    if (length > 500) {
      messageTextarea.value = messageTextarea.value.substring(0, 500);
      charCount.textContent = "500";
    }
  }

  messageTextarea.addEventListener("input", updateCharCount);

  // === VALIDATION FUNCTIONS ===
  function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
  }

  function validatePhone(phone) {
    // Si le champ est vide, c'est valide (optionnel)
    if (!phone) return true;
    // Format basique : au moins 10 chiffres
    const cleanPhone = phone.replace(/\D/g, "");
    return cleanPhone.length >= 10;
  }

  function validateName(name) {
    return name.trim().length >= 2;
  }

  function validateMessage(message) {
    return message.trim().length >= 10;
  }

  // === ERROR HANDLING ===
  function showError(input, message) {
    const formGroup = input.closest(".form-group");

    // Supprimer l'ancienne erreur si elle existe
    const oldError = formGroup.querySelector(".error-message");
    if (oldError) {
      oldError.remove();
    }

    // Ajouter la classe d'erreur
    input.classList.add("error");

    // Créer le message d'erreur
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

  // Nom validation
  nameInput.addEventListener("blur", function () {
    if (this.value && !validateName(this.value)) {
      showError(this, "Le nom doit contenir au moins 2 caractères");
    } else {
      clearError(this);
    }
  });

  nameInput.addEventListener("input", function () {
    if (this.classList.contains("error")) {
      clearError(this);
    }
  });

  // Email validation
  emailInput.addEventListener("blur", function () {
    if (this.value && !validateEmail(this.value)) {
      showError(this, "Veuillez entrer une adresse email valide");
    } else {
      clearError(this);
    }
  });

  emailInput.addEventListener("input", function () {
    if (this.classList.contains("error")) {
      clearError(this);
    }
  });

  // Téléphone validation
  phoneInput.addEventListener("blur", function () {
    if (this.value && !validatePhone(this.value)) {
      showError(this, "Veuillez entrer un numéro de téléphone valide");
    } else {
      clearError(this);
    }
  });

  phoneInput.addEventListener("input", function () {
    if (this.classList.contains("error")) {
      clearError(this);
    }
  });

  // Sujet validation
  subjectSelect.addEventListener("change", function () {
    if (this.classList.contains("error")) {
      clearError(this);
    }
  });

  // Message validation
  messageTextarea.addEventListener("blur", function () {
    if (this.value && !validateMessage(this.value)) {
      showError(this, "Le message doit contenir au moins 10 caractères");
    } else {
      clearError(this);
    }
  });

  messageTextarea.addEventListener("input", function () {
    if (this.classList.contains("error")) {
      clearError(this);
    }
  });

  // === FORM SUBMISSION ===
  contactForm.addEventListener("submit", function (e) {
    e.preventDefault();

    // Réinitialiser toutes les erreurs
    [nameInput, emailInput, phoneInput, subjectSelect, messageTextarea].forEach(
      clearError
    );

    // Cacher le message de succès si affiché
    successMessage.style.display = "none";

    let hasError = false;

    // Valider le nom
    if (!nameInput.value) {
      showError(nameInput, "Le nom est requis");
      hasError = true;
    } else if (!validateName(nameInput.value)) {
      showError(nameInput, "Le nom doit contenir au moins 2 caractères");
      hasError = true;
    }

    // Valider l'email
    if (!emailInput.value) {
      showError(emailInput, "L'adresse email est requise");
      hasError = true;
    } else if (!validateEmail(emailInput.value)) {
      showError(emailInput, "Veuillez entrer une adresse email valide");
      hasError = true;
    }

    // Valider le téléphone (si rempli)
    if (phoneInput.value && !validatePhone(phoneInput.value)) {
      showError(phoneInput, "Veuillez entrer un numéro de téléphone valide");
      hasError = true;
    }

    // Valider le sujet
    if (!subjectSelect.value) {
      showError(subjectSelect, "Veuillez sélectionner un sujet");
      hasError = true;
    }

    // Valider le message
    if (!messageTextarea.value) {
      showError(messageTextarea, "Le message est requis");
      hasError = true;
    } else if (!validateMessage(messageTextarea.value)) {
      showError(
        messageTextarea,
        "Le message doit contenir au moins 10 caractères"
      );
      hasError = true;
    }

    // Si pas d'erreur, soumettre le formulaire
    if (!hasError) {
      // Ajouter l'état de chargement
      submitBtn.classList.add("loading");

      // Simuler une requête (remplace par ton appel AJAX réel)
      setTimeout(() => {
        // Retirer l'état de chargement
        submitBtn.classList.remove("loading");

        // Afficher le message de succès
        successMessage.style.display = "flex";

        // Réinitialiser le formulaire
        contactForm.reset();
        charCount.textContent = "0";
        charCount.style.color = "var(--color-gray-500)";

        // Scroller vers le message de succès
        successMessage.scrollIntoView({
          behavior: "smooth",
          block: "center",
        });

        // Masquer le message de succès après 8 secondes
        setTimeout(() => {
          successMessage.style.display = "none";
        }, 8000);

        // Ici tu peux faire ton appel AJAX
        // Pour l'exemple, on ne soumet pas le formulaire au serveur
        // contactForm.submit();
      }, 1500);
    } else {
      // Scroller vers la première erreur
      const firstError = contactForm.querySelector(".error");
      if (firstError) {
        firstError.scrollIntoView({ behavior: "smooth", block: "center" });
        firstError.focus();
      }
    }
  });

  // Animation au focus des inputs
  const inputs = contactForm.querySelectorAll(".form-input, .form-textarea");
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
  messageTextarea.addEventListener("input", function () {
    this.style.height = "auto";
    this.style.height = this.scrollHeight + "px";
  });
});
