function getContent() {
    let defaultValue = '{}';
    let storedValue = localStorage.getItem('fContent');

    if (storedValue === null) {
        localStorage.setItem('fContent', defaultValue);
        storedValue = defaultValue;
    }

    return JSON.parse(storedValue);
}

function removeElementsByClass(className) {
    const elements = document.querySelectorAll(`${className}`);
    elements.forEach(element => {
        element.remove();
    });
}

function loadFlems(content) {
    // let fContent = getContent();
    fContent = JSON.parse(content);
    let ele = document.getElementById('flem');
    let flems = Flems(ele, fContent);

    removeElementsByClass('.toolbar div.icon');

    return flems;
}

function addNameInput() {
    if (!document.getElementById('name')) {
        const runtime = document.querySelector('.runtime .toolbar');
        const wrapper = document.createElement('div');
        wrapper.classList.add('input-wrapper');
        const nameInput = document.createElement('input');
        nameInput.type = 'text';
        nameInput.id = 'name';
        nameInput.name = 'name';

        wrapper.appendChild(nameInput);
    }
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

    let fContent = getContent();
    let newFile = {
        name: filename,
        content: ''
    };

    if (fContent.hasOwnProperty('files')) {
        fContent.files.push(newFile);
    } else {
        fContent.files = [newFile]
    }

    localStorage.setItem('fContent', JSON.stringify(fContent));

    flems = loadFlems();
}
