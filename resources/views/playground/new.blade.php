@extends('layouts.simple')

@section('body')
    <style>
        html {
            height: 100%;
            width: 100%;
            margin: 0;
        }
        
        body {
            display: block;
            min-height: 100%;
            width: 100%;
            height: 100%;
            margin: 0;
        }

        .toolbar a {
            position: absolute;
            left: -4000px;
        }

        pre.CodeMirror-line:before {
            background-color: transparent;
            border: 0;
        }

        #flem {
            display: flex;
            width: 100%;
            height: 90vh;
            flex-direction: column;
            overflow: hidden;
        }

        #save {
            z-index: 999;
            width: 24px;
            height: 24px;
            background-color: #4caf50;
        }

        #save:hover {
            background-color: #65d597;
        }
    </style>

    <div class="container flex" id="home-default">
        <div id="flem"><div>
        <script src="/libs/lzstring/lz-string.min.js" type="text/javascript"></script>
        <script src="/libs/flems/dist/flems.js" type="text/javascript" charset="utf-8"></script>
            <script>
                flem = Flems(document.getElementById('flem'), {
                    files: [{
                        name: '.html'
                    }, {
                        name: '.js'
                    }, {
                        name: '.css'
                    }],
                    selected: '.html',
                });
                window.addEventListener('message', ({ data }) => {
                    let b = document.querySelector('.toolbar a');
                    b.dispatchEvent(new Event('mousedown'));
                    fContent = LZString.decompressFromEncodedURIComponent(b.href.substring(20));
                    console.log(fContent);
                    localStorage.setItem('fContent', fContent);
                    addSaveButton();
                });

                function addSaveButton()
                {
                    if (!document.getElementById('save')) {
                        const runtime = document.querySelector('.runtime .toolbar');
                        const saveButton = document.createElement('img');
                        saveButton.src = '/save_icon.svg';
                        saveButton.alt = 'Save this project';
                        saveButton.id = 'save';

                        saveButton.addEventListener('click', function () {
                            console.log('saving');
                        });
                        
                        runtime.appendChild(saveButton);
                    }
                }
        </script>
@stop
