<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kopi Keliling Tracker</title>
    <link rel="icon" type="image/png" href="{{ asset('images/ada-coffee-logo.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600;700;800&display=swap" rel="stylesheet" />
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""
    >
    <style>
        :root {
            color-scheme: light;
            --cream: #f7f0e3;
            --latte: #e8d9c5;
            --espresso: #3b2418;
            --mocha: #6a412d;
            --caramel: #b56a3b;
            --foam: rgba(255, 249, 241, 0.84);
            --leaf: #2f6b55;
            --line: rgba(76, 48, 33, 0.14);
            --app-bg:
                radial-gradient(circle at top left, rgba(181, 106, 59, 0.24), transparent 24%),
                radial-gradient(circle at bottom right, rgba(47, 107, 85, 0.12), transparent 22%),
                linear-gradient(145deg, #f2e6d5 0%, #f7f0e3 45%, #efe6d7 100%);
            --sidebar-bg: linear-gradient(180deg, #6a412d 0%, #8a5536 52%, #b56a3b 100%);
            --panel: rgba(255, 252, 247, 0.94);
            --panel-alt: rgba(255, 249, 241, 0.82);
            --panel-border: var(--line);
            --text-main: var(--espresso);
            --text-soft: rgba(59, 36, 24, 0.66);
            --accent: #2d63e2;
            --accent-soft: rgba(45, 99, 226, 0.12);
            --shadow-lg: 0 24px 80px rgba(59, 36, 24, 0.14);
            --shadow-sm: 0 16px 32px rgba(59, 36, 24, 0.1);
            --shell-radius: 38px;
            --page-pad: clamp(18px, 3.6vw, 68px);
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Figtree, sans-serif;
            color: var(--text-main);
            background: var(--app-bg);
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background:
                radial-gradient(circle at 20% 18%, rgba(255, 255, 255, 0.42), transparent 18%),
                radial-gradient(circle at 82% 10%, rgba(181, 106, 59, 0.12), transparent 16%),
                radial-gradient(circle at 76% 78%, rgba(47, 107, 85, 0.07), transparent 18%);
            pointer-events: none;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button,
        input,
        textarea,
        select {
            font: inherit;
        }

        .tracker-shell {
            width: 100%;
            min-height: 100vh;
            margin: 0;
            border-radius: 0;
            overflow: visible;
            box-shadow: none;
            border: 0;
            background: rgba(255, 250, 242, 0.88);
            backdrop-filter: none;
            position: relative;
            z-index: 1;
        }

        .tracker-topbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            padding: 14px var(--page-pad) 12px;
            border-bottom: 1px solid var(--panel-border);
            background: rgba(255, 250, 242, 0.88);
            box-shadow: 0 16px 36px rgba(59, 36, 24, 0.08);
            backdrop-filter: blur(18px);
        }

        .tracker-brand {
            display: flex;
            align-items: center;
            gap: 16px;
            min-width: 0;
        }

        .tracker-brand-mark {
            width: 76px;
            height: 76px;
            border-radius: 0;
            display: block;
            object-fit: contain;
            background: transparent;
            box-shadow: none;
            flex-shrink: 0;
        }

        .tracker-brand-copy strong,
        .tracker-brand-copy span {
            display: block;
        }

        .tracker-brand-copy strong {
            font-size: 1.05rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .tracker-brand-copy span {
            margin-top: 6px;
            color: var(--text-soft);
            font-size: 0.92rem;
        }

        .tracker-topbar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .mobile-nav-toggle {
            display: none;
            width: 44px;
            height: 44px;
            border: 1px solid var(--panel-border);
            border-radius: 16px;
            background: rgba(255, 252, 247, 0.92);
            box-shadow: var(--shadow-sm);
            color: var(--espresso);
            cursor: pointer;
            place-items: center;
        }

        .mobile-nav-toggle span {
            width: 20px;
            height: 2px;
            border-radius: 999px;
            background: currentColor;
            position: relative;
            transition: background 160ms ease;
        }

        .mobile-nav-toggle span::before,
        .mobile-nav-toggle span::after {
            content: "";
            position: absolute;
            left: 0;
            width: 20px;
            height: 2px;
            border-radius: 999px;
            background: currentColor;
            transition: transform 180ms ease, top 180ms ease;
        }

        .mobile-nav-toggle span::before {
            top: -7px;
        }

        .mobile-nav-toggle span::after {
            top: 7px;
        }

        .mobile-nav-toggle.is-open span {
            background: transparent;
        }

        .mobile-nav-toggle.is-open span::before {
            top: 0;
            transform: rotate(45deg);
        }

        .mobile-nav-toggle.is-open span::after {
            top: 0;
            transform: rotate(-45deg);
        }

        .tracker-chip,
        .tracker-link,
        .tracker-auth {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 18px;
            border-radius: 999px;
            border: 1px solid var(--panel-border);
            background: var(--panel);
            box-shadow: var(--shadow-sm);
            color: var(--text-main);
        }

        .tracker-chip {
            font-weight: 700;
        }

        .tracker-chip small {
            color: var(--text-soft);
            font-size: 0.78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .tracker-chip strong {
            display: block;
            font-size: 0.95rem;
        }

        .tracker-link,
        .tracker-auth {
            font-weight: 700;
        }

        .tracker-auth.is-primary {
            color: #fff;
            border-color: transparent;
            background: linear-gradient(135deg, var(--mocha) 0%, var(--caramel) 100%);
        }

        .tracker-content {
            padding: 24px var(--page-pad) 42px;
            display: grid;
            gap: 22px;
            background: rgba(255, 252, 247, 0.92);
        }

        .hero-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.08fr) minmax(320px, 0.72fr);
            gap: 22px;
            align-items: stretch;
        }

        .panel,
        .hero-card,
        .stat-card {
            border: 1px solid var(--panel-border);
            border-radius: 30px;
            background: var(--panel);
            box-shadow: var(--shadow-sm);
        }

        .hero-card {
            padding: 0;
            position: relative;
            overflow: hidden;
            display: grid;
            align-content: start;
        }

        .hero-card::after,
        .hero-map-card::after {
            content: "";
            position: absolute;
            inset: auto -40px -40px auto;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(181, 106, 59, 0.16), transparent 70%);
            pointer-events: none;
        }

        .hero-banner {
            min-height: 174px;
            background:
                linear-gradient(90deg, rgba(25, 18, 16, 0.18), rgba(25, 18, 16, 0.02)),
                url("{{ asset('images/coffee-hero-banner.jpg') }}") center / cover no-repeat;
            border-bottom: 1px solid var(--panel-border);
        }

        .hero-copy {
            padding: 28px 30px 30px;
            position: relative;
            z-index: 1;
        }

        .hero-map-card {
            position: relative;
            overflow: hidden;
            min-height: 100%;
            padding: 24px;
            display: grid;
            grid-template-rows: auto 1fr;
            gap: 18px;
            background:
                radial-gradient(circle at top left, rgba(181, 106, 59, 0.14), transparent 28%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 243, 236, 0.96));
        }

        .hero-map-heading {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            position: relative;
            z-index: 1;
        }

        .hero-map-heading h2 {
            margin-top: 12px;
            font-size: clamp(1.8rem, 3vw, 2.8rem);
            line-height: 1.02;
            letter-spacing: -0.04em;
        }

        .hero-map-heading p {
            margin-top: 10px;
            max-width: 60ch;
            color: var(--text-soft);
            line-height: 1.7;
        }

        .hero-map-frame {
            position: relative;
            min-height: 530px;
            border: 10px solid rgba(255, 255, 255, 0.84);
            border-radius: 30px;
            overflow: hidden;
            background: #f5efe6;
            box-shadow:
                inset 0 0 0 1px rgba(76, 48, 33, 0.08),
                0 22px 44px rgba(59, 36, 24, 0.14);
            z-index: 1;
        }

        .hero-showcase {
            margin: -24px calc(var(--page-pad) * -1) 0;
            position: relative;
        }

        .hero-banner-slide {
            min-height: 540px;
            padding: 86px min(9vw, 130px) 170px;
            position: relative;
            overflow: hidden;
            display: grid;
            align-items: center;
            color: #fff;
            background:
                linear-gradient(90deg, rgba(14, 12, 10, 0.82) 0%, rgba(14, 12, 10, 0.62) 42%, rgba(14, 12, 10, 0.28) 100%),
                linear-gradient(180deg, rgba(14, 12, 10, 0.18), rgba(14, 12, 10, 0.66)),
                url("{{ asset('images/coffee-hero-banner.jpg') }}") center / cover no-repeat;
        }

        .hero-banner-slide::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 18% 34%, rgba(255, 255, 255, 0.16), transparent 16%),
                linear-gradient(90deg, rgba(0, 0, 0, 0.22), transparent 62%);
            pointer-events: none;
        }

        .hero-banner-content {
            position: relative;
            z-index: 1;
            max-width: 760px;
        }

        .hero-banner-kicker {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            color: #ffc64a;
            font-weight: 800;
            letter-spacing: 0.02em;
            font-size: clamp(1rem, 1.4vw, 1.25rem);
        }

        .hero-banner-kicker::before {
            content: "";
            width: 34px;
            height: 2px;
            border-radius: 999px;
            background: currentColor;
        }

        .hero-banner-content h1 {
            margin-top: 22px;
            font-size: clamp(2.8rem, 5.4vw, 5.9rem);
            line-height: 1.03;
            letter-spacing: -0.055em;
            text-wrap: balance;
        }

        .hero-banner-content p {
            max-width: 610px;
            margin-top: 24px;
            color: rgba(255, 255, 255, 0.84);
            line-height: 1.8;
            font-size: clamp(1rem, 1.4vw, 1.18rem);
        }

        .hero-slider-dots {
            position: absolute;
            left: 50%;
            bottom: 52px;
            transform: translateX(-50%);
            z-index: 2;
            display: inline-flex;
            align-items: center;
            gap: 12px;
        }

        .hero-slider-dots span {
            width: 9px;
            height: 9px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.34);
        }

        .hero-slider-dots span.is-active {
            width: 42px;
            background: #fff;
        }

        .hero-floating-row {
            width: min(1240px, calc(100% - 56px));
            margin: -118px auto 0;
            position: relative;
            z-index: 5;
            display: grid;
            grid-template-columns: minmax(220px, 0.88fr) minmax(420px, 1.32fr) minmax(220px, 0.88fr);
            gap: 20px;
            align-items: end;
        }

        .hero-floating-card {
            padding: 26px;
            min-height: 190px;
            border-radius: 30px;
            border: 1px solid rgba(255, 255, 255, 0.72);
            background: rgba(255, 252, 247, 0.94);
            box-shadow: 0 28px 70px rgba(33, 24, 18, 0.22);
            backdrop-filter: blur(18px);
            transition: transform 180ms ease, box-shadow 180ms ease;
        }

        .hero-floating-card:hover,
        .hero-floating-map:hover {
            transform: translateY(-4px);
            box-shadow: 0 34px 86px rgba(33, 24, 18, 0.27);
        }

        .hero-floating-card small,
        .hero-floating-card strong,
        .hero-floating-card span {
            display: block;
        }

        .hero-floating-card small {
            color: var(--text-soft);
            font-size: 0.74rem;
            font-weight: 800;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        .hero-floating-card strong {
            margin-top: 14px;
            font-size: clamp(2rem, 3.4vw, 3.2rem);
            line-height: 1;
            letter-spacing: -0.04em;
        }

        .hero-floating-card span {
            margin-top: 12px;
            color: var(--text-soft);
            line-height: 1.7;
        }

        .hero-floating-card.is-highlight {
            color: #fff;
            border-color: rgba(255, 255, 255, 0.2);
            background:
                radial-gradient(circle at top right, rgba(255, 255, 255, 0.16), transparent 34%),
                linear-gradient(180deg, #6a412d 0%, #8a5536 58%, #b56a3b 100%);
        }

        .hero-floating-card.is-highlight small,
        .hero-floating-card.is-highlight span {
            color: rgba(255, 249, 241, 0.82);
        }

        .hero-floating-map {
            padding: 18px;
            border-radius: 34px;
            border: 1px solid rgba(255, 255, 255, 0.7);
            background: rgba(255, 252, 247, 0.94);
            box-shadow: 0 30px 80px rgba(33, 24, 18, 0.24);
            backdrop-filter: blur(18px);
            transition: transform 180ms ease, box-shadow 180ms ease;
        }

        .hero-floating-map-header {
            padding: 8px 10px 16px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
        }

        .hero-floating-map-header h2 {
            margin-top: 8px;
            font-size: clamp(1.35rem, 2.2vw, 2rem);
            line-height: 1.06;
            letter-spacing: -0.035em;
        }

        .hero-floating-map-header p {
            margin-top: 8px;
            max-width: 52ch;
            color: var(--text-soft);
            line-height: 1.6;
            font-size: 0.94rem;
        }

        .hero-floating-map .hero-map-frame {
            min-height: 370px;
            border-width: 8px;
            border-radius: 26px;
            box-shadow: inset 0 0 0 1px rgba(76, 48, 33, 0.08);
        }

        .hero-floating-map #map {
            min-height: 370px;
        }

        .hero-showcase {
            margin: -24px calc(var(--page-pad) * -1) 0;
            padding: clamp(34px, 5vw, 72px) 0 clamp(38px, 5vw, 72px);
            position: relative;
            overflow: hidden;
            background:
                linear-gradient(90deg, rgba(8, 12, 12, 0.7) 0%, rgba(8, 12, 12, 0.42) 46%, rgba(8, 12, 12, 0.68) 100%),
                linear-gradient(180deg, rgba(8, 12, 12, 0.18), rgba(8, 12, 12, 0.62)),
                url("{{ asset('images/coffee-hero-banner-new.png') }}") center / cover no-repeat;
        }

        .hero-showcase::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 18% 18%, rgba(255, 255, 255, 0.12), transparent 18%),
                linear-gradient(90deg, rgba(0, 0, 0, 0.34), transparent 58%);
            pointer-events: none;
        }

        .hero-landing-grid {
            width: min(1440px, calc(100% - 64px));
            margin: 0 auto;
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1fr;
            gap: clamp(22px, 3vw, 28px);
            align-items: stretch;
        }

        .hero-gps-panel {
            padding: 20px;
            border-radius: 26px;
            border: 1px solid rgba(255, 255, 255, 0.62);
            background: rgba(255, 252, 247, 0.94);
            box-shadow: 0 32px 90px rgba(0, 0, 0, 0.34);
            backdrop-filter: blur(18px);
            display: grid;
            grid-template-rows: auto 1fr;
            gap: 18px;
            justify-self: stretch;
            align-self: stretch;
            width: 100%;
        }

        .hero-gps-panel .hero-map-frame {
            min-height: 520px;
            border-width: 6px;
            border-radius: 22px;
            box-shadow:
                inset 0 0 0 1px rgba(76, 48, 33, 0.08),
                0 16px 34px rgba(59, 36, 24, 0.12);
        }

        .hero-gps-panel #map {
            min-height: 520px;
        }

        .hero-gps-caption {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 16px;
            align-items: end;
            padding: 0;
        }

        .hero-gps-caption h2 {
            margin-top: 8px;
            font-size: clamp(1.35rem, 2vw, 2rem);
            line-height: 1.06;
            letter-spacing: -0.035em;
        }

        .hero-gps-caption p {
            margin-top: 8px;
            max-width: 72ch;
            color: var(--text-soft);
            line-height: 1.6;
            font-size: 0.96rem;
        }

        .between-map-section {
            width: 100%;
            max-width: none;
            margin: 10px 0 14px;
            position: relative;
            z-index: 2;
            justify-self: stretch;
            padding: clamp(28px, 3vw, 40px);
            border-radius: 34px;
        }

        .between-map-section .hero-gps-caption {
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: end;
            gap: 22px;
        }

        .between-map-section .hero-gps-caption h2 {
            font-size: clamp(2.3rem, 3.6vw, 4rem);
        }

        .between-map-section .hero-gps-caption p {
            max-width: 96ch;
            font-size: 0.98rem;
            line-height: 1.66;
        }

        .between-map-section .hero-map-frame,
        .between-map-section #map {
            min-height: 760px;
        }

        .hero-description-panel {
            padding: clamp(22px, 3vw, 34px);
            border: 1px solid rgba(255, 255, 255, 0.24);
            border-radius: 26px;
            background:
                linear-gradient(180deg, rgba(12, 13, 12, 0.28), rgba(12, 13, 12, 0.18)),
                rgba(255, 255, 255, 0.08);
            box-shadow: 0 28px 70px rgba(0, 0, 0, 0.18);
            backdrop-filter: blur(10px);
            color: #fff;
            text-shadow: 0 20px 50px rgba(0, 0, 0, 0.28);
            justify-self: stretch;
            align-self: stretch;
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .hero-description-panel .eyebrow {
            background: rgba(255, 198, 74, 0.16);
            color: #ffc64a;
            border: 1px solid rgba(255, 198, 74, 0.2);
        }

        .hero-description-panel h1 {
            margin-top: 18px;
            max-width: 18ch;
            font-size: clamp(2.4rem, 4vw, 4.8rem);
            line-height: 1.02;
            letter-spacing: -0.06em;
            text-wrap: balance;
        }

        .hero-description-panel p {
            max-width: 86ch;
            margin-top: 20px;
            color: rgba(255, 255, 255, 0.86);
            line-height: 1.72;
            font-size: clamp(1rem, 1.18vw, 1.12rem);
        }

        .hero-info-cards {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-top: 22px;
        }

        .hero-info-card {
            min-height: 112px;
            padding: 16px 18px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.54);
            background: rgba(255, 252, 247, 0.92);
            box-shadow: 0 24px 54px rgba(0, 0, 0, 0.18);
            color: var(--text-main);
            text-shadow: none;
            backdrop-filter: blur(14px);
        }

        .hero-info-card small,
        .hero-info-card strong,
        .hero-info-card span {
            display: block;
        }

        .hero-info-card small {
            color: var(--text-soft);
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        .hero-info-card strong {
            margin-top: 10px;
            font-size: clamp(1.55rem, 2.1vw, 2.12rem);
            line-height: 1;
            letter-spacing: -0.04em;
        }

        .hero-info-card span {
            margin-top: 10px;
            color: var(--text-soft);
            line-height: 1.45;
            font-size: 0.86rem;
        }

        .hero-info-card.is-highlight {
            color: #fff;
            border-color: rgba(255, 255, 255, 0.22);
            background:
                radial-gradient(circle at top right, rgba(255, 255, 255, 0.18), transparent 34%),
                linear-gradient(180deg, #6a412d 0%, #8a5536 58%, #b56a3b 100%);
        }

        .hero-info-card.is-highlight small,
        .hero-info-card.is-highlight span {
            color: rgba(255, 249, 241, 0.82);
        }

        .about-section {
            display: grid;
            grid-template-columns: minmax(0, 1.18fr) minmax(280px, 0.62fr);
            gap: clamp(24px, 2.2vw, 34px);
            align-items: center;
            justify-content: center;
            padding: clamp(30px, 4vw, 52px);
            overflow: hidden;
        }

        .about-copy {
            justify-self: stretch;
            width: 100%;
            max-width: 980px;
            padding-right: clamp(6px, 1.5vw, 24px);
        }

        .slide-in-up {
            opacity: 0;
            transform: translateY(48px);
            transition:
                opacity 700ms ease,
                transform 700ms cubic-bezier(0.22, 1, 0.36, 1);
        }

        .slide-in-up.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        .about-copy h2 {
            margin-top: 16px;
            max-width: 12ch;
            font-size: clamp(2.6rem, 4.1vw, 4.8rem);
            line-height: 1.02;
            letter-spacing: -0.055em;
            text-align: left;
        }

        .about-copy p {
            max-width: 62ch;
            margin-top: 16px;
            color: var(--text-soft);
            line-height: 1.74;
            font-size: clamp(1rem, 1.08vw, 1.05rem);
            text-align: left;
        }

        .about-copy .eyebrow {
            display: flex;
            width: fit-content;
        }

        .about-points {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-top: 26px;
            max-width: 860px;
        }

        .about-point {
            padding: 16px;
            border-radius: 18px;
            border: 1px solid var(--panel-border);
            background: rgba(255, 249, 241, 0.76);
            text-align: left;
        }

        .about-point strong,
        .about-point span {
            display: block;
        }

        .about-point strong {
            color: var(--espresso);
        }

        .about-point span {
            margin-top: 8px;
            color: var(--text-soft);
            line-height: 1.55;
            font-size: 0.9rem;
        }

        .about-photo-wrap {
            position: relative;
            justify-self: end;
            width: min(100%, 340px);
        }

        .about-photo-wrap::before {
            content: "";
            position: absolute;
            inset: 24px -16px -18px 26px;
            border-radius: 28px;
            background: linear-gradient(135deg, rgba(106, 65, 45, 0.18), rgba(181, 106, 59, 0.28));
            transform: rotate(3deg);
        }

        .about-photo {
            position: relative;
            width: 100%;
            aspect-ratio: 4 / 5;
            object-fit: cover;
            border-radius: 28px;
            border: 10px solid rgba(255, 252, 247, 0.94);
            box-shadow: 0 28px 70px rgba(59, 36, 24, 0.22);
            background: var(--latte);
        }

        .menu-section {
            position: relative;
            overflow: hidden;
            padding: clamp(30px, 4vw, 54px);
            scroll-margin-top: 120px;
            background:
                radial-gradient(circle at top left, rgba(181, 106, 59, 0.2), transparent 26%),
                linear-gradient(135deg, rgba(255, 252, 247, 0.98), rgba(248, 239, 226, 0.94));
        }

        .menu-section::before {
            content: "";
            position: absolute;
            inset: auto -80px -150px auto;
            width: 340px;
            height: 340px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(47, 107, 85, 0.14), transparent 68%);
            pointer-events: none;
        }

        .menu-section-header {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(250px, 0.42fr);
            gap: 24px;
            align-items: end;
        }

        .menu-section-header h2 {
            margin-top: 16px;
            max-width: 12ch;
            font-size: clamp(2.35rem, 4vw, 4.6rem);
            line-height: 1;
            letter-spacing: -0.055em;
        }

        .menu-section-header p {
            max-width: 68ch;
            margin-top: 16px;
            color: var(--text-soft);
            line-height: 1.82;
            font-size: clamp(1rem, 1.15vw, 1.08rem);
        }

        .menu-note {
            justify-self: end;
            padding: 18px;
            border-radius: 24px;
            border: 1px solid rgba(76, 48, 33, 0.12);
            background: rgba(255, 249, 241, 0.72);
            box-shadow: var(--shadow-sm);
        }

        .menu-note strong,
        .menu-note span {
            display: block;
        }

        .menu-note strong {
            font-size: 1.4rem;
            letter-spacing: -0.04em;
        }

        .menu-note span {
            margin-top: 8px;
            color: var(--text-soft);
            line-height: 1.6;
            font-size: 0.94rem;
        }

        .menu-showcase {
            position: relative;
            z-index: 1;
            margin-top: 30px;
        }

        .menu-list {
            display: grid;
            grid-template-columns: 1fr;
            gap: 18px;
            max-width: 1120px;
            margin: 0 auto;
        }

        .menu-card {
            --menu-card-height: 280px;
            position: relative;
            display: grid;
            grid-template-columns: minmax(260px, 0.42fr) minmax(0, 1fr);
            overflow: hidden;
            height: var(--menu-card-height);
            min-height: var(--menu-card-height);
            border-radius: 32px;
            border: 1px solid rgba(76, 48, 33, 0.12);
            background:
                radial-gradient(circle at top left, rgba(181, 106, 59, 0.08), transparent 32%),
                rgba(255, 252, 247, 0.94);
            box-shadow: 0 18px 42px rgba(59, 36, 24, 0.09);
            transition:
                transform 180ms ease,
                border-color 180ms ease,
                box-shadow 180ms ease;
        }

        .menu-card.is-reverse {
            grid-template-columns: minmax(0, 1fr) minmax(240px, 0.42fr);
        }

        .menu-card.is-reverse .menu-card-image-wrap {
            grid-column: 2;
            grid-row: 1;
        }

        .menu-card.is-reverse .menu-card-body {
            grid-column: 1;
            grid-row: 1;
            text-align: right;
        }

        .menu-card.is-reverse .menu-card-title-row {
            flex-direction: row-reverse;
        }

        .menu-card.is-reverse .menu-tags {
            justify-content: flex-end;
        }

        .menu-card.is-reverse .menu-badge {
            right: 14px;
            left: auto;
        }

        .menu-card:hover {
            transform: translateY(-4px);
            border-color: rgba(181, 106, 59, 0.36);
            box-shadow: 0 24px 56px rgba(59, 36, 24, 0.14);
        }

        .menu-card-image-wrap {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            height: 100%;
            min-height: 0;
            background:
                radial-gradient(circle at 50% 44%, rgba(181, 106, 59, 0.16), transparent 46%),
                #fff;
        }

        .menu-card-image {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transition: transform 220ms ease;
        }

        .menu-card:hover .menu-card-image {
            transform: scale(1.05);
        }

        .menu-badge {
            position: absolute;
            top: 14px;
            left: 14px;
            padding: 8px 10px;
            border-radius: 999px;
            background: rgba(255, 252, 247, 0.88);
            color: var(--mocha);
            font-size: 0.74rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            box-shadow: 0 12px 26px rgba(59, 36, 24, 0.12);
        }

        .menu-card-body {
            height: 100%;
            align-self: center;
            padding: clamp(22px, 3vw, 34px);
            display: grid;
            align-content: center;
            gap: 10px;
        }

        .menu-card-title-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
        }

        .menu-card h3 {
            margin: 0;
            font-size: clamp(1.55rem, 2.5vw, 2.2rem);
            line-height: 1.06;
            letter-spacing: -0.035em;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .menu-price {
            flex-shrink: 0;
            padding: 8px 11px;
            border-radius: 999px;
            background: rgba(181, 106, 59, 0.12);
            color: var(--caramel);
            font-weight: 900;
        }

        .menu-card p {
            max-width: none;
            margin: 0;
            color: var(--text-soft);
            line-height: 1.78;
            font-size: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .menu-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 0;
            max-height: 72px;
            overflow: hidden;
        }

        .menu-tags span {
            padding: 7px 10px;
            border-radius: 999px;
            border: 1px solid rgba(76, 48, 33, 0.1);
            background: rgba(255, 249, 241, 0.72);
            color: rgba(59, 36, 24, 0.74);
            font-size: 0.82rem;
            font-weight: 700;
        }

        .menu-empty {
            padding: 20px;
            border-radius: 22px;
            border: 1px dashed rgba(76, 48, 33, 0.22);
            background: rgba(255, 252, 247, 0.9);
            color: var(--text-soft);
            line-height: 1.7;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: var(--accent-soft);
            color: var(--accent);
            font-size: 0.74rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        h1,
        h2,
        h3,
        p {
            margin: 0;
        }

        .hero-card h1 {
            margin-top: 18px;
            max-width: 10ch;
            font-size: clamp(2.4rem, 4.2vw, 4rem);
            line-height: 1;
            letter-spacing: -0.04em;
        }

        .hero-card p {
            margin-top: 18px;
            max-width: 62ch;
            color: var(--text-soft);
            line-height: 1.8;
            font-size: 1rem;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 26px;
        }

        .button-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 14px 22px;
            border-radius: 999px;
            font-weight: 700;
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }

        .button-link:hover {
            transform: translateY(-1px);
        }

        .button-link.primary {
            color: #fff;
            background: linear-gradient(135deg, var(--mocha) 0%, var(--caramel) 100%);
            box-shadow: 0 18px 32px rgba(106, 65, 45, 0.18);
        }

        .button-link.secondary {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid var(--panel-border);
        }

        .hero-side {
            display: grid;
            gap: 16px;
        }

        .hero-status-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 14px;
            margin-top: 24px;
        }

        .stat-card {
            padding: 24px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.88), rgba(255, 249, 241, 0.96)),
                var(--panel);
        }

        .stat-card small,
        .stat-card strong,
        .stat-card span {
            display: block;
        }

        .stat-card small {
            color: var(--text-soft);
            font-size: 0.74rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .stat-card strong {
            margin-top: 14px;
            font-size: clamp(2rem, 4vw, 2.8rem);
            line-height: 1;
        }

        .stat-card span {
            margin-top: 12px;
            color: var(--text-soft);
            line-height: 1.7;
        }

        .stat-card.is-highlight {
            color: #fff;
            background: linear-gradient(180deg, #6a412d 0%, #8a5536 56%, #b56a3b 100%);
            border-color: transparent;
        }

        .stat-card.is-highlight small,
        .stat-card.is-highlight span {
            color: rgba(255, 249, 241, 0.8);
        }

        .map-layout {
            display: grid;
            grid-template-columns: minmax(300px, 0.68fr) minmax(0, 1.32fr);
            gap: 22px;
            align-items: start;
        }

        .panel {
            padding: 28px;
        }

        .panel h2,
        .panel h3 {
            margin-top: 14px;
            font-size: clamp(1.8rem, 3vw, 2.6rem);
            line-height: 1.04;
            letter-spacing: -0.03em;
        }

        .panel p {
            margin-top: 12px;
            color: var(--text-soft);
            line-height: 1.8;
        }

        .overview-points {
            margin-top: 24px;
            display: grid;
            gap: 12px;
        }

        .overview-point {
            padding: 16px 18px;
            border-radius: 22px;
            background: var(--panel-alt);
            border: 1px solid var(--panel-border);
        }

        .overview-point strong,
        .overview-point span {
            display: block;
        }

        .overview-point strong {
            font-size: 0.98rem;
        }

        .overview-point span {
            margin-top: 8px;
            color: var(--text-soft);
            line-height: 1.6;
            font-size: 0.92rem;
        }

        .map-panel {
            overflow: hidden;
            background:
                radial-gradient(circle at top right, rgba(181, 106, 59, 0.12), transparent 24%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 243, 236, 0.96));
        }

        .map-panel-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 18px;
        }

        .live-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 16px;
            border-radius: 999px;
            background: rgba(47, 107, 85, 0.12);
            color: var(--leaf);
            font-weight: 700;
            white-space: nowrap;
        }

        .map-stage {
            position: relative;
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid rgba(76, 48, 33, 0.1);
            background: rgba(255, 252, 247, 0.88);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.5);
        }

        .map-stage::after {
            content: "";
            position: absolute;
            inset: auto 22px 18px auto;
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(181, 106, 59, 0.14), transparent 68%);
            pointer-events: none;
            z-index: 401;
        }

        #map {
            width: 100%;
            height: 100%;
            min-height: 510px;
        }

        .map-avatar-marker {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            color: #fff;
            border: 3px solid rgba(255, 250, 242, 0.96);
            box-shadow: 0 14px 28px rgba(20, 18, 16, 0.28);
            position: relative;
        }

        .map-avatar-marker::after {
            content: "";
            position: absolute;
            left: 50%;
            bottom: -10px;
            width: 16px;
            height: 16px;
            background: inherit;
            border-right: inherit;
            border-bottom: inherit;
            transform: translateX(-50%) rotate(45deg);
            border-radius: 0 0 6px 0;
            box-sizing: border-box;
        }

        .map-avatar-marker svg {
            width: 20px;
            height: 20px;
            position: relative;
            z-index: 1;
        }

        .map-avatar-marker.is-user {
            background: linear-gradient(135deg, #2d63e2, #6f9bff);
            box-shadow:
                0 0 0 8px rgba(45, 99, 226, 0.18),
                0 14px 28px rgba(45, 99, 226, 0.28);
        }

        .map-avatar-marker.is-driver {
            background: linear-gradient(135deg, #6a412d, #b56a3b);
            box-shadow:
                0 0 0 8px rgba(181, 106, 59, 0.18),
                0 14px 28px rgba(59, 36, 24, 0.28);
        }

        .history-list {
            list-style: none;
            margin: 24px 0 0;
            padding: 0;
            display: grid;
            gap: 12px;
            max-height: 460px;
            overflow: auto;
        }

        .history-list li {
            padding: 18px;
            border-radius: 20px;
            border: 1px solid var(--panel-border);
            background: rgba(255, 255, 255, 0.92);
        }

        .history-list strong {
            display: block;
            font-size: 1rem;
        }

        .history-coords {
            margin-top: 8px;
            color: var(--text-soft);
            line-height: 1.7;
            font-size: 0.92rem;
        }

        .history-list small {
            display: block;
            margin-top: 10px;
            color: rgba(59, 36, 24, 0.56);
        }

        footer {
            padding: 4px 6px 2px;
            text-align: center;
            color: var(--text-soft);
            font-size: 0.92rem;
        }

        .leaflet-container {
            background: #f5efe6;
            font-family: Figtree, sans-serif;
        }

        .leaflet-control-zoom {
            border: 0 !important;
            border-radius: 18px !important;
            overflow: hidden;
            box-shadow: 0 16px 28px rgba(59, 36, 24, 0.18) !important;
        }

        .leaflet-control-zoom a {
            width: 42px !important;
            height: 42px !important;
            line-height: 42px !important;
            color: var(--espresso) !important;
            background: rgba(255, 255, 255, 0.98) !important;
            border-bottom: 1px solid rgba(76, 48, 33, 0.08) !important;
        }

        .leaflet-control-attribution {
            border-radius: 14px 0 0 0;
            background: rgba(255, 249, 241, 0.88) !important;
            backdrop-filter: blur(10px);
        }

        @media (max-width: 1100px) {
            .hero-grid,
            .map-layout,
            .hero-floating-row,
            .hero-landing-grid,
            .menu-section-header,
            .menu-showcase {
                grid-template-columns: 1fr;
            }

            .hero-showcase {
                margin-left: calc(var(--page-pad) * -1);
                margin-right: calc(var(--page-pad) * -1);
            }

            .hero-landing-grid {
                width: min(760px, calc(100% - 36px));
                min-height: auto;
            }

            .between-map-section {
                width: 100%;
                margin-top: 8px;
                padding: 24px;
            }

            .between-map-section .hero-map-frame,
            .between-map-section #map {
                min-height: 560px;
            }

            .hero-floating-row {
                width: min(720px, calc(100% - 36px));
                margin-top: -96px;
            }

            .hero-floating-map {
                order: -1;
            }

            .hero-gps-panel .hero-map-frame,
            .hero-gps-panel #map {
                min-height: 440px;
            }

            #map,
            .hero-map-frame {
                height: 360px;
                min-height: 360px;
            }

            .menu-note {
                justify-self: stretch;
            }

        }

        @media (max-width: 760px) {
            .tracker-shell {
                width: 100%;
                margin: 0;
                border-radius: 0;
            }

            .tracker-topbar,
            .tracker-content {
                padding-left: var(--page-pad);
                padding-right: var(--page-pad);
            }

            .tracker-topbar {
                padding-top: 12px;
                padding-bottom: 12px;
                align-items: center;
                flex-direction: row;
            }

            .tracker-brand-copy span {
                display: none;
            }

            .mobile-nav-toggle {
                display: inline-grid;
                margin-left: auto;
            }

            .hero-card,
            .hero-map-card,
            .hero-floating-card,
            .hero-floating-map,
            .hero-gps-panel,
            .panel,
            .stat-card {
                padding: 20px;
                border-radius: 24px;
            }

            .hero-gps-panel {
                padding: 12px;
                border-radius: 18px;
            }

            .hero-description-panel {
                border-radius: 20px;
            }

            .hero-card {
                padding: 0;
            }

            .hero-copy {
                padding: 22px;
            }

            .hero-banner {
                min-height: 130px;
            }

            .hero-showcase {
                margin: -18px calc(var(--page-pad) * -1) 0;
                padding: 38px 0;
                background-position: 32% center;
            }

            .hero-banner-slide {
                min-height: 520px;
                padding: 58px 22px 142px;
            }

            .hero-slider-dots {
                bottom: 36px;
            }

            .hero-floating-row {
                width: min(100% - 24px, 620px);
                margin-top: -86px;
                gap: 14px;
            }

            .hero-floating-map-header {
                flex-direction: column;
            }

            .hero-landing-grid {
                width: min(100% - 24px, 620px);
                gap: 18px;
            }

            .between-map-section {
                width: 100%;
                padding: 16px;
            }

            .hero-gps-caption {
                grid-template-columns: 1fr;
                align-items: start;
            }

            .hero-description-panel h1 {
                max-width: 14ch;
                font-size: clamp(2.6rem, 14vw, 4.4rem);
            }

            .hero-info-cards {
                grid-template-columns: 1fr;
            }

            .about-section,
            .about-points {
                grid-template-columns: 1fr;
            }

            .about-section {
                gap: 22px;
            }

            .about-photo-wrap {
                justify-self: stretch;
                width: min(100%, 360px);
                margin: 0 auto;
            }

            .menu-section {
                padding: 22px;
            }

            .menu-section-header h2 {
                max-width: 10ch;
                font-size: clamp(2.2rem, 13vw, 3.8rem);
            }

            .menu-list {
                gap: 14px;
            }

            .menu-card,
            .menu-card.is-reverse {
                grid-template-columns: 1fr;
                grid-template-rows: 190px auto;
                height: auto;
                min-height: 0;
            }

            .menu-card.is-reverse .menu-card-image-wrap,
            .menu-card.is-reverse .menu-card-body {
                grid-column: 1;
                grid-row: auto;
            }

            .menu-card-image-wrap {
                height: 190px;
            }

            .menu-card-body {
                height: auto;
                align-content: start;
                gap: 8px;
            }

            .menu-card p {
                -webkit-line-clamp: 3;
            }

            .menu-card.is-reverse .menu-card-body {
                text-align: left;
            }

            .menu-card.is-reverse .menu-card-title-row {
                flex-direction: row;
            }

            .menu-card.is-reverse .menu-tags {
                justify-content: flex-start;
            }

            .menu-card.is-reverse .menu-badge {
                right: auto;
                left: 14px;
            }

            .tracker-topbar-actions {
                display: none;
                position: absolute;
                top: calc(100% + 10px);
                left: var(--page-pad);
                right: var(--page-pad);
                width: auto;
                padding: 14px;
                border: 1px solid var(--panel-border);
                border-radius: 22px;
                background: rgba(255, 250, 242, 0.97);
                box-shadow: 0 24px 60px rgba(59, 36, 24, 0.18);
                backdrop-filter: blur(18px);
                justify-content: flex-start;
                align-items: stretch;
                flex-direction: column;
                z-index: 1001;
            }

            .tracker-topbar-actions.is-open {
                display: flex;
            }

            .tracker-chip,
            .tracker-link,
            .tracker-auth,
            .button-link {
                width: 100%;
                justify-content: center;
            }

            .map-panel-header {
                flex-direction: column;
            }

            #map,
            .hero-map-frame {
                height: 420px;
                min-height: 420px;
            }

            .hero-floating-map .hero-map-frame {
                min-height: 320px;
            }

            .hero-floating-map #map {
                min-height: 320px;
            }

            .hero-gps-panel .hero-map-frame,
            .hero-gps-panel #map {
                min-height: 420px;
            }

            .between-map-section .hero-map-frame,
            .between-map-section #map {
                min-height: 460px;
            }
        }
    </style>
