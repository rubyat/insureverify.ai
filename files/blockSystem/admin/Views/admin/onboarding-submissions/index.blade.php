@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">{{ __('Onboarding Submissions') }}</h1>
        <div class="card mb-4">
            <div class="card-body">
                <form method="get" class="form-inline mb-3">
                    <input type="text" name="s" value="{{ request('s') }}" class="form-control mr-2" placeholder="Search by name or email">
                    <button class="btn btn-primary" type="submit">Search</button>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Phone') }}</th>
                                <th>{{ __('Submitted At') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($rows as $row)
                            <tr>
                                <td>{{ $row->full_name }}</td>
                                <td>{{ $row->email_address }}</td>
                                <td>{{ $row->phone_number }}</td>
                                <td>{{ $row->submitted_at ?? $row->created_at }}</td>
                                <td>
                                    <a href="{{ route('template.admin.onboarding_submissions.detail', $row->id) }}" class="btn btn-sm btn-info">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5">No submissions found.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $rows->links() }}
            </div>
        </div>
    </div>
@endsection
