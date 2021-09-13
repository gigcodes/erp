
                @foreach ($discountsaleprice as $d)
                   <tr>
                       <td class="small">{{ $d->id }}</td>
                       <td>{{ $d->type }} </td>
                       <td>
                         @php
                              if ($d->type=='brand')
                               {
                                $r=\App\Brand::where('id',$d->type_id)->first();
                                echo $r->name;
                               }

                               if ($d->type=='product')
                               {
                                $r=\App\Product::where('id',$d->type_id)->first();
                                echo $r->name;
                               }

                               if ($d->type=='category')
                               {
                                $r=\App\Category::where('id',$d->type_id)->first();
                                echo $r->title;
                               }

                               if ($d->type=='store_website')
                               {
                                $r=\App\StoreWebsite::where('id',$d->type_id)->first();
                                echo $r->title;
                               }
                                 
                         @endphp
                      

                       
                       </td>
                       <td>{{ $d->supplier }}</td>
                       <td>{{ date('d-m-Y',strtotime($d->start_date)) }}</td>
                       <td>{{date('d-m-Y',strtotime($d->end_date)) }}</td>
                       <td>{{ $d->amount }}</td>
                       <td>{{ $d->amount_type }}</td>
                       <td>
                         @php
                       //  $d->start_date=date('d-m-Y',strtotime($d->start_date));
                       //  $d->end_date=date('d-m-Y',strtotime($d->end_date));

                         @endphp
                       <button type="button" class="btn btn-image edit-form d-inline"  data-toggle="modal" data-target="#cashCreateModal" data-edit="{{ json_encode($d) }}"><img src="/images/edit.png" /></button>  
                       {!! Form::open(['method' => 'DELETE','url' => ['discount-sale-price', $d->id],'style'=>'display:inline']) !!}
                           <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                           {!! Form::close() !!}

                       </td>
                       
                   </tr>
               @endforeach