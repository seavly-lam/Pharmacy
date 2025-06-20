<!-- Header -->
<div class="header">

    <!-- Logo and Brand Name -->
    <div class="header-left d-flex align-items-center">

        <!-- Invisible logo placeholders for layout consistency -->
        <a href="{{ route('dashboard') }}" class="logo"></a>
        <a href="{{ route('dashboard') }}" class="logo logo-small"></a>

        <!-- Brand Name Text Logo -->
        <a href="{{ route('dashboard') }}"
           class="brand-text logo-text font-weight-bold align-self-start"
           id="brandLogo">
            veecare
        </a>
    </div>

    <!-- Toggle Sidebar -->
    <a href="javascript:void(0);" id="toggle_btn">
        <i class="fe fe-text-align-left"></i>
    </a>

    <!-- Mobile Menu Toggle -->
    <a class="mobile_btn" id="mobile_btn">
        <i class="fa fa-bars"></i>
    </a>

    <!-- Header Right Menu -->
    <ul class="nav user-menu">
        <li class="nav-item dropdown">
            <a href="#" data-target="#add_sales" title="make a sale" data-toggle="modal" class="dropdown-toggle nav-link">
                <i class="fas fa-cash-register"></i>
            </a>
        </li>

        <!-- Notifications -->
        <li class="nav-item dropdown noti-dropdown">
            <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                <i class="fe fe-bell"></i>
                <span class="badge badge-pill">{{ auth()->user()->unReadNotifications->count() }}</span>
            </a>
            <div class="dropdown-menu notifications">
                <div class="topnav-dropdown-header">
                    <span class="notification-title">Notifications</span>
                    <a href="{{ route('mark-as-read') }}" class="clear-noti">Mark All As Read</a>
                </div>
                <div class="noti-content">
                    <ul class="notification-list">
                        @foreach (auth()->user()->unReadNotifications as $notification)
                            <li class="notification-message">
                                <a href="{{ route('read') }}">
                                    <div class="media">
                                        <span class="avatar avatar-sm">
                                            <img class="avatar-img rounded-circle"
                                                 src="{{ asset('storage/purchases/' . $notification['image']) }}"
                                                 alt="Product image">
                                        </span>
                                        <div class="media-body">
                                            <h6 class="text-danger">Stock Alert</h6>
                                            <p class="noti-details">
                                                <span class="noti-title">{{ $notification->data['product_name'] }} is only {{ $notification->data['quantity'] }} left.</span>
                                                <span>Please update the purchase quantity</span>
                                            </p>
                                            <p class="noti-time"><span class="notification-time">{{ $notification->created_at->diffForHumans() }}</span></p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="topnav-dropdown-footer">
                    <a href="#">View all Notifications</a>
                </div>
            </div>
        </li>

        <!-- User Menu -->
        <li class="nav-item dropdown has-arrow">
            <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                <span class="user-img">
                    <img class="rounded-circle"
                         src="{{ !empty(auth()->user()->avatar) ? asset('storage/users/' . auth()->user()->avatar) : asset('assets/img/avatar_1nn.png') }}"
                         width="31"
                         alt="avatar">
                </span>
            </a>
            <div class="dropdown-menu">
                <div class="user-header">
                    <div class="avatar avatar-sm">
                        <img src="{{ !empty(auth()->user()->avatar) ? asset('storage/users/' . auth()->user()->avatar) : asset('assets/img/avatar_1nn.png') }}"
                             class="avatar-img rounded-circle"
                             alt="User Image">
                    </div>
                    <div class="user-text">
                        <h6>{{ auth()->user()->name }}</h6>
                    </div>
                </div>
                <a class="dropdown-item" href="{{ route('profile') }}">My Profile</a>
                @can('view-settings')
                    <a class="dropdown-item" href="{{ route('settings') }}">Settings</a>
                @endcan
                <a href="javascript:void(0)" class="dropdown-item">
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <button type="submit" class="btn">Logout</button>
                    </form>
                </a>
            </div>
        </li>
    </ul>
</div>
<!-- /Header -->

<!-- Font and Style for Logo Text -->
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap');
    .logo-text {
        font-family: 'Poppins', sans-serif;
		font-weight: 600;
		font-size: 1.3rem;
		color: #1abc9c;
		text-transform: lowercase;
		letter-spacing: 1.2px;
		margin-top: 11px;
        transition: font-size 0.3s ease, opacity 0.3s ease;
    }

    .logo-text.shrink {
        font-size: 1rem;
        opacity: 0.85;
    }

    @media (max-width: 768px) {
        .logo-text {
            font-size: 1rem;
            margin-top: 8px;
        }
    }
</style>

<!-- JS for Shrinking Brand Text When Sidebar Collapsed -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggleBtn = document.getElementById('toggle_btn');
        const brandLogo = document.getElementById('brandLogo');
        const body = document.body;

        toggleBtn.addEventListener('click', function () {
            setTimeout(() => {
                if (body.classList.contains('mini-sidebar')) {
                    brandLogo.classList.add('shrink');
                } else {
                    brandLogo.classList.remove('shrink');
                }
            }, 100);
        });
    });
</script>
