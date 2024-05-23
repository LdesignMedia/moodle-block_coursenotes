import Ajax from 'core/ajax';
import Log from 'core/log';

export const init = () => {
    const courseNoteExists = document.getElementById('coursenote-display').textContent.trim() !== '';

    // Hide form if course note exists.
    if (courseNoteExists) {
        document.getElementById('coursenote-form').style.display = 'none';
    }

    document.getElementById('edit-icon').addEventListener('click', function () {
        document.getElementById('coursenote-display').style.display = 'none';
        this.style.display = 'none'; // Hide the edit icon itself.
        document.getElementById('coursenote-form').style.display = 'block';
    });

    document.getElementById('cancel-button').addEventListener('click', function () {
        document.getElementById('coursenote-display').style.display = 'block';
        document.getElementById('edit-icon').style.display = 'block'; // Show the edit icon again.
        document.getElementById('coursenote-form').style.display = 'none';
    });

    const textarea = document.getElementById('coursenote-textarea');
    let timeoutId;
    // Fetch the block instance ID from the DOM.
    const blockInstanceId = textarea.dataset.blockinstanceid;
    const courseId = textarea.dataset.courseid;

    textarea.addEventListener('input', () => {
        clearTimeout(timeoutId);

        if (textarea.value.length > 10) {
            timeoutId = setTimeout(() => {
                saveNoteAJAX(textarea.value);
            }, 10000);
        }
    });

    const saveNoteAJAX = (note) => {
        Ajax.call([{
            methodname: 'block_coursenotes_save_note',
            args: {
                coursenote: note,
                blockinstanceid: blockInstanceId, // Pass the block instance ID.
                courseid: courseId // Pass the course ID.
            },
            done: (response) => {
                if (response.status) {
                    Log.log('Note saved successfully');
                } else {
                    Log.log('Error saving note:', response.message);
                }
            },
            fail: (error) => {
                Log.error('AJAX error: ' + JSON.stringify(error));
            }
        }]);
    };
};
