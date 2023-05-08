<!-- Modal -->
<div id="quick-instruction-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Quick Instruction</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            @php
                $pageInstruction = \App\PageInstruction::where("page",request()->fullUrl())->first()
            @endphp
            <textarea id="editor-instruction-content" data-url="{{ route('instructionCreate') }}" data-page="{{ request()->fullUrl() }}" class="editor-instruction-content" name="instruction">{{ ($pageInstruction) ? $pageInstruction->instruction : "" }}</textarea>
        </div>
    </div>
</div>

<div id="takenote-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Quick Instruction</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="" id="save-note-form">
                <div class="col-12 mt-4">
                    @php
                        $category = \App\PageNotesCategories::pluck('name', 'id')->toArray();
                    @endphp
                    <div class="form-group">
                        <label for="">Category:</label>
                        <select name="" id="takenote-category" class="form-control">
                            <option disabled selected>Select</option>
                            @if (isset($category) && count($category) > 0)
                                @foreach ($category as $key => $item)
                                    <option value="{{$key}}">{{$item}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
    
                    {{-- data-url="{{ route('page-notes.createNote') }}" data-page="{{ request()->fullUrl() }}" --}}
                    <div class="form-group takenote-content-container">
                        <div class="form-group">
                            <label for="">Title:</label>
                            <input type="text" name="title" id="note_title" class="form-control">
                        </div>
                        <div class="form-group">
                            <textarea id="takenoteContent" class="editor-takenote-content" name="note"></textarea>
                        </div>
                        <div class="form-group">
                            <button id="saveNoteButton" class="btn btn-secondary">Click</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        CKEDITOR.replace("takenoteContent");

        $(".takenote-content-container").hide();
        $("#takenote-category").change(function (e) { 
            e.preventDefault();
            let url = "{{request()->fullUrl()}}";
            let category_id = $(this).val();
            $("#takenoteContent").attr("data-category", category_id);

            $.ajax({
                type: "get",
                url: "{{route('page-notes.getValue')}}",
                data: {
                    url,
                    _token: "{{ csrf_token() }}",
                    category_id: $(this).val()
                },
                beforeSend: function(){
                    $(".takenote-content-container").hide();
                },
                success: function (response) {
                    if(response && response.data){
                        CKEDITOR.instances.takenoteContent?.setData(response.data);
                        $("#note_title").val(response.title);
                    } else {
                        CKEDITOR.instances.takenoteContent?.setData("");
                        $("#note_title").val("");
                    }
                    $(".takenote-content-container").show();
                }
            });
        });

        $("#saveNoteButton").click(function (e) { 
            e.preventDefault();
            let url = "{{request()->fullUrl()}}";
            let note = CKEDITOR.instances.takenoteContent?.getData();
            let title = $("#note_title").val();

            if(title == ""){
                toastr['error']('Please enter Note title.');
                return
            }
            if(note == ""){
                toastr['error']('Please enter Note.');
                return
            }
            $.ajax({
                type: "post",
                url: "{{route('page-notes.createNote')}}",
                data: {
                    url,
                    category_id: $("#takenote-category").val(),
                    note,
                    title,
                    _token: "{{ csrf_token() }}",
                },
                beforeSend: function(){
                    
                },
                success: function (response) {
                    toastr['success']('Note updated successfully.');
                },
                error: function() {
                    toastr['success']('Error while adding note.');
                }
            });
        });
    });
</script>