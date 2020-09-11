<div class="modal" role="dialog" id="editModal" >
    <div class="edit-drafted" role="document">
        <form action="" method="POST" enctype="multipart/form-data" id="formDraftedProduct" data-id="{{ $product->id }}">
            @csrf

            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Brand:</strong>
                        <input type="text" name="brand " value="{{ $product->brand }}" class="form-control" placeholder="Brand">
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Category:</strong>
                        <input type="text" name="category" value="{{ $product->category }}" class="form-control" placeholder="Category">
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Short description:</strong>
                        <input type="text" name="short_description" value="{{ $product->short_description }}" class="form-control" placeholder="Short description">
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Price:</strong>
                        <input type="text" name="price " value="{{ $product->price }}" class="form-control" placeholder="price">
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Status:</strong>
                        <input type="text" name="status " value="{{ $product->status }}" class="form-control" placeholder="Status">
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>


        </form>
    </div>
</div>
<style>
    .edit-drafted {
        background-color: white;
        padding:20px;
        max-width:500px;
        margin:1.75rem auto
    }
</style>