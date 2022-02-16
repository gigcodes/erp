<script type="text/x-jsrender" id="template-result-block">
	{{props data}}
      <tr>
      	<td>{{:prop.created_at}}</td>
      	<td>{{:prop.message}}</td>
      	<td>{{:prop.sender}}</td>
      	<td>{{:prop.sender_name}}</td>
      	<td></td>
      </tr>
    {{/props}}  
</script>