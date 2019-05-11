@extends('layouts.app')

@section('large_content')
    <div class="row">
        <div class="col-md-12">
            <h1 class="text-center" style="background: #CCCCCC;padding: 20px">Cold Leads</h1>
        </div>
        <div class="col-md-12">
            <table class="table table-striped">
                <tr>
                    <th>S.N</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Platform</th>
                    <th>Platform ID</th>
                    <th>Rating</th>
                    <th>Bio</th>
                    <th>External URLS</th>
                    <th>Add Because of</th>
                    <th>Actions</th>
                </tr>
                @foreach($leads as $key=>$lead)
                    <tr>
                        <td style="vertical-align: top">
                            {{ $key+1 }}
                        </td>
                        <td style="vertical-align: top">
                            <img src="{{ $lead->image }}" alt="IMAGE" style="width: 150px; border-radius: 50%" class="img img-thumbnai">
                        </td>
                        <td>{{ $lead->name }}</td>
                        <td style="vertical-align: top">
                            <a class="show-overview" data-username="{{$lead->username}}" data-uid="{{$lead->platform_id}}">{{ '@' . $lead->username }}</a>
                        </td>
                        <td>{{ $lead->platform }}</td>
                        <td>{{ $lead->platform_id ?? 'N/A' }}</td>
                        <td style="vertical-align: top">
                            {{ $lead->rating }}
                        </td>
                        <td>
                            {{ $lead->bio }}
                        </td>
                        <td>
                            @if ($lead->platform == 'instagram')
                                <a href="https://instagram.com/{{ $lead->username }}">View IG Profile</a>
                            @endif
                        </td>
                        <td>
                            {{ $lead->because_of ?? 'N/A' }}
                        </td>
                        <td style="width: 100px">
                            <button style="display: inline" class="btn btn-sm btn-success add add-to-customers username-{{$lead->username}}" data-type="customer" data-username="{{$lead->username}}" data-id="{{$lead->platform_id}}" data-name="{{$lead->name ?? $lead->username}}" data-bio="{{$lead->bio ?? ''}}" data-imageurl="{{$lead->image}}" class="btn btn-sm btn-success" title="Add to Customers">
                                <i class="fa fa-plus"></i>
                            </button>
                            <form style="display: inline" method="post" action="{{ action('ColdLeadsController@destroy', $lead->id) }}">
                                @csrf
                                @method('DELETE')
                                <button style="display: inline" class="btn btn-danger btn-sm">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="11" class=text-center>
                            @foreach($lead->products as $product)
                                <button title="Reply via Instagram" type="button" class="btn btn-primary text-center" data-toggle="modal" data-target="#instagram-{{$product->id}}-{{$key}}">
                                    <img src="{{$product->imageurl}}" style="width: 200px;">
                                    <br>
                                    <i class="fa fa-comment"></i>
                                </button>

                                <!-- The Modal -->
                                <div class="modal" id="instagram-{{$product->id}}-{{$key}}">
                                    <div class="modal-dialog">
                                        <div class="modal-content">

                                            <!-- Modal Header -->
                                            <div class="modal-header">
                                                <h4 class="modal-title">Reply To Comment On Post</h4>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>

                                            <!-- Modal body -->
                                            <div class="modal-body">
                                                <form method="post" style="display: inline" action="{{ action('ReviewController@sendDm') }}">
                                                    @csrf
                                                    <div class="form-group">
                                                        <div class="text-center">
                                                            <h3>{{ $product->name }}</h3>
                                                            <h5>{{ $product->sku }}</h5>
                                                            <img src="{{$product->imageurl}}" style="width: 400px;">
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="username" value="{{$lead->username}}">
                                                    <input type="hidden" name="product_id" value="{{$product->id}}">
                                                    <div class="form-group">
                                                        <label for="id">Username</label>
                                                        <select name="id" id="id" class="form-control">
                                                            <option value="0">Select Username</option>
                                                            @foreach($accounts as $account)
                                                                @if ($account->platform == 'instagram')
                                                                    <option value="{{$account->id}}">{{ $account->last_name }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="message">Message</label>
                                                        <textarea style="height: 300px;" name="message" id="message" placeholder="Type message..." class="form-control">Looks like you're interested in {{ $product->brands->name }} products. Here is the one you might like, and many more at sololuxury.com
Product Name: {{$product->name}}
Price: Rs. {{$product->price}}</textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <button class="btn btn-success">
                                                            <i class="da fa-reply"></i> Send DM
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>

                                            <!-- Modal footer -->
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
        <div class="card-modal">
            <div id="content"></div>
            <div class="text-center" id="loading">
                <h3 class="text-center">Loading Customer Profile...</h3>
                <img style="width: 100px;" src="{{ asset('images/loading_new.gif') }}" />
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>

        .card-modal {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            width: 600px;
            background-image: linear-gradient(180deg,#21c8f6,#637bff);
            border-radius: 20px;
        }
        .card {
            box-shadow: 0px 0px 16px -8px rgba(0,0,0,1);
            border-radius:20px;
            display: flex;
        }

        .card-left {
            background-image: linear-gradient(180deg,#21c8f6,#637bff);
            padding: 20px;
            border-radius: 20px 21px 0px 0px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
        }

        .card-right {
            padding: 0 20px;
            text-align: justify;
            display:flex;
            flex-direction: column;
        }

        .card-meta {
            display:flex;
            padding-bottom: 20px;
            font-size: 12px;
            font-weight: bold;
            flex-direction: row;
        }

        a {
            color: inherit;
            text-decoration: none;
            font-weight: bold;
        }

        .card-left img {
            margin: 20px 0;
            border-radius: 100%;
        }
        .card-left span {
            text-align: center;
            font-size: 12px;
        }

        .card-link {
            background: rgba(0,0,0,0.3);
            padding: 10px;
            border-radius: 25px;
            font-size: 12px;
        }

        .show-overview {
            color: #2ab27b !important;
            font-weight: bolder;
            font-size: 14px;
            cursor: pointer;
        }
    </style>
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
@endsection

@section('scripts')
    <script>
        var nextPage = null;
        $(document).ready(function() {
            $(document).mousemove(function(e) {
                window.x = e.pageX;
                window.y = e.pageY;
            });

            $('.add').click(function() {
                let nme = $(this).attr('data-name');
                let username = $(this).attr('data-username');
                let image = $(this).attr('data-imageurl');
                let bio = $(this).attr('data-bio');
                let type = $(this).attr('data-type');
                let id = $(this).attr('data-id');

                let rating = prompt("Give this user a rating. 1 to 10");
                let self = this;

                $.ajax({
                    url: "{{ action('InstagramProfileController@add') }}",
                    type: 'post',
                    data: {
                        name: nme,
                        username: username,
                        image: image,
                        bio: bio,
                        type: type,
                        _token: "{{csrf_token()}}",
                        id: id,
                        rating: rating
                    },
                    success: function() {
                        alert("Added to " + type + " database successfully!");
                        $(self).attr('disabled', 'true');
                        $(self).html('<i class="fa fa-check"></i>');
                    }
                });
            });

            $(document).mouseup(function(e)
            {
                var container = $(".card-modal");
                if (!container.is(e.target) && container.has(e.target).length === 0)
                {
                    container.hide();
                }
            });

            $(document).on('click', '.show-overview', function() {
                let username = $(this).attr('data-username');
                $("#content").html('');
                $.ajax({
                    url: '{{action('InstagramProfileController@show', '')}}/'+username,
                    success: function(response) {
                        $("#content").html('');
                        let item = response;
                        $(".username-"+item.username).attr('data-name', item.name);
                        $(".username-"+item.username).attr('data-bio', item.bio);
                        let data = '<div class="card"><div class="card-left"> <a href="https://instagram.com/'+item.username+'" class="card-link">'+item.username+'</a><img src="'+item.profile_pic_url+'" /> </div> <div class="card-right"> <h3 class="card-title">'+item.name+'</h3> <p>'+item.bio+'</p> <div class="card-meta"> <span>Fling <i class="fa fa-users"></i> '+item.following_count+'</span> &nbsp; <span>Flwrs <i class="fa fa-users"></i> '+item.followers_count+'</span> &nbsp;<span>Posts <i class="fa fa-image"></i> '+item.media+'</span></div> </div></div>';
                        $("#content").append(data);
                        $("#loading").hide();
                    }, beforeSend: function() {
                        $("#loading").show();
                        $(".card-modal").fadeIn("slow");
                    }
                });
            });
        });
    </script>
@endsection