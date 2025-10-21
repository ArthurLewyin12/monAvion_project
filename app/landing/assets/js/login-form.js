// assets/js/login-form.js

document.addEventListener("DOMContentLoaded", function () {
  const loginForm = document.getElementById("loginForm");
  const emailInput = document.getElementById("email");
  const passwordInput = document.getElementById("password");
  const submitBtn = loginForm.querySelector(".submit-btn");
  const togglePasswordBtn = document.getElementById("togglePassword");

  // Toggle password visibility
  if (togglePasswordBtn) {
    togglePasswordBtn.addEventListener("click", function () {
      const eyeOpen = this.querySelector(".eye-open");
      const eyeClosed = this.querySelector(".eye-closed");

      if (passwordInput.type === "password") {
        // Afficher le mot de passe
        passwordInput.type = "text";
        eyeOpen.style.display = "none";
        eyeClosed.style.display = "block";
        this.setAttribute("aria-label", "Masquer le mot de passe");
      } else {
        // Masquer le mot de passe
        passwordInput.type = "password";
        eyeOpen.style.display = "block";
        eyeClosed.style.display = "none";
        this.setAttribute("aria-label", "Afficher le mot de passe");
      }
    });
  }

  // Validation en temps réel
  function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
  }

  function validatePassword(password) {
    return password.length >= 6;
  }

  // Afficher les erreurs
  function showError(input, message) {
    const inputField = input.closest(".input-field");

    // Supprimer l'ancienne erreur si elle existe
    const oldError = inputField.querySelector(".error-message");
    if (oldError) {
      oldError.remove();
    }

    // Ajouter la classe d'erreur
    input.classList.add("error");

    // Créer le message d'erreur
    const errorDiv = document.createElement("div");
    errorDiv.className = "error-message";
    errorDiv.innerHTML = `<span>⚠️</span> ${message}`;
    inputField.appendChild(errorDiv);
  }

  // Supprimer les erreurs
  function clearError(input) {
    const inputField = input.closest(".input-field");
    const errorMessage = inputField.querySelector(".error-message");

    if (errorMessage) {
      errorMessage.remove();
    }

    input.classList.remove("error");
  }

  // Validation de l'email
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

  // Validation du mot de passe
  passwordInput.addEventListener("blur", function () {
    if (this.value && !validatePassword(this.value)) {
      showError(this, "Le mot de passe doit contenir au moins 6 caractères");
    } else {
      clearError(this);
    }
  });

  passwordInput.addEventListener("input", function () {
    if (this.classList.contains("error")) {
      clearError(this);
    }
  });

  // Soumission du formulaire
  loginForm.addEventListener("submit", function (e) {
    e.preventDefault();

    // Réinitialiser les erreurs
    clearError(emailInput);
    clearError(passwordInput);

    let hasError = false;

    // Valider l'email
    if (!emailInput.value) {
      showError(emailInput, "L'adresse email est requise");
      hasError = true;
    } else if (!validateEmail(emailInput.value)) {
      showError(emailInput, "Veuillez entrer une adresse email valide");
      hasError = true;
    }

    // Valider le mot de passe
    if (!passwordInput.value) {
      showError(passwordInput, "Le mot de passe est requis");
      hasError = true;
    } else if (!validatePassword(passwordInput.value)) {
      showError(
        passwordInput,
        "Le mot de passe doit contenir au moins 6 caractères",
      );
      hasError = true;
    }

    // Si pas d'erreur, soumettre le formulaire
    if (!hasError) {
      // Ajouter l'état de chargement
      submitBtn.classList.add("loading");
      submitBtn.textContent = "";

      // Simuler une requête (remplace par ton appel AJAX réel)
      setTimeout(() => {
        // Ici tu peux faire ton appel AJAX
        // Pour l'exemple, on soumet le formulaire normalement
        loginForm.submit();
      }, 1000);
    }
  });

  // Animation au focus des inputs
  const inputs = loginForm.querySelectorAll(".input");
  inputs.forEach((input) => {
    input.addEventListener("focus", function () {
      this.parentElement.style.transform = "scale(1.01)";
      this.parentElement.style.transition = "transform 0.2s ease";
    });

    input.addEventListener("blur", function () {
      this.parentElement.style.transform = "scale(1)";
    });
  });
});
