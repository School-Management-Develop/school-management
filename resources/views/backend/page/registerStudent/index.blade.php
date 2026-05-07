    <!doctype html>
    <html lang="{{ app()->getLocale() }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Student Registration</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/image.png') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
        <link rel="preconnect" href="https://googleapis.com">
        <link rel="preconnect" href="https://gstatic.com" crossorigin>
        <link href="https://googleapis.com/css2?family=Noto+Sans+Khmer:wght@100..900&display=swap" rel="stylesheet">

        <style>
            @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Khmer:wght@100..900&family=Siemreap&display=swap');

            html[lang="kh"] body {
                font-family: "Noto Sans Khmer", sans-serif;
                font-size: 19px;
            }

            html[lang="en"] body {
                font-size: 19px;
                font-family: "Noto Sans Khmer", sans-serif;
            }

            @media (max-width: 480px) {
                html[lang="en"] body {
                    font-size: 10px;
                }

                label {
                    font-size: 16px;
                }

                .text-muted {
                    font-size: 13px;
                }
            }

            body {
                background: #f6f7fb;
            }

            .card {
                border-radius: 16px;
                z-index: 1;
                position: relative;
            }

            .btn {
                border-radius: 12px;
                background: #00A76D;
                color: white;
            }

            .form-control,
            .form-select {
                height: 48px;
                border-radius: 12px;
                border-color: rgba(4, 83, 20, 0.8);
            }

            .form-control:focus {
                box-shadow: 0 0 0 0.15rem rgba(35, 102, 226, 0.2);
            }

            textarea.form-control {
                min-height: 50px;
            }

            .item-preview-card {
                display: none;
                border: 1px solid #eef0f4;
                border-radius: 14px;
                background: #fff;
                padding: 14px;
            }

            .item-preview-image {
                width: 90px;
                height: 90px;
                object-fit: cover;
                border-radius: 12px;
                border: 1px solid #ddd;
                background: #f8f9fa;
            }

            .language-btn {
                border-width: 1px;
                transition: all 0.25s ease;
            }

            .language-btn:hover {
                background: #f8f9fa;
                transform: translateY(-1px);
                box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
            }

            .language-menu {
                min-width: 80px;
                --bs-dropdown-link-active-bg: #f1f3f5;
                --bs-dropdown-link-active-color: #212529;
            }

            .language-menu .dropdown-item {
                transition: all 0.2s ease;
            }

            .language-menu .dropdown-item:hover {
                background-color: #f1f3f5;
                transform: scale(1.08);
            }

            .language-menu .dropdown-item.active,
            .language-menu .dropdown-item:active {
                background-color: #f1f3f5 !important;
                color: #212529 !important;
            }

            .dropdown .btn.show,
            .dropdown .btn:active,
            .dropdown .btn:focus,
            .dropdown .btn:focus-visible {
                background: #000000 !important;
                color: white !important;
                box-shadow: none !important;
                outline: none !important;
            }

            .header-wrap {
                align-items: center;
                gap: 10px;
            }

            .header-wrap img {
                width: 80px;
                height: auto;
                object-fit: contain;
                flex-shrink: 0;
            }

            .header-wrap .title-box {
                flex: 1;
                min-width: 0;
            }

            @media (max-width: 480px) {
                .policy {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    font-size: 12px;
                }

                .header-wrap img {
                    width: 55px;
                    margin-left: 3%;
                }

                .header-wrap .title-box h3 {
                    font-size: 1.1rem;
                }

                .text-end {
                    margin-right: 3%;
                }
            }

            .no-drop {
                cursor: no-drop !important;
            }

            .redred {
                color: red;
            }

            .ts-wrapper.single .ts-control {
                border: 1px solid green !important;
                height: 42px !important;
                border-radius: 10px !important;
                padding-top: 6px !important;
            }

            .gray {
                opacity: 0.6;
            }

            .logo-img {
                margin-left: 2.8%;
            }

            /* Inline group error box */
            .group-error-box {
                display: none;
                align-items: center;
                gap: 8px;
                background: #FEF2F2;
                border-left: 4px solid #ef4444;
                border-radius: 0 8px 8px 0;
                padding: 10px 14px;
                margin-top: 6px;
                font-size: 13px;
                color: #dc2626;
            }
        </style>
    </head>

    <body>

        <div class="container py-4" style="max-width:700px">
            <div class="text-center mb-3 d-flex header-wrap">
                <img src="{{ asset('assets/img/Logo.png') }}" alt="Logo" class="mb-2 logo-img">
                <div class="text-center title-box" style="padding-right: 20px;">
                    <h3 class="mb-1" style="margin-top: 5px">{{ __('app.Form Borrow Item') }}</h3>
                    <div class="text-muted">{{ __('app.fill_in_your_info') }}</div>
                </div>
            </div>

            <div class="card shadow-sm border-0 p-3">
                <div class="dropdown text-end">
                    <button class="btn rounded-pill px-3 py-2 shadow-sm dropdown-toggle" type="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        @if (app()->getLocale() == 'kh')
                            <img src="{{ asset('assets/img/world.png') }}" alt="Khmer" width="23" height="23"
                                class="rounded-circle">&nbsp;{{ __('app.khmer') }}
                        @else
                            <img src="{{ asset('assets/img/united-states-of-america.png') }}" alt="English" width="23"
                                height="23" class="rounded-circle">&nbsp;{{ __('app.english') }}
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow rounded-4 p-2 mt-2 language-menu">
                        <li>
                            <a class="dropdown-item rounded-3 text-center py-2" href="{{ route('language.switch', 'en') }}"
                                title="English">
                                <img src="{{ asset('assets/img/united-states-of-america.png') }}" alt="English"
                                    width="25" height="25" class="rounded-circle">&nbsp;English
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item rounded-3 text-start py-2" href="{{ route('language.switch', 'kh') }}"
                                title="Khmer">
                                <img src="{{ asset('assets/img/world.png') }}" alt="Khmer" width="25" height="25"
                                    class="rounded-circle">&nbsp;ខ្មែរ
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4">

                    <form method="POST" action="{{ route('submissions.store.public') }}" id="registerForm">
                        @csrf

                        <!-- STUDENT -->
                        <div class="mb-3">
                            <label class="form-label">{{ __('app.Student Name') }} <span class="redred">*</span></label>
                            <input type="text" id="student_name" name="student_name" class="form-control"
                                list="students_list" autocomplete="off" value="{{ old('student_name') }}"
                                placeholder="{{ __('app.full_name') }}" required>
                            <small id="student_help" class="text-muted"></small>
                        </div>

                        <!-- GROUP -->
                        <div class="mb-3">
                            <label class="form-label">{{ __('app.groups') }} <span class="redred">*</span></label>
                            <input type="text" id="group_search" name="group_search" class="form-control"
                                list="groups_list" placeholder="{{ __('app.Input group name') }}" autocomplete="off"
                                value="{{ old('group_search') }}" oninput="this.value = this.value.toUpperCase()">
                            <datalist id="groups_list">
                                @foreach ($groups as $g)
                                    <option value="{{ $g->group_name }}" data-id="{{ $g->group_id }}"></option>
                                @endforeach
                            </datalist>
                            <input type="hidden" name="group_id" id="group_id">

                            <!-- Inline group error -->
                            <div id="group_error" class="group-error-box">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                <span id="group_error_text"></span>
                            </div>
                        </div>

                        <!-- GENDER -->
                        <div class="mb-3">
                            <label class="form-label">{{ __('app.Gender') }} <span class="redred">*</span></label>
                            <select id="gender" name="gender" class="form-select" required>
                                <option value="" class="gray">-- {{ __('app.select_gender') }} --</option>
                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>
                                    {{ __('app.male') }}</option>
                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>
                                    {{ __('app.female') }}</option>
                            </select>
                        </div>

                        <!-- PHONE -->
                        <div class="mb-3">
                            <label class="form-label">{{ __('app.Phone Number') }} <span class="redred">*</span></label>
                            <input type="text" id="phone_number" name="phone_number" class="form-control"
                                value="{{ old('phone_number') }}" maxlength="10" inputmode="numeric"
                                placeholder="{{ __('app.Phone Number') }}" required>
                            <small id="phone_error" class="text-danger"></small>
                        </div>

                        <!-- ITEM SELECT -->
                        <div class="mb-3">
                            <label class="form-label">{{ __('app.item') }} <span class="redred">*</span></label>
                            <select id="item_id" name="item_id" placeholder="{{ __('app.Select or type item...') }}"
                                required>
                                <option value="">Search item...</option>
                                @foreach ($items as $item)
                                    <option value="{{ $item->Itemid }}" data-name="{{ $item->display_name }}"
                                    data-image="{{ $item->image ? Storage::url($item->image) : '' }}"
                                        {{ old('item_id') == $item->Itemid ? 'selected' : '' }}>
                                        {{ $item->display_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- ADAPTER WARNING -->
                        <div id="error_alert" class="d-none"
                            style="display:flex; gap:10px; align-items:flex-start;
                                border-left:4px solid #F59E0B;
                                border-radius:0 8px 8px 0;
                                background:#FFFBEB;
                                padding:12px 16px; margin-bottom:1rem; line-height:1.6;">
                            <img src="{{ asset('assets/img/brake-warning-regular (2).png') }}" id="icon"
                                alt="warning" width="20" height="20" style="margin-top:2px; flex-shrink:0;">
                            <span id="alert_text" style="font-size:13px; color:#D97706; margin-left:10px;"></span>
                        </div>

                        <!-- ITEM PREVIEW -->
                        <div id="itemPreview" class="item-preview-card mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <img id="previewImage" class="item-preview-image">
                                <div>
                                    <div class="fw-bold" id="previewName"></div>
                                    <div class="text-muted small">Selected item preview</div>
                                </div>
                            </div>
                        </div>

                        <!-- QTY -->
                        <div class="mb-3">
                            <label class="form-label">{{ __('app.qty') }} <span class="redred">*</span></label>
                            <select name="qty" id="qty" class="form-select" required>
                                <option value="1" selected>1</option>
                            </select>
                        </div>

                        <div class="mb-3 d-none">
                            <label class="form-label">Status</label>
                            <input type="text" name="status" class="form-control"
                                value="{{ old('status', 'BORROWED') }}" readonly>
                        </div>

                        <!-- NOTES -->
                        <div class="mb-3 d-none" id="notes_wrapper">
                            <label class="form-label">{{ __('app.notes') }}</label>
                            <textarea name="notes" id="notes" class="form-control" style="resize: none;"></textarea>
                        </div>

                        <div class="policy" style="margin-bottom: 2px">
                            <input type="checkbox" name="policy" id="policy" style="margin-bottom: 10px;">
                            <label for="policy" class="policy" style="color: red; margin-bottom: 10px;">
                                {{ __('app.I agree to the') }}
                            </label>
                        </div>

                        <button type="submit" class="btn btn-success w-100" id="submitBtn" disabled>
                            {{ __('app.Submit') }}
                        </button>

                    </form>
                </div>
            </div>

            <div class="text-center text-muted mt-3" style="font-size:13px">
                &copy; {{ date('Y') }} Setec Institute System. All rights reserved.
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {

                // ── SweetAlert flash messages ─────────────────────────────────────
                @if (session('success'))
                    Swal.fire({
                        icon: 'success',
                        title: 'Done!',
                        text: "{{ session('success') }}",
                        confirmButtonColor: '#198754',
                        timer: 20000
                    });
                @endif

                @if ($errors->any())
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: `<div style="text-align:left;">
                            <ul class="list-group list-group-flush">
                                @foreach ($errors->all() as $error)
                                    <li class="list-group-item text-danger border-0 text-center">
                                        <i class="bi bi-x-circle me-2"></i> {{ $error }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>`,
                        confirmButtonColor: '#dc3545',
                    });
                @endif

                @if (session('error'))
                    Swal.fire({
                        icon: 'warning',
                        title: 'Wait a minute...',
                        text: "{{ session('error') }}",
                        confirmButtonColor: '#ffc107',
                    });
                @endif

                // ── Element refs ──────────────────────────────────────────────────
                const originalSelect    = document.getElementById('item_id');
                const qtySelect         = document.getElementById('qty');
                const itemPreview       = document.getElementById('itemPreview');
                const previewImage      = document.getElementById('previewImage');
                const previewName       = document.getElementById('previewName');
                const studentNameInput  = document.getElementById('student_name');
                const genderSelect      = document.getElementById('gender');
                const phoneNumberInput  = document.getElementById('phone_number');
                const phoneError        = document.getElementById('phone_error');
                const groupSearchInput  = document.getElementById('group_search');
                const groupIdInput      = document.getElementById('group_id');
                const groupsList        = document.getElementById('groups_list');
                const groupError        = document.getElementById('group_error');
                const groupErrorText    = document.getElementById('group_error_text');
                const studentHelp       = document.getElementById('student_help');
                const erroradapter      = document.getElementById('error_alert');
                const form              = document.getElementById('registerForm');
                const submitBtn         = document.getElementById('submitBtn');
                const notesWrapper      = document.getElementById('notes_wrapper');
                const notesField        = document.getElementById('notes');

                // ── Group helpers ─────────────────────────────────────────────────
                function syncGroupId() {
                    const value = groupSearchInput.value.trim();
                    groupIdInput.value = '';
                    groupsList.querySelectorAll('option').forEach(opt => {
                        if (opt.value === value) groupIdInput.value = opt.dataset.id;
                    });
                }

                function showGroupError(msg) {
                    groupErrorText.textContent = msg;
                    groupError.style.display   = 'flex';
                    groupSearchInput.style.borderColor = '#ef4444';
                    groupSearchInput.setCustomValidity(msg);
                }

                function clearGroupError() {
                    groupError.style.display   = 'none';
                    groupErrorText.textContent = '';
                    groupSearchInput.style.borderColor = '#22c55e';
                    groupSearchInput.setCustomValidity('');
                }

                function validateGroup() {
                    syncGroupId();
                    const typed = groupSearchInput.value.trim();

                    if (!typed) {
                        groupError.style.display = 'none';
                        groupSearchInput.style.borderColor = '';
                        groupSearchInput.setCustomValidity('{{ __('app.this group is not found') }}');
                        return false;
                    }

                    if (!groupIdInput.value) {
                        showGroupError('{{ __('app.this group is not found') }}');
                        return false;
                    }

                    clearGroupError();
                    return true;
                }

                // ── Group events ──────────────────────────────────────────────────
                groupSearchInput.addEventListener('input', function () {
                    syncGroupId();
                    const typed = this.value.trim();

                    if (!typed) {
                        groupError.style.display = 'none';
                        this.style.borderColor   = '';
                        this.setCustomValidity('');
                        return;
                    }

                    if (!groupIdInput.value) {
                        showGroupError('{{ __('app.this group is not found') }}');
                    } else {
                        clearGroupError();
                    }
                });

                groupSearchInput.addEventListener('blur', function () {
                    validateGroup();
                });

                // ── Phone helpers ─────────────────────────────────────────────────
                function validateKhmerPhoneNumber() {
                    const value = phoneNumberInput.value.trim();
                    phoneError.textContent = '';
                    phoneNumberInput.setCustomValidity('');

                    if (value === '') {
                        phoneError.textContent = '{{ __('app.Phone number is required.') }}';
                        phoneNumberInput.setCustomValidity('Invalid phone number');
                        return false;
                    }
                    if (!/^0[0-9]{8,9}$/.test(value)) {
                        phoneError.textContent = '{{ __('app.Phone number must be 9 or 10 digits and start with 0.') }}';
                        phoneNumberInput.setCustomValidity('Invalid phone number');
                        return false;
                    }
                    if (['0123456789', '0987654321', '0000000000'].includes(value)) {
                        phoneError.textContent = '{{ __('app.Please enter a valid phone number.') }}';
                        phoneNumberInput.setCustomValidity('Invalid phone number');
                        return false;
                    }
                    return true;
                }

                phoneNumberInput.addEventListener('input', function () {
                    this.value = this.value.replace(/\D/g, '').slice(0, 10);
                    phoneError.textContent = '';
                    this.setCustomValidity('');
                });

                phoneNumberInput.addEventListener('keypress', function (e) {
                    if (!/[0-9]/.test(e.key)) e.preventDefault();
                });

                // ── Item UI ───────────────────────────────────────────────────────
                function setQtyOptions(isSocket) {
                    qtySelect.innerHTML = '';
                    (isSocket ? [1, 2, 3] : [1]).forEach(num => {
                        const opt = document.createElement('option');
                        opt.value = num;
                        opt.textContent = num;
                        qtySelect.appendChild(opt);
                    });
                    qtySelect.value = '1';
                }

                function updateItemUI(selectedValue) {
                    const selectedOption = originalSelect.querySelector(`option[value="${selectedValue}"]`);

                    if (!selectedOption || !selectedValue) {
                        previewImage.src = '';
                        previewName.textContent = '';
                        itemPreview.style.display = 'none';
                        setQtyOptions(false);
                        // notesField.required = false;
                        return;
                    }

                    const image     = selectedOption.getAttribute('data-image') || '';
                    const name      = selectedOption.getAttribute('data-name')  || '';
                    const lowerName = name.toLowerCase().trim();

                    erroradapter.classList.add('d-none');
                    if (
                        lowerName.includes('adaptor laptop') ||
                        lowerName.includes('ឆ្នាំងសាក laptop') ||
                        lowerName.includes('adaptor​ laptop')
                    ) {
                        document.getElementById('alert_text').textContent =
                            '{{ __('app.Please check if the charger is compatible with the laptop.') }}';
                        erroradapter.classList.remove('d-none');
                    }

                    previewImage.src          = image;
                    previewName.textContent   = name;
                    itemPreview.style.display = 'block';

                    const isSocket = lowerName.includes('socket') || lowerName.includes('ព្រី');
                    setQtyOptions(isSocket);
                    // const isOther = lowerName.includes('other') || lowerName.includes('ផ្សេង');
                    if (!isSocket) {
                        notesWrapper.classList.remove('d-none');
                        notesField.required = true;
                    } else {
                        notesWrapper.classList.add('d-none');
                        notesField.value = '';
                        notesField.required = false;
                    }
                }

                new TomSelect('#item_id', {
                    create: false,
                    sortField: { field: 'text', direction: 'asc' },
                    onChange: value => updateItemUI(value)
                });

                // ── Student autofill ──────────────────────────────────────────────
                function clearAutoFilledFields() {
                    genderSelect.value      = '';
                    phoneNumberInput.value  = '';
                    groupSearchInput.value  = '';
                    groupIdInput.value      = '';
                }

                let studentTimer = null;
                studentNameInput.addEventListener('input', function () {
                    clearTimeout(studentTimer);
                    const studentName = this.value.trim();
                    studentHelp.textContent = '';

                    if (studentName.length < 2) { clearAutoFilledFields(); return; }

                    studentTimer = setTimeout(() => {
                        fetch(`{{ route('register.checkStudentName') }}?student_name=${encodeURIComponent(studentName)}`)
                            .then(r => r.json())
                            .then(data => {
                                if (data.exists) {
                                    studentNameInput.value  = data.student_name;
                                    genderSelect.value      = data.gender        || '';
                                    phoneNumberInput.value  = data.phone_number  || '';
                                    groupSearchInput.value  = data.group_name    || '';
                                    syncGroupId();
                                    studentHelp.className   = 'text-success';
                                } else {
                                    clearAutoFilledFields();
                                    studentHelp.textContent = '';
                                }
                            })
                            .catch(err => {
                                clearAutoFilledFields();
                                studentHelp.textContent = '';
                                console.error('Student name check error:', err);
                            });
                    }, 300);
                });

                // ── Form submit ───────────────────────────────────────────────────
                form.addEventListener('submit', function (e) {
                    const isPhoneValid = validateKhmerPhoneNumber();
                    const isGroupValid = validateGroup();

                    if (!isPhoneValid || !isGroupValid) {
                        e.preventDefault();
                        if (!isGroupValid) {
                            groupSearchInput.focus();
                        } else {
                            phoneNumberInput.reportValidity();
                        }
                        return;
                    }

                    submitBtn.disabled     = true;
                    submitBtn.textContent  = '{{ __('app.Submitting...') }}';
                });

                // ── Policy checkbox ───────────────────────────────────────────────
                document.getElementById('policy').addEventListener('change', function () {
                    submitBtn.disabled = !this.checked;
                });

                // ── Init ──────────────────────────────────────────────────────────
                updateItemUI(originalSelect.value || '');
                syncGroupId();
            });
        </script>

        <script src="{{ asset('assets/js/fcf4581152.js') }}"></script>
        <script src="https://kit.fontawesome.com/fcf4581152.js" crossorigin="anonymous"></script>
    </body>

    </html>