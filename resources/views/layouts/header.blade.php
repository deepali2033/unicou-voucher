 <header class="main-header mb-4 d-flex justify-content-between align-items-center py-3 border-bottom">
     <div class="header-left">
         <!-- Space for page title or breadcrumbs if needed -->
     </div>

     <div class="header-right d-flex align-items-center gap-3">
         <div class="notification-bell">
             <i class="far fa-bell" style="font-size: 1.2rem; color: #666; cursor: pointer;"></i>
         </div>

         <div class="country-flag">
             <img src="https://flagcdn.com/w40/pk.png" width="25" alt="Pakistan Flag" style="border-radius: 2px;">
         </div>

         <div class="user-dropdown d-flex align-items-center gap-2">
             <img src="{{ asset('images/user.png') }}" class="user-avatar rounded-circle" width="40" height="40">
             <div class="user-info d-flex flex-column">
                 <span class="user-name fw-bold" style="font-size: 0.9rem; line-height: 1;">{{ Auth::user()->name }}</span>
                 <small class="user-role text-muted" style="font-size: 0.75rem;">Agent</small>
             </div>

             <div class="dropdown">
                 <a href="#" class="text-decoration-none text-dark" data-bs-toggle="dropdown">
                     <i class="fas fa-chevron-down" style="font-size: 0.8rem; color: #999;"></i>
                 </a>
                 <div class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                     <a class="dropdown-item" href="#">Manage Account</a>
                     <div class="dropdown-divider"></div>
                     <form method="POST" action="{{ route('auth.logout') }}">
                         @csrf
                         <button type="submit" class="dropdown-item">Logout</button>
                     </form>
                 </div>
             </div>
         </div>
     </div>
 </header>