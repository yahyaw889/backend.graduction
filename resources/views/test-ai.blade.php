<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test AI Diagnosis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">🧪 Test AI Integration (Monkeypox)</h4>
                </div>
                <div class="card-body">
                    <form action="/api/ai-diagnosis" method="POST" enctype="multipart/form-data" id="aiForm">
                        <div class="mb-3">
                            <label class="form-label">Upload Skin Image</label>
                            <input type="file" class="form-control" name="image" required accept="image/*">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Patient Age</label>
                                <input type="number" class="form-control" name="patient_age" value="25">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Patient Gender</label>
                                <select name="patient_gender" class="form-select">
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Symptoms</label>
                            <input type="text" class="form-control" name="symptoms" value="Fever, rash on face, fatigue">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Duration (Days)</label>
                            <input type="number" class="form-control" name="duration_days" value="4">
                        </div>
                        <button type="submit" class="btn btn-success w-100" id="submitBtn">Analyze with AI 🤖</button>
                    </form>
                    
                    <div id="loading" class="text-center mt-4 d-none">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 text-muted">AI is analyzing the image... Please wait.</p>
                    </div>

                    <div id="result" class="mt-4 d-none">
                        <h5 class="border-bottom pb-2">AI Result:</h5>
                        <pre id="jsonResult" class="bg-dark text-success p-3 rounded" style="max-height: 400px; overflow-y: auto;"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('aiForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = document.getElementById('submitBtn');
    const loading = document.getElementById('loading');
    const resultDiv = document.getElementById('result');
    const jsonResult = document.getElementById('jsonResult');

    submitBtn.disabled = true;
    loading.classList.remove('d-none');
    resultDiv.classList.add('d-none');

    // We don't send auth token here for testing purposes if the route is public,
    // but if it's protected by sanctum, we need a workaround. Let's assume it's public for now.
    fetch('/api/ai-diagnosis', {
        method: 'POST',
        headers: {
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        loading.classList.add('d-none');
        resultDiv.classList.remove('d-none');
        jsonResult.textContent = JSON.stringify(data, null, 4);
        submitBtn.disabled = false;
    })
    .catch(error => {
        loading.classList.add('d-none');
        resultDiv.classList.remove('d-none');
        jsonResult.textContent = 'Error: ' + error.message;
        submitBtn.disabled = false;
    });
});
</script>
</body>
</html>
