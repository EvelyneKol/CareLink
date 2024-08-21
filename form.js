// συνάρτηση για εναλλαγή ανέμεσα στα tabs
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

// συνάρτηση για διαχείρηση login
function login() {
    var username = document.getElementById("username").value;
    var password = document.getElementById("password").value;

    if (username === "" || password === "") {
        alert("Please fill in all fields.");
    } else if (password.length < 8 || password.length > 15 || !/[A-Z]/.test(password) || !/[a-z]/.test(password)) {
        alert("Password must be 8-15 characters long and include at least one number and one uppercase and lowercase letter.");
    } 
}



// συνάρτηση για έλεγχο έγκυρου κωδικού κατα το signup
function validatePassword() {
    var name = document.getElementById("name").value;
    var lastname = document.getElementById("lastname").value;
    var email = document.getElementById("email").value;
    var password = prompt("Enter your password");

    if (name === "" || lastname === "" || email === "" || password === "") {
        alert("Please fill in all fields.");
    } else if (password.length < 8 || password.length > 15 || !/[A-Z]/.test(password) || !/[a-z]/.test(password)) {
        alert("Password must be 8-15 characters long and include at least one number and one uppercase and lowercase letter.");
    } else {
        // μήνυμα επιτυχούς εγγραφής
        alert("Account created!");
    }
}

//συνάρτηση για εμφάνιση κωδικού 
function passwordvisibility() {
    var x = document.getElementById("password");
    if (x.type === "password") {
        x.type = "text";
    } else {
        x.type = "password";
    }
}