</head>
<body>
    <div class="tracker-shell">
        <header class="tracker-topbar">
            <div class="tracker-brand">
                <img
                    class="tracker-brand-mark"
                    src="{{ asset('images/ada-coffee-logo.png') }}"
                    alt="AD.A Coffee"
                >
                <div class="tracker-brand-copy">
                    <strong>AD.A Coffee</strong>
                    <span>Peta publik dengan bahasa visual yang selaras dengan dashboard internal.</span>
                </div>
            </div>

            <button
                class="mobile-nav-toggle"
                id="mobile-nav-toggle"
                type="button"
                aria-label="Buka menu navigasi"
                aria-expanded="false"
                aria-controls="tracker-nav-actions"
            >
                <span></span>
            </button>

            <div class="tracker-topbar-actions" id="tracker-nav-actions">
                <div class="tracker-chip">
                    <div>
                        <small>Status</small>
                        <strong><span id="active-unit-count">{{ count($activeUnits) }}</span> gerobak aktif</strong>
                    </div>
                </div>

                <a href="#menu" class="tracker-link">Menu</a>

                @auth
                    <a href="{{ route('dashboard') }}" class="tracker-link">Buka Dashboard</a>

                    <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                        @csrf
                        <button type="submit" class="tracker-auth">Logout</button>
                    </form>
                @else
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="tracker-auth is-primary">Login Dashboard</a>
                    @endif
                @endauth
            </div>
        </header>

        <main class="tracker-content">
            <section class="hero-showcase" id="beranda">
                <div class="hero-landing-grid" id="lacak">
                    <section class="hero-description-panel">
                        <span class="eyebrow">Kopi Keliling Tracker</span>
                        <h1>Kopling terdekat langsung terlihat.</h1>
                        <p>
                            Temukan gerobak Kopi Keliling dari halaman depan. Panel utama di atas merangkum info
                            lokasi, lalu card peta di tengah halaman menampilkan posisimu, Kopling aktif, dan jarak
                            terdekat secara realtime dari browser.
                        </p>

                        <div class="hero-actions">
                            @auth
                                <a href="{{ route('dashboard') }}" class="button-link secondary">Masuk ke Dashboard</a>
                            @elseif (Route::has('login'))
                                <a href="{{ route('login') }}" class="button-link secondary">Masuk sebagai Admin</a>
                            @endif
                        </div>

                        <div class="hero-info-cards">
                            <article class="hero-info-card is-highlight">
                                <small>Kopling tersedia</small>
                                <strong><span id="active-unit-count-hero">{{ count($activeUnits) }}</span></strong>
                                <span>Unit Kopi Keliling yang sedang online dan mengirim lokasi terbaru.</span>
                            </article>

                            <article class="hero-info-card">
                                <small>Kopling terdekat</small>
                                <strong id="nearest-distance">Mencari...</strong>
                                <span id="nearest-copy">Aktifkan izin lokasi browser untuk melihat jarak gerobak paling dekat.</span>
                            </article>
                        </div>
                    </section>
                </div>
            </section>

            <section class="panel about-section slide-in-up" id="tentang">
                <div class="about-copy">
                    <span class="eyebrow">About Us</span>
                    <h2>Kopling lahir dari gerobak kopi yang dekat dengan pelanggan.</h2>
                    <p>
                        Kopi Keliling bukan hanya soal menjual minuman, tetapi tentang membuat kopi lebih mudah
                        ditemukan di titik ramai kota. Melalui tracker GPS ini, pelanggan bisa melihat gerobak
                        yang aktif, mendekat ke lokasi terdekat, dan menikmati kopi tanpa harus menebak posisi penjual.
                    </p>
                    <p>
                        Kami menggabungkan gerobak sederhana, menu yang familiar, dan teknologi realtime agar usaha
                        kecil terasa lebih modern, transparan, dan siap menjangkau lebih banyak pelanggan.
                    </p>

                    <div class="about-points">
                        <div class="about-point">
                            <strong>Dekat</strong>
                            <span>Gerobak hadir di sekitar aktivitas harian pelanggan.</span>
                        </div>
                        <div class="about-point">
                            <strong>Realtime</strong>
                            <span>Lokasi Kopling aktif diperbarui langsung lewat sistem GPS.</span>
                        </div>
                        <div class="about-point">
                            <strong>Sederhana</strong>
                            <span>Konsep gerobak tetap ringan, tapi pengalamannya dibuat digital.</span>
                        </div>
                    </div>
                </div>

                <div class="about-photo-wrap">
                    <img
                        class="about-photo"
                        src="{{ asset('images/about-cart.jpg') }}"
                        alt="Gerobak kopi keliling"
                    >
                </div>
            </section>

            <section class="hero-gps-panel between-map-section slide-in-up" id="lacak">
                <div class="hero-gps-caption">
                    <div>
                        <span class="eyebrow">GPS Live Map</span>
                        <h2>Peta Kopling terdekat.</h2>
                        <p>
                            Card peta sekarang berada tepat di bawah section About Us dan di atas Katalog Menu, dengan
                            posisi tengah supaya jadi jembatan visual antar dua section utama. Izinkan lokasi browser
                            agar ikon kamu dan driver aktif langsung terlihat di area peta.
                        </p>
                    </div>

                    <div class="live-pill">GPS aktif otomatis</div>
                </div>

                <div class="hero-map-frame">
                    <div id="map"></div>
                </div>
            </section>

            <section class="panel menu-section slide-in-up" id="menu">
                <div class="menu-section-header">
                    <div>
                        <span class="eyebrow">Katalog Menu</span>
                        <h2>Menu gerobak yang siap menemani hari.</h2>
                        <p>
                            Pilih menu favorit AD.A Coffee langsung dari katalog ini. Dari kopi hitam yang ringan,
                            signature gula aren, sampai non coffee yang manis dan creamy.
                        </p>
                    </div>

                    <div class="menu-note">
                        <strong>
                            @if ($menuStartingPrice !== null)
                                Mulai Rp{{ number_format((int) $menuStartingPrice, 0, ',', '.') }}
                            @else
                                Katalog sedang disiapkan
                            @endif
                        </strong>
                        <span>Harga dan varian menu mengikuti katalog terbaru dari dashboard owner.</span>
                    </div>
                </div>

                <div class="menu-showcase">
                    <div class="menu-list">
                        @forelse ($menuCatalog as $menu)
                            @php
                                $imagePath = $menu->image_path;
                                $menuImage = blank($imagePath)
                                    ? asset('images/about-cart.jpg')
                                    : (\Illuminate\Support\Str::startsWith($imagePath, ['http://', 'https://'])
                                        ? $imagePath
                                        : asset(ltrim($imagePath, '/')));
                            @endphp
                            <article class="menu-card {{ $loop->even ? 'is-reverse' : '' }}">
                                <div class="menu-card-image-wrap">
                                    <img
                                        class="menu-card-image"
                                        src="{{ $menuImage }}"
                                        alt="{{ $menu->name }}"
                                    >
                                    <span class="menu-badge">{{ $menu->category }}</span>
                                </div>
                                <div class="menu-card-body">
                                    <div class="menu-card-title-row">
                                        <h3>{{ $menu->name }}</h3>
                                        <span class="menu-price">Rp{{ number_format((int) $menu->price, 0, ',', '.') }}</span>
                                    </div>
                                    <p>{{ $menu->description ?: 'Menu favorit pelanggan Kopi Keliling siap dipesan dari gerobak terdekat.' }}</p>
                                    @if (! empty($menu->tags))
                                        <div class="menu-tags">
                                            @foreach ($menu->tags as $tag)
                                                <span>{{ $tag }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <div class="menu-empty">
                                Belum ada menu aktif saat ini. Owner bisa menambahkan menu dari Dashboard Owner ke halaman Katalog Menu.
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>

            <section class="panel">
                <span class="eyebrow">Lokasi Terbaru</span>
                <h3>Daftar posisi terakhir per gerobak aktif.</h3>
                <p>Panel ini akan selalu menampilkan pembaruan lokasi terbaru yang berhasil masuk ke sistem.</p>
                <ul id="history-list" class="history-list"></ul>
            </section>

            <footer>
                Kopi Keliling Tracker menggunakan Laravel, Traccar Android, dan Leaflet.js untuk membantu pelanggan menemukan gerobak kopi.
            </footer>
        </main>
    </div>

    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""
    ></script>
    <script>
        const state = {
            locations: @json($locations),
            activeUnits: @json($activeUnits),
            markers: [],
            userMarker: null,
            userLatLng: null,
            map: null,
            hasAutoFramedMap: false,
        };

        const endpoints = {
            latest: @json(route('api.location.latest')),
        };

        const mobileNavToggle = document.getElementById('mobile-nav-toggle');
        const trackerNavActions = document.getElementById('tracker-nav-actions');
        const slideInElements = document.querySelectorAll('.slide-in-up');

        function closeMobileNav() {
            if (!mobileNavToggle || !trackerNavActions) {
                return;
            }

            mobileNavToggle.classList.remove('is-open');
            trackerNavActions.classList.remove('is-open');
            mobileNavToggle.setAttribute('aria-expanded', 'false');
        }

        if (mobileNavToggle && trackerNavActions) {
            mobileNavToggle.addEventListener('click', () => {
                const isOpen = trackerNavActions.classList.toggle('is-open');

                mobileNavToggle.classList.toggle('is-open', isOpen);
                mobileNavToggle.setAttribute('aria-expanded', String(isOpen));
            });

            trackerNavActions.querySelectorAll('a, button[type="submit"]').forEach((item) => {
                item.addEventListener('click', closeMobileNav);
            });

            document.addEventListener('click', (event) => {
                if (
                    !trackerNavActions.contains(event.target) &&
                    !mobileNavToggle.contains(event.target)
                ) {
                    closeMobileNav();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeMobileNav();
                }
            });
        }

        if ('IntersectionObserver' in window) {
            const slideObserver = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        slideObserver.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.2,
            });

            slideInElements.forEach((element) => slideObserver.observe(element));
        } else {
            slideInElements.forEach((element) => element.classList.add('is-visible'));
        }

        const map = L.map('map', {
            zoomControl: false,
        }).setView([-2.5489, 118.0149], 5);
        state.map = map;

        L.control.zoom({
            position: 'bottomright',
        }).addTo(map);

        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19,
            detectRetina: true,
            attribution: 'Tiles &copy; Esri &mdash; Source: Esri, Maxar, Earthstar Geographics, and the GIS User Community',
        }).addTo(map);

        const driverIcon = L.divIcon({
            className: 'driver-location-marker-wrapper',
            html: `
                <div class="map-avatar-marker is-driver" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="8" r="4" fill="currentColor"></circle>
                        <path d="M5.5 19.5c1.8-3.4 4.2-5.1 6.5-5.1s4.7 1.7 6.5 5.1" fill="currentColor"></path>
                    </svg>
                </div>
            `,
            iconSize: [44, 54],
            iconAnchor: [22, 48],
            popupAnchor: [0, -40],
        });

        const userIcon = L.divIcon({
            className: 'user-location-marker-wrapper',
            html: `
                <div class="map-avatar-marker is-user" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="8" r="4" fill="currentColor"></circle>
                        <path d="M5.5 19.5c1.8-3.4 4.2-5.1 6.5-5.1s4.7 1.7 6.5 5.1" fill="currentColor"></path>
                    </svg>
                </div>
            `,
            iconSize: [44, 54],
            iconAnchor: [22, 48],
            popupAnchor: [0, -40],
        });

        function updateSummary() {
            document.getElementById('active-unit-count').textContent = state.activeUnits.length;
            document.getElementById('active-unit-count-hero').textContent = state.activeUnits.length;
        }

        function calculateDistanceKm(from, to) {
            const earthRadiusKm = 6371;
            const toRadians = (degrees) => degrees * Math.PI / 180;
            const latDelta = toRadians(to.lat - from.lat);
            const lngDelta = toRadians(to.lng - from.lng);
            const startLat = toRadians(from.lat);
            const endLat = toRadians(to.lat);

            const haversine =
                Math.sin(latDelta / 2) ** 2 +
                Math.cos(startLat) * Math.cos(endLat) * Math.sin(lngDelta / 2) ** 2;

            return earthRadiusKm * 2 * Math.atan2(Math.sqrt(haversine), Math.sqrt(1 - haversine));
        }

        function formatDistance(distanceKm) {
            if (distanceKm < 1) {
                return `${Math.round(distanceKm * 1000)} m`;
            }

            return `${distanceKm.toFixed(distanceKm < 10 ? 1 : 0)} km`;
        }

        function updateNearestUnit() {
            const distanceEl = document.getElementById('nearest-distance');
            const copyEl = document.getElementById('nearest-copy');

            if (!state.userLatLng) {
                distanceEl.textContent = 'Mencari...';
                copyEl.textContent = 'Aktifkan izin lokasi browser untuk melihat jarak gerobak paling dekat.';
                return;
            }

            if (!state.activeUnits.length) {
                distanceEl.textContent = '-';
                copyEl.textContent = 'Belum ada Kopling yang tersedia di peta saat ini.';
                return;
            }

            const nearest = state.activeUnits
                .filter((unit) => unit.latitude !== null && unit.longitude !== null)
                .map((unit) => ({
                    ...unit,
                    distanceKm: calculateDistanceKm(state.userLatLng, {
                        lat: Number(unit.latitude),
                        lng: Number(unit.longitude),
                    }),
                }))
                .sort((a, b) => a.distanceKm - b.distanceKm)[0];

            if (!nearest) {
                distanceEl.textContent = '-';
                copyEl.textContent = 'Data koordinat Kopling belum lengkap untuk menghitung jarak.';
                return;
            }

            distanceEl.textContent = formatDistance(nearest.distanceKm);
            copyEl.textContent = `${nearest.unit_name || nearest.device_id || 'Kopling'} adalah Kopling terdekat dari lokasimu.`;
        }

        function renderHistory() {
            const list = document.getElementById('history-list');
            list.innerHTML = '';

            if (!state.activeUnits.length) {
                const item = document.createElement('li');
                item.textContent = 'Belum ada lokasi realtime yang masuk.';
                list.appendChild(item);
                return;
            }

            [...state.activeUnits]
                .sort((a, b) => (b.recorded_at || '').localeCompare(a.recorded_at || ''))
                .forEach((location) => {
                    const item = document.createElement('li');
                    item.innerHTML = `
                        <strong>${location.unit_name || location.device_id || 'Traccar Device'}</strong>
                        <div class="history-coords">${location.driver_name ? `Driver: ${location.driver_name}` : 'Driver belum di-assign'}</div>
                        <div class="history-coords">${location.unit_code ? `Kode: ${location.unit_code}` : 'Kode unit belum tersedia'}</div>
                        <div class="history-coords">${location.latitude}, ${location.longitude}</div>
                        <small>${location.recorded_at ?? 'Belum tersimpan ke database'}</small>
                    `;
                    list.appendChild(item);
                });
        }

        function buildDisplayPositions(locations) {
            const groups = new Map();

            locations.forEach((location) => {
                const key = `${location.latitude},${location.longitude}`;

                if (!groups.has(key)) {
                    groups.set(key, []);
                }

                groups.get(key).push(location);
            });

            return locations.map((location) => {
                const key = `${location.latitude},${location.longitude}`;
                const group = groups.get(key) || [location];
                const index = group.indexOf(location);

                if (group.length === 1 || index === -1) {
                    return {
                        ...location,
                        displayLatitude: location.latitude,
                        displayLongitude: location.longitude,
                    };
                }

                const angle = (Math.PI * 2 * index) / group.length;
                const distance = 0.000045;

                return {
                    ...location,
                    displayLatitude: location.latitude + Math.sin(angle) * distance,
                    displayLongitude: location.longitude + Math.cos(angle) * distance,
                };
            });
        }

        function renderMap() {
            const displayLocations = buildDisplayPositions(state.activeUnits);
            const latLngs = displayLocations.map((location) => [location.displayLatitude, location.displayLongitude]);
            const boundsLatLngs = [...latLngs];

            state.markers.forEach((marker) => marker.remove());
            state.markers = [];

            if (!latLngs.length) {
                if (state.userLatLng && !state.hasAutoFramedMap) {
                    map.setView([state.userLatLng.lat, state.userLatLng.lng], 15);
                    state.hasAutoFramedMap = true;
                }

                renderHistory();
                updateSummary();
                updateNearestUnit();
                return;
            }

            displayLocations.forEach((unitLocation) => {
                const marker = L.marker([unitLocation.displayLatitude, unitLocation.displayLongitude], {
                    icon: driverIcon,
                }).addTo(map).bindPopup(`
                    <strong>${unitLocation.unit_name || 'Gerobak Kopi'}</strong><br>
                    Driver: ${unitLocation.driver_name || '-'}<br>
                    Device: ${unitLocation.device_id || '-'}<br>
                    Lat: ${unitLocation.latitude}<br>
                    Lng: ${unitLocation.longitude}<br>
                    Battery: ${unitLocation.battery_level !== null ? `${unitLocation.battery_level}%` : '-'}<br>
                    Updated: ${unitLocation.recorded_at || '-'}
                `);

                marker.on('click', () => {
                    map.flyTo([unitLocation.displayLatitude, unitLocation.displayLongitude], Math.max(map.getZoom(), 18), {
                        animate: true,
                        duration: 0.8,
                    });
                });

                state.markers.push(marker);
            });

            if (state.userLatLng) {
                boundsLatLngs.push([state.userLatLng.lat, state.userLatLng.lng]);
            }

            if (!state.hasAutoFramedMap) {
                if (boundsLatLngs.length === 1) {
                    map.setView(latLngs[0], 17);
                } else if (boundsLatLngs.length > 1) {
                    map.fitBounds(boundsLatLngs, { padding: [34, 34] });
                }

                state.hasAutoFramedMap = true;
            }

            requestAnimationFrame(() => {
                map.invalidateSize();
            });

            renderHistory();
            updateSummary();
            updateNearestUnit();
        }

        function setUserLocation(position) {
            state.userLatLng = {
                lat: position.coords.latitude,
                lng: position.coords.longitude,
            };

            if (!state.userMarker) {
                state.userMarker = L.marker([state.userLatLng.lat, state.userLatLng.lng], {
                    icon: userIcon,
                    zIndexOffset: 1000,
                }).addTo(map).bindPopup('Posisi kamu saat ini');

                state.userMarker.on('click', () => {
                    map.flyTo([state.userLatLng.lat, state.userLatLng.lng], Math.max(map.getZoom(), 18), {
                        animate: true,
                        duration: 0.8,
                    });
                });
            } else {
                state.userMarker.setLatLng([state.userLatLng.lat, state.userLatLng.lng]);
            }

            renderMap();
        }

        function requestUserLocation() {
            if (!navigator.geolocation) {
                document.getElementById('nearest-distance').textContent = '-';
                document.getElementById('nearest-copy').textContent = 'Browser ini belum mendukung deteksi lokasi.';
                return;
            }

            navigator.geolocation.watchPosition(
                setUserLocation,
                () => {
                    updateNearestUnit();
                },
                {
                    enableHighAccuracy: true,
                    maximumAge: 30000,
                    timeout: 10000,
                },
            );
        }

        async function refreshLocations() {
            const response = await fetch(endpoints.latest, {
                headers: {
                    Accept: 'application/json',
                },
            });

            const payload = await response.json();

            if (!response.ok) {
                throw new Error(payload.message || 'Gagal mengambil lokasi terbaru.');
            }

            state.locations = payload.locations || [];
            state.activeUnits = payload.active_units || [];
            renderMap();
        }

        renderMap();
        requestUserLocation();
        refreshLocations().catch(() => null);
        setInterval(() => {
            refreshLocations().catch(() => null);
        }, 8000);
    </script>
</body>
</html>
