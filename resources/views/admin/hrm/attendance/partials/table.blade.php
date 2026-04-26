<div class="table-responsive">
    <table class="table align-middle mb-0 table-hover table-centered">
        <thead class="bg-light-accent text-dark">
            <tr>
                <th>SL</th>
                <th>Employee</th>
                <th>Date</th>
                <th>Clock In</th>
                <th>Clock Out</th>
                <th>Total Time</th>
                <th>Type</th>
            </tr>
        </thead>
        <tbody>
            @php $sl = \App\HelperClass::indexNumberSerialization($attendances); @endphp
            @forelse($attendances as $attendance)
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            @if($attendance->admin->image)
                                <img src="{{ asset('storage/' . $attendance->admin->image) }}" alt="" class="avatar-xs rounded-circle">
                            @else
                                <div class="avatar-xs d-flex align-items-center justify-content-center bg-soft-primary text-primary rounded-circle">
                                    {{ substr($attendance->admin->name, 0, 1) }}
                                </div>
                            @endif
                            <span class="fw-medium">{{ $attendance->admin->name }}</span>
                        </div>
                    </td>
                    <td>{{ $attendance->date->format('d M, Y') }}</td>
                    <td><span class="badge bg-soft-success text-success">{{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('h:i A') : '--:--' }}</span></td>
                    <td><span class="badge bg-soft-danger text-danger">{{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('h:i A') : '--:--' }}</span></td>
                    <td>
                        @php
                            $hours = floor($attendance->total_minutes / 60);
                            $minutes = $attendance->total_minutes % 60;
                        @endphp
                        {{ $hours }}h {{ $minutes }}m
                    </td>
                    <td>
                        @if($attendance->is_manual)
                            <span class="badge bg-info">Manual</span>
                        @else
                            <span class="badge bg-primary">Auto</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No attendance records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3 no-print">
    {{ $attendances->appends(request()->all())->links() }}
</div>
