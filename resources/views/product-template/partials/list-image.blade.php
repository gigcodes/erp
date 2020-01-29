
@foreach($templates as $template)
<tr>
<td>{{ $template->id }}</td>
<td>{{ $template->template_no }}</td>
<td><img src="{{ $template->getMedia(config('constants.media_tags'))->first() ? $template->getMedia(config('constants.media_tags'))->first()->getUrl() : '' }}" class="img-responsive grid-image" alt="" id="img{{ $template->id }}" data-media="{{ $template->getMedia(config('constants.media_tags'))->first() ? $template->getMedia(config('constants.media_tags'))->first()->id : ''}}" width="200" height="200" /></td>
<td>{{ $template->product_title }}</td>
<td>@if($template->brand) {{ $template->brand->name }} @endif</td>
<td>@if($template->category) {{ $template->category->title }} @endif</td>
<td>{{ $template->currency }}</td>
<td>{{ $template->price }}</td>
<td>{{ $template->discounted_price }}</td>
<td>{{ $template->created_at }}</td>

</tr>
@endforeach


