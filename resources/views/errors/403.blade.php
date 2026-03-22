<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Forbidden</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
        }
        .error-container {
            text-align: center;
            max-width: 500px;
            padding: 40px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }
        .error-code {
            font-size: 80px;
            font-weight: 900;
            color: #dc3545;
            margin-bottom: 10px;
        }
        .error-message {
            font-size: 24px;
            font-weight: 700;
            color: #343a40;
            margin-bottom: 20px;
        }
        .error-description {
            color: #6c757d;
            margin-bottom: 30px;
        }
        .btn-back {
            background-color: #7AAACE;
            border-color: #7AAACE;
            color: white;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background-color: #5a8fb2;
            color: white;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">403</div>
        <div class="error-message">Access Denied</div>
        <p class="error-description">
            Sorry, you don't have permission to access this operation. Please contact your administrator if you think this is a mistake.
        </p>
        <a href="javascript:history.back()" class="btn-back">Go Back</a>
    </div>
</body>
</html>
