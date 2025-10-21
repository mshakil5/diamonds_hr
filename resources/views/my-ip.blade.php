<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Check My IP</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      background-color: #f8f9fa;
    }
    .card {
      border-radius: 1rem;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>
  <div class="card p-4 text-center">
    <h4>Your IP Address</h4>
    <div class="input-group mt-3">
      <input type="text" class="form-control" id="ipAddress" value="{{ $clientIp }}" readonly>
      <button class="btn btn-primary" id="copyBtn">Copy</button>
    </div>
    <small class="text-muted mt-2 d-block">Click the button to copy your IP</small>
  </div>

  <script>
    document.getElementById('copyBtn').addEventListener('click', function() {
      const ipField = document.getElementById('ipAddress');
      ipField.select();
      ipField.setSelectionRange(0, 99999);
      navigator.clipboard.writeText(ipField.value)
        .then(() => {
          this.textContent = "Copied!";
          setTimeout(() => this.textContent = "Copy", 2000);
        });
    });
  </script>
</body>
</html>
