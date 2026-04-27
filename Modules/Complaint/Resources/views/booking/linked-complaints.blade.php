{{--
    Complaint module — booking complaints panel.
    Pushed to the @stack('booking-complaints') in the BookingModule booking details view.
    Shows all complaint-category tickets linked to this booking.
--}}
@if (in_array('complaint', user_modules()) && isset($bookingDetails) && !empty($bookingDetails->id))
    @php
        $bookingComplaints = \App\Models\Ticket::forBooking($bookingDetails->id)
            ->latest()
            ->get();
    @endphp

    <div class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="f-16 f-w-600 mb-0">
                {{ __('complaint::modules.complaint') }}
                @if ($bookingComplaints->count() > 0)
                    <span class="badge badge-warning ml-1">{{ $bookingComplaints->count() }}</span>
                @endif
            </h5>
            @if (user()->permission('manage_complaints') !== 'none')
                <a href="{{ route('complaint.create') }}?booking_id={{ $bookingDetails->id }}"
                   class="btn btn-primary btn-sm f-12">
                    + {{ __('complaint::app.complaint.addComplaint') }}
                </a>
            @endif
        </div>

        @if ($bookingComplaints->isEmpty())
            <p class="text-muted f-13">{{ __('messages.noRecordFound') }}</p>
        @else
            <div class="table-responsive">
                <table class="table table-bordered f-13">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('app.subject') }}</th>
                            <th>{{ __('app.status') }}</th>
                            <th>{{ __('complaint::modules.resolutionType') }}</th>
                            <th>{{ __('app.createdOn') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bookingComplaints as $ct)
                            <tr>
                                <td>{{ $ct->id }}</td>
                                <td>
                                    <a href="{{ route('tickets.show', $ct->id) }}">
                                        {{ $ct->subject }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $ct->status === 'open' ? 'danger' : ($ct->status === 'resolved' ? 'success' : 'secondary') }}">
                                        {{ ucfirst($ct->status) }}
                                    </span>
                                </td>
                                <td>{{ $ct->resolution_type ? ucfirst(str_replace('_', ' ', $ct->resolution_type)) : '—' }}</td>
                                <td>{{ $ct->created_on }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endif
