@extends('layouts.app')

@section('content')
    <div class="row" id="common-page-layout">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">CMS Pages "{{ $store->title }}"<span class="count-text"></span></h2>
        </div>
        <br>
        <div class="col-lg-12 margin-tb" id="page-view-result">
            <div class="col-lg-12 pl-5 pr-5">
                <div style="display: flex !important; float: right !important;">
                    <div>
                        <a href="#" class="btn btn-xs btn-secondary create-new-cms-page">Create</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 pl-5 pr-5">
            <form action="/store-website/generate-api-token" method="post">
                <?php echo csrf_field(); ?>

                <div class="col-md-12">
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered overlay api-token-table">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Title</th>
                                <th>Created</th>
                                <th>Updated</th>
                                <th>Is active</th>
                                <th>Edit</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php /** @var \App\Models\MagentoModuleCareers $career */ ?>
                            @foreach($pages as $page)
                                <tr>
                                    <td class="td-id-{{ $page->id }}">
                                        {{ $page->id }}
                                    </td>
                                    <td class="td-title-{{ $page->id }}">
                                        {{ $page->title }}
                                    </td>
                                    <td class="td-creation-time-{{ $page->id }}">
                                        {{ $page->creation_time }}
                                    </td>
                                    <td class="td-update-time-{{ $page->id }}">
                                        {{ $page->update_time }}
                                    </td>
                                    <td class="td-is-active-{{ $page->id }}">
                                        <?=$page->is_active ? 'Yes' : 'No' ?>
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-xs btn-secondary btn-edit-cms-page td-edit-{{ $page->id }}" data-id="{{ $page->id }}">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <style>
        #iframe {
            z-index: 99999;
            display: none;
            position: fixed;
            width: 100%;
            height: 100%;
            margin: auto;
            /* Center the element vertically */
            top: 0;
            bottom: 0;
            /* Center the element horizontally */
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.825);
        }
        #iframe > iframe {
            width: 90%;
            height: 80%;
            overflow-y: scroll;
            margin: auto;
            position: absolute;
            top: 0; left: 0; bottom: 0; right: 0;
            border: none;
            outline: none;
        }
        .close-iframe-btn {
            position: absolute;
            top: 0; right: 0;
            margin: 15px;
            width: 40px;
            height: 40px;
            border: none;
            outline: none;
            background: rgba(200,200,200, 1);
        }
        .close-iframe-btn span {
            font-size: 29px;
        }
    </style>
    <div class="modal-iframe">
        <div id="iframe">
            <button type="button" class="btn-close close-iframe-btn" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <iframe
                    id="inlineFrameExample"
                    title="Inline Frame Example">
            </iframe>
        </div>
    </div>
    <script>
        $(document).on("click", ".create-new-cms-page", function (e) {
            e.preventDefault();
            $("#iframe > iframe").attr("src", '{{ $store->magento_url }}/cms/page/new/');
            $('#iframe').show();
        });

        $(document).on("click", ".open-iframe-btn", function (e) {
            e.preventDefault();
            $('#iframe').show();
        });

        $(document).on("click", ".close-iframe-btn", function (e) {
            e.preventDefault();
            $('#iframe').hide();
        });

        $('a.btn-edit-cms-page').click(function(e) {
            e.preventDefault();
            $('#iframe').show();

            let pageId = $(this).attr('data-id');

            $("#iframe > iframe").attr("src", '{{ $store->magento_url }}/cms/page/edit/page_id/' + pageId + '/');
        });
    </script>
@endsection