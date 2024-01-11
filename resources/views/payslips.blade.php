<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslips</title>
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

    @foreach ($employees as $employee)
        <section style=" text-align: center; ">
            <h2 >Payslip </h2>
            <h4> Payment slip from the month of {{ $date['from'] }} to {{ $date['to'] }}</h4>
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
                <td>{{ $employee->toArray()['position']['title'] }}</td>
            </tr>
            <tr>
                <td>Hourly Rate</td>
                <td>{{ $employee->rate }}</td>
            </tr>
            <tr>
                <td>Total Hours</td>
                <td>{{ $employee->hours }}</td>
            </tr>
            <tr>
                <td>Deductions</td>
                <td>-0.00</td>
            </tr>
            <tr>
                <td><strong>Total Amount</strong></td>
                <td><strong>{{ $employee->net }}</strong></td>
            </tr>
        </table>

        <div style="
            border: none;
            border-top: 3px dotted #000;
            color: #fff;
            background-color: #fff;
            height: 1px;
            width: 100%;
            margin: 0 0px 50px;
        "></div>
    @endforeach

</body>
</html>
