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

        div.fields {
            margin-top: 4px;
            margin-bottom: 6px;
            padding-bottom: 2px;
            border-bottom: 1px solid gray;
        }

        div.fields label {
            display: inline-block;
        }

        input[name='name'] {
            width: 48%;
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
    </style>

    <div class="container flex" id="home-default">
        <form method="post" action="/playground" id="form">
            <div class="controls">
                <a href="#" id="pg-save">
                    <img src="/icons/save_64.png" alt="Save this playground">
                </a>
            </div>
            <div class="fields">
                @csrf
                <label for="name">Title:</label>
                <input type="text" name="name">

                @if (Auth::user()->hasRole(5))
                <label for="publish">Publish</label>
                <input type="checkbox" name="publish">

                <label for="locked">Locked</label>
                <input type="checkbox" name="locked">
                @endif
            </div>
            <input type="hidden" name="content" id="playground-content">
            <div id="flem"><div>
            <script src="/libs/lzstring/lz-string.min.js" type="text/javascript"></script>
            <script src="/playground.js?20" type="text/javascript"></script>
            <script src="/libs/flems/dist/flems.js" type="text/javascript" charset="utf-8"></script>
            <script>
                let flems = loadFlems('{}');
                const pgSave = document.getElementById('pg-save');

                window.addEventListener('message', function (event) {
                    const saveButton = document.querySelector('.toolbar a');
                    const formInput = document.getElementById('playground-content');
                    saveButton.dispatchEvent(new Event('mousedown'));
                    formInput.value = LZString.decompressFromEncodedURIComponent(saveButton.href.substring(20));
                    console.log(formInput.value);
                });

                function savePlayground() {
                    const form = document.getElementById('form');
                    form.submit();
                }

                pgSave.addEventListener('click', function () {
                    savePlayground();
                });
            </script>
        <form>
    </div>
@stop
