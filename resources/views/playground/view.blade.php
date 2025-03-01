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

        #flems {
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
            /* opacity: 0.4; */
        }
    </style>

    <div class="container flex" id="home-default">
        <div>
            <form action="/playground/{{ $playground->id }}/update" method="post" id="pg-form">
                <div class="controls">
                    <!-- Save output display -->
                    <span id="save-msg">loading</span>
                    <!-- Save -->
                    <a href="#" title="Save this playground">
                        <img src="/icons/save_64.png" alt="Save this playground" id="pg-save">
                    </a>
                    @if ($playground->user_id == Auth::user()->id || Auth::user()->hasRole(5))
                        <!-- Add a file -->
                        <a href="#" id="pg-add-file" title="Add a file">
                            <img src="/icons/file_plus_64.png" alt="Add a file" id="pg-addfile">
                        </a>
                        <!-- Locked -->
                        <a href="/playground/{{ $playground->id }}/toggle?locked=1" title="Unlock" class="ajax locked"  data-id="{{ $playground->id }}">
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
        <div id="flems"><div>
        <script src="/playground/playground.js" type="text/javascript"></script>
        <script src="/playground/flems.js" type="text/javascript" charset="utf-8"></script>
        <script>
            const contentElement = document.getElementById('playground-content');
            const saveMsg = document.getElementById('save-msg');
            const pgForm = document.getElementById('pg-form');
            const playgroundId = {{ $playground->id }};
            let flems, playgroundLock = true;

            document.addEventListener('DOMContentLoaded', function() {
                // fetch the stored content jy AJAX
                loadContent();
                // load our Flems container
                const state = JSON.parse(contentElement.value);
                let flems = loadFlems(state);

                // Role-based listeners
                // if the user is NOT an instructors, then these will not have any 
                // action associated to them, and will no-op
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
                @endif

                heartbeat();
            });
        </script>
@stop
