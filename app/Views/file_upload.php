<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Learning Materials</title>
</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-sm rounded-4">
          <div class="card-header bg-primary text-white text-center">
            <h4>Upload a File</h4>
          </div>
          <div class="card-body">
            
            
            <form action="<?= base_url('upload/save') ?>" method="post" enctype="multipart/form-data">
              <div class="mb-3">
                <label for="file" class="form-label">Choose File</label>
                <input type="file" name="userfile" id="file" class="form-control" required>
              </div>

              <div class="d-grid">
                <button type="submit" class="btn btn-success">Upload</button>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>