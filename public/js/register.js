document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    const errorToast = document.getElementById("error");

    function showError(message) {
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

        const firstName = document.getElementById("fname").value;
        const lastName = document.getElementById("lname").value;
        const email = document.getElementById("email").value;
        const password = document.getElementById("password").value;
        const confirmPassword = document.getElementById("con-password").value;
        const role = document.querySelector('input[name="role"]:checked')?.value;

        if (!role) {
            showError("Please select a role.");
            return;
        }

        const requestData = {
            first_name: firstName,
            last_name: lastName,
            email: email,
            password: password,
            confirm_password: confirmPassword,
            role: role
        };

        try {
            const response = await fetch("/api/auth/register", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(requestData),
            });

            const result = await response.json();

            if (response.ok) {
                console.log("Success:", result);
                window.location.href = "/login";
            } else {
                const errorMessage = "Registration failed. Make sure all fields are correct";
                showError(errorMessage);
                console.error("Error:", result);
            }
        } catch (error) {
            showError("Request failed. Please check your connection and try again.");
            console.error("Request failed:", error);
        }
    });
});
