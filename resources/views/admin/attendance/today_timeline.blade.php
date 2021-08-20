@extends('layouts.master')

@section('content')
<!-- Breadcrumbs Start -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Today Attendance Timeline</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ url('attendance/today_timeline') }}">Attendance</a></li>
          <li class="breadcrumb-item active">Today Timeline</li>
        </ol>
      </div>
    </div>
  </div>
</div>
<!-- Breadcrumbs End -->

<!-- Error Message Section Start -->
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
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="far fa-calendar-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Present</span>
                        <span class="info-box-number">{{$present}}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="far fa-calendar-times"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Absent</span>
                        <span class="info-box-number">{{$absent}}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="far fa-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Delays</span>
                        <span class="info-box-number">{{$delays}}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="fas fa-calendar-times"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Leaves</span>
                        <span class="info-box-number">{{$leavesCount}}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <span><input id="selectDate" value="{{$today}}" class="form-control col-3" type="date" name="date"/></span>
                        <hr>
                        <div class="table-responsive">
                            <table id="today_timeline" class="table table-bordered table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Designation</th>
                                    <th>Branch</th>
                                    <th>Time in</th>
                                    <th>Time Out</th>
                                    <th>Total Time</th>
                                    <th>Delay</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($employees as $employee)
                                        <tr>
                                            <td>{{$employee['firstname']}} {{$employee['lastname']}}</td>
                                            <td>{{$employee['designation']}}</td>
                                            <td>{{isset($employee['branch']) ? $employee['branch']['name'] : ''}}</td>
                                            <td>
                                                @if(isset($employee['attendanceSummary'][0]) && $employee['attendanceSummary'][0]['first_timestamp_in'] != '')
                                                    {{isset($employee['attendanceSummary'][0]) ? Carbon\Carbon::parse($employee['attendanceSummary'][0]['first_timestamp_in'])->format('h:i a') : ''}}
                                                @endif

                                                @foreach($employeeLeave as $key=>$leave)
                                                    @if( $employee->id==$key)
                                                        <p class="text-white badge badge-warning font-weight-bold">On Leave</p>
                                                    @endif
                                                @endforeach

                                                @if(!isset($employee['attendanceSummary'][0]) && !in_array($employee->id,$employeeLeave))
                                                    <p class="text-white badge badge-danger font-weight-bold">Absent</p>
                                                @endif

                                            </td>

                                            <td>
                                                @if(isset($employee['attendanceSummary'][0]) && $employee['attendanceSummary'][0]['last_timestamp_out'] != '')
                                                    {{Carbon\Carbon::parse($employee['attendanceSummary'][0]['last_timestamp_out'])->format('h:i a')}}
                                                @else
                                                @endif
                                            </td>

                                            <td>
                                                @if(isset($employee['attendanceSummary'][0]) && $employee['attendanceSummary'][0]['last_timestamp_out'] != '')
                                                    {{isset($employee['attendanceSummary'][0]) ? gmdate('H:i', floor(number_format(($employee['attendanceSummary'][0]['total_time'] / 60), 2, '.', '') * 3600))  : ''}}
                                                @endif
                                            </td>
                                            <td>{{isset($employee['attendanceSummary'][0]) ? $employee['attendanceSummary'][0]['is_delay'] : ''}}</td>
                                            <td class="text-nowrap">
                                                <a class="btn btn-info btn-sm" href="{{route('attendance.createBreak', $employee['id'])}}/{{$today}}" title="Add Attendance"> <i class="fas fa-plus text-white"></i></a>
                                                <a class="btn btn-warning btn-sm" data-toggle="modal" data-target="#popup{{ $employee['id'] }}" title="Edit Attendance"> <i class="fas fa-pencil-alt text-white"></i></a>
                                            </td>
                                        </tr>
                                        <div class="modal fade" id="popup{{ $employee['id'] }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{route('attendance.storeAttendanceSummaryToday')}}" method='POST'>
                                                        {{ csrf_field() }}
                                                        <input type="hidden" name="employee_id" value="{{$employee['id']}}"/>

                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Add/Update Attendance</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">×</span>
                                                            </button>
                                                        </div>

                                                        <div class="modal-body">
                                                            Fill the form below to add/update Attendance of Employee "{{$employee['firstname']}} {{$employee['lastname']}}":
                                                            <div class="col-12 pt-2 pl-0 pr-0">
                                                                <div class="form-group">
                                                                    <label for="date">Today's Date</label>
                                                                    <input type="date" id="selectCurrentDate" class="form-control" name="date" value="{{isset($employee['attendanceSummary'][0]) ? $employee['attendanceSummary'][0]['date']: $today}}"/>
                                                                </div>
                                                            </div>
                                                            <div class="col-12 pl-0 pr-0">
                                                                <div class="form-group">
                                                                    <label for="date">Time In</label>
                                                                    <input type="datetime-local" class="form-control" name="time_in" value="{{isset($employee['attendanceSummary'][0]) ? date('Y-m-d\TH:i',strtotime($employee['attendanceSummary'][0]['first_timestamp_in'])) : ''}}" />
                                                                </div>
                                                            </div>
                                                            <div class="col-12 pl-0 pr-0">
                                                                <div class="form-group">
                                                                    <label for="date">Time Out</label>
                                                                    <input type="datetime-local" class="form-control" name="time_out" value="{{isset($employee['attendanceSummary'][0]) && $employee['attendanceSummary'][0]['last_timestamp_out']!=""  ? date('Y-m-d\TH:i',strtotime($employee['attendanceSummary'][0]['last_timestamp_out'])) : ''}}" />
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer justify-content-between">
                                                            <button type="button" class="btn btn-default" data-dismiss="modal"><span class="d-xs-inline d-sm-none d-md-none d-lg-none"><i class="fas fa-window-close"></i></span><span class="d-none d-xs-none d-sm-inline d-md-inline d-lg-inline"> Cancel</span></button>
                                                            <button type="submit" class="btn btn-primary create-btn" id="add-btn" ><span class="d-xs-inline d-sm-none d-md-none d-lg-none"><i class="fas fa-check-circle"></i></span><span class="d-none d-xs-none d-sm-inline d-md-inline d-lg-inline"> Present</span></button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
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

<script src="{{asset('assets/plugins/moment/moment.js')}}"></script>
<script>
    $(document).ready(function () {
        $('#today_timeline').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
        });
    });

    $("input.zoho").click(function (event) {
        if ($(this).is(":checked")) {
            $("#div_" + event.target.id).show();
        } 
        else {
            $("#div_" + event.target.id).hide();
        }
    });

    $("input.zoho").click(function (event) {
        if ($(this).is(":checked")) {
            $("#div_" + event.target.id).show();
        } else {
            $("#div_" + event.target.id).hide();
        }
    });

    $(document).ready(function () {
        $("#selectDate").change(function(e){
            var url = "{{route('today_timeline')}}/" + $(this).val();

            if (url) {
                window.location = url;
            }
            return false;
        });
    });

    $(document).ready(function () {
        $("#selectCurrentDate").change(function(e){
            var url = "{{route('today_timeline')}}/" + $(this).val();

            if (url) {
                window.location = url;
            }
            return false;
        });
    });
</script>
@stop