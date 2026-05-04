<section>
    <form method="post" action="{{ route('profile.update') }}" class="row g-3" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="col-12">
            <label class="form-label fw-semibold">Profile Photo</label>

            <div class="d-flex align-items-center gap-3 flex-wrap">
                @if (!empty($user->photo))
                    <img src="{{ Storage::url($user->photo) }}"
                        alt="Profile"
                        class="rounded-circle border"
                        style="width:72px; height:72px; object-fit:cover;">
                @else
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-uppercase border"
                        style="width:72px; height:72px; background:#e0e7ff; color:#3730a3; font-size:24px;">
                        {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
                    </div>
                @endif

                <div class="flex-grow-1">
                    <input type="file" name="photo" class="form-control" accept="image/*">
                    @if ($errors->get('photo'))
                        <div class="text-danger small mt-1">{{ $errors->first('photo') }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12">
            <label for="name" class="form-label fw-semibold">Name</label>
            <input id="name" name="name" type="text" class="form-control"
                value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @if ($errors->get('name'))
                <div class="text-danger small mt-1">{{ $errors->first('name') }}</div>
            @endif
        </div>

        <div class="col-12">
            <label for="email" class="form-label fw-semibold">Email</label>
            <input id="email" name="email" type="email" class="form-control"
                value="{{ old('email', $user->email) }}" required autocomplete="username">
            @if ($errors->get('email'))
                <div class="text-danger small mt-1">{{ $errors->first('email') }}</div>
            @endif
        </div>

        <div class="col-12 d-flex align-items-center gap-3 pt-2">
            <button type="submit" class="btn btn-dark rounded-pill px-4">Save Changes</button>

            @if (session('status') === 'profile-updated')
                <span class="text-success small">Saved successfully.</span>
            @endif
        </div>
    </form>
</section>