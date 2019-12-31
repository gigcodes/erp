@foreach($products as $product)
                        <tr>

                            <td><input type="checkbox" name="issue" value="{{ $product->id }}" class="checkBox">
                                {{ $product->id }}</td>
                            <td>@if($product->product) @if (isset($product->product->product_category)) {{ $product->product->product_category->title }} @endif @endif</td> 
                            <td>@if($product->product) {{ $product->product->supplier }} @endif</td> 
                            <td>@if($product->product)  @if ($product->product->brand) {{ $product->product->brands->name }} @endif @endif</td>    
                            <td> <img src="{{ $product->media ? $product->media->getUrl() : '' }}" alt="" onmouseover="bigImg('{{ $product->media ? $product->media->getUrl() : '' }}')" style="max-width: 150px; max-height: 150px;"></td>
                            <td> <img src="{{ $product->newMedia ? $product->newMedia->getUrl() : '' }}" alt="" height="150" width="150" onmouseover="bigImg('{{ $product->newMedia ? $product->newMedia->getUrl() : '' }}')"></td>
                            <td>{{ number_format((float)str_replace('0:00:','',$product->speed), 4, '.', '') }} sec</td>
                            <td>{{ $product->updated_at->format('d-m-Y : H:i:s') }}</td>
                            <td>@if($product->product) {{ $product->product->status_id }} @endif</td>
                            <td>{!! $product->getProductIssueStatus($product->id) !!}</td>
                           
                        </tr>
                    @endforeach