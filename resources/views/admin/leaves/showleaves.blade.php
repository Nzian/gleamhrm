@extends('layouts.master')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">My Leaves</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ url('employee_leaves') }}">Attendance</a></li>
          <li class="breadcrumb-item active">My Leaves</a></li>
        </ol>
      </div>
    </div>
  </div>
</div>
<!-- Error Message Section Start -->
@if ($errors->any())
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-danger">
                        <a href="#" class="close" data-dismiss="alert">&times;</a>
                        @foreach ($errors->all() as $error)
                          <li><strong>Error!</strong> {{ $error }}</li>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@if (Session::has('error'))
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="alert alert-danger" align="left">
                    <a href="#" class="close" data-dismiss="alert">&times;</a>
                    <strong>Error!</strong> {{Session::get('error')}}
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@if (Session::has('success'))
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="alert alert-success" align="left">
                    <a href="#" class="close" data-dismiss="alert">&times;</a>
                    <strong>Success!</strong> {{Session::get('success')}}
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<!-- Error Message Section End -->

<!-- Main Content Start -->
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="text-right">
                            <button type="button" onclick="window.location.href='{{route('leaves')}}'" class="btn btn-info btn-rounded" title="Apply for Leave"><i class="fas fa-plus"></i><span class="d-none d-xs-none d-sm-inline d-md-inline d-lg-inline"> Apply for Leave</span></button>
                        </div>
                        
                        <hr>
                        <div class="table-responsive">
                            <table id="my_leaves" class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Leave Type</th>
                                        <th>Date From</th>
                                        <th>Date To</th>
                                        <th>Subject</th>
                                        <th>Status</th>
                                        @if(Auth::user()->id == 1)
                                            <th>Actions</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($leaves as $leave)
                                        <tr>
                                            <td>{{$leave->leaveType->name}}</td>
                                            <td>{{Carbon\Carbon::parse($leave->datefrom)->format('Y-m-d')}}</td>
                                            <td>{{Carbon\Carbon::parse($leave->dateto)->format('Y-m-d')}}</td>
                                            <td>{{$leave->subject}}</td>
                                            <td>
                                                @if($leave->status == 'pending') 
                                                    <div class="text-white badge badge-secondary font-weight-bold">Pending</div> 
                                                @endif
                                                @if($leave->status == 'Approved') 
                                                    <div class="text-white badge badge-success font-weight-bold">Approved</div> 
                                                @endif
                                                @if($leave->status == 'Declined') 
                                                    <div class="text-white badge badge-danger font-weight-bold">Declined</div> 
                                                @endif
                                            </td>
                                            <td class="row">
                                                <a class="btn btn-info btn-sm ml-1" href="{{route('leave.show',['id'=>$leave->id])}}" title="Show Leave"> <i class="fas fa-eye text-white"></i></a>
                                                @if(strtolower($leave->status) == 'pending' || $leave->status == '')
                                                    <a class="btn btn-warning btn-sm ml-1" href="{{route('leave.edit',['id'=>$leave->id])}}" title="Edit Leave"> <i class="fas fa-pencil-alt text-white"></i></a>
                                                    <form action="{{ route('leave.destroy' , $leave->employee_id) }}" method="post">
                                                        {{ csrf_field() }}
                                                        <button class="btn btn-danger btn-sm ml-1" type="submit" title="Delete Leave"><i class="fas fa-trash-alt text-white"></i></button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Main Content End -->

<script>
    $(".update_status").on('change', function (event) {
        if ($(this).val() !== '') {
            location.href = "{{url('/')}}/leave/updateStatus/" + $(this).attr('id') + '/' + $(this).val();
        }
    });

    $(document).ready(function () {
        $('#my_leaves').DataTable({
          "paging": true,
          "lengthChange": true,
          "searching": true,
          "ordering": true,
          "info": true,
          "autoWidth": false,
          "responsive": true,
        });
    });
</script>
@stop