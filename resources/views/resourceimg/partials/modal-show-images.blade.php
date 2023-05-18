
<div class="modal fade" id="showresource" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" style="font-size: 24px;">Resources Center Images</h2>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                @if (Auth::user()->hasRole('Admin'))
                    {!! Form::open(['route' => 'delete.resource']) !!}
                    <input type="hidden" name="id" value="{{ $allresources['id'] }}">
                    <button type="submit" name="button_type" value="delete" class="pull-right btn btn-image"><img
                            src="/images/delete.png" /></button>
                    {!! Form::close() !!}
                @endif
                @isset($allresources['images'])
                    @if ($allresources['images'] != null)
                        @foreach (json_decode($allresources['images']) as $image)
                        <div class="row">
                            <div class="col-md-6" style="margin-top: 15px">
                                <img id='myimage2' class="myImg"
                                    src="{{ URL::to('/category_images/' . $image) }}"
                                    alt="{{ URL::to('/category_images/' . $image) }}"
                                    style="width: 600px !important;height: 300px !important;">
                            </div>
                            </div>
                        @endforeach
                    @endif
                @endisset

            </div>
            <div class="modal-footer">
                <canvas style="border:none;display: none;" id="my_canvas"></canvas>
                <input type="hidden" autocomplete="off" class="form-control" name="image2" id="cpy_img">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i
                        class="fa fa-times"></i></button>
            </div>
        </div>
    </div>
</div>
