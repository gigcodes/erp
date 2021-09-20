

                    
                        @foreach ($magentoSettings as $magentoSetting)
                            <tr>
                                <td>{{ $magentoSetting->id }}</td>

                                @if($magentoSetting->scope === 'default')

                                        <td>{{ $magentoSetting->website->website }}</td>
                                        <td>-</td>
                                        <td>-</td>

                                @elseif($magentoSetting->scope === 'websites')
                                
                                        <td>{{ $magentoSetting->store &&  $magentoSetting->store->website &&  $magentoSetting->store->website->storeWebsite ? $magentoSetting->store->website->storeWebsite->website : '-' }}</td>
                                        <td>{{ $magentoSetting->store->website->name }}</td>
                                        <td>-</td>
                                        
                                @else
                                        <td>{{ $magentoSetting->storeview && $magentoSetting->storeview->websiteStore && $magentoSetting->storeview->websiteStore->website && $magentoSetting->storeview->websiteStore->website->storeWebsite ? $magentoSetting->storeview->websiteStore->website->storeWebsite->website : '-' }}</td>
                                        <td>{{ $magentoSetting->storeview && $magentoSetting->storeview->websiteStore ? $magentoSetting->storeview->websiteStore->name : '-' }}</td>
                                        <td>{{ $magentoSetting->storeview->code }}</td>
                                @endif

                                <td>{{ $magentoSetting->scope }}</td>
                                <td>{{ $magentoSetting->name }}</td>
                                <td>{{ $magentoSetting->path }}</td>
                                <td>{{ $magentoSetting->value }}</td>
								<td  style="width:6% !important;">@if(isset($newValues[$magentoSetting['id']]))
										{{ $newValues[$magentoSetting['id']] }}
									@endif
								</td>
                                <td>{{ $magentoSetting->created_at }}</td>
                                <td>{{ $magentoSetting->uname }}</td>
                                <td>
                                    <button type="button" value="{{ $magentoSetting->scope }}" class="btn btn-image edit-setting" data-setting="{{ json_encode($magentoSetting) }}" ><img src="/images/edit.png"></button>
                                    <button type="button" data-id="{{ $magentoSetting->id }}" class="btn btn-image delete-setting" ><img src="/images/delete.png"></button>
                                </td>
                            </tr>
                        @endforeach
                  