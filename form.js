// Function to switch between tabs
function showTab(tabName) {
    var tabs = document.getElementsByClassName("tab");
    for (var i = 0; i < tabs.length; i++) {
        tabs[i].style.display = "none";
    }

    var buttons = document.getElementsByClassName("tabs")[0].getElementsByTagName("button");
    for (var i = 0; i < buttons.length; i++) {
        buttons[i].classList.remove("active");
    }

    document.getElementById(tabName + "-tab").style.display = "block";
    event.currentTarget.classList.add("active");
}

// Function to handle login
function login() {
    var username = document.getElementById("username").value;
    var password = document.getElementById("password").value;

    if (username === "" || password === "") {
        alert("Please fill in all fields.");
    } else if (password.length < 8 || password.length > 15 || !/[A-Z]/.test(password) || !/[!@#$%^&*()_+{}\[\]:;<>,.?~\\/-]/.test(password)) {
        alert("Password must be 8-15 characters long and include at least one capital letter and one symbol.");
    } else {
        // Add your login logic here
        alert("Logging in...");
    }
}

// Function to validate password during signup
function validatePassword() {
    var name = document.getElementById("name").value;
    var lastname = document.getElementById("lastname").value;
    var email = document.getElementById("email").value;
    var password = prompt("Enter your password");

    if (name === "" || lastname === "" || email === "" || password === "") {
        alert("Please fill in all fields.");
    } else if (password.length < 8 || password.length > 15 || !/[A-Z]/.test(password) || !/[!@#$%^&*()_+{}\[\]:;<>,.?~\\/-]/.test(password)) {
        alert("Password must be 8-15 characters long and include at least one capital letter and one symbol.");
    } else {
        // Add your signup logic here
        alert("Account created!");
    }
}


function passwordvisibility() {
    var x = document.getElementById("password");
    if (x.type === "password") {
        x.type = "text";
    } else {
        x.type = "password";
    }
}

