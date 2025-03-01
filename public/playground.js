

function flashMsg(message) {
    saveMsg.innerText = message;

    setTimeout(() => {
        saveMsg.innerText = '';
    }, 1000);
}

function loadContent() {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `/playground/${playgroundId}/content`, false); // `false` makes the request synchronous
    xhr.send(null);

    if (xhr.status === 200) {
        try {
            const jsonData = JSON.parse(xhr.responseText);
            contentElement.value = JSON.stringify(jsonData);
            flashMsg('content loaded');

            return true;
        } catch (e) {
            console.error('Error parsing JSON:', e);
            return false;
        }
    } else {
        console.error('Error loading JSON, status code:', xhr.status);
        return false;
    }
}

function getContent() {
    return JSON.parse(contentElement.value);
}

function savePlayground(id, auto) {
    const form = document.getElementById('pg-form');
    const formData = new FormData(form);
    const url = `/playground/${id}/update`;

    fetch(url, {
        method: 'POST',
        body: formData,
    })
        .then(response => response.json())
        .then(data => {
            if (data) {
                console.log('Save Success:', data);
                document.getElementById('pg-locked').src = data.locked ? '/icons/unlock_64.png' : '/icons/lock_64.png';
                let pgLocked = document.getElementById('pg-published');
                if (pgLocked) {
                    pgLocked.src = data.locked ? '/icons/unsend_64.png' : '/icons/send_64.png';
                    if (data.locked) {
                        document.getElementById('pg-addfile').classList.add('disabled');
                    } else {
                        document.getElementById('pg-addfile').classList.remove('disabled');
                    }
                }

                if (auto) {
                    flashMsg('auto-saved');
                } else {
                    flashMsg('saved');
                }
            }
        })
        .catch(error => {
            console.error('Save Error:', error);
        });
}

function removeElementsByClass(className) {
    const elements = document.querySelectorAll(`${className}`);
    elements.forEach(element => {
        element.remove();
    });
}

function loadFlems(state) {
    let ele = document.getElementById('flems');
    flems = Flems(ele, state);
    removeElementsByClass('.toolbar div.icon');

    return flems;
}

function getFileExtension(filename) {
    if (!filename || typeof filename !== 'string') {
        return ''; // Handle empty or invalid input
    }

    const parts = filename.split('.');
    if (parts.length <= 1) {
        return ''; // No extension found
    }
    return parts.pop();
}

function addFile() {
    console.log('adding file...');

    let allowedExtensions = ['js', 'css', 'html'];
    let filename = prompt('enter filename');
    let ext = getFileExtension(filename);

    if (!allowedExtensions.includes(ext)) {
        alert(ext + ' is not a valid extension!');
        return;
    }

    let state = getContent();
    let newFile = {
        name: filename,
        content: ''
    };

    if (state.hasOwnProperty('files')) {
        state.files.push(newFile);
    } else {
        state.files = [newFile]
    }

    flems = loadFlems(state);
}

function touchUrl(url) {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', url);
    xhr.send(null);

    if (xhr.status === 200) {
        try {
            const data = JSON.parse(xhr.responseText);
            if (data.playground) {
                if (data.playgroud.published) {
                    document.getElementById('published-img').src = data.playgroud.published ? '/icons/unsend_64.png' : '/icons/send_64.png';
                }
                if (data.playgroud.locked) {
                    document.getElementById('locked-img').src = data.playgroud.locked ? '/icons/unlock_64.png' : '/icons/lock_64.png';
                }
            }

            return true;
        } catch (e) {
            console.error('Error parsing JSON:', e);
            return false;
        }
    } else {
        console.error('Error loading JSON, status code:', xhr.status);
        return false;
    }
}

function statusUpdate() {
    fetch(`/playground/${playgroundId}/status`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return response.json();
        })
        .then(data => {
            const filterGreen = 'invert(48%) sepia(61%) saturate(4835%) hue-rotate(97deg) brightness(112%) contrast(114%)';
            const filterRed = 'invert(12%) sepia(99%) saturate(3695%) hue-rotate(358deg) brightness(95%) contrast(108%);';
            const pgLocked = document.getElementById('pg-locked');
            const pgAddfile = document.getElementById('pg-addfile');
            const pgSave = document.getElementById('pg-save');

            pgLocked.src = data.locked ? '/icons/lock_64.png' : '/icons/unlock_64.png';
            pgLocked.style.filter = data.locked ? filterRed : filterGreen;
            pgAddfile.style.filter = data.locked ? filterRed : filterGreen;
            pgSave.style.filter = data.locked ? filterRed : filterGreen;
            playgroudLock = data.locked;

            if (data.locked) {

            }
        })
        .catch(error => {
            // Handle errors here
            console.error('There was a problem with the status fetch operation:', error);
        });
}

function heartbeat() {
    statusUpdate();
    setTimeout(heartbeat, 3500);
}

function updatePlayground() {
    document.getElementById('pg-locked').src = data.locked ? '/icons/unlock_64.png' : '/icons/lock_64.png';
}

/* LISTENERS */
document.getElementById('pg-save').addEventListener('click', function (e) {
    if (playgroudLock) {
        return;
    }

    flems.reload();
    e.preventDefault();
    e.stopPropagation();
    savePlayground(playgroundId);
});

document.getElementById('pg-addfile').addEventListener('click', function (e) {
    if (playgroudLock) {
        return;
    }
    
    e.preventDefault();
    e.stopPropagation();
    addFile();
});

document.getElementById('pg-reload').addEventListener('click', function (e) {
    e.preventDefault();
    e.stopPropagation();
    flems.reload();
});

window.addEventListener('stateChanged', function (event) {
    // console.log(event);
    const formInput = document.getElementById('playground-content');
    formInput.value = JSON.stringify(event.detail);
    savePlayground(playgroundId, true);
});

window.addEventListener('beforeunload', function (event) {
    savePlayground(playgroundId, true);
});

