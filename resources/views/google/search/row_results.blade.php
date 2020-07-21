@foreach($posts as $key=>$post)
<tr>
  <td><input type="checkbox" class="searchDelete" id="{{$post->id}}" /></td>
  <td>{{ date('d-M-Y H:i:s', strtotime($post->posted_at)) }}</td>
  <td>{{ $post->hashTags->hashtag }}</td>
  <td><a style="word-break:break-all; white-space: normal;" href="{{ $post->location }}" target="_blank">{{ $post->location }}</a></td>
  <td>{{ wordwrap($post->caption,75, "\n", true) }}</td>
</tr>
@endforeach

<script>
  $(document).on('click', '.searchDelete', function() {
    var id = $(this).attr('id');


    swal({
        title: "Are you sure?",
        text: "Are you sure that you want to delete this record?",
        icon: "warning",
        dangerMode: true,
      })
      .then(willDelete => {
        if (willDelete) {
          $.ajax({
           type: "DELETE",
           headers: {
             "X-CSRF-TOKEN": "{{csrf_token()}}"
           },
           cache: false,
           contentType: false,
           processData: false,
           url: "{{ url('google/search/results') }}/" + id,
           success: function(html) {
            swal("Deleted!", "Your record  has been deleted!", "success");
              location.reload();
           }
         })
        }
      });







  });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>