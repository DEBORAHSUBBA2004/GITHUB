// UI interaction for switching between Sign Up and Sign In
const signUpButton = document.getElementById('signUp');
const signInButton = document.getElementById('signIn');
const container = document.getElementById('container');

signUpButton.addEventListener('click', () => {
    container.classList.add("right-panel-active");
    signInButton.classList.remove("active");
    signUpButton.classList.add("active");
    console.log("Sign Up button clicked");
});

signInButton.addEventListener('click', () => {
    container.classList.remove("right-panel-active");
    signInButton.classList.add("active");
    signUpButton.classList.remove("active");
    console.log("Sign In button clicked");
});

// Separate form submission logic

/*NO PASTE*/
document.querySelectorAll('.no-paste').forEach(element => {
    element.addEventListener('paste', (e) => {
        e.preventDefault();
        alert('Pasting is not allowed in this field.');
    });
});
