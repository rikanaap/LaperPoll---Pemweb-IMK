@if(session('success'))
    <div class="alert alert--success">
        <span class="material-icons-round">check_circle</span>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert--error">
        <span class="material-icons-round">error</span>
        {{ session('error') }}
    </div>
@endif