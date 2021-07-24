@foreach ($websites as $list)
    {{-- <tr> --}}
        {{-- <td>{{ \Carbon\Carbon::parse($list->created_at)->format('d-m-y') }} </td> --}}
        {{-- <td>{{ $list->id }}</td> --}}
        {{-- <td>{{ $list->storeWebsite->website ?? '' }}</td> --}}
        {{-- <td>{{ $list->name }}</td> --}}
        {{-- <td></td> --}}

        {{-- <td> --}}
            {{-- <button title="Open Images" type="button" class="btn preview-attached-img-btn btn-image no-pd" --}}
                {{-- data-suggestedproductid="{{ $list->id }}"> --}}
                {{-- <img src="/images/forward.png" style="cursor: default;"> --}}
            {{-- </button> --}}
            {{-- <button title="Send Images" type="button" class="btn btn-image sendImageMessage no-pd" data-id="{{$list->id}}" data-suggestedproductid="{{$list->id}}"><img src="/images/filled-sent.png" /></button> --}}
        {{-- </td> --}}
    {{-- </tr> --}}


    @if (!$list->stores)

        <tr class="expand-{{ $list->id }} hidden">
            <td colspan="4" class="text-center">Stores</td>
        </tr>

        <tr class="expand-{{ $list->id }} hidden">
            <td>{{ 'No Store found' }}</td>
        </tr>
    @endif

    @foreach ($list->stores as $stIndex => $store)

        @if ($stIndex == 0)
            <tr class="expand-{{ $list->id }} hidden">
                <td colspan="4" class="text-center">
                    <h4>Stores</h4>
                </td>
            </tr>
        @endif

            @foreach ($store->storeViewMany as $item)
                
                <tr class="expand-{{ $list->id }}">
                    <td>{{ \Carbon\Carbon::parse($store->created_at)->format('d-m-y') }}</td>
                    <td>{{ $store->id }}</td>
                    <td>{{ $list->storeWebsite->website ?? '' }}</td>
                    <td>{{ $store->name }}</td>
                    <td>{{ $item->name }}({{ $item->code }})</td>

                    <td>
                        <button data-website={{ $list->storeWebsite->website ?? '' }} type="button" class="btn btn-xs btn-image scrapper-python-modal" title="Scrapper action" data-toggle="modal" data-target="#scrapper-python-modal">
                            <img src="/images/add.png" alt="" style="cursor: pointer">
                        </button>
                        

                        <button data-url="{{ route('scrapper.phyhon.listImages', ['id' => $store->id,'code' => $item->code]) }}" title="Open Images"
                            type="button" class="btn show-scrape-images btn-image no-pd"
                            data-suggestedproductid="{{ $store->id }}">
                            <img src="/images/forward.png" style="cursor: default;">
                        </button>

                        <span class="btn p-0"> <input type="checkbox" class="defaultInput" {{ $store->is_default ? 'checked' : '' }}
                                onclick="setStoreAsDefault(this)" data-website-id="{{ $list->id }}"
                                data-store-id="{{ $store->id }}" /> Set as default</span>

                        <!-- Button trigger modal -->


                        {{-- <button title="Send Images" type="button" class="btn btn-image sendImageMessage no-pd" data-id="{{$store->id}}" data-suggestedproductid="{{$list->id}}"><img src="/images/filled-sent.png" /></button> --}}
                    </td>

                    <!--  <td colspan="7" id="attach-image-list-{{ $list['id'] }}">
                    @if ($list['scrapper_image'])
                    {{-- @include('scrapper-phyhon.list-image-products') --}}
                        
                    @endif
                    </td> -->
                </tr>

            @endforeach
            



        <tr class="expand-images-{{ $store->id }} hidden">
            <td colspan="7" id="attach-image-list-{{ $store->id }}">
                {{-- @if ($store->scrapperImage) --}}
                {{-- @include('scrapper-phyhon.list-image-products') --}}
                {{-- @endif --}}
            </td>
        </tr>

    @endforeach

    <tr class="expand-{{ $list->id }} hidden">
        <td colspan="4" class="text-center">
            <hr>
        </td>
    </tr>

@endforeach
{{--<tr>--}}
{{--    <td colspan="4">--}}
{{--        {{ $websites->appends(request()->except('page'))->links() }}--}}
{{--    </td>--}}
{{--</tr>--}}

<!-- Modal -->
<div class="modal fade" id="scrapper-python-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="scrapper-python-title"> Site scrapper action </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">



                <form action="" method="POST" id="scrapper-python-form">
                    @csrf

                    <div class="d-flex justify-content-between mt-2 mb-4">
                        <div class="">
                            <div class="mb-3">
                                <input type="radio" name="name" id="start" value="start" checked>
                                <label class="form-check-label" for="start">
                                    Start
                                </label>
                            </div>
                            <div>
                                <input type="radio" name="type" id="desktop" value="desktop" checked>
                                <label class="form-check-label" for="desktop">
                                    Desktop
                                </label>
                            </div>
                        </div>
                        <div class="">
                            <div class="mb-3">
                                <input type="radio" name="name" id="stop" value="stop">
                                <label class="form-check-label" for="stop">
                                    Stop
                                </label>
                            </div>
                            <div>
                                <input type="radio" name="type" id="mobile" value="mobile">
                                <label class="form-check-label" for="mobile">
                                    Mobile
                                </label>
                            </div>

                        </div>

                        <div class="">
                            <div class="mb-3">
                                <input type="radio" name="name" id="get-status" value="get-status">
                                <label class="form-check-label" for="get-status">
                                    Get status
                                </label>
                            </div>
                            <div>
                                <input type="radio" name="type" id="tablet" value="tablet">
                                <label class="form-check-label" for="tablet">
                                    Tablet
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer pb-0">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Send request</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>


<script>
    let websiteName = null;

    $(document).on('click', '.scrapper-python-modal', function(e) {

        websiteName = $(this).data('website').replace('www.', '').replace('.com', '')

    })


    $(document).on('submit', '#scrapper-python-form', function(e) {

        e.preventDefault()

        $this = $(this)
        const formData = $this.serialize()
        const typeOfData = $('input[name="type"]:checked').val();
        const nameOfData = $('input[name="name"]:checked').val();

        console.log(websiteName, 'aaaaaaaaa')

        $.ajax({
            type: 'POST',
            url: "/scrapper-python/call",
            beforeSend: function() {
                $("#loading-image").show();
            },
            data: {
                _token: "{{ csrf_token() }}",
                webName: websiteName,
                type: typeOfData,
                data_name: nameOfData
            },
            dataType: "json"
        }).done(function(response) {
            $("#loading-image").hide();
            if (response.message) {
                toastr['success'](response.message, 'success');
            } else {
                toastr['error'](response.err, 'error');
            }
            $('#scrapper-python-modal').modal('hide')
        }).fail(function(response) {
            $("#loading-image").hide();
            $('#scrapper-python-modal').modal('hide')

            console.log("Sorry, something went wrong");
        });

    })
</script>
