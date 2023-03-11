    <form  id="create-form" action="{{ route('social.post.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="config_id" value="{{$id}}"/>
        <div class="modal-header">
            <h4 class="modal-title">Page Posting</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Picture <small class="text-danger">* You can select multiple images only </small></label>
                <input type="file"  multiple="multiple"  name="source[]" class="form-control-file">
                @if ($errors->has('source.*'))
                <p class="text-danger">{{$errors->first('source.*')}}</p>
                @endif
            </div>
            <div class="form-group">
                <label>Video</label>
                <input type="file"  name="video1" class="form-control-file">
                @if ($errors->has('video'))
                <p class="text-danger">{{$errors->first('video')}}</p>
                @endif
            </div>
            
            <div class="form-group">
                <label for="">Hashtags</label>
                <input type="text" name="hashtags"  id="show_hashtag_field_stop" value="" class="form-control" placeholder="Type your hashtags">
                <div ></div>
                @if ($errors->has('hashtags'))
                <p class="text-danger">{{$errors->first('hashtags')}}</p>
                @endif
            </div>
            <div class="form-group" id="update_hashtag_auto">
              
            </div>

            <div class="form-group">
                <label for="">Message</label>
                <input type="text" name="message" class="form-control" placeholder="Type your message">
                @if ($errors->has('message'))
                <p class="text-danger">{{$errors->first('message')}}</p>
                @endif
            </div>

            <div class="form-group">
                <label for="">Description</label>
                <textarea name="description" class="form-control" cols="30" rows="5"></textarea>
            </div>

            <div class="form-group">
                <label for="">Post on
                    <small class="text-danger">
                    * Can be Scheduled too </small>
                </label>
                <input  type="date"  name="date" class="form-control">
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-secondary">Post</button>
        </div>
    </form>

    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
    </script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js">
    </script>
    <script type="text/javascript">
         function hashsuggetion(val){
           document.getElementById("show_hashtag_field").value += " #"+val;
        }
     $(document).ready(function(){
       
        $("#show_hashtag_field").on("keyup", function() {
            $("#loading-image").show();
            var text = $(this).val();

            var n = text.split(" ");
            var lastWord = n[n.length - 1];

            var startWith = lastWord.charAt(0);
            if(startWith=="#")
            {
                console.log("last word: "+lastWord);
                var wordToSearch = lastWord.substring(1);
                if(wordToSearch)
                {
                    $.ajax({
                        type: "get",
                        url: "/instagram/get/hashtag/"+wordToSearch,
                        async: true,
                        dataType: 'json',
                        beforeSend: function () {
                           // $("#show_hashtag_field").attr('contenteditable','false');
                           $("#loading-image").show();
                        },
                        success: function(data){
                            $("#loading-image").hide();
                            const myArray = data.post.split(" ");
                            console.log(myArray);
                            $('#update_hashtag_auto').html('');
                            myArray.forEach(element => {
                                const valwithouthash = element.split("#");
                                $('#update_hashtag_auto').append("<div class='chip light-blue lighten-2 white-text waves-effect'><a href='#' onclick=hashsuggetion('"+valwithouthash[1]+"') data-hashtag='"+element+"' data-caption ='"+element+"' >"+element+"</a></div>"); //Fills the #auto div with the options

                            });
                        }
                    });
                }else{
                    console.log("No Hashtag word entered");
                }
            }else{
                console.log("Typing normal caption");
            }
          

        });
    });   
    </script>