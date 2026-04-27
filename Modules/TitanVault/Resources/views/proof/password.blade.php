<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Password Required — TitanVault Proof</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body { background: #f5f6fa; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .password-card { background: #fff; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,.1); padding: 2.5rem; max-width: 420px; width: 100%; }
    </style>
</head>
<body>

<div class="password-card">
    <div class="text-center mb-4">
        <i class="fa fa-lock fa-3x text-secondary mb-2 d-block"></i>
        <h5 class="font-weight-bold">Password Required</h5>
        <p class="text-muted f-14">This proof is password-protected. Please enter the password to continue.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <p class="mb-0">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('titan-vault.proof.password', $token) }}">
        @csrf
        <div class="form-group">
            <label for="password" class="f-14">Password</label>
            <input type="password" id="password" name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="Enter password…" autofocus required>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary btn-block">
            <i class="fa fa-unlock mr-1"></i> Access Proof
        </button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
