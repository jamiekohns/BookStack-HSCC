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
            height: 85vh;
            flex-direction: column;
            overflow: hidden;
        }

        #save {
            width: 24px;
            height: 24px;
            border: 1px solid white;
            border-radius: 3px;
        }

        #save:hover {
            border: 1px solid green;
        }

        #addFile {
            width: 24px;
            height: 24px;
            border: 1px solid white;
            border-radius: 3px;
        }

        #addFile:hover {
            border: 1px solid gray
        }

        #name {
            height: 32px;
            margin-right: 50%;
        }

        span.pg-title {
            font-size: 1.5em;
            font-weight: 700;
        }

        .controls {
            float: right;
        }

        .controls img {
            height: 32px;
            width: 32px;
        }

        .controls a {
            background-color: #f2f2f2;
            border-radius: 4px;
            display: inline-block;
            padding: 2px;
            width: 36px;
            height: 36px;
        }

        .controls a:hover {
            background-color: #ccc;

        }

        #save-msg {
            display: inline-block;
            padding-top: 6px;
            vertical-align: top;
            color: #838383;
        }

        .controls .disabled {
            opacity: 0.4;
        }
    </style>

    <div class="container flex" id="home-default">
        <div>
            <form action="/playground/{{ $playground->id }}/update" method="post" id="pg-form">
                <div class="controls">
                    <!-- Save output display -->
                    <span id="save-msg">loading</span>
                    <!-- Save -->
                    <a href="#" id="pg-save" title="Save this playground">
                        <img src="/icons/save_64.png" alt="Save this playground">
                    </a>
                    @if ($playground->user_id == Auth::user()->id || Auth::user()->hasRole(5))
                        <!-- Add a file -->
                        <a href="#" id="pg-add-file" title="Add a file">
                            <img src="/icons/file_plus_64.png" alt="Add a file" id="pg-addfile">
                        </a>
                        <!-- Locked -->
                        <a href="/playground/{{ $playground->id }}/toggle?locked=1" title="Unlock" class="ajax locked @if(!Auth::user()->hasRole(5))disabled @endif"  data-id="{{ $playground->id }}">
                            <img src="/icons/{{ $playground->locked ? 'unlock' : 'lock' }}_64.png" id="pg-locked">
                        </a>
                        <!-- Published -->
                        @if (Auth::user()->hasRole(5))
                            <a href="/playground/{{ $playground->id }}/toggle?published=1" title="Unpublish" class="ajax published" data-id="{{ $playground->id }}">
                                <img src="/icons/{{ $playground->published ? 'unsend' : 'send' }}_64.png" id="pg-published">
                            </a>
                        @endif
                    @else
                        
                    @endif
                    <a href="#" id="pg-reload" title="Reload output window">
                        <img src="/icons/reload_64.png" alt="Reload output">
                    </a>
                </div>
                <span class="pg-title">{{ $playground->name }}</span>
                <div>
                    <span class="pg-creator">by {{ $playground->user->name }} {{ $playground->user->role }}</span>
                    <!-- <span class="pg-updated">last updated: {{ $playground->updated_at->diffForHumans() }}</span> -->
                </div>
                @csrf
                <input type="hidden" name="name" id="playground-name" value="{{ $playground->name }}">
                <input type="hidden" name="content" id="playground-content">
                <input type="hidden" name="user_id" id="playground-user-id" value="{{ $playground->user_id }}">
                <input type="hidden" name="source_id" id="playground-source-id" value="{{ $playground->source_id }}">
            </form>
        </div>
        <div id="flem"><div>
        <script>
            const contentElement = document.getElementById('playground-content');
            const saveMsg = document.getElementById('save-msg');

            function flashMsg(message) {
                saveMsg.innerText = message;

                setTimeout(() => {
                    saveMsg.innerText = '';
                }, 1000);
            }

            function loadContent() {
                const xhr = new XMLHttpRequest();
                xhr.open('GET', '/playground/{{ $playground->id }}/content', false); // `false` makes the request synchronous
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
            loadContent();
        </script>
        <script src="/libs/lzstring/lz-string.min.js" type="text/javascript"></script>
        <script src="/playground.js?22" type="text/javascript"></script>
        <script src="/libs/flems/dist/flems.js" type="text/javascript" charset="utf-8"></script>
        <script>
            let flems = loadFlems(contentElement.value);
            const pgForm = document.getElementById('pg-form')

            function savePlayground(auto) {
                const form = document.getElementById('pg-form');
                const formData = new FormData(form);
                fetch('/playground/{{ $playground->id }}/update', {
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

            setInterval(() => {
                savePlayground(1);
            }, 7000);

            function serializeForm(form) {
                let data = new FormData(form);
                let output = [];
                for (const [name, value] of data) {
                    output.push(`${encodeURIComponent(name)}=${encodeURIComponent(value)}`);
                }
                return output.join('&');
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

            document.getElementById('pg-save').addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                savePlayground();
            });

            document.getElementById('pg-addfile').addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                addFile();
            });

            document.getElementById('pg-reload').addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                flems.reload();
            });

            @if (Auth::user()->hasRole(5))
                document.getElementById('pg-locked').addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    touchUrl('/playground/{{ $playground->id }}/toggle?locked=1')
                });

                document.getElementById('pg-published').addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    touchUrl('/playground/{{ $playground->id }}/toggle?published=1')
                });
            @else
                document.getElementById('pg-locked').addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                });
            @endif

            window.addEventListener('message', function (event) {
                const saveButton = document.querySelector('.toolbar a');
                const formInput = document.getElementById('playground-content');
                saveButton.dispatchEvent(new Event('mousedown'));
                formInput.value = LZString.decompressFromEncodedURIComponent(saveButton.href.substring(20));
                savePlayground(1);
            });


        </script>
@stop
