document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    const errorToast = document.getElementById("error");
  
    function showError(message) {
      if (!errorToast) return;
      errorToast.querySelector(".borderv2").innerHTML = `
        <div class="error-icon"></div>
        ${message}
      `;
      errorToast.classList.add("active");
      setTimeout(() => {
        errorToast.classList.remove("active");
      }, 5000);
    }
  
    form.addEventListener("submit", async function (event) {
      event.preventDefault();
  
      const email = document.getElementById("email").value;
      const password = document.getElementById("password").value;
  
      const requestData = {
        email: email,
        password: password,
      };
  
      try {
        const response = await fetch("/api/auth/login", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(requestData),
        });
  
        let result;
        try {
          result = await response.json();
        } catch (parseError) {
          result = {};
        }
  
        if (response.ok) {
          localStorage.setItem("user", JSON.stringify(result.data));
          if (result.data.role === "ADMIN") {
            window.location.href = "/admin";
          } else {
            window.location.href = "/workspace";
          }
        } else {
          const errorMessage = "Login failed. Please check your credentials.";
          showError(errorMessage);
          console.error("Error:", result);
        }
      } catch (error) {
        showError("Request failed. Please check your connection and try again.");
        console.error("Error:", error);
      }
    });
  });
  