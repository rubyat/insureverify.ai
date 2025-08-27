@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">{{ __('Onboarding Submission Detail') }}</h1>
        <div class="card mb-4">
            <div class="card-body">
                <a href="{{ route('template.admin.onboarding_submissions.index') }}" class="btn btn-secondary mb-3">Back to
                    List</a>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th>Name</th>
                            <td>{{ $row->full_name }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $row->email_address }}</td>
                        </tr>
                        <tr>
                            <th>Phone</th>
                            <td>{{ $row->phone_number }}</td>
                        </tr>
                        <tr>
                            <th>Business Name</th>
                            <td>{{ $row->business_name }}</td>
                        </tr>
                        <tr>
                            <th>Vehicle Location</th>
                            <td>{{ $row->vehicle_location_city_state }}</td>
                        </tr>
                        <tr>
                            <th>Number of Vehicles</th>
                            <td>{{ $row->num_vehicles }}</td>
                        </tr>
                        <tr>
                            <th>Host Cities</th>
                            <td>{{ $row->host_cities }}</td>
                        </tr>
                        <tr>
                            <th>Vehicle Types</th>
                            <td>{{ is_array($row->vehicle_types) ? implode(', ', $row->vehicle_types) : $row->vehicle_types }}
                            </td>
                        </tr>
                        <tr>
                            <th>Other Vehicle Type</th>
                            <td>{{ $row->vehicle_types_other_text }}</td>
                        </tr>
                        <tr>
                            <th>Vehicle Ownership</th>
                            <td>{{ $row->vehicle_ownership }}</td>
                        </tr>
                        <tr>
                            <th>Listed Before</th>
                            <td>{{ $row->listed_before }}</td>
                        </tr>
                        <tr>
                            <th>Platforms Listed</th>
                            <td>{{ is_array($row->platforms_listed) ? implode(', ', $row->platforms_listed) : $row->platforms_listed }}
                            </td>
                        </tr>
                        <tr>
                            <th>Other Platform</th>
                            <td>{{ $row->platforms_listed_other_text }}</td>
                        </tr>
                        <tr>
                            <th>Number of Rentals Completed</th>
                            <td>{{ $row->num_rentals_completed }}</td>
                        </tr>
                        <tr>
                            <th>Valid Registration/Insurance</th>
                            <td>{{ $row->valid_reg_ins }}</td>
                        </tr>
                        <tr>
                            <th>FYV Insurance Interest</th>
                            <td>{{ $row->fyv_insurance_interest }}</td>
                        </tr>
                        <tr>
                            <th>Docs Ready for Verification</th>
                            <td>{{ $row->docs_ready_for_verification }}</td>
                        </tr>
                        <tr>
                            <th>Rental Management</th>
                            <td>{{ $row->rental_management }}</td>
                        </tr>
                        <tr>
                            <th>Designated Pickup Location</th>
                            <td>{{ $row->designated_pickup_location }}</td>
                        </tr>
                        <tr>
                            <th>Pickup Location Address</th>
                            <td>{{ $row->pickup_location_address }}</td>
                        </tr>
                        <tr>
                            <th>Delivery Method</th>
                            <td>{{ $row->delivery_method }}</td>
                        </tr>
                        <tr>
                            <th>Storage Service Interest</th>
                            <td>{{ $row->storage_service_interest }}</td>
                        </tr>
                        <tr>
                            <th>Vehicle Availability</th>
                            <td>{{ $row->vehicle_availability }}</td>
                        </tr>
                        <tr>
                            <th>FYV Goals</th>
                            <td>{{ is_array($row->fyv_goals) ? implode(', ', $row->fyv_goals) : $row->fyv_goals }}</td>
                        </tr>
                        <tr>
                            <th>Other Goals</th>
                            <td>{{ $row->fyv_goals_other_text }}</td>
                        </tr>
                        <tr>
                            <th>How Heard</th>
                            <td>{{ $row->how_heard }}</td>
                        </tr>
                        <tr>
                            <th>Referral Name</th>
                            <td>{{ $row->heard_referral_name }}</td>
                        </tr>
                        <tr>
                            <th>Other How Heard</th>
                            <td>{{ $row->heard_other_text }}</td>
                        </tr>
                        <tr>
                            <th>Anything Else</th>
                            <td>{{ $row->anything_else }}</td>
                        </tr>
                        <tr>
                            <th>Submitted At</th>
                            <td>{{ $row->submitted_at ?? $row->created_at }}</td>
                        </tr>
                        <tr>
                            <th>IP Address</th>
                            <td>{{ $row->ip_address }}</td>
                        </tr>
                        <tr>
                            <th>User Agent</th>
                            <td>{{ $row->user_agent }}</td>
                        </tr>
                        <tr>
                            <th>Driver License Upload</th>
                            <td>
                                @if ($row->driver_license_upload)
                                    @php $ext = strtolower(pathinfo($row->driver_license_upload, PATHINFO_EXTENSION)); @endphp
                                    @if (in_array($ext, ['jpg', 'jpeg', 'png']))
                                        <a href="{{ asset('storage/' . $row->driver_license_upload) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $row->driver_license_upload) }}"
                                                alt="Driver License"
                                                style="max-width:120px;max-height:120px;display:block;">
                                        </a>
                                    @endif
                                    <a href="{{ asset('storage/' . $row->driver_license_upload) }}"
                                        target="_blank">Download</a>
                                @else
                                    <span class="text-muted">Not provided (optional)</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Vehicle Registration Upload</th>
                            <td>
                                @if ($row->vehicle_registration_upload)
                                    @php $ext = strtolower(pathinfo($row->vehicle_registration_upload, PATHINFO_EXTENSION)); @endphp
                                    @if (in_array($ext, ['jpg', 'jpeg', 'png']))
                                        <a href="{{ asset('storage/' . $row->vehicle_registration_upload) }}"
                                            target="_blank">
                                            <img src="{{ asset('storage/' . $row->vehicle_registration_upload) }}"
                                                alt="Vehicle Registration"
                                                style="max-width:120px;max-height:120px;display:block;">
                                        </a>
                                    @endif
                                    <a href="{{ asset('storage/' . $row->vehicle_registration_upload) }}"
                                        target="_blank">Download</a>
                                @else
                                    <span class="text-muted">Not provided (optional)</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Proof of Insurance Upload</th>
                            <td>
                                @if ($row->proof_of_insurance_upload)
                                    @php $ext = strtolower(pathinfo($row->proof_of_insurance_upload, PATHINFO_EXTENSION)); @endphp
                                    @if (in_array($ext, ['jpg', 'jpeg', 'png']))
                                        <a href="{{ asset('storage/' . $row->proof_of_insurance_upload) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $row->proof_of_insurance_upload) }}"
                                                alt="Proof of Insurance"
                                                style="max-width:120px;max-height:120px;display:block;">
                                        </a>
                                    @endif
                                    <a href="{{ asset('storage/' . $row->proof_of_insurance_upload) }}"
                                        target="_blank">Download</a>
                                @else
                                    <span class="text-muted">Not provided (optional)</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
