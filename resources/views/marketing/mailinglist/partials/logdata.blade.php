@foreach ($logs as $log)

    <tr>
        <td>{{ $log->id }}</td>
        <td>{{ $log->service }}</td>
        <td>{{  $log->maillist_id  }}</td>
        <td>{{  $log->email  }}</td>
        <td>{{  $log->name  }}</td>
        <td>{{  $log->url  }}</td>
        <?php 
            $requests='';
            if($log->request_data!=''){
                $requests=json_decode($log->request_data);
                // foreach($requestData as $key=>$val){$val=json_encode($val);
                //     $requests.= "$key => $val"."<br/>";
                // }
            }   

        ?>
        <td><?php echo "<pre>"; print_r($requests);echo "</pre>";?></td>
        <?php 
            $requests='';
            if($log->response_data!=''){
                $requests=json_decode($log->response_data);
                // foreach($requestData as $key=>$val){$val=json_encode($val);
                //     $requests.= "$key => $val"."<br/>";
                // }
            }   

        ?>
       <td><?php  echo "<pre>"; print_r($requests);echo "</pre>";?></td>
        <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d-m-y H:i:s')  }}</td>
       
    </tr>
@endforeach