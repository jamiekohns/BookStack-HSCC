@extends('layouts.simple')

@section('body')
    <div class="container flex" id="home-default">
        <div class="grid" style="height: 80px; background-color: grey;">
            Controls go here
        </div>
        <div class="flex-fill">
            <!-- CSS Fix for colliding Flems.io sytles -->
            <style>
                #flems pre:before {
                    background-color: transparent;
                    border-inline-end: 0;
                }
            </style>
            <div id="flems" style="width:100%;min-height: 50vh;"></div>
                        
            <script src="https://flems.io/flems.html" type="text/javascript" charset="utf-8"></script>
            <script>
            const flems = Flems(document.getElementById('flems'), {
                files: [
                    {
                        name: 'index.html',
                        content: '<h1>Hello World</h1>'
                    },
                    {
                    name: 'app.js',
                    content: 'console.log("ready");'
                    }
                ],
                shareButton: false,
                // autoHeight: true,
            })
            </script>


        </div>
    </div>

@stop
