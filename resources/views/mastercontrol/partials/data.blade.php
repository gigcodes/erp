<div class="table-responsive mt-3">
        <table class="table table-bordered" id="master-table">
            <thead>
            <tr>
                <th>Columns</th>
                <th>S. No</th>
                <th>Page Name</th>
                <th>Particulars</th>
                <th>Time Spent</th>
                <th>Remarks</th>
                <th>Action / Time</th>
            </tr>
            </thead>
            <tbody>
              <tr>
                <td>Broadcasts</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table"><div class="table"></div>
                </td>
              </tr>
              <tr>
                <td>Tasks</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table"><div class="table"></div>
                </td>
              </tr>
              <tr>
                <td>Statutory Tasks</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table"><div class="table"></div>
                </td>
              </tr>
              <tr>
                <td>Orders</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table"><div class="table"></div>
                </td>
              </tr>
              <tr>
                <td>Purchases</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table"><div class="table"></div>
                </td>
              </tr>
              <tr>
                <td>Scraping</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table"><div class="table"></div>
                </td>
              </tr>
              <tr>
                <td>Reviews</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table"><div class="table"></div>
                </td>
              </tr>
               <tr>
                <td>Emails</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table"><div class="table"></div>
                </td>
              </tr>
               <tr>
                <td>Accounting</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table"><div class="table"></div>
                </td>
              </tr>
               <tr>
                <td>Suppliers</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table"><div class="table"></div>
                </td>
              </tr>
               <tr>
                <td>Vendors</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table"><div class="table"></div>
                </td>
              </tr>
               <tr>
                <td>Customer</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table"><div class="table"></div>
                </td>
              </tr>
               <tr>
                <td>Old issues</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table"><div class="table"></div>
                </td>
              </tr>
               <tr>
                <td>Excel Scrapping</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table"><div class="table"></div>
                </td>
              </tr>
              <tr>
                <td>Crop Reference Grid</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table">
                  <div class="table">
                   <table>
                   <tr>
                    <th width="32%"></th>
                    <th>Today Cropped</th>
                    <th>Last 7 Days</th>
                    <th>Total Crop Reference</th>
                    <th>Pending Products</th>
                    <th>Products With Out Category</th>
                    
                   </tr>
                   <tr>
                    <td width="32%"></td>
                    <td>{{ $cropReferenceDailyCount }}</td>
                    <td>{{ $cropReferenceWeekCount }}</td>
                    <td>{{ $cropReference }}</td>
                    <td>{{ $pendingCropReferenceProducts }}</td>
                    <td>{{ $pendingCropReferenceCategory }}</td>
                    
                   </tr>
                 </table>
                   </div> 
                </td>
              </tr>
              <tr>
                <td>Product Stats</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table"><div class="table">
                  <table class="table table-striped table-bordered">
                    <tr>
                        <th>Import</th>
                        <th>Scraping</th>
                        <th>Is being scraped</th>
                        <th>Queued for AI</th>
                        <th>Auto crop</th>
                    </tr>
                    @php
                    $count = 0;
                    @endphp
                    <tr>
                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$import]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$import] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                         </td>
                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$scrape]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$scrape] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>
                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$isBeingScraped]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$isBeingScraped] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>
                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$AI]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$AI] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>
                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$autoCrop]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$autoCrop] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Is being cropped</th>
                        <th>Crop Approval</th>
                        <th>Crop Sequencing</th>
                        <th>Is being sequenced</th>
                        <th>Image Enhancement</th>
                    </tr>
                    <tr>
                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$isBeingCropped]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$isBeingCropped] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>
                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$cropApproval]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$cropApproval] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>

                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$cropSequencing]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$cropSequencing] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>

                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$isBeingSequenced]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$isBeingSequenced] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>
                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$imageEnhancement]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$imageEnhancement] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Is being enhanced</th>
                        <th>Manual Attribute</th>
                        <th>Final Approval</th>
                        <th>Queued for Magento</th>
                        <th>In Magento</th>
                    </tr>
                    <tr>
                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$isBeingEnhanced]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$isBeingEnhanced] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>
                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$manualAttribute]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$manualAttribute] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>
                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$finalApproval]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$finalApproval] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>
                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$pushToMagento]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$pushToMagento] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>
                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$inMagento]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$inMagento] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Unable to scrape</th>
                        <th>Unable to scrape images</th>
                        <th>Crop Rejected</th>
                        <th>Crop Skipped</th>
                    </tr>
                    <tr>
                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$unableToScrape]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$unableToScrape] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>
                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$unableToScrapeImages]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$unableToScrapeImages] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>

                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$cropRejected]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$cropRejected] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>
                        <td>@if(isset($productStats[\App\Helpers\StatusHelper::$cropSkipped]))
                            {{ (int) $productStats[\App\Helpers\StatusHelper::$cropSkipped] }}
                            @else
                            0
                            @php
                            $count++;
                            @endphp
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2">&nbsp;</th>
                        <th>Others</th>
                        <th>Scraped Total</th>
                        <th>Total</th>
                    </tr>
                    <tr>
                        <td colspan="2">&nbsp;</td>
                        <td style="background-color: #eee;"><strong style="font-size: 1.5em; text-align: center;">{{ $count }}</strong></td>
                        <td style="background-color: #eee;"><strong style="font-size: 1.5em; text-align: center;">{{ isset($resultScrapedProductsInStock[0]->ttl) ? (int) $resultScrapedProductsInStock[0]->ttl : '-' }}</strong></td>
                        <td style="background-color: #eee;"><strong style="font-size: 1.5em; text-align: center;">{{ (int) array_sum($productStats) }}</strong></td>
                    </tr>
                </table>
                </div>
                </td>
              </tr>
              <tr>
                <td>Crop Job Errors</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table">
                  <div class="table">
                   <table>
                   <tr>
                    <th>Cron</th>
                    <th>Last Run at</th>
                    <th>Error</th>
                   </tr>
                   <?php if(!empty($cronLastErrors)){ ?>
                     <?php foreach($cronLastErrors as $cronLastError) { ?>
                       <tr>
                          <td>{{ $cronLastError->signature }}</td>
                          <td>{{ $cronLastError->start_time }}</td>
                          <td>{{ $cronLastError->last_error }}</td>
                       </tr>
                      <?php } ?>
                    <?php } ?>
                 </table>
                   </div> 
                </td>
              </tr>
              <tr>
                <td>Latest Scraper Remarks</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="7" class="sub-table">
                  <div class="table">
                   <table>
                   <tr>
                    <th>No</th>
                    <th>Scraper name</th>
                    <th>Created at</th>
                    <th>User Name</th>
                    <th>Remark</th>
                   </tr>
                   <?php if(!empty($latestRemarks)){  $i = 1;?>
                     <?php foreach($latestRemarks as $latestRemark) { ?>
                       <tr>
                          <td>{{ $i }}</td>
                          <td>{{ $latestRemark->scraper_name }}</td>
                          <td>{{ date("Y-m-d H:i",strtotime($latestRemark->created_at)) }}</td>
                          <td>{{ $latestRemark->user_name }}</td>
                          <td>{{ $latestRemark->remark }}</td>
                       </tr>
                      <?php $i++; } ?>
                    <?php } ?>
                 </table>
                   </div> 
                </td>
              </tr>
           </tbody>
        </table>
    </div>