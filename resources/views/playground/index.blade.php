@extends('layouts.simple')

@section('body')
    <link href="https://unpkg.com/vanilla-datatables@latest/dist/vanilla-dataTables.min.css" rel="stylesheet" type="text/css">
    <script src="https://unpkg.com/vanilla-datatables@latest/dist/vanilla-dataTables.min.js" type="text/javascript"></script>
    <style>
        .actions img {
            width: 16px;
            height: 16px;
        }

        .menu-action img {
            height: 32px;
            width: 32px;
        }

        .delete {
            filter: invert(12%) sepia(99%) saturate(3695%) hue-rotate(358deg) brightness(95%) contrast(108%);
        }
        .locked {
            filter: invert(12%) sepia(99%) saturate(3695%) hue-rotate(358deg) brightness(95%) contrast(108%);
        }
        .unlocked {
            filter: invert(16%) sepia(100%) saturate(5904%) hue-rotate(104deg) brightness(95%) contrast(103%);
        }
        .copy {
            filter: invert(7%) sepia(83%) saturate(7258%) hue-rotate(206deg) brightness(87%) contrast(142%);
        }
        .disabled {
            filter: invert(94%) sepia(0%) saturate(4185%) hue-rotate(19deg) brightness(80%) contrast(76%);
        }
    </style>
    <div class="container grid menu-actions">
        <div class="menu-action">
            <a href="/playground/new" class="float right" title="add NEW">
                <img src="/icons/plus_64.png" id="create">
            </a>
        </div>
    </div>
    <div class="container grid">
        <table id="playgrounds">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Owner</th>
                    <th>Source</th>
                    @if (Auth::user()->hasRole(5))
                    <th>Last Updated</th>
                    @endif
                    <th>Actions</th>
                </th>
            </thead>
            <tbody>
            @foreach ($playgrounds as $playground)
                <tr>
                    <td>{{ $playground->id }}</td>
                    <td>
                        @if ($playground->user->id == Auth::user()->id)
                            <a href="/playground/{{ $playground->id }}">
                                {{ $playground->name }}
                            </a>
                        @elseif (Auth::user()->hasRole(5))
                            <a href="/playground/{{ $playground->id }}">
                                {{ $playground->name }}
                            </a>
                        @else
                            {{ $playground->name }} 
                        @endif
                    </td>
                    <td>{{ $playground->user->name }}</td>
                    
                    @if (Auth::user()->hasRole(5))
                    <td>
                        @if ($playground->source_id)
                            {{ $playground->source->user->name }} : {{ $playground->source->name }}
                        @endif
                    </td>
                    @endif
                    
                    <td>{{ $playground->updated_at->diffForHumans() }}</td>
                    <td class="actions">
                    @if (Auth::user()->hasRole(5) || $playground->published)
                        @if (!$playground->source_id || Auth::user()->hasRole(5))
                            <a href="/playground/{{ $playground->id }}/copy" title="Make a copy" class="make-copy" data-name="{{ $playground->name }}">
                                <img src="/icons/copy_64.png" alt="Make a copy" class="copy">
                            </a>
                        @else 
                            <!-- <img src="/icons/copy_64.png" alt="This is a copy" class="disabled" title="This is a copy and cannot be re-copied"> -->
                        @endif
                    @endif
                    @if (Auth::user()->hasRole(5) || 
                        $playground->user->id == Auth::user()->id)
                        <a href="/playground/{{ $playground->id }}/delete" title="Delete" class="delete">
                            <img src="/icons/trash_64.png" alt="Delete this playground">
                        </a>
                    @endif
                    @if (Auth::user()->hasRole(5))
                        <a href="/playground/{{ $playground->id }}/toggle?locked=1" 
                            title="{{ $playground->locked ? 'Unlock' : 'Lock' }}" 
                            class="ajax {{ $playground->locked ? 'locked' : 'unlocked' }} lock"  
                            data-id="{{ $playground->id }}">
                            <img src="/icons/{{ $playground->locked ? 'lock' : 'unlock' }}_64.png">
                        </a>
                    @endif
                    @if (Auth::user()->hasRole(5) || 
                        Auth::user()->hasRole(1))
                        <a href="/playground/{{ $playground->id }}/toggle?published=1" title="Unpublish" class="ajax published" data-id="{{ $playground->id }}">
                            <img src="/icons/{{ $playground->published ? 'send' : 'unsend' }}_64.png">
                        </a>
                    @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    
    <script>
        const container = window;
        container.addEventListener('click', function(event) {
            const target = event.target;
            const parent = target.parentElement;
            const classes = parent.classList;
            const id = parent.dataset.id;

            switch (true) {
                case classes.contains('make-copy'):
                    let name = parent.dataset.name;
                    parent.href += '?name='+prompt('Enter a name for this copy', name + ' COPY');
                break;

                case classes.contains('delete'):
                    if (confirm("Are you SURE you want to delete this playground?") !== true) {
                        event.preventDefault();
                        event.stopPropagation();
                        return;
                    }
                break;

                case target.id == 'create':
                    parent.href += '?name='+prompt('Enter a name for your new playground');
                break;

                case classes.contains('ajax'):
                    let act = classes.contains('lock') ? 'locked' : 'published';
                    let url = '/playground/'+id+'/toggle?'+act+'=1';

                    event.preventDefault();

                    fetch(url)
                    .then(response => {
                        if (!response.ok) {
                        throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.playground) {
                            const pg = JSON.parse(data.playground);

                            parent.parentElement.querySelector('a.lock img').src = pg.locked ? '/icons/unlock_64.png' : '/icons/lock_64.png';
                            parent.parentElement.querySelector('a.published img').src = pg.published ? '/icons/unsend_64.png' : '/icons/send_64.png';
                            
                            console.log(pg);
                        } else {
                            throw new Error('no pg in data!');
                        }
                    })
                    .catch(error => {
                        // Handle errors
                        console.error('There was a problem fetching the data:', error);
                    });

                    parent.blur();
                break;
            }
        });

        let dataTable = new DataTable(document.getElementById('playgrounds'));
    </script>

    
@stop
