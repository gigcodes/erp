<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-6 d-inline form-inline">
                <input style="width: 87%" type="text" name="category_name" placeholder="Enter New Category" class="form-control mb-3 quick_category">
                <button class="btn btn-secondary quick_category_add" style="position: absolute;  margin-left: 8px;">+</button>
            </div>
            <div class="col-6 d-inline form-inline" style="padding-left: 0px;">
                <div style="float: left; width: 86%">
                    <select name="quickCategory" class="form-control mb-3 quickCategory">
                        <option value="">Select Category</option>

                        @php
                        $reply_categories = \App\ReplyCategory::select('id', 'name')->with('approval_leads')->orderby('id', 'DESC')->get();
                        @endphp

                        @foreach($reply_categories as $category)
                            <option value="{{ $category->approval_leads }}" data-id="{{$category->id}}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="float: right; width: 14%;">
                    <a class="btn btn-image delete_category"><img src="/images/delete.png"></a>
                </div>
            </div>
            <div class="col-6 d-inline form-inline">
                <input style="width: 87%" type="text" name="quick_comment" placeholder="Enter New Quick Comment" class="form-control mb-3 quick_comment">
                <button class="btn btn-secondary quick_comment_add" style="position: absolute;  margin-left: 8px;">+</button>
            </div>
            <div class="col-6 d-inline form-inline" style="padding-left: 0px;">
                <div style="float: left; width: 86%">
                    <select name="quickComment" class="form-control quickCommentEmail">
                        <option value="">Quick Reply</option>
                    </select>
                </div>
                <div style="float: right; width: 14%;">
                    <a class="btn btn-image delete_quick_comment"><img src="/images/delete.png"></a>
                </div>
            </div>
        </div>
    </div>    
</div>