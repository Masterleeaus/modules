@extends('layouts.app')
<?php $pageTitle = $pageTitle ?? 'New Bill'; ?>
@section('content')
@include('accountings::partials.nav')


<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">New Bill</h4>
        <a href="{{ route('bills.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="post" action="{{ route('bills.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Vendor</label>
                        <select name="vendor_id" class="form-select">
                            <option value="">—</option>
                            @foreach($vendors as $v)
                                <option value="{{ $v->id }}">{{ $v->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Bill #</label>
                        <input type="text" name="bill_number" class="form-control" />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Bill Date</label>
                        <input type="date" name="bill_date" class="form-control" />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Due Date</label>
                        <input type="date" name="due_date" class="form-control" />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            @foreach(['draft','approved','unpaid','paid'] as $s)
                                <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr />

                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Lines</h6>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addLine()">Add line</button>
                </div>

                <div class="table-responsive mt-2">
                    <table class="table table-sm" id="linesTable">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th style="width:140px">Account</th>
                                <th style="width:120px">Tax</th>
                                <th style="width:160px">Service Line</th>
                                <th style="width:90px">Qty</th>
                                <th style="width:120px">Unit</th>
                                <th style="width:140px">Job Ref (optional)</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input name="lines[0][description]" class="form-control form-control-sm" /></td>
                                <td>
                                    <select name="lines[0][coa_id]" class="form-select form-select-sm">
                                        <option value="">—</option>
                                        @foreach($accounts as $a)
                                            <option value="{{ $a->id }}">{{ $a->coa }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="lines[0][tax_code_id]" class="form-select form-select-sm">
                                        <option value="">—</option>
                                        @foreach($taxCodes as $t)
                                            <option value="{{ $t->id }}">{{ $t->code }} ({{ (float)$t->rate*100 }}%)</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="lines[0][service_line_id]" class="form-select form-select-sm">
                                        <option value="">—</option>
                                        @foreach($serviceLines as $s)
                                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input name="lines[0][qty]" value="1" class="form-control form-control-sm" /></td>
                                <td><input name="lines[0][unit_price]" value="0" class="form-control form-control-sm" /></td>
                                <td><input name="lines[0][job_ref]" class="form-control form-control-sm" /></td>
                                <td class="text-end"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeLine(this)">X</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="3"></textarea>
                </div>

                <div class="mt-3">
                    <button class="btn btn-primary">Save Bill</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let lineIdx = 1;
function addLine() {
  const tbody = document.querySelector('#linesTable tbody');
  const tr = document.createElement('tr');
  tr.innerHTML = `
    <td><input name="lines[${lineIdx}][description]" class="form-control form-control-sm" /></td>
    <td>
      <select name="lines[${lineIdx}][coa_id]" class="form-select form-select-sm">
        <option value="">—</option>
        @foreach($accounts as $a)
          <option value="{{ $a->id }}">{{ $a->coa }}</option>
        @endforeach
      </select>
    </td>
    <td>
      <select name="lines[${lineIdx}][tax_code_id]" class="form-select form-select-sm">
        <option value="">—</option>
        @foreach($taxCodes as $t)
          <option value="{{ $t->id }}">{{ $t->code }} ({{ (float)$t->rate*100 }}%)</option>
        @endforeach
      </select>
    </td>
    <td>
      <select name="lines[${lineIdx}][service_line_id]" class="form-select form-select-sm">
        <option value="">—</option>
        @foreach($serviceLines as $s)
          <option value="{{ $s->id }}">{{ $s->name }}</option>
        @endforeach
      </select>
    </td>
    <td><input name="lines[${lineIdx}][qty]" value="1" class="form-control form-control-sm" /></td>
    <td><input name="lines[${lineIdx}][unit_price]" value="0" class="form-control form-control-sm" /></td>
    <td><input name="lines[${lineIdx}][job_ref]" class="form-control form-control-sm" /></td>
    <td class="text-end"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeLine(this)">X</button></td>
  `;
  tbody.appendChild(tr);
  lineIdx++;
}
function removeLine(btn){
  const tr = btn.closest('tr');
  if(tr) tr.remove();
}
</script>

@endsection
