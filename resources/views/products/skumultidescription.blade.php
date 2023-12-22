    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Scraped Products ( {{ count($products)}} )</h2>
        </div>
    </div>
    
    <div class="col-md-12 ">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered" id="quick-reply-list" style="table-layout: fixed;">
                <tr>
                    <th width="2%">#</th>
                    <th width="6%">Product Name</th>
                    <th width="3%">Product Brand</th>
                    <th width="3%">Website</th>
                    <th width="10%">Description</th>
                    <th width="5%"><input type="button" onclick="setProductDescription()" data-sku="{{$sku}}" class="btn btn-secondary setProductDescription" value="Update"></th>
                </tr>
                @foreach($products as $key=>$product)
                    <tr>
                        <td>{{ $product->sid }}</td>
                        <td class="Website-task visible-app">
                            {{$product->pname}}
                        </td>
                        <td class="Website-task visible-app">
                            {{$product->bname}}
                        </td>
                        <td class="Website-task visible-app">
                            {{$product->website}}
                        </td>
                        <td class="Website-task visible-app">
                             {{$product->description}}
                        </td>
                        <td class="Website-task visible-app clickable">
                            <input type="text" name="product" value="{{ $key + 1 }}" data-id="{{ $product->sid }}" class="product-checkbox mr-2"> 
                        </td>
                        
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="checkProduct" tabindex="-1" role="dialog" aria-labelledby="checkProductLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="checkProductLabel">Record Check Result</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="modal-body-content">
          <!-- Content will go here -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
<script>
    function setProductDescription() {
        // Get the value of data-sku
        var sku = $(".setProductDescription").data("sku");

        $.ajax({
        headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
        url: '{{ route("products.multidescription.skucheck")}}',
        method: "POST",
        data: { sku: sku },
        success: function (data) {
            // Display result in the modal
            $("#modal-body-content").html(data.result);

            // Show the modal
            $("#checkProduct").modal("show");
        },
        error: function (error) {
            console.log(error);
        }
        });
        
        var productData = [];
        $("input.product-checkbox").each(function () {
            var productId = $(this).data("id");
            var productValue = $(this).val();
            productData.push({ id: productId, value: productValue });
        });
        
        // Display the array of objects containing data-id and value
        console.log("Product sku:", sku);
        console.log("Product Data:", productData);
        
        // Add your logic to update the product description using the extracted values
    }
</script>