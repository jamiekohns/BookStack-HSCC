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
            <div id="flems" style="width:100%;min-height: 100%;"></div>
                        
            <script src="https://flems.io/flems.html" type="text/javascript" charset="utf-8"></script>
            <script>
            new Jotted(document.querySelector('#demo'), {
              files: [{
                type: 'css',
                url: 'index.styl'
              }, {
                type: 'html',
                content: '<h1>Demo</h1>'
              }],
              showBlank: true,
              plugins: [
                'stylus',
                {
                  name: 'codemirror',
                  options: {
                    lineNumbers: false
                  }
                }
              ]
            });
            </script>


        </div>
    </div>

@stop
