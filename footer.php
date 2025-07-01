</div> 
<footer class="bg-dark text-white text-center py-3 mt-5">
  &copy; 2025 WatchList. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function setupToggle(passwordInputId, toggleButtonId, iconId) {
  const input = document.getElementById(passwordInputId);
  const button = document.getElementById(toggleButtonId);
  const icon = document.getElementById(iconId);

  if (input && button && icon) {
    button.addEventListener("click", () => {
      const isPassword = input.type === "password";
      input.type = isPassword ? "text" : "password";
      icon.classList.toggle("bi-eye");
      icon.classList.toggle("bi-eye-slash");
    });
  }
}

// Setup toggle for each password field
setupToggle("registerPassword", "toggleRegisterPassword", "toggleIconRegister");
setupToggle("confirmPassword", "toggleConfirmPassword", "toggleIconConfirm");
setupToggle("loginPassword", "togglePassword", "toggleIcon");
</script>

</body>
</html>
