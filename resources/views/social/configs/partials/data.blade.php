@if($socialConfigs->isEmpty())

<tr>
 <td>
   No Result Found
 </td>
</tr>
@else



<tr>
 <td>-</td>
 <td>Facebook</td>
 <td>Design</td>
 <td>Design Page</td>
 <td>Active</td>
 <td>-</td>
 <td>
   <a class="btn btn-sm" href="{{route('social.post.index',5)}} ">Manage Posts</a>
   <!-- <a class="btn btn-sm" href="{{ route('social.account.posts',5) }} ">Webhook Posts</a> -->
 </td>
</tr>

@endif