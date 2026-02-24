
<div class="table-responsive printBody">
    @if(isset($from) && isset($to))
        @php
            $distributorName = $distributor_id ? \App\Models\Distributor::find($distributor_id)->distributor_name : 'All';
            $tsoName = $tso_id ? \App\Models\TSO::find($tso_id)->name : 'All';
        @endphp

        <div class="dates-info-head text-center" >
            <p>
                <strong>Daily Food</strong><br>
                <strong>Daily Report</strong><br>
                <b>From:</b> {{ \Carbon\Carbon::parse($from)->format('d-M-Y') }} |
                <b>To:</b> {{ \Carbon\Carbon::parse($to)->format('d-M-Y') }} |
                <b>Distributor:</b> {{ $distributorName }} |
                <b>TSO:</b> {{ $tsoName }}
            </p>
        </div>
    @endif
    <table class="table table-bordered filterTable">
        <thead>
            <tr>
                <th>SNo</th>
                <th>Dated</th>
                <th>Employee Name</th>
                <th>Sale Order No</th>
                <th>Customer</th>
                <th>Net Amount</th>
                <th>Time In</th>
                <th>Time Out</th>
                <!-- <th>Travel Time</th> -->
                <th>Time Difference</th>
                <th>Distance</th>
                <th>Time Spent</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productivity as $key => $row)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $row->dated }}</td>
                    <td>{{ $row->employee_name }}</td>
                    <td>{{ $row->sale_order_no }}</td>
                    <td>{{ $row->customer }}</td>
                    <td>{{ number_format($row->net_amount,2) }}</td>
                    <td>{{ $row->time_in ? date('H:i:s', strtotime($row->time_in)) : '-' }}</td>
                    <td>{{ $row->time_out ? date('H:i:s', strtotime($row->time_out)) : '-' }}</td>
                    <td>{{ $row->time_diff }}</td>
                    <td>{{ $row->distance }} km</td>
                    <td>{{ $row->time_spent }}</td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <th colspan="5">Total</th>
                <th>{{ number_format($totalNet,2) }}</th>
                <th colspan="4"></th>
                <th>{{ $totalTimeSpent }}</th>
            </tr>
        </tfoot>
    </table>
</div>