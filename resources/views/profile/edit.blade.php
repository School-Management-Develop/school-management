@extends('backend.layout.master')

@section('title', 'Profile')

@section('contents')
    <style>
        /* Custom styling to match the screenshot */
        .custom-toast {
            position: relative;
            overflow: hidden;
            /* Keeps the progress bar inside the rounded corners */
            animation: slideRotateIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }

        @keyframes slideRotateIn {
            from {
                transform: translateX(100%) rotate(10deg);
                opacity: 0;
            }

            to {
                transform: translateX(0) rotate(0deg);
                opacity: 1;
            }
        }

        /* 2. The Progress Bar "Run Back" */
        .progress-loader {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 4px;
            width: 100%;
            opacity: 0.6;
            animation: runBack 4s linear forwards;
        }

        @keyframes runBack {
            from {
                width: 100%;
            }

            to {
                width: 0%;
            }
        }

        /* 3. Smooth exit when closed */
        .alert.fade.show {
            transition: all 0.4s ease;
        }
    </style>
     {{-- Alert Container for Success and Error Messages --}}
    <div id="alert-container" class="position-fixed top-0 end-0 p-3" style="z-index: 9999; max-width: 400px;">
        {{-- Success --}}
        @if (session('success'))
            <div class="alert custom-toast alert-success border-0 border-start border-5 border-success shadow-sm rounded-4 fade show bg-white mb-3"
                role="alert">
                <div class="d-flex align-items-center p-2">
                    <div class="me-3 fs-4 text-success"><i class="bi bi-check-circle-fill"></i></div>
                    <div>
                        <strong class="d-block text-dark">Success</strong>
                        <span class="text-muted small">{{ session('success') }}</span>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
                <div class="progress-loader bg-success"></div>
            </div>
        @endif

        {{-- Errors --}}
        @if ($errors->any())
            @foreach ($errors->all() as $e)
                <div class="alert custom-toast alert-danger border-0 border-start border-5 border-danger shadow-sm rounded-4 fade show bg-white mb-2"
                    role="alert">
                    <div class="d-flex align-items-center p-2">
                        <div class="me-3 fs-4 text-danger"><strong>!</strong></div>
                        <div>
                            <strong class="d-block text-dark">Error</strong>
                            <span class="text-muted small">{{ $e }}</span>
                        </div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                    </div>
                    <div class="progress-loader bg-danger"></div>
                </div>
            @endforeach
        @endif
    </div>
    <div class="container-fluid py-4">
        @php
            $user = Auth::user();
            $profilePhoto = !empty($user?->photo) ? Storage::url($user->photo) : null;
            $userInitial = strtoupper(substr($user->name ?? 'A', 0, 1));
        @endphp

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-0">
                <div class="p-3 p-md-4 text-white rounded-4"
                    style="background: linear-gradient(135deg, #111827 0%, #1f2937 45%, #4338ca 100%);">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                        <div class="d-flex align-items-center gap-3">
                            @if ($profilePhoto)
                                <img src="{{ asset('storage/' . $user->photo) }}" alt="Profile Photo"
                                    class="rounded-circle border border-white shadow-sm"
                                    style="width:72px; height:72px; object-fit:cover;">
                            @else
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-uppercase shadow-sm"
                                    style="width:72px; height:72px; background: rgba(255,255,255,0.18); font-size: 28px;">
                                    {{ $userInitial }}
                                </div>
                            @endif

                            <div>
                                <h2 class="fw-bold mb-1 text-white">{{ __('app.my_profile') }}</h2>
                                <div class="text-white-50 mb-1">
                                    Manage your account information and password
                                </div>
                                <div class="small text-white-50">
                                    {{ $user->email ?? '' }}
                                </div>
                            </div>
                        </div>

                        <div>
                            <a href="{{ route('dashboard') }}" class="btn btn-light rounded-pill px-4">
                                <i class="bi bi-arrow-left me-1"></i> {{ __('app.back_to_dashboard') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4 text-center">
                        <div class="mb-3">
                            <div class="d-flex justify-content-center align-items-center">
                                @if ($profilePhoto)
                                    <img src="{{ asset('storage/' . $user->photo) }}" alt="Profile Photo"
                                        class="rounded-circle border border-white shadow-sm"
                                        style="width: 72px; height: 72px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-uppercase shadow-sm "
                                        style="width: 72px; height: 72px; background: rgba(97, 55, 248, 0.18); font-size: 28px; color: black;">
                                        {{ $userInitial }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <h4 class="fw-bold mb-1">{{ $user->name ?? 'Admin' }}</h4>
                        <div class="text-secondary mb-2">{{ $user->email ?? '' }}</div>

                        <span class="badge rounded-pill bg-success text-white px-3 py-2">
                            Online
                        </span>

                        <hr class="my-4">

                        <div class="text-start">


                            <div class="mb-3">
                                <div class="text-secondary small">Joined</div>
                                <div class="fw-semibold">
                                    {{ $user->created_at ? $user->created_at->timezone('Asia/Jakarta')->format('d M Y H:i') : '-' }}
                                </div>
                            </div>

                            <div>
                                <div class="text-secondary small">Last Update</div>
                                <div class="fw-semibold">
                                    {{ $user->updated_at ? $user->updated_at->timezone('Asia/Jakarta')->format('d M Y H:i') : '-' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-8">
                <div class="d-flex flex-column gap-4">

                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <h4 class="fw-bold mb-1">{{ __('app.Profile Information') }}</h4>
                                <div class="text-secondary">
                                    {{ __('app.Update your account\'s profile information and email address.') }}
                                </div>
                            </div>

                            {{-- Profile info form --}}
                            <form method="POST" action="{{ route('profile.update') }}" class="row g-3">
                                @csrf
                                @method('PATCH')

                                <div class="col-12">
                                    <label for="name" class="form-label fw-semibold">{{ __('app.Name') }}</label>
                                    <input id="name" name="name" type="text" class="form-control"
                                        value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="email" class="form-label fw-semibold">{{ __('app.Email') }}</label>
                                    <input id="email" name="email" type="email" class="form-control"
                                        value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-dark">{{ __('app.Save') }}</button>
                                </div>
                            </form>

                            <hr class="my-4">

                            {{-- Photo form --}}
                            <div>
                                <label class="form-label fw-semibold">{{ __('app.Profile Photo') }}</label>

                                <div class="d-flex align-items-center gap-3 flex-wrap mb-3">
                                    @if ($user->photo)
                                        <img src="{{ asset('storage/' . $user->photo) }}" alt="Profile"
                                            class="rounded-circle border"
                                            style="width:80px; height:80px; object-fit:cover;">
                                    @else
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-uppercase border"
                                            style="width:80px; height:80px; background:#e0e7ff; color:#3730a3; font-size:24px;">
                                            {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
                                        </div>
                                    @endif
                                </div>

                                @if (!$user->photo)
                                    <form method="POST" action="{{ route('profile.photo.store') }}"
                                        enctype="multipart/form-data">
                                        @csrf

                                        <input type="file" name="photo" class="form-control mb-2" accept="image/*">

                                        @error('photo')
                                            <div class="text-danger small mb-2">{{ $message }}</div>
                                        @enderror

                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('profile.photo.update') }}"
                                        enctype="multipart/form-data">
                                        @csrf

                                        <input type="file" name="photo" class="form-control mb-2" accept="image/*">

                                        @error('photo')
                                            <div class="text-danger small mb-2">{{ $message }}</div>
                                        @enderror

                                        <button type="submit" class="btn btn-warning">Change Photo</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <h4 class="fw-bold mb-1">{{ __('app.Update Password') }}</h4>
                                <div class="text-secondary">
                                    {{ __('app.Use a strong password to keep your account secure.') }}</div>
                            </div>

                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    {{-- <div class="card border-0 shadow-sm rounded-4 border border-danger-subtle">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <h4 class="fw-bold text-danger mb-1">Delete Account</h4>
                                <div class="text-secondary">
                                    Permanently delete your account and all associated data.
                                </div>
                            </div>

                            @include('profile.partials.delete-user-form')
                        </div>
                    </div> --}}

                </div>
            </div>
        </div>

    </div>
     <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.custom-toast');

            alerts.forEach(function(alert) {
                // Match this time (5000ms) to your CSS animation time
                setTimeout(function() {
                    let bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                    if (bsAlert) {
                        bsAlert.close();
                    }
                }, 4000);
            });
        });
    </script>
@endsection
