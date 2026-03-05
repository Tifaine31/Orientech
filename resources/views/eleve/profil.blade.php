@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="card shadow border-0 rounded-4 p-4 p-md-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-orientech mb-0">Modifier mon profil</h4>
            <button class="btn btn-outline-success rounded-pill" onclick="window.location.href='{{ url('/eleve') }}'">
                ← Retour
            </button>
        </div>

        @if (session('success'))
            <div class="alert alert-success">Profil mis à jour.</div>
        @elseif (session('error'))
            @if (session('error') === 'photo')
                <div class="alert alert-danger">Erreur : format photo invalide (jpg, jpeg, png, webp).</div>
            @elseif (session('error') === '2')
                <div class="alert alert-danger">Aucune modification détectée.</div>
            @else
                <div class="alert alert-danger">Erreur : vérifie le mot de passe actuel.</div>
            @endif
        @endif

        <form method="post" action="{{ url('/eleve/profil') }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label fw-bold">Photo de profil</label>
                    <div class="d-flex align-items-center gap-3">
                        @if (!empty($eleve->photo))
                            <img src="{{ $eleve->photo }}" alt="Photo profil" style="width:72px;height:72px;object-fit:cover;border-radius:50%;border:1px solid #ddd;">
                        @else
                            <div style="width:72px;height:72px;border-radius:50%;border:1px solid #ddd;display:flex;align-items:center;justify-content:center;">
                                -
                            </div>
                        @endif
                        <input type="file" name="photo" class="form-control" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Mot de passe actuel</label>
                    <input type="password" name="current_password" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Nouveau mot de passe</label>
                    <input type="password" name="new_password" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Confirmer le nouveau mot de passe</label>
                    <input type="password" name="confirm_password" class="form-control">
                </div>
            </div>

            <div class="text-center mt-4">
                <button class="btn btn-orientech rounded-pill px-5" type="submit">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
