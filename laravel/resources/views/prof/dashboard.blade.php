@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="card shadow border-0 rounded-4 p-4 p-md-5">
        <h4 class="fw-bold text-orientech mb-4">Tableau de bord</h4>

        <div class="row g-4 justify-content-center">
            <div class="col-md-6 col-lg-5">
                <button class="dashboard-btn" onclick="location.href='{{ url('/prof/gestion-classes') }}'">
                    Gérer Classes
                </button>
            </div>
            <div class="col-md-6 col-lg-5">
                <button class="dashboard-btn" onclick="location.href='{{ url('/prof/gestion-eleves') }}'">
                    Gérer Élèves
                </button>
            </div>
            <div class="col-md-6 col-lg-5">
                <button class="dashboard-btn" onclick="location.href='{{ url('/prof/gestion-parcours') }}'">
                    Gérer Parcours
                </button>
            </div>
            <div class="col-md-6 col-lg-5">
                <button class="dashboard-btn" onclick="location.href='{{ url('/prof/gestion-seances') }}'">
                    Gérer Séances
                </button>
            </div>
            <div class="col-md-6 col-lg-5">
                <button class="dashboard-btn" onclick="location.href='{{ url('/prof/historique-seances') }}'">
                    Historique Séances
                </button>
            </div>
            <div class="col-md-6 col-lg-5">
                <button class="dashboard-btn" onclick="location.href='{{ url('/prof/gestion-boitiers') }}'">
                    Gérer Boîtiers
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
