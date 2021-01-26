<script type="text/x-jsrender" id="template-result-block">
    <div class="table-responsive mt-3">
        <table class="table table-bordered">
            <thead>
              <tr>
                <th>Id</th>
                <th>Category</th>
                <th>Meta Title</th>
                <th>Meta Keyword</th>
                <th>Meta Description</th>
                <th>Created at</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
                {{props data}}
                  <tr>
                    <td><input type="checkbox" class="groups" name="groups[]" value="{{:prop.id}}">&nbsp;{{:prop.id}}</td>
                    <td>{{:prop.title}}</td>
                    <td>{{:prop.meta_title}}</td>
                    <td>{{:prop.meta_keyword}}</td>
                    <td>{{:prop.meta_description}}</td>
                    <td>{{:prop.created_at}}</td>
                    <td>
                        <button type="button" title="Push" data-id="{{>prop.id}}" class="btn btn-push">
                            <i class="fa fa-upload" aria-hidden="true"></i>
                        </button>
                        <button type="button" title="Edit" data-id="{{>prop.id}}" class="btn btn-edit-template">
                            <i class="fa fa-edit" aria-hidden="true"></i>
                        </button>
                        <button type="button" title="Delete" data-id="{{>prop.id}}" class="btn btn-delete-template">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </button>
                        <button type="button" title="Language" data-id="{{>prop.id}}" class="btn btn-translate-for-other-language">
                            <i class="fa fa-language" aria-hidden="true"></i>
                        </button>
                    </td>
                  </tr>
                {{/props}}
            </tbody>
        </table>
        {{:pagination}}
    </div>
</script> 