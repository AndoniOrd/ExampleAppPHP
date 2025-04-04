<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Import Errors Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            background-color: #f4f4f4;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }
        h1 {
            color: #333;
        }
        h2 {
            color: #555;
            margin-top: 30px;
        }
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
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .error-section {
            margin-bottom: 30px;
        }
        .summary {
            background-color: #f4f4f4;
            padding: 15px;
            margin-top: 20px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Email Contacts Import Errors Report</h1>
    </div>

    <p>The following errors occurred during your recent email contacts import:</p>

    <div class="summary">
        <p><strong>Summary:</strong></p>
        <ul>
            <li>Existing emails: {{ count($errors['existing']) }}</li>
            <li>Invalid emails: {{ count($errors['invalid']) }}</li>
            <li>Empty/incomplete rows: {{ count($errors['empty']) }}</li>
        </ul>
    </div>

    @if(count($errors['existing']) > 0)
    <div class="error-section">
        <h2>Existing Emails ({{ count($errors['existing']) }})</h2>
        <p>These emails already exist in the database and were not imported:</p>
        <table>
            <thead>
                <tr>
                    <th>Row</th>
                    <th>Email</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody>
                @foreach($errors['existing'] as $error)
                <tr>
                    <td>{{ $error['row'] }}</td>
                    <td>{{ $error['email'] }}</td>
                    <td>{{ $error['reason'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if(count($errors['invalid']) > 0)
    <div class="error-section">
        <h2>Invalid Emails ({{ count($errors['invalid']) }})</h2>
        <p>These emails have invalid formats or other issues:</p>
        <table>
            <thead>
                <tr>
                    <th>Row</th>
                    <th>Email</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody>
                @foreach($errors['invalid'] as $error)
                <tr>
                    <td>{{ $error['row'] }}</td>
                    <td>{{ $error['email'] }}</td>
                    <td>{{ $error['reason'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if(count($errors['empty']) > 0)
    <div class="error-section">
        <h2>Empty or Incomplete Data ({{ count($errors['empty']) }})</h2>
        <p>These rows had missing required fields:</p>
        <table>
            <thead>
                <tr>
                    <th>Row</th>
                    <th>Email</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody>
                @foreach($errors['empty'] as $error)
                <tr>
                    <td>{{ $error['row'] }}</td>
                    <td>{{ $error['email'] }}</td>
                    <td>{{ $error['reason'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <p>Please review and correct these issues if needed.</p>
</body>
</html>