export const init = () => {
    const courseNoteExists = document.getElementById('coursenote-display').textContent.trim() !== '';

    // Hide form if course note exists
    if (courseNoteExists) {
        document.getElementById('coursenote-form').style.display = 'none';
    }

    document.getElementById('edit-icon').addEventListener('click', function () {
        document.getElementById('coursenote-display').style.display = 'none';
        this.style.display = 'none'; // Hide the edit icon itself
        document.getElementById('coursenote-form').style.display = 'block';
    });

    document.getElementById('cancel-button').addEventListener('click', function () {
        document.getElementById('coursenote-display').style.display = 'block';
        document.getElementById('edit-icon').style.display = 'block'; // Show the edit icon again
        document.getElementById('coursenote-form').style.display = 'none';
    });
};
