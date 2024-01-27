<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $employee->fullname }} - Payslip</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #111;
            color: #fff;
        }
    </style>
</head>
<body>

    <section style=" text-align: center; ">
        <h2 >Payslip </h2>
        <h4> Payment slip from the month of {{ $dates['from']->format('F d, Y') }} to {{ $dates['to']->format('F d, Y') }}</h4>
    </section>

    <table>
        <tr>
            <th>Item</th>
            <th>Amount</th>
        </tr>
        <tr>
            <td>Employee ID</td>
            <td>{{ $employee->code }}</td>
        </tr>
        <tr>
            <td>Employee Name</td>
            <td>{{ $employee->user->fullname }}</td>
        </tr>
        <tr>
            <td>Position</td>
            <td>{{ @$employee->toArray()['position']['title'] }}</td>
        </tr>
        <tr>
            <td>Hourly Rate</td>
            <td>{{ $employee->rate }}</td>
        </tr>
        <tr>
            <td>Total Hours</td>
            <td>{{ $employee->hours }}</td>
        </tr>
        {{-- <tr>
            <td>Total Overtime Hours</td>
            <td>{{ $employee->overtime }}</td>
        </tr> --}}
        <tr>
            <td>Deductions</td>
            <td>-0.00</td>
        </tr>
        <tr>
            <td>Leaves</td>
            <td>
                <ul>
                    @foreach ( $employee->leaves as $leave )
                        <li>{{ $leave->start->format('M d, Y') }} to {{ $leave->end->format('M d, Y') }}</li>
                    @endforeach
                </ul>
            </td>
        </tr>
        <tr>
            <td><strong>Total Amount</strong></td>
            <td><strong>{{ $employee->net }}</strong></td>
        </tr>
    </table>

</body>
</html>
