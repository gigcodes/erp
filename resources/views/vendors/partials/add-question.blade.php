<div id="newQuestionModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="mt-0">Add new Question</h3>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form style="padding:10px;" action="{{ route('vendor.question.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <textarea class="form-control" name="question" placeholder="Enter Question" value="{{ old('question') }}" required></textarea>

                    @if ($errors->has('question'))
                        <div class="alert alert-danger">{{$errors->first('question')}}</div>
                    @endif
                </div>

                <button type="submit" class="btn btn-secondary">Add Question</button>
            </form>

            @if(!empty($vendor_questions))
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <th>Question</th>
                        </tr>
                        <?php
                        foreach ($vendor_questions as $questions) { ?>
                            <tr>
                                <td><?php echo $questions->question; ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
<div id="vqa-histories-list" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Questions</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th width="30%">Question</th>
                                <th width="20%">Answer</th>
                            </tr>
                        </thead>
                        <tbody class="vqa-histories-list-view">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="vqa-answer-histories-list" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Answer Histories</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th width="30%">Answer</th>
                                <th width="30%">Created Date</th>
                            </tr>
                        </thead>
                        <tbody class="vqa-answer-histories-list-view">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>