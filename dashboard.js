
document.addEventListener('DOMContentLoaded', () => {
    const profilePictureInput = document.getElementById('profile-picture');
    const profilePictureDisplay = document.getElementById('profile-picture-display');
    const logoutButton = document.getElementById('logout-button');

    profilePictureInput.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                profilePictureDisplay.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    logoutButton.addEventListener('click', () => {
        fetch('logout.php', {
            method: 'POST',
        }).then(response => {
            if (response.ok) {
                window.location.href = 'login.php';
            }
        });
    });
});
