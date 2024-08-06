@extends('layouts.app')
@section('title', 'Excel Handler')
@section('colWidth', '8')
@section('content')
    <div class="card bg-info-subtle">
        <h1 class="card-header">Welcome to Excel Handler & Mailer</h1>
        <div class="card-body p-3">
            <div class="row mb-2">
                <div class="col-md-6">
                    <a href="{{ Route('add') }}" style="width:100%" class="btn btn-info"><i class="bi bi-person-fill-add"></i> Add Data Manually</a>
                </div>
                <div class="col-md-6">
                    <a href="{{ Route('upload') }}" style="width:100%" class="btn btn-primary"><i class="bi bi-file-earmark-spreadsheet-fill"></i> Import from File</a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <a href="{{ Route('show') }}" style="width:100%" class="btn btn-success"><i class="bi bi-file-spreadsheet-fill"></i> Go to Data Table</a>
                </div>
                <div class="col-md-6">
                    <a href="{{ Route('mailBatch') }}" style="width:100%" class="btn btn-warning" onclick="return confirm('Are you sure?\nThis will send all the users in the dataset a Test Email.')"><i class="bi bi-envelope-at-fill"></i> Batch Mail Users in Dataset</a>
                </div>
            </div>
        </div>
        @include('layouts.footer')
    </div>
@endsection
