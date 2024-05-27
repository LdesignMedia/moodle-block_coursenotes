import Ajax from 'core/ajax';
import Log from 'core/log';

export const init = () => {
    let noteHistory = [];
    const courseNoteExists = document.getElementById('coursenote-display').textContent.trim() !== '';
    const textarea = document.getElementById('coursenote-textarea');

    // Hide form if course note exists.
    if (courseNoteExists) {
        document.getElementById('coursenote-form').style.display = 'none';
    }

    document.getElementById('edit-icon').addEventListener('click', function () {
        document.getElementById('coursenote-display').style.display = 'none';
        this.style.display = 'none'; // Hide the edit icon itself.
        document.getElementById('coursenote-form').style.display = 'block';

        fetchNoteHistory();
    });

    document.getElementById('cancel-button').addEventListener('click', function () {
        document.getElementById('coursenote-display').style.display = 'block';
        document.getElementById('edit-icon').style.display = 'block'; // Show the edit icon again.
        document.getElementById('coursenote-form').style.display = 'none';
    });

    document.getElementById('undo-button').addEventListener('click', function () {
        if (noteHistory.length > 1) {
            noteHistory.pop(); // Remove current note.
            const lastNote = noteHistory[noteHistory.length - 1];
            document.getElementById('coursenote-textarea').value = lastNote;
            if (noteHistory.length <= 1) {
                this.style.display = 'none'; // Hide undo button if only one note left.
            }
        }
    });

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
                blockinstanceid: blockInstanceId,
                courseid: courseId
            },
            done: (response) => {
                if (response.status) {
                    noteHistory.push(note);
                    noteHistory.shift();
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

    const fetchNoteHistory = () => {
        Ajax.call([{
            methodname: 'block_coursenotes_fetch_notes',
            args: {
                blockinstanceid: blockInstanceId,
                courseid: courseId
            },
            done: (response) => {
                if (response.status) {
                    noteHistory = response.notes;
                    if (noteHistory.length > 0) {
                        document.getElementById('undo-button').style.display = 'block';
                    }
                    Log.log(response);
                    Log.log('Note history fetched successfully');
                } else {
                    Log.log('Error fetching note history:', response.message);
                }
            },
            fail: (error) => {
                Log.error('AJAX error: ' + JSON.stringify(error));
            }
        }]);
    };
};
