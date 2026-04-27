@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h4 class="mb-3">Add Bank Account</h4>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('bank-accounts.store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name</label>
                        <input name="name" class="form-control" required placeholder="Main Business Account">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Institution</label>
                        <input name="institution" class="form-control" placeholder="ANZ / CBA / Westpac / NAB">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Currency</label>
                        <input name="currency" class="form-control" value="AUD">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Account last 4</label>
                        <input name="account_number_last4" class="form-control" maxlength="8">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">BSB</label>
                        <input name="bsb" class="form-control" maxlength="16">
                    </div>
                </div>

                <button class="btn btn-primary">Save</button>
                <a href="{{ route('bank-accounts.index') }}" class="btn btn-light">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
