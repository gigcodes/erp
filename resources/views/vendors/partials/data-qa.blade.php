@foreach ($VendorQuestionAnswer as $vendor)
    <!-- @if(!empty($dynamicColumnsToShowVendorsfc))
        <tr>
            @if (!in_array('ID', $dynamicColumnsToShowVendorsfc))
                <td>{{ $vendor->name }}</td>
            @endif

            @if (!in_array('Categgory', $dynamicColumnsToShowVendorsfc))
                <td>@if(!empty($vendor->category->title)) {{ $vendor->category->title }} @endif</td>
            @endif
            
            @if($vendor_questions)
                @foreach($vendor_questions as $question_data)
                    @if (!in_array($question_data->id, $dynamicColumnsToShowVendorsfc))
                        <td>
                            <div class=" mb-1 p-0 d-flex pt-2 mt-1">
                                <input style="margin-top: 0px;width:80% !important;" type="text" class="form-control " name="message" placeholder="Answer" id="answer_{{ $vendor->id }}_{{ $question_data->id }}" data-vendorid="{{ $vendor->id }}" data-question_id="{{ $question_data->id }}">
                                <div style="margin-top: 0px;" class="d-flex p-0">
                                    <button class="btn pr-0 btn-xs btn-image " onclick="saveAnswer({{ $vendor->id }}, {{ $question_data->id }})"><img src="/images/filled-sent.png"></button>
                                    <button type="button" data-vendorid="{{ $vendor->id }}" data-question_id="{{ $question_data->id }}" class="btn btn-image answer-history-show p-0 ml-2" title="Answer Histories"><i class="fa fa-info-circle"></i></button>
                                </div>
                            </div>
                        </td>
                    @endif
                @endforeach
            @endif
        </tr>
    @else -->
        <tr>
            <td>{{ $vendor->name }}</td>
            <td>@if(!empty($vendor->category->title)) {{ $vendor->category->title }} @endif</td>
            @if($vendor_questions)
                @foreach($vendor_questions as $question_data)
                    <td>
                        <div class=" mb-1 p-0 d-flex pt-2 mt-1">
                            <input style="margin-top: 0px;width:80% !important;" type="text" class="form-control " name="answer" placeholder="Answer" id="answer_{{ $vendor->id }}_{{ $question_data->id }}" data-vendorid="{{ $vendor->id }}" data-question_id="{{ $question_data->id }}">
                            <div style="margin-top: 0px;" class="d-flex p-0">
                                <button class="btn pr-0 btn-xs btn-image " onclick="saveAnswer({{ $vendor->id }}, {{ $question_data->id }})"><img src="/images/filled-sent.png"></button>
                                <button type="button" data-vendorid="{{ $vendor->id }}" data-question_id="{{ $question_data->id }}" class="btn btn-image answer-history-show p-0 ml-2" title="Answer Histories"><i class="fa fa-info-circle"></i></button>
                            </div>
                        </div>
                    </td>
                @endforeach
            @endif
        </tr>
    <!-- @endif -->
@endforeach