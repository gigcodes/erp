 @php $count = 1; @endphp
                    @foreach($medias as $key=>$post)
                   
                       <tr>
                                <td>
                                    {{ $count }}
                                    @php $count++ @endphp
                                    <br>
                                    <a class="btn btn-sm btn-image hide-media" data-id="{{$post['media_id']}}">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                                <td>#{{ $hashtag->hashtag }}</td>
                                <td><a href="https://instagram.com/{{$post['username']}}"> 
                                     {{ $post['username'] }}
                                       
                                    </a></td>
                                <td><a href="https://instagram.com/p/{{$post['code']}}">Visit Post</a></td>
                                <td>
                                    @foreach($post->media_url as $url)
                                         @if (isset($url['media_type']) &&  $url['media_type'] === 1)

                                            <a href="{{$url['url']}}"><img src="{{ $url['url'] }}" style="width: 100px;"></a>
                                             @break
                                         @else
                                            <a href="{{ $url }}"><img src="{{ $url }}" style="width: 100px;"></a>
                                            @break
                                        @endif 
                                    @endforeach
                                   
                                </td>
                                <td style="word-wrap: break-word;text-align: justify;">
                                    <div class="expand-row" style="width:150px;text-align: justify">
                                        <span class="td-mini-container">
                                            {{ strlen($post['caption']) > 20 ? substr($post['caption'], 0, 20).'...' : $post['caption'] }}
                                          </span>

                                        <span class="td-full-container hidden">
                                            {{ $post['caption'] }}
                                        </span>
                                    </div>
                                </td>

                                <td>
                                    <div>
                                        {{ count($post->comments) }}
                                    </div>
                                </td>
                                <td>
                                    <div>
                                         <span class="td-mini-container">
                                            {{ strlen($post['location']) > 5 ? substr($post['location'], 0, 5).'...' : $post['location'] }}
                                          </span>
                                        <span class="td-full-container hidden">
                                            {{ $post['location'] }}
                                        </span>
                                    </div>
                                </td>
                                <td>{{ $post['created_at']->format('d-m-y') }}</td>
                                <td> @if(isset($post->send_comment[0])) 
                                     <span class="td-mini-container" style="color: red;">
                                        
                                            {{ strlen($post->send_comment[0]->comment) > 30 ? substr($post->send_comment[0]->comment, 0, 30).'...' : $post->send_comment[0]->comment }}
                                          </span>
                                        <span class="td-full-container hidden">
                                            {{ $post->send_comment[0]->comment }}
                                        </span>
                                @endif
                                <button type="button" class="btn btn-xs btn-image" onclick="loadComments({{ $post->id }})" title="Load messages"><img src="/images/chat.png" alt=""></button>
                            </td>
                                <td style="width: 600px;">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <select class="form-control" name="account_id" id="account_id_{{$post['post_id']}}">
                                                @foreach($accs as $cc)
                                                    <option value="{{ $cc->id }}">{{ $cc->last_name }}</option>
                                                @endforeach
                                            </select>
                                            <select class="form-control" name="narrative_{{$post['post_id']}}" id="narrative_{{$post['post_id']}}">
                                                <option value="common">Common</option>
                                                <option value="promotion">Promotion</option>
                                                <option value="victim">Victim</option>
                                                <option value="troll">Troll</option>
                                            </select>
                                        </div>
                                        <div class="col-md-9">
                                            <textarea type="text" rows="4" class="comment-it form-control" data-author="{{$post['username']}}" data-code="{{$post['code']}}" data-mediaId="{{$post['post_id']}}" placeholder="Type comment..."></textarea>
                                        </div>
                                    </div>
                                </td>
                                 <td></td>
                            </tr>

                            @include('instagram.hashtags.partials.comments')    
                    @endforeach