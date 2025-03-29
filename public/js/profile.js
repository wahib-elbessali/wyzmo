document.addEventListener("DOMContentLoaded", () => {
    const user = JSON.parse(localStorage.getItem("user"));
    
    const userInfoDiv = document.getElementById("userInfo");
    if (user) {
      userInfoDiv.innerHTML = `
        <p><strong>User ID:</strong> ${user.user_id}</p>
        <p><strong>Email:</strong> ${user.email}</p>
        <p><strong>First Name:</strong> ${user.first_name}</p>
        <p><strong>Last Name:</strong> ${user.last_name}</p>
        <p><strong>Role:</strong> ${user.role}</p>
      `;
    } else {
      userInfoDiv.textContent = "No user information found.";
    }
    
    function getColor(input) {
      let sum = 0;
      for (let i = 0; i < input.length; i++) {
        sum += input.charCodeAt(i);
      }
      const index = sum % 6;
      const colors = [ "#aae5af", "#f5d99a", "#f1879e", "#d3e3f3", "#ddf0b9", "#c0f0be" ];
      return colors[index];
    }
    
    const profilePicture = () => {
      const profileButton = document.getElementById("profileButton");
      profileButton.innerHTML = `
        <div style="background-color: ${getColor(user.user_id)}">
          <div ></div>
          <div>${user.first_name[0].toUpperCase()}</div>
        </div>
      `;
    };

    const logo = document.getElementById("logo");
    logo.addEventListener("click", () => {
        window.location.href = "/workspace"
    })
    
    profilePicture();
  });