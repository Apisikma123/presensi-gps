@extends('layouts.mobile.modern')

@section('title', 'Profile')

@section('header_left')
    <a href="{{ route('dashboard.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-white active:scale-95 transition-all">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@push('mystyle')
    <style>
        .form-container {
            padding: 12px 6px;
        }

        .profile-card-surface {
            background: #ffffff;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 20px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
            padding: 20px 16px;
        }

        .form-label-group {
            position: relative;
            margin-bottom: 14px;
            background: #F8FAF8 !important;
            border: 1px solid rgba(15, 23, 42, 0.12);
            border-radius: 14px;
            overflow: hidden;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .form-label-group:focus-within {
            border-color: #1E4D3E !important;
            background: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(30, 77, 62, 0.08) !important;
        }

        .form-label-group .input-icon {
            position: absolute;
            left: 14px;
            top: 13px;
            font-size: 18px;
            color: #94a3b8;
            z-index: 10;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        .form-label-group:focus-within .input-icon {
            color: #1E4D3E;
        }

        .form-label-group input,
        .form-label-group textarea {
            width: 100% !important;
            height: 48px;
            padding: 18px 14px 2px 42px !important;
            font-size: 13px;
            font-weight: 500;
            color: #0f172a;
            background: transparent !important;
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            display: block !important;
        }

        .form-label-group textarea {
            height: 84px !important;
            padding-top: 22px !important;
            resize: none;
        }

        .form-label-group label {
            position: absolute;
            top: 13px;
            left: 42px;
            font-size: 13px;
            color: #64748b;
            pointer-events: none;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            margin-bottom: 0;
            z-index: 5;
        }

        .form-label-group input:focus ~ label,
        .form-label-group input:not(:placeholder-shown) ~ label,
        .form-label-group textarea:focus ~ label,
        .form-label-group textarea:not(:placeholder-shown) ~ label {
            top: 4px;
            left: 42px;
            font-size: 10px;
            font-weight: 700;
            color: #1E4D3E;
        }

        /* Foto Profil */
        .profile-photo-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 22px;
            position: relative;
        }

        .profile-photo-box {
            position: relative;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            padding: 3px;
            background: #ffffff;
            border: 2px solid rgba(15, 23, 42, 0.08);
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
            cursor: pointer;
        }

        .profile-photo-box img,
        .profile-photo-box .photo-placeholder {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .profile-photo-box .photo-placeholder {
            background-size: cover;
            background-position: center;
        }

        .profile-photo-edit-badge {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #1E4D3E;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #ffffff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.12);
        }

        .btn-submit-modern {
            width: 100%;
            height: 50px;
            background: #1E4D3E;
            color: #ffffff;
            border: none;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 8px;
            box-shadow: 0 2px 6px rgba(30, 77, 62, 0.15);
            transition: all 0.15s ease;
        }

        .btn-submit-modern:active {
            transform: scale(0.98);
            background: #16382E;
        }
    </style>
@endpush

@section('content')
    <div class="fade-up form-container pb-24">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="formProfile" autocomplete="off">
            @csrf
            @method('PUT')

            {{-- Profile Photo & Header --}}
            <div class="profile-photo-wrapper">
                <div class="profile-photo-box" onclick="document.getElementById('foto').click()">
                    @if (!empty($karyawan->foto))
                        <div class="photo-placeholder" style="background-image: url({{ getfotoKaryawan($karyawan->foto) }});"></div>
                    @else
                        <img src="{{ asset('assets/img/avatars/No_Image_Available.jpg') }}" alt="Profile Photo">
                    @endif
                    <div class="profile-photo-edit-badge">
                        <ion-icon name="camera" style="font-size:14px;"></ion-icon>
                    </div>
                </div>
                <h4 class="text-[14px] font-bold text-slate-800 mt-2 mb-0.5 text-center">
                    {{ $karyawan->nama_karyawan ?? $user->name }}
                </h4>
                <span class="text-[11px] font-mono font-medium px-2.5 py-0.5 rounded-full bg-white text-slate-600 border border-slate-200/80 shadow-xs">
                    NIK: {{ $karyawan->nik ?? '-' }}
                </span>
            </div>

            {{-- Hidden Input Foto --}}
            <input type="file" name="foto" id="foto" accept=".jpg, .jpeg, .png, .webp" style="display: none;">

            {{-- Nama Lengkap --}}
            <div class="form-label-group">
                <ion-icon name="person-outline" class="input-icon"></ion-icon>
                <input type="text" name="nama_karyawan" id="nama_karyawan" placeholder=" " value="{{ $karyawan->nama_karyawan ?? '' }}" required>
                <label for="nama_karyawan">Nama Lengkap</label>
            </div>

            {{-- No. KTP --}}
            <div class="form-label-group">
                <ion-icon name="card-outline" class="input-icon"></ion-icon>
                <input type="text" name="no_ktp" id="no_ktp" placeholder=" " value="{{ $karyawan->no_ktp ?? '' }}" required>
                <label for="no_ktp">No. KTP</label>
            </div>

            {{-- No. HP --}}
            <div class="form-label-group">
                <ion-icon name="call-outline" class="input-icon"></ion-icon>
                <input type="text" name="no_hp" id="no_hp" placeholder=" " value="{{ $karyawan->no_hp ?? '' }}" required>
                <label for="no_hp">No. HP</label>
            </div>

            {{-- Alamat --}}
            <div class="form-label-group">
                <ion-icon name="location-outline" class="input-icon"></ion-icon>
                <textarea name="alamat" id="alamat" placeholder=" " required>{{ $karyawan->alamat ?? '' }}</textarea>
                <label for="alamat">Alamat</label>
            </div>

            {{-- Username --}}
            <div class="form-label-group">
                <ion-icon name="at-outline" class="input-icon"></ion-icon>
                <input type="text" name="username" id="username" placeholder=" " value="{{ $user->username }}" required>
                <label for="username">Username</label>
            </div>

            {{-- Email --}}
            <div class="form-label-group">
                <ion-icon name="mail-outline" class="input-icon"></ion-icon>
                <input type="email" name="email" id="email" placeholder=" " value="{{ $user->email }}" required>
                <label for="email">Email</label>
            </div>



            {{-- Submit Button --}}
            <button type="submit" class="btn-submit-modern" id="btnSimpan">
                <ion-icon name="save-outline" style="font-size:18px;"></ion-icon>
                <span>Update Profile</span>
            </button>
        </form>
    </div>
@endsection

@push('myscript')
    <script>
        // Instant Avatar Preview on Photo Select
        document.getElementById('foto').addEventListener('change', function() {
            let file = this.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    let box = document.querySelector('.profile-photo-box');
                    let placeholder = box.querySelector('.photo-placeholder');
                    let img = box.querySelector('img');
                    if (placeholder) {
                        placeholder.style.backgroundImage = 'url(' + e.target.result + ')';
                    } else if (img) {
                        img.src = e.target.result;
                    }
                };
                reader.readAsDataURL(file);
            }
        });

        $(function() {
            $("#formProfile").submit(function(e) {
                let nama_karyawan = $('input[name="nama_karyawan"]').val();
                let no_ktp = $('input[name="no_ktp"]').val();
                let no_hp = $('input[name="no_hp"]').val();
                let alamat = $('textarea[name="alamat"]').val();
                let username = $('input[name="username"]').val();
                let email = $('input[name="email"]').val();

                if (nama_karyawan == "" || no_ktp == "" || no_hp == "" || alamat == "" || username == "" || email == "") {
                    e.preventDefault();
                    Swal.fire({title: "Oops!", text: 'Semua Bidang Harus Diisi !', icon: "warning"});
                    return false;
                }

                const btn = document.getElementById('btnSimpan');
                btn.disabled = true;
                btn.innerHTML = `<ion-icon name="sync-outline" class="animate-spin"></ion-icon><span>Menyimpan...</span>`;
            });


        });
    </script>
@endpush
