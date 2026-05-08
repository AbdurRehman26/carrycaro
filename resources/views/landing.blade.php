<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CarryCaro</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="landing-page">
<header class="site-header" aria-label="Primary navigation">
    <a class="brand-mark" href="{{ url('/') }}" aria-label="CarryCaro home">
        <img src="{{ asset('logo.png') }}" alt="">
        <span>CarryCaro</span>
    </a>
    <nav class="site-nav" aria-label="Primary">
        <a href="#how-it-works">How it works</a>
        <a href="#routes">Routes</a>
        <a href="{{ route('docs.swagger') }}">API docs</a>
    </nav>
    <a class="header-action" href="{{ url('/api/trips') }}">Browse trips</a>
</header>

<main>
    <section class="hero-section">
        <div class="hero-visual" aria-hidden="true">
            <img src="{{ asset('logo.png') }}" alt="">
        </div>
        <div class="hero-content">
            <p class="eyebrow">Peer-to-peer travel delivery</p>
            <h1>CarryCaro</h1>
            <p class="hero-copy">
                Connect with travelers already flying your route and turn spare luggage space into fast, personal delivery.
            </p>
            <div class="hero-actions">
                <a class="primary-action" href="{{ url('/api/trips') }}">Find a route</a>
                <a class="secondary-action" href="{{ route('docs.swagger') }}">View API</a>
            </div>
        </div>
        <div class="route-strip" aria-label="Popular routes">
            <span>Berlin</span>
            <span>Frankfurt</span>
            <span>Istanbul</span>
            <span>Dubai</span>
            <span>London</span>
        </div>
    </section>

    <section class="trust-band" aria-label="CarryCaro highlights">
        <div>
            <strong>City-to-city</strong>
            <span>Search verified routes by departure city, arrival city, and date.</span>
        </div>
        <div>
            <strong>Traveler-led</strong>
            <span>Trips show airline, timing, available weight, and price details.</span>
        </div>
        <div>
            <strong>API-ready</strong>
            <span>Mobile teams can build directly from the documented Laravel API.</span>
        </div>
    </section>

    <section class="flow-section" id="how-it-works">
        <div class="section-heading">
            <p class="eyebrow">Simple flow</p>
            <h2>From route search to handoff</h2>
        </div>
        <div class="flow-grid">
            <article>
                <span>01</span>
                <h3>Search the route</h3>
                <p>Filter trips by origin, destination, and departure window to find travelers heading your way.</p>
            </article>
            <article>
                <span>02</span>
                <h3>Compare capacity</h3>
                <p>Check available weight, pricing, airline details, and traveler notes before making contact.</p>
            </article>
            <article>
                <span>03</span>
                <h3>Coordinate delivery</h3>
                <p>Use profile and trip details to plan a handoff that works for both sides of the journey.</p>
            </article>
        </div>
    </section>

    <section class="routes-section" id="routes">
        <div class="routes-copy">
            <p class="eyebrow">Built for movement</p>
            <h2>Turn everyday travel into a delivery network.</h2>
            <p>
                CarryCaro gives travelers a clean way to publish available space, while senders can discover routes without waiting for traditional courier schedules.
            </p>
            <a class="text-link" href="{{ url('/api/cities') }}">Explore cities</a>
        </div>
        <div class="route-board" aria-label="Example route board">
            <div class="board-row">
                <span>BER</span>
                <strong>Berlin to Dubai</strong>
                <em>5 kg open</em>
            </div>
            <div class="board-row">
                <span>FRA</span>
                <strong>Frankfurt to Istanbul</strong>
                <em>3 kg open</em>
            </div>
            <div class="board-row">
                <span>LHR</span>
                <strong>London to Karachi</strong>
                <em>8 kg open</em>
            </div>
        </div>
    </section>
</main>

<footer class="site-footer">
    <span>CarryCaro</span>
    <a href="{{ route('docs.openapi') }}">OpenAPI spec</a>
</footer>
</body>
</html>
