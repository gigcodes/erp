
<div class="col-md-12">
    <table class="table table-bordered" style="table-layout: fixed;">
        <thead>
            <tr>
                <th>Ip</th>
                <th>Website</th>
                <th>Url</th>
                <th>Brand</th>
                <th>Category</th>
                <th>Title</th>
                <th>Description</th>
                <th>Properties</th>
                <th>Size System</th>
                <th>currency</th>
                <th>Price</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody class="conent">
            @foreach($products as $product)
            <tr>
                <td class="Website-task" title="{{isset($product->ip_address) ? $product->ip_address : "-"}}">
                    {{isset($product->ip_address) ? $product->ip_address : "-"}}
                </td>
                <td class="Website-task" title="{{isset($product->website) ? $product->website : "-"}}">
                    {{isset($product->website) ? $product->website : "-"}}
                </td>
                <td class="Website-task" title="{{isset($product->url) ? $product->url : "-"}}">
                    {{isset($product->url) ? $product->url : "-"}}
                </td>
                <td class="Website-task" title="{{isset($product->brand_name) ? $product->brand_name : "-"}}">
                    {{isset($product->brand_name) ? $product->brand_name : "-"}}
                </td>
                <td class="Website-task" title="{{isset($product->category_name) ? $product->category_name : "-"}}">
                    {{isset($product->category_name) ? $product->category_name : "-"}}
                </td>
                <td class="Website-task"title="{{isset($product->title) ? $product->title : "-"}}">
                    {{isset($product->title) ? $product->title : "-"}}
                </td>
                <td class="Website-task" title="{{isset($product->description) ? $product->description : "-"}}">
                    {{isset($product->description) ? $product->description : "-"}}
                </td>
                <td>
                    {{isset($product->properties) ? $product->properties : "-"}}
                </td>
                <td>
                    {{isset($product->size_system) ? $product->size_system : "-"}}
                </td>
                <td>
                    {{isset($product->currency) ? $product->currency : "-"}}
                </td>
                <td>
                    {{isset($product->price) ? $product->price : "-"}}
                </td>
                <td>
                    {{isset($product->updated_at) ? $product->updated_at : "-"}}
                 </td>
            </tr>
        @endforeach
       </tbody>
    </table> 
</div>