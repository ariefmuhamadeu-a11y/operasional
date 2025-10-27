{{-- resources/views/layouts/partials/alerts.blade.php --}}
@if (session('status'))
  <div class="alert alert-success alert-dismissible fade show">
    {{ session('status') }}
    <button
      type="button"
      class="btn-close"
      data-bs-dismiss="alert"
    ></button>
  </div>
@endif

@if (session('error'))
  <div class="alert alert-danger alert-dismissible fade show">
    {{ session('error') }}
    <button
      type="button"
      class="btn-close"
      data-bs-dismiss="alert"
    ></button>
  </div>
@endif
