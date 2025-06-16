@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Audit Log Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('audit-logs.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px;">Date & Time</th>
                                    <td>{{ $auditLog->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>User</th>
                                    <td>{{ $auditLog->user ? $auditLog->user->name : 'System' }}</td>
                                </tr>
                                <tr>
                                    <th>Event</th>
                                    <td>
                                        <span class="badge bg-{{ $auditLog->event === 'created' ? 'success' : ($auditLog->event === 'updated' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($auditLog->event) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Model</th>
                                    <td>
                                        {{ class_basename($auditLog->auditable_type) }}
                                        @if($auditLog->auditable)
                                            #{{ $auditLog->auditable_id }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>IP Address</th>
                                    <td>{{ $auditLog->ip_address }}</td>
                                </tr>
                                <tr>
                                    <th>User Agent</th>
                                    <td>{{ $auditLog->user_agent }}</td>
                                </tr>
                                <tr>
                                    <th>URL</th>
                                    <td>{{ $auditLog->url }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h4>Changes</h4>
                            @if($auditLog->event === 'created')
                                <div class="alert alert-success">
                                    <h5>New Record Created</h5>
                                    <table class="table table-sm">
                                        @foreach($auditLog->new_values as $key => $value)
                                            <tr>
                                                <th>{{ ucfirst($key) }}</th>
                                                <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            @elseif($auditLog->event === 'updated')
                                <div class="alert alert-warning">
                                    <h5>Record Updated</h5>
                                    <table class="table table-sm">
                                        @foreach($auditLog->new_values as $key => $value)
                                            @if(isset($auditLog->old_values[$key]) && $auditLog->old_values[$key] !== $value)
                                                <tr>
                                                    <th>{{ ucfirst($key) }}</th>
                                                    <td>
                                                        <span class="text-danger">{{ is_array($auditLog->old_values[$key]) ? json_encode($auditLog->old_values[$key]) : $auditLog->old_values[$key] }}</span>
                                                        →
                                                        <span class="text-success">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-danger">
                                    <h5>Record Deleted</h5>
                                    <table class="table table-sm">
                                        @foreach($auditLog->old_values as $key => $value)
                                            <tr>
                                                <th>{{ ucfirst($key) }}</th>
                                                <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 