<<<<<<< HEAD
@foreach ($customers_all as $c)
@php
 $used_credit = \App\CreditHistory::where('customer_id',$c->id)->where('type','MINUS')->sum('used_credit');
 $credit_in = \App\CreditHistory::where('customer_id',$c->id)->where('type','ADD')->sum('used_in');

 @endphp
   <tr>
     <td>{{ $c->name }}</td>
     <td>{{ $c->email }}</td>
     <td>{{ $c->phone }}</td>
     <td>{{ $c->title }}</td>
     <td>{{ date("d-m-Y",strtotime($c->created_at)) }}</td>
     <td>{{ $c->credit  + $credit_in }}</td>
     <td>{{ $used_credit }}</td>
     <td>{{ ($c->credit + $credit_in ) - $used_credit }}</td>
   </tr>
 @endforeach
=======
@foreach ($customers_all as $c) 
@php 
         $used_credit = \App\CreditHistory::where('customer_id',$c->id)->where('type','MINUS')->sum('used_credit');
         $credit_in = \App\CreditHistory::where('customer_id',$c->id)->where('type','ADD')->sum('used_in');
        
         @endphp
           <tr>
             <td>{{ $c->name }}</td>
             <td>{{ $c->email }}</td>
             <td>{{ $c->phone }}</td>
             <td>{{ $c->title }}</td>
             <td>{{ date("d-m-Y",strtotime($c->created_at)) }}</td>
             <td>{{ $c->credit  + $credit_in }}</td>
             <td>{{ $used_credit }}</td>
             <td>{{ ($c->credit + $credit_in ) - $used_credit }}</td>
             <td><a href="#" onclick="getLogs('{{ $c->id}}')"><i class="fa fa-eye"></i></a></td>
             
           
           </tr>
         @endforeach
>>>>>>> master
      