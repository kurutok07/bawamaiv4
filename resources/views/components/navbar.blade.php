<header class="main-header">
        <div class="container header-flex">
          <div class="logo">
            <img src="{{ asset('assets/1.png') }}" alt="Logo SD Bawamai" class="logo-img">
            <span class="logo-text">SD Bawamai Pontianak</span>
          </div>
      
          <button class="nav-toggle" aria-label="Buka menu">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
          </button>
      
          <nav class="main-nav">
            <ul class="main-menu">
              <li><a href="{{ url('/') }}">Beranda</a></li>
              
              @auth
                <li><a href="{{ route('dashboard') }}" class="user-menu-item">Dashboard Saya</a></li>
                <li>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color: #d32f2f;">
                        Keluar <i class="fas fa-sign-out-alt"></i>
                    </a>
                </li>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
              @else
                <li><a href="{{ route('login') }}" class="login-btn">Masuk / Login</a></li>
              @endauth
            </ul>
      
            <form id="search-form" class="search-form-visible" action="#" method="get">
              <input type="search" name="q" placeholder="Cari..." class="search-input">
              <button type="submit" class="search-submit-btn">
                <img src="{{ asset('assets/logo/search.png') }}" alt="Cari">
              </button>
            </form>
          </nav>
        </div>
</header>