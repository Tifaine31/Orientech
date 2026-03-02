@extends('layouts.auth')

@section('content')
    <div class="container-fluid py-3">
        <img src="{{ asset('logo_complet.png') }}" alt="ORIENTECH95" height="70">
    </div>

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow border-0 login-card mt-n5">
            <div class="card-body p-4 p-md-5">

                <form method="post" action="{{ url('/login') }}">
                    @csrf
                    <div class="mb-5">
                        <label class="form-label fw-bold text-orientech">Login</label>
                        <input type="text"
                               name="login"
                               class="form-control rounded-pill bg-secondary text-white"
                               placeholder="John.Doe"
                               required>
                    </div>

                    <div class="mb-5">
                        <label class="form-label fw-bold text-orientech">Mot de passe</label>
                        <div class="position-relative">
                            <input type="password"
                                   id="password"
                                   name="password"
                                   class="form-control rounded-pill bg-secondary text-white pe-5"
                                   placeholder="************"
                                   required>
                            <button type="button"
                                    id="togglePassword"
                                    class="btn btn-sm btn-outline-light position-absolute top-50 end-0 translate-middle-y me-2 rounded-pill px-2 py-0"
                                    aria-label="Afficher le mot de passe">
                                Afficher
                            </button>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit"
                                class="btn btn-orientech rounded-pill px-5 py-3">
                            Connexion
                        </button>
                    </div>

                    @if (session('error') === '1')
                        <p class="text-danger text-center mt-4">
                            Identifiant ou mot de passe incorrect
                        </p>
                    @endif
                </form>

            </div>
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const togglePasswordBtn = document.getElementById('togglePassword');

        if (passwordInput && togglePasswordBtn) {
            togglePasswordBtn.addEventListener('click', function () {
                const show = passwordInput.type === 'password';
                passwordInput.type = show ? 'text' : 'password';
                togglePasswordBtn.textContent = show ? 'Masquer' : 'Afficher';
                togglePasswordBtn.setAttribute(
                    'aria-label',
                    show ? 'Masquer le mot de passe' : 'Afficher le mot de passe'
                );
            });
        }
    </script>
@endsection
