function validateForm() {
    const username = document.getElementById("username").value;
    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm-password").value;
    if (password !== confirmPassword) 
        {
            alert("Passwords doesn't not match. Please try again.");
            return false;
        }
    if (username.length < 3 || username.length > 25) 
        {
            alert("Username must be between 3 and 25 characters.");
            return false;
        }
    if (password.length < 8) 
        {
            alert("Password must be at least 8 characters long.");
            return false;
    }
    return true;
}    
