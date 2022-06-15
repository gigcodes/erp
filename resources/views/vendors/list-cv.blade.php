 @extends('layouts.app')

@section('title', 'Vendor Info')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
@endsection

@section('large_content')

    
    @include('partials.flash_messages')

    <div class="row">
      <div class="col-lg-12 margin-tb mb-3">
        <h2 class="page-heading">Vendor CV </h2>
        <div class="pull-left ">
          <form class="form-inline" action="{{ route('vendor.cv.search') }}" method="GET">
            <div class="form-group  my-3">
              <input name="vendoe_id" type="text" class="form-control"
                     value="{{ old('vendoe_id') ?? '' }}"
                     placeholder="Vendor Id">
            </div> &nbsp;&nbsp;&nbsp;&nbsp;
            <div class="form-group ">
              <input name="first_name" type="text" class="form-control"
                     value="{{ old('first_name') ?? '' }}"
                     placeholder="First Name">
            </div>&nbsp;&nbsp;&nbsp;&nbsp;
            <div class="form-group ">
              <input name="second_name" type="text" class="form-control"
                     value="{{ old('second_name') ?? '' }}"
                     placeholder="Second Name">
            </div>&nbsp;&nbsp;&nbsp;&nbsp;
            <div class="form-group ">
              <input name="email" type="text" class="form-control"
                     value="{{ old('email') ?? '' }}"
                     placeholder="Email">
            </div>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <div class="form-group ">
              <input name="mobile" type="text" class="form-control"
                     value="{{ old('mobile') ?? '' }}"
                     placeholder="Mobile">
            </div>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <div class="form-group ">
              <input name="expected_salary_in_usd" type="text" class="form-control"
                     value="{{ old('expected_salary_in_usd') ?? '' }}"
                     placeholder="Expected Salary">
            </div>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
          </form>
        </div>
      </div>
    </div>
    <div class="table-responsive mt-3">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th width="5%">ID</th>
            <th width="5%">Vendor ID</th>
            <th width="5%">Mr/Miss</th>
            <th width="10%">First Name</th>
            <th width="10%">Second Name</th>
            <th width="10%">Email</th>
            <th width="10%">Mobile</th>
            <th width="10%">Salary</th>
            <th width="10%">Expected Salary</th>
            <th width="10%">Work Hour</th>
            <th width="10%">Time Zone</th>
            <th width="10%">Start Day</th>
            <th width="10%">End Day</th>
            <th width="10%">Career Objective</th>
            <th width="10%">Work Experience</th>
          </tr>
        </thead>

        <tbody>
          @foreach ($resumes as $resume)
            <tr>
                <td>{{ $resume->id }}</td>
                <td>{{$resume->vendor_id}}</td>
                <td>{{$resume->pre_name}}</td>
                <td>{{$resume->first_name}}</td>
                <td>{{$resume->second_name}}</td>
                <td>{{$resume->email}}</td>
                <td>{{$resume->mobile }}</td>
                <td>{{$resume->salary_in_usd}}</td>
                <td>{{$resume->expected_salary_in_usd}}</td>
                <td>{{$resume->preferred_working_hours }}</td>
                <td>{{$resume->time_zone}}</td>
                <td>{{$resume->start_day}}</td>
                <td>{{$resume->end_day}}</td>
                <td class="expand-row table-hover-cell" style="word-break: break-all;">
                <span class="td-mini-container">
                  {{ strlen($resume->career_objective) > 10 ? substr($resume->career_objective, 0, 10) : $resume->career_objective }}
                </span>

                    <span class="td-full-container hidden">
                  {{ $resume->career_objective }}
                </span>
                </td>
                <td><a href="#" class="workExpariense">See</a></td>
              
              {{-- <td>
                <div class="d-flex">
                  <button type="button" class="btn btn-image edit-vendor" data-toggle="modal" data-target="#paymentShowModal" data-payment="{{ json_encode($payment) }}" title="View Payment Detail" data-currency="{{ $currencies[$payment->currency]??'N/A' }}"><img src="/images/view.png" /></button>
                    <button type="button" class="btn btn-image edit-vendor" data-toggle="modal" data-target="#paymentFormModal" data-payment="{{ json_encode($payment) }}" title="Edit Payment Detail"><img src="/images/edit.png" /></button>
                  {!! Form::open(['method' => 'DELETE','route' => ['vendors.payments.destroy', $vendor->id,$payment->id],'style'=>'display:inline']) !!}
                    <button type="submit" class="btn btn-image" title="Delete Payment detail"><img src="/images/delete.png" /></button>
                  {!! Form::close() !!}
                </div>
              </td> --}}
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {!! $resumes->appends(Request::except('page'))->links() !!}

    

    <div id="paymentShowModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Payment Detail</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
            </div>

        </div>
    </div>

@endsection

@section('scripts')
  <script type="text/javascript">
      $('#paymentShowModal').on('show.bs.modal', function (event) {
          var modal = $(this)
          var button = $(event.relatedTarget)
          var payment = button.data('payment')
          var status = payment.status ? 'Paid' : 'Pending';
          var currency = button.data('currency');
          var html = '<div class="row">' +
              '<div class="col-12">Currency : '+currency+'</div>' +
              '<div class="col-6">Payment Date : '+payment.payment_date+'</div>' +
              '<div class="col-6">Amount : '+payment.payable_amount+'</div>' +
              '<div class="col-6">Service Provided : '+payment.service_provided+'</div>' +
              '<div class="col-6">Module : '+payment.module+'</div>' +
              '<div class="col-6">Work Hour : '+payment.work_hour+'</div>' +
              '<div class="col-6">Status: '+status+'</div>' +
              '<div class="col-6">Paid Date: '+payment.paid_date+'</div>' +
              '<div class="col-6">Paid Amount: '+payment.paid_amount+'</div>' +
              '<div class="col-6">Description: <p>'+payment.description+'</p> </div>' +
              '</div>'
          modal.find('.modal-body').html(html);
      })

      
  </script>
@endsection
