<!DOCTYPE html>
<html lang="en" class="form-screen">
<head>
    <x-admin-header-css></x-admin-header-css>
    <style>
        .error-message {
            text-align: center;
            color: red;
            font-size: 1.2em;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div id="app">

    <section class="section main-section">
        <div class="card">
            <header class="card-header">
                <p class="card-header-title">
                    <span class="icon"><i class="mdi mdi-lock"></i></span>
                   Admin Login
                </p>
            </header>
            <div class="card-content">
                <form method="POST" action="{{ route('loginAdmin') }}">
                    @csrf
                    @if ($errors->has('login'))
                        <div class="error-message">
                            <p>{{ $errors->first('login') }}</p>
                        </div>
                    @endif
                    <div class="field spaced">
                        <label class="label">Email</label>
                        <div class="control icons-left">
                            <input class="input" type="email" name="email" placeholder="user@example.com" autocomplete="username">
                            <span class="icon is-small left"><i class="mdi mdi-account"></i></span>
                        </div>
                        <p class="help">
                            Please enter your login
                        </p>
                    </div>

                    <div class="field spaced">
                        <label class="label">Password</label>
                        <p class="control icons-left">
                            <input class="input" type="password" name="password" placeholder="Password" autocomplete="current-password">
                            <span class="icon is-small left"><i class="mdi mdi-asterisk"></i></span>
                        </p>
                        <p class="help">
                            Please enter your password
                        </p>
                    </div>

                    <div class="field spaced">
                        <div class="control">
                            <label class="checkbox"><input type="checkbox" name="remember" value="1" checked>
                                <span class="check"></span>
                                <span class="control-label">Remember</span>
                            </label>
                        </div>
                    </div>

                    <hr>

                    <div class="field grouped">
                        <div class="control">
                            <button type="submit" class="button blue">
                                Login
                            </button>
                        </div>
                        <div class="control">
                            <a href="index.html" class="button">
                                Back
                            </a>
                        </div>
                    </div>

                </form>
            </div>
        </div>

    </section>


</div>

<x-admin-footer-js></x-admin-footer-js>
</body>
</html>
