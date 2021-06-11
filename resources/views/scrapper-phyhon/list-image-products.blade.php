<div class="customer-count customer-list-{{$list->id}} customer-{{$list->id}}" style="padding: 0px 10px;">   
        @php
            $oldDate = null;
            $count   = 0;
            $images = null;
            // dd( $list->store_website_id );
            $webStore = \App\WebsiteStore::where('website_id',$list->id)->first();
            if( $webStore ){
                $website_store_views = \App\WebsiteStoreView::where('website_store_id',$webStore->id)->first();
                if( $website_store_views ){
                    $images = \App\scraperImags::where('store_website',$list->store_website_id)->where('website_id',$website_store_views->code)->get()->toArray();
                }
            }

            // dump($list);
            // dump($webStore);
            // dump($website_store_views);
            // dd($images);

        @endphp
        @foreach($images as $image)
                {{-- @foreach($imageM->scrapperImage->toArray() as $image) --}}

                <?php
                    if ( date( 'Y-m-d' ,strtotime($image['created_at'])) !== $oldDate ) { 
                        $count = 0; 
                        $oldDate = date( 'Y-m-d' ,strtotime($image['created_at']));
                    ?>
                        <div class="row">
                            <div class="col-md-12">
                                <br>
                                <h5 class="product-attach-date" style="margin: 5px 0px;">{{$image['created_at']}} || Number Of Images:{{count($images)}}</h5> 

                                <hr style="margin: 5px 0px;">
                            </div>
                        </div> 

                    <?php } ?>
                    
                @if ($image['img_name'] )
                    @php
                    if($count == 6){
                        $count = 0;
                    }
                    @endphp
                        @if($count == 0)
                            <div class="row parent-row">
                        @endif
                        <div class="col-md-2 col-xs-4 text-center product-list-card mb-4 " style="padding:0px 5px;margin-bottom:2px !important;">
                            <div style="border: 1px solid #bfc0bf;padding:0px 5px;">
                                <div data-interval="false" id="carousel_{{ $image['id'] }}" class="carousel slide" data-ride="carousel">
                                    <a href="#" data-toggle="tooltip" data-html="true" data-placement="top" >
                                        <div class="carousel-inner maincarousel">
                                            <div class="item" style="display: block;"> <img src="{{ urldecode(asset( 'scrappersImages/'.$image['img_name']))}}" style="height: 150px; width: 150px;display: block;margin-left: auto;margin-right: auto;"> </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="row pl-4 pr-4" style="padding: 0px; margin-bottom: 8px;">

                                </div>
                            </div>
                        </div>
                    @php
                    if( $count == 0 || $count == 6){
                        echo '</div>';
                    }
                    @endphp
                @endif
            @php $count++;  @endphp
        {{-- @endforeach --}}
        @endforeach
        <br>
</div>