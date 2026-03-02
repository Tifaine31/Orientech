@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="card shadow border-0 rounded-4 p-4 p-md-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-orientech mb-0">Gestion des utilisateurs</h4>
            <button class="btn btn-outline-success rounded-pill" onclick="window.location.href='{{ url('/admin') }}'">
                <- Retour
            </button>
        </div>

        <div class="mb-4">
            <button class="btn btn-orientech rounded-pill px-4"
                    onclick="location.href='{{ url('/admin/utilisateurs/ajouter') }}'">
                + Ajouter un utilisateur
            </button>
        </div>

        @if (session('success') === '1')
            <div class="alert alert-success">Utilisateur cree avec succes</div>
        @endif
        @if (session('updated') === '1')
            <div class="alert alert-success">Utilisateur modifie avec succes</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-secondary">
                    <tr>
                        <th>Nom</th>
                        <th>Prenom</th>
                        <th>Login</th>
                        <th>Role</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($users as $u)
                    <tr>
                        <td>{{ $u->nom }}</td>
                        <td>{{ $u->prenom }}</td>
                        <td>{{ $u->login }}</td>
                        <td>{{ $u->role ?: 'eleve' }}</td>
                        <td><span class="badge bg-success">Actif</span></td>
                        <td>
                            <a class="btn btn-sm btn-outline-success rounded-pill me-2"
                               href="{{ url('/admin/utilisateurs/' . (int)$u->id . '/modifier') }}">
                                Modifier
                            </a>
                            <form class="d-inline"
                                  method="post"
                                  action="{{ url('/admin/utilisateurs/' . (int)$u->id . '/supprimer') }}"
                                  onsubmit="return confirm('Supprimer definitivement cet utilisateur ?');">
                                @csrf
                                <button class="btn btn-sm btn-outline-danger rounded-pill" type="submit">
                                    Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
