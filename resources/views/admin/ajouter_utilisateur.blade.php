@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="card shadow border-0 rounded-4 p-4 p-md-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-orientech mb-0">Ajouter un utilisateur</h4>
            <button class="btn btn-outline-success rounded-pill" onclick="window.location.href='{{ url('/admin/utilisateurs') }}'">
                <- Retour
            </button>
        </div>

        <form method="post" action="{{ url('/admin/utilisateurs/ajouter') }}">
            @csrf
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Nom</label>
                    <input type="text" name="nom" class="form-control rounded-pill" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Prenom</label>
                    <input type="text" name="prenom" class="form-control rounded-pill" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Login</label>
                    <input type="text" name="login" class="form-control rounded-pill" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Mot de passe</label>
                    <input type="password" name="password" class="form-control rounded-pill" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Role</label>
                    <select name="acreditation" class="form-select rounded-pill" required>
                        <option value="">-- Choisir un role --</option>
                        <option>Admin</option>
                        <option>Prof</option>
                        <option>Eleve</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Statut du compte</label>
                    <select class="form-select rounded-pill" disabled>
                        <option>Actif</option>
                    </select>
                </div>
            </div>

            <div class="text-center mt-5">
                <button type="submit" class="btn btn-orientech rounded-pill px-5 me-3">
                    Creer l'utilisateur
                </button>
                <button type="button" class="btn btn-outline-success rounded-pill px-5"
                        onclick="window.location.href='{{ url('/admin/utilisateurs') }}'">
                    Annuler
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const error = "{{ session('error') }}";
if (error === "1") {
    alert("Erreur: login deja utilise ou champs invalides.");
}
</script>
@endsection
