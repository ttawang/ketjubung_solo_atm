<form action="{{ isset($id) ? route('management.users.update', $id) : route('management.users.store') }}"
    onsubmit="submitForm(event, $(this));" method="POST" class="formInput">
    @if (isset($id))
        @method('PATCH')
    @endif
    <div class="form-group">
        <label>Nama</label>
        <input type="text" value="{{ $data->name ?? '' }}" class="form-control" name="input[name]" required>
    </div>
    <div class="form-group">
        <label>Nickname</label>
        <input type="text" value="{{ $data->nickname ?? '' }}" class="form-control" name="input[nickname]" required>
    </div>
    <div class="form-group">
        <label>Role</label>
        <select name="input[roles_id]" id="select_role" class="form-control select2"
            data-route="{{ route('helper.getRole') }}" data-placeholder="-- Pilih Role --"></select>
    </div>
    <div class="form-group">
        <label>Email</label>
        <input type="email" value="{{ $data->email ?? '' }}" class="form-control" name="input[email]" required>
    </div>
    <div class="form-group">
        <label>Password</label>
        <input type="password" class="form-control" name="input[password]" {{ isset($id) ? '' : 'required' }}>
        @if (isset($id))
            <small>*) Kosongkan jika password tidak dirubah.</small>
        @endif
    </div>
</form>
