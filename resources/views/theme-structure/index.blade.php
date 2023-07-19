@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Theme Structure</h2>
    </div>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
@endif
<div class="row m-3">
    <div class="col-lg-12">
        <div id="jstree"></div>
    </div>
</div>   
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>
<script>
        $(document).ready(function () {
            $('#jstree').jstree({
                'core': {
                    'data': {!! $tree !!}
                },
                'plugins': ['contextmenu'],
                'contextmenu': {
                    'items': function (node) {
                        var menuItems = {
                            'create_folder': {
                                'label': 'Create Folder',
                                'action': function () {
                                    createFolder(node);
                                }
                            },
                            'create_file': {
                                'label': 'Create File',
                                'action': function () {
                                    createFile(node);
                                }
                            }
                        };

                        if (node.id === '#') {
                            delete menuItems.create_file;
                        }

                        return menuItems;
                    }
                }
            });
        });

        function reloadTree() {
            $.ajax({
                url: '/theme-structure/reload-tree',
                type: 'GET',
                success: function(data) {
                    $('#jstree').jstree(true).settings.core.data = data;
                    $('#jstree').jstree(true).refresh();
                },
                error: function(error) {
                    console.log('Error reloading tree: ', error);
                }
            });
        }

        function createFolder(node) {
            var folderName = prompt('Enter the folder name:');
            if (folderName) {
                var newNode = { name: folderName, is_file: 0, parent_id: node.id };
                $.ajax({
                    type: 'POST',
                    url: '/theme-structure',
                    data: newNode,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        reloadTree();
                    }
                });
            }
        }

        function createFile(node) {
            var fileName = prompt('Enter the file name:');
            if (fileName) {
                var newNode = { name: fileName, is_file: 1, parent_id: node.id };
                $.ajax({
                    type: 'POST',
                    url: '/theme-structure/theme-file-store',
                    data: newNode,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        reloadTree();
                    }
                });
            }
        }
    </script>
@endsection