// assets/js/inscription-form.js

document.addEventListener("DOMContentLoaded", function () {
  const signupForm = document.getElementById("signupForm");
  const prenomInput = document.getElementById("prenom");
  const nomInput = document.getElementById("nom");
  const emailInput = document.getElementById("email");
  const telephoneInput = document.getElementById("telephone");
  const passwordInput = document.getElementById("password");
  const passwordConfirmInput = document.getElementById("password_confirm");
  const termsCheckbox = document.getElementById("terms");
  const submitBtn = signupForm.querySelector(".signup-button");
  const togglePasswordBtn = document.getElementById("togglePassword");
  const togglePasswordConfirmBtn = document.getElementById("togglePasswordConfirm");

  // === TOGGLE PASSWORD VISIBILITY ===
  function setupPasswordToggle(toggleBtn, passwordField) {
    if (!toggleBtn || !passwordField) return;

    toggleBtn.addEventListener("click", function () {
      const eyeOpen = this.querySelector(".eye-open");
      const eyeClosed = this.querySelector(".eye-closed");

      if (passwordField.type === "password") {
        // Afficher le mot de passe
        passwordField.type = "text";
        eyeOpen.style.display = "none";
        eyeClosed.style.display = "block";
        this.setAttribute("aria-label", "Masquer le mot de passe");
      } else {
        // Masquer le mot de passe
        passwordField.type = "password";
        eyeOpen.style.display = "block";
        eyeClosed.style.display = "none";
        this.setAttribute("aria-label", "Afficher le mot de passe");
      }
    });
  }

  setupPasswordToggle(togglePasswordBtn, passwordInput);
  setupPasswordToggle(togglePasswordConfirmBtn, passwordConfirmInput);

  // === PASSWORD STRENGTH INDICATOR ===
  const strengthBar = document.getElementById("strengthBarFill");
  const strengthText = document.getElementById("strengthText");

  function checkPasswordStrength(password) {
    if (!password) {
      return { strength: "none", score: 0 };
    }

    let score = 0;

    // Longueur
    if (password.length >= 8) score++;
    if (password.length >= 12) score++;

    // Contient des minuscules et majuscules
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;

    // Contient des chiffres
    if (/\d/.test(password)) score++;

    // Contient des caractères spéciaux
    if (/[^A-Za-z0-9]/.test(password)) score++;

    if (score <= 2) return { strength: "weak", score };
    if (score <= 3) return { strength: "medium", score };
    return { strength: "strong", score };
  }

  function updatePasswordStrength() {
    const password = passwordInput.value;
    const result = checkPasswordStrength(password);

    // Réinitialiser les classes
    strengthBar.className = "strength-bar-fill";
    strengthText.className = "strength-text";

    if (result.strength === "none") {
      strengthText.textContent = "";
      return;
    }

    strengthBar.classList.add(result.strength);
    strengthText.classList.add(result.strength);

    if (result.strength === "weak") {
      strengthText.textContent = "⚠️ Mot de passe faible";
    } else if (result.strength === "medium") {
      strengthText.textContent = "✓ Mot de passe moyen";
    } else {
      strengthText.textContent = "✓ Mot de passe fort";
    }
  }

  passwordInput.addEventListener("input", updatePasswordStrength);

  // === VALIDATION FUNCTIONS ===
  function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
  }

  function validatePassword(password) {
    return password.length >= 8;
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
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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

  // Prénom validation
  prenomInput.addEventListener("blur", function () {
    if (this.value && !validateName(this.value)) {
      showError(this, "Le prénom doit contenir au moins 2 caractères");
    } else {
      clearError(this);
    }
  });

  prenomInput.addEventListener("input", function () {
    if (this.classList.contains("error")) {
      clearError(this);
    }
  });

  // Nom validation
  nomInput.addEventListener("blur", function () {
    if (this.value && !validateName(this.value)) {
      showError(this, "Le nom doit contenir au moins 2 caractères");
    } else {
      clearError(this);
    }
  });

  nomInput.addEventListener("input", function () {
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
  telephoneInput.addEventListener("blur", function () {
    if (this.value && !validatePhone(this.value)) {
      showError(this, "Veuillez entrer un numéro de téléphone valide");
    } else {
      clearError(this);
    }
  });

  telephoneInput.addEventListener("input", function () {
    if (this.classList.contains("error")) {
      clearError(this);
    }
  });

  // Password validation
  passwordInput.addEventListener("blur", function () {
    if (this.value && !validatePassword(this.value)) {
      showError(this, "Le mot de passe doit contenir au moins 8 caractères");
    } else {
      clearError(this);
    }
  });

  passwordInput.addEventListener("input", function () {
    if (this.classList.contains("error")) {
      clearError(this);
    }
  });

  // Password confirmation validation
  passwordConfirmInput.addEventListener("blur", function () {
    if (this.value && this.value !== passwordInput.value) {
      showError(this, "Les mots de passe ne correspondent pas");
    } else {
      clearError(this);
    }
  });

  passwordConfirmInput.addEventListener("input", function () {
    if (this.classList.contains("error")) {
      clearError(this);
    }
  });

  // Vérifier la correspondance quand le premier mot de passe change
  passwordInput.addEventListener("input", function () {
    if (passwordConfirmInput.value && passwordConfirmInput.value !== this.value) {
      showError(passwordConfirmInput, "Les mots de passe ne correspondent pas");
    } else if (passwordConfirmInput.value) {
      clearError(passwordConfirmInput);
    }
  });

  // === FORM SUBMISSION ===
  signupForm.addEventListener("submit", function (e) {
    e.preventDefault();

    // Réinitialiser toutes les erreurs
    [prenomInput, nomInput, emailInput, telephoneInput, passwordInput, passwordConfirmInput].forEach(clearError);

    let hasError = false;

    // Valider le prénom
    if (!prenomInput.value) {
      showError(prenomInput, "Le prénom est requis");
      hasError = true;
    } else if (!validateName(prenomInput.value)) {
      showError(prenomInput, "Le prénom doit contenir au moins 2 caractères");
      hasError = true;
    }

    // Valider le nom
    if (!nomInput.value) {
      showError(nomInput, "Le nom est requis");
      hasError = true;
    } else if (!validateName(nomInput.value)) {
      showError(nomInput, "Le nom doit contenir au moins 2 caractères");
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
    if (telephoneInput.value && !validatePhone(telephoneInput.value)) {
      showError(telephoneInput, "Veuillez entrer un numéro de téléphone valide");
      hasError = true;
    }

    // Valider le mot de passe
    if (!passwordInput.value) {
      showError(passwordInput, "Le mot de passe est requis");
      hasError = true;
    } else if (!validatePassword(passwordInput.value)) {
      showError(passwordInput, "Le mot de passe doit contenir au moins 8 caractères");
      hasError = true;
    }

    // Valider la confirmation du mot de passe
    if (!passwordConfirmInput.value) {
      showError(passwordConfirmInput, "Veuillez confirmer votre mot de passe");
      hasError = true;
    } else if (passwordConfirmInput.value !== passwordInput.value) {
      showError(passwordConfirmInput, "Les mots de passe ne correspondent pas");
      hasError = true;
    }

    // Vérifier l'acceptation des CGU
    if (!termsCheckbox.checked) {
      const formGroup = termsCheckbox.closest(".form-group");
      const errorDiv = document.createElement("div");
      errorDiv.className = "error-message";
      errorDiv.innerHTML = `
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10"/>
          <line x1="12" y1="8" x2="12" y2="12"/>
          <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        Vous devez accepter les conditions générales d'utilisation
      `;

      // Supprimer l'ancienne erreur si elle existe
      const oldError = formGroup.querySelector(".error-message");
      if (oldError) {
        oldError.remove();
      }

      formGroup.appendChild(errorDiv);
      hasError = true;
    }

    // Si pas d'erreur, soumettre le formulaire
    if (!hasError) {
      // Ajouter l'état de chargement
      submitBtn.classList.add("loading");

      // Simuler une requête (remplace par ton appel AJAX réel)
      setTimeout(() => {
        // Ici tu peux faire ton appel AJAX
        // Pour l'exemple, on soumet le formulaire normalement
        signupForm.submit();
      }, 1500);
    } else {
      // Scroller vers la première erreur
      const firstError = signupForm.querySelector(".error");
      if (firstError) {
        firstError.scrollIntoView({ behavior: "smooth", block: "center" });
        firstError.focus();
      }
    }
  });

  // Supprimer l'erreur CGU quand la checkbox est cochée
  termsCheckbox.addEventListener("change", function () {
    const formGroup = this.closest(".form-group");
    const errorMessage = formGroup.querySelector(".error-message");
    if (errorMessage && this.checked) {
      errorMessage.remove();
    }
  });

  // Animation au focus des inputs
  const inputs = signupForm.querySelectorAll(".form-input");
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
});
