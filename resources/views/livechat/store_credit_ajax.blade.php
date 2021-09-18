         @foreach ($customers_all as $c)
           <tr>
             <td>{{ $c->name }}</td>
             <td>{{ $c->email }}</td>
             <td>{{ $c->phone }}</td>
             <td>{{ $c->title }}</td>
             <td>{{ $c->credit }}</td>
             <td>{{ $c->used_credit }}</td>
             <td>{{ $c->credit - $c->used_credit }}</td>
            
             
           
           </tr>
         @endforeach
      