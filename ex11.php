<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SkyWings - Airline & Cargo Services</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <link
      href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css"
      rel="stylesheet"
    />
    <style>
      :where([class^="ri-"])::before {
        content: "\f3c2";
      }
      body {
        font-family: "Inter", sans-serif;
        margin: 0;
      }
      /* Header */
      header {
        background-color: #ffffff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      }
      header .container {
        max-width: 1280px;
        margin-left: auto;
        margin-right: auto;
        padding-left: 16px;
        padding-right: 16px;
        padding-top: 16px;
        padding-bottom: 16px;
      }
      header .flex {
        display: flex;
        justify-content: center;
      }
      header a {
        font-family: "Pacifico", cursive;
        font-size: 2.25rem;
        color: #0b4d75;
        text-decoration: none;
      }

      /* Hero Section */
      .hero-section {
        position: relative;
      }
      .hero-gradient {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to right, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.3));
        z-index: 10;
      }
      .hero-bg {
        height: 600px;
        width: 100%;
        background-image: url("https://picsum.photos/1920/800?image=1080"); /* Placeholder airplane image */
        background-size: cover;
        background-position: center;
      }
      .hero-content {
        max-width: 1280px;
        margin-left: auto;
        margin-right: auto;
        padding-left: 16px;
        padding-right: 16px;
        position: relative;
        z-index: 20;
      }
      .hero-text {
        position: absolute;
        top: 50%;
        left: 0;
        transform: translateY(-50%);
        max-width: 640px;
        padding-left: 16px;
        padding-right: 16px;
        color: #ffffff;
      }
      .hero-text h1 {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 16px;
      }
      .hero-text p {
        font-size: 1.125rem;
        margin-bottom: 32px;
      }
      .hero-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
      }
      .btn-primary {
        background-color: #0b4d75;
        color: #ffffff;
        padding: 12px 24px;
        font-weight: 500;
        border-radius: 8px;
        text-decoration: none;
        white-space: nowrap;
        transition: background-color 0.2s;
      }
      .btn-primary:hover {
        background-color: rgba(11, 77, 117, 0.9);
      }
      .btn-secondary {
        background-color: #ffffff;
        color: #0b4d75;
        padding: 12px 24px;
        font-weight: 500;
        border-radius: 8px;
        text-decoration: none;
        white-space: nowrap;
        transition: background-color 0.2s;
      }
      .btn-secondary:hover {
        background-color: #f7fafc;
      }
      @media (max-width: 768px) {
        .hero-text h1 {
          font-size: 2.25rem;
        }
      }

      /* Search Panel */
      .search-section {
        background-color: #ffffff;
        padding-top: 24px;
        padding-bottom: 24px;
      }
      .search-container {
        max-width: 1280px;
        margin-left: auto;
        margin-right: auto;
        padding-left: 16px;
        padding-right: 16px;
      }
      .search-panel {
        background-color: #ffffff;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        padding: 24px;
        margin-top: -96px;
        position: relative;
        z-index: 30;
      }
      .search-tabs {
        display: flex;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 24px;
      }
      .tab {
        padding: 12px 16px;
        font-weight: 500;
        font-size: 1rem;
        white-space: nowrap;
        cursor: pointer;
      }
      .tab-active {
        color: #0052cc;
        border-bottom: 2px solid #0052cc;
      }
      .tab-inactive {
        color: #6b7280;
      }
      .search-form {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
        margin-bottom: 16px;
      }
      @media (min-width: 768px) {
        .search-form {
          grid-template-columns: repeat(3, 1fr);
        }
      }
      .form-group {
        position: relative;
      }
      .form-group label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 4px;
      }
      .input-icon {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        padding-left: 12px;
        display: flex;
        align-items: center;
        pointer-events: none;
      }
      .input-icon div {
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
      }
      .form-input {
        width: 100%;
        padding: 8px 12px 8px 40px;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        font-size: 0.875rem;
        transition: border-color 0.2s, box-shadow 0.2s;
      }
      .form-input:focus {
        outline: none;
        border-color: #0b4d75;
        box-shadow: 0 0 0 3px rgba(11, 77, 117, 0.2);
      }
      .form-input[type="date"] {
        padding-left: 40px;
      }
      .form-submit {
        display: flex;
        justify-content: flex-end;
      }
      .search-btn {
        background-color: #0b4d75;
        color: #ffffff;
        padding: 8px 24px;
        font-weight: 500;
        border-radius: 8px;
        border: none;
        white-space: nowrap;
        cursor: pointer;
        transition: background-color 0.2s;
      }
      .search-btn:hover {
        background-color: rgba(11, 77, 117, 0.9);
      }

      /* Service Highlights */
      .services-section {
        padding-top: 64px;
        padding-bottom: 64px;
        background-color: #f7fafc;
      }
      .services-container {
        max-width: 1280px;
        margin-left: auto;
        margin-right: auto;
        padding-left: 16px;
        padding-right: 16px;
      }
      .services-title {
        font-size: 1.875rem;
        font-weight: 700;
        text-align: center;
        margin-bottom: 48px;
      }
      .services-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 32px;
      }
      @media (min-width: 768px) {
        .services-grid {
          grid-template-columns: repeat(3, 1fr);
        }
      }
      .service-card {
        background-color: #ffffff;
        padding: 24px;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: box-shadow 0.2s;
        text-align: center;
      }
      .service-card:hover {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
      }
      .service-icon {
        width: 64px;
        height: 64px;
        background-color: rgba(11, 77, 117, 0.1);
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        margin-left: auto;
        margin-right: auto;
      }
      .service-icon div {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0b4d75;
        font-size: 1.5rem;
      }
      .service-card h3 {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 12px;
      }
      .service-card p {
        color: #4b5563;
        margin-bottom: 24px;
      }
      .service-card button {
        color: #0b4d75;
        font-weight: 500;
        border-radius: 8px;
        background: none;
        border: none;
        cursor: pointer;
        white-space: nowrap;
        transition: color 0.2s;
      }
      .service-card button:hover {
        color: rgba(11, 77, 117, 0.8);
      }

      /* Featured Destinations */
      .destinations-section {
        padding-top: 64px;
        padding-bottom: 64px;
        background-color: #ffffff;
      }
      .destinations-container {
        max-width: 1280px;
        margin-left: auto;
        margin-right: auto;
        padding-left: 16px;
        padding-right: 16px;
      }
      .destinations-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
      }
      .destinations-title {
        font-size: 1.875rem;
        font-weight: 700;
      }
      .scroll-buttons {
        display: flex;
        gap: 8px;
      }
      .scroll-btn {
        width: 40px;
        height: 40px;
        border-radius: 9999px;
        border: 1px solid #d1d5db;
        display: flex;
        align-items: center;
        justify-content: center;
        background: none;
        cursor: pointer;
        transition: background-color 0.2s;
      }
      .scroll-btn:hover {
        background-color: #f3f4f6;
      }
      .scroll-btn div {
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .destination-scroll {
        overflow-x: auto;
        padding-bottom: 16px;
      }
      .destination-scroll::-webkit-scrollbar {
        height: 6px;
      }
      .destination-scroll::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
      }
      .destination-scroll::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
      }
      .destination-scroll::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
      }
      .destinations-list {
        display: flex;
        gap: 24px;
        min-width: max-content;
      }
      .destination-card {
        position: relative;
        width: 256px;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      }
      .destination-card img {
        width: 100%;
        height: 160px;
        object-fit: cover;
        object-position: top;
      }
      .destination-card .card-content {
        padding: 16px;
      }
      .destination-card h3 {
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 4px;
      }
      .destination-info {
        display: flex;
        align-items: center;
        font-size: 0.875rem;
        color: #4b5563;
        margin-bottom: 8px;
      }
      .destination-info div {
        width: 16px;
        height: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 4px;
      }
      .destination-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
      }
      .destination-footer p {
        font-weight: 500;
      }
      .destination-footer .price {
        color: #0b4d75;
      }
      .quick-book {
        opacity: 0;
        background-color: #0b4d75;
        color: #ffffff;
        font-size: 0.875rem;
        padding: 4px 12px;
        border-radius: 8px;
        border: none;
        white-space: nowrap;
        cursor: pointer;
        transition: opacity 0.2s, background-color 0.2s;
      }
      .destination-card:hover .quick-book {
        opacity: 1;
      }
      .quick-book:hover {
        background-color: rgba(11, 77, 117, 0.9);
      }

      /* Why Choose Us */
      .why-choose-section {
        padding-top: 64px;
        padding-bottom: 64px;
        background-color: #f7fafc;
      }
      .why-choose-container {
        max-width: 1280px;
        margin-left: auto;
        margin-right: auto;
        padding-left: 16px;
        padding-right: 16px;
      }
      .why-choose-title {
        font-size: 1.875rem;
        font-weight: 700;
        text-align: center;
        margin-bottom: 48px;
      }
      .why-choose-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 32px;
      }
      @media (min-width: 768px) {
        .why-choose-grid {
          grid-template-columns: repeat(2, 1fr);
        }
      }
      @media (min-width: 1024px) {
        .why-choose-grid {
          grid-template-columns: repeat(4, 1fr);
        }
      }
      .reason-card {
        background-color: #ffffff;
        padding: 24px;
        border-radius: 8px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        transition: box-shadow 0.2s;
      }
      .reason-card:hover {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      }
      .reason-icon {
        width: 48px;
        height: 48px;
        background-color: rgba(11, 77, 117, 0.1);
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
      }
      .reason-icon div {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0b4d75;
      }
      .reason-card h3 {
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 8px;
      }
      .reason-card p {
        color: #4b5563;
      }

      /* Latest Updates */
      .updates-section {
        padding-top: 64px;
        padding-bottom: 64px;
        background-color: #ffffff;
      }
      .updates-container {
        max-width: 1280px;
        margin-left: auto;
        margin-right: auto;
        padding-left: 16px;
        padding-right: 16px;
      }
      .updates-title {
        font-size: 1.875rem;
        font-weight: 700;
        text-align: center;
        margin-bottom: 48px;
      }
      .updates-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 32px;
      }
      @media (min-width: 768px) {
        .updates-grid {
          grid-template-columns: repeat(3, 1fr);
        }
      }
      .update-card {
        background-color: #f9fafb;
        padding: 24px;
        border-radius: 8px;
      }
      .update-icon {
        width: 48px;
        height: 48px;
        background-color: rgba(11, 77, 117, 0.1);
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
      }
      .update-icon div {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0b4d75;
      }
      .update-card h3 {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 12px;
      }
      .update-card p {
        color: #4b5563;
        margin-bottom: 16px;
      }
      .update-card a {
        color: #0b4d75;
        font-weight: 500;
        text-decoration: none;
        transition: color 0.2s;
      }
      .update-card a:hover {
        color: rgba(11, 77, 117, 0.8);
      }

      /* Footer */
      footer {
        background-color: #1f2937;
        color: #ffffff;
        padding-top: 64px;
        padding-bottom: 32px;
      }
      .footer-container {
        max-width: 1280px;
        margin-left: auto;
        margin-right: auto;
        padding-left: 16px;
        padding-right: 16px;
      }
      .footer-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 32px;
        margin-bottom: 48px;
      }
      @media (min-width: 768px) {
        .footer-grid {
          grid-template-columns: repeat(2, 1fr);
        }
      }
      @media (min-width: 1024px) {
        .footer-grid {
          grid-template-columns: repeat(4, 1fr);
        }
      }
      .footer-section h3 {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 24px;
      }
      .footer-section p {
        color: #9ca3af;
        margin-bottom: 24px;
      }
      .social-links {
        display: flex;
        gap: 16px;
      }
      .social-link {
        width: 40px;
        height: 40px;
        border-radius: 9999px;
        background-color: #374151;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background-color 0.2s;
        text-decoration: none;
      }
      .social-link:hover {
        background-color: #0b4d75;
      }
      .social-link div {
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .footer-list {
        list-style: none;
        padding: 0;
        margin: 0;
      }
      .footer-list li {
        margin-bottom: 12px;
      }
      .footer-list a {
        color: #9ca3af;
        text-decoration: none;
        transition: color 0.2s;
      }
      .footer-list a:hover {
        color: #ffffff;
      }
      .contact-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 16px;
      }
      .contact-item div {
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0b4d75;
        margin-right: 12px;
      }
      .contact-item span {
        color: #9ca3af;
      }
      .newsletter h4 {
        font-weight: 500;
        color: #ffffff;
        margin-bottom: 16px;
      }
      .newsletter-form {
        display: flex;
      }
      .newsletter-input {
        flex: 1;
        background-color: #374151;
        border: none;
        color: #ffffff;
        padding: 8px 16px;
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
        font-size: 0.875rem;
      }
      .newsletter-input:focus {
        outline: none;
        box-shadow: 0 0 0 1px #0b4d75;
      }
      .newsletter-btn {
        background-color: #0b4d75;
        color: #ffffff;
        padding: 8px 16px;
        border-top-right-radius: 8px;
        border-bottom-right-radius: 8px;
        border: none;
        white-space: nowrap;
        cursor: pointer;
        transition: background-color 0.2s;
      }
      .newsletter-btn:hover {
        background-color: rgba(11, 77, 117, 0.9);
      }
      .footer-bottom {
        border-top: 1px solid #4b5563;
        padding-top: 32px;
      }
      .footer-bottom-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
      }
      @media (min-width: 768px) {
        .footer-bottom-content {
          flex-direction: row;
          justify-content: space-between;
          text-align: left;
        }
      }
      .footer-bottom p {
        color: #6b7280;
        font-size: 0.875rem;
        margin-bottom: 16px;
      }
      @media (min-width: 768px) {
        .footer-bottom p {
          margin-bottom: 0;
        }
      }
      .footer-links {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 16px;
      }
      .footer-links a {
        color: #6b7280;
        font-size: 0.875rem;
        text-decoration: none;
        transition: color 0.2s;
      }
      .footer-links a:hover {
        color: #ffffff;
      }
      .payment-icons {
        margin-top: 24px;
        display: flex;
        justify-content: center;
        gap: 16px;
      }
      .payment-icon {
        width: 40px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
        font-size: 1.25rem;
      }

      /* Custom Input Styles */
      input[type="date"]::-webkit-calendar-picker-indicator {
        opacity: 0;
      }
      .custom-checkbox {
        position: relative;
        padding-left: 28px;
        cursor: pointer;
      }
      .custom-checkbox input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
      }
      .checkmark {
        position: absolute;
        top: 0;
        left: 0;
        height: 20px;
        width: 20px;
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 4px;
      }
      .custom-checkbox input:checked ~ .checkmark {
        background-color: #0052cc;
        border-color: #0052cc;
      }
      .checkmark:after {
        content: "";
        position: absolute;
        display: none;
      }
      .custom-checkbox input:checked ~ .checkmark:after {
        display: block;
      }
      .custom-checkbox .checkmark:after {
        left: 7px;
        top: 3px;
        width: 6px;
        height: 11px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
      }
      .custom-switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
      }
      .custom-switch input {
        opacity: 0;
        width: 0;
        height: 0;
      }
      .switch-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: 0.4s;
        border-radius: 24px;
      }
      .switch-slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.4s;
        border-radius: 50%;
      }
      .custom-switch input:checked + .switch-slider {
        background-color: #0052cc;
      }
      .custom-switch input:checked + .switch-slider:before {
        transform: translateX(20px);
      }
    </style>
  </head>
  <body>
    <header>
      <div class="container">
        <div class="flex">
          <a href="#" class="logo">logo</a>
        </div>
      </div>
    </header>
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-gradient"></div>
      <div
        class="hero-bg"
      ></div>
      <div class="hero-content">
        <div class="hero-text">
          <h1>
            Connecting the World, One Flight at a Time
          </h1>
          <p>
            Experience seamless travel and cargo solutions with our global
            network of services designed for reliability and comfort.
          </p>
          <div class="hero-buttons">
            <button class="btn-primary">
              Book Passenger Flights
            </button>
            <button class="btn-secondary">
              Ship Cargo
            </button>
          </div>
        </div>
      </div>
    </section>
    <!-- Search Panel -->
    <section class="search-section">
      <div class="search-container">
        <div class="search-panel">
          <div class="search-tabs">
            <button class="tab tab-active">
              Passenger Flights
            </button>
            <button class="tab tab-inactive">
              Cargo Shipping
            </button>
          </div>
          <div class="passenger-tab">
            <form>
              <div class="search-form">
                <div class="form-group">
                  <label>From</label>
                  <div class="relative">
                    <div class="input-icon">
                      <div>
                        <i class="ri-flight-takeoff-line"></i>
                      </div>
                    </div>
                    <input
                      type="text"
                      placeholder="City or Airport"
                      class="form-input"
                    />
                  </div>
                </div>
                <div class="form-group">
                  <label>To</label>
                  <div class="relative">
                    <div class="input-icon">
                      <div>
                        <i class="ri-flight-land-line"></i>
                      </div>
                    </div>
                    <input
                      type="text"
                      placeholder="City or Airport"
                      class="form-input"
                    />
                  </div>
                </div>
                <div class="form-group">
                  <label>Departure</label>
                  <div class="relative">
                    <div class="input-icon">
                      <div>
                        <i class="ri-calendar-line"></i>
                      </div>
                    </div>
                    <input
                      type="date"
                      class="form-input"
                    />
                  </div>
                </div>
              </div>
              <div class="form-submit">
                <button type="submit" class="search-btn">
                  Search Flights
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
    <!-- Service Highlights -->
    <section class="services-section">
      <div class="services-container">
        <h2 class="services-title">Our Services</h2>
        <div class="services-grid">
          <div class="service-card">
            <div class="service-icon">
              <div>
                <i class="ri-plane-line ri-2x"></i>
              </div>
            </div>
            <h3>
              Passenger Services
            </h3>
            <p>
              Experience comfort and convenience with our premium passenger
              flights connecting over 120 destinations worldwide.
            </p>
            <div>
              <button>
                Learn More
              </button>
            </div>
          </div>
          <div class="service-card">
            <div class="service-icon">
              <div>
                <i class="ri-truck-line ri-2x"></i>
              </div>
            </div>
            <h3>
              Cargo Solutions
            </h3>
            <p>
              Reliable and efficient cargo shipping services for businesses of
              all sizes, with specialized handling options.
            </p>
            <div>
              <button>
                Learn More
              </button>
            </div>
          </div>
          <div class="service-card">
            <div class="service-icon">
              <div>
                <i class="ri-gift-line ri-2x"></i>
              </div>
            </div>
            <h3>
              Special Offers
            </h3>
            <p>
              Exclusive deals and promotions for both frequent flyers and cargo
              clients, with seasonal discounts.
            </p>
            <div>
              <button>
                Learn More
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- Featured Destinations -->
    <section class="destinations-section">
      <div class="destinations-container">
        <div class="destinations-header">
          <h2 class="destinations-title">Featured Destinations</h2>
          <div class="scroll-buttons">
            <button id="scroll-left" class="scroll-btn">
              <div>
                <i class="ri-arrow-left-s-line"></i>
              </div>
            </button>
            <button id="scroll-right" class="scroll-btn">
              <div>
                <i class="ri-arrow-right-s-line"></i>
              </div>
            </button>
          </div>
        </div>
        <div class="destination-scroll">
          <div class="destinations-list">
            <div class="destination-card">
              <img
                src="https://picsum.photos/400/300?image=1081"
                alt="New York"
              />
              <div class="card-content">
                <h3>London → New York</h3>
                <div class="destination-info">
                  <div>
                    <i class="ri-flight-takeoff-line"></i>
                  </div>
                  <span>Heathrow (LHR)</span>
                </div>
                <div class="destination-info">
                  <div>
                    <i class="ri-flight-land-line"></i>
                  </div>
                  <span>JFK International (JFK)</span>
                </div>
                <div class="destination-footer">
                  <p>
                    From <span class="price">$349</span>
                  </p>
                  <button class="quick-book">
                    Book Now
                  </button>
                </div>
              </div>
            </div>
            <div class="destination-card">
              <img
                src="https://picsum.photos/400/300?image=1082"
                alt="Paris"
              />
              <div class="card-content">
                <h3>Dubai → Paris</h3>
                <div class="destination-info">
                  <div>
                    <i class="ri-flight-takeoff-line"></i>
                  </div>
                  <span>Dubai (DXB)</span>
                </div>
                <div class="destination-info">
                  <div>
                    <i class="ri-flight-land-line"></i>
                  </div>
                  <span>Charles de Gaulle (CDG)</span>
                </div>
                <div class="destination-footer">
                  <p>
                    From <span class="price">$429</span>
                  </p>
                  <button class="quick-book">
                    Book Now
                  </button>
                </div>
              </div>
            </div>
            <div class="destination-card">
              <img
                src="https://picsum.photos/400/300?image=1083"
                alt="Singapore"
              />
              <div class="card-content">
                <h3>Tokyo → Singapore</h3>
                <div class="destination-info">
                  <div>
                    <i class="ri-flight-takeoff-line"></i>
                  </div>
                  <span>Narita (NRT)</span>
                </div>
                <div class="destination-info">
                  <div>
                    <i class="ri-flight-land-line"></i>
                  </div>
                  <span>Changi (SIN)</span>
                </div>
                <div class="destination-footer">
                  <p>
                    From <span class="price">$599</span>
                  </p>
                  <button class="quick-book">
                    Book Now
                  </button>
                </div>
              </div>
            </div>
            <div class="destination-card">
              <img
                src="https://picsum.photos/400/300?image=1084"
                alt="Sydney"
              />
              <div class="card-content">
                <h3>Hong Kong → Sydney</h3>
                <div class="destination-info">
                  <div>
                    <i class="ri-flight-takeoff-line"></i>
                  </div>
                  <span>Hong Kong (HKG)</span>
                </div>
                <div class="destination-info">
                  <div>
                    <i class="ri-flight-land-line"></i>
                  </div>
                  <span>Kingsford Smith (SYD)</span>
                </div>
                <div class="destination-footer">
                  <p>
                    From <span class="price">$849</span>
                  </p>
                  <button class="quick-book">
                    Book Now
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- Why Choose Us -->
    <section class="why-choose-section">
      <div class="why-choose-container">
        <h2 class="why-choose-title">Why Choose Us</h2>
        <div class="why-choose-grid">
          <div class="reason-card">
            <div class="reason-icon">
              <div>
                <i class="ri-global-line"></i>
              </div>
            </div>
            <h3>Global Network</h3>
            <p>
              Connecting over 120 destinations across 6 continents with our
              extensive flight network.
            </p>
          </div>
          <div class="reason-card">
            <div class="reason-icon">
              <div>
                <i class="ri-shield-check-line"></i>
              </div>
            </div>
            <h3>Reliable Service</h3>
            <p>
              Consistently ranked among the most punctual airlines with a 94%
              on-time arrival rate.
            </p>
          </div>
          <div class="reason-card">
            <div class="reason-icon">
              <div>
                <i class="ri-money-dollar-circle-line"></i>
              </div>
            </div>
            <h3>Competitive Prices</h3>
            <p>
              Transparent pricing with no hidden fees and regular promotions for
              both passengers and cargo.
            </p>
          </div>
          <div class="reason-card">
            <div class="reason-icon">
              <div>
                <i class="ri-customer-service-2-line"></i>
              </div>
            </div>
            <h3>24/7 Support</h3>
            <p>
              Round-the-clock customer service available in multiple languages
              for all your travel needs.
            </p>
          </div>
        </div>
      </div>
    </section>
    <!-- Latest Updates -->
    <section class="updates-section">
      <div class="updates-container">
        <h2 class="updates-title">Latest Updates</h2>
        <div class="updates-grid">
          <div class="update-card">
            <div class="update-icon">
              <div>
                <i class="ri-plane-line"></i>
              </div>
            </div>
            <h3>New Routes</h3>
            <p>
              Introducing daily direct flights between major business hubs:
              London-Dubai, Singapore-Tokyo, and New York-Paris.
            </p>
            <a href="#" class="learn-more">Learn More →</a>
          </div>
          <div class="update-card">
            <div class="update-icon">
              <div>
                <i class="ri-truck-line"></i>
              </div>
            </div>
            <h3>Cargo Network</h3>
            <p>
              Expanded cargo services to 15 new destinations with enhanced
              tracking and handling facilities.
            </p>
            <a href="#" class="learn-more">Learn More →</a>
          </div>
          <div class="update-card">
            <div class="update-icon">
              <div>
                <i class="ri-shield-check-line"></i>
              </div>
            </div>
            <h3>Safety First</h3>
            <p>
              Implementation of advanced safety protocols and new sanitization
              measures across our fleet.
            </p>
            <a href="#" class="learn-more">Learn More →</a>
          </div>
        </div>
      </div>
    </section>
    <!-- Footer -->
    <footer>
      <div class="footer-container">
        <div class="footer-grid">
          <div class="footer-section">
            <h3>About Us</h3>
            <p>
              SkyWings is a global airline offering passenger and cargo services
              to over 120 destinations worldwide with a commitment to safety,
              reliability, and customer satisfaction.
            </p>
            <div class="social-links">
              <a href="#" class="social-link">
                <div>
                  <i class="ri-facebook-fill"></i>
                </div>
              </a>
              <a href="#" class="social-link">
                <div>
                  <i class="ri-twitter-x-fill"></i>
                </div>
              </a>
              <a href="#" class="social-link">
                <div>
                  <i class="ri-instagram-fill"></i>
                </div>
              </a>
              <a href="#" class="social-link">
                <div>
                  <i class="ri-linkedin-fill"></i>
                </div>
              </a>
            </div>
          </div>
          <div class="footer-section">
            <h3>Passenger Services</h3>
            <ul class="footer-list">
              <li><a href="#">Book Flights</a></li>
              <li><a href="#">Check-in Online</a></li>
              <li><a href="#">Flight Status</a></li>
              <li><a href="#">Baggage Information</a></li>
              <li><a href="#">Travel Insurance</a></li>
              <li><a href="#">Frequent Flyer Program</a></li>
              <li><a href="#">Special Assistance</a></li>
            </ul>
          </div>
          <div class="footer-section">
            <h3>Cargo Services</h3>
            <ul class="footer-list">
              <li><a href="#">Book Cargo</a></li>
              <li><a href="#">Track Shipment</a></li>
              <li><a href="#">Cargo Rates</a></li>
              <li><a href="#">Specialized Cargo</a></li>
              <li><a href="#">Dangerous Goods</a></li>
              <li><a href="#">Cargo Insurance</a></li>
              <li><a href="#">Business Solutions</a></li>
            </ul>
          </div>
          <div class="footer-section">
            <h3>Contact & Support</h3>
            <ul class="footer-list">
              <li class="contact-item">
                <div><i class="ri-map-pin-line"></i></div>
                <span
                  >SkyWings Tower, 123 Aviation Blvd, New York, NY 10001, USA</span
                >
              </li>
              <li class="contact-item">
                <div><i class="ri-phone-line"></i></div>
                <span>+1 (800) 123-4567</span>
              </li>
              <li class="contact-item">
                <div><i class="ri-mail-line"></i></div>
                <span>info@skywings.com</span>
              </li>
            </ul>
            <div class="newsletter">
              <h4>Subscribe to our newsletter</h4>
              <div class="newsletter-form">
                <input
                  type="email"
                  placeholder="Your email address"
                  class="newsletter-input"
                />
                <button class="newsletter-btn">Subscribe</button>
              </div>
            </div>
          </div>
        </div>
        <div class="footer-bottom">
          <div class="footer-bottom-content">
            <p>© 2025 SkyWings Airlines. All rights reserved.</p>
            <div class="footer-links">
              <a href="#">Privacy Policy</a>
              <a href="#">Terms of Service</a>
              <a href="#">Cookie Policy</a>
              <a href="#">Sitemap</a>
            </div>
          </div>
          <div class="payment-icons">
            <div class="payment-icon">
              <i class="ri-visa-fill ri-lg"></i>
            </div>
            <div class="payment-icon">
              <i class="ri-mastercard-fill ri-lg"></i>
            </div>
            <div class="payment-icon">
              <i class="ri-paypal-fill ri-lg"></i>
            </div>
            <div class="payment-icon">
              <i class="ri-apple-fill ri-lg"></i>
            </div>
            <div class="payment-icon">
              <i class="ri-google-fill ri-lg"></i>
            </div>
          </div>
        </div>
      </div>
    </footer>
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        // Destination scroll functionality
        const scrollContainer = document.querySelector(".destination-scroll");
        const scrollLeftBtn = document.getElementById("scroll-left");
        const scrollRightBtn = document.getElementById("scroll-right");
        scrollLeftBtn.addEventListener("click", function () {
          scrollContainer.scrollBy({ left: -300, behavior: "smooth" });
        });
        scrollRightBtn.addEventListener("click", function () {
          scrollContainer.scrollBy({ left: 300, behavior: "smooth" });
        });
      });
      document.addEventListener("DOMContentLoaded", function () {
        // Tab switching functionality
        const passengerTab = document.querySelector(".tab-active");
        const cargoTab = passengerTab.nextElementSibling;
        cargoTab.addEventListener("click", function () {
          passengerTab.classList.remove("tab-active");
          cargoTab.classList.add("tab-active");
          // Cargo form toggle would go here
        });
        passengerTab.addEventListener("click", function () {
          cargoTab.classList.remove("tab-active");
          passengerTab.classList.add("tab-active");
        });
      });
    </script>
  </body>
</html>