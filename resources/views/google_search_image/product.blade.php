@extends('layouts.app')

@section("styles")
@endsection
<style type="text/css">
    .card {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        max-width: 300px;
        margin: auto;
        text-align: center;
        font-family: arial;
    }

    .price {
        color: grey;
        font-size: 22px;
    }

    .card button {
        border: none;
        outline: 0;
        padding: 12px;
        color: white;
        background-color: #000;
        text-align: center;
        cursor: pointer;
        width: 100%;
        font-size: 18px;
    }

    .card button:hover {
        opacity: 0.7;
    }
</style>
@section('content')
    @include('partials.flash_messages')
    <div class="row" style="padding-top: 10px;">
        <?php if(!empty($product)) { ?>
        <div class="col-md-12">
            <div class="card col-lg-6" style="margin:auto;float:none;">
                <h1><?php echo "#" . $product->id . " " . $product->name ?></h1>
                <p class="price">SKU : <a href="https://www.google.com/search?q=<?= $product->sku ?>" target="_blank"><?php echo $product->sku ?></a></p>
                <p class="price">Brand : <?php echo isset($product->brands->name) ? $product->brands->name : ""; ?></p>
                <p class="price">Description : <?php echo $product->short_description ?></p>
                <?php $brand = isset($product->brands->name) ? $product->brands->name : ""; ?>
                <p>
                    <button data-keyword="<?php echo implode(',', array_filter([$brand, $product->name, $product->sku])); ?>" class="get-images">Get Images</button>
                    <br/>
                    <br/>
                    <button data-keyword="<?php echo $product->sku; ?>" class="get-images">Get Images (by SKU only)</button>
                </p>
            </div>
        </div>
        <div class="col-md-12" style="text-align:right;">
            <button class="attach-and-continue btn btn-lg btn-success">Attach And Continue</button>
            <button class="skip-product btn btn-lg btn-danger pull-left">Skip Product</button>
        </div>
        <form method="post" id="save-images" action="{{ route('google.search.product-save') }}">
            {{ csrf_field() }}
            <input type="hidden" name="product_id" value="<?php echo $product->id; ?>">
            <div class="col-md-12 image-result-show">

            </div>
        </form>
    </div>
    <?php } else { ?>
       <?php echo "No products found"; ?>
 <?php } ?>

@endsection

@section('scripts')

    <script type="text/javascript">

        var productSearch = $(".get-images");
        productSearch.on("click", function () {
            var keyword = $(this).data("keyword");
            $.ajax({
                url: "{!! env('GOOGLE_CUSTOM_SEARCH') !!}&q=" + keyword + "&searchType=image&imgSize=large", success: function (result) {
                    // console.log(result);
                    if (result.searchInformation.totalResults != undefined && parseInt(result.searchInformation.totalResults) > 0) {
                        var i = 1;
                        $(".image-result-show").html('');
                        $.each(result.items, function (k, v) {

                            var template = '<div class="col-md-3"><div class="card" style="width: 18rem;">';
                            template += '<img title="' + v.title + '" class="card-img-top" src="' + v.link + '" alt="' + v.title + '" onclick="toggleCheckbox(' + i + ');">';
                            template += '<div class="card-body">';
                            template += '<input type="checkbox" id="checkbox-' + i + '" class="selected-image" name="images[]" value="' + v.link + '">';
                            template += '</div>';
                            template += '</div></div>';

                            $(".image-result-show").append(template);
                            i++;
                        });
                    } else {
                        alert('No images found');
                    }
                }
            });
        });

        $(".attach-and-continue").on("click", function () {
            var selectedImages = $(".selected-image:checked").length;
            if (selectedImages > 0) {
                $("#save-images").submit();
            } else {
                alert("Please Select Images from list and then proceed");
            }
        });

        $(".skip-product").on("click", function () {
            $("#save-images").submit();
        });

        function toggleCheckbox(id) {
            $('#checkbox-' + id).click();
        }

    </script>


@endsection
