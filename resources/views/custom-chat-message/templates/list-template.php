<script type="text/x-jsrender" id="template-result-block">
	{{props data}}
      <tr>
      	<td>{{:prop.created_at}}</td>
            <td class="show_chat_message" data-content="{{:prop.message}}">{{:prop.message}}</td>
      	<td>{{:prop.sender}}</td>
      	<td>{{:prop.sender_name}}</td>
      	<td><button type="button" data-message="{{>prop.message}}" class="btn btn-delete-template copy_chat_message"><i class="fa fa-clipboard" aria-hidden="true"></i></button></td>
      </tr>
    {{/props}}  
</script>