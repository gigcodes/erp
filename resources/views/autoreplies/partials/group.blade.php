<div id="groupCreateModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Create Keyword Group</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <strong>Name:</strong>
                       <input type="text" name="name" id="keywordname" class="form-control">
                    </div>
                    <div class="form-group">
                        <strong>Existing Group:</strong>
                        @foreach($groupKeywords as $groupKeyword)
                       <select class="form-control" id="keywordGroup">
                           <option value="{{ $groupKeyword->id }}">{{ $groupKeyword->group_name }}</option>
                       </select>
                       @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-secondary" onclick="createGroup()">Create</button>
                </div>
            </form>
        </div>

    </div>
</div>


<div id="groupPhraseCreateModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Create Phrase Group</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <strong>Name:</strong>
                       <input type="text" name="name" id="phrasename" class="form-control">
                    </div>
                    <div class="form-group">
                        <strong>Existing Group:</strong>
                        @foreach($groupPhrases as $groupPhrase)
                       <select class="form-control" id="phraseGroup">
                           <option value="{{ $groupPhrases->id }}">{{ $groupPhrases->group_name }}</option>
                       </select>
                       @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-secondary" onclick="createGroupPhrase()">Create</button>
                </div>
            </form>
        </div>

    </div>
</div>


