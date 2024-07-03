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
        window.location.reload();
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
        let formattedNote;
        formattedNote = stripHtmlTags(note);
        Ajax.call([{
            methodname: 'block_coursenotes_save_note',
            args: {
                coursenote: formattedNote,
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

    const stripHtmlTags = (str) => {
        return str.replace(/<\/?[^>]+(>|$)/g, "");
    };

    const fetchNoteHistory = () => {
        Ajax.call([{
            methodname: 'block_coursenotes_fetch_notes',
            args: {
                courseid: courseId
            },
            done: (response) => {
                if (response.status) {
                    noteHistory = response.notes;
                    if (response.note_count > 1) {
                        document.getElementById('undo-button').style.display = 'block';
                    } else {
                        document.getElementById('undo-button').style.display = 'none';
                    }
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
