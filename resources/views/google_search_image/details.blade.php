@extends('layouts.app')

@section("styles")
@endsection
<style type="text/css">
  .dis-none {
    display: none;
  }
</style>
@section('content')
  @include('partials.flash_messages')

  <div class="productGrid" id="productGrid">
      <form  method="POST" action="{{route('google.details.image')}}">
        {{ csrf_field() }}
        <input id="search-product-url" type="hidden" name="url">
      </form>

      <div class="row">
        <div class="col-md-12">
            <div class="well">
              <p><a href="javascript:;">Guess Labels</a></p>
              <p>
                <?php if(!empty($result["labels"])) { ?>
                  <?php foreach($result["labels"] as $labels) { ?>
                    <span class="label label-default"><?php echo $labels; ?></span>
                  <?php } ?>  
                <?php } ?> 
              </p>
            </div>
            <div class="well">
              <p><a href="javascript:;">Web Entities</a></p>
              <p>
                <?php if(!empty($result["entities"])) { ?>
                  <?php foreach($result["entities"] as $entities) { ?>
                    <span class="label label-default"><?php echo $entities; ?></span>
                  <?php } ?>  
                <?php } ?> 
              </p>
            </div>
        </div>
        <div class="col-md-12">
            <div class="row">
              <div class="col-md-12">
                  <div class="well">
                    <h1> Best Matching Images in Site </h1>
                  </div>
                  <?php if(!empty($result["pages"])){ ?>
                    <?php foreach($result["pages"] as $pages) { ?>
                      <div class="col-md-4" style="float:left">
                        <div class="panel panel-primary">
                          <div class="panel-body"><img src="<?php echo $result['image']; ?>" class="img-responsive" style="width:250px; height:250px;" alt="Image"></div>
                          <div class="panel-footer">
                            <a href="<?php echo $pages; ?>" target="__blank">
                              <button title="<?php echo $pages; ?>" class="btn btn-secondary">Go To <?php echo substr($pages, 0, 30) ?> ..</button>
                            </a>
                          </div>
                        </div>
                      </div>
                    <?php } ?>
                  <?php } ?>
              </div> 
              <div class="col-md-12">
                  <div class="well">
                    <h1> Best Full matching Images</h1>
                  </div>
                  <?php if(!empty($result["matching_images"])){ ?>
                    <?php foreach($result["matching_images"] as $images) { ?>
                      <div class="col-md-4" style="float:left">
                        <div class="panel panel-primary">
                          <div class="panel-body"><img src="<?php echo $images; ?>" class="img-responsive" style="width:250px; height:250px;" alt="Image"></div>
                          <div class="panel-footer">
                              <button data-href="<?php echo $images; ?>" class="btn btn-secondary btn-img-details">
                                  Get Details
                              </button>
                          </div>
                        </div>
                      </div>
                    <?php } ?>
                  <?php } ?>
              </div>
              <div class="col-md-12">
                  <div class="well">
                    <h1> Best Partial matching Images</h1>
                  </div>
                  <?php if(!empty($result["partial_matching"])){ ?>
                    <?php foreach($result["partial_matching"] as $images) { ?>
                      <div class="col-md-4" style="float:left">
                        <div class="panel panel-primary">
                          <div class="panel-body"><img src="<?php echo $images; ?>" class="img-responsive" style="width:250px; height:250px;" alt="Image"></div>
                          <div class="panel-footer">
                              <button data-href="<?php echo $images; ?>" class="btn btn-secondary btn-img-details">
                                  Get Details
                              </button>
                          </div>
                        </div>
                      </div>
                    <?php } ?>
                  <?php } ?>
              </div>
              <div class="col-md-12">
                  <div class="well">
                    <h1> Best Similar matching Images</h1>
                  </div>  
                  <?php if(!empty($result["similar_images"])){ ?>
                    <?php foreach($result["similar_images"] as $images) { ?>
                      <div class="col-md-4" style="float:left">
                        <div class="panel panel-primary">
                          <div class="panel-body"><img src="<?php echo $images; ?>" class="img-responsive" style="width:250px; height:250px;" alt="Image"></div>
                          <div class="panel-footer">
                              <button data-href="<?php echo $images; ?>" class="btn btn-secondary btn-img-details">
                                  Get Details
                              </button>
                          </div>
                        </div>
                      </div>
                    <?php } ?>
                  <?php } ?>
              </div>   
            </div>  
        </div>
      </div>
      
  </div>

@endsection

@section('scripts')
 
 <script type="text/javascript">
    
    var detailsBtn = $(".btn-img-details");
        detailsBtn.on("click", function() {
            var $this = $(this);
            $("#search-product-url").val($(this).data("href"));
            $("#search-product-url").closest("form").submit();
        });

 </script> 
  

@endsection
