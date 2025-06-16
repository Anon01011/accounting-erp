@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Audit Logs</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('audit-logs.index') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="date_from">Date From</label>
                                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="date_to">Date To</label>
                                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="event">Event Type</label>
                                    <select class="form-control" id="event" name="event">
                                        <option value="">All Events</option>
                                        <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Created</option>
                                        <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Updated</option>
                                        <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="search">Search</label>
                                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Search in changes...">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('audit-logs.index') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>User</th>
                                    <th>Event</th>
                                    <th>Model</th>
                                    <th>Changes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($auditLogs as $log)
                                    <tr>
                                        <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                        <td>{{ $log->user ? $log->user->name : 'System' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $log->event === 'created' ? 'success' : ($log->event === 'updated' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($log->event) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ class_basename($log->auditable_type) }}
                                            @if($log->auditable)
                                                #{{ $log->auditable_id }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($log->event === 'created')
                                                <span class="text-success">Created new record</span>
                                            @elseif($log->event === 'updated')
                                                @foreach($log->new_values as $key => $value)
                                                    @if(isset($log->old_values[$key]) && $log->old_values[$key] !== $value)
                                                        <div>
                                                            <strong>{{ ucfirst($key) }}:</strong>
                                                            <span class="text-danger">{{ $log->old_values[$key] }}</span>
                                                            →
                                                            <span class="text-success">{{ $value }}</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @else
                                                <span class="text-danger">Record deleted</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('audit-logs.show', $log) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View Details
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No audit logs found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $auditLogs->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 